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
<h4><i class="fa fa-users fa-fw"></i> Customers</h4>
<div class="well well-sm hidden-print">
	<div class="btn-group btn-group-sm">
		<?php echo anchor('customers/add','<i class="fa fa-plus-circle fa-fw"></i> Add Customer','class = "btn btn-sm btn-primary"')?>
        <?php echo anchor('customers/import','<i class="fa fa-upload fa-fw"></i> Import','class = "btn btn-sm btn-primary" data-toggle="popover" data-placement="top" data-content="Import Bulk Customers"') ?>
        <?php echo anchor('customers/download','<i class="fa fa-download fa-fw"></i> Export','class = "btn btn-sm btn-primary" data-toggle="popover" data-placement="top" data-content="Export '.$this->session->userdata('cmp_name').' customers"') ?>
	</div>	
</div>
<?php echo form_open(base_url().'customers/lookup',array('method' => 'get')) ?>
<div class="panel panel-default">
    <div class="panel-heading">Filter Customers</div>
    <div class="panel-body">
		<div class="row">
			<div class="col-md-6">
	            <div class="form-group">
				<?php echo form_input(array('autocomplete' => 'off', 'value' => isset($_GET['search_customer']) ? $_GET['search_customer'] : '', 'placeholder' => 'Search by Name / Code / Mobile','id' => 'search_customer','name' => 'search_customer',"class" => "form-control")) ?>
				</div>
    		</div>
        </div>
        <div class="effect">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="cust_group" class="input-group-addon">Customer Group</label>
                            <?php echo form_dropdown('cust_group', $group_combo, isset($_GET['cust_group']) ? $_GET['cust_group'] : '','class="form-control input-sm" id="cust_group"') ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="dob_date" class="input-group-addon">Birth</label>
                            <?php echo form_input(array('autocomplete' => 'off','value' => isset($_GET['dob_date']) ? $_GET['dob_date'] : '','name' => 'dob_date','id' => 'dob_date','class' => 'form-control input-sm')) ?>
                        </div>
                    </div>	
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="ann_date" class="input-group-addon">Anniversary</label>
                            <?php echo form_input(array('autocomplete' => 'off','value' => isset($_GET['ann_date']) ? $_GET['ann_date'] : '','name' => 'ann_date','id' => 'ann_date','class' => 'form-control input-sm')) ?>
                        </div>
                    </div>
                </div>                                                    
            </div>
    
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="date_after" class="input-group-addon">Modified On/After</label>
                            <?php echo form_input(array('autocomplete' => 'off','value' => isset($_GET['date_after']) ? $_GET['date_after'] : '', 'name' => 'date_after','id' => 'date_after','class' => 'form-control input-sm')) ?>	
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="date_before" class="input-group-addon">Modified On/before</label>
                            <?php echo form_input(array('autocomplete' => 'off','value' => isset($_GET['date_before']) ? $_GET['date_before'] : '', 'name' => 'date_before','id' => 'date_before','class' => 'form-control input-sm')) ?>	
                        </div>
                    </div>
                </div>                                    
            </div>                                                
		</div>
        <div class="row">
	        <div class="col-md-12">
                <div class="btn-group">
                <button type="submit" class="btn btn-success btn-sm">Search</button> 
                <button type="button" id="filter_button" class="btn btn-danger btn-sm"><i class="fa fa-filter"></i> Filter Options</button>
                </div>
            </div>
        </div>

	</div>
</div>   
<?php echo form_close() ?>
 
<?php
if(!empty($_GET['dob_date']) || !empty($_GET['ann_date']) || !empty($_GET['date_after']) || !empty($_GET['date_before']) || !empty($_GET['search_customer']) || !empty($_GET['cust_group'])) {
	echo '<input type="hidden" id="toggle_filter" value="1">';
} else {
	echo '<input type="hidden" id="toggle_filter" value="0">';	
}
?>
<div class="panel panel-default">
    <div class="panel-heading">Current customers</div>
    <div class="panel-body">
<?php
$header_keys = array("cust_name","cust_code","cust_comp","cust_dob","cust_ann","cust_group","cust_city","cust_trade","cust_credit");
$filter_array = array('search_customer','cust_group','dob_date','ann_date','date_after','date_before');
if(isset($_GET['search_customer']) || isset($_GET['cust_group']) || isset($_GET['dob_date']) || isset($_GET['ann_date']) || isset($_GET['date_after']) || isset($_GET['date_before']))
{
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
						$get_array[$h_values.'_arrow'] = '<span class="pull-right"><i class="fa fa-caret-up"></span>';
					} else {
						$get_array[$h_values.'_flow'] = "&flow=asc";
						$get_array[$h_values.'_arrow'] = '<span class="pull-right"><i class="fa fa-caret-down"></span>';
					}
				} else {
					$get_array[$h_values.'_flow'] = "&flow=asc";
					$get_array[$h_values.'_arrow'] = '<span class="pull-right"><i class="fa fa-caret-down"></span>';
				}
				$unset_array = array_diff($header_keys,array($h_values));
				foreach($unset_array as $u_values)
				{
					$get_array[$u_values.'_flow'] = "";
					$get_array[$u_values.'_arrow'] = '<span class="pull-right"><i class="fa fa-sort"></span>';
				}		
			break;
		}
	}
} else {
	foreach($header_keys as $h_values)
	{
		$get_array[$h_values.'_flow'] = "";
		$get_array[$h_values.'_arrow'] = '<span class="pull-right"><i class="fa fa-sort"></span>';
	}
}

$tmpl = array (
	'table_open'   => '<table class="table table-striped table-curved" id="customers_table">',
);
$this->table->set_template($tmpl);			
$heading = array(
				anchor('customers/lookup?sort=cust_name'.$get_array['cust_name_flow'].$get_filter,'Customer '.$get_array['cust_name_arrow']),
				anchor('customers/lookup?sort=cust_code'.$get_array['cust_code_flow'].$get_filter,'Customer Code '.$get_array['cust_code_arrow']),
				anchor('customers/lookup?sort=cust_comp'.$get_array['cust_comp_flow'].$get_filter,'Company '.$get_array['cust_comp_arrow']),
				anchor('customers/lookup?sort=cust_dob'.$get_array['cust_dob_flow'].$get_filter,'Birth '.$get_array['cust_dob_arrow']),
				anchor('customers/lookup?sort=cust_ann'.$get_array['cust_ann_flow'].$get_filter,'Anniversary '.$get_array['cust_ann_arrow']),
				anchor('customers/lookup?sort=cust_group'.$get_array['cust_group_flow'].$get_filter,'Group '.$get_array['cust_group_arrow']),
				anchor('customers/lookup?sort=cust_city'.$get_array['cust_city_flow'].$get_filter,'City '.$get_array['cust_city_arrow']),
				anchor('customers/lookup?sort=cust_trade'.$get_array['cust_trade_flow'].$get_filter,'Trade '.$get_array['cust_trade_arrow']),
				anchor('customers/lookup?sort=cust_credit'.$get_array['cust_credit_flow'].$get_filter,'Credit '.$get_array['cust_credit_arrow']),
				array('data' => '','class' => 'no-print')
				);
$this->table->set_heading($heading);

if(isset($results['cust_id']))
{	
	foreach($results['cust_id'] as $key => $cust_id)
	{
		$this->table->add_row(
						anchor('customers/'.$results['cust_id'][$key],$results['cust_name'][$key],'class="btn btn-xs btn-link"'),
						$results['cust_code'][$key],
						$results['cust_comp'][$key],
						$results['cust_dob'][$key],
						$results['cust_ann'][$key],
						$results['cust_group'][$key],
						$results['cust_city'][$key],
						$results['cust_trade'][$key],
						$results['cust_credit'][$key],
						array('data' => anchor('customers/edit/id/'.$results['cust_id'][$key],'<i class="fa fa-edit"></i> Edit','class = "btn btn-success btn-xs"'),'align' => 'center')
						);
	}
} else {
	$this->table->add_row('--','--','--','--','--','--','--','--','--','--');
}
echo $this->table->generate().'<br>';
?>
	</div>
    <div class="panel-footer">
		<span class="badge">
        	<?php
			$plural = $tot_prd_count > 1 ? 's' : '';
            echo 'Showing '.$page_prd_count.' of '.$tot_prd_count.' row'.$plural;
			?>
        </span>
	</div>        
</div>
<?php
echo $links;
?>