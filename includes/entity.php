   <?php
   require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/settings.php');
   require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/db_connect.php');

   if (!isset($smarty)) {
      $smarty = new Smarty_Musshop();
   }

   $myGoods       = new Goods();
   $myCategory    = new Category();
   $myUser        = new User();
   $myOrders      = new Orders();
   $myOrderGoods  = new Order_Goods();
   $mySubcategory = new Subcategory();

   class Entity
   {
      public
         $tblName = '';

      public
         $fieldsHash = [],
         $fieldsArr  = [];

      function __construct()
      {
         foreach ($this->fieldsArr as &$value) {
            $this->fieldsHash[$value['name']] = &$value;
            if ($value['refKey']) {
               $value['getRefData'] = function() use($value) {
                  $c = new $value['refTbl']();
                  $data = $c->select(['id', $value['refField'], $value['refName']],
                                     [$value['refName']],
                                     false,
                                     PDO::FETCH_ASSOC);
                  $result = [];
                  foreach ($data as $k => $v) {
                     $result[$v[$value['refField']]] = $v[$value['refName']];
                  }
                  return $result;
               };
            }
            $value = &$this->fieldsHash[$value['name']];
         }
      }

      public function edit($columns, $values, $id)
      {
         global $db_link;
         $new_columns = array_map(function($a) {
                                    return "$a=:$a";
                                 }, $columns);
         $query = "UPDATE $this->tblName SET ".implode(', ', $new_columns)." WHERE id=:id";
         $columns[] = 'id';
         $values['id'] = $id;
         $st = $db_link->prepare($query);
         if (!$st) {
            return false;
         }
         bindParams($st, $columns, $values);
         return $st->execute();
      }


      public function add($columns, $values)
      {
         global $db_link;
         $query = "INSERT INTO $this->tblName(".implode(', ', $columns).") VALUES(:".implode(', :', $columns).")";
         $st = $db_link->prepare($query);
         if (!$st) {
            return false;
         }
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
         if (!$st) {
            return false;
         }
         $count = count($values);
         $result = true;
         for ($i = 0; $i < $count && $result; $i++) {
            $result &= $st->bindValue(':id', $values[$i]);
            $st->execute();
         }
         return $result;
      }

      public function select($selectArgs, $ordersArgs, $useRef = false, $selectType = PDO::FETCH_NUM)
      {
         $tblName   = $this->tblName;
         $query     = 'SELECT ';
         $joins     = [];
         $alias     = [];
         $orderIdx  = 0;
         $cur_alias = $tblName;
         foreach ($selectArgs as $key => $column) {
            if ($useRef && $this->fieldsHash[$column]['refKey']) {
               $refTbl = $this->fieldsHash[$column]['refTbl'];
               $new_alias = $refTbl;
               $alias[] = $new_alias;
               $refFld = "$new_alias.".$this->fieldsHash[$column]['refField'];
               $selectArgs[$key] = "$refFld";
               $str = "INNER JOIN $refTbl $new_alias ON $cur_alias.$column = $refFld";
               $joins[] = $str;
               $refFld = "$new_alias.".$this->fieldsHash[$column]['refName'];
               if ($orderIdx < count($ordersArgs) && $selectArgs[$key] == $ordersArgs[$orderIdx]) {
                  $ordersArgs[$orderIdx] = "$refFld";
               }
               $selectArgs[$key] .= ", $refFld";
            } else {
               if ($orderIdx < count($ordersArgs) && $selectArgs[$key] == $ordersArgs[$orderIdx]) {
                  $ordersArgs[$orderIdx] = "$cur_alias.$column";
               }
               $selectArgs[$key] = "$cur_alias.$column";
            }
         }
         $orderStr = count($ordersArgs) ? 'ORDER BY '.implode(', ', $ordersArgs) : '';
         $query .= implode(', ', $selectArgs)." FROM $this->tblName $cur_alias ".implode(' ', $joins).$orderStr;
         global $db_link;
         $st = $db_link->query($query);
         return $st->fetchAll($selectType);
      }

      public function makeEditTable($isShowTable = false)
      {
         global $smarty;
         $selectArr  = [];
         $titlesArr  = [];
         $refFields  = [];
         $categories = [];
         foreach ($this->fieldsArr as $column) {
            $selectArr[] = $column['name'];
            $refFields[] = intval($column['refKey']);
            if ($column['refKey']) {
               $categories[$column['name']] = $column['getRefData']();
               $refFields[] = 0;
            }
            $titlesArr[] = $column['caption'];
         }
         $data = $this->select($selectArr, [], true);
         $varArr = ['tableRows' => $data,
                    'tblTitles' => $titlesArr,
                    'refFields' => $refFields,
                    'table' => $this->tblName];
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
         $fieldsHash = [],
         $fieldsArr  =
            [
               [
                'name'    => 'id',
                'caption' => '№',
                'type'    => 'int',
                'refKey'  => false
               ],
               [
                'name'    => 'marking',
                'caption' => 'Артикул',
                'type'    => 'varchar',
                'refKey'  => false
               ],
               [
                'name'    => 'name',
                'caption' => 'Название',
                'type'    => 'varchar',
                'refKey'  => false
               ],
               [
                'name'     => 'category_id',
                'caption'  => 'Категория',
                'type'     => 'varchar',
                'refKey'   => true,
                'refTbl'   => 'category',
                'refField' => 'id',
                'refName'  => 'name'
               ],
               [
                'name'    => 'amount',
                'caption' => 'Количество',
                'type'    => 'varchar',
                'refKey'  => false
               ],
            ];

      function __construct()
      {
         $this->tblName = 'goods';
         parent::__construct();
         $this->fieldsHash['category_id']['getRefData'] = function() {
            global $db_link;
            $st = $db_link->prepare(
               'SELECT c.id as id, c.name as name FROM subcategory s1 INNER JOIN category c ON c.id = s1.id
               WHERE NOT EXISTS (SELECT * FROM subcategory s2 WHERE s1.id = s2.parent_id AND s2.id <> s2.parent_id)
               ORDER BY c.name');
            $st->execute();
            $data = $st->fetchAll(PDO::FETCH_ASSOC);
            $result = [];
            foreach ($data as $value) {
               $result[$value['id']] = $value['name'];
            }
            return $result;
         };
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
         $fieldsHash = [],
         $fieldsArr  =
            [
               [
                'name'    => 'id',
                'caption' => '№',
                'type'    => 'int',
                'refKey'  => false
               ],
               [
                'name'    => 'name',
                'caption' => 'Категория',
                'type'    => 'varchar',
                'refKey'  => false
               ],
            ];

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
         $fieldsHash = [],
         $fieldsArr  =
            [
               [
                'name'    => 'id',
                'caption' => '№',
                'type'    => 'int',
                'refKey'  => false
               ],
               [
                'name'    => 'name',
                'caption' => 'Имя',
                'type'    => 'Varchar',
                'refKey'  => false
               ],
               [
                'name'    => 'email',
                'caption' => 'Email',
                'type'    => 'varchar',
                'refKey'  => false
               ],
               [
                'name'    => 'login',
                'caption' => 'Логин',
                'type'    => 'varchar',
                'refKey'  => false
               ],
               [
                'name'    => 'password',
                'caption' => 'Хэш пароля',
                'type'    => 'char',
                'refKey'  => false
               ],
               [
                'name'    => 'verification',
                'caption' => 'Подтверждение регистрации',
                'type'    => 'varchar',
                'refKey'  => false
               ],
               [
                'name'    => 'salt',
                'caption' => 'Соль (для хэша)',
                'type'    => 'varchar',
                'refKey'  => false
               ],
               [
                'name'    => 'register_date',
                'caption' => 'Дата регистрации',
                'type'    => 'timestamp',
                'refKey'  => false
               ],
            ];

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
         $fieldsHash = [],
         $fieldsArr  =
            [
               [
                'name'    => 'id',
                'caption' => '№',
                'type'    => 'int',
                'refKey'  => false
               ],
               [
                'name'    => 'order_id',
                'caption' => '№ заказа',
                'type'    => 'int',
                'refKey'  => false
               ],
               [
                'name'     => 'user_id',
                'caption'  => 'Имя пользователя',
                'type'     => 'varchar',
                'refKey'   => true,
                'refTbl'   => 'user',
                'refField' => 'id',
                'refName'  => 'name'
               ],
               [
                'name'    => 'order_date',
                'caption' => 'Дата заказа',
                'type'    => 'timestamp',
                'refKey'  => false
               ]
            ];

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
         $fieldsHash = [],
         $fieldsArr  =
            [
               [
                'name'    => 'id',
                'caption' => '№',
                'type'    => 'int',
                'refKey'  => false
               ],
               [
                'name'     => 'order_id',
                'caption'  => '№ заказа',
                'type'     => 'int',
                'refKey'   => true,
                'refTbl'   => 'orders',
                'refField' => 'id',
                'refName'  => 'order_id'
               ],
               [
                'name'     => 'good_id',
                'caption'  => 'Заказанные товары',
                'type'     => 'int',
                'refKey'   => true,
                'refTbl'   => 'goods',
                'refField' => 'id',
                'refName'  => 'name'
               ],
            ];

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
         $fieldsHash = [],
         $fieldsArr  =
            [
               [
                'name'     => 'id',
                'caption'  => 'Категория',
                'type'     => 'int',
                'refKey'   => true,
                'refTbl'   => 'category',
                'refField' => 'id',
                'refName'  => 'name'
               ],
               [
                'name'     => 'parent_id',
                'caption'  => 'Родительская категория',
                'type'     => 'int',
                'refKey'   => false
               ],
            ];

      function __construct()
      {
         $this->tblName = 'subcategory';
         parent::__construct();
      }

      public function edit($columns, $values, $id)
      {
         global $db_link;
         $st = $db_link->prepare('UPDATE category SET name=? WHERE id=?');
         if (!$st->execute([$values['name'], $values['id']])) {
            return false;
         }
         $st = $db_link->prepare('SELECT id FROM goods WHERE category_id=?');
         if ($st->execute([$values['parent_id']])) {
            $cnt = count($st->fetchAll());
         } else {
            return false;
         }
         if (!$cnt) {
            $p_id = $values['parent_id'] == -1 ? $values['id'] : $values['parent_id'];
            $st = $db_link->prepare('UPDATE subcategory SET parent_id=? WHERE id=?');
            return $st->execute([$p_id, $values['id']]);
         } else {
            return false;
         }
      }

      public function add($columns, $values)
      {
         global $db_link;
         try {
            $db_link->beginTransaction();
            $st = $db_link->prepare('INSERT INTO category(name) VALUES(?)');
            $st->execute([$values['name']]);
            $st = $db_link->prepare("INSERT INTO $this->tblName(id, parent_id) VALUES(?, ?)");
            $id = $db_link->lastInsertId();
            $p_id = $values['parent_id'] == -1 ? $id : $values['parent_id'];
            $st->execute([$id, $p_id]);
            $db_link->commit();
         } catch (Exception $e) {
            $db_link->rollBack();
            return false;
         }
         return true;
      }

      public function delete($values)
      {
         if (!isset($values)) {
            return false;
         }
         global $db_link;
         $st = $db_link->prepare('DELETE FROM category WHERE id=?');
         if (!$st) {
            return false;
         }
         return $st->execute([$values[0]]);
      }

      public function makeEditTable($isShowTable = false)
      {
         global $db_link;
         $result    = $this->select(['id', 'parent_id'], [], false, PDO::FETCH_ASSOC);
         $vertex    = [];
         $sub_trees = [];
         foreach ($result as $k => $v) {
            if ($v['parent_id'] == $v['id']) {
               $vertex[] = $v['parent_id'];
            } else {
               $sub_trees[$v['parent_id']][] = $v['id'];
            }
         }
         $c = new Category();
         $result = $c->select(['id', 'name'], [], false, PDO::FETCH_ASSOC);
         $names  = [];
         foreach ($result as $k => $v) {
            $names[$v['id']][] = $v['name'];
         }
         $leaf = [];
         $buildTree = function(&$t, $id) use(&$buildTree, &$leaf, $sub_trees, $names) {
            $cnt    = 0;
            $t[$id] = [];
            if (isset($sub_trees[$id])) {
               foreach ($sub_trees[$id] as $k => $v) {
                  $buildTree($t[$id], $v);
                  $cnt++;
               }
            }
            if (!$cnt) {
               $leaf[$id] = $names[$id][0];
            }
         };
         foreach ($vertex as $v) {
            $buildTree($tree, $v);
         }
         $buildTree = function($t) use(&$buildTree, $names) {
            if (!count($t)) {
               return '';
            }
            $result = "<ul>\n";
            foreach ($t as $k => $sub) {
               $result .= "<li id='category_$k'><a href='javascript:void(0)'>" . $names[$k][0] . "</a>";
               $result .= $buildTree($sub);
               $result .= "</li>\n";
            }
            $result .= "</ul>\n";
            return $result;
         };
         $result = $buildTree($tree);
         global $smarty;
         $selectArr   = [];
         $titlesArr   = [];
         $refFields   = [];
         $categories  = [];
         $selectArr[] = 'category_id';
         $refFields[] = intval($this->fieldsArr[0]['refKey']);
         if ($this->fieldsArr[0]['refKey']) {
            $categories['category_id'] = $this->fieldsArr[0]['getRefData']();
            $refFields[] = 0;
         }
         $titlesArr[] = 'Родительская категория';
         $varArr = ['selectArr' => $selectArr,
                    'tblTitles' => $titlesArr,
                    'refFields' => $refFields,
                    'categories' => $categories,
                    'table' => $this->tblName];
         if ($isShowTable) {
            $smarty->assign('list', $result);
            $smarty->assign('varArr', $varArr);
            $smarty->assign('caption', 'Добро пожаловать в дерево категорий, котятки!');
         } else {
            return $result;
         }
      }

   }

?>