<?php
	require_once('settings.php');
	require_once('db_connect.php');

	$result = Array('result' => false, 'id' => 0, 'category' => 0);

	switch ($_POST['type'])
	{
		case 'add':
			$st = $db_link->prepare('INSERT INTO products(marking, name, category_id, amount) VALUES (?, ?, ?, ?)');
			$st->bindParam(1, $_POST['marking']);
			$st->bindParam(2, $_POST['name']);
			$st->bindParam(3, $_POST['category_id']);
			$st->bindValue(4, intval($_POST['amount']));
			$st->execute();
			//duplicate key
			if ($st->errorInfo()[0] == '23000')
			{
				break;
			}
			$st = $db_link->prepare('SELECT p.id, c.name FROM products p
											 INNER JOIN category c ON c.id = p.category_id WHERE p.marking=?');
			$st->bindParam(1, $_POST['marking']);
			$st->execute();
			$data = $st->fetchAll(PDO::FETCH_ASSOC);
			$result['id'] = $data[0]['id'];
			$result['category'] = $data[0]['name'];
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
			if ($st->errorInfo()[0] == '23000')
			{
				break;
			}
			$st = $db_link->prepare('SELECT c.name FROM products p
											 INNER JOIN category c ON c.id = p.category_id WHERE p.id=?');
			$st->bindParam(1, $_POST['id']);
			$st->execute();
			$data = $st->fetchAll(PDO::FETCH_ASSOC);
			$result['id'] = $_POST['id'];
			$result['category'] = $data[0]['name'];
			$result['result'] = true;
			break;
		case 'delete':
			if (isset($_POST['del_products']))
			{
				$last_idx = array_pop($_POST['del_products']);
				$count = count($_POST['del_products']);
				$st = $db_link->prepare('DELETE FROM products WHERE '.str_repeat(' id=? OR ', $count).'id=?');
				for ($i = 0; $i < $count; $i++)
				{
					$st->bindParam($i + 1, $_POST['del_products'][$i]);
				}
				$st->bindParam($count + 1, $last_idx);
				$st->execute();
				$result['result'] = true;
			}
			else
			{
				$result['result'] = false;
			}
			break;
		default:
	}
	echo json_encode($result);
?>