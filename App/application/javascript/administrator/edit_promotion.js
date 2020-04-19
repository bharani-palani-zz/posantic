$(function(){
	csrf =  $("input[name=csrf_test_name]").val()
	base_url = $('#base_url').val()
	$("#item_sku").focus()
	$( "#promo_start ,#promo_end" ).datepicker({  
  			//maxDate: new Date(),
			minDate: new Date(),
			dateFormat: 'yy-mm-dd',  
			changeYear: true,
			changeMonth: true,
			beforeShow: function(){    
				$(".ui-datepicker").css({'font-size' : 12}) 
				$(".ui-state-highlight").css({'background' : '#fc9900'}) 
			}
	});	
	$(document).on('click','.del_row',function(){
		var id = $(this).parent().parent().find('.promotions_child_id').val()
		var $this = $(this)
		$this.hide()
		$this.parent().parent().closest('tr').find('td:last').append('<i class="fa fa-refresh fa-spin fa-2x fa-fw"></i>')
		$this.parent().parent().closest('tr').find('td:not(:last-child)').css({'background' : '#fff'})
		$this.parent().parent().closest('tr').find('td:last').css({'background-color': 'rgba(201,048,044,0.7)'})
		$.ajax({ 
			url: $('#prom_del_url').val(),
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
	$('#item_sku').autocomplete({
		delay: 1000,	
		autoFocus: true,
		open: function() { 
			$('#item_sku').width();
			$('.ui-menu').height(150);
			$(".ui-menu").css({"font-size": "12px"})
		},
		focus: function( event, ui ) {
			//$('html,body').animate({scrollTop: $(this).offset().top},1000);
		},
		source: function (request, response) {
			$.ajax({ 
				url: $('#prom_url').val(),
				data: { term: $("#item_sku").val(), csrf_test_name : $("input[name=csrf_test_name]").val()},
				dataType: "json",
				type: "POST",
				success: function(data){
					response($.map(data, function (v,i) {
					   return {
						   label: v.product_name+' | SKU: '+v.sku,
						   value: v.product_name,
						   product_id: v.product_id,
						   related_product: v.related_product,
						   sku: v.sku,
						   loyalty: v.loyalty,
						   margin: v.margin,
						   supplier_price: v.price,
						   retail_price: v.retail_price,
						   discount: 0,
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
			var item_name = ui.item.value
			var product_id = ui.item.product_id
			var related_product = ui.item.related_product
			var sku = ui.item.sku
			var margin = ui.item.margin
			var discount = ui.item.discount
			var supplier_price = Number(ui.item.supplier_price)
			var retail_price = ui.item.retail_price
			var loyalty = ui.item.loyalty
			var promotions_parent_id = $('#promotions_parent_id').val()
			
			$.ajax({ 
				url: $('#ins_prom_url').val(),
				data: { params: [product_id,margin, discount,retail_price,loyalty,promotions_parent_id,related_product], csrf_test_name : $("input[name=csrf_test_name]").val()},
				type: "POST",
				success: function(data){
					if(data == 0)
					{
						alert('Oops! Something messed up. Please try again')	
					} else if(data == 2){
						alert('This Product is already added for current promotion')
					} else {
						var elm = '<tr><td><a href="'+base_url+'products/'+product_id+'" class="btn btn-xs btn-default">'+item_name+'</a>';
						elm += '<input type="hidden" name="promotions[child_id][]" class="promotions_child_id" value="'+data+'">';
						elm += '<input type="hidden" name="promotions[product_id][]" class="promotions_product_id" value="'+product_id+'">';
						elm += '<input type="hidden" class="all_supp" value="'+supplier_price+'">';
						elm += '</td>';
						elm += '<td>'+addCommas(Number(supplier_price).toFixed(2))+'</td>'
						elm += '<td><input type="text" name="promotions[promo_margin][]" class="form-control input-sm all_margin" size="5" value="'+margin+'"></td>'
						elm += '<td><input type="text" name="promotions[promo_disc][]" class="form-control input-sm all_discount" size="5" value="0"></td>'
						elm += '<td><input type="text" name="promotions[promo_mrp][]" class="form-control input-sm all_retail" size="5" value="'+retail_price+'"></td>'
						elm += '<td><input type="text" name="promotions[promo_loyalty][]" class="form-control input-sm" size="5" value="'+loyalty+'"></td>'
						elm += '<td><input type="text" name="promotions[promo_min_units][]" class="form-control input-sm all_min" size="5" value="0"></td>'
						elm += '<td><input type="text" name="promotions[promo_max_units][]" class="form-control input-sm all_max" size="5" value="0"></td>'
						elm += '<td align="center"><button type="button" class="btn btn-xs btn-danger del_row"><i class="fa fa-remove"></i></button></td>'
						elm += '</tr>'			
						$('.dyn_add > tbody > tr').eq(0).after($(elm))
					}
				},
				error: function(xhr,textStatus, errorThrown) {
					console.log(xhr.responseText);
				} 		
			});
			$("#item_sku").val('').focus()
			$('html,body').animate({scrollTop: $(this).offset().top},1000);
			
		}
	});
	$(document).on('keyup','.all_margin',function(){
		var margin = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val())
		var disc = isNaN(parseFloat($(this).parent().next().find('.all_discount').val())) ? 0 : parseFloat($(this).parent().next().find('.all_discount').val())
		var supp = isNaN(parseFloat($(this).parent().parent().find('.all_supp').val().replace(",",""))) ? 0 : parseFloat($(this).parent().parent().find('.all_supp').val().replace(",",""))
		//alert(margin+'=='+disc+'=='+supp)
		var rp = (supp + (supp * (margin/100))) 
		var disc_price = (rp * (disc/100))
		var mrp = (rp - disc_price).toFixed(2)
		$(this).parent().parent().find('.all_retail').val(mrp)
	});
	$(document).on('keyup','.all_discount',function(){
		var disc = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val())
		var margin = isNaN(parseFloat($(this).parent().prev().find('.all_margin').val())) ? 0 : parseFloat($(this).parent().prev().find('.all_margin').val())
		var supp = isNaN(parseFloat($(this).parent().parent().find('.all_supp').val().replace(",",""))) ? 0 : parseFloat($(this).parent().parent().find('.all_supp').val().replace(",",""))
		var rp = (supp + (supp * (margin/100))) 
		var disc_price = (rp * (disc/100))
		var mrp = (rp - disc_price).toFixed(2)
		$(this).parent().parent().find('.all_retail').val(mrp)
	});
	$('#all_promo_margin').keyup(function(){
		var margin = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val())
		$('.all_margin').val(margin)
		$('.all_margin').each(function(){
			var disc = isNaN(parseFloat($(this).parent().parent().find('.all_discount').val())) ? 0 : parseFloat($(this).parent().parent().find('.all_discount').val())
			var supp = isNaN(parseFloat($(this).parent().parent().find('.all_supp').val().replace(",",""))) ? 0 : parseFloat($(this).parent().parent().find('.all_supp').val().replace(",",""))
			var rp = (supp + (supp * (margin/100))) 
			var disc_price = (rp * (disc/100))
			var mrp = (rp - disc_price).toFixed(2)
			$(this).parent().parent().find('.all_retail').val(mrp)
		});
	});
	$('#all_promo_disc').keyup(function(){
		var disc = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val())
		$('.all_discount').val(disc)
		$('.all_margin').each(function(){
			var margin = isNaN(parseFloat($(this).parent().parent().find('.all_margin').val())) ? 0 : parseFloat($(this).parent().parent().find('.all_margin').val())
			var supp = isNaN(parseFloat($(this).parent().parent().find('.all_supp').val().replace(",",""))) ? 0 : parseFloat($(this).parent().parent().find('.all_supp').val().replace(",",""))
			var rp = (supp + (supp * (margin/100))) 
			var disc_price = (rp * (disc/100))
			var mrp = (rp - disc_price).toFixed(2)
			$(this).parent().parent().find('.all_retail').val(mrp)
		});
	});
	$('#all_promo_min').keyup(function(){
		var mini = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val())
		$('.all_min').val(mini)
	});
	$('#all_promo_max').keyup(function(){
		var maxi = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val())
		$('.all_max').val(maxi)
	});
	$('#myform').submit(function(){
		$.fancybox('<h2 align="center" style="background:#ffb951; padding:10px;">Loading..</h2>',{
			closeBtn: false,
			minWidth: 250,
			minHeight: 20,
			padding: 0,
			margin: 0,
			modal : true,
			helpers:  {
				overlay : {
					css : {
						'background' : 'rgba(0, 0, 0, 0.5)'
					}
				}				
			}
		});
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
