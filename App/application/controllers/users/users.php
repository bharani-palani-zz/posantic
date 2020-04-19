<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Users extends CI_Controller
{
	public $account_handle = null;
	public $acc;
	public $privelage;
	public $pos_user;
	public $user_id;
	public $loc_id;
	public $currency;
	public $loc_str;
	public $tz;
	public $subdomain;
    public function __construct() 
    {
        parent::__construct();
		$this->acc = $this->session->userdata('acc_no');
		$this->privelage = $this->session->userdata('privelage');
		$this->pos_user = $this->session->userdata('pos_user');
		$this->user_id = $this->session->userdata('user_id');
		$this->currency = $this->session->userdata('currency');
		$this->loc_str = $this->session->userdata('loc_str');
		$this->loc_id = $this->session->userdata('loc_id');
		$this->tz = $this->session->userdata('tz');
		$this->subdomain = $this->session->userdata('subdomain');
		$this->is_valid_browser_domain = is_this_subdomain_browser($this->subdomain);
		$validity = $this->login_model->check_validity($this->acc);
		if($validity == 0)
		{
			redirect(base_url().'account');
		}		
		$bonafide = $this->master_model->plan_bonafide($this->acc);
		$this->account_handle = $bonafide['stores_handle'];
    }
	public function index()
	{		
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage <= 2 ) { $this->show_users(); } else { redirect(base_url().'users/'.$this->user_id); }
		}
	}
	public function add()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$data['desig']['Fit as'] = $this->user_model->get_roles($this->privelage);
			if($this->privelage == 1){
				$company = $this->user_model->get_locations($this->acc);
			} else if($this->privelage == 2 && $this->loc_id == 'ALL OUTLETS'){
				$company = $this->user_model->get_locations($this->acc);
			} else if($this->privelage == 2 && $this->loc_id != 'ALL OUTLETS'){
				$company = $this->user_model->get_mgr_own_locations($this->loc_id, $this->acc);
			}
			$data['company']['Belong to outlet'] = ($this->loc_id == 'ALL OUTLETS') ? array('' => 'ALL OUTLETS') + $company : $company;
			//header
			$header['view']['title'] = 'Add user';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
			$this->load->view('top_page/top_page',$header);
			
			//body
			$this->load->view('users/add_user',$data);
			
			//footer
			$this->load->view('bottom_page/bottom_page'); 
			
		}
	}
	public function isEmailExist($email) {
		$this->load->view('session/pos_session');
		$this->db->select('user_id');
		$this->db->where('user_mail', $email);
		$this->db->where('account_no', $this->acc);
		$query = $this->db->get('pos_e_login');
		if ($query->num_rows() > 0) {
			$this->form_validation->set_message('isEmailExist', 'This %s is already used by another user. Please try another.');
			return false;
		} else {
			return true;
		}
	}
	public function isMyEmailExist($email) {
		$this->load->view('session/pos_session');
		$user_id = $this->input->post('edit_user_id');
		$this->db->select('user_name');
		$this->db->where_in('user_mail', $email);
		$this->db->where(array('account_no' => $this->acc, 'user_id != ' => $user_id));
		$query = $this->db->get('pos_e_login');
		if($query->num_rows() > 0) {
			$this->form_validation->set_message('isMyEmailExist', 'This New %s is already used by another user. Please try another.');
			return false;
		} else {
			return true;
		}
	}
	public function isUserExist($user_name) {
		$this->load->view('session/pos_session');
		$this->db->select('user_id');
		$this->db->where('user_name', $user_name);
		$this->db->where('account_no', $this->acc);
		$query = $this->db->get('pos_e_login');
		if ($query->num_rows() > 0) {
			$this->form_validation->set_message('isUserExist', 'This %s is already occupied by another user. Please try another.');
			return false;
		} else {
			return true;
		}
	}
	public function isMyUserExist($user_name) {
		$this->load->view('session/pos_session');
		$user_id = $this->input->post('edit_user_id');
		$this->db->select('user_name');
		$this->db->where_in('user_name', $user_name);
		$this->db->where(array('account_no' => $this->acc, 'user_id != ' => $user_id));
		$query = $this->db->get('pos_e_login');
		if ($query->num_rows() > 0) {
			$this->form_validation->set_message('isMyUserExist', 'This %s is already occupied by another user. Please try another.');
			return false;
		} else {
			return true;
		}
	}
	public function show_users()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$c_symbol = $this->currency_model->getsymbol($this->currency);
			$data['stat'] = $this->status_codes->get_status_code();
			$data['desig'] = $this->user_model->all_roles();
			if($this->privelage == 1){
				$data['company'] = $this->user_model->get_locations($this->acc);
			} else {
				$data['company'] = array($this->loc_id => $this->loc_str);	
			}
			$data['company'] = ($this->account_handle == 'Multiple') ? array('' => 'ALL OUTLETS') + $data['company'] : $data['company'];
			$company_assoc = $this->user_model->get_locations_assoc_array($this->acc,$this->account_handle);
			switch($this->privelage)
			{
				case 1:
				$users_data = $this->user_model->all_users($this->acc);	
				break;
				case 2;
				$users_data = $this->user_model->current_users($this->acc,$this->loc_id,$this->privelage);
				break;
			}
			$timezone = $this->tz;
			$daylight_saving = date("I");
			$j=0;
			for($i=0;$i<count($users_data);$i++)
			{
				$data['users']['static']['symbol'] = $c_symbol;
				$data['users']['static']['data-id'] = $users_data[$i][0];
				$data['users']['rows'][$i][0] = $users_data[$i][0];
				$data['users']['rows'][$i][1] = $users_data[$i][1]; 
				$data['users']['rows'][$i][2] = $users_data[$i][4] != '' ? $company_assoc[$users_data[$i][4]] : 'ALL OUTLETS';
				$data['users']['rows'][$i][3] = $data['stat'][$users_data[$i][2]];
				$data['users']['rows'][$i][4] = $users_data[$i][6];
				$data['users']['rows'][$i][5] =	$users_data[$i][7];
				$data['users']['rows'][$i][6] =	$users_data[$i][8];
				$data['users']['rows'][$i][7] = $data['desig'][$users_data[$i][3]];
				$data['users']['rows'][$i][8] = $users_data[$i][5];
				$data['users']['rows'][$i][9] = $users_data[$i][9] > 0 ? unix_to_human(gmt_to_local(strtotime($users_data[$i][9]), $timezone, $daylight_saving)) : '';
				$data['users']['rows'][$i][10] = $users_data[$i][10];
				$j++;
			}
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			if($this->session->flashdata('form_success')) {
				$data['form_success'] = $this->session->flashdata('form_success');
			}
			//header
			$header['view']['title'] = 'Users';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
			$header['style'][1] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
			$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css')."\n";
			$header['style'][3] = link_tag(POS_CSS_ROOT.'repository/datatables-responsive/css/dataTables.responsive.css')."\n";
			$header['style'][4] = link_tag(POS_CSS_ROOT.'repository/datatables/media/css/dataTables.fixedColumns.css')."\n";
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
			$this->load->view('top_page/top_page',$header);
			
			//body
			$this->load->view('users/show_users',$data);
			
			//footer
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
			$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/touch-punch.js').'"></script>'."\n";
			$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-touch.js').'"></script>'."\n";
			$footer['foot']['script'][3] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables/media/js/jquery.dataTables.min.js"></script>'."\n";
			$footer['foot']['script'][4] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>'."\n";
			$footer['foot']['script'][5] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables/media/js/dataTables.fixedColumns.min.js"></script>'."\n";
			$footer['foot']['script'][6] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/users.js').'"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer); 
			
		}
	}
	public function find_user($user_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$data = $this->user_model->user_details($user_id,$this->acc);
			$data = $data + array('user_id' => $user_id);
			$data['roles'] = $this->user_model->all_roles();
			$data['stat'] = array(10 => 'Active', 20 => 'Inactive');
			$data['company'] = $this->user_model->get_locations($this->acc);
			$data['company'] = ($this->loc_id == 'ALL OUTLETS') ? array('' => 'ALL OUTLETS') + $data['company'] : $data['company'];
			if(!empty($data['user_name']))
			{
				//header
				$header['view']['title'] = 'Update user';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				// body
				switch($this->privelage)
				{
					case 1:
						$this->load->view('users/update_user',$data);				
					break;
					case 2:
						$this->load->view('users/update_user',$data);				
					break;
					case 3:
						if($this->user_id == $data['user_id'])
						{
							$this->load->view('users/update_user',$data);								
						} else {
							$this->load->view('noaccess/noaccess_div');			
						}
					break;
					default:
						$this->load->view('noaccess/noaccess_div');			
				}
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('site_404/url_404'); 
			}		
		}
	}
	public function add_user()
	{
		$this->load->view('session/pos_session');
		$count = $this->M_current_proc_names();
		//$user_limit = $this->session->userdata('plan_user_limit');
		$bonafide = $this->master_model->plan_bonafide($this->acc);
		$user_limit = $bonafide['users_limit'];		
		
		if($user_limit > $count)
		{
			$data['user_name'] = $this->input->post('new_emp_name');
			$data['disp_name'] = $this->input->post('new_disp_name');
			$data['new_emp_pass'] = $this->input->post('new_emp_pass');
			$data['email'] = $this->input->post('email');
			$data['company'] = $this->input->post('company');
			$data['emp_mobile'] = $this->input->post('emp_mobile');
			$data['email'] = $this->input->post('email');
			$data['role'] = $this->input->post('role');
			$data['acc'] = $this->acc;
			$data['subdomain'] = $this->subdomain;
			$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
			$this->form_validation->set_rules('new_emp_name', 'User Name', 'trim|required|max_length[25]|min_length[4]|callback_isUserExist|xss_clean');
			$this->form_validation->set_rules('new_disp_name', 'Display Name', 'trim|required|max_length[25]|min_length[4]|xss_clean');
			$this->form_validation->set_rules('new_emp_pass', 'Password', 'trim|required|min_length[8]|max_length[25]|xss_clean');
			$this->form_validation->set_rules('new_emp_check_pass', 'Retype Password','required|matches[new_emp_pass]');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_isEmailExist|xss_clean');
			$this->form_validation->set_rules('emp_mobile', 'Mobile phone', 'trim|required|numeric|exact_length[10]|xss_clean');
			$this->form_validation->set_rules('company', 'Company', 'trim|xss_clean');
			$this->form_validation->set_rules('role', 'Role', 'trim|xss_clean');
			if($this->form_validation->run() == FALSE)
			{
				$this->add();
			} else {
				$response = $this->user_model->add_user($data);
				$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
								1 => 'User Successfully Created!', 
								);
				$div = ($response == 1) ? 'form_success' : 'form_errors';
				$this->session->set_flashdata($div, ucfirst(strtolower($phrase[$response])));
				redirect(base_url().'users');
			}
		} else {
			$this->session->set_flashdata('form_errors', 'Sorry! You have exceeded maximum user limit. Please <big>'.anchor('account','Upgrade').'</big> to avail additional users');
			redirect(base_url().'users');
		}
	}
	public function M_current_proc_names()
	{
		$this->load->view('session/pos_session');
		$query = $this->db->get_where('pos_e_login',array('account_no' => $this->acc)); 
		return $query->num_rows();
	}

	public function save_user($user_id)
	{
		$this->load->view('session/pos_session');
		$data['edit_user_id'] = $this->input->post('edit_user_id');
		$data['edit_emp_status'] = $this->input->post('edit_emp_status');
		$data['edit_emp_outlet'] = $this->input->post('edit_emp_outlet');
		$data['edit_emp_role'] = $this->input->post('edit_emp_role');		
		$data['edit_emp_name'] = $this->input->post('edit_emp_name');
		$data['edit_disp_name'] = $this->input->post('edit_disp_name');
		$data['edit_email'] = $this->input->post('edit_email');
		$data['edit_emp_mobile'] = $this->input->post('edit_emp_mobile');
		$data['edit_pass'] = $this->input->post('edit_pass');
		$data['edit_type_pass'] = $this->input->post('edit_type_pass');
		$data['acc'] = $this->acc;
		$data['subdomain'] = $this->subdomain;
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('edit_emp_name', 'User Name', 'trim|required|max_length[25]|min_length[4]|callback_isMyUserExist|xss_clean');
		$this->form_validation->set_rules('edit_disp_name', 'Display Name', 'trim|required|max_length[25]|min_length[4]|xss_clean');
		$this->form_validation->set_rules('edit_email', 'Email', 'trim|required|valid_email|callback_isMyEmailExist|xss_clean');
		$this->form_validation->set_rules('edit_emp_mobile', 'Mobile phone', 'trim|required|numeric|exact_length[10]|xss_clean');
		if($this->form_validation->run())
		{
			if(strlen($data['edit_pass']) > 0 || strlen($data['edit_type_pass']) > 0)
			{
				$this->form_validation->set_rules('edit_pass', 'Password', 'trim|required|min_length[8]|max_length[25]|xss_clean');
				$this->form_validation->set_rules('edit_type_pass', 'Retype Password','required|matches[edit_pass]');
				$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
				if($this->form_validation->run())
				{					
					$response = $this->user_model->save_user($data);
					$phrase = array(0 => 'Error: Oops! Something Went Wrong! Please Try Again',
									1 => 'User "'.$data['edit_disp_name'].'" Successfully Updated!', 
									);
					$div = ($response == 1) ? 'form_success' : 'form_errors';
					$this->session->set_flashdata($div, ucfirst(strtolower($phrase[$response])));
					redirect(base_url().'users');
				} else {
					$this->find_user($user_id);
				}
			} else {
					$response = $this->user_model->save_user($data);
					$phrase = array(0 => 'Error: Oops! Something Went Wrong! Please Try Again',
									1 => 'User "'.$data['edit_disp_name'].'" Successfully Updated!', 
									);
					$div = ($response == 1) ? 'form_success' : 'form_errors';
					$this->session->set_flashdata($div, ucfirst(strtolower($phrase[$response])));
					redirect(base_url().'users');
			}
		} else {
			$this->find_user($user_id);
		}
	}
	public function save_target()
	{
		$this->load->view('session/pos_session');
		if($this->privelage == 1 || $this->privelage == 2)
		{
			$data['new_val'] = $this->input->post('new_val');
			$data['target_id'] = $this->input->post('target_id');
			$data['emp_id'] = $this->input->post('emp_id');
			$data['acc'] = $this->acc;
			if(strlen($data['new_val']) > 0 && strlen($data['target_id']) > 0 && strlen($data['emp_id']) > 0)
			{
				$response = $this->user_model->save_target($data);
				echo $response;
			} else {
				echo 'NULL';	
			}
		} else {
			$this->load->view('noaccess/noaccess');				
		}
	}
	public function delete_user($user_id)
	{
		$this->load->view('session/pos_session');
		if($this->privelage == 1 || $this->privelage == 2)
		{
			$user_count = $this->user_model->user_count($this->acc);
			if($user_count > 1)
			{
				$data = $this->user_model->user_details($user_id,$this->acc);
				if(count($data) > 0)
				{	
					if($data['is_delete'] != 120)
					{
						$ext = '';
						$root = APPPATH.'user_images/'.md5($this->acc).'/users/'.$user_id.'_thumb';
						foreach (glob($root.".*") as $filename) {
							$ext = substr($filename,-3);
						}
						unlink($root.'.'.$ext);
						
						$response = $this->user_model->delete_user($user_id,$this->acc);
						$phrase = array(0 => 'Error: Oops! Something Went Wrong! Please Try Again',
										1 => 'User "'.$data['display_name'].'" Successfully Deleted!', 
										);
						$div = ($response == 1) ? 'form_success' : 'form_errors';
						$this->session->set_flashdata($div, ucfirst(strtolower($phrase[$response])));
						redirect(base_url().'users');
					} else {
						$this->session->set_flashdata('form_errors', 'You cant delete the primary user');
						redirect(base_url().'users');
					}
				} else {
					$this->load->view('site_404/url_404'); 				
				}
			} else {
				$this->session->set_flashdata('form_errors', 'You cant delete your only user');
				redirect(base_url().'users');
			}
		} else {
			$this->load->view('noaccess/noaccess');				
		}
	}
	public function delete_image($user_id)
	{
		$ext = '';
		$root = APPPATH.'user_images/'.md5($this->acc).'/users/'.$user_id.'_thumb';
		foreach (glob($root.".*") as $filename) {
			$ext = substr($filename,-3);
		}
		unlink($root.'.'.$ext);
		$this->session->set_flashdata('form_success', 'Image successfully deleted');
		redirect(base_url().'users/'.$user_id);
	}
}
?>
