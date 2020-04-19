<?php
class Outlet_model extends CI_Model
{
	public function outlet_assoc_id($acc)
	{
		$query = $this->db->get_where('pos_b_locations',array('account_no' => $acc));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[$row->loc_id] = $row->location;
			}
			return $array;
		} else {
			return array('' => '');	
		}
	}
	public function get_all_outlets($acc)
	{
		$this->db->select('a.location,
						  a.loc_id,
						  d.tax_name as outlet_tax,
						  if( 
						   d.is_group = 20, d.tax_val, (  
						   SELECT  
						   sum(pos_a_taxes.tax_val) 
						   FROM pos_a_taxes  
						   join pos_a_taxes_group on pos_a_taxes.tax_id = pos_a_taxes_group.assoc_tax_id  
						   where pos_a_taxes_group.parent_id = a.`outlet_tax`  
						   )  
						   ) as tax_val,
						   if(c.reg_stat = 30,c.reg_code,null) as reg_code, 
						   if(c.reg_stat = 30,c.reg_id,null) as reg_id, 
						   e.template_name, 
						   e.template_id,
						   f.quick_index,
						   f.quickey_name
						',false);
		$this->db->from('pos_b_locations as a');
		$this->db->join('pos_a_master as b', 'b.account_no = a.account_no','left');
		$this->db->join('pos_j1_registers as c', 'a.loc_id = c.location','left');
		$this->db->join('pos_a_taxes as d', 'd.tax_id = a.outlet_tax','left');
		$this->db->join('pos_c_reciept_template as e', 'e.template_id = c.receipt_template','left');
		$this->db->join('pos_i7_quickeys as f','f.quick_index = c.quickey_template','left');
		$this->db->where('a.outlet_stat = ',30);
		$this->db->where(array('a.account_no' => $acc));
		$this->db->order_by('location');
		$query = $this->db->get();
		if($query!=false){
			foreach($query->result() as $row)
			{
				$array[$row->loc_id]['outlet_str'] = $row->location;
				$array[$row->loc_id]['reg_id'][] = $row->reg_id;
				$array[$row->loc_id]['reg_code'][] = $row->reg_code;
				$array[$row->loc_id]['template_name'][] = $row->template_name;
				$array[$row->loc_id]['template_id'][] = $row->template_id;
				$array[$row->loc_id]['quickey_name'][] = $row->quickey_name;
				$array[$row->loc_id]['quickey_index'][] = $row->quick_index;
				$array[$row->loc_id]['outlet_tax'] = $row->outlet_tax;
				$array[$row->loc_id]['tax_val'] = $row->tax_val;
			}
			return $array;
		} else {
			return array();
		}
	}
	public function show_outlet($outlet_id,$acc)
	{
		$this->db->select('pos_b_locations.loc_id as id,
							pos_b_locations.location as loc_str,
							pos_b_locations.guest_addr_l1 as l1,
							pos_b_locations.guest_addr_l2 as l2,
							pos_b_locations.guest_city as city,
							pos_b_locations.guest_postalcode as pcode,
							pos_b_locations.guest_state as state,
							pos_b_locations.guest_country as country,
							pos_b_locations.guest_ll as ll,
							pos_b_locations.guest_email as email,
							pos_a_taxes.tax_name as tax_name,
							pos_b_locations.outlet_tax as outlet_tax
						');	
		$this->db->select('if(pos_a_taxes.is_group = 20,pos_a_taxes.tax_val,
							(
								SELECT 
									sum(pos_a_taxes.tax_val)
								FROM 
									pos_a_taxes
								join pos_a_taxes_group on pos_a_taxes.tax_id = pos_a_taxes_group.assoc_tax_id
								where
									pos_a_taxes_group.parent_id = `pos_b_locations`.`outlet_tax`)
							) as tax_val
						');
		$this->db->from('pos_b_locations');						
		$this->db->join('pos_a_taxes','pos_a_taxes.tax_id = pos_b_locations.outlet_tax');	
		$this->db->where(array('pos_b_locations.account_no' => $acc,'pos_b_locations.loc_id' => $outlet_id,'pos_b_locations.outlet_stat' => 30));
		$rows = $this->db->get();
		if($rows!=false){
			$array = array();
			foreach($rows->result() as $row){
				$array = array('id' => $row->id,'loc_str' => $row->loc_str,'l1' => $row->l1,'l2' => $row->l2,'city' => $row->city,'pcode' => $row->pcode,'state' => $row->state,
								'country' => $row->country,'ll' => $row->ll,'email' => $row->email,'tax_name' => $row->tax_name,'tax_val' => $row->tax_val,
								'outlet_tax' => $row->outlet_tax);
			}
			return $array;
		}
	}
	public function update_save_outlet($data)
	{
		$update = array(
					   'location' => $data['edit_loc_str'],
					   'guest_addr_l1' => $data['edit_l1'],
					   'guest_addr_l2' => $data['edit_l2'],
					   'guest_city' => $data['edit_city'],
					   'guest_postalcode' => $data['edit_pcode'],
					   'guest_state' => $data['edit_state'],
					   'guest_country' => $data['edit_country'],
					   'guest_ll' => $data['edit_ll'],
					   'guest_email' => $data['edit_email'],
					   'outlet_tax' => $data['edit_outlet_tax'],
					);
		
		$this->db->where(array('loc_id' => $data['edit_outlet_id'], 'account_no' => $data['edit_account']));
		if($this->db->update('pos_b_locations', $update))
		{
			return 1;	
		} else {
			return 0;
		}
	}
	public function get_outlet_id_if_like($str,$acc)
	{
		$this->db->select('loc_id');
		$this->db->like('location', $str); 
		$query = $this->db->get_where('pos_b_locations',array('account_no' => $acc,'outlet_stat' => 30));
		if($query->num_rows() > 0) { 
			$row = $query->row_array();
			return $row['loc_id'];
		} else {
			return NULL;
		}				
	}
	public function outlet_count($acc)
	{
		$this->db->select('count(*) as counted');
		$query = $this->db->get_where('pos_b_locations',array('account_no' => $acc,'outlet_stat' => 30));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['counted'];
		} else {
			return false;
		}
	}
	public function add_outlet_and_register($data)
	{
		$outlet_id = $this->taxes_model->make_single_uuid();
		$insert_outlet = array(
					'loc_id' => $outlet_id,
					'location' => $data['outlet_name'],
					'guest_addr_l1' => $data['outlet_addrr1'],
					'guest_addr_l2' => $data['outlet_addrr2'],
					'guest_city' => $data['outlet_city'],
					'guest_postalcode' => $data['outlet_pin'],
					'guest_state' => $data['outlet_state'],
					'guest_country' => $data['outlet_country'],
					'guest_ll' => $data['outlet_ll'],
					'guest_email' => $data['outlet_email'],
					'outlet_tax' => $data['outlet_tax'],
					'outlet_stat' => 30,
					'account_no' => $data['acc']		
						);
		if($this->db->insert('pos_b_locations',$insert_outlet))
		{
			$reg_id = $this->taxes_model->make_single_uuid();
			$insert_reg = array(
							'reg_id' => $reg_id,
							'reg_code' => $data['reg_name'],
							'reg_stat' => 30,
							'is_delete' => 10,
							'email_reciept' => $data['email_rec_stat'],
							'print_reciept' => $data['print_rec_stat'],
							'switch_users' => $data['ask_user_stat'],
							'ask_quotes' => $data['ask_quotes_stat'],
							'billno_sequence' => $data['reg_bill_seq'],
							'billno_prefix' => $data['reg_prefix'],
							'receipt_template' => $data['reg_rec_temp'],
							'quickey_template' => $data['reg_qt_temp'],
							'rounding_method' => $data['reg_bill_round'],
							'location' => $outlet_id,
							'account_no' => $data['acc']				
								);	
			if($this->db->insert('pos_j1_registers',$insert_reg))
			{
				return 1;
			} else {
				return 0;
			}
		}	
	}
	public function add_outlet($data)
	{
		$outlet_id = $this->taxes_model->make_single_uuid();
		$insert_outlet = array(
					'loc_id' => $outlet_id,
					'location' => $data['outlet_name'],
					'guest_addr_l1' => $data['outlet_addrr1'],
					'guest_addr_l2' => $data['outlet_addrr2'],
					'guest_city' => $data['outlet_city'],
					'guest_postalcode' => $data['outlet_pin'],
					'guest_state' => $data['outlet_state'],
					'guest_country' => $data['outlet_country'],
					'guest_ll' => $data['outlet_ll'],
					'guest_email' => $data['outlet_email'],
					'outlet_stat' => 30,
					'outlet_tax' => $data['outlet_tax'],
					'account_no' => $data['acc']
						);
		if($this->db->insert('pos_b_locations',$insert_outlet))
		{
			return 1;
		} else {
			return 0;
		}
	}
	public function delete_outlet($data)
	{
		$this->db->select('user_id');
		$this->db->select('user_name');
		$query = $this->db->get_where('pos_e_login',array('account_no' => $data['acc'], 'location' => $data['outlet_id'],'user_status !=' => 120));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$text[] = anchor(base_url('users/'.$row->user_id),$row->user_name,'style="color:#03f"');
			}
			if(count($text) > 1)
			{
				$lastItem = array_pop($text);
				$text = implode(', ', $text); 
				$text .= ' and '.$lastItem;
			} else {
				$text = $text[0];
			}
			return array(
				'stat' => 0,
				'error_str' => 'Some user(s) like "'.$text.'" are associated to this outlet, so it can`t be deleted. 
							Please remove or change user(s) to some other outlet and try again.'
			);
		} else {
			$update = array(
						   'outlet_stat' => 120
						);
			$this->db->where(array('account_no' => $data['acc'],'loc_id' => $data['outlet_id']));
			if($this->db->update('pos_b_locations', $update))
			{
				$this->db->where('location',$data['outlet_id']);
				$this->db->where('account_no',$data['acc']);
				$this->db->update('pos_j1_registers',array('reg_stat' =>  120));
				return array(
					'stat' => 1,
					'error_str' => 'Outlet successfully deleted.'
				);				
			}
				
		}
	}
}
?>