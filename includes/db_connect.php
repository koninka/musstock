<?php
	$db_link = new PDO(
							 'mysql:host='.$settings->db->host.';dbname='.$settings->db->database,
							 $settings->db->user,
							 $settings->db->password
							);
	$db_link->exec("SET CHARACTER SET utf8");

   function bindParams(&$h, $cols, $vals)
   {
      foreach ($cols as $k => $v) {
         $h->bindValue(":$v", $vals[$v]);
      }
   }
?>