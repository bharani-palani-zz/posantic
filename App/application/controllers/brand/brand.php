<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Brand extends CI_Controller
{
	public $acc;
	public $privelage;
	public $pos_user;
	public $user_id;
    public function __construct() 
    {
        parent::__construct();
		$this->pos_user = $this->session->userdata('pos_user');
		$this->user_id = $this->session->userdata('user_id');
		$this->privelage = $this->session->userdata('privelage');
		$this->acc = $this->session->userdata('acc_no');
		$subdomain = $this->session->userdata('subdomain');
		$this->is_valid_browser_domain = is_this_subdomain_browser($subdomain);
		$validity = $this->login_model->check_validity($this->acc);
		if($validity == 0)
		{
			redirect(base_url().'account');
		}
    }
	public function show_brands()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			//header
			$header['view']['title'] = 'Brands';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
			$this->load->view('top_page/top_page',$header);
			
			//body
			$data['brand_det'] = $this->brand_and_tag_model->get_brands($this->acc);
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			if($this->session->flashdata('form_success')) {
				$data['form_success'] = $this->session->flashdata('form_success');
			}
			$this->load->view('brand/show_brands',$data);
			
			//footer
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer);			
			
		}
	}
	public function open_edit_form($brand_id)
	{
		if($this->user_id)
		{
			if($this->privelage == 1 || $this->privelage == 2)
			{
				list($data['brand_id'],$data['brand_name']) = $this->brand_and_tag_model->get_brand_details($brand_id,$this->acc);
				$this->load->view('brand/edit_brand',$data);	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 style="padding:5px;">Session Expired! Please <a href="'.base_url().'products/brand">Login</a> again</h3>');
		}
	}
	public function update_brand($brand_id)
	{
		$this->load->view('session/pos_session');
		$brand_name = $this->input->post('brand_name');	
		$response = $this->brand_and_tag_model->update_brand($brand_id,$brand_name,$this->acc);	
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Brand name successfully Updated!', 
						);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('products/brand'));
	}
	public function delete_brand($brand_id)
	{
		$this->load->view('session/pos_session');
		$response = $this->brand_and_tag_model->delete_brand($brand_id,$this->acc);	
		$div = ($response['stat'] == 1) ? 'form_success' : 'form_errors';
		$phrase = array(0 => $response['error_str'],
						1 => $response['error_str'],
						2 => $response['error_str']
						);
		$this->session->set_flashdata($div, $phrase[$response['stat']]);
		redirect(base_url('products/brand'));
	}
	public function add_brand()
	{
		if($this->user_id)
		{
			if($this->privelage == 1 || $this->privelage == 2)
			{
				$this->load->view('brand/add_brand');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 style="padding:5px;">Session Expired! Please <a href="'.base_url().'products/brand">Login</a> again</h3>');
		}
	}
	public function insert_brand()
	{
		$this->load->view('session/pos_session');
		$brand_name = $this->input->post('brand_name');	
		$response = $this->brand_and_tag_model->insert_brand($brand_name,$this->acc);	
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Brand successfully added!', 
						);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('products/brand'));
	}
	public function show_categories()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			//header
			$header['view']['title'] = 'Categories';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
			$this->load->view('top_page/top_page',$header);
			
			//body
			$data['cat_det'] = $this->brand_and_tag_model->get_cats($this->acc);
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			if($this->session->flashdata('form_success')) {
				$data['form_success'] = $this->session->flashdata('form_success');
			}
			$this->load->view('brand/show_cats',$data);
			
			//footer			
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer);			
			
		}
	}
	public function add_cat()
	{
		if($this->user_id)
		{
			if($this->privelage == 1 || $this->privelage == 2)
			{
				$this->load->view('brand/add_cat');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 style="padding:5px;">Session Expired! Please <a href="'.base_url().'products/categories">Login</a> again</h3>');
		}
	}
	public function insert_cat()
	{
		$this->load->view('session/pos_session');
		$cat_name = $this->input->post('cat_name');	
		$response = $this->brand_and_tag_model->insert_cat($cat_name,$this->acc);	
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Category successfully added!', 
						);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('products/categories'));
	}
	public function delete_cat($cat_id)
	{
		$this->load->view('session/pos_session');
		$response = $this->brand_and_tag_model->delete_cat($cat_id,$this->acc);	
		$div = ($response['stat'] == 1) ? 'form_success' : 'form_errors';
		$phrase = array(0 => $response['error_str'],
						1 => $response['error_str'],
						2 => $response['error_str']
						);
		$this->session->set_flashdata($div, $phrase[$response['stat']]);
		redirect(base_url('products/categories'));
	}
	public function cat_edit_form($cat_id)
	{
		if($this->user_id)
		{
			if($this->privelage == 1 || $this->privelage == 2)
			{
				list($data['cat_id'],$data['cat_name']) = $this->brand_and_tag_model->get_cat_details($cat_id,$this->acc);
				$this->load->view('brand/edit_cat',$data);	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 style="padding:5px;">Session Expired! Please <a href="'.base_url().'products/categories">Login</a> again</h3>');
		}
	}
	public function update_cat($cat_id)
	{
		$this->load->view('session/pos_session');
		$cat_name = $this->input->post('brand_name');	
		$response = $this->brand_and_tag_model->update_cat($cat_id,$cat_name,$this->acc);	
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Category name successfully Updated!', 
						);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('products/categories'));
		
	}
	public function show_tags()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			//header
			$header['view']['title'] = 'Tags';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
			$this->load->view('top_page/top_page',$header);
			
			// body
			$data['tag_det'] = $this->brand_and_tag_model->get_tags($this->acc);
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			if($this->session->flashdata('form_success')) {
				$data['form_success'] = $this->session->flashdata('form_success');
			}
			$this->load->view('brand/show_tags',$data);
			
			//footer
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer);			
		}
		
	}
	public function add_tags()
	{
		if($this->user_id)
		{
			if($this->privelage == 1 || $this->privelage == 2)
			{
				$this->load->view('brand/add_tag');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 style="padding:5px;">Session Expired! Please <a href="'.base_url().'products/tags">Login</a> again</h3>');
		}
	}
	public function insert_tags()
	{
		$this->load->view('session/pos_session');
		$tag_name = $this->input->post('tag_name');	
		$response = $this->brand_and_tag_model->insert_main_tag($tag_name,$this->acc);	
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Product Tag successfully added!', 
						);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('products/tags'));
	}
	public function tags_edit_form($tag_id)
	{
		if($this->user_id)
		{
			if($this->privelage == 1 || $this->privelage == 2)
			{
				list($data['tag_id'],$data['tag_name']) = $this->brand_and_tag_model->get_tag_details($tag_id,$this->acc);
				$this->load->view('brand/edit_tag',$data);	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 style="padding:5px;">Session Expired! Please <a href="'.base_url().'products/tags">Login</a> again</h3>');
		}
	}
	public function update_tag($tag_id)
	{
		$this->load->view('session/pos_session');
		$tag_name = $this->input->post('tag_name');	
		$response = $this->brand_and_tag_model->update_tag($tag_id,$tag_name,$this->acc);	
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Tag name successfully Updated!', 
						);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('products/tags'));
		
	}
	public function delete_tag($tag_id)
	{
		$this->load->view('session/pos_session');
		$response = $this->brand_and_tag_model->delete_main_tag($tag_id,$this->acc);	
		$div = ($response['stat'] == 1) ? 'form_success' : 'form_errors';
		$phrase = array(0 => $response['error_str'],
						1 => $response['error_str'],
						2 => $response['error_str']
						);
		$this->session->set_flashdata($div, $phrase[$response['stat']]);
		redirect(base_url('products/tags'));
	}

}