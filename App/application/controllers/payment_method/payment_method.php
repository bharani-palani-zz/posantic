<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Payment_method extends CI_Controller
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
    }
	public function add_type()
	{
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$data['method_combo'] = $this->payment_type_model->master_pay_types_combo();
				$this->load->view('payment_method/add_method',$data);	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 style="padding:5px;">Session Expired! Please <a href="'.base_url().'setup/pay_methods">Login</a> again</h3>');
		}
	}
	public function get_pay_type_fields()
	{
		$this->load->view('session/pos_session');
		$method_id = $this->input->get('method_id');
		$response_array = array();
		$response_array['type_data'] = $this->payment_type_model->get_pay_type_fields($method_id);
		echo json_encode($response_array);	
	}
	public function get_country_type_fields()
	{
		$this->load->view('session/pos_session');
		$load_code_for = $this->input->get('load_code_for');
		$response_array = $this->setup_model->get_payment_dynamic_select($load_code_for);
		echo json_encode($response_array);	
	}
	public function insert_method()
	{
		$this->load->view('session/pos_session');
		$method_config = $this->input->post('payment_method');
		$data['method_label'] = $this->input->post('method_label');
		$data['method_sort'] = $this->input->post('method_sort');
		$data['method_id'] = key($method_config);
		$master_config = $this->payment_type_model->master_payment_config($data['method_id']);
		foreach($master_config as $key => $value)
		{
			$data['insert_array'][$key] = array_key_exists($value,$method_config[$data['method_id']]) ? $method_config[$data['method_id']][$value] : 0;
		}
		$data['acc'] = $this->acc;
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Payment method successfully created!', 
						);
		$response = $this->payment_type_model->insert_method($data);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('setup/pay_methods'));		
	}
	public function delete_method($method_index)
	{
		$this->load->view('session/pos_session');
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Payment method successfully deleted!', 
						);
		$response = $this->payment_type_model->delete_method($method_index,$this->acc);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('setup/pay_methods'));		
	}
	public function type_edit_form($type_id)
	{
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$data['method_data'] = $this->payment_type_model->get_payment_type_if_id($type_id,$this->acc);
				if(strlen($data['method_data']['master_id']) > 0)
				{
					$this->load->view('payment_method/edit_method',$data);	
				} else {
					die('<h2 align="center">Unknown Payment Method</h2>');	
				}
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 style="padding:5px;">Session Expired! Please <a href="'.base_url().'setup/pay_methods">Login</a> again</h3>');
		}
	}
	public function update_method($method_index)
	{
		$this->load->view('session/pos_session');
		$data['master_id'] = $method_index;
		$data['method_label'] = $this->input->post('method_label');
		$data['method_sort'] = $this->input->post('method_sort');
		$data['payment_method'] = $this->input->post('payment_method');
		$data['acc'] = $this->acc;
		$phrase = array(0 => 'Error: Oops! Something Went Wrong! please Try Again',
						1 => 'Payment method successfully updated!', 
						);
		$response = $this->payment_type_model->update_method($data);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('setup/pay_methods'));		
	}
}