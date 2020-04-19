<?php
$this->load->view('session/pos_session');
echo doctype('html5')."\n"; 
?>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="description" content="">
<meta name="author" content="">
<?php
echo '<link REL="SHORTCUT ICON" HREF="'.base_url().POS_IMG_ROOT.'browser_icon/icon.jpg'.'">'."\n";
echo "<title>".ucwords($this->session->userdata('pos_hoster_cmp'))." / Account</title>"."\n";
echo link_tag(BS3_MAIN_CSS)."\n";
echo link_tag(BS3_SIDEBOX_CSS)."\n";
echo link_tag(BS3_FA_CSS)."\n";
echo '<link rel="stylesheet" type="text/css" media="print" href="'.base_url(PRINT_CSS).'">'."\n";
echo '<link rel="stylesheet" type="text/css" href="'.base_url(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css').'">'."\n";
?>
</head>
<body>
	<div id="pageLoading"></div>
	<header>
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div class="menu-toggler visible-sm visible-xs">
                    <img src="<?php echo base_url(APPPATH.'images/assets/posantic_logo.svg')?>" class="img-thumbnail" onClick="location.href='<?php echo base_url(); ?>'">
                </div>
                <div class="btn-group btn-group-sm menu-toggler visible-lg visible-md" style="margin:7px;">
                    <img src="<?php echo base_url(APPPATH.'images/assets/posantic_logo.svg')?>" class="btn btn-default img-thumbnail brand" onClick="location.href='<?php echo base_url(); ?>'">
                    <button type="button" class="btn btn-default sidebar-toggler">
                        <i class="fa fa-lg fa-chevron-circle-left fa-fw"></i>
                    </button>                    
				</div>          
	            <ul class="nav navbar-top-links navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-heart fa-fw"></i> Themes <b class="caret"></b></a>
                    </li>                                
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i><?php echo ucwords($this->session->userdata('pos_display_user'));?> <b class="caret"></b></a>
                    </li>
                    <li>
                        <a href="<?php echo base_url();?>logout">
                            <i class="glyphicon glyphicon-log-out"></i> Logout
                        </a>
                    </li>
	            </ul>
			</div>
        </nav>        
	</header>
    <div id="wrapper">
        <nav role="navigation" style="margin-bottom: 0; margin-top: -3px;">
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse" id="sidebar-area">
                    <ul class="nav" id="sidebar">
						<li>
                            <a class="active-icon" href="<?php echo base_url('account') ?>"><i class="fa fa-cloud fw"></i> <span class="side-menu-title">Account</span></a>                        
                        </li>
                    </ul>
					<?php $this->load->view('top_page/usage',$usage) ?>
				</div>
			</div>
		</nav>                                                
        <div id="page-wrapper" class="container-fluid">
            <div class="alert alert-warning hidden-print text-center text-uppercase small" role="alert">
                <?php echo $expired_token ?>
            </div>
