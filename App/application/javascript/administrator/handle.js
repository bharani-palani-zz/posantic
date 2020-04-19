$(function(){	
	$('.variant_main').click(function(){
		var $this = $(this)
		if($this.parent().parent().next("tr").hasClass('appended_variants'))
		{
			//$this.parent().parent().nextUntil('.no-hover').remove();
			$('.appended_variants').remove()
			$this.parent().find('.arr').html('&nbsp;<i class="fa fa-caret-up"></i>')
		} else {
			$elm = '';
			var product_id = $(this).attr('data-id')
			$.ajax({ 
				url: $('#get_variants_url').val(),
				data: { variant_product_id: product_id, stat:$('#product_stat').val(), csrf_test_name : $("input[name=csrf_test_name]").val()},
				type: "GET",
				beforeSend: function(){
					$this.closest('tr').after('<tr><td colspan="8" align="center" class="bg-primary"><h3 class="glyphicon glyphicon-refresh animate-spinning"></h3></td></tr>'); 
				},
				success: function(data){
					$.each(JSON.parse(data),function(index, eached) {
						$hide = eached.status == 40 ? '<a title="Show this product on sales" href="#" data-scale="VARIANTS" data-clause="1" data-hide-id="'+eached.variant_index+'" data-url="'+$('#this_url').val()+'/show/id/'+eached.variant_index+'" class="btn btn-sm btn-danger hide_prd"><span class="glyphicon glyphicon-thumbs-up"></span> Show</a>' : 
						'<a title="Hide this product from sales" href="#" data-scale="VARIANTS" data-clause="0" data-hide-id="'+eached.variant_index+'" data-url="'+$('#this_url').val()+'/hide/id/'+eached.variant_index+'" class="btn btn-sm btn-danger hide_prd"><span class="glyphicon glyphicon-thumbs-down"></span> Hide</a>';
						$elm += '<tr class="appended_variants"><td colspan="4">&nbsp;</td><td class="h6"><a class="btn btn-xs btn-default" href="'+$('#this_url').val()+'/'+eached.variant_index+'">'+eached.prod_name+'</a></td><td>'+eached.retail_price+'</td><td>'+eached.stock+'</td><td class="no-print"><span class="btn-group btn-group-sm last_td"><a class="btn btn-sm btn-success loading_modal" href="'+$('#this_url').val()+'/'+eached.variant_index+'/edit"><i class="fa fa-edit"></i> Edit</a>'+$hide+'</span></td></tr>'
					});
					$this.parent().find('.arr').html('&nbsp;<i class="fa fa-caret-down"></i>')
					$this.closest('tr').after($elm); 
				},
				error: function(xhr,textStatus, errorThrown) {
					console.log(xhr.responseText);
					//alert(errorThrown)
				},
				complete: function(){
					$('.animate-spinning').parent().parent().remove()	
				}
			});
		 }
		return false
	});
	scale = $('#tag_scale').val()
	prd_id = $('#prd_id').val()
	$('#prd_tags').autocomplete({
			delay: 1000,	
			autoFocus: true,
			open: function() { 
				$('.ui-menu').width($('#prd_tags').width()+20);
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
						//alert(textStatus);
						console.log(xhr)
					} 		
				 });
			},
			select: function(event, ui) { 
				event.preventDefault();
				var tag_id = ui.item.t_id
				var tag_name = ui.item.label
				if($('#tag_id_'+tag_id).length < 1)
				{
					$elm = '<span class="tag label label-info"><input id="tag_id_'+tag_id+'" type="hidden" name="tag_id[]" value="'+tag_id+'"><span>'+tag_name+'</span><a data-scale="" data-tag-id="" data-product-id="" class="remove_tag" href="#"><i class="remove glyphicon glyphicon-remove-sign glyphicon-white"></i></a></span>'
					$('#ajax_tags_div').append($elm)
				}
				$('#prd_tags').val('')
			}
	});
	$('.remove_tag').on('click',function(){
		$elm = $(this).parent()
		$elm.fadeOut(500, function(){
			$elm.remove();
		})	
		return false
	});
	$( ".effect" ).hide()
	function runEffect() {
		var selectedEffect = 'slide'; //drop
		var options = {direction: "down"};
		$( ".effect" ).toggle( selectedEffect, options, 'slow' );
	};
	$( "#filter_button" ).click(function() {
		runEffect();
    });
	if($('#toggle_filter').val() == 1)
	{
		$( "#filter_button" ).click()	
	}	
	$(document).on('click','.hide_prd',function(){
		$id = $(this).attr('data-hide-id')
		$url = $(this).attr('data-url')
		$clause = $(this).attr('data-clause')
		$scale = $(this).attr('data-scale')
		
		var hide_str = $('#product_stat').find('option[value="HIDDEN"]').text();
		hide_pos = hide_str.indexOf("(") + 1;
		var hide_int = hide_str.slice(hide_pos, -1);
	
		var visible_str = $('#product_stat').find('option[value="VISIBLE"]').text();
		visible_pos = visible_str.indexOf("(") + 1;
		var visible_int = visible_str.slice(visible_pos, -1);
		
		var $this = $(this)
		//$this.hide()
		$this.parent().parent().closest('tr').find('td:last .last_td').hide()
		$this.parent().parent().closest('tr').find('td:last').append('<div align="center"><h3><span class="glyphicon glyphicon-refresh animate-spinning"></span></h3></div>')
		$this.parent().parent().closest('tr').find('td:not(:last-child)').css({'background' : '#fff'})
		$this.parent().parent().closest('tr').find('td:last').css({'background-color': 'rgba(217,83,79,0.7)'})
		$.ajax({ 
			url: $url,
			data: { clause: $clause, id : $id, scale : $scale, csrf_test_name: $("input[name=csrf_test_name]").val()},
			type: "POST",
			context: $this,
			success: function(data){
				if(data == 1)
				{
					$this.parent().parent().closest('tr').hide(1000,function(){
						$this.parent().parent().closest('tr').remove();										
					});	
					if($clause == 1)
					{
						$v_int = parseInt(visible_int)+1
						$('#product_stat').find('option[value="VISIBLE"]').text("Visible Products ("+$v_int+")")	
						$('#product_stat').find('option[value="HIDDEN"]').text("Hidden Products ("+(parseInt(hide_int)-1)+")")	
					} else {
						$h_int = parseInt(hide_int)+1
						$('#product_stat').find('option[value="HIDDEN"]').text("Hidden Products ("+$h_int+")")	
						$('#product_stat').find('option[value="VISIBLE"]').text("Visible Products ("+(parseInt(visible_int)-1)+")")	
					}
					$('#product_panel .panel-footer').remove()				
				} else {
					$this.parent().parent().closest('tr').find('td:last').append('<div align="center">Oops! Try again</div>')
					$this.parent().parent().closest('tr').find('td:last').css({'background-color': 'rgba(217,83,79,0.4)'})
					$this.hide()
				}
			},
			error: function(xhr,textStatus, errorThrown) {
				console.log(xhr.responseText);
			}
		});
		
		return false
	});
//	var table = $('#product_table').DataTable({
//		responsive: true,
//		paging: false,
//		info: false,
//		scrollCollapse: true,
//		scrollX: '1450px',
//		//scrollY: '500px',
//		ordering: false,
//		"bAutoWidth": false, // this is important to resize width during orientation
//		searching: false,
//		columnDefs: [
//            { width: '10%', targets: 0 },
//            { width: '15%', targets: 1 },
//            { width: '15%', targets: 2 },
//            { width: '15%', targets: 3 },
//            { width: '15%', targets: 4 },
//            { width: '10%', targets: 5 },
//            { width: '10%', targets: 6 },
//            { width: '25%', targets: 7 },
//        ]
//	});

});
