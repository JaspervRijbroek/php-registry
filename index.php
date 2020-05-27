<?php

require_once 'lib/Autoloader.php';

ini_set('memory_limit', '512M');

\Library\Autoloader::register();
\Library\Router::register();

\Library\Application::run();