<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Setup extends CI_Controller
{
	public $acc;
	public $privelage;
	public $pos_user;
	public $user_id;
	public $max_qt_headers;
	public $max_qt_products_per_page;
	public $max_qt_pages;
	
    public function __construct() 
    {
        parent::__construct();
		$this->pos_user = $this->session->userdata('pos_user');
		$this->user_id = $this->session->userdata('user_id');
		$this->privelage = $this->session->userdata('privelage');
		$this->acc = $this->session->userdata('acc_no');
		$this->max_qt_headers = 4;
		$this->max_qt_products_per_page = 36;
		$this->max_qt_pages = 10;
		$subdomain = $this->session->userdata('subdomain');
		$this->is_valid_browser_domain = is_this_subdomain_browser($subdomain);
		$validity = $this->login_model->check_validity($this->acc);
		if($validity == 0)
		{
			redirect(base_url().'account');
		}		
    }
	public function make_setup()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1)
			{
				//header
				$header['view']['title'] = 'Setup';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$path = APPPATH.'user_images/'.md5($this->acc);	
				$data['cur_stocks'] = $this->product_model->product_count($this->acc);
				$data['cur_stocks'] = $data['cur_stocks']['grand_total'];
				$proc_names = $this->user_model->M_current_proc_names();
				$data['user_count'] = count($proc_names);
				$data['cust_count'] = $this->customer_model->customer_count();
				$currency = $this->setup_model->M_setup_currency();
				$data['country_dropdown'] = $this->setup_model->get_countries_select();
				foreach($currency as $arr)
				{
					$data['curr'][$arr[0]] = $arr[1];
				}
				$data['master_data'] = $this->setup_model->M_get_masterdata($this->acc);
				$bool = array('' => '--', 1 => 'Enable',0 => 'Disable');
				$data['account'] = $this->login_model->get_timezone_loc_plan_validity($this->acc);
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$data['countries_assoc'] = $this->setup_model->countries_assoc();
				$this->load->view('setup/setup',$data);
				
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/setup.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.js"></script>'."\n";
				$footer['foot']['script'][3] =  '<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/respond.js/1.3.0/respond.js"></script>'."\n";
				$footer['foot']['script'][4] =  '<script type="text/javascript" src="http://maps.google.com/maps/api/js"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer); 
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function outlets_and_registers()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1)
			{
				//header
				$header['view']['title'] = 'Outlets and Registers';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);

				//body
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$data['outlets'] = $this->outlet_model->get_all_outlets($this->acc);
				$this->load->view('setup/outlets_and_registers',$data);	
				
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/outlets_and_registers.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function taxes()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1)
			{
				//header
				$header['view']['title'] = 'Taxes';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$data['taxes'] = $this->taxes_model->get_all_single_taxes($this->acc);
				$data['outlet_taxes'] = $this->taxes_model->get_all_outlet_taxes($this->acc);
				$data['group_taxes'] = $this->taxes_model->get_group_taxes($this->acc);
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$this->load->view('setup/taxes',$data);	
				
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function create_tax()
	{
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$this->load->view('taxes/add_tax');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h4>Session Expired! Please <a href="'.base_url().'setup/taxes">Login</a> again</h4>');
		}
	}
	public function add_tax()
	{
		$this->load->view('session/pos_session');
		$tax_name = $this->input->post('tax_name');	
		$tax_rate = $this->input->post('tax_rate');	
		$redirect = $this->input->post('redirect');	
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Tax Successfully Created!', 
						2 => 'Tax name already exists. Try another tax name!', 
						);
		$response = $this->taxes_model->add_tax($tax_name,$tax_rate,$this->acc);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, ucfirst(strtolower($phrase[$response])));
		redirect($redirect);
	}
	public function group_tax()
	{
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$data['single_taxes'] = $this->taxes_model->get_single_taxes_combo($this->acc);
				$this->load->view('taxes/group_tax',$data);	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h4>Session Expired! Please <a href="'.base_url().'setup/taxes">Login</a> again</h4>');
		}
	}
	public function add_group()
	{
		$this->load->view('session/pos_session');
		$tax_group_name = $this->input->post('grp_tax_name');	
		$tax_group_array = $this->input->post('tax_groups');	
		$redirect = $this->input->post('redirect');	
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Group Tax Successfully Created!', 
						2 => 'Group Tax name already exist. please Try another group name', 
						);
		$response = $this->taxes_model->add_group($tax_group_name,$tax_group_array,$this->acc);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$string = ucfirst(strtolower($phrase[$response])); //capitalise paragraph
		$string = preg_replace_callback('/[.!?].*?\w/', create_function('$matches', 'return strtoupper($matches[0]);'),$string);
		$this->session->set_flashdata($div, $string);
		redirect($redirect);
	}
	public function edit_single_tax($tax_id)
	{
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				list($data['single_tax_name'],$data['single_tax_rate']) = $this->taxes_model->get_single_tax_data($tax_id,$this->acc);
				$data['single_tax_id'] = $tax_id;
				$this->load->view('taxes/edit_single_tax',$data);	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 style="padding:5px;">Session Expired! Please <a href="'.base_url().'setup/taxes">Login</a> again</h3>');
		}
	}
	public function edit_group_tax($tax_id)
	{
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$data = $this->taxes_model->get_group_tax_data($tax_id,$this->acc);
				$data['get_single_taxes_combo'] = $this->taxes_model->get_single_taxes_combo($this->acc);
				$data['group_tax_id'] = $tax_id;
				$this->load->view('taxes/edit_group_tax',$data);	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 style="padding:5px;">Session Expired! Please <a href="'.base_url().'setup/taxes">Login</a> again</h3>');
		}
	}
	public function delete_group_tax($grp_tax_id)
	{
		$this->load->view('session/pos_session');
		if($this->privelage == 1)
		{
			$response = $this->taxes_model->delete_group_tax($grp_tax_id,$this->acc);
			$div = ($response['stat'] == 1) ? 'form_success' : 'form_errors';
			$phrase = array(0 => $response['error_str'],
							1 => $response['error_str'], 
							);
			$this->session->set_flashdata($div, $phrase[$response['stat']]);
			redirect(base_url().'setup/taxes');
			
		} else {
			$this->load->view('noaccess/noaccess');	
		}
	}
	public function delete_single_tax($single_tax_id)
	{
		$this->load->view('session/pos_session');
		if($this->privelage == 1)
		{
			list($tax_name,$tax_val) = $this->taxes_model->get_single_tax_data($single_tax_id,$this->acc);
			if(!empty($tax_name)) // check if its a valid id
			{
				$response = $this->taxes_model->delete_single_tax($single_tax_id,$this->acc);
				$div = ($response['stat'] == 1) ? 'form_success' : 'form_errors';
				$phrase = array(0 => $response['error_str'],
								1 => $response['error_str'], 
								2 => $response['error_str'], 
								);
				$this->session->set_flashdata($div, $phrase[$response['stat']]);
				redirect(base_url().'setup/taxes');
			} else {
				$this->load->view('site_404/url_404'); 
			}
		} else {
			$this->load->view('noaccess/noaccess');	
		}
	}
	public function update_group_tax($grp_tax_id)
	{
		$this->load->view('session/pos_session');
		$parent_name = $this->input->post('parent_name');
		$new_group_taxes = $this->input->post('group_taxes');
		$hid_all_taxes = $this->input->post('hid_all_taxes');
		$delete = array_diff($hid_all_taxes,$new_group_taxes); //delete unwanted taxes
		$insert = array_unique(array_diff($new_group_taxes,$hid_all_taxes)); // insert new updated taxes which must be unique
		if($this->privelage == 1)
		{
			$response = $this->taxes_model->update_group_tax($grp_tax_id,$parent_name,$insert,$delete,$this->acc);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$phrase = array(0 => 'Unknown Error! Please try again!',
							1 => 'Tax group successfully Updated.', 
							2 => 'Group Tax name already exist. Please Try another group name!', 
							);
			$this->session->set_flashdata($div, $phrase[$response]);
			redirect(base_url().'setup/taxes');
			
		} else {
			$this->load->view('noaccess/noaccess');	
		}
	}
	public function update_single_tax($single_tax_id)
	{
		$this->load->view('session/pos_session');
		list($tax_name,$tax_val) = $this->taxes_model->get_single_tax_data($single_tax_id,$this->acc);
		if($tax_name != '') // check if its a valid id
		{
			$single_tax_name = $this->input->post('single_tax_name');
			$single_tax_rate = $this->input->post('single_tax_rate');
			if($this->privelage == 1)
			{
				$response = $this->taxes_model->update_single_tax($single_tax_id,$single_tax_name,$single_tax_rate,$this->acc);
				$div = ($response == 1) ? 'form_success' : 'form_errors';
				$phrase = array(0 => 'Unknown Error! Please try again!',
								1 => 'Tax successfully Updated.', 
								2 => 'Tax name already exists. Try another tax name!', 
								);
				$this->session->set_flashdata($div, $phrase[$response]);
				redirect(base_url().'setup/taxes');				
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			$this->load->view('site_404/url_404'); 
		}
	}
	public function update_account()
	{
		$this->load->view('session/pos_session');
		$error = '';
		if(!empty($_FILES['userfile']['name'])) 
		{
			$logo_dir = APPPATH.'user_images/'.md5($this->acc).'/logo';
			if(!is_dir($logo_dir)){mkdir($logo_dir,0777,true);}		
			$path = $logo_dir;
			$config['upload_path'] = './'.$path.'/';
			$config['allowed_types'] = 'gif|jpg|png|GIF|JPG|PNG';
			$config['max_size']	= 1 * 1024;
			$config['max_width']  = '3264';
			$config['max_height']  = '2448';
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if(!$this->upload->do_upload())
			{
				$error = $this->upload->display_errors();
				$img_info = false;
			} else {			
				$file_pattern = './'.$path.'/logo_thumb.*';			
				$file_pattern2 = './'.$path.'/large_logo_thumb.*';			
				array_map( "unlink", glob( $file_pattern ) );
				array_map( "unlink", glob( $file_pattern2 ) );

				$data = array('upload_data' => $this->upload->data());
				$file = $data['upload_data']['file_name'];
				$file_ext = $data['upload_data']['file_ext'];
				
				$filename = 'logo';			
				$old_name = './'.$path.'/'.$file;
				$new_name = './'.$path.'/'.$filename.$file_ext;

				rename($old_name,$new_name);
	
				$config['image_library'] = 'gd2';
				$config['source_image'] = $path.'/'.$filename.$file_ext;
				$config['new_image'] = $path.'/'.$filename.$file_ext;
				$config['create_thumb'] = TRUE;
				$config['maintain_ratio'] = FALSE;
				$config['width'] = '150';
				$config['height'] = '150';
				$this->load->library('image_lib', $config);
				$this->image_lib->initialize($config);				
				if($this->image_lib->resize())
				{
					$config['image_library'] = 'gd2';
					$config['source_image'] = $path.'/'.$filename.$file_ext;
					$config['new_image'] = $path.'/large_'.$filename.$file_ext;
					$config['create_thumb'] = TRUE;
					$config['maintain_ratio'] = TRUE;
					$config['width'] = 1024;
					$config['height'] = 768;			
					$this->load->library('image_lib', $config);
					$this->image_lib->initialize($config);				
					if($this->image_lib->resize())
					{
						unlink($new_name);
						$img_info = true;
					}
				}		
			}
		} else {
			$img_info = false;
		}
		
		$data['company_name'] = $this->input->post('company_name'); 
		$data['curr'] = $this->input->post('curr'); 
		$data['tz'] = $this->input->post('timezones'); 
		$data['fb'] = $this->input->post('fb'); 

		$data['contact_name'] = $this->input->post('contact_name'); 
		$data['contact_mobile'] = $this->input->post('contact_mobile'); 
		$data['contact_email'] = $this->input->post('contact_email'); 
		$data['contact_addr1'] = $this->input->post('contact_addr1'); 
		$data['contact_addr2'] = $this->input->post('contact_addr2'); 
		$data['contact_city'] = $this->input->post('contact_city'); 
		$data['contact_state'] = $this->input->post('contact_state'); 
		$data['contact_postalcode'] = $this->input->post('contact_postalcode'); 
		$data['contact_country'] = $this->input->post('contact_country'); 
		$data['merchant_id'] = $this->input->post('merchant_id'); 
		$data['latitude'] = $this->input->post('c_lat'); 
		$data['longitude'] = $this->input->post('c_long'); 

		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('company_name', 'Company Name', 'trim|required|max_length[25]|min_length[4]|xss_clean');
		if($this->form_validation->run() == FALSE)
		{
			$this->make_setup();
		} else {
			$response = $this->setup_model->update_account($data);
			$res = ($response == 1) ? 'Account information successfully saved ' : 'Oops! Some thing gone wrong. Please try again.';	
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$phrase = ($img_info == true) ? 'with logo' : '';
			$this->session->set_flashdata($div, $res.$phrase.$error);
			redirect(base_url().'setup');
		}
	}
	public function set_loyalty()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1)
			{
				//header
				$header['view']['title'] = 'Set Loyalty';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$data = $this->setup_model->loyalty_stat($this->acc);
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$this->load->view('customers/set_loyalty',$data);
				
				//footer
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
				$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
				$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/set_loyalty.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function update_loyalty()
	{
		$this->load->view('session/pos_session');
		$data['hid_id'] = $this->input->post('hid_id'); 
		$data['enable_loyalty'] = $this->input->post('enable_loyalty');
		$data['loyalty_sale'] = $this->input->post('loyalty_sale');
		$data['loyalty_reward'] = $this->input->post('loyalty_reward');
		$data['acc'] = $this->acc;
		if(count($data) > 0)
		{
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Loyalty settings Successfully Updated!', 
						);
			if($data['enable_loyalty'] == 10)
			{		
				$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
				$this->form_validation->set_rules('loyalty_sale', 'Sale Value', 'trim|required|numeric|xss_clean');
				$this->form_validation->set_rules('loyalty_reward', 'Reward Value', 'trim|required|numeric|xss_clean');
				if($this->form_validation->run() == FALSE)
				{
					$this->set_loyalty();
				} else {
					$response = $this->setup_model->update_loyalty($data);
					$div = ($response == 1) ? 'form_success' : 'form_errors';
					$this->session->set_flashdata($div, ucfirst(strtolower($phrase[$response])));
					redirect(base_url().'setup/loyalty');						
				}
			} else {
				$response = $this->setup_model->update_loyalty($data);
				$div = ($response == 1) ? 'form_success' : 'form_errors';
				$this->session->set_flashdata($div, ucfirst(strtolower($phrase[$response])));
				redirect(base_url().'setup/loyalty');						
			}
		} else {
			$this->load->view('site_404/url_404'); 			
		}
	}
	public function pay_methods()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1)
			{
				//header
				$header['view']['title'] = 'Payment Methods';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$data = array();
				$data['pay_methods'] = $this->payment_type_model->get_my_payment_types($this->acc);
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$this->load->view('setup/payment_methods',$data);	
				
				//footer				
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function quick_touch()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 || $this->privelage == 2)
			{
				//header
				$header['view']['title'] = 'Quick Touch';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$data = array();
				$data['max_qt_headers'] = $this->max_qt_headers;
				$data['max_qt_products_per_page'] = $this->max_qt_products_per_page;
				$data['max_qt_pages'] = $this->max_qt_pages;
				$data['quickeys'] = $this->quickey_model->show_quickeys($this->acc);
				$data['register_quickeys'] = $this->register_model->get_register_outlet_details($this->acc);
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$settings = $this->account_model->current_plan_status($this->acc);
				$data['timezone'] = $settings['timezone']; 
				$this->load->view('quicktouch/quick_touch',$data);	
				
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}		
	}
	public function show_quick_touch($touch_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 || $this->privelage == 2)
			{
				$data = array();
				$data['max_qt_headers'] = $this->max_qt_headers;
				$data['max_qt_products_per_page'] = $this->max_qt_products_per_page;
				$data['max_qt_pages'] = $this->max_qt_pages;
				$data['touch_id'] = $touch_id;
				$data['parent_touch'] = $this->quickey_model->quickey_details($touch_id,$this->acc);
				$data['touch_data'] = $this->quickey_model->get_touch_data($touch_id,$this->acc);
				if(!is_null($data['parent_touch']))
				{
					if($this->session->flashdata('form_errors')) {
						$data['form_errors'] =  $this->session->flashdata('form_errors');
					}
					if($this->session->flashdata('form_success')) {
						$data['form_success'] = $this->session->flashdata('form_success');
					}
					//header
					$header['view']['title'] = 'Edit Quick Touch';
					$role = $this->roles_model->get_roles($this->privelage);
					list($header['role_code'],$header['role_name']) = $role;
					$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
					$header['style'][1] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
					$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/quick_touch/quick_touch.css')."\n";
					$header['style'][3] = link_tag(POS_CSS_ROOT.'repository/autocomplete/autocomplete.css')."\n";
					$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
					$this->load->view('top_page/top_page',$header);
					
					//body
					$this->load->view('quicktouch/edit_quick_touch',$data);	
					
					//footer	
					$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
					$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-touch.js').'"></script>'."\n";
					$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/touch-punch.js').'"></script>'."\n";
					$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
					$footer['foot']['script'][4] = '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/quick_touch.js').'"></script>'."\n";
					$this->load->view('bottom_page/bottom_page',$footer);			
					
				} else {
					$this->load->view('site_404/url_404'); 					
				}
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}		
	}
	public function get_uuid()
	{
		if($this->input->get('count'))
		{
			$count = $this->input->get('count');
			for($i=0;$i<$count;$i++)
			{
				$uuid[] = $this->taxes_model->make_single_uuid();
			}
			echo json_encode($uuid);
		} else {
			die('Parameters not found!');	
		}
	}
	public function update_quick_touch()
	{
		$data = $this->input->post();
		if(
			isset($data['group_params']) || isset($data['group_name_params']) || isset($data['group_delete_params']) || isset($data['add_group_params'])
				|| isset($data['prd_params']) || isset($data['color_change_params']) || isset($data['rename_label_params']) || isset($data['delete_product_params'])
					|| isset($data['update_prd_page']) || isset($data['delete_page']) || isset($data['add_page']) || isset($data['insert_product'])
		)
		{
			//print_r( $data);
			$this->quickey_model->update_quick_touch($data);
		} else {
			die('Parameters not found!');	
		}
	}
	public function quick_touch_search()
	{
		$this->load->view('session/pos_session');
		$term = $this->input->get('term',TRUE);
		$rows = $this->quickey_model->quick_key_Autocomplete(array('keyword' => $term),$this->acc);
		$json_array = array();
		foreach ($rows as $row)
	    	$json_array[] = array('product_id' => $row->product_id, 'prod_name' => $row->prod_name,'scale' => $row->scale, 'labled' => $row->label);
		echo json_encode($json_array);
	}
	public function update_quicktouch($touch_id)
	{
		$this->load->view('session/pos_session');
		$data['touch_id'] = $touch_id;
		$data['touch_name'] = $this->input->post('quicktouch_name');
		$data['acc'] = $this->acc;
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('quicktouch_name', 'Quick touch name', 'trim|required|xss_clean');
		if($this->form_validation->run() == FALSE)
		{
			$this->show_quick_touch($touch_id);
		} else {
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Quick Touch - '.$data['touch_name'].' successfully updated!', 
						);
			$response = $this->quickey_model->update_quickey($data);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			redirect(base_url().'setup/quicktouch');						
		}
	}
	public function quicktouch_add()
	{
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$this->load->view('quicktouch/add');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 style="padding:5px;">Session Expired! Please <a href="'.base_url().'setup/quicktouch">Login</a> again</h3>');
		}
	}
	public function add_quicktouch()
	{
		$data['quicktouch_name'] = $this->input->post('quicktouch_name');
		$data['acc'] = $this->acc;	
		$touch_id = $this->quickey_model->add_quicktouch($data);
		if($touch_id == false)
		{
			$this->session->set_flashdata('form_errors', 'Oops! Something Went Wrong! please try again');
			redirect(base_url().'setup/quicktouch');						
		} else {
			redirect(base_url().'setup/quicktouch/edit/id/'.$touch_id);						
		}
	}
	public function delete_quick_touch($touch_id)
	{
		$this->load->view('session/pos_session');
		$data['touch_id'] = $touch_id;
		$data['acc'] = $this->acc;	
		$phrase = array(0 => 'This Quick touch cant be deleted while it is associated to one of your registers. Please handle your register to some other quick
								touch and try again.',
					1 => 'Quick Touch successfully deleted!', 
					);
		$response = $this->quickey_model->delete_quick_touch($data);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url().'setup/quicktouch');										
	}
	public function theme_post()
	{
		$theme_root = $this->input->post('theme_root');
		$theme_user = $this->input->post('theme_user');
		$response = $this->menu_model->theme_post($theme_root,$theme_user);
		echo $response;	
	}
}
?>
