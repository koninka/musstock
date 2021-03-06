<?php
   require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/settings.php');
   require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/db_connect.php');

   if (!isset($smarty)) {
      $smarty = new Smarty_Musshop();
   }

   $query = 'SELECT p.id, p.marking, p.name, c.name as category_name, c.id as category_id, p.amount
                           FROM products p INNER JOIN category c ON p.category_id = c.id ORDER BY p.id';

   $st = $db_link->query($query);
   $data = $st->fetchAll(PDO::FETCH_ASSOC);
   $smarty->assign('table_rows', $data);

   $admin_table = $smarty->fetch('admin_edit_table.tpl');
?>