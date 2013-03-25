<?php
	require_once('settings.php');
	require_once('db_connect.php');

	$result = Array('result' => false, 'table' => '', 'message' => 'Неполадки с базой данных');

	switch ($_POST['type']) {
		case 'add':
			$st = $db_link->prepare('INSERT INTO products(marking, name, category_id, amount) VALUES (?, ?, ?, ?)');
			$st->bindParam(1, $_POST['marking']);
			$st->bindParam(2, $_POST['name']);
			$st->bindParam(3, $_POST['category_id']);
			$st->bindValue(4, intval($_POST['amount']));
			$st->execute();
			if ($st->errorInfo()[0] == '23000') {
				$result['message'] = 'В базе данных уже существует товар с таким артикулом!';
				break;
			}
			$result['result'] = true;
			break;
		case 'change':
			$st = $db_link->prepare('UPDATE products SET marking=?, name=?, category_id=?, amount=? WHERE id=?');
			$st->bindParam(1, $_POST['marking']);
			$st->bindParam(2, $_POST['name']);
			$st->bindParam(3, $_POST['category_id']);
			$st->bindValue(4, intval($_POST['amount']));
			$st->bindParam(5, $_POST['id']);
			$st->execute();
			if ($st->errorInfo()[0] == '23000') {
				$result['message'] = 'В базе данных уже существует товар с таким артикулом!';
				break;
			}
			$result['result'] = true;
			break;
		case 'delete':
			if (isset($_POST['del_products'])) {
				$st = $db_link->prepare('DELETE FROM products WHERE id=:id');
				$count = count($_POST['del_products']);
				for ($i = 0; $i < $count; $i++) {
					$st->bindParam('id', $_POST['del_products'][$i]);
					$st->execute();
				}
				$result['result'] = true;
			}
			else {
				$result['result'] = false;
			}
			break;
		default:
	}
	if ($result['result']) {
		include_once('products_table.php');
		$result['table'] = $products_table;
	}
	echo json_encode($result);
?>