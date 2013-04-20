var editType = 'change';

$(document).ready(function() {
   $('#edit #tbl').bind("select_node.jstree", function(e, data) {
      $(this).jstree("toggle_node", data.rslt.obj);
      $(this).jstree("deselect_node", data.rslt.obj);
   })
   .jstree({
      "core"   : {
         "animation" : 180
      },
      "themes" : {
         "theme" : "default",
         "icons" : false
      },
      "plugins"  : ["themes", "html_data", "sort"]
   });
});

function toggleOption(newId, parentId) {
   $('#edit-box select#category_id option').prop('disabled', false);
   var $box = $('#edit-box select#category_id');
   $box.children('option[value="'+newId+'"]').prop('disabled', true);
   $box.children('option[value="'+parentId+'"]').prop("selected", "selected");
   $('#edit #tbl li#category_'+newId).find('li').each(function(idx, el) {
      var li_id = $(this).attr('id');
      var id = (li_id.substring(li_id.indexOf('_') + 1));
      $('#edit-box select#category_id option[value="'+id+'"]').prop('disabled', true);
   });
}

$(document).on('click', '#tbl ul li', function(e) {
   toggleOption();
   var $editBox = $('#edit-box');
   $editBox.find('form legend').text('Редактирование');
   $editBox.find('#in_change').removeProp('disabled');
   $editBox.find('#in_add').removeProp('checked');
   $editBox.find('#in_change').prop('checked', 'checked');
   $editBox.find('#in_id').css('display', 'block');
   var li_id = $(this).attr('id');
   var id = (li_id.substring(li_id.indexOf('_') + 1));
   var p_id = $($(this).parents().get(1)).attr('id');
   p_id = (p_id.substring(p_id.indexOf('_') + 1));
   if (p_id == 'tbl') {
      p_id = -1;
   }
   toggleOption(id, p_id);
   $('#edit-box input#category').val($("select#category_id option[value='"+id+"']").text());
   $('#edit-box input#id').val(id);
   $('#edit-box').find('button[value="add"]').text('Редактировать');
   $('#edit-box').find('button[value="add"]').prop('value', 'change');
   $('#edit-box').find('button[value="delete"]').css('display', 'inline-block');
   e.stopPropagation();
});

$(document).on('click', '#edit-box button', function(e) {
   editType = $(this).val();
});
