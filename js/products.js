$(document).ready(function() {
	$('#check_box').change(function() {
		var $td = $("#admin-table tr td:last-child");
		if (this.checked == true) {
			$td.css("visibility", "visible");
			$("#admin-table tr:last-child").css("visibility", "visible");
			$("#edit-box").css("display", "block");
		} else {
			$td.css("visibility", "hidden");
			$("#admin-table tr:last-child").css("visibility", "hidden");
			$("#edit-box").css("display", "none");
		}
		return false;
	});

	$(document).on('click', '#admin-table tr td:not(.last)', function() {
		var $td = $("#admin-table tr td:last-child");
		var tr = $(this).parent().get(0);
		if (tr.rowIndex == 0 || (tr.rowIndex == $("#edit table tr").length - 1) || $td.css("display") == "none") {
			return;
		}
		$("#edit-box form legend").text('Редактирование');
		$("#edit-box #in_change").removeProp('disabled');
		$("#edit-box #in_add").removeProp("checked");
		$("#edit-box #in_change").prop('checked', 'checked');
		$("#edit-box #in_id").css('display', 'block');
		var $input = $("#edit-box input#id");
		$input.val(tr.cells[0].innerHTML);
		$("#edit-box input#marking").val(tr.cells[1].innerHTML);
		$("#edit-box input#name").val(tr.cells[2].innerHTML);
		$("#edit-box select").prop('selectedIndex', tr.cells[3].abbr);
		$("#edit-box input#amount").val(tr.cells[4].innerHTML);
		$("#edit-box button").prop('value', 'change');
		$("#edit-box button").text('Редактировать');
	});

	$("#edit-box #in_add").change(function() {
		$("#edit-box form legend").text('Добавление');
		$("#edit-box #in_id").css('display', 'none');
		$("#edit-box button").prop('value', 'add');
		$("#edit-box button").text('Добавить');
	});

	$("#edit-box #in_change").change(function() {
		$("#edit-box form legend").text('Редактирование');
		$("#edit-box #in_id").css('display', 'block');
		$("#edit-box button").prop('value', 'change');
		$("#edit-box button").text('Редактировать');
	});

	$(document).on('submit', 'form.edit-form', function() {
		var c = $("#edit form input:checked");
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
					"json"
				);
		return false;
	});
});
