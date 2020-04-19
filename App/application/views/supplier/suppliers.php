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
?>
<h4><i class="fa fa-truck"></i> Suppliers</h4>
<div class="table-responsive">
    <div class="table-curved-div">
    <?php
    $tmpl = array (
        'table_open'   => '<table class="table table-striped table-condensed table-curved" id="supplier_table">',
        'table_close'   => '</table>'
    );
    $this->table->set_template($tmpl);			
    $heading = array('Company','Contact Person','Mobile','Email','Phone','');
    $this->table->set_heading($heading);
    foreach($all_suppliers['supp_id'] as $key => $value)
    {
        $this->table->add_row(
            $all_suppliers['cmp_name'][$key],
            $all_suppliers['auth_pers'][$key],
            $all_suppliers['mobile'][$key],
            $all_suppliers['email'][$key],
            $all_suppliers['ll'][$key],
            '<div class="btn-group btn-group-sm">'.anchor('products/lookup?supplier='.$all_suppliers['supp_id'][$key],'<i class="fa fa-cube fa-fw"></i> Products','class="btn btn-sm btn-default"').anchor('supplier/'.$all_suppliers['supp_id'][$key].'/edit','<i class="fa fa-edit fa-fw"></i> Edit','class="btn btn-sm btn-success"').anchor('supplier/'.$all_suppliers['supp_id'][$key].'/show','<i class="fa fa-bars fa-fw"></i> Show','class="btn btn-sm btn-danger"').'</div>'
            );
    }
    echo $this->table->generate()."\n";
    
    ?>
    </div>
</div>
