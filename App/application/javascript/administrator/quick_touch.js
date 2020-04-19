$(function(){
	var csrf = $("input[name=csrf_test_name]").val();
	var merch_id = $('#merchant_id').val()
	var touch_id = $('#touch_id').val()
	var setting_image = '<i class="fa fa-bars"></i>'
	var json = $.parseJSON($('#quick_touch_json').val())
	var max_group_count = parseInt($('#max_qt_headers').val())
	var max_page_count = parseInt($('#max_qt_pages').val())
	var max_page_product = parseInt($('#max_qt_products_per_page').val())
	
	var colors_array = ["#eeeeee","#f1a5a7","#bee4f6","#dbc0f1","#bbf6be","#fadbab","#f5afe2","#f5f6b9","#cbcdcc"]
	var ua = navigator.userAgent,
    focused = (ua.match(/iPad/i)) ? "touchstart" : "mouseup";
	$("[data-toggle=popover]").popover();
	$(document).on(focused, function (e) {
		//$('body').find('div.popover').popover('hide');
		$('[data-toggle="popover"]').each(function () {
			if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
				$(this).popover('destroy');
				remove_arrow_box()
			}
		});
	});

	function ellipsize($str, $max_length, $position)
	{
		// Is the string long enough to ellipsize?
		if($str.length <= $max_length)
		{
			return $str;
		}
		$ellipsis = '&hellip;'
		$beg = $str.substr(0, Math.floor($max_length * $position));
		$position = ($position > 1) ? 1 : $position;
		if($position === 1)
		{
			$end = $str.substr( 0, $max_length - $beg.length);
		}
		else
		{
			$end = $str.substr($str.length - ($max_length - $beg.length));
		}
		return $beg+$ellipsis+$end;
	}
	function assure_add_group()
	{
		if($('.top_group').length >= max_group_count)
		{
			$('#add_group').attr('disabled','disabled')	
		} else {
			$('#add_group').removeAttr('disabled')	
		}		
	}
	function draw_topgroup()
	{
		var count = $('.top_group').length
		var col = Math.floor(12 / count)
		$('.top_group').each(function(index, element) {			
			$(this).removeClass('col-lg-3 col-lg-4 col-lg-6 col-lg-12')
			$(this).removeClass('col-md-3 col-md-4 col-md-6 col-md-12')
			$(this).removeClass('col-sm-3 col-sm-4 col-sm-6 col-sm-12')
			$(this).removeClass('col-xs-3 col-xs-4 col-xs-6 col-xs-12')
			$(this).addClass('top_group col-lg-'+col+' col-md-'+col+' col-sm-'+col+' col-xs-'+col)
		});
	}
	function remove_arrow_box()
	{
		$('body').find('div.popover').popover('destroy');			
	}
	$(document).on('click','.reset_sel',function(e){
		$(this).closest('div.popover').popover('hide');
	})	
	
	$html = '<div id="sortable">';
	var col = Math.floor(12 / json.quick_touch.group.length)
	$.each(json.quick_touch.group,function(index, eached) {
		$html += '<div class="col-lg-'+col+' col-md-'+col+' col-sm-'+col+' col-xs-'+col+' top_group" id="'+eached.group_id+'" data-id="'+eached.group_id+'" data-position="'+eached.group_position+'"><p><span class="group_config" data-toggle="popover" data-id="'+eached.group_id+'" data-position="'+eached.group_position+'">'+setting_image+'</span>&nbsp;<span class="group_name_span">'+ellipsize(eached.group_name,20,0.5)+'</span></p></div>';
		//make product buttons	
		$page = '';
		$.each(eached.pages,function(page_index, page_array) {
			$div = '<ul class="sub_prd_ul" id="'+eached.group_id+'_page_'+page_array.page+'" data-page="'+page_array.page+'" data-group="'+eached.group_id+'">';
			$.each(page_array.keys,function(key_id,key_array){
				if(key_array.product_id)
				{
					$div += '<li class="sub_products col-lg-2 col-md-2 col-sm-2 col-xs-2" data-toggle="popover" id="'+key_array.product_id+'" data-page="'+page_array.page+'" data-group="'+eached.group_id+'" style="background:'+key_array.colour+'" data-product-id="'+key_array.product_id+'" data-scale="'+key_array.scale+'"><p>'+key_array.label+'</p></li>';
				}
			});
			$page += '<button type="button" class="btn btn-success btn-xs btn-circle pagewise" data-id="'+eached.group_id+'_page_'+page_array.page+'" data-group="'+eached.group_id+'" data-page="'+page_array.page+'" data-page-index="'+page_array.page_index+'">'+(parseInt(page_array.page)+1)+'</button>'
			$div += '</ul>';
			$('#product_field').append($div)
			
			$( "#"+eached.group_id+'_page_'+page_array.page ).sortable({
				cursor: "move",
				revert: true,
				cursorAt: { top: 0, left: 0 },
				appendTo: 'body',
				update: function( event, ui ) {
					var udata = $(this).sortable('toArray');
					$.ajax({ 
						url: $('#quick_touch_url').val(),
						data: { csrf_test_name : csrf, prd_params : udata, merchant_id : merch_id, touch_id : touch_id},
						type: "POST",
						success: function(data){
		
						},
						error: function(xhr,textStatus, errorThrown) {
							alert(errorThrown);
						} 		
					});
				}
			});
		})
		$('#paginate_field').append('<div class="paginate paginate_numbers" id="'+eached.group_id+'" align="center">'+$page+'</div>')
	});
	$html += '</div>';
	$('#cat_field').append($html)
	$('.sub_prd_ul').hide()
	$('.sub_prd_ul').first().show()
	$('.paginate').hide()
	$('.paginate').first().show()
	$('.paginate').first().find('button:first-child').attr('data-current',0)

	$(document).on('click','.pagewise',function(){
		$('#product_field .sub_prd_ul').hide()
		page = $(this).attr('data-id')
		$('#product_field .sub_prd_ul#'+page).show()
		$('.pagewise').each(function(){
			$(this).removeAttr('data-current')		
		});
		$(this).attr('data-current',$(this).attr('data-page'))
		return false
	});

	$(document).on('click','.top_group',function(){
		$('.sub_prd_ul').hide()
		group = $(this).attr('data-id')
		$('#product_field .sub_prd_ul#'+group+'_page_0').show()
		$('#paginate_field .paginate').hide()
		$('#paginate_field .paginate[id='+group+']').show()
		$('.pagewise').each(function(){
			$(this).removeAttr('data-current')		
		});
		$('#paginate_field .paginate[id='+group+'] button:first-child').attr('data-current',0)
		
	});

	$( "#sortable" ).sortable({
		cursor: "move",
		revert: true,
		
		update: function( event, ui ) {
			var udata = $(this).sortable('toArray');
			$.ajax({ 
				url: $('#quick_touch_url').val(),
				data: { csrf_test_name : csrf, group_params : udata, merchant_id : merch_id, touch_id : touch_id},
				type: "POST",
				success: function(data){

				},
				error: function(xhr,textStatus, errorThrown) {
					alert(errorThrown);
				} 		
			});
		},
		start: function(event,ui){
			group = ui.item.attr('data-id')
			$('.sub_prd_ul').hide()
			$('.sub_prd_ul#'+group+'_page_0').show()
			$('.paginate').hide()
			$('.paginate[id='+group+']').show()
		}
	});
    $( "#sortable" ).disableSelection();	
	assure_add_group()

	$(document).on('click','.group_config',function(){
		var disab = $('.top_group').length > 1 ? '' : 'disabled';
		remove_arrow_box()
		value = $(this).next().text()
		grp_position = $(this).attr('data-position')
		grp_id = $(this).attr('data-id')
		$but1 = '<span class="input-group-btn"><button type="button" class="btn btn-success" data-oldval="'+value+'" data-id="'+grp_id+'" data-position="'+grp_position+'" id="save_group_name"><i class="fa fa-save"></i> Save</button></span>';
		$but2 = '<span class="input-group-btn"><button type="button" '+disab+' class="btn btn-danger" data-position="'+grp_position+'" data-id="'+grp_id+'" id="delete_top_group"><i class="fa fa-times"></i> Delete</button></span>';
		elm = '<div class="input-group input-group-sm">'+$but1+' <input type="text" autocomplete="off" value="'+value+'" class="form-control" data-position="'+grp_position+'" id="tbTextbox" > '+$but2+'</div>';
		$(this).popover({
			html : true, 
			trigger: 'manual',
			placement: "top",
			container: "body",
			content: function() {
			  return elm
			}
		}).popover('toggle');

		$('#tbTextbox').focus().val(value)
	});
	$(document).on('click','#delete_top_group',function(){
		if($('.top_group').length > 1)
		{
			if(confirm('Are you sure to delete this group with all of its products and pages associated to it???'))
			{
				position = $(this).attr('data-position')
				newval = $('#tbTextbox').val()
				group_id = $(this).attr('data-id');	
				$.ajax({ 
					url: $('#quick_touch_url').val(),
					data: { csrf_test_name : csrf, group_delete_params : [group_id], merchant_id : merch_id, touch_id : touch_id},
					type: "POST",
					success: function(data){
						if(typeof data == "string")
						{
							draw_topgroup()		
							assure_add_group()
						}
					},
					error: function(xhr,textStatus, errorThrown) {
						console.log(xhr);
					} 		
				});
				$('body').find('div.popover').popover('destroy');	
				$( ".top_group[data-position='" + position + "']" ).remove()
				$('.sub_products[data-group='+group_id+']').remove()
				$('.sub_prd_ul').first().show()
				$('.paginate#'+group_id).remove()
				$('.paginate').hide()
				$('.paginate').first().show()
				
			} else {
				$('body').find('div.popover').popover('destroy');	
				return false	
			}
		}
	});
	$(document).on('click','#save_group_name',function(){
		position = $(this).attr('data-position')
		old_val = $(this).attr('data-oldval')
		newval = $('#tbTextbox').val()	
		group_id = $(this).attr('data-id');	
		$.ajax({ 
			url: $('#quick_touch_url').val(),
			data: { csrf_test_name : csrf, group_name_params : [newval,group_id], merchant_id : merch_id, touch_id : touch_id},
			type: "POST",
			success: function(data){

			},
			error: function(xhr,textStatus, errorThrown) {
				alert(errorThrown);
			} 		
		});
		$( ".top_group[data-position='" + position + "'] .group_name_span" ).html(ellipsize(newval,25, 0.5))
		remove_arrow_box()

	});
	$(document).on('click','#add_group',function(){
				remove_arrow_box()
		$but1 = '<span class="input-group-btn"><button type="button" class="btn btn-success" id="save_new_group"><i class="fa fa-save"></i> Save</button></span>';
		$but2 = '<span class="input-group-btn"><button type="button" class="btn btn-danger reset_sel"><i class="fa fa-times"></i> Cancel</button></span>';
		elm = '<div class="input-group input-group-sm">'+$but1+' <input type="text" class="form-control" id="new_grp_Textbox"> '+$but2+'</div>';
		$(this).popover({
			html : true, 
			trigger: 'manual',			
			placement: "top",
			container: "body",			
			content: function() {
			  return $(elm)
			}
		}).popover('toggle');
		$('#new_grp_Textbox').focus().val('')
	});
	$(document).on('click','#add_page',function(){
		remove_arrow_box()
		pos = $(this).offset()
		if($('.paginate:visible button').length >= max_page_count)
		{
			$but1 = '<button type="button" class="btn btn-success page_config" disabled id="add_new_page"><i class="fa fa-plus"></i></button>';
		} else {
			$but1 = '<button type="button" title="Add page" class="btn btn-success page_config" id="add_new_page"><i class="fa fa-plus"></i></button>';
		}
		if($('.pagewise:visible').length > 1)
		{
			$but2 = '<button type="button" title="Delete last slide" class="btn btn-danger page_config" id="delete_last_slide"><i class="fa fa-minus"></i></button>';
		} else {
			$but2 = '<button type="button" class="btn btn-danger page_config" disabled id="delete_last_slide"><i class="fa fa-minus"></i></button>';
		}
		elm = '<div class="btn-group btn-group-sm">'+$but2+$but1+'</div>';
		$(this).popover({
			html : true, 
			trigger: 'manual',						
			placement: "top",
			container: "body",
			content: function() {
			  return elm
			}
		}).popover('toggle');

	});

	$(document).on('click','#save_new_group',function(){
		grp_name = $('#new_grp_Textbox').val()
		grp_pos = $('.top_group').length
		$.get($('#quick_touch_uuid').val()+'?count=2',function(uuid){
			var uuid = JSON.parse(uuid);
			group_id = uuid[0]
			page_index = uuid[1]
			$.ajax({ 
				url: $('#quick_touch_url').val(),
				data: { csrf_test_name : csrf, add_group_params : [grp_name,grp_pos,group_id,page_index], merchant_id : merch_id, touch_id : touch_id},
				type: "POST",
				success: function(data){
	
				},
				error: function(xhr,textStatus, errorThrown) {
					alert(errorThrown);
				} 		
			});
			$html = '<div class="top_group" id="'+group_id+'" data-id="'+group_id+'" data-position="'+grp_pos+'"><p><span class="group_config" data-toggle="popover" data-id="'+group_id+'" data-position="'+grp_pos+'">'+setting_image+'</span>&nbsp;<span class="group_name_span">'+ellipsize(grp_name,15, 0.5)+'</span></p></div>';
			$('#sortable').append($html)
			$div = '<ul class="sub_prd_ul" id="'+group_id+'_page_0" data-page="0" data-group="'+group_id+'"></ul>';
			$('.sub_prd_ul').hide()
			$('#product_field').append($div)
			$page = '<button class="btn btn-success btn-xs btn-circle pagewise" data-id="'+group_id+'_page_0" data-page="0" data-group="'+group_id+'" data-page-index="'+page_index+'">1</button>'
			$('.paginate').hide()
			$('#paginate_field').append('<div class="paginate paginate_numbers no-print" id="'+group_id+'" align="center">'+$page+'</div>')

			$('.pagewise').each(function(){
				$(this).removeAttr('data-current')		
			});
			$('#paginate_field .paginate[id='+group_id+'] button:first-child').attr('data-current',0)
			assure_add_group()
			draw_topgroup()
			remove_arrow_box()
		});
	});
	$(document).on('click','#add_new_page',function(){
		page_count = parseInt($('.pagewise:visible').length)
		group_id = $('.paginate:visible').attr('id')
		$.get($('#quick_touch_uuid').val()+'?count=1',function(uuid){
			var uuid = JSON.parse(uuid);
			$.ajax({ 
				url: $('#quick_touch_url').val(),
				data: { csrf_test_name : csrf, add_page : [group_id,page_count,uuid[0]], merchant_id : merch_id, touch_id : touch_id},
				type: "POST",
				success: function(data){

				},
				error: function(xhr,textStatus, errorThrown) {
					console.log(xhr.responseText);
				} 		
			})
			$div = '<ul class="sub_prd_ul" id="'+group_id+'_page_'+page_count+'" data-page="'+page_count+'" data-group="'+group_id+'"></ul>';
			$('#product_field').append($div)
			$page = '<button type="button" class="btn btn-success btn-xs btn-circle pagewise" data-id="'+group_id+'_page_'+page_count+'" data-group="'+group_id+'" data-page="'+page_count+'" data-page-index="'+uuid[0]+'">'+(page_count+1)+'</button>'
			$('.paginate[id='+group_id+']').append($page)
			$('.pagewise[data-id='+group_id+'_page_'+page_count+']').click()
			remove_arrow_box()			
		});
		remove_arrow_box()			

	});
	$(document).on('click','#delete_last_slide',function(){
		group_id = $('.paginate:visible').attr('id')
		page_count = $('.pagewise:visible').length
		index_page = $('.paginate:visible .pagewise:last').html()
		page = parseInt(index_page)-1
		page_uuid = $('.paginate:visible .pagewise:last').attr('data-page-index')
		if(page_count > 1) // delete if pages more than 1
		{
			if(confirm('Are you sure to delete the last slide with all of its products associated to it???'))
			{
				$.ajax({ 
					url: $('#quick_touch_url').val(),
					data: { csrf_test_name : csrf, delete_page : [group_id,page_uuid], merchant_id : merch_id, touch_id : touch_id},
					type: "POST",
					success: function(data){
						remove_arrow_box()			
						$('.sub_prd_ul[id='+group_id+'_page_'+page+']').remove()
						$('.pagewise[data-id='+group_id+'_page_'+page+']').remove()
						$('.pagewise[data-id='+group_id+'_page_0]').click()
					},
					error: function(xhr,textStatus, errorThrown) {
						alert(errorThrown);
					} 		
				});
			} else {
				remove_arrow_box()			
				return false	
			}
		}
		remove_arrow_box()			
		
	});
	$(document).on('click','.sub_products',function(){
		remove_arrow_box()
		prod_id = $(this).attr('id')
		group_id = $(this).attr('data-group')
		page = $(this).attr('data-page')
		pos = $(this).offset()
		value = $(this).find('p').html()

		$but1 = '<span class="input-group-btn"><button type="button" class="btn btn-danger bl_button" data-page="'+page+'" data-group="'+group_id+'" data-prod-id="'+prod_id+'" id="delete_product"><i class="fa fa-times"></i> Delete</button></span>';
		$but2 = '<span class="input-group-btn"><button type="button" class="btn btn-success bl_button reset_sel"><i class="fa fa-power-off"></i> Cancel</button></span>';
		$col = '<div class="colour_tab">'
		colors_array.forEach(function(item) {
			$col += '<button type="button" data-color="'+item+'" data-prod-id="'+prod_id+'" style="background:'+item+';" class="btn color_picker">&nbsp;</button>'
		});
		$col += '</div>';
		elm = '<div class="form-group input-group input-group-sm">'+$but2+' <input type="text" value="'+value+'" data-oldval="'+value+'" data-prod-id="'+prod_id+'" class="form-control" id="prdTextbox"/> '+$but1+'</div>';
		elm += '<div class="text-center">'+$col+'</div>'
		$(this).popover({
			html : true, 
			trigger: 'manual',						
			placement: "top",
			container: "body",
			content: function() {
			  return elm
			}
		}).popover('toggle');
	});
	$(document).on('click','#delete_product',function(){
		if($('.top_group').length > 1 || $('.sub_products:visible').length > 1)
		{
			$prd_id = $(this).attr('data-prod-id')
			group_id = $(this).attr('data-group')
			page = $(this).attr('data-page')
			page_index = $('.pagewise[data-current]').attr('data-page-index')
			$.ajax({ 
				url: $('#quick_touch_url').val(),
				data: { csrf_test_name : csrf, delete_product_params : [$prd_id,group_id,page_index], merchant_id : merch_id, touch_id : touch_id},
				type: "POST",
				success: function(data){

				},
				error: function(xhr,textStatus, errorThrown) {
					alert(errorThrown);
				} 		
			});			
			$('.sub_products[id='+$prd_id+'][data-group='+group_id+'][data-page='+page+']').remove()
		}
		remove_arrow_box()			
	});
	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
		  clearTimeout (timer);
		  timer = setTimeout(callback, ms);
		};
	})();
	$(document).on('keyup','#prdTextbox',function(){
		var self=this;
		delay(function(){
			$prd_id = $(self).attr('data-prod-id')
			oldval = $(self).attr('data-oldval')
			newval = $(self).val()
			if(oldval != newval)
			{
				$.ajax({ 
					url: $('#quick_touch_url').val(),
					data: { csrf_test_name : csrf, rename_label_params : [newval,$prd_id], merchant_id : merch_id, touch_id : touch_id},
					type: "POST",
					success: function(data){
		
					},
					error: function(xhr,textStatus, errorThrown) {
						alert(errorThrown);
					} 		
				});
			}
			$('.sub_products#'+$prd_id).find('p').html(ellipsize(newval,20, 0.5))
		}, 1000 );
	});
	$(document).on('click','.color_picker',function(){
		var self=this;
		delay(function(){
			$color = $(self).attr('data-color')
			$prd_id = $(self).attr('data-prod-id')
			$.ajax({ 
				url: $('#quick_touch_url').val(),
				data: { csrf_test_name : csrf, color_change_params : [$color,$prd_id], merchant_id : merch_id, touch_id : touch_id},
				type: "POST",
				success: function(data){
	
				},
				error: function(xhr,textStatus, errorThrown) {
					alert(errorThrown);
				} 		
			});
			$('.sub_products#'+$prd_id).css({'background':$color})
		}, 1000 );
	});
	$("#item_sku").keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if (keycode == '13') {
			event.preventDefault();
			event.stopPropagation();    
		}
	});
	$('#item_sku').autocomplete({	
		autoFocus: true,
		delay: 1000,
		open: function() { 
			$('.ui-menu').width($('#item_sku').width());
			$('.ui-menu').height(150);
			$(".ui-menu").css({"font-size": "12px"})
		},
		source: function (request, response) {
			$.ajax({ 
				url: $('#quick_search_url').val(),
				data: { term: $("#item_sku").val(), csrf_test_name : csrf},
				type: "GET",
				success: function(data){
					response($.map(JSON.parse(data), function (v,i) {
					   return {
						   label: v.prod_name,
						   value: v.prod_name,
						   product_id: v.product_id,
						   scale: v.scale,
						   labled: v.labled
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
			var scale = ui.item.scale
			var labled = ui.item.labled
			group_id = $('.pagewise[data-current]').attr('data-group')
			page_index = $('.pagewise[data-current]').attr('data-page-index')
			page = $('.pagewise[data-current]').attr('data-page')
			prd_position = $('.sub_prd_ul[data-group='+group_id+'] li:visible').length
			if(prd_position < max_page_product)
			{
				if($('.sub_products#'+product_id).length < 1) 
				{
					var $color = colors_array[Math.floor(Math.random()*colors_array.length)]
					$.ajax({ 
						url: $('#quick_touch_url').val(),
						data: { csrf_test_name : csrf, insert_product : [prd_position,page_index,group_id,product_id,$color,labled], merchant_id : merch_id, touch_id : touch_id},
						type: "POST",
						success: function(data){
			
						},
						error: function(xhr,textStatus, errorThrown) {
							console.log(xhr.responseText);
						} 		
					});
					$div = '<li class="sub_products col-lg-2 col-md-2 col-sm-2 col-xs-2" id="'+product_id+'" data-page="'+page+'" data-group="'+group_id+'" style="background:'+$color+'" data-product-id="'+product_id+'" data-scale="'+scale+'"><p>'+labled+'</p></li>';
					$('.sub_prd_ul[id='+group_id+'_page_'+page+']').append($div)
				}
			}
			$('#item_sku').val('')
		}
	});
	$('#touchform').submit(function(){
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