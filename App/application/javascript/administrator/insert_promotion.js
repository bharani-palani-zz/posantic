$(function(){
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
});
