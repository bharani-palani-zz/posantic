<?php
class Secure_404 extends CI_Controller
{
    public function __construct() 
    {
        parent::__construct();
    }
    public function index()
	{
		redirect(base_url('signin'));
		//$this->load->view('site_404/url_404'); 
	}
}
?>