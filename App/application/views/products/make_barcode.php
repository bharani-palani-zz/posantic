<?php
if($this->session->flashdata('form_errors')) { 
	echo '<div class="alert alert-md alert-danger fade in">';
	echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
	echo '<span class="glyphicon glyphicon-remove-sign"></span> '.$this->session->flashdata('form_errors');
	echo '</div>';
}
?>
<h4><i class="fa fa-barcode fa-lg"></i> Print Barcodes <i class="fa fa-barcode fa-lg"></i></h4>
<div class="panel panel-default">
    <div class="panel-heading">
		<?php echo $product_name ?> - <?php echo $scale_str[$product_scale] ?>
    </div>
    <div class="panel-body">
		<div class="jumbotron text-center">
			<div class="container-fluid">        
                <img class="img-thumbnail" src="<?php echo base_url().'products/draw_jumbo_barcode/'.$sku ?>">
                <hr>
                <div class="text-center"><kbd><?php echo $sample ?></kbd></div>
			</div>	
        </div>
		<?php 
        echo form_open(base_url().'barcode/make_barcode'); 
        echo form_hidden(array('product_id' => $product_id));
        echo form_hidden(array('product_name' => $product_name));
        echo form_hidden(array('variant_name' => $variant_name));
        echo form_hidden(array('product_scale' => $product_scale));
        echo form_hidden(array('retail_price' => $retail_price));
        echo form_hidden(array('pos_id' => $pos_id));
        echo form_hidden(array('barcode_prefix' => $barcode_prefix));
        ?>
    	<div class="row">
        	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<?php if($product_scale != 2) { ?>
            	<div class="form-group input-group">
                	<label for="bcode_val" class="input-group-addon">Barcode Data</label>
					<?php echo form_input(array('name' => 'bcode_val','id' => 'bcode_val','class' => 'form-control input-sm','value' => $sku,'readonly' => 'readonly'))?>
                </div>
                <?php } else { ?>
            	<div class="form-group input-group">
                	<label for="kilo_val" class="input-group-addon" data-toggle="popover" data-placement="top" data-content="<?php echo $sku_caption ?>">KILO Value</label>
					<?php echo form_input(array('autocomplete' => 'off','name' => 'kilo_val','id' => 'kilo_val','class' => 'form-control input-sm','value' => 1 ))?>
                </div>
                <?php } ?>
            	<div class="form-group input-group">
                	<label for="codetype" class="input-group-addon">Barcode Type</label>
					<?php echo form_dropdown('codetype',$bcode_array,'ean13','id="codetype" class="form-control input-sm"')?>
                </div>
            	<div class="form-group input-group">
                	<label for="printertype" class="input-group-addon">Printer Type</label>
					<?php echo form_dropdown('printertype',$printer_type,1,'id="printertype" class="form-control input-sm"') ?>
                	<span class="input-group-addon">*Only for raw print output</span>
                </div>            
            	<div class="form-group input-group">
                	<label for="fitpage" class="input-group-addon"><i class="fa fa-file-pdf-o"></i> PDF fit page</label>
					<?php echo form_dropdown('fitpage',$fit_page,14,'id="fitpage" class="form-control input-sm"') ?>
                	<span class="input-group-addon">*Only for PDF Output</span>
                </div>
			</div>
        	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            	<div class="form-group input-group">
                	<label for="bcode_count" class="input-group-addon">Barcode Count</label>
					<?php echo form_input(array('autocomplete' => 'off','name' => 'bcode_count','id' => 'bcode_count','class' => 'form-control input-sm','value' => 1)) ?>
                </div>
            	<div class="form-group input-group">
                	<label for="bcode_height" class="input-group-addon">Barcode Height</label>
					<?php echo form_input(array('autocomplete' => 'off','name' => 'bcode_height','id' => 'bcode_height','class' => 'form-control input-sm','value' => 50,'placeholder' => '*Min-10 / Max-500')) ?>
                </div>
            	<div class="form-group input-group">
                	<label for="bcode_font" class="input-group-addon">Barcode Text font Size</label>
					<?php echo form_input(array('autocomplete' => 'off','name' => 'bcode_font','id' => 'bcode_font','class' => 'form-control input-sm','value' => 3, 'placeholder' => '*Min-3 / Max-5')) ?>
                </div>
            </div>
        </div>
		<div class="row">
        	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                	<div class="col-lg-4 col-md-6">
                        <div class="form-group input-group">
                            <label for="outlet" class="input-group-addon">Issuing outlet</label>
                            <?php echo form_dropdown('outlet',$company,'','id="outlet" class="form-control input-sm"')?>
                        </div>
					</div>
                	<div class="col-lg-2 col-md-3">
                        <div class="form-group input-group">
							<?php 
                            echo form_checkbox(array('data-on-color' => 'success', 'data-off-color' => 'danger', 'data-on-text' => 'Inclusive', 'data-off-text' => 'Exclusive', 'data-label-text' => 'Tax','data-size' => 'small','name' => 'tax_switch', 'checked' => true , 'value' => 30))
                            ?>
						</div>
					</div>
                    <div class="col-lg-2 col-md-3">
                        <div class="form-group input-group">
							<?php 
                            echo form_checkbox(array('data-on-color' => 'success', 'data-off-color' => 'danger', 'data-on-text' => 'Show', 'data-off-text' => 'Hide', 'data-label-text' => 'Caption','data-size' => 'small','name' => 'cap_switch', 'checked' => true , 'value' => 30))
                            ?>
						</div>
                    </div>
                </div>
			</div>
		</div>
        <div class="form-group">
            <button type="submit" name="bcode_sub" value="raw" id="bcode_sub" class="btn btn-success btn-sm search_button loading_modal" ><i class="fa fa-columns"></i> Raw Print</button> 
            <button type="submit" name="bcode_down" value="down" id="bcode_down" class="btn btn-danger btn-sm"><i class="fa fa-file-pdf-o"></i> Print PDF</button>
        </div>				        
    <?php echo form_close() ?>
    </div>
</div>