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

//Rota para categoria com as páginas 
$app -> get ("/categories/:idcategory", function($idcategory)
	{
		$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

		$category = new Category();
		$category -> get ((int)$idcategory);

		$pagination = $category -> getProductsPage();

		$pages = [];
		for ($i=1; $i <= $pagination['pages']; $i++) 
		{ 
			array_push ($pages,
				[
					'link' => '/categories/' . $category -> getidcategory() . '?page=' . $i,
					'page' => $i
				]); 
		}

		$page = new Page();
		$page -> setTpl("category",
			[
				'category' => $category -> getValues(),
				'products' => $pagination ["data"],
				'pages' => $pages
			]);
	});

//Rota para acessar a página de detalhes do produto
$app -> get("/products/:desurl", function($desurl)
{
	$product = new Product();
	$product -> getFromUrl($desurl);

	$page = new Page();
	$page -> setTpl("product-detail",
		[
			'product' => $product -> getValues(),
			'categories' => $product -> getCategories()
		]);
});
?>