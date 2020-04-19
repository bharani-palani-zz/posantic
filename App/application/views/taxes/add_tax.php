<script language="javascript">
$(function(){
	$('#error_tax_name').hide()
	$('#error_tax_rate').hide()
	$('#tax_name').focus();
	$('.fb_cancel').click(function(){
		$.fancybox.close()
	});
	$('#form_add_tax').submit(function(){
		if($('#tax_name').val() != '' && $('#tax_rate').val() != '')	
		{
			if($('#tax_name').val().length <= 25)
			{
				if(!isNaN($('#tax_rate').val()))
				{
					return true	
				} else {
					$('#error_tax_rate').show()
					return false	
				}
			} else {
				$('#error_tax_name').show()
				return false	
			}
		} else {
			if($('#tax_name').val() == "" && $('#tax_rate').val() == "") {
				$('#error_tax_name').show()
				$('#error_tax_rate').show()
			} else if($('#tax_name').val() == '' ){
				$('#error_tax_name').show()
			} else if($('#tax_name').val().length > 25) {
				$('#error_tax_name').show()
			} else if($('#tax_rate').val() == '') {
				$('#error_tax_rate').show()
			} else if(isNaN($('#tax_rate').val())) {
				$('#error_tax_rate').show()
			}
			return false	
		}
	});
});
</script>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4>Add New Sales Tax</h4>
</div>
<?php 
echo form_open(base_url().'setup/add_tax',array('id' => 'form_add_tax'));
echo form_hidden('redirect',$this->agent->referrer())
?>
<div class="modal-body">
    <div class="input-group form-group">
      <label for="tax_name" class="input-group-addon">
           Tax name
      </label>
      <?php echo form_input(array('autocomplete' => 'off', 'name' => 'tax_name', 'class' => 'form-control', 'id' => 'tax_name','placeholder' => 'Max 25 Characters')) ?>
    </div>
    <p id="error_tax_name" class="col-md-12 form_errors messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> Required / Max - 25 characters</p>

    <div class="input-group form-group">
      <label for="tax_rate" class="input-group-addon">
          Tax Rate %
      </label>
      <?php echo form_input(array('autocomplete' => 'off', 'name' => 'tax_rate', 'class' => 'form-control', 'id' => 'tax_rate','placeholder' => 'Upto 3 decimals')) ?>
    </div>
    <div id="error_tax_rate" class="col-md-12 form_errors messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> Required / Numeric</div><br>
</div>
<div class="modal-footer">
    <button type="submit" name="add_single_tax" class="btn btn-success"><i class="fa fa-save fa-fw"></i>Save Tax</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>Close</button>
</div>
<?php
echo form_close();
?>