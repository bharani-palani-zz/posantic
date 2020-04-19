<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Inventory extends CI_Controller
{
	public $acc;
	public $privelage;
	public $pos_user;
	public $user_id;
	public $pos_display_user;
	public $loc_id;
    public function __construct() 
    {
        parent::__construct();
		$this->acc = $this->session->userdata('acc_no');
		$this->privelage = $this->session->userdata('privelage');
		$this->pos_user = $this->session->userdata('pos_user');
		$this->user_id = $this->session->userdata('user_id');
		$this->pos_display_user = $this->session->userdata('pos_display_user');
		$this->loc_id = $this->session->userdata('loc_id');
		$subdomain = $this->session->userdata('subdomain');
		$this->is_valid_browser_domain = is_this_subdomain_browser($subdomain);
		$validity = $this->login_model->check_validity($this->acc);
		if($validity == 0)
		{
			redirect(base_url().'account');
		}
		$this->load->library('csvreader');
		$this->load->dbutil();
    }
	public function stock_transfer()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				//header
				$header['view']['title'] = 'Stock Transfer';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
				$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$data = array();
				$data['outlets'] = array('' => '') + $this->user_model->get_locations($this->acc);
				$data['random_string'] = 'Transfer('.$this->generateRandomString(5).') '.date('d/m/Y');
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$this->load->view('inventory/stock_transfer',$data);
				
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
				$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function stock_return()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				$data = array();
				$data['outlets'] = array('' => '') + $this->user_model->get_locations($this->acc);
				$data['suppliers'] = $this->supplier_model->M_get_supplier($this->acc);
				$data['random_string'] = 'Return('.$this->generateRandomString(5).') '.date('d/m/Y');
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				//header
				$header['view']['title'] = 'Stock return';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
				$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$this->load->view('inventory/stock_return',$data);
				
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
				$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('noaccess/noaccess');	
			}
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
	function outlet_check($source)
	{
		if($source == $this->input->post('dest_outlet'))
		{
			$this->form_validation->set_message('outlet_check', 'The %s field can not be same as destination field');
			return FALSE;
		} else {
			return TRUE;
		}
	}
	public function add_stock_transfer()
	{
		$this->load->view('session/pos_session');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('transfer_name', 'transfer name', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('source_outlet', 'source outlet', 'trim|required|callback_outlet_check|xss_clean');
		$this->form_validation->set_rules('dest_outlet', 'destination outlet', 'trim|required|xss_clean');
		if($this->form_validation->run() == FALSE)
		{
			$this->stock_transfer();
		} else {
			if (empty($_FILES['userfile']['name'])) 
			{
				$import_data = array();
			} else {
				$root = APPPATH;
				$dirame = '/user_images/'.md5($this->acc).'/csv_inv_import';
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
					$this->session->set_flashdata('form_errors', $str);
					redirect(base_url().'inventory/stock_order');
				} else {
					$upload = array('upload_data' => $this->upload->data());
					$file = $upload['upload_data']['file_name'];
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
					if($csv_fields_array === $csv_headers_array)
					{
						if(count($csv_array) <= 500)
						{
							foreach($csv_array as $sub_array)
							{
								$keys = key($sub_array);
								$keys = explode(",",$keys);
								$values = str_getcsv($sub_array[key($sub_array)],",",'"');
								$import_data['sku'][] = $values[0];
								$import_data['supplier_price'][$values[0]] = $values[1];
								$import_data['quantity'][$values[0]] = $values[2];
							}
							$import_data['sku_str'] = '"'.implode('","',$import_data['sku']).'"';
						} else {
							$this->session->set_flashdata('form_errors', 'CSV import file exceeded 500 rows');
							redirect(base_url().'inventory/stock_transfer');						
						}
					} else {
						$this->session->set_flashdata('form_errors', 'CSV Data headers are obsolete');
						redirect(base_url().'inventory/stock_transfer');						
					}
				}
			}
			$data['import_data'] = $import_data;
			$data['transfer_id'] = $this->taxes_model->make_single_uuid();
			$data['transfer_name'] = $this->input->post('transfer_name');
			$data['source_outlet'] = $this->input->post('source_outlet');
			$data['dest_outlet'] = $this->input->post('dest_outlet');
			$data['reorder_stat'] = $this->input->post('reorder_stat');
			$data['inventory_case'] = 17;
			$data['acc'] = $this->acc;
			$transfer_id = $this->inventory_model->add_stock_transfer($data);
			if($transfer_id == false)
			{
				$this->session->set_flashdata('form_errors', 'Oops! Something Went Wrong! please try again');
				redirect(base_url().'inventory/stock_transfer');						
			} else {
				redirect(base_url().'inventory/freight/edit/id/'.$data['transfer_id']);						
			}
		}		
	}
	public function add_stock_order()
	{
		$this->load->view('session/pos_session');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('order_name', 'order name', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('dest_outlet', 'destination outlet', 'trim|required|xss_clean');
		$this->form_validation->set_rules('supplier', 'supplier', 'trim|required|xss_clean');
		if($this->form_validation->run() == FALSE)
		{
			$this->stock_order();
		} else {
			if (empty($_FILES['userfile']['name'])) 
			{
				$import_data = array();
			} else {
				$root = APPPATH;
				$dirame = '/user_images/'.md5($this->acc).'/csv_inv_import';
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
					$this->session->set_flashdata('form_errors', $str);
					redirect(base_url().'inventory/stock_order');
				} else {
					$upload = array('upload_data' => $this->upload->data());
					$file = $upload['upload_data']['file_name'];
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
					if($csv_fields_array === $csv_headers_array)
					{
						if(count($csv_array) <= 500)
						{
							foreach($csv_array as $sub_array)
							{
								$keys = key($sub_array);
								$keys = explode(",",$keys);
								$values = str_getcsv($sub_array[key($sub_array)],",",'"');
								$import_data['sku'][] = $values[0];
								$import_data['supplier_price'][$values[0]] = $values[1];
								$import_data['quantity'][$values[0]] = $values[2];
							}
							$import_data['sku_str'] = '"'.implode('","',$import_data['sku']).'"';
						} else {
							$this->session->set_flashdata('form_errors', 'CSV import file exceeded 500 rows');
							redirect(base_url().'inventory/stock_order');						
						}
					} else {
						$this->session->set_flashdata('form_errors', 'CSV Data headers are obsolete');
						redirect(base_url().'inventory/stock_order');						
					}
				}
			}
			$data['import_data'] = $import_data;
			$data['transfer_id'] = $this->taxes_model->make_single_uuid();
			$data['transfer_name'] = $this->input->post('order_name');
			$data['dest_outlet'] = $this->input->post('dest_outlet');
			$data['supplier'] = $this->input->post('supplier');
			$data['reorder_stat'] = $this->input->post('reorder_stat');
			$data['inventory_case'] = 18;
			$data['acc'] = $this->acc;
			$transfer_id = $this->inventory_model->add_stock_transfer($data);
			if($transfer_id == false)
			{
				$this->session->set_flashdata('form_errors', 'Oops! Something Went Wrong! please try again');
				redirect(base_url().'inventory/stock_order');						
			} else {
				redirect(base_url().'inventory/freight/edit/id/'.$data['transfer_id']);						
			}
		}		
	}
	public function add_stock_return()
	{
		$this->load->view('session/pos_session');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('return_name', 'return name', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('supplier', 'supplier', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('return_outlet', 'returning outlet', 'trim|required|xss_clean');
		if($this->form_validation->run() == FALSE)
		{
			$this->stock_return();
		} else {
			if (empty($_FILES['userfile']['name'])) 
			{
				$import_data = array();
			} else {
				$root = APPPATH;
				$dirame = '/user_images/'.md5($this->acc).'/csv_inv_import';
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
					$this->session->set_flashdata('form_errors', $str);
					redirect(base_url().'inventory/stock_order');
				} else {
					$upload = array('upload_data' => $this->upload->data());
					$file = $upload['upload_data']['file_name'];
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
					if($csv_fields_array === $csv_headers_array)
					{
						if(count($csv_array) <= 500)
						{
							foreach($csv_array as $sub_array)
							{
								$keys = key($sub_array);
								$keys = explode(",",$keys);
								$values = str_getcsv($sub_array[key($sub_array)],",",'"');
								$import_data['sku'][] = $values[0];
								$import_data['supplier_price'][$values[0]] = $values[1];
								$import_data['quantity'][$values[0]] = $values[2];
							}
							$import_data['sku_str'] = '"'.implode('","',$import_data['sku']).'"';
						} else {
							$this->session->set_flashdata('form_errors', 'CSV import file exceeded 500 rows');
							redirect(base_url().'inventory/return');						
						}
					} else {
						$this->session->set_flashdata('form_errors', 'CSV Data headers are obsolete');
						redirect(base_url().'inventory/return');						
					}
				}
			}
			$data['import_data'] = $import_data;			
			$data['transfer_id'] = $this->taxes_model->make_single_uuid();
			$data['transfer_name'] = $this->input->post('return_name');
			$data['supplier'] = $this->input->post('supplier');
			$data['source_outlet'] = $this->input->post('return_outlet');
			$data['dest_outlet'] = $this->input->post('return_outlet');			
			$data['inventory_case'] = 19;
			$data['reorder_stat'] = NULL;
			$data['acc'] = $this->acc;
			$transfer_id = $this->inventory_model->add_stock_transfer($data);
			if($transfer_id == false)
			{
				$this->session->set_flashdata('form_errors', 'Oops! Something Went Wrong! please try again');
				redirect(base_url().'inventory/return');						
			} else {
				redirect(base_url().'inventory/freight/edit/id/'.$data['transfer_id']);						
			}
		}		
	}
	public function edit_transfer($transfer_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				$data = array();
				$data['details'] = $this->inventory_model->transfer_main_details($transfer_id,$this->acc);
				if(!is_null($data['details']))
				{
					if($data['details']['transfer_stat'] == 5)
					{
						$config["base_url"] = base_url()."inventory/freight/edit/id/".$transfer_id."/page";
						$config["total_rows"] = $this->inventory_model->get_transfer_subproducts_count('','',$transfer_id,$data['details']['dest_outlet_id'],$this->acc);
						$config["per_page"] = 50;
						$config["uri_segment"] = 7;
						$config["num_links"] = 10;
	
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
						$page = ($this->uri->segment(7)) ? $this->uri->segment(7) : 0;
	
						$data['sub_product_array'] = $this->inventory_model->get_transfer_subproducts_data($config["per_page"], $page,$transfer_id,$data['details']['dest_outlet_id'],$this->acc);
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
						$header['view']['title'] = 'Transfer edit';
						$role = $this->roles_model->get_roles($this->privelage);
						list($header['role_code'],$header['role_name']) = $role;
						$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
						$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/autocomplete/autocomplete.css')."\n";
						$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
						$this->load->view('top_page/top_page',$header);
						
						//body
						$this->load->view('inventory/edit_transfer',$data);
						
						//footer
						$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
						$footer['foot']['script'][1] = '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'inventory/edit_transfer.js').'"></script>'."\n";
						$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
						$this->load->view('bottom_page/bottom_page',$footer);			
					} else {
						$this->session->set_flashdata('form_errors', 'Oops !!! This transfer was already sent and cant be updated..');
						redirect(base_url().'inventory/freight/'.$data['details']['transfer_index']);						
					}
				} else {
					$this->load->view('site_404/url_404'); 				
				}
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function transfer_autocomplete()
	{
		$this->load->view('session/pos_session');
		$outlet_id = $this->input->post('outlet_id');
		$term = $this->input->post('term',TRUE);
		$rows = $this->inventory_model->transfer_GetAutocomplete(array('keyword' => $term),$outlet_id,$this->acc);
		$json_array = array();
		foreach ($rows as $row)
	    	$json_array[] = array('product_id' => $row->product_id, 'prod_name' => $row->prod_name,'source_stock' => $row->source_stock,'supplier_price' => $row->supplier_price,'sku' => $row->sku,'reorder_qty' => $row->reorder_qty);
		echo json_encode($json_array);		
	}
	public function insert_ajax_transfer()
	{
		$data = $this->input->post();
		$data['acc'] = $this->acc;
		$response = $this->inventory_model->insert_ajax_transfer($data);
		echo $response;
	}
	public function delete_transfer_single_product()
	{
		$this->load->view('session/pos_session');
		$child_id = $this->input->post('child_id');
		$response = $this->inventory_model->delete_transfer_single_product($child_id,$this->acc);	
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
		
	}
	public function update_stock_transfer($transfer_id)
	{
		$this->load->view('session/pos_session');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('transfer_name', 'transfer name', 'trim|required|max_length[40]|xss_clean');
		if($this->form_validation->run() == FALSE)
		{
			$this->edit_transfer($transfer_id);
		} else {
			$data['transfer_id'] = $transfer_id;
			$data['transfer_name'] = $this->input->post('transfer_name');
			$data['transfer'] = $this->input->post('transfer');
			$data['acc'] = $this->acc;
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			if($this->session->flashdata('form_success')) {
				$data['form_success'] = $this->session->flashdata('form_success');
			}
			$response = $this->inventory_model->update_stock_transfer($data);
			if($response == false)
			{
				$this->session->set_flashdata('form_errors', 'Oops! Something Went Wrong! please try again');
				redirect(base_url().'inventory/freight/'.$transfer_id);						
			} else {
				$this->session->set_flashdata('form_success', 'Stock transfer successfully updated');
				redirect(base_url().'inventory/freight/'.$transfer_id);						
			}
		}
	}
	public function show_transfer($transfer_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				$data = array();
				$data['details'] = $this->inventory_model->transfer_main_details($transfer_id,$this->acc);	
				if(!is_null($data['details']))
				{
					$config["base_url"] = base_url()."inventory/freight/".$transfer_id."/page";
					$config["total_rows"] = $this->inventory_model->get_transfer_subproducts_count('','',$transfer_id,$data['details']['dest_outlet_id'],$this->acc);
					$config["per_page"] = 50;
					$config["uri_segment"] = 5;
					$config["num_links"] = 10;

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
					$page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
					
					$data['sub_product_array'] = $this->inventory_model->get_transfer_subproducts_data($config["per_page"], $page,$transfer_id,$data['details']['dest_outlet_id'],$this->acc);
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
					$header['view']['title'] = 'Inventory Transfer';
					$role = $this->roles_model->get_roles($this->privelage);
					list($header['role_code'],$header['role_name']) = $role;
					$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
					$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
					$this->load->view('top_page/top_page',$header);
					
					//body
					$this->load->view('inventory/show_transfer',$data);
					
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
	public function activity()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{				
				$data = array();
				$data['outlets'] = array('' => '') + $this->user_model->get_locations($this->acc);
				$data['suppliers'] = $this->supplier_model->M_get_supplier($this->acc);
				$data['log_codes'] = array('ALL' => 'All Orders') + $this->log_code_model->get_log_codes_wrt_sector(array('inventory_case'));
				$where_array = array('transfer_stat' => 5);
				$data['init_transfer_stat'] = 5;
				$config["base_url"] = base_url()."inventory/page";
				$config["total_rows"] = $this->inventory_model->all_activity_tot_rows('','',$where_array,$this->acc);
				$config["per_page"] = 50;
				$config["uri_segment"] = 3;
				$config["num_links"] = 10;

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
				$data["all_activity"] = $this->inventory_model->all_activity_page_limit($config["per_page"], $page ,$where_array,$this->acc);
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
				$header['view']['title'] = 'Inventory';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$this->load->view('inventory/activity',$data);
				
				//footer				
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'inventory/activity.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function search()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				$data = array();
				$data['outlets'] = array('' => '') + $this->user_model->get_locations($this->acc);
				$data['suppliers'] = $this->supplier_model->M_get_supplier($this->acc);
				$data['log_codes'] = array('ALL' => 'All Orders') + $this->log_code_model->get_log_codes_wrt_sector(array('inventory_case'));
				$data['init_transfer_stat'] = '';
				$config["base_url"] = base_url()."inventory/lookup".'?'.http_build_query($_GET);
				///$where_array = array('log_stat' => 5);
				$where_array['transfer_name'] = $this->db->escape_like_str($this->input->get('transfer_name'));
				$where_array['transfer_stat'] = $this->db->escape_like_str($this->input->get('transfer_stat'));
				$where_array['from_date'] = $this->db->escape_like_str($this->input->get('from_date'));
				$where_array['from_date'] = $this->input->get('from_date') ? strtotime($this->input->get('from_date')) : '';
				$where_array['to_date'] = $this->input->get('to_date') ? strtotime($this->input->get('to_date')) + 86400 : '';
				$where_array['source_outlet'] = $this->input->get('source_outlet');
				$where_array['dest_outlet'] = $this->input->get('dest_outlet');
				$where_array['supplier'] = $this->input->get('supplier');

				$where_array['sort'] = $this->input->get('sort');
				$where_array['flow'] = $this->input->get('flow');

				$config['page_query_string'] = TRUE;
				$config["total_rows"] = $this->inventory_model->all_activity_tot_rows('','',$where_array,$this->acc);
				$config["per_page"] = 50;
				$config["uri_segment"] = 3;
				$config["num_links"] = 10;

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

				$config['anchor_class'] = 'class="link_search"';
				$this->pagination->initialize($config);
				
				$page = $this->input->get("per_page") ? $this->input->get("per_page") : 0;
				//$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
				$data["all_activity"] = $this->inventory_model->all_activity_page_limit($config["per_page"], $page ,$where_array,$this->acc);
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
				$header['view']['title'] = 'Inventory';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$this->load->view('inventory/activity',$data);
				
				//footer				
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'inventory/activity.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function send($transfer_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				$data = array();
				$data['transfer_id'] = $transfer_id;
				$data['supp_details'] = $this->inventory_model->transfer_supplier_details($transfer_id,$this->acc);
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$this->load->view('inventory/send',$data);
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function send_email($transfer_id)
	{
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('rec', 'recipient name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('id', 'email', 'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('cc', 'CC', 'trim|valid_email|xss_clean');
		$this->form_validation->set_rules('sub', 'subject', 'trim|required|xss_clean');
		$this->form_validation->set_rules('msg', 'message', 'trim|xss_clean');
		if($this->form_validation->run() == FALSE)
		{
			$errors = validation_errors();
			$this->session->set_flashdata('form_errors', 'Mail order not sent... '.$errors);
			redirect(base_url().'inventory/freight/'.$transfer_id);						
		} else {
			$sett = $this->admin_model->settings_model();
			$ser_provider_href = $sett[2];
			$ser_provider = $sett[3];
			$this->load->library('email');		
			$post = $this->input->post();
			$merchant = $this->session->userdata('cmp_name');
			$just_domain = preg_replace("/^(.*\.)?([^.]*\..*)$/", "$2", $_SERVER['HTTP_HOST']); 
			$data['details'] = $this->inventory_model->transfer_main_details($transfer_id,$this->acc);
			$data['details']['source_outlet_id'] = $data['details']['source_outlet_id'];
			$sub_product_array = $this->inventory_model->get_transfer_subproducts_data(500, 0,$transfer_id,$data['details']['source_outlet_id'],$this->acc);
			if(isset($post['show_supp']))
			{
				$supp_head = '<th>Supplier Price</th><th>Total cost</th>';
			} else {
				$supp_head = NULL;
			}
			$table = '<table align="center" width="75%" border="2">';
				$table .= '<tr><th>Product</th><th>SKU</th><th>Count</th>'.$supp_head.'</tr>';
				$tot_count = 0;
				$tot_supp = 0;
				if(isset($sub_product_array['product_id']) and count($sub_product_array['product_id']) > 0)
				{
					foreach($sub_product_array['product_id'] as $key => $value)
					{
						$table .= '<tr>';
							$table .= '<td>'.$sub_product_array['prod_name'][$key].'</td>';
							$table .= '<td>'.$sub_product_array['sku'][$key].'</td>';
							$table .= '<td align="right">'.$sub_product_array['ordered'][$key].'</td>';
							if(isset($post['show_supp']))
							{
								$table .= '<td align="right">'.$sub_product_array['supplier_price'][$key].'</td>';
								$table .= '<td align="right">'.$sub_product_array['ordered'][$key] * $sub_product_array['supplier_price'][$key].'</td>';
								$tot_supp += $sub_product_array['ordered'][$key] * $sub_product_array['supplier_price'][$key];
							}
						$table .= '</tr>';
						$tot_count += $sub_product_array['ordered'][$key];
					}
				} else {
					$table .= '<tr><td colspan="5" align="center">No products added for consignment</td></tr>';
				}
				$supp_foot = $tot_supp > 0 ? '<td align="right"><b>'.$this->currency_model->moneyFormat($tot_supp,$this->session->userdata('currency')).'</b></td>' : '<td align="right"><b>0</b></td>';
			
			$table .= '<tr><th>Total</th><td>-</td><td align="right"><b>'.$tot_count.'</b></td>';
			if(isset($post['show_supp']))
			{
				$table .= '<td>-</td>'.$supp_foot;
			}
			$table .= '</tr>';
			$table .= '</table>';
			//warning: unable to send mail to yahoo. to achive email smtp setting has to be accomplished
			$this->email->from('noreply@'.$just_domain, $merchant); //alert mail will only be sent if host has this email id as valid
			$this->email->cc($post['cc']);
			$this->email->bcc('');
			$this->email->to($post['id']); 
			$this->email->subject($post['sub']);
			$msg = '<html>
					<head>
					</head>
					<body>
					<h2>To: '.$post['rec'].'</h2>
					<h2>For Outlet: '.$data['details']['dest_outlet'].'</h2>
					<p>'.$post['msg'].'</p>
					<h4>Consignment</h4>
					<p>'.$table.'</p>
					<p>Regards,<br>'.$merchant.'<br><small>*Powered by <a href="'.$ser_provider_href.'">'.$ser_provider.'</a><small></p>
					</body>
					</html>
					';
			$this->email->message($msg);	
			if($this->email->send())
			{
				$res = $this->send_flag($transfer_id);
				if($res == 1)
				{
					$this->session->set_flashdata("form_success", "Mail & transfer sent successfully");
					redirect(base_url('inventory/freight/'.$transfer_id));
				} else {
					$this->session->set_flashdata("form_success", "Transfer send successfully, but mail not sent");
					redirect(base_url('inventory/freight/'.$transfer_id));					
				}
			} else {
				$this->session->set_flashdata("form_errors", "Mail not sent. Please try again");
				redirect(base_url('inventory/freight/'.$transfer_id));				
			}
		}
	}
	public function send_flag($transfer_id)
	{
		$response = $this->inventory_model->send_transfer($transfer_id,$this->acc);
		return $response;
	}
	public function cancel($transfer_id)
	{
		$this->load->view('session/pos_session');
		if($this->privelage == 1 or $this->privelage == 2)
		{
			$data['details'] = $this->inventory_model->transfer_main_details($transfer_id,$this->acc);	
			if(!is_null($data['details']))
			{
				if($data['details']['transfer_stat'] != 6)
				{
					$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
									1 => 'Transfer successfully cancelled!', 
									);
					$response = $this->inventory_model->cancel_transfer($transfer_id,$this->acc);
					$div = ($response == 1) ? 'form_success' : 'form_errors';
					$this->session->set_flashdata($div, $phrase[$response]);
					redirect(base_url('inventory'));
				} else {
					$this->session->set_flashdata('form_errors', 'This transfer was already received');
					redirect(base_url('inventory'));
				}
			} else {
				$this->load->view('site_404/url_404'); 								
			}
		} else {
			$this->load->view('noaccess/noaccess');	
		}
	}
	public function marksent($transfer_id)
	{
		$this->load->view('session/pos_session');
		if($this->privelage == 1 or $this->privelage == 2)
		{
			$data['details'] = $this->inventory_model->transfer_main_details($transfer_id,$this->acc);	
			if(!is_null($data['details']))
			{
				if($data['details']['transfer_stat'] != 6)
				{
					$this->load->view('session/pos_session');
					$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
									1 => 'Transfer successfully marked as sent!', 
									);
					$response = $this->send_flag($transfer_id);
					$div = ($response == 1) ? 'form_success' : 'form_errors';
					$this->session->set_flashdata($div, $phrase[$response]);
					redirect(base_url('inventory/freight/'.$transfer_id));		
				} else {
					$this->session->set_flashdata('form_errors', 'This transfer was already received');
					redirect(base_url('inventory/activity'));
				}
			} else {
				$this->load->view('site_404/url_404'); 								
			}
		} else {
			$this->load->view('noaccess/noaccess');	
		}
	}
	public function receive($transfer_id)
	{
		$this->load->view('session/pos_session');
		if($this->privelage == 1 or $this->privelage == 2)
		{
			$data['details'] = $this->inventory_model->transfer_main_details($transfer_id,$this->acc);	
			if(!is_null($data['details']))
			{
				if($data['details']['transfer_stat'] != 6)
				{
					$session = array(
								'user_id' => $this->user_id,
								);
					$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
									1 => 'Transfer successfully done. Cross check your stock counts now', 
									);
					$response = $this->inventory_model->do_transfer($transfer_id,$data,$session,$this->acc);
					$div = ($response == 1) ? 'form_success' : 'form_errors';
					$this->session->set_flashdata($div, $phrase[$response]);
					redirect(base_url('inventory/freight/'.$transfer_id));		
				} else {
					$this->session->set_flashdata('form_errors', 'This transfer was already managed');
					redirect(base_url('inventory/freight/'.$transfer_id));					
				}
			} else {
				$this->load->view('site_404/url_404'); 								
			}
		} else {
			$this->load->view('noaccess/noaccess');	
		}
	}
	public function export($transfer_id)
	{
		$this->load->view('session/pos_session');
		$products = $this->inventory_model->download_transfers($transfer_id,$this->acc);
		$delimiter = ",";
        $newline = "\r\n";
        $data = $this->dbutil->csv_from_result($products, $delimiter, $newline);
		$this->load->helper('download');

		$details['details'] = $this->inventory_model->transfer_main_details($transfer_id,$this->acc);	
		$timezone = $this->session->userdata('tz');
		$daylight_saving = date("I");
		$prefix = date('Y_m_d_h_i_s',gmt_to_local(time(),$timezone, $daylight_saving));
		force_download($details['details']['transfer_name']."_".$prefix.'.csv',$data);
	}
	public function stock_order()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				//header
				$header['view']['title'] = 'Stock order';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
				$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body				
				$data = array();
				$data['outlets'] = array('' => '') + $this->user_model->get_locations($this->acc);
				$data['suppliers'] = $this->supplier_model->M_get_supplier($this->acc);
				$data['random_string'] = 'Order('.$this->generateRandomString(5).') '.date('d/m/Y');
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$this->load->view('inventory/stock_order',$data);
				
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
				$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function stock_take_list()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {		
			//header
			$header['view']['title'] = 'Stock Take';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
			$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css')."\n";
			$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/datatables-responsive/css/dataTables.responsive.css')."\n";
			$header['style'][3] = link_tag(POS_CSS_ROOT.'repository/stocktake/stocktake.css')."\n";
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
			$this->load->view('top_page/top_page',$header);
			$data['all_details'] = $this->inventory_model->stock_take_all_details($this->acc);
			$settings = $this->account_model->current_plan_status($this->acc);
			$data['timezone'] = $settings['timezone']; 
			
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			if($this->session->flashdata('form_success')) {
				$data['form_success'] = $this->session->flashdata('form_success');
			}

			$this->load->view('inventory/stock_take_list',$data);
	
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
			$footer['foot']['script'][1] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables/media/js/jquery.dataTables.min.js"></script>'."\n";
			$footer['foot']['script'][2] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>'."\n";
			$footer['foot']['script'][3] =  '<script src="'.base_url(POS_JS_ROOT).'/inventory/stock_take_list.js"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer);
		}
	}
	public function insert_stock_take()
	{
		
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				//header
				$header['view']['title'] = 'Stock Take';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
				$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				//body
		
				$data = array();
				$data['random_string'] = 'Stock Take('.$this->generateRandomString(5).') '.date('d/m/Y');
				$data['outlets'] = array('' => '') + $this->user_model->get_locations($this->acc);

				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$this->load->view('inventory/stock_take',$data);
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	
		//footer
		$this->load->view('bottom_page/bottom_page');			
	}
	public function add_stock_take()
	{
		$this->load->view('session/pos_session');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('take_name', 'stock take name', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('outlet', 'outlet', 'trim|required|xss_clean');
		if($this->form_validation->run() == FALSE)
		{
			$this->insert_stock_take();
		} else {
			$data['take_id'] = $this->taxes_model->make_single_uuid();
			$data['take_name'] = $this->input->post('take_name');
			$data['outlet'] = $this->input->post('outlet');
			$data['acc'] = $this->acc;
			$transfer_id = $this->inventory_model->add_stock_take($data);
			if($transfer_id == false)
			{
				$this->session->set_flashdata('form_errors', 'Oops! Something Went Wrong! please try again');
				redirect(base_url().'inventory/stock_take');						
			} else {
				redirect(base_url().'inventory/stock_take/edit/id/'.$data['take_id']);						
			}
		}		
	}
	public function edit_stock_take($take_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				$data = array();
				$data['take_id'] = $take_id;
				$data['details'] = $this->inventory_model->stock_take_main_details($take_id,$this->acc);
				if(!is_null($data['details']))
				{
					if($data['details']['status_id'] == 50) //only if pending
					{					
						//header
						$header['view']['title'] = 'Stock Take';
						$role = $this->roles_model->get_roles($this->privelage);
						list($header['role_code'],$header['role_name']) = $role;
						$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
						$header['style'][1] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
						$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
						$header['style'][3] = link_tag(POS_CSS_ROOT.'repository/stocktake/stocktake.css')."\n";
						$header['style'][4] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
						$header['style'][5] = link_tag(POS_CSS_ROOT.'repository/autocomplete/autocomplete.css')."\n";
						$header['style'][6] = link_tag(POS_CSS_ROOT.'repository/custom-scrollbar/jquery.mCustomScrollbar.css')."\n";
						
						$header['top_menu'] = $this->menu_model->get_menu($this->privelage,false);
						$this->load->view('top_page/top_page',$header);
						
						$settings = $this->account_model->current_plan_status($this->acc);
						$data['timezone'] = $settings['timezone']; 
						
						if($this->session->flashdata('form_errors')) {
							$data['form_errors'] =  $this->session->flashdata('form_errors');
						}
						if($this->session->flashdata('form_success')) {
							$data['form_success'] = $this->session->flashdata('form_success');
						}
						//body
						$this->load->view('inventory/edit_stock_take',$data);
						// footer
						$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
						$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
						if(preg_match('/(chromium)[ \/]([\w.]+)/', $ua))
							$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT).'/repository/js/indexeddbshim.min.js"></script>'."\n";
						elseif(preg_match('/(chrome)[ \/]([\w.]+)/', $ua))
							$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT).'/repository/js/indexeddbshim.min.js"></script>'."\n";
						elseif(preg_match('/(safari)[ \/]([\w.]+)/', $ua))
							$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT).'/repository/js/indexeddbshim.min.js"></script>'."\n";
						elseif(preg_match('/(opera)[ \/]([\w.]+)/', $ua))
							$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT).'/repository/js/indexeddbshim.min.js"></script>'."\n";
						elseif(preg_match('/(msie)[ \/]([\w.]+)/', $ua))
							$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT).'/repository/js/indexeddbshim.min.js"></script>'."\n";
						elseif(preg_match('/(mozilla)[ \/]([\w.]+)/', $ua))
								$footer['foot']['script'][1] = '';
						$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT).'/repository/js/dexie.min.js"></script>'."\n";
						$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT).'/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>'."\n";
						$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT).'/inventory/edit_stock_take.js"></script>'."\n";
						$this->load->view('bottom_page/bottom_page',$footer);
					} else {
						$this->session->set_flashdata('form_errors', 'The inventory count "'.$data['details']['stocktake_name'].'" action was already furnished');
						redirect(base_url().'inventory/stock_take');						
					}
				} else {
					$this->load->view('site_404/url_404'); 				
				}
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}		
	}
	public function csv_headers()
	{
		$csv_headers = '"sku","supplier_price","quantity"';
		return $csv_headers;
	}
	public function csv_sample()
	{
		$this->load->view('session/pos_session');
		$this->load->library('csvreader');
		$this->load->helper('file');
		$this->load->dbutil();
		$this->load->helper('download');
		$csv_headers = $this->csv_headers();
		force_download('sample_inventory_import('.rand(10000,100000).').csv',$csv_headers);
	}
	public function finalise($take_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				$data = array();
				$data['take_id'] = $take_id;
				$data['details'] = $this->inventory_model->stock_take_main_details($take_id,$this->acc);
				if(!is_null($data['details']))
				{
					if($data['details']['status_id'] == 50) //only if pending
					{					
						//header
						$header['view']['title'] = 'Finalise Stock Take';
						$role = $this->roles_model->get_roles($this->privelage);
						list($header['role_code'],$header['role_name']) = $role;
						$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
						$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css')."\n";
						$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/datatables-responsive/css/dataTables.responsive.css')."\n";
						$header['style'][3] = link_tag(POS_CSS_ROOT.'repository/stocktake/stocktake.css')."\n";
						$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
						$this->load->view('top_page/top_page',$header);
						
						$settings = $this->account_model->current_plan_status($this->acc);
						$data['timezone'] = $settings['timezone']; 
						
						$data['countables'] = $this->inventory_model->get_stocktake_countables($take_id,$this->acc);
						
						if($this->session->flashdata('form_errors')) {
							$data['form_errors'] =  $this->session->flashdata('form_errors');
						}
						if($this->session->flashdata('form_success')) {
							$data['form_success'] = $this->session->flashdata('form_success');
						}
						//body
						$this->load->view('inventory/finalise',$data);
						// footer
						$footer['foot']['script'][0] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables/media/js/jquery.dataTables.min.js"></script>'."\n";
						$footer['foot']['script'][1] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>'."\n";
						$footer['foot']['script'][2] =  '<script src="'.base_url(POS_JS_ROOT).'/inventory/finalise.js"></script>'."\n";
						$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
						$this->load->view('bottom_page/bottom_page',$footer);
					} else {
						$this->session->set_flashdata('form_errors', 'The inventory count "'.$data['details']['stocktake_name'].'" action was already furnished');
						redirect(base_url().'inventory/stock_take');						
					}
				} else {
					$this->load->view('site_404/url_404'); 				
				}
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}		
	}
	public function stock_take_delete($take_id)	
	{
		$this->load->view('session/pos_session');
		$data['details'] = $this->inventory_model->stock_take_main_details($take_id,$this->acc);
		if($data['details']['status_id'] != 120)
		{
			$response = $this->inventory_model->delete_stock_take($take_id,$this->acc);
			if($response == true)
			{
				$this->session->set_flashdata('form_success', 'Stocktake successfully deleted.');
			} else {
				$this->session->set_flashdata('form_errors', 'Oops! Something Went Wrong! please try again');
			}
		} else {
			$this->session->set_flashdata('form_errors', 'This stocktake was already deleted');
		}
		redirect(base_url().'inventory/stock_take');						
		
	}
	public function stock_take_complete($take_id)
	{
		$this->load->view('session/pos_session');
		if($this->input->post('selected_product') == false)
		{
			$this->session->set_flashdata('form_errors', 'No products selected for stock take.');
			redirect(base_url().'inventory/stock_take');						
		} else {
			$selected_products = $this->input->post('selected_product');
			$data['details'] = $this->inventory_model->stock_take_main_details($take_id,$this->acc);
			if($data['details']['status_id'] != 60)
			{
				$response = $this->inventory_model->complete_stock_take($take_id,$selected_products,$this->acc);
				if($response == true)
				{
					$this->session->set_flashdata('form_success', 'Stocktake completed successfully.');
				} else {
					$this->session->set_flashdata('form_errors', 'Oops! Something Went Wrong! please try again');
				}
			} else {
				$this->session->set_flashdata('form_errors', 'This stocktake was already completed');
			}
			redirect(base_url().'inventory/stock_take');						
		}
	}
	public function show_stock_take($take_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				$data = array();
				$data['take_id'] = $take_id;
				$data['details'] = $this->inventory_model->stock_take_main_details($take_id,$this->acc);
				if(!is_null($data['details']))
				{
					//header
					$header['view']['title'] = 'Stock Take details';
					$role = $this->roles_model->get_roles($this->privelage);
					list($header['role_code'],$header['role_name']) = $role;
					$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
					$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css')."\n";
					$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/datatables-responsive/css/dataTables.responsive.css')."\n";
					$header['style'][3] = link_tag(POS_CSS_ROOT.'repository/stocktake/stocktake.css')."\n";
					$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
					$this->load->view('top_page/top_page',$header);
					
					$settings = $this->account_model->current_plan_status($this->acc);
					$data['timezone'] = $settings['timezone']; 
					
					$data['countables'] = $this->inventory_model->get_stocktake_countables($take_id,$this->acc);
 					
					if($this->session->flashdata('form_errors')) {
						$data['form_errors'] =  $this->session->flashdata('form_errors');
					}
					if($this->session->flashdata('form_success')) {
						$data['form_success'] = $this->session->flashdata('form_success');
					}
					//body
					$this->load->view('inventory/stock_take_details',$data);
					// footer
					$footer['foot']['script'][0] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables/media/js/jquery.dataTables.min.js"></script>'."\n";
					$footer['foot']['script'][1] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>'."\n";
					$footer['foot']['script'][2] =  '<script src="'.base_url(POS_JS_ROOT).'/inventory/stock_take_list.js"></script>'."\n";
					$this->load->view('bottom_page/bottom_page',$footer);

				} else {
					$this->load->view('site_404/url_404'); 				
				}
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}		
	}
	public function export_stocktake_csv($take_id)
	{
		$this->load->view('session/pos_session');
		$products = $this->inventory_model->download_stocktake($take_id,$this->acc);
		$delimiter = ",";
        $newline = "\r\n";
        $data = $this->dbutil->csv_from_result($products, $delimiter, $newline);
		$this->load->helper('download');

		$details['details'] = $this->inventory_model->stock_take_main_details($take_id,$this->acc);	
		$timezone = $this->session->userdata('tz');
		$daylight_saving = date("I");
		$prefix = date('Y_m_d_h_i_s',gmt_to_local(time(),$timezone, $daylight_saving));
		force_download($details['details']['stocktake_name']."_".$prefix.'.csv',$data);
	}
}

