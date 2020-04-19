<script language="javascript">
$(function(){
	$('#error_tax_name').hide();
	$('#form_edit_customer_group').submit(function(){
		if($('#group_name').val() == "")
		{
			$('#error_tax_name').show();	
			return false
		}
	});
});
</script>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4><i class="fa fa-edit"></i> Edit Group Name</h4>
</div>
<?php 
echo form_open(base_url().'customers/update_group/'.$grp_id,array('id' => 'form_edit_customer_group'));
?>
<div class="modal-body">
    <div class="form-group">
        <div class="input-group">
            <label for="group_name" class="input-group-addon"><i class="fa fa-bars fa-fw"></i> Group name</label>
            <?php echo form_input(array('autocomplete' => 'off', 'value' => $cust_group_name, 'name' => 'group_name', 'class' => 'form-control', 'id' => 'group_name','placeholder' => 'Max 25 Characters')) ?>
		</div>
        <p id="error_tax_name" class="form_errors col-md-12 text-danger"><span class="glyphicon glyphicon-remove"></span> Required / Max - 25 characters</p>
	</div> 
</div>          	
<div class="modal-footer">
    <button type="submit" class="btn btn-success" name="edit_group" id="edit_group"><i class="fa fa-save"></i> Update Group</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>
<?php
echo form_close();
?>