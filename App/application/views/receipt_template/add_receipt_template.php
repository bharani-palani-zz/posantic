<h4><i class="fa fa-plus fa-fw"></i> Add Receipt <span class="glyphicon glyphicon-text-size"></span>emplate</h4>
<hr>
<?php
echo form_open(base_url().'setup/receipt_template/create');
?>
<div class="panel panel-default">
	<div class="panel-heading">Template Config</div>
    <div class="panel-body">
    <?php if(validation_errors()) { ?>
    	<div class="row">
        	<div class="col-md-12">
                <div class="alert alert-sm alert-danger fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <span class="glyphicon glyphicon-remove-sign"></span> Please resolve the following errors	
                </div>
			</div>
		</div>
    <?php } ?>
    	<div class="row">
        	<div class="col-md-6 text-center">
                <div class="input-group pad-5px">
                  <label for="temp_name" class="input-group-addon">
                      Bill template name
                  </label>
                  <?php echo form_input(array('autocomplete' => 'off', 'name' => 'temp_name', 'id' => 'temp_name','class' => 'form-control','placeholder' => 'Max 25 Characters','value' => set_value('temp_name'))) ?>
                </div>
            </div>
			<?php if(form_error('temp_name')) { ?><div class="col-sm-12 messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('temp_name') ?></div><?php } ?>
        </div>
        <!--End of header name-->
        <br>
    	<div class="row">
			<div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4 col-sm-7 col-sm-offset-3 rec_temp">
				<div class="row">
					<div class="col-sm-12">
                    	<div class="panel panel-default">
	                    	<div class="panel-heading">
                                <div class="input-group">
                                    <label for="header_type" class="input-group-addon ">
	                                    Logo type
                                    </label>
                                    <?php echo form_dropdown('header_type',$combo_rec_headers,'','id="header_type" class="form-control"') ?>
                                </div>
                            </div>
	                    	<div class="panel-body">
                                <div id="myCarousel" class="slide">
                                    <!-- Wrapper for slides -->
                                    <div class="carousel-inner" role="listbox">
									<?php 
									$slides_classes = array(
														'1_text' => '<img class="img-circle" width="150" height="150" src="'.base_url().APPPATH.'images/assets/apple_logo.jpg'.'?random='.time().'" />',
														'2_text' => '<img class="img-rounded" width="150" height="150" src="'.base_url().APPPATH.'images/assets/apple_logo.jpg'.'?random='.time().'" />',
														'3_text' => '<div class="text-center" style="width:80%; height:150px; border-radius:10px; border: solid 1px #ddd;"><h3 style="line-height: 120px; vertical-align:miidle"><b>'.$this->session->userdata('cmp_name').'</b></h3></div>',
														'4_text' => '<img class="img-rounded" width="150" height="150" src="'.base_url().APPPATH.'images/assets/no_logo.jpg'.'?random='.time().'" />',
														);
									foreach($combo_rec_headers['Heading as'] as $key => $value) { 
									$active = key($combo_rec_headers['Heading as']) == $key ? 'active' : '';
                                    ?>	
                                        <div class="item <?php echo $active ?>" align="center">
											<?php echo $slides_classes[$key.'_text'] ?>
                                        </div>
                                    <?php
									}
									?>
                                    </div>                                
                                </div>
							</div>
                            <div class="panel-footer">
                                <small><?php echo anchor(base_url('/setup'),'<i class="fa fa-upload fa-fw"></i> Upload','class="btn btn-xs btn-primary"') ?> your logo image @ setup panel to ensure image in bills. A clear gray/black scale image brings good priniting resolution.</small>
                            </div>
						</div>
					</div>
				</div>    
                
				<div class="row">
					<div class="col-md-12">
                    	<div class="panel panel-default" id="header-text-panel">
	                    	<div class="panel-heading">
                            	<div class="panel-title">
                                    <a data-toggle="collapse" data-parent="#header-text-panel" href="#collapse-header-panel"><i class="fa fa-file-text fa-fw"></i>Header text</a>
                                    <h6>Contact Address, phone, email, web, taxation number etc.</h6>
                                    <h4 align="center"><span class="label label-success">SHOW / HIDE</span></h4> 
                                    <div align="center">
                                        <?php echo form_radio(array('data-on-text' => 'ON','data-off-text' => 'ON','data-on-color' => 'success','data-off-color' => 'danger','data-label-text' => 'Header text','data-size' => 'mini','name' => 'show_addrr', 'id' => 'show_addrr_true', 'checked' => true , 'value' => 10)) ?>
                                        <?php echo form_radio(array('data-on-text' => 'OFF','data-off-text' => 'OFF','data-on-color' => 'success','data-off-color' => 'danger','data-label-text' => 'Header text','data-size' => 'mini','name' => 'show_addrr', 'id' => 'show_addrr_false', 'checked' => false , 'value' => 20)) ?>
                                    </div>
                                    
                                </div>
                            </div>
	                    	<div class="panel-body panel-collapse collapse in" id="collapse-header-panel">
                                <div align="center">
                                    <?php echo form_textarea(array('name' => 'temp_header_text', 'id' => 'temp_header_text')) ?>
                                </div>
							</div>
                        </div>
					</div>	                
                </div>

				<div class="row">
					<div class="col-md-12">
                    	<div class="panel panel-default" id="checkout-settings-panel">
	                    	<div class="panel-heading">
                            	<div class="panel-title">
	                            	<a data-toggle="collapse" data-parent="#checkout-settings-panel" href="#collapse-checkout-settings-panel"><i class="fa fa-shopping-cart fa-fw"></i>Checkout settings</a>
                                </div>    
                            </div>
	                    	<div class="panel-body panel-collapse collapse in" id="collapse-checkout-settings-panel">
                                <table class="table table-condensed">
                                    <tr><th>Product</th><th>Qty</th><th>Price</th></tr>
                                    <tr><td>--</td><td>--</td><td>--</td></tr>
                                    <tr><td>..</td><td>..</td><td>..</td></tr>
                                    <tr><td>..</td><td>..</td><td>..</td></tr>
                                </table>
                                <h4 align="center"><span class="label label-success">SHOW / HIDE</span></h4>
                                <div class="well well-default well-sm text-center">
									<?php echo form_radio(array('data-on-text' => 'ON','data-off-text' => 'ON','data-on-color' => 'success','data-off-color' => 'danger','data-label-text' => 'Discounts','data-size' => 'mini','name' => 'show_disc', 'id' => 'show_disc_true', 'checked' => true , 'value' => 10)) ?>
                                    <?php echo form_radio(array('data-on-text' => 'OFF','data-off-text' => 'OFF','data-on-color' => 'success','data-off-color' => 'danger','data-label-text' => 'Discounts','data-size' => 'mini','name' => 'show_disc', 'id' => 'show_disc_false', 'checked' => false , 'value' => 20)) ?>
								</div>
                                <div class="well well-default well-sm text-center">
									<?php echo form_radio(array('data-on-text' => 'ON','data-off-text' => 'ON','data-on-color' => 'success','data-off-color' => 'danger','data-label-text' => 'Loyalty','data-size' => 'mini','name' => 'show_loyalty', 'id' => 'show_loyalty_true', 'checked' => true , 'value' => 10)) ?>
                                    <?php echo form_radio(array('data-on-text' => 'OFF','data-off-text' => 'OFF','data-on-color' => 'success','data-off-color' => 'danger','data-label-text' => 'Loyalty','data-size' => 'mini','name' => 'show_loyalty', 'id' => 'show_loyalty_false', 'checked' => false , 'value' => 20)) ?>
								</div>
                                <div class="well well-default well-sm text-center">
									<?php echo form_radio(array('data-on-text' => 'ON','data-off-text' => 'ON','data-on-color' => 'success','data-off-color' => 'danger','data-label-text' => 'Promotions','data-size' => 'mini','name' => 'show_promo', 'id' => 'show_promo_true', 'checked' => false , 'value' => 10)) ?>
                                    <?php echo form_radio(array('data-on-text' => 'OFF','data-off-text' => 'OFF','data-on-color' => 'success','data-off-color' => 'danger','data-label-text' => 'Promotions','data-size' => 'mini','name' => 'show_promo', 'id' => 'show_promo_false', 'checked' => true , 'value' => 20)) ?>
								</div>
                                <div class="well well-default well-sm text-center">
									<?php echo form_radio(array('data-on-text' => 'ON','data-off-text' => 'ON','data-on-color' => 'success','data-off-color' => 'danger','data-label-text' => 'Quotes','data-size' => 'mini','name' => 'show_quotes', 'id' => 'show_quotes_true', 'checked' => false , 'value' => 10)) ?>
                                    <?php echo form_radio(array('data-on-text' => 'OFF','data-off-text' => 'OFF','data-on-color' => 'success','data-off-color' => 'danger','data-label-text' => 'Quotes','data-size' => 'mini','name' => 'show_quotes', 'id' => 'show_quotes_false', 'checked' => true , 'value' => 20)) ?>
								</div>
							</div>
                        </div>
					</div>	
				</div>


				<div class="row">
					<div class="col-md-12">
                    	<div class="panel panel-default" id="control-settings-panel">
	                    	<div class="panel-heading">
                            	<div class="panel-title">
                            		<a data-toggle="collapse" data-parent="#control-settings-panel" href="#collapse-control-settings-panel"><i class="fa fa-cog fa-fw"></i> Control settings</a>
								</div>
                            </div>
	                    	<div class="panel-collapse collapse in" id="collapse-control-settings-panel">
                                <div class="container-fluid">                        
                                    <br>                                                        
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group form-group">
                                              <label for="temp_printer_type" class="input-group-addon ">
                                                  <i class="fa fa-print fa-fw"></i> Printer type
                                              </label>
                                              <?php echo form_dropdown('temp_printer_type',$printer_types,'','id="temp_printer_type" class="form-control"') ?>
                                            </div>
                                        </div>            
                                    </div>
                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group form-group">
                                                <label for="temp_operator_caption" class="input-group-addon">
                                                    Cashier/Operator caption
                                                </label>
                                                <?php echo form_input(array('value' => set_value('temp_operator_caption','Operator'),'autocomplete' => 'off', 'name' => 'temp_operator_caption', 'id' => 'temp_operator_caption','class' => 'form-control','placeholder' => 'Max 15 Characters')) ?>
                                            </div>
                                            <?php if(form_error('temp_operator_caption')) { ?><div class="col-sm-12 messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('temp_operator_caption') ?></div><?php } ?>
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group form-group">
                                                <label for="temp_bill_no_caption" class="input-group-addon ">
                                                    Bill number caption
                                                </label>
                                                <?php echo form_input(array('value' => set_value('temp_bill_no_caption','Bill no'),'autocomplete' => 'off', 'name' => 'temp_bill_no_caption', 'id' => 'temp_bill_no_caption','class' => 'form-control','placeholder' => 'Max 15 Characters')) ?>
                                            </div>
                                            <?php if(form_error('temp_bill_no_caption')) { ?><div class="col-sm-12 messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('temp_bill_no_caption') ?></div><?php } ?>
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group form-group">
                                                <label for="temp_disc_caption" class="input-group-addon ">
                                                    Discount caption
                                                </label>
                                                <?php echo form_input(array('value' => set_value('temp_disc_caption','Discount'),'autocomplete' => 'off', 'name' => 'temp_disc_caption', 'id' => 'temp_disc_caption','class' => 'form-control','placeholder' => 'Max 15 Characters')) ?>
                                            </div>
                                            <?php if(form_error('temp_disc_caption')) { ?><div class="col-sm-12 messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('temp_disc_caption') ?></div><?php } ?>
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group form-group">
                                                <label for="temp_tax_caption" class="input-group-addon ">
                                                    Tax caption
                                                </label>
                                                <?php echo form_input(array('value' => set_value('temp_tax_caption','Tax'),'autocomplete' => 'off', 'name' => 'temp_tax_caption', 'id' => 'temp_tax_caption','class' => 'form-control','placeholder' => 'Max 15 Characters')) ?>
                                            </div>
                                            <?php if(form_error('temp_tax_caption')) { ?><div class="col-sm-12 messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('temp_tax_caption') ?></div><?php } ?>
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group form-group">
                                                <label for="temp_change_caption" class="input-group-addon ">
                                                    Tender change caption
                                                </label>
                                                <?php echo form_input(array('value' => set_value('temp_change_caption','Change'),'autocomplete' => 'off', 'name' => 'temp_change_caption', 'id' => 'temp_change_caption','class' => 'form-control','placeholder' => 'Max 15 Characters')) ?>
                                            </div>
                                            <?php if(form_error('temp_change_caption')) { ?><div class="col-sm-12 messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('temp_change_caption') ?></div><?php } ?>
                                        </div>
                                    </div>
                                                                
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group form-group">
                                                <label for="temp_loyalty_caption" class="input-group-addon ">
                                                    Loyalty caption
                                                </label>
                                                <?php echo form_input(array('value' => set_value('temp_loyalty_caption','Loyalty'),'autocomplete' => 'off', 'name' => 'temp_loyalty_caption', 'id' => 'temp_loyalty_caption','class' => 'form-control','placeholder' => 'Max 15 Characters')) ?>
                                            </div>
                                            <?php if(form_error('temp_loyalty_caption')) { ?><div class="col-sm-12 messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('temp_loyalty_caption') ?></div><?php } ?>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group form-group">
                                                <label for="temp_total_caption" class="input-group-addon ">
                                                    Total caption
                                                </label>
                                                <?php echo form_input(array('value' => set_value('temp_total_caption','Total'),'autocomplete' => 'off', 'name' => 'temp_total_caption', 'id' => 'temp_total_caption','class' => 'form-control','placeholder' => 'Max 15 Characters')) ?>
                                            </div>
                                            <?php if(form_error('temp_total_caption')) { ?><div class="col-sm-12 messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('temp_total_caption') ?></div><?php } ?>
                                        </div>
                                    </div>
    
                                    <div class="row pad-5px">
                                        <div class="col-md-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">Footer Text</div>
                                                <div class="panel-body">
                                                    <div align="center">
                                                        <?php echo form_textarea(array('name' => 'temp_footer_text', 'id' => 'temp_footer_text')) ?>
                                                    </div>
                                                </div>
                                                <div class="panel-footer">
                                                    <small>Terms / Disclaimer / Greeting Message</small>
                                                </div>
                                            </div>
                                        </div>	                
                                    </div>
								</div>
                            </div>    
                    	</div>
                        <div class="well well-default well-sm text-center">
                            <?php echo form_radio(array('data-on-color' => 'success','data-off-color' => 'danger','data-label-text' => 'Show barcode','data-size' => 'mini','name' => 'show_barcode', 'id' => 'show_barcode_true', 'checked' => true , 'value' => 10)) ?>
                            <?php echo form_radio(array('data-on-color' => 'success','data-off-color' => 'danger','data-label-text' => 'Show barcode','data-size' => 'mini','name' => 'show_barcode', 'id' => 'show_barcode_false', 'checked' => false , 'value' => 20)) ?>
                        </div>
						<center>
	                        <div id="barcodeTarget"></div>
                        </center>
                    </div>            
                </div>
                
            </div>
		</div>        
    </div>

    <div class="panel-footer">
    <button type="submit" name="insert_temp" class="btn btn-success"><i class="fa fa-save fa-fw"></i>Save Receipt Template</button>
	<?php
	echo anchor('setup/outlets_and_registers','<i class="fa fa-times fa-fw"></i>Cancel', 'class = "btn btn-danger btn-md"');
	?>    
    </div>
</div>
<?php
echo form_close();
?>
