$(function(){
	tinymce.init({
			selector: "textarea",
			plugins : 'advlist autolink link image lists charmap textcolor',
			menubar : false,
			theme: "modern",
			menubar: "tools table format view insert edit",
			resize: false,
			toolbar: "styleselect | bold italic | bullist numlist outdent indent | forecolor backcolor ", 
	});	
	$('#header_type').on('change',function(){
		var optionSelected = Number($(this).find("option:selected").val());
		$("#myCarousel").carousel(optionSelected-1).carousel("pause");
	});
	var settings = {
	output:'css',
		bgColor: '#FFFFFF',
		color: '#000',
		barWidth: 2,
		barHeight: 30,
		moduleSize: 5,
		posX: 10,
		posY: 20,
		addQuietZone: 1
	};	
	function randomString(len, charSet) {
		charSet = charSet || 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		var randomString = '';
		for (var i = 0; i < len; i++) {
			var randomPoz = Math.floor(Math.random() * charSet.length);
			randomString += charSet.substring(randomPoz,randomPoz+1);
		}
		return randomString;
	}
	value = randomString(5, '12345689');
	$("#barcodeTarget").html("").show().barcode(value, 'code128', settings);


	$('input[id="show_barcode_true"]').bootstrapSwitch('state') == true ? $("#barcodeTarget").show() : $("#barcodeTarget").hide()
	$('input[name="show_barcode"]').on('switchChange.bootstrapSwitch',function(event, state){
		if(this.value == 10)
		{
			$("#barcodeTarget").show()
		} else {
			$("#barcodeTarget").fadeOut(500)			
		}
	});


	
})
