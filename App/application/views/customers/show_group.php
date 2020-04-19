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
	'table_open'   => '<table class="table table-striped table-curved">'
);
$this->table->set_template($tmpl);			
$heading = array('Customer Group Name','Created','');
$this->table->set_heading($heading);
$daylight_saving = date("I");
if(count($groups) > 0)
{	
	foreach($groups['grp_index'] as $key => $value)
	{
		$edit = $groups['is_delete'][$key] == 10 ? '<a class="btn btn-success btn-xs" data-toggle="modal" data-target="#ajax_c_grp_modal" href="'.base_url().'customers/edit_customer_group/'.$groups['grp_index'][$key].'"><i class="glyphicon glyphicon-edit"></i> Edit</a>' : '';
		$delete = $groups['is_delete'][$key] == 10 ? anchor('customer_group/delete/id/'.$groups['grp_index'][$key],'<i class="glyphicon glyphicon-remove"></i> Delete','class="btn btn-danger btn-xs" data-confirm="Deleting this customer group will update the associated customers to `General customer` group and remove promotions associated for this customer group? This cant be restored..."') : '';
		$this->table->add_row($groups['group_name'][$key],unix_to_human(gmt_to_local(strtotime($groups['updated_at'][$key]),$timezone, $daylight_saving)),
			array(
				'align' => 'center',
				'data' => '<div class="btn-group btn-group-xs">'.$edit.$delete.'</div>'
				)
		);
	}
} else {
	$this->table->add_row(array('data' => '<p>:::No Customers found:::</p>','colspan' => 8,'align' => 'center'));		
}
$grp_tbl = $this->table->generate();

?>
<h4><i class="fa fa-bars fa-fw"></i> Customer Group</h4>
<h5 class="hidden-print">Categorize your customers to groups from where they belong too. Ex: VIP, Celebrities, Family etc..</h5>
<hr>
<div class="well well-sm hidden-print">
    <?php echo anchor(base_url().'customers/add_group', '<i class="fa fa-plus fa-fw"></i>Add Customer Group','class = "btn btn-primary btn-sm" data-toggle="modal" data-target="#ajax_c_grp_modal"') ?>
	<?php echo anchor('customers','<i class="fa fa-angle-double-left"></i> Back','class = "btn btn-sm btn-outline btn-primary"') ?>    
</div>
<div class="modal fade" id="ajax_c_grp_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Available groups</h4>
	</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                <?php echo $grp_tbl ?>
                </div>
            </div>	
        </div>
    </div>
    <div class="panel-footer">
		<small class="text-capitalize"><span class="glyphicon glyphicon-hand-right"></span> Grouping helps particular customers to avail promotions</small>
	</div>
</div>