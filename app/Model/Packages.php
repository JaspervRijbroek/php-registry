<?php

declare(strict_types=1);

namespace App\Model;

use Library\Model;
use Library\Repository;

/**
 * Class Packages
 *
 * @method string getName()
 * @method string getVersion()
 * @method string getFile()
 * @method Packages setName(string $name)
 * @method Packages setVersion(string $version)
 * @method Packages setFile(string $file)
 *
 * @package App\Model
 */
class Packages extends Model
{
    public function getContent(): array
    {
        return json_decode($this->getData('content'), true);
    }

    public function setContent(array $content): Packages
    {
        $this->setData('content', json_encode($content));

        return $this;
    }

    public function getOwner(): Users
    {
        $repository = new Repository(Users::class);

        /* @var $user Users */
        $user = $repository->find($this->getData('owner'));

        return $user;
    }

    public function setOwner(int $user): Packages
    {
        $this->setData('owner', $user);

        return $this;
    }
}