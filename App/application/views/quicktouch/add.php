<script language="javascript">
$(function(){
	$('#error_qt_name').hide()
	$('#form_add_qt').submit(function(){
		if($('#quicktouch_name').val().length <= 25 && $('#quicktouch_name').val() != "")
		{
			return true	
		} else {
			$('#error_qt_name').show()
			return false	
		}
	});
});
</script>
<?php 
echo form_open(base_url().'setup/add_quicktouch',array('id' => 'form_add_qt'));
?>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4>Add New Quick Touch</h4>
</div>
<div class="modal-body">
    <div class="input-group form-group">
        <label for="quicktouch_name" class="input-group-addon">Name</label>
		<?php echo form_input(array('autocomplete' => 'off', 'name' => 'quicktouch_name', 'class' => 'form-control input-sm', 'id' => 'quicktouch_name','placeholder' => 'Max 25 Characters')) ?>
	</div>
    <p id="error_qt_name" class="col-sm-12 text-danger form_errors"><span class="glyphicon glyphicon-remove"></span> Required / Max - 25 characters</p>
    <br>
</div>    
<div class="modal-footer">
    <button type="submit" class="btn btn-success insert_ajax_btn" data-for-zone="brand" data-for-select="#product_brand"><i class="fa fa-save"></i> Save & Start</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-remove"></i> Cancel</button>
</div>
<?php
echo form_close();
?>