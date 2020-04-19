<?php
class MY_Security extends CI_Security {

    public function __construct()
    {
        parent::__construct();
    }

    public function csrf_show_error()
    {
		// This core function redirects to main login page incase of csrf cache cleared during form login by the user or auto
		// you cant use any codeigniter functions here as the security class is first initialized
        header('Location: ' . htmlspecialchars($_SERVER['REQUEST_URI']), TRUE, 200);
    }
}