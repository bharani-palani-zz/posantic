<script language="javascript">
$(function(){
	$('#error_group_name').hide()
	$('#form_add_group').submit(function(){
		if($('#group_name').val() == "")
		{
			$('#error_group_name').show()
			return false				
		}
	});
});
</script>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4><i class="fa fa-plus"></i> Add Customer Group</h4>
</div>
<?php 
echo form_open(base_url().'customers/create_group',array('id' => 'form_add_group'));
?>
<div class="modal-body">
    <div class="form-group">
        <div class="input-group">
            <label for="group_name" class="input-group-addon"><i class="fa fa-bars fa-fw"></i> Group name</label>
            <?php echo form_input(array('autocomplete' => 'off', 'name' => 'group_name', 'class' => 'form-control', 'id' => 'group_name','placeholder' => 'Max 25 Characters')) ?>	
        </div>
        <p id="error_group_name" class="form_errors col-md-12 text-danger"><span class="glyphicon glyphicon-remove"></span> The field is Required</p>
    </div>    
    <p>Ex: VIP, Celebrities, Family etc..</p>
</div>    
<div class="modal-footer">
    <button type="submit" class="btn btn-success" name="add_group" id="add_group"><i class="fa fa-save"></i> Add Group</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>Close</button>
</div>

<?php
echo form_close();
?>