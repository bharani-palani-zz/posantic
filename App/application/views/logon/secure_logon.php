<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo $cmp;?> / Secure login</title>
<link rel="shortcut icon" href="<?php echo base_url(POS_IMG_ROOT.'browser_icon/icon.jpg');?>">
<?php 
echo link_tag(BS3_MAIN_CSS)."\n";
echo link_tag(BS3_FA_CSS)."\n";
echo link_tag('application/style/login/css/style.css')."\n";
$domain = preg_replace('/^www\./', '', $web);

?>
</head>
<body>
<!-- Top content -->
<div class="top-content">
    <div class="inner-bg">
        <div class="container">
            <div class="row">            
                <div class="col-sm-6 col-sm-offset-3 col-xs-12 form-box">
                    <div class="form-top">
                        <div class="form-top-left">
                            <h3><img width="150" height="75" src="<?php echo base_url(APPPATH.'images/assets/posantic_logo.svg')?>" class="img-responsive" ></h3>
                            <div class="row">
                                <div class="small">
                                    <?php 
                                    if($this->session->flashdata('find_error')) {
                                        echo '<div class="col-lg-12 text-danger">';
                                        echo '<span class="glyphicon glyphicon-remove-sign"></span> '.$this->session->flashdata('find_error');
                                        echo '</div>';
                                    }									
                                    ?>
									<?php if(form_error('store_name')) { ?><div class="col-lg-12 text-danger"><span class="glyphicon glyphicon-remove-sign"></span> <?php echo form_error('store_name') ?></div><?php } ?>                                
                                </div>
                            </div>
                        </div>
                    </div>                            
                    <div class="form-bottom">
						<?php echo form_open(base_url().'find_store',array('class' => '')); ?>
                        <div class="row">
                            <input type="hidden" id="hid_base_url" value="<?php echo base_url()?>">                        
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <?php echo form_input(array('autocomplete' => 'off', 'placeholder' => 'Store name', 'name' => 'store_name', 'id' => 'store_name',"class" => "form-control input-md", "value" => set_value('store_name'))) ?>
                                        <label for="store_name" class="input-group-addon bg-danger"><?php echo $domain ?></label>
                                    </div>
                                </div>     
                                <div class="row">
                                    <div class="col-lg-4 pull-right">
	                                    <button type="submit" name="log_in" class="btn btn-md btn-success text-uppercase"><i class="fa fa-keyboard-o fa-fw"></i> Start</button>
                                    </div>
                                </div>
                             </div>
                        </div>
                        <?php echo form_close() ?>
                    </div>
                    <!--form bottom ends-->
                    <br>
                    <div class="row">
                        <div class="col-xs-12 text-center small">
                            Don't have an account ??? <a href="<?php echo base_url('signup')?>" class="btn btn-xs btn-danger text-uppercase">Try <?php echo $cmp ?> for free</a>
                        </div>                    
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