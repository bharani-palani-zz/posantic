<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Download_controller extends CI_Controller
{
	public $acc;
	public $privelage;
	public $pos_user;
	public $user_id;
	public $up_limit = 1000;
	public $max_variants = 3;
    public function __construct() 
    {
        parent::__construct();
		$this->load->library('csvreader');
		$this->load->dbutil();
		$this->acc = $this->session->userdata('acc_no');
		$this->privelage = $this->session->userdata('privelage');
		$this->pos_user = $this->session->userdata('pos_user');
		$this->user_id = $this->session->userdata('user_id');
		$this->load->model('products_model/product_form_model');
		$validity = $this->login_model->check_validity($this->acc);
		$subdomain = $this->session->userdata('subdomain');
		$this->is_valid_browser_domain = is_this_subdomain_browser($subdomain);
		if($validity == 0)
		{
			redirect(base_url().'account');
		}		
    }
	public function download_products()
	{
		$this->load->view('session/pos_session');
		$this->load->model('download/download_model');
		$where_array = array('search' => '','product_stat' => 1,'product_cat' => '','product_brand' => '','supplier_id' => '','tag_id' => '');
		$products = $this->download_model->download_products($where_array,$this->acc);
		$delimiter = ",";
        $newline = "\r\n";
        $data = $this->dbutil->csv_from_result($products, $delimiter, $newline);
		$this->load->helper('download');
		force_download($this->session->userdata('pos_hoster_cmp').'-products('.rand(10000,100000).').csv',$data);
	}
	public function csv_headers()
	{
		$this->load->view('session/pos_session');
		$locs = $this->user_model->get_locations($this->acc);
		$tax = '';
		foreach($locs as $key => $value)
		{
			$tax .= '"outlet_locale_tax_'.$value.'",';
		}
		$tax = rtrim($tax, ',');
		$report1 = '"id","handle","product_name","sku","product_scale",'.$tax.',"description","supplier_name","product_category","product_brand","product_tag","supplier_or_operated_price","retail_price","warehouse_id","purchase_id","variant_one_name","variant_one_value","variant_two_name","variant_two_value","variant_three_name","variant_three_value","associate_SKU","associate_quantity","loyalty_value","product_weight","trace_inventory","show_in_shopping_cart","shipment","visibility",';
		$report2 = '';
		foreach($locs as $key => $value)
		{
			$report2 .= '"inventory_'.$value.'","reorder_'.$value.'","restock_'.$value.'",';
		}
		$report2 = rtrim($report2, ',');
		$new_report = $report1.$report2;
		return $new_report;	
	}
	public function bulk_sample()
	{
		$this->load->view('session/pos_session');
		$this->load->helper('file');
		$this->load->dbutil();
		$this->load->helper('download');
		$csv_headers = $this->csv_headers();
		force_download('sample_import.csv('.rand(10000,100000).').csv',$csv_headers);

	}
	public function import()
	{
		if(!$this->pos_user || !$this->user_id || $this->is_valid_browser_domain == false) 
		{
			$this->load->library('../controllers/default/user');
			$this->user->login();
		} else {
			if($this->privelage == 1 or $this->privelage == 2)
			{
				//header
				$header['view']['title'] = 'Import Products';
				$role = $this->roles_model->get_roles($this->privelage);
				list($header['role_code'],$header['role_name']) = $role;
				$header['style'][0] = link_tag(POS_CSS_ROOT.'repository/custom_css/posantic_custom.css')."\n";
				$header['top_menu'] = $this->menu_model->get_menu($this->privelage);
				$this->load->view('top_page/top_page',$header);
				
				//body
				$data = array();
				if($this->session->flashdata('form_errors')) {
					$data['form_errors'] =  $this->session->flashdata('form_errors');
				}
				if($this->session->flashdata('form_success')) {
					$data['form_success'] = $this->session->flashdata('form_success');
				}
				$this->load->view('products/bulk_import',$data);
				
				//footer
				$this->load->view('bottom_page/bottom_page');			
			} else {
				$this->load->view('noaccess/noaccess');
			}
		}
	}
	public function confirm_string_occurs($search,$find_array)
	{
		$i=0;
		foreach($find_array as $values)
		{
			if(strpos($values, $search) !== false) 
			{
				$i++; 
			}
		}
		return $i > 0 ? true : false;
	}
	public function import_action()
	{
		$this->load->view('session/pos_session');
		$root = APPPATH;
		$dirame = '/user_images/'.md5($this->acc).'/csv_import';
		if(!is_dir($root.$dirame)){mkdir($root.$dirame,0777,true);}
		$path = $root.$dirame;
		$config['upload_path'] = './'.$path.'/';
		$config['allowed_types'] = 'csv|CSV';
		$config['max_size']	= '2048'; // 2mb
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if(!$this->upload->do_upload())
		{
			$error = array('errorcode' => $this->upload->display_errors());
			$str = str_replace(array('<p>','</p>'),'',$error['errorcode']);
			$this->session->set_flashdata('form_errors', 'Error: '.$str);
			redirect(base_url().'products/import');
		} else {
			$data = array('upload_data' => $this->upload->data());
			$file = $data['upload_data']['file_name'];
			$hash = md5($this->acc);			
			$old_name = './'.$path.'/'.$file;
			$new_name = './'.$path.'/'.$hash.'.csv';
			rename($old_name,$new_name);
			//upload done
			$csv_array = $this->csvreader->parse_file($new_name);
			$csv_fields = $this->csvreader->get_fields();
			$csv_fields_array = explode(",",$csv_fields[0]);
			$csv_headers = $this->csv_headers();
			$csv_headers = str_replace('"', "", $csv_headers);
			$csv_headers_array = explode(",",$csv_headers);
			$tot_rows = count($csv_array);
			$blend_data = array();
			if($csv_array)
			{
				if(count($csv_array) <= $this->up_limit)
				{
					$min_req_fields = array('id','handle','product_name','sku','product_scale',	'description',	'supplier_name', 'product_category', 'product_brand','product_tag','supplier_or_operated_price','retail_price',	'warehouse_id',	'purchase_id','variant_one_name','variant_one_value','variant_two_name','variant_two_value','variant_three_name','variant_three_value',	'associate_sku','associate_quantity','loyalty_value','product_weight','trace_inventory','show_in_shopping_cart','shipment',	'visibility');
					//csv field validation
					if(
						count(array_intersect($csv_fields_array,$min_req_fields)) == count($min_req_fields) && //check minimum required static fields
							$this->confirm_string_occurs('outlet_locale_tax_',$csv_fields_array) == true && // check tax filed like exists
								$this->confirm_string_occurs('inventory_',$csv_fields_array) == true && // check inventory fieled like exists
									$this->confirm_string_occurs('reorder_',$csv_fields_array) == true && // check reorder fieled like exists
										$this->confirm_string_occurs('restock_',$csv_fields_array) == true // check reorder fieled like exists
					)
					{
						if(count($csv_fields_array) <= 40)
						{
							$this->benchmark->mark('code_start');
							list($a,$b,$c,$d,$e,$f,$g,$h,$i) = array(0,0,0,0,0,0,0,0,0);
							foreach($csv_array as $sub_array)
							{
								$new_row = array();
								$outlet_tax = array();
								$keys = key($sub_array);
								$keys = explode(",",$keys);
								$values = str_getcsv($sub_array[key($sub_array)],",",'"');
								$row = array_combine($keys,$values);
								if($row['product_scale'] != 'BLEND')
								{
									$post_data['product_id'] = empty($row['id']) ? $this->taxes_model->make_single_uuid() : $row['id'];
									$post_data['p_name'] = $row['product_name'];
									$post_data['p_handle'] = str_replace(' ','',$row['handle']);
									$post_data['new_desc'] = $row['description'];
									$post_data['product_brand'] = $row['product_brand'] == "" ? NULL : $this->brand_and_tag_model->get_brand_id_wrt_name($row['product_brand'],$this->acc);
									$post_data['product_cat'] = $row['product_category'] == "" ? NULL : $this->brand_and_tag_model->get_cat_id_wrt_name($row['product_category'],$this->acc);
									$tag_name_array = array_unique(array_filter(explode(";",$row['product_tag'])));
									$post_data['tag_id'] = count($tag_name_array) > 0 ? $this->brand_and_tag_model->sanitise_bulk_tag($tag_name_array,$this->acc) : array();
									$post_data['price'] = $row['supplier_or_operated_price'];
									$post_data['margin'] = (is_numeric($row['retail_price']) && is_numeric($row['supplier_or_operated_price'])) ? ($row['retail_price']/$row['supplier_or_operated_price'] * 100) - 100 : 0;	
									$post_data['retail'] = $row['retail_price'];
									$post_data['sku'] = $row['sku'];
									$post_data['prd_weight'] = $row['product_weight'];
									$post_data['loyalty_val'] = $row['loyalty_value'];
									$post_data['show_cart'] = $row['show_in_shopping_cart'] == 1 ? 30 : 40;
									$post_data['visib_stat'] = $row['visibility'] == 1 ? 30 : 40;
									$post_data['trace_inv'] = $row['trace_inventory'] == 1 ? 30 : 40;
									$post_data['prd_wh_id'] = $row['warehouse_id'];
									$post_data['prd_pur_id'] = $row['purchase_id'];						
									$post_data['ship_stat'] = $row['visibility'] == 1 ? 30 : 40;
									$post_data['user_id'] = $this->user_id;
									$post_data['acc'] = $this->acc;							
									$post_data['sale_tax'] = array();
									foreach($keys as $head_values)
									{
										if(strpos($head_values, 'outlet_locale_tax_') !== false) {
											if(!empty($row[$head_values]) || $row[$head_values] != "")
											{
												$tax_id = $this->taxes_model->get_tax_id_if_like($row[$head_values],$this->acc);
											} else {
												$tax_id = '';	
											}
											$outlet_tax_name = str_replace(array('outlet_locale_tax_'), "", $head_values);
											$def_outlet_id =  $this->outlet_model->get_outlet_id_if_like($outlet_tax_name,$this->acc);
											$post_data['def_location'][$def_outlet_id] = $def_outlet_id;
											$post_data['inv_outlet'][$def_outlet_id] = $def_outlet_id;
											$post_data['sale_tax'][$def_outlet_id] = $tax_id;
										}	
										if(strpos($head_values, 'inventory_') !== false) {
											$outlet_name = str_replace(array('inventory_'), "", $head_values);
											$outlet_id =  $this->outlet_model->get_outlet_id_if_like($outlet_name,$this->acc);
											$post_data['cur_stk'][$outlet_id] = $row[$head_values];
										}	
										if(strpos($head_values, 'reorder_') !== false) {
											$outlet_name = str_replace(array('reorder_'), "", $head_values);
											$outlet_id =  $this->outlet_model->get_outlet_id_if_like($outlet_name,$this->acc);
											$post_data['reorder_stk'][$outlet_id] = $row[$head_values];
										}	
										if(strpos($head_values, 'restock_') !== false) {
											$outlet_name = str_replace(array('restock_'), "", $head_values);
											$outlet_id =  $this->outlet_model->get_outlet_id_if_like($outlet_name,$this->acc);
											$post_data['reorder_qty'][$outlet_id] = $row[$head_values];
										}	
																
									}
									$post_data['new_supplier'] =  $row['supplier_name'] != "" ? $this->supplier_model->get_supplier_id_if_like($row['supplier_name'],$this->acc) : NULL;
								}						
								switch($row['product_scale'])
								{
									case "NUM":	
										if(empty($row['id']))
										{
											$response = $this->product_form_model->insert_num_product($post_data);
										} else {
											$check = $this->product_model->check_product($row['id'],$this->acc);
											if(!is_null($check))
											{
												$response = $this->product_form_model->update_num($post_data);
											} else {
												$response = 0;	
											}
										}
										if($response == 1)
										{
											$a++;	
										} else if($response == 2) {
											$b++;	
										} else if($response == 3) {
											$c++;	
										} else if($response == 4) {
											$d++;	
										} else if($response == 0) {
											$e++;	
										}
									break;
										case "KILO":	
											if(empty($row['id']))
											{
												$response = $this->product_form_model->insert_kilo_product($post_data);
											} else {
												$check = $this->product_model->check_product($row['id'],$this->acc);
												if(!is_null($check))
												{
													$response = $this->product_form_model->update_kilo($post_data);
												} else {
													$response = 0;	
												}
											}
											if($response == 1)
											{
												$a++;	
											} else if($response == 2) {
												$b++;	
											} else if($response == 3) {
												$c++;	
											} else if($response == 4) {
												$d++;	
											} else if($response == 0) {
												$e++;	
											} else if($response == 0) {
												$h++;
											}
										break;
										case "VARIANTS":
											$post_data['var_type_name'][0] = strlen($row['variant_one_name']) > 0 ? $this->variant_model->get_variant_attr_id_wrt_name($row['variant_one_name'],$this->acc) : NULL;
											$post_data['var_type_name'][1] = strlen($row['variant_two_name']) > 0 ? $this->variant_model->get_variant_attr_id_wrt_name($row['variant_two_name'],$this->acc) : NULL;
											$post_data['var_type_name'][2] = strlen($row['variant_three_name']) > 0 ? $this->variant_model->get_variant_attr_id_wrt_name($row['variant_three_name'],$this->acc) : NULL;
											$post_data['var_type_name'] = array_filter($post_data['var_type_name']);
											$post_data['max_variants'] = $this->max_variants;
											$post_data['new_var_method'][0] = strlen($row['variant_one_value']) > 0 ? $row['variant_one_value'] : NULL;
											$post_data['new_var_method'][1] = strlen($row['variant_two_value']) > 0 ? $row['variant_two_value'] : NULL;
											$post_data['new_var_method'][2] = strlen($row['variant_three_value']) > 0 ? $row['variant_three_value'] : NULL;
											$post_data['new_var_method'] = array_filter($post_data['new_var_method']);
											if(count($post_data['var_type_name']) > 0)
											{
												if(empty($row['id']))
												{
													$post_data['variant_id'] = $this->taxes_model->make_single_uuid();
													$check_parent_variant_id = $this->product_model->get_variant_parent_id_wrt_handle($post_data['p_handle'],$this->acc);
													if(is_null($check_parent_variant_id))
													{
													  $post_data['parent_product_id'] = $this->taxes_model->make_single_uuid();
													  $response = $this->product_form_model->insert_variant_product($post_data);
													} else {
													  $post_data['product_id'] = $check_parent_variant_id;
													  $response = $this->product_form_model->insert_child_variant_product($post_data);
													}
												} else {
													$check_parent_variant_id = $this->product_model->get_variant_parent_id_wrt_handle($post_data['p_handle'],$this->acc);
													if(!is_null($check_parent_variant_id))
													{
														$post_data['product_id'] = $row['id'];
														$post_data['main_product_id'] = $check_parent_variant_id;
														$response = $this->product_form_model->update_variant($post_data);
													} else {
														$response = 0;	
													}
												}
											} else {
												$response = 0;	
											}
											if($response == 1)
											{
												$a++;	
											} else if($response == 2) {
												$b++;	
											} else if($response == 3) {
												$c++;	
											} else if($response == 4) {
												$d++;	
											} else if($response == 5) {
												$f++;	
											} else if($response == 0) {
												$e++;	
											} else if($response == 6) {
												$i++;	
											}
										break;
										case "BLEND":								
											$blend_data[$row['handle']]['product_id'][] = $row['id'];
											$blend_data[$row['handle']]['p_handle'] = $row['handle'];
											$blend_data[$row['handle']]['p_name'] = $row['product_name'];
											//description
											$desc[$row['handle']][] = $row['description'];
											$desc_str = current(array_filter($desc[$row['handle']]));
											$blend_data[$row['handle']]['new_desc'] = $desc_str;
											//price
											$price[$row['handle']][] = $row['supplier_or_operated_price'];
											$price_str = current(array_filter($price[$row['handle']]));
											$blend_data[$row['handle']]['price'] = is_numeric($price_str) ? $price_str : 1;
											//retail
											$retail[$row['handle']][] = $row['retail_price'];
											$retail_str = current(array_filter($retail[$row['handle']]));
											$blend_data[$row['handle']]['retail'] = is_numeric($retail_str) ? $retail_str : 1;
											//margin
											$blend_data[$row['handle']]['margin'] = ($blend_data[$row['handle']]['retail']/$blend_data[$row['handle']]['price'] * 100) - 100;	
											//sku
											$sku[$row['handle']][] = $row['sku'];
											$sku_str = current(array_filter($sku[$row['handle']]));
											$blend_data[$row['handle']]['sku'] = $sku_str;
											//weight
											$weight[$row['handle']][] = $row['product_weight'];
											$weight_str = current(array_filter($weight[$row['handle']]));
											$blend_data[$row['handle']]['prd_weight'] = $weight_str;
											//loyalty
											$loyalty[$row['handle']][] = $row['loyalty_value'];
											$loyalty_str = current(array_filter($loyalty[$row['handle']]));
											$blend_data[$row['handle']]['loyalty_val'] = $loyalty_str;
											//shopping cart
											$shopping[$row['handle']][] = $row['show_in_shopping_cart'];
											$shopping_str = current(array_filter($shopping[$row['handle']])) == 1 ? 30 : 40;
											$blend_data[$row['handle']]['show_cart'] = $shopping_str;
											//warehouse_id
											$wh[$row['handle']][] = $row['warehouse_id'];
											$wh_str = current(array_filter($wh[$row['handle']]));
											$blend_data[$row['handle']]['prd_wh_id'] = $wh_str;
											//purchase_id
											$pc[$row['handle']][] = $row['purchase_id'];
											$pc_str = current(array_filter($pc[$row['handle']]));
											$blend_data[$row['handle']]['prd_pur_id'] = $pc_str;
											//shipment
											$ship[$row['handle']][] = $row['shipment'];
											$ship_str = current(array_filter($ship[$row['handle']])) == 1 ? 30 : 40;
											$blend_data[$row['handle']]['ship_stat'] = $ship_str;
											//visiblity
											$visib[$row['handle']][] = $row['visibility'];
											$visib_str = current(array_filter($visib[$row['handle']])) == 1 ? 30 : 40;
											$blend_data[$row['handle']]['visib_stat'] = $visib_str;
											//brand
											$brand[$row['handle']][] = $row['product_brand'];
											$blend_data[$row['handle']]['product_brand'] = current(array_unique(array_filter($brand[$row['handle']])));		
											//category
											$category[$row['handle']][] = $row['product_category'];
											$blend_data[$row['handle']]['product_cat'] = current(array_unique(array_filter($category[$row['handle']])));		
											//supplier
											$supplier[$row['handle']][] = $row['supplier_name'];
											$blend_data[$row['handle']]['new_supplier'] = current(array_unique(array_filter($supplier[$row['handle']])));		
											//tag
											$tag_array[$row['handle']][] = array_unique(array_filter(explode(";",$row['product_tag'])));
											$blend_data[$row['handle']]['tags'] = current(array_unique(array_filter($tag_array[$row['handle']])));		
											//associate_product_id
											$assoc_prd_id[$row['handle']][] = $row['id'];
											$blend_data[$row['handle']]['blend_product_id'] = array_values($assoc_prd_id[$row['handle']]);	
											//associate_sku
											$assoc_sku[$row['handle']][] = $row['associate_sku'];
											$blend_data[$row['handle']]['assoc_sku'] = array_values($assoc_sku[$row['handle']]);	
											//associate_qty
											$assoc_qty[$row['handle']][] = $row['associate_quantity'];
											$blend_data[$row['handle']]['assoc_qty'] = array_values($assoc_qty[$row['handle']]);		
											//tax
											foreach($keys as $head_values)
											{
												if(strpos($head_values, 'outlet_locale_tax_') !== false) 
												{
													//outlet
													$outlet_tax_name = str_replace(array('outlet_locale_tax_'), "", $head_values);
													$outlet[$row['handle']][] = $outlet_tax_name;
													$blend_data[$row['handle']]['outlet'] = array_unique(array_filter($outlet[$row['handle']]));		
													//tax
													$tax[$row['handle']][] = $row[$head_values];
													$blend_data[$row['handle']]['tax'] = array_unique(array_filter($tax[$row['handle']]));		
		
												}	
											}
										break;
									default:
										$g++;
								}
							}
							if(count($blend_data) > 0)
							{						
								foreach($blend_data as $handle_array)
								{
									$blend_post_data['acc'] = $this->acc;
									$blend_post_data['user_id'] = $this->user_id;
									$blend_post_data['p_handle'] = $handle_array['p_handle'];
									$blend_post_data['p_name'] = $handle_array['p_name'];
									$blend_post_data['new_desc'] = ($handle_array['new_desc'] == false) ? "" : $handle_array['new_desc'];
									
									$blend_post_data['price'] = $handle_array['price'];
									$blend_post_data['margin'] = $handle_array['margin'];
									$blend_post_data['retail'] = $handle_array['retail'];
									$blend_post_data['sku'] = $handle_array['sku'];
									$blend_post_data['prd_weight'] = $handle_array['prd_weight'];
									$blend_post_data['loyalty_val'] = $handle_array['loyalty_val'];
		
									$blend_post_data['show_cart'] = $handle_array['show_cart'];
									$blend_post_data['prd_wh_id'] = $handle_array['prd_wh_id'];
									$blend_post_data['prd_pur_id'] = $handle_array['prd_pur_id'];
									$blend_post_data['ship_stat'] = $handle_array['ship_stat'];
									$blend_post_data['visib_stat'] = $handle_array['visib_stat'];
									
									$blend_post_data['product_brand'] = $handle_array['product_brand'] == "" ? NULL : $this->brand_and_tag_model->get_brand_id_wrt_name($handle_array['product_brand'],$this->acc);
									$blend_post_data['product_cat'] = $handle_array['product_cat'] == "" ? NULL : $this->brand_and_tag_model->get_cat_id_wrt_name($handle_array['product_cat'],$this->acc);
									$blend_post_data['new_supplier'] =  $handle_array['new_supplier'] != "" ? $this->supplier_model->get_supplier_id_if_like($handle_array['new_supplier'],$this->acc) : NULL;
									$blend_post_data['tag_id'] = count($handle_array['tags']) > 0 ? $this->brand_and_tag_model->sanitise_bulk_tag($handle_array['tags'],$this->acc) : array();
		
									foreach($handle_array['outlet'] as $o_key => $outlet_str)
									{
										$outlet_id = $this->outlet_model->get_outlet_id_if_like($outlet_str,$this->acc);
										$blend_post_data['def_location'][$outlet_id] = $outlet_id;
										$blend_post_data['inv_outlet'][$outlet_id] = $outlet_id;
										$blend_post_data['sale_tax'][$outlet_id] = array_key_exists($o_key,$handle_array['tax']) ? $this->taxes_model->get_tax_id_if_like($handle_array['tax'][$o_key],$this->acc) : '';
										
									}
								
									$assoc_id = array();
									$assoc_qty = array();
									$fltered_sku = array_values(array_filter($handle_array['assoc_sku']));
									$fltered_qty = array_values(array_filter($handle_array['assoc_qty']));
									foreach($fltered_sku as $assoc_key => $assoc_sku)
									{
										$assoc_id[] = $this->product_model->get_id_wrt_sku_if_blend($assoc_sku,$this->acc);
										$assoc_qty[] = $fltered_qty[$assoc_key];
									}
									$blend_post_data['blend_product_id'] = array_filter($assoc_id);
									$blend_post_data['blend_prd_qty'] = array_filter($assoc_qty);
									
									//insert or update
									if(count($blend_post_data['blend_product_id']) > 0)
									{
										if(empty($handle_array['product_id'][0]))
										{
												$blend_post_data['product_id'] = $this->taxes_model->make_single_uuid();
												$response = $this->product_form_model->insert_blend_product($blend_post_data);
										} else {
											$blend_post_data['product_id'] = current($handle_array['product_id']);
											$check = $this->product_model->check_product($blend_post_data['product_id'],$this->acc);
											if(!is_null($check))
											{
												$ahead = $this->product_model->get_blend_sub_products($blend_post_data['product_id'],$this->acc);
												$blend_post_data['ahead_blend'] = $ahead['product_id'];
												$response = $this->product_form_model->update_blend($blend_post_data);
											} else {
												$response = 0;	
											}
										}
									} else {
										$response = 0;	
									}
									if($response == 1)
									{
										$a++;	
									} else if($response == 2) {
										$b++;	
									} else if($response == 3) {
										$c++;	
									} else if($response == 4) {
										$d++;	
									} else if($response == 0) {
										$e++;	
									}
									
								}
							}
							$this->benchmark->mark('code_end');
							$phrase = '<ul class="list-group"><li class="list-group-item">';
							$phrase .= $tot_rows.' CSV row(s) progressed..</li>';	
							if($a > 0)
							{
								$phrase .= '<li class="list-group-item">'.$a.' Product(s) successfully imported</li>';	
							} 
							if($b > 0) {
								$phrase .= '<li class="list-group-item text-danger"><b>'.$b.' Product(s) upload failed - SKU can`t start with "KILO" scale barcode prefix.</b></li>';	
							} 
							if($c > 0) {
								$phrase .= '<li class="list-group-item text-danger"><b>'.$c.' Product(s) upload failed: SKU has already been used for some other product.</b></li>';	
							} 
							if($d > 0) {
								$phrase .= '<li class="list-group-item text-danger"><b>'.$d.' Product(s) upload failed: You have exceeded maximum product limit, please '.anchor('account','upgrade').' your account to add more products.</b></li>';	
							} 
							if($e > 0) {
								$phrase .= '<li class="list-group-item text-danger"><b>'.$e.' Product(s) upload dropped, since an obsolete product found</b></li>';	
							}					
							if($f > 0) {
								$phrase .= '<li class="list-group-item text-danger"><b>'.$f.' Product(s) upload dropped, since some variant for the product already exist</b></li>';	
							}					
							if($g > 0) {
								$phrase .= '<li class="list-group-item text-danger"><b>'.$g.' row(s) upload dropped, since an unknown "product_scale" Column found</b></li>';	
							}	
							if($h > 0) {
								$phrase .= '<li class="list-group-item text-danger"><b>'.$h.' row(s) upload dropped, since "KILO" scale products are restricted to a maximum of 99,999 count only.</b></li>';	
							}	
							if($i > 0) {
								$phrase .= '<li class="list-group-item text-danger"><b>'.$i.' row(s) upload dropped, since variant attributes are found to be invalid.</b></li>';	
							}	
							$precent = !is_float($a / $tot_rows) ? ($a / $tot_rows) * 100 : number_format(($a / $tot_rows) * 100,2);
							$phrase .= '<li class="list-group-item"><i class="fa fa-upload fa-fw"></i> Bulk Import (csv rows to product) '.$precent.'% done.</li>';	
							$phrase .= '<li class="list-group-item"><i class="fa fa-clock-o fa-fw"></i> Latency '.$this->benchmark->elapsed_time('code_start', 'code_end').' seconds</li>';	
							$phrase .= '</ul>';
							$this->session->set_flashdata('form_success', $phrase);
							redirect(base_url().'products/import');						
						} else {
							$this->session->set_flashdata('form_errors', 'Oops... Try to import data for maximum 3 outlets at a time. Please rearrange CSV and try again.');
							redirect(base_url().'products/import');						
						}
					} else {
						$this->session->set_flashdata('form_errors', 'Error: CSV fields are obsolete. Download the sample to allocate correct CSV fields.');
						redirect(base_url().'products/import');						
					}
				} else {
					$this->session->set_flashdata('form_errors', 'Error: CSV Data upload limit is '.$this->up_limit.', but "'.count($csv_array).'" rows found. Try a bit littler!');
					redirect(base_url().'products/import');						
				}
			} else {
				$this->session->set_flashdata('form_errors', 'Error: CSV Data not found!');
				redirect(base_url().'products/import');						
			}
		}
	}
}
?>