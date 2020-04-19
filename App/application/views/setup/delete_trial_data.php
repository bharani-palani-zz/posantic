<script>
$(function(){
	$('#products_and_sales').on('click',function(){
		if($(this).prop('checked') == true)
		{
			$('#only_sales').prop('checked',true)
			$('#only_sales').attr('disabled','disabled')
		} else {
			$('#only_sales').prop('checked',false)
			$('#only_sales').prop('disabled',false)
		}
	});
});
</script>
<?php
echo form_open(base_url().'account/trash_form_trial_data'); 
?>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4>Delete trial data</h4>
</div>
<div class="modal-body">
	<div>
    Delete your testing data once you are activating to a paid plan. This
    feature helps you in not holding your dummy testing data as they are of no
    use. Deleting testing data is permenant and cannot be restored.
    </div><br>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="checkbox">
              <label><?php echo form_checkbox(array('name' => 'check_trial_delete[delete][]','id' => 'only_sales','value' => 'only_sales' ,'checked' => false ))?> Delete sale transactions</label>
            </div>    
            <div>
            Deleting your sales transactions includes all the sales you've done with register closures, customer associated data & loyalty balances
            </div>
        </div>
    </div>    
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="checkbox">
              <label><?php echo form_checkbox(array('name' => 'check_trial_delete[delete][]','id' => 'products_and_sales','value' => 'products_and_sales' ,'checked' => false ))?> Delete products and sale transactions</label>
            </div>    
            <div>
            Deleting your products includes with its associated sales tansactions,
            inventory records, categories, tags, suppliers, brands, promotions and quicktouch.
            </div>
            <h5>
            <strong>Note:</strong> Your configured master taxes, users, suppliers, categories, tags, brands, outlets, registers & payment methods are still safe.
            </h5>
        </div>
    </div>    
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="checkbox">
              <label><?php echo form_checkbox(array('name' => 'check_trial_delete[delete][]','id' => 'only_customer','value' => 'only_customer' ,'checked' => false ))?> Delete customers</label>
            </div>    
            <div>
            Delete customers and its associated groups
            </div>
        </div>
    </div>    
</div>    
<div class="modal-footer">
    <button type="submit" class="btn btn-success"><i class="fa fa-trash-o"></i> Delete data</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-remove"></i> Cancel</button>
</div>
<?php
echo form_close(); 
?>
