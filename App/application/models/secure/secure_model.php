<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Secure_model extends CI_Model
{
	public $validity_days;
 	public function __construct()
    {
        parent::__construct();
		$this->validity_days = 30;
		$this->load->helper('text');
    }
	public function reserved_domains()
	{
		$reserved = array(
						'posantic',
						'info',
						'support',
						'blog',
						'careers',
						'admin',
						'site',
						'secure',
						'developer'
					);
		return $reserved;					
	}
	public function check_domain($domain)
	{
		$reserved = $this->reserved_domains();
		if(!in_array(strtolower($domain),$reserved))
		{
			$query = $this->db->get_where('pos_a_master',array('subdomain' => $domain));
			if($query->num_rows() > 0)
			{
				return false;	
			} else {
				return true;
			}
		} else {
			return false;	
		}
	}
	private function calc_timezone($rawOffset,$dstOffset)
	{
		$tot_offset = $rawOffset + $dstOffset;
		$this->db->select('zone_id');
		$this->db->from('pos_1a_timezone');
		$this->db->where('offset',$tot_offset);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row['zone_id'];	
		} else {
			// if time zone id not found set default as UTC
			return 'UTC';	
		}
	}
	private function calc_account_type()
	{
		// trial account secret key
		return 545259; 
	}
	private function calc_plan_code($outlet_type)
	{
		// if outlet == 1 then single outlet plan else multiple outlet plan
		return $outlet_type	== "1+" ? 5678 : 4567;	
	}	
	private function signup_master($signup_array,$account_id)
	{
		$insert = array(
					'account_no' => $account_id,
					'business_type' => $signup_array['business_type'],
					'subdomain' => strtolower($signup_array['subdomain']),
					'cmp_name' => $signup_array['store_name'],
					'timezone' => $this->calc_timezone($signup_array['rawOffset'],$signup_array['dstOffset']),
					'currency' => $signup_array['contact_currency'],
					'validity' => $this->validity_days,
					'credit_amount' => 1,
					'usage_in_bytes' => 1024,
					'usage_percent' => 0,
					'account_type' => $this->calc_account_type(),
					'plan_code' => $this->calc_plan_code($signup_array['outlet_type']),
					'guest_fbid' => "",
					'contact_name' => $signup_array['contact_name'],
					'contact_mobile' => $signup_array['contact_mobile'],
					'contact_email' => $signup_array['contact_email'],
					'contact_addr1' => $signup_array['address_1'],
					'contact_addr2' => $signup_array['address_2'],
					'contact_city' => $signup_array['city'],
					'contact_state' => $signup_array['state'],
					'contact_postalcode' => $signup_array['zip'],
					'contact_country' => $signup_array['country'],
					'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
					'latitude' => $signup_array['latitude'],
					'longitude' => $signup_array['longitude'],
					'account_stat' => 10		
					);
		return $this->db->insert('pos_a_master',$insert);			
	}
	private function signup_taxes($tax_id,$account_id)
	{
		$insert = array(
					'tax_id' => $tax_id,
					'tax_name' => 'No Tax',
					'is_group' => 20,
					'tax_val' => 0,
					'is_delete' => 20,
					'tax_stat' => 30,
					'account_no' =>	$account_id	
					);
		return $this->db->insert('pos_a_taxes',$insert);			
	}
	private function signup_outlet($signup_array,$outlet_id,$tax_id,$account_id)
	{
		$insert = array(
					'loc_id' => $outlet_id,
					'location' => trim(ellipsize($signup_array['city'],10,1,"...").' Debut Store'),
					'guest_addr_l1' => "",
					'guest_addr_l2' => "",
					'guest_city' => $signup_array['city'],
					'guest_postalcode' => "",
					'guest_state' => $signup_array['state'],
					'guest_country' => $signup_array['country'],
					'guest_ll' => "",
					'guest_email' => "",
					'outlet_tax' => $tax_id,
					'outlet_stat' => 30,
					'account_no' => $account_id	
					);
		return $this->db->insert('pos_b_locations',$insert);			
	}
	private function signup_template($template_id,$account_id)
	{
		$insert = array(
					'template_id' => $template_id,
					'template_name' => 'Default',
					'bill_header_type' => 3,
					'header_text' => "",
					'show_disc_bill' => 20,
					'show_loyalty_bill' => 20,
					'show_address_bill' => 20,
					'show_promotions' => 20,
					'show_bill_quotes' => 20,
					'show_barcode' => 10,
					'billno_label' => "Bill No",
					'cashier_label' => "Cashier",
					'disc_label' => "Discount",
					'tax_label' => "Tax",
					'change_label' => "Change",
					'total_label' => "Total",
					'loyalty_label' => "Loyalty",
					'footer_text' => "",
					'receipt_printer_type' => 2,
					'is_delete' => 20,
					'account_no' => $account_id			
					);
		return $this->db->insert('pos_c_reciept_template',$insert);			
	}
	private function signup_login($signup_array,$user_id,$account_id)
	{
		$concat = $user_id.$signup_array['contact_password'].$signup_array['subdomain'].$account_id;
		$hash_pass = $this->encrypt->sha1($concat);		
		$insert = array(
					'user_id' => $user_id,
					'user_name' => $signup_array['contact_name'],
					'display_name' => $signup_array['contact_name'],
					'password' => $hash_pass,
					'user_status' => 10,
					'privelage' => 1,
					'user_mail' => $signup_array['contact_email'],
					'user_mobile' => $signup_array['contact_mobile'],
					'is_delete' => 120,
					'target_day' => 0,
					'target_week' => 0,
					'target_month' => 0,
					'last_login' => mdate('%Y-%m-%d %H:%i:%s', now()),
					'current_login' => mdate('%Y-%m-%d %H:%i:%s', now()),
					'account_no' => $account_id,
					'location' => NULL,
					'my_theme' => ""						
					);	
		$this->db->insert('pos_e_login',$insert);			
		return $hash_pass;
	}
	private function signup_loyalty($account_id)
	{
		$loyalty_id = $this->taxes_model->make_single_uuid();
		$insert = array(
					'l_id' => $loyalty_id,
					'status' => 20,
					'sale_value' => 0,
					'reward_value' => 0,
					'account_no' => $account_id
					);	
		return $this->db->insert('pos_e_loyalty',$insert);			
	}
	private function signup_payment_setup($payment_master_id,$account_id)
	{
		$cash_array = $this->db->get_where('pos_1a_payment_types',array('type_code' => 'CASH'))->row_array();
		$card_array = $this->db->get_where('pos_1a_payment_types',array('type_code' => 'CARD'))->row_array();
		$loyalty_array = $this->db->get_where('pos_1a_payment_types',array('type_code' => 'LOYALTY'))->row_array();
		$gv_array = $this->db->get_where('pos_1a_payment_types',array('type_code' => 'GIFTVOUCHER'))->row_array();
		$insert = array(
					array(
						'pay_master_id' => $payment_master_id['id_1'],
						'method_id' => $cash_array['type_index'],
						'pay_alias_name' => 'Cash',
						'is_delete' => 40, // set default cash as static method and undeletable
						'pay_stat' => 100,
						'is_hidden' => 10,
						'sort_order' => 0,
						'account_no' => $account_id
						),
					array(
						'pay_master_id' => $payment_master_id['id_2'],
						'method_id' => $card_array['type_index'],
						'pay_alias_name' => 'Card',
						'is_delete' => 30,
						'pay_stat' => 100,
						'is_hidden' => 10,
						'sort_order' => 1,
						'account_no' => $account_id
						),
					array(
						'pay_master_id' => $payment_master_id['id_3'],
						'method_id' => $loyalty_array['type_index'],
						'pay_alias_name' => 'Loyalty',
						'is_delete' => 40,
						'pay_stat' => 100,
						'is_hidden' => 20,
						'sort_order' => 0,
						'account_no' => $account_id
						),
					array(
						'pay_master_id' => $payment_master_id['id_4'],
						'method_id' => $gv_array['type_index'],
						'pay_alias_name' => 'Gift Voucher',
						'is_delete' => 40,
						'pay_stat' => 100,
						'is_hidden' => 20,
						'sort_order' => 0,
						'account_no' => $account_id
						),
					);
		$this->db->insert_batch('pos_e_payment_master',$insert);			

		$cash_config_array = $this->db->get_where('pos_1a_payment_types_attributes',array('type_id' => $cash_array['type_index']))->row_array();
		$card_config_array = $this->db->get_where('pos_1a_payment_types_attributes',array('type_id' => $card_array['type_index']))->row_array();
		$loyalty_config_array = $this->db->get_where('pos_1a_payment_types_attributes',array('type_id' => $loyalty_array['type_index']))->row_array();
		$gv_config_array = $this->db->get_where('pos_1a_payment_types_attributes',array('type_id' => $gv_array['type_index']))->row_array();

		$insert_config = array(
					array(
						'method_index' => $this->taxes_model->make_single_uuid(),
						'master_id' => $payment_master_id['id_1'],
						'attr_id' => $cash_config_array['attr_id'],
						'attr_values' => "",
						'account_no' => $account_id			
					),
					array(
						'method_index' => $this->taxes_model->make_single_uuid(),
						'master_id' => $payment_master_id['id_2'],
						'attr_id' => $card_config_array['attr_id'],
						'attr_values' => "",
						'account_no' => $account_id			
					),
					array(
						'method_index' => $this->taxes_model->make_single_uuid(),
						'master_id' => $payment_master_id['id_3'],
						'attr_id' => $loyalty_config_array['attr_id'],
						'attr_values' => "",
						'account_no' => $account_id			
					),
					array(
						'method_index' => $this->taxes_model->make_single_uuid(),
						'master_id' => $payment_master_id['id_4'],
						'attr_id' => $gv_config_array['attr_id'],
						'attr_values' => "",
						'account_no' => $account_id			
					)
				  );
		return $this->db->insert_batch('pos_e_payment_method_config',$insert_config);			
	}
	private function signup_supplier($signup_array,$account_id)
	{
		$supp_id = $this->taxes_model->make_single_uuid();
		$insert = array(
					'supp_id' => $supp_id,
					'cmp_name' => $signup_array['store_name'],
					'supp_description' => "",
					'auth_pers' => $signup_array['contact_name'],
					'mobile' => $signup_array['contact_mobile'],
					'll' => "",
					'email' => $signup_array['contact_email'],
					'web' => "",
					'addrr1' => $signup_array['address_1'],
					'addrr2' => $signup_array['address_2'],
					'city' => $signup_array['city'],
					'postal_code' => $signup_array['zip'],
					'state' => $signup_array['state'],
					'country' => $signup_array['country'],
					'fax' => "",
					'is_delete' => 40,
					'supp_stat' => 30,
					'account_no' => $account_id
					);
		return $this->db->insert('pos_e_suppliers',$insert);			
	}
	private function variant_types()
	{
		$types = array(
					'FABRIC' => 'Fabric',
					'MATERIAL' => 'Material',
					'STYLE' => 'Style',
					'TITLE' => 'Title',
					'GENDER' => 'Gender',
					'SEX' => 'Sex',
					'TYPE' => 'Type',
					'MAKE' => 'Make',
					'OPTION' => 'Option',
					'FIT' => 'Fit',
					'FLAVOUR' => 'Flavour',
					'COLOUR' => 'Colour',
					'SIZE' => 'Size',
					);
		return $types;					
	}
	private function signup_variant_types($account_id)
	{
		$types = $this->variant_types();
		foreach($types as $key => $value)
		{
			$insert[] = array(
							'cust_var_id' => $this->taxes_model->make_single_uuid(),
							'cust_var_name' => $key,
							'cust_var_value' => $value,
							'account_no' => $account_id			
							);	
		}
		return $this->db->insert_batch('pos_i1_0_cust_variant_types',$insert);
	}
	private function signup_kilo_prefix($account_id)
	{
		$supp_id = $this->taxes_model->make_single_uuid();
		$insert = array(
					'prefix_key' => $this->taxes_model->make_single_uuid(),
					'prefix_val' => 20,
					'account_no' => $account_id				
					  );
		return $this->db->insert('pos_i1_kilo_product_prefix',$insert);			
	}
	private function signup_product_brand($account_id)
	{
		$insert = array(
					'brand_index' => $this->taxes_model->make_single_uuid(),
					'brand_name' => "General Brand",
					'account_no' => $account_id	
					);
		return $this->db->insert('pos_i1_product_brand',$insert);			
	}
	private function signup_product_category($account_id)
	{
		$insert = array(
					  array(
					  'cat_id' => $this->taxes_model->make_single_uuid(),
					  'cat_name' => "Standard products",
					  'account_no' => $account_id	
					  ),
					  array(
					  'cat_id' => $this->taxes_model->make_single_uuid(),
					  'cat_name' => "Fast moving products",
					  'account_no' => $account_id	
					  )
				  );
		return $this->db->insert_batch('pos_i1_product_category',$insert);			
	}
	private function signup_product($outlet_id,$account_id)
	{
		$standard_product_id = $this->taxes_model->make_single_uuid();
		$standard_product_name = "Scented Candle (Demo)";
		$standard_product_desc = '<p>This is a standard product sold by singles of quantities.</p>';
		
		$kilo_product_id = $this->taxes_model->make_single_uuid();
		$kilo_product_name = "Rice (Demo)";
		$kilo_product_desc = '<p>This is a kilo based product sold in (kg)scale.</p>';
		
		$variant_product_id = $this->taxes_model->make_single_uuid();
		$variant_product_name = "Blue jeans (Demo)";
		$variant_product_desc = '<p>This is a variant product, available in small/medium/large/XL variants</p>';
		$variant_index = $this->taxes_model->make_single_uuid();
		
		$gift_product_id = $this->taxes_model->make_single_uuid();
		$gift_product_name = "Gift Voucher";
		$gift_product_desc = '<p>Gift voucher for all customers</p>';

		$insert['main'] = array(
					array(
					'product_id' => $standard_product_id,
					'product_name' => $standard_product_name,
					'product_scale' => 1,
					'handle' => str_replace(' ', '', $standard_product_name),
					'description' => $standard_product_desc,
					'account_no' => $account_id
					),	
					array(
					'product_id' => $kilo_product_id,
					'product_name' => $kilo_product_name,
					'product_scale' => 2,
					'handle' => str_replace(' ', '',$kilo_product_name),
					'description' => $kilo_product_desc,
					'account_no' => $account_id
					),	
					array(
					'product_id' => $variant_product_id,
					'product_name' => $variant_product_name,
					'product_scale' => 3,
					'handle' => str_replace(' ', '', $variant_product_name),
					'description' => $variant_product_desc,
					'account_no' => $account_id
					),	
					array(
					'product_id' => $gift_product_id,
					'product_name' => $gift_product_name,
					'product_scale' => 1,
					'handle' => str_replace(' ', '', $gift_product_name),
					'description' => $gift_product_desc,
					'account_no' => $account_id
					)	
				);
		$this->db->insert_batch('pos_i1_products',$insert['main']);			
		$insert['standard'] = array(
								array(
									'product_id' => $standard_product_id,
									'price' => 200,
									'margin' => 40,
									'retail_price' => 280,
									'sku' => 10001,
									'product_weight' => 0.1,
									'loyalty' => 2.8,
									'is_shopping_cart' => 40,
									'status' => 30,
									'track_inventory' => 30,
									'wearhouse_id' => "",
									'purchase_id' => "",
									'ship_stat' => 40,
									'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
									'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
									'account_no' => $account_id
								),
								array(
									'product_id' => $gift_product_id,
									'price' => 0,
									'margin' => 0,
									'retail_price' => 0,
									'sku' => "posantic-gift-voucher",
									'product_weight' => 0,
									'loyalty' => 0,
									'is_shopping_cart' => 40,
									'status' => 25, // freezed gift voucher as product
									'track_inventory' => 30,
									'wearhouse_id' => "",
									'purchase_id' => "",
									'ship_stat' => 40,
									'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
									'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
									'account_no' => $account_id
								),
							);
		$this->db->insert_batch('pos_i1_products_2_num',$insert['standard']);			
		$insert['standard_inv'] = array(
								'inv_indx' => $this->taxes_model->make_single_uuid(),
								'product_id' => $standard_product_id,
								'current_stock' => 100,
								'reorder_stock' => 3,
								'reorder_qty' => 100,
								'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
								'location' => $outlet_id,
								'account_no' => $account_id
								);
		$this->db->insert('pos_i2_a_inventory',$insert['standard_inv']);			
		
		$insert['kilo'] = array(
								'product_id' => $kilo_product_id,
								'pos_id' => 1,
								'price' => 100,
								'margin' => 30,
								'retail_price' => 130,
								'sku' => 10002,
								'product_weight' => 1,
								'loyalty' => 1.3,
								'is_shopping_cart' => 40,
								'status' => 30,
								'track_inventory' => 30,
								'wearhouse_id' => "",
								'purchase_id' => "",
								'ship_stat' => 40,
								'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
								'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
								'account_no' => $account_id
								);
		$this->db->insert('pos_i1_products_3_kilo',$insert['kilo']);			
		$insert['kilo_inv'] = array(
								'inv_indx' => $this->taxes_model->make_single_uuid(),
								'product_id' => $kilo_product_id,
								'current_stock' => 100,
								'reorder_stock' => 3,
								'reorder_qty' => 100,
								'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
								'location' => $outlet_id,
								'account_no' => $account_id
								);
		$this->db->insert('pos_i2_a_inventory',$insert['kilo_inv']);	
				
		$insert['variant'] = array(
							'variant_index' => $variant_index,
							'product_id' => $variant_product_id,
							'price' => 2000,
							'margin' => 40,
							'retail_price' => 2800,
							'sku' => 10003,
							'product_weight' => 0.5,
							'loyalty' => 2.8,
							'position' => 0,
							'is_shopping_cart' => 30,
							'status' => 30,
							'track_inventory' => 30,
							'wearhouse_id' => "",
							'purchase_id' => "",
							'ship_stat' => 40,
							'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
							'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
							'account_no' => $account_id
							);
		$this->db->insert('pos_i1_products_1_variants',$insert['variant']);	
		$insert['variant_inv'] = array(
							'inv_var_indx' => $this->taxes_model->make_single_uuid(),
							'parent_product' => $variant_product_id,
							'variant_id' => $variant_index,
							'current_stock' => 100,
							'reorder_stock' => 3,
							'reorder_qty' => 100,
							'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
							'location' => $outlet_id,
							'account_no' => $account_id
							);			
		$this->db->insert('pos_i2_a_inventory_variant',$insert['variant_inv']);	

		$query = $this->db->get_where('pos_i1_0_cust_variant_types',array('account_no' => $account_id,'cust_var_value' => 'Size'));	
		$row = $query->row_array();
		$attr_id = $row['cust_var_id'];
		$insert['variant_attr'] = array(
							'attr_id' => $this->taxes_model->make_single_uuid(),
							'product_id' => $variant_product_id,
							'variant_id' => $variant_index,
							'attribute_id' => $attr_id,
							'attribute_val' => "Small",
							'account_no' => $account_id
							);
		$this->db->insert('pos_i1_products_1_variants_attributes',$insert['variant_attr']);		
		
		//quickeys
		$quickey_main_id = $this->taxes_model->make_single_uuid();
		$quickey_group_id = $this->taxes_model->make_single_uuid();
		$quickey_page_id = $this->taxes_model->make_single_uuid();
		$insert['quickey_main'] = array(
							'quick_index' => $quickey_main_id, 
							'quickey_name' => "Default",
							'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
							'is_delete' => 20,
							'account_no' => $account_id
							);
		$this->db->insert('pos_i7_quickeys',$insert['quickey_main']);		
		$insert['quickey_group'] = array(
							'qk_grp_index' => $quickey_group_id,
							'quickey_id' => $quickey_main_id,
							'grp_name' => "Default",
							'grp_position' => 0,
							'account_no' => $account_id		
							);
		$this->db->insert('pos_i8_quickey_group',$insert['quickey_group']);		
		$insert['quickey_page'] = array(
							'page_index' => $quickey_page_id,
							'quickey_id' => $quickey_main_id,
							'group_id' => $quickey_group_id,
							'page_no' => 0,
							'account_no' => $account_id
							);	
		$this->db->insert('pos_i8_quickey_group_page',$insert['quickey_page']);		
		$insert['quickey_child'] = array(
										array(
										'child_index' => $this->taxes_model->make_single_uuid(),
										'quickey_id' => $quickey_main_id,
										'prd_position' => 0,
										'group_page' => $quickey_page_id,
										'group' => $quickey_group_id,
										'product_id' => $standard_product_id,
										'colour' => "#bbf6be",
										'label' => $standard_product_name,
										'account_no' => $account_id
										),
										array(
										'child_index' => $this->taxes_model->make_single_uuid(),
										'quickey_id' => $quickey_main_id,
										'prd_position' => 1,
										'group_page' => $quickey_page_id,
										'group' => $quickey_group_id,
										'product_id' => $kilo_product_id,
										'colour' => "#f5afe2",
										'label' => $kilo_product_name,
										'account_no' => $account_id
										),
										array(
										'child_index' => $this->taxes_model->make_single_uuid(),
										'quickey_id' => $quickey_main_id,
										'prd_position' => 1,
										'group_page' => $quickey_page_id,
										'group' => $quickey_group_id,
										'product_id' => $variant_product_id,
										'colour' => "#f1a5a7",
										'label' => $variant_product_name,
										'account_no' => $account_id
										),
									);
		$this->db->insert_batch('pos_i9_quickey_child',$insert['quickey_child']);		
	}
	private function signup_customer_group($account_id)
	{
		$general_id = $this->taxes_model->make_single_uuid();
		$insert['group'] = array(
					'grp_index' => $general_id,
					'group_name' => "General Customers",
					'is_delete' => 20,
					'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
					'account_no' => $account_id		
					);
		$this->db->insert('pos_i2_b_customer_group',$insert['group']);		
		$insert['walkin'] = array(
								'cust_id' => $this->taxes_model->make_single_uuid(),
								'group_id' => $general_id,
								'c_code' => 'posantic-walkin',
								'c_company' => "",
								'c_dob' => "",
								'c_anniversary' => "",
								'c_name' => "Walkin Customers",
								'c_gender' => "M",
								'c_address_l1' => "",
								'c_address_l2' => "",
								'c_city' => "",
								'c_state' => "",
								'c_pincode' => "",
								'c_country' => "",
								'c_mobile' => "",
								'c_ll' => "",
								'c_email' => "",
								'c_fb_id' => "",
								'c_website' => "",
								'c_desc' => "",
								'c_lat' => NULL,
								'c_long' => NULL,
								'enable_loyalty' => 30,
								'cust_stat' => 40,
								'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
								'account_no' => $account_id	
							);
		$this->db->insert('pos_i2_customers',$insert['walkin']);		
	}
	private function signup_register($signup_array,$outlet_id,$account_id)
	{
		$this->db->select('template_id')->from('pos_c_reciept_template')->where('account_no',$account_id);
		$query = $this->db->get();
		$row = $query->row_array();
		$template_id = $row['template_id'];
		
		$this->db->select('quick_index')->from('pos_i7_quickeys')->where('account_no',$account_id);
		$query = $this->db->get();
		$row = $query->row_array();
		$quickey_id = $row['quick_index'];
		
		$insert = array(
					'reg_id' => $this->taxes_model->make_single_uuid(),
					'reg_code' => trim(ellipsize($signup_array['city'],12,1,"...")." register"),
					'reg_stat' => 30,
					'is_delete' => 10,
					'email_reciept' => 40,
					'print_reciept' => 30,
					'switch_users' => 40,
					'ask_quotes' => 40,
					'billno_sequence' => 1,
					'billno_prefix' => "",
					'receipt_template' => $template_id,
					'quickey_template' => $quickey_id,
					'rounding_method' => 1,
					'location' => $outlet_id,
					'account_no' => $account_id
					);
		$this->db->insert('pos_j1_registers',$insert);		
	}
	public function signup_account($signup_array)
	{
		$this->db->trans_begin();
		$this->db->trans_start();

		$account_id = $this->taxes_model->make_single_uuid();
		$tax_id = $this->taxes_model->make_single_uuid();
		$outlet_id = $this->taxes_model->make_single_uuid();
		$template_id = $this->taxes_model->make_single_uuid();
		$user_id = $this->taxes_model->make_single_uuid();
		$payment_master_id = array(
								'id_1' => $this->taxes_model->make_single_uuid(),
								'id_2' => $this->taxes_model->make_single_uuid(),
								'id_3' => $this->taxes_model->make_single_uuid(),
								'id_4' => $this->taxes_model->make_single_uuid(),
								);
		
		$this->signup_master($signup_array,$account_id);
		$this->signup_taxes($tax_id,$account_id);
		$this->signup_outlet($signup_array,$outlet_id,$tax_id,$account_id);
		$this->signup_template($template_id,$account_id);
		$hash_pass = $this->signup_login($signup_array,$user_id,$account_id);
		$this->signup_loyalty($account_id);
		$this->signup_payment_setup($payment_master_id,$account_id);
		$this->signup_supplier($signup_array,$account_id);
		$this->signup_variant_types($account_id);
		$this->signup_kilo_prefix($account_id);
		$this->signup_product_brand($account_id);
		$this->signup_product_category($account_id);
		$this->signup_product($outlet_id,$account_id);
		$this->signup_customer_group($account_id);
		$this->signup_register($signup_array,$outlet_id,$account_id);

		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return false;
		} else {
			if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') { // if ssl connection
				$http = 'https://';
			} else {
				$http = 'http://';
			}
			//$http = "";
			$info = parse_url(base_url());
			$host = $info['host'];			

			$host_names = explode(".", $host);
			$bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
			$root =  $signup_array['subdomain'].'.'.$bottom_host_name.'/welcome';
			$base_root =  $signup_array['subdomain'].'.'.$bottom_host_name.'/';
			$base_url = $bottom_host_name == 'localhost.com' ? $http.$signup_array['subdomain'].'.localhost.com/posantic/App/' : $http.$base_root;
			$redirect = $bottom_host_name == 'localhost.com' ? $http.$signup_array['subdomain'].'.localhost.com/posantic/App/welcome' : $http.$root;
			
			return array(
					'acc_no' => $account_id, 'subdomain' => strtolower($signup_array['subdomain']),
					'user_id' => $user_id, 'display_name' => $signup_array['contact_name'],
					'username' => $signup_array['contact_name'],'userpassword' => $signup_array['contact_password'],
					'user_mail' => $signup_array['contact_email'],'hash_pass' => $hash_pass,
					'userpassword' => $signup_array['contact_password'], 'pos_user' => $signup_array['contact_name'],
					'privelage' => 1, 'cmp_name' => $signup_array['store_name'],
					'base_url' => $base_url,'redirect_URL' => $redirect
					);	
		}
	}
	public function check_captcha_value($captcha){
		$expiration = now()-7200; // Two hour limit
		$ip = $_SERVER['REMOTE_ADDR'];
		$this->db->query("DELETE FROM pos_1_captcha WHERE captcha_time < ".$expiration);	
	
		// Then see if a captcha exists:
		$sql = "SELECT COUNT(*) AS count FROM pos_1_captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
		$binds = array($captcha, $ip, $expiration);
		$query = $this->db->query($sql, $binds);
		$row = $query->row();	
		if($row->count == 0)
		{
			return false;
		} else {
			return true;
		}
	}
}