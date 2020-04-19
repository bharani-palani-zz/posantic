<div class="welcome">
    <div class="jumbotron">
    <?php
    $http = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $cloudurl = strtolower($http.$master_data[15].'.'.$this->session->userdata('pos_hoster_cmp').'.com');
    ?>
    <h3><span class="label label-success">Hello,</span></h3>
    <h5 class="text-success">Welcome and great to see you choosing <span class="text-uppercase label label-success"><?php echo strtolower($this->session->userdata('pos_hoster_cmp')); ?></span></h5>
	<h5 class="text-success">
    We've configured the following setup to run a successful store operating with sales, credits, returns, loyalties, gift vouchers and more.
    </h5>
</div>

<ul class="list-group">
    <li class="list-group-item">Business name <span class="badge"><?php echo $master_data[14] ?></span></li>
    <li class="list-group-item">Cloud URL <span class="badge"><?php echo $cloudurl ?></span></li>
    <li class="list-group-item">
    	Outlets / registers  <span class="badge">1 / 1</span>
        <br><?php echo anchor('setup/outlets_and_registers','<i class="fa fa-plus fa-fw"></i>Add Outlets / registers','class="btn btn-xs btn-danger"')?>
    </li>
    <li class="list-group-item">
        Demo Products / tax / payment methods <span class="badge">3 / 1 / 2</span>
        <br><?php echo anchor('products/add_product','<i class="fa fa-plus fa-fw"></i>Add Products','class="btn btn-xs btn-danger"')?>
        <div><small>*Demo products can be deleted any time and you can still add more products to your account</small></div>
    </li>
    <li class="list-group-item">Quick touch layout <span class="badge">1</span></li>
    <li class="list-group-item">
    	Primary user (store account owner)
        <br><?php echo anchor('users/add','<i class="fa fa-plus fa-fw"></i>Add new users','class="btn btn-xs btn-danger"')?>        
        <span class="badge">1</span>
    </li>
    <li class="list-group-item">
        Currency 
        <br><?php echo anchor('setup','<i class="fa fa-pencil fa-fw"></i>Configure localization','class="btn btn-xs btn-danger"')?>        
        <span class="badge"><?php echo $account['currency'] ?></span>
    </li>
    <li class="list-group-item">Timezone <span class="badge"><?php echo $account['timezone_name'] ?></span>
        <div><small>*You can connect multiple outlets associated to timezone, locality and currency</small></div>
    </li>
</ul>    
<?php echo anchor(base_url(),'YOUR THERE ... START SELLING NOW ...','class="btn btn-success btn-block"') ?>
</div>