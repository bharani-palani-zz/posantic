<h4><i class="fa fa-ambulance"></i> Stock Order</h4>
<h6>Order stocks to suppliers</h6>
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
<?php echo form_open_multipart(base_url().'inventory/add_stock_order',array('id' => 'myform','size' => '5000')); ?>
<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-list-alt"></i> Order form</div>
    <div class="panel-body">
		<div class="row">
			<div class="col-lg-6 col-md-6">
    
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="order_name" class="input-group-addon">Order name</label>
                                <?php echo form_input(array('autocomplete' => 'off','name' => 'order_name', 'id' => 'order_name',"class" => "form-control input-sm", "value" => set_value('order_name',$random_string))) ?>
                            </div>
                        </div>
                    </div>
                    <?php if(form_error('order_name')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('order_name') ?></p><?php } ?>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="supplier" class="input-group-addon">Supplier</label>
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
                                <label for="dest_outlet" class="input-group-addon">Destination Outlet</label>
                                <?php echo form_dropdown('dest_outlet', $outlets,set_value('dest_outlet'),'id="dest_outlet" class="form-control input-sm"') ?>
                            </div>
                        </div>
                    </div>
                    <?php if(form_error('dest_outlet')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('dest_outlet') ?></p><?php } ?>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <?php echo form_checkbox(array('data-label-width' => 150,'data-on-color' => 'success', 'data-off-color' => 'danger', 'data-on-text' => 'Yes', 'data-off-text' => 'No', 'data-label-text' => 'Auto fill reorder level stocks','data-size' => 'small','name' => 'reorder_stat', 'id' => 'reorder_true', 'checked' => true , 'value' => 30)) ?>
                        </div>
                        <h6>*Max 500 products at a time</h6>
                    </div>
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
            <div class="col-lg-12">
                <button type="submit" class="btn btn-success btn-md search_button loading_modal" id="start_order"><i class="fa fa-check"></i> Start Order</button> 
            </div>
        </div>
                 
	</div>
</div>   

<?php echo form_close() ?>