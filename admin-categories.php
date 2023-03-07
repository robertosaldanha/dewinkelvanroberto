<?php

use \roberto\PageAdmin;
use \roberto\Model\User;
use \roberto\Model\Category;
 
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


?>