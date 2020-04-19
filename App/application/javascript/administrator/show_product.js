$(function(){
	$('[data-toggle="popover"]').popover({ trigger: "hover",container: 'body' });	
	var csrf = $("input[name=csrf_test_name]").val();
	var merch_id = $('#merchant_id').val()
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
		runEffect()
	}	

	//$('.var_columns').hide();
	$('.var_columns').each(function() {
		if($(this).text() == "")
		{
			$(this).hide()
		}
	});
//	colspan_row = Number($('th.var_columns:visible').size())
//	$('.colspan_row').attr('colspan',colspan_row+2)
	$('.toggle').hide();
	$('.togglelink').on('click', function (e) {
		e.preventDefault();
		var $this = $(this)
		if($this.parent().parent().next(".toggle").is(':visible'))
		{
			$this.parent().parent().nextUntil('.no-hover').hide('slow');
			$this.find('i').removeClass('fa-caret-down')
			$this.find('i').addClass('fa-caret-up')
		} else {
			$this.parent().parent().nextUntil('.no-hover').show('slow');
			$this.find('i').removeClass('fa-caret-up')
			$this.find('i').addClass('fa-caret-down')
		}
	});
	
	$( "#date_start ,#date_end" ).datepicker({  
  			maxDate: new Date(),
			dateFormat: 'dd-M-yy',  
			yearRange: "-5:+0",
			changeYear: true,
			changeMonth: true,
			beforeShow: function(){    
				$(".ui-datepicker").css({'font-size' : 12}) 
				$(".ui-state-highlight").css({'background' : '#ffb951'}) 
			}
	});
	$( ".effect" ).hide();
	$( "#button" ).click(function() {
      runEffect();
    });
	$('#variant_table').sortable({
		items: 'tbody.sortable-row',
		cursor: "move",
		revert: true,
		forcePlaceholderSize: true,
		forceHelperSize: true,
		placeholder: "sortable-placeholder",
		start: function(e, ui ){
			ui.placeholder.height(ui.helper.outerHeight());
		},
		update: function( event, ui ) {
			var udata = $(this).sortable('toArray');	
			$.ajax({ 
				url: $('#update_variant_url').val(),
				data: { csrf_test_name : csrf, prd_params : udata, merchant_id : merch_id},
				type: "POST",
				success: function(data){

				},
				error: function(xhr,textStatus, errorThrown) {
					console.log(errorThrown);
				} 		
			})
		}				
	}).disableSelection();
});
