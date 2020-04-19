<?php
class Site_404 extends CI_Controller
{
    public function __construct() 
    {
        parent::__construct();
    }
    public function index()
	{
		$url = $_SERVER['HTTP_HOST'];
		$host_names = explode(".",  $url);
		if(count($host_names) == 3)
		{
			// call secure page for secure domain users
			if($host_names[0] == 'secure') {
				$array = $this->admin_model->settings_model();
				$data = array(
						'hotline' => $array[0],
						'email' => $array[1],
						'web' => $array[2],
						'cmp' => $array[3],
						'version_type' => $array[4],
						'version_year' => $array[5]
						);
				$this->load->view('logon/secure_404',$data);
			} else {
				// call session set for App users
				$this->load->view('session/pos_session');
				$this->load->view('site_404/url_404'); 
			}
		} else {
			// call session set for App users as localhost domain
			$this->load->view('session/pos_session');
			$this->load->view('site_404/url_404'); 
		}
	}
}
?>