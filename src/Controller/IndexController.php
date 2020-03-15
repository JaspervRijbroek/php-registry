<?php

namespace App\Controller;

use App\Entity\Package;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     */
    public function index()
    {
        $package = $this->getDoctrine()
            ->getRepository(Package::class)
            ->find('1');

        var_dump($package);
        die('Called');
    }
}