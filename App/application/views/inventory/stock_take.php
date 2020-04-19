<h4><i class="fa fa-sort-amount-asc"></i> Stock Take</h4>
<h6>Count stocks in your outlets</h6>
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
<?php echo form_open_multipart(base_url().'inventory/add_stock_take',array('id' => 'myform','size' => '5000')); ?>
<div class="panel panel-default" id="product_panel">
    <div class="panel-heading"><i class="fa fa-list-alt"></i> Stock take form</div>
    <div class="panel-body">
		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
	            <div class="form-group">
                    <div class="input-group">
                        <label for="take_name" class="input-group-addon">Stock take name</label>
						<?php echo form_input(array('autocomplete' => 'off','name' => 'take_name', 'id' => 'take_name',"class" => "form-control input-sm", "value" => set_value('take_name',$random_string))) ?>
					</div>
        		</div>
    		</div>
			<?php if(form_error('take_name')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('take_name') ?></p><?php } ?>
        </div>
		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
	            <div class="form-group">
                    <div class="input-group">
                        <label for="outlet" class="input-group-addon">Select Outlet</label>
						<?php echo form_dropdown('outlet', $outlets,set_value('outlet'),'id="outlet" class="form-control input-sm"') ?>
					</div>
        		</div>
    		</div>
			<?php if(form_error('outlet')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('outlet') ?></p><?php } ?>
        </div>
		<div class="row">
			<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                <button type="submit" class="btn btn-success btn-sm search_button loading_modal" id="start_count"><i class="fa fa-sort-amount-asc fa-fw"></i> Start Counting</button> 
			</div>
		</div>
	</div>
</div>   
<?php echo form_close() ?>
