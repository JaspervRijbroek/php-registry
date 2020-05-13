<?php

require_once 'lib/Autoloader.php';

\Library\Autoloader::register();
\Library\Router::register();

\Library\Application::run();