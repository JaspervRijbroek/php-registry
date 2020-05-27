<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Users;
use Library\Controller;
use Library\Response;
use Library\Traits\Authenticate;

class User extends Controller
{
    use Authenticate;

    /**
     * @Route("/-/user/org.couchdb.user:{user}", "PUT")
     */
    public function login()
    {
        $repository = $this->getRepository(Users::class);
        $users = $repository->findBy(['username' => $this->request->getBody('name')]);

        if (!count($users)) {
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

    /**
     * This method checks who is currently logged in for this request
     *
     * @Route("/whoami")
     * @Route("/-/whoami")
     * @return Response The response for this request.
     */
    public function whoami(): Response
    {
        $user = $this->authenticate();

        return $this->response
            ->setBody(
                [
                    'username' => $user->getUsername()
                ]
            );
    }
}