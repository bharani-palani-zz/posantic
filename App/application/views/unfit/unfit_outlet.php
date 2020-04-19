<?php
$header['view']['title'] = 'Show Outlet';
$header['head']['style'][0] = link_tag(POS_CSS_ROOT.'app_Styles.css')."\n";
$header['priv'] = $this->session->userdata('privelage');
$role = $this->roles_model->get_roles($header['priv']);
$header['role_code'] = $role[0][0];
$header['role_name'] = $role[0][1];
$header['top_menu'] = $this->menu_model->get_menu($header['priv']);
$header['activate_tab'] = 1;
$this->load->view('top_page/top_page',$header);
?>
<div class="jumbotron" align="center">
	<div class="container-fluid">
        <h2>Your Current Plan does not support in adding outlets</h2>
        <h3>Please upgrade your account to handle multiple outlets</h3>
        <p><?php echo anchor(base_url().'account','Upgrade now >>','class = "btn btn-lg btn-success"') ?></p>
	</div>
</div>

<?php
$this->load->view('bottom_page/bottom_page');
?>