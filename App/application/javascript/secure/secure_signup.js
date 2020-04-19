$(document).ready(function () {
var navListItems = $('div.setup-panel div a'),
	allWells = $('.setup-content'),
	allNextBtn = $('.nextBtn');

	allWells.not('#step-1').hide();

	navListItems.click(function (e) {
		e.preventDefault();
		var $target = $($(this).attr('href')),
		$item = $(this);
		
		if($target.css('display') == 'none')
		{	
			if (!$item.hasClass('disabled')) {
				navListItems.removeClass('btn-primary').addClass('btn-default');
				$item.addClass('btn-primary');
				allWells.hide();
				$target.toggle( "slide" );
				$target.find('input:eq(0)').focus();
			}
		}
	});

  allNextBtn.click(function(){
	  var curStep = $(this).closest(".setup-content"),
		  curStepBtn = curStep.attr("id"),
		  nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
		  curInputs = curStep.find("input[type='text'],input[type='url'],textarea[textarea]"),
		  isValid = true;

	  $(".form-group").removeClass("has-error");
	  for(var i=0; i<curInputs.length; i++){
		  if (!curInputs[i].validity.valid){
			  isValid = false;
			  $(curInputs[i]).closest(".form-group").addClass("has-error");
		  }
	  }

	  if (isValid)
		  nextStepWizard.removeAttr('disabled').trigger('click');
  });

  $('div.setup-panel div a.btn-primary').trigger('click');	
	
	$('.b_type').on('click',function(){
		$('.btype_checked').html('')
		btype_id = $(this).attr('data-id')
		$('#business_type').val(btype_id)
		$(this).find('.btype_checked').html('<i class="fa  fa-check-square-o fa-fw"></i>')
	});

	$('.outlet_type').on('click',function(){
		$('.outlet_type_checked').html('')
		outlet_type_id = $(this).attr('data-id')
		$('#outlet_type').val(outlet_type_id)
		$(this).find('.outlet_type_checked').html('<i class="fa  fa-check-square-o fa-fw"></i>')
	});
	if (typeof google === 'object' && typeof google.maps === 'object') {
	google.maps.event.addDomListener(window, 'load', function () {
		var places = new google.maps.places.Autocomplete(document.getElementById('contact_city'));
		google.maps.event.addListener(places, 'place_changed', function () {
			var place = places.getPlace();
			var components = {}; 
			$.each(place.address_components, function(k,v1) {
				jQuery.each(v1.types, function(k2, v2){
					components[v2] = [v1.short_name,v1.long_name]
				});
			});			
			var zip = typeof components.postal_code == "object" ? components.postal_code[0] : "";
				$('#zip').val(zip)
			var street_number = typeof components.street_number == "object" ? components.street_number[0] : "";		
			var street_name = typeof components.route == "object" ? components.route[1] : "";		
				$('#address_1').val((street_number+" "+street_name).trim())
			var sublocality = typeof components.sublocality == "object" ? components.sublocality[0] : "";		
				$('#address_2').val(sublocality)
			var city = typeof components.locality == "object" ? components.locality[0] : "";
				$('#city').val(city)
			var state = typeof components.administrative_area_level_1 == "object" ? components.administrative_area_level_1[1] : "";
				$('#state').val(state)
			var country = typeof components.country == "object" ? components.country[0] : "";
				$('#country').val(country)

			
			if(typeof place.geometry != "undefined")
			{
				var latitude = place.geometry.location.lat();
				var longitude = place.geometry.location.lng();
				$('#latitude').val(latitude)
				$('#longitude').val(longitude)
				timeStamp = Date.now()/1000
				var url = 'https://maps.googleapis.com/maps/api/timezone/json?location='+latitude+','+longitude+'&timestamp='+timeStamp;
				$.getJSON(url, function( responseJSON ) {
					$('#rawOffset').val(responseJSON.rawOffset)
					$('#dstOffset').val(responseJSON.dstOffset)
					$('#timeZoneId').val(responseJSON.timeZoneId)
					$('#timeZoneName').val(responseJSON.timeZoneName)
					$('#contact_city').blur()
				})
				.error(function() { 
					$('#rawOffset').val('')
					$('#dstOffset').val('')
					$('#timeZoneId').val('')
					$('#timeZoneName').val('')
				})
			} else {
				$('#contact_city').val('').focus()
				$('#rawOffset').val('')
				$('#dstOffset').val('')
				$('#timeZoneId').val('')
				$('#timeZoneName').val('')
			}
		});
	});
	}
	function check_fields()
	{
		if(
			$('#rawOffset').val().length > 0 &&
			$('#dstOffset').val().length > 0 &&
			$('#timeZoneId').val().length > 0 &&
			$('#timeZoneName').val().length > 0
		)
		{
			return true	
		} else {
			return false	
		}
	}

	$('#contact_city').validator({
		custom: {
			location: function ($el) {
				return ($el.val().length > 0 && check_fields() == true)  ? true : false
			}
		},
		errors: {
			location: "This Device is already exist",
		}						
	})					

	$('#form-1').on("keyup keypress", function(e){
		var code = e.keyCode || e.which;
		if (code == 13) { 
		  e.preventDefault();
		  return false;
		}
  	});
	$('#form-1').on('submit', function(){	
		$('#modal-3').modal({show:true, backdrop: 'static'})
		var inter = 2000
		$('#myCarousel').carousel({ interval: inter })
		$("#form-1").submit()
		$('#myCarousel').on('slid.bs.carousel', function () {
			var totalItems = $('.item').length;
			var currentIndex = $('div.active').index() + 1;
			if(totalItems == currentIndex){
				$(this).carousel("pause")
			}	
		})
	});
});
