<?php

/*
 *  Name:   Brandon Mitchell
 *  Description:    The logout page.  Users are directed here when they hit the
 *                  logout button.  Destrous the session and redirects to the 
 *                  home page
 */

session_start();
session_unset();
session_destroy();

header('location: .');
?>