<h4><i class="fa fa-cab"></i> Stock Transfer</h4>
<h6>Transfer stocks between outlets</h6>
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
<?php echo form_open_multipart(base_url().'inventory/add_stock_transfer',array('id' => 'myform','size' => '5000')); ?>
<div class="panel panel-default" id="product_panel">
    <div class="panel-heading"><i class="fa fa-list-alt"></i> Transfer form</div>
    <div class="panel-body">
		<div class="row">
			<div class="col-lg-6 col-md-6">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="transfer_name" class="input-group-addon">Tranfer name</label>
                                <?php echo form_input(array('autocomplete' => 'off','name' => 'transfer_name', 'id' => 'transfer_name',"class" => "form-control input-sm", "value" => set_value('transfer_name',$random_string))) ?>
                            </div>
                        </div>
                    </div>
                    <?php if(form_error('transfer_name')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('transfer_name') ?></p><?php } ?>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="source_outlet" class="input-group-addon">Source Outlet</label>
                                <?php echo form_dropdown('source_outlet', $outlets,set_value('source_outlet'),'id="source_outlet" class="form-control input-sm"') ?>
                            </div>
                        </div>
                    </div>
                    <?php if(form_error('source_outlet')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('source_outlet') ?></p><?php } ?>
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
                    </div>
                </div>  
			</div>
            <div class="col-lg-6 col-md-6">
				<?php 
				$data['supplier_note'] = false;
				$this->load->view('inventory/import_div',$data)
				?>
            </div>
		</div>            
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                <button type="submit" class="btn btn-success btn-sm search_button loading_modal" id="start_order"><i class="fa fa-check"></i> Start Transfer</button> 
            </div>
        </div>
	</div>
</div>   

<?php echo form_close() ?>