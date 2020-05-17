<?php

declare(strict_types=1);

namespace Library\Traits;

use App\Model\Users;
use Library\DB;

trait Authenticate
{
    private $user = null;

    /**
     * This method will authenticate the user.
     * If the user was already authenticated (for this controller) it will be returned.
     *
     * @return Users The user data
     */
    public function getUser(): Users
    {
        if($this->user) {
            return $this->user;
        }

        $token = $this->getRequest()->getHeader('authorization');
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
        $user = new Users($user);

        if(!$user) {
            throw new \Error('No token found, are you logged in?');
        }

        return $this->user = $user;
    }

    /**
     * This method will check if the authenticated user, if authentication was a success
     * has the required permissions.
     *
     * If the required permission is found the user is returned for potential further use.
     *
     * @param string $role The role to check if the user has permission
     * @return array The user if the current user has the requested permission.
     */
    public function authenticate(string $role): Users
    {
        $user = $this->getUser();

        if(!in_array($role, $user->getRoles())) {
            throw new \Error('No permissions for this operation');
        }

        return $user;
    }
}