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
<h4><i class="fa fa-map-marker fa-fw"></i> Outlets and Registers</h4>
<hr>
<div class="well well-sm hidden-print">
	<div class="btn-group  btn-group-sm">
		<?php echo anchor('setup/outlet/add','<i class="fa fa-plus"></i> Add Outlet','class = "btn btn-primary"') ?>    
        <div class="btn-group" role="menu">
            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Receipt templates <span class="caret"></span></button>    
            <ul class="dropdown-menu dropdown-menu-right">
              <li><?php echo anchor('setup/receipt_template/add','<i class="fa fa-plus-circle"></i> Add Receipt Template','class = ""') ?></li>
              <li role="presentation" class="divider"></li>
              <li><?php echo anchor('setup/receipt_template/show','<span class="glyphicon glyphicon-text-size"></span> Saved Receipt Templates','class = ""') ?></li>
            </ul>          
        </div>  
    </div>
</div>
<div class="table-responsive">
	<?php
    $tmpl = array (
        'table_open'   => '<table class="table table-striped table-curved table-condensed" id="out_reg_table">',
    );
    $this->table->set_template($tmpl);	
    $tax_header = $this->session->userdata('plan_store_handle') == 'Multiple' ? 'Default Locale Tax' : 'Default Outlet Tax';		
    $heading = array(
					'Outlet',
					$tax_header,
					'Registers',
					'Bill <span class="glyphicon glyphicon-text-size"></span>emplate',
					''
					); 
    $this->table->set_heading($heading);
    foreach($outlets as $o_key => $o_array)
    {
        $cell = array('data' => count(array_filter($o_array['reg_id']))> 0 ? count(array_filter($o_array['reg_id'])).' Register(s) <i class="fa fa-chevron-circle-down fa-fw"></i>' : 'No Register added','colspan' => 2);	
        $this->table->add_row(
                            anchor('setup/outlet/'.$o_key, $outlets[$o_key]['outlet_str'],'class="btn btn-xs btn-default"'),
                            $outlets[$o_key]['outlet_tax'].': ['.$outlets[$o_key]['tax_val'].'%]',$cell,
                            array(
								'data' => '<div class="btn-group btn-group-xs">'.anchor('setup/outlet/'.$o_key.'/edit','<span class="glyphicon glyphicon-edit "></span> Edit Outlet','class="btn btn-success btn-xs"')."&nbsp;".
								anchor('setup/register/'.$o_key.'/add','<span class="glyphicon glyphicon-plus"></span> Add register','class="btn btn-danger btn-xs"').'</div>',
								'align' => 'center',
							)
							);
        $cell = array('data' => '','align' => 'right', 'colspan' => 2);
        foreach($o_array['reg_id'] as $r_key => $reg_id)
        {
            if(!empty($reg_id))
            $this->table->add_row(
                            $cell,anchor('setup/register/'.$reg_id, $outlets[$o_key]['reg_code'][$r_key], 'class="btn btn-xs btn-primary"'),
                            anchor('setup/receipt_template/'.$outlets[$o_key]['template_id'][$r_key].'/edit','Edit Template','class="btn btn-default btn-xs"'),
                            array('data' => '<div class="btn-group  btn-group-xs">'.
								anchor('setup/register/'.$reg_id.'/edit','Edit Register','class="btn btn-default btn-xs"').'&nbsp;'.
								anchor('setup/quicktouch/edit/id/'.$outlets[$o_key]['quickey_index'][$r_key].'/edit','Edit Quick Touch','class="btn btn-default btn-xs"').
								'</div>','align' => 'center'
							)
							);	
        }
        $this->table->add_row(array('data' => '&nbsp;','colspan' => 5));
    }
    echo $this->table->generate()."\n";
	?>
</div>

