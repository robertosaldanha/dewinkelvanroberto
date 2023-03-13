<?php

use \roberto\Page;
use \roberto\Model\Category;
use \roberto\Model\Product;

//Rota para a home
$app -> get ('/', function()
{
	$products = Product::listAll();

	$page = new Page();
	$page -> setTpl("index",
		[
			'products' => Product::checklist ($products)
		]);	
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
				'products' => Product::checklist($category -> getProducts())
			]);
	});
?>