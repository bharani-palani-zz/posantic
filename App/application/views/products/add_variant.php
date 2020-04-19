<h4><i class="fa fa-cube fa-fw"></i> Add variant / <?php echo $product_name ?></h4>
<hr>
<?php echo form_open_multipart(base_url().'products/create_variant/'.$product_id,array('id' => 'myform','size' => '5000'))?>
<input type="hidden" name="new_p_scale" id="new_p_scale" value="VARIANTS">
<div class="panel panel-default" id="collapse-product-det-panel">
    <div class="panel-heading">
        <div class="panel-title">
            <a data-toggle="collapse" data-parent="#collapse-product-det-panel" href="#collapse-product-det"><small>Product details</small></a>
        </div>
	</div>        
    <div class="panel-body panel-collapse collapse in" id="collapse-product-det">
		<div class="row">
			<div class="col-lg-12">
				<?php
                if(validation_errors()){
                    echo '<div class="alert alert-md alert-danger fade in"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-remove"></span> Please resolve the following errors</div>';
                }		
                ?>
			</div>
		</div>   
		<div class="row">
			<div class="col-md-6">
	            <div class="form-group">
                    <?php echo form_radio(array('data-on-color' => 'success', 'data-on-text' => 'Enabled', 'data-off-text' => 'Enable', 'data-label-text' => 'Visibility','data-size' => 'small','name' => 'visib_stat', 'id' => 'visib_true', 'checked' => true , 'value' => 30)) ?>
                    &nbsp;
                    <?php echo form_radio(array('data-on-color' => 'danger', 'data-on-text' => 'Disabled', 'data-off-text' => 'Disable', 'data-label-text' => 'Visibility','data-size' => 'small','name' => 'visib_stat', 'id' => 'visib_false', 'checked' => false , 'value' => 40)) ?>
				</div>
			</div>
		</div>  
		<div class="row">
			<div class="col-md-4">
	            <div class="form-group">
                    <div class="form-group input-group">
                        <span class="input-group-addon" data-toggle="popover" data-placement="top" data-content="Internal accounting" >Wearhouse Id</span>
						<?php echo form_input(array('autocomplete' => 'off','name' => 'prd_wh_id','id' => 'prd_wh_id',"class" => "form-control input-sm", "value" => set_value('prd_wh_id',''))) ?>
					</div>
				</div>
			</div>
			<div class="col-md-4">
	            <div class="form-group">
                    <div class="form-group input-group">
                        <span class="input-group-addon" data-toggle="popover" data-placement="top" data-content="Internal accounting" >Purchase Id</span>
						<?php echo form_input(array('autocomplete' => 'off','name' => 'prd_pur_id','id' => 'prd_pur_id',"class" => "form-control input-sm", "value" => set_value('prd_pur_id',''))) ?>
					</div>
				</div>
			</div>                                    
			<div class="col-md-4">
	            <div class="form-group input-group">
                    <span class="input-group-addon" data-toggle="popover" data-placement="top" data-content="Allowed Types - GIF|JPG|PNG. Max file size: 3MB">Product image</span>
                    <label class="btn btn-primary" for="my-file-selector">
                        <?php echo form_upload(array('name' => 'userfile', 'id' => 'my-file-selector', 'style' => 'display:none;')) ?>
                        Choose file...
                    </label>    
				</div>
			</div>                                    
		</div>                  
	</div>
</div>    
<div class="panel panel-default" id="var_content">
    <?php
    echo "<input type='hidden' id='hid_var' value='".json_encode($var_types)."' />";
    echo form_hidden('main_product_id',$product_id);
    $tbody = '';
    foreach($attributes as $a_key => $attribute_id)
    {
        $tbody .= '<div class="form-group input-group"><span class="input-group-addon">'.$variant_dropdown[$a_key].'</span>';
        $tbody .= '<input type="hidden" name="var_type_name[]" value="'.$a_key.'">';
        $tbody .= '<input type="text" value="'.set_value('new_var_method['.$a_key.']').'" class="form-control input-sm var_tbox" id="new_var_method" name="new_var_method[]" placeholder="Option value" autocomplete="off"/>';
        $tbody .= '</div>';
    }
    ?>
    <div class="panel-heading">
        <div class="panel-title">
            <a data-toggle="collapse" data-parent="#var_content" href="#collapse-var-content"><small>Add variant values</small></a>
        </div>           
    </div>
    <div class="panel-body panel-collapse collapse in" id="collapse-var-content">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?php echo $tbody ?>	
        </div>
        <?php if(form_error('new_var_method[]')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('new_var_method[]') ?></p><?php } ?>
        <?php if(form_error('var_type_name[]')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('var_type_name[]') ?></p><?php } ?>                
    </div>
</div>    

<div class="panel panel-default" id="sku_div">
    <div class="panel-heading">
        <div class="panel-title">
	    	<a data-toggle="collapse" data-parent="#sku_div" href="#collapse-product-sku"><small>Product Stock keeping unit</small></a>
    	</div>
    </div>
    <div class="panel-body panel-collapse collapse in" id="collapse-product-sku">
		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
				<?php $error_c = form_error('sku') ? 'has-error' : ''; ?> 
	            <div class="form-group <?php echo $error_c ?>">
                    <div class="form-group input-group">
                        <span class="input-group-addon" data-toggle="popover" data-placement="top" title="The SKU must be unique and should not repeat for any other products" data-content="Stock keeping unit / Universal Product Code(UPC) / Own Product Barcodes. KILO scale selling products inherit their own barcodes respective to weight/length/litre, irrespective to SKU/UPC defined." >SKU / UPC / Barcode</span>
						<?php echo form_input(array('autocomplete' => 'off','name' => 'sku','id' => 'sku',"class" => "form-control input-sm", "value" => set_value('sku',$auto_sku))) ?>
					</div>
					<?php if(form_error('sku')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('sku') ?></p><?php } ?>                
				</div>
			</div>
		</div>                                                
	</div>
</div>    
<div class="panel panel-default" id="price_div">
    <div class="panel-heading">
        <div class="panel-title">
        	<a data-toggle="collapse" data-parent="#price_div" href="#collapse-price-div"><small>Product Pricing</small></a>
        </div>
	</div>        
    <div class="panel-body panel-collapse collapse in" id="collapse-price-div">
    	<div class="col-lg-6 col-lg-offset-3 col-md-12 col-sm-12 col-xs-12">
            <table class="table table-striped table-condensed table-curved">
                <thead>
                    <tr>
                        <th><?php echo form_label('Supplier / Operated Price', 'price') ?></th>
                        <th><?php echo form_label('Profit Margin%', 'margin') ?></th>
                        <th><?php echo form_label('Retail Price', 'retail') ?></th>
                    </tr>
                </thead>
                <tbody>                
                    <tr>
                    	<?php
						$price_error = form_error('price') ? '<p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span>'.form_error('price').'<p>' : '';
						$margin_error = form_error('margin') ? '<p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span>'.form_error('margin').'<p>' : '';
						$retail_error = form_error('retail') ? '<p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span>'.form_error('retail').'<p>' : '';
						?>
                        <td><?php echo form_input(array('autocomplete' => 'off','name' => 'price','id' => 'price',"class" => "form-control input-sm","value" => set_value('price',0))).$price_error ?></td>
                        <td><?php echo form_input(array('autocomplete' => 'off','name' => 'margin','id' => 'margin',"class" => "form-control input-sm","value" => set_value('margin',0))).$margin_error ?></td>
                        <td><?php echo form_input(array('autocomplete' => 'off','name' => 'retail','id' => 'retail',"class" => "form-control input-sm","value" => set_value('retail',0))).$retail_error ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
		<div class="col-lg-6 col-lg-offset-3 col-md-12 col-sm-12 col-xs-12 text-center form-group">
        	<button type="button" class="btn btn-xs btn-primary" id="show_locale_tax">Show locale taxes</button>
		</div>   
        <table class="table table-striped table-condensed table-curved" id="tax_table">
            <thead>
                <tr>
                    <th>Outlet</th>
                    <th>Tax</th>
                    <th>Tax Amount</th>
                    <th>Retail Price With Tax</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach($def_locale_tax['loc_id'] as $key => $sub_array){
                $combo = array('Default Outlet Locale Tax' => array('' => 'Current outlet tax &rarr; ('.$def_locale_tax['tax_val'][$key].'%)')) + $single_group_taxes_combo;				
            ?>
            <tr>
                <td><?php echo $def_locale_tax['location'][$key].form_hidden('def_location[]',$def_locale_tax['loc_id'][$key]) ?></td>
                <td><?php echo form_dropdown('qty_scale_tax[]',$combo,'','class="qty_scale_tax form-control input-sm"') ?></td>
                <td class="qty_scale_tax_amt">0</td>
                <td class="mrp_scale_tax_amt">0</td>
            </tr>				                    
            <?php
            }
            ?>
            </tbody>
        </table>                                                     
	</div>
</div>
<?php
$loyalty = $this->customer_model->check_loyalty_set($this->session->userdata('acc_no'));
list($loyalty_sale,$loyalty_reward) = $this->customer_model->loyalty_params($this->session->userdata('acc_no'));
$loyalty_stat = ($loyalty == true) ? 1 : 0; 
echo '<input type="hidden" id="loyalty_stat" value="'.$loyalty_stat.'" />';
echo '<input type="hidden" id="loyalty_sale" value="'.$loyalty_sale.'" />';
echo '<input type="hidden" id="loyalty_reward" value="'.$loyalty_reward.'" />';
$this->load->helper('text');
if($loyalty)
{
?>
<div class="panel panel-default" id="loyalty_div">
    <div class="panel-heading">
        <div class="panel-title">
            <a data-toggle="collapse" data-parent="#loyalty_div" href="#collapse-loyalty-div"><small>Loyalty</small></a>
        </div>
	</div>
    <div class="panel-body panel-collapse collapse in" id="collapse-loyalty-div">
		<div class="form-group">
        	<input type="hidden" name="loyalty_def_val" id="loyalty_def_val" value="">
			<?php echo form_radio(array('data-label-width' => 100,'data-on-color' => 'success', 'data-on-text' => 'Yes', 'data-off-text' => 'No', 'data-label-text' => ellipsize('Loyalty '.$loyalty_sale.' : '.$loyalty_reward,15,.5) ,'data-size' => 'small','name' => 'loyalty_stat', 'id' => 'loyalty_true', 'checked' => true , 'value' => 30)) ?>
            &nbsp;
            <?php echo form_radio(array('data-label-width' => 100,'data-on-color' => 'info', 'data-on-text' => 'Yes', 'data-off-text' => 'No', 'data-label-text' => 'Custom Loyalty','data-size' => 'small','name' => 'loyalty_stat', 'id' => 'loyalty_false', 'checked' => false , 'value' => 40)) ?>        
        </div>
        <div class="row" id="def_loyalty_group">
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group input-group">
                <span class="input-group-addon" id="loyalty_def_span"></span>
                <?php echo form_input(array('autocomplete' => 'off','name' => 'loyalty_cust_val','id' => 'loyalty_cust_val', "class" => "form-control input-sm", "value" => set_value('loyalty_cust_val'))) ?>        
                </div>
			</div>
		</div>                            
	</div>
</div>    
<?php
} else {
	echo '<input type="hidden" id="loyalty_cust_val" value="0" />';	
}
?>
<div class="panel panel-default" id="inventory_div">
    <div class="panel-heading">
		<?php echo form_checkbox(array('data-off-color' => 'danger', 'data-on-color' => 'success', 'data-label-text' => 'Track Inventory', 'data-size' => 'small', 'name' => 'trace_inv','id' => 'trace_inv','value' => 30,'checked' => TRUE))?> 
    </div>
    <div class="panel-body">
        <table id="inventory_table" class="table table-striped table-condensed table-curved">
            <thead>
                <tr>
                    <th>Outlet</th>
                    <th>Current Stock</th>
                    <th data-toggle="popover" data-placement="top" data-content="While stock inventory reaches this level, stock is ordered">Reorder Level</th>
                    <th data-toggle="popover" data-placement="top" data-content="The quantity for which the stock is to be ordered">Reorder Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($def_locale_tax['loc_id'] as $key => $sub_array){ ?>
                    <tr>
                        <td><?php echo $def_locale_tax['location'][$key] ?></td>
                        <td><?php echo form_hidden('inv_outlet[]',$def_locale_tax['loc_id'][$key]).form_input(array('value' => set_value('cur_stk['.$key.']',0), 'autocomplete' => 'off','name' => 'cur_stk['.$key.']', "class" => "form-control input-sm", 'id' => 'cur_stk['.$key.']')) ?></td>
                        <td><?php echo form_input(array('value' => set_value('reorder_stk['.$key.']',0),'autocomplete' => 'off','name' => 'reorder_stk['.$key.']',"class" => "form-control input-sm", 'id' => 'reorder_stk['.$key.']')) ?></td>
                        <td><?php echo form_input(array('value' => set_value('reorder_qty['.$key.']',0),'autocomplete' => 'off','name' => 'reorder_qty['.$key.']',"class" => "form-control input-sm", 'id' => 'reorder_qty['.$key.']')) ?></td>
                    </tr>                            
                <?php } ?>
            </tbody>
        </table>        
	</div>
</div>    
<div class="panel panel-default" id="ship_div">
	<div class="panel-heading">
        <div class="panel-title">
            <a data-toggle="collapse" data-parent="#ship_div" href="#collapse-ship-div"><small>Require shipping for this product</small></a>
        </div>
    </div>
	<div class="panel-body panel-collapse collapse in" id="collapse-ship-div">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12 col-sm-12">
                <div class="form-group input-group">
                    <span class="input-group-addon" data-toggle="popover" data-placement="top" data-content="Product Weight For Shipments / Delivery. With / Without packing" >Product weight</span>
                    <?php echo form_input(array('autocomplete' => 'off','name' => 'prd_weight','id' => 'prd_weight',"class" => "form-control input-sm", "value" => set_value('prd_weight',0))) ?>
                    <span class="input-group-addon">Kg</span>
                </div>
            </div>
        </div>
		<?php echo form_checkbox(array('data-off-color' => 'danger', 'data-on-color' => 'success', 'data-label-text' => 'Shipping', 'data-size' => 'small', 'name' => 'ship_stat','id' => 'ship_stat','value' => 30,'checked' => TRUE)) ?>
	</div>
</div>    
<div class="btn-group">
    <button type="submit" name="insert_product" id="insert_product" class="btn btn-success loading_modal"><i class="fa fa-save"></i> Add variant</button>
    <?php echo anchor('products/'.$product_id,'<i class="fa fa-remove"></i> Cancel','class="btn btn-danger loading_modal"') ?>
</div>
<?php
echo form_close();
?>
