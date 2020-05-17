<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Users;
use Library\Controller;
use Library\DB;
use Library\Response;

class User extends Controller
{
    /**
     * @Route("/-/user/org.couchdb.user:{user}", "PUT")
     */
    public function login()
    {
        error_log(print_r($this->request->getBody(), true));

        $repository = $this->getRepository(Users::class);
        $users = $repository->findBy('username', $this->request->getBody('name'));

        if(!count($users)) {
            return $this->response
                ->setStatus(404)
                ->setBody(
                    [
                        'success' => false,
                        'message' => 'Incorrect credentials'
                    ]
                );
        }

        /* @var $user Users */
        $user = array_shift($users);

        // Check if the passwords match.
        if (!password_verify($this->request->getBody('password'), $user->getPassword())) {
            return $this->response
                ->setStatus(404)
                ->setBody(
                    [
                        'success' => false,
                        'message' => 'Incorrect credentials'
                    ]
                );
        }

        // Generate a secure token.
        // And save this token to the database.
        $token = openssl_random_pseudo_bytes(20);
        $token = bin2hex($token);

        $user->setToken($token);

        // Update everything in the repository.
        $repository->flush();

        return $this
            ->response
            ->setStatus(Response::STATUS_CREATED)
            ->setBody(
                [
                    'ok' => 'Authenticated as ' . $user->getUsername(),
                    'token' => $token
                ]
            );
    }
}