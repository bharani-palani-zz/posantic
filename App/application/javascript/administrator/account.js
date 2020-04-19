$(function(){
	$('.tooltips').tooltip({
		html: true, 
	}); 
	$('#payment_summary').hide()
	$('.select_plan').on('click',function(){ 
		$('.select_term').removeClass('btn-success')
		var plan_id = $(this).attr('data-plan-id');
		$sel_loader = $(this).find('.select_loader')
		$.ajax({ 
			url: $('#find_plan_pricing_url').val(),
			data: { plan_index: plan_id, csrf_test_name : $("input[name=csrf_test_name]").val()},
			type: "GET",
			beforeSend: function(data){
				elm = '<i class="fa fa-circle-o-notch fa-spin"></i>'
				$('#act_plan_str').html(elm)
				$('#act_plan_price').html(elm)
				$('#act_reg_per_price').html(elm)
				$('#save_span').html(elm)
				$('#save_reg_span').html(elm)
				$('#total_savings').html(elm)
				$('#register_tot_pricing').html(elm)
				$('#total_pricing').html(elm)
				$('#pay_term').html(elm)
				$('#term_pricing').html(elm)
				$sel_loader.html(elm)

				$('#discount_string').hide()
				$('#discount_field').hide()
				$('#p_code').val('').triggerHandler('keyup');
			},
			success: function(data){
				obj = JSON.parse(data)
				var sel_plan_code = obj.plan_code
				var sel_month_plan_price = obj.monthly_price
				var sel_month_reg_price = obj.register_cost_monthly
								
				var current_reg_count = parseInt(obj.reg_count)
				var monthly_reg_pricing = current_reg_count * parseFloat(sel_month_reg_price)
				total_pricing = parseInt(monthly_reg_pricing) + parseInt(sel_month_plan_price)
				
				$('#act_reg_per_price').text(thousand_seperator(sel_month_reg_price))
				$('#total_pricing').text(thousand_seperator(total_pricing))
				$('#term_pricing').text(thousand_seperator(total_pricing))
				$('#register_tot_pricing').text(thousand_seperator(monthly_reg_pricing))
				$('#act_plan_str').text(sel_plan_code)
				$('#act_plan_price').text(thousand_seperator(parseInt(sel_month_plan_price)))
				$('#act_reg_count').text(current_reg_count)
				$('#save_span').text(0)
				$('#save_reg_span').text(0)
				$('#term_num').text(1)
				$('#total_savings').text(0)
				$('.handle_plan').attr('id',plan_id)
				$('.handle_plan').attr('data-plan',sel_plan_code)
				$('#pay_term').text('Month')
				
				$('.btn_monthly').addClass('btn-success')
				$('#opted_plan').val(plan_id)
				$('#opted_term').val(1)
				$('#opted_price').val(total_pricing)
				$('#check_agree_terms').prop('checked', false)
				$('.submit_post').addClass('disabled')
				pricing_validation()
			},
			error: function(xhr,textStatus, errorThrown) {
				alert(errorThrown);
			}, 		
			complete: function() {
				var start = new Date;
				var max_sec = 300
				$('#payment_summary').show()
				var my_set = setInterval(function() {
					var rem_sec = max_sec - Math.floor((new Date - start) / 1000);
					var minutes = parseInt( rem_sec / 60 ) % 60;
					var seconds = rem_sec % 60;		
					var res = minutes + ":" + (seconds  < 10 ? "0" + seconds : seconds);
					$('#time_lapse').text(res);
				},1000);
				var my_timeout = setTimeout(function() {
					clearInterval(my_set);
					$('#payment_summary').fadeOut('fast');
					$('#time_lapse').empty();
				}, max_sec * 1000);						
				if($('#time_lapse').text() != '') {
					clearInterval(my_set);
					clearTimeout(my_timeout)
				}
				$sel_loader.html('')
				$('html,body').animate({scrollTop: $('#payment_summary').offset().top},1000);
			} 		
		 });
	});
	$('.select_term').on('click',function(){
		if($(this).hasClass('btn-success') == false)
		{
			$('.select_term').removeClass('btn-success')
			$(this).addClass('btn-success')
			var term = $(this).attr('id')
			var plan_id = $('.handle_plan').attr('id')
			$.ajax({ 
				url: $('#find_term_pricing_url').val(),
				data: { plan_index: plan_id, termed: term, csrf_test_name : $("input[name=csrf_test_name]").val()},
				type: "GET",
				beforeSend: function(data){
					elm = '<i class="fa fa-circle-o-notch fa-spin"></i>'
					$('#payment_summary #plan_content').append($('<div align="center" style="position:absolute; margin-left:200px; z-index:1;">'+elm+'</div>').addClass('big_loader').css({'font-size':'50px'})).css('opacity','0.5')
					$('#act_plan_str').html(elm)
					$('#act_plan_price').html(elm)
					$('#act_reg_per_price').html(elm)
					$('#act_reg_count').html(elm)
					$('#save_span').html(elm)
					$('#save_reg_span').html(elm)
					$('#total_savings').html(elm)
					$('#register_tot_pricing').html(elm)
					$('#total_pricing').html(elm)
					$('#pay_term').html(elm)
					$('#term_num').html(elm)
					$('#term_pricing').html(elm)
					
					$('#discount_string').hide()
					$('#discount_field').hide()
					
				},
				success: function(data){
					obj = JSON.parse(data)
					var plan_code = obj.plan_code
					var plan_price = obj.plan_price
					var save_price = obj.save_price
					var reg_price = obj.reg_price
					var reg_count = obj.reg_count
					var save_reg_price = obj.save_reg_price
					var total_savings = obj.total_savings
					var tot_reg_price = obj.tot_reg_price
					var total_pricing = obj.total_pricing
					var term_pricing = obj.term_pricing
					
					$('#act_plan_str').text(plan_code)
					$('#act_plan_price').text(thousand_seperator(plan_price))
					$('#act_reg_per_price').text(thousand_seperator(reg_price))
					$('#act_reg_count').text(reg_count)
					$('#save_span').text(thousand_seperator(save_price))
					$('#save_reg_span').text(thousand_seperator(save_reg_price))
					$('#total_savings').text(thousand_seperator(total_savings))
					$('#register_tot_pricing').text(thousand_seperator(tot_reg_price))
					$('#total_pricing').text(thousand_seperator(total_pricing))
					var plan_str = { 1: "Month", 3: "Quarter", 6: "Bi-annual", 12: "Annual"};
					$('#pay_term').text(plan_str[term])
					$('#term_num').text(term)
					$('#term_pricing').text(thousand_seperator(term_pricing))
					
					$('#opted_plan').val(plan_id)
					$('#opted_term').val(term)
					$('#opted_price').val(term_pricing)
					$('#check_agree_terms').prop('checked', false)
					$('.submit_post').addClass('disabled')
					$('.cc_post').removeClass('disabled')			
					pricing_validation()
				},
				error: function(xhr,textStatus, errorThrown) {
					alert(errorThrown);
				},
				complete: function(){
					$('#payment_summary #plan_content .big_loader').remove()
					$('#payment_summary #plan_content').css('opacity','1')					
					$('#p_code').val('').triggerHandler('keyup');
				}
			})
		}
	});
	$('#check_agree_terms').click(function(){
		if($(this).prop('checked') == false)
		{
			$('.submit_post').addClass('disabled')
			$('.cc_post').removeClass('disabled')			
		} else {
			$('.submit_post').removeClass('disabled')			
			$('.cc_post').addClass('disabled')
		}
		pricing_validation()
	});
	$('#p_code').keyup(function() {
		if($(this).val().length == 0)
		{
			$('#verify_p_code').addClass('disabled')			
		} else {
			$('#verify_p_code').removeClass('disabled')						
		}
    });
	$('#verify_p_code').on('click',function(){
		var $p_code = $('#p_code').val()
		var term = $('.select_term[class~="btn-success"]').attr('id')
		var plan_id = $('.handle_plan').attr('id')

		$.ajax({ 
			url: $('#find_code_discount_url').val(),
			data: { p_code: $p_code, plan_index: plan_id, termed: term, csrf_test_name : $("input[name=csrf_test_name]").val()},
			type: "POST",
			beforeSend: function(data){
				elm = '<i class="fa fa-circle-o-notch fa-spin"></i>'
				$('#term_pricing').html(elm)
			},
			success: function(data){
				obj = JSON.parse(data)
				var status = obj.status
				var discount = parseFloat(obj.discount)
				term_pricing = parseInt(obj.term_pricing)
				$('#discount_string').show().text(status)
				$('#discount_field').show().text(' @ '+discount+'% discount')
				discount_pricing = Math.round(term_pricing - (term_pricing * discount/100))
				$('#term_pricing').text(thousand_seperator(discount_pricing))
				$('#opted_price').val(discount_pricing)
			},
			error: function(xhr,textStatus, errorThrown) {
				alert(errorThrown);
			},
			complete: function(){
				$('#verify_p_code').addClass('disabled')
			}
		});
	});
});
function pricing_validation() {
	if(isNumeric($('#opted_price').val()) == false)
	{
		$('.submit_post').addClass('disabled')
		$('.cc_post').attr('data-original-title','Plan not available')			
	}
	if(parseInt($('#opted_price').val()) < 0)
	{
		$('.submit_post').addClass('disabled')
		$('.cc_post').attr('data-original-title','Pricing cant be negative')			
	}
}
function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
function thousand_seperator(str)
{
	var curr = $('#merchant_currency').val();	
	return curr == "INR" ? indian_thousand_seperator(str) : global_thousand_seperator(str)
}
function indian_thousand_seperator(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	var z = 0;
	var len = String(x1).length;
	var num = parseInt((len/2)-1);	
	while (rgx.test(x1))
	{
		if(z > 0)
		{
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		} else {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
			rgx = /(\d+)(\d{2})/;
		}
		z++;
		num--;
		if(num == 0)
		{
			break;
		}
	}
	return x1 + x2
}
function global_thousand_seperator(str)
{
	return str.toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")	
}
