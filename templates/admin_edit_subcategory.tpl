{extends file='page.2column.tpl'}
{block name='title'}Админка - Редактирование{/block}
{block name='links' append}
  <script type="text/javascript" src="/js/jquery.jstree.js"></script>
  <script type="text/javascript" src="/js/_lib/jquery.cookie.js"></script>
  <script type="text/javascript" src="/js/_lib/jquery.hotkeys.js"></script>
  <script type="text/javascript" src="/js/subcategory.js"></script>
  {include file='js_edit_subcategory.tpl'}
{/block}
{block name='left_column'}
  {include file='admin_menu.tpl'}
  {include file='left_menu.tpl'}
{/block}
{block name='page_title'}Добро пожаловать на страничку редактирования, котятки!{/block}
{block name='center_column'}
  <div id="edit">
    <h2>{$caption}</h2>
    <div>
      <label for="check_box">Редактировать
        <input type="checkbox" value="1000" id="check_box"/>
      </label>
      <div id="tbl">
        {$list}
      </div>
    </div>
  </div>
<div id="edit-box" style="display:none">
  <form method="post" action="/admin/products" class="edit-form">
  <fieldset>
    <legend>Добавление</legend>
    <div id="in_id" style="display: none">
      <label for="id">№:</label>
        <input type="text" name="id" id="id" disabled>
    </div>
    {foreach from=$varArr.selectArr item=item name=field}
      <label for="{$item}">{$varArr.tblTitles[$smarty.foreach.field.index]}</label>
      {if isset($varArr.categories[$item])}
        <select name="{$item}" id="{$item}" style="margin-bottom: 3px">
          <option value="-1">Без родителя</option>
          {html_options options=$varArr.categories[$item]}
        </select>
<!--         <input type="checkbox" id="in_add" name="sumbit_type" value="root" >
        <label for="root">Верхний уровень</label> -->
      {else}
        <input type="text" name="{$item}" id="{$item}">
      {/if}
    {/foreach}
    <label for="category">Категория</label>
      <input type="text" name="category" id="category">
    <button type="submit" name="submit" value="add">Добавить</button>
    <button type="submit" name="submit" value="delete" style="display: none;">Удалить</button>
    <!-- <button type="submit" name="submit" value="delete" style="display: none">Удалить</button> -->
  </fieldset>
  </form>
   <input type="radio" id="in_add" name="sumbit_type" value="add" checked><label for="in_add">Добавление</label>
   <input type="radio" id="in_change" name="sumbit_type" value="change" disabled>
   <label for="in_change">Редактирование</label>
 </div>
{/block}