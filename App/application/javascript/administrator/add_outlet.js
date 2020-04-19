$(function(){
	$('[data-toggle="popover"]').popover({ trigger: "hover",container: 'body' });	
	$('#has_register').on('switchChange.bootstrapSwitch', function (e,data) {
		if(data)
		{
			$( ".reg_div" ).show('slow');	
			$('#insert_outlet').html('<i class="fa fa-shopping-cart"></i> Create Outlet With Register')
		} else {
			$( ".reg_div" ).hide('slow');	
			$('#insert_outlet').html('<i class="fa fa-shopping-cart"></i> Create Outlet Only')
		}
	});
});
