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
list($yy,$mm,$dd) = explode("-",$sub_product_details['promo_start']);
$prom_start = checkdate($mm,$dd,$yy) ? $sub_product_details['promo_start'] : '';
list($e_yy,$e_mm,$e_dd) = explode("-",$sub_product_details['promo_end']);
$prom_end = checkdate($e_mm,$e_dd,$e_yy) ? $sub_product_details['promo_end'] : '';

?>
<h4><i class="fa fa-bullhorn"></i> Update Promotion</h4>
<?php echo form_open_multipart(base_url().'promotion/change/'.$sub_product_details['promotion_index'],array('id' => 'myform','size' => '5000')) ?>
<div class="panel panel-default hidden-print" id="product_panel">
    <div class="panel-heading"> Promotion Form</div>
    <div class="panel-body">
			<div class="row">
            	<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">	
                    <div class="form-group input-group">
                    	<label for="promo_name" class="input-group-addon">Promotion name</label>
                        <?php echo form_input(array('autocomplete' => 'off','name' => 'promo_name', 'id' => 'promo_name',"class" => "form-control input-sm", "value" => set_value('promo_name',$sub_product_details['promo_name']))) ?>
                    </div>
					<?php if(form_error('promo_name')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('promo_name') ?></p><?php } ?>
                </div>
            </div>        
			<div class="row">
            	<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">	
                    <div class="form-group input-group">
                    	<label for="promo_cust_group" class="input-group-addon">Customer Group</label>
                        <?php echo form_dropdown('promo_cust_group', $group_combo, $sub_product_details['customer_group'],'id="promo_cust_group" class="form-control input-sm"') ?>
                    </div>
					<?php if(form_error('promo_cust_group')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('promo_cust_group') ?></p><?php } ?>
                </div>
            </div>        
			<div class="row">
            	<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">	
                    <div class="form-group input-group">
                    	<label for="promo_outlet" class="input-group-addon">For Outlet</label>
                        <?php echo form_dropdown('promo_outlet', $company, $sub_product_details['loc_id'],'id="promo_outlet" class="form-control input-sm"') ?>
                    </div>
					<?php if(form_error('promo_outlet')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('promo_outlet') ?></p><?php } ?>
                </div>
            </div>        
			<div class="row"><div class="col-lg-6"><h6>*Leave date field blank for anytime promotion</h6></div></div>
			<div class="row">
            	<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">	
                    <div class="form-group input-group">
                    	<label for="promo_start" class="input-group-addon">Promotion Start</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'promo_start','id' => 'promo_start','class' => 'form-control input-sm','value' => set_value('promo_start',$prom_start))) ?>
                    </div>
					<?php if(form_error('promo_start')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('promo_start') ?></p><?php } ?>
                </div>
            </div>        
			<div class="row">
            	<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">	
                    <div class="form-group input-group">
                    	<label for="promo_end" class="input-group-addon">Promotion End</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'promo_end','id' => 'promo_end','class' => 'form-control input-sm','value' => set_value('promo_end',$prom_end))) ?>
                    </div>
					<?php if(form_error('promo_end')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('promo_end') ?></p><?php } ?>
                </div>
            </div>        
	</div>
</div>    
<div class="table-responsive">
	<?php
    $tmpl = array ( 'table_open'  => '<table class="table table-striped table-condensed table-curved dyn_add" id="edit_prom_table">' );
    $this->table->set_template($tmpl);			
    $heading = array('data' => "Add Products",'colspan' => 9);
	$heading = array(
		array('data' => 'Product/SKU','class' => 'locale_tax_header subtable_td'),
		array('data' => 'Supplier or<br>operated price','class' => 'locale_tax_header subtable_td'),
		array('data' => 'Margin%','class' => 'locale_tax_header subtable_td'),
		array('data' => 'Discount%','class' => 'locale_tax_header subtable_td'),
		array('data' => 'Retail Price<br> Without Tax','class' => 'locale_tax_header subtable_td'),
		array('data' => 'Loyalty<br>To Earn','class' => 'locale_tax_header subtable_td'),
		array('data' => 'Min<br>Units','class' => 'locale_tax_header subtable_td'),
		array('data' => 'Max<br>Units','class' => 'locale_tax_header subtable_td'),
		array('data' => '','class' => 'locale_tax_header subtable_td')
	);
	
    $this->table->set_heading($heading);
    echo '<input type="hidden" id="prom_url" value="'.site_url('promotion/promo_autocomplete').'">';
    echo '<input type="hidden" id="prom_del_url" value="'.site_url('promotion/delete_single_product').'">';
    echo '<input type="hidden" id="ins_prom_url" value="'.site_url('promotion/insert_ajax_promo').'">';
    echo '<input type="hidden" id="base_url" value="'.base_url().'">';
	echo '<input type="hidden" id="promotions_parent_id" value="'.$sub_product_details['promotion_index'].'">';
    ?>
    <div class="panel panel-default" id="product_panel">
        <div class="panel-heading"> Add Products</div>
        <div class="panel-body">
			<div class="table-responsive">
                <div class="table-curved-div">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-4 col-xs-4">	
                            <div class="form-group">
                                <?php  echo form_input(array('autocomplete' => 'off','name' => 'item_sku','id' => 'item_sku', 'class' => 'form-control', "placeholder" => "Search Product / SKU / handle")) ?>
                            </div>
                        </div>
                    </div>   
                    <?php
                    $this->table->add_row(
                        '<kbd>Change All</kbd>','',
                        array('data' => form_input(array('autocomplete' => 'off', 'size' => 5, 'name' => 'all_promo_margin','id' => 'all_promo_margin','class' => 'form-control input-sm')),'align' => 'center'),
                        array('data' => form_input(array('autocomplete' => 'off', 'size' => 5, 'name' => 'all_promo_disc','id' => 'all_promo_disc','class' => 'form-control input-sm')),'align' => 'center'),
                        '','',
                        array('data' => form_input(array('autocomplete' => 'off', 'size' => 5, 'name' => 'all_promo_min','id' => 'all_promo_min','class' => 'form-control input-sm')),'align' => 'center'),
                        array('data' => form_input(array('autocomplete' => 'off', 'size' => 5, 'name' => 'all_promo_max','id' => 'all_promo_max','class' => 'form-control input-sm')),'align' => 'center'),
                        ''
                    );
                    if(isset($sub_product_array['child_id']))
                    {
                        foreach($sub_product_array['child_id'] as $key => $value)
                        {
                            $this->table->add_row(
                                            anchor('products/'.$sub_product_array['product_id'][$key],$sub_product_array['product_name'][$key],'class="btn btn-xs btn-default"').
                                            '<input type="hidden" name="promotions[child_id][]" class="promotions_child_id" value="'.$sub_product_array['child_id'][$key].'">'.
                                            '<input type="hidden" name="promotions[product_id][]" class="promotions_product_id" value="'.$sub_product_array['product_id'][$key].'">'.
                                            '<input type="hidden" class="all_supp" value="'.$sub_product_array['supplier_price'][$key].'">',
                                            array('data' => $this->currency_model->moneyFormat($sub_product_array['supplier_price'][$key],$this->session->userdata('currency'))),
                                            array('data' => form_input(array('autocomplete' => 'off', 'size' => 5, 'name' => 'promotions[promo_margin][]','class' => 'form-control input-sm all_margin','value' => $sub_product_array['margin'][$key])),'align' => 'center'),
                                            array('data' => form_input(array('autocomplete' => 'off', 'size' => 5, 'name' => 'promotions[promo_disc][]','class' => 'form-control input-sm all_discount','value' => $sub_product_array['discount'][$key])),'align' => 'center'),
                                            array('data' => form_input(array('autocomplete' => 'off', 'size' => 5, 'name' => 'promotions[promo_mrp][]','class' => 'form-control input-sm all_retail','value' => $sub_product_array['retail_price'][$key])),'align' => 'center'),
                                            array('data' => form_input(array('autocomplete' => 'off', 'size' => 5, 'name' => 'promotions[promo_loyalty][]','class' => 'form-control input-sm','value' => $sub_product_array['loyalty'][$key])),'align' => 'center'),
                                            array('data' => form_input(array('autocomplete' => 'off', 'size' => 5, 'name' => 'promotions[promo_min_units][]','class' => 'form-control input-sm all_min','value' => $sub_product_array['min_qty'][$key])),'align' => 'center'),
                                            array('data' => form_input(array('autocomplete' => 'off', 'size' => 5, 'name' => 'promotions[promo_max_units][]','class' => 'form-control input-sm all_max','value' => $sub_product_array['max_qty'][$key])),'align' => 'center'),
                                            array('data' => '<button type="button" class="btn btn-xs btn-danger del_row"><i class="fa fa-remove"></i></button>','align' => 'center')
                                            
                                            );
                        }
                    }
                    echo $this->table->generate();			
                    echo $links;
                    ?>    
                </div>            
            </div>            
        </div>	
    </div>
</div>
<div class="input-group">
    <div class="btn-group btn-group-sm"> 
        <button type="submit" class="btn btn-success" name="insert_promotion" id="insert_promotion"><i class="fa fa-save"></i> Save Promotion</button>
        <?php echo anchor('promotion','<i class="fa fa-times"></i> Cancel', 'class = "btn btn-danger"') ?>
    </div>    
</div>
<?php echo form_close() ?>