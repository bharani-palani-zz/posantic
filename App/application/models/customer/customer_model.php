<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Customer_model extends CI_Model
{
	public $account_custdb_handle = null;
    public function __construct() 
    {
        parent::__construct();
		$bonafide = $this->master_model->plan_bonafide($this->session->userdata('acc_no'));
		$this->account_custdb_handle = $bonafide['customer_db_count'];		
    }
	public function customer_count()
	{
		$this->db->select('count(*)');
		$query = $this->db->get_where('pos_i2_customers',array('account_no' => $this->session->userdata('acc_no')));
		if ($query->num_rows() > 0)
		{
		   $row = $query->row_array();
		   $count = $row['count(*)'];
		   return $count;
		} else {
		   return 0;	
		}
	}
	public function check_loyalty_set($acc)
	{
		$this->db->select('count(*)');
		$query = $this->db->get_where('pos_e_loyalty',array('account_no' => $acc,'status' => 10));
		if($query->num_rows() > 0) { 
			$row = $query->row_array();
			return $row['count(*)'] > 0 ? true : false;
		} else {
			return false;
		}				
	}
	public function loyalty_params($acc)
	{
		$this->db->select('sale_value');
		$this->db->select('reward_value');
		$query = $this->db->get_where('pos_e_loyalty',array('account_no' => $acc,'status' => 10));
		if($query->num_rows() > 0) { 
			$row = $query->row_array();
			return array($row['sale_value'],$row['reward_value']);
		} else {
			return array(NULL,NULL);
		}				
	}
	public function group_combo($acc)
	{
		$this->db->select('*');
		$this->db->order_by('updated_at');
		$query = $this->db->get_where('pos_i2_b_customer_group',array('account_no' => $acc));
		if($query->num_rows() > 0) 
		{ 
			foreach($query->result() as $row){
				$array[$row->grp_index] = $row->group_name;
			}			
			return $array;
		} else {
			return array('' => '');	
		}
	}
	public function add_cust_group($grp_name,$acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_i2_b_customer_group',array('account_no' => $acc,'group_name' => $grp_name));
		if($query->num_rows() > 0) { 
			$row = $query->row_array();
			if($row['counted'] < 1)
			{		
				$array = array(
							'grp_index' => $this->taxes_model->make_single_uuid(),
							'group_name' => $grp_name,
							'is_delete' => 10,
							'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
							'account_no' => $acc
							);
				if($this->db->insert('pos_i2_b_customer_group',$array))
				{
					return 1;	
				} else {
					return 0;	
				}
			} else {
				return 2;	
			}
		} else {
			return 0;	
		}
	}
	public function group_list($acc)
	{
		$query = $this->db->get_where('pos_i2_b_customer_group',array('account_no' => $acc));
		if($query->num_rows() > 0) 
		{ 
			foreach ($query->list_fields() as $field)
			{
				foreach($query->result_array() as $row)
				{
					$array[$field][] = $row[$field];
				}
			} 
			return $array;				
		} else {
			return array();
		}
	}
	public function get_groupid_like_name($grp_name,$acc)
	{
		$query = $this->db->get_where('pos_i2_b_customer_group',array('account_no' => $acc,'group_name' => $grp_name));
		if($query->num_rows() > 0) 
		{ 
		   $row = $query->row_array();
		   return $row['grp_index'];
		} else {
			$query = $this->db->get_where('pos_i2_b_customer_group',array('account_no' => $acc,'is_delete' => 20));
			if($query->num_rows() > 0) 
			{ 
				$row = $query->row_array();
				return $row['grp_index'];
			} else {
				return NULL;	
			}
		}
		
	}
	public function make_customer_code()
	{
		$ccode = strtoupper(dechex(now()).dechex($this->customer_count()));
		return $ccode;
	}
	public function insert_customer($data)
	{
		$tot_customers = $this->customer_count($data['acc']);
		$stk_limit = $this->account_custdb_handle;
		if(($tot_customers + 1) <= $stk_limit)
		{		
			$insert = array(
							'cust_id' => $this->taxes_model->make_single_uuid(),
							'group_id' => $data['cust_group'],
							'c_code' => $data['cust_code'] == "" ? $this->make_customer_code() : $data['cust_code'],
							'c_company' => $data['comp_name'],
							'c_dob' => $data['cust_dob']['yy']."-".$data['cust_dob']['mm']."-".$data['cust_dob']['dd'],
							'c_anniversary' => $data['cust_ann']['yy']."-".$data['cust_ann']['mm']."-".$data['cust_ann']['dd'],
							'c_name' => $data['cust_name'],
							'c_gender' => $data['cust_gender'] == "" ? "" : $data['cust_gender'],
							'c_address_l1' => $data['cust_addrr_1'],
							'c_address_l2' => $data['cust_addrr_2'],
							'c_city' => $data['cust_city'],
							'c_state' => $data['cust_state'],
							'c_pincode' => $data['cust_pcode'],
							'c_country' => $data['cust_country'],
							'c_mobile' => $data['cust_mobile'],
							'c_ll' => $data['cust_ll'],
							'c_email' => $data['cust_email'],
							'c_fb_id' => $data['cust_fb'],
							'c_website' => $data['cust_web'],
							'c_desc' => $data['cust_desc'],
							'c_lat' => is_numeric($data['latitude']) ? $data['latitude'] : NULL,
							'c_long' => is_numeric($data['longitude']) ? $data['longitude'] : NULL,
							'enable_loyalty' => $data['cust_enable_loyalty'],
							'cust_stat' => 100,
							'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
							'account_no' => $data['acc'],
							);		
			if($this->db->insert('pos_i2_customers', $insert))
			{
				return 1;
			} else {
				return 0;
			}
		} else {
			return 2;
		}
	}
	public function update_customer($data)
	{
		$update = array(
						'group_id' => $data['cust_group'],
						'c_code' => $data['cust_code'] == "" ? $this->make_customer_code() : $data['cust_code'],
						'c_company' => $data['comp_name'],
						'c_dob' => $data['cust_dob']['yy']."-".$data['cust_dob']['mm']."-".$data['cust_dob']['dd'],
						'c_anniversary' => $data['cust_ann']['yy']."-".$data['cust_ann']['mm']."-".$data['cust_ann']['dd'],
						'c_name' => $data['cust_name'],
						'c_gender' => $data['cust_gender'],
						'c_address_l1' => $data['cust_addrr_1'],
						'c_address_l2' => $data['cust_addrr_2'],
						'c_city' => $data['cust_city'],
						'c_state' => $data['cust_state'],
						'c_pincode' => $data['cust_pcode'],
						'c_country' => $data['cust_country'],
						'c_mobile' => $data['cust_mobile'],
						'c_ll' => $data['cust_ll'],
						'c_email' => $data['cust_email'],
						'c_fb_id' => $data['cust_fb'],
						'c_website' => $data['cust_web'],
						'c_desc' => $data['cust_desc'],
						'enable_loyalty' => $data['cust_enable_loyalty'],
						'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
						);		
		$this->db->where('cust_id',$data['cust_id']);
		$this->db->where('account_no',$data['acc']);
		if($this->db->update('pos_i2_customers', $update))
		{
			return 1;
		} else {
			return 0;
		}
	}
	public function update_customer_coordinates($data)
	{
		$update = array(
						'c_lat' => $data['c_lat'],
						'c_long' => $data['c_long'],
						'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
						);		
		$this->db->where('cust_id',$data['cust_id']);
		$this->db->where('account_no',$data['acc']);
		if($this->db->update('pos_i2_customers', $update))
		{
			return 1;
		} else {
			return 0;
		}
	}
	public function get_customer_sql($search,$limit, $start, $acc,$get_array)
	{
		$this->db->select('a.cust_id as cust_id');
		$this->db->select('a.c_name as cust_name');
		$this->db->select('a.c_code as cust_code');
		$this->db->select('a.c_company as cust_comp');
		$this->db->select('a.c_dob as cust_dob');
		$this->db->select('a.c_anniversary as cust_ann');
		$this->db->select('b.group_name as cust_group');
		$this->db->select('a.c_city as cust_city');
		$this->db->select('0 as cust_trade',false);
		$this->db->select('0 as cust_credit',false);
		$this->db->from('pos_i2_customers as a');
		$this->db->join('pos_i2_b_customer_group as b','b.grp_index = a.group_id');
		$this->db->join('pos_a_master as c','c.account_no = a.account_no');
		$this->db->where('a.cust_stat',100);
		$this->db->where('c.account_no',$acc);
		$this->db->where('(a.c_name like "%'.$search.'%" or a.c_mobile like "%'.$search.'%" or a.c_code like "%'.$search.'%")',NULL,false);
		if(count($get_array) > 0)
		{
			if(isset($get_array['cust_group']))
			{
				if($get_array['cust_group'] != "ALL" && !empty($get_array['cust_group']))
				{
					$this->db->where('a.group_id', $get_array['cust_group']); 
				}
			}
			if(isset($get_array['cust_dob']))
			{
				if(strlen($get_array['cust_dob']) > 0)
				{
					$this->db->where('a.c_dob', $get_array['cust_dob']); 
				}
			}
			if(isset($get_array['cust_ann']))
			{
				if(strlen($get_array['cust_ann']) > 0)
				{
					$this->db->where('a.c_anniversary', $get_array['cust_ann']); 
				}
			}
			if(isset($get_array['date_after']))
			{
				if(strlen($get_array['date_after']) > 0)
				{
					$this->db->where('a.updated_at >=', $get_array['date_after']); 
				}
			}
			if(isset($get_array['date_before']))
			{
				if(strlen($get_array['date_before']) > 0)
				{
					$this->db->where('a.updated_at <=', $get_array['date_before']); 
				}
			}
		}
		//waiting
		//join customer data from transaction table to YTD trade and credit		
		
		if($limit > 0)
		{
			$this->db->limit($limit,$start);
		}
		if($get_array['sort'])
		{
			$flow = $get_array['flow'] ? $get_array['flow'] : "desc";
			$this->db->order_by($get_array['sort'], $flow); 
		}
		$query = $this->db->get();
		return $query;		
	}
	public function get_customer_tot_rows($search,$limit, $start, $acc,$get_array)
	{
		$query = $this->get_customer_sql($search,$limit, $start, $acc,$get_array);
		return $query->num_rows();
	}
	public function get_customers($search,$limit, $start, $acc,$get_array)
	{
		$query = $this->get_customer_sql($search,$limit, $start, $acc,$get_array);
		if($query->num_rows() > 0)
		{
			$array = array();
			foreach($query->result() as $row)
			{
				$array['cust_id'][] = $row->cust_id;
				$array['cust_name'][] = $row->cust_name;
				$array['cust_code'][] = $row->cust_code;
				$array['cust_comp'][] = $row->cust_comp;
				$array['cust_dob'][] = $row->cust_dob;
				$array['cust_ann'][] = $row->cust_ann;
				$array['cust_group'][] = $row->cust_group;
				$array['cust_city'][] = $row->cust_city;
				$array['cust_trade'][] = $row->cust_trade;
				$array['cust_credit'][] = $row->cust_credit;
			}
			return $array;
		} else {
			return array('cust_id' => NULL);	
		}
	}
	public function customer_data($id,$acc)
	{
		$this->db->select('*');
		$this->db->from('pos_i2_customers as a');
		$this->db->join('pos_i2_b_customer_group as b','a.group_id = b.grp_index');
		$this->db->where('a.account_no',$acc);
		$this->db->where('a.cust_id',$id);
		$query = $this->db->get();
		if($query->num_rows() > 0) 
		{ 
			foreach ($query->list_fields() as $field)
			{
				foreach($query->result_array() as $row)
				{
					$array[$field] = $row[$field];
				}
			} 
			return $array;				
		} else {
			return NULL;
		}
	}
	public function delete_customer($id,$acc)
	{
		$this->db->where('cust_id',$id);
		$this->db->where('account_no',$acc);
		if($this->db->update('pos_i2_customers',array('cust_stat' => 120)))
		{
			return 1;
		} else {
			return 0;
		}
	}
	public function get_group_data($grp_id,$acc)
	{
		$query = $this->db->get_where('pos_i2_b_customer_group',array('account_no' => $acc,'grp_index' => $grp_id));
		if($query->num_rows() > 0) 
		{ 
		   $row = $query->row_array();
		   return $row['group_name'];
		} else {
			return '';
		}
	}
	public function update_group($grp_id,$grp_name,$acc)
	{
		$this->db->where('grp_index',$grp_id);
		$this->db->where('account_no',$acc);
		if($this->db->update('pos_i2_b_customer_group', array('group_name' => $grp_name,'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()))))
		{
			return 1;
		} else {
			return 0;
		}
	}
	public function delete_group($id,$acc)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		
		$this->db->select('grp_index');
		$query = $this->db->get_where('pos_i2_b_customer_group',array('is_delete' => 20,'account_no' => $acc));
		if($query->num_rows() > 0) 
		{ 
			$row = $query->row_array();
			$all_id = $row['grp_index'];
			$this->db->where('account_no',$acc);
			$this->db->where('group_id',$id);
			$this->db->update('pos_i2_customers',array('group_id' => $all_id)); // updating to general customers
			
			$this->db->where('account_no',$acc);
			$this->db->where('customer_group',$id);			
		    $this->db->delete('pos_i6_promgroup');

			$this->db->where('account_no',$acc);
			$this->db->where('grp_index',$id);			
		    $this->db->delete('pos_i2_b_customer_group');
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return 0;
		} else {
			return 1;	
		}
	}
	public function download_customers($acc)
	{
		$this->db->select('a.cust_id as id');	
		$this->db->select('a.c_name as customer_name');	
		$this->db->select('b.group_name as group_name');	
		$this->db->select('a.c_company as company_name');	
		$this->db->select('a.c_code as customer_code');	
		$this->db->select('a.c_dob as cust_dob');	
		$this->db->select('a.c_anniversary as cust_anniversary');	
		$this->db->select('a.c_gender as cust_gender');	
		$this->db->select('a.c_address_l1 as cust_address_1');	
		$this->db->select('a.c_address_l2 as cust_address_2');	
		$this->db->select('a.c_city as cust_city');	
		$this->db->select('a.c_state as cust_state');	
		$this->db->select('a.c_pincode as cust_pincode');	
		$this->db->select('a.c_country as cust_country');	
		$this->db->select('a.c_mobile as cust_mobile');	
		$this->db->select('a.c_ll as cust_landline');	
		$this->db->select('a.c_email as cust_email');	
		$this->db->select('a.c_fb_id as cust_facebook');	
		$this->db->select('a.c_website as cust_website');	
		$this->db->select('a.c_desc as cust_description');	
		$this->db->select('a.c_lat as latitude');	
		$this->db->select('a.c_long as longitude');	
		$this->db->select('if(a.enable_loyalty = 30,1,0) as cust_enable_loyalty',false);	
		$this->db->from('pos_i2_customers as a');
		$this->db->join('pos_i2_b_customer_group as b','a.group_id = b.grp_index');
		$this->db->where('a.cust_stat',100);
		$this->db->where('a.account_no',$acc);
		$query = $this->db->get();
		return $query;
	}
}
?>