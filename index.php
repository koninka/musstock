<?php
   error_reporting(E_ALL);
   require_once('includes/settings.php');
   require_once('includes/db_connect.php');
   require_once('includes/entity.php');

   $smarty = new Smarty_Musshop();
   $smarty->force_compile = true;

   $req = explode('/', substr($_SERVER['REQUEST_URI'], 1));
   switch ($req[0]) {
      case '': case null: case false:
         // $smarty->display('index.tpl');
         break;
      case 'admin':
         switch ($req[1]) {
            case 'goods': case 'products':
               $myGoods->makeEditTable(true);
               $smarty->display('admin_edit_all.tpl');
               break;
            case 'category':
               $myCategory->makeEditTable(true);
               $smarty->display('admin_edit_all.tpl');
               break;
            case 'order_goods':
               $myOrderGoods->makeEditTable(true);
               $smarty->display('admin_edit_all.tpl');
               break;
            case 'orders':
               $myOrders->makeEditTable(true);
               $smarty->display('admin_edit_all.tpl');
               break;
            case 'subcategory':
               $mySubcategory->makeEditTable(true);
               $smarty->display('admin_edit_subcategory.tpl');
               break;
            default:
               $myUser->makeEditTable(true);
               $smarty->display('admin_edit_all.tpl');
               break;
         }
         break;
      default:
         $smarty->assign('caption', 'Ошибка 404');
   }
?>