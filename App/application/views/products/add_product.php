<?php
if(isset($form_errors)) { 
	echo '<div class="alert alert-md alert-danger fade in">';
	echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
	echo '<span class="glyphicon glyphicon-remove-sign"></span> '.$form_errors;
	echo '</div>';
}
if(isset($form_success)) {
	echo '<div class="alert alert-sm alert-success fade in">';
	echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
	echo '<span class="glyphicon glyphicon-ok-sign"></span> '.$form_success;
	echo '</div>';
}

foreach($def_locale_tax['loc_id'] as $key => $loc_value)
{
	$location[] = array('key' => $loc_value,'value' => $def_locale_tax['location'][$key]);
}
echo '<input type="hidden" id="tag_url" value="'.site_url('products/tag_autocomplete').'">';					
echo '<input type="hidden" id="insert_tag_url" value="'.site_url('products/insert_tag').'">';					
echo '<input type="hidden" id="max_variants" value="'.$max_variants.'">';					
echo form_open_multipart(base_url().'products/insert_product',array('id' => 'myform','size' => '5000'));
?>
<h4><i class="fa fa-cube fa-fw"></i> Add Product</h4>
<input type="hidden" id="tag_scale" value="">
<input type="hidden" id="prd_id" value="">
<?php
$content = array(
				'bg' => array(
					'NUM' => 'bg-success','KILO' => 'bg-info','VARIANTS' => 'bg-warning','BLEND' => 'bg-danger'
				),
				'desc' => array(
					'NUM' => 'Standard product','KILO' => 'Weighing scale product','VARIANTS' => 'Product having variants','BLEND' => 'Group products'
				),
				'fa' => array(
					'NUM' => 'fa fa-cube','KILO' => 'fa fa-barcode','VARIANTS' => 'fa fa-tags','BLEND' => 'fa fa-cubes'
				),
				
			);

?>
<div class="row">
    <?php foreach($options as $key => $scale) { ?>
        <div class="col-lg-3 col-md-3 col-xs-12 form-group">
            <button type="button" class="btn btn-danger col-lg-12 col-md-12 col-xs-12 <?php echo $content['bg'][$key]?> text-center select_scale" id="<?php echo $key ?>">
                <h4 class="pull-right tick_area"></h4>
                <h6><?php echo $content['desc'][$key] ?></h6>
                <h1><i class="<?php echo $content['fa'][$key] ?>"></i></h1>
                <h6><?php echo $scale ?></h6>
            </button>
        </div>
    <?php } ?>    
</div>
<!--Dynamic ajax load content-->
<div class="modal fade" id="ajax_insert_dyn_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        </div>
    </div>
</div>

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
			<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
				<?php $error_c = form_error('p_name') ? 'has-error' : ''; ?> 
	            <div class="form-group <?php echo $error_c ?>">
                    <div class="input-group">
                        <label for="p_name" class="input-group-addon">Product Name</label>
                    	<?php echo form_input(array('autocomplete' => 'off','name' => 'p_name', 'id' => 'p_name',"class" => "form-control input-sm", "value" => set_value('p_name'))) ?>
					</div>
					<?php if(form_error('p_name')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('p_name') ?></p><?php } ?>
                </div>    
			</div>
		</div>            

		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
				<?php $error_c = form_error('p_handle') ? 'has-error' : ''; ?> 
	            <div class="form-group <?php echo $error_c ?>">
                    <div class="input-group">
                        <label for="p_handle" class="input-group-addon" data-toggle="popover" data-placement="top" data-content="A human prefered unique identifier for a product is automatically generated from its name.This field is to be unique for all products.">Handle</label>
						<?php echo form_input(array('autocomplete' => 'off','name' => 'p_handle', 'id' => 'p_handle',"class" => "form-control input-sm", "value" => set_value('p_handle'))) ?>
					</div>
					<?php if(form_error('p_handle')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('p_handle') ?></p><?php } ?>
                </div>    
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
			<div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default" id="collapse-product-panel">
                    <div class="panel-heading" data-toggle="popover" data-placement="top" data-content="All Products are classified with some description on it. Your description patern is the format displayed in online cart.">
                    	<div class="panel-title">
                        	<a class="" data-toggle="collapse" data-parent="#collapse-product-panel" href="#collapse-product-desc"><small>Product description</small></a>
                        </div>
                    </div>
                    <div class="panel-body panel-collapse collapse in" id="collapse-product-desc">
					<?php
					echo form_textarea(array(
							'name'        => 'new_desc',
							'id'          => 'new_desc',
							'class' 		=> 'smallborder_orange',
							'value'       => set_value('new_desc'),
						  ));
					?>	
					</div>
                </div>
			</div>
		</div>   

		<div class="row">
			<div class="col-md-6">
	            <div class="form-group">
                    <div class="input-group">
                        <label for="new_supplier" class="input-group-addon" >Supplier</label>
						<?php echo form_dropdown('new_supplier', $suppliers,set_value('new_supplier'),'id="new_supplier" class="form-control input-sm"') ?>
	                    <span class="input-group-btn"><a class="btn btn-sm btn-default" data-toggle="modal" data-target="#ajax_insert_dyn_modal" href="<?php echo base_url().'products/create_supplier' ?>"><i class="fa fa-plus"></i></a></span>
					</div>
				</div>
			</div>
			<div class="col-md-6">
	            <div class="form-group input-group">
                    <label for="product_cat" class="input-group-addon" data-toggle="popover" title="Alert" data-placement="top" data-content="Categorize this product to group for reporting and device/shopping cart selection.">Product Category</label>
                    <?php echo form_dropdown('product_cat', $prd_category_combo,set_value('product_cat'),'id="product_cat" class="form-control input-sm"') ?>
                    <span class="input-group-btn"><a class="btn btn-sm btn-default" data-toggle="modal" data-target="#ajax_insert_dyn_modal" href="<?php echo base_url().'products/create_category' ?>"><i class="fa fa-plus"></i></a></span>
				</div>
			</div>                                    
		</div>                                                                                         	                                  	

		<div class="row">
			<div class="col-md-6">
	            <div class="form-group">
                    <div class="input-group">
                        <label for="product_brand" class="input-group-addon" >Product Brand</label>
						<?php echo form_dropdown('product_brand', $brand_combo,set_value('product_brand'),'id="product_brand" class="form-control input-sm"') ?>
	                    <span class="input-group-btn"><a class="btn btn-sm btn-default" data-toggle="modal" data-target="#ajax_insert_dyn_modal" href="<?php echo base_url().'products/create_brand' ?>"><i class="fa fa-plus"></i></a></span>
					</div>
				</div>
			</div>
			<div class="col-md-6">
	            <div class="form-group input-group">
                    <label for="prd_tags" class="input-group-addon" data-toggle="popover" data-placement="top" data-content="Eg: Shirt, T-Shirt, Lion Logo shirt, Yellow T-shirt etc.. for product &ldquo;IPL CSK Jersey&rdquo;. Type value and add..">Product tags</label>
                    <?php echo form_input(array('autocomplete' => 'off', 'placeholder' => 'Search/add tags for this product','id' => 'prd_tags',"class" => "form-control input-sm")) ?>
                    <span class="input-group-btn"><button type="button" id="add_tag" class="btn btn-primary btn-sm">Add</button></span>
				</div>
                <div id="ajax_tags_div" class="form-group"></div>
			</div>                                    
		</div>                                                                                         	                                  	                                                                                               	                                  	
	</div>
</div>    
<div class="panel panel-default" id="additional_div">
    <div class="panel-heading">
        <div class="panel-title">
	    	<a data-toggle="collapse" data-parent="#additional_div" href="#collapse-add-sku"><small>Product InStore details</small></a>
    	</div>
    </div>
    <div class="panel-body panel-collapse collapse in" id="collapse-add-sku">
		<div class="row">
			<div class="col-md-4">
	            <div class="form-group">
                    <div class="form-group input-group">
                        <label for="prd_wh_id" class="input-group-addon" data-toggle="popover" data-placement="top" data-content="Internal accounting" >Wearhouse Id</label>
						<?php echo form_input(array('autocomplete' => 'off','name' => 'prd_wh_id','id' => 'prd_wh_id',"class" => "form-control input-sm", "value" => set_value('prd_wh_id',''))) ?>
					</div>
				</div>
			</div>
			<div class="col-md-4">
	            <div class="form-group">
                    <div class="form-group input-group">
                        <label for="prd_pur_id" class="input-group-addon" data-toggle="popover" data-placement="top" data-content="Internal accounting" >Purchase Id</label>
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
                        <label for="sku" class="input-group-addon" data-toggle="popover" data-placement="top" title="The SKU must be unique and should not repeat for any other products" data-content="Stock keeping unit / Universal Product Code(UPC) / Own Product Barcodes. KILO scale selling products inherit their own barcodes respective to weight/length/litre, irrespective to SKU/UPC defined." >SKU / UPC / Barcode</label>
						<?php echo form_input(array('autocomplete' => 'off','name' => 'sku','id' => 'sku',"class" => "form-control input-sm", "value" => set_value('sku',$auto_sku))) ?>
					</div>
					<?php if(form_error('sku')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('sku') ?></p><?php } ?>                
				</div>
			</div>
		</div>                                                
	</div>
</div>    
<?php echo form_dropdown('new_p_scale', $options, set_value('new_p_scale'), 'id="new_p_scale" class="form-control input-sm"') ?>
<div class="panel panel-default" id="var_content">
	<?php
    echo "<input type='hidden' id='hid_var' value='".json_encode($var_types)."' />";
    echo "<input type='hidden' id='loc_json' value='".json_encode($location)."' />";
    echo "<input type='hidden' id='all_json' value='".json_encode($def_locale_tax)."' />";
    ?>
    <div class="panel-heading">
	    <div class="panel-title">
    		<a data-toggle="collapse" data-parent="#var_content" href="#collapse-var-content"><small>Product having variants</small></a>
		</div>           
    </div>
    <div class="panel-body panel-collapse collapse in" id="collapse-var-content">
    	<div class="row">
        	<div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default" id="variant_table">
                    <div class="panel-heading">
                    	Classify your product variant
                        <h6>Use the dropdown options to select / create. Option name max 10 Characters allowed.</h6>
					</div>
                    <div class="panel-body">
        
                    </div>    
                    <div class="panel-footer">
                        <button type="button" class="add_row btn btn-xs btn-primary">Add Another Variant</button>
                        <h6>Maximum of <?php echo $max_variants ?> Variant types allowed. E.g. Size, Color, Gender Etc..</h6>
                    </div>    
                </div>
			</div>
		</div>    
		<?php if(form_error('new_var_method[]')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('new_var_method[]') ?></p><?php } ?>
		<?php if(form_error('var_type_name[]')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('var_type_name[]') ?></p><?php } ?>                
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
                $combo = array('Default Outlet Locale Tax' => array('' => 'Current outlet tax ('.$def_locale_tax['tax_val'][$key].'%)')) + $single_group_taxes_combo;
            ?>
            <tr>
                <td class="col-lg-3 col-md-3 col-sm-3 col-xs-3"><?php echo $def_locale_tax['location'][$key].form_hidden('def_location[]',$def_locale_tax['loc_id'][$key]) ?></td>
                <td class="col-lg-5 col-md-5 col-sm-3 col-xs-3">
                    <div class="input-group">
                        <?php echo form_dropdown('qty_scale_tax[]',$combo,'','class="qty_scale_tax form-control input-sm" id="qty_scale_tax_'.$def_locale_tax['loc_id'][$key].'"') ?>
                        <span class="input-group-btn"><a class="btn btn-sm btn-default" data-toggle="modal" data-target="#ajax_insert_dyn_modal" href="<?php echo base_url().'products/create_tax?for_outlet='.$def_locale_tax['loc_id'][$key] ?>"><i class="fa fa-plus"></i></a></span>
                    </div>
                </td>
                <td class="qty_scale_tax_amt col-lg-2 col-md-2 col-sm-3 col-xs-3">0</td>
                <td class="mrp_scale_tax_amt col-lg-2 col-md-2 col-sm-3 col-xs-3">0</td>
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
                <label for="loyalty_cust_val" class="input-group-addon" id="loyalty_def_span"></label>
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
<div id="blend_div">
    <div class="panel panel-default">
        <input type="hidden" id="blend_url" value="<?php echo site_url('products/blend_autocomplete') ?>">
        <div class="panel-heading">
            <div class="panel-title">
				<a data-toggle="collapse" data-parent="#blend_div" href="#collapse-blend-div"><small>Group Products</small></a> 
                <h6>*Search / Scan products to add them to group. Product qty can be fractional.</h6>
            </div>
		</div>            
        <div class="panel-body panel-collapse collapse in" id="collapse-blend-div">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group input-group">
                        <label for="item_sku" class="input-group-addon">Add Collections</label>
                        <?php echo form_input(array('autocomplete' => 'off','name' => 'item_sku','id' => 'item_sku', 'class' => 'form-control' , "placeholder" => "Product / SKU")) ?>
                    </div>
                    <?php if(form_error('blend_prd_qty[]')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('blend_prd_qty[]') ?></p><?php } ?>                
                </div>
            </div>
            <table id="bend_app_div">
                <tbody></tbody>
            </table>        
			<?php $this->load->view('products/example') ?>
        </div>    
    </div>    
</div>
<div class="panel panel-default" id="inventory_div">
    <div class="panel-heading">
		<?php echo form_checkbox(array('data-off-color' => 'danger', 'data-on-color' => 'success', 'data-on-text' => 'Yes', 'data-off-text' => 'No', 'data-label-text' => 'Track Inventory', 'data-size' => 'small', 'name' => 'trace_inv','id' => 'trace_inv','value' => 30,'checked' => TRUE))?> 
    </div>
    <div class="panel-body">
        <table id="inventory_table" class="table table-striped table-condensed table-curved">
            <thead>
                <tr>
                    <th class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Outlet</th>
                    <th class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Current Stock</th>
                    <th class="col-lg-3 col-md-3 col-sm-3 col-xs-3" data-toggle="popover" data-placement="top" data-content="While stock inventory reaches this level, stock is ordered">Reorder Level</th>
                    <th class="col-lg-3 col-md-3 col-sm-3 col-xs-3" data-toggle="popover" data-placement="top" data-content="The quantity for which the stock is to be ordered">Restock Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($def_locale_tax['loc_id'] as $key => $sub_array){ ?>
                    <tr>
                        <td><?php echo $def_locale_tax['location'][$key] ?></td>
                        <td><div class="input-group"><?php echo form_hidden('inv_outlet[]',$def_locale_tax['loc_id'][$key]).form_input(array('value' => set_value('cur_stk['.$key.']',0), 'autocomplete' => 'off','name' => 'cur_stk['.$key.']', "class" => "form-control input-sm", 'id' => 'cur_stk['.$key.']')) ?><span class="input-group-addon inv_postfix"></span></div></td>
                        <td><div class="input-group"><?php echo form_input(array('value' => set_value('reorder_stk['.$key.']',0),'autocomplete' => 'off','name' => 'reorder_stk['.$key.']',"class" => "form-control input-sm", 'id' => 'reorder_stk['.$key.']')) ?><span class="input-group-addon inv_postfix"></span></div></td>
                        <td><div class="input-group"><?php echo form_input(array('value' => set_value('reorder_qty['.$key.']',0),'autocomplete' => 'off','name' => 'reorder_qty['.$key.']',"class" => "form-control input-sm", 'id' => 'reorder_qty['.$key.']')) ?><span class="input-group-addon inv_postfix"></span></div></td>
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
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group input-group">
                    <label for="prd_weight" class="input-group-addon" data-toggle="popover" data-placement="top" data-content="Product Weight For Shipments / Delivery. With / Without packing">Product weight</label>
                    <?php echo form_input(array('autocomplete' => 'off','name' => 'prd_weight','id' => 'prd_weight',"class" => "form-control input-sm", "value" => set_value('prd_weight',0))) ?>
                    <span class="input-group-addon">Kg</span>
                </div>
            </div>
        </div>
        <div class="row text-danger"><div class="col-lg-12 col-md-12 col-sm-12"><small id="prd_ship_text"></small></div></div>
		<?php echo form_checkbox(array('data-off-color' => 'danger', 'data-on-color' => 'success','data-on-text' => 'Yes', 'data-off-text' => 'No', 'data-label-text' => 'Shipping', 'data-size' => 'small', 'name' => 'ship_stat','id' => 'ship_stat','value' => 30,'checked' => TRUE)) ?>
	</div>
</div>    

<div class="panel panel-default" id="track_div">
	<div class="panel-heading"><span data-toggle="popover" data-placement="top" data-content="Grant this product to show in Shopping Cart Integration">Shopping cart integration</span></div>
	<div class="panel-body">
		<?php echo form_checkbox(array('data-label-width' => 120,'data-off-color' => 'danger', 'data-on-color' => 'success', 'data-on-text' => 'Yes', 'data-off-text' => 'No', 'data-label-text' => 'Add to shopping cart', 'data-size' => 'small', 'name' => 'show_cart','id' => 'show_cart','value' => 30,'checked' => TRUE)) ?>
	</div>
</div>    
<button type="submit" name="insert_product" id="insert_product" class="btn btn-block btn-success loading_modal"><i class="fa fa-save"></i> Save Product</button>
<?php
echo form_close();
?>
<div id="add_var_form" class="modal modal-sm fade container" role="dialog" style="padding-top:15%;">
	<input type="hidden" id="cust_var_post_url" value="<?php echo site_url('products/add_custom_variant') ?>">	
    <input type="hidden" id="hid_sel" />	
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close dismiss_cust_var" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 id="dataConfirmLabel">Create Custom Variant</h4>
        </div>
        <div class="modal-body">
            <div class="form-group input-group">
	            <span class="input-group-addon">Key value</span>
    	        <input type="text" maxlength="10" autocomplete="off" name="new_add_var_key" id="new_add_var_key" class="form-control">
			</div>                
            <div class="form-group input-group">
	            <span class="badge">Ex: <small class="text-uppercase">Size, Colour, Gender</small>... etc</span>
            </div>
            <div class="form-group form_errors" id="error_var_key">
				<p class="col-md-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <small>Required / Max - 10 characters</small></p>
			</div>
            <div class="form-group input-group">
	            <span class="input-group-addon">Display value</span>
    	        <input type="text" maxlength="10" autocomplete="off" name="new_add_var_val" id="new_add_var_val" class="form-control">
			</div>                
            <div class="form-group input-group">
	            <span class="badge"><small>Ex: Size, Colour, Gender... etc</small></span>
			</div>	
            <div class="form-group form_errors" id="error_var_val">
				<p class="col-md-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <small>Required / Max - 10 characters</small></p>
			</div>
        </div>
        <div class="modal-footer">
        	<button type="button" id="butt_add_var" class = "btn btn-sm btn-success">Add Variant</button>
            <button class="btn btn-danger dismiss_cust_var" data-dismiss="modal" aria-hidden="false">Close</button>
        </div>  
    </div>  
</div>