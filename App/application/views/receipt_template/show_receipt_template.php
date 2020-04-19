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
<h4><span class="glyphicon glyphicon-text-size"></span> Receipt Templates</h4>
<hr>
<div class="panel panel-default">
    <div class="panel-heading">Available Bill Templates</div>
    <div class="panel-body">
        <div class="table-responsive">
        <?php
        $status = array(20 => 'No',10 => 'Yes');
        $tmpl = array (
            'table_open'   => '<table class="table table-striped table-curved">',
        );
        $this->table->set_template($tmpl);			
        $heading = array('Template Name','Header Type','Show Discounts','Show Loyalty','Show address','Show Promotions','Show comments in bill','&nbsp;');
        $this->table->set_heading($heading);
        foreach($template_id as $key => $temp_id)
        {
            $this->table->add_row(
			$template_name[$key],$bill_header_name[$key],$status[$show_disc_bill[$key]],$status[$show_loyalty_bill[$key]],$status[$show_address_bill[$key]],$status[$show_promotions[$key]],$status[$show_bill_quotes[$key]],
			anchor('setup/receipt_template/'.$temp_id.'/edit','<span class="glyphicon glyphicon-hand-right"></span>','class="btn btn-success btn-circle" data-toggle="popover" data-placement="left"  data-content="View / Edit"')
			);				
        }
        echo $this->table->generate();
        ?>
        </div>
    </div>
    <div class="panel-footer"><i class="fa fa-hand-o-right fa-fw"></i>Set your prefered bill to look like</div>
</div>        	