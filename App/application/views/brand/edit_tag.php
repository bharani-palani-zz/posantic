<script language="javascript">
$(function(){
	$('#error_tag_name').hide()
	$('#form_edit_tag').submit(function(){
		if($('#tag_name').val().length < 1)
		{
			$('#error_tag_name').show()
			return false
		}
	});
});
</script>

<?php 
echo form_open(base_url().'tags/update_tag/'.$tag_id,array('id' => 'form_edit_tag'));
?>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4>Edit Brand</h4>
</div>
<div class="modal-body">
    <div class="input-group">
        <label for="tag_name" class="input-group-addon">
             Tag name
        </label>
      	<?php echo form_input(array('autocomplete' => 'off', 'value' => $tag_name, 'name' => 'tag_name', 'class' => 'form-control', 'id' => 'tag_name','placeholder' => 'Max 25 Characters')) ?>
    </div>
	<div id="error_tag_name" class="col-md-12 form_errors messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> Required / Max - 25 characters</div><br>

</div>
<div class="modal-footer">
    <button type="submit" name="edit_tag" class="btn btn-success"><i class="fa fa-save fa-fw"></i>Update</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>Close</button>
</div>

<?php
echo form_close();
?>