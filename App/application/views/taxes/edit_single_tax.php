<script language="javascript">
$(function(){
	$('#error_tax_name').hide()
	$('#error_tax_rate').hide()
	$('#form_edit_single_tax').submit(function(){
		if($('#single_tax_name').val() != '' && $('#single_tax_rate').val() != '')	
		{
			if($('#single_tax_name').val().length <= 25)
			{
				if(!isNaN($('#single_tax_rate').val()))
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
			if($('#single_tax_name').val() == "" && $('#single_tax_rate').val() == "") {
				$('#error_tax_name').show()
				$('#error_tax_rate').show()
			} else if($('#single_tax_name').val() == '' ){
				$('#error_tax_name').show()
			} else if($('#single_tax_name').val().length > 25) {
				$('#error_tax_name').show()
			} else if($('#single_tax_rate').val() == '') {
				$('#error_tax_rate').show()
			} else if(isNaN($('#single_tax_rate').val())) {
				$('#error_tax_rate').show()
			}
			return false	
		}
	});

});
</script>

<?php 
echo form_open(base_url().'setup/update_single_tax/'.$single_tax_id,array('id' => 'form_edit_single_tax'));
echo form_hidden('redirect',$this->agent->referrer())
?>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4>Edit Tax</h4>
</div>
<div class="modal-body">
    <div class="input-group form-group">
      <label for="single_tax_name" class="input-group-addon font-12px">
          Tax Name
      </label>
      <?php echo form_input(array('autocomplete' => 'off', 'value' => $single_tax_name, 'name' => 'single_tax_name', 'class' => 'form-control', 'id' => 'single_tax_name','placeholder' => 'Max 25 Characters')) ?>
    </div>
    <p id="error_tax_name" class="col-md-12 form_errors messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> Required / Max - 25 characters</p>

    <div class="input-group form-group">
      <label for="single_tax_rate" class="input-group-addon font-12px">
          Tax Rate
      </label>
      <?php echo form_input(array('autocomplete' => 'off', 'value' => $single_tax_rate, 'size' => 12, 'name' => 'single_tax_rate', 'class' => 'form-control', 'id' => 'single_tax_rate','placeholder' => 'Upto 3 decimals')) ?></p>
    </div>
    <p id="error_tax_rate" class="col-md-12 form_errors messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> Required / Numeric</p>
	<br>

</div>
<div class="modal-footer">
    <button type="submit" name="edit_single_tax" class="btn btn-success"><i class="fa fa-save fa-fw"></i>Update</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>Close</button>
</div>
<?php
echo form_close();
?>