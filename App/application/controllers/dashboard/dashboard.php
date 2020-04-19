<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard extends CI_Controller
{
	var $acc;
	var $privelage;
	var $pos_user;
	var $user_id;
    public function __construct() 
    {
        parent::__construct();
		$this->acc = $this->session->userdata('acc_no');
		$this->privelage = $this->session->userdata('privelage');
		$this->pos_user = $this->session->userdata('pos_user');
		$this->user_id = $this->session->userdata('user_id');
		$subdomain = $this->session->userdata('subdomain');
		$this->is_valid_browser_domain = is_this_subdomain_browser($subdomain);
		$validity = $this->login_model->check_validity($this->acc);
		if($validity == 0)
		{
			redirect(base_url().'account');
		}		
    }
	public function index()
	{
		redirect(base_url());	
	}
	public function show_dashboard()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			//header
			$header['view']['title'] = 'Dashboard';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
						
			$this->load->view('top_page/top_page',$header);
			
			//body
			$this->load->view('dashboard/dashboard');
			
			//footer
			$this->load->view('bottom_page/bottom_page');			
		}
	}
}
?>