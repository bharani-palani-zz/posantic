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
<h4><i class="fa fa-user fa-fw"></i> Add User</h4>
<hr>
<?php echo form_open_multipart(base_url().'users/insert_form_user','id="user_form" size="5000"'); ?>
<div class="panel panel-default">
    <div class="panel-heading">
        User Details
	</div>
    <div class="panel-body">
    	<div class="row">
        	<div class="col-lg-6 col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <label for="new_emp_name" class="input-group-addon"><i class="fa fa-user fa-fw"></i> User name</label>
                        <?php echo form_input(array('value' => set_value('new_emp_name'),'autocomplete' => 'off', 'name' => 'new_emp_name', 'id' => 'new_emp_name','class' => 'form-control input-sm','placeholder' => 'Max 25 characters'))?>
                    </div>
                    <?php if (form_error('new_emp_name')) { ?><p class="col-xs-12"><div class="text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('new_emp_name') ?></div></p><?php } ?>                    
                </div>                    
                <div class="form-group">
                    <div class="input-group">
                        <label for="new_disp_name" class="input-group-addon"><i class="fa fa-power-off fa-fw"></i> Display name</label>
                        <?php echo form_input(array('value' => set_value('new_disp_name'),'autocomplete' => 'off', 'name' => 'new_disp_name', 'id' => 'new_disp_name','class' => 'form-control input-sm','placeholder' => 'Max 25 characters'))?>
                    </div>
                    <?php if (form_error('new_disp_name')) { ?><p class="col-xs-12"><div class="text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('new_disp_name') ?></div></p><?php } ?>                    
                </div>                    						
                <div class="form-group">
                    <div class="input-group">
                        <label for="new_emp_pass" class="input-group-addon"><i class="fa fa-lock fa-fw"></i> Password</label>
                        <?php echo form_password(array('value' => set_value('new_emp_pass'),'autocomplete' => 'off', 'name' => 'new_emp_pass', 'id' => 'new_emp_pass','class' => 'form-control input-sm','placeholder' => 'Min 8 characters'))?>
                    </div>
                    <?php if (form_error('new_emp_pass')) { ?><p class="col-xs-12"><div class="text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('new_emp_pass') ?></div></p><?php } ?>                    
                </div>                        
                <div class="form-group">
                    <div class="input-group">
                        <label for="new_emp_check_pass" class="input-group-addon"><i class="fa fa-key fa-fw"></i> Password again</label>
                        <?php echo form_password(array('value' => set_value('new_emp_check_pass'),'autocomplete' => 'off', 'name' => 'new_emp_check_pass', 'id' => 'new_emp_check_pass','class' => 'form-control input-sm','placeholder' => 'Min 8 characters')) ?>
                    </div>
                    <?php if (form_error('new_emp_check_pass')) { ?><p class="col-xs-12"><div class="text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('new_emp_check_pass') ?></div></p><?php } ?>                    
                </div>                   	
                <div class="form-group">
                    <div class="input-group">
                        <label for="email" class="input-group-addon"><i class="fa fa-envelope fa-fw"></i> Email</label>
                        <?php echo form_input(array('value' => set_value('email'),'autocomplete' => 'off', 'size' => 40, 'name' => 'email', 'id' => 'email','class' => 'form-control input-sm','placeholder' => 'Currently active email')) ?>
                    </div>
                    <?php if (form_error('email')) { ?><p class="col-xs-12"><div class="text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('email') ?></div></p><?php } ?>                    
                </div>                    
                <div class="form-group">
                    <div class="input-group">
                        <label for="emp_mobile" class="input-group-addon"><i class="fa fa-phone fa-fw"></i> Mobile</label>
                        <?php echo form_input(array('value' => set_value('emp_mobile'),'autocomplete' => 'off', 'name' => 'emp_mobile', 'id' => 'emp_mobile','class' => 'form-control input-sm','placeholder' => '10 Digit Mobile Number')) ?>
                    </div>
                    <?php if (form_error('emp_mobile')) { ?><p class="col-xs-12"><div class="text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('emp_mobile') ?></div></p><?php } ?>
                </div>                    
                <div class="form-group">
                    <div class="input-group">
                        <label for="company" class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i> For Outlet</label>
                        <?php echo form_dropdown('company', $company,set_value('company'),'id="company" class="form-control input-sm"') ?>
                    </div>
                </div>                    
                <div class="form-group">
                    <div class="input-group">
                        <label for="priv" class="input-group-addon"><i class="fa fa-heart fa-fw"></i> Select role</label>
                        <?php echo form_dropdown('role', $desig,set_value('role'),'id="priv" class="form-control input-sm"') ?>
                    </div>
                </div>                    
        	</div>
        	<div class="col-lg-6 col-md-6 text-center">
                <div class="jumbotron">
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
            </div>
		</div>            
	</div>            
    <div class="panel-footer">
    	<button type="submit" name="insert_emp" id="insert_emp" class="btn btn-success">
        	<i class="fa fa-user"></i> Add
        </button>
		<?php echo anchor('users','<i class="fa fa-remove"></i> Cancel', 'class = "btn btn-danger"'); ?>
	</div>
</div>
<?php echo form_close() ?>
