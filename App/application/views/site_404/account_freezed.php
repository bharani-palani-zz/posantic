<?php
echo doctype('html5')."\n"; 
echo '<html>'."\n";
echo '<head>'."\n";
echo '<link REL="SHORTCUT ICON" HREF="'.base_url().POS_IMG_ROOT.'browser_icon/icon.jpg'.'">'."\n";
echo link_tag(BS3_MAIN_CSS)."\n";
echo link_tag(BS3_FA_CSS)."\n";
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />"."\n";
echo "<title>Account freezed</title>"."\n";

$domain = $_SERVER['HTTP_HOST'];
$domain_array = explode('.', $domain,2);
if(count($domain_array) == 2)
{
	$domain_str = $domain_array[1];
} else {
	$domain_str = $domain;
}
?>
<body>
<div class="container-fluid">
    <div align="center" style="color:#fc645f">
		<h1><i style="color:#000" class="fa fa-fast-backward fa-5x fa-fw"></i> <i class="fa fa-pause fa-5x fa-fw"></i> <i class="fa fa-fast-forward fa-5x fa-fw" style="color:#000"></i></h1>
        <h2 class="text-uppercase">Your account is freezed</h2>
    </div>
    <h3 align="center">
    	You could not access <span class="text-uppercase"><?php echo $cmp ?> App</span> while your account status is in "FREEZE" state
	</h3>        
    <h4 align="center">
    	<a href="tel:<?php echo $hotline;?>" class="btn btn-primary btn-xs"><i class="fa fa-phone fa-fw"></i>Contact</a> our support team or 
        <a class="btn btn-xs btn-success" href="mailto:support@posantic.com?subject=My account is freezed"><i class="fa fa-envelope-o fa-fw"></i> Mail</a> 
        us to resolve this issue.
    </h4>
    <h6 align="center" class="text-muted">Don't worry.. your data is safe on our servers</h6>
</div>    
</body>
<?php
echo '<script type="text/javascript" src="'.base_url(JQUERY_FOR_SB).'"></script>'."\n";
echo '<script type="text/javascript" src="'.base_url(BS_MAIN_JS).'"></script>'."\n";
?>
</html>