<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User extends CI_Controller 
{	
	public $acc;
	public $user_id;
	public $privelage;
	public $subdomain;
	public $url;
    public function __construct() 
    {
        parent::__construct();
		$this->acc = $this->session->userdata('acc_no');
		$this->privelage = $this->session->userdata('privelage');
		$this->user_id = $this->session->userdata('user_id');

		// check which domain user logs in
		$this->url = $_SERVER['HTTP_HOST'];
		if($this->url == '192.168.1.9' or $this->url == 'localhost')
		{
			$this->subdomain = 'posgear';
		} else {
			$parsedUrl = parse_url($this->url);
			$host = explode('.', $parsedUrl['path']);
			$subdomains = array_slice($host, 0, count($host) - 2 );
			$this->subdomain = $subdomains[0];
		}
    }
	public function index()
	{
		$host_names = explode(".",  $this->url);
		if(count($host_names) >= 3)
		{
			switch ($host_names[0]) {
				case 'secure':
					$this->startup_page();
				break;
				default:	
					$this->login();
			}
		} else {
			//for localhost.com and ipad 192.168.1.9
			// change this later for main website https://posantic.com
			$this->login();
		}
	}
	public function login_data()
	{
		$array = $this->admin_model->settings_model();
		$data = array(
				'hotline' => $array[0],
				'email' => $array[1],
				'web' => $array[2],
				'cmp' => $array[3],
				'version_type' => $array[4],
				'version_year' => $array[5]
				);
		return $data;
	}
	public function logged_as()
	{
		//logged as others account users
		$account_det = $this->admin_model->logged_as($this->subdomain);
		if(!is_null($account_det))
		{
			list($acc,$cmp_name,$account_stat) = $account_det;
			if( preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $acc))
			{
				$data = $this->login_data();
				if($account_stat == 10) // if account active
				{
					//$this->load->model('admin_model');						
					$this->session->set_userdata(array('acc_no' => $acc, 'cmp_name' => $cmp_name, 'subdomain' => $this->subdomain));
					$this->load->view('logon/logon',$data);
				} else if($account_stat == 20) { // if account inactive
					$this->load->view('site_404/account_inactive',$data); 				
				} else if($account_stat == 25) { // if account freezed
					$this->load->view('site_404/account_freezed',$data); 				
				}
			}
		} else {
			$this->load->view('site_404/store_404'); 				
		}		
	}
	public function login()
	{
		$sub = is_this_subdomain_browser($this->session->userdata('subdomain'));
		if($this->user_id && $sub == true)
		{
			$validity = $this->login_model->check_validity($this->acc);
			if($validity == 0)
			{
				redirect(base_url().'account');
			}
			//header
			$header['view']['title'] = 'Sell Screen';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage,false);
			// in the above line, notify is disabled. To enable remove the optional 2nd parameter(false)
			$this->load->view('top_page/top_page',$header);			
			$this->load->view('sale/sale');		
			$this->load->view('bottom_page/bottom_page');
			

		} else {
			$this->logged_as();
		}
	}
	// for secure page	
	public function startup_page()
	{
		if($this->subdomain == "secure")
		{			
			$data = $this->login_data();
			$this->load->view('logon/secure_logon',$data); 				
		} else {
			if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') { // if ssl connection
				$http = 'https';
			} else {
				$http = 'http';
			}
			$info = parse_url(base_url());
			$host = $info['host'];			
			$host_names = explode(".", $host);
			$bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
			$root =  'secure.'.$bottom_host_name;
			$redirect = $bottom_host_name == 'localhost.com' ? $http.'://secure.localhost.com/posantic/App' : $http.'://'.$root;
			redirect($redirect);
		}
	}	
	public function find_store()
	{
		$store_name = $this->input->post('store_name');
		$this->form_validation->set_error_delimiters('<span class="form_errors">', '</span>');		
		$this->form_validation->set_rules('store_name', 'store name', 'trim|required|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{
			$data = $this->login_data();
			$this->load->view('logon/secure_logon',$data); 				
		} else {
			$account_det = $this->admin_model->logged_as($store_name);
			if(!is_null($account_det))
			{
				if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') { // if ssl connection
					$http = 'https';
				} else {
					$http = 'http';
				}
				$info = parse_url(base_url());
				$host = $info['host'];			

				$host_names = explode(".", $host);
				$bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
				$root =  $store_name.'.'.$bottom_host_name;
				$_SERVER['HTTP_HOST'] = $root;
				$redirect = $bottom_host_name == 'localhost.com' ? $http.'://'.$store_name.'.localhost.com/posantic/App' : $http.'://'.$root;
				redirect($redirect);
			} else {
				$this->session->set_flashdata('find_error', 'Store does not exist');
				redirect(base_url());						
			}		
		}
	}
	public function signup()
	{
		if($this->subdomain == "secure")
		{
			//$this->session->sess_destroy();	
			$this->load->helper('captcha');
			$this->load->helper('url');
			$this->load->helper('string');
			// waiting
			// delete application/images/captcha folder using php cron job. Each image consumes 3kb to 4kb.
			$rand = random_string('alnum', 8);
			$vals = array(
				'word'       => $rand,
				'img_path'   => POS_IMG_ROOT.'captcha/',
				'img_url'    => POS_IMG_ROOT.'captcha/',
				'img_width'  => '150',
				'img_height' => 33,
				'expiration' => 7200
				);
			$cap = create_captcha($vals);
			
			$cap_data = array(
				'captcha_time'	=> $cap['time'],
				'ip_address'	=> $this->input->ip_address(),
				'word'			=> $rand
				);
			
			$query = $this->db->insert_string('pos_1_captcha', $cap_data);
			$this->db->query($query);
			$data = $this->login_data();
			$data['cap_image'] = $cap['image'];
			
			$data['country_dropdown'] = $this->setup_model->get_countries_select();
			$data['business_type'] = $this->setup_model->business_type();
			$currency = $this->setup_model->M_setup_currency();
			$data['curr']['--']['INR'] = 'Indian Rupee - INR';
			$data['curr']['--']['USD'] = 'United States Dollar - USD';
			$data['curr']['--']['AUD'] = 'Australian Dollar - AUD';
			$data['curr']['--']['GBP'] = 'United Kingdom Pound - GBP';
			$data['curr']['--']['CAD'] = 'Canadian Dollar - CAD';
			$data['curr']['--']['NZD'] = 'New Zealand Dollar - NZD';
			$data['curr']['--']['SGD'] = 'Singapore Dollar - SGD';
			$data['curr']['--']['AED'] = 'United Arab Emirates Dirham - AED';
			foreach($currency as $arr)
			{
				$data['curr']['---'][$arr[0]] = $arr[1];
			}
			if($this->session->flashdata('form_errors')) {
				$data['form_errors'] =  $this->session->flashdata('form_errors');
			}
			$this->load->view('logon/secure_signup',$data);
		} else {
			if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') { // if ssl connection
				$http = 'https';
			} else {
				$http = 'http';
			}
			$info = parse_url(base_url());
			$host = $info['host'];			
			$host_names = explode(".", $host);
			$bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
			$root =  'secure.'.$bottom_host_name;
			$redirect = $bottom_host_name == 'localhost.com' ? $http.'://secure.localhost.com/posantic/App' : $http.'://'.$root;
			redirect($redirect);
		}
	}
	public function terms()
	{
		$this->load->view('setup/terms');	
	}	
	public function checkdomain()
	{
		$this->load->model('secure/secure_model');
		$domain = $this->input->get('subdomain');
		$response = $this->secure_model->check_domain($domain);
		if($response == true)
		{
			http_response_code(200);
		} else {
			http_response_code(400);
		}
	}
	public function google_maps_custom_error($str)
	{
		if($str == "")
		{
			$this->form_validation->set_message('google_maps_custom_error', 'Unable to retrieve %s.');
			return false;	
		}
		return true;
	}
	public function captcha_checking($captcha){
		$this->load->model('secure/secure_model');
		if($this->secure_model->check_captcha_value($captcha))
		{
			 return true;
		} else {
			$this->form_validation->set_message('captcha_checking', '%s is not matching');
			return false;
		}
	}
	public function signup_form()
	{
		$this->form_validation->set_error_delimiters('<div class="form_errors">', '</div>');		
		$this->form_validation->set_rules('business_type', 'business type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('outlet_type', 'outlet type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('store_name', 'store name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('subdomain', 'private subdomain', 'trim|required|alpha_numeric|xss_clean');
		$this->form_validation->set_rules('contact_name', 'contact name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('contact_mobile', 'mobile', 'trim|required|max_length[10]|xss_clean');
		$this->form_validation->set_rules('contact_email', 'email', 'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('contact_password', 'password', 'trim|required|min_length[8]|xss_clean');

		$this->form_validation->set_rules('country', 'locality country', 'callback_google_maps_custom_error');
		$this->form_validation->set_rules('rawOffset', 'locality timezone offset', 'callback_google_maps_custom_error');
		$this->form_validation->set_rules('dstOffset', 'locality daylight saving offset', 'callback_google_maps_custom_error');
		$this->form_validation->set_rules('latitude', 'locality latitude coordinates', 'callback_google_maps_custom_error');
		$this->form_validation->set_rules('longitude', 'locality longitude coordinates', 'callback_google_maps_custom_error');
		$this->form_validation->set_rules('contact_currency', 'currency', 'trim|required|xss_clean');
		$this->form_validation->set_rules('captcha', 'Captcha value', 'callback_captcha_checking');
		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('form_errors', '<h4><span class="glyphicon glyphicon-remove-sign"></span> Typographic Error</h4><p>'.validation_errors().'</p>');
			redirect(base_url('signup'));						
		} else {
			$this->load->model('secure/secure_model');
			$signup_array = array();
			foreach($this->input->post() as $key => $value)
			{
				$signup_array[$key] = $this->input->post($key);
			}
			sleep(1);
			$response_array = $this->secure_model->signup_account($signup_array);
			if($response_array != false and is_array($response_array))
			{
				// email thank u registration..
				//warning: unable to send mail to yahoo. to achive email smtp setting has to be accomplished
				$hoster_data = array();
				$hoster_data = $this->login_data();
				
				$user_data = array(
					'display_name' => $response_array['display_name'],
					'username' =>  $response_array['username'],
					'user_mail' => $response_array['user_mail'],
					'userpassword' => $response_array['userpassword'],
					'base_url' => $response_array['base_url']
				);
				
				$data = array_merge($hoster_data,$user_data);
				
				$msg = $this->load->view('email_content/thankyou_registration',$data,true);
				$this->load->library('email');		
				$config = array(
					'charset'=>'utf-8',
					'wordwrap'=> TRUE,
					'mailtype' => 'html'
				);
				
				$this->email->initialize($config);
				
				$this->email->from($hoster_data['email'], $hoster_data['cmp']); //alert mail will only be sent if host has this email id as valid
				$this->email->cc('');
				$this->email->bcc('');
				$this->email->to($response_array['user_mail']); 
				$this->email->subject('Welcome to '.$hoster_data['cmp']);
				$this->email->message($msg);	
				$this->email->send();
				
				$this->load->library('../controllers/login/affirm');
				// auto login after signup				
				$set_sess_array = array('subdomain' => $response_array['subdomain'],'cmp_name' => $response_array['cmp_name']);
				$this->affirm->signin(
					$response_array['user_id'],$response_array['username'],$response_array['display_name'],$response_array['userpassword'],
					$response_array['hash_pass'],$response_array['acc_no'],$response_array['redirect_URL'],$set_sess_array
					);
			} else {
				$this->session->set_flashdata('form_errors', '<h4><span class="glyphicon glyphicon-remove-sign"></span> Unable to create your account</h4><p>Please try again!</p>');
				redirect(base_url('signup'));						
			}
		}
	}
	public function welcome()
	{
		if(!$this->user_id) 
		{
			$this->login();
		} else {
			//header
			$header['view']['title'] = 'Welcome';
			$role = $this->roles_model->get_roles($this->privelage);
			list($header['role_code'],$header['role_name']) = $role;
			$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
			$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
			$this->load->view('top_page/top_page',$header);
	
			$data['master_data'] = $this->setup_model->M_get_masterdata($this->acc);
			$data['account'] = $this->login_model->get_timezone_loc_plan_validity($this->acc);
	
			$this->load->view('dashboard/welcome',$data);
					
			//footer
			$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
			$this->load->view('bottom_page/bottom_page',$footer);			
		}
	}
	
	
}
?>