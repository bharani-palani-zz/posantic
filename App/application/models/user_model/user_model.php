<?php
class User_model extends CI_Model
{
    public function __construct() 
    {
        parent::__construct();
		$this->load->library('upload');
		$this->load->library('image_lib');
	}
	public function user_count($acc)
	{
		$this->db->select('count(*)');
		$query = $this->db->get_where('pos_e_login',array('account_no' => $acc,'user_status' => 10));
		if ($query->num_rows() > 0)
		{
		   $row = $query->row_array();
		   $count = $row['count(*)'];
		   return $count;
		} else {
		   return 0;	
		}
	}
	public function get_roles($priv)
	{
		$this->db->select('*');
		$where_array = ($priv < 2) ? array('priv_id >=' => $priv) : array('priv_id >' => $priv);
		$this->db->where($where_array);
		$rows = $this->db->get('pos_1a_privileges');	
		if($rows!=false){
			foreach($rows->result() as $row){
				$array[$row->priv_id] = strtoupper($row->priv_acronym.' - '.$row->priv_name);	
			}
		}
		return $array;
	}
	public function all_roles()
	{
		$rows = $this->db->get('pos_1a_privileges');	
		if($rows!=false){
			foreach($rows->result() as $row){
				$array[$row->priv_id] = strtoupper($row->priv_acronym.' - '.$row->priv_name);	
			}
		}
		return $array;
	}
	public function user_details($user_id,$acc)
	{
		$this->db->select('*');
		$this->db->where(array('account_no' => $acc,'user_id' => $user_id,'user_status !=' => 120));	
		$query = $this->db->get('pos_e_login');
		if($query!=false){
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
	public function user_details_wrt_email($email_or_userid,$acc)
	{
		$this->db->select('*');
		if(strpos($email_or_userid,'@') === false)
		{
			$this->db->where('user_name',$email_or_userid);
		} else {
			$this->db->where('user_mail',$email_or_userid);
		}
		$this->db->where(array('account_no' => $acc , 'user_status' => 10));	
		$query = $this->db->get('pos_e_login');
		if($query!=false){
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
	public function all_users($acc)
	{
		$this->db->select('*');
		$rows = $this->db->get_where('pos_e_login',array('account_no' => $acc,'user_status !=' => 120));
		if($rows!=false){
			foreach($rows->result() as $row){
				$array[] = array($row->user_id, $row->display_name, $row->user_status, $row->privelage,$row->location,$row->user_mail ,$row->target_day,$row->target_week,$row->target_month,$row->last_login, $row->user_mobile);				
			}
		}
		return $array;
	}
	public function current_users($acc,$loc_id,$priv)
	{
		$this->db->select('*');
		$where_array = ($loc_id == 'ALL OUTLETS') ? array('account_no' => $acc,'privelage >=' => $priv,'user_status !=' => 120) : array('account_no' => $acc,'location' => $loc_id,'privelage >=' => $priv,'user_status != ' => 120);
		$this->db->where($where_array);
		$rows = $this->db->get_where('pos_e_login');
		if($rows!=false){
			$array = array();
			foreach($rows->result() as $row){
				$array[] = array($row->user_id,$row->display_name, $row->user_status, $row->privelage,$row->location,$row->user_mail,$row->target_day,$row->target_week,$row->target_month,$row->last_login, $row->user_mobile);
			}
		}
		return $array;
	}
	public function get_locations($acc)
	{
		$this->db->select('*');
		$this->db->order_by('location');
		$rows = $this->db->get_where('pos_b_locations',array('account_no' => $acc,'outlet_stat' => 30));
		if($rows!=false){
			foreach($rows->result() as $row){
				$array[$row->loc_id] = $row->location;
			}
			return $array;
		} else {
			return array('' => 'NULL');	
		}
	}
	public function get_mgr_own_locations($loc,$acc)
	{
		$this->db->select('*');
		$rows = $this->db->get_where('pos_b_locations',array('account_no' => $acc, 'loc_id' => $loc,'outlet_stat' => 30));
		if($rows!=false){
			$array = array();
			foreach($rows->result() as $row){
				$array[$row->loc_id] = $row->location;
			}
			return $array;
		} else {
			return array('' => 'NULL');				
		}
	}
	public function get_locations_assoc_array($acc,$account_handle)
	{
		$params = array($acc);
		$rows = $this->db->query("select * from pos_b_locations where account_no = ? and outlet_stat = 30 order by loc_id", $params); 
		if($rows!=false){
			foreach($rows->result() as $row){
				$array[$row->loc_id] = $row->location;
			}
			return ($account_handle == 'Multiple') ? array('' => 'ALL OUTLETS') + $array : $array;
		} else {
			return array('' => '');	
		}
	}
	public function get_location_assoc_array_wrt_user()
	{
		if($this->session->userdata('privelage') == 1)
		{
			$sql = 	"select * from pos_b_locations where account_no = ? and user_status = 10";
			$params = array($this->session->userdata('acc_no'));
		} else {
			$sql = "select * from pos_b_locations where account_no = ? and loc_id = ? and user_status = 10";
			$params = array($this->session->userdata('acc_no'),$this->session->userdata('loc_id'));
		}
		$rows = $this->db->query($sql, $params); 
		$array = array();
		if($rows!=false){
			foreach($rows->result() as $row){
				$array[$row->loc_id] = $row->location;
			}
			return $array;
		} else {
			return array();	
		}
	}
	public function M_current_proc_names()
	{
		$proc_name = array();
		$param = array($this->session->userdata('acc_no'));
		$rows = $this->db->query("select user_id,user_name,user_status,privelage from pos_e_login where account_no = ? and user_status = 10", $param); 
		if($rows!=false){
			foreach($rows->result() as $row){
				$proc_name[] = $row->user_name;
			}
		}
		return $proc_name;
	}
	public function all_user_dropdown($acc)
	{
		$query = $this->db->get_where('pos_e_login',array('account_no' => $acc,'user_status' => 10));
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$array[$row->user_id] = $row->display_name;
			}
			return $array;
		} else {
			return array();
		}	
	}
	public function get_all_locations($acc)
	{
		$this->db = $this->load->database('default', TRUE);  
		$this->db->select('*');
		$this->db->order_by('location');
		$rows = $this->db->get_where('pos_b_locations',array('account_no' => $acc));
		if($rows!=false){
			foreach($rows->result() as $row){
				$array[] = array($row->loc_id,$row->location);
			}
		}
		return $array;
		$this->db->close();		
	}
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
	public function do_upload($id)
	{
		//image upload
		$avatar_dir = APPPATH.'user_images/'.md5($this->session->userdata('acc_no')).'/users';
		if(!is_dir($avatar_dir)){mkdir($avatar_dir,0777,true);}		
		$path = $avatar_dir;
		$config['upload_path'] = './'.$path.'/';
		$config['allowed_types'] = 'gif|jpeg|jpg|png';
		$config['max_size']	= '3072';
		$config['max_width']  = '3264';
		$config['max_height']  = '2448';
		$this->upload->initialize($config);
		if(!$this->upload->do_upload())
		{
			return false;
		} else {			
			$file_pattern = './'.$path.'/'.$id.'_thumb.*';			
			array_map( "unlink", glob( $file_pattern ) );

			$data = array('upload_data' => $this->upload->data());
			$file = $data['upload_data']['file_name'];
			$file_ext = $data['upload_data']['file_ext'];
			$filename = $id;			
			$old_name = './'.$path.'/'.$file;
			$new_name = './'.$path.'/'.$filename.$file_ext;			

			rename($old_name,$new_name);

			$config['image_library'] = 'gd2';
			$config['source_image'] = $path.'/'.$filename.$file_ext;
			$config['new_image'] = $path.'/'.$filename.$file_ext;
			$config['create_thumb'] = TRUE;
			$config['maintain_ratio'] = FALSE;
			$config['width'] = 300;
			$config['height'] = 300;			
			$this->load->library('image_lib', $config);
			$this->image_lib->initialize($config);				
			if($this->image_lib->resize())
			{
				unlink($new_name);
				return true;
			}		
		}
	}
	public function add_user($data)
	{
		$uuid = $this->make_single_uuid();
		$concat = $uuid.$data['new_emp_pass'].$data['subdomain'].$data['acc']; // secret combination to create sha1
		$hash_pass = $this->encrypt->sha1($concat);		
		$data = array(
						'user_id' => $uuid,
						'user_name' => $data['user_name'],
						'display_name' => $data['disp_name'],
						'password' => $hash_pass,
						'user_status' => 10,
						'privelage' => $data['role'],
						'user_mail' => $data['email'],
						'user_mobile' => $data['emp_mobile'],
						'is_delete' => 100,
						'target_day' => 0,
						'target_week' => 0,
						'target_month' => 0,
						'last_login' => '',
						'current_login' => '',
						'account_no' => $data['acc'],
						'location' => $data['company'] == '' ? NULL : $data['company'],
						); 
		if($this->db->insert('pos_e_login', $data))
		{
			if(!empty($_FILES['userfile']['name'])) {
				$bool = $this->do_upload($uuid);
			}
			return 1;
		} else {
			return 0;
		}
	}
	public function save_password($data)
	{
		$concat = $data['user_id'].$data['password'].$data['subdomain'].$data['acc']; // secret combination to update sha1
		$hash_pass = $this->encrypt->sha1($concat);		
		$update = array(
					   'password' => $hash_pass
					);
		$this->db->where(array('user_id' => $data['user_id'], 'account_no' => $data['acc']));
		if($this->db->update('pos_e_login', $update))
		{
			return 1;	
		} else {
			return 0;
		}
	}
	public function save_user($data)
	{
		$uuid = $this->make_single_uuid();
		$concat = $data['edit_user_id'].$data['edit_pass'].$data['subdomain'].$data['acc']; // secret combination to update sha1
		$hash_pass = $this->encrypt->sha1($concat);		
		$update = array(
					   'user_name' => strlen($data['edit_emp_name']) > 0 ? $data['edit_emp_name'] : NULL,
					   'display_name' => strlen($data['edit_disp_name']) > 0  ? $data['edit_disp_name'] : NULL,
					   'password' => strlen($data['edit_pass']) > 0 ? $hash_pass : NULL,
					   'user_status' => strlen($data['edit_emp_status']) > 0 ? $data['edit_emp_status'] : NULL,
					   'privelage' => strlen($data['edit_emp_role']) > 0 ? $data['edit_emp_role'] : NULL,
					   'user_mail' => strlen($data['edit_email']) > 0 ? $data['edit_email'] : NULL,
					   'user_mobile' => strlen($data['edit_emp_mobile']) > 0 ? $data['edit_emp_mobile'] : NULL,
					);
		$update = array_filter($update) + array('location' => $data['edit_emp_outlet'] == '' ? NULL : $data['edit_emp_outlet']);
		$this->db->where(array('user_id' => $data['edit_user_id'], 'account_no' => $data['acc']));
		if($this->db->update('pos_e_login', $update))
		{
			if(!empty($_FILES['userfile']['name'])) {
				$bool = $this->do_upload($data['edit_user_id']);
			}			
			return 1;	
		} else {
			return 0;
		}
	}
	public function save_target($data)
	{
		switch ($data['target_id']) {
			case "set_day":
				$column = 'target_day';
				break;
			case "set_week":
				$column = 'target_week';
				break;
			case "set_month":
				$column = 'target_month';
				break;
		}

		$update = array(
					   $column => $data['new_val']
					);
		$this->db->where(array('user_id' => $data['emp_id'], 'account_no' => $data['acc']));
		if($this->db->update('pos_e_login', $update))
		{
			return 1;	
		} else {
			return 0;
		}
	}
	public function delete_user($user_id,$acc)
	{
		$this->db->where(array('user_id' => $user_id,'account_no' => $acc));
		if($this->db->update('pos_e_login', array('user_status' => 120)))
		{
			return 1;
		} else {
			return 0;
		}
	}
}
?>