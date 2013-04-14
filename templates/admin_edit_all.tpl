{extends file='page.2column.tpl'}
{block name='title'}Админка - Редактирование{/block}
{block name='links' append}
	{include file='js_edit.tpl'}
{/block}
{block name='left_column'}
	{include file='admin_menu.tpl'}
	{include file='left_menu.tpl'}
{/block}
{block name='page_title'}Добро пожаловать на страничку редактирования, котятки!{/block}
{block name='center_column'}
<div id="edit">
	<h2>Список товаров</h2>
	<div>
		<label for="check_box">Редактировать
			<input type="checkbox" value="1000" id="check_box"/>
		</label>
		<div id="tbl">
			{$editTable}
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

		{foreach from=$selectArr item=item name=field}
			<label for="{$item}">{$tblTitles[$smarty.foreach.field.iteration]}</label>
			{if isset($categories[$item])}
				<select name="{$item}" id="{$item}" style="display: block; margin-bottom: 3px">
					<option disabled>Выберите категорию</option>
					{html_options options=$categories[$item]}
				</select>
			{else}
				<input type="text" name="{$item}" id="{$item}">
			{/if}
		{/foreach}
		<button type="submit" name="submit" value="add">Добавить</button>
	</fieldset>
	</form>
   <input type="radio" id="in_add" name="sumbit_type" value="add" checked><label for="in_add">Добавление</label>
   <input type="radio" id="in_change" name="sumbit_type" value="change" disabled>
   <label for="in_change">Редактирование</label>
 </div>
{/block}