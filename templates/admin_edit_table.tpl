<form method="post" action="/admin/products" class="edit-form">
<table id="admin-table">
  <tbody>
    <tr>
      {for $i=1 to $tblTitles|@count - 1}
        <th>{$tblTitles[$i]}</th>
      {/for}
      <td></td>
    </tr>
    {for $i=0 to $tableRows|@count - 1}
      <tr id="row_{$tableRows[$i][0]}">
        {assign var='idx' value=0}
        {assign var='row' value=$tableRows[$i]}
        {section name=cell loop=$row}
          {assign var='j' value=$smarty.section.cell.index}
          {if $j > 0}
            {if $j+$idx < $row|@count}
              {if $refFields[$j+$idx]}
                <td abbr={$row[$j+$idx]}>{$row[$j+$idx+1]}</td>
                {assign var='idx' value=$idx+1}
              {else}
                <td>{$row[$j+$idx]}</td>
              {/if}
            {/if}
          {/if}
        {/section}
        <td class="last"><input type="checkbox" name="del_product[]" value={$tableRows[$i][0]}></td>
      </tr>
      {/for}
    <tr>
      {for $i=1 to $tblTitles|@count - 1}
        <td>
        {if $i + 1 == $tblTitles|@count}
          {if $tableRows|@count - 1}
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