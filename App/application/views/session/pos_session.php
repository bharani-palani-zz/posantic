<?php
if(!$this->session->userdata('pos_user') || !$this->session->userdata('user_id')) {
	$this->session->set_flashdata('Notify', 'Session Expired! Please Login Again');
	redirect(base_url());
}
?>