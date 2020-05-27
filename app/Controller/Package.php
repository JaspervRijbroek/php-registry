<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Packages;
use Library\Cache;
use Library\Config;
use Library\Controller;
use Library\Response;

class Package extends Controller
{
    /**
     * This method will check if the package is registered in our database,
     * if not, it will preform a call to NPM to get the JSON and store the data in cache.
     *
     * @Route("/{package}")
     * @Route("/{package}/{version}")
     * @return Response The response for the current request.
     */
    public function find(): Response
    {
        $version = $this->request->getParam('version');
        $name = urldecode($this->request->getParam('package'));
        $repository = $this->getRepository(Packages::class);
        $select = [
            'name' => urldecode($name)
        ];

        if ($version) {
            $select['version'] = $version;
        }

        /** @var Packages[] $packages */
        $packages = $repository->findBy($select);

        if (!count($packages)) {
            return $this->callNPM();
        }

        if ($version) {
            // Return just one package version.
            $packages = $this->formatVersions($packages);
            $package = array_shift($packages);

            return $this->response
                ->setBody($package);
        }

        $package = $packages[count($packages) - 1];

        return $this->response
            ->setBody(
                [
                    '_id' => $package->getName(),
                    'name' => $package->getName(),
                    'description' => $package->getContent()['description'],
                    'versions' => $this->formatVersions($packages),
                    'dist-tags' => [
                        'latest' => $package->getVersion()
                    ]
                ]
            );
    }

    public function callNPM(): Response
    {
        // Get the request path.
        $path = $_SERVER['REQUEST_URI'];
        $baseUrl = Config::get('npm.upstream');
        $npmUrl = $baseUrl . $path;

        if (Cache::has($npmUrl)) {
            return $this->response
                ->setBody(Cache::get($npmUrl));
        }

        // Do the curl request
        $curl = curl_init($npmUrl);
        curl_setopt_array(
            $curl,
            [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true
            ]
        );
        $response = curl_exec($curl);
        curl_close($curl);

        $content = json_decode($response, true);
        Cache::add($npmUrl, $content);

        return $this->response
            ->setBody($content);
    }

    public function formatVersions(array $packages): array
    {
        $versions = [];

        foreach ($packages as $package) {
            $versions[$package->getVersion()] = $package->getContent();
            $versions[$package->getVersion()]['dist']['tarball'] = 'http://' . $_SERVER['HTTP_HOST'] . '/download/package/' . $package->getId();

            // I don't feel like recalculating the integrity.
            unset($versions[$package->getVersion()]['dist']['integrity']);
        }

        return $versions;
    }

    /**
     * This will initiate a file download.
     *
     * @Route("/download/package/{id}")
     * @return void
     */
    public function download(): void
    {
        // Here we can download the file for consumption.
        // It will always be tgz files.
        $repository = $this->getRepository(Packages::class);

        /** @var Packages $package */
        $package = $repository->find((int)$this->request->getParam('id'));

        $this->response
            ->downloadFile($package->getFile());

        return;
    }
}