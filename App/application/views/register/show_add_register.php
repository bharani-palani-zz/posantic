<h4><i class="fa fa-plus fa-fw"></i> Add new register for <?php echo $location ?></h4>
<hr>
<?php 
echo form_open(base_url().'setup/register/create_register/id/'.$loc_id);
?>
<div class="panel panel-default reg_div">
    <div class="panel-heading">Register Details</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="reg_name" class="input-group-addon"><span class="glyphicon glyphicon-th-list"></span> Register Name</label>
							<?php
                            echo form_input(array('autocomplete' => 'off', 'placeholder' => 'Max 25 Characters', 'value' =>set_value('reg_name'), 'name' => 'reg_name', 'id' => 'reg_name','class' => 'form-control input-sm'))
                            ?>
						</div>
						<?php if(form_error('reg_name')) { ?><p class="col-sm-12 messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('reg_name') ?></p><?php } ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="reg_prefix" class="input-group-addon" data-toggle="popover" data-placement="top"  data-content="A prefix string for your bills. Eg: Your register name's short cut code else leave blank"><span class="glyphicon glyphicon-text-background"></span> Register Bill number Prefix</label>
							<?php
                            echo form_input(array('autocomplete' => 'off', 'placeholder' => 'Max 5 Characters', 'name' => 'reg_prefix', 'id' => 'reg_prefix','class' => 'form-control input-sm'))
                            ?>
						</div>
                    </div>
                </div>
            </div>  
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="reg_bill_seq" class="input-group-addon" data-toggle="popover" data-placement="top"  data-content="An integer value, your bill number to start with. You can change this any time relevent to your audit advises"><i class="fa fa-sort-numeric-asc fa-fw"></i> Register Bill Sequence</label>
							<?php
                            echo form_input(array('autocomplete' => 'off', 'placeholder' => 'Any number', 'name' => 'reg_bill_seq', 'id' => 'reg_bill_seq','class' => 'form-control input-sm'))
                            ?>
						</div>
                    </div>
				</div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="reg_rec_temp" class="input-group-addon"><span class="glyphicon glyphicon-text-size"></span> Receipt Template</label>
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
                            <label for="reg_qt_temp" class="input-group-addon"><i class="fa fa-desktop"></i> Quick Touch Template</label>
							<?php
                            echo form_dropdown('reg_qt_temp',$quicktouch_combo,'','class="form-control input-sm" id="reg_qt_temp"')
                            ?>
						</div>
                    </div>
				</div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="reg_bill_round" class="input-group-addon"><i class="fa fa-arrows-alt"></i> Bill Round</label>
							<?php
                            echo form_dropdown('reg_bill_round',$round_method,'','class="form-control input-sm" id="reg_bill_round"')
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
                                <?php echo form_radio(array('data-on-color' => 'success', 'data-on-text' => 'Enabled', 'data-off-text' => 'Enable', 'data-label-text' => 'Email Receipt','data-size' => 'small','name' => 'reg_email_rec', 'id' => 'email_rec_true', 'checked' => true , 'value' => 30)) ?>
                                &nbsp;
                                <?php echo form_radio(array('data-on-color' => 'danger', 'data-on-text' => 'Disabled', 'data-off-text' => 'Disable', 'data-label-text' => 'Email Receipt','data-size' => 'small','name' => 'reg_email_rec', 'id' => 'email_rec_false', 'checked' => false , 'value' => 40)) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">            
                        <div class="col-md-12">
		                    <div class="form-group">
                                <?php echo form_radio(array('data-on-color' => 'success', 'data-on-text' => 'Enabled', 'data-off-text' => 'Enable', 'data-label-text' => 'PrintReceipt','data-size' => 'small','name' => 'reg_print_rec', 'id' => 'print_rec_true', 'checked' => true , 'value' => 30)) ?> 
								&nbsp;
                                <?php echo form_radio(array('data-on-color' => 'danger', 'data-on-text' => 'Disabled', 'data-off-text' => 'Disable', 'data-label-text' => 'PrintReceipt','data-size' => 'small','name' => 'reg_print_rec', 'id' => 'print_rec_false', 'checked' => false , 'value' => 40)) ?>
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
			Use preferred register control settings for a flawless sale	
		</div>
</div>
<div class="row">
    <div class="col-lg-12">
    <button type="submit" class="btn btn-success btn-md" name="insert_register" id="insert_register">
      <i class="fa fa-save fa-fw"></i>Add Register for <?php echo $location ?>
    </button>    
	<?php echo anchor('setup/outlets_and_registers','<i class="fa fa-times fa-fw"></i>Cancel', 'class = "btn btn-danger btn-md"')  ?>  
	</div>
</div>

<?php
echo form_close();
?>
