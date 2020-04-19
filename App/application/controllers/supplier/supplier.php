<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Supplier extends CI_Controller
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
	public function index()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				//header
				$header['view']['title'] = 'Suppliers';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$data = array();
				$data['all_suppliers'] = $this->supplier_model->get_all_suppliers($this->acc);
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$this->load->view('supplier/suppliers',$data);
				
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function add()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				//header
				$header['view']['title'] = 'Add Supplier';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$data = array();
				$data['country_dropdown'] = array('' => '--') + $this->setup_model->get_countries_select();
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$this->load->view('supplier/add_supplier',$data);
				
				//footer
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'tinymce/tinymce.min.js').'"></script>'."\n";
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'supplier/add_supplier.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function unique_supplier($cmp_str)
	{
		$this->db->select('count(*) as counted');	
		$query = $this->db->get_where('pos_e_suppliers',array('account_no' => $this->acc,'cmp_name' => $cmp_str));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			if($row['counted'] > 0)
			{
				$this->form_validation->set_message('unique_supplier', 'This supplier company name already exist. Please try another.');
				return false;	
			} else {
				return true;	
			}
		} else {
			return false;
		}
	}
	public function create()
	{
		$this->load->view('session/pos_session');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('contact_name', 'Contact name', 'trim|required|max_length[25]|min_length[4]|xss_clean');
		$this->form_validation->set_rules('cmp_name', 'Company name', 'trim|required|max_length[25]|min_length[4]|xss_clean|callback_unique_supplier[cmp_name]');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->add();
		} else {
			$data['contact_name'] = $this->input->post('contact_name');
			$data['cmp_name'] = $this->input->post('cmp_name');
			$data['supp_desc'] = $this->input->post('supp_desc');
			$data['contact_mobile'] = $this->input->post('contact_mobile');
			$data['contact_phone'] = $this->input->post('contact_phone');
			$data['contact_email'] = $this->input->post('contact_email');
			$data['contact_web'] = $this->input->post('contact_web');
			$data['contact_fax'] = $this->input->post('contact_fax');
			$data['contact_addr1'] = $this->input->post('contact_addr1');
			$data['contact_addr2'] = $this->input->post('contact_addr2');
			$data['contact_city'] = $this->input->post('contact_city');
			$data['contact_state'] = $this->input->post('contact_state');
			$data['contact_postalcode'] = $this->input->post('contact_postalcode');
			$data['contact_country'] = $this->input->post('contact_country');
			$data['acc'] = $this->acc;
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Supplier successfully created!', 
						);
			$response = $this->supplier_model->insert_supplier($data);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			redirect(base_url().'supplier');						
		}
	}
	public function edit($supp_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				$data = array();
				$data['country_dropdown'] = array('' => '--') + $this->setup_model->get_countries_select();
				$data['supp_details'] = $this->supplier_model->get_supplier_details($supp_id,$this->acc);
				if(!is_null($data['supp_details']))
				{
					if($this->session->flashdata('form_errors')) {
						$data['form_errors'] =  $this->session->flashdata('form_errors');
					}
					if($this->session->flashdata('form_success')) {
						$data['form_success'] = $this->session->flashdata('form_success');
					}
					//header
					$header['view']['title'] = 'Edit Supplier';
					$role = $this->roles_model->get_roles($this->privelage);
					list($header['role_code'],$header['role_name']) = $role;
					$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
					$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
					$this->load->view('top_page/top_page',$header);
					
					//body					
					$this->load->view('supplier/edit_supplier',$data);
					
					//footer
					$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'tinymce/tinymce.min.js').'"></script>'."\n";
					$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'supplier/add_supplier.js').'"></script>'."\n";
					$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
					$this->load->view('bottom_page/bottom_page',$footer);			
					
				} else {
					$this->load->view('site_404/url_404'); 			
				}
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function unique_supplier_except_me($cmp_str,$supp_id)
	{
		$this->db->select('count(*) as counted');	
		$this->db->where('supp_id !=',$supp_id);
		$query = $this->db->get_where('pos_e_suppliers',array('account_no' => $this->acc,'cmp_name' => $cmp_str));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			if($row['counted'] > 0)
			{
				$this->form_validation->set_message('unique_supplier_except_me', 'This supplier`s company name already exist. Please try another.');
				return FALSE;	
			} else {
				return TRUE;	
			}
		} else {
			return FALSE;
		}
	}
	public function change($supp_id)
	{
		$this->load->view('session/pos_session');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('contact_name', 'Contact name', 'trim|required|max_length[25]|min_length[4]|xss_clean');
		$this->form_validation->set_rules('cmp_name', 'Company name', 'trim|required|max_length[25]|min_length[4]|xss_clean|callback_unique_supplier_except_me['.$supp_id.']');
		if($this->form_validation->run() == FALSE)
		{
			$this->edit($supp_id);
		} else {
			$data['supp_id'] = $supp_id;
			$data['contact_name'] = $this->input->post('contact_name');
			$data['cmp_name'] = $this->input->post('cmp_name');
			$data['supp_desc'] = $this->input->post('supp_desc');
			$data['contact_mobile'] = $this->input->post('contact_mobile');
			$data['contact_phone'] = $this->input->post('contact_phone');
			$data['contact_email'] = $this->input->post('contact_email');
			$data['contact_web'] = $this->input->post('contact_web');
			$data['contact_fax'] = $this->input->post('contact_fax');
			$data['contact_addr1'] = $this->input->post('contact_addr1');
			$data['contact_addr2'] = $this->input->post('contact_addr2');
			$data['contact_city'] = $this->input->post('contact_city');
			$data['contact_state'] = $this->input->post('contact_state');
			$data['contact_postalcode'] = $this->input->post('contact_postalcode');
			$data['contact_country'] = $this->input->post('contact_country');
			$data['acc'] = $this->acc;
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Supplier successfully Updated!', 
						);
			$response = $this->supplier_model->update_supplier($data);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, $phrase[$response]);
			redirect(base_url().'supplier/'.$supp_id.'/show');						
		}
	}
	public function show($supp_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				$data = array();
				$data['supp_details'] = $this->supplier_model->get_supplier_details($supp_id,$this->acc);
				$data['countries_assoc'] = $this->setup_model->countries_assoc();
				if(!is_null($data['supp_details']))
				{
					if($this->session->flashdata('form_errors')) {
						$data['form_errors'] =  $this->session->flashdata('form_errors');
					}
					if($this->session->flashdata('form_success')) {
						$data['form_success'] = $this->session->flashdata('form_success');
					}
					//header
					$header['view']['title'] = 'Supplier details';
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
					$this->load->view('supplier/show_supplier',$data);
					
					//footer
					$footer['foot']['script'][0] =  '<script src="http://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.js"></script>'."\n";
					$footer['foot']['script'][1] =  '<script src="http://cdnjs.cloudflare.com/ajax/libs/respond.js/1.3.0/respond.js"></script>'."\n";
					$footer['foot']['script'][2] =  '<script src="http://maps.google.com/maps/api/js?sensor=false"></script>'."\n";
					$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'supplier/show_supplier.js').'"></script>'."\n";
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
	public function delete($supp_id)
	{
		$this->load->view('session/pos_session');
		$response = $this->supplier_model->delete_supplier($supp_id,$this->acc);
		$div = ($response['stat'] == 1) ? 'form_success' : 'form_errors';
		$phrase = array(0 => $response['error_str'],
						1 => $response['error_str'],
						2 => $response['error_str']
						);
		$this->session->set_flashdata($div, $phrase[$response['stat']]);
		redirect(base_url().'supplier');						
	}
}
?>