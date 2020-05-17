<?php

declare(strict_types=1);

namespace App\Controller;

use Library\Autoloader;
use Library\Controller;
use Library\DB;
use Library\Response;

class Setup extends Controller
{
    /**
     * This route is called upon setup.
     *
     * @Route("/migrations/setup")
     */
    public function index(): Response
    {
        $db = DB::getInstance();

        // Check if we are already setup.
        // All the tables and stuff are migrations, so if we have a migrations table, we are setup.
        $db->create('migrations', [
            'id' => ['INT', 'AUTO_INCREMENT', 'PRIMARY KEY'],
            'timestamp' => ['INT'],
            'name' => ['VARCHAR(255)']
        ]);

        $this->migrate();

        return $this->response->setBody(
            [
                'message' => 'Migrations all setup'
            ]
        );
    }

    /**
     * This method will run all the new migrations.
     *
     * @Route("/migrations/run")
     */
    public function migrate() {
        // Run all migrations by doing a glob and sending them all.
        $db = DB::getInstance();
        $files = glob(Autoloader::getRoot() . '/migrations/*');
        $migrationsDone = $db->select('migrations', ['timestamp']);
        $migrationsDone = array_column($migrationsDone, 'timestamp');

        foreach($files as $file) {
            $filename = basename($file);
            preg_match('/(.*?)_(.*?)\.php/', $filename, $matches);

            if(in_array($matches[1], $migrationsDone)) {
                // Skip when the migration is done
                continue;
            }

            require_once $file;

            $db->insert('migrations', [
                'timestamp' => $matches[1],
                'name' => $matches[2]
            ]);
        }

        return $this->response->setBody(
            [
                'message' => 'All migrations run successfully'
            ]
        );
    }
}