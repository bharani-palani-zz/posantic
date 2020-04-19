<script language="javascript">
$(document).ready(function(){
	var opts = Number($('.old_group_taxes:first option').length)
	if($('.nodes').length < opts)
	{
		$('.add_row').show()	
	} else {
		$('.add_row').hide()	
	}

	var json = jQuery.parseJSON($('#hid_json').val())
	enable_disable();
	$('.add_row').on('click',function(){
		enable_disable()
		var opts = Number($('.old_group_taxes:first option').length)
		if($('#edit_grp_table tr.nodes .old_group_taxes').length < opts)
		{
			$elm = '<tr class="nodes"><td><div class="input-group"><select class="old_group_taxes form-control input-sm" name="group_taxes[]" >';
			$.each(json,function(index,item){
				$elm += '<optgroup label="'+index+'"><option value="">--</option>';
				$.each(item,function(i,v){
					$elm += "<option value="+i+">"+v+"</option>"
				});
			});
			$elm += '</select><span class="input-group-addon"><a href="#" class="del_row"><i class="glyphicon glyphicon-remove-sign text-danger"></i></a></span></div></td></tr>';
			$($elm).appendTo($("#edit_grp_table tbody"));
			serialize_tbody()
			enable_disable();
			if($('#edit_grp_table tr.nodes .old_group_taxes').length == opts)
			{
				$('.add_row').hide()	
			}
		} 
	});
	$('.old_group_taxes').on('change click',function() {
		$('.old_group_taxes option').attr('disabled',false);
		enable_disable()
	});	
	//$('#edit_grp_table .del_row').on('click',function(e) {
	$(document).on("click", "a.del_row" , function(e) {		
		e.preventDefault();
		$(this).parent().parent().parent().remove()
		var opts = Number($('.old_group_taxes:first option').length)
		if($('#edit_grp_table tr.nodes .old_group_taxes').length < opts)
		{
			$('.add_row').show()	
		}
		enable_disable()
		serialize_tbody()	
	});
	
	
});
function enable_disable()
{
	$('.old_group_taxes').each( function(){
		var $this = $(this);
		$('.old_group_taxes').not($this).find('option').each(function(){
		   if($(this).attr('value') == $this.val())
			   $(this).attr('disabled',true);
		});
	});	
}
function serialize_tbody()
{
	m=0;
	$('#edit_grp_table tr.nodes').each(function(){
		$(this).find('.old_group_taxes').attr({
			id: 'group_taxes'+m,
		})
		m++
	});	
}

</script>

<?php 
echo form_open(base_url().'setup/update_group_tax/'.$group_tax_id,array('id' => 'form_edit_group_tax'));
echo form_hidden('redirect',$this->agent->referrer())
?>
<input type="hidden" id="hid_json" value="<?php echo htmlspecialchars(json_encode($get_single_taxes_combo))?>">
<div class="modal-header">
    <button class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4>Edit Group Tax</h4>
</div>
<div class="modal-body">
    <div class="panel panel-default">
    <div class="input-group pad-5px">
      <label for="parent_name" class="input-group-addon font-12px">
          Group Tax Name
      </label>
      <?php echo form_input(array('autocomplete' => 'off', 'size' => 30, 'value' => $parent_name, 'name' => 'parent_name', 'class' => 'form-control', 'id' => 'parent_name','placeholder' => 'Max 25 Characters')) ?>
    </div>
	</div>
    <div class="panel panel-default">
        <div class="panel-heading">
              <label for="tax_groups" class="control-label">Associated Taxes</label>
        </div>
        <div class="panel-body">
            <table id="edit_grp_table" class="table table-bordered">
                <?php $j=0; foreach($group_tax_id_name as $key => $childs){ ?>
                    <?php echo form_hidden('hid_all_taxes[]',$key) ?>
                    <tr class="nodes no-hover">
                        <td>
                        <div class="input-group">
                        <?php 
	                        $del = ($j > 1) ? '<span class="input-group-addon"><a href="#" class="del_row"><i class="glyphicon glyphicon-remove-sign text-danger"></i></a></span>' : '<span class="input-group-addon"><i class="glyphicon glyphicon-ok-circle text-danger"></i></span>';
                        echo form_dropdown('group_taxes[]',$get_single_taxes_combo,$key,'class = "old_group_taxes form-control input-sm"').$del; 
                        ?>
                        </div>
                        </td>
                    </tr>
                <?php 
                    $j++;
                } 
                ?>
            </table>
            <button type="button" class="add_row btn btn-xs btn-primary">Add more Taxes</button>
        </div>
        <div class="panel-footer">
        	<div>
            	<small><span class="glyphicon glyphicon-pushpin"></span> Updated Group tax rate will recompute all the retail product's price connected with this tax.</small>
            </div>
		</div>
	</div>
</div>
<div class="modal-footer">
    <button type="submit" name="edit_group_tax" class="btn btn-success"><i class="fa fa-save fa-fw"></i>Update</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>Close</button>
</div>
<?php
echo form_close();
?>