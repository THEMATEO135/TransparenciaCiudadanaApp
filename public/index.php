<?php
require __DIR__ . '/../vendor/autoload.php';


$routes = require __DIR__ . '/../config/routes.php';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


if (!isset($routes[$path])) {
http_response_code(404);
echo '404 - Not Found';
exit;
}


list($class, $method) = $routes[$path];
$controller = new $class();
$controller->{$method}();