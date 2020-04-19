<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Menu_model extends CI_Model
{
	public $acc;
	public $user_id;
	public $pos_user;
	public $privelage;
    public function __construct() 
    {
        parent::__construct();
		$this->privelage = $this->session->userdata('privelage');
		$this->pos_user = $this->session->userdata('pos_user');
		$this->user_id = $this->session->userdata('user_id');
		$this->acc = $this->session->userdata('acc_no');
    }
	public function get_menu($priv,$show_notify = true)
	{
		switch ($priv) {
		   case 1:
				 $priv_user = 'adm_user';
				 break;
		   case 2:
				 $priv_user = 'mgr_user';
				 break;
		   case 3:
				 $priv_user = 'deo_user';
				 break;
		}	
		$this->db->select('file_menu');
		$this->db->select('menu_order');
		$this->db->select('file_href as href');
		$this->db->select('file_name as named');
		$this->db->select('click_root as root');
		$this->db->select('target');
		$this->db->select('glyphicon');
		$this->db->from('pos_1b_menu');
		$this->db->where(array($priv_user => 1,'status' => 1));
		$this->db->order_by('menu_order','asc');
		$this->db->order_by('submenu_order','asc');
		$rows = $this->db->get();
		if ($rows->num_rows() > 0)
		{
			foreach($rows->result() as $row)
			{
				$array['menu'][$row->file_menu]['file_menu'] = $row->file_menu;
				$array['menu'][$row->file_menu]['menu_order'] = $row->menu_order;
				$array['menu'][$row->file_menu]['href'][] = $row->href;
				$array['menu'][$row->file_menu]['named'][] = $row->named;
				$array['menu'][$row->file_menu]['glyphicon'][] = $row->glyphicon;
				$array['menu'][$row->file_menu]['root'] = $row->root;
				$array['menu'][$row->file_menu]['target'] = $row->target;
			}
			$array['usage'] = $this->login_model->get_timezone_loc_plan_validity($this->acc);
			$array['theme'] = $this->get_user_theme();
			$array['user_id'] = $this->user_id;
			$array['notify'] = $this->notify($show_notify);
			return $array;
		} else {
			return false;	
		}
	}
	public function notify($show_notify = true)
	{
		if($show_notify == true)
		{
			$validity_array = $this->login_model->get_timezone_loc_plan_validity($this->acc);
			$numDays = $validity_array['validity'];
			$plural = $numDays > 1 ? 's' : '';
			$notify = array();
			switch($validity_array['account_code']){
				case "PAY":
					if($validity_array['usage_percent'] > 100)
					{
						$notify['string'] = 'Your '.$validity_array['account_string']." expired, since you've exceeded maximum storage limit, Upgrade Now !!";
					} else
					if($validity_array['credit_amount'] <= 0)
					{
						$notify['string'] = 'Your '.$validity_array['account_string']." expired cause of insufficient funds, Upgrade Now !!";
					} else
					if($validity_array['validity'] <= 0)
					{
						$notify['string'] = 'Your '.$validity_array['account_string']." validity has expired, Activate Now !!";
					} else {
						$notify['string'] = 'Your '.$validity_array['account_string']." expires in <span class='badge'>".$numDays."</span> day".$plural.", ".anchor('account','Activate Now','class="btn btn-xs btn-success"');
						$notify['show'] = $numDays <= 10 ? true : false;
					}
					$notify['stat'] = 'danger';
				break;
				case "TRY":
					if($validity_array['usage_percent'] > 100)
					{
						$notify['string'] = 'Your '.$validity_array['account_string']." expired, since you've exceeded maximum storage limit, Upgrade Now !!";
					} else
					// warning: add pos_a_master->credit_amount as 1 for trial users during merchant creation, he critically need at least 1 Rs for using trial.
					if($validity_array['credit_amount'] <= 0)
					{
						$notify['string'] = 'Your '.$validity_array['account_string']." expired cause of insufficient funds, Upgrade Now !!";
					} else
					if($validity_array['validity'] <= 0)
					{
						$notify['string'] = 'Your '.$validity_array['account_string']." validity has expired, Activate Now !!";
					} else {
						$notify['string'] = 'Your '.$validity_array['account_string']." expires in <span class='badge'>".$numDays."</span> day".$plural.", ".anchor('account','Activate Now','class="btn btn-xs btn-success"');
					}
					$notify['show'] = true;
					$notify['stat'] = 'warning';
				break;
				case "TES":
					$notify['string'] = 'Dear '.$this->session->userdata('pos_hoster_cmp').', our '.$validity_array['account_string']." expires in <span class='badge'>".$numDays."</span> day".$plural."!";
					$notify['show'] = true;
					$notify['stat'] = 'warning';
				break;
			}		
			return $notify;
		} else {
			return array('string' => false,'show' => false,'stat' => false);
		}
	}
	public function get_user_theme()
	{
		$this->db->select('my_theme');
		$query = $this->db->get_where('pos_e_login',array('user_id' => $this->user_id));
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return strlen($row['my_theme']) > 0 ? $row['my_theme'] : NULL;
		} else {
			return NULL;	
		}
	}
	public function theme_post($theme_root,$theme_user)
	{
		$this->db->where('user_id',$theme_user);
		if($this->db->update('pos_e_login',array('my_theme' => $theme_root)))
		{
			return 1;	
		} else {
			return 0;
		}
	}
}
?>