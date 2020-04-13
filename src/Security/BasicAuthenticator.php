<?php

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BasicAuthenticator extends \Symfony\Component\Security\Guard\AbstractGuardAuthenticator
{
    private $em;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @inheritDoc
     */
    public function start(
        \Symfony\Component\HttpFoundation\Request $request,
        \Symfony\Component\Security\Core\Exception\AuthenticationException $authException = null
    ) {
        $data = [
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function supports(\Symfony\Component\HttpFoundation\Request $request)
    {
        return $request->request->get('username') && $request->request->get('password');
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(\Symfony\Component\HttpFoundation\Request $request)
    {
        return [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider)
    {
        return $this->em->getRepository(\App\Entity\User::class)
            ->findOneBy(
                [
                    'username' => $credentials['username']
                ]
            );
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, \Symfony\Component\Security\Core\User\UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(
        \Symfony\Component\HttpFoundation\Request $request,
        \Symfony\Component\Security\Core\Exception\AuthenticationException $exception
    ) {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(
        \Symfony\Component\HttpFoundation\Request $request,
        \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token,
        string $providerKey
    ) {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe()
    {
        return false;
    }
}