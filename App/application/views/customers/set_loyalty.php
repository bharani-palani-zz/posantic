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
$tmpl = array (
	'table_open'   => '<table class="table table-bordered table-striped">'
);
$this->table->set_template($tmpl);			
echo form_open(base_url().'setup/update_loyalty');
echo form_hidden('hid_id',$l_id);
?>
<h4><i class="fa fa-gift fa-fw"></i> Loyalty Setttings</h4>
<hr>
<div class="panel panel-default">
    <div class="panel-heading">
        Do you wish to offer loyalty for your customers ???
	</div>
    <div class="panel-body">
        <div class="form-group">
        	Grant Loyalty
			<?php echo form_radio(array('data-label-text' => 'Enabled','data-size' => 'small','name' => 'enable_loyalty', 'id' => 'enable_true', 'checked' => $status == 10 ? true : false, 'value' => 10)) ?>
            &nbsp;
            <?php echo form_radio(array('data-label-text' => 'Disabled','data-size' => 'small','name' => 'enable_loyalty', 'id' => 'enable_false', 'checked' => $status == 20 ? true : false, 'value' => 20)) ?>
		</div>
        <div class="row">
            <div class="form-group col-md-6">
                <div class="input-group">
                    <label for="loyalty_sale" class="input-group-addon">Sale value</label>                                    	
                    <?php echo form_input(array('autocomplete' => 'off', 'name' => 'loyalty_sale', 'id' => 'loyalty_sale', 'class' => 'form-control','value' => set_value('loyalty_sale',$sale_value))) ?>
                </div>
                <?php if (form_error('loyalty_sale')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('loyalty_sale') ?></div></p><?php } ?>
            </div>         
        </div>           
        <div class="row">
            <div class="form-group col-md-6">
                <div class="input-group">
                    <label for="loyalty_reward" class="input-group-addon">Reward value</label>                                    	
                    <?php echo form_input(array('autocomplete' => 'off', 'name' => 'loyalty_reward', 'id' => 'loyalty_reward', 'class' => 'form-control','value' => set_value('loyalty_reward',$reward_value))) ?>
                </div>
                <?php if (form_error('loyalty_reward')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('loyalty_reward') ?></div></p><?php } ?>
            </div>                    
		</div>
    </div>
    <div class="panel-footer">
		<?php echo anchor('customers','<i class="fa fa-angle-double-left"></i> Back','class = "btn btn-md btn-outline btn-success"') ?>        
    	<button type="submit" name="save_loyalty" id="save_loyalty" class="btn btn-success">
        	<i class="fa fa-save"></i> Save Setting
        </button>
	</div>
</div>
    
<?php
echo form_close();
?>