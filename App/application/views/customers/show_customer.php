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
<h4><i class="fa fa-users fa-fw"></i> Customers details / <?php echo $customer_data['c_name'] ?></h4>
<hr>
<div class="well well-sm">
	<?php echo anchor('customers/edit/id/'.$customer_data['cust_id'],'<i class="fa fa-edit fa-fw"></i> Edit '.$customer_data['c_name'],'class = "btn btn-sm btn-success"')?>
    <span class="pull-right"><?php echo anchor('customers/delete/id/'.$customer_data['cust_id'],'<i class="fa fa-trash-o fa-fw"></i> Delete Customer','class="btn btn-sm btn-danger" data-confirm="Delete This Customer? This cant be restored..."')?></span>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
		<?php echo form_open(base_url().'customers/update_coordinates/id/'.$customer_data['cust_id']) ?>
        <div class="alert alert-danger"><i class="fa fa-hand-o-right"> Drag, mark & save customer <i class="fa fa-map-marker"></i> location on map to ease address access</i></div>
        <div class="form-group input-group">
            <?php 
				$lat = empty($customer_data['c_lat']) ? 0 : $customer_data['c_lat']; 
				$long = empty($customer_data['c_long']) ? 0 : $customer_data['c_long'];
			?>
            <label for="c_lat" class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i> Latitude</label>
            <input type="text" id="c_lat" name="c_lat" class="form-control" value="<?php echo $lat ?>" readonly style="background:#fff;">
            <label for="c_long" class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i> Longitude</label>
            <input type="text" id="c_long" name="c_long" class="form-control" value="<?php echo $long ?>" readonly style="background:#fff;">
            <span class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-save fa-fw"></i> Save</button></span>
		</div> 
        <?php echo form_close() ?>           
    </div>
    <div class="panel-body">
		<?php
        $address_array = array(
						$customer_data['c_address_l1'],
						$customer_data['c_address_l2'],
						$customer_data['c_city'], 
						$customer_data['c_state'],
						$customer_data['c_pincode'], 
						array_key_exists($customer_data['c_country'],$countries_assoc) ? $countries_assoc[$customer_data['c_country']] : ''
						);
        $address_array = array_filter($address_array);
        $address_str = implode("+",$address_array);	
        ?>
        <input type="hidden" id="cust_geo_address" value="<?php echo $address_str ?>">
        <input type="hidden" id="cust_geo_zoom" value="<?php echo count($address_array) == 1 ? 4 : 15 ?>">
		<div id="map-container" style="height:300px;" class="jumbotron"></div>
    	<div class="row">
	    	<div class="col-md-12">
                <ul class="list-group">
                    <li class="list-group-item active">
						<?php if(strlen($customer_data['c_mobile']) > 0 ) { ?>
                                <a href="tel:<?php echo $customer_data['c_mobile'] ?>" class="badge "><span class="glyphicon glyphicon-phone"></span> <?php echo $customer_data['c_mobile']?></a>
                        <?php } ?>                        
                        <h4><?php echo $customer_data['c_name'] ?></h4>
                        <p class="list-group-item-text">Group / <?php echo $customer_data['group_name'] ?></p>
						<?php if(strlen($customer_data['c_company']) > 0 ) { ?>
                            <p class="list-group-item-text">Company / <?php echo $customer_data['c_company']?></p>
                        <?php } ?>      
					</li>
                    <li class="list-group-item">
                    	<div class="row">
                        	<div class="col-md-6">
                                <address>
                                <?php if(strlen($customer_data['c_address_l1']) > 0 ) { ?>
                                    <i class="fa fa-map-marker fa-fw"></i> <?php echo $customer_data['c_address_l1']?>
                                <?php } 
                                    if(strlen($customer_data['c_address_l2']) > 0 ) { ?>
                                    <?php echo $customer_data['c_address_l2']?><br>
                                <?php } ?>
                                <?php if(strlen($customer_data['c_city']) > 0 ) { ?>
                                    <i class="fa fa-home fa-fw"></i> <?php echo $customer_data['c_city']?><br>
                                <?php } ?>
                                <?php if(strlen($customer_data['c_state']) > 0 ) { ?>
                                    <i class="fa fa-road fa-fw"></i> <?php echo $customer_data['c_state']?><br>
                                <?php } ?>
                                <?php if(strlen($customer_data['c_pincode']) > 0 ) { ?>
                                    <i class="fa fa-code fa-fw"></i> <?php echo $customer_data['c_pincode']?><br>
                                <?php } ?>
                                <?php if(strlen($customer_data['c_country']) > 0 ) { ?>
                                    <i class="fa fa-globe fa-fw"></i> <?php echo array_key_exists($customer_data['c_country'],$countries_assoc) ? $countries_assoc[$customer_data['c_country']] : 'Country not found'?>
                                <?php } ?>
                                </address>
							</div>
                        	<div class="col-md-6">
                                <ul class="list-group">
                                    <li class="list-group-item active">Total Spent <span class="badge">0</span></li>
                                    <li class="list-group-item">Year To Date <span class="badge">0</span></li>
                                    <li class="list-group-item">Balance <span class="badge">0</span></li>
                                    <li class="list-group-item">Loyalty Earned <span class="badge">0</span></li>
                                    <li class="list-group-item">Loyalty claimed <span class="badge">0</span></li>                    
                                </ul>
							</div>
						</div>                            
					</li>
                    <li class="list-group-item">
                    	<div class="row">
                        	<div class="col-md-6">
                            	<ul class="list-group">
                                	<li class="list-group-item active">
										<h4><i class="fa fa-google-plus-square fa-fw"></i> Google Maps</h4>
                                        <p class="list-group-item-text"><i class="fa fa-camera fa-fw"></i> Find customer address using QR Code Reader App</p>		
                                    </li>
                                    <li class="list-group-item " id="qr-code-container"></li>
								</ul>                            
                            </div>
                        	<div class="col-md-6">
                            	<ul class="list-group inner">
									<?php if(strlen($customer_data['c_mobile']) > 0 ) { ?>
                                        <li class="list-group-item active"><h4><i class="fa fa-microphone fa-fw"></i> Other Wire / Air</h4></li>    
                                    <?php } ?>
                                    <?php if(strlen($customer_data['c_ll']) > 0 ) { ?>
                                        <li class="list-group-item">
                                                <span class="glyphicon glyphicon-phone-alt"></span> <?php echo $customer_data['c_ll']?>
                                        </li>
                                    <?php } ?>
                                    <?php if(strlen($customer_data['c_website']) > 0 ) { ?>
                                        <li class="list-group-item">
                                            <a class="btn btn-xs btn-success" href="http://<?php echo $customer_data['c_website']?>" target="_blank"><i class="fa fa-cloud"></i> <?php echo $customer_data['c_website']?></a>
                                        </li>
                                    <?php } ?>
                                    <?php if(strlen($customer_data['c_email']) > 0 ) { ?>
                                        <li class="list-group-item">
                                            <a class="btn btn-xs btn-success" href="mailto:<?php echo $customer_data['c_email'];?>"><i class="fa fa-envelope"></i> <?php echo $customer_data['c_email']?></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
						</div>
                    </li>
				</ul>
			</div>                      
        </div>
    </div>
</div>
<?php echo form_open('customers/'.$customer_data['cust_id'],array('method' => 'get','id' => 'myform')); 
$date_start = date('01-M-Y',now());
$days_in_month = days_in_month(date('m'),date('Y'));
$date_end = $days_in_month."-".date('M')."-".date('Y');
$start = form_input(array('autocomplete' => 'off', 'name' => 'date_start','id' => 'date_start','class' => 'form-control input-sm','value' => isset($_GET['date_start']) ? $_GET['date_start'] : $date_start));
$end = form_input(array('autocomplete' => 'off', 'name' => 'date_end','id' => 'date_end','class' => 'form-control input-sm','value' => isset($_GET['date_end']) ? $_GET['date_end'] : $date_end));
$log_code_drop = form_dropdown('log_code', $log_codes,isset($_GET['log_code']) ? $_GET['log_code'] : '','id="log_code" class="form-control input-sm"');
$users = form_dropdown('users', $users,isset($_GET['users']) ? $_GET['users'] : '','id="users" class="form-control input-sm"');
$register_drop = form_dropdown('register_drop',$register_combo,'','id="register_drop" class="form-control input-sm"');
$rec_number = form_input(array('autocomplete' => 'off', 'name' => 'rec_number','id' => 'rec_number','class' => 'form-control input-sm','value' => ''));
?>
<div class="panel panel-default">
    <div class="panel-heading">Filter Criteria</div>
    <div class="panel-body">
		<div class="row">
			<div class="col-md-4">
	            <div class="form-group input-group">
                	<label for="date_start" class="input-group-addon">Start date</label>
					<?php echo $start ?>
                </div>
			</div>
			<div class="col-md-4">
	            <div class="form-group input-group">
                	<label for="date_end" class="input-group-addon">End date</label>
					<?php echo $end?>
                </div>
			</div>
		</div>  
        <div class="effect">
			<div class="row">
                <div class="col-md-6">
                    <div class="form-group input-group">
                        <label for="register_drop" class="input-group-addon">Register</label>
						<?php echo $register_drop ?>	
                    </div>
				</div>
                <div class="col-md-6">
                    <div class="form-group input-group">
                        <label for="rec_number" class="input-group-addon">Receipt number</label>
						<?php echo $rec_number ?>	
                    </div>
				</div>

                <div class="col-md-6">
                    <div class="form-group input-group">
                        <label for="users" class="input-group-addon">User</label>
						<?php echo $users ?>	
                    </div>
				</div>

                <div class="col-md-6">
                    <div class="form-group input-group">
                        <label for="log_code" class="input-group-addon">Action</label>
						<?php echo $log_code_drop ?>	
                    </div>
				</div>
			</div>
		</div>  
        <div class="btn-group">
            <button type="submit" class="btn btn-success btn-sm">Show Logs</button>
            <button type="button" id="filter_button" class="btn btn-danger btn-sm"><i class="fa fa-filter"></i> Filter Options</button>
        </div>
                  
	</div>
</div> 
<?php 
echo form_close(); 
?>
<?php
if(isset($_GET['register_drop']) || isset($_GET['rec_number'])|| isset($_GET['users']) || isset($_GET['log_code'])) {

	echo '<input type="hidden" id="toggle_filter" value="1">';
} else {
	echo '<input type="hidden" id="toggle_filter" value="0">';	
}
$tmpl = array (
	'table_open'   => '<table class="table table-striped table-curved table-condensed">',
);
$this->table->set_template($tmpl);			
$heading = array('Date','User','Register','Receipt','Note','Status','Total');
$this->table->set_heading($heading);
$this->table->add_row(array('data' => ':::Pending:::','colspan' => 7,'align' => 'center'));
echo $this->table->generate().'<br>';
?>