<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/03/07 16:51:10 $
 		File Versie					: $Revision: 1.13 $

 		$Log: CRM_nawPortaalSync.php,v $
 		Revision 1.13  2018/03/07 16:51:10  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/02/19 07:17:00  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2018/02/17 19:16:15  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2017/11/25 20:22:26  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2017/10/21 17:28:59  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2017/10/04 16:07:15  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2017/09/25 06:33:04  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2017/09/24 10:03:47  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2017/03/25 15:54:47  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/09/30 16:04:41  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/09/23 15:02:51  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/09/05 16:21:33  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/06/08 07:54:21  rvv
 		*** empty log message ***
 		

*/

include_once("wwwvars.php");
include_once('../classes/portaalSync.php');

$portaalSync=new portaalSync();

$melding='';
if($_POST['updateportaal']==1)
{
  $portaalUpdates=$portaalSync->CRM_syncPortaal(false);
  $melding.= $portaalSync->updatePortaal($portaalUpdates);
  $portaalUpdates=$portaalSync->CRM_syncPortaalPortefeuilleClusters(false);
  $melding.= $portaalSync->updatePortaalClusters($portaalUpdates);
}

echo template($__appvar["templateContentHeader"],$content);
?>


<form method="POST" name="editForm">
<input type="hidden" name="verwerk" value="1">
<table class="list_tabel" cellspacing="0">

<?
echo $melding;
$portaalSync->CRM_syncPortaal(true);
$portaalSync->CRM_syncPortaalPortefeuilleClusters(true);
?>
</table>
<br><br>

<input  class="" type="hidden"  value="1" name="updateportaal" >

<div class="formlinks"> <input type="submit" value="Update portaal" > </div>

</form>
<?




echo template($__appvar["templateRefreshFooter"],$content);
?>