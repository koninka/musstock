<form method="post" action="/admin/products" class="edit-form">
<table id="admin-table">
  <tbody>
    <tr>
      {for $i=1 to $varArr.tblTitles|@count - 1}
        <th>{$varArr.tblTitles[$i]}</th>
      {/for}
      <td></td>
    </tr>
    {for $i=0 to $varArr.tableRows|@count - 1}
      <tr id="row_{$varArr.tableRows[$i][0]}">
        {assign var='idx' value=0}
        {assign var='row' value=$varArr.tableRows[$i]}
        {section name=cell loop=$row}
          {assign var='j' value=$smarty.section.cell.index}
          {if $j > 0}
            {if $j+$idx < $row|@count}
              {if $varArr.refFields[$j+$idx]}
                <td abbr={$row[$j+$idx]}>{$row[$j+$idx+1]}</td>
                {assign var='idx' value=$idx+1}
              {else}
                <td>{$row[$j+$idx]}</td>
              {/if}
            {/if}
          {/if}
        {/section}
        <td class="last"><input type="checkbox" name="del_product[]" value={$varArr.tableRows[$i][0]}></td>
      </tr>
      {/for}
    <tr>
      {for $i=1 to $varArr.tblTitles|@count - 1}
        <td>
        {if $i + 1 == $varArr.tblTitles|@count}
          {if $varArr.tableRows|@count - 1}
            <button type="submit" name="submit" value="delete">Удалить</button>
          {/if}
        {/if}
        </td>
      {/for}
      <td class="last"></td>
    </tr>
  </tbody>
</table>
</form>