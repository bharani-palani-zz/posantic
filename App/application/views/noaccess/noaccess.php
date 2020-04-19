<?php
$header['view']['title'] = 'No Access';
$header['priv'] = $this->session->userdata('privelage');
$role = $this->roles_model->get_roles($header['priv']);
list($header['role_code'],$header['role_name']) = $role;
$header['top_menu'] = $this->menu_model->get_menu($header['priv']);
$this->load->view('top_page/top_page',$header);
$this->load->view('noaccess/noaccess_div');			
$this->load->view('bottom_page/bottom_page');			
?>
