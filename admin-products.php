<?php

use \roberto\PageAdmin;
use \roberto\Model\User;
use \roberto\Model\Product;

//Rota para a página de produtos do admin
$app -> get("/admin/products", function()
{
	User::verifyLogin();

	$products = Product::listAll();

	$page = new PageAdmin();
	$page -> setTpl("products", 
		[
			"products" => $products
		]);
});

//Rota para acessar página de criação de produto
$app -> get("/admin/products/create", function()
{
	User::verifyLogin();

	$page = new PageAdmin();
	$page -> setTpl("products-create");
});

//Rota enviar formulário de produto
$app -> post("/admin/products/create", function()
{
	User::verifyLogin();

	$product = new Product();
	$product -> setData($_POST);
	$product -> save();

	header("Location:/admin/products");
	exit;
});

//Rota para acessar página de edição de produto
$app -> get("/admin/products/:idproduct", function($idproduct)
{
	User::verifyLogin();

	$product = new Product();
	$product -> get ((int)$idproduct);

	$page = new PageAdmin();
	$page -> setTpl("products-update",
		[
			'product' => $product -> getValues()
		]);
});

//Rota enviar edição de produto
$app -> post("/admin/products/:idproduct", function($idproduct)
{
	User::verifyLogin();

	$product = new Product();
	$product -> get ((int)$idproduct);
	$product -> setData($_POST);
	$product -> save();
	$product -> setPhoto($_FILES["file"]);

	header('Location: /admin/products');
	exit;

});

//Rota para deletar um produto
$app -> get("/admin/products/:idproduct/delete", function($idproduct)
{
	User::verifyLogin();

	$product = new Product();
	$product -> get ((int)$idproduct);
	$product -> delete();

	header ('Location: /admin/products');
	exit;

});

?>