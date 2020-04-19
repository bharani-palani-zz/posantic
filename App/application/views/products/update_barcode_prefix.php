<script language="javascript">
$(function(){
	$('.prefix_range').on('click',function(){
		$('.prefix_range').removeClass('btn-primary')
		var val = $(this).attr('data-value');
		$('#prefix_string').val(val)
		$(this).attr('data-value',val).addClass('btn-primary')
	});
});
</script>
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4>Change weighing scale barcode prefix</h4>
</div>
<?php 
echo form_open(base_url().'products/change_barcode_prefix',array('id' => 'form_change_barcode_prefix'));
echo form_hidden('redirect',$this->agent->referrer());
$prefix = $this->product_model->get_barcode_prefix($this->session->userdata('acc_no'));
?>
<div class="modal-body">
	<div class="panel panel-default">
	    <div class="panel-heading text-danger">
            <div class="input-group">
                <div class="btn-group btn-group-sm">
                	<button type="button" class="btn btn-primary">Select Prefix</button>
                    <?php 
                    $prefix_array = array(20,21,22,23,24,25,26,27,28,29,99);
                    foreach($prefix_array as $prefix_val) {
                        $active = $prefix == $prefix_val ? "btn-danger active" : 'btn-default';
                    ?>
                        <button class="btn <?php echo $active ?> prefix_range " data-value="<?php echo $prefix_val ?>" type="button"><?php echo $prefix_val ?></button>
                    <?php } ?>
                </div>
                <input type="hidden" name="prefix_string" id="prefix_string" value="<?php echo $prefix; ?>">
            </div>        
        </div>
	    <div class="panel-body small">
            <p>
                Eg: Consider some products "Rice/Milk/Cable" sold by "Weight/Litre/Meter" respectively @ 52.40 KILO, billing barcode will be <?php echo $prefix ?>12345052403. 
                Where "<?php echo $prefix ?>" is the prefix, "12345" is the product ID, "05240" is the customer uptake volume and last digit is the checksum as shown below.
            </p>
            <p align="center">
                <img class="pos_menu_a" src="<?php echo base_url().'products/temp_barcode/'.$prefix.'12345052403' ?>">
            </p>
            <p>
                Note: Instore custom coupon barcodes starts with series "20 - 29 or 99". This is a global practice, so it is advised to use one of these range. 
                Please note your other SKU/UPC/Barcodes should not start with these series, else the product cant be created. Do not start sales without configuring prefix correctly,
                else refresh sales again. 
            </p>
		</div>
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success" name="add_single_tax" id="add_single_tax"><i class="fa fa-edit"></i> Update</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-remove"></i> Close</button>
</div>

<?php
echo form_close();
?>