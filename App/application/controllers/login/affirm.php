<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Affirm extends CI_Controller 
{	
	public $acc;	
	public $subdomain;	
	public $username;	
	public $userpassword;	
	public $user_id;
	public $display_name;
	public $hash_pass;
	public $redirect_URL;
    public function __construct() 
    {
        parent::__construct();
    }
	public function index()
	{
		$this->acc = $this->session->userdata('acc_no');
		$this->subdomain = $this->session->userdata('subdomain');

		$this->username = $this->input->post('username');
		$this->userpassword = $this->input->post('pwd');
		$this->redirect_URL = $this->input->post('redirect_URL');

		list($this->user_id,$this->username,$this->display_name) = 
			(strpos($this->username,'@') === false) ? $this->login_model->get_username_from_name($this->username,$this->acc) : 
				$this->login_model->get_username_from_email($this->username,$this->acc);

		$concat = $this->user_id.$this->userpassword.$this->subdomain.$this->acc;
		$this->hash_pass = $this->encrypt->sha1($concat);	
				
		$this->signin($this->user_id,$this->username,$this->display_name,$this->userpassword,$this->hash_pass,$this->acc,$this->redirect_URL,array());
	}
	public function validate_user($user_id,$username,$hash_pass,$acc)
	{
		$validate_location = $this->login_model->getloc($user_id,$username,$hash_pass,$acc);
		return $validate_location;
	}
	public function signin($user_id,$username,$display_name,$userpassword,$hash_pass,$acc,$redirect_URL,$set_session_array)
	{
		$validate_location = $this->validate_user($user_id,$username,$hash_pass,$acc);
		if($validate_location !== false)
		{
			$hoster_array = $this->login_model->get_hoster_details();
			//make session variables
			//get App provider details
			$sess_data[0] = array(
				'pos_hoster_hotline' => $hoster_array['pos_hoster_hotline'],
				'pos_hoster_cmp' => $hoster_array['pos_hoster_cmp'],
				'pos_hoster_web' => $hoster_array['pos_hoster_web'],
				'pos_hoster_email' => $hoster_array['pos_hoster_email'],
				'pos_hoster_fb' => $hoster_array['pos_hoster_fb'],
				'pos_hoster_ver' => $hoster_array['pos_hoster_ver'],
				'pos_hoster_year' => $hoster_array['pos_hoster_year']
			);

			$id_priv_array = $this->login_model->get_id_privelage($username,$hash_pass,$acc);
			//make user session variables
			$sess_data[1] = array(
			   'user_id' => $id_priv_array['user_id'],
			   'user_password' => $userpassword,
			   'privelage' => $id_priv_array['privelage'],
			   'acc_no'  => $acc,
			   'loc_id' => $validate_location,
			   'is_primary' => $id_priv_array['is_delete'],
			   'pos_user' => $username,
			   'pos_display_user' => $display_name,
			   'pos_user_mail' => $id_priv_array['user_mail']
			);
			
			//get timezone details
			$timezone_loc_plan_validity = $this->login_model->get_timezone_loc_plan_validity($acc);
			$sess_data[2] = array(
				'tz' => $timezone_loc_plan_validity['tz'],
				'plan_code' => $timezone_loc_plan_validity['plan_code'],
				'currency' => $timezone_loc_plan_validity['currency'],
				'validity' => $timezone_loc_plan_validity['validity'],
				'fbid' => $timezone_loc_plan_validity['fbid'],
				'account_code' => $timezone_loc_plan_validity['account_code'],
				'account_string' => $timezone_loc_plan_validity['account_string'],
				'plan_registers' => $timezone_loc_plan_validity['plan_registers'],
				
				'plan_str' => $timezone_loc_plan_validity['plan_str'],
				'plan_store_handle' => $timezone_loc_plan_validity['plan_store_handle'],
				'plan_stk_limit' => $timezone_loc_plan_validity['plan_stk_limit'],
				'plan_user_limit' => $timezone_loc_plan_validity['plan_user_limit'],
				'plan_cust_db_count' => $timezone_loc_plan_validity['plan_cust_db_count'],
				'plan_registers' => $timezone_loc_plan_validity['plan_registers'],
				
			);
			//make Plan session variables
			$cookie = array(
				'name'   => 'posantic_subdomain',
				'value'  => md5($this->subdomain),
				'expire' => '0',
				'domain' => '.'.$this->subdomain.'.localhost.com',
				'path'   => '/',
				'prefix' => '',
				'secure' => FALSE
			);	
			$this->load->helper('cookie');
			$this->input->set_cookie($cookie);
			
			$sess = array_merge($sess_data[0],$sess_data[1],$sess_data[2],$set_session_array);
			$this->session->set_userdata($sess);
						
			$validity_days = $this->login_model->check_validity($acc);
			if($validity_days <= 0)
			{
				redirect($redirect_URL.'account');
			} else {
				$this->session->unset_userdata('redirect_URL');
				redirect($redirect_URL);
			}
		} else {
			$this->session->set_flashdata('Notify', 'Invalid User or Password!');
			$this->session->set_userdata(array('redirect_URL' => $this->agent->referrer()));
			redirect(base_url());			
		}
	}
	public function logout()
	{
		$this->session->sess_destroy();		
		redirect(base_url());
	}
}
?>