<?php
if(isset($form_errors)) {
	echo '<div class="alert alert-sm alert-danger fade in">';
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
<h4><i class="fa fa-shield fa-fw"></i>Product Brands</h4>
<h6 class="hidden-print">*Configure some brands that your products inherit. Your products are grouped by brands on e-commerce integration and mobile ordering system</h6>
<hr>
<div class="well well-sm hidden-print">
    <?php echo anchor(base_url().'brand/add','<i class="fa fa-plus fa-fw"></i>Add Brand','class = "btn btn-primary btn-sm" data-toggle="modal" data-target="#ajax_brand_modal"') ?>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Available Brands</div>
    <div class="panel-body">
		<?php
        $tmpl = array ( 'table_open'  => '<table class="table table-striped table-curved">' );
        $this->table->set_template($tmpl);			
        $heading = array('data' => 'Brand name','colspan' => 5);
        $this->table->set_heading($heading);
        if(count($brand_det['brand_index']) > 0)
        {
            foreach($brand_det['brand_index'] as $key => $brand_val)
            {
                $this->table->add_row(
                    $brand_det['brand_name'][$key],
                    anchor(base_url('products/lookup?product_brand='.$brand_det['brand_index'][$key]),'View products','class="btn btn-xs btn-primary"'),
					array('align' => 'center','data' =>
                    '<a data-toggle="modal" data-target="#ajax_brand_modal" href="'.base_url('brand/edit_form/'.$brand_det['brand_index'][$key]).'"><i class="btn btn-success btn-xs glyphicon glyphicon-edit"></i></a>'.'&nbsp;'.
                    anchor(base_url('brand/delete/id/'.$brand_det['brand_index'][$key]),'<i class="btn btn-danger btn-xs glyphicon glyphicon-remove"></i>','data-confirm="Delete This Brand? This cant be restored..."')
					)
                );
            }
        } else {
            $this->table->add_row(array('data' => ':::No Brands Found:::','align' => 'center'));
        }
        echo $this->table->generate().'<br>';
        ?>
    </div>
	<div class="panel-footer">
    	<small><i class="fa fa-pencil fa-fw"></i> Define numerous brands available in your outlets</small>
    </div>	
</div>
<div class="modal fade" id="ajax_brand_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        </div>
    </div>
</div>