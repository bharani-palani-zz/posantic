<?php
class Payment_type_model extends CI_Model
{
	public function master_pay_types_combo()
	{
		$this->db->order_by('order_by');
		$this->db->where(array('posantic_status' => 1, 'is_static' => 0));
		$query = $this->db->get('pos_1a_payment_types');
		if($query->num_rows() > 0)
		{
			$type = array(0 => 'Default', 1 => 'Integrated');
			foreach($query->result() as $row)
			{
				$array[$type[$row->is_integration]][$row->type_index] = $row->type_name;
			}
			return $array;
		} else {
			return array(array('Default' => NULL));	
		}
	}
	public function get_payment_type_if_id($type_id,$acc)
	{
		$this->db->select('*');
		$this->db->from('pos_e_payment_master as a');
		$this->db->join('pos_e_payment_method_config as b','a.pay_master_id = b.master_id','left');
		$this->db->join('pos_1a_payment_types_attributes as c','c.attr_id = b.attr_id','left');
		$this->db->join('pos_1a_payment_types as d','d.type_index = a.method_id','left');
		$this->db->where('a.pay_master_id',$type_id);
		$this->db->where('a.account_no',$acc);
		$query = $this->db->get();

		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array['master_id'] = $row->pay_master_id;
				$array['method_id'] = $row->method_id;
				$array['label'] = $row->pay_alias_name;
				$array['image_location'] = $row->image_location;
				$array['external_type_link'] = $row->external_type_link;
				$array['type_description'] = $row->type_description;
				$array['sort_order'] = $row->sort_order;
				$array['attributes'][$row->method_index]['keys'] = $row->attr_id;
				$array['attributes'][$row->method_index]['values'] = $row->attr_values;
				$array['attributes'][$row->method_index]['attr_name'] = $row->attr_name;
				$array['attributes'][$row->method_index]['html_label'] = $row->html_label;
				$array['attributes'][$row->method_index]['placeholder'] = $row->placeholder;
				$array['attributes'][$row->method_index]['html_type'] = $row->html_type;
				$array['attributes'][$row->method_index]['html_load_db_data'] = $row->html_load_db_data;
				$array['attributes'][$row->method_index]['html_label_sub_caption'] = $row->html_label_sub_caption;
			} 
			return $array;				
		} else {
			return NULL;	
		}
	}
	public function get_my_payment_types($acc)
	{
		
		$this->db->where('is_hidden',10);
		$this->db->where('pay_stat',100);
		$this->db->order_by('sort_order');
		$query = $this->db->get_where('pos_e_payment_master',array('account_no' => $acc));
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
			return null;	
		}
	}
	public function get_pay_type_fields($method_id)
	{
		$this->db->from('pos_1a_payment_types_attributes as a');	
		$this->db->join('pos_1a_payment_types as b','b.type_index = a.type_id');
		$this->db->where('a.type_id',$method_id);
		$this->db->order_by('a.order_by');
		$query = $this->db->get();
		return $query->result(); 
	}
	public function master_payment_config($method_id)
	{
		$this->db->select('attr_id');
		$this->db->select('attr_name');
		$this->db->order_by('order_by');
		$query = $this->db->get_where('pos_1a_payment_types_attributes',array('type_id' => $method_id));	
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$array[$row->attr_id] = $row->attr_name;
			} 
			return $array;				
		} else {
			return NULL;	
		}
		
	}
	public function insert_method($data)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		$master_id = $this->taxes_model->make_single_uuid();
		$insert['main'] = array(
							'pay_master_id' => $master_id,
							'method_id' => $data['method_id'],
							'pay_alias_name' => $data['method_label'],
							'is_delete' => 30,
							'pay_stat' => 100,
							'is_hidden' => 10,
							'sort_order' => $data['method_sort'],
							'account_no' => $data['acc']		
							);
		$this->db->insert('pos_e_payment_master', $insert['main']);
		foreach($data['insert_array'] as $attr_key => $attr_value)
		{
			$insert['config'][] = array(
								'method_index' => $this->taxes_model->make_single_uuid(),
								'master_id' => $master_id,
								'attr_id' => $attr_key,
								'attr_values' => $attr_value,
								'account_no' => $data['acc']
								);
		}
		$this->db->insert_batch('pos_e_payment_method_config', $insert['config']);
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return 0;
		} else {
			return 1;	
		}
	}
	public function delete_method($master_id,$acc)
	{
		$this->db->where(array('account_no' => $acc,'pay_master_id' => $master_id, 'is_delete' => 30));
		if($this->db->update('pos_e_payment_master',array('pay_stat' => 120)))
		{
			return 1;
		} else {
			return 0;	
		}
	}
	public function update_method($data)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		$this->db->where('pay_master_id', $data['master_id']);			
		$this->db->where('account_no', $data['acc']);			
		$this->db->update('pos_e_payment_master',array('pay_alias_name' => $data['method_label'],'sort_order' => $data['method_sort']));
		foreach($data['payment_method'] as $key => $value)
		{
			$this->db->where(array('account_no' => $data['acc'], 'method_index' => $key));
			$this->db->update('pos_e_payment_method_config',array('attr_values' => $value));
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return 0;
		} else {
			return 1;	
		}
	}
}
?>