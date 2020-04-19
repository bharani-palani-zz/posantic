<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Receipt_template extends CI_Controller
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
		$validity = $this->login_model->check_validity($this->acc);
		$subdomain = $this->session->userdata('subdomain');
		$this->is_valid_browser_domain = is_this_subdomain_browser($subdomain);
		if($validity == 0)
		{
			redirect(base_url().'account');
		}		
    }
	public function show_receipt_template()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1)
			{
				$data = $this->receipt_template_model->get_all_templates($this->acc);
				if(isset($data['template_id']))
				{
					if($this->session->flashdata('form_errors')) {
						$data['form_errors'] =  $this->session->flashdata('form_errors');
					}
					if($this->session->flashdata('form_success')) {
						$data['form_success'] = $this->session->flashdata('form_success');
					}
					//header
					$header['view']['title'] = 'All templates';
					$role = $this->roles_model->get_roles($this->privelage);
					list($header['role_code'],$header['role_name']) = $role;
					$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
					$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
					$this->load->view('top_page/top_page',$header);
					
					//body
					$this->load->view('receipt_template/show_receipt_template',$data);		
					
					//footer
					$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/edit_register.js').'"></script>'."\n";
					$this->load->view('bottom_page/bottom_page',$footer);			
						
				} else {
					$this->load->view('site_404/url_404'); 				
				}
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function add_receipt_template()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1)
			{
				$data['printer_types'] = $this->receipt_template_model->get_printer_types();
				$data['combo_rec_headers'] = $this->receipt_template_model->get_receipt_headers();
				//header
				$header['view']['title'] = 'Add receipt template';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$this->load->view('receipt_template/add_receipt_template',$data);	
				
				//footer		
				$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'tinymce/tinymce.min.js').'"></script>'."\n";
				$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'barcode/jquery-barcode-2.0.2.min.js').'"></script>'."\n";
				$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
				$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
				$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
				$footer['foot']['script'][5] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/receipt_template.js').'"></script>'."\n";
				$this->load->view('bottom_page/bottom_page',$footer);			
				
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function update_receipt_template($template_id)
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1)
			{
				$data = $this->receipt_template_model->get_template_wrt_id($template_id,$this->acc);
				$data['combo_rec_headers'] = $this->receipt_template_model->get_receipt_headers();
				$data['printer_types'] = $this->receipt_template_model->get_printer_types();
				if(isset($data['template_id']))
				{
					//header
					$header['view']['title'] = 'Update template';
					$role = $this->roles_model->get_roles($this->privelage);
					list($header['role_code'],$header['role_name']) = $role;
					$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
					$header['style'][1] = link_tag(POS_CSS_ROOT.'repository/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')."\n";
					$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
					$this->load->view('top_page/top_page',$header);
					
					//body
					$this->load->view('receipt_template/update_receipt_template',$data);	
					
					//footer
					$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'tinymce/tinymce.min.js').'"></script>'."\n";
					$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'barcode/jquery-barcode-2.0.2.min.js').'"></script>'."\n";
					$footer['foot']['script'][2] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/highlight.js').'"></script>'."\n";
					$footer['foot']['script'][3] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/dist/js/bootstrap-switch.js').'"></script>'."\n";
					$footer['foot']['script'][4] =  '<script type="text/javascript" src="'.base_url(POS_CSS_ROOT.'repository/bootstrap-switch/docs/js/main.js').'"></script>'."\n";
					$footer['foot']['script'][5] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/receipt_template.js').'"></script>'."\n";
					$footer['foot']['script'][6] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
					$this->load->view('bottom_page/bottom_page',$footer);			
							
				} else {
					$this->load->view('site_404/url_404'); 								
				}
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		}
	}
	public function create_receipt_template()
	{
		$this->load->view('session/pos_session');
		if($this->privelage == 1)
		{
			$data['temp_name'] = $this->input->post('temp_name'); 
			$data['printer_type'] = $this->input->post('printer_type'); 
			$data['header_type'] = $this->input->post('header_type'); 
			$data['show_disc'] = $this->input->post('show_disc')== '' ? 20 : 10; 
			$data['show_loyalty'] = $this->input->post('show_loyalty')== '' ? 20 : 10;
			$data['show_addrr'] = $this->input->post('show_addrr')== '' ? 20 : 10;
			$data['show_promo'] = $this->input->post('show_promo')== '' ? 20 : 10;
			$data['show_quotes'] = $this->input->post('show_quotes')== '' ? 20 : 10;
			$data['show_barcode'] = $this->input->post('show_barcode')== '' ? 20 : 10;
			$data['temp_header_text'] = $this->input->post('temp_header_text');
			$data['temp_bill_no_caption'] = $this->input->post('temp_bill_no_caption');
			$data['temp_operator_caption'] = $this->input->post('temp_operator_caption');
			$data['temp_disc_caption'] = $this->input->post('temp_disc_caption');
			$data['temp_tax_caption'] = $this->input->post('temp_tax_caption');
			$data['temp_change_caption'] = $this->input->post('temp_change_caption');
			$data['temp_loyalty_caption'] = $this->input->post('temp_loyalty_caption');
			$data['temp_total_caption'] = $this->input->post('temp_total_caption');
			$data['temp_footer_text'] = $this->input->post('temp_footer_text');
			$data['acc'] = $this->acc;
			$val =& $this->form_validation; 
			$val->set_error_delimiters('<span class="form_errors">', '</span>');		
			$val->set_rules('temp_name', 'Template Name', 'trim|required|max_length[25]|min_length[1]|xss_clean');
			$val->set_rules('temp_bill_no_caption', 'Bill number caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
			$val->set_rules('temp_operator_caption', 'Cashier/Operator caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
			$val->set_rules('temp_disc_caption', 'Discount caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
			$val->set_rules('temp_tax_caption', 'Tax caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
			$val->set_rules('temp_change_caption', 'Tender change caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
			$val->set_rules('temp_loyalty_caption', 'Loyalty caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
			$val->set_rules('temp_total_caption', 'Total caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
			if($val->run() == FALSE)
			{
				$this->add_receipt_template();		
			} else {
				$phrase = array(0 => 'Error: Oops! Something Went Wrong! Please Try Again',
								1 => 'Receipt Template Successfully Created!', 
								);
				$response = $this->receipt_template_model->insert_template($data);
				$div = ($response == 1) ? 'form_success' : 'form_errors';
				$this->session->set_flashdata($div, ucfirst(strtolower($phrase[$response])));
				redirect(base_url().'receipt_template/show');
			}
		}
	}
	public function update_save_receipt_template($template_id)
	{
		$this->load->view('session/pos_session');
		$data['template_id'] = $template_id; 
		$data['temp_name'] = $this->input->post('temp_name'); 
		$data['printer_type'] = $this->input->post('printer_type'); 
		$data['header_type'] = $this->input->post('header_type'); 
		$data['show_disc'] = $this->input->post('show_disc');
		$data['show_loyalty'] = $this->input->post('show_loyalty');
		$data['show_addrr'] = $this->input->post('show_addrr');
		$data['show_promo'] = $this->input->post('show_promo');
		$data['show_quotes'] = $this->input->post('show_quotes');
		$data['show_barcode'] = $this->input->post('show_barcode');
		$data['temp_header_text'] = $this->input->post('temp_header_text');
		$data['temp_bill_no_caption'] = $this->input->post('temp_bill_no_caption');
		$data['temp_operator_caption'] = $this->input->post('temp_operator_caption');
		$data['temp_disc_caption'] = $this->input->post('temp_disc_caption');
		$data['temp_tax_caption'] = $this->input->post('temp_tax_caption');
		$data['temp_change_caption'] = $this->input->post('temp_change_caption');
		$data['temp_loyalty_caption'] = $this->input->post('temp_loyalty_caption');
		$data['temp_total_caption'] = $this->input->post('temp_total_caption');
		$data['temp_footer_text'] = $this->input->post('temp_footer_text');
		$data['acc'] = $this->acc;
		$val =& $this->form_validation; 
		$val->set_error_delimiters('<span class="form_errors">', '</span>');		
		$val->set_rules('temp_name', 'Template Name', 'trim|required|max_length[25]|min_length[1]|xss_clean');
		$val->set_rules('temp_bill_no_caption', 'Bill number caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
		$val->set_rules('temp_operator_caption', 'Cashier/Operator caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
		$val->set_rules('temp_disc_caption', 'Discount caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
		$val->set_rules('temp_tax_caption', 'Tax caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
		$val->set_rules('temp_change_caption', 'Tender change caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
		$val->set_rules('temp_loyalty_caption', 'Loyalty caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
		$val->set_rules('temp_total_caption', 'Total caption', 'trim|required|max_length[15]|min_length[3]|xss_clean');
		if($val->run() == FALSE)
		{
			$this->update_receipt_template($template_id);	
		} else {
			$phrase = array(0 => 'Error: Oops! Something Went Wrong! Please Try Again',
							1 => 'Receipt Template Successfully Updated!', 
							);
			$response = $this->receipt_template_model->update_template($data);
			$div = ($response == 1) ? 'form_success' : 'form_errors';
			$this->session->set_flashdata($div, ucfirst(strtolower($phrase[$response])));
			redirect(base_url().'setup/receipt_template/show');
		}
	}
	public function delete_receipt_template($template_id)
	{
		$this->load->view('session/pos_session');
		$response = $this->receipt_template_model->delete_template($template_id,$this->acc);
		$div = ($response['stat'] == 1) ? 'form_success' : 'form_errors';
		$phrase = array(0 => $response['error_str'],
						1 => $response['error_str'], 
						2 => $response['error_str'], 
						);
		$this->session->set_flashdata($div, $phrase[$response['stat']]);
		redirect(base_url().'setup/receipt_template/show');
	}
}
?>