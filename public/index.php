<?php

session_start();

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/config/database.php';

require_once BASE_PATH . '/app/core/Controller.php';
require_once BASE_PATH . '/app/core/Model.php';
require_once BASE_PATH . '/app/core/Router.php';
require_once BASE_PATH . '/app/core/Session.php';
require_once BASE_PATH . '/app/core/Auth.php';

$router = new Router();

require_once BASE_PATH . '/routes/web.php';

$router->dispatch();