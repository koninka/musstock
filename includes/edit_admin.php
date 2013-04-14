<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/settings.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/entity.php');

	$result = Array('result' => false, 'table' => '', 'message' => 'Ошибка при выполнении запроса!');

	$columns = $_POST['columns'];
	$values = isset($_POST['values']) ? $_POST['values'] : '';
	$obj = new $_POST['table']();
	switch ($_POST['type']) {
		case 'add':
			$result['result'] = $obj->add($columns, $values);
			break;
		case 'change':
			$result['result'] = $obj->edit($columns, $values, $_POST['id']);
			// if ($st->errorInfo()[0] == '23000') {
				// $result['message'] = 'В базе данных уже существует товар с таким артикулом!';
				// break;
			// }
			break;
		case 'delete':
			$result['result'] = $obj->delete($_POST['del_products']);
			break;
		default:
	}
	if ($result['result']) {
		$result['table'] = $obj->makeEditTable();
	}
	echo json_encode($result);
?>