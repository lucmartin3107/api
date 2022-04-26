<?php

namespace App\Security;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractGuardAuthenticator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getCredentials(Request $request)
    {
        $authorizationHeader = $request->headers->get('Authorization');
        return substr($authorizationHeader, 7);
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization')
            && 0 === strpos($request->headers->get('Authorization'), 'Bearer ');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $data = $this->jwtEncoder->decode($credentials);
        if ($data === false) {
            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }
        $email = $data['email'];
        return $this->em
            ->getRepository('AppBundle:User')
            ->findOneBy(['email' => $email]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
        'message' => 'Authentication Required'
    ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

}