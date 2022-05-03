<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\DBAL\Driver\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UserController extends ApiController
{
    /**
     * @Route("/api/register", name="app_register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();

        $request = $this->transformJsonBody($request);

        $email = $request->get('email', '');
        $login = $request->get('login', '');
        $password = $request->get('password', '');
        $firstname = $request->get('firstname', '');
        $lastname = $request->get('lastname', '');

        if (empty($email) || empty($login) || empty($password)){
            return $this->respondValidationError("Invalid Login or Password or Email or Firstname or Lastname");
        }
        $user->setLogin($login);
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setPassword($userPasswordHasher->hashPassword($user, $password));
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }

        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getEmail()));
    }
    /**
     * @Route("/api/users", name="app_user_show", methods={"GET"})
     */
    public function show(UserService $userService): JsonResponse
    {
        return $this->json($userService->getCurrentUser());
    }
    /**
     * @Route("/api/users", name="app_user_update", methods={"PUT"})
     */
    public function update(Request $request, UserService $userService, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $request = $this->transformJsonBody($request);

        $user = $userService->getCurrentUser();

        $email = $request->get('email', $user->getEmail());
        $login = $request->get('login', $user->getLogin());
        if ($request->getPassword()) {
            $password = $request->get('password', $user->getPassword());
        } else {
            $password = null;
        }
        $firstname = $request->get('firstname', $user->getFirstname());
        $lastname = $request->get('lastname', $user->getLastname());

        $user->setLogin($login);
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        if ($password) {
            $user->setPassword($userPasswordHasher->hashPassword($user, $password));
        }
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }

        return $this->respondWithSuccess(sprintf('User %s successfully updated', $user->getEmail()));
    }
}