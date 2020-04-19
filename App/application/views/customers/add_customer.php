<h4><i class="fa fa-user fa-fw"></i> Add Customer</h4>
<hr>
<?php
echo form_open(base_url().'customers/insert',array('id' => 'form_add_cust'));
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-plus fa-fw"></i> Customer details
	</div>
    <div class="panel-body">
		<div class="row">
        	<div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <label for="cust_name" class="input-group-addon"><i class="fa fa-user fa-fw"></i></label>
	                    <?php echo form_input(array('autocomplete' => 'off', 'placeholder' => '25 Characters', 'name' => 'cust_name', 'id' => 'cust_name',"class" => "form-control input-sm", "value" => set_value('cust_name'))) ?>
                        <label for="cust_code" class="input-group-addon"><span class="fa fa-paperclip fa-fw"></span></label>
						<?php echo form_input(array('data-toggle' => "popover", 'data-placement' => "top", 'data-title' => 'Tip:','data-content' => "Leave customer code field blank, so we can create one for you", 'autocomplete' => 'off', 'placeholder' => 'Customer Code', 'name' => 'cust_code', 'id' => 'cust_code',"class" => "form-control input-sm", "value" => set_value('cust_code'))) ?>
                    </div>
                    <?php if (form_error('cust_name')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('cust_name') ?></div></p><?php } ?>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <label for="cust_group" class="input-group-addon"><i class="fa fa-link fa-fw"></i> Customer Group</label>
						<?php echo form_dropdown('cust_group', $group_combo, '','id="cust_group" class="form-control input-sm"')?>
					</div>
				</div>                    
            </div>
        	<div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <label for="comp_name" class="input-group-addon"><i class="fa fa-location-arrow fa-fw"></i> Company</label>
	                    <?php echo form_input(array('autocomplete' => 'off', 'placeholder' => '25 Characters', 'name' => 'comp_name', 'id' => 'comp_name',"class" => "form-control input-sm", "value" => set_value('comp_name'))) ?>
					</div>
                    <?php if (form_error('comp_name')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('comp_name') ?></div></p><?php } ?>
				</div>                                
                <div class="form-group">
                    <div class="input-group">
						<big><i class="fa fa-male fa-fw"></i></big> <?php echo form_radio(array('data-label-text' => 'Male','data-size' => 'small','name' => 'cust_gender', 'id' => 'cust_gender_male', 'checked' => true , 'value' => 'M')) ?>
						&nbsp;
                        <big><i class="fa fa-female fa-fw"></i></big> <?php echo form_radio(array('data-label-text' => 'Female','data-size' => 'small','name' => 'cust_gender', 'id' => 'cust_gender_female', 'checked' => false , 'value' => 'F')) ?>
                    </div>
				</div>                    				            
            </div>            
        </div>
        <div class="row">
	        <div class="col-md-6">
                <h5><i class="fa fa-calendar fa-fw"></i> Birth</h5>
                <div class="form-group">
                    <div class="input-group">
                        <label for="cust_dob[dd]" class="input-group-addon">Day</label>
	                    <?php echo form_input(array('autocomplete' => 'off','placeholder' => 'dd', 'name' => 'cust_dob[dd]', 'id' => 'cust_dob[dd]',"class" => "form-control input-sm", "value" => set_value('cust_dob[dd]'))) ?>
                        <label for="cust_dob[mm]" class="input-group-addon">Month</label>
						<?php echo form_input(array('autocomplete' => 'off','placeholder' => 'mm', 'name' => 'cust_dob[mm]', 'id' => 'cust_dob[mm]',"class" => "form-control input-sm", "value" => set_value('cust_dob[mm]'))) ?>
                        <label for="cust_dob[yy]" class="input-group-addon">Year</label>
						<?php echo form_input(array('autocomplete' => 'off','placeholder' => 'yyyy', 'name' => 'cust_dob[yy]', 'id' => 'cust_dob[yy]',"class" => "form-control input-sm", "value" => set_value('cust_dob[yy]'))) ?>
                    </div>
                    <?php if (form_error('cust_dob')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('cust_dob') ?></div></p><?php } ?>
                </div>    			
    		</div>    
            <div class="col-md-6">
                <h5><i class="fa fa-calendar fa-fw"></i> Anniversary</h5>
                <div class="form-group">
                    <div class="input-group">
                        <label for="cust_ann[dd]" class="input-group-addon">Day</label>
	                    <?php echo form_input(array('autocomplete' => 'off','placeholder' => 'dd', 'name' => 'cust_ann[dd]', 'id' => 'cust_ann[dd]',"class" => "form-control input-sm", "value" => set_value('cust_ann[dd]'))) ?>
                        <label for="cust_ann[mm]" class="input-group-addon">Month</label>
						<?php echo form_input(array('autocomplete' => 'off','placeholder' => 'mm', 'name' => 'cust_ann[mm]', 'id' => 'cust_ann[mm]',"class" => "form-control input-sm", "value" => set_value('cust_ann[mm]'))) ?>
                        <label for="cust_ann[yy]" class="input-group-addon">Year</label>
						<?php echo form_input(array('autocomplete' => 'off','placeholder' => 'yyyy', 'name' => 'cust_ann[yy]', 'id' => 'cust_ann[yy]',"class" => "form-control input-sm", "value" => set_value('cust_ann[yy]'))) ?>
                    </div>
                    <?php if (form_error('cust_ann')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('cust_ann') ?></div></p><?php } ?>
                </div>            
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Contact Information
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <label for="cust_mobile" class="input-group-addon"><span class="glyphicon glyphicon-phone"></span> Mobile</label>
                                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'cust_mobile', 'id' => 'cust_mobile',"class" => "form-control input-sm", "value" => set_value('cust_mobile'))) ?>
                                    </div>
                                    <?php if (form_error('cust_mobile')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('cust_mobile') ?></div></p><?php } ?>
                                </div>                                
                                <div class="form-group">
                                    <div class="input-group">
                                        <label for="cust_ll" class="input-group-addon"><span class="glyphicon glyphicon-phone-alt"></span> Land line</label>
                                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'cust_ll', 'id' => 'cust_ll',"class" => "form-control input-sm", "value" => set_value('cust_ll'))) ?>
                                    </div>
                                    <?php if (form_error('cust_ll')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('cust_ll') ?></div></p><?php } ?>
                                </div>                                
                                <div class="form-group">
                                    <div class="input-group">
                                        <label for="cust_addrr_1" class="input-group-addon"><span class="glyphicon glyphicon-map-marker"></span> Address 1</label>
                                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'cust_addrr_1', 'id' => 'cust_addrr_1',"class" => "form-control input-sm", "value" => set_value('cust_addrr_1'))) ?>
                                    </div>
                                    <?php if (form_error('cust_addrr_1')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('cust_addrr_1') ?></div></p><?php } ?>
                                </div>                                
                                <div class="form-group">
                                    <div class="input-group">
                                        <label for="cust_addrr_2" class="input-group-addon"><span class="glyphicon glyphicon-map-marker"></span> Address 2</label>
                                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'cust_addrr_2', 'id' => 'cust_addrr_2',"class" => "form-control input-sm", "value" => set_value('cust_addrr_2'))) ?>
                                    </div>
                                    <?php if (form_error('cust_addrr_2')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('cust_addrr_2') ?></div></p><?php } ?>
                                </div>                                
                                <div class="form-group">
                                    <div class="input-group">
                                        <label for="cust_city" class="input-group-addon"><span class="glyphicon glyphicon-home"></span> City</label>
                                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'cust_city', 'id' => 'cust_city',"class" => "form-control input-sm", "value" => set_value('cust_city'))) ?>
                                    </div>
                                    <?php if (form_error('cust_city')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('cust_city') ?></div></p><?php } ?>
                                </div>                                
                                <div class="form-group">
                                    <div class="input-group">
                                        <label for="cust_state" class="input-group-addon"><span class="glyphicon glyphicon-road"></span> State</label>
                                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'cust_state', 'id' => 'cust_state',"class" => "form-control input-sm", "value" => set_value('cust_state'))) ?>
                                    </div>
                                    <?php if (form_error('cust_state')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('cust_state') ?></div></p><?php } ?>
                                </div>                                
							</div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <label for="cust_pcode" class="input-group-addon"><i class="fa fa-code fa-fw"></i> Postal code</label>
                                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'cust_pcode', 'id' => 'cust_pcode',"class" => "form-control input-sm", "value" => set_value('cust_pcode'))) ?>
                                    </div>
                                    <?php if (form_error('cust_pcode')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('cust_pcode') ?></div></p><?php } ?>
                                </div>                                
                                <div class="form-group">
                                    <div class="input-group">
                                        <label for="cust_country" class="input-group-addon"><i class="fa fa-globe fa-fw"></i> Country</label>
                                        <?php echo form_dropdown('cust_country', $country_dropdown, $master_data[11],'id="cust_country" class="form-control input-sm"') ?>
                                    </div>
                                </div>                                
                                <div class="form-group">
                                    <div class="input-group">
                                        <label for="cust_email" class="input-group-addon"><i class="fa fa-envelope fa-fw"></i> Email</label>
                                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'cust_email', 'id' => 'cust_email',"class" => "form-control input-sm", "value" => set_value('cust_email'))) ?>
                                    </div>
                                    <?php if (form_error('cust_email')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('cust_email') ?></div></p><?php } ?>
                                </div>                                
                                <div class="form-group">
                                    <div class="input-group">
                                        <label for="cust_web" class="input-group-addon"><i class="fa fa-cloud fa-fw"></i> Website</label>                                    	
                                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'cust_web', 'id' => 'cust_web',"class" => "form-control input-sm", "value" => set_value('cust_web'))) ?>
                                    </div>
                                    <?php if (form_error('comp_web')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('comp_web') ?></div></p><?php } ?>
                                </div>                                
                                <div class="form-group">
                                    <div class="input-group">
                                        <label for="cust_fb" class="input-group-addon"><i class="fa fa-facebook fa-fw"></i> Facebook Id</label>
                                        <?php echo form_input(array('autocomplete' => 'off', 'size' => 50, 'name' => 'cust_fb', 'id' => 'cust_fb',"class" => "form-control input-sm", "value" => set_value('cust_fb'))) ?>
                                    </div>
                                    <?php if (form_error('cust_fb')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('cust_fb') ?></div></p><?php } ?>
                                </div>                                
                                <div class="form-group">
                                    <div class="input-group">
										<?php echo form_radio(array('data-on-color' => 'success', 'data-on-text' => 'Enabled', 'data-off-text' => 'Enable','data-label-text' => 'Loyalty','data-size' => 'small','name' => 'cust_enable_loyalty', 'id' => 'cust_loyalty_true', 'checked' => true , 'value' => 30)) ?>
                                        &nbsp
                                        <?php echo form_radio(array('data-on-color' => 'danger', 'data-on-text' => 'Disabled', 'data-off-text' => 'Disable', 'data-label-text' => 'Loyalty','data-size' => 'small','name' => 'cust_enable_loyalty', 'id' => 'cust_loyalty_false', 'checked' => false , 'value' => 40)) ?>
                                    </div>
                                </div>                    				            

							</div>
						</div>                            
                        <div class="row">
                            <div class="col-md-12">
                            <h4>Customer Description</h4>
							<?php
							echo form_textarea(array(
										  'name'        => 'cust_desc',
										  'id'          => 'cust_desc',
										  'class' 		=> 'smallborder_orange',
										  'value'       => set_value('cust_desc'),
										));
							
							?>
							</div>
						</div>                            
                    </div>
                    
				</div>                    
            </div>
        </div>                
        
	</div>
    <div class="panel-footer">
		<?php echo anchor('customers','<i class="fa fa-angle-double-left"></i> Back','class = "btn btn-md btn-outline btn-success"') ?>    
	    <button type="submit" name="add_customer" class="btn btn-success"><i class="fa fa-save fa-fw"></i>Add Customer</button>
		<?php
        echo anchor('customers','<i class="fa fa-times fa-fw"></i>Cancel', 'class = "btn btn-danger btn-md"');
        ?>    
	</div>
    
</div>    
<?php
echo form_close();
?>