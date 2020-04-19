<div class="panel-group" id="accordionTwo">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionTwo" href="#collapseFour">Import Products</a>
			</div>
		</div>
		<div class="panel-collapse collapse in" id="collapseFour">
			<div class="panel-body">
                <div class="col-md-12 text-center">
                    <label class="btn btn-primary" for="my-file-selector">
                        <?php echo form_upload(array('name' => 'userfile', 'id' => 'my-file-selector', 'style' => 'display:none;')) ?>
                        Choose CSV file...
                    </label>
                </div>
			</div>
			<div class="list-group">
				<li class="list-group-item"> 
                    Maximum of 500 products(i.e., csv rows) can be uploaded on one shot. If exceeded upload is dropped.
                    <a href="<?php echo base_url().'inventory/csv_sample'?>" class="btn btn-xs btn-danger">Download</a> the sample to upload.
                </li>
				<?php if($supplier_note == true) { ?>
				<li class="list-group-item"> 
                    If your csv product's sku are not associated with your supplier, those products are skipped.
                    Dont worry, still you can add products to any suppliers in the edit page.
                </li>
				<li class="list-group-item"> 
                    3 CSV fields: "SKU", "Supplier_price", "quantity". The supplier_price field is optional.
                    You can leave the supplier_price filed blank. We'll consider that from the product menu, else
                    type your prefered supplier price to inform your suppliers. 
                </li>
                <?php } ?>
			</div>
		</div>
	</div>
</div>
