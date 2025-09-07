<?php
namespace App\Controllers;


use App\Models\UserModel;


class HomeController extends BaseController {
public function index() {
$userModel = new UserModel();
$users = $userModel->all(20);
$this->render('home.php', ['users' => $users]);
}
}