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
<h4><i class="fa fa-desktop"></i> Quick Touch</h4>
<h6 class="hidden-print">
	*Add some products on your sell screen to touch and select products.
    Maximum of <?php echo $max_qt_headers ?> header groups, <?php echo $max_qt_products_per_page ?> products per page with <?php echo $max_qt_pages ?> paginations can be configured with your customised options.
    Dont forget to associate corresponding quicktouch to <?php echo anchor('setup/outlets_and_registers','outlet register','class="btn btn-xs btn-primary"') ?> once configured.
</h6>
<div class="well well-sm hidden-print">
	<?php echo anchor(base_url().'quicktouch/add','<i class="fa fa-plus fa-fw"></i>Add New Quick Touch','class = "btn btn-sm btn-primary" data-toggle="modal" data-target="#ajax_qt_modal"') ?>
</div>
<div class="modal fade" id="ajax_qt_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="table-responsive">
<?php
$daylight_saving = date("I");
$tmpl = array ( 'table_open'  => '<table class="table table-curved">' );
$this->table->set_template($tmpl);			
$heading = array("Quick Touch",'Updated At','');
$this->table->set_heading($heading);
foreach($quickeys['quick_index'] as $key => $value)
{
	$edit_delete = $quickeys['is_delete'][$key] == 10 ? '<div class="btn-group btn-group-sm">'.anchor('setup/quicktouch/edit/id/'.$quickeys['quick_index'][$key],'<i class="fa fa-pencil fa-fw"></i> Edit','class="btn btn-sm btn-success"').anchor('quicktouch/delete/id/'.$quickeys['quick_index'][$key],'<i class="fa fa-trash-o fa-fw"></i>Delete','class="btn btn-sm btn-danger" data-confirm="Delete This Quick Touch? This cant be restored..."').'</div>' : anchor('setup/quicktouch/edit/id/'.$quickeys['quick_index'][$key],'<i class="fa fa-pencil fa-fw"></i>Edit','class="btn btn-sm btn-success"');
	$this->table->add_row(
		$quickeys['quickey_name'][$key],
		unix_to_human(gmt_to_local(strtotime($quickeys['updated_at'][$key]),$timezone, $daylight_saving)),
		$edit_delete
		);
}
echo $this->table->generate().'<br>';
?>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Quick Touch assoiated registers</div>
    <div class="panel-body">
		<div class="table-responsive">
			<?php
            $heading = array("Register",'Outlet','Quicktouch','');
            $this->table->set_heading($heading);
            if(!empty($register_quickeys))
            {
                foreach($register_quickeys['reg_id'] as $key => $value)
                {
                    $this->table->add_row(
						$register_quickeys['reg_code'][$key],
						$register_quickeys['location'][$key],
						$register_quickeys['quickey_name'][$key],
						anchor('setup/register/'.$register_quickeys['reg_id'][$key].'/edit','<i class="fa fa-pencil fa-fw"></i>Edit','class="btn btn-sm btn-success"')
						);
                }
            } else {
                $this->table->add_row(array('data' => ':::No registers found for quicktouch:::','colspan' => 4,'align' => 'center'));	
            }
            echo $this->table->generate().'<br>';
            ?>        
        </div>
	</div>
</div>    