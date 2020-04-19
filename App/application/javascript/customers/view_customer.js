$(document).ready(function() {
	$( "#date_start ,#date_end" ).datepicker({  
  			maxDate: new Date(),
			dateFormat: 'dd-M-yy',  
			changeYear: true,
			changeMonth: true,
			beforeShow: function(){    
				$(".ui-datepicker").css({'font-size' : 12}) 
				$(".ui-state-highlight").css({'background' : '#ffb951'}) 
			}
	});
	function runEffect() {
		  var selectedEffect = 'slide'; //drop
		  var options = {direction: "down"};
		  $( ".effect" ).toggle( selectedEffect, options, 500 );
	};
	$( ".effect" ).hide();
	$( "#filter_button" ).click(function() {
      runEffect();
    });
	if($('#toggle_filter').val() == 1)
	{
		$( "#filter_button" ).click()	
	}

	function initialize() {
		var zoom_int = Number($('#cust_geo_zoom').val());		
		var address = $('#cust_geo_address').val();
		var c_lat = parseFloat($('#c_lat').val());		
		var c_long = parseFloat($('#c_long').val());		
		if(c_lat == 0 || c_long == 0)
		{
			var geocoder = new google.maps.Geocoder();
			geocoder.geocode( { 'address': address}, function(results, status) {		
				if (status == google.maps.GeocoderStatus.OK) {
					c_lat = results[0].geometry.location.lat();
					c_long = results[0].geometry.location.lng();
					document.getElementById('c_lat').value = c_lat;
					document.getElementById('c_long').value = c_long;			
					var myLatlng = new google.maps.LatLng(c_lat,c_long);
					var myOptions = {
					  zoom: zoom_int,
					  center: myLatlng,
					  mapTypeId: google.maps.MapTypeId.ROADMAP
					}
					var map = new google.maps.Map(document.getElementById("map-container"), myOptions);
					addMarker(myLatlng, 'Mark customer location', map);
					
					var options = {
						render: 'div',
						ecLevel: 'H',
						minVersion: parseInt(6, 10),
						fill: '#333333',
						background: '#ffffff',
						text: 'http://maps.google.com/maps?q='+c_lat+','+c_long,
						size: parseInt(200, 9),
						radius: parseInt(10, 10) * 0.01,
						quiet: parseInt(1, 10),
						mode: parseInt(1, 10),
						mSize: parseInt(11, 10) * 0.01,
						mPosX: parseInt(0, 0) * 0.01,
						mPosY: parseInt(0, 0) * 0.01,
						label: '',
						fontname: 'Ubuntu',
						fontcolor: '#ff9818',
						image: ''
					};
					$("#qr-code-container").empty().qrcode(options);
					
				}
			});
		} else {
			var myLatlng = new google.maps.LatLng(c_lat,c_long);
			var myOptions = {
			  zoom: zoom_int,
			  center: myLatlng,
			  mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			var map = new google.maps.Map(document.getElementById("map-container"), myOptions);		
			addMarker(myLatlng, 'Mark customer location', map);


			var options = {
				render: 'div',
				ecLevel: 'H',
				minVersion: parseInt(6, 10),
				fill: '#333333',
				background: '#ffffff',
				text: 'http://maps.google.com/maps?q='+c_lat+','+c_long,
				size: parseInt(200, 9),
				radius: parseInt(10, 10) * 0.01,
				quiet: parseInt(1, 10),
				mode: parseInt(1, 10),
				mSize: parseInt(11, 10) * 0.01,
				mPosX: parseInt(0, 0) * 0.01,
				mPosY: parseInt(0, 0) * 0.01,
				label: '',
				fontname: 'Ubuntu',
				fontcolor: '#ff9818',
				image: ''
			};
			$("#qr-code-container").empty().qrcode(options);
		}	
	}
	function addMarker(latlng,title,map) {
		var marker = new google.maps.Marker({
				position: latlng,
				map: map,
				title: title,
				draggable:true
		});
	
		google.maps.event.addListener(marker,'drag',function(event) {
			document.getElementById('c_lat').value = event.latLng.lat();
			document.getElementById('c_long').value = event.latLng.lng();
		});
	
		google.maps.event.addListener(marker,'dragend',function(event) {
			document.getElementById('c_lat').value = event.latLng.lat();
			document.getElementById('c_long').value = event.latLng.lng();
		});
	}	
	google.maps.event.addDomListener(window, 'load', initialize);
});
