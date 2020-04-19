<h4><i class="fa fa-history"></i> Stock return</h4>
<h6>Return stocks from outlet to supplier</h6>
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
<?php echo form_open_multipart(base_url().'inventory/add_stock_return',array('id' => 'myform','size' => '5000')); ?>
<div class="panel panel-default" id="product_panel">
    <div class="panel-heading"><i class="fa fa-list-alt"></i> Return form</div>
    <div class="panel-body">
		<div class="row">
			<div class="col-lg-6 col-md-6">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="return_name" class="input-group-addon">Return name</label>
                                <?php echo form_input(array('autocomplete' => 'off','name' => 'return_name', 'id' => 'return_name',"class" => "form-control input-sm", "value" => set_value('return_name',$random_string))) ?>
                            </div>
                        </div>
                    </div>
                    <?php if(form_error('return_name')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('return_name') ?></p><?php } ?>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="supplier" class="input-group-addon">Return To Supplier</label>
                                <?php echo form_dropdown('supplier', $suppliers,set_value('supplier'),'id="supplier" class="form-control input-sm"') ?>
                            </div>
                        </div>
                    </div>
                    <?php if(form_error('supplier')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('supplier') ?></p><?php } ?>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="return_outlet" class="input-group-addon">Returning Outlet</label>
                                <?php echo form_dropdown('return_outlet', $outlets,set_value('return_outlet'),'id="return_outlet" class="form-control input-sm"') ?>
                            </div>
                        </div>
                    </div>
                    <?php if(form_error('return_outlet')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('return_outlet') ?></p><?php } ?>
                </div>
			</div>
            <div class="col-lg-6 col-md-6">
				<?php 
				$data['supplier_note'] = true;
				$this->load->view('inventory/import_div',$data)
				?>
            </div>
		</div>            
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                <button type="submit" class="btn btn-success btn-sm search_button loading_modal" id="start_return"><i class="fa fa-check"></i> Start Return</button> 
            </div>
        </div>
	</div>
</div>   

<?php echo form_close() ?>