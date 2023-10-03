<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/03/30 07:31:21 $
 		File Versie					: $Revision: 1.15 $

 		$Log: mainmenu_stamgegevens_algemeen.php,v $
 		Revision 1.15  2020/03/30 07:31:21  cvs
 		call 8469
 		
 		Revision 1.14  2020/01/27 10:57:20  cvs
 		update 6-11-2019
 		
 		Revision 1.13  2019/12/11 10:57:16  cvs
 		call 7606
 		
 		Revision 1.12  2019/11/06 07:23:20  cvs
 		update 6-11-2019
 		
 		Revision 1.11  2019/10/09 09:54:09  cvs
 		call 8025
 		
 		Revision 1.10  2019/10/04 07:43:07  cvs
 		call 7598
 		
 		Revision 1.9  2019/04/18 08:03:26  cvs
 		abnv2
 		

 * 
 */

$mnu->addItem("Transactiecodes","AAB transaktiescodes","url=aabtransaktiecodesList.php");
$mnu->addItem("Transactiecodes","AAB v2 transaktiescodes","url=abnV2transactiecodesList.php");
$mnu->addItem("Transactiecodes","AABBE transaktiescodes","url=AABBEtransactiecodesList.php");
$mnu->addItem("Transactiecodes","BIL transaktiescodes","url=biltransactiecodesList.php");
$mnu->addItem("Transactiecodes","BNPBGL transaktiescodes","url=bnpbglTransactiecodesList.php");
$mnu->addItem("Transactiecodes","BTC transaktiescodes","url=btcTransactiecodesList.php");
$mnu->addItem("Transactiecodes","CACEIS transaktiescodes","url=caceisTransactiecodesList.php");
$mnu->addItem("Transactiecodes","CAW transaktiescodes","url=cawTransactiecodesList.php");
$mnu->addItem("Transactiecodes","DeGiro transaktiescodes","url=degirotransactiecodesList.php");
$mnu->addItem("Transactiecodes","DIL transaktiescodes","url=dilTransactiecodesList.php");
$mnu->addItem("Transactiecodes","FVL transaktiescodes","url=lanschottransactiecodesList.php");
$mnu->addItem("Transactiecodes","GS transaktiescodes","url=gsTransactiecodesList.php");
$mnu->addItem("Transactiecodes","HSBC transaktiescodes","url=hsbcTransactiecodesList.php");
$mnu->addItem("Transactiecodes","ING transaktiescodes","url=ingtransactiecodesList.php");
$mnu->addItem("Transactiecodes","IB transaktiescodes","url=ibTransactiecodesList.php");
$mnu->addItem("Transactiecodes","JB transaktiescodes","url=jbtransactiecodesList.php");
$mnu->addItem("Transactiecodes","JBlux transaktiescodes","url=jbluxtransactiecodesList.php");
$mnu->addItem("Transactiecodes","JP morgan transaktiescodes","url=jpmtransactiecodesList.php");
$mnu->addItem("Transactiecodes","Kasbank transaktiescodes","url=kasbanktransactiecodesList.php");
$mnu->addItem("Transactiecodes","KBC transaktiescodes","url=kbctransactiecodesList.php");
$mnu->addItem("Transactiecodes","KNOX transaktiescodes","url=knoxTransactiecodesList.php");
$mnu->addItem("Transactiecodes","Lombard transaktiescodes","url=lombardtransactiecodesList.php");
$mnu->addItem("Transactiecodes","LYNX transaktiescodes","url=lynxtransactiecodesList.php");
$mnu->addItem("Transactiecodes","ModuleZ transaktiescodes","url=moduleztransactiecodesList.php");
$mnu->addItem("Transactiecodes","Optimix transaktiescodes","url=opttransactiecodesList.php");
$mnu->addItem("Transactiecodes","Pictet transaktiescodes","url=pictettransactiecodesList.php");
$mnu->addItem("Transactiecodes","Quintet transaktiescodes","url=quintetTransactiecodesList.php");
$mnu->addItem("Transactiecodes","Rabo transaktiescodes","url=rabotransactiecodesList.php");
$mnu->addItem("Transactiecodes","Sarasin transaktiescodes","url=sarTransactiecodesList.php");
$mnu->addItem("Transactiecodes","SAXO transaktiescodes","url=saxoTransactiecodesList.php");
$mnu->addItem("Transactiecodes","SOC transaktiescodes","url=socgenTransactiecodesList.php");
$mnu->addItem("Transactiecodes","UBP transaktiescodes","url=ubptransactiecodesList.php");
$mnu->addItem("Transactiecodes","UBSLUX transaktiescodes","url=ubsluxtransactiecodesList.php");
$mnu->addItem("Transactiecodes","VLCH transaktiescodes","url=vlchTransactiecodesList.php");
$mnu->addItem("Transactiecodes","VP transaktiescodes","url=vpTransactiecodesList.php");

if ($USR == "JBR"  OR $USR == "FEGT" OR  $USR == "AIRS")
{
  $mnu->addItem("Transactiecodes");
  $mnu->addItem("Transactiecodes","importAfwijkingen","url=importafwijkingenList.php");
  $mnu->addItem("Transactiecodes","invulinstructies","url=invulinstructiesList.php");
}

$mnu->addItem("Transactiecodes");
$mnu->addItem("Transactiecodes","import Zoek/Vervang","url=importzoekvervangList.php");

