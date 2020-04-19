<?php
echo doctype('html5')."\n"; 
echo '<html>'."\n";
echo '<head>'."\n";
echo '<link REL="SHORTCUT ICON" HREF="'.base_url().POS_IMG_ROOT.'browser_icon/icon.png'.'">'."\n";
echo link_tag(BS3_MAIN_CSS)."\n";
echo link_tag(BS3_FA_CSS)."\n";
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />"."\n";
echo "<title>Invalid Merchant</title>"."\n";

$domain = $_SERVER['HTTP_HOST'];
$domain_array = explode('.', $domain,2);
if(count($domain_array) == 2)
{
	$domain_str = $domain_array[1];
} else {
	$domain_str = $domain;
}
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') { // if ssl connection
	$http = 'https';
} else {
	$http = 'http';
}
$root = $domain_str == 'localhost.com' ? $http.'://secure.localhost.com/posantic/App/signup' : $http.'://secure.'.$_SERVER['HTTP_HOST'].'/signup';
$this->session->sess_destroy();		
?>
<body>
<div class="h2">
    <div align="center">
        <img class="img-responsive" src="<?php echo base_url(POS_IMG_ROOT.'404/404_store.png')?>" />
    </div>
    <h3 align="center">This is not a valid merchant domain. May be this domain is now free for you</h3>
    <div align="center">
		<?php echo anchor($root,'<i class="fa fa-sign-in fa-fw"></i>Signup Now','class="btn btn-lg btn-success"') ?>
    </div>
</div>    
</body>
<?php
echo '<script type="text/javascript" src="'.base_url(JQUERY_FOR_SB).'"></script>'."\n";
echo '<script type="text/javascript" src="'.base_url(BS_MAIN_JS).'"></script>'."\n";
?>
</html>