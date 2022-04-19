<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Mysqli\Exception\StatementError;
use Doctrine\DBAL\Driver\SQLSrv\Exception\Error;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends ApiController
{
    /**
     * @Route("/api/register", name="app_register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator): Response
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

        $serializer = $this->container->get('serializer');
        $reports = $serializer->serialize($user, 'json');

        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getEmail()));

        return new Response($reports);
    }
}