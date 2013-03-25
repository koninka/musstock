$(document).ready(function() {
	$('#check_box').change(function() {
		var $td = $('#admin-table tr td:last-child');
		if (this.checked == true) {
			$td.css('visibility', 'visible');
			$('#admin-table tr:last-child').css('visibility', 'visible');
			$('#edit-box').css('display', 'block');
		} else {
			$td.css('visibility', 'hidden');
			$('#admin-table tr:last-child').css('visibility', 'hidden');
			$('#edit-box').css('display', 'none');
		}
		return false;
	});

	$(document).on('click', '#admin-table tr td:not(.last)', function() {
		var $td = $('#admin-table tr td:last-child');
		var tr = $(this).parent().get(0);
		if (tr.rowIndex == 0 || (tr.rowIndex == $('#edit table tr').length - 1) || $td.css('display') == 'none') {
			return;
		}
		var $editBox = $('#edit-box');
		$editBox.find('form legend').text('Редактирование');
		$editBox.find('#in_change').removeProp('disabled');
		$editBox.find('#in_add').removeProp('checked');
		$editBox.find('#in_change').prop('checked', 'checked');
		$editBox.find('#in_id').css('display', 'block');
		var $input = $editBox.find('input#id');
		$input.val(tr.cells[0].innerHTML);
		$editBox.find('input#marking').val(tr.cells[1].innerHTML);
		$editBox.find('input#name').val(tr.cells[2].innerHTML);
		$editBox.find('select').prop('selectedIndex', tr.cells[3].abbr);
		$editBox.find('input#amount').val(tr.cells[4].innerHTML);
		$editBox.find('button').prop('value', 'change');
		$editBox.find('button').text('Редактировать');
	});

	$('#edit-box #in_add').change(function() {
		var $editBox = $('#edit-box');
		$editBox.find('form legend').text('Добавление');
		$editBox.find('#in_id').css('display', 'none');
		$editBox.find('button').prop('value', 'add').text('Добавить');
	});

	$('#edit-box #in_change').change(function() {
		var $editBox = $('#edit-box');
		$editBox.find('form legend').text('Редактирование');
		$editBox.find('#in_id').css('display', 'block');
		$editBox.find('button').prop('value', 'change').text('Редактировать');
	});

	$(document).on('submit', 'form.edit-form', function() {
		var c = $('#edit form input:checked');
		var del = []
		$.each(c, function (i, v) {
			del.push(v.value);
		});
		var atype        = $(this).find('button').val();
		var aid          = $(this).find('input[name="id"]').val();
		var amarking     = $(this).find('input[name="marking"]').val();
		var aname        = $(this).find('input[name="name"]').val();
		var acategory_id = $(this).find('select option:selected').val();
		var val          = parseInt($(this).find('input[name="amount"]').val());
		var aamount      = isNaN(val) ? 0 : val;
		$.post(
					'/includes/products_db.php',
					{
						type: 			atype,
						id: 				aid,
						marking: 		amarking,
						name: 			aname,
						category_id: 	acategory_id,
						amount: 			aamount,
						del_products: 	del
					},
					function(data) {
						if (data.result) {
							$('#tbl').html(data.table);
							$('#admin-table tr td:last-child').css('visibility', 'visible');
							$('#admin-table tr:last-child').css('visibility', 'visible');
						} else {
							alert(data.message);
						}
					},
					'json'
				);
		return false;
	});
});
