<?php

namespace roberto\Model;

use \roberto\DB\Database;
use \roberto\Model;
//use \roberto\Mailer;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './lib/vendor/autoload.php';
	


class User extends Model

{
	const SESSION = "User";
	const GEHEIM = "FIANNE_DAUPHINE_";

	/*protected $fields = [
		"iduser", "idperson", "deslogin", "despassword", "inadmin", "desemail", "nrphone", "dtregister", "desperson"
	];*/

	public static function login($login, $password)
	{
		$sql = new Database();
		$results = $sql -> select ("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
			":LOGIN" => $login
		));

		if (count($results) === 0)
		{
			throw new \Exception ("Usuário inexistente ou senha inválida.");
		}

		$data = $results [0];

		if (password_verify($password, $data["despassword"]) === true)
		{
			$user = new User();

			$user -> setData($data);

			$_SESSION[User::SESSION] =  $user -> getValues();

			return $user;

		} else {
			throw new \Exception("Usuário inexistente ou senha inválida.");
		}
	}

	public static function verifyLogin($inadmin = true)
	{
		if
		(
			!isset($_SESSION[User::SESSION]) 
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
			||
			(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
		)
		{
			header("Location: /admin/login");
			exit;
		}
	}

	public static function logout()
	{
		$_SESSION[User::SESSION] = NULL;
	}

	public static function listAll()
	{
		$sql = new Database();
		return $sql -> select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
	}
	//
	public function get ($iduser)
	{
		$sql = new Database();
		$results = $sql -> select ("SELECT * FROM tb_users a INNER JOIN tb_persons b USING (idperson) WHERE a.iduser = :iduser;", array
			(
				":iduser" => $iduser
			));
		$data = $results[0];
		$this ->setData($data);
	} 
	//

	public function save()
	{
		$sql = new Database();
		/*
		pdesperson VARCHAR (64),
		pdeslogin VARCHAR (64),
		pdespassword VARCHAR (256),
		pdesemail VARCHAR (128),
		pnrphone BIGINT,
		pinadmin TINYINT
		*/
		$results = $sql -> select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array
			(
				":desperson" => $this -> getdesperson(),
				":deslogin" =>$this -> getdeslogin(),
				":despassword" =>$this -> getdespassword(),
				":desemail" =>$this -> getdesemail(),
				":nrphone" =>$this -> getnrphone(),
				":inadmin" =>$this -> getinadmin()
			));

		$this -> setData($results[0]);
	}

	public function update()
	{
		$sql = new Database();
		$results = $sql -> select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array
			(
				":iduser" => $this-> getiduser(),
				":desperson" => $this -> getdesperson(),
				":deslogin" =>$this -> getdeslogin(),
				":despassword" =>$this -> getdespassword(),
				":desemail" =>$this -> getdesemail(),
				":nrphone" =>$this -> getnrphone(),
				":inadmin" =>$this -> getinadmin()
			)); 
		$data = $results[0];
		$this ->setData($data);
	}

	public function delete ()
	{
		$sql = new Database();
		$sql -> query ("CALL sp_users_delete(:iduser)", array(
			":iduser" => $this -> getiduser()
		));
	}

	public static function getForgot($email, $inadmin = true)
	{
		$sql = new Database();

		$results = $sql -> select 
		("
			SELECT * FROM db_ecommerce.tb_persons a
			INNER JOIN db_ecommerce.tb_users b USING (idperson)
			WHERE a.desemail = :email;
		", array 
		(
			":email" => $email
		));

		if (count($results) === 0)
		{
			throw new \Exception ("Não foi possível recuperar a senha.");
		}else{
			$data = $results [0];
			$recoveryResults = $sql -> select ("CALL sp_userspasswordsrecoveries_create (:iduser, :desip)", array
				(
					":iduser" => $data ["iduser"],
					":desip" => $_SERVER ["REMOTE_ADDR"]
				));
			if (count($recoveryResults) === 0)
			{
				throw new \Exception ("Não foi possível recuperar a senha.");
			}else{
				$recoveredData = $recoveryResults[0];
				$iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
				$code = openssl_encrypt($recoveredData['idrecovery'], 'aes-256-cbc', User::GEHEIM, 0, $iv);
				$resultaat = base64_encode($iv.$code);
				if ($inadmin === true)
					{
						$link = "http://dewinkelvanroberto.nl/admin/forgot/reset?code=$resultaat";
					} else {
						$link = "http://dewinkelvanroberto.nl/admin/forgot/reset?code=$resultaat";
					}

				$mailer = new Mailer ($data["desemail"], $data["desperson"], "Redefinição de senha | dewinkelvanroberto", "forgot", array
					(
						"name" => $data ["desperson"],
						"link" => $link
					));
				$mailer -> send();

   
				return $data;

			}
		}
	}

	public static function validForgotDecrypt($resultaat)
	{
		$resultaat = base64_decode($resultaat);
		$code = mb_substr($resultaat, openssl_cipher_iv_length('aes-256-cbc'), null, '8bit');
		$iv = mb_substr($resultaat, 0, openssl_cipher_iv_length('aes-256-cbc'), '8bit');
		$idrecovery = openssl_decrypt($code, 'aes-256-cbc', User::GEHEIM, 0, $iv);
		$sql = new Database();
		$results = $sql -> select 
		("
			SELECT *
			FROM db_ecommerce.tb_userspasswordsrecoveries a
			INNER JOIN db_ecommerce.tb_users b USING (iduser)
			INNER JOIN db_ecommerce.tb_persons c USING (idperson)
			WHERE
			a.idrecovery = :idrecovery
			AND
			a.dtrecovery IS NULL 
			AND 
			DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
		", array
		(
			":idrecovery" => $idrecovery
		));
		if (count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha.");
		} else {
			return $results[0];
		}
	}

	public static function setForgotUsed($idrecovery)
	{
		$sql = new Database();
		$sql -> query ("UPDATE db_ecommerce.tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array
			(
				":idrecovery" => $idrecovery
			));
	}

	public function setPassword($password)
	{
		$sql = new Database();
		$sql -> query ("UPDATE db_ecommerce.tb_users SET despassword = :password WHERE iduser = :iduser", array
			(
				":password" => $password,
				":iduser" => $this -> getiduser()
			));
	}
}

?>