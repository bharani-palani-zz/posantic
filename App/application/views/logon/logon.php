<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo $cmp;?> - Cloud based point of sale</title>
<link rel="shortcut icon" href="<?php echo base_url(POS_IMG_ROOT.'browser_icon/icon.png');?>">
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
                            <h3><?php echo $cmp ?></h3>
                            <h4><?php echo $this->session->userdata('subdomain'); ?></h4>
                            <div class="row">
                                <div class="col-md-12 small">
                                    <?php 
                                    if($this->session->flashdata('Notify')) {
                                        echo '<div class="text-danger">';
                                        echo '<span class="glyphicon glyphicon-remove-sign"></span> '.$this->session->flashdata('Notify');
                                        echo '</div>';
                                    }
                                    if($this->session->flashdata('Notify2')) {
                                        echo '<div class="text-danger">';
                                        echo '<span class="glyphicon glyphicon-remove-sign"></span> '.$this->session->flashdata('Notify2');
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-top-right">
                            <i class="fa fa-shopping-cart fa-fw"></i>
                        </div>
                    </div>                            
                    <div class="form-bottom">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 align="center">What's New ???</h4>
                                <center>
                                    <div id="myCarousel" class="carousel slide" data-ride="carousel">
                                        <div class="carousel-inner" role="listbox">
                                            <div class="item active">
                                            	<?php
												$width = 150;
												$height = 150;
												?>
    	                                        <img class="img-circle" src="<?php echo base_url()?>application/images/login-img/report.jpg" width="<?php echo $width ?>" height="<?php echo $height ?>" alt="Chania">
	                                            <div class="text-center">
    	                                            Enhanced reporting
                                                </div>                                            
                                            </div>
                                            
                                            <div class="item">
	                                            <img class="img-circle" src="<?php echo base_url()?>application/images/login-img/ecom.jpg" width="<?php echo $width ?>" height="<?php echo $height ?>" alt="Chania">
	                                            <div class="text-center">
    	                                            Ecommerce ready
                                                </div>                                            
                                            </div>
                                            
                                            <div class="item">
	                                            <img class="img-circle" src="<?php echo base_url()?>application/images/login-img/scanner.jpg" width="<?php echo $width ?>" height="<?php echo $height ?>" alt="Chania">
	                                            <div class="text-center">
    	                                            Barcode scan
                                                </div>                                            
                                            </div>
                                            
                                            <div class="item">
	                                            <img class="img-circle" src="<?php echo base_url()?>application/images/login-img/touch.jpg" width="<?php echo $width ?>" height="<?php echo $height ?>" alt="Chania">
	                                            <div class="text-center">
    	                                            Ease touch
                                                </div>                                            
                                            </div>                                        
                                        </div><br><br>
                                        <div>
                                            <ol class="carousel-indicators">
                                            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                                            <li data-target="#myCarousel" data-slide-to="1"></li>
                                            <li data-target="#myCarousel" data-slide-to="2"></li>
                                            <li data-target="#myCarousel" data-slide-to="3"></li>
                                            </ol>
                                        </div>
                                    </div>
                                </center>
                            </div>
                            <div class="col-md-6"> 
                                <h2>Sign in</h2>
                                   
                                <?php 
                                $qs = strlen($_SERVER['QUERY_STRING']) > 0 ? "?".$_SERVER['QUERY_STRING'] : "";
                                $redirect = $this->session->userdata('redirect_URL') ? $this->session->userdata('redirect_URL').$qs : current_url().$qs;
                                echo form_open(base_url().'signin',array('class' => 'login-form'));
                                echo form_hidden('redirect_URL',$redirect);				
                                ?>                            
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-user fa-fw"></span></span>
                                                <input type="text" name="username" id="username" placeholder="Email / Username" class="form-username form-control" id="username">
                                                <span class="input-group-addon"><span class="fa fa-envelope fa-fw"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                        
                                <input type="hidden" id="hid_base_url" value="<?php echo base_url()?>">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-lock fa-fw"></span></span>
                                                <input type="password" name="pwd" placeholder="Password" class="form-password form-control" id="pwd">
                                            </div>    
                                        </div>
                                    </div>
                                </div>                                        
                                <button type="submit" name="log_in" class="btn btn-lg btn-success btn-block">Sign in!</button>
                                <?php echo form_close();?>
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <a href="<?php echo base_url('rescue/forgot_password')?>" class="btn btn-xs btn-danger">Forgot Password</a>
                                    </div>
                                </div>
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