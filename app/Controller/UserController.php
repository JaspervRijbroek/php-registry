<?php

declare(strict_types=1);

namespace App\Controller;

use Library\Controller;
use Library\DB;
use Library\Response;

class UserController extends Controller
{
    /**
     * @Route("/-/user/org.couchdb.user:(.*)", "PUT")
     */
    public function login()
    {
        $users = DB::getInstance()->select(
            'users',
            '*',
            ['email' => $this->request->getBody('email')]
        );
        $user = array_shift($users);

        // Check if the passwords match.
        if (!password_verify($this->request->getBody('password'), $user['password'])) {
            throw new \Error('Invalid authentication');
        }

        // Generate a secure token.
        // And save this token to the database.
        $token = openssl_random_pseudo_bytes(20);
        $token = bin2hex($token);

        DB::getInstance()->update('users', ['token' => $token], ['email' => $this->request->getBody('email')]);

        return $this
            ->response
            ->setStatus(Response::STATUS_CREATED)
            ->setBody(
                [
                    'ok' => 'Authenticated as ' . $user['name'],
                    'token' => $token
                ]
            );
    }
}