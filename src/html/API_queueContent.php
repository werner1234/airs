<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 18 augustus 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/03/01 08:57:20 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: API_queueContent.php,v $
    Revision 1.1  2019/03/01 08:57:20  cvs
    call 7364

    Revision 1.1  2017/08/18 14:42:58  cvs
    call 5815

 	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_APIextern.php");

$apiExtern = new AE_cls_APIextern();

?>
<style>
  .dispContainer{
    background: #EEE;
  }
  .dispRow{
    padding:10px;
  }
  .dispKey{
    display: inline-block;
    width: 120px;
  }
  .dispValue{
    display: inline-block;
    font-weight: bold;
  }

</style>

<div class="dispContainer">
  <?=$apiExtern->getContentById($_GET["id"])?>
</div>

