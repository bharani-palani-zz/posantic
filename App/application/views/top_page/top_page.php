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
echo '<link REL="SHORTCUT ICON" HREF="'.base_url().POS_IMG_ROOT.'browser_icon/icon.png'.'">'."\n";
echo "<title>".ucwords($this->session->userdata('pos_hoster_cmp'))." / ".$view['title']."</title>"."\n";
echo link_tag(BS3_MAIN_CSS)."\n";
echo link_tag(BS3_METISMENU_CSS)."\n";
echo '<link rel="stylesheet" type="text/css" media="print" href="'.base_url(PRINT_CSS).'">'."\n";
if(!empty($style))
{
	foreach($style as $sty)
	{
		echo $sty;
	}
}
echo link_tag(BS3_XL_CSS)."\n";
echo link_tag(BS3_FA_CSS)."\n";
if(!is_null($top_menu['theme']))
{
	$theme_url = $top_menu['theme'];	
} else {
	$theme_url = BS3_SIDEBOX_CSS;	
}
echo '<link href="'.base_url($theme_url).'" rel="stylesheet" type="text/css" id="toggle_stylesheet" />';

// user image
$ext = '';
$root = APPPATH.'user_images/'.md5($this->session->userdata('acc_no')).'/users/'.$top_menu['user_id'].'_thumb';
foreach (glob($root.".*") as $filename) {
	$ext = substr($filename,-3);
}
$image_href = $root.'.'.$ext;
if(file_exists($image_href))
{
	$image_href = '<img height="70" width="70" class="img-circle" src="'.base_url().$image_href.'?random='.time().'" />';
} else {
	$image_href = '<button type="button" class="btn btn-circle btn-xl btn-success user-btn"><i class="fa fa-user fa-2x"></i></button>';								
}

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
                <input type="hidden" id="theme_user" value="<?php echo $this->session->userdata('user_id') ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" id="theme_url" value="<?php echo base_url('setup/theme_post') ?>">                      
                <input type="hidden" id="base_url" value="<?php echo base_url() ?>">                      
	            <ul class="nav navbar-top-links navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i id="theme_icon" class="fa fa-heart fa-fw"></i> Themes <b class="caret"></b></a>
                        <ul class="dropdown-menu" id="theme_options">
                            <li><a data-root="application/style/repository/css/sidebar.css" class="theme_selector"><i style="color:#f15c58;" class="fa fa-lg fa-fw fa-circle"></i> Candy Red</a></li>
                            <li><a data-root="application/style/repository/css/eco_green.css"class="theme_selector"><i style="color:#5cb85c;" class="fa fa-lg fa-fw fa-circle"></i> Eco Green</a></li>	                            
                            <li><a data-root="application/style/repository/css/sea_blue.css"class="theme_selector"><i style="color:#337ab7;" class="fa fa-lg fa-fw fa-circle"></i> Sea Blue</a></li>	                            
                            <li><a data-root="application/style/repository/css/orchid_purple.css"class="theme_selector"><i style="color:#7602ae;" class="fa fa-lg fa-fw fa-circle"></i> Orchid Purple</a></li>	  
                        </ul>
                    </li>             
                    <!--waiting: ajax sales including user, mail and sale totals-->                   
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i><?php echo ucwords($this->session->userdata('pos_display_user'));?> <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li class="text-center">
                                <?php echo $image_href ?>
                            </li>
                            <li class="text-center">
                            	<p>
                                <div class="container-fluid"><b><?php echo $this->session->userdata('pos_user_mail') ?></b></div>
                                <div class="text-center">(<?php echo $this->session->userdata('pos_user') ?>)</div>
                                </p>
                            </li>
                            <li class="small text-center text-muted">
                            	 <p>
                                 	<div>Today's sales <?php echo $s = rand(1000,10000) / 100 ?></div>
									<div>This month sales <?php echo $s * rand(20,31) ?></div>
                                </p>
                            </li>
                            <li>
                                <div class="container-fluid">
                                	<div class="text-muted small">Todays target</div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped active" role="progressbar"
                                        aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:40%">
                                          40%
                                        </div>
                                    </div>                      

                                	<div class="text-muted small">This week target</div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped active" role="progressbar"
                                        aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:60%">
                                          60%
                                        </div>
                                    </div>                      

                                	<div class="text-muted small">This month target</div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped active" role="progressbar"
                                        aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width:80%">
                                          80%
                                        </div>
                                    </div>                      
                                </div>      
                            </li>
                        </ul>
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
        <nav role="navigation" style="margin-bottom: 0; margin-top: -3px;" class="hidden-print">
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse" id="sidebar-area">                
                    <ul class="nav" id="sidebar">
						<?php foreach($top_menu['menu'] as $menu_name => $sub_array) 
                        {
                            if($menu_name == 'administrator'){
                                $revised_menu = ucwords(str_replace(array($menu_name)," ",$role_name)); 
                                $glyphicon = 'fa fa-user fa-fw';
                            } else {
                                $revised_menu = ucwords($menu_name);
                                $glyphicon = $sub_array['glyphicon'][0];
                            }
                            if($sub_array['root'] == 'PARENT' && $menu_name != 'sale')
                            {
                                $p_href = base_url().$sub_array['href'][0];	
                            } else if($sub_array['root'] == 'PARENT' && $menu_name == 'sale'){
                                $p_href = base_url();					
                            } else if($sub_array['root'] == 'EXTINCT'){
                                $p_href = $sub_array['href'][0];					
                            } else {
                                $p_href = "#";	
                            }
                            $active = $p_href == current_url() ? 'active' : '';
                        ?>
                        <?php  
                        if(count($sub_array['href']) > 1)
                        {
							?>
							<li>
								<a class="dropdown-collapse active-icon" href="<?php echo $p_href ?>" target="<?php echo $sub_array['target'] ?>"><i class="<?php echo $glyphicon ?>"></i> <span class="side-menu-title"><?php echo $revised_menu ?></span><span class="fa arrow"></span></a>
								<ul id="<?php echo $menu_name ?>" <ul class="nav nav-second-level">
									<?php foreach($sub_array['named'] as $key => $value) { ?>
										<li><a href="<?php echo base_url().$sub_array['href'][$key];?>"><i class="<?php echo $sub_array['glyphicon'][$key]?>"></i>  <?php echo $value;?></a></li>
									<?php } ?>
								</ul>	
							</li>
							<?php } else { ?>
							<li>
								<a class="active-icon" href="<?php echo $p_href ?>" target="<?php echo $sub_array['target'] ?>"><i class="<?php echo $glyphicon ?>"></i> <span class="side-menu-title"><?php echo $revised_menu?></span></a>
							</li>
							<?php	
							}
                        }
                        ?>
					</ul>
                        <?php $this->load->view('top_page/usage',$top_menu['usage']) ?>
				</div>
			</div>
		</nav>
        <div id="page-wrapper" class="container-fluid">
	        <div class="col-xl-10 col-xl-offset-1">
			<?php if($top_menu['notify']['show']) { ?>
                <div class="alert alert-<?php echo $top_menu['notify']['stat']?> hidden-print text-center text-uppercase small" role="alert">
                    <?php echo $top_menu['notify']['string']; ?>     
                </div>
            <?php } else { ?>    
            <br>        	
            <?php } ?>

