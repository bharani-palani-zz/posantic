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
<h4><i class="fa fa-edit fa-fw"></i> Update User</h4>
<hr>
<?php
if($this->session->userdata('is_primary') == 120 and $user_id != $this->session->userdata('user_id'))
{
	$delete =  anchor('users/delete/id/'.$user_id, '<i class="fa fa-trash-o fa-fw"></i> Delete '.$display_name, 'data-toggle="modal" data-target="#ajax_user_modal" class="btn btn-danger btn-xs" data-confirm="Delete '.$display_name.'? This cant be restored..."');	
} else if($this->session->userdata('privelage') < $privelage){
	$delete =  anchor('users/delete/id/'.$user_id, '<i class="fa fa-trash-o fa-fw"></i> Delete '.$display_name, 'data-toggle="modal" data-target="#ajax_user_modal" class="btn btn-danger btn-xs" data-confirm="Delete '.$display_name.'? This cant be restored..."');	
} else {
	$delete =  NULL;	
}
// user image
$ext = '';
$root = APPPATH.'user_images/'.md5($this->session->userdata('acc_no')).'/users/'.$user_id.'_thumb';
foreach (glob($root.".*") as $filename) {
	$ext = substr($filename,-3);
}
$image_href = $root.'.'.$ext;
if(file_exists($image_href))
{
	$image_href = '<img height="150" width="150" class="img-circle" src="'.base_url().$image_href.'?random='.time().'" />';
	$image_delete = anchor(base_url().'users/delete_image/'.$user_id,'<i class="fa fa-trash-o fa-fw"></i> Delete Image','class="btn btn-xs btn-danger"  data-confirm="Delete user image ???"');
} else {
	$image_href = '<div class="img-circle center-block" style="padding:10px; width:150px; background: #f15c58; color:#fff; height: 150px; line-height:165px;"><i class="fa fa-user fa-5x"></i></div>';								
	$image_delete = NULL;
}
?>
<!--Warning users have parked or lay by sales should not be deleted-->
<?php echo form_open_multipart(base_url().'users/update/id/'.$user_id,'id="user_form" size="5000"'); 
echo form_hidden('edit_user_id',$user_id);	
?>
<div class="modal fade" id="ajax_user_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-user fa-fw"></i> <?php echo $display_name ?>
        <span class="pull-right"><?php echo $delete ?></span>
	</div>
    <div class="panel-body">
        <div class="row">
        	<div class="col-lg-6 col-md-6">
				<?php if($this->session->userdata('user_id') != $user_id) { ?>
                    <div class="form-group">
                        <div class="input-group">
                            <label for="edit_emp_status" class="input-group-addon"><i class="fa fa-bullseye fa-fw"></i> Status</label>
                            <?php echo form_dropdown('edit_emp_status', $stat,$user_status,'class="form-control input-sm" id="edit_emp_status"')?>
                        </div>						
                    </div>
                <?php } else { echo form_hidden('edit_emp_status',$user_status); } ?>
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_emp_name" class="input-group-addon"><i class="fa fa-user fa-fw"></i> User name</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'edit_emp_name', 'id' => 'edit_emp_name', 'class' => 'form-control input-sm','value' => set_value('edit_emp_name',$user_name))) ?>
                    </div>
                    <?php if (form_error('edit_emp_name')) { ?><p class="col-xs-12"><div class="text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('edit_emp_name') ?></div></p><?php } ?>                    
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_disp_name" class="input-group-addon"><i class="fa fa-user fa-fw"></i> Display name</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'edit_disp_name', 'id' => 'edit_disp_name', 'class' => 'form-control input-sm','value' => set_value('edit_disp_name',$display_name))) ?>
                    </div>
                    <?php if (form_error('edit_disp_name')) { ?><p class="col-xs-12"><div class="text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('edit_disp_name') ?></div></p><?php } ?>                    
                </div>                    
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_pass" class="input-group-addon"><i class="fa fa-lock fa-fw"></i> Password</label>
                        <?php echo form_password(array('autocomplete' => 'off', 'name' => 'edit_pass', 'id' => 'edit_pass', 'class' => 'form-control input-sm', 'value' => set_value('edit_pass')))?>
                    </div>
                    <?php if (form_error('edit_pass')) { ?><p class="col-xs-12"><div class="text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('edit_pass') ?></div></p><?php } ?>                    
                </div>                    
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_type_pass" class="input-group-addon"><i class="fa fa-key fa-fw"></i> Passwor again</label>
                        <?php echo form_password(array('autocomplete' => 'off', 'name' => 'edit_type_pass', 'id' => 'edit_type_pass','class' => 'form-control input-sm','value' => set_value('edit_type_pass'))) ?>
                    </div>
                    <?php if (form_error('edit_type_pass')) { ?><p class="col-xs-12"><div class="text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('edit_type_pass') ?></div></p><?php } ?>                    
                </div>                    
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_email" class="input-group-addon"><i class="fa fa-envelope fa-fw"></i> Email</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'size' => 40, 'name' => 'edit_email', 'id' => 'edit_email','class' => 'form-control input-sm','value' => set_value('edit_email',$user_mail))) ?>
                    </div>
                    <?php if (form_error('edit_email')) { ?><p class="col-xs-12"><div class="text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('edit_email') ?></div></p><?php } ?>                    
                </div>                    
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_emp_mobile" class="input-group-addon"><i class="fa fa-phone fa-fw"></i> Mobile</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'edit_emp_mobile', 'id' => 'edit_emp_mobile','class' => 'form-control input-sm','value' => set_value('edit_emp_mobile',$user_mobile))) ?>
                    </div>
                    <?php if (form_error('edit_emp_mobile')) { ?><p class="col-xs-12"><div class="text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('edit_emp_mobile') ?></div></p><?php } ?>
                </div>                    
                <?php if($this->session->userdata('privelage') == 1 and $user_id != $this->session->userdata('user_id')) { ?>
                    <div class="form-group">
                        <div class="input-group">
                            <label for="edit_emp_outlet" class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i> For Outlet</label>
                            <?php echo form_dropdown('edit_emp_outlet',$company,$location,'class="form-control input-sm" id="edit_emp_outlet"') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <label for="edit_emp_role" class="input-group-addon"><i class="fa fa-heart fa-fw"></i> Select role</label>
                            <?php echo form_dropdown('edit_emp_role', $roles,$privelage,'class="form-control input-sm" id="edit_emp_role"') ?>
                        </div>
                    </div>
                <?php } else if($this->session->userdata('privelage') < $privelage) { ?>
                    <div class="form-group">
                        <div class="input-group">
                            <label for="edit_emp_outlet" class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i> For Outlet</label>
                            <?php echo form_dropdown('edit_emp_outlet',$company,$location,'class="form-control input-sm" id="edit_emp_outlet"') ?>
                        </div>
                    </div>                        
                    <div class="form-group">
                        <div class="input-group">
                            <label for="edit_emp_role" class="input-group-addon"><i class="fa fa-heart fa-fw"></i> Select role</label>
                            <?php echo form_dropdown('edit_emp_role', $roles,$privelage,'class="form-control input-sm" id="edit_emp_role"') ?>
                        </div>
                    </div>                        
                <?php } else { 
                    echo form_hidden('edit_emp_outlet',$location);	
                    echo form_hidden('edit_emp_role',$privelage);		
                 }
                 ?>
            </div>
        	<div class="col-lg-6 col-md-6">
                <div class="jumbotron">
                	<div class="row">
                        <div class="col-lg-6 text-center">
                            <h3>
                                <i class="fa  fa-file-image-o fw"></i> Upload Image
                            </h3>
                            <h4>
                                <label class="btn btn-primary" for="my-file-selector">
                                    <?php echo form_upload(array('name' => 'userfile', 'id' => 'my-file-selector', 'style' => 'display:none;')) ?>
                                    Choose file...
                                </label>                    
                            </h4>
                            <h5>Maximum file size - 2Mb</h5> 
                            <h5>File Types - gif|jpeg|jpg|png </h5>
                        </div>
                        <div class="col-lg-6 text-center">
                            <div class="form-group">
	                        	<?php echo $image_href ?>
                            </div>
                            <div class="form-group">
	                        	<?php echo $image_delete ?>                            
                            </div>
                        </div>         
                    </div>           	
                </div>
            </div>            
        </div>                                             
	</div>  
    <div class="panel-footer">
	    <button type="submit" name="update_emp" class="btn btn-success"><i class="fa fa-user fa-fw"></i>Update</button>
		<?php echo anchor('users','<i class="fa fa-times fa-fw"></i>Cancel', 'class = "btn btn-danger"'); ?>
	</div>
</div>
<?php
echo form_close();
?>