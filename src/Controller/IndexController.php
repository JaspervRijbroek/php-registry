<?php

namespace App\Controller;

use App\Entity\Package;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function index()
    {
//        echo '<pre>';
            $this->getDoctrine()->getRepository(Package::class)->findAll();
//        echo '</pre>';
//        die();


        die('Called');
    }
}