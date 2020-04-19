<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Register extends CI_Controller
{
	public $acc;
	public $privelage;
	public $pos_user;
	public $user_id;
	public $plan_registers;
    public function __construct() 
    {
        parent::__construct();
		$this->pos_user = $this->session->userdata('pos_user');
		$this->user_id = $this->session->userdata('user_id');
		$this->privelage = $this->session->userdata('privelage');
		$this->acc = $this->session->userdata('acc_no');
		$this->plan_registers = $this->session->userdata('plan_registers');
		$subdomain = $this->session->userdata('subdomain');
		$this->is_valid_browser_domain = is_this_subdomain_browser($subdomain);
		$validity = $this->login_model->check_validity($this->acc);
		if($validity == 0)
		{
			redirect(base_url().'account');
		}		
    }
	public function add_register($outlet_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$register_limit = $this->plan_registers;
			$current_registers = count($this->register_model->get_all_registers_combo($this->acc));
			if($register_limit > $current_registers)
			{
				if($this->privelage == 1)
				{
					$data = $this->register_model->get_register_wrt_outlet($outlet_id,$this->acc);
					$data['template_combo'] = $this->receipt_template_model->template_combo($this->acc);
					$data['quicktouch_combo'] = $this->quickey_model->quickey_combo($this->acc);
					$data['round_method'] = $this->setup_model->M_get_rounding_methods();	
					if(!empty($data['location']))
					{	
						//header
						$header['view']['title'] = 'Add Register';
						$role = $this->roles_model->get_roles($this->privelage);
						list($header['role_code'],$header['role_name']) = $role;
						$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
						$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
						$this->load->view('top_page/top_page',$header);
					
						//body
						$this->load->view('register/show_add_register',$data);
						
						//footer
						$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
						$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
						$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
						$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/add_outlet.js').'"></script>'."\n";
						$this->load->view('bottom_page/bottom_page',$footer);			
						
					} else {
						$this->load->view('site_404/url_404'); 				
					}
				} else {
					$this->load->view('noaccess/noaccess');	
				}
			} else {
				$this->session->set_flashdata('form_errors', 'Your current plan reaches the maximum number of register(s), please '.anchor('account','upgrade').' your account to add more registers.');
				redirect(base_url().'setup/outlets_and_registers');						
			}
		}
	}
	public function show_register($register_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1)
			{
				$data = $this->register_model->show_view_register($register_id,$this->acc);
				if(!empty($data['reg_code']))
				{	
					//header
					$header['view']['title'] = 'Show Register';
					$role = $this->roles_model->get_roles($this->privelage);
					list($header['role_code'],$header['role_name']) = $role;
					$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
					$this->load->view('top_page/top_page',$header);
				
					//body
					$this->load->view('register/show_register',$data);
					
					//footer
					$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
					$this->load->view('bottom_page/bottom_page',$footer);			
					
				} else {
					$this->load->view('site_404/url_404'); 				
				}
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function create_register($outlet_id)
	{
		$this->load->view('session/pos_session');
		$data['reg_name'] = $this->input->post('reg_name');
		$data['reg_email_rec'] = $this->input->post('reg_email_rec');
		$data['reg_print_rec'] = $this->input->post('reg_print_rec');
		$data['reg_prefix'] = $this->input->post('reg_prefix');
		$data['reg_bill_seq'] = $this->input->post('reg_bill_seq');
		$data['reg_rec_temp'] = $this->input->post('reg_rec_temp');
		$data['reg_bill_round'] = $this->input->post('reg_bill_round');
		$data['reg_qt_temp'] = $this->input->post('reg_qt_temp');
		$data['ask_quotes_stat'] = $this->input->post('ask_quotes_stat');
		$data['ask_user_stat'] = $this->input->post('ask_user_stat');
		$data['outlet_id'] = $outlet_id;
		$data['acc'] = $this->acc;
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('reg_name', 'Register name', 'trim|required|max_length[25]|xss_clean');
		if($this->form_validation->run() == FALSE)
		{
			$this->add_register($outlet_id);
		} else {
			$account = $this->account_model->current_plan_status($this->acc);
			if($account['account_code'] == 'TRY' or $account['account_code'] == 'TES')
			{
				$response = $this->register_model->insert_register($data);
			} else if($account['account_code'] == 'PAY') {
//				waiting				
//				while making payment -> what happens if merchant adds another register on another computer.
//				A wait status flag has to be set during making payments.
//				merchant must pay add register amount on payment gateway
//				if($this->payment_gateway->add_register_payment($acc))
//				{
//					$response = $this->register_model->insert_register($data);				
//				} else {
//					$response = 2;
//				}
			} else {
				$response = 0;	
			}
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Register successfully created!', 
						2 => 'Unable to process transaction for adding register'
						);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			redirect(base_url().'setup/outlet/'.$outlet_id);						
		}
	}
	public function update_register($register_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1)
			{
				$data = $this->register_model->show_view_register($register_id,$this->acc);
				$data['template_combo'] = $this->receipt_template_model->template_combo($this->acc);
				$data['quicktouch_combo'] = $this->quickey_model->quickey_combo($this->acc);
				$data['round_method'] = $this->setup_model->M_get_rounding_methods();	
				if(!empty($data['reg_code']))
				{	
					//header
					$header['view']['title'] = 'Edit Register';
					$role = $this->roles_model->get_roles($this->privelage);
					list($header['role_code'],$header['role_name']) = $role;
					$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
					$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
					$this->load->view('top_page/top_page',$header);
				
					//body
					$this->load->view('register/edit_register',$data);
					
					//footer
					$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
					$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
					$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
					$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/edit_register.js').'"></script>'."\n";
					$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
					$this->load->view('bottom_page/bottom_page',$footer);			
					
				} else {
					$this->load->view('site_404/url_404'); 				
				}
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function modify_register($reg_id)
	{
		$this->load->view('session/pos_session');
		$data['reg_name'] = $this->input->post('reg_name');
		$data['reg_email_rec'] = $this->input->post('reg_email_rec');
		$data['reg_print_rec'] = $this->input->post('reg_print_rec');
		$data['reg_prefix'] = $this->input->post('reg_prefix');
		$data['reg_bill_seq'] = $this->input->post('reg_bill_seq');
		$data['reg_bill_round'] = $this->input->post('reg_bill_round');
		$data['reg_rec_temp'] = $this->input->post('reg_rec_temp');
		$data['reg_qt_temp'] = $this->input->post('reg_qt_temp');
		$data['ask_quotes_stat'] = $this->input->post('ask_quotes_stat');
		$data['ask_user_stat'] = $this->input->post('ask_user_stat');
		$data['reg_id'] = $reg_id;
		$data['acc'] = $this->acc;
		$this->form_validation->set_error_delimiters('<p class="form_errors">', '</p>');		
		$this->form_validation->set_rules('reg_name', 'Register name', 'trim|required|max_length[25]|xss_clean');
		if($this->form_validation->run() == FALSE)
		{
			$this->update_register($reg_id);
		} else {
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Register successfully updated!', 
						);
			$response = $this->register_model->modify_register($data);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			redirect(base_url().'setup/outlets_and_registers');						
		}
	}
	public function delete_register($reg_id)
	{
		if($this->privelage == 1)
		{
			$data['reg_id'] = $reg_id;
			$data['acc'] = $this->acc;
			$response = $this->register_model->delete_register($data);
			$div = ($response['stat'] == 1) ? 'form_success' : 'form_errors';
			$phrase = array(0 => $response['error_str'],
							1 => $response['error_str'], 
							2 => $response['error_str'], 
							);
			$this->session->set_flashdata($div, $phrase[$response['stat']]);
			redirect(base_url().'setup/outlets_and_registers');						
		} else {
			$this->load->view('noaccess/noaccess');	
		}		
	}
}
?>