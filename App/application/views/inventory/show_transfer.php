<?php
$c_symbol = $this->currency_model->getsymbol($this->session->userdata('currency'));
$daylight_saving = date("I");
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
if(form_error('rec'))
{
	echo form_error('rec');	
}
?>
<h4><i class="fa fa-flask"></i> Inventory / <?php echo $details['transfer_name']?></h4>
<div class="well well-sm">
	<div class="btn-group btn-group-sm">
	    <?php echo anchor('inventory','<i class="fa fa-angle-double-left"></i> Back','class = "btn btn-outline btn-success"') ?>
		<?php echo ($details['transfer_stat'] == 5) ? anchor('inventory/freight/edit/id/'.$details['transfer_index'],'<i class="fa fa-pencil"></i> Edit products','class = "btn btn-success loading_modal" title="Edit ordered stocks"') : '' ?>
        <div class="btn-group" role="menu">
            <button class="btn btn-sm btn-default dropdown-toggle" type="button" data-toggle="dropdown">Actions <span class="caret"></span></button>    
            <ul class="dropdown-menu">
                <li><?php echo ($details['towards_id'] == 19 and $details['transfer_stat'] == 8) ? anchor('inventory/freight/'.$details['transfer_index'].'/return','<i class="fa fa-undo"></i> Return','data-confirm="My consignment is ready... <br>Continue to return ??"') : '' ?>
                <li><?php echo ($details['transfer_stat'] == 8 and $details['towards_id'] != 19) ? anchor('inventory/freight/'.$details['transfer_index'].'/receive','<span class="glyphicon glyphicon-import"></span> Receive','data-confirm="My consignment has arrived... <br>Continue to receive ??"') : '' ?></li>
                <li><?php echo ($details['transfer_stat'] == 5) ? anchor('inventory/freight/'.$details['transfer_index'].'/send','<i class="fa fa-share"></i> Send Order','data-toggle="modal" data-target="#ajax_send_dyn_modal"') : '' ?></li>
                <li><?php echo ($details['transfer_stat'] == 8) ? anchor('inventory/freight/'.$details['transfer_index'].'/send','<i class="fa fa-share"></i> Resend Order','data-toggle="modal" data-target="#ajax_send_dyn_modal"') : '' ?></li>
                <li><?php echo ($details['transfer_stat'] == 5) ? anchor('inventory/freight/'.$details['transfer_index'].'/marksent','<span class="glyphicon glyphicon-pushpin"></span> Mark as sent','data-confirm="Just mark order as sent ... I` ll manage inventory action once my consignment is acknowledged !!!"') : '' ?></li>
                <li><?php echo anchor('inventory/freight/'.$details['transfer_index'].'/export','<i class="fa fa-download"></i> Export CSV') ?>
            </ul>          
        </div>  
	</div>
    <div class="pull-right"><?php echo ($details['transfer_stat'] != 21) ? anchor('inventory/freight/cancel/id/'.$details['transfer_index'],'<i class="fa fa-trash-o"></i> Cancel order','class="btn btn-sm btn-danger" data-confirm="Cancel this transfer? This cant be restored..."') : '' ?></div>
</div>   
<div class="modal fade" id="ajax_send_dyn_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <span><i class="fa fa-flash"></i> <?php echo $details['towards']?></span>
        <?php 
		$header_col = $details['transfer_stat'] == 21 ? 'text-success' : 'text-danger';
		$header_status_fa = $details['transfer_stat'] == 21 ? 'fa fa-thumbs-o-up' : 'fa-hand-o-right';
		
		?>
        <span class="pull-right <?php echo $header_col ?>"><i class="fa <?php echo $header_status_fa ?>"></i> <?php echo $details['log_name']?></span>
    </div>
    <div class="panel-body">
		<div class="row">
        	<?php if(strlen($details['supplier_name']) > 0) { ?>
        	<div class="col-lg-4 col-md-4 form-group">
            	<div class="row">
					<?php $supplier_str = $details['towards_id'] == 19 ? 'Return to Supplier' : 'Supplier'; ?>
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><strong><?php echo $supplier_str ?></strong></div>
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><?php echo $details['supplier_name'] ?></div>
				</div>                    
            </div>
            <?php } ?>
            <?php if($details['towards_id'] == 17) { ?>
        	<div class="col-lg-4 col-md-4 form-group">
            	<div class="row">
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><strong>Source Outlet</strong></div>
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><?php echo $details['source_outlet'] ?></div>
				</div>                    
            </div>
            <?php } ?>
            <?php $dest_outlet_str = $details['towards_id'] == 19 ? 'Returning' : 'Destination'; ?>
        	<div class="col-lg-4 col-md-4 form-group">
            	<div class="row">
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><strong><?php echo $dest_outlet_str ?> Outlet</strong></div>
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><?php echo $details['dest_outlet'] ?></div>
				</div>                    
            </div>
		</div>        
	</div>
    <div class="panel-footer">
		<div class="row">
        	<div class="col-lg-12 col-md-12">
            	<div class="row">
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><span><i class="fa fa-calendar"></i> Started @ <?php echo unix_to_human(gmt_to_local(strtotime($details['created_at']),$timezone, $daylight_saving)) ?></span></div>
					<?php if($details['transfer_stat'] == 21) { ?>
	                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><span class="pull-right text-success"><i class="fa fa-clock-o"></i> Completed @ <?php echo unix_to_human(gmt_to_local(strtotime($details['recieved_at']),$timezone, $daylight_saving)) ?></span></div>                        
                    <?php } ?>
				</div>                    
            </div>
		</div>
            		
		
	</div>    
</div>    
<div class="table-responsive">
    <div class="table-curved-div">
		<?php
        $tmpl = array ( 'table_open'  => '<table class="table table-striped table-condensed table-curved">' );
        $this->table->set_template($tmpl);		
		$action = $details['towards_id'] == 19 ? 'Returned' : 'Recieved';	
        $heading = array('Product','SKU','Outlet Stock','Ordered',$action,'Supplier Price','Total Supplier Cost','Retail Price','Total Retail Price');
        $this->table->set_heading($heading);
        if(isset($sub_product_array['product_id']))
        {
            list($main_ordered,$main_recieved,$main_supp,$main_ret) = array(0,0,0,0);
            foreach($sub_product_array['product_id'] as $key => $value)
            {
                $tot_supp = $sub_product_array['supplier_price'][$key] * $sub_product_array['ordered'][$key];
                $tot_retail = $sub_product_array['retail_price'][$key] * $sub_product_array['ordered'][$key];		
                $main_ordered += $sub_product_array['ordered'][$key];
                $main_recieved += $sub_product_array['recieved'][$key];
                $main_supp += $tot_supp;
                $main_ret += $tot_retail;
				$stk_class = $sub_product_array['source_stock'][$key] > 0 ? 'text-success' : 'text-danger';
                $this->table->add_row(
                                    anchor('products/'.$sub_product_array['product_id'][$key],$sub_product_array['prod_name'][$key],'class="btn btn-xs btn-default"'),
                                    $sub_product_array['sku'][$key],
                                    '<span class="'.$stk_class.'">'.$sub_product_array['source_stock'][$key].'</span>',
                                    $sub_product_array['ordered'][$key],
                                    $sub_product_array['recieved'][$key],
                                    $this->currency_model->moneyFormat($sub_product_array['supplier_price'][$key],$this->session->userdata('currency')),
                                    $this->currency_model->moneyFormat($tot_supp,$this->session->userdata('currency')),	
                                    $this->currency_model->moneyFormat($sub_product_array['retail_price'][$key],$this->session->userdata('currency')),
                                    $this->currency_model->moneyFormat($tot_retail,$this->session->userdata('currency'))
                                    );
        
            }
            $this->table->add_row(array('data' => '<strong>Total</strong>','style' => 'border-top:#6e6e6e double 3px; border-bottom:#6e6e6e double 3px;'),'','',
									array('data' => '<strong>'.$main_ordered.'</strong>','style' => 'border-top:#6e6e6e double 3px; border-bottom:#6e6e6e double 3px;'),
									array('data' => '<strong>'.$main_recieved.'</strong>','style' => 'border-top:#6e6e6e double 3px; border-bottom:#6e6e6e double 3px;'),'',
									array('data' => '<strong><sup>'.$c_symbol.'</sup> '.$this->currency_model->moneyFormat($main_supp,$this->session->userdata('currency')).'</stonng>','style' => 'border-top:#6e6e6e double 3px; border-bottom:#6e6e6e double 3px;'),'',
									array('data' => '<strong><sup>'.$c_symbol.'</sup> '.$this->currency_model->moneyFormat($main_ret,$this->session->userdata('currency')).'</stonng>','style' => 'border-top:#6e6e6e double 3px; border-bottom:#6e6e6e double 3px;')
                                    );
            
        } else {
            $this->table->add_row(array('data' => ':::No products found:::','colspan' => 9,'align' => 'center'));
        }
        echo $this->table->generate();
        echo $links;
        ?>    
	</div>
</div>    