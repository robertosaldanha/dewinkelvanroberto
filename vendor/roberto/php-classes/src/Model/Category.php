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
	}
	
}

?>