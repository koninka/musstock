<form method="post" action="/admin/products" class="edit-form">
<table id="admin-table">
  <tbody>
    <tr>
      {foreach from=$tblTitles item=title}
        <th>{$title}</th>
      {/foreach}
      <td></td>
    </tr>
    {foreach from=$tableRows item=row name=tr}
      <tr id="row_{$row[0]}">
        {assign var='idx' value=0}
        {section name=cell loop=$row}
          {assign var='j' value=$smarty.section.cell.index}
          {if $j+$idx < $row|@count}
            {if $refFields[$j+$idx]}
              <td abbr={$row[$j+$idx]}>{$row[$j+$idx+1]}</td>
              {assign var='idx' value=$idx+1}
            {else}
              <td>{$row[$j+$idx]}</td>
            {/if}
          {/if}
        {/section}
        <td class="last"><input type="checkbox" name="del_product[]" value={$row[0]}></td>
      </tr>
    {/foreach}
    <tr>
      {section name=cell loop=$tblTitles|@count}
        <td>
        {if $smarty.section.cell.index + 1 == $tblTitles|@count}
          {if $tableRows|@count}
            <button type="submit" name="submit" value="delete">Удалить</button>
          {/if}
        {/if}
        </td>
      {/section}
      <td class="last"></td>
    </tr>
  </tbody>
</table>
</form>