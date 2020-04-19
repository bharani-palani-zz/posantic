<?php
class Shipment_model extends CI_Model
{
	public function shipment_method_count()
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('count(*)');
		$query = $this->db->get_where('pos_j3_shipment',array('account_no' => $this->session->userdata('acc_no')));
        if($query->num_rows() > 0) {
			$row = $query->row_array();
			$count =  $row['count(*)'];
			return $count;
		}
		$this->db->close();		
	}
	public function M_get_countries()
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('country_id');
		$this->db->select('country_name');
		$query = $this->db->get('pos_i8_countries');
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row){
				$array[] = array($row->country_id,$row->country_name);
			}
			return $array;
		}
		$this->db->close();		
	}
	public function M_get_states($country_id)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('state_id');
		$this->db->select('state_name');
		$this->db->order_by("state_name", "asc");
		$query = $this->db->get_where('pos_i9_states',array('country_id' => $country_id));
		if($query->num_rows() > 0)
		{
			$html = '<option value="">--</option>';
			foreach($query->result() as $row){
				$html .= '<option value="'.$row->state_id.'">'.$row->state_name.'</option>';
			}
			return $html;
		}
		$this->db->close();		
	}
	public function M_get_cities($state_id,$country_id)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('city_id');
		$this->db->select('city_name');
		$this->db->select('latitude');
		$this->db->select('longitude');
		$this->db->order_by("city_name", "asc");
		$query = $this->db->get_where('pos_j1_cities',array('state_id' => $state_id,'country_id' => $country_id));
		if($query->num_rows() > 0)
		{
			$html = '<option value="">--</option>';
			foreach($query->result() as $row){
				$html .= '<option id="'.$row->latitude.'~'.$row->longitude.'" value="'.$row->city_id.'">'.$row->city_name.'</option>';
			}
			return $html;
		}
		$this->db->close();		
	}
	public function get_shipping_vendor($country_id)
	{
		$this->db->select('ship_id');
		$this->db->select('vendor_title');
		$where = "country_id = 0 OR country_id = {$country_id}";
		$this->db->where($where);
		$query = $this->db->get('pos_j2_shipping_vendor');
		if($query->num_rows() > 0)
		{
			$html = '<option value="">--</option>';
			foreach($query->result() as $row){
				$html .= '<option value="'.$row->ship_id.'">'.$row->vendor_title.'</option>';
			}
			return $html;
		}
		$this->db->close();		
	}
	public function check_range_existence($table,$is,$r1_field,$r2_field,$r1,$r2,$acc)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('count(*)');
		$this->db->where($r1_field.' >=', $r1);
		$this->db->where($r2_field.' <=', $r2);		
		$this->db->where($is.' =', 1);		
		$query = $this->db->get_where($table,array('account_no' => $acc));
        if($query->num_rows() > 0) {
			$row = $query->row_array();
			$count =  $row['count(*)'];
			return $count;
		}
	}
	public function check_locale_existence($table,$field,$value,$acc)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('count(*)');
		$query = $this->db->get_where($table,array($field => $value,'account_no' => $acc));
        if($query->num_rows() > 0) {
			$row = $query->row_array();
			$count =  $row['count(*)'];
			return $count;
		}
	}
	public function check_free_flat_existence($table,$is,$acc)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('count(*)');
		$this->db->where($is.' =', 1);
		$query = $this->db->get_where($table,array('account_no' => $acc));
        if($query->num_rows() > 0) {
			$row = $query->row_array();
			$count =  $row['count(*)'];
			return $count;
		}
	}
	public function shipment_count()
	{
		$this->db = $this->load->database('default', TRUE);  
		$query = $this->db->query('select count(*) from pos_j3_shipment where account_no = '.$this->session->userdata('acc_no'));
		if ($query->num_rows() > 0)
		{
		   $row = $query->row_array();
		   $count = $row['count(*)'];
		   return $count;
		} else {
		   return 0;	
		}
	}
	public function M_insert_shipment($array)
	{
		$this->db = $this->load->database('default', TRUE);  
		$data = array(
			'ship_key' => $array[0],
			'is_free' => $array[1],
			'is_flat_shipment' => $array[2],
			'is_weightable' => $array[3],
			'weigh_min_val' => $array[4],
			'weigh_max_val' => $array[5],
			'is_costable' => $array[6],
			'cost_min_val' => $array[7],
			'cost_max_val' => $array[8],
			'is_locale' => $array[9],
			'locale_id' => $array[10],
			'vendor_id' => $array[11],
			'cmp_cost' => $array[12],
			'ship_cost' => $array[13],
			'updated_by' => $array[14],
			'updated_at' => $array[15],
			'account_no' => $array[16]
		);
		if($this->db->insert('pos_j3_shipment', $data))
		{
			return 1;	
		} else {
			return 0;
		}
	}
	public function M_get_ship_methods($sel_array,$is,$table,$acc)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select($sel_array);
		$this->db->where($is.' = ', 1);
		$query = $this->db->get_where($table,array('account_no' => $acc));
		return $query;

	}
	public function update_flat_cost($value,$id)
	{
		$this->db = $this->load->database('default', TRUE);  
		$data = array(
					   'cmp_cost' => $value,
					   'ship_cost' => $value,
					   'updated_at' => time()
					);
		
		$this->db->where('ship_key', $id);
		if($this->db->update('pos_j3_shipment', $data))
		{
			return true;
		} else {
			return false;	
		}
	}
	public function M_update_weight_cmp_cost($value,$id)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('ship_cost');
		$query = $this->db->get_where('pos_j3_shipment',array('ship_key' => $id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$ship_cost = $row['ship_cost'];
			if($value < $ship_cost)
			{
				$data = array(
							   'cmp_cost' => $value,
							   'updated_at' => time()
							);
				
				$this->db->where('ship_key', $id);
				if($this->db->update('pos_j3_shipment', $data))
				{
					return true;
				} else {
					return false;	
				}
			} else {
				return false;	
			}
		} else {
		   return false;	
		}
	}
	public function M_update_weight_cust_cost($value,$id)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('cmp_cost');
		$query = $this->db->get_where('pos_j3_shipment',array('ship_key' => $id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$cmp_cost = $row['cmp_cost'];
			if($value > $cmp_cost)
			{
				$data = array(
							   'ship_cost' => $value,
							   'updated_at' => time()
							);
				
				$this->db->where('ship_key', $id);
				if($this->db->update('pos_j3_shipment', $data))
				{
					return true;
				} else {
					return false;	
				}
			} else {
				return false;	
			}
		} else {
		   return false;	
		}
	}
	public function M_update_weight_min_val($value,$id)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('weigh_max_val');
		$query = $this->db->get_where('pos_j3_shipment',array('ship_key' => $id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$max_val = $row['weigh_max_val'];
			if($value < $max_val)
			{
				$data = array(
							   'weigh_min_val' => $value,
							   'updated_at' => time()
							);
				
				$this->db->where('ship_key', $id);
				if($this->db->update('pos_j3_shipment', $data))
				{
					return true;
				} else {
					return false;	
				}
			} else {
				return false;	
			}
		} else {
		   return false;	
		}		
	}
	public function M_update_weight_max_val($value,$id)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('weigh_min_val');
		$query = $this->db->get_where('pos_j3_shipment',array('ship_key' => $id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$min_val = $row['weigh_min_val'];
			if($value > $min_val)
			{
				$data = array(
							   'weigh_max_val' => $value,
							   'updated_at' => time()
							);
				
				$this->db->where('ship_key', $id);
				if($this->db->update('pos_j3_shipment', $data))
				{
					return true;
				} else {
					return false;	
				}
			} else {
				return false;	
			}
		} else {
		   return false;	
		}		
	}
	public function M_update_price_cmp_cost($value,$id)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('ship_cost');
		$query = $this->db->get_where('pos_j3_shipment',array('ship_key' => $id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$ship_cost = $row['ship_cost'];
			if($value < $ship_cost)
			{
				$data = array(
							   'cmp_cost' => $value,
							   'updated_at' => time()
							);
				
				$this->db->where('ship_key', $id);
				if($this->db->update('pos_j3_shipment', $data))
				{
					return true;
				} else {
					return false;	
				}
			} else {
				return false;	
			}
		} else {
		   return false;	
		}		
	}
	public function M_update_price_cust_cost($value,$id)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('cmp_cost');
		$query = $this->db->get_where('pos_j3_shipment',array('ship_key' => $id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$cmp_cost = $row['cmp_cost'];
			if($value > $cmp_cost)
			{
				$data = array(
							   'ship_cost' => $value,
							   'updated_at' => time()
							);
				
				$this->db->where('ship_key', $id);
				if($this->db->update('pos_j3_shipment', $data))
				{
					return true;
				} else {
					return false;	
				}
			} else {
				return false;	
			}
		} else {
		   return false;	
		}		
	}
	public function M_update_price_min_val($value,$id)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('cost_max_val');
		$query = $this->db->get_where('pos_j3_shipment',array('ship_key' => $id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$max_val = $row['cost_max_val'];
			if($value < $max_val)
			{
				$data = array(
							   'cost_min_val' => $value,
							   'updated_at' => time()
							);
				
				$this->db->where('ship_key', $id);
				if($this->db->update('pos_j3_shipment', $data))
				{
					return true;
				} else {
					return false;	
				}
			} else {
				return false;	
			}
		} else {
		   return false;	
		}		
	}
	public function M_update_price_max_val($value,$id)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('cost_min_val');
		$query = $this->db->get_where('pos_j3_shipment',array('ship_key' => $id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$min_val = $row['cost_min_val'];
			if($value > $min_val)
			{
				$data = array(
							   'cost_max_val' => $value,
							   'updated_at' => time()
							);
				
				$this->db->where('ship_key', $id);
				if($this->db->update('pos_j3_shipment', $data))
				{
					return true;
				} else {
					return false;	
				}
			} else {
				return false;	
			}
		} else {
		   return false;	
		}		
	}
	public function M_delete_shipment($id)
	{
		$this->db = $this->load->database('default', TRUE);  
		if($this->db->delete('pos_j3_shipment', array('ship_key' => $id)))
		{
			return true;
		} else {
			return false;	
		}
	}
	public function M_get_shipment_method_val($method,$weight,$cost)
	{
		$this->db = $this->load->database('default', TRUE);  
		switch($method){
			case 'ship_free':
				return 0;
			break; 
			case 'ship_flat':
				$this->db->select('ship_cost');
				$this->db->where('is_flat_shipment',1);
			break; 
			case 'ship_weight':
				$this->db->select('ship_cost');
				$this->db->where('is_weightable',1);
				$this->db->where('weigh_min_val <=', $weight);
				$this->db->where('weigh_max_val >=', $weight);				
			break; 
			case 'ship_price':
				$this->db->select('ship_cost');
				$this->db->where('is_costable',1);
				$this->db->where('cost_min_val <=', $cost);
				$this->db->where('cost_max_val >=', $cost);				
			break; 
		}
		$query = $this->db->get_where('pos_j3_shipment',array('account_no' => $this->session->userdata('acc_no')));
		if($query->num_rows() > 0) {
			$row = $query->row_array();
			$count =  $row['ship_cost'];
			return $count;
		} else {
			return 0;	
		}
	}
	public function select_ship_store_cities($acc,$loc)
	{
		$this->db = $this->load->database('default', TRUE);  
		$sql = "select 
					pos_j1_cities.city_id as cityid, 
				    pos_j3_shipment.ship_cost as shipcost,
					pos_j1_cities.city_name as cityname
				from
					pos_j3_shipment, pos_j1_cities
				where
					pos_j1_cities.city_id = pos_j3_shipment.locale_id and
					pos_j3_shipment.is_locale = 1 and
					pos_j3_shipment.account_no = ? and
					pos_j3_shipment.location = ?
					";
		$param = array($acc,$loc);
		$query = $this->db->query($sql, $param); 
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row){
				$array[] = array($row->cityid,$row->shipcost,$row->cityname);
			}
			return $array;
		}
		$this->db->close();		
	}
}
?>