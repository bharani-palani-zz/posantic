<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login_model extends CI_Model
{
	public function getloc($user_id,$username,$hash_pass,$acc_no)
	{
		$this->db->select('count(*)');
		$query = $this->db->get_where('pos_e_login',array('user_id' => $user_id, 'user_name' => $username,'password' => $hash_pass,'account_no' => $acc_no,'user_status' => 10));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			if($row['count(*)'] > 0)
			{
				// update current login time to db wrt user
				$data = array('last_login' => mdate('%Y-%m-%d %H:%i:%s', now()));				
				$this->db->where('user_id', $user_id);
				if($this->db->update('pos_e_login', $data))
				{
					$this->db->select('location');
					$query = $this->db->get_where('pos_e_login',array('user_id' => $user_id, 'user_name' => $username,'password' => $hash_pass,'account_no' => $acc_no,'user_status' => 10));
					$row = $query->row_array();
					return ($row['location'] == '') ? 'ALL OUTLETS' : $row['location'];
				}
			} else {
				return false;	
			}
		} else {
			return false;	
		}
	}
	public function get_username_from_name($username,$acc_no)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('user_id');
		$this->db->select('user_name');
		$this->db->select('display_name');
		$query = $this->db->get_where('pos_e_login',array('user_name' => $username, 'account_no' => $acc_no,'user_status' => 10));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$user =  array($row['user_id'],$row['user_name'],$row['display_name']);
			return $user;
		} else {
			return array(NULL,NULL,NULL);	
		}		
	}
	public function get_username_from_email($user_mail,$acc_no)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('user_id');
		$this->db->select('user_name');
		$this->db->select('display_name');
		$query = $this->db->get_where('pos_e_login',array('user_mail' => $user_mail, 'account_no' => $acc_no,'user_status' => 10));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$user =  array($row['user_id'],$row['user_name'],$row['display_name']);
			return $user;
		} else {
			return array(NULL,NULL,NULL);	
		}		
	}
	
	public function check_validity($acc_no)
	{
		$this->db->select('*');
		$query = $this->db->get_where('pos_a_master',array('account_no' => $acc_no));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			if($row['usage_percent'] > 100)
			{
				return 0;
			}
			if($row['credit_amount'] <= 0)
			{
				return 0;
			}
			if($row['validity'] <= 0)
			{
				return 0;
			}
			if($row['account_stat'] != 10) // if aaccount not active
			{
				return 0;
			}
			return 1;
		} else {
			return 0;	
		}
	}
	public function get_id_privelage($username,$userpassword,$acc_no)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('privelage');
		$this->db->select('user_id');
		$this->db->select('user_mail');
		$this->db->select('is_delete');
		$query = $this->db->get_where('pos_e_login',array('user_name' => $username, 'password' => $userpassword,'account_no' => $acc_no,'user_status' => 10));
		if($query->num_rows() > 0){
			$array = array();
			foreach($query->result_array() as $row)
			{
				$array['privelage'] = $row['privelage'];
				$array['user_id'] = $row['user_id'];
				$array['user_mail'] = $row['user_mail'];
				$array['is_delete'] = $row['is_delete'];
			}
			return $array;
		} else {
			return false;	
		}
	}
	public function get_timezone_loc_plan_validity($acc_no)
	{
		$this->db->select('*');		
		$this->db->from('pos_a_master as a');
		$this->db->join('pos_1a_account_type as b','b.index_id = a.account_type');
		$this->db->join('pos_2_userplans as c','c.plan_id = a.plan_code');
		$this->db->join('pos_1a_timezone as d','d.zone_id = a.timezone');
		$this->db->where('a.account_no',$acc_no);
		$this->db->where('hex(b.account_code) = a.account_type'); // info: secret security: a.account type is hex(b.account_code)
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{			
			$row = $query->row_array();
			// waiting, get usage -> idea is to set an incrementer or decrementer value in db->pos_a_master->usage_in_bytes column for getting storage size
			// this value has to be updated by cron every day
			// same as validity and credit amount
			// so now, check validity, credit amount, during ajax post on sales works easy on sales webservice API
			// return value in bytes
			
			$base = log($row['usage_in_bytes'], 1024);
			$suffixes = array('', 'kb', 'Mb', 'Gb', 'Tb','Pb');   
			$usage_str = round(pow(1024, $base - floor($base)), 2) . $suffixes[floor($base)];
			$array = array(
						'tz' => $row['timezone'],
						'timezone_name' => $row['zone_name'],
						'plan_registers' => !is_numeric($row['registers']) ? constant($row['registers']) : $row['registers'],

						'plan_str' => $row['plan_code'],
						'plan_store_handle' => $row['stores_handle'],
						'plan_stk_limit' => !is_numeric($row['stock_limit']) ? constant($row['stock_limit']) : $row['stock_limit'],
						'plan_user_limit' => !is_numeric($row['users_limit']) ? constant($row['users_limit']) : $row['users_limit'],
						'plan_cust_db_count' => !is_numeric($row['customer_db_count'])? constant($row['customer_db_count']) : $row['customer_db_count'],
						'plan_registers' => !is_numeric($row['registers']) ? constant($row['registers']) : $row['registers'],


						'plan_code' => $row['plan_code'],
						'validity' => $row['validity'],
						'usage_bytes' => $row['usage_in_bytes'],
						'usage_str' => $usage_str,
						'memory_limit_gb' => is_numeric($row['memory_limit_gb']) ? $row['memory_limit_gb'] : constant($row['memory_limit_gb']),
						'usage_percent' => $row['usage_percent'],
						'credit_amount' => $row['credit_amount'],
						'currency' => $row['currency'],
						'fbid' => $row['guest_fbid'],
						'account_code' => $row['account_code'],
						'account_string' => $row['account_string'],
						);
			return $array;			
		} else {
			return false;
		}
	}
	public function get_hoster_details()
	{
		$param = array(9884856788);
		$query = $this->db->query("select * from pos_1a_settings where setting_id = ?",$param);
		if($query->num_rows() > 0){
			$array = array();
			foreach($query->result_array() as $row)
			{
				$array['pos_hoster_hotline'] = $row['pos_hotline'];
				$array['pos_hoster_cmp'] = $row['pos_cmp'];
				$array['pos_hoster_web'] = $row['pos_web'];
				$array['pos_hoster_email'] = $row['pos_email'];
				$array['pos_hoster_fb'] = $row['pos_fbid'];
				$array['pos_hoster_ver'] = $row['pos_version'];
				$array['pos_hoster_year'] = $row['pos_year'];
			}
			return $array;
		} else {
			return false;	
		}
	}
	public function get_userplan($plan_id,$acc_no)
	{
		$query = $this->db->get_where('pos_2_userplans',array('plan_id' => $plan_id));
		if($query->num_rows() > 0){
			$array = array();
			foreach($query->result_array() as $row)
			{
	
				$array['plan_str'] = $row['plan_code'];
				$array['plan_store_handle'] = $row['stores_handle']; 
				$array['plan_stk_limit'] = !is_numeric($row['stock_limit']) ? constant($row['stock_limit']) : $row['stock_limit'];
				$array['plan_user_limit'] = !is_numeric($row['users_limit']) ? constant($row['users_limit']) : $row['users_limit'];
				$array['plan_cust_db_count'] = !is_numeric($row['customer_db_count'])? constant($row['customer_db_count']) : $row['customer_db_count'];
				$array['plan_registers'] = !is_numeric($row['registers']) ? constant($row['registers']) : $row['registers'];
			}
			return $array;
		} else {
			return false;	
		}
	}
	public function get_billing_data($acc_no,$loc_id)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('*');
		$query = $this->db->get_where('pos_b_locations',array('account_no' => $acc_no, 'loc_id' => $loc_id));
		if($query->num_rows() > 0){
			$array = array();
			foreach($query->result_array() as $row)
			{
				$array['addr1'] = $row['guest_addr_l1'];
				$array['addr2'] = $row['guest_addr_l2'];
				$array['ll'] = $row['guest_ll'];
				$array['city'] = $row['guest_city'];
				$array['postalcode'] = $row['guest_postalcode'];
				$array['state'] = $row['guest_state'];
				$array['country'] = $row['guest_country'];
				$array['web'] = $row['guest_web'];
				$array['cemail'] = $row['guest_email'];
			}
			return $array;
		} else {
			return false;	
		}
	}
	public function foldersize($path)
	{
		$total_size = 0;
		if(is_dir($path))
		{
			$files = scandir($path);
			$cleanPath = rtrim($path, '/'). '/';
			foreach($files as $t) {
				if ($t<>"." && $t<>"..") {
					$currentFile = $cleanPath . $t;
					if (is_dir($currentFile)) {
						$size = $this->foldersize($currentFile);
						$total_size += $size;
					}
					else {
						$size = filesize($currentFile);
						$total_size += $size;
					}
				}   
			}
			return $total_size;
		} else {
			return 0;	
		}
	}
}
?>