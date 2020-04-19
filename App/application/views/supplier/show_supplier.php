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
<h4><i class="fa fa-truck"></i> Supplier details</h4>
<div class="well well-sm hidden-print">
	<?php echo anchor('supplier/'.$supp_details['supp_id'].'/edit','<i class="fa fa-edit fa-fw"></i> Edit Supplier','class = "btn btn-sm btn-success"') ?>
    <?php if($supp_details['is_delete'] == 30 ){?>
    	<div class="pull-right"><?php echo '<span>'.anchor('supplier/'.$supp_details['supp_id'].'/delete','<i class="fa fa-trash-o"></i> Delete Supplier','class = "btn btn-sm btn-danger" data-confirm="Delete this Supplier? This cant be restored..."').'</span>' ?></div>
    <?php } ?>    
</div>
<div class="panel panel-default" id="product_panel">
    <div class="panel-heading"><i class="fa fa-navicon fa-fw"></i> <?php echo $supp_details['cmp_name'] ?></div>
    <div class="panel-body">
		<div class="row">
        	<div class="col-lg-4 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-user"></i> <?php echo $supp_details['auth_pers'] ?></div>
                    <div class="panel-body">
                        <address>
                            <?php if(strlen($supp_details['addrr1']) > 0 ) { ?>
                                <h5><?php echo $supp_details['addrr1']?></h5>
                            <?php } ?>
                            <?php if(strlen($supp_details['addrr2']) > 0 ) { ?>
                                <h5><?php echo $supp_details['addrr2']?></h5>
                            <?php } ?>
                            <?php if(strlen($supp_details['city']) > 0 ) { ?>
                                <h5><?php echo $supp_details['city']?></h5>
                            <?php } ?>
                            <?php if(strlen($supp_details['postal_code']) > 0 ) { ?>
                                <h5><?php echo $supp_details['postal_code']?></h5>
                            <?php } ?>
                            <?php if(strlen($supp_details['state']) > 0 ) { ?>
                                <h5><?php echo $supp_details['state']?></h5>
                            <?php } ?>
                            <?php if(strlen($supp_details['country']) > 0 ) { ?>
                                <h5><?php echo $supp_details['country']?></h5>
                            <?php } ?>
                        </address>
					</div>
				</div>                    				
			</div>
        	<div class="col-lg-4 col-md-6">
                <ul class="list-group">
                	<li class="list-group-item active"><i class="fa fa-hand-o-right"></i> Contacts</li>
                    <?php if(strlen($supp_details['mobile']) > 0 ) { ?>
                        <li class="list-group-item"><a class="btn btn-xs btn-danger" href="tel:<?php echo $supp_details['mobile']?>"><i class="fa fa-mobile"></i> <?php echo $supp_details['mobile']?></a></li>
                    <?php } ?>
                    <?php if(strlen($supp_details['ll']) > 0 ) { ?>
                        <li class="list-group-item"><a class="btn btn-xs btn-default" href="tel:<?php echo $supp_details['ll']?>"><span class="glyphicon glyphicon-phone-alt"></span> <?php echo $supp_details['ll']?></a></li>
                    <?php } ?>
                    <?php if(strlen($supp_details['fax']) > 0 ) { ?>
                        <li class="list-group-item"><a class="btn btn-xs btn-default" href="tel:<?php echo $supp_details['fax']?>"><i class="fa fa-fax fa-fw"></i> <?php echo $supp_details['fax']?></a></li>
                    <?php } ?>
                    <?php if(strlen($supp_details['web']) > 0 ) { ?>
                        <li class="list-group-item"><a class="btn btn-xs btn-default" href="<?php echo $supp_details['web']?>" target="_blank"><i class="fa fa-cloud"></i> <?php echo $supp_details['web']?></a></li>
                    <?php } ?>
                    <?php if(strlen($supp_details['email']) > 0 ) { ?>
                        <li class="list-group-item"><a class="btn btn-xs btn-default" href="mailto:<?php echo $supp_details['email'];?>"><i class="fa fa-envelope"></i> <?php echo $supp_details['email']?></a></li>
                    <?php } ?>
        
                </ul>   
			</div>                     
        	<div class="col-lg-4 col-md-6">
				<?php
                $address_array = array(
                                $supp_details['addrr1'],
                                $supp_details['addrr2'],
                                $supp_details['city'], 
                                $supp_details['state'],
                                $supp_details['postal_code'], 
                                array_key_exists($supp_details['country'],$countries_assoc) ? $countries_assoc[$supp_details['country']] : ''
                                );
                $address_array = array_filter($address_array);
                $address_str = implode("+",$address_array);	
                ?>            
                <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-location-arrow"></i> Map</div>
                    <div class="panel-body">
                        <input type="hidden" id="cust_geo_address" value="<?php echo $address_str ?>">
                        <input type="hidden" id="cust_geo_zoom" value="<?php echo count($address_array) == 1 ? 4 : 15 ?>">
						<div id="map-container" style="height:200px;"></div>
					</div>
				</div>                    
			</div>
		</div>
        <?php if(strlen($supp_details['supp_description']) > 0 ) { ?>
        <div class="row">
            <div class="col-lg-12">
                <ul class="list-group">
                    <li class="list-group-item active">Description</li>
                    <li class="list-group-item"><?php echo $supp_details['supp_description']?></li>
                </ul>
            </div>
        </div>
		<?php } ?>
                            
	</div>
</div>    
