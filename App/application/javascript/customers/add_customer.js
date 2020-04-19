$(function(){
	tinymce.init({
			selector: "textarea",
			width: '100%',
			height: 100,
			plugins : 'advlist autolink link image lists charmap textcolor',
			menubar : false,
			theme: "modern",
			menubar: "tools table format view insert edit",
			resize: false,
			toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor ", 
	});	
	$('[data-toggle="popover"]').popover({ trigger: "hover",container: 'body' });
});
