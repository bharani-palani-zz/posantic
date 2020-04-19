<script language="javascript">
$(function(){
	$('#grp_error_tax_name').hide()
	$('#grp_error_group_name').hide()
	$('#form_group_tax').submit(function(){
		if($('#grp_tax_name').val().length < 1 || $('#grp_tax_name').val().length > 25)
		{
			$('#grp_error_tax_name').show()
			return false	
		}
		if($('#tax_groups option:selected').length < 2)
		{
			$('#grp_error_group_name').show()
			return false				
		}
	});
});
</script>

<?php 
echo form_open(base_url().'setup/add_group',array('id' => 'form_group_tax'));
echo form_hidden('redirect',$this->agent->referrer()) 
?>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4>Group Taxes</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        <div class="input-group form-group">
          <label for="grp_tax_name" class="input-group-addon">Group Name</label>
          <?php echo form_input(array('autocomplete' => 'off', 'name' => 'grp_tax_name', 'class' => 'form-control', 'id' => 'grp_tax_name','placeholder' => 'Max 25 Characters')) ?>
        </div>
        <p id="grp_error_tax_name" class="form_errors messageContainer text-danger">
            <span class="glyphicon glyphicon-remove"></span> Required / Max - 25 characters
        </p>
	</div>
    <div class="panel panel-default">
        <div class="panel-heading">Select Taxes</div>
        <div class="panel-body">
          <?php echo form_multiselect('tax_groups[]', $single_taxes, '', 'class="form-control" id="tax_groups"'); ?>
        </div>
        <div class="panel-footer">
        	<div class="row">
                <div id="grp_error_group_name" class="col-md-12 form_errors messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> Minimum 2 Taxes To Be Selected For Grouping</div>
        	</div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" name="add_group_tax" class="btn btn-success"><i class="fa fa-save fa-fw"></i>Make Group Tax</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>Close</button>
</div>
<?php
echo form_close();
?>