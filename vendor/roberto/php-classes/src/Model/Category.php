<?php

namespace roberto\Model;

use \roberto\DB\Database;
use \roberto\Model;	

class Category extends Model

{
		
	public static function listAll()
	{
		$sql = new Database();
		return $sql -> select("SELECT * FROM tb_categories ORDER BY descategory");
	}

	public function save()
	{
		$sql = new Database();
		$results = $sql -> select("CALL sp_categories_save(:idcategory, :descategory)", array
			(
				":idcategory" => $this -> getidcategory(),
				":descategory" =>$this -> getdescategory(),
			));

		$this -> setData($results[0]);

		Category::updateFile();
	}

	public function get($idcategory)
	{
		$sql = new Database();
		$results = $sql -> select ("SELECT * FROM db_ecommerce.tb_categories WHERE idcategory = :idcategory",
			[
				':idcategory' => $idcategory
			]);

		$this -> setData($results[0]);
	}

	public function delete()
	{
		$sql = new Database();
		$sql -> query("DELETE FROM db_ecommerce.tb_categories WHERE idcategory = :idcategory",
			[
				':idcategory' => $this -> getidcategory()
			]);

		Category::updateFile();
	}

	public static function updateFile()
	{
		$categories = Category::listAll();

		$html = [];
		foreach ($categories as $row)
		{
			array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
		}
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));
	}
	
}

?>