<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Account extends CI_Controller
{
	public $acc;
	public $pos_user;
	public $user_id;
	public $privelage;
	public $host;
    public function __construct() 
    {
        parent::__construct();
		$this->privelage = $this->session->userdata('privelage');
		$this->pos_user = $this->session->userdata('pos_user');
		$this->user_id = $this->session->userdata('user_id');
		$this->acc = $this->session->userdata('acc_no');
		$this->host = ($_SERVER['HTTP_HOST'] == "192.168.1.9") ? 'posgear' : $this->session->userdata('subdomain');
		$this->is_valid_browser_domain = is_this_subdomain_browser($this->host);
    }
	public function index()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1)
			{
				$data['merchant_details'] = $this->merchant_current_details();
				if($data['merchant_details']['account_stat'] == 10) // if account is active
				{
					list($dump['outlet_count'],$dump['reg_count'],$dump['prd_count'],$dump['user_count'],$dump['cust_count']) = $this->get_count_info();
					$data['outlet_str'] = $dump['outlet_count'] > 1 ? $dump['outlet_count'].' Outlets' : $dump['outlet_count'].' Outlet';
					$data['reg_str'] = $dump['reg_count'] > 1 ? $dump['reg_count'].' Registers' : $dump['reg_count'].' Register';
					$data['prd_str'] = $dump['prd_count'] > 1 ? $dump['prd_count'].' Products' : $dump['prd_count'].' Product';
					$data['user_str'] = $dump['user_count'] > 1 ? $dump['user_count'].' Users' : $dump['user_count'].' User';
					$data['cust_str'] = $dump['cust_count'] > 1 ? $dump['cust_count'].' Customers' : $dump['cust_count'].' Customer';
					$data['symbol'] = "<i class='fa fa-inr fa-fw'></i>";
					$data['reg_count'] = $dump['reg_count'];
	
					$recommend = $this->account_model->recommended_plans($dump['prd_count'],$dump['user_count'],$dump['cust_count'],$dump['outlet_count']);
					$rec_plan_key = min(array_keys($recommend));
					$data['rec_plan_key'] = $rec_plan_key;
					$data['rec_plan_array'] = $recommend;
					$data['recomend_str'] = $data['merchant_details']['account_type_code'] == "TRY" ? "We recommend you to activate ".$recommend[$rec_plan_key]." plan" : "You&rsquo;ve currently activated ".$data['merchant_details']['plan_code']." plan, though we recommend ".$recommend[$rec_plan_key]." plan";
					$data['validity'] = $this->login_model->check_validity($this->acc);
	
					$usage = $this->login_model->get_timezone_loc_plan_validity($this->acc);
					if($data['validity'] == 0) // if validity expires
					{
						$notify = $this->menu_model->notify();
						$this->load->view('top_page/expired_top_page',array('usage' => $usage,'expired_token' => $notify['string']));
					} else if($usage['usage_percent'] > 100) { // if storage limit expires
						$notify = $this->menu_model->notify();
						$this->load->view('top_page/expired_top_page',array('usage' => $usage,'expired_token' => $notify['string']));
					} else {
						$header['view']['title'] = 'Account';
						$role = $this->roles_model->get_roles($this->privelage);
						$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
						list($header['role_code'],$header['role_name']) = $role;
						$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
						$this->load->view('top_page/top_page',$header);
					}
					$data['plan_promotions'] = $this->account_model->get_plan_promotions_if_today();
					$data['plan_summary'] = $this->account_plan_summary();
					if($this->session->flashdata('form_errors')) {
						$data['form_errors'] =  $this->session->flashdata('form_errors');
					}
					if($this->session->flashdata('form_success')) {
						$data['form_success'] = $this->session->flashdata('form_success');
					}
					$this->load->view('setup/account', $data);					
	
					$footer['foot']['script'][0] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/account.js').'"></script>'."\n";
					$footer['foot']['script'][1] =  '<script type="text/javascript" src="'.base_url(POS_JS_ROOT.'administrator/confirm_modal_ajax.js').'"></script>'."\n";
					$this->load->view('bottom_page/bottom_page',$footer);			
					//$this->output->enable_profiler(TRUE);
				} else if($data['merchant_details']['account_stat'] == 20){ // if account is in-acive
					$array = $this->admin_model->settings_model();
					$data = array(
							'hotline' => $array[0],
							'email' => $array[1],
							'web' => $array[2],
							'cmp' => $array[3],
							'version_type' => $array[4],
							'version_year' => $array[5]
							);
					$this->load->view('site_404/account_inactive',$data); 				
				} else if($data['merchant_details']['account_stat'] == 25){ // if account is in freeze state
					$array = $this->admin_model->settings_model();
					$data = array(
							'hotline' => $array[0],
							'email' => $array[1],
							'web' => $array[2],
							'cmp' => $array[3],
							'version_type' => $array[4],
							'version_year' => $array[5]
							);
					$this->load->view('site_404/account_freezed',$data); 		
				} else {
					$this->load->view('site_404/store_404');		
				}
			} else {
				$this->load->view('noaccess/noaccess');		
			}
		}
	}
	public function get_count_info()
	{
		$this->load->view('session/pos_session');
		$outlet_count = $this->outlet_model->outlet_count($this->acc);
		$reg_count = $this->register_model->register_count($this->acc);
		$prd_count = $this->product_model->product_count($this->acc);
		$prd_count = $prd_count['grand_total'];
		$user_count = $this->user_model->user_count($this->acc);
		$cust_count = $this->customer_model->customer_count();
		return array($outlet_count,$reg_count,$prd_count,$user_count,$cust_count);
	}
	public function merchant_current_details()
	{
		$data = $this->account_model->get_merchant_account_details($this->host);
		return $data;
	}
	public function account_plan_summary()
	{
		$data = $this->account_model->get_account_all();
		return $data;
	}
	public function find_plan_pricing()
	{
		$this->load->view('session/pos_session');
		$plan_index = $this->input->get('plan_index');	
		$acc = $this->acc;
		$response = $this->account_model->find_plan_pricing($plan_index,$acc);
		echo json_encode($response);
	}
	public function find_term_pricing()
	{
		$this->load->view('session/pos_session');
		$plan_index = $this->input->get('plan_index');	
		$termed = $this->input->get('termed');	
		$acc = $this->acc;
		$response = $this->account_model->find_term_pricing($plan_index,$termed,$acc);
		echo json_encode($response);
	}
	public function payment_gateway()
	{
		$post = $this->input->post();
		echo '<pre>';
		print_r($post);
		echo '<h3>Payment gateway procedure starts here</h3>';
	}
	public function delete_trial_data()
	{
		if($this->session->userdata('user_id'))
		{
			if($this->session->userdata('privelage') == 1)
			{
				$this->load->view('setup/delete_trial_data');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 align="center" style="padding:5px;">Session Expired! Please <a href="'.base_url().'account">Login</a> again</h3>');
		}
	}
	public function trash_form_trial_data()
	{
		$data = $this->input->post('check_trial_delete');
		$data['acc'] = $this->acc;
		$phrase = array(
					0 => 'Nothing is selected to delete trial data',
					1 => 'Trial data successfully deleted.' 
					);
		$response = $this->account_model->trash_form_trial_data($data);
		$div = ($response == 1) ? 'form_success' : 'form_errors';
		$this->session->set_flashdata($div, $phrase[$response]);
		redirect(base_url('account'));						
	}
	public function delete_account()
	{
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$this->load->view('setup/delete_account');
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 align="center" style="padding:5px;">Session Expired! Please <a href="'.base_url().'account">Login</a> again</h3>');
		}
	}
	public function terms()
	{
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$this->load->view('setup/terms');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 align="center" style="padding:5px;">Session Expired! Please <a href="'.base_url().'account">Login</a> again</h3>');
		}
	}
	public function find_code_discount()
	{
		$code = $this->input->post('p_code');
		$plan_index = $this->input->post('plan_index');	
		$termed = $this->input->post('termed');	
		$acc = $this->acc;
		$response = $this->account_model->find_code_discount($code,$plan_index,$termed,$acc);
		echo json_encode($response);
	}
	public function form_delete_account()
	{
		$data['cancel_id'] =  $this->taxes_model->make_single_uuid();
		$data['cancel_reason'] = $this->input->post('reason_string');
		$data['cancel_comments'] = $this->input->post('comments');
		$data['cancelled_at'] = mdate('%Y-%m-%d %h:%i:%s', now());
		$data['cancelled_by'] = $this->user_id;
		$data['account_no'] = $this->acc;
		$response = $this->account_model->form_delete_account($data);
		$this->session->sess_destroy();
		redirect(base_url());
		// waiting a paid user should not be charged in future if he/she deletes the account
		//header("Location: https://".$this->session->userdata('pos_hoster_web')."?driven=precancel"); 
	}
	public function manage_storage_space()
	{
		if($this->user_id)
		{
			if($this->privelage == 1)
			{
				$this->load->view('setup/manage_storage_space');	
			} else {
				$this->load->view('noaccess/noaccess');	
			}
		} else {
			die( '<h3 align="center" style="padding:5px;">Session Expired! Please <a href="'.base_url().'account">Login</a> again</h3>');
		}
	}
	public function manage_space_for_date_range()
	{
		// waiting to get transaction count of date range
		$post = $this->input->get('post');
		if(count($post[0]) > 0 or count($post[1]) > 0)
		{
			//idea is
			// if $post[0] is set get trx data count w.r.t before date
			// if $post[1] is set get trx data count w.r.t date range
			//echo rand(10,10000);	
			$trx_count_data = rand(10,10000);
			if(strlen($post[0][0]) > 0) // before
			{
				$before_parse = date_parse($post[0][0]);
				if(count($before_parse['errors']) < 1)
				{
					$response = array('count' => $trx_count_data, 'string' => ' transactions found before '.date('d-M-Y h:i a',strtotime($post[0][0])));
					echo json_encode($response);
				} else {
					$response = array('count' => 0, 'string' => 'Invalid before date');
					echo json_encode($response);
				}
			} else if(strlen($post[1][0]) > 0 and strlen($post[1][1]) > 0) { // between date range
				$start_parse = date_parse($post[1][0]);
				$end_parse = date_parse($post[1][1]);
				if(count($start_parse['errors']) < 1 and count($end_parse['errors']) < 1)
				{
					$response = array('count' => $trx_count_data, 'string' => 'transactions found between '.date('d-M-Y h:i a',strtotime($post[1][0])).' and '.date('d-M-Y h:i a',strtotime($post[1][1])));
					echo json_encode($response);
				} else {
					$response = array('count' => 0, 'string' => 'Invalid date range');
					echo json_encode($response);
				}
			} else if(strlen($post[2][0])) { // after
				$after_parse = date_parse($post[2][0]);
				if(count($after_parse['errors']) < 1)
				{
					$response = array('count' => $trx_count_data, 'string' => ' transactions found after '.date('d-M-Y h:i a',strtotime($post[2][0])));
					echo json_encode($response);
				} else {
					$response = array('count' => 0, 'string' => 'Invalid after date');
					echo json_encode($response);
				}
			} else {
				$response = array('count' => 0, 'string' => NULL);
				echo json_encode($response);
			}
		} else {
			die('Illegal params');	
		}
	}
	public function form_manage_space()
	{
		// waiting to delete transaction w.r.t date range
		//idea is 
		//if($this->input->post('range_before')) is set delete transactions before date range
		//if($this->input->post('range_start') and $this->input->post('range_end')) is set delete trx between date range
		// while deleting remember about time zone
		echo '<pre>';	
		print_r($this->input->post());
	}
}