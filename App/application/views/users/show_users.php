<?php
echo '<input type="hidden" name="'.$this->security->get_csrf_token_name().'" value="'.$this->security->get_csrf_hash().'" />';
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
	'table_open'   => '<table class="table table-striped table-curved" id="user_table">'
);
$this->table->set_template($tmpl);			
$heading = array(
			'User',
			'Mobile',
			'For Outlet',
			'Status',
			'Daily Target',
			'Weekly Target',
			'Monthly Target',
			'Privilege',
			'Email',
			'Last Login'
			);
$this->table->set_heading($heading);
$j=0;
foreach($users['rows'] as $row){
	$ext = '';
	$root = APPPATH.'user_images/'.md5($this->session->userdata('acc_no')).'/users/'.$row[0].'_thumb';
	foreach (glob($root.".*") as $filename) {
		$ext = substr($filename,-3);
	}
	$image_href = $root.'.'.$ext;
	if(file_exists($image_href))
	{
		$image_href = '<img height="50" width="50" class="img-circle" src="'.base_url().$image_href.'?random='.time().'" />';
	} else {
		$image_href = '<i class="fa fa-user fa-3x"></i>';								
	}
	
	$this->table->add_row(
		'<div class="text-center">'.$image_href.'<br>'.anchor(base_url().'users/'.$row[0],$row[1],'class="label label-danger"').'</div>',
		$row[10],$row[2],$row[3],
		array('data' => 
			'<div class="row"><div class="col-lg-12"><div class="input-group">
				<span class="input-group-addon">'.$users['static']['symbol'].'</span>'.
				form_input(array('style' => 'z-index:0','name' => $j.'set_day','id' => $j.'set_day','autocomplete' => 'off', 'size' => 5, 'data-id' => $row[0], 'target-id' => 'set_day', 'class' => 'form-control input-sm target_TB set_target','value' => $row[4])).
				'<span class="t_load input-group-addon"></span>
			</div></div></div>'
			),
		array('data' => 
			'<div class="row"><div class="col-lg-12"><div class="input-group">
				<span class="input-group-addon">'.$users['static']['symbol'].'</span>'.
				form_input(array('style' => 'z-index:0','name' => $j.'set_week','id' => $j.'set_week','autocomplete' => 'off', 'size' => 5, 'data-id' => $row[0], 'target-id' => 'set_week', 'class' => 'form-control input-sm target_TB set_target','value' => $row[5])).
				'<span class="t_load input-group-addon"></span>
			</div></div></div>'
			),
		array('data' => 
			'<div class="row"><div class="col-lg-12"><div class="input-group">
				<span class="input-group-addon">'.$users['static']['symbol'].'</span>'.
				form_input(array('style' => 'z-index:0','name' => $j.'set_month','id' => $j.'set_month','autocomplete' => 'off', 'size' => 5, 'data-id' => $row[0], 'target-id' => 'set_month', 'class' => 'form-control input-sm target_TB set_target','value' => $row[6])).
				'<span class="t_load input-group-addon"></span>
			</div></div></div>'
			),
		$row[7],$row[8],$row[9]
	);
	$j++;
}
$user_tbl = $this->table->generate();

 
?>
<h4><i class="fa fa-users fa-fw"></i> Users</h4>
<hr>
<input type="hidden" id="user_url" data-url="<?php echo base_url('users/save_target') ?>">

<div class="well well-sm">
    <?php echo anchor(base_url().'users/add','<i class="fa fa-plus fa-fw"></i>Add New User','class = "btn btn-primary btn-sm"') ?>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        Current users
	</div>
    <div class="panel-body">
	        <?php echo $user_tbl ?>
	</div>            
    <div class="panel-footer">
		<small><i class="fa fa-hand-o-right fa-fw"></i> Set substantial targets to fortify user sales</small>
	</div>
</div>