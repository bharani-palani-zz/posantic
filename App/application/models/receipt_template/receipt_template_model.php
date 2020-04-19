<?php
class Receipt_template_model extends CI_Model
{
	public function make_single_uuid()
	{
		$this->db->select('uuid() as uuid');	
		$query = $this->db->get();
		$row = $query->row_array();
		$str = str_replace("-","",$row['uuid']);
		$first = substr($str,20,8);
		$sec = substr($str,28,4);
		$third = substr($str,12,4);
		$forth = substr($str,16,4);
		$fifth = substr($str,8,4).substr($str,0,8);
		return sprintf('%s-%s-%s-%s-%s',$first,$sec,$third,$forth,$fifth);
	}
	public function get_all_templates($acc)
	{
		$this->db->select('*');	
		$this->db->from('pos_c_reciept_template');
		$this->db->join('pos_1a_receipt_header_types', 'pos_1a_receipt_header_types.header_id = pos_c_reciept_template.bill_header_type');
		$this->db->where(array('pos_c_reciept_template.account_no' => $acc));
		$query = $this->db->get();	
		if($query != false){
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
	public function get_receipt_headers()
	{
		$query = $this->db->get('pos_1a_receipt_header_types');	
		if($query != false){
			$array = array();
			foreach($query->result_array() as $row)
			{
				$array['Heading as'][$row['header_id']] = $row['bill_header_name'];
			}
			return $array;
		} else {
			return array();
		}		

	}
	public function get_printer_types()
	{
		$this->db->select('*');
		$this->db->from('pos_1a_printers');
		$this->db->order_by('order_type');
		$query = $this->db->get();	
		if($query != false){
			$array = array();
			foreach($query->result_array() as $row)
			{
				$array['Printer Type'][$row['printer_id']] = $row['printer_type_code']." - ".$row['printer_type'];
			}
			return $array;
		} else {
			return array();
		}		

	}
	public function get_template_wrt_id($template_id,$acc)
	{
		$this->db->select('*');
		$query = $this->db->get_where('pos_c_reciept_template',array('account_no' => $acc, 'template_id' => $template_id));
		if($query != false){
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
	public function insert_template($data)
	{
		$insert = array(
						'template_id' => $this->make_single_uuid(),
						'template_name' => $data['temp_name'],
						'bill_header_type' => $data['header_type'],
						'header_text' => $data['temp_header_text'],
						'show_disc_bill' => $data['show_disc'],
						'show_loyalty_bill' => $data['show_loyalty'],
						'show_address_bill' => $data['show_addrr'],
						'show_promotions' => $data['show_promo'],
						'show_bill_quotes' => $data['show_quotes'],
						'show_barcode' => $data['show_barcode'],
						'billno_label' => $data['temp_bill_no_caption'],
						'cashier_label' => $data['temp_operator_caption'],
						'disc_label' => $data['temp_disc_caption'],
						'tax_label' => $data['temp_tax_caption'],
						'change_label' => $data['temp_change_caption'],
						'total_label' => $data['temp_total_caption'],
						'loyalty_label' => $data['temp_loyalty_caption'],
						'footer_text' => $data['temp_footer_text'],
						'receipt_printer_type' => $data['printer_type'],
						'is_delete' => 10,
						'account_no' => $data['acc'],
						); 
		if($this->db->insert('pos_c_reciept_template', $insert))
		{
			return 1;
		} else {
			return 0;
		}
	}
	public function update_template($data)
	{
		$update = array(
						'template_name' => $data['temp_name'],
						'bill_header_type' => $data['header_type'],
						'header_text' => $data['temp_header_text'],
						'show_disc_bill' => $data['show_disc'],
						'show_loyalty_bill' => $data['show_loyalty'],
						'show_address_bill' => $data['show_addrr'],
						'show_promotions' => $data['show_promo'],
						'show_bill_quotes' => $data['show_quotes'],
						'show_barcode' => $data['show_barcode'],
						'billno_label' => $data['temp_bill_no_caption'],
						'cashier_label' => $data['temp_operator_caption'],
						'disc_label' => $data['temp_disc_caption'],
						'tax_label' => $data['temp_tax_caption'],
						'change_label' => $data['temp_change_caption'],
						'total_label' => $data['temp_total_caption'],
						'loyalty_label' => $data['temp_loyalty_caption'],
						'footer_text' => $data['temp_footer_text'],
						'receipt_printer_type' => $data['printer_type'],
						); 
		$this->db->where(array('template_id' => $data['template_id'], 'account_no' => $data['acc']));
		if($this->db->update('pos_c_reciept_template', $update))
		{
			return 1;	
		} else {
			return 0;
		}
	}
	public function delete_template($template_id,$acc)
	{
		$this->db->select('count(*) as counted');
		$this->db->where_in('template_id',array($template_id));	
		$query = $this->db->get_where('pos_c_reciept_template',array('account_no' => $acc));
		$row = $query->row_array();
		if($row['counted'] > 0)
		{
			$oldv = $this->db->db_debug;
			$this->db->db_debug = FALSE; 	
			$this->db->where('is_delete', 10);
			$this->db->where('template_id', $template_id);
			$this->db->where('account_no', $acc);
			$this->db->delete('pos_c_reciept_template'); 
			$aff = $this->db->affected_rows();
			$this->db->db_debug = $oldv; 
			if($aff < 1) {
				return array(
							'stat' => 0,
							'error_str' => 'This receipt template is already set for one of your outlets and it can`t be deleted. 
											Please change the outlets / registers associated template to some other one and try again.'
							);				
			} else {
				return array(
							'stat' => 1,
							'error_str' => 'Template Successfully Deleted!'
							);				
			}
		} else {
			return array(
						'stat' => 2,
						'error_str' => 'This Receipt template did not exist!'
						);				
		}
	}
	public function template_combo($acc)
	{
		$this->db->select('template_id');
		$this->db->select('template_name');
		$this->db->order_by('is_delete','desc');
		$query = $this->db->get_where('pos_c_reciept_template',array('account_no' => $acc));
		if($query != false){
			$array = array();
			foreach($query->result() as $row)
			{
				$array['Select receipt template'][$row->template_id] = $row->template_name;
			}
			return $array;
		} else {
			return array('' => 'NULL');
		}		
	}
}
?>