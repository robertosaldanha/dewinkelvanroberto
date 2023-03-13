<?php

use \roberto\PageAdmin;
use \roberto\Model\User;
use \roberto\Model\Category;
use \roberto\Model\Product;
 
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

//Rota para acessar a página de products/categories
$app -> get("/admin/categories/:idcategory/products", function($idcategory)
{
	User::verifyLogin();

	$category = new Category();
	$category -> get((int)$idcategory);

	$page = new PageAdmin();
	$page -> setTpl("categories-products",
	[
		'category' => $category -> getValues(),
		'productsRelated' => $category -> getProducts(),
		'productsNotRelated' => $category -> getProducts(false)
	]);
});

//Rota para adicionar um produto a uma categoria
$app -> get("/admin/categories/:idcategory/products/:idproduct/add", function($idcategory, $idproduct)
{
	User::verifyLogin();

	$category = new Category();
	$category -> get((int)$idcategory);

	$product = new Product();
	$product -> get((int)$idproduct);

	$category -> addProduct($product);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;
});

//Rota para remover um produto de uma categoria
$app -> get("/admin/categories/:idcategory/products/:idproduct/remove", function($idcategory, $idproduct)
{
	User::verifyLogin();

	$category = new Category();
	$category -> get((int)$idcategory);

	$product = new Product();
	$product -> get((int)$idproduct);

	$category -> removeProduct($product);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;
});


?>