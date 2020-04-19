$(function(){
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
	$("#from_date,#to_date" ).datepicker({  
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
