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
<input type="hidden" id="tag_scale" value="">
<input type="hidden" id="get_variants_url" value="<?php echo base_url().'products/get_sub_variants'?>">
<input type="hidden" id="tag_url" value="<?php echo site_url('products/tag_autocomplete') ?>">
<input type="hidden" id="this_url" value="<?php echo base_url('products') ?>">
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
<h4><i class="fa fa-cubes"></i> Products</h4>
<div class="well well-sm hidden-print">
	<div class="btn-group btn-group-sm">
        <?php echo anchor('products/add_product','<i class="fa fa-plus-circle"></i> Add Product','class = "btn btn-primary loading_modal"') ?>
        <?php echo anchor('products/import','<i class="fa fa-upload"></i> Bulk Import','class = "btn btn-primary loading_modal"') ?>
        <div class="btn-group btn-group-sm">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-wrench"></i> Others <span class="caret"></span></button>
            <ul class="dropdown-menu pull-right" role="menu">
                <li><a href="<?php echo base_url().'products/update_barcode_prefix' ?>" data-toggle="modal" data-target="#ajax_prefix_modal" ><i class="fa fa-barcode"></i> Change weighing scale barcode prefix</a></li>
                <li class="divider"></li>
                <li><?php echo anchor('products/export_kilo_products','<i class="fa fa-download"></i> Export kilo scale products') ?></li>
                <li class="divider"></li>
                <li><?php echo anchor('products/download','<i class="fa fa-download"></i> Export all products') ?></li>
                <?php if(strlen($delete_hidden) > 0) { ?>
	                <li class="divider"></li>
                    <li><?php echo $delete_hidden ?></li>
                <?php } ?>
            </ul>
		</div>            
	</div>
</div>    
<div class="modal fade" id="ajax_prefix_modal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>
<?php echo form_open(base_url().'products/lookup',array('method' => 'get','id' => 'search_form')) ?>
<div class="panel panel-default hidden-print">
    <div class="panel-heading"><i class="fa fa-filter"></i> Filter Products</div>
    <div class="panel-body">
		<div class="row">
			<div class="col-md-6">
	            <div class="form-group">
                    <div class="input-group">
                        <label for="search_product" class="input-group-addon">Product / SKU / Handle</label>
						<?php echo form_input(array('autocomplete' => 'off', 'value' => isset($_GET['search_product']) ? $_GET['search_product'] : '', 'placeholder' => 'Search','id' => 'search_product','name' => 'search_product',"class" => "form-control input-sm")) ?>
					</div>
        		</div>
    		</div>
        </div>
        <div class="effect">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="product_stat" class="input-group-addon">Show</label>
                            <?php echo form_dropdown('product_stat', $status_combo,isset($_GET['product_stat']) ? $_GET['product_stat'] : '','id="product_stat" class="form-control input-sm"')?>
                        </div>
                    </div>
                </div> 
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="product_cat" class="input-group-addon">Category</label>
                            <?php echo form_dropdown('product_cat', $prd_category_combo,isset($_GET['product_cat']) ? $_GET['product_cat'] : '','id="product_cat" class="form-control input-sm"')?>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="product_brand" class="input-group-addon">Brand</label>
                            <?php echo form_dropdown('product_brand', $brand_combo,isset($_GET['product_brand']) ? $_GET['product_brand'] : '','id="product_brand" class="form-control input-sm"')?>
                        </div>
                    </div>
                </div> 
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="supplier" class="input-group-addon">Supplier</label>
                            <?php echo form_dropdown('supplier', $suppliers,isset($_GET['supplier']) ? $_GET['supplier'] : '','id="supplier" class="form-control input-sm"')?>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="prd_tags" class="input-group-addon">Tags</label>
                            <?php echo form_input(array('autocomplete' => 'off', 'placeholder' => 'Search tags','id' => 'prd_tags',"class" => "form-control input-sm"))?>
                        </div>
                    </div>                    		
                </div>
            </div> 
		</div>
		<?php
        $span = '';
        if(isset($_GET['tag_id']))
        {
            if($_GET['tag_id'])
            {
                $tags = $this->brand_and_tag_model->get_tag_names_if_ids($_GET['tag_id'],$this->session->userdata('acc_no'));
                if($tags != false)
                {
                    foreach($tags as $key => $value)
                    {
                    	$span .= '<span class="tag label label-info">
									<input id="tag_id_'.$key.'" type="hidden" name="tag_id[]" value="'.$key.'">
									<span>'.$value.'</span>
									<a href="#" data-scale="" data-tag-id="" data-product-id="" class="remove_tag"><i class="remove glyphicon glyphicon-remove-sign glyphicon-white"></i></a> 
								  </span>';
					}
                }
            }
        }
        ?>      
        <div class="row form-group">
	        <div class="col-md-12" id="ajax_tags_div">
				<?php echo $span ?>
			</div>
		</div>            
        <div class="row">
	        <div class="col-md-12">
                <div class="btn-group">
                <button type="submit" class="btn btn-success btn-sm search_button loading_modal" id="prd_search"><i class="fa fa-search"></i> Search</button> 
                <button type="button" id="filter_button" class="btn btn-danger btn-sm"><i class="fa fa-filter"></i> Filter Options</button>
                <?php if(count($_GET) > 0) { ?>
                <?php echo anchor(base_url().'products','<i class="fa fa-times-circle"></i> Clear filter','class="btn btn-default btn-sm bg-danger"') ?>
                <?php } ?>
                </div>
            </div>
        </div>
		<?php
        if(!empty($_GET['product_stat']) || !empty($_GET['product_cat']) || !empty($_GET['product_brand']) || !empty($_GET['supplier']) || !empty($_GET['tag_id'])) {
			echo '<input type="hidden" id="toggle_filter" value="1">';
		} else {
			echo '<input type="hidden" id="toggle_filter" value="0">';				
        } 
		?>
	</div>
</div>    
<?php echo form_close() ?>
<?php
$tmpl = array (
	'table_open'   => '<table class="table table-striped table-condensed table-curved" id="product_table">',
);
$daylight_saving = date("I");
$header_keys = array("product_name","updated_at","supplier","variants","price","stock");
$filter_array = array('search_product','product_stat','product_cat','product_brand','supplier','tag_id');
if(isset($_GET['search_product']) || isset($_GET['product_stat']) || isset($_GET['product_cat']) || isset($_GET['product_brand']) || isset($_GET['supplier']) || isset($_GET['tag_id']))
{
	foreach($filter_array as $value)
	{
		if(isset($_GET[$value]) && !empty($_GET[$value]))
		{
			if(!is_array($_GET[$value]))
			{
				if(strlen($_GET[$value]) > 0)
				{
					$get_filter[$value] = $_GET[$value];
				}
			} else {
				$get_filter[$value] = $_GET[$value];
			}
		}
	}
} else {
	$get_filter = array("" => "");
}
$get_filter = strlen(http_build_query($get_filter)) > 1 ? "&".http_build_query($get_filter) : ''; // check this, but works good
$get_filter_str = substr($get_filter,1);
if(isset($_GET['sort']))
{
	foreach($header_keys as $h_values)
	{
		switch ($_GET['sort']) 
		{
		   case $h_values:
				if(isset($_GET['flow']))
				{		
					if($_GET['flow'] == "asc")
					{
						$get_array[$h_values.'_flow'] = "&flow=desc";
						$get_array[$h_values.'_arrow'] = '<i class="fa fa-caret-up">';
					} else {
						$get_array[$h_values.'_flow'] = "&flow=asc";
						$get_array[$h_values.'_arrow'] = '<i class="fa fa-caret-down">';
					}
				} else {
					$get_array[$h_values.'_flow'] = "&flow=asc";
					$get_array[$h_values.'_arrow'] = '<i class="fa fa-caret-down">';
				}
				$unset_array = array_diff($header_keys,array($h_values));
				foreach($unset_array as $u_values)
				{
					$get_array[$u_values.'_flow'] = "";
					$get_array[$u_values.'_arrow'] = '<i class="fa fa-sort">';
				}		
			break;
		}
	}
} else {
	foreach($header_keys as $h_values)
	{
		$get_array[$h_values.'_flow'] = "";
		$get_array[$h_values.'_arrow'] = '<i class="fa fa-sort">';
	}
}
$this->table->set_template($tmpl);	
$heading = array(
				'&nbsp;',
				anchor('products/lookup?sort=product_name'.$get_array['product_name_flow'].$get_filter,'Product '.$get_array['product_name_arrow'],'class="loading_modal"'),
				anchor('products/lookup?sort=updated_at'.$get_array['updated_at_flow'].$get_filter,'Updated '.$get_array['updated_at_arrow'],'class="loading_modal"'),
				anchor('products/lookup?sort=supplier'.$get_array['supplier_flow'].$get_filter,'Supplier '.$get_array['supplier_arrow'],'class="loading_modal"'),
				anchor('products/lookup?sort=variants'.$get_array['variants_flow'].$get_filter,'Variants '.$get_array['variants_arrow'],'class="loading_modal"'),
				anchor('products/lookup?sort=price'.$get_array['price_flow'].$get_filter,'Price '.$get_array['price_arrow'],'class="loading_modal"'),
				anchor('products/lookup?sort=stock'.$get_array['stock_flow'].$get_filter,'Stock '.$get_array['stock_arrow'],'class="loading_modal"'),
				array('data' => anchor('products/export?'.$get_filter_str,'<span class="label label-primary">Export Filtered Products</span>','style="text-align:center" class="hidden-print"')));
$this->table->set_heading($heading);
$this->load->helper('text');
if(count($results) > 0)
{	
	foreach($results as $row)
	{
		$main_variant_qs = !empty($row['variants']) ? '?main_variant=true' : '';
		$edit = anchor('products/'.$row['update_id'].'/edit'.$main_variant_qs,'<i class="fa fa-edit"></i> Edit','class="btn btn-sm btn-success loading_modal"');
		$hide = $row['status'] == 40 ? 
		' <a title="Show this product on sales" href="#" data-scale="'.$row['product_scale'].'" data-clause="1" data-hide-id="'.$row['update_id'].'" data-url="'.base_url().'products/show/id/'.$row['update_id'].'" class="btn btn-sm btn-danger hide_prd"><span class="glyphicon glyphicon-thumbs-up"></span> Show</a>' : 
		'<a  href="#" title="Hide this product from sales" data-scale="'.$row['product_scale'].'" data-clause="0" data-hide-id="'.$row['update_id'].'" data-url="'.base_url().'products/hide/id/'.$row['update_id'].'" class="btn btn-sm btn-danger hide_prd"><span class="glyphicon glyphicon-thumbs-down"></span> Hide</a>';

		$base = $row['update_id'];
		$ext = '';
		$root = APPPATH.'user_images/'.md5($this->session->userdata('acc_no')).'/products/'.$base.'_thumb';
		foreach (glob($root.".*") as $filename) {
			$ext = substr($filename,-3);
		}
		$image_href = $root.'.'.$ext;
		if(file_exists($image_href))
		{
			$image_href = '<img height="50" width="50" class="img-circle" src="'.base_url().$image_href.'?random='.time().'" />';
		} else {
			//$image_href = base_url().APPPATH.'images/assets/noproduct.jpg';	
			$image_href = '<i class="fa fa-cube fa-3x"></i>';								
		}
		$s = $row['variants'] > 1 ? 's' : '';
		$variants = !empty($row['variants']) ? '<button data-id="'.$row['product_id'].'" class="btn btn-xs btn-primary variant_main">'.$row['variants'].' variant'.$s.' found</button>&nbsp;<span class="arr"><i class="fa fa-caret-up fa-fw"></i></span>' : '--';
		$pos_id = strlen($row['pos_id']) > 0 ? "<div><span class='label label-default'>Kilo Id: ".$row['pos_id']."</span></div>" : NULL;
		$this->table->add_row(
						array(
						'data' => $image_href, 
						'align' => 'center',
						"title" => $row['product_name']." | SKU: ".$row['sku']
						),
						anchor('products/'.$row['product_id'],$row['product_name'],'class="btn btn-xs btn-default"').
						'<div><span class="label label-default">SKU: '.ellipsize($row['sku'],15, .5).'</span></div>'.$pos_id,
						'<h6><span class="glyphicon glyphicon-pencil"></span> '.unix_to_human(gmt_to_local(strtotime($row['updated_at']),$timezone, $daylight_saving)).'</h6>'.
							'<h6><span class="glyphicon glyphicon-off"></span> '.unix_to_human(gmt_to_local(strtotime($row['created_at']),$timezone, $daylight_saving)).'</h6>'						,
						array('data' => $row['supplier'],'class' => 'h6'),
						array('data' => $variants),$row['price'],$row['stock'],
						array('data' => '<div class="last_td"><div class="btn-group btn-group-sm">'.$edit.$hide.'</div></div>','class' => 'no-hover')
						);	
	}
	$plural = $tot_prd_count > 1 ? 's' : '';
} else {
	$this->table->add_row(array('--','--','--','--','--','--','--','--'));
	$plural = '';
}
$prd_tbl = $this->table->generate();

?>
<div class="panel panel-default" id="product_panel">
    <div class="panel-heading"><i class="fa fa-list-alt"></i> Products Table</div>
    <div class="panel-body">
		<div class="table-responsive">
			<div class="table-curved-div">
				<?php echo $prd_tbl ?>
            </div>
		</div>            
	</div>
    <div class="panel-footer">
		<i class="fa fa-hand-o-right"></i> <?php echo 'Showing '.$page_prd_count.' of '.$tot_prd_count.' row'.$plural ?>
	</div>
</div>   
<?php
echo $links;
?>
