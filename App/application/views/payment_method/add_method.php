<script language="javascript">
$(function(){
	$('#error_method_name').hide()
	$('#form_method_add').submit(function(){
		if($('#method_label').val().length < 1)
		{
			$('#error_method_name').show()
			return false
		}
	});
	$('.method_options').click(function(){
	if($(this).hasClass('btn-success') == false)
	{
		$('.method_options').removeClass('btn-success')
		$(this).addClass('btn-success')
		$val = $(this).text()
		$method_id = $(this).attr('data-method-id')
		$form_loader = $('#form_loader')
		$form_content = $('#form_content')
		$form_desc_content = $('#form_desc_content')
		$form_desc_content.empty()
		elm = '<i class="fa fa-circle-o-notch fa-spin"></i>'
		$html = '';
		$desc_html = ''
		$type_id = '';
		$.ajax({ 
			url: $('#pay_method_url').val(),
			data: { method_id: $method_id, csrf_test_name : $("input[name=csrf_test_name]").val()},
			type: "GET",
			beforeSend: function(data){
				$form_content.empty()
				$form_loader.html('<h2>'+elm+'</h2>')
			},
			success: function(data){
				var obj = JSON.parse(data)
				$.each(obj.type_data, function (i,v) {
					attr_type = ''
					html_type = v.html_type
					$sub_caption = v.html_label_sub_caption.length > 0 ? '<span class="input-group-addon"><small>'+v.html_label_sub_caption+'</small></span>' : '';
					if(html_type == "text")
					{ 
						attr_type = '<label><div class="input-group"><span class="input-group-addon">'+v.html_label+'</span><input type="text" autocomplete="off" class="form-control input-sm" placeholder="'+v.placeholder+'" name="payment_method['+v.type_id+']['+v.attr_name+']" value="'+v.default_attr_value+'">'+$sub_caption+'</div></label>' 
					} else if(html_type == "select") {
						if(v.html_load_db_data.length > 0)
						{
							$.ajax({ 
								url: $('#country_type_fields_url').val(),
								data: { load_code_for: v.html_load_db_data, csrf_test_name : $("input[name=csrf_test_name]").val()},
								type: "GET",
								success: function(data){
									$c_html = '';
									var obj2 = JSON.parse(data)
									$.each(obj2,function(key,value){
										$c_html += '<option value="'+key+'">'+value+'</option>'
									});
									attr_type = '<label><div class="input-group"><span class="input-group-addon">'+v.html_label+'</span><select class="form-control input-sm" name="payment_method['+v.type_id+']['+v.attr_name+']">'+$c_html+'</select>'+$sub_caption+'</div></label>'
									$form_content.append(attr_type)
								}
							});
						}
					} else if(html_type == "boolean") {
						attr_type = '<div class="checkbox"><label><input type="checkbox" checked="checked" name="payment_method['+v.type_id+']['+v.attr_name+']" value="'+v.default_attr_value+'">'+v.html_label+'</label><small>'+$sub_caption+'</small></div>'
					}
					$html += attr_type
					$thumb = v.is_integration == 1 ? "img-thumbnail" : "";
					var qs = new Date - 1;
					$img = v.image_location.length > 0 ? '<p align="center"><img class="'+$thumb+'" src="'+$('#img_root').val()+v.image_location+'?qs='+qs+'" /></p>' : '';
					$desc_link = v.external_type_link.length > 0 ? '<br><br><a target="_blank" href="'+v.external_type_link+'" class="btn btn-success btn-block">Know more...</a>' : ''
					//$desc_html = v.type_description.length > 0 ? '<div class="">'+$img+v.type_description+$desc_link+'</div>' : ''
					$desc_html = '<div class="">'+$img+v.type_description+$desc_link+'</div>'
					$type_id = v.type_id
				});
				$html2 = '<label><div class="input-group"><label for="method_label" class="input-group-addon">Label</label><input placeholder="Multilingual" autocomplete="off" type="text" class="form-control input-sm" name="method_label" id="method_label"><label for="method_sort" class="input-group-addon">Sort</label><input type="number" class="form-control input-sm" name="method_sort" id="method_sort" id="method_label"></div></label>';
				$form_content.append($html2+$html)
				$form_desc_content.html($desc_html)
				$('#method_label').val($val)
				$('#add_method').removeClass('disabled')
			},
			complete: function(data){
				$form_loader.empty()
			},
			error: function(xhr,textStatus, errorThrown) {
				alert(errorThrown);
			}, 		
		});
	}
	});
});
</script>

<?php 
echo form_open(base_url().'payment_method/insert_method',array('id' => 'form_method_add'));
?>
<div class="modal-header">
	<input type="hidden" id="pay_method_url" value="<?php echo base_url('payment_method/get_pay_type_fields') ?>">
	<input type="hidden" id="country_type_fields_url" value="<?php echo base_url('payment_method/get_country_type_fields') ?>">
    <input type="hidden" id="img_root" value="<?php echo base_url().APPPATH.'images/' ?>">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
	<h4><span class="glyphicon glyphicon-plus"></span> Add Payment Method</h4>
</div>
<div class="modal-body">
    <div class="row form-group">
    	<?php if(count($method_combo['Default']) > 0) { ?>
        <div class="col-lg-12">
        	<h5><span class="label label-danger">Default Methods</span></h5>
            <div class="btn-group" role="group" aria-label="...">
            	<?php foreach($method_combo['Default'] as $d_key => $d_val) { ?>
                <div class="btn-group" role="group">
                    <button type="button" data-method-id="<?php echo $d_key ?>" class="btn btn-default method_options" id="1"><?php echo $d_val ?></button>
                </div>
                <?php 
				} 
				?>
			</div>		
        </div>
    	<?php } ?>
		<?php if(count($method_combo['Integrated']) > 0) { ?>
        <div class="col-lg-12">
        	<h5><span class="label label-danger">Integrated Methods</span></h5>
            <div class="btn-group" role="group" aria-label="...">
		        <button type="button" class="btn btn-default disabled"><i class="fa fa-credit-card fa-fw"></i></button> 
            	<?php foreach($method_combo['Integrated'] as $i_key => $i_val) { ?>
                <div class="btn-group" role="group">
                    <button type="button" data-method-id="<?php echo $i_key ?>" class="btn btn-default method_options" id="1"><?php echo $i_val ?></button>
                </div>
                <?php 
				} 
				?>
			</div>		
        </div>
    	<?php } ?>
	</div>  
    <p id="error_method_name" class="messageContainer text-danger form_errors"><span class="glyphicon glyphicon-remove"></span> Required / Max - 15 characters</p>
    <div class="row">
    	<div class="col-lg-6 col-md-6">
	        <div align="center" id="form_loader"></div>
		    <div id="form_content"></div>
        </div>
    	<div class="col-lg-6 col-md-6">
		    <div id="form_desc_content">
            </div>
        </div>
	</div>        
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success disabled" name="add_method" id="add_method"><i class="fa fa-save"></i> Save Method</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
</div>

<?php
echo form_close();
?>