$(function(){
	$('[data-toggle="popover"]').popover({ trigger: "hover",container: 'body' });	
	$( ".effect" ).hide()
	function runEffect() {
		  var selectedEffect = 'slide'; //drop
		  var options = {direction: "down"};
		  $( ".effect" ).toggle( selectedEffect, options, 500 );
	};
	$( "#filter_button" ).click(function() {
      runEffect();
    });
	if($('#toggle_filter').val() == 1)
	{
		$( "#filter_button" ).click()	
	}
	$( "#dob_date,#ann_date" ).datepicker({  
  			maxDate: new Date(),
			dateFormat: 'dd-M-yy',  
			yearRange: "-100:+0",
			changeYear: true,
			changeMonth: true,
			beforeShow: function(){    
				$(".ui-datepicker").css({'font-size' : 12}) 
				$(".ui-state-highlight").css({'background' : '#ffb951'}) 
			}
	});
	$( "#date_before,#date_after" ).datepicker({  
  			maxDate: new Date(),
			dateFormat: 'dd-M-yy',  
			changeYear: true,
			changeMonth: true,
			beforeShow: function(){    
				$(".ui-datepicker").css({'font-size' : 12}) 
				$(".ui-state-highlight").css({'background' : '#ffb951'}) 
			}
	});
	var table = $('#customers_table').DataTable({
		responsive: true,
		paging: false,
		info: false,
		scrollCollapse: true,
		scrollX: '1200px',
		//scrollY: '500px',
		ordering: false,
		"bAutoWidth": false, // this is important to resize width during orientation
		searching: false,
		columnDefs: [
            { width: '10%', targets: 0 },
            { width: '12%', targets: 1 },
            { width: '10%', targets: 2 },
            { width: '10%', targets: 3 },
            { width: '10%', targets: 4 },
            { width: '12%', targets: 5 },
            { width: '10%', targets: 6 },
            { width: '10%', targets: 7 },
            { width: '10%', targets: 8 },
            { width: '2%', targets: 9 },
        ]
	});
	new $.fn.dataTable.FixedColumns( table, {
        leftColumns: 1,
    });
	$( window ).resize(function() {
		new $.fn.dataTable.FixedColumns( table, {
			leftColumns: 1,
		});
	});
});
