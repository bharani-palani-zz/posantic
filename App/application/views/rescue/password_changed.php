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
<title><?php echo $cmp;?> - Password Changed</title>
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
					        <h3><span class="glyphicon glyphicon-ok-sign"></span> Password successfully changed</h3>
                        </div>
                        <div class="form-top-right">
                            <i class="fa fa-envelope fa-fw"></i>
                        </div>
                    </div>                            
                    <div class="form-bottom">
                        <div class="row">
                            <div class="col-md-12">                                    
                                <input type="hidden" id="hid_base_url" value="<?php echo base_url()?>">
						        <p><?php echo anchor(base_url(),'Sign in now','class="btn btn-lg btn-success btn-block"') ?></p>
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