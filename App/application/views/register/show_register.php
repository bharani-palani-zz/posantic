<h4><span class="glyphicon glyphicon-map-marker"></span> Outlet <?php echo $location ?> "<?php echo $reg_code ?>" Register Details</h4>
<hr>

<div class="well well-sm">
	<div class="btn-group btn-group-sm">
		<?php echo anchor('setup/register/'.$reg_id.'/edit','<i class="fa fa-fw fa-pencil"></i> Edit Register','class = "btn btn-primary"') ?>
		<?php 
        $outlet_count = $this->outlet_model->outlet_count($this->session->userdata('acc_no'));
        echo $outlet_count > 1 ? anchor('register/delete/id/'.$reg_id,'<i class="fa fa-fw fa-trash-o"></i> Delete Register','class = "btn btn-danger" data-confirm="Delete this register? This cant be restored..."') : '' 
        ?>
    </div>
</div>    
<div class="table-responsive">
<?php
$status = array(40 => 'Disabled',30 => 'Enabled');
$tmpl = array (
	'table_open'   => '<table class="table table-bordered table-striped">',
);
$this->table->set_template($tmpl);			
$heading = array('Register Name','Email Receipt','Print Receipt','Ask User Change','Ask Quotes','Bill Prefix','Bill No Sequence');
$this->table->set_heading($heading);
$this->table->add_row($reg_code,$status[$email_reciept],$status[$print_reciept],$status[$switch_users],$status[$ask_quotes],$billno_prefix,$billno_sequence);				
echo $this->table->generate();
?>
</div>
<h3>Register Sales & closures - Pending</h3>
