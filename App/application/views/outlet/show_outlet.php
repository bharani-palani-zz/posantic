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
	'table_open'   => '<table class="table table-bordered table-striped table-condensed">',
);
$this->table->set_template($tmpl);			
$this->table->add_row('Address - 1',$l1);				
$this->table->add_row('Address - 2',$l2);				
$this->table->add_row('City',$city);				
$this->table->add_row('Postal code',$pcode);				
$this->table->add_row('State',$state);				
$this->table->add_row('Country',$country);				
$this->table->add_row('Phone',$ll);				
$this->table->add_row('Email',$email);				
$this->table->add_row('Outlet Tax',$tax_name.': ['.$tax_val.'%]');				
$this->table->add_row(array('data' => '&nbsp;', 'colspan' => 2));
$tbl = $this->table->generate();

$heading = array('Register name',array('data' => 'Receipt Template Name', 'colspan' => 2),'');
$this->table->set_heading($heading);
if(isset($registers))
{
	foreach($registers as $key => $reg_array)
	{
		$this->table->add_row(
			anchor('setup/register/'.$registers[$key]['reg_id'],$registers[$key]['reg_code'],'class="btn btn-xs btn-primary"'),
			$registers[$key]['template_name'],
			anchor('receipt_template/'.$registers[$key]['template_id'].'/edit','<span class="glyphicon glyphicon-edit "></span>','class="btn btn-success btn-xs btn-circle" data-placement="top" data-toggle="popover" data-content="Edit Template"'),
			anchor('setup/register/'.$registers[$key]['reg_id'].'/edit','<span class="glyphicon glyphicon-pencil"></span>','class="btn btn-success btn-xs btn-circle" data-placement="top" data-toggle="popover" data-content="Edit Register"'));				
	}
	$this->table->add_row(array('data' => '&nbsp;', 'colspan' => 4));
} else {
	$this->table->add_row(array('data' => '::No Register added for this outlet::','colspan' => 4,'align' => 'center'));
}
$reg_table = $this->table->generate();

?>
<h4><span class="glyphicon glyphicon-map-marker"></span> Outlet details</h4>
<hr>
<div class="well well-sm">
    <div class="btn-group btn-group-sm">
        <?php echo anchor('setup/register/'.$id.'/add','<i class="fa fa-fw fa-plus"></i> Add Register','class = "btn btn-primary"')?>
        <?php echo anchor('setup/outlet/'.$id.'/edit','<i class="fa fa-fw fa-pencil"></i> Edit Outlet','class="btn btn-primary"')?>
        <?php
        $outlet_count = $this->outlet_model->outlet_count($this->session->userdata('acc_no'));
        $delete = $outlet_count > 1 ? '&nbsp;'.anchor('outlet/'.$id.'/delete','<i class="fa fa-trash-o fa-fw"></i> Delete Outlet','class="btn btn-danger" data-confirm="Delete this Outlet? This cant be restored..."') : '';
        echo $delete;
        ?>                    
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo $loc_str ?> outlet details</div>
    <div class="table-responsive">
        <div class="panel-body">
            <?php echo $tbl ?>
        </div>
    </div>
    <div class="panel-footer">
		<i class="fa fa-hand-o-right fa-fw "></i> Furnish correct outlet details for ecommerce to find your outlets
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo $loc_str ?> outlet registers</div>
    <div class="table-responsive">
        <div class="panel-body">
            <?php echo $reg_table ?>
        </div>
    </div>
    <div class="panel-footer">
		<i class="fa fa-hand-o-right fa-fw "></i> Create multiple registers for this outlet, incase your checkout lane is not enough..
    </div>
</div>