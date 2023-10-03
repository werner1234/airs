<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/10/30 13:31:24 $
 		File Versie					: $Revision: 1.5 $

 		$Log: orderregelscontrole.php,v $
 		Revision 1.5  2011/10/30 13:31:24  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2009/01/20 17:46:01  rvv
 		*** empty log message ***

 		Revision 1.3  2006/11/14 11:53:52  rvv
 		Wijzigen orders beperkt

 		Revision 1.2  2006/10/17 08:31:09  rvv
 		Vermogensbeheerder ophalen uit portefeuille

 		Revision 1.1  2006/10/17 06:16:11  rvv
 		ordercontrole


*/

include_once("wwwvars.php");
include_once("../classes/AE_cls_progressbar.php");
include_once("./orderControlleRekenClass.php");
include_once("./rapport/rapportRekenClass.php");

echo template($__appvar["templateContentHeader"],$content);
flush();

echo "<BR> Herberekening controles. <br>";


$prb 						= new ProgressBar(536,8);
$prb->color 		= 'maroon';
$prb->bgr_color = '#ffffff';
$prb->brd_color = 'Silver';
$prb->left 			= 0;
$prb->top 			=	0;
$prb->show();


$db=new DB();
$db2=new DB();

//$query = "SELECT Vermogensbeheerders.order_controle FROM Vermogensbeheerders, Orders WHERE Vermogensbeheerders.Vermogensbeheerder = Orders.Vermogensbeheerder	AND Orders.orderid = '".$orderid."' " ;
//	$db->SQL($query);
//	$checks = $db->lookupRecord();
//
//$checks = unserialize($checks['order_controle']);

$controle = new orderControlleBerekening();
$checks = $controle->getchecks();

$controle->setallchecks($__ORDERvar["orderControles"]);

$query = "SELECT * FROM OrderRegels WHERE orderid = '$orderid'" ;
$db->SQL($query);
$db->Query();

$records = $db->records();

			$prb->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
			$prb->moveStep($pro_step);

while ($orderRegel = $db->nextRecord())
{
     $query = "	SELECT Vermogensbeheerder FROM Portefeuilles
  				WHERE portefeuille = '".$orderRegel['portefeuille']."'";
    $db2->SQL($query);
	$vermogenbeheerder = $db2->lookupRecord();
	$vermogenbeheerder = $vermogenbeheerder['Vermogensbeheerder'];

	$controle->setchecks(unserialize($checks[$vermogenbeheerder]));
	$controle->setregels(unserialize($orderRegel['controle_regels']));
    $hoogste=0;
    $controle->setdata($orderRegel['orderid'],$orderRegel['portefeuille'],$orderRegel['valuta'],$orderRegel['aantal'],true);
    $resultaat=$controle->check() ;

  foreach($resultaat as $keyname => $value)
	{
	 if ($value > $hoogste ) $hoogste = $value;
	}

  $query = "UPDATE OrderRegels SET controle = '$hoogste'
  			WHERE orderid = '$orderid'
  			AND portefeuille = '".$orderRegel['portefeuille']."'
  			AND valuta = '".$orderRegel['valuta']."' ";

  $db2->SQL($query);
  $db2->Query();
  $pro_step += $pro_multiplier;
  $prb->moveStep($pro_step);

}

$query = "UPDATE Orders SET controle_datum = NOW() WHERE orderid = '$orderid' ";
$db->SQL($query);
$db->Query();

$prb->hide();

?>
<br>
<b>Berekening voltoold.</b>
<?

//echo template($__appvar["templateRefreshFooter"],$content);
?>