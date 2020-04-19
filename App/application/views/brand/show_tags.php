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
<h4><i class="fa fa-tags fa-fw"></i>Product Tags</h4>
<h6 class="hidden-print">*Configure some tags for your products. A product can have multiple tags.</h6>
<h6>Eg: Tags like Shirt, T-Shirt, Lion Logo shirt, Yellow T-shirt, CSK shirt, IPL Shirts etc.. for product IPL CSK Jersey</h6>
<hr>
<div class="well well-sm hidden-print">
    <?php echo anchor(base_url().'tags/add','Add Tag','class = "btn btn-primary btn-sm" data-toggle="modal" data-target="#ajax_tag_modal"') ?>
</div>
<div class="panel panel-default">
    <div class="panel-heading"><strong>Available Tags</strong></div>
    <div class="panel-body">
	<?php
    $tmpl = array ( 'table_open'  => '<table class="table table-striped table-curved">' );
    $this->table->set_template($tmpl);			
    $heading = array('data' => 'Tag details','colspan' => 5);
    $this->table->set_heading($heading);
    if(count($tag_det['tag_id']) > 0)
    {
        foreach($tag_det['tag_id'] as $key => $val)
        {
            $this->table->add_row(
                $tag_det['tag_name'][$key],
                anchor(base_url('products/lookup?tag_id='.$tag_det['tag_id'][$key]),'View products','class="btn btn-xs btn-primary" data-placement="top" data-toggle="tooltip" title="aassa"'),
				array('align' => 'center','data' =>
                '<a data-toggle="modal" data-target="#ajax_tag_modal" href="'.base_url('tag/edit_tag/'.$tag_det['tag_id'][$key]).'"><i class="btn btn-success btn-xs glyphicon glyphicon-edit"></i></a>'.'&nbsp;'.
                anchor(base_url('tags/delete/id/'.$tag_det['tag_id'][$key]),'<i class="btn btn-danger btn-xs glyphicon glyphicon-remove"></i>','data-confirm="Delete This Tag? This cant be restored..."')
				)
            );
        }
    } else {
        $this->table->add_row(array('data' => ':::No Tags Found:::','align' => 'center'));
    }
    echo $this->table->generate().'<br>';
    ?>
    </div>
	<div class="panel-footer">
    	<small><i class="fa fa-pencil fa-fw"></i> Define numerous tags for your products.</small>
    </div>	
</div>

<div class="modal fade" id="ajax_tag_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        </div>
    </div>
</div>