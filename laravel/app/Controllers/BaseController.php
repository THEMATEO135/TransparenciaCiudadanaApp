<?php
namespace App\Controllers;


class BaseController {
protected function render($view, $params = []) {
// Extrae variables para la vista
extract($params, EXTR_OVERWRITE);
ob_start();
require __DIR__ . '/../Views/' . $view;
$content = ob_get_clean();
require __DIR__ . '/../Views/layouts/main.php';
}


protected function redirect($url) {
header('Location: ' . $url);
exit;
}
}