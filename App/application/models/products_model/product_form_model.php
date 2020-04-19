<?php
class Product_form_model extends Product_model
{
    public function __construct() 
    {
        parent::__construct();
		$this->load->library('upload');
		$this->load->library('image_lib');
    }
	public function insert_num_product($data)
	{
		$tot_products = $this->product_count($data['acc']);
		$tot_products = $tot_products['grand_total'];
		$stk_limit = $this->account_stk_handle;
		if(($tot_products + 1) <= $stk_limit)
		{		
			if($this->check_sku_exists($data['sku'],$data['acc']))
			{
				if($this->check_barcode_prefix($data['sku'],$data['acc']))
				{
					$this->db->trans_begin();
					$this->db->trans_start();
					$prd_id = $data['product_id'];
					$insert['ins_main_prd'] = array(
									'product_id' => $prd_id,
									'product_name' => $data['p_name'],
									'product_scale' => 1,
									'handle' => str_replace(' ','',$data['p_handle']),
									'description' => $data['new_desc'],									
									'account_no' => $data['acc']
									);		
					$this->db->insert('pos_i1_products', $insert['ins_main_prd']);
					if(strlen($data['product_brand']) > 0)
					{
						$insert['ins_brand'] = array(
											'prd_brand_id' => $this->taxes_model->make_single_uuid(),
											'product_id' => $prd_id,
											'brand_id' => $data['product_brand'],
											'account_no' => $data['acc']						
												);
						$this->db->insert('pos_i1_products_6_brand', $insert['ins_brand']);
					}
					if(strlen($data['product_cat']) > 0)
					{
						$insert['ins_cat'] = array(
											'prd_cat_id' => $this->taxes_model->make_single_uuid(),
											'product_id' => $prd_id,
											'category_id' => $data['product_cat'],
											'account_no' => $data['acc']				
												);
						$this->db->insert('pos_i1_products_7_category', $insert['ins_cat']);
					}
					if(strlen($data['new_supplier']) > 0)
					{
						$insert['ins_supp'] = array(
											'prd_supplier_id' => $this->taxes_model->make_single_uuid(),
											'product_id' => $prd_id,
											'supplier_id' => $data['new_supplier'],
											'account_no' => $data['acc']				
												);
						$this->db->insert('pos_i1_products_8_supplier', $insert['ins_supp']);
					}
					$insert['ins_prd'] = array(
									'product_id' => $prd_id,
									'price' => $data['price'],
									'margin' => $data['margin'],
									'retail_price' => $data['retail'],
									'sku' => $data['sku'],
									'product_weight' => $data['prd_weight'],
									'loyalty' => $data['loyalty_val'],
									'is_shopping_cart' => $data['show_cart'],
									'status' => $data['visib_stat'],
									'track_inventory' => $data['trace_inv'],
									'wearhouse_id' => $data['prd_wh_id'],
									'purchase_id' => $data['prd_pur_id'],
									'ship_stat' => $data['ship_stat'],
									'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
									'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
									'account_no' => $data['acc'],
									); 
					$this->db->insert('pos_i1_products_2_num', $insert['ins_prd']);
					if(count($data['tag_id']) > 0)
					{
						foreach($data['tag_id'] as $tag_id)
						{
							$insert['ins_tag'][] = array(
												'prd_tag_index' => $this->taxes_model->make_single_uuid(),
												'tagged_id' => $tag_id,
												'product_id' => $prd_id,
												'account_no' => $data['acc']							
												);
						}
						$this->db->insert_batch('pos_i1_products_5_tags', $insert['ins_tag']);
					}
					foreach($data['def_location'] as $key => $outlet_id)
					{
						if($data['sale_tax'][$key] != '' && !empty($outlet_id))
						{
							$insert['tax'][] = array(
										'tax_index' => $this->taxes_model->make_single_uuid(),
										'tax_id' => $data['sale_tax'][$key],
										'main_product' => $prd_id,
										'location' => $outlet_id,
										'account_no' => $data['acc']
										); 
						}
					}
					if(array_key_exists('tax',$insert))
					{
						$this->db->insert_batch('pos_i1_products_tax', $insert['tax']);
					}
					if($data['trace_inv'] == 30)
					{
						if(array_key_exists($key,$data['cur_stk']))
						{						
							foreach($data['inv_outlet'] as $key => $outlet_id)
							{
								if(!is_null($outlet_id))
								{
									$insert['inv'][] = array(
												'inv_indx' => $this->taxes_model->make_single_uuid(),
												'product_id' => $prd_id,
												'current_stock' => $data['cur_stk'][$key],
												'reorder_stock' => $data['reorder_stk'][$key],
												'reorder_qty' => $data['reorder_qty'][$key],
												'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
												'location' => $outlet_id,
												'account_no' => $data['acc'],
												);
								}
							}
							if(array_key_exists('inv',$insert))
							{
								$this->db->insert_batch('pos_i2_a_inventory', $insert['inv']);
							}
						}
					}
					foreach($data['inv_outlet'] as $key => $outlet_id)
					{
						if(!is_null($outlet_id))
						{
							$insert['log'][] = array(
										'log_index' => $this->taxes_model->make_single_uuid(),
										'user_id' => $data['user_id'],	
										'master_product' => $prd_id,
										'log_code' => 3,	
										'feed' => $data['cur_stk'][$key],	
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
										'location' => $outlet_id,	
										'account_no' => $data['acc'],	
										);				
						}
					}
					if(array_key_exists('log',$insert))
					{
						$this->db->insert_batch('pos_i1_products_log', $insert['log']);
					}
					if(!empty($_FILES['userfile']['name'])) {
						$bool = $this->do_upload($prd_id);
					}
					$this->db->trans_complete();
					if($this->db->trans_status() === FALSE)
					{
						return 0;
					} else {
						return 1;	
					}
				} else {
					return 2;	
				}
			} else {
				return 3;		
			}
		} else {
			return 4;
		}
	}
	public function insert_kilo_product($data)
	{
		if($this->kilo_prd_count_ls_99999($data['acc']))
		{
			$tot_products = $this->product_count($data['acc']);
			$tot_products = $tot_products['grand_total'];
			$stk_limit = $this->account_stk_handle;
			if(($tot_products + 1) <= $stk_limit)
			{		
				if($this->check_sku_exists($data['sku'],$data['acc']))
				{
					if($this->check_barcode_prefix($data['sku'],$data['acc']))
					{
						$this->db->trans_begin();
						$this->db->trans_start();
						$prd_id = $data['product_id'];
						$insert['ins_main_prd'] = array(
										'product_id' => $prd_id,
										'product_name' => $data['p_name'],
										'product_scale' => 2,
										'handle' => str_replace(' ','',$data['p_handle']),
										'description' => $data['new_desc'],									
										'account_no' => $data['acc'],
										);		
						$this->db->insert('pos_i1_products', $insert['ins_main_prd']);
						if(strlen($data['product_brand']) > 0)
						{
							$insert['ins_brand'] = array(
												'prd_brand_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $prd_id,
												'brand_id' => $data['product_brand'],
												'account_no' => $data['acc']						
													);
							$this->db->insert('pos_i1_products_6_brand', $insert['ins_brand']);
						}
						if(strlen($data['product_cat']) > 0)
						{
							$insert['ins_cat'] = array(
												'prd_cat_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $prd_id,
												'category_id' => $data['product_cat'],
												'account_no' => $data['acc']				
													);
							$this->db->insert('pos_i1_products_7_category', $insert['ins_cat']);
						}
						if(strlen($data['new_supplier']) > 0)
						{
							$insert['ins_supp'] = array(
												'prd_supplier_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $prd_id,
												'supplier_id' => $data['new_supplier'],
												'account_no' => $data['acc']				
													);
							$this->db->insert('pos_i1_products_8_supplier', $insert['ins_supp']);
						}
						$insert['ins_prd'] = array(
										'product_id' => $prd_id,
										'pos_id' => $this->make_pos_id($data['acc']),
										'price' => $data['price'],
										'margin' => $data['margin'],
										'retail_price' => $data['retail'],
										'sku' => $data['sku'],
										'product_weight' => $data['prd_weight'],
										'loyalty' => $data['loyalty_val'],
										'is_shopping_cart' => $data['show_cart'],
										'status' => $data['visib_stat'],
										'track_inventory' => $data['trace_inv'],
										'wearhouse_id' => $data['prd_wh_id'],
										'purchase_id' => $data['prd_pur_id'],
										'ship_stat' => $data['ship_stat'],
										'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
										'account_no' => $data['acc'],
										); 
						$this->db->insert('pos_i1_products_3_kilo', $insert['ins_prd']);
						if(count($data['tag_id']) > 0)
						{
							foreach($data['tag_id'] as $tag_id)
							{
								$insert['ins_tag'][] = array(
													'prd_tag_index' => $this->taxes_model->make_single_uuid(),
													'tagged_id' => $tag_id,
													'product_id' => $prd_id,
													'account_no' => $data['acc']							
													);
							}
							$this->db->insert_batch('pos_i1_products_5_tags', $insert['ins_tag']);
						}						
						foreach($data['def_location'] as $key => $outlet_id)
						{
							if($data['sale_tax'][$key] != '' && !empty($outlet_id))
							{
								$insert['tax'][] = array(
											'tax_index' => $this->taxes_model->make_single_uuid(),
											'tax_id' => $data['sale_tax'][$key],
											'main_product' => $prd_id,
											'location' => $outlet_id,
											'account_no' => $data['acc']
											); 
							}
						}
						if(array_key_exists('tax',$insert))
						{
							$this->db->insert_batch('pos_i1_products_tax', $insert['tax']);
						}
						if($data['trace_inv'] == 30)
						{
							if(array_key_exists($key,$data['cur_stk']))
							{							
								foreach($data['inv_outlet'] as $key => $outlet_id)
								{
									if(!is_null($outlet_id))
									{							
										$insert['inv'][] = array(
													'inv_indx' => $this->taxes_model->make_single_uuid(),
													'product_id' => $prd_id,
													'current_stock' => $data['cur_stk'][$key],
													'reorder_stock' => $data['reorder_stk'][$key],
													'reorder_qty' => $data['reorder_qty'][$key],
													'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
													'location' => $outlet_id,
													'account_no' => $data['acc'],
													);
									}
								}
								if(array_key_exists('inv',$insert))
								{
									$this->db->insert_batch('pos_i2_a_inventory', $insert['inv']);
								}
							}
						}
						foreach($data['inv_outlet'] as $key => $outlet_id)
						{
							if(!is_null($outlet_id))
							{
								$insert['log'][] = array(
											'log_index' => $this->taxes_model->make_single_uuid(),
											'user_id' => $data['user_id'],	
											'master_product' => $prd_id,
											'log_code' => 3,	
											'feed' => $data['cur_stk'][$key],	
											'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
											'location' => $outlet_id,	
											'account_no' => $data['acc'],	
											);				
							}
						}
						if(array_key_exists('log',$insert))
						{
							$this->db->insert_batch('pos_i1_products_log', $insert['log']);
						}
						if(!empty($_FILES['userfile']['name'])) {
							$bool = $this->do_upload($prd_id);
						}
						$this->db->trans_complete();
						if($this->db->trans_status() === FALSE)
						{
							return 0;
						} else {
							return 1;	
						}
					} else {
						return 2;	
					}
				} else {
					return 3;		
				}
			} else {
				return 4;
			}
		} else {
			return 5;	
		}
	}
	public function insert_variant_product($data)
	{
		$tot_products = $this->product_count($data['acc']);
		$tot_products = $tot_products['grand_total'];
		$stk_limit = $this->account_stk_handle;
		if(($tot_products + 1) <= $stk_limit)
		{		
			if($this->check_sku_exists($data['sku'],$data['acc']))
			{
				if($this->check_barcode_prefix($data['sku'],$data['acc']))
				{
					if(count(array_filter($data['var_type_name'])) > 0)
					{
						$this->db->trans_begin();
						$this->db->trans_start();
						$prd_id = $data['parent_product_id'];
						$insert['ins_main_prd'] = array(
										'product_id' => $prd_id,
										'product_name' => $data['p_name'],
										'product_scale' => 3,
										'handle' => str_replace(' ','',$data['p_handle']),
										'description' => $data['new_desc'],									
										'account_no' => $data['acc'],
										);		
						$this->db->insert('pos_i1_products', $insert['ins_main_prd']);
						$variant_index = $data['variant_id'];
						$insert['ins_prd'] = array(
										'variant_index' => $variant_index,
										'product_id' => $prd_id,
										'price' => $data['price'],
										'margin' => $data['margin'],
										'retail_price' => $data['retail'],
										'sku' => $data['sku'],
										'product_weight' => $data['prd_weight'],
										'loyalty' => $data['loyalty_val'],
										'position' => 1,
										'is_shopping_cart' => $data['show_cart'],
										'status' => $data['visib_stat'],
										'track_inventory' => $data['trace_inv'],
										'wearhouse_id' => $data['prd_wh_id'],
										'purchase_id' => $data['prd_pur_id'],
										'ship_stat' => $data['ship_stat'],
										'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
										'account_no' => $data['acc'],
										); 
						$this->db->insert('pos_i1_products_1_variants', $insert['ins_prd']);
						$data['var_type_name'] = array_slice($data['var_type_name'],0,$data['max_variants'],true);
						foreach($data['var_type_name'] as $a_key => $attribute)
						{
							$insert['attribute'][] = array(
													'attr_id' => $this->taxes_model->make_single_uuid(),
													'product_id' => $prd_id,
													'variant_id' => $variant_index,
													'attribute_id' => $attribute,
													'attribute_val' => str_replace(',','',$data['new_var_method'][$a_key]),
													'account_no' => $data['acc']						
													);	
						}
						$this->db->insert_batch('pos_i1_products_1_variants_attributes', $insert['attribute']);
						if(count($data['tag_id']) > 0)
						{
							foreach($data['tag_id'] as $tag_id)
							{
								$insert['ins_tag'][] = array(
													'prd_tag_index' => $this->taxes_model->make_single_uuid(),
													'tagged_id' => $tag_id,
													'product_id' => $prd_id,
													'account_no' => $data['acc']							
														);
							}
							$this->db->insert_batch('pos_i1_products_5_tags', $insert['ins_tag']);
						}	
						if(strlen($data['product_brand']) > 0)
						{
							$insert['ins_brand'] = array(
												'prd_brand_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $prd_id,
												'brand_id' => $data['product_brand'],
												'account_no' => $data['acc']						
													);
							$this->db->insert('pos_i1_products_6_brand', $insert['ins_brand']);
						}
						if(strlen($data['product_cat']) > 0)
						{
							$insert['ins_cat'] = array(
												'prd_cat_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $prd_id,
												'category_id' => $data['product_cat'],
												'account_no' => $data['acc']				
													);
							$this->db->insert('pos_i1_products_7_category', $insert['ins_cat']);
						}
						if(strlen($data['new_supplier']) > 0)
						{
							$insert['ins_supp'] = array(
												'prd_supplier_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $prd_id,
												'supplier_id' => $data['new_supplier'],
												'account_no' => $data['acc']				
													);
							$this->db->insert('pos_i1_products_8_supplier', $insert['ins_supp']);
						}
						foreach($data['def_location'] as $key => $outlet_id)
						{
							if($data['sale_tax'][$key] != '' && !empty($outlet_id))
							{
								$insert['tax'][] = array(
											'tax_var_index' => $this->taxes_model->make_single_uuid(),
											'tax_id' => $data['sale_tax'][$key],
											'product_id' => $prd_id,
											'variant_id' => $variant_index,
											'location' => $outlet_id,
											'account_no' => $data['acc']
											); 
							}
						}
						if(array_key_exists('tax',$insert))
						{
							$this->db->insert_batch('pos_i1_products_tax_variant', $insert['tax']);
						}
						if($data['trace_inv'] == 30)
						{
							if(array_key_exists($key,$data['cur_stk']))
							{							
								foreach($data['inv_outlet'] as $key => $outlet_id)
								{
									if(!is_null($outlet_id))
									{
										$insert['inv'][] = array(
													'inv_var_indx' => $this->taxes_model->make_single_uuid(),
													'parent_product' => $prd_id,
													'variant_id' => $variant_index,
													'current_stock' => $data['cur_stk'][$key],
													'reorder_stock' => $data['reorder_stk'][$key],
													'reorder_qty' => $data['reorder_qty'][$key],
													'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
													'location' => $outlet_id,
													'account_no' => $data['acc']
													);
									}
								}
								if(array_key_exists('inv',$insert))
								{
									$this->db->insert_batch('pos_i2_a_inventory_variant', $insert['inv']);
								}
							}
						}
						foreach($data['inv_outlet'] as $key => $outlet_id)
						{
							if(!is_null($outlet_id))
							{
								$insert['log'][] = array(
											'log_var_index' => $this->taxes_model->make_single_uuid(),
											'user_id' => $data['user_id'],	
											'master_product' => $prd_id,
											'variant_id' => $variant_index,
											'log_code' => 3,
											'feed' => $data['cur_stk'][$key],	
											'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
											'location' => $outlet_id,	
											'account_no' => $data['acc']
											);				
							}
						}
						if(array_key_exists('log',$insert))
						{
							$this->db->insert_batch('pos_i1_products_log_variants', $insert['log']);
						}
						if(!empty($_FILES['userfile']['name'])) {
							$bool = $this->do_upload($variant_index);
						}
						$this->db->trans_complete();
						if($this->db->trans_status() === FALSE)
						{
							return 0;
						} else {
							return 1;	
						}
					} else {
						return 6;	
					}
				} else {
					return 2;	
				}		
			} else {
				return 3;		
			}
		} else {
			return 4;
		}
	}
	public function insert_child_variant_product($data)
	{
		if($this->check_child_variant_exist($data['new_var_method'],$data['product_id'],$data['acc']))
		{
			$tot_products = $this->product_count($data['acc']);
			$tot_products = $tot_products['grand_total'];
			$stk_limit = $this->account_stk_handle;
			if(($tot_products + 1) <= $stk_limit)
			{		
				if($this->check_sku_exists($data['sku'],$data['acc']))
				{
					if($this->check_barcode_prefix($data['sku'],$data['acc']))
					{
						if(count(array_filter($data['var_type_name'])) > 0)
						{
							$this->db->trans_begin();
							$this->db->trans_start();
							$variant_index = $this->taxes_model->make_single_uuid();
							$insert['ins_prd'] = array(
											'variant_index' => $variant_index,
											'product_id' => $data['product_id'],
											'price' => $data['price'],
											'margin' => $data['margin'],
											'retail_price' => $data['retail'],
											'sku' => $data['sku'],
											'product_weight' => $data['prd_weight'],
											'loyalty' => $data['loyalty_val'],
											'position' => 1,
											'is_shopping_cart' => $data['show_cart'],
											'status' => $data['visib_stat'],
											'track_inventory' => $data['trace_inv'],
											'wearhouse_id' => $data['prd_wh_id'],
											'purchase_id' => $data['prd_pur_id'],
											'ship_stat' => $data['ship_stat'],
											'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
											'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
											'account_no' => $data['acc'],
											); 
							$this->db->insert('pos_i1_products_1_variants', $insert['ins_prd']);
							$data['var_type_name'] = array_slice($data['var_type_name'],0,$data['max_variants'],true);
							foreach($data['var_type_name'] as $a_key => $attribute)
							{
								$insert['attribute'][] = array(
														'attr_id' => $this->taxes_model->make_single_uuid(),
														'product_id' => $data['product_id'],
														'variant_id' => $variant_index,
														'attribute_id' => $attribute,
														'attribute_val' => str_replace(',','',$data['new_var_method'][$a_key]),
														'account_no' => $data['acc']						
														);	
							}
							$this->db->insert_batch('pos_i1_products_1_variants_attributes', $insert['attribute']);
							foreach($data['def_location'] as $key => $outlet_id)
							{
								if($data['sale_tax'][$key] != '' && !empty($outlet_id))
								{
									$insert['tax'][] = array(
												'tax_var_index' => $this->taxes_model->make_single_uuid(),
												'tax_id' => $data['sale_tax'][$key],
												'product_id' => $data['product_id'],
												'variant_id' => $variant_index,
												'location' => $outlet_id,
												'account_no' => $data['acc']
												); 
								}
							}
							if(array_key_exists('tax',$insert))
							{
								$this->db->insert_batch('pos_i1_products_tax_variant', $insert['tax']);
							}
							if($data['trace_inv'] == 30)
							{
								if(array_key_exists($key,$data['cur_stk']))
								{
									foreach($data['inv_outlet'] as $key => $outlet_id)
									{
										if(!is_null($outlet_id))
										{
											$insert['inv'][] = array(
														'inv_var_indx' => $this->taxes_model->make_single_uuid(),
														'parent_product' => $data['product_id'],
														'variant_id' => $variant_index,
														'current_stock' => $data['cur_stk'][$key],
														'reorder_stock' => $data['reorder_stk'][$key],
														'reorder_qty' => $data['reorder_qty'][$key],
														'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
														'location' => $outlet_id,
														'account_no' => $data['acc'],
														);
										}
									}
									if(array_key_exists('inv',$insert))
									{
										$this->db->insert_batch('pos_i2_a_inventory_variant', $insert['inv']);
									}
								}
							}
							foreach($data['inv_outlet'] as $key => $outlet_id)
							{
								if(!is_null($outlet_id))
								{	
									$insert['log'][] = array(
												'log_var_index' => $this->taxes_model->make_single_uuid(),
												'user_id' => $data['user_id'],	
												'master_product' => $data['product_id'],	
												'variant_id' => $variant_index,
												'log_code' => 3,
												'feed' => $data['cur_stk'][$key],	
												'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
												'location' => $outlet_id,	
												'account_no' => $data['acc']
												);				
								}
							}
							if(array_key_exists('log',$insert))
							{
								$this->db->insert_batch('pos_i1_products_log_variants', $insert['log']);
							}
							if(!empty($_FILES['userfile']['name'])) {
								$bool = $this->do_upload($variant_index);
							}
							$this->db->trans_complete();
							if($this->db->trans_status() === FALSE)
							{
								return 0;
							} else {
								return 1;	
							}
						} else {
							return 6;	
						}
					} else {
						return 2;	
					}		
				} else {
					return 3;		
				}
			} else {
				return 4;
			}
		} else {
			return 5;	
		}
	}
	public function insert_blend_product($data)
	{
		$tot_products = $this->product_count($data['acc']);
		$tot_products = $tot_products['grand_total'];
		$stk_limit = $this->account_stk_handle;
		if(($tot_products + 1) <= $stk_limit)
		{		
			if($this->check_sku_exists($data['sku'],$data['acc']))
			{
				if($this->check_barcode_prefix($data['sku'],$data['acc']))
				{
					$this->db->trans_begin();
					$this->db->trans_start();
					$prd_id = $data['product_id'];
					$insert['main_prd'] = array(
								'product_id' => $prd_id,
								'product_name' => $data['p_name'],
								'product_scale' => 4,
								'handle' => str_replace(' ','',$data['p_handle']),
								'description' => $data['new_desc'],									
								'account_no' => $data['acc']					
								);					
					$this->db->insert('pos_i1_products', $insert['main_prd']);
					$insert['ins_main_blend_prd'] = array(
									'blend_product_id' => $prd_id,
									'price' => $data['price'],
									'margin' => $data['margin'],
									'retail_price' => $data['retail'],
									'sku' => $data['sku'],
									'product_weight' => $data['prd_weight'],
									'loyalty' => $data['loyalty_val'],
									'is_shopping_cart' => $data['show_cart'],
									'wearhouse_id' => $data['prd_wh_id'],
									'purchase_id' => $data['prd_pur_id'],
									'ship_stat' => $data['ship_stat'],
									'status' => $data['visib_stat'],
									'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
									'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
									'account_no' => $data['acc'],
									); 
					$this->db->insert('pos_i1_products_0_blend', $insert['ins_main_blend_prd']);
					if(strlen($data['product_brand']) > 0)
					{
						$insert['ins_brand'] = array(
											'prd_brand_id' => $this->taxes_model->make_single_uuid(),
											'product_id' => $prd_id,
											'brand_id' => $data['product_brand'],
											'account_no' => $data['acc']						
												);
						$this->db->insert('pos_i1_products_6_brand', $insert['ins_brand']);
					}
					if(strlen($data['product_cat']) > 0)
					{
						$insert['ins_cat'] = array(
											'prd_cat_id' => $this->taxes_model->make_single_uuid(),
											'product_id' => $prd_id,
											'category_id' => $data['product_cat'],
											'account_no' => $data['acc']				
												);
						$this->db->insert('pos_i1_products_7_category', $insert['ins_cat']);
					}
					if(strlen($data['new_supplier']) > 0)
					{
						$insert['ins_supp'] = array(
											'prd_supplier_id' => $this->taxes_model->make_single_uuid(),
											'product_id' => $prd_id,
											'supplier_id' => $data['new_supplier'],
											'account_no' => $data['acc']				
												);
						$this->db->insert('pos_i1_products_8_supplier', $insert['ins_supp']);
					}
					if(count($data['tag_id']) > 0)
					{
						foreach($data['tag_id'] as $tag_id)
						{
							$insert['ins_tag'][] = array(
												'prd_tag_index' => $this->taxes_model->make_single_uuid(),
												'tagged_id' => $tag_id,
												'product_id' => $prd_id,
												'account_no' => $data['acc']							
												);
						}
						$this->db->insert_batch('pos_i1_products_5_tags', $insert['ins_tag']);
					}					
					foreach($data['def_location'] as $key => $outlet_id)
					{
						if($data['sale_tax'][$key] != '' && !empty($outlet_id))
						{
							$insert['tax'][] = array(
										'tax_index' => $this->taxes_model->make_single_uuid(),
										'tax_id' => $data['sale_tax'][$key],
										'main_product' => $prd_id,
										'location' => $outlet_id,
										'account_no' => $data['acc']
										); 
						}
					}
					if(array_key_exists('tax',$insert))
					{
						$this->db->insert_batch('pos_i1_products_tax', $insert['tax']);
					}
					foreach($data['blend_product_id'] as $key => $blend_product_id)
					{
						$scale = $this->check_scale($blend_product_id,$data['acc']);
						if($scale == 3)
						{
							$main_product_id = $this->get_main_id_wrt_variant_id($blend_product_id,$data['acc']);
							$insert['blend_inv_var'][] = array(
										'blend_var_index' => $this->taxes_model->make_single_uuid(),
										'blend_product' => $prd_id,
										'product_id' => $main_product_id,
										'variant_id' => $blend_product_id,
										'variant_qty' => $data['blend_prd_qty'][$key],
										'account_no' => $data['acc'],
										);							
						} else {
							$insert['blend_inv_main'][] = array(
										'blend_index' => $this->taxes_model->make_single_uuid(), 
										'blend_product' => $prd_id,
										'parent_product' => $blend_product_id,
										'parent_qty' => $data['blend_prd_qty'][$key],
										'account_no' => $data['acc'],
										);
						}
					}
					if(isset($insert['blend_inv_main']))
					{
						$this->db->insert_batch('pos_i1_products_4_blend', $insert['blend_inv_main']);
					} 
					if(isset($insert['blend_inv_var']))
					{
						$this->db->insert_batch('pos_i1_products_4_blend_variant', $insert['blend_inv_var']);
					}
					foreach($data['inv_outlet'] as $key => $outlet_id)
					{
						if(!is_null($outlet_id))
						{
							$get_min_stock = $this->get_min_blend_qty($data['blend_product_id'],$data['blend_prd_qty'],$data['acc'],$outlet_id);
							$insert['log'][] = array(
										'log_index' => $this->taxes_model->make_single_uuid(),
										'user_id' => $data['user_id'],	
										'master_product' => $prd_id,
										'log_code' => 3,	
										'feed' => $get_min_stock,	
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
										'location' => $outlet_id,	
										'account_no' => $data['acc'],	
										);				
						}
					}
					if(array_key_exists('log',$insert))
					{
						$this->db->insert_batch('pos_i1_products_log', $insert['log']);
					}
					if(!empty($_FILES['userfile']['name'])) {
						$bool = $this->do_upload($prd_id);
					}
					$this->db->trans_complete();
					if($this->db->trans_status() === FALSE)
					{
						return 0;
					} else {
						return 1;	
					}
				} else {
					return 2;	
				}		
			} else {
				return 3;	
			}
		} else {
			return 4;	
		}
	}
	public function update_num($data)
	{
		if($this->check_sku_not_this_product($data['sku'],$data['product_id'],$data['acc']))
		{
			if($this->check_barcode_prefix($data['sku'],$data['acc']))
			{
				$this->db->trans_begin();
				$this->db->trans_start();
				$update['main_prd'] = array(
								'product_name' => $data['p_name'],
								'handle' => str_replace(' ','',$data['p_handle']),
								'description' => $data['new_desc'],									
								);		
				$this->db->where('product_id', $data['product_id']);
				$this->db->where('account_no', $data['acc']);
				$this->db->update('pos_i1_products', $update['main_prd']);
				$update['sub_prd'] = array(
								'price' => $data['price'],
								'margin' => $data['margin'],
								'retail_price' => $data['retail'],
								'sku' => $data['sku'],
								'product_weight' => $data['prd_weight'],
								'loyalty' => $data['loyalty_val'],
								'is_shopping_cart' => $data['show_cart'],
								'status' => $data['visib_stat'],
								'track_inventory' => $data['trace_inv'],
								'wearhouse_id' => $data['prd_wh_id'],
								'purchase_id' => $data['prd_pur_id'],
								'ship_stat' => $data['ship_stat'],
								'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
								); 
				$this->db->where('product_id', $data['product_id']);
				$this->db->where('account_no', $data['acc']);
				$this->db->update('pos_i1_products_2_num', $update['sub_prd']);
				if(empty($data['new_supplier']))
				{
					$this->db->delete('pos_i1_products_8_supplier', array('product_id' => $data['product_id'], 'account_no' => $data['acc']));
				} else {
					$check = $this->check_eav_exist('pos_i1_products_8_supplier','product_id',$data['product_id'],$data['acc']);
					if($check)
					{
						$this->db->where('product_id', $data['product_id']);
						$this->db->where('account_no', $data['acc']);
						$this->db->update('pos_i1_products_8_supplier', array('supplier_id' => $data['new_supplier']));						
					} else {						
						$insert['supplier'] = array(
												'prd_supplier_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $data['product_id'],
												'supplier_id' => $data['new_supplier'],
												'account_no' => $data['acc'],						
													);
						$this->db->insert('pos_i1_products_8_supplier', $insert['supplier']);
					}
				}
				if(empty($data['product_cat']))
				{
					$this->db->delete('pos_i1_products_7_category', array('product_id' => $data['product_id'], 'account_no' => $data['acc']));
				} else {
					$check = $this->check_eav_exist('pos_i1_products_7_category','product_id',$data['product_id'],$data['acc']);
					if($check)
					{
						$this->db->where('product_id', $data['product_id']);
						$this->db->where('account_no', $data['acc']);
						$this->db->update('pos_i1_products_7_category', array('category_id' => $data['product_cat']));						
					} else {						
						$insert['category'] = array(
												'prd_cat_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $data['product_id'],
												'category_id' => $data['product_cat'],
												'account_no' => $data['acc'],						
													);
						$this->db->insert('pos_i1_products_7_category', $insert['category']);
					}
				}

				if(empty($data['product_brand']))
				{
					$this->db->delete('pos_i1_products_6_brand', array('product_id' => $data['product_id'], 'account_no' => $data['acc']));
				} else {
					$check = $this->check_eav_exist('pos_i1_products_6_brand','product_id',$data['product_id'],$data['acc']);
					if($check)
					{
						$this->db->where('product_id', $data['product_id']);
						$this->db->where('account_no', $data['acc']);
						$this->db->update('pos_i1_products_6_brand', array('brand_id' => $data['product_brand']));						
					} else {						
						$insert['brand'] = array(
												'prd_brand_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $data['product_id'],
												'brand_id' => $data['product_brand'],
												'account_no' => $data['acc'],						
													);
						$this->db->insert('pos_i1_products_6_brand', $insert['brand']);
					}
				}
				foreach($data['def_location'] as $key => $outlet_id)
				{
					$check = $this->check_product_tax_exist($data['product_id'],$outlet_id,$data['acc']);
					if($check)
					{
						if($data['sale_tax'][$key] != '' && !empty($outlet_id))
						{
							$update['tax'] = array(
										'tax_id' => $data['sale_tax'][$key],
										); 
							$this->db->where('main_product', $data['product_id']);			
							$this->db->where('location', $outlet_id);			
							$this->db->where('account_no', $data['acc']);			
							$this->db->update('pos_i1_products_tax', $update['tax']);
						} else {
							$this->db->delete('pos_i1_products_tax', array('main_product' => $data['product_id'],'account_no' => $data['acc'],'location' => $outlet_id)); 
						}
					} else {
						if($data['sale_tax'][$key] != '' && !empty($outlet_id))
						{
							$insert['tax'] = array(
								'tax_index' => $this->taxes_model->make_single_uuid(),
								'tax_id' => $data['sale_tax'][$key],
								'main_product' => $data['product_id'],
								'location' => $outlet_id,
								'account_no' => $data['acc']
							); 
							$this->db->insert('pos_i1_products_tax', $insert['tax']); 
						}
					}
				}
				if($data['trace_inv'] == 30)
				{
					foreach($data['inv_outlet'] as $key => $outlet_id)
					{
						if(!is_null($outlet_id))
						{	
							if(array_key_exists($key,$data['cur_stk']))
							{
								$assure = $this->check_inventory_stock_exist($data['cur_stk'][$key],$data['product_id'],$outlet_id,$data['acc']);
								if(!$assure)
								{
									$insert['log'] = array(
												'log_index' => $this->taxes_model->make_single_uuid(),
												'user_id' => $data['user_id'],	
												'master_product' => $data['product_id'],																																									
												'log_code' => 2,	
												'feed' => $data['cur_stk'][$key],	
												'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
												'location' => $outlet_id,	
												'account_no' => $data['acc'],	
												);				
									$this->db->insert('pos_i1_products_log', $insert['log']);
								}						
								$check = $this->check_inventory_exist($data['product_id'],$outlet_id,$data['acc']);
								if($check)
								{
									$update['inv'] = array(
												'current_stock' => $data['cur_stk'][$key],
												'reorder_stock' => $data['reorder_stk'][$key],
												'reorder_qty' => $data['reorder_qty'][$key],
												'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
												);
									$this->db->where('product_id', $data['product_id']);			
									$this->db->where('location', $outlet_id);			
									$this->db->where('account_no', $data['acc']);			
									$this->db->update('pos_i2_a_inventory', $update['inv']);
								} else {
									$insert['inv'] = array(
												'inv_indx' => $this->taxes_model->make_single_uuid(),
												'product_id' => $data['product_id'],
												'current_stock' => $data['cur_stk'][$key],
												'reorder_stock' => $data['reorder_stk'][$key],
												'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
												'location' => $outlet_id,
												'account_no' => $data['acc'],
												);
									$this->db->insert('pos_i2_a_inventory',$insert['inv']);
								}
							}
						}
					}
				} else {
					$this->db->delete('pos_i2_a_inventory', array('product_id' => $data['product_id'],'account_no' => $data['acc'])); 
				}
				if(!empty($_FILES['userfile']['name'])) {
					$bool = $this->do_upload($data['product_id']);
				}
				$this->db->trans_complete();
				if($this->db->trans_status() === FALSE)
				{
					return 0;
				} else {
					return 1;	
				}
			} else {
				return 2;	
			}
		} else {
			return 3;
		}
	}
	public function update_kilo($data)
	{
		if($this->check_sku_not_this_product($data['sku'],$data['product_id'],$data['acc']))
		{
			if($this->check_barcode_prefix($data['sku'],$data['acc']))
			{
				$this->db->trans_begin();
				$this->db->trans_start();
				$update['main_prd'] = array(
								'product_name' => $data['p_name'],
								'handle' => str_replace(' ','',$data['p_handle']),
								'description' => $data['new_desc'],									
								);		
				$this->db->where('product_id', $data['product_id']);
				$this->db->where('account_no', $data['acc']);
				$this->db->update('pos_i1_products', $update['main_prd']);
				$update['sub_prd'] = array(
								'price' => $data['price'],
								'margin' => $data['margin'],
								'retail_price' => $data['retail'],
								'sku' => $data['sku'],
								'product_weight' => $data['prd_weight'],
								'loyalty' => $data['loyalty_val'],
								'is_shopping_cart' => $data['show_cart'],
								'status' => $data['visib_stat'],
								'track_inventory' => $data['trace_inv'],
								'wearhouse_id' => $data['prd_wh_id'],
								'purchase_id' => $data['prd_pur_id'],
								'ship_stat' => $data['ship_stat'],
								'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
								); 
				$this->db->where('product_id', $data['product_id']);
				$this->db->where('account_no', $data['acc']);
				$this->db->update('pos_i1_products_3_kilo', $update['sub_prd']);

				if(empty($data['new_supplier']))
				{
					$this->db->delete('pos_i1_products_8_supplier', array('product_id' => $data['product_id'], 'account_no' => $data['acc']));
				} else {
					$check = $this->check_eav_exist('pos_i1_products_8_supplier','product_id',$data['product_id'],$data['acc']);
					if($check)
					{
						$this->db->where('product_id', $data['product_id']);
						$this->db->where('account_no', $data['acc']);
						$this->db->update('pos_i1_products_8_supplier', array('supplier_id' => $data['new_supplier']));						
					} else {						
						$insert['supplier'] = array(
												'prd_supplier_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $data['product_id'],
												'supplier_id' => $data['new_supplier'],
												'account_no' => $data['acc'],						
													);
						$this->db->insert('pos_i1_products_8_supplier', $insert['supplier']);
					}
				}
				if(empty($data['product_cat']))
				{
					$this->db->delete('pos_i1_products_7_category', array('product_id' => $data['product_id'], 'account_no' => $data['acc']));
				} else {
					$check = $this->check_eav_exist('pos_i1_products_7_category','product_id',$data['product_id'],$data['acc']);
					if($check)
					{
						$this->db->where('product_id', $data['product_id']);
						$this->db->where('account_no', $data['acc']);
						$this->db->update('pos_i1_products_7_category', array('category_id' => $data['product_cat']));						
					} else {						
						$insert['category'] = array(
												'prd_cat_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $data['product_id'],
												'category_id' => $data['product_cat'],
												'account_no' => $data['acc'],						
													);
						$this->db->insert('pos_i1_products_7_category', $insert['category']);
					}
				}

				if(empty($data['product_brand']))
				{
					$this->db->delete('pos_i1_products_6_brand', array('product_id' => $data['product_id'], 'account_no' => $data['acc']));
				} else {
					$check = $this->check_eav_exist('pos_i1_products_6_brand','product_id',$data['product_id'],$data['acc']);
					if($check)
					{
						$this->db->where('product_id', $data['product_id']);
						$this->db->where('account_no', $data['acc']);
						$this->db->update('pos_i1_products_6_brand', array('brand_id' => $data['product_brand']));						
					} else {						
						$insert['brand'] = array(
												'prd_brand_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $data['product_id'],
												'brand_id' => $data['product_brand'],
												'account_no' => $data['acc'],						
													);
						$this->db->insert('pos_i1_products_6_brand', $insert['brand']);
					}
				}

				foreach($data['def_location'] as $key => $outlet_id)
				{
					if(!is_null($outlet_id))
					{
						$assure = $this->check_inventory_stock_exist($data['cur_stk'][$key],$data['product_id'],$outlet_id,$data['acc']);
						if(!$assure)
						{
							$insert['log'] = array(
										'log_index' => $this->taxes_model->make_single_uuid(),
										'user_id' => $data['user_id'],	
										'master_product' => $data['product_id'],																																																			
										'log_code' => 2,	
										'feed' => $data['cur_stk'][$key],	
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
										'location' => $outlet_id,	
										'account_no' => $data['acc'],	
										);				
							$this->db->insert('pos_i1_products_log', $insert['log']);
						}						
						$check = $this->check_product_tax_exist($data['product_id'],$outlet_id,$data['acc']);
						if($check)
						{
							if($data['sale_tax'][$key] != '' && !empty($outlet_id))
							{
								$update['tax'] = array(
											'tax_id' => $data['sale_tax'][$key],
											); 
								$this->db->where('main_product', $data['product_id']);			
								$this->db->where('location', $outlet_id);			
								$this->db->where('account_no', $data['acc']);			
								$this->db->update('pos_i1_products_tax', $update['tax']);
							} else {
								$this->db->delete('pos_i1_products_tax', array('main_product' => $data['product_id'],'account_no' => $data['acc'],'location' => $outlet_id)); 
							}
						} else {
							if($data['sale_tax'][$key] != '' && !empty($outlet_id))
							{
								$insert['tax'] = array(
									'tax_index' => $this->taxes_model->make_single_uuid(),
									'tax_id' => $data['sale_tax'][$key],
									'main_product' => $data['product_id'],
									'location' => $outlet_id,
									'account_no' => $data['acc']
								); 
								$this->db->insert('pos_i1_products_tax', $insert['tax']); 
							}
						}
					}
				}
				if($data['trace_inv'] == 30)
				{
					if(array_key_exists($key,$data['cur_stk']))
					{					
						foreach($data['inv_outlet'] as $key => $outlet_id)
						{
							$check = $this->check_inventory_exist($data['product_id'],$outlet_id,$data['acc']);
							if($check)
							{
								$update['inv'] = array(
											'current_stock' => $data['cur_stk'][$key],
											'reorder_stock' => $data['reorder_stk'][$key],
											'reorder_qty' => $data['reorder_qty'][$key],
											'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
											);
								$this->db->where('product_id', $data['product_id']);			
								$this->db->where('location', $outlet_id);			
								$this->db->where('account_no', $data['acc']);			
								$this->db->update('pos_i2_a_inventory', $update['inv']);
							} else {
								$insert['inv'] = array(
											'inv_indx' => $this->taxes_model->make_single_uuid(),
											'product_id' => $data['product_id'],
											'current_stock' => $data['cur_stk'][$key],
											'reorder_stock' => $data['reorder_stk'][$key],
											'reorder_qty' => $data['reorder_qty'][$key],
											'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
											'location' => $outlet_id,
											'account_no' => $data['acc'],
											);
								$this->db->insert('pos_i2_a_inventory',$insert['inv']);
							}
						}
					}
				} else {
					$this->db->delete('pos_i2_a_inventory', array('product_id' => $data['product_id'],'account_no' => $data['acc'])); 
				}
				if(!empty($_FILES['userfile']['name'])) {
					$bool = $this->do_upload($data['product_id']);
				}
				$this->db->trans_complete();
				if($this->db->trans_status() === FALSE)
				{
					return 0;
				} else {
					return 1;	
				}
			} else {
				return 2;	
			}
		} else {
			return 3;
		}
	}
	public function update_variant($data)
	{
		if($this->check_sku_not_this_product($data['sku'],$data['product_id'],$data['acc']))
		{
			if($this->check_barcode_prefix($data['sku'],$data['acc']))
			{
				$this->db->trans_begin();
				$this->db->trans_start();
				$update['main_prd'] = array(
								'product_name' => $data['p_name'],
								'handle' => str_replace(' ','',$data['p_handle']),
								'description' => $data['new_desc'],									
								);		
				$this->db->where('product_id', $data['main_product_id']);
				$this->db->where('account_no', $data['acc']);
				$this->db->update('pos_i1_products', $update['main_prd']);
				$update['sub_prd'] = array(
								'price' => $data['price'],
								'margin' => $data['margin'],
								'retail_price' => $data['retail'],
								'sku' => $data['sku'],
								'product_weight' => $data['prd_weight'],
								'loyalty' => $data['loyalty_val'],
								'is_shopping_cart' => $data['show_cart'],
								'status' => $data['visib_stat'],
								'track_inventory' => $data['trace_inv'],
								'wearhouse_id' => $data['prd_wh_id'],
								'purchase_id' => $data['prd_pur_id'],
								'ship_stat' => $data['ship_stat'],
								'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
								); 
				$this->db->where('variant_index', $data['product_id']);
				$this->db->where('account_no', $data['acc']);
				$this->db->update('pos_i1_products_1_variants', $update['sub_prd']);
				$data['var_type_name'] = array_slice($data['var_type_name'],0,$data['max_variants'],true);				
				foreach($data['var_type_name'] as $v_key => $v_value)
				{
					$update['var_attr'] = array(
											'attribute_val' => str_replace(',','',$data['new_var_method'][$v_key])
											);
					$this->db->where('attribute_id', $v_value);
					$this->db->where('variant_id', $data['product_id']);
					$this->db->where('account_no', $data['acc']);
					$this->db->update('pos_i1_products_1_variants_attributes', $update['var_attr']); 
				}
				if(empty($data['new_supplier']))
				{
					$this->db->delete('pos_i1_products_8_supplier', array('product_id' => $data['main_product_id'], 'account_no' => $data['acc']));
				} else {
					$check = $this->check_eav_exist('pos_i1_products_8_supplier','product_id',$data['main_product_id'],$data['acc']);
					if($check)
					{
						$this->db->where('product_id', $data['main_product_id']);
						$this->db->where('account_no', $data['acc']);
						$this->db->update('pos_i1_products_8_supplier', array('supplier_id' => $data['new_supplier']));						
					} else {						
						$insert['supplier'] = array(
												'prd_supplier_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $data['main_product_id'],
												'supplier_id' => $data['new_supplier'],
												'account_no' => $data['acc'],						
													);
						$this->db->insert('pos_i1_products_8_supplier', $insert['supplier']);
					}
				}
				if(empty($data['product_cat']))
				{
					$this->db->delete('pos_i1_products_7_category', array('product_id' => $data['main_product_id'], 'account_no' => $data['acc']));
				} else {
					$check = $this->check_eav_exist('pos_i1_products_7_category','product_id',$data['main_product_id'],$data['acc']);
					if($check)
					{
						$this->db->where('product_id', $data['main_product_id']);
						$this->db->where('account_no', $data['acc']);
						$this->db->update('pos_i1_products_7_category', array('category_id' => $data['product_cat']));						
					} else {						
						$insert['category'] = array(
												'prd_cat_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $data['main_product_id'],
												'category_id' => $data['product_cat'],
												'account_no' => $data['acc'],						
													);
						$this->db->insert('pos_i1_products_7_category', $insert['category']);
					}
				}

				if(empty($data['product_brand']))
				{
					$this->db->delete('pos_i1_products_6_brand', array('product_id' => $data['main_product_id'], 'account_no' => $data['acc']));
				} else {
					$check = $this->check_eav_exist('pos_i1_products_6_brand','product_id',$data['main_product_id'],$data['acc']);
					if($check)
					{
						$this->db->where('product_id', $data['main_product_id']);
						$this->db->where('account_no', $data['acc']);
						$this->db->update('pos_i1_products_6_brand', array('brand_id' => $data['product_brand']));						
					} else {						
						$insert['brand'] = array(
												'prd_brand_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $data['main_product_id'],
												'brand_id' => $data['product_brand'],
												'account_no' => $data['acc'],						
													);
						$this->db->insert('pos_i1_products_6_brand', $insert['brand']);
					}
				}
				
				foreach($data['def_location'] as $key => $outlet_id)
				{
					$check = $this->check_variant_tax_exist($data['product_id'],$outlet_id,$data['acc']);
					if($check)
					{
						if($data['sale_tax'][$key] != '' && !empty($outlet_id))
						{
							$update['tax'] = array(
										'tax_id' => $data['sale_tax'][$key],
										); 
							$this->db->where('variant_id', $data['product_id']);			
							$this->db->where('location', $outlet_id);			
							$this->db->where('account_no', $data['acc']);			
							$this->db->update('pos_i1_products_tax_variant', $update['tax']);
						} else {
							$this->db->delete('pos_i1_products_tax_variant', array('variant_id' => $data['product_id'],'account_no' => $data['acc'],'location' => $outlet_id)); 
						}
					} else {
						if($data['sale_tax'][$key] != '' && !empty($outlet_id))
						{
							$insert['tax'] = array(
								'tax_var_index' => $this->taxes_model->make_single_uuid(),
								'tax_id' => $data['sale_tax'][$key],
								'product_id' => $data['main_product_id'],
								'variant_id' => $data['product_id'],
								'location' => $outlet_id,
								'account_no' => $data['acc']
							); 
							$this->db->insert('pos_i1_products_tax_variant', $insert['tax']); 
						}
					}
				}
				if($data['trace_inv'] == 30)
				{
					if(array_key_exists($key,$data['cur_stk']))
					{					
						foreach($data['inv_outlet'] as $key => $outlet_id)
						{
							if(!is_null($outlet_id))
							{
								$assure = $this->check_inventory_variant_stock_exist($data['cur_stk'][$key],$data['product_id'],$outlet_id,$data['acc']);
								if(!$assure)
								{
									$insert['log'] = array(
												'log_var_index' => $this->taxes_model->make_single_uuid(),
												'user_id' => $data['user_id'],	
												'master_product' => $data['main_product_id'],																						
												'variant_id' => $data['product_id'],
												'log_code' => 2,	
												'feed' => $data['cur_stk'][$key],	
												'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
												'location' => $outlet_id,	
												'account_no' => $data['acc'],	
												);				
									$this->db->insert('pos_i1_products_log_variants', $insert['log']);
								}						
								$check = $this->check_inventory_variant_exist($data['product_id'],$outlet_id,$data['acc']);
								if($check)
								{
									$update['inv'] = array(
												'current_stock' => $data['cur_stk'][$key],
												'reorder_stock' => $data['reorder_stk'][$key],
												'reorder_qty' => $data['reorder_qty'][$key],
												'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
												);
									$this->db->where('parent_product', $data['main_product_id']);			
									$this->db->where('variant_id', $data['product_id']);			
									$this->db->where('location', $outlet_id);			
									$this->db->where('account_no', $data['acc']);			
									$this->db->update('pos_i2_a_inventory_variant', $update['inv']);
								} else {
									$insert['inv'] = array(
												'inv_var_indx' => $this->taxes_model->make_single_uuid(),
												'parent_product' => $data['main_product_id'],
												'variant_id' => $data['product_id'],
												'current_stock' => $data['cur_stk'][$key],
												'reorder_stock' => $data['reorder_stk'][$key],
												'reorder_qty' => $data['reorder_qty'][$key],
												'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
												'location' => $outlet_id,
												'account_no' => $data['acc'],
												);
									$this->db->insert('pos_i2_a_inventory_variant',$insert['inv']);
								}
							}
						}
					}
				} else {
					$this->db->delete('pos_i2_a_inventory_variant', array('parent_product' => $data['main_product_id'],'variant_id' => $data['product_id'],'account_no' => $data['acc'])); 
				}
				if(!empty($_FILES['userfile']['name'])) {
					$bool = $this->do_upload($data['product_id']);
				}
				$this->db->trans_complete();
				if($this->db->trans_status() === FALSE)
				{
					return 0;
				} else {
					return 1;	
				}
			} else {
				return 2;	
			}
		} else {
			return 3;
		}
	}
	public function update_blend($data)
	{
		if($this->check_sku_not_this_product($data['sku'],$data['product_id'],$data['acc']))
		{
			if($this->check_barcode_prefix($data['sku'],$data['acc']))
			{
				$this->db->trans_begin();
				$this->db->trans_start();
				$update['master_prd'] = array(
										'product_name' => $data['p_name'],
										'handle' => str_replace(' ','',$data['p_handle']),
										'description' => $data['new_desc'],									
										);
				$this->db->where('product_id', $data['product_id']);
				$this->db->where('account_no', $data['acc']);
				$this->db->update('pos_i1_products', $update['master_prd']);
				// if inventory is altered do this
				$exist_product = $this->product_model->get_blend_sub_products($data['product_id'],$data['acc']);
				if($data['blend_product_id'] != $exist_product['product_id'] || $data['blend_prd_qty'] != $exist_product['parent_qty'])
				{
					foreach($data['def_location'] as $key => $outlet_id) //update log before updating blend products
					{
						if(!is_null($outlet_id))
						{
							$blend_stock = $this->check_blend_inv_stock($data['blend_product_id'],$data['blend_prd_qty'],$data['acc'],$outlet_id);
							$insert['log'] = array(
										'log_index' => $this->taxes_model->make_single_uuid(),
										'user_id' => $data['user_id'],	
										'master_product' => $data['product_id'],
										'log_code' => 2,	
										'feed' => $blend_stock,	
										'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),	
										'location' => $outlet_id,	
										'account_no' => $data['acc'],	
										);				
							$this->db->insert('pos_i1_products_log', $insert['log']);
						}
					}
				}
				$update['main_prd'] = array(
								'price' => $data['price'],
								'margin' => $data['margin'],
								'retail_price' => $data['retail'],
								'sku' => $data['sku'],
								'product_weight' => $data['prd_weight'],
								'loyalty' => $data['loyalty_val'],
								'is_shopping_cart' => $data['show_cart'],
								'wearhouse_id' => $data['prd_wh_id'],
								'purchase_id' => $data['prd_pur_id'],
								'ship_stat' => $data['ship_stat'],
								'status' => $data['visib_stat'],
								'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
								); 
				$this->db->where('blend_product_id', $data['product_id']);
				$this->db->where('account_no', $data['acc']);
				$this->db->update('pos_i1_products_0_blend', $update['main_prd']);
				
				foreach($data['blend_product_id'] as $key => $blend_product_id)
				{
					$scale = $this->check_scale($blend_product_id,$data['acc']);
					if($scale == 3)
					{
						$check = $this->check_blend_exist_table('pos_i1_products_4_blend_variant','variant_id',$blend_product_id,'blend_product',$data['product_id'],$data['acc']);
						if($check)
						{
							$update['blend_var'] = array(
														'variant_qty' => $data['blend_prd_qty'][$key],
														  );
							$this->db->where(array('blend_product' => $data['product_id'],'variant_id' => $blend_product_id));
							$this->db->update('pos_i1_products_4_blend_variant', $update['blend_var']);
						} else {	
							$main_product_id = $this->get_main_id_wrt_variant_id($blend_product_id,$data['acc']);						
							$insert['blend_var'] = array(
										'blend_var_index' => $this->taxes_model->make_single_uuid(),
										'blend_product' => $data['product_id'],
										'product_id' => $main_product_id,
										'variant_id' => $blend_product_id,
										'variant_qty' => $data['blend_prd_qty'][$key],
										'account_no' => $data['acc'],
										);	
							$this->db->insert('pos_i1_products_4_blend_variant', $insert['blend_var']);					
						}
					} else {
						$check = $this->check_blend_exist_table('pos_i1_products_4_blend','parent_product',$blend_product_id,'blend_product',$data['product_id'],$data['acc']);
						if($check)
						{
							$update['blend_main'] = array(
														'parent_qty' => $data['blend_prd_qty'][$key],
														  );
							$this->db->where('blend_product' , $data['product_id']);							  
							$this->db->where('parent_product' , $blend_product_id);							  
							$this->db->update('pos_i1_products_4_blend', $update['blend_main']);
														  
						} else {
							$insert['blend_main'] = array(
										'blend_index' => $this->taxes_model->make_single_uuid(),
										'blend_product' => $data['product_id'],
										'parent_product' => $blend_product_id,
										'parent_qty' => $data['blend_prd_qty'][$key],
										'account_no' => $data['acc'],
										);
							$this->db->insert('pos_i1_products_4_blend', $insert['blend_main']);
						}
					}
				}
				$delete_array = array_diff($data['ahead_blend'],$data['blend_product_id']); //delete unwanted products
				if(count($delete_array) > 0)
				{
					$this->db->where_in('parent_product', $delete_array);
					$this->db->where('blend_product', $data['product_id']);			
					$this->db->delete('pos_i1_products_4_blend',array('account_no' => $data['acc']));

					$this->db->where_in('variant_id', $delete_array);
					$this->db->where('blend_product', $data['product_id']);			
					$this->db->delete('pos_i1_products_4_blend_variant',array('account_no' => $data['acc']));
				}
				if(empty($data['new_supplier']))
				{
					$this->db->delete('pos_i1_products_8_supplier', array('product_id' => $data['product_id'], 'account_no' => $data['acc']));
				} else {
					$check = $this->check_eav_exist('pos_i1_products_8_supplier','product_id',$data['product_id'],$data['acc']);
					if($check)
					{
						$this->db->where('product_id', $data['product_id']);
						$this->db->where('account_no', $data['acc']);
						$this->db->update('pos_i1_products_8_supplier', array('supplier_id' => $data['new_supplier']));						
					} else {						
						$insert['supplier'] = array(
												'prd_supplier_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $data['product_id'],
												'supplier_id' => $data['new_supplier'],
												'account_no' => $data['acc'],						
													);
						$this->db->insert('pos_i1_products_8_supplier', $insert['supplier']);
					}
				}
				if(empty($data['product_cat']))
				{
					$this->db->delete('pos_i1_products_7_category', array('product_id' => $data['product_id'], 'account_no' => $data['acc']));
				} else {
					$check = $this->check_eav_exist('pos_i1_products_7_category','product_id',$data['product_id'],$data['acc']);
					if($check)
					{
						$this->db->where('product_id', $data['product_id']);
						$this->db->where('account_no', $data['acc']);
						$this->db->update('pos_i1_products_7_category', array('category_id' => $data['product_cat']));						
					} else {						
						$insert['category'] = array(
												'prd_cat_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $data['product_id'],
												'category_id' => $data['product_cat'],
												'account_no' => $data['acc'],						
													);
						$this->db->insert('pos_i1_products_7_category', $insert['category']);
					}
				}

				if(empty($data['product_brand']))
				{
					$this->db->delete('pos_i1_products_6_brand', array('product_id' => $data['product_id'], 'account_no' => $data['acc']));
				} else {
					$check = $this->check_eav_exist('pos_i1_products_6_brand','product_id',$data['product_id'],$data['acc']);
					if($check)
					{
						$this->db->where('product_id', $data['product_id']);
						$this->db->where('account_no', $data['acc']);
						$this->db->update('pos_i1_products_6_brand', array('brand_id' => $data['product_brand']));						
					} else {						
						$insert['brand'] = array(
												'prd_brand_id' => $this->taxes_model->make_single_uuid(),
												'product_id' => $data['product_id'],
												'brand_id' => $data['product_brand'],
												'account_no' => $data['acc'],						
													);
						$this->db->insert('pos_i1_products_6_brand', $insert['brand']);
					}
				}
				foreach($data['def_location'] as $key => $outlet_id)
				{
					if(!is_null($outlet_id))
					{
						$check = $this->check_product_tax_exist($data['product_id'],$outlet_id,$data['acc']);
						if($check)
						{
							if($data['sale_tax'][$key] != '' && !empty($outlet_id))
							{
								$update['tax'] = array(
											'tax_id' => $data['sale_tax'][$key],
											); 
								$this->db->where('main_product', $data['product_id']);			
								$this->db->where('location', $outlet_id);			
								$this->db->where('account_no', $data['acc']);			
								$this->db->update('pos_i1_products_tax', $update['tax']);
							} else {
								$this->db->delete('pos_i1_products_tax', array('main_product' => $data['product_id'],'account_no' => $data['acc'],'location' => $outlet_id)); 
							}
						} else {
							if($data['sale_tax'][$key] != '' && !empty($outlet_id))
							{
								$insert['tax'] = array(
									'tax_index' => $this->taxes_model->make_single_uuid(),
									'tax_id' => $data['sale_tax'][$key],
									'main_product' => $data['product_id'],
									'location' => $outlet_id,
									'account_no' => $data['acc']
								); 
								$this->db->insert('pos_i1_products_tax', $insert['tax']); 
							}
						}
					}
				}
				if(!empty($_FILES['userfile']['name'])) {
					$bool = $this->do_upload($data['product_id']);
				}
				$this->db->trans_complete();
				if($this->db->trans_status() === FALSE)
				{
					return 0;
				} else {
					return 1;	
				}
			} else {
				return 2;	
			}
		} else {
			return 3;
		}
	}
}
/*Alert: Close php "?>" tag is creating problems. Barcode zend, download csv not working on closing this. Do not close.. its hard to find errors*/