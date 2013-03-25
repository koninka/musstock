<?php
	require_once('products_table.php');

	$st = $db_link->query('SELECT id, name FROM category');
	$data = $st->fetchAll(PDO::FETCH_ASSOC);
	$categories = Array();
	foreach ($data as $key => $value)
	{
		$categories[$value['id']] = $value['name'];
	}

	$smarty->assign('products_table', $products_table);
	$smarty->assign('categories', $categories);
	$smarty->assign('caption', 'Добро пожаловать на страничку товаров, котятки');
	$smarty->assign('container', array(0 => 'admin_products_middle.tpl'));
?>