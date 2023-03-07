<?php

use \roberto\PageAdmin;
use \roberto\Model\User;

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

//Rota para acessar esqueci a senha
$app -> get("/admin/forgot", function()
{
	$page = new PageAdmin(
		[
			"header"=>false,
			"footer"=>false
		]);
	$page -> setTpl("forgot");
});

//Rota para acessar esqueci a senha
$app ->post("/admin/forgot", function()
{
	$user = User::getForgot($_POST["email"]);
	header ("Location: /admin/forgot/sent");
	exit;
});

//Rota para página de sucesso ao enviar email
$app -> get ("/admin/forgot/sent", function ()
{
	$page = new PageAdmin 
	([
		"header" => false,
		"footer" => false
	]);
	$page -> setTpl ("forgot-sent");
});

//Rota para inserir o email para resetar password
$app -> get ("/admin/forgot/reset", function ()
{
	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin
	([
		"header" => false,
		"footer" => false
	]);
$page -> setTpl ("forgot-reset", array
	(
		"name" => $user ["desperson"],
		"code" => $_GET["code"]
	));

});

//Rota de sucesso ao enviar email de password reset
$app -> post("/admin/forgot/reset", function ()
{
	$forgot = User::validForgotDecrypt($_POST["code"]);
	User::setForgotUsed($forgot["idrecovery"]);
	$user = new User();
	$user -> get ((int) $forgot ["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, 
		[
			"cost" => 12
		]);
	$user -> setPassword($password);

	$page = new PageAdmin
	([
		"header" => false,
		"footer" => false
	]);

	$page -> setTpl("forgot-reset-success");

}); 

?>