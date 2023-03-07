<?php

use \roberto\PageAdmin;
use \roberto\Model\User;

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

?>