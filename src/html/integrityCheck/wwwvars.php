<?php
/*
    AE-ICT sourcemodule created 23 Mar 2020
    Author              : Chris van Santen
    Filename            : wwwvars.php

    $Log: wwwvars.php,v $
    Revision 1.2  2020/06/19 10:49:30  cvs
    call 3205

    Revision 1.1  2020/03/25 15:06:35  cvs
    call 3205

    Revision 1.1  2020/03/23 13:04:14  cvs
    call 3205

*/

if (version_compare(phpversion(), '5.3.0', '<'))
{
  include_once("AE_lib2.php3");
}


include_once("../../config/local_vars.php");
include_once("../../config/vars.php");
include_once("../../config/auth.php");


