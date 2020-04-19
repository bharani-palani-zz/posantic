$(function(){
	$("#range_start,#range_end,#range_before,#range_after").attr('readonly','readonly').css('background','#fff')
	$("#range_before,#range_start,#range_end,#range_after" ).datepicker({  
		maxDate: new Date(),
		dateFormat: 'dd-M-yy',  
		changeYear: true,
		changeMonth: true,
		beforeShow: function(){    
			$(".ui-datepicker").css({'font-size' : 12}) 
			$(".ui-state-highlight").css({'background' : '#ffb951'}) 
		}
	});
	$("#range_before,#range_after").focus(function(){
		$('.range_singles').not(this).attr('disabled','disabled').css('background','#ddd')
	});
	$("#range_start,#range_end").focus(function(){
		$("#range_before,#range_after").attr('disabled','disabled').css('background','#ddd')
	});
	$('#reset').click(function(){
		$('input[type="text"]').val('').attr('disabled',false).css('background','#fff')
		$('#check_storage').attr('disabled',false)
		$('#manage_div').hide();
		$('input[type="text"]').datepicker({
			maxDate: new Date(),
			dateFormat: 'dd-M-yy',  
			changeYear: true,
			changeMonth: true,
			beforeShow: function(){    
				$(".ui-datepicker").css({'font-size' : 12}) 
				$(".ui-state-highlight").css({'background' : '#ffb951'}) 
			}
		});
	});
	$('#manage_div').hide();
	$('#check_storage').on('click',function(){
		$before = $('#range_before').val()
		$after = $('#range_after').val()
		$start = $('#range_start').val()		
		$end = $('#range_end').val()	
		if($before.length > 0 || $after.length > 0 || $start.length > 0 || $end.length > 0)
		{
			var inputs = [
				[$before],
				[$start,$end],
				[$after]
			];			
			$.ajax({ 
				url: $('#manage_space_url').val(),
				data: { post: inputs/*, csrf_test_name : $("input[name=csrf_test_name]").val()*/},
				type: "GET",
				beforeSend: function(data){
					elm = '<i class="fa fa-circle-o-notch fa-spin"></i>'
					$('#loader').html(elm)
				},
				success: function(data){
					obj = JSON.parse(data)
					if(parseInt(obj.count) > 0)
					{
						$('.trx_count').text(obj.count)
						$('.trx_string').text(obj.string)
						$('#risk_check').prop('checked', false).triggerHandler('click');
						$('#manage_div').show()
						$('input[type="text"]').datepicker("destroy");

					}
				},
				error: function(xhr,textStatus, errorThrown) {
					alert(errorThrown);
				}, 		
				complete: function() {
					$('#loader').empty()
				}
			});
			$(this).attr('disabled','disabled')
		}
	});
	$('#risk_check').on('click',function(){
		if($(this).prop('checked') == false)
		{
			$('#manage_space').addClass('disabled')
		} else {
			$('#manage_space').removeClass('disabled')
		}
	});
});
