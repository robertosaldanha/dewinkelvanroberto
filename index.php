<?php

session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \roberto\Page;
use \roberto\PageAdmin;
use \roberto\Model\User;
use \roberto\Model\Category;

$app = new \Slim\Slim();

$app -> config ('debug', true);

require_once("site.php");
require_once("functions.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");


$app -> run();

?>