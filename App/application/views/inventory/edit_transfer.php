<?php
$c_symbol = $this->currency_model->getsymbol($this->session->userdata('currency'));
$daylight_saving = date("I");
?>
<h4><i class="fa fa-pencil"></i> Edit Inventory</h4>
<?php
echo form_open(base_url().'inventory/update_stock_transfer/'.$details['transfer_index'],array('id' => 'myform','size' => '5000'));
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <span><i class="fa fa-flash"></i> <?php echo $details['towards']?></span>
        <span class="pull-right text-danger"><i class="fa fa-hand-o-right"></i> <?php echo $details['log_name']?></span>
    </div>
    <div class="panel-body">
		<div class="row">
        	<div class="col-lg-4 col-md-6">
	            <div class="form-group">
                    <div class="input-group">
                        <label for="transfer_name" class="input-group-addon">Transfer name</label>
						<?php echo form_input(array('autocomplete' => 'off','name' => 'transfer_name', 'id' => 'transfer_name',"class" => "form-control input-sm", "value" => set_value('transfer_name',$details['transfer_name']))) ?>
					</div>
        		</div>
			</div>
			<?php if(form_error('transfer_name')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('transfer_name') ?></p><?php } ?>
		</div>            
		<div class="row">
			<?php if(strlen($details['supplier_name']) > 0) { ?>
        	<div class="col-lg-4 col-md-4 form-group">
            	<div class="row">
					<?php $supplier_str = $details['towards_id'] == 19 ? 'Return to Supplier' : 'Supplier'; ?>
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><strong><?php echo $supplier_str ?></strong></div>
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><?php echo $details['supplier_name'] ?></div>
				</div>                    
            </div>
			<?php } ?>
            <?php if($details['towards_id'] == 17) { ?>
        	<div class="col-lg-4 col-md-4 form-group">
            	<div class="row">
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><strong>Source Outlet</strong></div>
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><?php echo $details['source_outlet'] ?></div>
				</div>                    
            </div>
			<?php } ?>
            <?php $dest_outlet_str = $details['towards_id'] == 19 ? 'Returning' : 'Destination'; ?>
        	<div class="col-lg-4 col-md-4 form-group">
            	<div class="row">
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><strong><?php echo $dest_outlet_str ?> Outlet</strong></div>
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><?php echo $details['dest_outlet'] ?></div>
				</div>                    
            </div>
		</div>
	</div>
    <div class="panel-footer">
		<i class="fa fa-calendar"></i> Commenced on <?php echo unix_to_human(gmt_to_local(strtotime($details['created_at']),$timezone, $daylight_saving)) ?>
	</div>    
    
</div>    
<div class="row">
    <div class="col-lg-6 col-md-8">
        <div class="form-group">
            <div class="input-group input-group-md">
                <label for="item_sku" class="input-group-addon">Add products</label>
                <?php echo form_input(array('autocomplete' => 'off','class' => 'form-control','name' => 'item_sku','id' => 'item_sku', "placeholder" => "Search Product / SKU / handle")) ?>
            </div>
        </div>
    </div>
</div>            
<div class="table-responsive">
<?php
$tmpl = array ( 'table_open'  => '<table class="table table-striped table-condensed table-curved dyn_add" id="edit_trn_table">' );
$this->table->set_template($tmpl);			
$heading = array(
				array('data' => '<strong>Product</strong>','class' => 'subtable_td','align' => 'center'),
				array('data' => '<strong>Outlet Stock</strong>','class' => 'subtable_td','align' => 'center'),
				array('data' => '<strong>Order Qty</strong>','class' => 'subtable_td','align' => 'center'),
				array('data' => '<strong>Supplied Price</strong>','class' => 'subtable_td','align' => 'center'),
				array('data' => '<strong>Total</strong>','class' => 'subtable_td','align' => 'center'),
				array('data' => '','class' => 'subtable_td','align' => 'center')
				);
$this->table->set_heading($heading);
echo '<input type="hidden" id="dest_outlet_id" value="'.$details['dest_outlet_id'].'">';
echo '<input type="hidden" id="source_outlet_id" value="'.$details['source_outlet_id'].'">';
echo '<input type="hidden" id="transfer_url" value="'.site_url('inventory/transfer_autocomplete').'">';
echo '<input type="hidden" id="transfer_del_url" value="'.site_url('inventory/delete_transfer_single_product').'">';
echo '<input type="hidden" id="ins_transfer_url" value="'.site_url('inventory/insert_ajax_transfer').'">';
echo '<input type="hidden" id="base_url" value="'.base_url().'">';
echo '<input type="hidden" id="transfer_parent_id" value="'.$details['transfer_index'].'">';

if(isset($sub_product_array['product_id']))
{
	foreach($sub_product_array['product_id'] as $key => $value)
	{
		$stk_class = $sub_product_array['source_stock'][$key] > 0 ? 'text-success' : 'text-danger';
		$this->table->add_row(
						anchor('products/'.$sub_product_array['product_id'][$key],$sub_product_array['prod_name'][$key],'class = "btn btn-xs btn-default"').
						'<input type="hidden" name="transfer[child_id][]" class="transfer_child_id" value="'.$sub_product_array['child_id'][$key].'">',
						'<span class="'.$stk_class.'">'.$sub_product_array['source_stock'][$key].'</span>',
						array('data' => form_input(array('autocomplete' => 'off', 'size' => 5, 'name' => 'transfer[ordered][]','class' => 'form-control input-sm all_ordered','value' => $sub_product_array['ordered'][$key])),'align' => 'center'),
						array('data' => form_input(array('autocomplete' => 'off', 'size' => 5, 'name' => 'transfer[supp_price][]','class' => 'form-control input-sm all_supplier','value' => $sub_product_array['supplier_price'][$key])),'align' => 'center'),
						array('data' => number_format(($sub_product_array['ordered'][$key] * $sub_product_array['supplier_price'][$key]),2),'class' => 'total_trx'),
						array('data' => '<button type="button" class="btn btn-xs btn-danger && del_row" title="Remove"><i class="fa fa-times"></i></button>','align' => 'center')
						);
	}
} else {
	$this->table->add_row(array('data' => '','colspan' => 6));
}
echo $this->table->generate();
echo $links;
?>
</div>
<div class="row">
    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
		<?php echo anchor('inventory','<i class="fa fa-angle-double-left"></i> Back','class = "btn btn-sm btn-outline btn-success"') ?>    
        <button type="submit" class="btn btn-success btn-sm search_button loading_modal" name="insert_transfer" id="insert_transfer"><i class="fa fa-save"></i> Update Consignment</button> 
        <?php echo anchor('inventory/freight/'.$details['transfer_index'],'<i class="fa fa-power-off"></i> Cancel', 'class = "btn btn-sm btn-danger"') ?>
    </div>
</div>

<?php
echo form_close(); 
?>

