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
<h4><i class="fa fa-cloud"></i> Account</h4>
<div class="alert alert-sm alert-success fade in">
	<?php 
	$hold_tooltip = '<br><p>'.$outlet_str.'</p>'; 
	$hold_tooltip .= '<p>'.$reg_str.'</p>'; 
	$hold_tooltip .= '<p>'.$prd_str.'</p>'; 
	$hold_tooltip .= '<p>'.$user_str.'</p>'; 
	$hold_tooltip .= '<p>'.$cust_str.'</p>'; 
	?>
    <button data-placement="bottom" data-original-title="<?php echo $hold_tooltip ?>" class="btn btn-xs btn-default tooltips">Your current data</button> is safe on our servers.
    <?php echo $recomend_str ?>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" id="find_plan_pricing_url" value="<?php echo base_url('account/find_plan_pricing') ?>">
<input type="hidden" id="find_term_pricing_url" value="<?php echo base_url('account/find_term_pricing') ?>">
<input type="hidden" id="find_code_discount_url" value="<?php echo base_url('account/find_code_discount') ?>">
<div class="row" id="account_container">
	<?php
    foreach($plan_summary as $plan_array) {
		$rec_btn = $plan_array['plan_id'] == $rec_plan_key ? '<p align="center"><span class="label label-success">Recommended</span></p>' : '&nbsp;';
	?>
    <div class="col-lg-3 col-md-3 col-sm-6" style="padding:10px;" id="<?php echo $plan_array['plan_id'] ?>">
        <div class="header">
        	<?php echo $rec_btn ?>
            <h3 align="center"><?php echo $plan_array['plan_code'] ?></h3>
            <h3 align="center"><sup><?php echo $symbol ?></sup><?php echo number_format($plan_array['monthly_price']) ?> <sub><small> / Month</small></sub></h3>
            <?php
			$save_q = number_format(($plan_array['monthly_price'] * 3) - ($plan_array['quarter_early_disc'] * 3) , 0);
			$save_h = number_format(($plan_array['monthly_price'] * 6) - ($plan_array['half_early_disc'] * 6) , 0);
			$save_y = number_format(($plan_array['monthly_price'] * 12) - ($plan_array['yearly_disc'] * 12) , 0);

            $pricing_tooltip = '<br><p>Quarterly '.number_format($plan_array['quarter_early_disc']).' <sup>'.$symbol.'</sup>/ <sub><small>MONTH</small></sub></p>';
            $pricing_tooltip .= '<p>Biannually '.number_format($plan_array['half_early_disc']).' <sup>'.$symbol.'</sup>/ <sub><small>MONTH</small></sub></p>';
            $pricing_tooltip .= '<p>Annually '.number_format($plan_array['yearly_disc']).' <sup>'.$symbol.'</sup>/ <sub><small>MONTH</small></sub></p>';
            ?>
            <p align="center"><button data-placement="bottom" data-original-title="<?php echo $pricing_tooltip ?>" class="btn btn-xs btn-danger tooltips">Special Pricing</button></p>
        </div>
        <div class="plan_content">
            <br>
            <p align="center">Outlets <strong><?php echo $plan_array['stores_handle'] ?></strong></p>
            <p align="center"><?php echo is_numeric($plan_array['stock_limit']) ? 'Product limit <b>'.$plan_array['stock_limit'].'</b>' : '<b>Unlimited Products</b>' ?></p>
            <?php
            $users = (is_numeric($plan_array['users_limit']) == TRUE ) ? 'User limit '.$plan_array['users_limit'] : 'Unlimited users';
            $cust = (is_numeric($plan_array['customer_db_count']) == TRUE) ? 'Customer Database '.$plan_array['customer_db_count'] : 'Unlimited customers';
            $reg = (is_numeric($plan_array['registers']) == TRUE) ? 'Max Registers '.$plan_array['registers'] : 'Unlimited registers';
            $features = '<br><p>'.$users.'</p>';
            $features .= '<p>'.$cust.'</p>';
            $features .= '<p>'.$reg.'</p>';
            $features .= '<p>Loyalty program</p>';
            $features .= '<p>Promotion program</p>';
            $features .= '<p>Inventory program</p>';
            $features .= '<p>Enhanced reporting</p>';
            $features .= '<p>'.$plan_array['support'].' Support</p>';
            ?>	
            <p align="center"><button data-placement="bottom" data-original-title="<?php echo $features ?>" class="btn btn-xs btn-default tooltips">Key features</button></p>
            <p align="center"><?php echo $plan_array['ecommerce'] == 1 ? 'Ecommerce Ready' : '&nbsp;' ?></p>
            <?php
            $mem = (is_numeric($plan_array['memory_limit_gb']) == TRUE ) ? 'Storage '.$plan_array['memory_limit_gb'].' GB' : 'Unlimited storage';
            $reg_pricing_tooltip = '<h5><em>Additional purchase</em></h5><p>Monthly '.number_format($plan_array['register_cost_monthly']).'<sup>'.$symbol.'</sup></p>';
            $reg_pricing_tooltip .= '<p>Quarterly '.number_format($plan_array['register_cost_quarter_yearly']).'<sup>'.$symbol.'</sup> / <sub><small>MONTH</small></sub></p>';
            $reg_pricing_tooltip .= '<p>Biannually '.number_format($plan_array['register_cost_half_yearly']).'<sup>'.$symbol.'</sup> / <sub><small>MONTH</small></sub></p>';
            $reg_pricing_tooltip .= '<p>Annually '.number_format($plan_array['register_cost_yearly']).'<sup>'.$symbol.'</sup> / <sub><small>MONTH</small></sub></p>';
            $reg_pricing_tooltip .= '<h6><em>One register added free as default</em></h6>';
			?>
            <p align="center"><button data-placement="bottom" data-original-title="<?php echo $reg_pricing_tooltip ?>" class="btn btn-xs btn-default tooltips">Register Pricing</button></p>
            <p align="center"><?php echo $mem ?></p>
            <?php
			$disabled = array_key_exists($plan_array['plan_id'],$rec_plan_array) ? '' : 'disabled';
			$disabled_btn = array_key_exists($plan_array['plan_id'],$rec_plan_array) ? 'btn-success' : 'btn-default';
			$disabled_str = array_key_exists($plan_array['plan_id'],$rec_plan_array) ? 'Activate' : 'Usage Exceeded';
            ?>
            <div class="container-fluid">
                <p align="center">
                    <button type="button" data-plan-id="<?php echo $plan_array['plan_id'] ?>" class="btn btn-block <?php echo $disabled." ".$disabled_btn ?> select_plan">
						<span class="select_loader h4"></span>
						<?php echo $disabled_str?>
                    </button>
                </p>
            </div>
        </div>
        <br>
    </div>
    <?php
    }
    ?>
</div>
<div class="modal fade" id="delete_trial_data_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="terms_div">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="jumbotron">
<?php
if($merchant_details['account_type_code'] == 'TRY') //waiting.. change this TRY. TES done for testing purpose
{
?>
    <h4>Hi <?php echo $this->session->userdata('cmp_name'); ?>,</h4>
    <div>
        It is advised to delete your testing data before activating any of the paid plans. 
        Else your reporting inherits your dummy testing data after activating.
    </div><br>
    <div>
        <a class="btn btn-block btn-danger" data-toggle="modal" data-target="#delete_trial_data_modal" href="<?php echo base_url('account/delete_trial_data') ?>">Delete Trial Data</a>
    </div>
<?php
} else {
?>
    <h4>Hi <?php echo $this->session->userdata('cmp_name'); ?>,</h4>
	<div>
    	Are you facing any storage limit issues ??
        You have an option of maintaining your storage space by deleting your very old history data.
        By doing this you can still hold your uninterrupted current plan with its maximum storage limit without
        upgrading to a maximum plan.
    </div><br>
    <div>
        <a data-toggle="modal" data-target="#delete_trial_data_modal" class="btn btn-block btn-danger" href="<?php echo base_url('account/manage_storage_space') ?>"><i class="fa fa-database fa-fw"></i>Manage Storage Space</a>
    </div>
<?php
}
?>
</div>
	<?php if(!is_null($plan_promotions)){ ?>
	<div class="alert alert-md alert-danger">
        <b><?php echo $plan_promotions['plan_prom_code'] ?></b>
        <?php echo $plan_promotions['prom_description'] ?>
    </div>
	<?php } ?>

<?php
echo form_open(base_url().'account/payment_gateway',array('id' => 'payment_form')); 
?>
<div class="panel panel-default" id="payment_summary">
	<input type="hidden" id="merchant_currency" value="<?php echo $merchant_details['currency'];?>">
    <div class="panel-heading">
    	<i class="fa fa-credit-card"></i> Payment Summary
    	<span class="pull-right text-danger badge" id="time_lapse"></span>
    </div>
    <div class="panel-body">
		<div class="row form-group">
            <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12 form-group">
                <button type="button" class="btn btn-danger handle_plan hide"><i class="fa fa-calendar fa-fw"></i></button> 
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default select_term btn_monthly" id="1">Monthly</button>
                    </div>
                    <div class="btn-group" role="group">
						<button type="button" class="btn btn-default select_term btn_quarterly" id="3">Quarterly</button> 
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default select_term btn_half_yearly" id="6">Bi-annually</button> 
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default select_term btn_Yearly" id="12">Annually</button> 
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<div class="input-group input-group-sm">
					<?php echo form_input(array('id' => 'p_code','autocomplete' => 'off', 'name' => 'p_code','placeholder' => 'Offer Code','class' => 'form-control')) ?>
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-danger disabled" id="verify_p_code">Verify</button>
                    </span>
                </div>
            </div>
		</div>
        <div class="row" id="plan_content">
			<div class="col-lg-6 col-md-8">    
                <div class="row form-group">
                    <div class="col-lg-6 col-md-8 col-sm-8 col-xs-8">Selected Plan</div>
                    <div class="col-lg-6 col-md-4 col-sm-4 col-xs-4"><span id="act_plan_str"></span></div>
                </div>
                <div class="row form-group">
                    <div class="col-lg-6 col-md-8 col-sm-8 col-xs-8">
                        Plan pricing <br>
                        <span class="label label-danger">You save <small id="save_span">0</small>&nbsp;<sup><?php echo $symbol ?></sup></span>
                    </div>
                    <div class="col-lg-6 col-md-4 col-sm-4 col-xs-4"><span class="curr_symbol"><?php echo $symbol ?></span> <span id="act_plan_price"></span></div>
                </div>
                <?php
                $add_reg_count = $reg_count > 1 ? $reg_count-1 : 0;
                ?>
                <div class="row">
                    <div class="col-lg-6 col-md-8 col-sm-8 col-xs-8">
                        Additional Register (<span id="act_reg_count"></span> &times; <span id="act_reg_per_price"></span>)<br>
                        <span class="label label-danger">You save <small id="save_reg_span">0</small>&nbsp;<sup><?php echo $symbol ?></sup></span>
                    </div>
                    <div class="col-lg-6 col-md-4 col-sm-4 col-xs-4"><span class="curr_symbol"><?php echo $symbol ?></span><span id="register_tot_pricing"></span></div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12"><hr></div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-8 col-sm-8 col-xs-7">
                        Total<br>
                        <span class="label label-danger">Total savings <small id="total_savings">0</small><sup><?php echo $symbol ?></sup></span>
                    </div>
                    <div class="col-lg-6 col-md-4 col-sm-4 col-xs-5">
                        <h4>
                        	<span class="curr_symbol"><?php echo $symbol ?></span>
                            <span id="total_pricing"></span>&nbsp;&times;&nbsp;<span id="term_num">1</span>&nbsp;
                        </h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-8 col-sm-8 col-xs-8">
                    	<span class="label label-primary" id="discount_string"></span>
                    </div>
                    <div class="col-lg-6 col-md-4 col-sm-4 col-xs-4">
                    	<span class="label label-primary" id="discount_field"></span>
                    </div>
				</div>
                <div class="row">
                    <div class="col-lg-12 col-md-12"><hr></div>
                </div>
			</div>
		</div>    
                
        <input type="hidden" id="opted_plan" name="opted_plan">
        <input type="hidden" id="opted_term" name="opted_term">
        <input type="hidden" id="opted_price" name="opted_price">
        
        <div class="row">
            <div class="col-lg-7 col-md-7 col-sm-4 form-group">

            </div>        
            <div class="col-lg-5 col-md-5 col-sm-8">
                <div class="form-group">
                    <div class="btn-group btn-block">
                        <button data-placement="top" data-original-title="<small>Check agree to terms & conditions</small>" type="button" class="btn btn-md btn-default cc_post tooltips col-lg-3 col-md-4 col-sm-4 col-xs-4">
                            <i class="fa fa-cc-visa fa-fw"></i>
                            <i class="fa fa-cc-mastercard fa-fw"></i>
                            <i class="fa fa-cc-amex fa-fw"></i>
                        </button>
                        <button type="submit" class="btn btn-md btn-success loading_modal submit_post disabled col-lg-9 col-md-8 col-sm-8 col-xs-8">
                        Pay <sup><small class="curr_symbol"><?php echo $symbol ?></small></sup>
                        <span id="term_pricing"></span> 
                        <sub> / <small id="pay_term">Month</small></sub>
                        </button>
                    </div>
                </div>
            </div>
        </div>
	</div>
    <div class="checkbox" align="center">
        <label>
			<?php echo form_checkbox(array('id' => 'check_agree_terms','value' => '' ,'checked' => false ))?> 
            I agree to <?php echo $this->session->userdata('pos_hoster_cmp') ?>
            <a data-toggle="modal" data-target="#delete_trial_data_modal" href="<?php echo base_url('account/terms') ?>">terms & conditions</a>
            on activating my account
        </label>
    </div>    
</div>    
<?php
echo form_close();
?>
<a data-toggle="modal" data-target="#delete_trial_data_modal" href="<?php echo base_url('account/delete') ?>" data-placement="top" data-original-title="Are you sure.. <br>Delete your account<br> permenantly ??" class="btn btn-md btn-outline btn-danger tooltips"><i class="fa fa-ban fa-fw"></i> Delete my account</a>
