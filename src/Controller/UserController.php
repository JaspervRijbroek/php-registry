<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/-/user/{couchdb_user}", methods={"GET"})
     */
    public function singleUser(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->json([
            'ok' => sprintf('you are authenticated as $s', $request->getUser())
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/-/user/token/*", methods={"DELETE"})
     * @param Request $request
     */
    public function logout(Request $request)
    {
        die('Called');
    }
}