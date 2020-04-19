<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Outlet extends CI_Controller
{
	public $acc;
	public $privelage;
	public $pos_user;
	public $user_id;
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
	public function add_outlet()
	{
		$bonafide = $this->master_model->plan_bonafide($this->acc);
		if($bonafide['stores_handle'] == 'Multiple')
		{
			if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
			{
				$this->load->library('../controllers/default/user');
				$this->user->login();
			} else {
				if($this->privelage == 1)
				{
					$data = array();
					$data['country_dropdown'] = $this->setup_model->get_countries_select();
					$data['template_combo'] = $this->receipt_template_model->template_combo($this->acc);
					$data['def_locale_tax'] = $this->taxes_model->get_single_group_taxes_combo($this->acc);
					$data['quicktouch_combo'] = $this->quickey_model->quickey_combo($this->acc);
					$data['round_method_combo'] = $this->setup_model->M_get_rounding_methods();	
					//header
					$header['view']['title'] = 'Add Outlet';
					$role = $this->roles_model->get_roles($this->privelage);
					list($header['role_code'],$header['role_name']) = $role;
					$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
					$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
					$this->load->view('top_page/top_page',$header);
					
					//body
					$this->load->view('outlet/add_outlet',$data);
					
					//footer
					$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
					$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
					$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
					$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/add_outlet.js').'"></script>'."\n";
					$this->load->view('bottom_page/bottom_page',$footer);			
					
				} else {
					$this->load->view('noaccess/noaccess');	
				}
			}
		} else {
			$this->load->view('unfit/unfit_outlet');
		}
	}
	public function create_outlet()
	{
		$this->load->view('session/pos_session');
		$data['outlet_name'] = $this->input->post('outlet_name');
		$data['outlet_tax'] = $this->input->post('outlet_tax');
		$data['outlet_addrr1'] = $this->input->post('outlet_addrr1');
		$data['outlet_addrr2'] = $this->input->post('outlet_addrr2');
		$data['outlet_city'] = $this->input->post('outlet_city');
		$data['outlet_state'] = $this->input->post('outlet_state');
		$data['outlet_pin'] = $this->input->post('outlet_pin');
		$data['outlet_country'] = $this->input->post('outlet_country');
		$data['outlet_ll'] = $this->input->post('outlet_ll');
		$data['outlet_email'] = $this->input->post('outlet_email');
		$data['reg_name'] = $this->input->post('reg_name');
		$data['reg_rec_temp'] = $this->input->post('reg_rec_temp');
		$data['reg_qt_temp'] = $this->input->post('reg_qt_temp');
		$data['reg_prefix'] = $this->input->post('reg_prefix');
		$data['reg_bill_seq'] = $this->input->post('reg_bill_seq');
		$data['email_rec_stat'] = $this->input->post('email_rec_stat');
		$data['print_rec_stat'] = $this->input->post('print_rec_stat');
		$data['ask_user_stat'] = $this->input->post('ask_user_stat');
		$data['ask_quotes_stat'] = $this->input->post('ask_quotes_stat');
		$data['reg_bill_round'] = $this->input->post('reg_bill_round');
		$data['acc'] = $this->acc;
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('outlet_name', 'Outlet name', 'trim|required|max_length[25]|xss_clean');
		$this->form_validation->set_rules('outlet_addrr1', 'Address 1', 'trim|required|xss_clean');
		$this->form_validation->set_rules('outlet_addrr2', 'Address 2', 'trim|required|xss_clean');
		$this->form_validation->set_rules('outlet_city', 'City', 'trim|required|xss_clean');
		$this->form_validation->set_rules('outlet_state', 'State', 'trim|required|xss_clean');
		$this->form_validation->set_rules('outlet_pin', 'Pincode', 'trim|required|xss_clean');
		$this->form_validation->set_rules('outlet_ll', 'Phone', 'trim|required|xss_clean');
		$this->form_validation->set_rules('outlet_email', 'Email'.$this->input->post('reg_name').'', 'trim|required|valid_email|xss_clean');
//		waiting				
//		while merchant making payment -> what happens if merchant adds another outlet/register on another computer.
//		A wait status flag has to be set during making payments.
//		merchant must pay add register amount on payment gateway
		$account = $this->login_model->get_timezone_loc_plan_validity($data['acc']);
		if($account['plan_store_handle'] == "Multiple")
		{
			if((int)$this->input->post('has_register') == 30)
			{
				$this->form_validation->set_rules('reg_name', 'register name', 'trim|required|max_length[25]|xss_clean');
				if($this->form_validation->run() == FALSE)
				{
					$this->add_outlet();
				} else {	
					//waiting	
					//if($this->payment_gateway->add_register_payment($acc)) payment accepted then create outlet and register. Else throw error and redirect to outlet register
					$response = $this->outlet_model->add_outlet_and_register($data);
					$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
									1 => 'Outlet and register successfully created', 
									);
					$div = ($response == 1) ? 'form_success' : 'form_errors';
					$this->session->set_flashdata($div, $phrase[$response]);
					redirect(base_url().'setup/outlets_and_registers');	
				}	
			} else {
				if($this->form_validation->run() == FALSE)
				{
					$this->add_outlet();
				} else {		
					$response = $this->outlet_model->add_outlet($data);
					$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
									1 => 'Outlet successfully created', 
									);
					$div = ($response == 1) ? 'form_success' : 'form_errors';
					$this->session->set_flashdata($div, $phrase[$response]);
					redirect(base_url().'setup/outlets_and_registers');
				}
			}
		} else {
			$this->session->set_flashdata('form_errors', 'You are not allowed to create outlet on your current plan.');
			redirect(base_url().'setup/outlets_and_registers');
		}
	}
	public function show_outlet($outlet_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1)
			{
				$data = $this->outlet_model->show_outlet($outlet_id,$this->acc) + $this->register_model->get_register($outlet_id,$this->acc);	
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				if(!empty($data['loc_str']))
				{	
					//header
					$header['view']['title'] = 'Show Outlet';
					$role = $this->roles_model->get_roles($this->privelage);
					list($header['role_code'],$header['role_name']) = $role;
					$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
					$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css')."\n";
					$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/datatables-responsive/css/dataTables.responsive.css')."\n";
					$header['style'][3] = link_tag(POS_CSS_ROOT.'repository/datatables/media/css/dataTables.fixedColumns.css')."\n";
					$header['style'][4] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
					$header['style'][5] = link_tag(POS_CSS_ROOT.'repository/autocomplete/autocomplete.css')."\n";
					$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
					$this->load->view('top_page/top_page',$header);
					
					//body
					$this->load->view('outlet/show_outlet',$data);
					
					//footer
					$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
					$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/show_outlet.js').'"></script>'."\n";
					$this->load->view('bottom_page/bottom_page',$footer);			
					
				} else {
					$this->load->view('site_404/url_404'); 				
				}
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function update_outlet($outlet_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1)
			{
				$data = $this->outlet_model->show_outlet($outlet_id,$this->acc);
				if(isset($data['loc_str']))
				{
					$data['get_single_group_taxes_combo'] = $this->taxes_model->get_single_group_taxes_combo($this->acc);
					$data['get_countries_select'] = $this->setup_model->get_countries_select();
					//header
					$header['view']['title'] = 'Edit Outlet';
					$role = $this->roles_model->get_roles($this->privelage);
					list($header['role_code'],$header['role_name']) = $role;
					$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
					$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css')."\n";
					$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/datatables-responsive/css/dataTables.responsive.css')."\n";
					$header['style'][3] = link_tag(POS_CSS_ROOT.'repository/datatables/media/css/dataTables.fixedColumns.css')."\n";
					$header['style'][4] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
					$header['style'][5] = link_tag(POS_CSS_ROOT.'repository/autocomplete/autocomplete.css')."\n";
					$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
					$this->load->view('top_page/top_page',$header);
					
					//body
					$this->load->view('outlet/update_outlet',$data);
					
					//footer
					$this->load->view('bottom_page/bottom_page');			
					
				} else {
					$this->load->view('site_404/url_404'); 
				}
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function update_save_outlet($outlet_id)
	{
		$this->load->view('session/pos_session');
		if($this->privelage == 1)
		{
			$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
			$this->form_validation->set_rules('edit_loc_str', 'Outlet Name', 'trim|required|max_length[25]|min_length[1]|xss_clean');
			if($this->form_validation->run() == FALSE)
			{
				$data = $this->outlet_model->show_outlet($outlet_id,$this->acc);
				$data['get_single_group_taxes_combo'] = $this->taxes_model->get_single_group_taxes_combo($this->acc);
				$data['get_countries_select'] = $this->setup_model->get_countries_select();
				$this->load->view('outlet/update_outlet',$data);
			} else {
				$data['edit_outlet_id'] = $outlet_id; 
				$data['edit_loc_str'] = $this->input->post('edit_loc_str'); 
				$data['edit_outlet_tax'] = $this->input->post('edit_outlet_tax'); 
				$data['edit_l1'] = $this->input->post('edit_l1'); 
				$data['edit_l2'] = $this->input->post('edit_l2');
				$data['edit_city'] = $this->input->post('edit_city');
				$data['edit_state'] = $this->input->post('edit_state');
				$data['edit_email'] = $this->input->post('edit_email');
				$data['edit_pcode'] = $this->input->post('edit_pcode');
				$data['edit_country'] = $this->input->post('edit_country');
				$data['edit_ll'] = $this->input->post('edit_ll');
				$data['edit_account'] = $this->acc;
				$response = $this->outlet_model->update_save_outlet($data);
				$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
								1 => 'Outlet updated', 
								);
				$div = ($response == 1) ? 'form_success' : 'form_errors';
				$this->session->set_flashdata($div, ucfirst(strtolower($phrase[$response])));
				redirect(base_url().'setup/outlets_and_registers');
			}
		} else {
			$this->load->view('noaccess/noaccess');	
		}
	}
	public function delete_outlet($outlet_id)
	{
		$this->load->view('session/pos_session');
		if($this->privelage == 1)
		{
			$outlet_count = $this->outlet_model->outlet_count($this->acc);
			$other_reg_count = $this->register_model->other_register_count_not_outlet($outlet_id,$this->acc);
			if($outlet_count > 1 && $other_reg_count >= 1) 
			{
				$data['outlet_id'] = $outlet_id;
				$data['acc'] = $this->acc;
				$response = $this->outlet_model->delete_outlet($data);
				$div = ($response['stat'] == 1) ? 'form_success' : 'form_errors';
				$phrase = array(0 => $response['error_str'],
								1 => $response['error_str']
								);
				$this->session->set_flashdata($div, $phrase[$response['stat']]);
				redirect(base_url().'setup/outlets_and_registers');						
			} else {
				$this->session->set_flashdata('form_errors', 'OOPS! Atleast you need 1 outlet');
				redirect(base_url().'setup/outlets_and_registers');
			}
		} else {
			$this->load->view('noaccess/noaccess');	
		}
	}
}
?>