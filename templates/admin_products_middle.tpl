<div id="edit">
	<h2>Список товаров</h2>
	<div>
		<label for="check_box">Редактировать
			<input type="checkbox" value="1000" id="check_box"/>
		</label>
		<div id="tbl">
			{$products_table}
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