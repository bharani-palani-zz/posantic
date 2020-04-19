<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo $cmp;?> / Signup</title>
<link rel="shortcut icon" href="<?php echo base_url(POS_IMG_ROOT.'browser_icon/icon.png');?>">
<?php 
echo link_tag(BS3_MAIN_CSS)."\n";
echo link_tag(BS3_FA_CSS)."\n";
echo link_tag(base_url('application/style/repository/css/secure_signup.css'));
$domain = preg_replace('/^www\./', '', $web);
?>
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top" style="height:60px;">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="https://<?php echo $web ?>">
                <img src="<?php echo base_url(APPPATH.'images/assets/posantic_logo.svg')?>" class="img-thumbnail" width="100" height="50">
            </a>
            <!--<a class="navbar-brand" href="https://<?php echo $web ?>"><small class="text-uppercase"><?php echo $cmp ?></small></a>-->
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li><a style="line-height:30px;" href="http://support.posantic.com" target="_blank" class=""><i class="fa fa-support fa-fw"></i> Support</a></li>
                <li><a style="line-height:30px;" href="mailto:<?php echo $email;?>" class=""><i class="fa fa-envelope-o fa-fw"></i> MailUs</a></li>
                <li><a style="line-height:30px;" href="tel:<?php echo $hotline;?>" class=""><i class="fa fa-phone fa-fw"></i><?php echo $hotline;?></a></li>
                <li><a style="line-height:30px;" href="<?php echo base_url('startup') ;?>" class=""><i class="fa fa-sign-in fa-fw"></i> Sign in with existing account</a></li>
            </ul>
        </div>
    </div>
</nav>
<br>
<br>
<br>
<div class="container-fluid">
    <div class="text-center">
        <h3>Start your 30 day free trial</h3>
        <h5 class="text-muted">No credit card. No payments</h5>
	</div>     
        <div class="row">
            <div class="col-lg-6 col-lg-offset-3 col-md-10 col-md-offset-1">
				<?php
                if(isset($form_errors)) { 
                    echo '<div class="alert alert-md alert-warning fade in">';
                    echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                    echo $form_errors;
					echo '<h5>Please try again</h5>';
                    echo '</div>';
                }
                ?>                   
                <div class="stepwizard">
                    <div class="stepwizard-row setup-panel">
                        <div class="stepwizard-step">
                            <a href="#step-1" type="button" class="btn btn-success btn-circle">1</a>
                            <p>Step 1</p>
                        </div>
                        <div class="stepwizard-step">
                            <a href="#step-2" type="button" class="btn btn-success btn-circle" disabled="disabled">2</a>
                            <p>Step 2</p>
                        </div>
                        <div class="stepwizard-step">
                            <a href="#step-3" type="button" class="btn btn-success btn-circle" disabled="disabled">3</a>
                            <p>Step 3</p>
                        </div>
                    </div>
                </div>
            </div>
		</div>            
		<?php echo form_open(base_url().'signup_form',array('data-toggle' => "validator",'role' => "form",'id' => 'form-1')); ?>
            <div class="row setup-content" id="step-1">
                <div class="col-lg-6 col-lg-offset-3 col-md-10 col-md-offset-1 col-xs-12">
                    <h4 class="text-muted">Business Type</h4>
                    <div class="container-fluid text-center">
                        <div class="row">
                        	<?php foreach($business_type['btype_id'] as $type_key => $type_id) { ?>
                                <div class="col-lg-3 col-md-3 col-xs-12 form-group">
                                    <button type="button" class="btn btn-danger col-lg-12 col-md-12 col-xs-12 b_type nextBtn" data-id="<?php echo $business_type['btype_id'][$type_key]?>">
                                    		<div class="pull-right btype_checked"></div>
                                            <h3><i class="<?php echo $business_type['btype_glyphicon'][$type_key]?>"></i></h3>
                                            <h6 class=""><?php echo $business_type['btype_string'][$type_key]?></h6>
                                    </button>
                                </div>
                            <?php } ?>
                            <input type="hidden" name="business_type" id="business_type">
                        </div>
                    </div>	
                    <br><br>
                </div>
            </div>  
            
            <div class="row setup-content" id="step-2" style="display:none;">
                <div class="col-lg-6 col-lg-offset-3 col-md-10 col-md-offset-1 col-xs-12">
                        <h4 class="text-muted">Outlet Type</h4>
                        <div class="container-fluid">
                            <div class="row">
	                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                    <button type="button" class="col-lg-12 col-md-12 col-xs-12 btn btn-danger outlet_type nextBtn" data-id="1">
                                    		<div class="pull-right outlet_type_checked"></div>                                    
                                            <h3><i class="fa fa-stop fa-2x"></i></h3>
                                            <h4 class="">Single Outlet</h4>
                                    </button>
                                </div>
	                            <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                                    <button type="button" class="col-lg-12 col-md-12 col-xs-12 btn btn-danger outlet_type nextBtn" data-id="1+">
                                    		<div class="pull-right outlet_type_checked"></div>                                    
                                            <h3><i class="fa fa-delicious fa-2x"></i></h3>
                                            <h4 class="">Multiple Outlet</h4>
                                    </button>
                                </div>
                                <input type="hidden" name="outlet_type" id="outlet_type">
							</div>
						</div>                            
                </div>
            </div>                  

            <div class="row setup-content" id="step-3" style="display:none;">
                <div class="col-lg-6 col-lg-offset-3 col-md-10 col-md-offset-1 col-xs-12">
                    <div class="col-md-12">
                        <h4 class="text-muted">Config & localization</h4>
                        <div class="form-group has-feedback">
                            <div class="input-group">
                                <label for="store_name" class="control-label input-group-addon"><i class="fa fa-home fa-fw"></i> </label>
								<?php echo form_input(array('autocomplete' => 'off',"data-error" => "Store name field is required.", "data-minlength" => "5", "maxlength" => "25", "pattern" => "^[_A-z0-9]{1,}$", 'placeholder' => 'Store name', 'name' => 'store_name', 'id' => 'store_name',"class" => "form-control input-md", "required" => "required")) ?>
                            </div>
                            <div class="help-block with-errors"></div>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>    	
                        <div class="form-group has-feedback">
                            <div class="input-group">
                                <?php echo form_input(array("data-remote" => base_url('checkdomain'),'autocomplete' => 'off',"data-minlength" => "5", "data-error" => "This domain is not available.", "pattern" => "^[_A-z0-9]{1,}$", "maxlength" => "25",'placeholder' => 'Private domain', 'name' => 'subdomain', 'id' => 'subdomain',"class" => "form-control input-md","required" => "required")) ?>
                                <label for="subdomain" class="input-group-addon"><?php echo $domain ?>&nbsp;&nbsp;&nbsp;&nbsp;</label>
                            </div>
                            <div class="help-block with-errors"></div>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>    	
                        <div class="form-group has-feedback">
                            <div class="input-group">
                                <label for="contact_name" class="input-group-addon"><i class="fa fa-user fa-fw"></i></label>
                                <?php echo form_input(array('autocomplete' => 'off','placeholder' => 'Contact name', 'name' => 'contact_name', 'id' => 'contact_name',"class" => "form-control input-md", "required" => "required")) ?>
                            </div>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>    	
                        <div class="form-group has-feedback">
                            <div class="input-group">
                                <label for="contact_mobile" class="input-group-addon"><i class="fa fa-phone fa-fw"></i></label>
                                <?php echo form_input(array("data-minlength" => "10",'autocomplete' => 'off','placeholder' => 'Contact phone', "pattern" => "^[0-9]{1,}$", "data-error" => "10 digit mobile number", "maxlength" => "10", 'name' => 'contact_mobile', 'id' => 'contact_mobile',"class" => "form-control input-md","required" => "required")) ?>
                            </div>
                            <div class="help-block with-errors"></div>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>    	
                        <div class="form-group has-feedback">
                            <div class="input-group">
                                <label for="contact_email" class="input-group-addon"><i class="fa fa-envelope fa-fw"></i></label>
								<input type="email" maxlength="64" autocomplete="off" class="form-control input-md" name="contact_email" id="contact_email" placeholder="Contact Email" data-error="The email address is invalid" required>                                
                            </div>
                            <div class="help-block with-errors"></div>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>    	
                        <div class="form-group has-feedback">
                            <div class="input-group">
                                <label for="contact_password" class="input-group-addon"><i class="fa fa-lock fa-fw"></i></label>
                                <?php echo form_password(array("data-minlength" => "8",'autocomplete' => 'off', "data-error" => "Minimum of 8 characters", 'name' => 'contact_password', 'id' => 'contact_password','class' => 'form-control input-md','placeholder' => 'Login password',"required" => "required"))?>
                            </div>
                            <div class="help-block with-errors"></div>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>                        
                        
                        <div class="form-group has-feedback">
                            <div class="input-group">
                                <label for="contact_city" class="input-group-addon"><i class="fa fa-globe fa-fw"></i></label>
                                <?php echo form_input(array("data-location" => "location", "data-error" => "Location field is invalid", 'autocomplete' => 'off','placeholder' => 'Street name / city / country', 'name' => 'contact_city', 'id' => 'contact_city',"class" => "form-control input-md", "required" => "required")) ?>
                            </div>
                            <div class="help-block with-errors"></div>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>    	
                        <input type="hidden" name="zip" id="zip">
                        <input type="hidden" name="city" id="city">
                        <input type="hidden" name="state" id="state">
                        <input type="hidden" name="address_1" id="address_1">
                        <input type="hidden" name="address_2" id="address_2">
                        <input type="hidden" name="country" id="country">


                        <input type="hidden" name="rawOffset" id="rawOffset">
                        <input type="hidden" name="dstOffset" id="dstOffset">
                        <input type="hidden" name="timeZoneId" id="timeZoneId">
                        <input type="hidden" name="timeZoneName" id="timeZoneName">

                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <div class="form-group">
                            <div class="input-group">
                            <label for="contact_currency" class="input-group-addon">Currency</label>
                            <?php echo form_dropdown('contact_currency', $curr,'','class="form-control" id="contact_currency"') ?>
							</div>
                        </div>
                        <div class="row">
                        	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
								<?php
                                echo $cap_image;
                                ?>
							</div>
                        	<div class="col-lg-9 col-md-9 col-sm-6 col-xs-6">
		                        <input type="text" class="form-control input-md" name="captcha" placeholder="Type captcha(case sensitive)..."/>
							</div>                                
                        </div>
                        <h5 align="center">
                            On creating your account, you agree to the <a data-toggle="modal" data-target="#terms_modal" href="<?php echo base_url('terms') ?>">terms & conditions</a>
                        </h5>    
                        <h2>
                        	<button id="create_submit" class="btn btn-success btn-block btn-md pull-right text-uppercase" type="submit">start my store with <?php echo $cmp; ?></button>
                        </h2>
                        <div class="text-muted h2">
                            <br><hr>
                        </div>		            
                        
                    </div>
                </div>
            </div>
            <div class="modal fade" id="terms_modal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                    </div>
                </div>
            </div>
		<?php echo form_close(); ?>
</div>   
<div class="modal fade" id="modal-3">
    <div class="modal-dialog modal-sm">
        <div class="modal-content text-center">
            <img src="<?php echo base_url(APPPATH.'images/assets/posantic_logo.svg')?>" class="" width="150" height="75">
        	<h2><i class="fa fa-cog fa-spin fa-4x"></i></h2>
            <h3>Setting up your store</h3>
            <div class="container-fluid">
            
                <div id="myCarousel" class="carousel slide">
                    <div class="carousel-inner" role="listbox">
                        <div class="item active" align="center">
                        	<h4 class="btn btn-success"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Creating Account</h4>
                        </div>
                        <div class="item" align="center">
                            <h4 class="btn btn-success"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Setting up outlet</h4>
                        </div>
                        <div class="item" align="center">
                            <h4 class="btn btn-success"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Creating user and templates</h4>
                        </div>
                        <div class="item" align="center">
                            <h4 class="btn btn-success"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Configuring products</h4>
                        </div>
                        <div class="item" align="center">
                            <h4 class="btn btn-success"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Finalising cash register<br>settings</h4>
                        </div>
                    </div>
                </div>                
            	<hr>
            
			</div>
        </div>
    </div>
</div>
<?php
echo '<script type="text/javascript" src="'.base_url(JQUERY_FOR_SB).'"></script>'."\n";
echo '<script type="text/javascript" src="'.base_url(BS_MAIN_JS).'"></script>'."\n";
echo '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
echo '<script type="text/javascript" src="'.base_url('application/style/repository/js/validator.js').'"></script>'."\n";
echo '<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places"></script>'."\n";
echo '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'secure/secure_signup.js').'"></script>'."\n";
?>
</body>
</html>