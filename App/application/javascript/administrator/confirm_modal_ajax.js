/*Confirm modal and ajax refresh pageload*/
$(document).ready(function(){
	$('a[data-confirm]').on('click',function(ev) {
		var href = $(this).attr('href');
		if (!$('#dataConfirmModal').length) {
			$cont = '<div id="dataConfirmModal" class="modal modal-sm fade container" role="dialog" style="padding-top:15%">'
			$cont += '<div class="modal-content">'
			$cont += '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'
			$cont += '<h4 id="dataConfirmLabel">Are you sure ..</h4></div>'
			$cont += '<div class="modal-body"></div>'
			$cont += '<div class="modal-footer"><button class="btn btn-danger" data-dismiss="modal" aria-hidden="false"><i class="fa fa-remove"></i> Cancel</button>'
			$cont += '<a class="btn btn-success" id="dataConfirmOK"><i class="fa fa-check"></i> OK</a></div></div></div>'
			$('body').append($cont);
		} 
		$('#dataConfirmModal').find('.modal-body').html($(this).attr('data-confirm'));
		$('#dataConfirmOK').attr('href', href);
		$('#dataConfirmModal').modal({show:true,backdrop: 'static'});
		return false;
	});
	/*This refreshes modal content to get updated content on ajax call*/
	var loadingContent = '<div class="modal-header"><h3 align="center">Please wait...</h3></div><div class="modal-body"><div align="center"><i class="fa fa-circle-o-notch fa-spin fa-3x"></i></div></div>';	
	$("body").on("show.bs.modal", function (e) {
		if($('#dataConfirmModal').length < 1 && typeof $(e.relatedTarget).attr('href') != "undefined")
		{
			$(this).find(".modal-content").html(loadingContent);        
			var link = $(e.relatedTarget);
			$(this).find(".modal-content").load(link.attr("href"));
			// clear modal after 5 minutes i.e, 5000 milliseconds
//			setTimeout(function(){
//			  $('.modal').modal('hide')
//			}, 300000);	
		}
    }); 
	$('body').on('hidden.bs.modal', '.modal', function () {
	  $(this).removeData('bs.modal');
	});
	/*Page loading model with progress bar*/
	$('.loading_modal').on('click',function(){
		$cont = '<div class="modal fade" id="loading_modal">'
		$cont += '<div class="modal-dialog modal-md">'
					$cont += '<div class="progress">'
						$cont += '<div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">Loading... please wait</div>'
					$cont += '</div>'
			$cont += '</div>'
		$cont += '</div>'
		$('body').append($cont);
		$('#loading_modal').modal({show:true,backdrop: 'static'})
	})
	$('body form').on('submit',function(){
		$cont = '<div class="modal fade" id="loading_modal">'
		$cont += '<div class="modal-dialog modal-md">'
					$cont += '<div class="progress">'
						$cont += '<div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">Loading... please wait</div>'
					$cont += '</div>'
			$cont += '</div>'
		$cont += '</div>'
		$('body').append($cont);
		$('#loading_modal').modal({show:true,backdrop: 'static'})
	})
	
});