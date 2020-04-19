$(document).ready(function() {
	var zoom_int = Number($('#cust_geo_zoom').val());		
	var address = $('#cust_geo_address').val();

	var geocoder = new google.maps.Geocoder();
	geocoder.geocode( { 'address': address}, function(results, status) {		
		if (status == google.maps.GeocoderStatus.OK) {
			c_lat = results[0].geometry.location.lat();
			c_long = results[0].geometry.location.lng();
			var myLatlng = new google.maps.LatLng(c_lat,c_long);
			var myOptions = {
			  zoom: zoom_int,
			  center: myLatlng,
			  mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			var map = new google.maps.Map(document.getElementById("map-container"), myOptions);
			addMarker(myLatlng, 'Mark customer location', map);
								
		}
	});
	function addMarker(latlng,title,map) {
		var marker = new google.maps.Marker({
				position: latlng,
				map: map,
				title: title,
				draggable:true
		});
	
	}	


});