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

$add_new = anchor(base_url().'taxes/add','<i class="fa fa-plus fa-fw"></i>Add New Tax','class = "btn btn-primary btn-sm" data-toggle="modal" data-target="#ajax_tax_modal"');
$add_new_grp = anchor(base_url().'taxes/group/add','<span class="glyphicon glyphicon-compressed"></span> Group Taxes','class = "bl_button btn btn-primary btn-sm" data-toggle="modal" data-target="#ajax_tax_modal"');
$tmpl = array(
			'table_open'   => '<table class="table table-curved table-striped">'
			);
$this->table->set_template($tmpl);			
$heading = array('Tax Name','Tax rate','');
$this->table->set_heading($heading);
foreach($taxes['tax_id'] as $key => $tax_id)
{
	$options = $taxes['is_delete'][$key] == 10 ? '<a data-toggle="modal" data-target="#ajax_tax_modal" href="'.base_url().'taxes/edit_single/'.$tax_id.'"><i class="btn btn-success btn-xs glyphicon glyphicon-edit"></i></a>'.'&nbsp;'.anchor('taxes/'.$tax_id.'/delete','<i class="btn btn-danger btn-xs glyphicon glyphicon-remove"></i>','class="" data-confirm="delete this tax? This cant be restored..."') : '&nbsp;';
	$this->table->add_row($taxes['tax_name'][$key],$taxes['tax_val'][$key].'%',array('data' => $options,'align' => 'center'));				
}
$rate_tbl = $this->table->generate();		

$tax_header = $this->session->userdata('plan_store_handle') == 'Multiple' ? 'Outlet locale Taxes' : 'Default Outlet Tax';
$heading = array('Outlet','Tax name','Tax rate','');
$this->table->set_heading($heading);
foreach($outlet_taxes['loc_id'] as $key => $loc_id)
{
	$this->table->add_row(anchor('setup/outlet/'.$loc_id,$outlet_taxes['location'][$key],array('class' => 'btn btn-primary btn-xs')),
	$outlet_taxes['tax_name'][$key],$outlet_taxes['tax_val'][$key].'%',
	array('data' => anchor('setup/outlet/'.$loc_id.'/edit','<i class="btn btn-success btn-xs glyphicon glyphicon-edit"></i>'),'align' => 'center'));
}
$def_tax_tbl = $this->table->generate();

$heading = array('Group Tax Name','Associated Taxes','Rate','');
$this->table->set_heading($heading);
if(count($group_taxes) > 0)
{
	foreach($group_taxes as $grp_key => $grp_array)
	{
		$this->table->add_row($grp_array['group_name'],array('data' => count($grp_array['tax_id']).' Taxes found <i class="fa fa-chevron-circle-down fa-fw"></i>','colspan' => 3));
		foreach($grp_array['tax_names'] as $t_key => $tax_name_str)
		{
			$this->table->add_row('',array('data' => $tax_name_str,'align' => 'right'),array('data' => $grp_array['tax_val'][$t_key].'%' ,'align' => 'right'),'');	
		}
		$this->table->add_row(array('data' => '<b>Total</b>','align' => 'right','colspan' => 2),
			array('style' => 'border-top:#6e6e6e double 3px; border-bottom:#6e6e6e double 3px;',
				'data' => '<b>'.array_sum($grp_array['tax_val']).'%</b>','align' => 'right'
			),						
			array('data' =>
				'<a data-toggle="modal" data-target="#ajax_tax_modal" href="'.base_url().'taxes/edit_group/'.$grp_array['parent_id'].'" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;'.
				anchor('taxes/group/'.$grp_array['parent_id'].'/delete','<i class="glyphicon glyphicon-remove"></i>','class="btn btn-danger btn-xs" data-confirm="Delete Group Tax ???<br> This cant be restored..."'),
				'align' => 'center'				
				)
			);			
		$this->table->add_row(array('data' => '&nbsp;','colspan' => 4));
	}
} else {
	$this->table->add_row(array('data' => '::: Not Yet Set :::','colspan' => 4,'align' => 'center'));
}
$grp_tax_tbl = $this->table->generate();

?>
<!--For Ajax Loading form idea is here-->
<div class="modal fade" id="ajax_tax_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        </div>
    </div>
</div>

<h4><i class="fa fa-bolt fa-fw"></i> Sales Taxes</h4>
<h6 class="hidden-print">Add taxes for your products or outlets. These tax'es can be updated any time, before sales</h6>
<hr>
<div class="well well-sm hidden-print">
    <?php echo $add_new.' '.$add_new_grp ?>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Tax Rates</h4>
	</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                <?php echo $rate_tbl ?>
                </div>
            </div>	
        </div>
    </div>
    <div class="panel-footer">
		<small class="text-capitalize"><i class="fa fa-hand-o-right fa-fw"></i> Update or Delete taxes any time, before sales</small>
	</div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4><?php echo $tax_header ?></h4>
	</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                <?php echo $def_tax_tbl ?>
                </div>
            </div>	
        </div>
    </div>
    <div class="panel-footer">
		<small class="text-capitalize"><i class="fa fa-hand-o-right fa-fw"></i> Update outlet locale taxes any time, before sales</small>
	</div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
       <h4>Group Taxes</h4>
	</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                <?php echo $grp_tax_tbl ?>
                </div>
            </div>	
        </div>
    </div>
    <div class="panel-footer">
		<small class="text-capitalize"><span class="glyphicon glyphicon-compressed"></span> Group taxes together incase your products/outlets have multiple taxes. Update/delete anytime, before sales</small>
	</div>
</div>