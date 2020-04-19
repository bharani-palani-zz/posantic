$(document).ready(function () {
	$( "#accordion" ).accordion({heightStyle: "content" });
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
					addMarker(myLatlng, 'Mark your location', map);
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
			addMarker(myLatlng, 'I am here', map);

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
