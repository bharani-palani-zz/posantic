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
<h4><i class="fa fa-folder-open fa-fw"></i>Product Categories</h4>
<h6 class="hidden-print">*Configure some categories or types that your products belong to. Your products are grouped by these categories on e-commerce integration and mobile ordering system</h6>
<hr>
<div class="well well-sm hidden-print">
    <?php echo anchor(base_url().'category/add','<i class="fa fa-plus fa-fw"></i>Add Category','class = "btn btn-primary btn-sm" data-toggle="modal" data-target="#ajax_cat_modal"') ?>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Available Categories</div>
    <div class="panel-body">
	<?php
    $tmpl = array ( 'table_open'  => '<table class="table table-striped table-curved">' );
    $this->table->set_template($tmpl);			
    $heading = array('data' => 'Category name','colspan' => 5);
    $this->table->set_heading($heading);
    if(count($cat_det['cat_id']) > 0)
    {
        foreach($cat_det['cat_id'] as $key => $val)
        {
            $this->table->add_row(
                $cat_det['cat_name'][$key],
                anchor(base_url('products/lookup?product_cat='.$cat_det['cat_id'][$key]),'View products','class="btn btn-xs btn-primary"'),
				array('align' => 'center','data' =>
                '<a data-toggle="modal" data-target="#ajax_cat_modal" class="btn btn-success btn-xs glyphicon glyphicon-edit" href="'.base_url('category/edit_cat/'.$cat_det['cat_id'][$key]).'"></a>'.'&nbsp;'.
                anchor(base_url('category/delete/id/'.$cat_det['cat_id'][$key]),'<i class="btn btn-danger btn-xs glyphicon glyphicon-remove"></i>','data-confirm="Delete This Category? This cant be restored..."')
				)
            );
        }
    } else {
        $this->table->add_row(array('data' => ':::No Categories Found:::','align' => 'center'));
    }
    echo $this->table->generate().'<br>';
	?>
	</div>
	<div class="panel-footer">
    	<small><i class="fa fa-pencil fa-fw"></i> Define different category types available for your products</small>
    </div>	
</div>    
<div class="modal fade" id="ajax_cat_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        </div>
    </div>
</div>
