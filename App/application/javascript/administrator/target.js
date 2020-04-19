// JavaScript Document
$(document).ready(function(){
	var cct = $("input[name=csrf_test_name]").val();
	$('td.edit').click(function(){
		  $('.ajax').html($('.ajax input').val());
		  $('.ajax').removeClass('ajax');
		  $(this).addClass('ajax');
		  $(this).html('<input id="editbox" title="Hit Enter Key After Changes!" size="'+$(this).text().length+'" type="text" value="' + $(this).text() + '" class="editbox_orange"> ');
		  $('#editbox').focus();
	});
	$('td.edit').keydown(function(event){
		arr = $(this).attr('class').split( " " );
		 switch(arr[1]){
			case "target":
			zone = "target"
			break;
			case "perk":
			zone = "perk"
			break;
		 }
		root = $('#target_root').val()
		val = $('.ajax input').val();
		 if(event.which == 13)
		 { 
			$.ajax({    
				type: "POST",
				dataType:"json",
				url: root,
				data:{value : val , id : arr[2], pole : zone, csrf_test_name : cct},
				async: false,
				cache:false,
				success: function(data){
					if(data)
					{
						$('.ajax').html($('.ajax input').val());
						$('.ajax').removeClass('ajax');
						location.reload()
						//alert(data)
					} else {
						location.reload()
					}
				},
				error: function(jqXHR,textStatus,errorThrown)
				{alert(textStatus + " " + errorThrown)} 
			});
		 }
	});
	$('#editbox').live('blur',function(){
		 $('.ajax').html($('.ajax input').val());
		 $('.ajax').removeClass('ajax');
	});
	$('.delbutton').click(function(){
		tarid = $(this).attr('id')
		root = $('#del_tar_root').val()
		if(confirm($(this).attr('title')+'?'))
		{
			$.ajax({    
				type: "POST",
				dataType:"json",
				url: root,
				data:{id : tarid, csrf_test_name : cct},
				cache:false,
				async: false,
				success: function(data){
					if(data)
					{
						$('.ajax').html($('.ajax input').val());
						$('.ajax').removeClass('ajax');
						location.reload()
						//alert(data)
					} else {
						location.reload()
					}
				},
				error: function(jqXHR,textStatus,errorThrown)
				{alert(textStatus + " " + errorThrown)} 
			});
		}
	});
});