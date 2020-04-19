<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rescue extends CI_Controller
{
	public $acc;
	var $exp_key = null;
    public function __construct() 
    {
        parent::__construct();
		$this->load->helper('email');
		$this->acc = $this->session->userdata('acc_no');
		$subdomain = $this->session->userdata('subdomain');
		$this->is_valid_browser_domain = is_this_subdomain_browser($subdomain);
		$validity = $this->login_model->check_validity($this->acc);
		if($validity == 0)
		{
			redirect(base_url().'account');
		}
		$this->session->unset_userdata('user_id');
		$this->exp_key = "every dog has its data";
    }
	public function forgot_password()
	{
		$data = array();
		if($this->session->flashdata('form_errors')) {
			$data['form_errors'] =  $this->session->flashdata('form_errors');
		}
		if($this->session->flashdata('form_success')) {
			$data['form_success'] = $this->session->flashdata('form_success');
		}
		$this->load->view('rescue/forgot_password',$data);
	}
	public function reset_password()
	{
		$email_or_id = $this->input->post('email');
		$sett = $this->admin_model->settings_model();
		$ser_provider = $sett[3];
		$ser_provider_email = $sett[1];
		$user_detail = $this->user_model->user_details_wrt_email($email_or_id,$this->acc);
		$master_data = $this->setup_model->M_get_masterdata($this->acc);
		if(count($user_detail) > 0)
		{
			$encrypted_string = rawurlencode($this->encrypt->encode($user_detail['user_id']."|".(now()+3600),$this->exp_key));		
			$url = base_url().'rescue/change_password?token='.$encrypted_string;
			$this->load->library('email');		
			$full_domain = $_SERVER['SERVER_NAME'];
			$just_domain = preg_replace("/^(.*\.)?([^.]*\..*)$/", "$2", $_SERVER['HTTP_HOST']); //for testing purpose

			$this->email->from('support@'.$just_domain, $ser_provider);
			$this->email->cc('');
			$this->email->bcc($master_data[5]);
			$this->email->to($user_detail['user_mail']); 
			$this->email->subject('Your account password reset for '.$ser_provider.' account');
			$msg = '<html>
					<head>
					</head>
					<body>
					<p>Hi '.$user_detail['display_name'].',</p>
					<p>Please click the link below to reset your password</p>
					<p><a href="'.$url.'">'.$url.'</a></p>
					<p>This reset link will expire in 1 hour.</p>
					<p>If you have`nt request your password to be reset, ignore this email.</p>
					<p><br />Regards,</p>
					<p>The '.$ser_provider.' Team</p>				
					</div>
					</body>
					</html>
					';
			$this->email->message($msg);	
			if($this->email->send())
			{
				$this->session->set_flashdata("reset_mail_id", $user_detail['user_mail']);
				redirect(base_url('rescue/mail_sent'));
			} else {
				$this->session->set_flashdata("form_errors", "Oops! Something went wrong. Please try again");
				redirect(base_url('rescue/forgot_password'));	
			}
		} else {
			$this->session->set_flashdata("form_errors", "Error: Invalid Email or username");
			redirect(base_url('rescue/forgot_password'));	
		}
	}
	public function mail_sent()
	{
		$this->load->view('rescue/mail_sent');
	}
	public function change_password()
	{
		$data['token'] = $this->input->get('token');
		$un_hash = $this->encrypt->decode(rawurldecode($data['token']),$this->exp_key);
		if(strpos($un_hash,'|') === false)
		{
			$this->session->set_flashdata("form_errors", "Reset link broken. Please try again");
			redirect(base_url('rescue/forgot_password'));	
		} else {
			list($user_id,$time_limit) = explode("|",$un_hash);
			if(now() < $time_limit)
			{
				$check = preg_match( '/^\{?[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}\}?$/', $user_id);
				if($check)
				{
					$data['user_details'] = $this->user_model->user_details($user_id,$this->acc);
					$this->load->view('rescue/change_password',$data);
				} else {
					$this->session->set_flashdata("form_errors", "Reset link broken. Please try again");
					redirect(base_url('rescue/forgot_password'));	
				}
			} else {
				$this->session->set_flashdata("form_errors", "Reset link time expired. Please try again");
				redirect(base_url('rescue/forgot_password'));	
			}
		}
	}
	public function replace_password()
	{
		$encrypted_string = $this->input->post('token');
		$data['password'] = $this->input->post('password');
		$un_hash = $this->encrypt->decode(rawurldecode($encrypted_string),$this->exp_key);
		list($data['user_id'],$data['time_imit']) = explode("|",$un_hash);
		$data['subdomain'] = $this->session->userdata('subdomain');
		$data['acc'] = $this->acc;
		$check = preg_match( '/^\{?[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}\}?$/', $data['user_id']);
		if($check)
		{
			$response = $this->user_model->save_password($data);
			if($response == 1)
			{
				$this->load->view('rescue/password_changed');
			} else {
				$this->session->set_flashdata("form_errors", "Oops! Something went wrong. Please try again");
				redirect(base_url('rescue/forgot_password'));	
			}
		} else {
			$this->session->set_flashdata("form_errors", "Token Expired! Please try again");
			redirect(base_url('rescue/forgot_password'));	
		}
	}
}
?>