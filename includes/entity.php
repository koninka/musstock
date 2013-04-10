<?php
   require_once('settings.php');
   require_once('db_connect.php');

   if (!isset($smarty)) {
      $smarty = new Smarty_Musshop();
   }

   $myGoods = new Goods();
   $myCategory = new Category();
   $myUser = new User();
   print_r($myUser->fieldsHash);
   class Entity
   {
      public
         $tblName = '';

      public
         $fieldsHash = array(),
         $fieldsArr  = array();

      // abstract public function findById();

      function __construct()
      {
         foreach ($this->fieldsArr as $value) {
            $this->fieldsHash[$value['name']] = $value;
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
         $cur_alias = substr($tblName, 0, 1).substr($tblName, strlen($tblName) - 1, strlen($tblName));
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
         $caregories = array();
         foreach ($this->fieldsArr as $value) {
            array_push($selectArr, $value['name']);
            array_push($refFields, intval($value['refKey']));
            if ($value['refKey']) {
               array_push($refFields, 0);
               $c = new $value['refTbl']();
               $data = $c->select(array($value['refField'], $value['refName']), false, PDO::FETCH_ASSOC);
               $categories[$value['name']] = array();
               foreach ($data as $k => $v) {
                  $categories[$value['name']][$v['id']] = $v[$value['refName']];
               }
            }
            // array_push($titlesArr, $value['name']);
            array_push($titlesArr, $value['caption']);
         }
         $data = $this->select($selectArr, true);
         $smarty->assign('tableRows', $data);
         $smarty->assign('tblTitles', $titlesArr);
         $smarty->assign('refFields', $refFields);
         $smarty->assign('table', $this->tblName);
         $editTable = $smarty->fetch('admin_edit_table.tpl');
         if ($isShowTable) {
            $smarty->assign('categories', isset($categories) ? $categories : '');
            $smarty->assign('editTable', $editTable);
            array_shift($selectArr);
            $smarty->assign('selectArr', $selectArr);
            $smarty->assign('links', array('js_edit.tpl'));
            $smarty->assign('container', array(0 => 'admin_products_middle.tpl'));
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
                                 'name'     => 'verification',
                                 'caption'  => 'Подтверждение регистрации',
                                 'type'     => 'varchar',
                                 'refKey'   => false
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




?>