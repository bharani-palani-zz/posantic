<?php
class Quickey_model extends CI_Model
{
	public $max_qt_headers;
	public $max_qt_products_per_page;
	public $max_qt_pages;
    public function __construct() 
    {
        parent::__construct();
		$this->max_qt_headers = 4;
		$this->max_qt_products_per_page = 36;
		$this->max_qt_pages = 10;
    }
	public function show_quickeys($acc)
	{
		$query = $this->db->get_where('pos_i7_quickeys',array('account_no' => $acc));
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
			return array();	
		}				
	}
	public function quickey_combo($acc)
	{
		$query = $this->db->get_where('pos_i7_quickeys',array('account_no' => $acc));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array['Select Quick touch'][$row->quick_index] = $row->quickey_name;	
			}
			return $array;
		} else {
			return array(array('Select Quick touch') => '');	
		}
	}
	public function quickey_details($touch_id,$acc)
	{
		$query = $this->db->get_where('pos_i7_quickeys',array('account_no' => $acc,'quick_index' => $touch_id));
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
	public function get_touch_data($touch_id,$acc)
	{
		$this->db->select('grp.qk_grp_index as cat_id');
		$this->db->select('grp.grp_position as cat_position');
		$this->db->select('b.colour as colour');
		$this->db->select('b.product_id as product_id');
		$this->db->select('b.prd_position as prd_position');
		$this->db->select('a.page_no as prd_page');
		$this->db->select('b.label as label');
		$this->db->select('grp.grp_name as cat_name');
		$this->db->select('b.colour as colour');
		$this->db->select('a.page_index as page_index');
		$this->db->select('if(f.product_id = b.product_id,"VARIANTS",
							if(c.product_id = b.product_id,"NUM",
							if(d.blend_product_id = b.product_id,"BLEND",
							if(e.product_id = b.product_id,"KILO",NULL)))) as scale',false);
		$this->db->from('pos_i8_quickey_group_page as a');
		$this->db->join('pos_i8_quickey_group as grp','grp.qk_grp_index = a.group_id and a.account_no = grp.account_no','left');
		$this->db->join('pos_i9_quickey_child as b','a.page_index = b.group_page and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_2_num as c','c.product_id = b.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_0_blend as d','d.blend_product_id = b.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as e','e.product_id = b.product_id and a.account_no = e.account_no','left');
		$this->db->join('pos_i1_products as f','f.product_id = b.product_id and f.product_scale = "VARIANTS"  and a.account_no = f.account_no','left');
		$this->db->where('a.quickey_id',$touch_id);
		$this->db->where('a.account_no',$acc);
		$this->db->order_by('cat_position asc');
		$this->db->order_by('prd_page asc');
		$this->db->order_by('prd_position asc');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $key => $row)
			{
				$array['quick_touch']['group'][$row->cat_position]['group_name'] = $row->cat_name;				
				$array['quick_touch']['group'][$row->cat_position]['group_position'] = $row->cat_position;				
				$array['quick_touch']['group'][$row->cat_position]['group_id'] = $row->cat_id;	
				$array['quick_touch']['group'][$row->cat_position]['pages'][$row->prd_page]['page'] = $row->prd_page;
				$array['quick_touch']['group'][$row->cat_position]['pages'][$row->prd_page]['page_index'] = $row->page_index;
				$array['quick_touch']['group'][$row->cat_position]['pages'][$row->prd_page]['keys'][$row->product_id] = array(
																														'product_id' => $row->product_id,
																														'position' => $row->prd_position,
																														'colour' => $row->colour,
																														'scale' => $row->scale,
																														'label' => $row->label
																														);				
				
			}
			return $array;
		} else {
			return array('quick_touch' => array('group' => ''));	
		}				
	}
	public function update_quick_touch($data) // complete ajax
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		if(isset($data['group_params']))
		{
			foreach($data['group_params'] as $key => $value)
			{
				$update = array('grp_position' => $key);
				$this->db->where('qk_grp_index',$data['group_params'][$key]);	
				$this->db->where('quickey_id',$data['touch_id']);	
				$this->db->where('account_no',$data['merchant_id']);	
				$this->db->update('pos_i8_quickey_group',$update);
			}
		} else if(isset($data['group_name_params'])) {
			$update = array('grp_name' => $data['group_name_params'][0]);
			$this->db->where('qk_grp_index',$data['group_name_params'][1]);	
			$this->db->where('quickey_id',$data['touch_id']);	
			$this->db->where('account_no',$data['merchant_id']);	
			$this->db->update('pos_i8_quickey_group',$update);
		} else if(isset($data['group_delete_params'])) {
			$this->db->delete('pos_i9_quickey_child',array('group' => $data['group_delete_params'][0],'quickey_id' => $data['touch_id'],'account_no' => $data['merchant_id']));
			$this->db->delete('pos_i8_quickey_group_page',array('group_id' => $data['group_delete_params'][0],'quickey_id' => $data['touch_id'],'account_no' => $data['merchant_id']));
			$this->db->delete('pos_i8_quickey_group',array('qk_grp_index' => $data['group_delete_params'][0],'quickey_id' => $data['touch_id'],'account_no' => $data['merchant_id']));
		} else if(isset($data['add_group_params'])) {
			$this->db->select('count(*) as counted');
			$q = $this->db->get_where('pos_i8_quickey_group',array('quickey_id' => $data['touch_id'], 'account_no' => $data['merchant_id']));
			$row = $q->row_array();
			if($row['counted'] < $this->max_qt_headers)
			{
				$insert1 = array(
							'qk_grp_index' => $data['add_group_params'][2],
							'quickey_id' => $data['touch_id'],
							'grp_name' => $data['add_group_params'][0],
							'grp_position' => $data['add_group_params'][1],
							'account_no' => $data['merchant_id'] 						
								);
				$this->db->insert('pos_i8_quickey_group',$insert1);
				$insert2 = array(
								'page_index' => $data['add_group_params'][3],
								'quickey_id' => $data['touch_id'],
								'group_id' => $data['add_group_params'][2],
								'page_no' => 0,
								'account_no' => $data['merchant_id']			
								);			
				$this->db->insert('pos_i8_quickey_group_page',$insert2);
			}
		} else if(isset($data['prd_params'])) {
			foreach($data['prd_params'] as $key => $value)
			{
				$update = array('prd_position' => $key);
				$this->db->where('product_id',$data['prd_params'][$key]);	
				$this->db->where('quickey_id',$data['touch_id']);	
				$this->db->where('account_no',$data['merchant_id']);	
				$this->db->update('pos_i9_quickey_child',$update);
			}
		} else if(isset($data['color_change_params'])) {
			$update = array('colour' => $data['color_change_params'][0]);
			$this->db->where('product_id',$data['color_change_params'][1]);	
			$this->db->where('quickey_id',$data['touch_id']);	
			$this->db->where('account_no',$data['merchant_id']);	
			$this->db->update('pos_i9_quickey_child',$update);
		} else if(isset($data['rename_label_params'])) {
			$update = array('label' => $data['rename_label_params'][0]);
			$this->db->where('product_id',$data['rename_label_params'][1]);	
			$this->db->where('quickey_id',$data['touch_id']);	
			$this->db->where('account_no',$data['merchant_id']);	
			$this->db->update('pos_i9_quickey_child',$update);
		} else if(isset($data['delete_product_params'])) {
			$this->db->delete('pos_i9_quickey_child',array('product_id' => $data['delete_product_params'][0],'group' => $data['delete_product_params'][1],'group_page' => $data['delete_product_params'][2],'quickey_id' => $data['touch_id'],'account_no' => $data['merchant_id']));
		} else if(isset($data['delete_page'])) {
			$this->db->delete('pos_i8_quickey_group_page',array('page_index' => $data['delete_page'][1],'group_id' => $data['delete_page'][0],'quickey_id' => $data['touch_id'],'account_no' => $data['merchant_id']));
		} else if(isset($data['add_page'])) {
			$this->db->select('count(*) as counted');
			$q = $this->db->get_where('pos_i8_quickey_group_page',array('quickey_id' => $data['touch_id'], 'group_id' => $data['add_page'][0], 'account_no' => $data['merchant_id']));
			$row = $q->row_array();
			if($row['counted'] < $this->max_qt_pages)
			{
				$insert = array(
							'page_index' => $data['add_page'][2],
							'quickey_id' => $data['touch_id'],
							'group_id' => $data['add_page'][0],
							'page_no' => $data['add_page'][1],
							'account_no' => $data['merchant_id']			
							);
				$this->db->insert('pos_i8_quickey_group_page',$insert);
			}
		} else if(isset($data['update_prd_page'])) {
			foreach($data['update_prd_page'][0] as $key => $value)
			{
				$update = array('prd_page' => $key);
				$this->db->where('prd_page',$value);	
				$this->db->where('cat_group',$data['update_prd_page'][1]);	
				$this->db->where('quickey_id',$data['touch_id']);	
				$this->db->where('account_no',$data['merchant_id']);	
				$this->db->update('pos_i9_quickey_child',$update);
			}
		} else if(isset($data['insert_product'])) {
			$this->db->select('count(*) as counted');
			$q = $this->db->get_where('pos_i9_quickey_child',array('quickey_id' => $data['touch_id'], 'group' => $data['insert_product'][2], 'group_page' => $data['insert_product'][1], 'account_no' => $data['merchant_id']));
			$row = $q->row_array();
			if($row['counted'] < $this->max_qt_products_per_page)
			{			
				$child_id = $this->taxes_model->make_single_uuid();
				$insert = array(
							'child_index' => $child_id,
							'quickey_id' => $data['touch_id'],
							'prd_position' => $data['insert_product'][0],
							'group_page' => $data['insert_product'][1],
							'group' => $data['insert_product'][2],
							'product_id' => $data['insert_product'][3],
							'colour' => $data['insert_product'][4],
							'label' => $data['insert_product'][5],
							'account_no' => $data['merchant_id']							
							);
				$this->db->insert('pos_i9_quickey_child',$insert);
			}
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return 0;
		} else {
			return 1;	
		}
	}
	public function quick_key_Autocomplete($options = array(),$acc)
	{
		$this->db->select('a.product_id as product_id');
		$this->db->select('if(a.product_scale = 3,a.product_name,if(a.product_scale = 1,concat(a.product_name," | SKU: ",c.sku),if(a.product_scale = 2,concat(a.product_name," | SKU: ",d.sku),if(a.product_scale = 4,concat(a.product_name," | SKU: ",e.sku),NULL)))) as prod_name,',false);
		$this->db->select('a.product_scale as scale');
		$this->db->select('a.product_name as label');
		$this->db->from('pos_i1_products as a');
		$this->db->join('pos_i1_products_1_variants as b','a.product_id = b.product_id and a.account_no = b.account_no','left');
		$this->db->join('pos_i1_products_2_num as c','a.product_id = c.product_id and a.account_no = c.account_no','left');
		$this->db->join('pos_i1_products_3_kilo as d','a.product_id = d.product_id and a.account_no = d.account_no','left');
		$this->db->join('pos_i1_products_0_blend as e','a.product_id = e.blend_product_id and a.account_no = e.account_no','left');
		$this->db->where("
						(a.product_name  LIKE '%".$options['keyword']."%'
						OR  a.handle  LIKE '%".$options['keyword']."%'
						OR  b.sku  LIKE '%".$options['keyword']."%'
						OR  c.sku  LIKE '%".$options['keyword']."%'
						OR  d.sku  LIKE '%".$options['keyword']."%'
						OR  e.sku  LIKE '%".$options['keyword']."%')",
						NULL, false);		

		$this->db->where('a.account_no',$acc); // and clause must come at the last
		$this->db->where("(b.status = 30 or c.status = 30 or d.status = 30 or e.status = 30)"); 
		$this->db->order_by('prod_name');
		$this->db->group_by('product_id');
		$this->db->limit(100);
		$query = $this->db->get();
		return $query->result();
	}
	public function update_quickey($data)
	{
		$update = array(
					'quickey_name' => $data['touch_name'],
					'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now())	
					);
		$this->db->where('quick_index', $data['touch_id']);
		$this->db->where('account_no', $data['acc']);
		if($this->db->update('pos_i7_quickeys', $update))
		{
			return 1;	
		} else {
			return 0;
		}
	}
	public function add_quicktouch($data)
	{
		$this->db->trans_begin();
		$this->db->trans_start();
		$touch_id = $this->taxes_model->make_single_uuid();
		$insert['main'] = array(
							'quick_index' => $touch_id,
							'quickey_name' => $data['quicktouch_name'],
							'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
							'is_delete' => 10,
							'account_no' => $data['acc']							
								);
		$this->db->insert('pos_i7_quickeys', $insert['main']);
		$group_id = $this->taxes_model->make_single_uuid();
		$insert['group'] = array(
							'qk_grp_index' => $group_id,
							'quickey_id' => $touch_id,
							'grp_name' => 'Group 1',
							'grp_position' => 0,
							'account_no' => $data['acc']	
							);
		$this->db->insert('pos_i8_quickey_group', $insert['group']);
		$page_index = $this->taxes_model->make_single_uuid();
		$insert['page'] = array(
						'page_index' => $page_index,
						'quickey_id' => $touch_id,
						'group_id' => $group_id,
						'page_no' => 0,
						'account_no' => $data['acc']					
						);
		$this->db->insert('pos_i8_quickey_group_page', $insert['page']);
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return false;	
		} else {
			return $touch_id;
		}
	}
	public function delete_quick_touch($data)
	{
		$oldv = $this->db->db_debug;
		$this->db->db_debug = FALSE; 
		$this->db->delete('pos_i7_quickeys',array('quick_index' => $data['touch_id'],'is_delete' => 10,'account_no' => $data['acc']));
		$aff = $this->db->affected_rows();
		$this->db->db_debug = $oldv; 
		if($aff < 1) {
			return 0;
		} else {
			return 1;
		}
	}
}