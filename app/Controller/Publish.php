<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Packages;
use Library\Config;
use Library\Controller;
use Library\Response;
use Library\Traits\Authenticate;

class Publish extends Controller
{
    use Authenticate;

    /**
     * This method will create and place a file on the filesystem.
     *
     * @Route("/{package}", "put")
     * @Route("/{package}/{_rev}", "put")
     * @Route("/{package}/{_rev}/{revision}", "put")
     * @return Response The response when the request was successful.
     */
    public function index()
    {
        $user = $this->authenticate('admin');

        // Upload the provided package(s), we keep multiple in account just in case.
        $repository = $this->getRepository(Packages::class);
        $packageName = $this->request->getBody('_id');
        $versions = array_keys($this->request->getBody('versions'));
        $attachments = $this->request->getBody('_attachments');

        // We will only support uploading a single version at a time, this only is also possible with npm.
        if (!count($versions) || count($versions) > 1) {
            // We only accept one!
            throw new \Error('Only one version per upload accepted');
        }

        $version = array_shift($versions);
        $attachmentName = $packageName . '-' . $version . '.tgz';
        $attachment = $attachments[$attachmentName];

        // Check if the current version already exists, we don't want duplicates.
        $packages = $repository->findBy(
            [
                'name' => $packageName,
                'version' => $version
            ]
        );
        $count = count($packages);

        if ($count) {
            // This version already exists.
            throw new \Error('This version of this package already exists.');
        }

        $uploadPath = Config::get('upload.path');
        $filePath = implode('/', [$uploadPath, $attachmentName]);

        @mkdir(dirname($filePath), 777, true);
        @file_put_contents($filePath, base64_decode($attachment['data']));

        $content = $this->request->getBody('versions');
        $content = $content[$version];

        /* @var $package Packages */
        $package = $repository->create();
        $package->setName($packageName)
            ->setVersion($version)
            ->setContent($content)
            ->setOwner((int) $user->getId())
            ->setFile($filePath);

        $repository->flush();

        return $this->response
            ->setStatus(Response::STATUS_CREATED)
            ->setBody(
                [
                    'success' => true,
                    'ok' => 'created new package'
                ]
            );
    }

    /**
     * This method will remove a package from the system.
     *
     * @Route("/{package}", "delete")
     */
    public function delete()
    {
        // Delete the package here
    }
}