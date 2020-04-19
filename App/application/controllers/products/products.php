<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Products extends CI_Controller
{
	public $acc;
	public $privelage;
	public $pos_user;
	public $user_id;
	public $pos_display_user;
	public $loc_id;
	public $loc_str;
	public $max_variants = 3;
    public function __construct() 
    {
        parent::__construct();
		$this->acc = $this->session->userdata('acc_no');
		$this->privelage = $this->session->userdata('privelage');
		$this->loc_str = $this->session->userdata('loc_str');
		$this->pos_user = $this->session->userdata('pos_user');
		$this->user_id = $this->session->userdata('user_id');
		$this->pos_display_user = $this->session->userdata('pos_display_user');
		$this->loc_id = $this->session->userdata('loc_id');
		$this->load->model('products_model/product_form_model');
		$subdomain = $this->session->userdata('subdomain');

		$this->is_valid_browser_domain = is_this_subdomain_browser($subdomain);

		$validity = $this->login_model->check_validity($this->acc);
		if($validity == 0)
		{
			redirect(base_url().'account');
		}
		$this->load->dbutil();
		$this->load->library('csvreader');
		$this->load->helper('download');
    }
	public function index()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$data = array();
			$sort_array = array('product_stat' => 'VISIBLE','sort' => 'product_name','flow' => 'asc');
			$config["base_url"] = base_url()."products/page";
			$config["total_rows"] = $this->product_model->all_products_tot_rows('','','',$this->acc,$sort_array);
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

			$config['anchor_class'] = 'class="loading_modal"';
			$this->pagination->initialize($config);
			
			$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
			$data["results"] = $this->product_model->all_products_page_limit('',$config["per_page"], $page ,$this->acc,$sort_array);
			$data["links"] = $this->pagination->create_links();
			$data['prd_category_combo'] = $this->product_model->prd_category_combo($this->acc);
			$data['brand_combo'] = $this->brand_and_tag_model->brand_combo($this->acc);
			list($visible,$hidden) = $this->product_model->product_status_count($this->acc);
			$data['delete_hidden'] = '';
			$data['status_combo'] = array('VISIBLE' => 'Visible products ('.$visible.')', 'HIDDEN' => 'Hidden Products ('.$hidden.')');
			$data['suppliers'] = $this->supplier_model->M_get_supplier($this->acc);
			$data['tot_prd_count'] = $config["total_rows"];
			$data['page_prd_count'] = $config["total_rows"] > $config["per_page"] ? ($page+1)." - ".(count($data["results"])+$page) : (count($data["results"])+$page);

			//header
			$header['view']['title'] = 'Products';
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
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			if($this->session->flashdata('form_success')) {
				$data['form_success'] = $this->session->flashdata('form_success');
			}
			$settings = $this->account_model->current_plan_status($this->acc);
			$data['timezone'] = $settings['timezone']; 
			$this->load->view('products/handle',$data);
			
			//footer
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
			$footer['foot']['script'][1] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables/media/js/jquery.dataTables.min.js"></script>'."\n";
			$footer['foot']['script'][2] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>'."\n";
			$footer['foot']['script'][3] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables/media/js/dataTables.fixedColumns.min.js"></script>'."\n";
			$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/handle.js').'"></script>'."\n";
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
			$get['search_name'] = $this->db->escape_like_str($this->input->get('search_product'));
			$get['product_cat'] = $this->db->escape_like_str($this->input->get('product_cat'));
			$get['product_brand'] = $this->db->escape_like_str($this->input->get('product_brand'));
			$get['supplier_id'] = $this->db->escape_like_str($this->input->get('supplier'));
			$get['tag_id'] = $this->input->get('tag_id');
			$get['product_stat'] = $this->input->get('product_stat');
			$get['sort'] = $this->input->get('sort');
			$get['flow'] = $this->input->get('flow');

			$tot_rows = $this->product_model->all_products_tot_rows($get['search_name'],'','',$this->acc,$get);
			$tot_rows = !is_null($tot_rows) ? $tot_rows : 0;
			$config['page_query_string'] = TRUE;
			$config["total_rows"] = $tot_rows;
			$config["per_page"] = 50;
			$config['base_url'] = base_url()."products/lookup".'?'.http_build_query($_GET);
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
			$config['anchor_class'] = 'class="loading_modal"';
			$this->pagination->initialize($config);
		
			//header
			$header['view']['title'] = 'Products';
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
			$page = $this->input->get("per_page") ? $this->input->get("per_page") : 0;
			$data["results"] = $this->product_model->all_products_page_limit($get['search_name'],$config["per_page"], $page ,$this->acc,$get);
			$data["links"] = $this->pagination->create_links();
			$data['prd_category_combo'] = $this->product_model->prd_category_combo($this->acc);
			$data['brand_combo'] = $this->brand_and_tag_model->brand_combo($this->acc);
			list($visible,$hidden) = $this->product_model->product_status_count($this->acc);
			$data['delete_hidden'] = $hidden > 0 ? anchor('products/delete_hidden','<i class="fa fa-trash-o"></i> Delete hidden products','class="" data-confirm="Sure... Delete all hidden products? This cant be restored..."') : '';
			$data['status_combo'] = array('VISIBLE' => 'Visible products ('.$visible.')', 'HIDDEN' => 'Hidden Products ('.$hidden.')');
			$data['suppliers'] = $this->supplier_model->M_get_supplier($this->acc);
			$data['tot_prd_count'] = $config["total_rows"];
			$data['page_prd_count'] = $config["total_rows"] > $config["per_page"] ? ($page+1)." - ".(count($data["results"])+$page) : (count($data["results"])+$page);
			$settings = $this->account_model->current_plan_status($this->acc);
			$data['timezone'] = $settings['timezone']; 
			$this->load->view('products/handle',$data);

			//footer
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
			$footer['foot']['script'][1] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables/media/js/jquery.dataTables.min.js"></script>'."\n";
			$footer['foot']['script'][2] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>'."\n";
			$footer['foot']['script'][3] =  '<script src="'.base_url(POS_CSS_ROOT).'/repository/datatables/media/js/dataTables.fixedColumns.min.js"></script>'."\n";
			$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/handle.js').'"></script>'."\n";
			$footer['foot']['script'][5] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer);			
		}
	}
	public function update_barcode_prefix()
	{
		$this->load->view('session/pos_session');
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$this->load->view('products/update_barcode_prefix');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			echo '<h3>Session Expired! Please <a href="'.base_url().'">Re-login</a> again</h3>';
		}
	}
	public function create_category()
	{
		$this->load->view('session/pos_session');
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$this->load->view('products/create_category');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			echo '<h3>Session Expired! Please <a href="'.base_url().'products/add_product">Re-login</a> again</h3>';
		}
	}
	public function create_supplier()
	{
		$this->load->view('session/pos_session');
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$this->load->view('products/create_supplier');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			echo '<h3>Session Expired! Please <a href="'.base_url().'products/add_product">Re-login</a> again</h3>';
		}
	}
	public function create_tax()
	{
		$this->load->view('session/pos_session');
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$this->load->view('products/create_tax');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			echo '<h3>Session Expired! Please <a href="'.base_url().'products/add_product">Re-login</a> again</h3>';
		}
	}

	public function create_brand()
	{
		$this->load->view('session/pos_session');
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$this->load->view('products/create_brand');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			echo '<h3>Session Expired! Please <a href="'.base_url().'products/add_product">Re-login</a> again</h3>';
		}
	}
	public function category_activity()
	{
		$data = array();
		$data['cat_name'] = $this->input->post('cat_name');	
		$data['acc'] = $this->acc;
		$response = $this->product_model->insert_ajax_cat($data);
		echo json_encode($response);
	}
	public function supplier_activity()
	{
		$data = array();
		$data['supp_name'] = $this->input->post('supp_name');	
		$data['acc'] = $this->acc;
		$response = $this->product_model->insert_ajax_supplier($data);
		echo json_encode($response);
	}
	public function brand_activity()
	{
		$data = array();
		$data['brand_name'] = $this->input->post('brand_name');	
		$data['acc'] = $this->acc;
		$response = $this->product_model->insert_ajax_brand($data);
		echo json_encode($response);
	}
	public function tax_activity()
	{
		$data = array();
		$data['tax_name'] = $this->input->post('tax_name');	
		$data['tax_val'] = $this->input->post('tax_val');	
		$data['acc'] = $this->acc;
		$response = $this->product_model->insert_ajax_tax($data);
		echo json_encode($response);
	}
	public function change_barcode_prefix()
	{
		$this->load->view('session/pos_session');
		$prefix = $this->input->post('prefix_string');	
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Barcode prefix successfully updated!', 
						2 => 'Some of your existing products SKU already has that prefix. Try another.', 
						);
		$response = $this->product_model->change_barcode_prefix($prefix,$this->acc);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('products'));
	
	}
	public function add_product()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				//header
				$header['view']['title'] = 'Add product';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
				$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/autocomplete/autocomplete.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
	
				//body
				$data = array();
				$data['scale_list'] = $this->product_model->M_get_product_scale_assoc();
				$data['options'] = $this->product_model->M_product_scale_drop();
				$data['suppliers'] = $this->supplier_model->M_get_supplier($this->acc);
				$data['var_types'] = $this->variant_model->M_get_variants($this->acc);
				$data['def_locale_tax'] = $this->taxes_model->get_all_outlet_taxes($this->acc);
				$data['single_group_taxes_combo'] = $this->taxes_model->get_single_group_taxes_combo($this->acc);
				$data['auto_sku'] = $this->product_model->generate_incremented_sku($this->acc);
				$data['max_variants'] = $this->max_variants;
				
				$data['prd_category_combo'] = $this->product_model->prd_category_combo($this->acc);
				$data['brand_combo'] = $this->brand_and_tag_model->brand_combo($this->acc);
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$this->load->view('products/add_product',$data);
				
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
				$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'tinymce/tinymce.min.js').'"></script>'."\n";
				$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
				$footer['foot']['script'][5] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/add_product.js').'"></script>'."\n";
				$footer['foot']['script'][6] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/barcode/jquery.scannerdetection.compatibility.js').'"></script>'."\n";
				$footer['foot']['script'][7] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/barcode/jquery.scannerdetection.js').'"></script>'."\n";
				$footer['foot']['script'][8] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function insert_tag()
	{
		$tag_name = $this->input->post('tag_name');
		$prd_id = $this->input->post('prd_id');
		$resp = $this->brand_and_tag_model->insert_tag($tag_name,$prd_id,$this->acc);
		echo json_encode($resp);
	}
	public function insert_prd_tag()
	{
		$tag_id = $this->input->post('tag_id');
		$prd_id = $this->input->post('prd_id');
		$resp = $this->brand_and_tag_model->insert_product_tag($tag_id,$prd_id,$this->acc);
		echo $resp;
	}
	public function insert_product()
	{
		$this->load->view('session/pos_session');
		$scale = $this->input->post('new_p_scale');
		switch($scale){
			case 'NUM':
				$this->insert_num();
			break;
			case 'KILO':
				$this->insert_kilo();
			break;
			case 'VARIANTS':
				$this->insert_variant();
			break;
			case 'BLEND':
				$this->insert_blend();
			break;
		}
	}
	public function insert_num()
	{
		$this->load->view('session/pos_session');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('p_name', 'Product name', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('p_handle', 'Handle', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('sku', 'SKU', 'required|alpha_numeric|max_length[50]|xss_clean');
		$this->form_validation->set_rules('price', 'price', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('margin', 'margin', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('retail', 'retail price', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('product_cat', 'product category', 'xss_clean');
		$this->form_validation->set_rules('product_brand', 'product brand', 'xss_clean');
		$this->form_validation->set_rules('new_supplier', 'product supplier', 'xss_clean');
		$this->form_validation->set_rules('new_p_scale', 'product scale', 'xss_clean');
		$this->form_validation->set_rules('prd_wh_id', 'wearhouse id', 'xss_clean');
		$this->form_validation->set_rules('prd_pur_id', 'purchase id', 'xss_clean');
		$this->form_validation->set_rules('prd_weight', 'product weight', 'xss_clean');
		$cur_stk = $data['cur_stk'] = $this->input->post('cur_stk');
		$reorder_stk = $data['reorder_stk'] = $this->input->post('reorder_stk');
		$reorder_qty = $data['reorder_qty'] = $this->input->post('reorder_qty');
		foreach($cur_stk as $key => $value)
		{
			$this->form_validation->set_rules('cur_stk['.$key.']', $value, 'xss_clean');
		}
		foreach($reorder_stk as $key => $value)
		{
			$this->form_validation->set_rules('reorder_stk['.$key.']', $value, 'xss_clean');
		}
		foreach($reorder_qty as $key => $value)
		{
			$this->form_validation->set_rules('reorder_qty['.$key.']', $value, 'xss_clean');
		}
		
		if($this->form_validation->run() == FALSE)
		{
			$this->add_product();
		} else {
			$data['product_id'] = $this->taxes_model->make_single_uuid();
			$data['p_name'] = $this->input->post('p_name');
			$data['p_handle'] = $this->input->post('p_handle');
			$data['visib_stat'] = $this->input->post('visib_stat');
			$data['new_desc'] = $this->input->post('new_desc');
			$data['product_cat'] = $this->input->post('product_cat') == '' ? NULL : $this->input->post('product_cat');
			$data['product_brand'] = $this->input->post('product_brand') == '' ? NULL : $this->input->post('product_brand');
			$data['tag_id'] = $this->input->post('tag_id') ? $this->input->post('tag_id') : array();
			
			
			$data['new_supplier'] = $this->input->post('new_supplier') == '' ? NULL : $this->input->post('new_supplier');
			$data['prd_weight'] = $this->input->post('prd_weight') == '' ? 0 : $this->input->post('prd_weight');;
			$data['sku'] = $this->input->post('sku');
			$data['new_p_scale'] = $this->input->post('new_p_scale');
			$data['price'] = $this->input->post('price');
			$data['margin'] = $this->input->post('margin');
			$data['retail'] = $this->input->post('retail');
			$data['def_location'] = $this->input->post('def_location'); //array outlet id
			$data['sale_tax'] = $this->input->post('qty_scale_tax'); // array may be empty or defined 
			$data['loyalty_stat'] = $this->input->post('loyalty_stat'); // if setto 1 then default else loyalty_cust_val val
			$data['loyalty_def_val'] = $this->input->post('loyalty_def_val'); 
			$data['loyalty_cust_val'] = $this->input->post('loyalty_cust_val');
			$data['loyalty_val'] = $data['loyalty_stat'] ? $data['loyalty_def_val'] : $data['loyalty_cust_val'];
			$data['trace_inv'] = $this->input->post('trace_inv') == 30 ? 30 : 40; // if checked then only insert inventory
			$data['inv_outlet'] = $this->input->post('inv_outlet'); //array
			$data['cur_stk'] = $this->input->post('cur_stk'); //array
			$data['reorder_stk'] = $this->input->post('reorder_stk'); ///array
			$data['reorder_qty'] = $this->input->post('reorder_qty'); ///array
			$data['show_cart'] = $this->input->post('show_cart') == 30 ? 30 : 40;
			$data['prd_wh_id'] = $this->input->post('prd_wh_id');
			$data['prd_pur_id'] = $this->input->post('prd_pur_id');
			$data['ship_stat'] = $this->input->post('ship_stat') == 30 ? 30 : 40;
			$data['user_id'] = $this->user_id;
			$data['acc'] = $this->acc;
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Product successfully created. '.anchor(base_url().'products/add_product','Add another product','class="btn btn-xs btn-primary"'), 
						2 => 'Product creation failed - SKU cant start with weighing scale barcode prefix. Please try another.', 
						3 => 'Product creation failed: SKU has already been used for some other product. Please try another.',
						4 => 'Product creation failed: You have exceeded maximum product limit, please '.anchor('account','upgrade','class="btn btn-sm btn-primary"').' your account to add more products.'
						);
			$response = $this->product_form_model->insert_num_product($data);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			$redirect = ($response == 1) ? 'products/'.$data['product_id'] : 'products/add_product';
			redirect(base_url().$redirect);						
		}
	}
	public function insert_kilo()
	{
		$this->load->view('session/pos_session');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('p_name', 'Product name', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('p_handle', 'Handle', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('sku', 'SKU', 'required|alpha_numeric|max_length[50]|xss_clean');
		$this->form_validation->set_rules('price', 'price', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('margin', 'margin', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('retail', 'retail price', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('product_cat', 'product category', 'xss_clean');
		$this->form_validation->set_rules('product_brand', 'product brand', 'xss_clean');
		$this->form_validation->set_rules('new_supplier', 'product supplier', 'xss_clean');
		$this->form_validation->set_rules('new_p_scale', 'product scale', 'xss_clean');
		$this->form_validation->set_rules('prd_wh_id', 'wearhouse id', 'xss_clean');
		$this->form_validation->set_rules('prd_pur_id', 'purchase id', 'xss_clean');
		$this->form_validation->set_rules('prd_weight', 'product weight', 'xss_clean');
		$cur_stk = $data['cur_stk'] = $this->input->post('cur_stk');
		$reorder_stk = $data['reorder_stk'] = $this->input->post('reorder_stk');
		$reorder_qty = $data['reorder_qty'] = $this->input->post('reorder_qty');
		foreach($cur_stk as $key => $value)
		{
			$this->form_validation->set_rules('cur_stk['.$key.']', $value, 'xss_clean');
		}
		foreach($reorder_stk as $key => $value)
		{
			$this->form_validation->set_rules('reorder_stk['.$key.']', $value, 'xss_clean');
		}
		foreach($reorder_qty as $key => $value)
		{
			$this->form_validation->set_rules('reorder_qty['.$key.']', $value, 'xss_clean');
		}
		if($this->form_validation->run() == FALSE)
		{
			$this->add_product();
		} else {
			$data['product_id'] = $this->taxes_model->make_single_uuid();
			$data['p_name'] = $this->input->post('p_name');
			$data['p_handle'] = $this->input->post('p_handle');
			$data['visib_stat'] = $this->input->post('visib_stat');
			$data['new_desc'] = $this->input->post('new_desc');
			$data['product_cat'] = $this->input->post('product_cat') == '' ? NULL : $this->input->post('product_cat');

			$data['product_cat'] = $this->input->post('product_cat') == '' ? NULL : $this->input->post('product_cat');
			$data['product_brand'] = $this->input->post('product_brand') == '' ? NULL : $this->input->post('product_brand');
			$data['tag_id'] = $this->input->post('tag_id') ? $this->input->post('tag_id') : array();

			$data['new_supplier'] = $this->input->post('new_supplier') == '' ? NULL : $this->input->post('new_supplier');
			$data['prd_weight'] = $this->input->post('prd_weight') == '' ? 0 : $this->input->post('prd_weight');;
			$data['sku'] = $this->input->post('sku');
			$data['new_p_scale'] = $this->input->post('new_p_scale');
			$data['price'] = $this->input->post('price');
			$data['margin'] = $this->input->post('margin');
			$data['retail'] = $this->input->post('retail');
			$data['def_location'] = $this->input->post('def_location'); //array outlet id
			$data['sale_tax'] = $this->input->post('qty_scale_tax'); // array may be empty or defined
			$data['loyalty_stat'] = $this->input->post('loyalty_stat'); // if setto 1 then default else loyalty_cust_val val
			$data['loyalty_def_val'] = $this->input->post('loyalty_def_val');
			$data['loyalty_cust_val'] = $this->input->post('loyalty_cust_val');
			$data['loyalty_val'] = $data['loyalty_stat'] ? $data['loyalty_def_val'] : $data['loyalty_cust_val'];
			$data['trace_inv'] = $this->input->post('trace_inv') == 30 ? 30 : 40; // if checked then only insert inventory
			$data['inv_outlet'] = $this->input->post('inv_outlet'); //array
			$data['cur_stk'] = $this->input->post('cur_stk'); //array
			$data['reorder_stk'] = $this->input->post('reorder_stk'); ///array
			$data['show_cart'] = $this->input->post('show_cart') == 30 ? 30 : 40;;
			$data['user_id'] = $this->user_id;
			$data['prd_wh_id'] = $this->input->post('prd_wh_id');
			$data['prd_pur_id'] = $this->input->post('prd_pur_id');
			$data['ship_stat'] = $this->input->post('ship_stat') == 30 ? 30 : 40;
			$data['acc'] = $this->acc;
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Weighing scale product "'.$data['p_name'].'" successfully created. '.anchor(base_url().'products/add_product','Add another product','class="btn btn-xs btn-primary"'), 
						2 => 'Weighing scale product creation failed - SKU cant start with "KILO" scale barcode prefix. Please try another.', 
						3 => 'Weighing scale Product creation failed: SKU has already been used for some other product. Please try another.',
						4 => 'Weighing scale Product creation failed: You have exceeded maximum product limit, please '.anchor('account','upgrade','class="btn btn-xs btn-primary"').' your account to add more products.',
						5 => 'Error: Weighing scale products are restricted to a maximum of 99,999 count only. Delete some unused kilo scale products and try again.'
						);
			$response = $this->product_form_model->insert_kilo_product($data);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			$redirect = ($response == 1) ? 'products/'.$data['product_id'] : 'products/add_product';
			redirect(base_url().$redirect);						
		}
	}
	public function insert_variant()
	{
		$this->load->view('session/pos_session');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('p_name', 'Product name', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('p_handle', 'Handle', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('p_handle', 'Handle', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('sku', 'SKU', 'required|alpha_numeric|max_length[50]|xss_clean');
		$this->form_validation->set_rules('price', 'price', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('margin', 'margin', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('retail', 'retail price', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('product_cat', 'product category', 'xss_clean');
		$this->form_validation->set_rules('product_brand', 'product brand', 'xss_clean');
		$this->form_validation->set_rules('new_supplier', 'product supplier', 'xss_clean');
		$this->form_validation->set_rules('new_var_method[]', 'variant option value', 'required|xss_clean');
		$this->form_validation->set_rules('var_type_name[]', 'variant option dropdown', 'required|xss_clean');
		$this->form_validation->set_rules('new_p_scale', 'product scale', 'xss_clean');
		$this->form_validation->set_rules('prd_wh_id', 'wearhouse id', 'xss_clean');
		$this->form_validation->set_rules('prd_pur_id', 'purchase id', 'xss_clean');
		$this->form_validation->set_rules('prd_weight', 'product weight', 'xss_clean');
		$cur_stk = $data['cur_stk'] = $this->input->post('cur_stk');
		$reorder_stk = $data['reorder_stk'] = $this->input->post('reorder_stk');
		$reorder_qty = $data['reorder_qty'] = $this->input->post('reorder_qty');
		foreach($cur_stk as $key => $value)
		{
			$this->form_validation->set_rules('cur_stk['.$key.']', $value, 'xss_clean');
		}
		foreach($reorder_stk as $key => $value)
		{
			$this->form_validation->set_rules('reorder_stk['.$key.']', $value, 'xss_clean');
		}
		foreach($reorder_qty as $key => $value)
		{
			$this->form_validation->set_rules('reorder_qty['.$key.']', $value, 'xss_clean');
		}
		if($this->form_validation->run() == FALSE)
		{
			$this->add_product();
		} else {
			$data['variant_id'] = $this->taxes_model->make_single_uuid();
			$data['parent_product_id'] = $this->taxes_model->make_single_uuid();
			$data['var_type_name'] = $this->input->post('var_type_name'); // array
			$data['new_var_method'] = $this->input->post('new_var_method'); // array
			$data['p_name'] = $this->input->post('p_name');
			$data['p_handle'] = $this->input->post('p_handle');
			$data['visib_stat'] = $this->input->post('visib_stat');
			$data['new_desc'] = $this->input->post('new_desc');

			$data['product_cat'] = $this->input->post('product_cat') == '' ? NULL : $this->input->post('product_cat');
			$data['product_brand'] = $this->input->post('product_brand') == '' ? NULL : $this->input->post('product_brand');
			$data['tag_id'] = $this->input->post('tag_id') ? $this->input->post('tag_id') : array();
			$data['new_supplier'] = $this->input->post('new_supplier') == '' ? NULL : $this->input->post('new_supplier');

			$data['prd_weight'] = $this->input->post('prd_weight') == '' ? 0 : $this->input->post('prd_weight');;
			$data['sku'] = $this->input->post('sku');
			$data['new_p_scale'] = $this->input->post('new_p_scale');
			$data['price'] = $this->input->post('price');
			$data['margin'] = $this->input->post('margin');
			$data['retail'] = $this->input->post('retail');
			$data['def_location'] = $this->input->post('def_location'); //array outlet id
			$data['sale_tax'] = $this->input->post('qty_scale_tax'); // array may be empty or defined
			$data['loyalty_stat'] = $this->input->post('loyalty_stat'); // if setto 1 then default else loyalty_cust_val val
			$data['loyalty_def_val'] = $this->input->post('loyalty_def_val');
			$data['loyalty_cust_val'] = $this->input->post('loyalty_cust_val');
			$data['loyalty_val'] = $data['loyalty_stat'] ? $data['loyalty_def_val'] : $data['loyalty_cust_val'];
			$data['trace_inv'] = $this->input->post('trace_inv') == 30 ? 30 : 40; // if checked then only insert inventory
			$data['inv_outlet'] = $this->input->post('inv_outlet'); //array
			$data['cur_stk'] = $this->input->post('cur_stk'); //array
			$data['reorder_stk'] = $this->input->post('reorder_stk'); ///array
			$data['prd_wh_id'] = $this->input->post('prd_wh_id');
			$data['prd_pur_id'] = $this->input->post('prd_pur_id');
			$data['ship_stat'] = $this->input->post('ship_stat') == 30 ? 30 : 40;			
			$data['show_cart'] = $this->input->post('show_cart') == 30 ? 30 : 40;;
			$data['user_id'] = $this->user_id;
			$data['max_variants'] = $this->max_variants;
			$data['acc'] = $this->acc;
			
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Variant Product "'.$data['p_name'].'" successfully created. '.anchor(base_url().'products/add_product','Add another product','class="btn btn-xs btn-primary"'), 
						2 => 'Variant Product creation failed - SKU cant start with weighing scale barcode prefix. Please try another.', 
						3 => 'Variant Product creation failed: SKU has already been used for some other product. Please try another.',
						4 => 'Variant Product creation failed: You have exceeded maximum product limit, please '.anchor('account','upgrade','class="btn btn-xs btn-primary"').' your account to add more products.'
						);
			$response = $this->product_form_model->insert_variant_product($data);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			$redirect = ($response == 1) ? 'products/'.$data['parent_product_id'] : 'products/add_product';
			redirect(base_url().$redirect);						
		}
	}
	public function temp_barcode($sku)
	{
		$this->load->view('session/pos_session');
		$this->load->library('zend');
		$this->zend->load('Zend/Barcode');
		$rendererOptions = array();
		if(is_numeric($sku) && strlen($sku) == 13) 
		{ 
			$type = 'ean13' ; 
			$sku = substr($sku, 0, -1) ;
		} else if(is_numeric($sku) && strlen($sku) == 12) {
			$type = 'ean13' ; 
		} else if(!is_numeric($sku) && strtoupper($sku) == $sku){
			$type = 'code39'; 			
		} else if(strtoupper($sku) != $sku){
			$type = 'code128';
		} else {
			$type = 'code128';	
		}
		$barcodeOptions = array('text' => $sku,'barHeight' => 20);
		Zend_Barcode::render($type, 'image', $barcodeOptions, $rendererOptions);
	}	
	public function draw_jumbo_barcode($sku) 
	{
		$this->load->view('session/pos_session');
		$this->load->library('zend');
		$this->zend->load('Zend/Barcode');
		$rendererOptions = array();
		if(is_numeric($sku) && strlen($sku) == 13) 
		{ 
			$type = 'ean13' ; 
			$sku = substr($sku, 0, -1) ;
		} else if(is_numeric($sku) && strlen($sku) == 12) {
			$type = 'ean13' ; 
		} else if(!is_numeric($sku) && strtoupper($sku) == $sku){
			$type = 'code39'; 			
		} else if(strtoupper($sku) != $sku){
			$type = 'code128';
		} else {
			$type = 'code128';	
		}
		$barcodeOptions = array('text' => $sku,'barHeight' => 30,'factor'=> 3);
		Zend_Barcode::render($type, 'image', $barcodeOptions, $rendererOptions);
	}	
	public function make_barcode($product_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$check = $this->product_model->check_product($product_id,$this->acc);
			if(!is_null($check))
			{
				// error after this
				$this->load->model('barcode/barcode_model');
				switch ($check['type']) {
					case 'NUM':
						$data = $this->product_model->get_num_product_details($product_id,$this->acc);	
						$data['product_id'] = $product_id; 
						$data['pos_id'] = ''; 
						$data['sample'] = 'Retail Price - '.number_format($data['retail_price'],2);				
						$data['sku_caption'] = "";
						$data['barcode_prefix'] = '';
						$data['variant_name'] = '';
					break;
					case 'KILO':
						$data = $this->product_model->get_kilo_product_details($product_id,$this->acc);
						$data['product_id'] = $product_id; 
						$data['sku_caption'] = '*Min 1 digit notation of 2 decimal places i.e, 0.01 or Max 3 digit notation of 2 decimal places i.e, 999.99';
						$data_item = $this->barcode_model->pad_embed_string($data['pos_id'],5);
						$rand_var = number_format(rand(1*10, 20*10) / 10 , 2);
						$variant_item = $this->barcode_model->make_barcode_scale($rand_var);
						$data['barcode_prefix'] = $this->product_model->get_barcode_prefix($this->acc);
						$data_code = $data['barcode_prefix'].$data_item.$variant_item;
						$data['sku'] = $this->barcode_model->checksum($data_code);
						$data['sample'] = 'Example: Sell '.$data['product_name'].' '.$rand_var.' KILO(s) @ cost of '.number_format($data['retail_price'],2).'/<sub>Kilo</sub>';
						$data['variant_name'] = '';
					break;
					case 'VARIANTS':
						$data = $this->product_model->get_unique_variant_details($product_id,$this->acc);		
						$data['sku_caption'] = '';	
						$data['barcode_prefix'] = '';		
						$data['pos_id'] = ''; 
						$data['sample'] = $data['product_name'].' / '.$data['variant_name'].' | '.number_format($data['retail_price'],2).' '.$this->session->userdata('currency');
					break;
					case 'BLEND':
						$data = $this->product_model->get_blend_product_details($product_id,$this->acc);
						$data['product_id'] = $data['main_product_id'];
						$data['sku_caption'] = '';	
						$data['barcode_prefix'] = '';		
						$data['variant_name'] = '';
						$data['pos_id'] = '';
						$data['sample'] = number_format($data['retail_price'],2).' '.$this->session->userdata('currency'); 
					break;
					default:
					$this->load->view('site_404/url_404'); 			
				}
				$data['scale_str'] =  array(1 => 'Standard product',2 => 'Weighing scale product',3 => 'Product having variants',4 => 'Group products');
				$data['printer_type'] = array(1 => 'Barcode Printer', 2 => 'Print in Bulk ');
				$data['fit_page'] = array(
								1 => 'Level 1',
								2 => 'Level 2',
								3 => 'Level 3',
								4 => 'Level 4',
								5 => 'Level 5',
								6 => 'Level 6',
								7 => 'Level 7',
								8 => 'Level 8',
								9 => 'Level 9',
								10 => 'Level 10',
								11 => 'Level 11',
								12 => 'Level 12',
								13 => 'Level 13',
								14 => 'Level 14',
								15 => 'Level 15'
							);	
				$data['bcode_array'] = $this->barcode_model->barcode_types();
				
				//header
				$header['view']['title'] = 'Print barcode';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$data['company'] = $this->user_model->get_locations($this->acc);
				$this->load->view('products/make_barcode',$data);

				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
				$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/make_barcode.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('site_404/url_404'); 			
			}
		}
	}
	public function blend_autocomplete()
	{
		$this->load->view('session/pos_session');
		$term = $this->input->get('term',TRUE);
		$rows = $this->product_model->blend_GetAutocomplete(array('keyword' => $term),$this->acc);
		$json_array = array();
		foreach ($rows as $row)
	    	$json_array[] = array('indexed' => $row->indexed, 'prod_name' => $row->prod_name);
		echo json_encode($json_array);
	}	
	public function tag_autocomplete()
	{
		$this->load->view('session/pos_session');
		$term = $this->input->get('term',TRUE);
		$rows = $this->brand_and_tag_model->tag_GetAutocomplete(array('keyword' => $term),$this->acc);
		$json_array = array();
		foreach ($rows as $row)
	    	$json_array[] = array('tag_id' => $row->tag_id, 'tag_name' => $row->tag_name);
		echo json_encode($json_array);
	}	
	public function insert_blend()
	{
		$this->load->view('session/pos_session');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('p_name', 'Product name', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('p_handle', 'Handle', 'trim|required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('sku', 'SKU', 'required|alpha_numeric|max_length[50]|xss_clean');
		$this->form_validation->set_rules('price', 'price', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('margin', 'margin', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('retail', 'retail price', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('product_cat', 'product category', 'xss_clean');
		$this->form_validation->set_rules('product_brand', 'product brand', 'xss_clean');
		$this->form_validation->set_rules('new_supplier', 'product supplier', 'xss_clean');
		$this->form_validation->set_rules('new_p_scale', 'product scale', 'xss_clean');
		$this->form_validation->set_rules('prd_wh_id', 'wearhouse id', 'xss_clean');
		$this->form_validation->set_rules('prd_pur_id', 'purchase id', 'xss_clean');
		$this->form_validation->set_rules('prd_weight', 'product weight', 'xss_clean');
		$this->form_validation->set_rules('blend_prd_qty[]', 'group product quantity', 'required|numeric|xss_clean');
		if($this->form_validation->run() == FALSE)
		{
			$this->add_product();
		} else {
			$data['product_id'] = $this->taxes_model->make_single_uuid();
			$data['blend_prd_qty'] = $this->input->post('blend_prd_qty'); // array
			$data['blend_product_id'] = $this->input->post('blend_product_id'); // array
			$data['p_name'] = $this->input->post('p_name');
			$data['p_handle'] = $this->input->post('p_handle');
			$data['visib_stat'] = $this->input->post('visib_stat');
			$data['new_desc'] = $this->input->post('new_desc');

			$data['product_cat'] = $this->input->post('product_cat') == '' ? NULL : $this->input->post('product_cat');
			$data['product_brand'] = $this->input->post('product_brand') == '' ? NULL : $this->input->post('product_brand');
			$data['tag_id'] = $this->input->post('tag_id') ? $this->input->post('tag_id') : array();
			$data['new_supplier'] = $this->input->post('new_supplier') == '' ? NULL : $this->input->post('new_supplier');

			$data['prd_weight'] = $this->input->post('prd_weight') == '' ? 0 : $this->input->post('prd_weight');;
			$data['sku'] = $this->input->post('sku');
			$data['new_p_scale'] = $this->input->post('new_p_scale');
			$data['price'] = $this->input->post('price');
			$data['margin'] = $this->input->post('margin');
			$data['retail'] = $this->input->post('retail');
			$data['def_location'] = $this->input->post('def_location'); //array outlet id
			$data['sale_tax'] = $this->input->post('qty_scale_tax'); // array may be empty or defined
			$data['loyalty_stat'] = $this->input->post('loyalty_stat'); // if setto 1 then default else loyalty_cust_val val
			$data['loyalty_def_val'] = $this->input->post('loyalty_def_val');
			$data['loyalty_cust_val'] = $this->input->post('loyalty_cust_val');
			$data['loyalty_val'] = $data['loyalty_stat'] ? $data['loyalty_def_val'] : $data['loyalty_cust_val'];
			$data['trace_inv'] = $this->input->post('trace_inv') == 30 ? 30 : 40; // if checked then only insert inventory
			$data['prd_wh_id'] = $this->input->post('prd_wh_id');
			$data['prd_pur_id'] = $this->input->post('prd_pur_id');
			$data['ship_stat'] = $this->input->post('ship_stat') == 30 ? 30 : 40;
			$data['inv_outlet'] = $this->input->post('inv_outlet'); //array
			$data['show_cart'] = $this->input->post('show_cart') == 30 ? 30 : 40;;
			$data['user_id'] = $this->user_id;
			$data['acc'] = $this->acc;
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Product group "'.$data['p_name'].'" successfully created. '.anchor(base_url().'products/add_product','Add another product','class="btn btn-xs btn-primary"'), 
						2 => 'Product group creation failed: SKU cant start with "KILO" scale barcode prefix. Please try another.', 
						3 => 'Product group creation failed: SKU has already been used for some other product. Please try another.',
						4 => 'Product group creation failed: You have exceeded maximum product limit, please '.anchor('account','upgrade','class="btn btn-xs btn-primary"').' to add more products.'
						);
			$response = $this->product_form_model->insert_blend_product($data);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			$redirect = ($response == 1) ? 'products/'.$data['product_id'] : 'products/add_product';
			redirect(base_url().$redirect);						
		}
	}
	public function edit($product_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				$data = array();
				$data['check'] = $this->product_model->check_product($product_id,$this->acc);
				$data['scale_list'] = $this->product_model->M_get_product_scale_assoc();
				$data['options'] = $this->product_model->M_product_scale_drop();
				$data['suppliers'] = $this->supplier_model->M_get_supplier($this->acc);
				$data['def_locale_tax'] = $this->taxes_model->get_all_outlet_taxes($this->acc);
				$data['single_group_taxes_combo'] = $this->taxes_model->get_single_group_taxes_combo($this->acc);
				$data['prd_category_combo'] = $this->product_model->prd_category_combo($this->acc);
				$data['brand_combo'] = $this->brand_and_tag_model->brand_combo($this->acc);				
				if(!is_null($data['check']))
				{
					$product_scale = $this->product_model->check_scale($product_id,$this->acc);
					switch ($product_scale) {
						case 1:
							$data['product_taxes'] = $this->product_model->product_taxes($product_id,$this->acc);
							$data['details'] = $this->product_model->get_num_product_details($product_id,$this->acc);
							$data['tags'] = $this->brand_and_tag_model->get_prd_tags($product_id,$this->acc);
							$data['caption'] = 'Standard Product';	
							$data['details']['product_scale'] = "NUM";
							$data['product_id'] = $product_id;
						break;
						case 2:
							$data['product_taxes'] = $this->product_model->product_taxes($product_id,$this->acc);
							$data['details'] = $this->product_model->get_kilo_product_details($product_id,$this->acc);
							$data['tags'] = $this->brand_and_tag_model->get_prd_tags($product_id,$this->acc);
							$data['caption'] = 'Weighing scale product';	
							$data['details']['product_scale'] = "KILO";
							$data['product_id'] = $product_id;
						break;
						case 3:
							$data['product_taxes'] = $this->product_model->variant_taxes($product_id,$this->acc);
							$data['details'] = $this->product_model->get_variant_product_details($product_id,$this->acc);
							$data['var_types'] = $this->variant_model->M_get_variants($this->acc);
							$data['variant_dropdown'] = $this->variant_model->variant_dropdown($this->acc);
							$data['tags'] = $this->brand_and_tag_model->get_prd_tags($data['details']['main_product_id'],$this->acc);
							$data['caption'] = 'Product having variants';	
							$data['details']['product_scale'] = "VARIANTS";
							$data['product_id'] = $product_id;
						break;
						case 4:
							$data['product_taxes'] = $this->product_model->product_taxes($product_id,$this->acc);
							$data['details'] = $this->product_model->get_blend_product_details($product_id,$this->acc);
							$data['blend_prds'] = $this->product_model->get_blend_sub_products($product_id,$this->acc);
							$data['tags'] = $this->brand_and_tag_model->get_prd_tags($product_id,$this->acc);
							$data['caption'] = 'Grouped product';	
							$data['details']['product_scale'] = "BLEND";
							$data['product_id'] = $product_id;
						break;
						
					}
					//header
					$header['view']['title'] = 'Edit product';
					$role = $this->roles_model->get_roles($this->privelage);
					list($header['role_code'],$header['role_name']) = $role;
					$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
					$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
					$header['style'][2] = link_tag(POS_CSS_ROOT.'repository/autocomplete/autocomplete.css')."\n";
					$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
					$this->load->view('top_page/top_page',$header);
					
					//body
					$this->load->view('products/edit_product',$data);

					//footer
					$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
					$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
					$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
					$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'tinymce/tinymce.min.js').'"></script>'."\n";
					$footer['foot']['script'][5] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
					$footer['foot']['script'][6] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/add_product.js').'"></script>'."\n";
					$footer['foot']['script'][7] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/barcode/jquery.scannerdetection.compatibility.js').'"></script>'."\n";
					$footer['foot']['script'][8] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/barcode/jquery.scannerdetection.js').'"></script>'."\n";
					$footer['foot']['script'][9] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
					$this->load->view('bottom_page/bottom_page',$footer);			
				} else {
					$this->load->view('site_404/url_404'); 			
				}
			} else {
				$this->load->view('noaccess/noaccess');				
			}
		}
	}
	public function update($product_id)
	{
		$this->load->view('session/pos_session');
		$scale = $this->product_model->check_scale($product_id,$this->acc);
		$data = $this->input->post();
		$data['scale'] = $scale;
		$data['product_id'] = $product_id;
		
		$data['product_cat'] = $this->input->post('product_cat') == '' ? NULL : $this->input->post('product_cat');
		$data['product_brand'] = $this->input->post('product_brand') == '' ? NULL : $this->input->post('product_brand');
		$data['tag_id'] = $this->input->post('tag_id') ? $this->input->post('tag_id') : array();
			
		$data['loyalty_stat'] = $this->input->post('loyalty_stat'); 
		$data['loyalty_def_val'] = $this->input->post('loyalty_def_val');
		$data['loyalty_cust_val'] = $this->input->post('loyalty_cust_val');
		$data['loyalty_val'] = $data['loyalty_stat'] ? $data['loyalty_def_val'] : $data['loyalty_cust_val'];
		$data['loyalty_val'] = $data['loyalty_stat'] == 30 ? $data['loyalty_def_val'] : $data['loyalty_cust_val'];
		$data['sale_tax'] = $this->input->post('qty_scale_tax'); // array may be empty or defined 
		$data['new_supplier'] = $this->input->post('new_supplier') == '' ? NULL : $this->input->post('new_supplier');
		$data['prd_weight'] = $this->input->post('prd_weight') == '' ? 0 : $this->input->post('prd_weight');;
		$data['trace_inv'] = $this->input->post('trace_inv') ? 30 : 40;
		$data['ship_stat'] = $this->input->post('ship_stat') ? 30 : 40;
		$data['show_cart'] = $this->input->post('show_cart') ? 30 : 40;
		$data['user_id'] = $this->user_id;
		$data['acc'] = $this->acc;
		switch($scale){
			case 1:
				$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
				$this->form_validation->set_rules('p_name', 'Product name', 'trim|required|max_length[40]|xss_clean');
				$this->form_validation->set_rules('p_handle', 'Handle', 'trim|required|max_length[40]|xss_clean');
				$this->form_validation->set_rules('sku', 'SKU', 'required|alpha_numeric|max_length[50]|xss_clean');
				$this->form_validation->set_rules('price', 'price', 'required|numeric|xss_clean');
				$this->form_validation->set_rules('margin', 'margin', 'required|numeric|xss_clean');
				$this->form_validation->set_rules('retail', 'retail price', 'required|numeric|xss_clean');
				$this->form_validation->set_rules('product_cat', 'product category', 'xss_clean');
				$this->form_validation->set_rules('product_brand', 'product brand', 'xss_clean');
				$this->form_validation->set_rules('new_supplier', 'product supplier', 'xss_clean');
				$this->form_validation->set_rules('new_p_scale', 'product scale', 'xss_clean');		
				$this->form_validation->set_rules('prd_wh_id', 'wearhouse id', 'xss_clean');
				$this->form_validation->set_rules('prd_pur_id', 'purchase id', 'xss_clean');
				$this->form_validation->set_rules('prd_weight', 'product weight', 'xss_clean');
				
				$cur_stk = $data['cur_stk'] = $this->input->post('cur_stk');
				$reorder_stk = $data['reorder_stk'] = $this->input->post('reorder_stk');
				$reorder_qty = $data['reorder_qty'] = $this->input->post('reorder_qty');
				foreach($cur_stk as $key => $value)
				{
					$this->form_validation->set_rules('cur_stk['.$key.']', $value, 'xss_clean');
				}
				foreach($reorder_stk as $key => $value)
				{
					$this->form_validation->set_rules('reorder_stk['.$key.']', $value, 'xss_clean');
				}
				foreach($reorder_qty as $key => $value)
				{
					$this->form_validation->set_rules('reorder_qty['.$key.']', $value, 'xss_clean');
				}
				if($this->form_validation->run() == FALSE)
				{
					$this->edit($product_id);
				} else {
					$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
								1 => 'Standard product "'.$data['p_name'].'" successfully updated!', 
								2 => 'Standard product updation failed - SKU cant start with weiging scale barcode prefix. Please try another.', 
								3 => 'Standard product updation failed: SKU has already been used for some other product. Please try another.',
								);
					$response = $this->product_form_model->update_num($data);
					$div = ($response == 1) ? 'form_success' : 'form_errors';
					$this->session->set_flashdata($div, $phrase[$response]);
					redirect(base_url().'products/'.$product_id);						
				}
			break;
			case 2:
				$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
				$this->form_validation->set_rules('p_name', 'Product name', 'trim|required|max_length[40]|xss_clean');
				$this->form_validation->set_rules('p_handle', 'Handle', 'trim|required|max_length[40]|xss_clean');
				$this->form_validation->set_rules('sku', 'SKU', 'required|alpha_numeric|max_length[50]|xss_clean');
				$this->form_validation->set_rules('price', 'price', 'required|numeric|xss_clean');
				$this->form_validation->set_rules('margin', 'margin', 'required|numeric|xss_clean');
				$this->form_validation->set_rules('retail', 'retail price', 'required|numeric|xss_clean');
				$this->form_validation->set_rules('product_cat', 'product category', 'xss_clean');
				$this->form_validation->set_rules('product_brand', 'product brand', 'xss_clean');
				$this->form_validation->set_rules('new_supplier', 'product supplier', 'xss_clean');
				$this->form_validation->set_rules('new_p_scale', 'product scale', 'xss_clean');		
				$this->form_validation->set_rules('prd_wh_id', 'wearhouse id', 'xss_clean');
				$this->form_validation->set_rules('prd_pur_id', 'purchase id', 'xss_clean');
				$this->form_validation->set_rules('prd_weight', 'product weight', 'xss_clean');
				$cur_stk = $data['cur_stk'] = $this->input->post('cur_stk');
				$reorder_stk = $data['reorder_stk'] = $this->input->post('reorder_stk');
				$reorder_qty = $data['reorder_qty'] = $this->input->post('reorder_qty');
				foreach($cur_stk as $key => $value)
				{
					$this->form_validation->set_rules('cur_stk['.$key.']', $value, 'xss_clean');
				}
				foreach($reorder_stk as $key => $value)
				{
					$this->form_validation->set_rules('reorder_stk['.$key.']', $value, 'xss_clean');
				}
				foreach($reorder_qty as $key => $value)
				{
					$this->form_validation->set_rules('reorder_qty['.$key.']', $value, 'xss_clean');
				}
				if($this->form_validation->run() == FALSE)
				{
					$this->edit($product_id);
				} else {
					$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
								1 => 'Weighing scale product "'.$data['p_name'].'" successfully updated!', 
								2 => 'Weighing scale product "'.$data['p_name'].'" updation failed - SKU cant start with weighing scale barcode prefix. Please try another.', 
								3 => 'Weighing scale product "'.$data['p_name'].'" updation failed: SKU has already been used for some other product. Please try another.',
								);
					$response = $this->product_form_model->update_kilo($data);
					$div = ($response == 1) ? 'form_success' : 'form_errors';
					$this->session->set_flashdata($div, $phrase[$response]);
					redirect(base_url().'products/'.$product_id);						
				}
			break;
			case 3:
				$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
				$this->form_validation->set_rules('p_name', 'Product name', 'trim|required|max_length[40]|xss_clean');
				$this->form_validation->set_rules('p_handle', 'Handle', 'trim|required|max_length[40]|xss_clean');
				$this->form_validation->set_rules('sku', 'SKU', 'required|alpha_numeric|max_length[50]|xss_clean');
				$this->form_validation->set_rules('price', 'price', 'required|numeric|xss_clean');
				$this->form_validation->set_rules('margin', 'margin', 'required|numeric|xss_clean');
				$this->form_validation->set_rules('retail', 'retail price', 'required|numeric|xss_clean');
				$this->form_validation->set_rules('product_cat', 'product category', 'xss_clean');
				$this->form_validation->set_rules('product_brand', 'product brand', 'xss_clean');
				$this->form_validation->set_rules('new_supplier', 'product supplier', 'xss_clean');
				$this->form_validation->set_rules('new_p_scale', 'product scale', 'xss_clean');		
				$this->form_validation->set_rules('prd_wh_id', 'wearhouse id', 'xss_clean');
				$this->form_validation->set_rules('prd_pur_id', 'purchase id', 'xss_clean');
				$this->form_validation->set_rules('prd_weight', 'product weight', 'xss_clean');
				$this->form_validation->set_rules('new_var_method[]', 'variant option value', 'required|xss_clean');
				$this->form_validation->set_rules('var_type_name[]', 'variant option dropdown', 'required|xss_clean');
				$cur_stk = $data['cur_stk'] = $this->input->post('cur_stk');
				$reorder_stk = $data['reorder_stk'] = $this->input->post('reorder_stk');
				$reorder_qty = $data['reorder_qty'] = $this->input->post('reorder_qty');
				foreach($cur_stk as $key => $value)
				{
					$this->form_validation->set_rules('cur_stk['.$key.']', $value, 'xss_clean');
				}
				foreach($reorder_stk as $key => $value)
				{
					$this->form_validation->set_rules('reorder_stk['.$key.']', $value, 'xss_clean');
				}
				foreach($reorder_qty as $key => $value)
				{
					$this->form_validation->set_rules('reorder_qty['.$key.']', $value, 'xss_clean');
				}
				$data['max_variants'] = $this->max_variants;
				if($this->form_validation->run() == FALSE)
				{
					$this->edit($product_id);
				} else {
					$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
								1 => 'Variant product "'.$data['p_name'].'" successfully updated!', 
								2 => 'Variant product "'.$data['p_name'].'" updation failed - SKU cant start with weighing scale barcode prefix. Please try another.', 
								3 => 'Variant product "'.$data['p_name'].'" updation failed: SKU has already been used for some other product. Please try another.',
								);
					$response = $this->product_form_model->update_variant($data);
					$div = ($response == 1) ? 'form_success' : 'form_errors';
					$this->session->set_flashdata($div, $phrase[$response]);
					redirect(base_url().'products/'.$data['main_product_id']);						
				}
			break;
			case 4:
				$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
				$this->form_validation->set_rules('p_name', 'Product name', 'trim|required|max_length[40]|xss_clean');
				$this->form_validation->set_rules('p_handle', 'Handle', 'trim|required|max_length[40]|xss_clean');
				$this->form_validation->set_rules('sku', 'SKU', 'required|alpha_numeric|max_length[50]|xss_clean');
				$this->form_validation->set_rules('price', 'price', 'required|numeric|xss_clean');
				$this->form_validation->set_rules('margin', 'margin', 'required|numeric|xss_clean');
				$this->form_validation->set_rules('retail', 'retail price', 'required|numeric|xss_clean');
				$this->form_validation->set_rules('product_cat', 'product category', 'xss_clean');
				$this->form_validation->set_rules('product_brand', 'product brand', 'xss_clean');
				$this->form_validation->set_rules('new_supplier', 'product supplier', 'xss_clean');
				$this->form_validation->set_rules('new_p_scale', 'product scale', 'xss_clean');		
				$this->form_validation->set_rules('prd_wh_id', 'wearhouse id', 'xss_clean');
				$this->form_validation->set_rules('prd_pur_id', 'purchase id', 'xss_clean');
				$this->form_validation->set_rules('prd_weight', 'product weight', 'xss_clean');
				$this->form_validation->set_rules('blend_prd_qty[]', 'Group product quantity', 'required|max_length[10]|numeric|xss_clean');
				if($this->form_validation->run() == FALSE)
				{
					$this->edit($product_id);
				} else {
					$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
								1 => 'Group product "'.$data['p_name'].'" successfully updated!', 
								2 => 'Group product "'.$data['p_name'].'" updation failed - SKU cant start with weighing scale barcode prefix. Please try another.', 
								3 => 'Group product "'.$data['p_name'].'" updation failed: SKU has already been used for some other product. Please try another.',
								);
					$response = $this->product_form_model->update_blend($data);
					$div = ($response == 1) ? 'form_success' : 'form_errors';
					$this->session->set_flashdata($div, $phrase[$response]);
					redirect(base_url().'products/'.$product_id);						
				}
			break;
		}
	}
	public function get_sub_variants()
	{
		$this->load->view('session/pos_session');
		$variant_product_id = $this->input->get('variant_product_id');
		$product_stat = $this->input->get('stat');
		$response = $this->product_model->get_sub_variants($variant_product_id,$product_stat,$this->acc);
		echo json_encode($response);
	}
	public function show($product_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$data = array();
			$check = $this->product_model->check_valid_scale($product_id,$this->acc);
			if(!is_null($check))
			{
				$data['product_id'] = $product_id;
				if($this->privelage == 1){
					$company = $this->user_model->get_locations($this->acc);
				} else if($this->privelage == 2 && $this->loc_id == 'ALL OUTLETS'){
					$company = $this->user_model->get_locations($this->acc);
				} else if($this->privelage == 2 && $this->loc_id != 'ALL OUTLETS'){
					$company = $this->user_model->get_mgr_own_locations($this->loc_id, $this->acc);
				} else if($this->privelage == 3){
					$company = $this->user_model->get_mgr_own_locations($this->loc_id, $this->acc);
				}
				$data['company']['By outlet'] = ($this->loc_id == 'ALL OUTLETS') ? array('' => '') + $company : $company;
				$data['log_codes'] = array('' => '') + $this->log_code_model->get_all_log_codes_dropdown();
				if($this->loc_id == 'ALL OUTLETS')
				{
					$data['users']['By User'] = array('' => '') + $this->user_model->all_user_dropdown($this->acc);
				} else {
					$data['users']['By User'] = array($this->user_id => $this->pos_user);
				}
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$get['date_start'] = $this->input->get('date_start') ? mdate('%Y-%m-%d 00:00:00', strtotime($this->input->get('date_start'))) : mdate(date("Y").'-'.date("m").'-01 00:00:00',now());
				$get['date_end'] = $this->input->get('date_end') ? mdate('%Y-%m-%d 23:59:59', strtotime($this->input->get('date_end'))) : mdate(date("Y").'-'.date("m").'-'.days_in_month(date('m'),date('Y')).' 23:59:59',now());				
				$get['outlet'] = $this->input->get('outlet') ? $this->input->get('outlet') : '';
				$get['users'] = $this->input->get('users') ? $this->input->get('users') : '';
				$get['log_code'] = $this->input->get('log_code') ? $this->input->get('log_code') : '';				
	
				$config['page_query_string'] = TRUE;
				$config["per_page"] = 50;
				$config['base_url'] = base_url()."products/".$product_id.'?'.http_build_query($_GET);
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
	
				$config['anchor_class'] = 'class="loading_modal"';
				$page = $this->input->get("per_page") ? $this->input->get("per_page") : 0;
				$scale = $this->product_model->check_valid_scale($product_id,$this->acc);				
				switch($scale){
					case 1.0:
						$tot_rows = $this->log_code_model->get_product_logs($product_id,'','',$get,$this->acc);
						$tot_rows = !is_null($tot_rows) ? $tot_rows : 0;
						$config["total_rows"] = $tot_rows;						
						$data["results"] = $this->log_code_model->get_product_logs($product_id, $page ,$config["per_page"],$get,$this->acc);
						$data['details']['inventory'] = $this->inventory_model->get_num_inv_details($product_id,$this->acc);
						$data['details']['main'] = $this->product_model->get_num_product_details($product_id,$this->acc);	
						$data['details']['caption'] = 'Standard Product';					
						$data['details']['scale'] = ' /<sub>qty</sub>';	
						$data['details']['inv_scale'] = 'No(s)';
						$data['details']['pos_id'] = '';
						break;
					case 2.0:
						$tot_rows = $this->log_code_model->get_product_logs($product_id,'','',$get,$this->acc);
						$tot_rows = !is_null($tot_rows) ? $tot_rows : 0;
						$config["total_rows"] = $tot_rows;						
						$data["results"] = $this->log_code_model->get_product_logs($product_id, $page ,$config["per_page"],$get,$this->acc);
						$data['details']['inventory'] = $this->inventory_model->get_num_inv_details($product_id,$this->acc);	
						$data['details']['main'] = $this->product_model->get_kilo_product_details($product_id,$this->acc);	
						$data['details']['caption'] = 'Weighing scale product';						
						$data['details']['scale'] = ' /<sub>kilo</sub>';
						$data['details']['inv_scale'] = 'Kilo(s)';
						$prefix = $this->product_model->get_barcode_prefix($this->acc);
						$data['details']['pos_id'] = 'Billing code: '.$prefix.str_pad($data['details']['main']['pos_id'], 5, '0', STR_PAD_LEFT).'XXXXX where &times; = weight/length/litre';
						break;
					case 3.0:
						$tot_rows = $this->log_code_model->get_variant_logs($product_id,'','',$get,$this->acc);
						$tot_rows = !is_null($tot_rows) ? $tot_rows : 0;
						$config["total_rows"] = $tot_rows;						
						$data["results"] = $this->log_code_model->get_variant_logs($product_id, $page ,$config["per_page"],$get,$this->acc);
						$data['details']['inventory'] = $this->inventory_model->get_variant_inv_details($product_id,$this->acc);	
						$data['details']['main'] = $this->product_model->get_main_variant_product_details($product_id,$this->acc);	
						$data['details']['caption'] = 'Variant Product';						
						$data['details']['scale'] = ' /<sub>qty</sub>';	
						$data['details']['inv_scale'] = 'No(s)';
						$data['details']['pos_id'] = '';
						break;
					case 3.5:
						$tot_rows = $this->log_code_model->get_single_variant_logs($product_id,'','',$get,$this->acc);
						$tot_rows = !is_null($tot_rows) ? $tot_rows : 0;
						$config["total_rows"] = $tot_rows;						
						$data["results"] = $this->log_code_model->get_single_variant_logs($product_id, $page ,$config["per_page"],$get,$this->acc);
						$data['details']['inventory'] = $this->inventory_model->get_single_variant_inv_details($product_id,$this->acc);	
						$data['details']['main'] = $this->product_model->get_single_variant_product_details($product_id,$this->acc);	
						$data['details']['caption'] = 'Variant Product';						
						$data['details']['scale'] = ' /<sub>qty</sub>';	
						$data['details']['inv_scale'] = 'No(s)';
						$data['details']['pos_id'] = '';
						break;
					case 4.0:
						$tot_rows = $this->log_code_model->get_product_logs($product_id,'','',$get,$this->acc);
						$tot_rows = !is_null($tot_rows) ? $tot_rows : 0;
						$config["total_rows"] = $tot_rows;						
						$data["results"] = $this->log_code_model->get_product_logs($product_id, $page ,$config["per_page"],$get,$this->acc);

						$data['details']['inventory'] = $this->inventory_model->get_blend_inv_details($product_id,$this->acc);
						$data['details']['main'] = $this->product_model->get_blend_product_details($product_id,$this->acc);	
						$data['details']['main']['cmp_name'] = '';
						$data['details']['caption'] = 'Grouped Product';							
						$data['details']['scale'] = ' /<sub>qty</sub>';	
						$data['details']['inv_scale'] = 'No(s)';
						$data['details']['pos_id'] = '';
						break;
				}
				$settings = $this->account_model->current_plan_status($this->acc);
				$data['timezone'] = $settings['timezone']; 
				$this->pagination->initialize($config);		
				$data["links"] = $this->pagination->create_links();
				//header
				$header['view']['title'] = 'Product Details';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_JS_ROOT.'jquery-ui/css/jquery-ui.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$this->load->view('products/show_product',$data);
				
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-1.9.1.js').'"></script>'."\n";
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/jquery-ui-touch.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'jquery-ui/touch-punch.js').'"></script>'."\n";
				$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/show_product.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);	
				//$this->output->enable_profiler(TRUE);		
			} else {
				$this->load->view('site_404/url_404'); 			
			}
		}
	}
	
	public function add_variant($product_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			$data = $this->product_model->is_parent_variant($product_id,$this->acc);
			$data['scale_list'] = $this->product_model->M_get_product_scale_assoc();
			$data['options'] = $this->product_model->M_product_scale_drop();
			$data['suppliers'] = $this->supplier_model->M_get_supplier($this->acc);
			$data['var_types'] = $this->variant_model->M_get_variants($this->acc);
			$data['def_locale_tax'] = $this->taxes_model->get_all_outlet_taxes($this->acc);
			$data['single_group_taxes_combo'] = $this->taxes_model->get_single_group_taxes_combo($this->acc);
			$data['prd_category_combo'] = $this->product_model->prd_category_combo($this->acc);
			$data['brand_combo'] = $this->brand_and_tag_model->brand_combo($this->acc);
			$data['auto_sku'] = $this->product_model->generate_incremented_sku($this->acc);
			$data['attributes'] = $this->product_model->get_variant_attributes($product_id,$this->acc);
			$data['variant_dropdown'] = $this->variant_model->variant_dropdown($this->acc);
			$scale = $this->product_model->check_scale($product_id,$this->acc);
			if($scale == 3 and !is_null($scale))
			{
				//header
				$header['view']['title'] = 'Add Variant';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$this->load->view('products/add_variant',$data);
				
				// footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
				$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/add_product.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
			} else {
				$this->load->view('site_404/url_404'); 			
			}
		}
	}
	public function create_variant($product_id)
	{
		$this->load->view('session/pos_session');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('sku', 'SKU', 'required|alpha_numeric|max_length[50]|xss_clean');
		$this->form_validation->set_rules('price', 'price', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('margin', 'margin', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('retail', 'retail price', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('new_p_scale', 'product scale', 'xss_clean');		
		$this->form_validation->set_rules('prd_wh_id', 'wearhouse id', 'xss_clean');
		$this->form_validation->set_rules('prd_pur_id', 'purchase id', 'xss_clean');
		$this->form_validation->set_rules('prd_weight', 'product weight', 'xss_clean');
		$this->form_validation->set_rules('new_var_method[]', 'variant option value', 'required|xss_clean');
		$this->form_validation->set_rules('var_type_name[]', 'variant option dropdown', 'required|xss_clean');

		$cur_stk = $data['cur_stk'] = $this->input->post('cur_stk');
		$reorder_stk = $data['reorder_stk'] = $this->input->post('reorder_stk');
		$reorder_qty = $data['reorder_qty'] = $this->input->post('reorder_qty');
		foreach($cur_stk as $key => $value)
		{
			$this->form_validation->set_rules('cur_stk['.$key.']', $value, 'xss_clean');
		}
		foreach($reorder_stk as $key => $value)
		{
			$this->form_validation->set_rules('reorder_stk['.$key.']', $value, 'xss_clean');
		}
		foreach($reorder_qty as $key => $value)
		{
			$this->form_validation->set_rules('reorder_qty['.$key.']', $value, 'xss_clean');
		}
		if($this->form_validation->run() == FALSE)
		{
			$this->add_variant($product_id);
		} else {
			$data['product_id'] = $product_id;
			$data['var_type_name'] = $this->input->post('var_type_name'); // array
			$data['new_var_method'] = $this->input->post('new_var_method'); // array
			$data['visib_stat'] = $this->input->post('visib_stat');
			$data['prd_weight'] = $this->input->post('prd_weight') == '' ? 0 : $this->input->post('prd_weight');;
			$data['sku'] = $this->input->post('sku');
			$data['price'] = $this->input->post('price');
			$data['margin'] = $this->input->post('margin');
			$data['retail'] = $this->input->post('retail');
			$data['def_location'] = $this->input->post('def_location'); //array outlet id
			$data['sale_tax'] = $this->input->post('qty_scale_tax'); // array may be empty or defined
			$data['loyalty_stat'] = $this->input->post('loyalty_stat'); // if setto 1 then default else loyalty_cust_val val
			$data['loyalty_def_val'] = $this->input->post('loyalty_def_val');
			$data['loyalty_cust_val'] = $this->input->post('loyalty_cust_val');
			$data['loyalty_val'] = $data['loyalty_stat'] ? $data['loyalty_def_val'] : $data['loyalty_cust_val'];
			$data['trace_inv'] = $this->input->post('trace_inv') == 30 ? 30 : 40; // if checked then only insert inventory
			$data['prd_wh_id'] = $this->input->post('prd_wh_id');
			$data['prd_pur_id'] = $this->input->post('prd_pur_id');
			$data['ship_stat'] = $this->input->post('ship_stat');
			$data['inv_outlet'] = $this->input->post('inv_outlet'); //array
			$data['cur_stk'] = $this->input->post('cur_stk'); //array
			$data['reorder_stk'] = $this->input->post('reorder_stk'); ///array
			$data['reorder_qty'] = $this->input->post('reorder_qty'); ///array
			$data['show_cart'] = $this->input->post('show_cart') == 30 ? 30 : 40;;
			$data['user_id'] = $this->user_id;
			$data['acc'] = $this->acc;
			$data['max_variants'] = $this->max_variants;
			$error_variant = implode(" / ",array_filter($data['new_var_method']));
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Variant successfully created. '.anchor(base_url().'products/add_product','Add another product','class="btn btn-xs btn-primary"'), 
						2 => 'Unable to create variant. SKU cant start with weighing scale barcode prefix. Please try another.', 
						3 => 'Unable to create variant. SKU has already been used for some other product. Please try another.',
						4 => 'You have exceeded maximum product limit, please '.anchor('account','upgrade','class="btn btn-xs btn-primary"').' your account to add more products.',
						5 => 'The variant combination '.$error_variant.' already exist. Please try another.'
						);
			$response = $this->product_form_model->insert_child_variant_product($data);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			redirect(base_url().'products/'.$product_id);						
		}
	}
	public function delete_product($product_id)
	{
		$this->load->view('session/pos_session');
		$scale = $this->product_model->check_scale($product_id,$this->acc);
		if(!is_null($scale))
		{
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Product successfully deleted!', 
						2 => 'The product you are deleting is associated to other group products or activity. Please remove them from those associations and try again',
						3 => 'One of its product variant is associated to other group products or activity. Please remove them from those associations and try again',
						//waiting 4 => 'This product is associated to pending sale and it cant be deleted'
						);
			$response = $this->product_model->delete_product($scale,$product_id,$this->acc);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			redirect(base_url().'products');						
		} else {
			$this->load->view('site_404/url_404'); 			
		}
	}
	public function delete_all_variant($product_id)
	{
		$this->load->view('session/pos_session');
		$scale = $this->product_model->check_scale($product_id,$this->acc);
		if(!is_null($scale) && $scale == 3)
		{
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Product successfully deleted!', 
						2 => 'The product you are deleting is associated to other group products or activity. Please remove them from those associations and try again',
						3 => 'One of its product variant is associated to other group products or activity. Please remove them from those associations and try again',
						//waiting 4 => 'This product is associated to pending sale and it cant be deleted'
						);
			$response = $this->product_model->delete_all_variant($product_id,$this->acc);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			redirect(base_url().'products');						
		} else {
			$this->load->view('site_404/url_404'); 			
		}
	}
	public function delete_tag()
	{
		$product_id = $this->input->post('product_id');	
		$tag_id = $this->input->post('tag_id');	
		$resp = $this->brand_and_tag_model->delete_tag($product_id,$tag_id,$this->acc);
		echo $resp;
	}
	public function activate($id)
	{
		$data['id'] = $this->input->post('id');
		$data['clause'] = $this->input->post('clause');
		$data['scale'] = $this->input->post('scale');
		if(strlen($data['id']) > 0 && strlen($data['scale']) > 0 && strlen($data['clause']) > 0)
		{		
			$response = $this->product_model->ajax_product_status($data,$this->acc);
			echo $response;
		} else {
			echo json_encode(array('status' => 'invalid call'));	
		}
	}
	public function deactivate($id)
	{
		$data['id'] = $this->input->post('id');
		$data['clause'] = $this->input->post('clause');
		$data['scale'] = $this->input->post('scale');
		if(strlen($data['id']) > 0 && strlen($data['scale']) > 0 && strlen($data['clause']) > 0)
		{		
			$response = $this->product_model->ajax_product_status($data,$this->acc);
			echo $response;
		} else {
			echo json_encode(array('status' => 'invalid call'));	
		}
	}
	public function delete_hidden()
	{
		$response = $this->product_model->delete_hidden($this->acc);
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Hidden products successfully deleted except group associated products', 
						);		
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url().'products');						
			
	}
	public function export()
	{
		$this->load->view('session/pos_session');
		$this->load->model('download/download_model');
		if(isset($_GET['product_stat']))
		{
			$bool = $_GET['product_stat'] == "VISIBLE" ? 30 : 40;
		} else {
			$bool = 30;
		}
		$search = isset($_GET['search_product']) ? $_GET['search_product'] : '';
		$brand = isset($_GET['product_brand']) ? $_GET['product_brand'] : '';
		$cat = isset($_GET['product_cat']) ? $_GET['product_cat'] : '';
		$supp_id = isset($_GET['supplier']) ? $_GET['supplier'] : '';
		$tag_id = isset($_GET['tag_id']) ? $_GET['tag_id'] : '';
		$where_array = array('search' => $search,'product_stat' => $bool,'product_cat' => $cat,'product_brand' => $brand,'supplier_id' => $supp_id,'tag_id' => $tag_id);
		$products = $this->download_model->download_products($where_array,$this->acc);
		$delimiter = ",";
        $newline = "\r\n";
        $data = $this->dbutil->csv_from_result($products, $delimiter, $newline);
		force_download($this->session->userdata('pos_hoster_cmp').'-products('.rand(10000,100000).').csv',$data);
	}
	public function ajax_update_variant_pos()
	{
		$data = $this->input->post();
		if(isset($data['prd_params']))
		{
			$this->product_model->update_variant_pos($data);
		} else {
			die('Parameters not found!');	
		}
	}
	public function export_kilo_products()
	{
		$this->load->view('session/pos_session');
		$products = $this->product_model->download_kilo_products($this->acc);
		$this->output->enable_profiler(TRUE);
		$delimiter = ",";
        $newline = "\r\n";
        $data = $this->dbutil->csv_from_result($products, $delimiter, $newline);
		force_download($this->session->userdata('pos_hoster_cmp').'-kilo-products('.rand(10000,100000).').csv',$data);
	}
	public function add_custom_variant()
	{
		$data = array();
		$data['cust_var_key'] = $this->input->post('cust_var_key');	
		$data['cust_var_value'] = $this->input->post('cust_var_value');	
		$data['acc'] = $this->acc;
		$response = $this->variant_model->add_custom_variant($data);
		echo json_encode($response);
	}
}
?>