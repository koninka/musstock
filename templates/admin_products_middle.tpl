{* Smarty *}
Hello {$name|default:'Katty'}, welcome to Smarty!
<div id="edit">
	<h2>Список товаров</h2>
	<div>
		<label for="check_box">Редактировать
			<input type="checkbox" value="1000" id="check_box"/>
		</label>
		<form method="post" action="/admin/products" class="edit-form">
		<table id="admin-table">
			<tbody>
				<tr>
					<th>№</th>
					<th>Артикул</th>
					<th>Название</th>
					<th>Категория</th>
					<th>Количество</th>
					<td></td>
				</tr>
				{foreach from=$table_rows item=row}
			    <tr id="row_{$row.id}">
			    	<td>{$row.id}</td>
			    	<td>{$row.marking}</td>
			    	<td>{$row.name}</td>
						<td abbr={$row.category_id}>{$row.category_name}</td>
						<td>{$row.amount}</td>
						<td class="last"><input type="checkbox" name="del_product[]" value={$row.id}></td>
		    	</tr>
				{/foreach}
				<tr>
					<td></td><td></td><td></td><td></td>
					<td><button type="submit" name="submit" value="delete">Удалить</button></td>
					<td class="last"></td>
				</tr>
			</tbody>
		</table>
		</form>
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
		<label for="marking">Артикул:</label>
			<input type="text" name="marking" id="marking">
		<label for="name">Название товара:</label>
			<input type="text" name="name" id="name">
		<label for="category_id">Категория:</label>
			<select name="category_id" id="category_id">
				<option disabled>Выберите категорию</option>
				{html_options options=$categories}
			</select>
		<label for="amount">Количество:</label>
			<input type="text" name="amount" id="amount">
				<button type="submit" name="submit" value="add">Добавить</button>
	</fieldset>
	</form>
   <input type="radio" id="in_add" name="sumbit_type" value="add" checked><label for="in_add">Добавление</label>
   <input type="radio" id="in_change" name="sumbit_type" value="change" disabled>
   <label for="in_change">Редактирование</label>