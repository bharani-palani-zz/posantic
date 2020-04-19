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
$tmpl = array ( 'table_open'  => '<table class="table table-striped table-condensed table-curved" id="pay_methods">' );
$this->table->set_template($tmpl);			
$heading = array('Payment Methods','Action');
$this->table->set_heading($heading);
//echo '<pre>';
//print_r($pay_methods);
if(count($pay_methods) > 0)
{
	foreach($pay_methods['pay_master_id'] as $key => $value)
	{
		$delete = $pay_methods['is_delete'][$key] == 30 ? anchor('payment_method/delete/id/'.$pay_methods['pay_master_id'][$key],'<i class="fa fa-times"></i>','class="btn btn-danger btn-xs" data-confirm="Delete this payment type? This cant be restored..."') : '';
		$this->table->add_row(
			$pay_methods['pay_alias_name'][$key],
			anchor(base_url('payment_method/edit_form/'.$pay_methods['pay_master_id'][$key]),'<i class="fa fa-edit"></i>','class = "btn btn-success btn-xs" data-toggle="modal" data-target="#ajax_pay_modal"').'&nbsp;'.
			$delete
		);
	}
} else {
	$this->table->add_row(array('data' => ':::Payment Method Not Yet Set:::','colspan' => 2,'align' => 'center'));
}
$pay_table =  $this->table->generate().'<br>';

?>
<h4><i class="fa fa-cc-visa fa-fw"></i> Payment Methods</h4>
<h6 class="hidden-print">Configure your prefered payment methods that suits your business. These methods will be prompted on your sell screen during sale payments</h6>
<hr>
<div class="well well-sm hidden-print">
    <?php echo anchor(base_url().'payment_method/add','<i class="fa fa-plus fa-fw"></i> Add Payment Method','class = "btn btn-primary btn-sm" data-toggle="modal" data-target="#ajax_pay_modal"') ?>
</div>
<div class="modal fade" id="ajax_pay_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        Personalised Payment Methods For Your Business
	</div>
    <div class="panel-body">
        <div class="table-responsive">
	        <?php echo $pay_table ?>
        </div>
    </div>
    <div class="panel-footer">
		<small class="text-capitalize"><span class="fa fa-hand-o-right fa-fw"></span> Integrated payment methods requires special authentication. if you need one of them, be ready with your parameters</small>
	</div>
</div>