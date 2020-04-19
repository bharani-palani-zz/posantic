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
<h4><i class="fa fa-truck"></i> Edit Supplier</h4>
<?php echo form_open(base_url().'supplier/change/id/'.$supp_details['supp_id']) ?>
<div class="panel panel-default" id="product_panel">
    <div class="panel-heading"><i class="fa fa-list-alt"></i> Supplier Details</div>
    <div class="panel-body">
    
			<div class="row">
            	<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="contact_name" class="input-group-addon"><i class="fa fa-user"></i> Contact Name</label>
                            <?php echo form_input(array('autocomplete' => 'off', 'name' => 'contact_name', 'id' => 'contact_name','class' => 'form-control input-sm','value' => set_value('contact_name',$supp_details['auth_pers']))) ?>
                        </div>
                    </div>     
					<?php if(form_error('contact_name')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('contact_name') ?></p><?php } ?>                           		    
                </div>
            </div>

			<div class="row">
            	<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="cmp_name" class="input-group-addon"><i class="fa fa-location-arrow"></i> Company Name</label>
                            <?php echo form_input(array('autocomplete' => 'off', 'name' => 'cmp_name', 'id' => 'cmp_name','class' => 'form-control input-sm','value' => set_value('cmp_name',$supp_details['cmp_name']))) ?>
                        </div>
                    </div>            
					<?php if(form_error('cmp_name')) { ?><p class="col-sm-12 text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('cmp_name') ?></p><?php } ?>                           		                        		    
                </div>
            </div>

		<div class="row">
			<div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Supplier description</div>
                    <div class="panel-body">
					<?php
					echo form_textarea(array(
								'name'        => 'supp_desc',
								'id'          => 'supp_desc',
								'rows'		=> 5,
								'cols'		=> 50,
								'value'		=> $supp_details['supp_description']
								)
							);
					?>	
					</div>
                </div>
			</div>
		</div>   

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <div class="input-group">
                        <label for="contact_mobile" class="input-group-addon"><i class="fa fa-mobile"></i> Mobile</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'contact_mobile', 'id' => 'contact_mobile','class' => 'form-control input-sm','value' => $supp_details['mobile'])) ?>
                    </div>
                </div>            
			</div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <div class="input-group">
                        <label for="contact_fax" class="input-group-addon"><i class="fa fa-fax"></i> Fax</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'contact_fax', 'id' => 'contact_fax','class' => 'form-control input-sm','value' => $supp_details['fax'])) ?>
                    </div>
                </div>            
			</div>
		</div>            

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <div class="input-group">
                        <label for="contact_email" class="input-group-addon"><i class="fa fa-envelope"></i> Email</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'contact_email', 'id' => 'contact_email','class' => 'form-control input-sm','value' => $supp_details['email'])) ?>
                    </div>
                </div>            
			</div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <div class="input-group">
                        <label for="contact_web" class="input-group-addon"><i class="fa fa-cloud"></i> Website</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'contact_web', 'id' => 'contact_web','class' => 'form-control input-sm','value' => $supp_details['web'])) ?>
                    </div>
                </div>            
			</div>
		</div>            

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <div class="input-group">
                        <label for="contact_addr1" class="input-group-addon"><span class="glyphicon glyphicon-map-marker"></span> Address 1</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'contact_addr1', 'id' => 'contact_addr1','class' => 'form-control input-sm','value' => $supp_details['addrr1'])) ?>
                    </div>
                </div>            
			</div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <div class="input-group">
                        <label for="contact_addr2" class="input-group-addon"><span class="glyphicon glyphicon-map-marker"></span> Address 2</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'contact_addr2', 'id' => 'contact_addr2','class' => 'form-control input-sm','value' => $supp_details['addrr2'])) ?>
                    </div>
                </div>            
			</div>
		</div>            

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <div class="input-group">
                        <label for="contact_city" class="input-group-addon"><span class="glyphicon glyphicon-home"></span> City</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'contact_city', 'id' => 'contact_city','class' => 'form-control input-sm','value' => $supp_details['city'])) ?>
                    </div>
                </div>            
			</div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <div class="input-group">
                        <label for="contact_state" class="input-group-addon"><span class="glyphicon glyphicon-road"></span> State</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'contact_state', 'id' => 'contact_state','class' => 'form-control input-sm','value' => $supp_details['state'])) ?>
                    </div>
                </div>            
			</div>
		</div>            

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <div class="input-group">
                        <label for="contact_postalcode" class="input-group-addon"><i class="fa fa-code fa-fw"></i> Postal Code</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'contact_postalcode', 'id' => 'contact_postalcode','class' => 'form-control input-sm','value' => $supp_details['postal_code'])) ?>
                    </div>
                </div>            
			</div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <div class="input-group">
                        <label for="contact_phone" class="input-group-addon"><span class="glyphicon glyphicon-phone-alt"></span> Phone</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'name' => 'contact_phone', 'id' => 'contact_phone','class' => 'form-control input-sm','value' => $supp_details['ll'])) ?>
                    </div>
                </div>            
			</div>
		</div>            

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <div class="input-group">
                        <label for="contact_country" class="input-group-addon"><i class="fa fa-globe fa-fw"></i> Country</label>
                        <?php echo form_dropdown('contact_country', $country_dropdown,$supp_details['country'],'id="contact_country" class="form-control input-sm"') ?>
                    </div>
                </div>            
			</div>
		</div>
        <button type="submit" name="supplier_sub" id="supplier_sub" class="btn btn-sm btn-success loading_modal"><i class="fa fa-save"></i> Update Supplier</button>
		<?php echo anchor('supplier','<i class="fa fa-power-off fa-fw"></i> Cancel', 'class="btn btn-sm btn-danger"') ?>
	</div>
</div>            
<?php echo form_close() ?>
