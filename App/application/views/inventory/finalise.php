<?php
$daylight_saving = date("I");
$created = date('d-m-Y h:i a',gmt_to_local(strtotime($details['created_at']),$timezone, $daylight_saving));
$c_symbol = $this->currency_model->getsymbol($this->session->userdata('currency')).'&nbsp;';
$currency_session = $this->session->userdata('currency');
$matched = 0; $loss_unmatched = 0; $profit_unmatched = 0;
if(!is_null($countables))
{
	foreach($countables['id'] as $key => $value)
	{
		if((float)$countables['expected'][$key] == (float)$countables['counted'][$key])
		{
			$matched++;
		} else if((float)$countables['expected'][$key] > (float)$countables['counted'][$key]){
			$loss_unmatched++;
		} else if((float)$countables['expected'][$key] < (float)$countables['counted'][$key]){
			$profit_unmatched++;
		}
	}
}
?>
<h4><i class="fa fa-clipboard fa-fw"></i>Finalise Stock Take</h4>
<h4 class="text-success"><?php echo $details['stocktake_name'] ?> <i class="fa fa-clock-o fa-fw"></i><?php echo $created ?> <i class="fa fa-map-marker fa-fw"></i><?php echo $details['location'] ?></h4> 
<?php 
echo form_open(base_url().'inventory/stock_take/complete/id/'.$take_id,array('id' => 'form_inv_complete'));
?>    
<div class="panel with-nav-tabs panel-success">
    <div class="panel-heading">
        <ul class="nav nav-tabs nav-justified">
            <li class="active"><a href="#all" data-toggle="tab">All (<?php echo count($countables['id'])?>)</a></li>
            <li><a href="#matched" data-toggle="tab">Matched (<?php echo $matched?>)</a></li>
            <li><a href="#loss_unmatched" data-toggle="tab">Loss Counts (<?php echo $loss_unmatched?>)</a></li>
            <li><a href="#profit_unmatched" data-toggle="tab">Profit Counts (<?php echo $profit_unmatched?>)</a></li>
        </ul>
    </div>
    <div class="panel-body">
        <div class="table-responsive" style="overflow-x: initial;">
            <div class="tab-content">
                <div class="tab-pane fade in active" id="all">
				<?php
				$tot_cost_gain = 0; $tot_cost_loss = 0; $tot_count_gain = 0; $tot_count_loss = 0;
				if(count($countables) > 0)
				{
				?>
                <table class="table table-striped stock_take_list" cellspacing="0" width="100%">
                <thead>
                	<tr>
                    	<th style="display:none">hide</th>
                    	<th><input type="checkbox" id="control_select" checked="checked" /></th>
                    	<th>Product</th>
                    	<th>Expected</th>
                    	<th>Counted</th>
                    	<th>Cost gain</th>
                    	<th>Cost loss</th>
                    	<th>Count gain</th>
                    	<th>Count loss</th>
                    </tr>
                </thead>
                <tbody>                
                <?php	
				foreach($countables['product_name'] as $key => $value)
				{
					?>
					<tr>
                    	<td style="display:none"></td>
                    	<td align="center"><input type="checkbox" name="selected_product[]" class="selected_product" value="<?php echo $countables['id'][$key]?>" checked="checked" /></td>
						<td><?php echo $value ?></td>
						<td><?php echo round($countables['expected'][$key],3) ?></td>
						<td><?php echo round($countables['counted'][$key],3) ?></td>
						<td><?php echo round($countables['cost_gain'][$key],3) ?></td>
						<td><?php echo round($countables['cost_loss'][$key],3) ?></td>
						<td><?php echo round($countables['count_gain'][$key],3) ?></td>
						<td><?php echo round($countables['count_loss'][$key],3) ?></td>
					</tr>
					<?php	
					$tot_cost_gain += $countables['cost_gain'][$key];							
					$tot_cost_loss += $countables['cost_loss'][$key];							
					$tot_count_gain += $countables['count_gain'][$key];							
					$tot_count_loss += $countables['count_loss'][$key];							
				}
				?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th>--</th>
                        <th>--</th>
                        <th>--</th>
                        <th><?php echo $c_symbol.$this->currency_model->moneyFormat($tot_cost_gain,$currency_session)?></th>
                        <th><?php echo $c_symbol.$this->currency_model->moneyFormat($tot_cost_loss,$currency_session)?></th>
                        <th><?php echo $this->currency_model->moneyFormat($tot_count_gain,$currency_session)?></th>
                        <th><?php echo $this->currency_model->moneyFormat($tot_count_loss,$currency_session)?></th>
                    </tr>
                </tfoot>
                </table>
                <?php
				} else {
					echo '<h2 align="center" class="text-success"><i class="fa fa-clipboard fa-3x"></i></h2><h3 align="center" class="text-success">Stock count not started</h3>';			
				}
				?>
                </div>
                <div class="tab-pane fade" id="matched">
				<?php
				$tot_cost_gain = 0; $tot_cost_loss = 0; $tot_count_gain = 0; $tot_count_loss = 0;
				$no_matched = '<h2 align="center" class="text-success"><i class="fa fa-clipboard fa-3x"></i></h2><h3 align="center" class="text-success">No matched stocks</h3>';
				if(count($countables) > 0)
				{
				?>
                <table class="table table-striped stock_take_list" cellspacing="0" width="100%">
                <thead>
                	<tr>
                    	<th>Product</th>
                    	<th>Expected</th>
                    	<th>Counted</th>
                    	<th>Cost gain</th>
                    	<th>Cost loss</th>
                    	<th>Count gain</th>
                    	<th>Count loss</th>
                    </tr>
                </thead>
                <tbody>                
                <?php	
					$matched = array();
					foreach($countables['product_name'] as $key => $value)
					{
						if((float)$countables['expected'][$key] == (float)$countables['counted'][$key])
						{
							$matched[$countables['id'][$key]] = array(
								'name' => $value,
								'expected' => round($countables['expected'][$key],3),
								'counted' => round($countables['counted'][$key],3),
								'cost_gain' => round($countables['cost_gain'][$key],3),
								'cost_loss' => round($countables['cost_loss'][$key],3),
								'count_gain' => round($countables['count_gain'][$key],3),
								'count_loss' => round($countables['count_loss'][$key],3)
							);
							$tot_cost_gain += $countables['cost_gain'][$key];							
							$tot_cost_loss += $countables['cost_loss'][$key];							
							$tot_count_gain += $countables['count_gain'][$key];							
							$tot_count_loss += $countables['count_loss'][$key];							
						}
					}
					if(count($matched) > 0)
					{
						foreach($matched as $array_key => $sub_array)
						{
						?>
                        <tr>
                        	<td><?php echo $sub_array['name'] ?></td>
                        	<td><?php echo $sub_array['expected'] ?></td>
                        	<td><?php echo $sub_array['counted'] ?></td>
                        	<td><?php echo $sub_array['cost_gain'] ?></td>
                        	<td><?php echo $sub_array['cost_loss'] ?></td>
                        	<td><?php echo $sub_array['count_gain'] ?></td>
                        	<td><?php echo $sub_array['count_loss'] ?></td>
                        </tr>
                        <?php	
						}
					}
				?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th>--</th>
                        <th>--</th>
                        <th><?php echo $c_symbol.$this->currency_model->moneyFormat($tot_cost_gain,$currency_session)?></th>
                        <th><?php echo $c_symbol.$this->currency_model->moneyFormat($tot_cost_loss,$currency_session)?></th>
                        <th><?php echo $this->currency_model->moneyFormat($tot_count_gain,$currency_session,3)?></th>
                        <th><?php echo $this->currency_model->moneyFormat($tot_count_loss,$currency_session,3)?></th>
                    </tr>
                </tfoot>
                </table>
                <?php
				} else {
					echo $no_matched;				
				}
				?>
                </div>
                <div class="tab-pane fade" id="loss_unmatched">
				<?php
				$tot_cost_gain = 0; $tot_cost_loss = 0; $tot_count_gain = 0; $tot_count_loss = 0;
				$no_matched = '<h2 align="center" class="text-success"><i class="fa fa-clipboard fa-3x"></i></h2><h3 align="center" class="text-success">No loss stocks</h3>';
				if(count($countables) > 0)
				{
				?>
                <table class="table table-striped stock_take_list" cellspacing="0" width="100%">
                <thead>
                	<tr>
                    	<th>Product</th>
                    	<th>Expected</th>
                    	<th>Counted</th>
                    	<th>Cost gain</th>
                    	<th>Cost loss</th>
                    	<th>Count gain</th>
                    	<th>Count loss</th>
                    </tr>
                </thead>
                <tbody>                
                <?php	
					$matched = array();
					foreach($countables['product_name'] as $key => $value)
					{
						if((float)$countables['expected'][$key] > (float)$countables['counted'][$key])
						{
							$matched[$countables['id'][$key]] = array(
								'name' => $value,
								'expected' => round($countables['expected'][$key],3),
								'counted' => round($countables['counted'][$key],3),
								'cost_gain' => round($countables['cost_gain'][$key],3),
								'cost_loss' => round($countables['cost_loss'][$key],3),
								'count_gain' => round($countables['count_gain'][$key],3),
								'count_loss' => round($countables['count_loss'][$key],3)
							);
							$tot_cost_gain += $countables['cost_gain'][$key];							
							$tot_cost_loss += $countables['cost_loss'][$key];							
							$tot_count_gain += $countables['count_gain'][$key];							
							$tot_count_loss += $countables['count_loss'][$key];							
						}
					}
					if(count($matched) > 0)
					{
						foreach($matched as $array_key => $sub_array)
						{
						?>
                        <tr>
                        	<td><?php echo $sub_array['name'] ?></td>
                        	<td><?php echo $sub_array['expected'] ?></td>
                        	<td><?php echo $sub_array['counted'] ?></td>
                        	<td><?php echo $sub_array['cost_gain'] ?></td>
                        	<td><?php echo $sub_array['cost_loss'] ?></td>
                        	<td><?php echo $sub_array['count_gain'] ?></td>
                        	<td><?php echo $sub_array['count_loss'] ?></td>
                        </tr>
                        <?php	
						}
					}
				?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th>--</th>
                        <th>--</th>
                        <th><?php echo $c_symbol.$this->currency_model->moneyFormat($tot_cost_gain,$currency_session)?></th>
                        <th><?php echo $c_symbol.$this->currency_model->moneyFormat($tot_cost_loss,$currency_session)?></th>
                        <th><?php echo $this->currency_model->moneyFormat($tot_count_gain,$currency_session,3)?></th>
                        <th><?php echo $this->currency_model->moneyFormat($tot_count_loss,$currency_session,3)?></th>
                    </tr>
                </tfoot>
                </table>
                <?php
				} else {
					echo $no_matched;				
				}
				?>
                </div>
                <div class="tab-pane fade" id="profit_unmatched">
				<?php
				$tot_cost_gain = 0; $tot_cost_loss = 0; $tot_count_gain = 0; $tot_count_loss = 0;
				$no_matched = '<h2 align="center" class="text-success"><i class="fa fa-clipboard fa-3x"></i></h2><h3 align="center" class="text-success">No profit stocks</h3>';
				if(count($countables) > 0)
				{
				?>
                <table class="table table-striped stock_take_list" cellspacing="0" width="100%">
                <thead>
                	<tr>
                    	<th>Product</th>
                    	<th>Expected</th>
                    	<th>Counted</th>
                    	<th>Cost gain</th>
                    	<th>Cost loss</th>
                    	<th>Count gain</th>
                    	<th>Count loss</th>
                    </tr>
                </thead>
                <tbody>                
                <?php	
					$matched = array();
					foreach($countables['product_name'] as $key => $value)
					{
						if((float)$countables['expected'][$key] < (float)$countables['counted'][$key])
						{
							$matched[$countables['id'][$key]] = array(
								'name' => $value,
								'expected' => round($countables['expected'][$key],3),
								'counted' => round($countables['counted'][$key],3),
								'cost_gain' => round($countables['cost_gain'][$key],3),
								'cost_loss' => round($countables['cost_loss'][$key],3),
								'count_gain' => round($countables['count_gain'][$key],3),
								'count_loss' => round($countables['count_loss'][$key],3)
							);
							$tot_cost_gain += $countables['cost_gain'][$key];							
							$tot_cost_loss += $countables['cost_loss'][$key];							
							$tot_count_gain += $countables['count_gain'][$key];							
							$tot_count_loss += $countables['count_loss'][$key];							
						}
					}
					if(count($matched) > 0)
					{
						foreach($matched as $array_key => $sub_array)
						{
						?>
                        <tr>
                        	<td><?php echo $sub_array['name'] ?></td>
                        	<td><?php echo $sub_array['expected'] ?></td>
                        	<td><?php echo $sub_array['counted'] ?></td>
                        	<td><?php echo $sub_array['cost_gain'] ?></td>
                        	<td><?php echo $sub_array['cost_loss'] ?></td>
                        	<td><?php echo $sub_array['count_gain'] ?></td>
                        	<td><?php echo $sub_array['count_loss'] ?></td>
                        </tr>
                        <?php	
						}
					}
				?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th>--</th>
                        <th>--</th>
                        <th><?php echo $c_symbol.$this->currency_model->moneyFormat($tot_cost_gain,$currency_session)?></th>
                        <th><?php echo $c_symbol.$this->currency_model->moneyFormat($tot_cost_loss,$currency_session)?></th>
                        <th><?php echo $this->currency_model->moneyFormat($tot_count_gain,$currency_session,3)?></th>
                        <th><?php echo $this->currency_model->moneyFormat($tot_count_loss,$currency_session,3)?></th>
                    </tr>
                </tfoot>
                </table>
                <?php
				} else {
					echo $no_matched;				
				}
				?>
                </div>
            </div>
        </div>     
	</div>               
</div>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<?php echo anchor('inventory/stock_take/delete/id/'.$take_id,'<i class="fa fa-times-circle fa-fw"></i>Delete Counting','class="btn btn-danger btn-md" data-confirm="Delete this counting? This action would no more allow you to update or process counting."') ?>
    </div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
    	<div class="pull-right">
		<?php echo anchor('inventory/stock_take/edit/id/'.$take_id,'<i class="fa fa-qrcode fa-fw"></i>Count Again','class="btn btn-success btn-outline btn-md"') ?>
		<?php 
		if(count($countables) > 0) 
		{  
			//echo anchor('inventory/stock_take/complete/id/'.$take_id,'<i class="fa fa-check-circle fa-fw"></i>Complete','id="complete" class="btn btn-success btn-md" data-confirm="You&lsquo;ve finished counting?? Inventory stock count of concerned outlet products will be updated now.."');
			echo '<button id="complete" class="btn btn-success btn-md" type="submit"><i class="fa fa-check-circle fa-fw"></i>Complete</button>';
		} 
		?>
        </div>
	</div>    
</div>
<?php echo form_close() ?>
