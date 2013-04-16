<?php
   require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/settings.php');
   require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/db_connect.php');

   if (!isset($smarty)) {
      $smarty = new Smarty_Musshop();
   }

   $myGoods = new Goods();
   $myCategory = new Category();
   $myUser = new User();
   $myOrders = new Orders();
   $myOrderGoods = new Order_Goods();
   class Entity
   {
      public
         $tblName = '';

      public
         $fieldsHash = array(),
         $fieldsArr  = array();

      function __construct()
      {
         foreach ($this->fieldsArr as &$value) {
            $this->fieldsHash[$value['name']] = &$value;
            if ($value['refKey']) {
               $value['getRefData'] = function() use($value) {
                  $c = new $value['refTbl']();
                  $data = $c->select(array('id', $value['refField'], $value['refName']), false, PDO::FETCH_ASSOC);
                  $result = array();
                  foreach ($data as $k => $v) {
                     $result[$v[$value['refField']]] = $v[$value['refName']];
                  }
                  return $result;
               };
            }
         }
      }

      public function edit($columns, $values, $id)
      {
         global $db_link;
         function callback($a) {
            return "$a=:$a";
         };
         $new_columns = array_map('callback', $columns);
         $query = "UPDATE $this->tblName SET ".implode(', ', $new_columns)." WHERE id=:id";
         array_push($columns, 'id');
         $values['id'] = $id;
         $st = $db_link->prepare($query);
         if (!$st) return false;
         bindParams($st, $columns, $values);
         return $st->execute();
      }


      public function add($columns, $values)
      {
         global $db_link;
         $query = "INSERT INTO $this->tblName(".implode(', ', $columns).") VALUES(:".implode(', :', $columns).")";
         $st = $db_link->prepare($query);
         if (!$st) return false;
         bindParams($st, $columns, $values);
         return $st->execute();
      }

      public function delete($values)
      {
         if (!isset($values)) {
            return false;
         }
         global $db_link;
         $st = $db_link->prepare("DELETE FROM $this->tblName WHERE id=:id");
         if (!$st) return false;
         $count = count($values);
         $result = true;
         for ($i = 0; $i < $count && $result; $i++) {
            $result &= $st->bindValue(':id', $values[$i]);
            $st->execute();
         }
         return $result;
      }

      public function select($selectArgs, $useRef = false, $selectType = PDO::FETCH_NUM)
      {
         $tblName   = $this->tblName;
         $query     = 'SELECT ';
         $joins     = array();
         $alias     = array();
         $cur_alias = substr($tblName, 0, 1).substr($tblName, strlen($tblName) - 2, strlen($tblName));
         foreach ($selectArgs as $key => $value) {
            if ($useRef && $this->fieldsHash[$value]['refKey']) {
               $refTbl = $this->fieldsHash[$value]['refTbl'];
               $new_alias = substr($refTbl, 0, 1).substr($refTbl, strlen($refTbl) - 1, strlen($refTbl));
               array_push($alias, $new_alias);
               $refFld = "$new_alias.".$this->fieldsHash[$value]['refField'];
               $selectArgs[$key] = "$refFld";
               $str = "INNER JOIN $refTbl $new_alias ON $cur_alias.$value = $refFld";
               array_push($joins, $str);
               $refFld = "$new_alias.".$this->fieldsHash[$value]['refName'];
               $selectArgs[$key] .= ", $refFld";
            } else {
               $selectArgs[$key] = "$cur_alias.$value";
            }
         }
         $query .= implode(', ', $selectArgs)." FROM $this->tblName $cur_alias ".implode(' ', $joins);
         global $db_link;
         $st = $db_link->query($query);
         return $st->fetchAll($selectType);
      }

      public function makeEditTable($isShowTable = false)
      {
         global $smarty;
         $selectArr  = array();
         $titlesArr  = array();
         $refFields  = array();
         $categories = array();
         foreach ($this->fieldsArr as $value) {
            array_push($selectArr, $value['name']);
            array_push($refFields, intval($value['refKey']));
            if ($value['refKey']) {
               $categories[$value['name']] = $value['getRefData']();
               array_push($refFields, 0);
            }
            array_push($titlesArr, $value['caption']);
         }
         $data = $this->select($selectArr, true);
         $varArr = array('tableRows' => $data,
                         'tblTitles' => $titlesArr,
                         'refFields' => $refFields,
                         'table' => $this->tblName);
         $smarty->assign('varArr', $varArr);
         $editTable = $smarty->fetch('admin_edit_table.tpl');
         if ($isShowTable) {
            $varArr['categories'] = isset($categories) ? $categories : '';
            $varArr['editTable'] = $editTable;
            array_shift($selectArr);
            $varArr['selectArr'] = $selectArr;
            $smarty->assign('varArr', $varArr);
         } else {
            return $editTable;
         }
      }

   }

   class Goods extends Entity
   {
      public
         $_id          = 'id',
         $_marking     = 'marking',
         $_name        = 'name',
         $_category_id = 'category_id',
         $_amount      = 'amount';

      public
         $fieldsHash = array(),
         $fieldsArr  = array(
            array(
                  'name'    => 'id',
                  'caption' => '№',
                  'type'    => 'int',
                  'refKey'  => false
                  ),
            array(
                  'name'    => 'marking',
                  'caption' => 'Артикул',
                  'type'    => 'varchar',
                  'refKey'  => false
                  ),
            array(
                  'name'    => 'name',
                  'caption' => 'Название',
                  'type'    => 'varchar',
                  'refKey'  => false
                  ),
            array(
                  'name'     => 'category_id',
                  'caption'  => 'Категория',
                  'type'     => 'varchar',
                  'refKey'   => true,
                  'refTbl'   => 'category',
                  'refField' => 'id',
                  'refName'  => 'name'
                  ),
            array(
                  'name'    => 'amount',
                  'caption' => 'Количество',
                  'type'    => 'varchar',
                  'refKey'  => false
                  ),
             );

      function __construct()
      {
         $this->tblName = 'goods';
         parent::__construct();
      }

      public function makeEditTable($isShowTable = false)
      {
         global $smarty;
         if ($isShowTable) {
            parent::makeEditTable(true);
            $smarty->assign('caption', 'Добро пожаловать на страничку товаров, котятки');
         } else {
            return parent::makeEditTable();
         }
      }

   }

   class Category extends Entity
   {
      public
         $fieldsHash = array(),
         $fieldsArr  = array(
            array(
                  'name'    => 'id',
                  'caption' => '№',
                  'type'    => 'int',
                  'refKey'  => false
                  ),
            array(
                  'name'    => 'name',
                  'caption' => 'Категория',
                  'type'    => 'varchar',
                  'refKey'  => false
                  ),
             );

      function __construct()
      {
         $this->tblName = 'category';
         parent::__construct();
      }

      public function makeEditTable($isShowTable = false)
      {
         global $smarty;
         if ($isShowTable) {
            parent::makeEditTable($isShowTable);
            $smarty->assign('caption', 'Добро пожаловать на страничку категорий, котятки');
         } else {
            return parent::makeEditTable();
         }
      }

   }


   class User extends Entity
   {

      public
         $fieldsHash = array(),
         $fieldsArr  = array(
            array(
                  'name'    => 'id',
                  'caption' => '№',
                  'type'    => 'int',
                  'refKey'  => false
                  ),
            array(
                  'name'    => 'name',
                  'caption' => 'Имя',
                  'type'    => 'Varchar',
                  'refKey'  => false
                  ),
            array(
                  'name'    => 'email',
                  'caption' => 'Email',
                  'type'    => 'varchar',
                  'refKey'  => false
                  ),
            array(
                  'name'    => 'login',
                  'caption' => 'Логин',
                  'type'    => 'varchar',
                  'refKey'  => false
                  ),
            array(
                  'name'    => 'password',
                  'caption' => 'Хэш пароля',
                  'type'    => 'char',
                  'refKey'  => false
                  ),
            array(
                  'name'    => 'verification',
                  'caption' => 'Подтверждение регистрации',
                  'type'    => 'varchar',
                  'refKey'  => false
                  ),
            array(
                  'name'    => 'salt',
                  'caption' => 'Соль (для хэша)',
                  'type'    => 'varchar',
                  'refKey'  => false
                  ),
            array(
                  'name'    => 'register_date',
                  'caption' => 'Дата регистрации',
                  'type'    => 'timestamp',
                  'refKey'  => false
                  ),
             );

      function __construct()
      {
         $this->tblName = 'user';
         parent::__construct();
      }

      public function makeEditTable($isShowTable = false)
      {
         global $smarty;
         if ($isShowTable) {
            parent::makeEditTable($isShowTable);
            $smarty->assign('caption', 'Добро пожаловать на страничку пользователей, котятки');
         } else {
            return parent::makeEditTable();
         }
      }

   }

   class Orders extends Entity
   {
      public
         $fieldsHash = array(),
         $fieldsArr  = array(
            array(
                  'name'    => 'id',
                  'caption' => '№',
                  'type'    => 'int',
                  'refKey'  => false
                  ),
            array(
                  'name'    => 'order_id',
                  'caption' => '№ заказа',
                  'type'    => 'int',
                  'refKey'  => false
                  ),
            array(
                  'name'     => 'user_id',
                  'caption'  => 'Имя пользователя',
                  'type'     => 'varchar',
                  'refKey'   => true,
                  'refTbl'   => 'user',
                  'refField' => 'id',
                  'refName'  => 'name'
                  ),
            array(
                  'name'    => 'order_date',
                  'caption' => 'Дата заказа',
                  'type'    => 'timestamp',
                  'refKey'  => false
                  ),
             );

      function __construct()
      {
         $this->tblName = 'orders';
         parent::__construct();
      }

      public function makeEditTable($isShowTable = false)
      {
         global $smarty;
         if ($isShowTable) {
            parent::makeEditTable($isShowTable);
            $smarty->assign('caption', 'Добро пожаловать на страничку заказов, котятки');
         } else {
            return parent::makeEditTable();
         }
      }

   }

   class Order_goods extends Entity
   {
      public
         $fieldsHash = array(),
         $fieldsArr  = array(
            array(
                  'name'    => 'id',
                  'caption' => '№',
                  'type'    => 'int',
                  'refKey'  => false
                  ),
            array(
                  'name'     => 'order_id',
                  'caption'  => '№ заказа',
                  'type'     => 'int',
                  'refKey'   => true,
                  'refTbl'   => 'orders',
                  'refField' => 'id',
                  'refName'  => 'order_id'
                  ),
            array(
                  'name'     => 'good_id',
                  'caption'  => 'Заказанные товары',
                  'type'     => 'int',
                  'refKey'   => true,
                  'refTbl'   => 'goods',
                  'refField' => 'id',
                  'refName'  => 'name'
                  ),
             );

      function __construct()
      {
         $this->tblName = 'order_goods';
         parent::__construct();
      }

      public function makeEditTable($isShowTable = false)
      {
         global $smarty;
         if ($isShowTable) {
            parent::makeEditTable($isShowTable);
            $smarty->assign('caption', 'Добро пожаловать на страничку заказов, котятки');
         } else {
            return parent::makeEditTable();
         }
      }

   }

   class Subcategory extends Entity
   {
      public
         $fieldsHash = array(),
         $fieldsArr  = array(
            array(
                  'name'     => 'id',
                  'caption'  => 'Категория',
                  'type'     => 'int',
                  'refKey'   => true,
                  'refTbl'   => 'category',
                  'refField' => 'id',
                  'refName'  => 'name'
                  ),
            array(
                  'name'     => 'parent_id',
                  'caption'  => 'Родительская категория',
                  'type'     => 'int',
                  'refKey'   => false
                  ),
             );

      function __construct()
      {
         $this->tblName = 'Subcategory';
         parent::__construct();
      }

      public function makeEditTable($isShowTable = false)
      {
         global $smarty;
         if ($isShowTable) {
            parent::makeEditTable($isShowTable);
            $smarty->assign('caption', 'Добро пожаловать на страничку заказов, котятки');
         } else {
            return parent::makeEditTable();
         }
      }

   }

?>