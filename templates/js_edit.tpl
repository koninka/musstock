<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery('#check_box').change(function() {
      var jQuerytd = jQuery('#admin-table tr td:last-child');
      if (this.checked == true) {
        jQuerytd.css('visibility', 'visible');
        jQuery('#admin-table tr:last-child').css('visibility', 'visible');
        jQuery('#edit-box').css('display', 'block');
      } else {
        jQuerytd.css('visibility', 'hidden');
        jQuery('#admin-table tr:last-child').css('visibility', 'hidden');
        jQuery('#edit-box').css('display', 'none');
      }
      return false;
    });

    jQuery(document).on('click', '#admin-table tr td:not(.last)', function() {
      var jQuerytd = jQuery('#admin-table tr td:last-child');
      var tr = jQuery(this).parent().get(0);
      if (tr.rowIndex == 0 || (tr.rowIndex == jQuery('#edit table tr').length - 1) || jQuerytd.css('display') == 'none') {
        return;
      }
      var jQueryeditBox = jQuery('#edit-box');
      jQueryeditBox.find('form legend').text('Редактирование');
      jQueryeditBox.find('#in_change').removeProp('disabled');
      jQueryeditBox.find('#in_add').removeProp('checked');
      jQueryeditBox.find('#in_change').prop('checked', 'checked');
      jQueryeditBox.find('#in_id').css('display', 'block');
      var jQueryinput = jQueryeditBox.find('input#id');
      var tr_id = $(this).parent().attr('id');
      var id = (tr_id.substring(tr_id.indexOf('_') + 1));
      jQueryinput.val(id);
      {foreach from=$selectArr item=item name=field}
        {assign var='idx' value=$smarty.foreach.field.index}
        {if isset($categories[$item])}
          jQueryeditBox.find("select#{$item} option[value='"+tr.cells[{$idx}].abbr+"']")
                       .prop("selected", "selected");
        {else}
          jQueryeditBox.find('input#{$item}').val(tr.cells[{$idx}].innerHTML);
        {/if}
      {/foreach}
      jQueryeditBox.find('button').prop('value', 'change');
      jQueryeditBox.find('button').text('Редактировать');
    });

    jQuery('#edit-box #in_add').change(function() {
      var jQueryeditBox = jQuery('#edit-box');
      jQueryeditBox.find('form legend').text('Добавление');
      jQueryeditBox.find('#in_id').css('display', 'none');
      jQueryeditBox.find('button').prop('value', 'add').text('Добавить');
    });

    jQuery('#edit-box #in_change').change(function() {
      var jQueryeditBox = jQuery('#edit-box');
      jQueryeditBox.find('form legend').text('Редактирование');
      jQueryeditBox.find('#in_id').css('display', 'block');
      jQueryeditBox.find('button').prop('value', 'change').text('Редактировать');
    });

    jQuery(document).on('submit', 'form.edit-form', function() {
      var c = jQuery('#edit form input:checked');
      var del = []
      jQuery.each(c, function (i, v) {
        del.push(v.value);
      });
      jThis = jQuery(this);
      var acolumns = [];
      var avalues  = {};
      {foreach from=$selectArr item=item name=field}
        acolumns.push('{$item}');
        {if isset($categories[$item])}
          avalues['{$item}'] = jThis.find('select#{$item} option:selected').val();
        {else}
          avalues['{$item}'] = jThis.find('input[name="{$item}"]').val();
        {/if}
      {/foreach}
      var atype = jThis.find('button').val();
      var aid   = jThis.find('input[name="id"]').val();
      jQuery.post(
            '/includes/edit_admin.php',
            {
              type:         atype,
              id:           aid,
              table:        '{$table}',
              columns:      acolumns,
              values:       avalues,
              del_products: del
            },
            function(data) {
              if (data.result) {
                jQuery('#tbl').html(data.table);
                jQuery('#admin-table tr td:last-child').css('visibility', 'visible');
                jQuery('#admin-table tr:last-child').css('visibility', 'visible');
                alert('Успешно!');
              } else {
                alert(data.message);
              }
            },
            "json"
          );
      return false;
    });
  });
</script>
