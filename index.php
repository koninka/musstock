<?php
   error_reporting(E_ALL);

   require_once('includes/settings.php');
   require_once('includes/db_connect.php');
   require_once('includes/entity.php');

   $smarty = new Smarty_Musshop();

   $req = explode('/', substr($_SERVER['REQUEST_URI'], 1));
   switch ($req[0]) {
      case '': case null: case false:
         $smarty->assign('title', 'Musstok - good shop!');
         $smarty->assign('name', 'Katty');
         $smarty->assign('caption', 'Добро пожаловать в магазин, котятки');
         $smarty->assign('container', Array('main.tpl'));
         break;
      case 'admin':
         $smarty->assign('sb_components', Array('admin_menu.tpl', 'left_menu.tpl'));
         switch ($req[1]) {
            case 'goods': case 'products':
               $myGoods->makeEditTable(true);
               break;
            case 'category':
               $myCategory->makeEditTable(true);
               break;
            default:
               $myUser->makeEditTable(true);
               break;
         }
         $smarty->assign('container', array('admin_products_middle.tpl'));
         break;
      default:
         $smarty->assign('caption', 'Ошибка 404');
   }
   $smarty->display('page.tpl');
?>