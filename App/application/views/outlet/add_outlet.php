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
echo form_open(base_url().'setup/outlet/create_outlet');
?>
<h4><span class="glyphicon glyphicon-map-marker"></span> Add Outlet</h4>
<hr>
<div class="panel panel-default">
    <div class="panel-heading"><strong>Outlet Details</strong></div>
        <div class="panel-body">
			<?php
            $check = array(
            'name'        => 'has_register',
            'id'          => 'has_register',
            'value'       => 30,
			'data-on-text' => 'Yes',
			'data-off-text' => 'No',
			'data-label-text' => 'Add register to this outlet',
			'data-label-width' => 200,
            'checked'     => TRUE
            );						
            ?>
            <div class="well well-sm">
				<span data-placement="top" data-content="Check to add register to this outlet else uncheck if this is not an outlet, which may be a warehouse or storage yard, where a register is not required" data-toggle="popover" title="Alert:" data-placement = "top"><?php echo form_checkbox($check) ?></span>
			</div>
            <div class="row">
	            <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i> Outlet Name</span>
		                    <?php echo form_input(array('autocomplete' => 'off','name' => 'outlet_name', "class" => "form-control input-sm", 'id' => 'outlet_name' , "value" => set_value('outlet_name')))?>
        				</div>
                    	<?php if(form_error('outlet_name')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('outlet_name') ?></p><?php } ?>
                	</div>	    
				</div>
	            <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-bookmark fa-fw"></i> Outlet Tax</span>
							<?php
                            echo form_dropdown('outlet_tax', $def_locale_tax,'','class="form-control input-sm" id="outlet_tax" data-toggle="popover" title="Alert:" data-placement="top" data-content="This is the default tax for products sold at this outlet. If a product holds other tax for this outlet, that can be updated on edit product window"')
                            ?>
						</div>
					</div>                                        
				</div>
            </div>	
            <div class="row">
	            <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i> Address 1</span>
							<?php
                            echo form_input(array('size' => 40,'autocomplete' => 'off','name' => 'outlet_addrr1', "class" => "form-control input-sm", 'id' => 'outlet_addrr1', "value" => set_value('outlet_addrr1')))
                            ?>

						</div>
						<?php if(form_error('outlet_addrr1')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('outlet_addrr1') ?></p><?php } ?>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i> Address 2</span>
							<?php
                            echo form_input(array('size' => 40,'autocomplete' => 'off','name' => 'outlet_addrr2', "class" => "form-control input-sm", 'id' => 'outlet_addrr2',"value" => set_value('outlet_addrr2')))
                            ?>
						</div>
						<?php if(form_error('outlet_addrr2')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('outlet_addrr2') ?></p><?php } ?>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-home fa-fw"></i> City</span>
							<?php
                            echo form_input(array('autocomplete' => 'off','name' => 'outlet_city', "class" => "form-control input-sm", 'id' => 'outlet_city',"value" => set_value('outlet_city')))
                            ?>
						</div>
	                    <?php if(form_error('outlet_city')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('outlet_city') ?></p><?php } ?>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-road fa-fw"></i> State</span>
							<?php
                            echo form_input(array('autocomplete' => 'off','name' => 'outlet_state', "class" => "form-control input-sm", 'id' => 'outlet_state',"value" => set_value('outlet_state')))
                            ?>
						</div>
	                    <?php if(form_error('outlet_state')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('outlet_state') ?></p><?php } ?>
                    </div>
				</div>
	            <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-code fa-fw"></i> Pincode</span>
							<?php
                            echo form_input(array('autocomplete' => 'off','name' => 'outlet_pin', "class" => "form-control input-sm", 'id' => 'outlet_pin',"value" => set_value('outlet_pin')))
                            ?>
                        </div>
						<?php if(form_error('outlet_pin')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('outlet_pin') ?></p><?php } ?>
					</div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-globe fa-fw"></i> Country</span>
							<?php
                            echo form_dropdown('outlet_country', $country_dropdown,'','class="form-control input-sm" id="outlet_country"')
                            ?>
                        </div>
					</div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-phone-alt"></span> Phone</span>
							<?php
                            echo form_input(array('autocomplete' => 'off','name' => 'outlet_ll', "class" => "form-control input-sm", 'id' => 'outlet_ll',"value" => set_value('outlet_ll')))
                            ?>
                        </div>
	                    <?php if(form_error('outlet_ll')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('outlet_ll') ?></p><?php } ?>
					</div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span> Email</span>
							<?php
                            echo form_input(array('autocomplete' => 'off','name' => 'outlet_email', "class" => "form-control input-sm", 'id' => 'outlet_email',"value" => set_value('outlet_email')))
                            ?>
                        </div>
						<?php if(form_error('outlet_email')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('outlet_email') ?></p><?php } ?>
					</div>
				</div>
			</div>                
        </div>
    <div class="panel-footer">
	    <span class="glyphicon glyphicon-map-marker"></span> Your outlet locality detail is mandatory for users to map ecommerce stores online
    </div>
</div>
<div class="panel panel-default reg_div">
    <div class="panel-heading"><strong>Register Details</strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-th-list"></span> Register Name</span>
							<?php
                            echo form_input(array('autocomplete' => 'off','name' => 'reg_name', "class" => "form-control input-sm", 'id' => 'reg_name'))
                            ?>
						</div>
						<?php if(form_error('reg_name')) { ?><p class="col-sm-12 messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('reg_name') ?></p><?php } ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon" data-toggle="popover" data-placement="top"  data-content="A prefix string for your bills. Eg: Your register name's short cut code else leave blank"><span class="glyphicon glyphicon-text-background"></span> Register Bill number Prefix</span>
							<?php
                            echo form_input(array('size' => 5,'autocomplete' => 'off','name' => 'reg_prefix', "class" => "form-control input-sm", 'id' => 'reg_prefix'))
                            ?>
						</div>
                    </div>
                </div>
            </div>  
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon" data-toggle="popover" data-placement="top"  data-content="An integer value, your bill number to start with. You can change this any time relevent to your audit advises"><i class="fa fa-sort-numeric-asc fa-fw"></i> Register Bill Sequence</span>
							<?php
                            echo form_input(array('size' => 5,'autocomplete' => 'off','name' => 'reg_bill_seq', "class" => "form-control input-sm", 'id' => 'reg_bill_seq'))
                            ?>
						</div>
                    </div>
				</div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-text-size"></span> Receipt Template</span>
							<?php
                            echo form_dropdown('reg_rec_temp',$template_combo,'','class="form-control input-sm" id="reg_rec_temp"')
                            ?>
						</div>
                    </div>
				</div>
            </div>        
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-desktop"></i> Quick Touch Template</span>
							<?php
                            echo form_dropdown('reg_qt_temp',$quicktouch_combo,'','class="form-control input-sm" id="reg_qt_temp"')
                            ?>
						</div>
                    </div>
				</div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-arrows-alt"></i> Bill Round</span>
							<?php
                            echo form_dropdown('reg_bill_round',$round_method_combo,'','class="form-control input-sm" id="reg_bill_round"')
                            ?>
						</div>
                    </div>
				</div>
            </div>
            <br>      
            <div class="panel panel-default">
	            <div class="panel-heading">Control Settings</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
		                    <div class="form-group">
                                <?php echo form_radio(array('data-on-color' => 'success', 'data-on-text' => 'Enabled', 'data-off-text' => 'Enable', 'data-label-text' => 'Email Receipt','data-size' => 'small','name' => 'email_rec_stat', 'id' => 'email_rec_true', 'checked' => true , 'value' => 30)) ?>
                                &nbsp;
                                <?php echo form_radio(array('data-on-color' => 'danger', 'data-on-text' => 'Disabled', 'data-off-text' => 'Disable', 'data-label-text' => 'Email Receipt','data-size' => 'small','name' => 'email_rec_stat', 'id' => 'email_rec_false', 'checked' => false , 'value' => 40)) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">            
                        <div class="col-md-12">
		                    <div class="form-group">
                                <?php echo form_radio(array('data-on-color' => 'success', 'data-on-text' => 'Enabled', 'data-off-text' => 'Enable', 'data-label-text' => 'PrintReceipt','data-size' => 'small','name' => 'print_rec_stat', 'id' => 'print_rec_true', 'checked' => true , 'value' => 30)) ?> 
								&nbsp;
                                <?php echo form_radio(array('data-on-color' => 'danger', 'data-on-text' => 'Disabled', 'data-off-text' => 'Disable', 'data-label-text' => 'PrintReceipt','data-size' => 'small','name' => 'print_rec_stat', 'id' => 'print_rec_false', 'checked' => false , 'value' => 40)) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">                        	
                        <div class="col-md-12">
		                    <div class="form-group">
                            	<span data-toggle="popover" data-placement="top"  data-content="Ask user change on every sale. Change user during sales incase you have multiple cashiers working for the same register">
									<?php echo form_radio(array('data-on-color' => 'success', 'data-on-text' => 'Enabled', 'data-off-text' => 'Enable', 'data-label-text' => 'UserChange','data-size' => 'small','name' => 'ask_user_stat', 'id' => 'ask_user_true', 'checked' => false , 'value' => 30)) ?>
                                    &nbsp;
                                    <?php echo form_radio(array('data-on-color' => 'danger', 'data-on-text' => 'Disabled', 'data-off-text' => 'Disable', 'data-label-text' => 'UserChange','data-size' => 'small','name' => 'ask_user_stat', 'id' => 'ask_user_false', 'checked' => true , 'value' => 40)) ?>
								</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">                                        
                        <div class="col-lg-12">
		                    <div class="form-group">
                                <span data-toggle="popover" data-placement="top"  data-content="Ask for making quotes(notes, markings, notices, discounts etc..) on sales">
									<?php echo form_radio(array('data-on-color' => 'success', 'data-on-text' => 'Enabled', 'data-off-text' => 'Enable', 'data-label-text' => 'AskQuotes','data-size' => 'small','name' => 'ask_quotes_stat', 'id' => 'ask_quotes_true', 'checked' => false , 'value' => 30)) ?> 
                                    &nbsp;
                                    <?php echo form_radio(array('data-on-color' => 'danger', 'data-on-text' => 'Disabled', 'data-off-text' => 'Disable', 'data-label-text' => 'AskQuotes','data-size' => 'small','name' => 'ask_quotes_stat', 'id' => 'ask_quotes_false', 'checked' => true , 'value' => 40)) ?>
								</span>
                            </div>
                        </div>
                    </div>        
				</div>
			</div>                
		</div>
        <div class="panel-footer">
		<i class="fa fa-ban"></i> Disable unnecessary control settings if not required
		</div>
</div>

<div class="row">
    <div class="col-lg-12">
    <button type="submit" class="btn btn-success btn-md" name="insert_outlet" id="insert_outlet">
      <i class="fa fa-shopping-cart"></i> Create Outlet With Register
    </button>    
	<?php echo anchor('setup/outlets_and_registers','<span class="glyphicon glyphicon-remove"></span> Cancel', 'class = "btn btn-danger btn-md"')  ?>  
	</div>
</div>

<?php
echo form_close();
?>