<?php

session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \roberto\Page;
use \roberto\PageAdmin;
use \roberto\Model\User;

$app = new \Slim\Slim();

$app -> config ('debug', true);

$app -> get ('/', function()
{
	$page = new Page();
	$page -> setTpl("index");	
});

// rota para a página de admin
$app -> get ('/admin', function()
{
	User::verifyLogin();
	$page = new PageAdmin();
	$page -> setTpl("index");	
});

// rota para a página de login do admin
$app -> get ('/admin/login', function ()
{
	$page = new PageAdmin
	([
		"header" => false,
		"footer" => false
	]);
	//aqui insere o $opts na classe page para alterar os defaults.
	$page -> setTpl("login");
});

// rota para o login do admin
$app -> post('/admin/login', function()
{
	User::login($_POST["login"], $_POST["password"]);
	header("Location: /admin");
	exit;
});

//rota para o logout do admin
$app -> get ('/admin/logout', function()
{
	User::logout();
	header("Location: /admin/login");
	exit;
});

//rota para a página de usuários dentro do admin
$app -> get("/admin/users", function()
{
	User::verifyLogin();
	$users = User::listAll();
	$page = new PageAdmin();
	$page -> setTpl("users", array
		(
			"users" => $users 
		));

});

//rota para a criação de novos usuários dentro do admin
$app -> get("/admin/users/create", function()
{
	User::verifyLogin();
	$page = new PageAdmin();
	$page -> setTpl("users-create");
});

//rota para deletar um usuário no banco
//deve ser executada antes do iduser e nunca vai executar o /delete 
$app -> get("/admin/users/:iduser/delete", function ($iduser)
{
	User::verifyLogin();
	$user = new User(); 
	$user -> get ((int)$iduser);
	$user -> delete();
	header("Location: /admin/users");
	exit;
});

//rota para dar update nos usuários já existentes dentro do admin
$app -> get("/admin/users/:iduser", function($iduser)
{
	User::verifyLogin();
	//$page = new PageAdmin();
	
	//
	
	$user = new User();
	$user -> get((int)$iduser);
	$page = new PageAdmin();
	

	//$page -> setTpl("users-update");
	
	
	$page -> setTpl("users-update", array
		(
			"user" => $user -> getValues()
		));
	
	
});

//rota para salvar no banco
$app -> post("/admin/users/create", function()
{
	User::verifyLogin();
	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;
	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT,
	[
		"cost"=>12
	]);
	$user -> setData($_POST);
	$user -> save();
	header ("Location: /admin/users");
	exit;
});

//salvar o update no do banco
$app -> post ("/admin/users/:iduser", function($iduser)
{
	User::verifyLogin();
	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;  
	$user -> get ((int)$iduser);
	$user -> setData($_POST);
	$user -> update();
	header ("Location: /admin/users");
	exit;
});




$app -> run();


?>