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
<h4><span class="glyphicon glyphicon-map-marker"></span> Edit Outlet</h4>
<hr>
<?php
echo form_open(base_url().'setup/outlet/update/id/'.$id);
?>
<div class="panel panel-default">
    <div class="panel-heading">Outlet Details</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_loc_str" class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i> Outlet Name</label>
                        <?php echo form_input(array('value' => set_value('edit_loc_str',$loc_str), 'autocomplete' => 'off', 'name' => 'edit_loc_str', 'id' => 'edit_loc_str', 'class' => 'form-control input-sm', 'placeholder' => 'Max 25 Characters'))?>
                    </div>
                    <?php if(form_error('edit_loc_str')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('edit_loc_str') ?></p><?php } ?>
                </div>	    
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_outlet_tax" class="input-group-addon"><i class="fa fa-bookmark fa-fw"></i> Outlet Tax</label>
                        <?php
                        echo form_dropdown('edit_outlet_tax',$get_single_group_taxes_combo,$outlet_tax,'id="edit_outlet_tax" class="form-control input-sm"')
                        ?>
                    </div>
                </div>                                        
            </div>
        </div>	
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_l1" class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i> Address 1</label>
                        <?php
                        echo form_input(array('value' => $l1, 'autocomplete' => 'off', 'name' => 'edit_l1', 'id' => 'edit_l1', 'class' => 'form-control input-sm', 'placeholder' => 'Max 50 Characters'))
                        ?>
    
                    </div>
                </div>
    
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_l2" class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i> Address 2</label>
                        <?php
                        echo form_input(array('value' => $l2, 'autocomplete' => 'off', 'name' => 'edit_l2', 'id' => 'edit_l2', 'class' => 'form-control input-sm', 'placeholder' => 'Max 50 Characters'))
                        ?>
                    </div>
                </div>
    
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_city" class="input-group-addon"><i class="fa fa-home fa-fw"></i> City</label>
                        <?php
                        echo form_input(array('value' => $city,'autocomplete' => 'off', 'name' => 'edit_city', 'id' => 'edit_city', 'class' => 'form-control input-sm', 'placeholder' => 'Max 20 Characters'))
                        ?>
                    </div>
                </div>
    
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_state" class="input-group-addon"><i class="fa fa-road fa-fw"></i> State</label>
                        <?php
                        echo form_input(array('value' => $state,'autocomplete' => 'off', 'name' => 'edit_state', 'id' => 'edit_state', 'class' => 'form-control input-sm', 'placeholder' => 'Max 20 Characters'))
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_pcode" class="input-group-addon"><i class="fa fa-code fa-fw"></i> Pincode</label>
                        <?php
                        echo form_input(array('value' => $pcode,'autocomplete' => 'off', 'name' => 'edit_pcode', 'id' => 'edit_pcode', 'class' => 'form-control input-sm', 'placeholder' => 'Max 10 numbers'))
                        ?>
                    </div>
                </div>
    
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_country" class="input-group-addon"><i class="fa fa-globe fa-fw"></i> Country</label>
                        <?php
                        echo form_dropdown('edit_country',$get_countries_select,$country,'id="edit_country" class="form-control input-sm"')
                        ?>
                    </div>
                </div>
    
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_ll" class="input-group-addon"><span class="glyphicon glyphicon-phone-alt"></span> Phone</label>
                        <?php
                        echo form_input(array('value' => $ll,'autocomplete' => 'off', 'name' => 'edit_ll', 'id' => 'edit_ll', 'class' => 'form-control input-sm', 'placeholder' => 'Max 15 Characters'))
                        ?>
                    </div>
                </div>
    
                <div class="form-group">
                    <div class="input-group">
                        <label for="edit_email" class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span> Email</label>
                        <?php
                        echo form_input(array('value' => $email,'autocomplete' => 'off', 'name' => 'edit_email', 'id' => 'edit_email', 'class' => 'form-control input-sm', 'placeholder' => 'Max 20 Characters'))
                        ?>
                    </div>
                </div>
            </div>
        </div>                
    </div>
    <div class="panel-footer">
        <div class="row">
            <p class="col-lg-12">
			    <span class="glyphicon glyphicon-map-marker"></span> Your outlet locality detail is mandatory for users to map ecommerce stores online
            </p>
        </div>
        <div class="row">
            <div class="col-lg-12">
            <button type="submit" class="btn btn-success btn-md" name="update_outlet" id="update_outlet">
              <i class="fa fa-shopping-cart"></i> Update Outlet
            </button>    
            <?php echo anchor('setup/outlets_and_registers','<span class="glyphicon glyphicon-remove"></span> Cancel', 'class = "btn btn-danger btn-md"')  ?>  
            </div>
        </div>
    </div>    
</div>        
    
<?php
echo form_close();
?>