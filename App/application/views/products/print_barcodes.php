<?php echo doctype('html5');?>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Printing Barcodes...</title>
<head>
<style type="text/css">
@CHARSET "UTF-8";
@media all {
	.page-break	{ display: none;}
}
@media print {
	.page-break	{ display: block; page-break-after:always;}
}
.straight {
	padding: 10px 10px 10px 10px;
}
.caption th{
	font-size:22px;	
}
</style>
</head>
<body onLoad="javascript:window.print();window.close();">
<center>
<?php 
$this->load->helper('text');
$wh = empty($caption) ? 99 : 80;
if($printer == 1)
{
	for($i=1;$i<=$count;$i++) {	?>
		<img width="<?php echo $wh ?>%" height="<?php echo $wh ?>%" src="<?php echo base_url() ?>barcode/barcode_controller/render_barcode/<?php echo $text.'/'.$barHeight.'/'.$font.'/'.$codetype; ?>">
        <?php if(count($caption) > 0) { ?>
        <table class="caption">
        	<tr>
            <?php for($j=0;$j<count($caption);$j++){ $del = ($j + 1 != count($caption)) ? '&nbsp;|' : ''?>
                <th><?php echo $caption[$j] .$del ?></th>
            <?php } ?>
            </tr>
        </table>            	
        <?php } ?>
        <?php if($i != $count) {?>
            <div class="page-break"></div>
        <?php } ?>
	<?php } 
} else { 
		for($i=1;$i<=$count;$i++) { ?>
		<img width="<?php echo $wh ?>%" height="<?php echo $wh ?>%" src="<?php echo base_url() ?>barcode/barcode_controller/render_barcode/<?php echo $text.'/'.$barHeight.'/'.$font.'/'.$codetype; ?>">
        <?php if(count($caption) > 0) { ?>
        <table class="caption">
        	<tr>
            <?php for($j=0;$j<count($caption);$j++){ $del = ($j + 1 != count($caption)) ? '&nbsp;|' : ''?>
                <th><?php echo $caption[$j] .$del ?></th>
            <?php } ?>
            </tr>
        </table>            	    	
        <?php } ?>
        <div class="straight"></div>
<?php 	} 
} 
?>    
</center>
</body>
</html>