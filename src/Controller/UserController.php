<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/-/user/org.couchdb.user:{username}", methods={"PUT"})
     */
    public function login($username, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository(User::class);
        $user = $repository
            ->findOneBy(
                [
                    'username' => $username
                ]
            );

        die('Called');

        if (!$user) {
            throw new UnauthorizedHttpException('User not found');
        }

        $token = random_bytes(20);
        $token = bin2hex($token);

        $user->setToken($token);
        $manager->flush();

        die("Called");
        return $this->json(
            [
                'ok' => 'You are authenticated as ' . $username,
                'token' => ''
            ]
        )->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @Route("/-/user/{couchdb_user}", methods={"GET"})
     */
    public function singleUser(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->json(
            [
                'ok' => sprintf('you are authenticated as $s', $request->getUser())
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/-/user/token/*", methods={"DELETE"})
     * @param Request $request
     */
    public function logout(Request $request)
    {
        die('Called2');
    }
}