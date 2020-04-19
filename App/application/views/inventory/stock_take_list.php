<div class="hidden-print">
    <div class="btn-group btn-group-sm">
        <h4><i class="fa fa-clipboard fa-fw"></i>Stock Take</h4>
        <h6>Count stocks to maintain your inventory</h6>
    </div>
    <div class="pull-right">	
        <?php echo anchor('inventory/insert_stock_take','<i class="fa fa-plus fa-fw"></i>Add Stock take','class="btn btn-md btn-success"') ?>    
    </div>    
</div>
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

$tot['finished'] = array_key_exists(60,$all_details) ? count($all_details[60]['id']) : 0;
$tot['pending'] = array_key_exists(50,$all_details) ? count($all_details[50]['id']) : 0;
$tot['deleted'] = array_key_exists(120,$all_details) ? count($all_details[120]['id']) : 0;
$empty_div = '<h2 align="center" class="text-success"><i class="fa fa-clipboard fa-3x"></i></h2><h3 align="center" class="text-success">No records found</h3>';
?>
<div class="panel with-nav-tabs panel-success">
    <div class="panel-heading">
            <ul class="nav nav-tabs nav-justified">
                <li class="active"><a href="#all_details" data-toggle="tab">All</a></li>
                <li><a href="#finish_details" data-toggle="tab">Finished(<?php echo $tot['finished'] ?>)</a></li>
                <li><a href="#pending_details" data-toggle="tab">Pending(<?php echo $tot['pending'] ?>)</a></li>
                <li><a href="#deleted_details" data-toggle="tab">Deleted(<?php echo $tot['deleted'] ?>)</a></li>
            </ul>
    </div>
    <?php
    $tmpl1 = array (
        'table_open'   => '<table class="table table-striped stock_take_list" cellspacing="0" width="100%">',
        'heading_row_start'   => '<tr>',
        'heading_row_end'     => '</tr>',
        'heading_cell_start'  => '<th class="text-uppercase">',
        'heading_cell_end'    => '</th>',	
        
    );
    $this->table->set_template($tmpl1);	
    $heading = array('Stock take name','Outlet','Status');	
    ?>
    <div class="panel-body">
        <div class="table-responsive" style="overflow-x: initial;">
        <div class="tab-content">
            <div class="tab-pane fade in active" id="all_details">
            <?php
            $this->table->set_heading($heading);
            if(count($all_details) > 0)
            {
                foreach($all_details as $all_key => $stat_key)
                {
                    $edit_show_root = $all_key == '50' ? 'inventory/stock_take/edit/id/' : 'inventory/stock_take/show/id/';
                    foreach($stat_key['name'] as $s_key => $stat_value)
                    {
                        $created_at = unix_to_human(gmt_to_local(strtotime($stat_key['created_at'][$s_key]),$timezone, date("I")));
                        $this->table->add_row(
                                            anchor($edit_show_root.$stat_key['id'][$s_key],$stat_value,'class="btn btn-xs btn-default"').'<br><span class="label label-danger"><i class="fa fa-clock-o fa-fw"></i>'.$created_at.'</span>',
                                            $stat_key['location'][$s_key],
                                            $stat_key['status_code'][$s_key]
                                            );
                    }
                }
                echo $this->table->generate();
            } else {
                echo $empty_div;	
            }
            ?>                        
            </div>
            <div class="tab-pane fade" id="finish_details">
            <?php
            $this->table->set_heading($heading);
            if(array_key_exists(60,$all_details))
            {
                foreach($all_details[60]['id'] as $key => $value)
                {
                    $created_at = unix_to_human(gmt_to_local(strtotime($all_details[60]['created_at'][$key]),$timezone, date("I")));
                    $this->table->add_row(
                                        anchor('inventory/stock_take/show/id/'.$all_details[60]['id'][$key],$all_details[60]['name'][$key],'class="btn btn-xs btn-default"').'<br><span class="label label-danger"><i class="fa fa-clock-o fa-fw"></i>'.$created_at.'</span>',
                                        $all_details[60]['location'][$key],
                                        $all_details[60]['status_code'][$key]
                                        );
                }
                echo $this->table->generate();
            } else {
                echo $empty_div;				
            }
            ?>                        
            </div>
            <div class="tab-pane fade" id="pending_details">
            <?php
            $this->table->set_heading($heading);
            if(array_key_exists(50,$all_details))
            {
                foreach($all_details[50]['id'] as $key => $value)
                {
                    $created_at = unix_to_human(gmt_to_local(strtotime($all_details[50]['created_at'][$key]),$timezone, date("I")));
                    $this->table->add_row(
                                        anchor('inventory/stock_take/edit/id/'.$all_details[50]['id'][$key],$all_details[50]['name'][$key],'class="btn btn-xs btn-default"').'<br><span class="label label-danger"><i class="fa fa-clock-o fa-fw"></i>'.$created_at.'</span>',
                                        $all_details[50]['location'][$key],
                                        $all_details[50]['status_code'][$key]
                                        );
                }
                echo $this->table->generate();
            } else {
                echo $empty_div;				
            }
            ?>            
            </div>
            <div class="tab-pane fade" id="deleted_details">
            <?php
            $this->table->set_heading($heading);
            if(array_key_exists(120,$all_details))
            {
                foreach($all_details[120]['id'] as $key => $value)
                {
                    $created_at = unix_to_human(gmt_to_local(strtotime($all_details[120]['created_at'][$key]),$timezone, date("I")));
                    $this->table->add_row(
                                        anchor('inventory/stock_take/show/id/'.$all_details[120]['id'][$key],$all_details[120]['name'][$key],'class="btn btn-xs btn-default"').'<br><span class="label label-danger"><i class="fa fa-clock-o fa-fw"></i>'.$created_at.'</span>',
                                        $all_details[120]['location'][$key],
                                        $all_details[120]['status_code'][$key]
                                        );
                }
                echo $this->table->generate();
            } else {
                echo $empty_div;				
            }
            ?>                        
            </div>
        </div>
    </div>
    </div>
</div>

