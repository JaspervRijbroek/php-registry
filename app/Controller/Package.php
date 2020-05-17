<?php

declare(strict_types=1);

namespace App\Controller;

use Library\Controller;

class Package extends Controller
{
    /**
     * @Route("/{package}")
     * @Route("/{package}/{version}")
     */
    public function find()
    {
        $version = $this->request->getParam('version');
        $name = urldecode($this->request->getParam('package'));
        $select = [
            'name' => $name
        ];

        if($version) {
            $select['version'] = $version;
        }


        $package = $this->getDB()->select('packages', $select);
        // If we have a single version, return the content.

        if($version) {
            return $this->response
                ->setBody(json_decode($package['content']));
        }

        // Parse all the data and send it to the client.

        return $this->response;
    }
}