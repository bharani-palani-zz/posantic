<script language="javascript">
$(function(){
	$('#error_brand_name').hide()
	$('.insert_ajax_btn').click(function(){
		if($('#brand_name').val().length < 1)
		{
			$('#error_brand_name').show()
			return false	
		}
	});
});
</script>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4>Add Brand</h4>
</div>
<div class="modal-body">
	<input type="hidden" value="<?php echo base_url().'products/brand_activity' ?>" id="insert_ajax_url">
    <div class="input-group pad-5px">
        <label for="brand_name" class="input-group-addon">Brand name</label>
		<?php echo form_input(array('autocomplete' => 'off','name' => 'brand_name','id' => 'brand_name',"class" => "form-control",'placeholder' => 'Name'))?>
	</div>
    <p id="error_brand_name" class="col-sm-12 text-danger form_errors"><span class="glyphicon glyphicon-remove"></span> Required / Max - 25 characters</p>
    <br>
</div>    
<div class="modal-footer">
    <button type="button" class="btn btn-success insert_ajax_btn" data-for-zone="brand" data-for-select="#product_brand"><i class="fa fa-save"></i> Save brand</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-remove"></i> Cancel</button>
</div>
