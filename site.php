<?php

use \roberto\Page;
use \roberto\Model\Category;

//Rota para a home
$app -> get ('/', function()
{
	$page = new Page();
	$page -> setTpl("index");	
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
?>