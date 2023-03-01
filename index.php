<?php

session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \roberto\Page;
use \roberto\PageAdmin;
use \roberto\Model\User;
use \roberto\Model\Category;

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

//Rota para dar update nos usuários já existentes dentro do admin
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

//Rota para salvar no banco
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

//Salvar o update no do banco
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

//Rota para acessar categorias
$app -> get("/admin/categories", function()
{
	User::verifyLogin();

	$categories = Category::listAll();
	$page = new PageAdmin();
	$page -> setTpl("categories",
		[	
			'categories' => $categories
		]);
});

//Rota para criar uma categoria
$app -> get("/admin/categories/create", function()
{
	User::verifyLogin();

	$page = new PageAdmin();
	$page -> setTpl("categories-create");
});

//Rota para adicionar categoria ao banco
$app -> post("/admin/categories/create", function()
{
	User::verifyLogin();

	$category = new Category();
	$category -> setData($_POST);
	$category -> save();

	header ('Location: /admin/categories');
	exit;

});

//Rota do delete das categorias
$app -> get ("/admin/categories/:idcategory/delete", function ($idcategory)
{
	User::verifyLogin();

	$category = new Category();
	$category -> get((int)$idcategory);
	$category -> delete();

	header ('Location: /admin/categories');
	exit;
});		

//Rota para editar as categorias
$app -> get ("/admin/categories/:idcategory", function ($idcategory)
{
	User::verifyLogin();

	$category = new Category();
	$category -> get ((int)$idcategory);

	$page = new PageAdmin();
	$page -> setTpl("categories-update",
		[
			'category' => $category -> getValues()
		]);	
});	

//Rota para editar as categorias
$app -> post ("/admin/categories/:idcategory", function ($idcategory)
{
	User::verifyLogin();
	
	$category = new Category();
	$category -> get ((int)$idcategory);
	$category -> setData($_POST);
	$category -> save();

	header ('Location: /admin/categories');
	exit;
});

//Rota para categoria
$app -> get ("/categories/:idcategory", function($idcategory)
	{
		$category = new Category();
		$category -> get ((int)$idcategory);

		$page = new Page();
		$page -> setTpl("category",
			[
				'category' => $category -> getValues(),
				'products' => []
			]);
	});

$app -> run();


?>