<?php
if(isset($form_errors)) { 
	echo '<div class="alert alert-md alert-danger fade in">';
	echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
	echo $form_errors;
	echo '</div>';
}
if(isset($form_success)) {
	echo '<div class="alert alert-sm alert-success fade in">';
	echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
	echo $form_success;
	echo '</div>';
}
?>
<div class="modal fade" id="ajax_import_modal">
    <div class="modal-dialog modal-md">
            <div class="progress">
                <div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">
                  Loading... please wait
                </div>
            </div>
    </div>
</div>
<div class="jumbotron">
    <h2><i class="fa fa-upload fa-fw"></i> Import Products</h2> 
    <?php echo form_open_multipart(base_url().'products/import_action','id="bulk_upload_form" size="5000"') ?>
	<div class="row">
    	<div class="col-md-12 text-center">
        	<label class="btn btn-primary" for="my-file-selector">
            	<?php echo form_upload(array('name' => 'userfile', 'id' => 'my-file-selector', 'style' => 'display:none;')) ?>
                Choose file...
            </label>
        </div>
	</div>        
    <br>
	<div class="row">
    	<div class="col-md-12">
        	<?php echo form_submit('mass_upload','Import','class="btn btn-block btn-success" data-toggle="modal" data-target="#ajax_import_modal"') ?>
        </div>
    </div>    
    <h5>Maximum product limit is <?php echo defined($this->session->userdata('plan_cust_db_count')) ? '<big>&infin;</big>' : $this->session->userdata('plan_cust_db_count');?> for your outlet(s), <a href="<?php echo base_url().'products/csv_sample'?>" class="btn btn-xs btn-danger">Download</a> the sample to upload.</h5>
    <h5>Maximum of 1000 products(i.e., rows) can be uploaded on one shot. If exceeded upload is dropped</h5>
    <h5>Maximum file size - 2Mb</h5> 
    <?php echo form_close() ?>
</div>
<?php $this->load->view('products/bulk_import_tuts')?>
