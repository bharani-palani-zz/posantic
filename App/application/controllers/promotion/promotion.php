<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Promotion extends CI_Controller
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
		$validity = $this->login_model->check_validity($this->acc);
		$subdomain = $this->session->userdata('subdomain');
		$this->is_valid_browser_domain = is_this_subdomain_browser($subdomain);
		if($validity == 0)
		{
			redirect(base_url().'account');
		}		
    }
	public function index()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$data = array();
			if($this->session->flashdata('form_uploaded')) {
				$data['form_uploaded'] =  $this->session->flashdata('form_uploaded');
			}
			$data['promotions'] = $this->promotion_model->get_promotions($this->acc);
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			if($this->session->flashdata('form_success')) {
				$data['form_success'] = $this->session->flashdata('form_success');
			}
			$settings = $this->account_model->current_plan_status($this->acc);
			$data['timezone'] = $settings['timezone']; 		
			//header
			$header['view']['title'] = 'Promotions';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
			$this->load->view('top_page/top_page',$header);
			
			//body
			$this->load->view('promotion/promotions',$data);
			
			//footer
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer);			
			
		}
	}
	public function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}	
	public function add()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$data = array();
			$data['group_combo'] = $this->customer_model->group_combo($this->acc);
			$data['company'] = array(NULL => 'ALL OUTLETS') + $this->user_model->get_locations($this->acc);
			$data['random_string'] = $this->generateRandomString(5);
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			if($this->session->flashdata('form_success')) {
				$data['form_success'] = $this->session->flashdata('form_success');
			}
			//header
			$header['view']['title'] = 'Insert Promotions';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
			$header['style'][1] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
			$this->load->view('top_page/top_page',$header);
			
			//body
			$this->load->view('promotion/insert_promotion',$data);
			
			//footer
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
			$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/insert_promotion.js').'"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer);			
			
		}
	}
	public function date_valid($date)
	{
		if(empty($date))
		{
			return true;	
		} else {
			$date = explode("-",$date);
			list($date['yy'],$date['mm'],$date['dd']) = array($date[0],$date[1],$date[2]);			
			if(is_numeric($date['dd']) && is_numeric($date['mm']) && is_numeric($date['yy'])) 
			{      
				if (checkdate($date['mm'], $date['dd'], $date['yy']))
				{
					return TRUE;
				}
			}
			$this->form_validation->set_message('date_valid', 'The Date field is Invalid');
			return false;
		}
	}
	public function insert_promotion()
	{
		$this->load->view('session/pos_session');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('promo_name', 'Promotion name', 'trim|required|max_length[25]|xss_clean');
		$this->form_validation->set_rules('promo_start', 'Promotion Start', 'callback_date_valid');
		$this->form_validation->set_rules('promo_end', 'Promotion End', 'callback_date_valid');
		if($this->form_validation->run() == FALSE)
		{
			$this->add();
		} else {
			$insert = $this->input->post();
			$insert['promo_outlet'] = $insert['promo_outlet'] == "" ? NULL : $insert['promo_outlet'];
			$prom_id = $this->promotion_model->insert_prom_group($insert,$this->acc);
			if(!empty($_FILES['userfile']['name'])) 
			{
				$root = APPPATH;
				$dirame = '/user_images/'.md5($this->acc).'/csv_promo_import';
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
					$this->session->set_flashdata('form_errors', 'Error: '.$str);
					redirect(base_url('promotion/add'));
				} else {
					$data = array('upload_data' => $this->upload->data());
					$file = $data['upload_data']['file_name'];
					$hash = md5($this->acc);			
					$old_name = './'.$path.'/'.$file;
					$new_name = './'.$path.'/'.$hash.'.csv';
					rename($old_name,$new_name);

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
								list($a,$b,$c) = array(0,0,0);
								foreach($csv_array as $sub_array)
								{
									$keys = key($sub_array);
									$keys = explode(",",$keys);
									$values = str_getcsv($sub_array[key($sub_array)],",",'"');
									$main = array_combine($keys,$values);
									$main = $main + array('prom_id' => $prom_id);
									$response = $this->promotion_model->import_promotion($main,$this->acc);
									if($response == 1)
									{
										$a++;	
									} else if($response == 0) {
										$b++;	
									} else if($response == 3) {
										$c++;	
									}
								}
								$this->benchmark->mark('code_end');
								$phrase = '<ul class="list-group"><li class="list-group-item">';
								$phrase .= $tot_rows.' CSV row(s) progressed..</li>';	
								if($a > 0)
								{
									$phrase .= '<li class="list-group-item">'.$a.' Promotion Product(s) successfully uploaded</li>';	
								} 
								if($b > 0) {
									$phrase .= '<li class="list-group-item text-danger"><b>'.$b.' Product(s) upload dropped due to some unknown error</b></li>';	
								} 
								if($c > 0) {
									$phrase .= '<li class="list-group-item text-danger"><b>'.$b.' Product(s) upload failed since SKU of the product not found</b></li>';	
								} 
								$precent = !is_float($a / $tot_rows) ? ($a / $tot_rows) * 100 : number_format(($a / $tot_rows) * 100,2);
								$phrase .= '<li class="list-group-item"><i class="fa fa-upload fa-fw"></i> Bulk Import '.$precent.'% done.</li>';	
								$phrase .= '<li class="list-group-item"><i class="fa fa-clock-o fa-fw"></i> Latency '.$this->benchmark->elapsed_time('code_start', 'code_end').' seconds</li>';	
								$phrase .= '</ul>';
								$this->session->set_flashdata('form_uploaded', $phrase);
								redirect(base_url().'promotion');						
							} else {
								$this->session->set_flashdata('form_errors', 'Error: CSV Data upload limit is '.$up_limit.', but "'.count($csv_array).'" rows found. Try a bit littler!');
								redirect(base_url().'promotion');						
							}
						} else {
							$this->session->set_flashdata('form_errors', 'Error: CSV Data not found!');
							redirect(base_url().'promotion');						
						}
					} else {
						$this->session->set_flashdata('form_errors', 'Error: CSV Data headers are obsolete. Please '.anchor('promotion/csv_template','download').' the sample for proper format and try again!');
						redirect(base_url().'promotion');						
					}
				}
			} else {
				redirect(base_url().'promotion/edit/id/'.$prom_id);						
			}
		}
	}
	public function csv_headers()
	{
		$csv_headers = '"sku","retail_price","margin","discount","loyalty_set","min_units","max_units"';
		return $csv_headers;
	}
	public function download_template()
	{
		$this->load->view('session/pos_session');
		$this->load->helper('file');
		$this->load->dbutil();
		$this->load->helper('download');
		$csv_headers = $this->csv_headers();
		force_download('sample_promotion.csv',$csv_headers);
	}
	public function promotion_detail($prom_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$data = array();
			$config["base_url"] = base_url()."promotion/".$prom_id."/page";
			$config["total_rows"] = $this->promotion_model->get_promotion_subproducts_count('','',$prom_id,$this->acc);
			$config["per_page"] = 50;
			$config["uri_segment"] = 4;
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
			$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
			$data['prom_id'] = $prom_id;
			$data['details'] = $this->promotion_model->promotion_detail($prom_id,$this->acc);

			if(!is_null($data['details']))
			{
				$data['sub_products'] = $this->promotion_model->get_promotion_subproducts_data($config["per_page"], $page,$prom_id,$this->acc);
				$data["links"] = $this->pagination->create_links();				
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$settings = $this->account_model->current_plan_status($this->acc);
				$data['timezone'] = $settings['timezone']; 
				//header
				$header['view']['title'] = 'Promotion detail';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$this->load->view('promotion/promotion_detail',$data);
				
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('site_404/url_404'); 				
			}
		}
	}
	public function edit_promotion($prom_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$data = array();
			$config["base_url"] = base_url()."promotion/edit/id/".$prom_id."/page";
			$config["total_rows"] = $this->promotion_model->get_promotion_subproducts_count('','',$prom_id,$this->acc);
			$config["per_page"] = 50;
			$config["uri_segment"] = 6;
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
			$page = ($this->uri->segment(6)) ? $this->uri->segment(6) : 0;
			$data['prom_id'] = $prom_id;
			$data['sub_product_details'] = $this->promotion_model->promotion_detail($prom_id,$this->acc);
			if(!is_null($data['sub_product_details']))
			{
				$data['sub_product_array'] = $this->promotion_model->get_promotion_subproducts_data($config["per_page"], $page,$prom_id,$this->acc);
				$data["links"] = $this->pagination->create_links();				
				$data['group_combo'] = $this->customer_model->group_combo($this->acc);
				$data['company'] = array(NULL => 'ALL OUTLETS') + $this->user_model->get_locations($this->acc);
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				//header
				$header['view']['title'] = 'Edit Promotions';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
				$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/autocomplete/autocomplete.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$this->load->view('promotion/edit_promotion',$data);
				
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
				$footer['foot']['script'][1] = '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/edit_promotion.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('site_404/url_404'); 				
			}
		}

	}
	public function promo_autocomplete()
	{		
		$this->load->view('session/pos_session');
		$search = $this->input->post('term');
		$response = $this->promotion_model->get_autocomplete_promo($search,$this->acc);	
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
	}
	public function delete_single_product()
	{
		$this->load->view('session/pos_session');
		$child_id = $this->input->post('child_id');
		$response = $this->promotion_model->delete_single_product($child_id,$this->acc);	
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
	}
	public function insert_ajax_promo()
	{
		$data = $this->input->post();
		$data['acc'] = $this->acc;
		$response = $this->promotion_model->insert_ajax_promotion($data);
		echo $response;
	}
	public function update_promotion($prom_id)
	{
		$data = $this->input->post();
		$data['main_prom_id'] = $prom_id;
		$data['acc'] = $this->acc;
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
					1 => 'Promotion successfully saved!', 
					2 => 'No products found to set promotion', 
					);
		$response = $this->promotion_model->update_promotion($data);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url().'promotion/'.$prom_id);						
	}
	public function delete_promotion($prom_id)
	{
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
					1 => 'Prodmotion successfully deleted!', 
					);
		$response = $this->promotion_model->delete_promotion($prom_id,$this->acc);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url().'promotion');						
	}
}