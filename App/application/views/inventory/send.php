<!--validation done in controller-->
<?php echo form_open(base_url().'inventory/send_email/'.$transfer_id,array('id' => 'myform','size' => '5000')); ?>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4><i class="fa fa-share"></i> Send Order</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        <div class="input-group">
            <label for="rec" class="input-group-addon"><i class="fa fa-user"></i> Recipient Name</label>
            <?php echo form_input(array('autocomplete' => 'off','name' => 'rec', 'id' => 'rec',"class" => "form-control input-sm", "value" => set_value('rec', $supp_details['auth_pers']))) ?>
        </div>
	</div>
    <div class="form-group">
        <div class="input-group">
            <label for="id" class="input-group-addon"><i class="fa fa-envelope"></i> Email</label>
            <?php echo form_input(array('autocomplete' => 'off','name' => 'id', 'id' => 'id',"class" => "form-control input-sm", "value" => set_value('id',$supp_details['email']))) ?>
        </div>
	</div>
    <div class="form-group">
        <div class="input-group">
            <label for="cc" class="input-group-addon"><i class="fa fa-envelope"></i> CC</label>
            <?php echo form_input(array('autocomplete' => 'off','name' => 'cc', 'id' => 'cc',"class" => "form-control input-sm", "value" => set_value('cc'))) ?>
        </div>
	</div>
    <div class="form-group">
        <div class="input-group">
            <label for="sub" class="input-group-addon"><i class="fa fa-comment-o"></i> Subject</label>
            <?php echo form_input(array('autocomplete' => 'off','name' => 'sub', 'id' => 'sub',"class" => "form-control input-sm", "value" => set_value('sub'))) ?>
        </div>
	</div>
    <div class="form-group">            
		<?php echo form_textarea(array('autocomplete' => 'off','style' => 'resize:none','rows' => 5,'cols' => 50,'name' => 'msg', 'id' => 'msg',"class" => "form-control", "placeholder" => 'Message', "value" => set_value('msg'))) ?>
	</div>
    <?php
	$check1 = array(
	'name'        => 'show_supp',
	'id'          => 'show_supp',
	'value'       => 30,
	'checked'     => TRUE,
	);						
	?>
    <div class="checkbox">
      <label><?php echo form_checkbox($check1) ?>Show supplier price on mail</label>
    </div>
</div>    
<div class="modal-footer">
    <button type="submit" class="btn btn-sm btn-success insert_ajax_btn" data-for-zone="brand" data-for-select="#product_brand"><i class="fa fa-share"></i> Send</button>
    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fa fa-remove"></i> Cancel</button>
</div>
<?php echo form_close() ?>