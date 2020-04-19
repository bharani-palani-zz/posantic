<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<?php 
$this->load->model('admin_model');
$array = $this->admin_model->settings_model();
list($hotline,$email,$web,$cmp,$version_type,$version_year) = $array;
?>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo $cmp;?> - Forgot password</title>
<link rel="shortcut icon" href="<?php echo base_url(POS_IMG_ROOT.'browser_icon/icon.jpg');?>">
<?php 
echo link_tag(BS3_MAIN_CSS)."\n";
echo link_tag(BS3_FA_CSS)."\n";
echo link_tag('application/style/login/css/style.css')."\n";
?>
</head>
<body>
<!-- Top content -->
<div class="top-content">
    <div class="inner-bg">
        <div class="container">
            <div class="row">            
                <div class="col-sm-8 col-sm-offset-2 form-box">
                    <div class="form-top">
                        <div class="form-top-left">
                            <h3>Forgot your Password</h3>
                            <h5>Just feed your email or user name. We'll mail you the reset link!</h5>
                            <div class="row">
                                <div class="col-md-12 small">
                                    <?php 
									if(isset($form_errors)) { 
                                        echo '<div class="text-danger">';
										echo '<span class="glyphicon glyphicon-remove-sign"></span> '.$form_errors;
										echo '</div>';
									}
									if(isset($form_success)) {
                                        echo '<div class="text-danger">';
										echo '<span class="glyphicon glyphicon-ok-sign"></span> '.$form_success;
										echo '</div>';
									}
									?>                                    
                                </div>
                            </div>
                        </div>
                        <div class="form-top-right">
                            <i class="fa fa-lock fa-fw"></i>
                        </div>
                    </div>                            
                    <div class="form-bottom">
                        <div class="row">
                            <div class="col-md-12">                                    
                                <input type="hidden" id="hid_base_url" value="<?php echo base_url()?>">
                                	<?php
									echo form_open(base_url().'rescue/reset_password',array('class' => 'login-form'));
									?>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-envelope fa-fw"></span></span>
                                                <?php echo form_input(array('autocomplete' => 'off',"value" => set_value('email'),'name' => 'email', 'id' => 'email',"class" => "form-control input-lg",'placeholder' => 'Email / User name')) ?>
                                                <span class="input-group-addon"><span class="fa fa-user fa-fw"></span></span>
                                            </div>    
                                        </div>
					                    <?php if(form_error('email')) { ?><div class="col-sm-12 messageContainer text-danger"><span class="glyphicon glyphicon-remove"></span> <?php echo form_error('email') ?></div><?php } ?>
                                        <button type="submit" name="email_now" class="btn btn-lg btn-success btn-block">Send Email</button>
                                    <?php
									echo form_close();
									?>
                            </div>
                        </div>
                    </div>
                    <!--form bottom ends-->
                    <br>
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <div class="btn-group btn-group-xs">
                                <span class="btn btn-xs btn-default"><i class="fa fa-question-circle fa-fw"></i> Help</span>
                                <a href="http://support.posantic.com" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-support fa-fw"></i> Support</a>
                                <a href="mailto:<?php echo $email;?>" class="btn btn-primary btn-xs"><i class="fa fa-envelope-o fa-fw"></i> MailUs</a>
                                <a href="tel:<?php echo $hotline;?>" class="btn btn-primary btn-xs"><i class="fa fa-phone fa-fw"></i><?php echo $hotline;?></a>
                                <a href="http://<?php echo $web ?>" class="btn btn-xs btn-primary"><i class="fa fa-globe fa-fw"></i> <?php echo $cmp ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center">
                                <h6>Copyright <?php echo $version_year.' - '.date('Y');?><sup>&copy;</sup> Version <?php echo $version_type; ?>. All Rights Reserved.</h6>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo '<script type="text/javascript" src="'.base_url(JQUERY_FOR_SB).'"></script>'."\n";
echo '<script type="text/javascript" src="'.base_url(BS_MAIN_JS).'"></script>'."\n";
echo '<script type="text/javascript" src="'.base_url('application/style/login/js/jquery.backstretch.min.js').'"></script>'."\n";
echo '<script type="text/javascript" src="'.base_url('application/style/login/js/scripts.js').'"></script>'."\n";
?>
</body>
</html>