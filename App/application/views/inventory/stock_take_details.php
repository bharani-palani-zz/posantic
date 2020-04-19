<div class="hidden-print">
    <div class="btn-group btn-group-sm">
        <h4><i class="fa fa-clipboard fa-fw"></i>Stock Take details</h4>
        <h5><span class="label label-success"><?php echo $details['stocktake_name'] ?></span>&nbsp;<span class="label label-danger">Status:  <?php echo $details['status_code'] ?></span></h5>
    </div>
    <?php if(count($countables['id']) > 0) {?>
    <div class="pull-right">
        <?php echo anchor('inventory/stock_take/csv_export/'.$take_id,'<i class="fa fa-download fa-fw"></i>Export CSV','class="btn btn-md btn-success"') ?>    
    </div>
    <?php } ?>
</div>
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
$currency_session = $this->session->userdata('currency');
$c_symbol = $this->currency_model->getsymbol($this->session->userdata('currency')).'&nbsp;';
$matched = 0; $unmatched = 0;
if(!is_null($countables))
{
	foreach($countables['id'] as $key => $value)
	{
		if((float)$countables['expected'][$key] == (float)$countables['counted'][$key])
		{
			$matched++;
		} else {
			$unmatched++;
		}
	}
}
?>
<div class="panel with-nav-tabs panel-success">
    <div class="panel-heading">
        <ul class="nav nav-tabs nav-justified">
            <li class="active"><a href="#all_details" data-toggle="tab">All (<?php echo count($countables['id']) ?>)</a></li>
            <li><a href="#matched_details" data-toggle="tab">Matched (<?php echo $matched ?>)</a></li>
            <li><a href="#unmatched_details" data-toggle="tab">Unmatched (<?php echo $unmatched ?>)</a></li>
        </ul>
    </div>
    <div class="panel-body">
	    <div class="table-responsive" style="overflow-x: initial;">
            <div class="tab-content">
                <div class="tab-pane fade in active" id="all_details">
                	<?php if(count($countables) > 0){ ?>
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
                        $tot_cost_gain = 0; $tot_cost_loss = 0; $tot_count_gain = 0; $tot_count_loss = 0;
                        foreach($countables['product_name'] as $key => $value)
                        {
                            ?>
                            <tr>
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
                                <th><?php echo $c_symbol.$this->currency_model->moneyFormat($tot_cost_gain,$currency_session)?></th>
                                <th><?php echo $c_symbol.$this->currency_model->moneyFormat($tot_cost_loss,$currency_session)?></th>
                                <th><?php echo $this->currency_model->moneyFormat($tot_count_gain,$currency_session,3)?></th>
                                <th><?php echo $this->currency_model->moneyFormat($tot_count_loss,$currency_session,3)?></th>
                            </tr>
                        </tfoot>
                    </table>        
                    <?php } else { ?>
						<h2 align="center" class="text-success"><i class="fa fa-clipboard fa-3x"></i></h2><h3 align="center" class="text-success">No stock takes done</h3>                    
                    <?php } ?>
                </div>                
                <div class="tab-pane fade" id="matched_details">
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
                <div class="tab-pane fade" id="unmatched_details">
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
					if((float)$countables['expected'][$key] != (float)$countables['counted'][$key])
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

