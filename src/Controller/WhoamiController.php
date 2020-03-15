<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WhoamiController extends AbstractController
{
    /**
     * @Route("/whoami", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(Request $request)
    {
        return $this->json([
            'username' => $request->getUser()
        ]);
    }

    /**
     * @Route("/-/whoami", methods={"GET"})
     */
    public function dashIndex(Request $request)
    {
        return $this->index($request);
    }
}