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

$app -> get ('/admin', function()
{
	User::verifyLogin();
	$page = new PageAdmin();
	$page -> setTpl("index");	
});

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

$app -> post('/admin/login', function()
{
	User::login($_POST["login"], $_POST["password"]);
	header("Location: /admin");
	exit;
});

$app -> get ('/admin/logout', function()
	{
		User::logout();
		header("Location: /admin/login");
		exit;
 	}
);

$app -> run();


?>