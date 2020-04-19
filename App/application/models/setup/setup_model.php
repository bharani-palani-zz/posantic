<?php
class Setup_model extends CI_Model
{
	public function business_type()
	{
		$query = $this->db->get('pos_1a_business_type');	
		if($query->num_rows() > 0){
			$array = array();
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
	public function M_setup_currency()
	{
		$this->db->select('country_code');
		$this->db->select('currency_name');
		$this->db->select('symbol');
		$rows = $this->db->get('pos_1a_currency');
		if($rows->num_rows() > 0){
			foreach($rows->result() as $row){
				$array[] = array($row->country_code,$row->currency_name,$row->symbol);				
			}
			return $array;
		} else {
			return array('','','');	
		}
	}
	public function get_location_wrt_id($id)
	{
		$this->db = $this->load->database('default', TRUE);  
		$query = $this->db->query('select location from pos_b_locations where loc_id = '.$id);
		if ($query->num_rows() > 0)
		{
		   $row = $query->row_array();
		   $location = $row['location'];
		}
		return 	$location;
	}
	public function M_get_masterdata($acc) // done a bad job of holding numbers as key values.. but works good
	{
		$this->db->select('pos_a_master.timezone as tz,
						  pos_a_master.validity as val,
						  pos_a_master.plan_code as pcode,
						  pos_a_master.contact_name as contact_name, 
						  pos_a_master.contact_mobile as contact_mobile,
						  pos_a_master.contact_email as contact_email,
						  pos_a_master.contact_addr1 as contact_addr1,
						  pos_a_master.contact_addr2 as contact_addr2,
						  pos_a_master.contact_city as contact_city,
						  pos_a_master.contact_state as contact_state,
						  pos_a_master.contact_postalcode as contact_postalcode,
						  pos_a_master.contact_country as contact_country,
						  pos_2_userplans.stores_handle as store_handle,
						  pos_2_userplans.stock_limit as stock_limit,
						  pos_a_master.cmp_name as cmp_name,
						  pos_a_master.subdomain as subdomain,
						  pos_1a_account_type.account_string as account_mode,
						  pos_1a_account_type.account_code as account_code,
						  pos_1a_account_type.index_id as account_index,
						  pos_2_userplans.plan_code as plan_str,
						  pos_2_userplans.memory_limit_gb as memory_limit,
						  pos_a_master.latitude as latitude,
						  pos_a_master.longitude as longitude,
						  pos_a_master.created_at as created_at
						  ');
		$this->db->from('pos_a_master');						
		$this->db->join('pos_2_userplans', 'pos_2_userplans.plan_id = pos_a_master.plan_code');
		$this->db->join('pos_1a_account_type','pos_1a_account_type.index_id = pos_a_master.account_type');
		$this->db->where(array('pos_a_master.account_no' => $acc));
		$rows = $this->db->get();
		if($rows != false){
			foreach($rows->result() as $row)
			{
				$pcode = $row->pcode;
				$array = array(
					$row->tz,$row->val,$row->pcode,
					$row->contact_name,
					$row->contact_mobile,
					$row->contact_email,
					$row->contact_addr1,
					$row->contact_addr2,
					$row->contact_city,
					$row->contact_state,
					$row->contact_postalcode,
					$row->contact_country,
					$row->store_handle,
					is_numeric($row->stock_limit) ? $row->stock_limit : constant($row->stock_limit),
					$row->cmp_name,
					$row->subdomain
				);
			}
			$data = $this->login_model->get_userplan($pcode,$acc);
			array_push($array ,$data['plan_user_limit'],$data['plan_cust_db_count'],$row->account_mode,$row->account_code,$row->account_index,$row->plan_str,
				is_numeric($row->memory_limit) ? $row->memory_limit : constant($row->memory_limit),					
				$row->latitude,$row->longitude,$row->created_at);
			return $array;
		}
	}
	public function M_max_plan()
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select_max('plan_id');
		$query = $this->db->get('pos_2_userplans');
		if($query->num_rows() > 0)
		{
			$array = $query->row_array(0);
			$val = $array['plan_id'];
			return $val;
		} else {
			return 0;	
		}
		$this->db->close();		
	}
	public function M_get_rounding_methods()
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('round_id as id');
		$this->db->select('method_name as method');
		$query = $this->db->get('pos_1d_rounding_methods');
		$array = array();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row){
				$array['Rounding Methods'][$row->id] = $row->method;
			}
			return $array;
		}
		$this->db->close();		
		
	}
	public function get_countries_select($digit = 2)
	{
		switch ($digit) {
		   case 2:
				 $digit_val = 'country_code_2digit';
				 break;
		   case 3:
				 $digit_val = 'country_code_3digit';
				 break;
		}
		$this->db->select($digit_val.' as ids');	
		$this->db->select('country_name');	
		$this->db->order_by('country_name');	
		$query = $this->db->get('pos_1a_countries');	
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row){
				$array[$row->ids] = ucwords(strtolower($row->country_name));
			}
		} else {
			$array['NULL'] = NULL;	
		}
		return $array;
	}
	public function get_payment_dynamic_select($load_code_for)
	{
		if($load_code_for == 'COUNTRY_CODES_2_DIGIT')
		{
			$response_array = $this->get_countries_select(2); // for ebs and ccavenue
		} else if($load_code_for == 'COUNTRY_CODES_3_DIGIT') {
			$response_array = $this->get_countries_select(3); // may be later for other payment gateways
		} else if($load_code_for == 'SOCKET_CODES') {
			$response_array = array('USB' => 'USB', 'WIRELESS' => 'WIRELESS'); // for plutus only
		} else {
			$response_array = array('' => NULL);
		}
		return $response_array;
	}
	public function countries_assoc()
	{
		$this->db->select('*');	
		$this->db->order_by('country_name');	
		$query = $this->db->get('pos_1a_countries');	
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row){
				$array[$row->country_code_2digit] = ucwords(strtolower($row->country_name));
			}
		} else {
			$array['NULL'] = NULL;	
		}
		return $array;
	}
	public function update_account($data)
	{
		$update = array(
					   'cmp_name' => $data['company_name'],
					   'timezone' => $data['tz'],
					   'currency' => $data['curr'],
					   'guest_fbid' => $data['fb'],
					   'contact_name' => $data['contact_name'],
					   'contact_mobile' => $data['contact_mobile'],
					   'contact_email' => $data['contact_email'],
					   'contact_addr1' => $data['contact_addr1'],
					   'contact_addr2' => $data['contact_addr2'],
					   'contact_city' => $data['contact_city'],
					   'contact_state' => $data['contact_state'],
					   'contact_postalcode' => $data['contact_postalcode'],
					   'contact_country' => $data['contact_country'],
					   'latitude' => $data['latitude'],
					   'longitude' => $data['longitude']
					);
		
		$this->db->where(array('account_no' => $data['merchant_id']));
		if($this->db->update('pos_a_master', $update))
		{
			return 1;	
		} else {
			return 0;
		}		
	}
	public function loyalty_stat($acc)
	{
		$query = $this->db->get_where('pos_e_loyalty',array('account_no' => $acc));	
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
	public function update_loyalty($data)
	{
		$update = array(
					   'status' => $data['enable_loyalty'],
					   'sale_value' => $data['loyalty_sale'],
					   'reward_value' => $data['loyalty_reward'],
					);
		$this->db->where(array('l_id' => $data['hid_id'], 'account_no' => $data['acc']));
		if($this->db->update('pos_e_loyalty', $update))
		{
			return 1;	
		} else {
			return 0;
		}		
	}
	
}
?>