<?php
if(isset($form_errors)) { 
	echo '<div class="alert alert-md alert-danger fade in">';
	echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
	echo '<span class="glyphicon glyphicon-remove-sign"></span> '.$form_errors;
	echo '</div>';
}
if(isset($form_success)) {
	echo '<div class="alert alert-sm alert-success fade in">';
	echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
	echo '<span class="glyphicon glyphicon-ok-sign"></span> '.$form_success;
	echo '</div>';
}
list($yy,$mm,$dd) = explode("-",$details['promo_start']);
$prom_start = checkdate($mm,$dd,$yy) ? $details['promo_start'] : 'ALL TIME';
list($e_yy,$e_mm,$e_dd) = explode("-",$details['promo_end']);
$prom_end = checkdate($e_mm,$e_dd,$e_yy) ? $details['promo_end'] : 'ALL TIME';
$outlet = $details['loc_id'] == "" ? "ALL OUTLETS" : $details['location'];
$daylight_saving = date("I");
$tmpl = array ( 'table_open'  => '<table class="table table-striped table-condensed table-curved">' );
$this->table->set_template($tmpl);			
$this->table->set_heading("Promotion Name","Customer Group","Outlet","Valid From", "Valid To","Updated at");

?>
<h4><i class="fa fa-bullhorn"></i> Promotion detail</h4>
<div class="well well-sm hidden-print">
	<?php echo anchor('promotion/edit/id/'.$prom_id,'<i class="fa fa-edit"></i> Edit Promotion','class = "btn btn-sm btn-success"')?>
    <div class="pull-right"><?php echo anchor('promotion/delete/id/'.$prom_id,'<i class="fa fa-trash-o"></i> Delete Promotion','class="btn btn-sm btn-danger" data-confirm="Delete this promotion? This cant be restored..."')?></div>
</div>
<div class="table-responsive">
<?php
$this->table->add_row($details['promo_name'],$details['group_name'],$outlet,$prom_start,$prom_end,unix_to_human(gmt_to_local(strtotime($details['prom_updated_at']),$timezone, $daylight_saving)));
echo $this->table->generate().'<br>';
?>
</div>
<div class="table-responsive">
<?php
$this->table->set_heading("Promotion Product","Margin%","Discount%","Retail Price", "Loyalty Set","Min Units","Max Units");
if(isset($sub_products['product_name']))
{
	foreach($sub_products['product_name'] as $key => $value)
	{
		$this->table->add_row(
								anchor('products/'.$sub_products['product_id'][$key],$sub_products['product_name'][$key],'class="btn btn-xs btn-default"'),
								$sub_products['margin'][$key],
								$sub_products['discount'][$key],
								$sub_products['retail_price'][$key],
								$sub_products['loyalty'][$key],
								$sub_products['min_qty'][$key],
								$sub_products['max_qty'][$key]
							);	
	}
} else {
	$this->table->add_row(array('data' => ':::No Products:::','colspan' => 7,'align' => 'center'));	
}
echo $this->table->generate();
echo $links;

?>
</div>