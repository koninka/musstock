<?php

	//Модель REST
	//angular.js
	error_reporting(E_ALL);

	require_once('includes/settings.php');
	require_once('includes/db_connect.php');

	$smarty = new Smarty_Musshop();

	$req = explode('/', substr($_SERVER['REQUEST_URI'], 1));
	switch ($req[0]) {
		case '': case null: case false:
			$smarty->assign('title', 'GIIIIIIITTT!!');
			$smarty->assign('name', 'Katty');
			$smarty->assign('caption', 'Добро пожаловать в магазин, котятки');
			$smarty->assign('container', Array(0 => 'main.tpl'));
			break;
		case 'admin':
			switch ($req[1]) {
				case 'products':
					$smarty->assign('sb_components', Array(0 => 'left_menu.tpl'));
					include_once('includes/products.php');
					break;
				default:
					break;
			}
			break;
		default:
			$smarty->assign('caption', 'Ошибка 404');
	}

	$smarty->display('page.tpl');
?>