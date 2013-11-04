<?php

    /*
     * This is called to log out the user,
     * clear all the session data and set database fields and log entries and everything else in the logout process
     */
    error_reporting(E_ALL | E_WARNING | E_NOTICE);
    ini_set('display_errors', TRUE);
    require_once 'system/bootstrap.inc.php';
    if ($SESSION->isLoggedIn())
    {
       $SESSION->logoutUser();
       redirect_to(BASE_URL);
    }
    redirect_to(BASE_URL);