<?php
	$query = 'SELECT p.id, p.marking, p.name, c.name as category_name, c.id as category_id, p.amount
									FROM products p INNER JOIN category c ON p.category_id = c.id ORDER BY p.id';

	$st = $db_link->query($query);
	$data = $st->fetchAll(PDO::FETCH_ASSOC);
	$smarty->assign('table_rows', $data);

	$st = $db_link->query('SELECT id, name FROM category');
	$data = $st->fetchAll(PDO::FETCH_ASSOC);

	$categories = Array();
	foreach ($data as $key => $value)
	{
		$categories[$value['id']] = $value['name'];
	}

	$smarty->assign('categories', $categories);
	$smarty->assign('caption', 'Добро пожаловать на страничку товаров, котятки');
	$smarty->assign('container', array(0 => 'admin_products_middle.tpl'));
?>