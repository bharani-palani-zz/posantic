<script language="javascript">
$(function(){
	$('#error_method_name').hide()
	$('#form_method_add').submit(function(){
		if($('#method_label').val().length < 1)
		{
			$('#error_method_name').show()
			return false
		}
	});
});
</script>

<?php 
echo form_open(base_url().'payment_method/update_method/id/'.$method_data['master_id'],array('id' => 'form_method_add'));
?>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
	<h4><i class="fa fa-pencil fa-fw"></i> Update Payment Method</h4>
</div>
<div class="modal-body">
	<div class="row">
    	<div class="col-lg-6 col-md-6">
        <label>
        <div class="input-group">
        	<label for="method_label" class="input-group-addon">Label</label>
            <input placeholder="Multilingual" autocomplete="off" type="text" class="form-control input-sm" value="<?php echo $method_data['label']?>" name="method_label" id="method_label">
            <label for="method_sort" class="input-group-addon">Sort</label>
            <input type="number" value="<?php echo $method_data['sort_order']?>" class="form-control input-sm" name="method_sort" id="method_sort" id="method_label">
        </div>
        </label>
        <?php 
		foreach($method_data['attributes'] as $i_key => $attr_array) 
		{ 
			$sub_caption = strlen($attr_array['html_label_sub_caption'] > 0) ? '<span class="input-group-addon"><small>'.$attr_array['html_label_sub_caption'].'</small></span>' : '';
			if($attr_array['html_type'] == "text")
			{
				echo '<label><div class="input-group"><span class="input-group-addon">'.$attr_array['html_label'].'</span><input type="text" autocomplete="off" class="form-control input-sm" placeholder="'.$attr_array['placeholder'].'" name="payment_method['.$i_key.']" value="'.$attr_array['values'].'">'.$sub_caption.'</div></label>';	
			} else if($attr_array['html_type'] == "select") {
				$load_code_for = $attr_array['html_load_db_data'];
				$select = $this->setup_model->get_payment_dynamic_select($load_code_for);
				echo '<label><div class="input-group"><span class="input-group-addon">'.$attr_array['html_label'].'</span>'.form_dropdown('payment_method['.$i_key.']',$select,$attr_array['values'],'class="form-control input-sm"').'</div></label>';
			} else if($attr_array['html_type'] == "boolean") {
				$checked = $attr_array['values'] == 1 ? 'checked' : NULL;
				echo '<input type="hidden" name="payment_method['.$i_key.']" value="0">';
				echo '<div class="checkbox"><label><input type="checkbox" value="1" '.$checked.' name="payment_method['.$i_key.']">'.$attr_array['html_label'].'</label><small>'.$sub_caption.'</small></div>';
			}
		}
		?>
        </div>
    	<div class="col-lg-6 col-md-6">
        <?php if(strlen($method_data['image_location']) > 0) { ?>
		<p align="center"><img class="img-thumbnail" src="<?php echo base_url().APPPATH.'images/'.$method_data['image_location'] ?>" /></p>        
        <?php } ?>
        <?php if(strlen($method_data['type_description']) > 0) { ?>
		<div><?php echo $method_data['type_description'] ?></div>
		<?php } ?>
        <?php if(strlen($method_data['external_type_link']) > 0) { ?>
		<br><a target="_blank" href="<?php echo $method_data['external_type_link'] ?>" class="btn btn-success btn-block">Know more...</a>
		<?php } ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success" name="update_method" id="update_method"><i class="fa fa-save"></i> Update</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
</div>
<?php
echo form_close();
?>