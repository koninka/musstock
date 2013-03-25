<?php
   require_once('settings.php');
   require_once('db_connect.php');

   if (!isset($smarty)) {
      $smarty = new Smarty_Musshop();
   }

   $query = 'SELECT p.id, p.marking, p.name, c.name as category_name, c.id as category_id, p.amount
                           FROM products p INNER JOIN category c ON p.category_id = c.id ORDER BY p.id';

   $st = $db_link->query($query);
   $data = $st->fetchAll(PDO::FETCH_ASSOC);
   $smarty->assign('table_rows', $data);

   $products_table = $smarty->fetch('admin_table.tpl');
?>