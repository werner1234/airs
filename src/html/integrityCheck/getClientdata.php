<?php
/*
    AE-ICT sourcemodule created 25 Mar 2020
    Author              : Chris van Santen
    Filename            : index.php

    $Log: getClientdata.php,v $
    Revision 1.3  2020/06/22 11:37:55  cvs
    call 3205

    Revision 1.2  2020/06/19 10:49:16  cvs
    call 3205

    Revision 1.1  2020/06/10 11:56:05  cvs
    call 3205

    Revision 1.1  2020/03/25 15:06:35  cvs
    call 3205

*/


$__appvar['date_seperator'] = "-";
$p = explode("html", getcwd());

$__appvar['base_dir']       = $p[0];

$inclFiles = array(
  $__appvar['base_dir']."config/local_vars.php",
  $__appvar['base_dir']."config/applicatie_functies_minimal.php",
  $__appvar['base_dir']."classes/AE_cls_config.php",
  $__appvar['base_dir']."classes/AE_cls_mysql.php",
  $__appvar['base_dir']."classes/AE_cls_integrityCheck.php"
);
//print_r($inclFiles);
foreach ($inclFiles as $file)
{
//  echo "<br>".$file."  ".(file_exists($file)?"ok":"fail");
  include_once($file);
}

echo "<h3>start controle</h3>";
$ic = new AE_cls_integrityCheck();
$ic->checkDays = 5;
$ic->loadTable("Fondsen");
$ic->loadTable("Fondskoersen");
$ic->loadTable("Portefeuilles");
$ic->loadTable("Rekeningafschriften");
$ic->loadTable("Rekeningen");
$ic->loadTable("Rekeningmutaties");
$__debug = true;
//debug($ic->getResults());
$ic->pushResults();
?>
<h3>controle klaar</h3>
<br/>
<br/>
<br/>
<br/>
<br/>
