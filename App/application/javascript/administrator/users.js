$(function(){
	csrf =  $("input[name=csrf_test_name]").val()
	var res = {
		//loader: $('<span><img src="'+$('#small-loader').val()+'"><span />',{class: 'small-loader' })
		loader: $('<span class="glyphicon glyphicon-repeat animate-spinning"></span>')
	};
    $('.set_target').on({
		focus: function(){
			var $this = $(this)
			$this.data('val',  $this.val() ); // save value
			$(window).off("resize");
		},
		keyup: function(e){
			var $this = $(this)
			if(e.keyCode == 13)
			{	
				if( $this.val() != $this.data('val') ){ //post only new values
					$this.data('val',  $this.val() ); 
					$this.change(); 
					var new_val = $this.val()
					var target_id = $this.attr('target-id')
					var emp_id = $this.attr('data-id')
					$.ajax({ 
						url: $('#user_url').attr("data-url"),
						data: { new_val: new_val, target_id: target_id, emp_id: emp_id, csrf_test_name: csrf},
						type: "POST",
						context: $this,
						beforeSend: function(){
							$this.next('span.t_load').html('').append(res.loader)
						},
						success: function(data){
							$this.parent().find(res.loader).remove();
							if(data == 1)
							{
								$this.prev('.input-group-addon').parent().addClass('has-success')
								$this.next('span.t_load').html('<span class="glyphicon glyphicon-ok"></span>')
								$this.parent().parent().parent().parent().next().find('.set_target').focus()
							}
						}
					});
				}
				$this.parent().next().find('.set_target').focus()
			}
		}
    });	
	var table = $('#user_table').DataTable({
		responsive: true,
		"paging": false,
		"info": false,
		scrollX: '1500px',
		scrollY: '500px',
		scrollCollapse: true,
		"ordering": false,
		"searching": false,
		"bAutoWidth": false, // this is important for resize width during orientation
		columnDefs: [
            { width: '5%', targets: 0},
            { width: '10%', targets: 1 },
            { width: '10%', targets: 2 },
            { width: '5%', targets: 3 },
            { width: '10%', targets: 4 },
            { width: '10%', targets: 5 },
            { width: '10%', targets: 6 },
            { width: '10%', targets: 7 },
            { width: '10%', targets: 8 },
            { width: '10%', targets: 9 },
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
