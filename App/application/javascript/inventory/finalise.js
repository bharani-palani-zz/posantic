$(function(){
	$('.stock_take_list').DataTable({
		searching: false,
		"bLengthChange": false,
		"responsive": true,
		"aoColumnDefs": [ { "bSortable": false, "aTargets": [ 1 ], "width": '1%' } ],
		"bPaginate" : false
	});
	$('.DataTables_sort_icon').remove();
	$('#control_select').on('change',function(){
		if($(this).is(':checked'))
		{
			$('.selected_product').prop('checked',true)	
			$('#complete').removeClass('disabled')
		} else {
			$('.selected_product').prop('checked',false)				
			$('#complete').addClass('disabled')
		}
	});
	$(document).on('change','.selected_product',function(){
		var len = $('input[name="selected_product[]"]:checked').length;
		if(len > 0)
		{
			$('#complete').removeClass('disabled')
		} else {
			$('#complete').addClass('disabled')
		}
	});
	$('#complete').on('click',function(){
		$cont = '<div id="completeModal" class="modal modal-sm fade container" role="dialog" style="padding-top:15%">'
		$cont += '<div class="modal-content">'
		$cont += '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'
		$cont += '<h4 id="dataConfirmLabel">Are you sure ..</h4></div>'
		$cont += '<div class="modal-body">You&lsquo;ve finished counting?? Inventory stock count of concerned outlet products will be updated now..</div>'
		$cont += '<div class="modal-footer"><button class="btn btn-danger" data-dismiss="modal" aria-hidden="false"><i class="fa fa-remove"></i> Cancel</button>'
		$cont += '<button type="button" class="btn btn-success" id="confirm_complete"><i class="fa fa-check fa-fw"></i>OK</button></div></div></div>'
		$('body').append($cont);
		$('#completeModal').modal({show:true,backdrop: 'static'});
		return false;
	});
	$(document).on('click','#confirm_complete',function(){
		$('#form_inv_complete').submit()
	});
});
