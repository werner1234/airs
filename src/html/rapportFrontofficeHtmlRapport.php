<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.3 $

 		$Log: rapportFrontofficeHtmlRapport.php,v $
 		Revision 1.3  2018/09/23 17:14:23  cvs
 		call 7175
 		
*/

//aevertaal: wat doet dit script??

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/selectOptieClass.php");

if(!checkAccess($type))
{
   if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
     $internDepotToegang="OR Portefeuilles.interndepot=1";

   if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	 {
	   $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
	 }
	 else
	 {
    	$join=" INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	                  JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker";
	    $beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
	 }
}
$query = "SELECT layout, CrmClientNaam,Export_data_frontOffice  FROM Vermogensbeheerders
					  JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder =  Vermogensbeheerders.Vermogensbeheerder
					 WHERE VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' LIMIT 1";
$DB = new DB();
$DB->SQL($query);
$DB->Query();
$rdata = $DB->nextRecord();

if($rdata['CrmClientNaam'] == '1')
  $query="SELECT Portefeuilles.Portefeuille, CRM_naw.naam FROM Portefeuilles LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille $join WHERE 1 $beperktToegankelijk";
else
  $query="SELECT Portefeuilles.Portefeuille, concat(Portefeuilles.Client,' - ', Clienten.Naam) as naam FROM (Portefeuilles, Clienten) $join WHERE 1 AND Portefeuilles.Client = Clienten.Client AND Portefeuilles.interndepot=0 AND Portefeuilles.Einddatum >= NOW() $beperktToegankelijk ORDER BY Portefeuilles.Client ASC";

$db=new DB();
$db->SQL($query);
$db->Query();
$portefeuilles=array();
while($data=$db->nextRecord())
{
  $portefeuilles[]=addslashes($data['Portefeuille'].'| '.$data['naam']);
}



  $style ='  <style type="text/css">
    
  </style>

';


$content['javascript'] = '

';




$template_content['style'].=$style;
$editcontent['jsincludes']='

';

$content['jsincludes']=$editcontent['jsincludes'];



echo template($__appvar["templateContentHeader"],$content);




?>
  <fieldset id="Selectie" >
    <legend accesskey="S"><?=vt("Selectie")?></legend>
<?


    $totJul=db2jul($totdatum);
    $totFromDatum=date("d-m-Y",$totJul);

    $jr = substr($totdatum,0,4);
    $maand = substr($totdatum,5,2);
    $kwartaal = ceil(date("m",$totJul) / 3);

    $datumSelctie['beginMaand']=date("d-m-Y",mktime(0,0,0,$maand-1,0,$jr));
    $datumSelctie['eindMaand']=date("d-m-Y",mktime(0,0,0,$maand,0,$jr));
    $datumSelctie['beginKwartaal']=date("d-m-Y",mktime(0,0,0,$kwartaal*3-5,0,$jr));
    $datumSelctie['eindKwartaal']=date("d-m-Y",mktime(0,0,0,$kwartaal*3-2,0,$jr));
    $datumSelctie['beginJaar']=date("d-m-Y",mktime(0,0,0,1,1,$jr-1));
    $datumSelctie['eindJaar']=date("d-m-Y",mktime(0,0,0,13,0,$jr-1));
    $datumSelctie['beginMaand2']=date("d-m-Y",mktime(0,0,0,$maand,0,$jr));
    $datumSelctie['beginKwartaal2']=date("d-m-Y",mktime(0,0,0,$kwartaal*3-2,0,$jr));
    $datumSelctie['beginJaar2']=date("d-m-Y",mktime(0,0,0,1,1,$jr));

    foreach ($datumSelctie as $naam=>$datum)
    {
      if(substr($naam,0,5)=='begin' && substr($datum,0,5)=='31-12')
        $datumSelctie[$naam]="01-01-".((substr($datum,6,4))+1);
    }


    ?>
    <table>
      <tr>
        <td width="100">
          <?=vt("Van datum")?>:
        </td>
        <td>
          <input name="datum_van" class="datepicker" />
        </td>

      </tr>
      <tr>
        <td>
          <?=vt("T/m datum")?>:
        </td>
        <td>
          <input name="datum_van" class="datepicker" />
        </td>

      </tr>

    </table>



  </fieldset>

<?



//if($__debug) {
//	echo getdebuginfo();
//}
echo template($__appvar["templateRefreshFooter"],$content);
?>