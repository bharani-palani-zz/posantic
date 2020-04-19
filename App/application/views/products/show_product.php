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
<input type="hidden" id="merchant_id" value="<?php echo $this->session->userdata('acc_no') ?>">
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
<input type="hidden" id="update_variant_url" value="<?php echo base_url('products/ajax_update_variant_pos') ?>">
<h4><i class="fa fa-cube fa-lg"></i> Product details</h4>
<hr>

<div class="well well-sm hidden-print">
	<?php if($details['main']['product_scale'] != 3) { ?>
    <div class="btn-group btn-group-sm">
		<?php echo anchor('products/'.$product_id.'/edit','<i class="fa fa-edit fa-lg"></i> Edit Product','class = "btn btn-primary"').'<a href="make_barcode/'.$product_id.'" class="btn btn-primary"><i class="fa fa-print fa-lg"></i> Print Barcode</a>' ?>
	</div>
    <div class="pull-right"><?php echo anchor('products/delete/id/'.$product_id,'<i class="fa fa-trash-o fa-lg"></i> Delete Product','class="btn btn-sm btn-danger" data-confirm="Delete this product? This cant be restored..."')?></div>
    <?php } else { ?>
    <div class="btn-group btn-group-sm">
		<?php echo anchor('products/add_variant/id/'.$product_id,'<i class="fa fa-plus fa-lg"></i> Add Variant','class = "btn btn-sm btn-primary"')?>
	</div>
	<div class="pull-right"><?php echo anchor('products/delete/variant/id/'.$product_id,'<i class="fa fa-trash-o fa-lg"></i> Delete product & all variants','class="btn btn-sm btn-danger" data-confirm="Delete product and all of its variants? This cant be restored..."')?></div>    
	<?php } ?>
</div>    
<?php
$c_symbol = $this->currency_model->getsymbol($this->session->userdata('currency'));
$image_href; $ext = '';
$root = APPPATH.'user_images/'.md5($this->session->userdata('acc_no')).'/products/'.$product_id.'_thumb';
foreach (glob($root.".*") as $filename) {
	$ext = substr($filename,-3);
}
$image_href = $root.'.'.$ext;
if(file_exists($image_href))
{
	$image_href = '<img class="img-circle" src="'.base_url().$image_href.'?random='.time().'" height="150" width="150"/>';
} else {
	$image_href = '<div class="img-circle bg-info" style="padding:10px; width:150px; height: 150px; line-height:165px;"><i class="fa fa-cube fa-5x"></i></div>';								
}
$tag_link = '';
if(strlen($details['main']['product_tag']) > 0)
{
	$tags = explode(';',$details['main']['product_tag']);
	foreach($tags as $tag_single)
	{
		$single = explode("/",$tag_single);	
		$tag_link .= '<span class="tag label label-info"><span>'.anchor(base_url('products/lookup?tag_id='.$single[1]),$single[0]).'</span>&nbsp;<i class="remove glyphicon glyphicon-remove-sign glyphicon-white"></i></span> ';
	}
} else {
	$tag_link = 'Nil';	
}
$status_codes = $this->status_codes->get_status_code();
$tmpl = array ('table_open'   => '<table class="table table-striped table-condensed table-curved">');

?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo $details['main']['product_name']?> 
    	<span class="hidden-print label label-success pull-right" data-toggle="popover" data-placement="top" data-content="<?php echo $details['pos_id'] ?>">
			<?php echo $details['caption'] ?>
        </span>
    </div>
    <div class="panel-body">
    	<?php
		$det_width = $details['main']['product_scale'] == 3 ? '' : '';
		?>
        <div class="row">
    		<p class="col-lg-12 col-md-12 col-sm-12">
	            Tags: <?php echo $tag_link ?>
            </p>
		</div>	
        <div class="row">
            <?php
			$image = array();
			if($details['main']['product_scale'] == 3) 
			{ 
				if(count($details['inventory']) > 0)
				{
					foreach($details['inventory'] as $key => $sub_array)
					{
						$variant_head = array_unique($details['inventory'][$key]['attribute_name']);
						$image_href; $ext = '';
						$root = APPPATH.'user_images/'.md5($this->session->userdata('acc_no')).'/products/'.$key.'_thumb';
						foreach (glob($root.".*") as $filename) {
							$ext = substr($filename,-3);
						}
						$image_href = $root.'.'.$ext;
						if(file_exists($image_href))
						{
							$image_href = '<img class="img-circle" src="'.base_url().$image_href.'?now='.now().'" height="150" width="150"/>';
						} else {
							$image_href = '<div class="img-circle bg-info" style="padding:10px; width:150px; height: 150px; line-height:165px;"><i class="fa fa-cube fa-5x"></i></div>';								
						}	
						$v_name = array_unique($details['inventory'][$key]['attribute_value']);
						$v_name = implode(" / ",$v_name);
						$image[] = '<p class="text-center">'.$v_name.'</p>
									'.$image_href.'
									<p><div class="text-center">SKU '.$details['inventory'][$key]['sku'].'</div>
									<div class="text-center">Price '.number_format($details['inventory'][$key]['retail_price']).'</div></p>';
					}
					$end_array = end($details['inventory']);
					$heading =  count($end_array['outlets']) > 1 ? array_merge($variant_head, array('SKU','Price','Outlet','InStock','&nbsp;')) : array_merge($variant_head, array('SKU','Price','InStock','&nbsp;'));
				} else {
					$end_array = "";
					$heading = array('Inventory not set');
				}
			} else {
				$image[0] = '<br>'.$image_href;
			}
			$col = count($image) > 1 ? 6 : 4;
			?>
    		<div class="col-lg-<?php echo $col ?> col-md-<?php echo $col ?> col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Description</div> 
				    <div class="panel-body" style="max-height: 200px ; min-height: 200px ;overflow-y: scroll;">
						<?php echo $details['main']['description'] ?>
					</div>
				</div>                    
            </div>
    		<div class="col-lg-<?php echo $col ?> col-md-<?php echo $col ?> col-sm-12">
            	<small>
                <ul class="list-group">
                    <?php if(strlen($details['main']['handle']) > 0) {?><li class="list-group-item active"><span class="badge">Handle</span><?php echo $details['main']['handle'] ?></li><?php } ?>
                    <?php if(strlen($details['main']['cat_name']) > 0) { ?><li class="list-group-item"><span class="badge">Category</span> <?php echo $details['main']['cat_name'] ?></li><?php } ?>
                    <?php if($details['main']['product_scale'] != 3 ) { ?> <li class="list-group-item"><span class="badge">SKU</span><?php echo $details['main']['sku'] ?></li><?php } ?>
                    <?php if(strlen($details['main']['status']) > 0) {?><li class="list-group-item"><span class="badge">Status</span><?php echo $status_codes[$details['main']['status']] ?></li><?php } ?>
                    <li class="list-group-item"><span class="badge">Price</span><?php echo '<sup>'.$c_symbol.'</sup> '.$this->currency_model->moneyFormat($details['main']['retail_price'],$this->session->userdata('currency')).$details['scale'] ?></li>
                    <?php if(strlen($details['main']['cmp_name']) > 0) { ?><li class="list-group-item"><span class="badge">Supplier</span><?php echo $details['main']['cmp_name'] ?></li><?php } ?>
                    <?php if(strlen($details['main']['brand_name']) > 0) { ?><li class="list-group-item"><span class="badge">Brand</span><?php echo $details['main']['brand_name'] ?></li><?php } ?>
                </ul>
                </small>
            </div>
			<?php $img_row = (count($image) > 1) ? 12 : 4; ?>
            <div class="hidden-print col-lg-<?php echo $img_row ?> col-md-<?php echo $img_row ?> col-sm-12" align="center">
				<?php $c_interval = count($image) > 1 ? 5000 : 'false'?>
                <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="<?php echo $c_interval ?>" style="padding:10px;">
                
                    <div class="carousel-inner" role="listbox">
                        <?php foreach($image as $key => $value) { $active = $key == 0 ? 'active' : ''; ?>
                            <div class="item <?php echo $active ?>"><?php echo $value ?></div>
                        <?php } ?>
                    </div>
                    <?php if (count($image) > 1) { ?>
                        <a class="left carousel-control bg-primary" href="#myCarousel" role="button" data-slide="prev" style="background:#111; border-top-left-radius:10px; border-bottom-left-radius:10px;">
                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="right carousel-control bg-primary" href="#myCarousel" role="button" data-slide="next" style="background:#111; border-top-right-radius:10px; border-bottom-right-radius:10px;">
                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>  
                    <?php } ?>
                </div>                                            
			</div>
            							            
        </div>        
	</div>
</div>
<?php 
if($details['main']['product_scale'] == 1 || $details['main']['product_scale'] == 2) { 
	if(count($details['inventory']) > 0) 
	{
		$this->table->set_template($tmpl);			
		$heading = array('Outlet','Instock');
		$this->table->set_heading($heading);
		foreach($details['inventory']['loc_id'] as $key => $loc_id)
		{
			$this->table->add_row($details['inventory']['location'][$key],$details['inventory']['stock'][$key]);
		}
		$tot_inv = $this->currency_model->moneyFormat(array_sum($details['inventory']['stock']),$this->session->userdata('currency'));
		$tot_stock_val = $this->currency_model->moneyFormat($tot_inv * $details['main']['retail_price'],$this->session->userdata('currency'));
		$this->table->add_row(array('data' => '<strong>Total</strong>','align' => 'right'),array('data' => '<b>'.$tot_inv." ".$details['inv_scale'].'</b>','style' => 'border-top:#6e6e6e double 3px; border-bottom:#6e6e6e double 3px;','width' => '20%'));
		$this->table->add_row(array('data' => '<span class="label label-danger">Stock value: '.$tot_stock_val.' <sup>'.$c_symbol.'</sup></span>','colspan' => 2,'align' => 'center'));
		$inv_tbl = $this->table->generate();
	}
} else if($details['main']['product_scale'] == 3) {
	$tot=0;
	$inv_tbl = '<table class="table table-striped table-condensed table-curved" id="variant_table">';
		$inv_tbl .= '<thead>';
			$inv_tbl .= '<tr>';
			foreach($heading as $value)
			{
				$inv_tbl .= '<th class="var_columns">'.$value.'</th>';
			}
			$inv_tbl .= '</tr>';
		$inv_tbl .= '</thead>';
		$colspan = array(3 => 5, 2 => 4, 1 => 3);
		foreach($details['inventory'] as $key => $sub_array)
		{	
			$attribute_values = array_values($details['inventory'][$key]['attribute_value']);
			if(count($sub_array['outlets']) > 1)
			{
			    $inv_tbl .= '<tbody class="sortable-row" id="'.$key.'">';
				$inv_tbl .= '<tr>';
				foreach($attribute_values as $attr_key => $col_attr_value)
				{
					$pop_content = $attr_key == 0 ? 'data-toggle="popover" data-placement="top" data-content="Drag & drop to re-arrange"' : '';
					$inv_tbl .= '<td class="var_columns" '.$pop_content.'>'.$col_attr_value .'</td>';
				}
					$inv_tbl .= '<td>'.$details['inventory'][$key]['sku'] .'</td>';
					$inv_tbl .= '<td>'.$details['inventory'][$key]['retail_price'].'</td>';
					$inv_tbl .= '<td>All Outlets</td>';		
					$inv_tbl .= '<td align="right"><button class="togglelink btn btn-primary btn-xs">'.array_sum($details['inventory'][$key]['stock']).' <i class="fa fa-caret-down"></i></button></td>';
					$inv_tbl .= '<td><div class="btn-group btn-group-xs">'.anchor('products/'.$key,'View','class="btn btn-default"').anchor('products/'.$key.'/edit','Edit','class="btn btn-success"').'<a class="btn btn-default" href="'.base_url('products/make_barcode/'.$key).'">Barcode</a>'.anchor('products/delete/id/'.$key,'Delete','class="btn btn-danger" data-confirm="Delete this variant? This cant be restored..."').'</div></td>';
				$inv_tbl .= '</tr>';
				$inv_tbl .= '<tr class="toggle">';

					$inv_tbl .= '<td colspan="'.(count($attribute_values) + 2) .'"></td>';
					$inv_tbl .= '<td colspan="2">';
						$inv_tbl .= '<ul class="list-group">';
							foreach($sub_array['stock'] as $s_key => $stock_value) 
							{ 
								$least_stock_key = array_search(min($sub_array['stock']), $sub_array['stock']);
								$l_active = $least_stock_key == $s_key ? 'text-danger' : '';
								$inv_tbl .= '<li class="list-group-item"><span class="badge">'.$stock_value .'</span> <span class="'.$l_active.'">'.$sub_array['outlets'][$s_key].'</span></li>';
								$tot +=  $stock_value * $details['inventory'][$key]['retail_price'];
							}
						$inv_tbl .= '</ul>';
					$inv_tbl .= '</td>';	
					$inv_tbl .= '<td colspan="2"></td>';
				$inv_tbl .= '</tr>';	
			} else {
				$inv_tbl .= '<tbody class="sortable-row" id="'.$key.'">';
                    $inv_tbl .= '<tr>';
					foreach($attribute_values as $attr_key => $col_attr_value)
					{
						$pop_content = $attr_key == 0 ? 'data-toggle="popover" data-placement="top" data-content="Drag & drop to re-arrange"' : '';
						$inv_tbl .= '<td class="var_columns" '.$pop_content.'>'.$col_attr_value .'</td>';
					}
                        $inv_tbl .= '<td>'.$details['inventory'][$key]['sku'].'</td>';
                        $inv_tbl .= '<td>'.$details['inventory'][$key]['retail_price'] .'</td>';
                        $inv_tbl .= '<td>'.array_sum($details['inventory'][$key]['stock']).'</td>';
                        $inv_tbl .= '<td><div class="btn-group btn-group-xs">'.anchor('products/'.$key,'View','class="btn btn-default"').anchor('products/'.$key.'/edit','Edit','class="btn btn-success"').'<a class="btn btn-default" href="'.base_url('products/make_barcode/'.$key).'">Barcode</a>'.anchor('products/delete/id/'.$key,'Delete','class="btn btn-danger" data-confirm="Delete this variant? This cant be restored..."').'</td>';
                    $inv_tbl .= '</tr>';			
				$inv_tbl .= '</tbody>';
				$tot +=  array_sum($details['inventory'][$key]['stock']) * $details['inventory'][$key]['retail_price'];
			}
			$tot = $tot > 0 ? $tot : 0;
			$inv_tbl .= '</tbody>';
		}
	$inv_tbl .= '<tr class="no-hover">';
		$inv_tbl .= '<td align="center" colspan="10"><span class="label label-danger">Stock Value '.$this->currency_model->moneyFormat($tot,$this->session->userdata('currency')).' <sup>'.$c_symbol.'</sup></span></td>';
	$inv_tbl .= '</tr>';
	$inv_tbl .= '</table>';
} else if($details['main']['product_scale'] == 4) {
	$tot = array();
	foreach($details['inventory']['calculate'] as $key => $calculate_array)
	{
		$parent_qty = $details['inventory']['calculate'][$key]['parent_qty'];
		foreach($calculate_array['outlets'] as $o_key => $o_value)
		{
			$ind_stock = floor($calculate_array['stock'][$o_key] / $parent_qty);
			$all_outlet_stock[$o_key][$o_value][] = $ind_stock;	
			$ind_outlet_stock[$o_key][] = $ind_stock;	

			$tot_stock[$key][] = $calculate_array['stock'][$o_key] / $parent_qty;
			$tot[$key]  = array_sum($tot_stock[$key]);
		}
	}
	$tot = 0;
	foreach($ind_outlet_stock as $ind_key => $ind_vale)
	{
		//summarise minimum outlet stock
		$tot += min($ind_outlet_stock[$ind_key]);
	}
	$tot_stock = $tot * $details['main']['retail_price'];
	$tot_stock = $tot_stock > 0 ? $tot_stock : 0;
	$end_array = end($details['inventory']['calculate']);
	
	$inv_tbl = '<table class="table table-striped table-condensed table-curved">';
		$inv_tbl .= '<thead>';
			$inv_tbl .= '<tr>';
				$inv_tbl .= '<th>Products</th>';
				$inv_tbl .= '<th>Quantity</th>';
				$inv_tbl .= '<th>SKU</th>';
				$inv_tbl .= count($end_array['outlets']) > 1 ? '<th>Outlet</th>' : '';
				$inv_tbl .= '<th>Instock</th>';            
			$inv_tbl .= '</tr>';
		$inv_tbl .= '</thead>';
		$inv_tbl .= '<tbody>';
		if(count($calculate_array['outlets']) > 1) 
		{ 
			$inv_tbl .= '<tr>';
				$inv_tbl .= '<td>'.$details['main']['product_name'].'</td>';
				$inv_tbl .= '<td>1</td>';
				$inv_tbl .= '<td>'.$details['main']['sku'].'</td>';
				$inv_tbl .= '<td>All Outlets</td>';
				$inv_tbl .= '<td><button class="togglelink btn btn-primary btn-xs">'.$tot.' <i class="fa fa-caret-down"></i></button></td>';
			$inv_tbl .= '</tr>';
            foreach($all_outlet_stock as $a_key => $stock_array)
            {
                foreach($stock_array as $s_val)
                {
					$min = min($s_val);
                    $tot += $min;
                }
				$inv_tbl .= '<tr class="toggle">';
					$inv_tbl .= '<td colspan="3"></td>';
					$inv_tbl .= '<td>'.key($stock_array).'</td>';
					$inv_tbl .= '<td>'.$min.'</td>';
				$inv_tbl .= '</tr>';	            
			}
		} else {
			$inv_tbl .= '<tr>';
				$inv_tbl .= '<td>'.$details['main']['product_name'].'</td>';
				$inv_tbl .= '<td>1</td>';
				$inv_tbl .= '<td>'.$details['main']['sku'].'</td>';
				$inv_tbl .= '<td>'.$tot.'</td>';
			$inv_tbl .= '</tr>';	    
		}
		foreach($details['inventory']['calculate'] as $key => $calculate_array)
		{
			$prod_name = $details['inventory']['calculate'][$key]['prod_name'];
			$sku = $details['inventory']['calculate'][$key]['sku'];
			$parent_qty = $details['inventory']['calculate'][$key]['parent_qty'];
			
			$stock_sum = array_sum($calculate_array['stock']);
			if(count($calculate_array['outlets']) > 1)
			{
				$inv_tbl .=  '<tr class="no-hover" style="border-bottom:solid 1px #ddd;">';
					$inv_tbl .=  '<td>'.anchor('products/'.$key.'/edit',$prod_name,'class="btn btn-xs btn-primary"').'</td>';
					$inv_tbl .=  '<td>'.$parent_qty.'</td>';
					$inv_tbl .=  '<td>'.$sku.'</td>';
					$inv_tbl .=  '<td>All Outlets</td>';
					$inv_tbl .=  '<td><button class="togglelink btn btn-primary btn-xs">'.$stock_sum.' <i class="fa fa-caret-down"></i></button></td>';
				$inv_tbl .=  '</tr>';
				foreach($calculate_array['outlets'] as $o_key => $outlet)
				{
					$stock = $calculate_array['stock'][$o_key];
					$inv_tbl .=  '<tr class="toggle">';
						$inv_tbl .=  '<td colspan="3"></td>';
						$inv_tbl .=  '<td>'.$outlet.'</td>';
						$inv_tbl .=  '<td>'.$stock.'</td>';
					$inv_tbl .=  '</tr>';		
				}
			} else {
				$inv_tbl .=  '<tr>';
					$inv_tbl .=  '<td>'.anchor('products/'.$key.'/edit',$prod_name,'class="btn btn-xs btn-primary"').'</td>';
					$inv_tbl .=  '<td>'.$parent_qty.'</td>';
					$inv_tbl .=  '<td>'.$sku.'</td>';
					$inv_tbl .=  '<td>'.$stock_sum.'</td>';
				$inv_tbl .=  '</tr>';
			}
		}	
        $inv_tbl .= '<tr class="no-hover">';
        	$inv_tbl .= '<td align="center" colspan="10"><span class="label label-danger">Stock value '.$this->currency_model->moneyFormat($tot_stock,$this->session->userdata('currency')).' <sup>'.$c_symbol.'</sup></span></td>';
        $inv_tbl .= '</tr>';
		$inv_tbl .= '</tbody>';

	$inv_tbl .= '</table>';
}
?>
<h4>Inventory</h4>
<div class="table-responsive">
    <?php echo $inv_tbl ?>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Product log report</div>
    <div class="panel-body">
	<?php
    echo form_open('products/'.$product_id,array('method' => 'get','id' => 'myform'));
    $date_start = date('01-M-Y',now());
    $days_in_month = days_in_month(date('m'),date('Y'));
    $date_end = $days_in_month."-".date('M')."-".date('Y');
    $start = form_input(array('style' => 'background:#fff','autocomplete' => 'off', 'name' => 'date_start','id' => 'date_start','class' => 'form-control input-sm','readonly' => 'readonly','value' => isset($_GET['date_start']) ? $_GET['date_start'] : $date_start));
    $end = form_input(array('style' => 'background:#fff', 'autocomplete' => 'off', 'name' => 'date_end','id' => 'date_end','class' => 'form-control input-sm','readonly' => 'readonly', 'value' => isset($_GET['date_end']) ? $_GET['date_end'] : $date_end));
    $outlet_drop = form_dropdown('outlet', $company,isset($_GET['outlet']) ? $_GET['outlet'] : '','id="outlet" class="form-control input-sm"');
    $users = form_dropdown('users', $users,isset($_GET['users']) ? $_GET['users'] : '','id="users" class="form-control input-sm"');
    $log_code_drop = form_dropdown('log_code', $log_codes,isset($_GET['log_code']) ? $_GET['log_code'] : '','id="log_code" class="form-control input-sm"');
    ?>
	<div class="row">
		<div class="col-lg-3 col-md-6 col-sm-12">
            <div class="form-group">
                <div class="input-group">
                    <label for="date_start" class="input-group-addon">Start date</label>
                    <?php echo $start ?>
				</div>
			</div>                
    	</div>
		<div class="col-lg-3 col-md-6 col-sm-12">
            <div class="form-group">
                <div class="input-group">
                    <label for="date_end" class="input-group-addon">End date</label>
                    <?php echo $end ?>
				</div>
			</div>                
    	</div>
    </div>	
    <div class="effect">
        <div class="row">
            <div class="col-lg-4 col-md-4">
                <div class="form-group">
                    <div class="input-group">
                        <label for="outlet" class="input-group-addon">Outlet</label>
                        <?php echo $outlet_drop ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="form-group">
                    <div class="input-group">
                        <label for="users" class="input-group-addon">User</label>
                        <?php echo $users ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="form-group">
                    <div class="input-group">
                        <label for="log_code" class="input-group-addon">Action</label>
                        <?php echo $log_code_drop ?>
                    </div>
                </div>
            </div>
        </div>                                    
	</div>
	<?php
    if(isset($_GET['outlet']) || isset($_GET['users']) || isset($_GET['log_code'])) {
        echo '<input type="hidden" id="toggle_filter" value="1">';
    } else {
        echo '<input type="hidden" id="toggle_filter" value="0">';				
    } 
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="btn-group">
            <button type="submit" class="btn btn-success btn-sm search_button loading_modal" id="prd_search">Show Logs</button> 
            <button type="button" id="filter_button" class="btn btn-danger btn-sm"><i class="fa fa-filter"></i> Filter Options</button>
			<?php if(count($_GET) > 0) { ?>
			<?php echo anchor(base_url().'products/'.$product_id,'<i class="fa fa-times-circle"></i> Clear filter','class="btn btn-default btn-sm bg-danger"') ?>
			<?php } ?>
            </div>
        </div>
    </div>
    
	<?php
    echo form_close();
    ?>
    </div>
</div>
<div class="table-responsive">    
<?php
$daylight_saving = date("I");

$this->table->set_template($tmpl);	
if($details['main']['product_scale'] == 3)
{
	$heading = array('Date','Variants','Outlet','User','Feed','Action');
} else {
	$heading = array('Date','Outlet','User','Feed','Action');
}
$this->table->set_heading($heading);
if(is_array($results))
{	
	foreach($results['updated_at'] as $key => $value)
	{
		$outlet = empty($results['location'][$key]) ? 'ALL OUTLETS' : $results['location'][$key];
		if($details['main']['product_scale'] == 3)
		{
			$this->table->add_row( // show columns with variants column
							date('D, j<\s\u\p>S</\s\u\p> M Y, h:i a',gmt_to_local(strtotime($value),$timezone, $daylight_saving)),
							array('class' => 'inv_scale','data' => $results['prod_name'][$key]),
							$outlet,$results['user'][$key],$results['feed'][$key],$results['log'][$key]
							);
		} else {
			$this->table->add_row( // show columns without variants column
							date('D, j<\s\u\p>S</\s\u\p> M Y, h:i a',gmt_to_local(strtotime($value),$timezone, $daylight_saving)),
							$outlet,$results['user'][$key],$results['feed'][$key],$results['log'][$key]
							);
		}
	}
} else {
	$this->table->add_row(array('data' => '<p>:::No Logs found:::</p>','colspan' => 6,'align' => 'center'));
}
echo $this->table->generate().'<br>';
echo $links;
?>
</div>
