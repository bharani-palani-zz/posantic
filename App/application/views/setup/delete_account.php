<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
	<h4><i class="fa fa-trash-o fa-fw"></i> Delete Account</h4>
</div>
<div class="modal-body">
<?php
$reason_options = array(
					'I am not happy with your themes' => 'I am not happy with your themes',
					'I am not happy with your design' => 'I am not happy with your design',
					'My requirements are not fulfilled' => 'My requirements are not fulfilled',
					'Support level was not up to my expectation' =>'Support level was not up to my expectation',
					'Pricing is not comprehensive' => 'Pricing is not comprehensive',
					'Pricing is expensive' => 'Pricing is expensive',
					'I am shutting down my business' => 'I am shutting down my business',
					'I just trespassed' => 'I just trespassed',
					'Other reason' => 'Other reason',
					);
echo form_open(base_url().'account/form_delete_account',array('id' => 'form_delete_account'));
?>
<div class="panel panel-default">
    <div class="panel-heading">
    	<i class="fa fa-thumbs-o-up"></i> Your Suggestions are worth us million
    </div>
    <div class="panel-body">
	    <div class="row">
		    <div class="col-lg-6 col-md-6">
                <div class="form-group input-group">
                    <label for="reason_string" class="input-group-addon">Reason</label>
                    <?php echo form_dropdown('reason_string', $reason_options, '', 'id="reason_string" class="form-control input-sm"') ?>
                </div>
                <label for="comments">Few words to share with </label>
                <div class="form-group">
				<?php
                echo form_textarea(array(
                          'name'        => 'comments',
                          'id'          => 'comments',
						  'rows'		=> '5',
						  'style'		=> 'resize:none',
                          'class' 		=> 'form-control',
                      ));
                ?>	
                </div>
			</div>
		</div>        
        <button type="submit" name="delete_account" id="delete_account" class="btn btn-outline btn-danger loading_modal"><i class="fa fa-ban"></i> Delete Account</button> 
	</div>
    <div class="panel-footer">
	<i class="fa fa-frown-o fa-fw"></i> We are sorry to see you going away from <?php echo $this->session->userdata('pos_hoster_cmp') ?>
	</div>
</div>    
<?php
echo form_close();
?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-remove"></i> Cancel</button>
</div>
