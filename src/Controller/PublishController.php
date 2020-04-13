<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PublishController extends AbstractController
{
    /**
     * @Route("/{package}", methods={"PUT"})
     */
    public function publishSimplePackage(Request $request)
    {
        return $this->json(
            [
                'success' => true,
                'ok' => 'created new package'
            ]
        )->setStatusCode(201);
    }

    /**
     * @Route("/{scope}/{package}", methods={"PUT"})
     */
    public function publishScopedPackage(Request $request)
    {
        return $this->json(
            [
                'success' => true,
                'ok' => 'created new package'
            ]
        )->setStatusCode(201);
    }
}