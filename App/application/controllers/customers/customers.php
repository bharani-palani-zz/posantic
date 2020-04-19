<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Customers extends CI_Controller
{
	public $acc;
	public $privelage;
	public $pos_user;
	public $user_id;
    public function __construct() 
    {
        parent::__construct();
		$this->load->library('csvreader');
		$this->load->dbutil();
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
	public function group()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			//header
			$header['view']['title'] = 'Customer Group';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
			$this->load->view('top_page/top_page',$header);
			
			//body
			$data = array();
			$data['groups'] = $this->customer_model->group_list($this->acc);
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			if($this->session->flashdata('form_success')) {
				$data['form_success'] = $this->session->flashdata('form_success');
			}	
			$settings = $this->account_model->current_plan_status($this->acc);
			$data['timezone'] = $settings['timezone']; 
			$this->load->view('customers/show_group',$data);
			
			//footer
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer);			
			
		}
	}
	public function handle()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$data = array();
			$sort_array = array('sort' => 'cust_name','flow' => 'asc');
			$config["base_url"] = base_url()."customers/page";
			$config["total_rows"] = $this->customer_model->get_customer_tot_rows('','','',$this->acc,$sort_array);
			$config["per_page"] = 50;
			$config["uri_segment"] = 3;
			$config["num_links"] = 5;
			$config['full_tag_open'] = '<div class="text-center"><ul class="pagination pagination-sm">';
			$config['full_tag_close'] = '</div></ul>';			
						
			$config['first_link'] = '<i class="fa fa-step-backward"></i>';
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';

			$config['last_link'] = '<i class="fa fa-step-forward"></i>';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';
			
			$config['next_link'] = '<i class="fa fa-forward"></i>';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			
			$config['prev_link'] = '<i class="fa fa-backward"></i>';
			$config['prev_tag_open'] = '<li>';
			$config['prev_tag_close'] = '</li>';
			
			$config['cur_tag_open'] = '<li class="active"><a href="#">';
			$config['cur_tag_close'] = '</a></li>';

			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';

			$this->pagination->initialize($config);
	
			$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
			$data["results"] = $this->customer_model->get_customers('',$config["per_page"], $page ,$this->acc,$sort_array);
			$data["links"] = $this->pagination->create_links();
			$data['group_combo'] = array('ALL' => '') + $this->customer_model->group_combo($this->acc);
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			if($this->session->flashdata('form_success')) {
				$data['form_success'] = $this->session->flashdata('form_success');
			}	
			//header
			$header['view']['title'] = 'Customers';
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
			$data['tot_prd_count'] = $config["total_rows"];
			$data['page_prd_count'] = $config["total_rows"] > $config["per_page"] ? ($page+1)." - ".(count($data["results"]["cust_id"])+$page) : (count($data["results"]["cust_id"])+$page);
			$this->load->view('customers/show',$data);
			
			//footer
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
			$footer['foot']['script'][1] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables/media/js/jquery.dataTables.min.js"></script>'."\n";
			$footer['foot']['script'][2] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>'."\n";
			$footer['foot']['script'][3] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables/media/js/dataTables.fixedColumns.min.js"></script>'."\n";
			$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'customers/show_customers.js').'"></script>'."\n";			
			$footer['foot']['script'][5] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer);			
			
		}
	}
	public function search()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$get = array();
			$get['search_customer'] = $this->db->escape_like_str($this->input->get('search_customer'));
			$get['cust_group'] = $this->db->escape_like_str($this->input->get('cust_group'));
			$get['cust_dob'] = strlen($this->input->get('dob_date')) > 0 ? date('Y-m-d',strtotime($this->input->get('dob_date'))) : '';
			$get['cust_ann'] = strlen($this->input->get('ann_date')) > 0 ? date('Y-m-d',strtotime($this->input->get('ann_date'))) : '';
			$get['date_after'] = strlen($this->input->get('date_after')) > 0 ? mdate('%Y-%m-%d 00:00:00', strtotime($this->input->get('date_after'))) : '';
			$get['date_before'] = strlen($this->input->get('date_before')) > 0 ? mdate('%Y-%m-%d 23:59:59', strtotime($this->input->get('date_before'))) : '';
			$get['sort'] = $this->input->get('sort');
			$get['flow'] = $this->input->get('flow');

			$tot_rows = $this->customer_model->get_customer_tot_rows($get['search_customer'],'','',$this->acc,$get);
			$config['page_query_string'] = TRUE;
			$config["total_rows"] = $tot_rows;
			$config["per_page"] = 50;
			$config['base_url'] = base_url()."customers/lookup".'?'.http_build_query($_GET);
			$config["uri_segment"] = 3;
			$config["num_links"] = 5;

			$config['full_tag_open'] = '<div class="text-center"><ul class="pagination pagination-sm">';
			$config['full_tag_close'] = '</div></ul>';			
						
			$config['first_link'] = 'First';
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';

			$config['last_link'] = 'Last';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';
			
			$config['next_link'] = '&gt;';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			
			$config['prev_link'] = '&lt;';
			$config['prev_tag_open'] = '<li>';
			$config['prev_tag_close'] = '</li>';
			
			$config['cur_tag_open'] = '<li class="active"><a href="#">';
			$config['cur_tag_close'] = '</a></li>';

			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';
			$this->pagination->initialize($config);
		
			//page differs like this for query strings
			$page = $this->input->get("per_page") ? $this->input->get("per_page") : 0;
			$data["results"] = $this->customer_model->get_customers($get['search_customer'],$config["per_page"], $page ,$this->acc,$get);
			$data["links"] = $this->pagination->create_links();
			$data['group_combo'] = array('ALL' => '') + $this->customer_model->group_combo($this->acc);
			$data['tot_prd_count'] = $config["total_rows"];
			$data['page_prd_count'] = $config["total_rows"] > $config["per_page"] ? ($page+1)." - ".(count($data["results"]["cust_id"])+$page) : (count($data["results"]["cust_id"])+$page);
			//header
			$header['view']['title'] = 'Customers';
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
			$this->load->view('customers/show',$data);
			
			//footer
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
			$footer['foot']['script'][1] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables/media/js/jquery.dataTables.min.js"></script>'."\n";
			$footer['foot']['script'][2] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>'."\n";
			$footer['foot']['script'][3] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables/media/js/dataTables.fixedColumns.min.js"></script>'."\n";
			$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'customers/show_customers.js').'"></script>'."\n";			
			$footer['foot']['script'][5] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer);			
		}
	}
	public function add()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			//header
			$header['view']['title'] = 'Add Customer';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
			$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
			$this->load->view('top_page/top_page',$header);
			
			//body
			$data = array();
			$data['country_dropdown'] = $this->setup_model->get_countries_select();
			$data['master_data'] = $this->setup_model->M_get_masterdata($this->acc);
			
			$data['group_combo'] = $this->customer_model->group_combo($this->acc);
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			if($this->session->flashdata('form_success')) {
				$data['form_success'] = $this->session->flashdata('form_success');
			}
			$this->load->view('customers/add_customer',$data);
			
			//footer
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'tinymce/tinymce.min.js').'"></script>'."\n";
			$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
			$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
			$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
			$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'customers/add_customer.js').'"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer);			
			
		}
		
	}
	public function add_group()
	{
		if($this->user_id)
		{
			if($this->session->userdata('privelage') == 1)
			{
				$this->load->view('customers/add_group');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 style="padding:5px;">Session Expired! Please <a href="'.base_url().'customers">Login</a> again</h3>');
		}
	}
	public function create_group()
	{
		$this->load->view('session/pos_session');		
		$grp_name = $this->input->post('group_name');
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Customer group Successfully Created!', 
						2 => 'Customer group already exists. Try another group name!', 
						);
		$response = $this->customer_model->add_cust_group($grp_name,$this->acc);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, ucfirst(strtolower($phrase[$response])));
		redirect(base_url('customers/group'));		
	}
	public function date_valid($date)
	{
		if (empty($date['dd']) && empty($date['mm']) && empty($date['yy'])) {      
			return true;
		}
		if (is_numeric($date['dd']) && is_numeric($date['mm']) && is_numeric($date['yy'])) {      
			if (checkdate($date['mm'], $date['dd'], $date['yy']))
			{
				return TRUE;
			}
		}
		$this->form_validation->set_message('date_valid', 'The Date field is Invalid');
		return false;
	}
  	public function insert()
	{
		$this->load->view('session/pos_session');
		$data = $this->input->post();
		$data['latitude'] = NULL;
		$data['longitude'] = NULL;
		$data['acc'] = $this->acc;
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('cust_name', 'Customer name', 'trim|required|max_length[25]|xss_clean');
		$this->form_validation->set_rules('comp_name', 'Company name', 'trim|max_length[25]|xss_clean');
		$this->form_validation->set_rules('cust_dob', 'Customer DOB', 'callback_date_valid');
		$this->form_validation->set_rules('cust_dob[dd]');
		$this->form_validation->set_rules('cust_dob[mm]');
		$this->form_validation->set_rules('cust_dob[yy]');
		$this->form_validation->set_rules('cust_ann', 'Customer Anniversary', 'callback_date_valid');
		$this->form_validation->set_rules('cust_ann[dd]');
		$this->form_validation->set_rules('cust_ann[mm]');
		$this->form_validation->set_rules('cust_ann[yy]');
		$this->form_validation->set_rules('cust_mobile');
		$this->form_validation->set_rules('cust_ll');
		$this->form_validation->set_rules('cust_addrr_1');
		$this->form_validation->set_rules('cust_addrr_2');
		$this->form_validation->set_rules('cust_city');
		$this->form_validation->set_rules('cust_state');
		$this->form_validation->set_rules('cust_web');
		$this->form_validation->set_rules('cust_fb');
		$this->form_validation->set_rules('cust_pcode');
		$this->form_validation->set_rules('cust_code');
		$this->form_validation->set_rules('cust_desc');
		$this->form_validation->set_rules('cust_email', 'email', 'valid_email');
		if($this->form_validation->run() == FALSE)
		{
			$this->add();
		} else {
			$this->load->view('session/pos_session');
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
							1 => 'Customer Successfully Created!', 
							2 => 'You have exceeded maximum Customer DB limit, please '.anchor('account','upgrade').' your account to add more customers.'
							);
			$response = $this->customer_model->insert_customer($data);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			redirect(base_url('customers'));		
		}
	}
	public function show_customer($id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$data = array();
			$data['register_combo'] = array('' => '') + $this->register_model->get_all_registers_combo($this->acc);
			$data['countries_assoc'] = $this->setup_model->countries_assoc();
			$data['customer_data'] = $this->customer_model->customer_data($id,$this->acc);
			//waiting
			//join customer data from transaction table to YTD trade and credit		
			$data['log_codes'] = array('' => '') + $this->log_code_model->get_sale_only_log_codes_dropdown();
			$data['users']['By User'] = array('' => '') + $this->user_model->all_user_dropdown($this->acc);
			if(!is_null($data['customer_data']) and $data['customer_data']['cust_stat'] == 100)
			{
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				//header
				$header['view']['title'] = 'Customer detail';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$this->load->view('customers/show_customer',$data);

				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'customers/view_customer.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$footer['foot']['script'][3] =  '<script src="http://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.js"></script>'."\n";
				$footer['foot']['script'][4] =  '<script src="http://cdnjs.cloudflare.com/ajax/libs/respond.js/1.3.0/respond.js"></script>'."\n";
				$footer['foot']['script'][5] =  '<script src="http://maps.google.com/maps/api/js?sensor=false"></script>'."\n";
				$footer['foot']['script'][6] = '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/qr-code/jquery.qrcode-0.11.0.js').'"></script>'."\n";
				$footer['foot']['script'][7] = '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/qr-code/ff-range.js').'"></script>'."\n";			
				$this->load->view('bottom_page/bottom_page',$footer);	
			} else {
				$this->load->view('site_404/url_404'); 			
			}
			
		}
	}
	public function edit_customer($id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			//header
			$header['view']['title'] = 'Edit Customer';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
			$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
			$this->load->view('top_page/top_page',$header);
			
			//body
			$data = array();
			$data['country_dropdown'] = $this->setup_model->get_countries_select();
			$data['group_combo'] = $this->customer_model->group_combo($this->acc);
			$data['customer_data'] = $this->customer_model->customer_data($id,$this->acc);
			if(!is_null($data['customer_data']) and $data['customer_data']['cust_stat'] == 100)
			{
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$this->load->view('customers/edit_customer',$data);
			} else {
				$this->load->view('site_404/url_404'); 			
			}
			//footer
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'tinymce/tinymce.min.js').'"></script>'."\n";
			$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
			$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
			$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
			$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'customers/add_customer.js').'"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer);			
			
		}
	}
	public function update_coordinates($id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$this->load->view('session/pos_session');
			$data = array();
			$data['cust_id'] = $id;
			$data['c_lat'] = $this->input->post('c_lat');
			$data['c_long'] = $this->input->post('c_long');
			$data['acc'] = $this->acc;
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
							1 => 'Customer coordinates successfully updated!', 
							);
			$response = $this->customer_model->update_customer_coordinates($data);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			redirect(base_url('customers/'.$id));		
		}
	}
	public function update($id)
	{
		$this->load->view('session/pos_session');
		$data = $this->input->post();
		$data['cust_id'] = $id;
		$data['acc'] = $this->acc;
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('cust_name', 'Customer name', 'trim|required|max_length[25]|xss_clean');
		$this->form_validation->set_rules('comp_name', 'Company name', 'trim|max_length[25]|xss_clean');
		$this->form_validation->set_rules('cust_dob', 'Customer DOB', 'callback_date_valid');
		$this->form_validation->set_rules('cust_dob[dd]');
		$this->form_validation->set_rules('cust_dob[mm]');
		$this->form_validation->set_rules('cust_dob[yy]');
		$this->form_validation->set_rules('cust_ann', 'Customer Anniversary', 'callback_date_valid');
		$this->form_validation->set_rules('cust_ann[dd]');
		$this->form_validation->set_rules('cust_ann[mm]');
		$this->form_validation->set_rules('cust_ann[yy]');
		$this->form_validation->set_rules('cust_mobile');
		$this->form_validation->set_rules('cust_ll');
		$this->form_validation->set_rules('cust_addrr_1');
		$this->form_validation->set_rules('cust_addrr_2');
		$this->form_validation->set_rules('cust_city');
		$this->form_validation->set_rules('cust_state');
		$this->form_validation->set_rules('cust_web');
		$this->form_validation->set_rules('cust_fb');
		$this->form_validation->set_rules('cust_pcode');
		$this->form_validation->set_rules('cust_code');
		$this->form_validation->set_rules('cust_desc');
		$this->form_validation->set_rules('cust_email', 'email', 'valid_email');
		if($this->form_validation->run() == FALSE)
		{
			$this->edit_customer($id);
		} else {
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
							1 => 'Customer Successfully Updated!', 
							);
			$response = $this->customer_model->update_customer($data);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			redirect(base_url('customers/'.$id));		
		}
		
	}
	public function delete_customer($id)
	{
		$this->load->view('session/pos_session');
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Customer Successfully Deleted!', 
						);
		$response = $this->customer_model->delete_customer($id,$this->acc);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('customers'));		
	}
	public function edit_customer_group($grp_id)
	{
		if($this->user_id)
		{
			if($this->session->userdata('privelage') == 1)
			{
				$data['cust_group_name'] = $this->customer_model->get_group_data($grp_id,$this->acc);
				$data['grp_id'] = $grp_id;
				$this->load->view('customers/edit_group',$data);	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 style="padding:5px;">Session Expired! Please <a href="'.base_url().'customers/group">Login</a> again</h3>');
		}
	}
	public function update_group($grp_id)
	{
		$this->load->view('session/pos_session');
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Customer Group Successfully Updated!', 
						);
		$grp_name = $this->input->post('group_name');
		$response = $this->customer_model->update_group($grp_id,$grp_name,$this->acc);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('customers/group'));		
	}
	public function delete_group($id)
	{
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Customer Group Successfully Deleted!', 
						);
		$response = $this->customer_model->delete_group($id,$this->acc);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('customers/group'));		
	}
	public function import()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			//header
			$header['view']['title'] = 'Import Customers';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
			$this->load->view('top_page/top_page',$header);
			
			//body
			$data = array();
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			if($this->session->flashdata('form_success')) {
				$data['form_success'] = $this->session->flashdata('form_success');
			}
			$this->load->view('customers/bulk_import',$data);
			
			//footer
			$this->load->view('bottom_page/bottom_page');			
			
		}
	}
	public function csv_headers()
	{
		$csv_headers = '"id","customer_name","group_name","company_name","customer_code","cust_dob","cust_anniversary","cust_gender","cust_address_1","cust_address_2","cust_city","cust_state","cust_pincode","cust_country","cust_mobile","cust_landline","cust_email","cust_facebook","cust_website","cust_description","latitude","longitude","cust_enable_loyalty"';
		return $csv_headers;
	}
	public function csv_sample()
	{
		$this->load->view('session/pos_session');
		$this->load->helper('file');
		$this->load->dbutil();
		$this->load->helper('download');
		$csv_headers = $this->csv_headers();
		force_download('sample_customer_import.csv',$csv_headers);
	}
	public function import_action()
	{
		$this->load->view('session/pos_session');
		$root = APPPATH;
		$dirame = '/user_images/'.md5($this->acc).'/csv_cust_import';
		if(!is_dir($root.$dirame)){mkdir($root.$dirame,0777,true);}
		$path = $root.$dirame;
		$config['upload_path'] = './'.$path.'/';
		$config['allowed_types'] = 'csv|CSV';
		$config['max_size']	= '2048'; // 2mb
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if(!$this->upload->do_upload())
		{
			$error = array('errorcode' => $this->upload->display_errors());
			$str = str_replace(array('<p>','</p>'),'',$error['errorcode']);
			$this->session->set_flashdata('form_errors', '<span class="glyphicon glyphicon-remove-sign"></span> '.$str);
			redirect(base_url().'customers/import');
		} else {
			$data = array('upload_data' => $this->upload->data());
			$file = $data['upload_data']['file_name'];
			$hash = md5($this->acc);			
			$old_name = './'.$path.'/'.$file;
			$new_name = './'.$path.'/'.$hash.'.csv';
			rename($old_name,$new_name);
			//upload done
			$csv_array = $this->csvreader->parse_file($new_name);
			$csv_fields = $this->csvreader->get_fields();
			$csv_fields_array = explode(",",$csv_fields[0]);
			$csv_headers = $this->csv_headers();
			$csv_headers = str_replace('"', "", $csv_headers);
			$csv_headers_array = explode(",",$csv_headers);
			$up_limit = 1000;			
			$tot_rows = count($csv_array);
			if($csv_fields_array === $csv_headers_array)
			{
				if($csv_array)
				{
					if(count($csv_array) <= $up_limit)
					{
						$this->benchmark->mark('code_start');
						list($a,$b,$c,$d) = array(0,0,0,0);
						$data['countries_assoc'] = $this->setup_model->countries_assoc();
						foreach($csv_array as $sub_array)
						{
							$new_row = array();
							$outlet_tax = array();
							$keys = key($sub_array);
							$keys = explode(",",$keys);
							$values = str_getcsv($sub_array[key($sub_array)],",",'"');
							$main = array_combine($keys,$values);
							if($main['customer_name'] != '')
							{
								$main['cust_country'] = array_key_exists(strtoupper($main['cust_country']),$data['countries_assoc']) ? strtoupper($main['cust_country']) : 'IN';
								$response = $this->import_bulk_customers($main);
								if($response == 1)
								{
									$a++;	
								} else if($response == 2) {
									$b++;	
								} else if($response == 0) {
									$c++;	
								}
							} else {
								$d++;
							}
						}
						$this->benchmark->mark('code_end');
						$phrase = '<ul class="list-group">';
						$phrase .= '<li class="list-group-item"><span class="glyphicon glyphicon-ok-sign"></span> '.$tot_rows.' CSV row(s) progressed..</li>';	
						if($a > 0)
						{
							$phrase .= '<li class="list-group-item"><span class="glyphicon glyphicon-ok-sign"></span> '.$a.' Customers(s) successfully imported</li>';	
						} 
						if($b > 0) {
							$phrase .= '<li class="list-group-item text-danger"><span class="glyphicon glyphicon-remove-sign"></span> '.$b.' Customers(s) upload failed: You have exceeded maximum customer limit, please '.anchor('account','upgrade','class="btn btn-xs btn-primary"').' your account to add more customers.</li>';	
						} 
						if($c > 0) {
							$phrase .= '<li class="list-group-item text-danger"><span class="glyphicon glyphicon-remove-sign"></span> '.$c.' Customers(s) upload dropped due to some unknown error</li>';	
						} 
						if($d > 0) {
							$phrase .= '<li class="list-group-item text-danger"><span class="glyphicon glyphicon-remove-sign"></span> '.$d.' Customers(s) upload failed since customer name not found.</li>';	
						} 
						$precent = !is_float($a / $tot_rows) ? ($a / $tot_rows) * 100 : number_format(($a / $tot_rows) * 100,2);
						$phrase .= '<li class="list-group-item"><i class="fa fa-upload fa-fw"></i> Bulk Import '.$precent.'% done.</li>';	
						$phrase .= '<li class="list-group-item"><i class="fa fa-clock-o fa-fw"></i> Latency '.$this->benchmark->elapsed_time('code_start', 'code_end').' seconds</li>';	
						$phrase .= '</ul>';
						$this->session->set_flashdata('form_success', $phrase);
						redirect(base_url().'customers/import');						
					} else {
						$this->session->set_flashdata('form_errors', 'Error: CSV Data upload limit is '.$up_limit.', but "'.count($csv_array).'" rows found. Try a bit littler!');
						redirect(base_url().'customers/import');						
					}
				} else {
					$this->session->set_flashdata('form_errors', 'Error: CSV Data not found!');
					redirect(base_url().'customers/import');						
				}
			} else {
				$this->session->set_flashdata('form_errors', 'Error: CSV Data headers are obsolete. Please '.anchor('customers/csv_sample','download','class="btn btn-xs btn-primary"').' the sample for proper format and try again!');
				redirect(base_url().'customers/import');										
			}
		}
	}
	public function import_bulk_customers($main)
	{
		$dob = date('Y-m-d',strtotime($main['cust_dob']));
		list($dob_yy,$dob_mm,$dob_dd) = strtotime($main['cust_dob']) > 0 ? explode('-',$dob) : array(0,0,0);
		$ann = date('Y-m-d',strtotime($main['cust_anniversary']));
		list($ann_yy,$ann_mm,$ann_dd) = strtotime($main['cust_anniversary']) > 0  ? explode('-',$ann) : array(0,0,0);
		$cust_group = $this->customer_model->get_groupid_like_name($main['group_name'],$this->acc);
		$data = array(
					'cust_group' => $cust_group,
					'cust_code' => $main['customer_code'],
					'comp_name' => $main['company_name'],
					'cust_dob' => array('yy' => $dob_yy,'mm' => $dob_mm,'dd' => $dob_dd),
					'cust_ann' => array('yy' => $ann_yy,'mm' => $ann_mm,'dd' => $ann_dd),
					'cust_name' => $main['customer_name'],
					'cust_gender' => in_array($main['cust_gender'],array('M','F')) ? $main['cust_gender'] : 'M',
					'cust_addrr_1' => $main['cust_address_1'],
					'cust_addrr_2' => $main['cust_address_2'],
					'cust_city' => $main['cust_city'],
					'cust_state' => $main['cust_state'],
					'cust_pcode' => $main['cust_pincode'],
					'cust_country' => $main['cust_country'],
					'cust_mobile' => $main['cust_mobile'],
					'cust_ll' => $main['cust_landline'],
					'cust_email' => $main['cust_email'],
					'cust_fb' => $main['cust_facebook'],
					'cust_web' => $main['cust_website'],
					'cust_desc' => $main['cust_description'],
					'cust_enable_loyalty' => $main['cust_enable_loyalty'] == 1 ? 30 : 40,
					'latitude' => $main['latitude'],
					'longitude' => $main['longitude'],
					'acc' => $this->acc,
					);
		if(empty($main['id']))
		{
			$response = $this->customer_model->insert_customer($data);
		} else {
			$data['cust_id'] = $main['id'];
			$response = $this->customer_model->update_customer($data);
		}
		return $response;
	}
	public function download_customers()
	{
		$this->load->view('session/pos_session');
		$this->load->helper('file');
		$this->load->dbutil();
		$this->load->helper('download');
		$query = $this->customer_model->download_customers($this->acc);
		$data = $this->dbutil->csv_from_result($query,',', "\r\n");
		force_download($this->session->userdata('pos_hoster_cmp').'-customers('.rand(10000,100000).').csv',$data);
		exit;
		
	}
}
?>