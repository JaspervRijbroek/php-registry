<?php

declare(strict_types=1);

namespace Library\Traits;

use Library\DB;

trait Authenticate
{
    private $user = null;

    public function getUser()
    {
        if($this->user) {
            return $this->user;
        }

        $token = $this->getRequest()->getHeader('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if (!$token) {
            throw new \Error('No token found, are you logged in?');
        }

        // Check the user to which the token belongs.
        $users = DB::getInstance()->select(
            'users',
            '*',
            ['token' => $token]
        );
        $user = array_shift($users);

        if(!$user) {
            throw new \Error('No token found, are you logged in?');
        }

        return $this->user = $user;
    }

    public function authenticate(string $role)
    {
        $user = $this->getUser();
        $roles = str_getcsv($user['roles']);

        return in_array($role, $roles);
    }
}