<?php

declare(strict_types=1);

namespace App\Model;

use Library\Model;

/**
 * Class Users
 *
 * @method string getPassword()
 * @method string getUsername()
 * @method string getToken()
 * @method Users setPassword(string $password)
 * @method Users setUsername(string $username)
 * @method Users setToken(string $token)
 *
 * @package App\Model
 */
class Users extends Model
{
    public function getRoles(): array
    {
        return str_getcsv($this->getData('roles'));
    }

    public function setRoles(array $roles): Users
    {
        $fp = fopen('php://temp', 'r+');
        fputcsv($fp, $roles);
        rewind($fp);
        $data = fread($fp, 1048576);
        fclose($fp);

        $this->setData('roles', rtrim($data, "\n"));

        return $this;
    }
}