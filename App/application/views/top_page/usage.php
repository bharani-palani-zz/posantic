<div class="container-fluid small usage_div">
    <?php 
	if(is_finite($memory_limit_gb)) { ?>
    <div class="row text-muted">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><?php echo $usage_str ?></div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">Usage</div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-right"><?php echo $memory_limit_gb ?>Gb</div>
    </div>
    <?php $active = $usage_percent > 90 ? 'active' : NULL ?>
    <div class="progress progress-striped <?php echo $active ?>" style="text-align:center;">
        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?php echo $usage_percent ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $usage_percent ?>%;">
            <span class="progress-value" style="position:absolute;right:0;left:0; color: black;"><small><?php echo $usage_percent ?>%</small></span>
        </div>
    </div>                    
    <?php } else { ?>
    <div class="row">
        <div class="col-lg-12"><br></div>
    </div>
    <div class="progress progress-striped">
        <div id="usage_progress" class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
            <span class="progress-value" style="position:absolute;right:0;left:0;"><small><b><i class="fa fa-database"></i> <?php echo $usage_str ?></b></small></span>
        </div>
    </div>               
    <?php } ?>
</div>


