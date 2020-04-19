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
<h4><i class="fa fa-bullhorn"></i> Add Promotion</h4>
<h6>*Please check you have set products correctly before starting this wizard</h6>
<div class="panel panel-default" id="product_panel">
    <div class="panel-heading"> Promotion Form</div>
    <div class="panel-body">
		<?php echo form_open_multipart(base_url().'promotion/insert_promotion',array('id' => 'myform','size' => '1000')); ?>
			<div class="row">
            	<div class="col-lg-4">	
                    <div class="form-group input-group">
                    	<label for="promo_name" class="input-group-addon">Promotion name</label>
                        <?php echo form_input(array('autocomplete' => 'off','name' => 'promo_name', 'id' => 'promo_name',"class" => "form-control input-sm", "value" => set_value('promo_name','Promo('.$random_string.') '.date('d/m/Y')))) ?>
                    </div>
					<?php if(form_error('promo_name')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('promo_name') ?></p><?php } ?>
                </div>
            </div>        
			<div class="row">
            	<div class="col-lg-4">	
                    <div class="form-group input-group">
                    	<label for="promo_cust_group" class="input-group-addon">Customer Group</label>
                        <?php echo form_dropdown('promo_cust_group', $group_combo, '','id="promo_cust_group" class="form-control input-sm"') ?>
                    </div>
					<?php if(form_error('promo_cust_group')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('promo_cust_group') ?></p><?php } ?>
                </div>
            </div>        
			<div class="row">
            	<div class="col-lg-4">	
                    <div class="form-group input-group">
                    	<label for="promo_outlet" class="input-group-addon">For Outlet</label>
                        <?php echo form_dropdown('promo_outlet', $company, '','id="promo_outlet" class="form-control input-sm"') ?>
                    </div>
					<?php if(form_error('promo_outlet')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('promo_outlet') ?></p><?php } ?>
                </div>
            </div>        
			<div class="row"><div class="col-lg-6"><h6>*Leave date field blank for anytime promotion</h6></div></div>
			<div class="row">
            	<div class="col-lg-4">	
                    <div class="form-group input-group">
                    	<label for="promo_start" class="input-group-addon">Promotion Start</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'promo_start','id' => 'promo_start','class' => 'form-control input-sm','value' => set_value('promo_start'))) ?>
                    </div>
					<?php if(form_error('promo_start')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('promo_start') ?></p><?php } ?>
                </div>
            </div>        
			<div class="row">
            	<div class="col-lg-4">	
                    <div class="form-group input-group">
                    	<label for="promo_end" class="input-group-addon">Promotion End</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'promo_end','id' => 'promo_end','class' => 'form-control input-sm','value' => set_value('promo_end'))) ?>
                    </div>
					<?php if(form_error('promo_end')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('promo_end') ?></p><?php } ?>
                </div>
            </div>        
			<div class="row">
            	<div class="col-lg-4">	
                    <div class="form-group input-group">
                        <span class="input-group-addon">CSV import</span>
                        <label class="btn btn-primary" for="my-file-selector">
                            <?php echo form_upload(array('name' => 'userfile', 'id' => 'my-file-selector', 'style' => 'display:none;')) ?>
                            Choose file...
                        </label>    
                    </div>
					<p><?php echo anchor('promotion/csv_template','Download template','class="btn btn-xs btn-info"') ?> <small>Max CSV file size: 2MB</small></p>
                </div>
            </div>        
			<div class="row">
            	<div class="col-lg-4">	
                    <div class="input-group">
                        <div class="btn-group btn-group-sm"> 
                            <button type="submit" class="btn btn-success" name="insert_promotion" id="insert_promotion"><i class="fa fa-save"></i> Save Promotion</button>
                            <?php echo anchor('promotion','<i class="fa fa-times"></i> Cancel', 'class = "btn btn-danger"') ?>
                        </div>    
                    </div>
				</div>
			</div>                
        <?php echo form_close() ?>
	</div>
</div>    