$(function(){
	var val = $('[name="enable_loyalty"]:checked').val();
	if(val == 20)
	{
		$('#loyalty_sale').attr('disabled',true)
		$('#loyalty_reward').attr('disabled',true)
	}
	$('[name="enable_loyalty"]').on('switchChange.bootstrapSwitch', function (e,data) {
		if(e.currentTarget.value == 20)
		{
			$('#loyalty_sale').attr('disabled',true)
			$('#loyalty_reward').attr('disabled',true)
		} else {
			$('#loyalty_sale').attr('disabled',false)
			$('#loyalty_reward').attr('disabled',false)
		}
	});
});
