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