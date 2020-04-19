<h4><i class="fa fa-flask fa-fw"></i> Inventory Activity</h4>
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
<?php echo form_open(base_url().'inventory/lookup',array('method' => 'get','id' => 'search_form')) ?>
<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-filter"></i> Filter inventory</div>
    <div class="panel-body">
		<div class="row">
			<div class="col-md-6">
	            <div class="form-group">
                    <div class="input-group">
                        <label for="transfer_name" class="input-group-addon">Transfer name</label>
						<?php echo form_input(array('autocomplete' => 'off', 'value' => isset($_GET['transfer_name']) ? $_GET['transfer_name'] : '', 'placeholder' => 'Search','id' => 'transfer_name','name' => 'transfer_name',"class" => "form-control input-sm")) ?>
					</div>
        		</div>
    		</div>
        </div>
        <div class="effect">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="transfer_stat" class="input-group-addon">Show</label>
                            <?php echo form_dropdown('transfer_stat', $log_codes,isset($_GET['transfer_stat']) ? $_GET['transfer_stat'] : $init_transfer_stat,'id="transfer_stat" class="form-control input-sm"')?>
                        </div>
                    </div>
                </div> 
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="from_date" class="input-group-addon">From Date</label>
                            <?php echo form_input(array('style' => 'background:#fff','autocomplete' => 'off','value' => isset($_GET['from_date']) ? $_GET['from_date'] : '', 'id' => 'from_date','name' => 'from_date',"class" => "form-control input-sm","readonly" => "readonly")) ?>
                        </div>
                    </div>
                </div> 
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="to_date" class="input-group-addon">To Date</label>
                            <?php echo form_input(array('style' => 'background:#fff','autocomplete' => 'off','value' => isset($_GET['to_date']) ? $_GET['to_date'] : '', 'id' => 'to_date','name' => 'to_date',"class" => "form-control input-sm","readonly" => "readonly")) ?>
                        </div>
                    </div>
                </div> 
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="source_outlet" class="input-group-addon">Source Outlet</label>
                            <?php echo form_dropdown('source_outlet', $outlets,isset($_GET['source_outlet']) ? $_GET['source_outlet'] : '','id="source_outlet" class="form-control input-sm"')?>
                        </div>
                    </div>
                </div> 
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="dest_outlet" class="input-group-addon">Destination Outlet</label>
                            <?php echo form_dropdown('dest_outlet', $outlets,isset($_GET['dest_outlet']) ? $_GET['dest_outlet'] : '','id="dest_outlet" class="form-control input-sm"') ?>
                        </div>
                    </div>
                </div> 
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="supplier" class="input-group-addon">Supplier</label>
                            <?php echo form_dropdown('supplier', $suppliers,isset($_GET['supplier']) ? $_GET['supplier'] : '','id="supplier" class="form-control input-sm"') ?>
                        </div>
                    </div>
                </div> 
            </div>

		</div>
        <div class="row">
	        <div class="col-md-12">
                <div class="btn-group">
                <button type="submit" class="btn btn-success btn-sm search_button loading_modal" id="prd_search"><i class="fa fa-search"></i> Search</button> 
                <button type="button" id="filter_button" class="btn btn-danger btn-sm">Filter Options <span class="caret"></span></button>
                <?php if(count($_GET) > 0) { ?>
                <?php echo anchor(base_url().'inventory','<i class="fa fa-times-circle"></i> Clear filter','class="btn btn-default btn-sm bg-danger"') ?>
                <?php } ?>
                </div>
            </div>
        </div>
		<?php
        if(!empty($_GET['supplier']) || !empty($_GET['dest_outlet']) || !empty($_GET['source_outlet']) || !empty($_GET['to_date']) || !empty($_GET['from_date']) || !empty($_GET['transfer_stat'])) {
			echo '<input type="hidden" id="toggle_filter" value="1">';
		} else {
			echo '<input type="hidden" id="toggle_filter" value="0">';				
        } 
		?>
        
	</div>
</div>    
<?php echo form_close();?>
<?php
$daylight_saving = date("I");
$tmpl = array ( 'table_open'  => '<table class="table table-striped table-condensed table-curved" id="activity_table">' );

$header_keys = array('transfer_name','created_at','source_outlet','dest_outlet','ordered','status','towards');
$filter_array = array('transfer_name','transfer_stat','created_at','source_outlet','dest_outlet','ordered','status','towards','from_date','to_date');
$arrow_root = base_url().APPPATH.'images/assets/arrows/';
if(isset($_GET['transfer_name']) || isset($_GET['transfer_stat']) || isset($_GET['from_date']) || isset($_GET['to_date']) || isset($_GET['dest_outlet']) || isset($_GET['source_outlet']) || isset($_GET['supplier']))
{
	//$get_filter = array();
	foreach($filter_array as $value)
	{
		if(isset($_GET[$value]) && !empty($_GET[$value]))
		{
			if(!is_array($_GET[$value]))
			{
				if(strlen($_GET[$value]) > 0)
				{
					$get_filter[$value] = $_GET[$value];
				}
			} else {
				$get_filter[$value] = $_GET[$value];
			}
		}
	}
} else {
	$get_filter = array("" => "");
}

$get_filter = strlen(http_build_query($get_filter)) > 1 ? "&".http_build_query($get_filter) : ''; // check this, but works good
$get_filter_str = substr($get_filter,1);
if(isset($_GET['sort']))
{
	foreach($header_keys as $h_values)
	{
		switch ($_GET['sort']) 
		{
		   case $h_values:
				if(isset($_GET['flow']))
				{		
					if($_GET['flow'] == "asc")
					{
						$get_array[$h_values.'_flow'] = "&flow=desc";
						$get_array[$h_values.'_arrow'] = '<i class="fa fa-caret-up">';
					} else {
						$get_array[$h_values.'_flow'] = "&flow=asc";
						$get_array[$h_values.'_arrow'] = '<i class="fa fa-caret-down">';
					}
				} else {
					$get_array[$h_values.'_flow'] = "&flow=asc";
					$get_array[$h_values.'_arrow'] = '<i class="fa fa-caret-down">';
				}
				$unset_array = array_diff($header_keys,array($h_values));
				foreach($unset_array as $u_values)
				{
					$get_array[$u_values.'_flow'] = "";
					$get_array[$u_values.'_arrow'] = '<i class="fa fa-sort">';
				}		
			break;
		}
	}
} else {
	foreach($header_keys as $h_values)
	{
		$get_array[$h_values.'_flow'] = "";
		$get_array[$h_values.'_arrow'] = '<i class="fa fa-sort">';
	}
}
//$heading = array('Name','Created','Source Outlet','Destination Outlet','Count','Status','Towards');
$heading = array(
				anchor('inventory/lookup?sort=transfer_name'.$get_array['transfer_name_flow'].$get_filter,'Transfer name '.$get_array['transfer_name_arrow'],'class="loading_modal"'),
				anchor('inventory/lookup?sort=created_at'.$get_array['created_at_flow'].$get_filter,'Created '.$get_array['created_at_arrow'],'class="loading_modal"'),
				anchor('inventory/lookup?sort=source_outlet'.$get_array['source_outlet_flow'].$get_filter,'Source Outlet '.$get_array['source_outlet_arrow'],'class="loading_modal"'),
				anchor('inventory/lookup?sort=dest_outlet'.$get_array['dest_outlet_flow'].$get_filter,'Destination Outlet '.$get_array['dest_outlet_arrow'],'class="loading_modal"'),
				anchor('inventory/lookup?sort=ordered'.$get_array['ordered_flow'].$get_filter,'Count '.$get_array['ordered_arrow'],'class="loading_modal"'),
				anchor('inventory/lookup?sort=status'.$get_array['status_flow'].$get_filter,'Status '.$get_array['status_arrow'],'class="loading_modal"'),
				anchor('inventory/lookup?sort=towards'.$get_array['towards_flow'].$get_filter,'Towards '.$get_array['towards_arrow'],'class="loading_modal"'),
				''
				);

$this->table->set_template($tmpl);			
$this->table->set_heading($heading);
if(isset($all_activity['transfer_index']))
{
	foreach($all_activity['transfer_index'] as $key => $value)
	{
		$ed_btn = $all_activity['stat_id'][$key] == 5 ? anchor('inventory/freight/edit/id/'.$all_activity['transfer_index'][$key],'Edit','class="btn btn-xs btn-success"') : '';
		$this->table->add_row(
							anchor(base_url('inventory/freight/'.$all_activity['transfer_index'][$key]),$all_activity['transfer_name'][$key],'class="btn btn-xs btn-default"'),
							unix_to_human(gmt_to_local(strtotime($all_activity['created_at'][$key]),$timezone, $daylight_saving)),
							$all_activity['source_outlet_str'][$key],
							$all_activity['dest_outlet_str'][$key],
							$all_activity['ordered'][$key],
							$all_activity['status'][$key],
							$all_activity['towards'][$key],
							array(
								'data' => '<div class="btn-group btn-group-xs">'.
								$ed_btn.
								anchor(base_url('inventory/freight/'.$all_activity['transfer_index'][$key]),'View','class="btn btn-xs btn-default"').
								'</div>'
								)
							);
	}
} else {	
	$this->table->add_row(array('data' => ':::No activity found:::','colspan' => 8,'align' => 'center'));
}
$inv_table = $this->table->generate();

?>
<div class="table-responsive">
    <div class="table-curved-div">
        <?php echo $inv_table ?>
    </div>
    <?php echo $links; ?>
</div>            
