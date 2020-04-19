            <div class="text-muted h5">
                &nbsp;<i class="fa fa-cloud fa-fw"></i><?php echo $this->session->userdata('pos_hoster_cmp'); ?>		
                <span class="pull-right"><i class="fa fa-shopping-cart fa-fw"></i><?php echo $this->session->userdata('cmp_name'); ?>&nbsp;</span>		
            </div>		            
        </div>    
    </div>    
	<?php
    echo '<script type="text/javascript" src="'.base_url(JQUERY_FOR_SB).'"></script>'."\n";
    echo '<script type="text/javascript" src="'.base_url(JQUERY_COOKIE).'"></script>'."\n";
    echo '<script type="text/javascript" src="'.base_url(BS_MAIN_JS).'"></script>'."\n";
    echo '<script type="text/javascript" src="'.base_url(BS3_METISMENU).'"></script>'."\n";
    echo '<script type="text/javascript" src="'.base_url(BS3_SIDEBOX_JS).'"></script>'."\n";
    if(!empty($foot['script']))
    {
        foreach($foot['script'] as $script)
        {
            echo $script;
        }
    }    
    ?>    
</body>
</html>