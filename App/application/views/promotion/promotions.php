<h4><i class="fa fa-bullhorn"></i> Promotions</h4>
<h6 class="hidden-print">*Add some products to promotional offers based on outlet, validity date range & customer group.
    Once done, this product pricing will be effective on your sell screen</h6>
<?php
if(isset($form_uploaded)) {
	echo '<div class="alert alert-md alert-success">';
	echo $form_uploaded;
	echo '</div>';
}
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
?>
<div class="well well-sm hidden-print">
	<?php echo anchor('promotion/add','<i class="fa fa-plus"></i> Add Promotion','class = "btn btn-sm btn-primary"')?>
</div>
<div class="table-responsive">
    <div class="table-curved-div">
    <?php
    $daylight_saving = date("I");
    $tmpl = array ( 'table_open'  => '<table class="table table-striped table-condensed table-curved" id="prom_table">' );
    $this->table->set_template($tmpl);			
    $this->table->set_heading("Promotion","Customer Group","Outlet","Valid From", "Valid To","Created At","");
    if(count($promotions) > 0)
    {
        foreach($promotions['promotion_index'] as $key => $value)
        {
            list($yy,$mm,$dd) = explode("-",$promotions['promo_start'][$key]);
            $prom_start = checkdate($mm,$dd,$yy) ? $promotions['promo_start'][$key] : 'ALL TIME';
            list($e_yy,$e_mm,$e_dd) = explode("-",$promotions['promo_end'][$key]);
            $prom_end = checkdate($e_mm,$e_dd,$e_yy) ? $promotions['promo_end'][$key] : 'ALL TIME';
            $outlet = $promotions['location'][$key] == "" ? "ALL OUTLETS" : $promotions['location'][$key];
            $this->table->add_row(
            anchor('promotion/'.$promotions['promotion_index'][$key],$promotions['promo_name'][$key],'class="btn btn-sm btn-default"'),
            $promotions['group_name'][$key],
            $outlet,$prom_start,$prom_end,
            unix_to_human(gmt_to_local(strtotime($promotions['prom_updated_at'][$key]),$timezone, $daylight_saving)),
            array('data' => '<div class="btn-group btn-group-sm">'.anchor('promotion/edit/id/'.$promotions['promotion_index'][$key],'<i class="fa fa-edit"></i> Edit','class="btn btn-success"').anchor('promotion/delete/id/'.$promotions['promotion_index'][$key],'<i class="fa fa-times"></i> Delete','class="btn btn-danger" data-confirm="Delete This Promotion? This cant be restored..."'),'class' => 'no-print')).'</div>';
        }
    } else {
        $this->table->add_row(array('data' => '<p>:::No Promotions Set Yet:::</p>','colspan' => 7,'align' => 'center'));		
    }
    echo $this->table->generate();
    ?>
    </div>
</div>
