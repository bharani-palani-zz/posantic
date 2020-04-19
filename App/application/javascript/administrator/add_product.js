$(function(){
	$('[data-toggle="popover"]').popover({ trigger: "hover",container: 'body' });	
	$('#new_p_scale').hide()
	var load_scale = $('#new_p_scale').val()
	var max_variants = parseInt($('#max_variants').val())
	$('#'+load_scale+' .tick_area').html('<i class="fa fa-check-square"></i>')
	handle_page(load_scale)
		
	$('.select_scale').on({
		'click' : function(){
			var scale = $(this).attr('id')
			$('.tick_area').html('')
			$('#'+scale+' .tick_area').html('<i class="fa fa-check-square"></i>')
			$('#new_p_scale').val(scale)
			handle_page(scale)
		},
		'mouseover' : function() {
			$(this).css({'border' : 'solid 1px #bbb','cursor' : 'pointer'})
		},
		'mouseout' : function() {
			$(this).css({'border' : ''})
		},
		'mouseenter' : function() {
			$('.select_scale').css({'border' : ''})
		}
	});
	if($('textarea').length > 0)
	{
		tinymce.init({
				selector: "textarea",
				height: 100,
				plugins : 'advlist autolink link image lists charmap textcolor',
				menubar : false,
				theme: "modern",
				menubar: "tools table format view insert edit",
				resize: false,
				toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor ", 
		});	
	}
	$('#p_name').keyup(function(){
		$('#p_handle').val($(this).val().replace(/ /g, ""))
	});
	$('#p_handle').keyup(function(){
		$(this).val($(this).val().replace(/ /g, ""))
	});

	$('#myform').find('input').keypress(function(e){
		if(e.which == 13 ) 
		{
			e.preventDefault();
		}
	});
	if($('#item_sku').length > 0)
	{
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
					url: $('#blend_url').val(),
					data: { term: $("#item_sku").val(), csrf_test_name : $("input[name=csrf_test_name]").val()},
					type: "GET",
					success: function(data){
						response($.map(JSON.parse(data), function (v,i) {
						   return {
							   label: v.prod_name,
							   value: v.prod_name,
							   product_id: v.indexed
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
				var k = 0;
				if($('.blend_SKU').length < 1)
				{
					var elm = $('<div class="row"><div class="col-md-6"><div class="form-group input-group"><span class="input-group-addon">'+item_name+'</span><input type="hidden" name="blend_product_id[]" class="blend_SKU" value="'+product_id+'"><input type="text" class="form-control input-sm" value="1.000" autocomplete="off" name="blend_prd_qty[]"><span class="input-group-btn"><button type="button" class="del_blend_row btn btn-sm btn-danger" title="Remove"><i class="fa fa-remove"></i></button></span></div></div></div>');		
					$('#bend_app_div tbody').after(elm)
					$("#item_sku").val('').focus()
					//$('html,body').animate({scrollTop: $(this).offset().top},1000);
				} else {
					$('.blend_SKU').each(function(index, element) {
						if($(this).val() == product_id)
						k++;
					});
					if(k == 0)
					{
						var elm = $('<div class="row"><div class="col-md-6"><div class="form-group input-group"><span class="input-group-addon">'+item_name+'</span><input type="hidden" name="blend_product_id[]" class="blend_SKU" value="'+product_id+'"><input type="text" class="form-control input-sm" value="1.000" autocomplete="off" name="blend_prd_qty[]"><span class="input-group-btn"><button type="button" class="del_blend_row btn btn-sm btn-danger" title="Remove"><i class="fa fa-remove"></i></button></span></div></div></div>');
						$('#bend_app_div tbody').after(elm)
						$("#item_sku").val('').focus()
						//$('html,body').animate({scrollTop: $(this).offset().top},1000);
					} else {
						$("#item_sku").val('').blur()
						$cont = '<div id="dataConfirmModal" class="modal modal-sm fade container" role="dialog" style="padding-top:15%">'
						$cont += '<div class="modal-content">'
						$cont += '<div class="modal-header">'
						$cont += '<h4 id="dataConfirmLabel">Oops..</h4></div>'
						$cont += '<div class="modal-body"><h5 class="text-center">We`ve already added this one!</h5></div>'
						$cont += '<div class="modal-footer"><button class="btn btn-danger" data-dismiss="modal" aria-hidden="false">Close</button>'
						$cont += '</div></div></div>'
						$('body').append($cont);
						$('#dataConfirmModal').modal({show:true,backdrop: 'static'});
					}
					
				}
			}
		});
	}
	if($('#item_sku').length > 0)
	{
		$('#item_sku').scannerDetection({
			minLength:4,
			endChar: [13]
		});
		$('#item_sku').bind('scannerDetectionComplete',function(e,data){
			var code = data.string
			$.ajax({ 
				url: $('#blend_url').val(),
				data: { term: code, csrf_test_name : $("input[name=csrf_test_name]").val()},
				type: "GET",
				success: function(data){
					$.map(JSON.parse(data), function (v,i) {
						var item_name = v.prod_name
						var product_id = v.indexed
						var k = 0;
						if($('.blend_SKU').length < 1)
						{
							var elm = $('<div class="row"><div class="col-md-6"><div class="form-group input-group"><span class="input-group-addon">'+item_name+'</span><input type="hidden" name="blend_product_id[]" class="blend_SKU" value="'+product_id+'"><input type="text" class="form-control input-sm" value="1.000" autocomplete="off" name="blend_prd_qty[]"><span class="input-group-btn"><button type="button" class="del_blend_row btn btn-sm btn-danger" title="Remove"><i class="fa fa-remove"></i></button></span></div></div>');
							$('#bend_app_div tbody').after(elm)
							$("#item_sku").val('').focus()
						} else {
							$('.blend_SKU').each(function(index, element) {
								if($(this).val() == product_id)
								k++;
							});
							if(k == 0)
							{
								var elm = $('<div class="row"><div class="col-md-6"><div class="form-group input-group"><span class="input-group-addon">'+item_name+'</span><input type="hidden" name="blend_product_id[]" class="blend_SKU" value="'+product_id+'"><input type="text" class="form-control input-sm" value="1.000" autocomplete="off" name="blend_prd_qty[]"><span class="input-group-btn"><button type="button" class="del_blend_row btn btn-sm btn-danger" title="Remove"><i class="fa fa-remove"></i></button></span></div></div>');
								$('#bend_app_div tbody').after(elm)
								$("#item_sku").val('').focus()
							} else {
								$("#item_sku").val('').blur()
								$cont = '<div id="dataConfirmModal" class="modal modal-sm fade container" role="dialog" style="padding-top:15%">'
								$cont += '<div class="modal-content">'
								$cont += '<div class="modal-header">'
								$cont += '<h4 id="dataConfirmLabel">Oops..</h4></div>'
								$cont += '<div class="modal-body"><h5 class="text-center">'+item_name+' is already added!</h5></div>'
								$cont += '<div class="modal-footer"><button class="btn btn-danger" data-dismiss="modal" aria-hidden="false">Close</button>'
								$cont += '</div></div></div>'
								$('body').append($cont);
								$('#dataConfirmModal').modal({show:true,backdrop: 'static'});
							}
							
						}
	
				   });
				},
				error: function(xhr,textStatus, errorThrown) {
					alert(errorThrown);
				} 		
			 });
	
		});
	}
	$(document).on('click','.del_blend_row',function(){
		$(this).closest('.row').fadeOut(500, function(){ $(this).remove();});
	});
	$('.add_row').click(function(){
		if($('#variant_table .panel-body tr').length <= 2)
		{
			var priv = $('.var_selectbox').length == 1 ? 0 : 1
			$elm = '<div class="form-group input-group"><span class="input-group-addon">Variant option</span>'
			$elm += '<select class="var_selectbox form-control input-sm" name="var_type_name[]">';
			$('.var_selectbox:eq('+priv+') option').each(function() { 
				$elm += "<option value="+this.value+">"+$(this).text()+"</option>"
			});			
			$tb = '<input type="text" class="form-control input-sm && var_tbox" id="new_var_method" name="new_var_method[]" placeholder="Option name" autocomplete="off"/>';
			$elm += '</select><span class="input-group-addon">Variant value</span>'+$tb+'<span class="input-group-btn"><a href="#" class="btn btn-sm btn-danger del_row" title="Remove"><i class="fa fa-remove"></i></a></span></div>';
			$("#variant_table .panel-body").append($elm);
			if($('#variant_table select').length == max_variants){$(this).hide()}

			$('.var_selectbox').each(function(){
				var $this = $(this);
				$('.var_selectbox').not($this).find('option').each(function(){
				   if($(this).attr('value') == $this.val() && $(this).attr('value') != 'CREATE')
					   $(this).attr('disabled',true);
				});
			});
			serialize_tbody()
		}
	});
	
	$(document).on('click','.del_row',function() {
		$(this).parent().parent().remove()
		if($('#variant_table select').length <= max_variants)
		{
			$('.add_row').show()	
		}
		$('.var_selectbox').each(function(){
			var $this = $(this);
			$('.var_selectbox').not($this).find('option').each(function(){
			   if($(this).attr('value') == $this.val())
			   {
				   $(this).attr('disabled',true);
			   } else {
				   $(this).attr('disabled',false);
			   }
			});
		});
		serialize_tbody()
		return false
	});
	
	$(document).on('change','.var_selectbox',function() {
		var box = $(this).attr('id')
		if($(this).val() != 'CREATE')
		{
			$('.var_selectbox option').attr('disabled',false);
			$('.var_selectbox').each(function(){
				var $this = $(this);
				$('.var_selectbox').not($this).find('option').each(function(){
				   if($(this).attr('value') == $this.val())
					   $(this).attr('disabled',true);
				});
			});
		} else {
			$('#add_var_form #new_add_var_key').val('')
			$('#add_var_form #new_add_var_val').val('')
			$('#add_var_form .form_errors').hide()
			$('#add_var_form #hid_sel').val(box)
			$('#add_var_form').modal({show:true,backdrop: 'static'});
		}	
	});	
	$('#butt_add_var').click(function(){
		target = $('#hid_sel').val()
		if($('#new_add_var_key').val().length > 0 && $('#new_add_var_key').val().length <= 10 && $('#new_add_var_key').val() != 'CREATE')
		{
			if($('#new_add_var_val').val().length > 0 && $('#new_add_var_val').val().length <= 10)
			{
				$.ajax({ 
					url: $('#cust_var_post_url').val(),
					data: { cust_var_key: $('#new_add_var_key').val(), cust_var_value:$('#new_add_var_val').val(), csrf_test_name : $("input[name=csrf_test_name]").val()},
					type: "POST",
					success: function(data){
						var resp = JSON.parse(data)
						if(resp.status == 'success')
						{
							$('.var_selectbox#'+target).append($('<option selected></option>').attr('value',resp.var_key).text(resp.var_value));	
						} else {
							alert('Oops.. Please try again!')
						}
					}
				});
				$('#add_var_form').modal('hide');											
			} else {
				$('#error_var_val').show()
			}
		} else {
			$('#error_var_key').show()
		}
	});
	$('.dismiss_cust_var').on('click',function(){
		target = $('#hid_sel').val()
		$('.var_selectbox#'+target).val("")
	});
	//pricing calculations
	$('#tax_table').hide();
	$('#show_locale_tax').click(function(){
		$('#tax_table').toggle();
	});
	$('#price').keyup(function(e){
		if(isNaN($(this).val()))
		{
			$(this).val('')	
		}
	})
	$('#margin').keyup(function(e){
		var price = isNaN($('#price').val()) ? 0 : Number($('#price').val())
		var margin = isNaN($(this).val()) ? 0 : Number($(this).val())
		var retail = (price + (price * (margin / 100))).toFixed(2)
		$('#retail').val(retail)
		$('.qty_scale_tax').each(function(){
			var str = $('option:selected', $(this)).text(), pos = str.indexOf("(") + 1;
			var outlet_tax = str.slice(pos, -1);
			var tax_val = Number(outlet_tax.replace("%",""));
			var ind_tax_amt = Number(retail * (tax_val / 100)).toFixed(2)
			var ind_mrp = (Number(ind_tax_amt) + Number(retail)).toFixed(2)
			$(this).closest('tr').find('.qty_scale_tax_amt').html(ind_tax_amt)
			$(this).closest('tr').find('.mrp_scale_tax_amt').html(ind_mrp)
		});
		var loyalty_sale = Number($('#loyalty_sale').val())
		var loyalty_reward = Number($('#loyalty_reward').val())
		var ratio = loyalty_reward/loyalty_sale
		var loyalty_def = (ratio * retail).toFixed(2)
		$('#loyalty_def_val').val(loyalty_def)
		if(loyalty_def != Number($('#loyalty_cust_val').val()) && $('#loyalty_cust_val').val().length > 0)
		{
			$('#loyalty_false').prop('checked',true)	
		} else {
			$('#loyalty_true').prop('checked',true)	
		}
		$('#loyalty_def_span').length > 0 ? $('#loyalty_def_span').html('<i class="fa fa-gift"></i> Override '+loyalty_def) : '';
	});
	$('#retail').keyup(function(e){
		var price = isNaN($('#price').val()) ? 0 : Number($('#price').val())
		var retail = isNaN($(this).val()) ? 0 : Number($(this).val())
		var margin = (((retail / price) * 100) - 100).toFixed(3) 
		margin = isNaN(margin) ? 0 : margin
		$('#margin').val(margin)
		$('.qty_scale_tax').each(function(){
			var str = $('option:selected', $(this)).text(), pos = str.indexOf("(") + 1;
			var outlet_tax = str.slice(pos, -1);
			var tax_val = Number(outlet_tax.replace("%",""));
			var ind_tax_amt = Number(retail * (tax_val / 100)).toFixed(2)
			var ind_mrp = (Number(ind_tax_amt) + Number(retail)).toFixed(2)
			$(this).closest('tr').find('.qty_scale_tax_amt').html(ind_tax_amt)
			$(this).closest('tr').find('.mrp_scale_tax_amt').html(ind_mrp)
		});
		var loyalty_sale = Number($('#loyalty_sale').val())
		var loyalty_reward = Number($('#loyalty_reward').val())
		var ratio = (loyalty_reward/loyalty_sale).toFixed(2)
		var loyalty_def = (ratio * retail).toFixed(2)
		$('#loyalty_def_span').length > 0 ? $('#loyalty_def_span').html('<i class="fa fa-gift"></i> Override '+loyalty_def) : '';
		if(loyalty_def != Number($('#loyalty_cust_val').val()) && $('#loyalty_cust_val').val().length > 0)
		{
			$('#loyalty_false').prop('checked',true)	
		} else {
			$('#loyalty_true').prop('checked',true)	
		}
	});
	$('#retail').keyup();
	if($('input[name="loyalty_stat"]').val() == 30)
	{
		$('#def_loyalty_group').hide()
	}
	$('input[name="loyalty_stat"]').on('switchChange.bootstrapSwitch',function(event, state){
		if(this.value == 40)
		{
			$('#def_loyalty_group').show('slow')
		} else {
			$('#def_loyalty_group').hide('slow')
		}
	});
	$('.qty_scale_tax').change(function(){
		var str = $('option:selected', $(this)).text(), pos = str.indexOf("(") + 1;
		var outlet_tax = str.slice(pos, -1);
		var tax_val = Number(outlet_tax.replace("%",""));
		var retail = $('#retail').val()
		var ind_tax_amt = Number(retail * (tax_val / 100)).toFixed(2)
		var ind_mrp = (Number(ind_tax_amt) + Number(retail)).toFixed(2)
		$(this).closest('tr').find('.qty_scale_tax_amt').html(ind_tax_amt)
		$(this).closest('tr').find('.mrp_scale_tax_amt').html(ind_mrp)

	});
	$('#loyalty_cust_val').focus(function(){
		$('#loyalty_false').prop('checked', true);
	})
	//price calculation end
	if($('#trace_inv').val() != 30)
	{
		$('#inventory_table').parent().hide()		
	}
	$('input[name="trace_inv"]').on('switchChange.bootstrapSwitch',function(event, state){
		if(event.currentTarget.checked == true)
		{
			$('#inventory_table').parent().show()
		} else {
			$('#inventory_table').parent().hide()
		}
	});
	if($('#trace_inv').val() != 30)
	{
		$('#trace_inv').click();
	}

	$('#new_cat').children('option').each(function() {
		if ($(this).is(':selected'))
		{ $(this).trigger('change');  }
	});	
	scale = $('#tag_scale').val()
	prd_id = $('#prd_id').val()
	if($('#prd_tags').length > 0)
	{
		$('#prd_tags').autocomplete({
				delay: 1000,	
				autoFocus: true,
				open: function() { 
					$('.ui-menu').width($('#prd_tags').width());
					$('.ui-menu').height(150);
					$(".ui-menu").css({"font-size": "12px"})
				},
				source: function (request, response) {
					$.ajax({ 
						url: $('#tag_url').val(),
						data: { term: $("#prd_tags").val(), csrf_test_name : $("input[name=csrf_test_name]").val()},
						type: "GET",
						success: function(data){
							response($.map(JSON.parse(data), function (v,i) {
							   return {
								   label: v.tag_name,
								   value: v.tag_name,
								   t_id: v.tag_id
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
					var tag_id = ui.item.t_id
					var tag_name = ui.item.label
					if($('#tag_id_'+tag_id).length < 1)
					{
						//if scale is not equal to something
						if(scale == "" && prd_id == "")
						{
							$elm = $('<span class="tag label label-info"><input id="tag_id_'+tag_id+'" type="hidden" name="tag_id[]" value="'+tag_id+'"><span>'+tag_name+'</span><a data-scale="" data-tag-id="" data-product-id="" class="remove_tag" href="#"><i class="remove glyphicon glyphicon-remove-sign glyphicon-white"></i></a></span>')
							$('#ajax_tags_div').append($elm)
						} else {
							//ajax insert prd tag
							$.ajax({ 
								url: $('#insert_prd_tag_url').val(),
								data: { tag_id: tag_id, prd_id: prd_id, csrf_test_name : $("input[name=csrf_test_name]").val()},
								type: "POST",
								success: function(data){
									if(data == true)
									{
										$elm = $('<span class="tag label label-info"><input id="tag_id_'+tag_id+'" type="hidden" name="tag_id[]" value="'+tag_id+'"><span>'+tag_name+'</span><a data-scale="'+scale+'" data-tag-id="'+tag_id+'" data-product-id="'+prd_id+'" class="remove_tag" href="#"><i class="remove glyphicon glyphicon-remove-sign glyphicon-white"></i></a></span>')
										$('#ajax_tags_div').append($elm)
									}
								},
								error: function(xhr,textStatus, errorThrown) {
									alert(errorThrown);
								} 		
						   });
						}
					}
					$('#prd_tags').val('')
				}
	
		});
	}
	$('#add_tag').click(function(){
		if($("#prd_tags").val().length > 0)
		{
			$.ajax({ 
				url: $('#insert_tag_url').val(),
				data: { tag_name: $("#prd_tags").val(), prd_id: prd_id, csrf_test_name : $("input[name=csrf_test_name]").val()},
				type: "POST",
				success: function(data){
					if(data != false)
					{
						var arr = JSON.parse(data);
						tag_id = arr[0]
						tag_name = arr[1]
						if($('#tag_id_'+tag_id).length < 1)
						{
							$elm = '<span class="tag label label-info"><input id="tag_id_'+tag_id+'" type="hidden" name="tag_id[]" value="'+tag_id+'"><span>'+tag_name+'</span><a data-scale="'+scale+'" data-tag-id="'+tag_id+'" data-product-id="'+prd_id+'" class="remove_tag" href="#"><i class="remove glyphicon glyphicon-remove-sign glyphicon-white"></i></a></span>'
							$('#ajax_tags_div').append($elm)
						}
					}
				},
				error: function(xhr,textStatus, errorThrown) {
					alert(errorThrown);
				} 		
			 });
		}
		 $("#prd_tags").val('')
	});
	$(document).on('click','.remove_tag',function(){
		if($(this).attr('data-scale') != '')
		{
			$elm = $(this).parent()
			tag_id = $(this).attr('data-tag-id')
			$.ajax({ 
				url: $('#delete_tag_url').val(),
				data: { tag_id: tag_id, product_id: prd_id, csrf_test_name : $("input[name=csrf_test_name]").val()},
				type: "POST",
				success: function(data){
					if(data == true)
					{
						$elm.fadeOut(500, function(){
							$elm.remove();
						})	
					}
				},
				error: function(xhr,textStatus, errorThrown) {
					//alert(errorThrown);
					console.log(xhr.responseText)
				} 		
			 });
		} else {
			$elm = $(this).parent()
			$elm.fadeOut(500, function(){
				$elm.remove();
			})	
			
		}
		return false
	});
	$(document).on('click','.insert_ajax_btn', function(){
		var for_zone = $(this).attr('data-for-zone')
		var for_select = $(this).attr('data-for-select')
		switch(for_zone){
			case 'category':
				postobj = {cat_name : $('#cat_name').val(), csrf_test_name : $("input[name=csrf_test_name]").val()}
			break;
			case 'supplier':
				postobj = {supp_name : $('#supp_name').val(), csrf_test_name : $("input[name=csrf_test_name]").val()}
			break;
			case 'brand':
				postobj = {brand_name : $('#brand_name').val(), csrf_test_name : $("input[name=csrf_test_name]").val()}
			break;
			case 'tax':
				postobj = {tax_name : $('#tax_name').val(), tax_val : $('#tax_val').val(), csrf_test_name : $("input[name=csrf_test_name]").val()}
			break;
		}
		$.ajax({ 
			url: $('#insert_ajax_url').val(),
			data: postobj,
			type: "POST",
			success: function(data){
				var resp = JSON.parse(data)
				if(resp.status == 'success')
				{
					$(for_select).append($('<option selected></option>').attr('value',resp.key).text(resp.value)).change();
				} else {
					alert('Oops.. Please try again!')
				}
			},
			error: function(xhr,textStatus, errorThrown) {
				alert(errorThrown);
				//console.log(xhr.responseText)
			} 		
		});
		$('#ajax_insert_dyn_modal').modal('hide');											
	});
});
function serialize_tbody()
{
	m=0;
	$('#variant_table .panel-body select').each(function(){
		$(this).attr({
			id: 'var_sel_grp'+m,
		})
		m++
	});	
}
function handle_page(scale)
{
	$('#cost').val('')
	$('#tax').val(0)
	$('#mrp').val('')
	switch(scale){
		case 'NUM':
			$('#prd_ship_text').html('')
			$('.inv_postfix').html('Qty')
			$('#blend_div').fadeOut(500)
			$('#inventory_div').fadeIn(500)
			$("#variant_table tbody").empty()
			$('#var_content').fadeOut(500)
		break;
		case 'KILO':
			$('#prd_ship_text').html('<i class="glyphicon glyphicon-alert"></i> Your customer`s sale product uptake volume is considered as product weight. Any how you can mark weight of additional packing stuffs<br><br>')
			$('#blend_div').fadeOut(500)
			$('#inventory_div').fadeIn(500)
			$("#variant_table tbody").empty()
			$('#var_content').fadeOut(500)
			$('.inv_postfix').html('Kg')
		break;
		case 'VARIANTS':
			$('#prd_ship_text').html('')
			$('.inv_postfix').html('Qty')
			$("#variant_table .panel-body").empty()
			$('#var_content').fadeIn(500)
			$('#var_content table tfoot').fadeIn(500)
			json = $.parseJSON($('#hid_var').val())
			$elm = '<div class="form-group input-group"><span class="input-group-addon">Variant option</span>'
			$elm += '<select class="var_selectbox form-control input-sm" name="var_type_name[]"><option selected value="">--</option><option value="CREATE">+ Add Custom</option>';
			$.each(json,function(index,item){
				$elm += "<option value="+item.code+">"+item.named+"</option>"
			});
			$tb = '<input type="text" class="form-control input-sm var_tbox" id="new_var_method" name="new_var_method[]" placeholder="Option name" autocomplete="off"/>';
			$elm += '</select><span class="input-group-addon">Variant value</span>'+$tb+'<span class="input-group-btn"><a href="#" class="btn btn-sm btn-danger disabled" title="Remove"><i class="fa fa-remove"></i></a></span></div>';
			if($('.add_row').is(":visible") == false)
			{
				$('.add_row').show()
			}
			$("#variant_table .panel-body").append($elm);
			serialize_tbody()
			$('#inventory_div').fadeIn(500)
			$('#blend_div').fadeOut(500)
		break;			
		case 'BLEND':
			$('#prd_ship_text').html('')
			$('#blend_div').fadeIn(500)
			$('#inventory_div').fadeOut(500)
			$("#variant_table tbody").empty()
			$('#var_content').fadeOut(500)
		break;			
		default:				
			$('#blend_div').fadeOut(500)
			$('#inventory_div').fadeIn(500)
			$("#variant_table tbody").empty()
			$('#var_content').fadeOut(500)
		break;			
	}
	
}