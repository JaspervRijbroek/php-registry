<?php

declare(strict_types=1);

namespace App\Controller;

use Library\Controller;
use Library\Response;
use Library\Traits\Authenticate;

class Index extends Controller
{
    use Authenticate;

    /**
     * @Route("/")
     */
    public function index(): Response
    {
        $this->authenticate('admin');

        return $this->response->setBody(
            [
                'hello' => 'world'
            ]
        );
    }
}