$(function(){
	csrf =  $("input[name=csrf_test_name]").val()
	base_url = $('#base_url').val()
	var $this = $('#item_sku')
	$('#item_sku').autocomplete({
		delay: 1000,	
		autoFocus: true,
		open: function() { 
			$('.ui-menu').width($('#item_sku').width());
			$('.ui-menu').height(150);
			$(".ui-menu").css({"font-size": "12px"})
		},
		focus: function( event, ui ) {
			//$('html,body').animate({scrollTop: $(this).offset().top},1000);
		},
		source: function (request, response) {
			$.ajax({ 
				url: $('#transfer_url').val(),
				data: { term: $("#item_sku").val(), outlet_id:$('#dest_outlet_id').val(), csrf_test_name : $("input[name=csrf_test_name]").val()},
				dataType: "json",
				type: "POST",
				success: function(data){
					response($.map(data, function (v,i) {
					   return {
						   label: v.prod_name+' | SKU: '+v.sku,
						   value: v.prod_name,
						   source_stock: v.source_stock,
						   product_id: v.product_id,
						   sku: v.sku,
						   supplier_price: v.supplier_price,
						   reorder_qty: v.reorder_qty,
						   related_product: v.related_product
					   };
				   }));
				},
				error: function(xhr,textStatus, errorThrown) {
					alert(errorThrown);
				} 		
			 });
		},
		select: function(event, ui) { 
			event.preventDefault();
			var prod_name = ui.item.value
			var source_stock = ui.item.source_stock
			var product_id = ui.item.product_id
			var sku = ui.item.sku
			var supplier_price = Number(ui.item.supplier_price)
			var transfer_parent_id = $('#transfer_parent_id').val()
			var reorder_qty = ui.item.reorder_qty
			$.ajax({ 
				url: $('#ins_transfer_url').val(),
				data: { params: [product_id,transfer_parent_id,supplier_price,reorder_qty], csrf_test_name : $("input[name=csrf_test_name]").val()},
				type: "POST",
				success: function(data)
				{
					if(data == 0)
					{
						alert('Oops! Something messed up. Please try again')	
					} else if(data == 2){
						alert('This Product is already added for current transfer')
					} else if(data == 3){
						alert('Maximum of only 500 products allowed per transfer')
					} else {
						var elm = '<tr><td><a class = "btn btn-xs btn-default" href="'+base_url+'products/'+product_id+'">'+prod_name+'</a>';
						elm += '<input type="hidden" name="transfer[child_id][]" class="transfer_child_id" value="'+data+'">';
						elm += '</td>';
						elm += '<td>'+Number(source_stock)+'</td>'
						elm += '<td align="center"><input type="text" name="transfer[ordered][]" autocomplete="off" class="form-control input-sm all_ordered" size="5" value="'+reorder_qty+'"></td>'
						elm += '<td align="center"><input type="text" name="transfer[supp_price][]" autocomplete="off" class="form-control input-sm all_supplier" size="5" value="'+supplier_price+'"></td>'
						elm += '<td class="total_trx">'+Number(supplier_price)+'</td>'
						elm += '<td align="center"><button type="button" class="btn btn-xs btn-danger && del_row" title="Remove"><i class="fa fa-times"></i></button></td>'
						elm += '</tr>'			
						if($('.dyn_add > tbody > tr').length > 0)
						{
							$('.dyn_add > tbody > tr:first').before($(elm))
						} else {
							$('.dyn_add tbody').append($(elm))
						}
			
						$('html,body').animate({scrollTop: $this.offset().top},1000);
					}
					$("#item_sku").val('').focus()
				},
				error: function(xhr,textStatus, errorThrown) {
					alert(errorThrown);
					//console.log(xhr.responseText);
				} 		
			});
		}
	});
	$(document).on('click','.del_row',function(){
		var id = $(this).parent().parent().find('.transfer_child_id').val()
		var $this = $(this)
		$this.hide()
		$this.parent().parent().closest('tr').find('td:last').append('<i class="fa fa-circle-o-notch fa-spin fa-2x fa-fw"></i>')
		$this.parent().parent().closest('tr').find('td:not(:last-child)').css({'background' : '#fff'})
		$this.parent().parent().closest('tr').find('td:last').css({'background-color': 'rgba(255,0,0,0.4)'})
		$.ajax({ 
			url: $('#transfer_del_url').val(),
			data: { child_id: id, csrf_test_name: csrf},
			type: "POST",
			context: $this,
			success: function(data){
				if(data == 1)
				{
					$this.parent().parent().closest('tr').hide(1000,function(){
						$this.parent().parent().closest('tr').remove();										
					});					
				} else {
					$this.parent().parent().closest('tr').find('td:last').append('Oops!')
					$this.parent().parent().closest('tr').find('td:last').css({'background-color': 'rgba(255,0,0,0.4)'})
					$this.hide()
				}
			},
			error: function(xhr,textStatus, errorThrown) {
				alert(errorThrown);
			}
		});
		return false
	});
	$(document).on('keyup','.all_ordered',function(){
		var ordered = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val())
		var supplier = isNaN(parseFloat($(this).parent().next().find('.all_supplier').val())) ? 0 : parseFloat($(this).parent().next().find('.all_supplier').val())
		tot = addCommas((ordered * supplier).toFixed(2))
		$(this).parent().parent().find('.total_trx').html(tot)
	});
	$(document).on('keyup','.all_supplier',function(){
		var supplier = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val())
		var ordered = isNaN(parseFloat($(this).parent().prev().find('.all_ordered').val())) ? 0 : parseFloat($(this).parent().prev().find('.all_ordered').val())
		tot = (ordered * supplier).toFixed(2)
		$(this).parent().parent().find('.total_trx').html(tot)
	});
});
function addCommas(nStr) {
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
