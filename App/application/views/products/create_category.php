<script language="javascript">
$(function(){
	$('#error_cat_name').hide()
	$('.insert_ajax_btn').click(function(){
		if($('#cat_name').val().length < 1)
		{
			$('#error_cat_name').show()
			return false	
		}
	});
});
</script>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4>Add Product Category</h4>
</div>
<div class="modal-body">
	<input type="hidden" value="<?php echo base_url().'products/category_activity' ?>" id="insert_ajax_url">
    <div class="input-group pad-5px">
        <label for="cat_name" class="input-group-addon">Category name</label>
		<?php echo form_input(array('autocomplete' => 'off','name' => 'cat_name','id' => 'cat_name',"class" => "form-control",'placeholder' => 'Name'))?>
	</div>
    <p id="error_cat_name" class="col-sm-12 text-danger form_errors"><span class="glyphicon glyphicon-remove"></span> Required / Max - 25 characters</p>
    <br>
</div>    
<div class="modal-footer">
    <button type="button" class="btn btn-success insert_ajax_btn" data-for-zone="category" data-for-select="#product_cat"><i class="fa fa-save"></i> Save</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-remove"></i> Cancel</button>
</div>
