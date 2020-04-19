<?php 
$header['view']['title'] = 'Browser Void';
$header['head']['style'][0] = link_tag(POS_CSS_ROOT.'app_Styles.css')."\n";
$header['priv'] = $this->session->userdata('privelage');
$role = $this->roles_model->get_roles($header['priv']);
$header['role_code'] = $role[0][0];
$header['role_name'] = $role[0][1];
$header['top_menu'] = $this->menu_model->get_menu($header['priv']);
$header['activate_tab'] = -1;
$this->load->view('top_page/top_page',$header);
?>
<div style="width:51%; margin-left:25%;" align="center">
    <div>
        <h2 class="cancel_button">Oops! "<?php echo $this->agent->browser();?>" is not supported which doesn`t have offline storage facility</h2>
    </div>
    <div>
        <h2>Download or switch to a different browser listed below</h2>
        <div>
            <p>
                <a href="http://www.google.com/chrome" target="_blank" style="color:#03F;">Download Google Chrome</a>
            </p>
            <p>
                <a href="http://www.apple.com/safari" target="_blank" style="color:#03F;">Download Safari</a>
            </p>
       </div>
    </div>
    <div align="center">
        <a class="bl_button" href="<?php echo base_url().'dashboard' ?>"><?php echo $_SERVER['HTTP_HOST']; ?> supports and works extremely well on the above browser. Anyhow, click to check <b>Dashboard</b></a>
    </div>
</div><br>
<br>
<?php
$this->load->view('bottom_page/bottom_page');
?>
