
jQuery(document).ready(function() {
	
    /*
        Fullscreen background
    */
    $.backstretch($('#hid_base_url').val()+"application/style/login/img/backgrounds/1.jpg");
    
    /*
        Form validation
    */
    $('.login-form input[type="text"], .login-form input[type="password"]').on('focus', function() {
    	$(this).removeClass('input-error');
    });
    
    $('.login-form').on('submit', function(e) {
    	$(this).find('input[type="text"], input[type="password"]').each(function(){
    		if( $(this).val() == "" ) {
    			e.preventDefault();
    			$(this).addClass('input-error');
    		}
    		else {
    			$(this).removeClass('input-error');
    		}
    	});
    });
	
	$('#main_error').hide()
	$('#error_pass').hide()
	$('#error_c_pass').hide()
	$('#change_form').submit(function(){
		if($('#password').val().length > 0)	
		{
			if($('#password').val().length < 8)	
			{
				$('#main_error').hide()
				$('#error_pass').show()
				return false
			} else if($('#password').val() != $('#c_password').val()) {
				$('#main_error').hide()
				$('#error_pass').hide()
				$('#error_c_pass').show()
				return false
			}
		} else {
			$('#main_error').show()
			$('#error_pass').hide()
			$('#error_c_pass').hide()
			return false	
		}
	});
	
});
