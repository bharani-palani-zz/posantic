<style>
    .ui-datepicker{ z-index:1151 !important; }
</style>
<?php
echo link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
echo link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
?>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4><i class="fa fa-database fa-fw"></i> Manage Storage Space</h4>
    <h6>Delete your history transcations and hold your current plan with its maximum storage limit avoiding upgrade to a maximum plan.</h6>
</div>
<div class="modal-body">
<?php echo form_open(base_url().'account/form_manage_space');?>
<div class="panel panel-default" id="manage_space_div">
    <div class="panel-heading">
    	<i class="fa fa-calendar fa-fw"></i> Select a date range
    </div>
    <input type="hidden" id="manage_space_url" value="<?php echo base_url('account/manage_space_for_date_range')?>">
    <div class="panel-body">
    	<div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="form-group input-group">
                    <label class="input-group-addon" for="range_before">Before</label>
                    <input type="text" name="range_before" id="range_before" class="form-control range_singles">
				</div>
			</div>
		</div>
    	<div class="row">
            <div class="col-lg-6 col-md-8">
                <div class="form-group input-group">
                    <label class="input-group-addon" for="range_start">Start</label>
                    <input type="text" name="range_start" id="range_start" class="form-control">
                    <label class="input-group-addon" for="range_end">End</label>
                    <input type="text" name="range_end" id="range_end" class="form-control">
                </div>
            </div>
        </div>
    	<div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="form-group input-group">
                    <label class="input-group-addon" for="range_after">After</label>
                    <input type="text" name="range_after" id="range_after" class="form-control range_singles">
				</div>
			</div>
		</div>
        <button type="button" class="btn btn-success" id="check_storage"><i class="fa fa-check"></i> Show</button>
        <button type="button" class="btn btn-danger" id="reset"><i class="fa fa-times"></i> Reset</button>

        <h2 align="center" id="loader"></h2>
        <div id="manage_div">
            <div id="manage_content" class="container_fluid">
                <div class="well">    
					<div class="container-fluid">
                        <em><h5 class="text-danger"><span class="trx_count"></span>&nbsp;<span class="trx_string"></span></h5></em>
                        <div class="checkbox form-group">
                            <label><input type="checkbox" id="risk_check">I understand the risk of deleting my records</label>
                        </div>
                        <button type="submit" id="manage_space" class="btn btn-block btn-danger disabled"><i class="fa fa-trash-o"></i>
                        Delete <span class="trx_count"></span> records
                        </button>
					</div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-footer">
		<h6>
        *Deletion includes sales and inventory activity records. Dont worry, your current products inventory will not be affected.
        Please note that all customer credits have been received during the date range before deleting. 
        Else reporting persuits obsolete data
        </h6>
	</div>
</div>    
<?php 
echo form_close();
echo $footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
echo $footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
echo $footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/manage_storage_space.js').'"></script>'."\n";
?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-remove"></i> Cancel</button>
</div>