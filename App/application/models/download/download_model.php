<?php
class Download_model extends CI_Model
{
	public function download_products($where_array,$acc)
	{
		$this->db->select('count(*) as counted'); 
		$query = $this->db->get_where('pos_b_locations',array('outlet_stat' => 30,'account_no' => $acc));
		$row = $query->row_array();
		$outlet_count = $row['counted'];

		$this->db->query('SET SESSION group_concat_max_len = 1000000;');
		$sql = "SELECT GROUP_CONCAT( 
							CONCAT( 
								'ifnull(MAX(IF(a.product_scale = 3,IF(mm.location = ''',z.loc_id, ''', nn.tax_name, NULL),
									IF(m.location = ''', z.loc_id, ''', n.tax_name, NULL))),null) AS ',concat('`','outlet_locale_tax_',z.location,'`' ) 
								) 
							) as tax_sql from pos_b_locations as z where z.outlet_stat = 30 and z.account_no = '".$acc."';";
		$query = $this->db->query($sql);
		$rows = $query->row_array();
		$dyn_fields['tax_sql'] = $rows['tax_sql'];

		$sql = "SELECT GROUP_CONCAT( 
								CONCAT( 
									'MAX(IF(a.product_scale = 3, IF(oo.location = ''', z.loc_id, ''', oo.current_stock, null), IF(o.location = ''',z.loc_id,''', o.current_stock,null))) AS ', concat('`','inventory_',z.location,'`'), 
									',MAX(IF(a.product_scale = 3, IF(oo.location = ''', z.loc_id, ''', oo.reorder_stock, null), IF(o.location = ''',z.loc_id,''', o.reorder_stock,null))) AS ', concat('`','reorder_',z.location,'`'), 
									',MAX(IF(a.product_scale = 3, IF(oo.location = ''', z.loc_id, ''', oo.reorder_qty, null), IF(o.location = ''',z.loc_id,''', o.reorder_qty,null))) AS ', concat('`','restock_',z.location,'`') 
								) 
							) as inv_sql from pos_b_locations as z where z.outlet_stat = 30 and z.account_no = '".$acc."';";
		$query = $this->db->query($sql);
		$rows = $query->row_array();
		$dyn_fields['inv_sql'] = $rows['inv_sql'];

		$this->db->select('CASE a.product_scale  
								when 4 then e.blend_product_id
								when 3 then d.variant_index 
								when 2 then a.product_id 
								when 1 then a.product_id 
							END as id',false);
		$this->db->select('a.handle as handle',false);
		$this->db->select('a.product_name as product_name',false);
		$this->db->select('CASE a.product_scale  
								when 4 then e.sku 
								when 3 then d.sku 
								when 2 then c.sku 
								when 1 then b.sku     
							end as sku',false);
		$this->db->select('ddd.scale_code as product_scale');
		$this->db->select($dyn_fields['tax_sql'],false);
		$this->db->select('a.description as description',false);
		$this->db->select('h.cmp_name as supplier_name',false);
		$this->db->select('i.cat_name as product_category',false);
		$this->db->select('j.brand_name as product_brand',false);
		$this->db->select('group_concat(distinct l.tag_name SEPARATOR ";" ) as product_tag',false);
		$this->db->select('CASE a.product_scale  
								when 4 then e.price 
								when 3 then d.price 
								when 2 then c.price 
								when 1 then b.price 
							END as supplier_or_operated_price',false);
		$this->db->select('CASE a.product_scale  
								when 4 then e.retail_price 
								when 3 then d.retail_price 
								when 2 then c.retail_price 
								when 1 then b.retail_price 
							END as retail_price',false);
		$this->db->select('CASE a.product_scale  
								when 4 then e.wearhouse_id 
								when 3 then d.wearhouse_id 
								when 2 then c.wearhouse_id 
								when 1 then b.wearhouse_id 
							END as warehouse_id',false);
		$this->db->select('CASE a.product_scale  
								when 4 then e.purchase_id 
								when 3 then d.purchase_id 
								when 2 then c.purchase_id 
								when 1 then b.purchase_id 
							END as purchase_id',false);
		$this->db->select('SUBSTRING_INDEX(SUBSTRING_INDEX(group_concat(distinct dd2.cust_var_value order by dd2.cust_var_id separator ","), ",", 1), ",", -1) as variant_one_name',false);
		$this->db->select('SUBSTRING_INDEX(SUBSTRING_INDEX(group_concat(distinct dd.attribute_val order by dd.attribute_id separator ","), ",", 1), ",", -1) as variant_one_value', false);
		$this->db->select("If(length(group_concat(distinct dd2.cust_var_value order by dd2.cust_var_id separator ',')) - length(replace(group_concat(distinct dd2.cust_var_value order by dd2.cust_var_id separator ','), ',', '')) = 1 or length(group_concat(distinct dd2.cust_var_value order by dd2.cust_var_id separator ',')) - length(replace(group_concat(distinct dd2.cust_var_value order by dd2.cust_var_id separator ','), ',', '')) = 2, SUBSTRING_INDEX(SUBSTRING_INDEX(group_concat(distinct dd2.cust_var_value order by dd2.cust_var_id separator ','), ',', 2), ',', -1) ,NULL) as variant_two_name",false);
		$this->db->select("If(length(group_concat(distinct dd.attribute_val order by dd.attribute_id separator ',')) - length(replace(group_concat(distinct dd.attribute_val order by dd.attribute_id separator ','), ',', '')) = 1 or length(group_concat(distinct dd.attribute_val order by dd.attribute_id separator ',')) - length(replace(group_concat(distinct dd.attribute_val order by dd.attribute_id separator ','), ',', '')) = 2, SUBSTRING_INDEX(SUBSTRING_INDEX(group_concat(distinct dd.attribute_val order by dd.attribute_id separator ','), ',', 2), ',', -1) ,NULL) as variant_two_value",false);
		$this->db->select("If(length(group_concat(distinct dd2.cust_var_value order by dd2.cust_var_id separator ',')) - length(replace(group_concat(distinct dd2.cust_var_value order by dd2.cust_var_id separator ','), ',', '')) > 1, SUBSTRING_INDEX(SUBSTRING_INDEX(group_concat(distinct dd2.cust_var_value order by dd2.cust_var_id separator ','), ',', 3), ',', -1) ,NULL) as variant_three_name",false);
		$this->db->select("If(length(group_concat(distinct dd.attribute_val order by dd.attribute_id separator ',')) - length(replace(group_concat(distinct dd.attribute_val order by dd.attribute_id separator ','), ',', '')) > 1, SUBSTRING_INDEX(SUBSTRING_INDEX(group_concat(distinct dd.attribute_val order by dd.attribute_id separator ','), ',', 3), ',', -1) ,NULL) as variant_three_value",false);
		$this->db->select('"" as associate_sku',false);
		$this->db->select('"" as associate_quantity',false);
		$this->db->select('CASE a.product_scale  
								when 4 then e.loyalty 
								when 3 then d.loyalty 
								when 2 then c.loyalty 
								when 1 then b.loyalty 
							END as loyalty_value',false);
		$this->db->select('CASE a.product_scale  
								when 4 then e.product_weight 
								when 3 then d.product_weight 
								when 2 then c.product_weight 
								when 1 then b.product_weight 
							END as product_weight',false);
		$this->db->select('CASE a.product_scale  
								when 4 then 1 
								when 3 then if(d.track_inventory = 30, "1", "0") 
								when 2 then if(c.track_inventory = 30, "1", "0") 
								when 1 then if(b.track_inventory = 30, "1", "0") 
							END as trace_inventory',false);
		$this->db->select('CASE a.product_scale  
								when 4 then if(e.is_shopping_cart = 30, "1", "0") 
								when 3 then if(d.is_shopping_cart = 30, "1", "0") 
								when 2 then if(c.is_shopping_cart = 30, "1", "0") 
								when 1 then if(b.is_shopping_cart = 30, "1", "0") 
							END as show_in_shopping_cart',false);
		$this->db->select('CASE a.product_scale  
								when 4 then if(e.ship_stat = 30, "1", "0") 
								when 3 then if(d.ship_stat = 30, "1", "0") 
								when 2 then if(c.ship_stat = 30, "1", "0") 
								when 1 then if(b.ship_stat = 30, "1", "0") 
							END as shipment',false);
		$this->db->select('CASE a.product_scale  
								when 4 then if(e.status = 30, "1", "0") 
								when 3 then if(d.status = 30, "1", "0") 
								when 2 then if(c.status = 30, "1", "0") 
								when 1 then if(b.status = 30, "1", "0") 
							END as visibility',false);
		$this->db->select($dyn_fields['inv_sql'],false);
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_2_num as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_1_variants as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_1_variants_attributes as dd','a.product_id = dd.product_id and dd.variant_id = d.variant_index and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_0_cust_variant_types as dd2','dd2.cust_var_id = dd.attribute_id','left');
		$this->db->join('pos_1d_product_scale as ddd','ddd.scale_id = a.product_scale','left');
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		#slaves
		$this->db->join('pos_i1_products_8_supplier as hh','hh.product_id = a.product_id','left');
		$this->db->join('pos_e_suppliers as h','h.supp_id = hh.supplier_id','left');
		$this->db->join('pos_i1_products_7_category as ii','ii.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_category as i','i.cat_id = ii.category_id','left');
		$this->db->join('pos_i1_products_6_brand as jj','jj.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_brand as j','j.brand_index = jj.brand_id','left');
		$this->db->join('pos_i1_products_5_tags as k','k.product_id = a.product_id','left');
		$this->db->join('pos_i1_product_tag as l','l.tag_id = k.tagged_id','left');
		$this->db->join('pos_i1_products_tax as m','m.main_product = a.product_id and m.account_no = a.account_no','left');
		$this->db->join('pos_i1_products_tax_variant as mm','mm.product_id = a.product_id and mm.variant_id = d.variant_index and mm.account_no = a.account_no','left');
		$this->db->join('pos_a_taxes as n','n.tax_id = m.tax_id and n.account_no = a.account_no','left');
		$this->db->join('pos_a_taxes as nn','nn.tax_id = mm.tax_id and nn.account_no = a.account_no','left');
		$this->db->join('pos_b_locations as z','z.loc_id = m.location and z.account_no = a.account_no','left');
		$this->db->join('pos_b_locations as zz','zz.loc_id = mm.location and zz.account_no = a.account_no','left');		
		$this->db->join('pos_i2_a_inventory as o','o.product_id = a.product_id and a.account_no = o.account_no','left');
		$this->db->join('pos_i2_a_inventory_variant as oo','oo.parent_product = a.product_id and oo.variant_id = d.variant_index and a.account_no = oo.account_no','left');
		if($where_array['product_stat'] == 1)
		{
			$this->db->where("(b.status not in (25,120) or c.status != 120 or d.status != 120 or e.status != 120)"); 
		} else {
			$this->db->where("(b.status = ".$where_array['product_stat']." or c.status = ".$where_array['product_stat']." or d.status = ".$where_array['product_stat']." or e.status = ".$where_array['product_stat'].")");
		}
		if(!empty($where_array['search']))
		{			
			$this->db->where("
							(a.product_name  LIKE '%".$where_array['search']."%'
							OR  a.handle  LIKE '%".$where_array['search']."%'
							OR  b.sku  LIKE '%".$where_array['search']."%'
							OR  c.sku  LIKE '%".$where_array['search']."%'
							OR  d.sku  LIKE '%".$where_array['search']."%'
							OR  e.sku  LIKE '%".$where_array['search']."%')",
							NULL, false);
		}
		if(strlen($where_array['product_cat']) > 0)
		{
			$this->db->where('i.cat_id', $where_array['product_cat']); 
		}
		if(strlen($where_array['product_brand']) > 0)
		{
			$this->db->where('j.brand_index', $where_array['product_brand']); 
		}
		if(strlen($where_array['supplier_id']) > 0)
		{
			$this->db->where('h.supp_id', $where_array['supplier_id']); 
		}
		if(is_array($where_array['tag_id']))
		{				
			$this->db->where_in('k.tagged_id', $where_array['tag_id']); 
		}
		$this->db->where('a.account_no', $acc); 
		$this->db->group_by('id');		
		$this->db->limit(1000);
		$this->db->get();
		$query1 = $this->db->last_query();
		
		$sql = 'SELECT 
				f.parent_product as id,
				a.handle,
				a.product_name,
				e.sku as sku,
				"BLEND" as product_scale,';
		$sql .= str_repeat('"",', $outlet_count);
		$sql .= '"" as description,
				"" as supplier_name,
				"" as product_category,
				"" as product_brand,
				"" as product_tag,
				"" as supplier_or_operated_price,
				"" as retail_price,
				"" as warehouse_id,
				"" as purchase_id,
				"","","","","","",
				case f.parent_product
					when b.product_id then b.sku
					when c.product_id then c.sku    
				end as associate_sku,
				f.parent_qty as associate_quantity,
				"" as loyalty_value,
				"" as product_weight,
				"" as trace_inventory,
				"" as show_in_shopping_cart,
				"" as shipment,
				"" as visibility,';
		  $sql .= rtrim(str_repeat('"",', $outlet_count * 3),",");
				
		  $sql .= ' FROM pos_i1_products as a
				LEFT JOIN pos_i1_products_0_blend as e ON a.product_id = e.blend_product_id and a.account_no = e.account_no
				LEFT JOIN pos_i1_products_4_blend as f ON a.product_id = f.blend_product and a.account_no = f.account_no
				LEFT JOIN pos_i1_products_2_num as b ON b.product_id = f.parent_product and a.account_no = b.account_no
				LEFT JOIN pos_i1_products_3_kilo as c ON c.product_id = f.parent_product and a.account_no = c.account_no
				#slaves
				LEFT JOIN `pos_i1_products_7_category` as ii ON `ii`.`product_id` = `a`.`product_id`
				LEFT JOIN `pos_i1_product_category` as i ON `i`.`cat_id` = `ii`.`category_id`
				LEFT JOIN `pos_i1_products_6_brand` as jj ON `jj`.`product_id` = `a`.`product_id`
				LEFT JOIN `pos_i1_product_brand` as j ON `j`.`brand_index` = `jj`.`brand_id`
				LEFT JOIN `pos_i1_products_8_supplier` as hh ON `hh`.`product_id` = `a`.`product_id`
				LEFT JOIN `pos_e_suppliers` as h ON `h`.`supp_id` = `hh`.`supplier_id`
				LEFT JOIN `pos_i1_products_5_tags` as k ON `k`.`product_id` = `a`.`product_id`
				LEFT JOIN `pos_i1_product_tag` as l ON `l`.`tag_id` = `k`.`tagged_id`
				#taxation

				
				WHERE a.account_no =  "'.$acc.'"
				and a.product_scale = 4
				and f.parent_product is not null';		
		if($where_array['product_stat'] == 1)
		{
			$sql .= ' AND e.status != 120 ';
		} else {
			$sql .= ' AND e.status = '.$where_array['product_stat'].' ';
		}
		if(!empty($where_array['search']))
		{			
			$sql .=	" and (a.product_name  LIKE '%".$where_array['search']."%' OR  a.handle  LIKE '%".$where_array['search']."%' OR  e.sku  LIKE '%".$where_array['search']."%') ";
		}
		if(strlen($where_array['product_cat']) > 0)
		{
			$sql .= ' and i.cat_id = "'.$where_array['product_cat'].'"'; 
		}
		if(strlen($where_array['product_brand']) > 0)
		{
			$sql .= ' and j.brand_index = "'.$where_array['product_brand'].'"'; 
		}
		if(strlen($where_array['supplier_id']) > 0)
		{
			$sql .= ' and h.supp_id = "'.$where_array['supplier_id'].'"'; 
		}
		if(is_array($where_array['tag_id']))
		{				
			$array_str_where_in = '"' . implode('","',$where_array['tag_id']) . '"';
			$sql .= ' and k.tagged_id in ('.$array_str_where_in.')'; 
		}
		$sql .= ' limit 1000';		
		$query2 = $this->db->query($sql);
		$query2 = $this->db->last_query();
		
		$sql = 'SELECT 
				ff.variant_id as id,
				a.handle,
				a.product_name,
				e.sku as sku,
				"BLEND" as product_scale,';
		$sql .= str_repeat('"",', $outlet_count);
		$sql .= '"" as description,
				"" as supplier_name,
				"" as product_category,
				"" as product_brand,
				"" as product_tag,
				"" as supplier_or_operated_price,
				"" as retail_price,
				"" as warehouse_id,
				"" as purchase_id,
				"","","","","","",
				case ff.variant_id
					when d.variant_index then d.sku
				end as associate_sku,
				ff.variant_qty as associate_quantity,
				"" as loyalty_value,
				"" as product_weight,
				"" as trace_inventory,
				"" as show_in_shopping_cart,
				"" as shipment,
				"" as visibility,';
		$sql .= rtrim(str_repeat('"",', $outlet_count * 3),",");				
		$sql .= ' FROM `pos_i1_products` as a
				LEFT JOIN `pos_i1_products_0_blend` as e ON `a`.`product_id` = `e`.`blend_product_id` and a.account_no = e.account_no
				LEFT JOIN `pos_i1_products_4_blend_variant` as ff ON `ff`.`blend_product` = `a`.`product_id`
				LEFT JOIN `pos_i1_products_1_variants` as d ON `d`.`product_id` = `ff`.`product_id` and d.variant_index = ff.variant_id
				#slaves
				LEFT JOIN `pos_i1_products_7_category` as ii ON `ii`.`product_id` = `a`.`product_id`
				LEFT JOIN `pos_i1_product_category` as i ON `i`.`cat_id` = `ii`.`category_id`
				LEFT JOIN `pos_i1_products_6_brand` as jj ON `jj`.`product_id` = `a`.`product_id`
				LEFT JOIN `pos_i1_product_brand` as j ON `j`.`brand_index` = `jj`.`brand_id`
				LEFT JOIN `pos_i1_products_8_supplier` as hh ON `hh`.`product_id` = `a`.`product_id`
				LEFT JOIN `pos_e_suppliers` as h ON `h`.`supp_id` = `hh`.`supplier_id`
				LEFT JOIN `pos_i1_products_5_tags` as k ON `k`.`product_id` = `a`.`product_id`
				LEFT JOIN `pos_i1_product_tag` as l ON `l`.`tag_id` = `k`.`tagged_id`

				WHERE `a`.`account_no` =  "'.$acc.'"
				and ff.variant_id is not null
				and a.product_scale = 4';

		if($where_array['product_stat'] == 1)
		{
			$sql .= ' AND e.status != 120 ';
		} else {
			$sql .= ' AND e.status = '.$where_array['product_stat'];
		}
		if(!empty($where_array['search']))
		{			
			$sql .=	" and (a.product_name  LIKE '%".$where_array['search']."%' OR  a.handle  LIKE '%".$where_array['search']."%' OR  e.sku  LIKE '%".$where_array['search']."%') ";
		}
		if(strlen($where_array['product_cat']) > 0)
		{
			$sql .= ' and i.cat_id = "'.$where_array['product_cat'].'"'; 
		}
		if(strlen($where_array['product_brand']) > 0)
		{
			$sql .= ' and j.brand_index = "'.$where_array['product_brand'].'"'; 
		}
		if(strlen($where_array['supplier_id']) > 0)
		{
			$sql .= ' and h.supp_id = "'.$where_array['supplier_id'].'"'; 
		}
		if(is_array($where_array['tag_id']))
		{				
			$array_str_where_in = '"' . implode('","',$where_array['tag_id']) . '"';
			$sql .= ' and k.tagged_id in ('.$array_str_where_in.')'; 
		}
				
		$sql .= ' order by product_name';
		$sql .= ' limit 1000';		
		$query3 = $this->db->query($sql);
		$query3 = $this->db->last_query();

		$query = $this->db->query($query1." UNION ".$query2." UNION ".$query3);
		
		return $query;
	}
}
?>