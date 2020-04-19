<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Account_model extends CI_Model
{
	public function get_account_data($plan_code)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('*');
		$query = $this->db->get_where('pos_2_userplans',array('plan_id' => $plan_code));
		if($query->num_rows() > 0){
			$array = array();
			foreach ($query->list_fields() as $field)
			{
				foreach($query->result_array() as $row)
				{
					$array[$field] = $row[$field];
				}

			} 
			return $array;
		} else {
			return array();	
		}
	}
	public function get_account_all()
	{
		$this->db->select('*');
		$this->db->order_by('plan_order');
		$query = $this->db->get_where('pos_2_userplans',array('plan_status' => 1));
		if($query->num_rows() > 0){
			foreach($query->result() as $row)
			{
				$array[$row->plan_id]['plan_id'] = $row->plan_id;
				$array[$row->plan_id]['plan_code'] = $row->plan_code;
				$array[$row->plan_id]['stores_handle'] = $row->stores_handle;
				$array[$row->plan_id]['max_stores'] = $row->max_stores;
				$array[$row->plan_id]['stock_limit'] = $row->stock_limit;
				$array[$row->plan_id]['users_limit'] = $row->users_limit;
				$array[$row->plan_id]['customer_db_count'] = $row->customer_db_count;
				$array[$row->plan_id]['registers'] = $row->registers;
				$array[$row->plan_id]['monthly_price'] = $row->monthly_price;
				$array[$row->plan_id]['yearly_disc'] = $row->yearly_disc;
				$array[$row->plan_id]['half_early_disc'] = $row->half_early_disc;
				$array[$row->plan_id]['quarter_early_disc'] = $row->quarter_early_disc;
				$array[$row->plan_id]['register_cost_monthly'] = $row->register_cost_monthly;
				$array[$row->plan_id]['register_cost_quarter_yearly'] = $row->register_cost_quarter_yearly;
				$array[$row->plan_id]['register_cost_half_yearly'] = $row->register_cost_half_yearly;
				$array[$row->plan_id]['register_cost_yearly'] = $row->register_cost_yearly;
				$array[$row->plan_id]['memory_limit_gb'] = $row->memory_limit_gb;
				$array[$row->plan_id]['ecommerce'] = $row->ecommerce;
				$array[$row->plan_id]['support'] = $row->support;
				$array[$row->plan_id]['plan_order'] = $row->plan_order;
				$array[$row->plan_id]['plan_status'] = $row->plan_status;
			}
			return $array;
		} else {
			return array();	
		}
	}
	public function get_merchant_account_details($host)
	{
		$this->db->select('a.account_no');
		$this->db->select('a.subdomain');
		$this->db->select('a.cmp_name');
		$this->db->select('a.timezone');
		$this->db->select('a.currency');
		$this->db->select('a.validity');
		$this->db->select('c.index_id as account_type_id');
		$this->db->select('c.account_string as account_type');
		$this->db->select('c.account_code as account_type_code');
		$this->db->select('b.plan_id');
		$this->db->select('b.plan_code');
		$this->db->select('a.account_stat');
		$this->db->from('pos_a_master as a');
		$this->db->join('pos_2_userplans as b','b.plan_id = a.plan_code');
		$this->db->join('pos_1a_account_type as c','c.index_id = a.account_type');
		$this->db->where('a.subdomain',$host);
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
	public function get_account_plan_code($host)
	{
		$this->db->select('plan_code');
		$this->db->select('currency');
		$rows = $this->db->get_where('pos_a_master',array('subdomain' => $host));
		if($rows!=false){
			$row = $rows->row_array();
			$array = array($row['plan_code'],$row['currency']);
			return $array;
		} else {
			return array(0,0);	
		}
		
	}
	public function recommended_plans($prd_count,$user_count,$cust_count,$outlet_count)
	{
		$this->db->select('plan_id');
		$this->db->select('plan_code');
		$this->db->from('pos_2_userplans');
		$this->db->where("(stock_limit >= ".$prd_count." or stock_limit = 'INF')");
		$this->db->where("(users_limit >= ".$user_count." or users_limit = 'INF')");
		$this->db->where("(customer_db_count >= ".$cust_count." or customer_db_count = 'INF')");
		$this->db->where("(max_stores >= ".$outlet_count." or max_stores = 'INF')");
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[$row->plan_id] = $row->plan_code;
			}
		} else {
			$array = array();
		}
		return $array;
	}
	public function current_plan_status($acc)
	{
		$this->db->select('*');
		$this->db->from('pos_a_master as a');
		$this->db->join('pos_1a_account_type as b','a.account_type = b.index_id');
		$this->db->where('a.account_no',$acc);
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
	public function find_plan_pricing($plan_id,$acc)
	{
		$reg_count = $this->register_model->register_count($acc) - 1;
		$this->db->select('*, "'.$reg_count.'" as reg_count',false);
		$this->db->where('plan_id',$plan_id);
		$this->db->from('pos_2_userplans');	
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
	public function find_term_pricing($plan_index,$termed,$acc)
	{
		$reg_count = $this->register_model->register_count($acc) - 1;
		$term_price_array = array(1 => 'monthly_price',3 => 'quarter_early_disc', 6 => 'half_early_disc', 12 => 'yearly_disc');
		$term_reg_price_array = array(1 => 'register_cost_monthly',3 => 'register_cost_quarter_yearly', 6 => 'register_cost_half_yearly', 12 => 'register_cost_yearly');
		$this->db->select($term_price_array[$termed] .' as plan_price');	
		$this->db->select('"'.$reg_count.'" as reg_count',false);
		$this->db->select($term_reg_price_array[$termed] .' as reg_price');	
		$this->db->select($term_reg_price_array[$termed] .' * '.$reg_count.' as tot_reg_price',false);	
		$this->db->select('monthly_price * '.$termed.' - '.$term_price_array[$termed].' * '.$termed.' as save_price',false);
		$this->db->select('(register_cost_monthly * '.$termed.' - '.$term_reg_price_array[$termed].' * '.$termed.') * '.$reg_count.' as save_reg_price',false);
		$this->db->select(
				'monthly_price * '.$termed.' - '.$term_price_array[$termed].' * '.$termed. 
				' + (register_cost_monthly * '.$termed.' - '.$term_reg_price_array[$termed].' * '.$termed.') * '.$reg_count.'
				as total_savings',false
				);
		$this->db->select($term_reg_price_array[$termed] .' * '.$reg_count.' + '.$term_price_array[$termed].' as total_pricing',false);
		$this->db->select('('.$term_reg_price_array[$termed] .' * '.$reg_count.' + '.$term_price_array[$termed].') * '.$termed.' as term_pricing',false);
		$this->db->select('plan_code');
		$this->db->from('pos_2_userplans');
		$this->db->where('plan_id' , $plan_index);
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
	public function trash_form_trial_data($data)
	{
		if(array_key_exists('delete',$data))
		{
			$this->db->trans_begin();
			$this->db->trans_start();
			$delete_tables_array = array();
			$delete_cust_grp_table = array();
			foreach($data['delete'] as $z_value)
			{
				if($z_value == 'only_sales')
				{
					//delete only sales
					// waiting to delete transaction table
					$delete_tables_array[] = 'pos_k2_transaction';
				}
				if($z_value == 'products_and_sales')
				{
					//delete prd and sales
					$delete_tables_array[] = 'pos_i1_products';
				}
				if($z_value == 'only_customer')
				{
					//delete only customers and groups
					$delete_cust_table['cust_table'] = 'pos_i2_customers';
					$delete_cust_grp_table['cust_grp_table'] = 'pos_i2_b_customer_group';
				}
			}
			$this->db->where('account_no', $data['acc']);
			$this->db->delete($delete_tables_array);		
			if(array_key_exists('cust_table',$delete_cust_table))
			{
				$this->db->where('cust_stat !=', 40);
				$this->db->where('account_no', $data['acc']);
				$this->db->delete('pos_i2_customers'); 				
			}
			if(array_key_exists('cust_grp_table',$delete_cust_grp_table))
			{
				$this->db->where('is_delete', 10);
				$this->db->where('account_no', $data['acc']);
				$this->db->delete('pos_i2_b_customer_group'); 				
			}
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE)
			{
				return 0;
			} else {
				return 1;	
			}
		} else {
			return 0;	
		}
	}
	public function form_delete_account($data)
	{
		$this->db->insert('pos_f_master_precancel',$data);	
	}
	public function find_code_discount($code,$plan_index,$termed,$acc)
	{
		$pricing_array = $this->find_term_pricing($plan_index,$termed,$acc);
		$time = mdate('%Y-%m-%d %H:%i:%s', now());
		//$time = '1448927999';
		$this->db->select('plan_prom_discount');
		$this->db->from('pos_2_userplans_promotions');
		$this->db->where('plan_prom_start'.' <=', $time);
		$this->db->where('plan_prom_end'.' >=', $time);
		$this->db->where('plan_prom_code',$code);
		$this->db->where('prom_status',1);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return array('status' => 'Gotcha! You`re there..','discount' => $row['plan_prom_discount'],'term_pricing' => $pricing_array['term_pricing']);	
		} else {
			return array('status' => 'Invalid offer code','discount' => 0,'term_pricing' => $pricing_array['term_pricing']);	
		}
	}
	public function get_plan_promotions_if_today()
	{
		$time = mdate('%Y-%m-%d %H:%i:%s', now());
		$this->db->select('*');
		$this->db->from('pos_2_userplans_promotions');
		$this->db->where('plan_prom_start'.' <=', $time);
		$this->db->where('plan_prom_end'.' >=', $time);
		$this->db->where('prom_status',1);
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
}