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
?>
<input type="hidden" id="setting_image" value="<?php echo base_url().APPPATH.'images/assets/settings.png' ?>">
<input type="hidden" id="quick_touch_json" value="<?php echo htmlentities(json_encode($touch_data)) ?>">
<input type="hidden" id="quick_touch_url" value="<?php echo base_url('quicktouch/update') ?>">
<input type="hidden" id="quick_search_url" value="<?php echo base_url('quicktouch/search') ?>">
<input type="hidden" id="quick_touch_uuid" value="<?php echo base_url('quicktouch/get_uuid') ?>">
<input type="hidden" id="merchant_id" value="<?php echo $this->session->userdata('acc_no') ?>">
<input type="hidden" id="touch_id" value="<?php echo $touch_id ?>">
<input type="hidden" id="max_qt_headers" value="<?php echo $max_qt_headers ?>">
<input type="hidden" id="max_qt_products_per_page" value="<?php echo $max_qt_products_per_page ?>">
<input type="hidden" id="max_qt_pages" value="<?php echo $max_qt_pages ?>">

<h4><i class="fa fa-desktop"></i> Quick Touch</h4>
<?php echo form_open(base_url().'setup/update_quicktouch/id/'.$touch_id,array('id' => 'touchform','size' => '5000')); ?>
<div class="panel panel-default">
    <div class="panel-heading">Configure Max <?php echo $max_qt_headers ?> header groups, <?php echo $max_qt_products_per_page ?> products per page with <?php echo $max_qt_pages ?> paginations for each group</div>
    <div class="panel-body">
		<?php $error_c = form_error('quicktouch_name') ? 'has-error' : ''; ?> 
        <div class="input-group form-group <?php echo $error_c ?> col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <label for="quicktouch_name" class="input-group-addon">Quick touch name</label>
            <?php echo form_input(array('autocomplete' => 'off','name' => 'quicktouch_name', 'id' => 'quicktouch_name',"class" => "form-control input-sm", "value" => set_value('quicktouch_name',$parent_touch['quickey_name']))) ?>
        </div>
		<?php if(form_error('quicktouch_name')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('quicktouch_name') ?></p><?php } ?>            
       <div class="input-group form-group col-lg-8 col-md-10 col-sm-12 col-xs-12">
            <label for="item_sku" class="input-group-addon">Select Products</label>
            <?php echo form_input(array('autocomplete' => 'off','name' => 'item_sku','id' => 'item_sku',"class" => "form-control input-md", "placeholder" => "Product / SKU")) ?>
            <span class="input-group-btn"><button type="button"  class="btn btn-danger" id="add_group"><i class="fa fa-plus"></i> Add Group</button></span>
        </div>

        <div class="table-responsive row">
            <div class="col-md-12 col-sm-12 col-xs-12">
        
                <div class="container-fluid" id="main">
                    <!--category field-->
                    <div class="row" id="cat_field"></div>               
                    <!--product field-->                                 
                    <div class="row" id="product_field"></div>    
                    <!--page field-->            
                    <div class="row" id="paginate_field">
                        <div class="pull-right"><button  type="button" id="add_page" class="btn btn-danger btn-circle fa fa-cog"></button>&nbsp;</div>
                    </div>                 
                </div>
                
            </div>
        </div>
	
    <!--panel body closes-->
    </div>
</div>
<div class="btn-group"> 
    <button type="submit" class="btn btn-success" name="update_quicktouch" id="update_quicktouch"><i class="fa fa-save"></i> Save Config</button>
    <?php 
    $delete = $parent_touch['is_delete'] == 20 ? '' : anchor('quicktouch/delete/id/'.$touch_id,'<i class="fa fa-times"></i> Delete', 'class = "btn btn-danger" data-confirm="Delete This Quick Touch? This cant be restored..."');
    echo $delete.anchor('setup/quicktouch','<i class="fa fa-power-off"></i> Cancel', 'class = "btn btn-danger"');
    ?>
</div>
<?php
echo form_close() 
?>