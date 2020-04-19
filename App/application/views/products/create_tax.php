<script language="javascript">
$(function(){
	$('#error_tax_name').hide()
	$('#error_tax_val').hide()
	$('.insert_ajax_btn').click(function(){
		if($('#tax_name').val().length < 1)
		{
			$('#error_tax_name').show()
			return false	
		}
		if($('#tax_val').val().length < 1 || isNaN($('#tax_val').val()))
		{
			$('#error_tax_val').show()
			return false	
		}
	});
});
</script>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4>Add Tax</h4>
</div>
<div class="modal-body">
<?php
$key = 'qty_scale_tax_'.$this->input->get('for_outlet');
?>
	<input type="hidden" value="<?php echo base_url().'products/tax_activity' ?>" id="insert_ajax_url">
    <div class="input-group form-group">
        <label for="tax_name" class="input-group-addon font-12px">Tax name</label>
		<?php echo form_input(array('autocomplete' => 'off','name' => 'tax_name','id' => 'tax_name',"class" => "form-control",'placeholder' => 'Name'))?>
	</div>
    <p id="error_tax_name" class="col-sm-12 text-danger form_errors"><span class="glyphicon glyphicon-remove"></span> Required / Max - 25 characters</p>
    <div class="input-group form-group">
        <label for="tax_val" class="input-group-addon">Tax value</label>
		<?php echo form_input(array('autocomplete' => 'off','name' => 'tax_val','id' => 'tax_val',"class" => "form-control",'placeholder' => 'Value'))?>
        <span class="input-group-addon font-12px">%</span>
	</div>
    <p id="error_tax_val" class="col-sm-12 text-danger form_errors"><span class="glyphicon glyphicon-remove"></span> Required / integer / float</p>
    <br>
</div>    
<div class="modal-footer">
    <button type="button" class="btn btn-success insert_ajax_btn" data-for-zone="tax" data-for-select="#<?php echo $key ?>"><i class="fa fa-save"></i> Save tax</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-remove"></i> Cancel</button>
</div>
