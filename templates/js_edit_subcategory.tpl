{extends file='js_edit.tpl'}
{block name='data_click'}{/block}
{block name='submit_data'}
{literal}
$(document).on('submit', 'form.edit-form', function() {
   var treeOpen = [];
   $('#edit #tbl .jstree-open').each(function (i, v) {
      var li_id = $(this).attr('id');
      var id    = (li_id.substring(li_id.indexOf('_') + 1));
      treeOpen.push(id);
   });
   jThis     = $(this);
   var pid   = jThis.find('select#category_id option:selected').val();
   var aid   = jThis.find('input[name="id"]').val();
   var aname = $('#edit-box input#category').val();
   var acolumns = ['id', 'name', 'category_id'];
   var avalues  = {'id': aid,
                   'parent_id': pid,
                   'name': aname};
   $.post(
         '/includes/edit_admin.php',
         {
           type         : editType,
           id           : aid,
{/literal}
           table        : '{$varArr.table}',
{literal}
           columns      : acolumns,
           values       : avalues,
           del_products : [aid]
         },
         function(data) {
            if (data.result) {
               var jtree = $.jstree._reference('#edit #tbl');
               jtree.destroy();
               $('#tbl').html(data.table);
               $('#edit #tbl')
                  .on('loaded.jstree', function() {
                     var length = treeOpen.length;
                     for (var i = 0; i < length; i++) {
                        jtree.open_node('#category_'+treeOpen[i]);
                     }
                     jtree.open_node('#category_'+pid, -1);
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
                  switch (editType) {
                  case 'add':
                     var li_id = $('#tbl li a:contains("'+aname+'")').parents('li').attr('id');
                     var id = (li_id.substring(li_id.indexOf('_') + 1));
                     $("#category_id").append($('<option value="'+id+'">'+aname+'</option>'));
                     break;
                  case 'delete':
                     $("#category_id option[value="+aid+"]").remove();
                     break;
                  }
               // alert('Успешно!');
            } else {
               alert(data.message);
            }
         },"json"
       );
   return false;
});
{/literal}
{/block}




