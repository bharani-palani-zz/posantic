<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo $cmp;?> / Signup</title>
<link rel="shortcut icon" href="<?php echo base_url(POS_IMG_ROOT.'browser_icon/icon.jpg');?>">
<?php 
echo link_tag(BS3_MAIN_CSS)."\n";
echo link_tag(BS3_FA_CSS)."\n";
$domain = preg_replace('/^www\./', '', $web);
?>
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="https://<?php echo $web ?>"><small class="text-uppercase"><?php echo $cmp ?></small></a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="http://support.posantic.com" target="_blank" class=""><i class="fa fa-support fa-fw"></i> Support</a></li>
                <li><a href="mailto:<?php echo $email;?>" class=""><i class="fa fa-envelope-o fa-fw"></i> MailUs</a></li>
                <li><a href="tel:<?php echo $hotline;?>" class=""><i class="fa fa-phone fa-fw"></i><?php echo $hotline;?></a></li>
                <li><a href="<?php echo base_url('startup') ;?>" class=""><i class="fa fa-sign-in fa-fw"></i> Sign in with existing account</a></li>
            </ul>
        </div>
    </div>
</nav>
<br>
<br>
<div class="jumbotron">
	<h2 align="center"><span class="label label-default">Oops.. This is not a valid page</span></h2>
	<h3 align="center" class="text-danger">It must be typographic error</h3>
	<h2 align="center"><?php echo anchor(base_url('startup'),'<i class="fa fa-arrow-circle-left fa-fw"></i>Back to home','class="btn btn-lg btn-success"') ?></h2>
</div>
<?php
echo '<script type="text/javascript" src="'.base_url(JQUERY_FOR_SB).'"></script>'."\n";
echo '<script type="text/javascript" src="'.base_url(BS_MAIN_JS).'"></script>'."\n";
?>
</body>
</html>