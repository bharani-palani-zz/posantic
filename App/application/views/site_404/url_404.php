<?php
$validity = $this->login_model->check_validity($this->session->userdata('acc_no'));
if($validity == 0)
{
	redirect(base_url().'account');
} else {
	$header['view']['title'] = 'Error 404';
	$header['priv'] = $this->session->userdata('privelage');
	$role = $this->roles_model->get_roles($header['priv']);
	list($header['role_code'],$header['role_name']) = $role;
	$header['top_menu'] = $this->menu_model->get_menu($header['priv']);
	$this->load->view('top_page/top_page',$header);
	?>
	<div class="jumbotron" align="center">
		<div class="container-fluid">    
            <img src="<?php echo base_url().'products/draw_jumbo_barcode/Error_404'?>" class="img-thumbnail" />
            <h3><a target="_blank" href="https://en.wikipedia.org/wiki/HTTP_404" class="btn btn-lg btn-danger">Error 404</a></h3>
            <div class="label label-primary">Oops!! The product you are finding may be extensive or rare.</div> <br>
            <div class="label label-success">Please Check again.</div>
		</div>
	</div>
	<?php
	$this->load->view('bottom_page/bottom_page');			
}
?>
