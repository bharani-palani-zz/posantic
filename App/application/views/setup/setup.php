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
echo form_open_multipart(base_url().'setup/update_account',array('id' => "upform",'name' => "upform", 'size' => '5000','class' => "form-horizontal"));	
echo form_hidden('merchant_id',$this->session->userdata('acc_no'));

//$vailidity_date = date('d/M/Y h:i A',gmt_to_local($master_data[1],$account['tz'], date("I")));
$days_left = $master_data[1];
$validity_time = date('h:i A',gmt_to_local(strtotime($master_data[25]),$account['tz'], date("I"))); // get time from created_data
$vailidity_date = date('d/M/Y ',gmt_to_local(strtotime('+'.$days_left.' day'),$account['tz'], date("I"))).$validity_time; // get date from remaining validity days
$ext = '';
$root = APPPATH.'user_images/'.md5($this->session->userdata('acc_no')).'/logo/logo_thumb';
foreach (glob($root.".*") as $filename) {
	$ext = substr($filename,-3);
}
$image_href = $root.'.'.$ext;
if(file_exists($image_href))
{
	$image_href = base_url().$image_href;
	$logic = 'Change';
} else {
	$image_href = base_url().APPPATH.'images/assets/outlet.png';									
	$logic = 'Upload';
}
$http = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$cloudurl = strtolower($http.$master_data[15].'.'.$this->session->userdata('pos_hoster_cmp').'.com');
$root = APPPATH.'user_images/'.md5($this->session->userdata('acc_no')).'/logo/logo_thumb';

$address_array = array(
				$master_data[6], // addr 1
				$master_data[7],// addr 2
				$master_data[8], // chennai
				$master_data[9], // state
				$master_data[10], // pincode
				array_key_exists($master_data[11],$countries_assoc) ? $countries_assoc[$master_data[11]] : ''
				);
$address_array = array_filter($address_array);
$address_str = implode("+",$address_array);	
?>
<input type="hidden" id="cust_geo_address" value="<?php echo $address_str ?>">
<input type="hidden" id="cust_geo_zoom" value="<?php echo count($address_array) == 1 ? 4 : 15 ?>">

<h4><i class="fa fa-wrench fa-fw"></i> Setup</h4>
<h6>Your business and contact information</h6>
<hr>

<div class="panel panel-default">
    <div class="panel-heading">Business Details</div>
    <div class="panel-body">
		<div class="row">
        	<div class="col-md-8">
            	<div class="container-fluid">
                    <div class="form-group">
                        <div class="input-group">
                          <label for="company_name" class="input-group-addon">Company Name</label>
                          <?php echo form_input(array('autocomplete' => 'off', 'size' => 20, 'name' => 'company_name', 'id' => 'company_name','class' => 'form-control','value' => set_value('company_name',$master_data[14]))) ?>
                        </div>
	                    <?php if (form_error('company_name')) { ?><p class="col-xs-12"><div class="messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('company_name') ?></div></p><?php } ?>                                            
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 col-md-3"><strong>Validity</strong></div>
                    <div class="col-lg-9 col-md-9"><?php echo $vailidity_date." | ".$days_left." Days Left" ?></div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 col-md-3"><strong>Account Type</strong></div>
                    <div class="col-lg-9 col-md-9"><?php echo $master_data[12] ?></div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 col-md-3"><strong>Account Mode</strong></div>
                    <div class="col-lg-9 col-md-9"><?php echo $master_data[18] ?></div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 col-md-3"><strong>Product Limit</strong></div>
                    <div class="col-lg-9 col-md-9"><?php echo defined($master_data[13]) ? $cur_stocks ." / &infin;" : $cur_stocks ." / ". $master_data[13] ?></div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 col-md-3"><strong>User Limit</strong></div>
                    <div class="col-lg-9 col-md-9"><?php echo defined($master_data[16]) ? $user_count." / &infin;" : $user_count." / ".$master_data[16] ?></div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 col-md-3"><strong>Customer Database</strong></div>
                    <div class="col-lg-9 col-md-9"><?php echo defined($master_data[17]) ? $cust_count." / &infin;" : $cust_count." / ".$master_data[17] ?></div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 col-md-3"><strong>Plan Code</strong></div>
                    <div class="col-lg-9 col-md-9"><?php echo $master_data[21] ?></div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 col-md-3"><label for="curr">Currency</label></div>
                    <div class="col-lg-9 col-md-9"><?php echo form_dropdown('curr', $curr,$account['currency'],'class="form-control" id="curr"') ?></div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 col-md-3"><strong>Time Zone</strong></div>
                    <div class="col-lg-9 col-md-9"><?php echo timezone_menu($account['tz'],'form-control') ?></div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 col-md-3"><label for="fb">Facebook URL</label></div>
                    <div class="col-lg-9 col-md-9"><?php echo form_input(array('autocomplete' => 'off', 'name' => 'fb', 'id' => 'fb','class' => 'form-control','placeholder' => 'Facebook Id','value' => $account['fbid'])) ?></div>
                </div>
            </div>
        	<div class="col-md-4 text-center">
                <h2><div class="label label-default">Cloud URL</div></h2>
                <p><b><?php echo $cloudurl ?></b></p>
                <em><small>*Allowed Types - GIF|JPG|PNG</small></em>
                <p>Max Image Size: 3264 X 2448<br />
                Max File Size: 3Mb<br />
                <img class="img-circle" src="<?php echo $image_href.'?random='.time() ?>" width="150" height="150" /><br />
                <?php echo $logic ?> Logo:<br />
                <label class="btn btn-primary" for="my-file-selector">
                    <?php echo form_upload(array('name' => 'userfile', 'id' => 'my-file-selector', 'style' => 'display:none;')) ?>
                    Choose file...
                </label>    
                </p>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Contact Information</div>
    <div class="panel-body">
    	<div class="container-fluid">
			<div class="row">
            	<div class="col-lg-6 col-md-6">
                    <div class="form-group input-group">
                        <label for="contact_name" class="input-group-addon">Name</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'size' => 20, 'name' => 'contact_name', 'id' => 'contact_name','class' => 'form-control','value' => $master_data[3])) ?>
                    </div>
                    <div class="form-group input-group">
                        <label for="contact_mobile" class="input-group-addon">Mobile</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'size' => 20, 'name' => 'contact_mobile', 'id' => 'contact_mobile','class' => 'form-control','value' => $master_data[4]))?>
                    </div>
                    <div class="form-group input-group">
                        <label for="contact_email" class="input-group-addon">Email</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'size' => 30, 'name' => 'contact_email', 'id' => 'contact_email','class' => 'form-control','value' => $master_data[5]))?>
                    </div>
                    <div class="form-group input-group">
                        <label for="contact_addr1" class="input-group-addon">Address 1</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'size' => 40, 'name' => 'contact_addr1', 'id' => 'contact_addr1','class' => 'form-control','value' => $master_data[6]))?>
                    </div>
                    <div class="form-group input-group">
                        <label for="contact_addr2" class="input-group-addon">Address 2</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'size' => 40, 'name' => 'contact_addr2', 'id' => 'contact_addr2','class' => 'form-control','value' => $master_data[7]))?>	
                    </div>
                    <div class="form-group input-group">
                        <label for="contact_city" class="input-group-addon">City</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'size' => 20, 'name' => 'contact_city', 'id' => 'contact_city','class' => 'form-control','value' => $master_data[8]))?>
                    </div>
                    <div class="form-group input-group">
                        <label for="contact_state" class="input-group-addon">State</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'size' => 20, 'name' => 'contact_state', 'id' => 'contact_state','class' => 'form-control','value' => $master_data[9]))?>
                    </div>
                    <div class="form-group input-group">
                        <label for="contact_postalcode" class="input-group-addon">Postal Code</label>
                        <?php echo form_input(array('autocomplete' => 'off', 'size' => 20, 'name' => 'contact_postalcode', 'id' => 'contact_postalcode','class' => 'form-control','value' => $master_data[10]))?>	
                    </div>
                    <div class="form-group input-group">
                        <label for="contact_country" class="input-group-addon font-12px">Country</label>
                        <?php echo form_dropdown('contact_country', $country_dropdown, $master_data[11],'class="form-control" id="contact_country"')?>
                    </div>
				</div>
                <div class="col-lg-6 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body"> 
                            <input type="hidden" id="c_lat" name="c_lat" class="form-control" value="<?php echo $master_data[23] ?>" readonly style="background:#fff;">
                            <input type="hidden" id="c_long" name="c_long" class="form-control" value="<?php echo $master_data[24] ?>" readonly style="background:#fff;">
                        	           
							<div id="map-container" style="height:320px;" class="jumbotron"></div>
	                        <h6>Your email and geo-location helps us to track you on mails and <?php echo strtolower($this->session->userdata('pos_hoster_cmp')) ?> updates.</h6>
                            <h6>Drag, mark and save geo-coordinates of your perfect location.</h6>
                        </div>
					</div>                        
                </div>
                
			</div>                
		</div>
    </div>
</div>

<button type="submit" class="btn btn-success btn-block" id="setup_sub">
  <i class="glyphicon glyphicon-floppy-save"></i>&nbsp;Save Account Information
</button>
<?php
echo form_close();
?>
