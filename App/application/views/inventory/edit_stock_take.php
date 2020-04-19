<?php
$daylight_saving = date("I");
$created = date('d-m-Y h:i a',gmt_to_local(strtotime($details['created_at']),$timezone, $daylight_saving));
?>

<div class="h4"><i class="fa fa-clipboard fa-fw"></i>Perform Stock Take</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" id="stocktake_id" value="<?php echo $details['stocktake_index'] ?>">
<input type="hidden" id="product_url" value="<?php echo base_url().'api/1.0/stocktake_products/'.$details['stocktake_index']?>">
<input type="hidden" id="stocktakes_url" value="<?php echo base_url().'api/1.0/stocktakes'?>">
<input type="hidden" id="stocktake_products_url" value="<?php echo base_url().'api/1.0/stocktake_sub_products'?>">
<input type="hidden" id="ST_post_url" value="<?php echo base_url().'api/1.0/stocktake_post_products'?>">
<input type="hidden" id="stocktake_counted_products" value="<?php echo base_url().'api/1.0/stocktake_counted_products/'.$details['stocktake_index'] ?>">
<input type="hidden" id="product_count_post_url" value="<?php echo base_url().'api/1.0/stocktake_post_product_count' ?>">
<div class="">
    <div class="row" id="stock_take_div">
    
        <div class="col-lg-8 col-md-8" id="product_div">
            <div id="st_text">
            	<h5>
					<?php echo $details['stocktake_name'] ?>&nbsp; 
                    <span class="label label-danger"><i class="fa fa-clock-o fa-fw"></i><?php echo $created ?></span>&nbsp;
                    <span class="label label-danger"><i class="fa fa-map-marker fa-fw"></i><?php echo $details['location'] ?></span>
                </h5>
            </div>
            <div class="row" id="search_div">
                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="search_prd" class="input-group-addon hidden-sm hidden-xs"><i class="fa fa-qrcode fa-fw"></i>Scan / search products</label>
                            <h4 class="visible-xs visible-sm">Search products</h4>
                            <?php echo form_input(array('autocomplete' => 'off','spellcheck' => "false",'name' => 'search_prd', 'id' => 'search_prd',"class" => "form-control input-lg col-sm-12 col-xs-12")) ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <div class="input-group">
                            <?php echo form_input(array('autocomplete' => 'off','name' => 'count_tb', 'id' => 'count_tb',"class" => "form-control input-lg","value" => 1)) ?>
                            <span class="input-group-btn"><button type="button" id="count_this" class="btn btn-success btn-lg disabled"><span style="font-size:14px;">COUNT</span></button></span>
                        </div>
                    </div>
                </div>
            </div>   
            <div id="review_div" class="table-responsive">
				<table class="table table-hover">
                	<thead>
                        <tr>
                            <th>Product</th>
                            <th>Expected</th>
                            <th>Counted</th>
                        </tr>
                    </thead>
                    <tbody id="append_review"></tbody>
                </table>
			</div>            
        </div>
        
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" id="cart_div">
            <div class="text-center text-uppercase" id="cart_header"><b>Counted items</b></div>
        	<div id="cart_list">
			</div>
        </div>
    </div>
	<div class="row" id="footer_div">    
		<div class="col-lg-12">
        	<div class="pull-right">
        	<a href="<?php echo base_url('inventory/stock_take') ?>" type="button" class="btn btn-danger btn-md"><i class="fa fa-pause fa-fw"></i>Pause Counting</a>
            <?php echo anchor('inventory/stock_take/finalise/id/'.$details['stocktake_index'],'<span class="glyphicon glyphicon-thumbs-up"></span> Finalise Counting','class="btn btn-success btn-md"') ?>
            </div>
        </div>
	</div>
</div>
