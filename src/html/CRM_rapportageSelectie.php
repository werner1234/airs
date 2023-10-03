<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/08 15:38:46 $
 		File Versie					: $Revision: 1.16 $

 		$Log: CRM_rapportageSelectie.php,v $
 		Revision 1.16  2020/07/08 15:38:46  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2018/04/14 17:21:13  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2017/11/30 16:29:56  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/11/30 16:08:09  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/11/29 16:16:06  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2017/11/25 20:22:26  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2017/11/23 07:34:33  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/11/27 11:07:26  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/11/10 16:01:52  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/01/10 14:09:26  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2015/10/26 14:33:05  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/10/25 13:08:50  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/10/21 16:13:47  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/02/07 20:32:39  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/04/23 16:17:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/08/04 10:47:37  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/06/06 14:10:12  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2008/05/16 08:04:51  rvv
 		*** empty log message ***

*/
include_once("wwwvars.php");
// set module naam voor autenticatie leeg = iedereen.
$__appvar["module"] = "";
$_SESSION['submenu'] = "";
$_SESSION['NAV'] = "";

//$content = array();
echo template($__appvar["templateContentHeader"],$content);

function maakTabel($frontOfficeData,$portefeuilleData,$clientData)
{
  global $__appvar,$USR;
  $db = new DB();
  $query="SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder,Vermogensbeheerders.kwartaalCheck, 
Vermogensbeheerders.CrmPortefeuilleInformatie,Vermogensbeheerders.Layout,Vermogensbeheerders.CRM_eigenTemplate,Vermogensbeheerders.check_portaalCrmVink
FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
  $db->SQL($query);
  $gebruikPortefeuilleInformatie = $db->lookupRecord();

  $alleRapporten = array();
  $headerRapport=array();
  foreach($frontOfficeData as $rapport=>$rapData)
    if($rapData['toon']==1)
    {
      $alleRapporten[$rapport] = $__appvar['Rapporten'][$rapport];
      if($rapData['shortName']<>'')
      {
        $headerRapport[$rapport]['shortName'] = $rapData['shortName'];
        $headerRapport[$rapport]['longName'] = $rapData['longName'];
      }
      else
      {
        $headerRapport[$rapport]['shortName'] = $rapport;
        $headerRapport[$rapport]['longName'] = $__appvar['Rapporten'][$rapport];
      }
    }
/*
  foreach($frontOfficeData as $rapport=>$rapData)
    if($rapData['toon']==0)
      $alleRapporten[$rapport]=$__appvar['Rapporten'][$rapport];
*/
 // if($__appvar['bedrijf']=='RCN')
    $periodenAll=array('d'=>'Dag','m'=>'Maand','k'=>'Kwartaal','h'=>'Halfjaar','j'=>'Jaar'); //
  $perioden=array();
   foreach($periodenAll as $key=>$value)
   {
     if($_GET['periode']==$key)
       $perioden[$key]=$value;
   }
 // else
  //  $perioden=array('d'=>'Dag','m'=>'Maand','k'=>'Kwartaal'); //
  
  if($gebruikPortefeuilleInformatie['check_portaalCrmVink']==1)
    $verzendTypen=array('email'=>'eMail','papier'=>'Papier','portaal'=>'Portaal','geen'=>'Geen');
  else
    $verzendTypen=array('email'=>'eMail','papier'=>'Papier','geen'=>'Geen');  
  
  $checkClick='onclick="this.cssText=\'background-color:#b0c4de;\'"';

  foreach ($perioden as $periodeLetter=>$periode)
  {
    $header='<tr>';
    $header.='<td><b><label title="Portefeuille">Portefeuille</label> </b></td>';
    $header.='<td><b><label title="email">eMail</label> </b></td>';
    $header.='<td><b><label title="ww">ww</label> </b></td>';
    $header.='<td><b><label title="Client">Client</label> </b></td>';
    foreach ($verzendTypen as $type=>$omschrijving)
      $header.='<td><b><label title="'.$omschrijving.'">'.$omschrijving.'</label> </b></td>';
    $header.='<td><b><label title="Aantal">Aantal</label> </b></td>'; 
      
    foreach ($alleRapporten as $rapport=>$omschrijving)
      $header.='<td><b><label title="'.$rapport.' - '.$headerRapport[$rapport]['longName'].'">'.$headerRapport[$rapport]['shortName'].'</label> </b></td>';
    $header.="<tr>\n";
    
    //$rapportageMatrix[$periodeLetter]['header']=$header;
    
    $teller=0;
    foreach($portefeuilleData as $portefeuille=>$pdata)
    {
      if($teller%20==0)
       $rapportageMatrix[$periodeLetter]['header'.$teller]=$header;
      $teller++;

      $regel='<tr>';
      $regel.='<td><b><label title="Portefeuille">'.$portefeuille.'</label> </b></td>';
      $regel.='<td><b><label title="email">'.$clientData[$portefeuille]['email'].'</label> </b></td>';
      $regel.='<td><b><label title="wachtwoord">'.$clientData[$portefeuille]['wachtwoord'].'</label> </b></td>';
      $regel.='<td><b><label title="Client">'.$clientData[$portefeuille]['Client'].'</label> </b></td>';
      foreach ($verzendTypen as $type=>$omschrijving)
      {
        if($periodeLetter=='d' && $type <>'portaal')
          $regel.="<td> &nbsp;</td>";
        else
        {
          if($pdata['verzending']['rap_'.$periodeLetter][$type] == 1)
            $checked='checked';
          else
            $checked='';
           $regel.="<td> <input type='checkbox' $checked name='rap_@_verzend_@_".$periodeLetter.'_@_'.$portefeuille."[]' value='".$type."'></td>";
        }
      }
      if($periodeLetter=='d')
        $regel.="<td> &nbsp;</td>";
      else
        $regel.="<td><input type='text' name='rap_@_aantal_@_".$periodeLetter.'_@_'.$portefeuille."' size='2' value='".$pdata['aantal'][$periodeLetter]."'></td>";
      foreach ($alleRapporten as $rapport=>$omschrijving)
      {
        if(in_array($rapport,$pdata['rap_'.$periodeLetter]))
            $checked='checked';
        else
           $checked='';
        $regel.="<td> <input type='checkbox' $checkClick $checked name='rap_@_rapport_@_".$periodeLetter.'_@_'.$portefeuille."[]' value='$rapport'></td>";
      }
      $regel.="<tr>\n";
 
      $rapportageMatrix[$periodeLetter][$portefeuille]=$regel;



    }
  }
  
  foreach($rapportageMatrix as $periode=>$regels)
  {
    $html[$periode] = "<table>";
    foreach($regels as $portefeuille=>$regeldata)
    {
       $html[$periode] .= "$regeldata";
    }
    $html[$periode] .= "</table>";
    unset($rapportageMatrix[$periode][$portefeuille]);
  }
  
  return $html;
}



$db=new DB();
$query = "SELECT Gebruikers.id, Gebruikers.CRMlevel, max(Vermogensbeheerders.CrmTerugRapportage) as CrmTerugRapportage, max(Vermogensbeheerders.CrmAutomatischVerzenden) as CrmAutomatischVerzenden, Export_data_frontOffice FROM Gebruikers
JOIN VermogensbeheerdersPerGebruiker ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker
JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
WHERE
VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' GROUP BY Gebruikers.id ";
$db->SQL($query);
$gebruikersData = $db->lookupRecord();
$frontOfficeData=unserialize($gebruikersData['Export_data_frontOffice']);



if($_SESSION['lastListQuery'])
{
    if(checkAccess('portefeuille'))
		{
			$join = "";
			$beperktToegankelijk = "";
		}
		else
		{
			$join = "LEFT JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
		           LEFT JOIN Gebruikers as GebruikersRechten ON GebruikersRechten.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker
		           LEFT JOIN laatstePortefeuilleWaarde as laatstePortefeuilleWaarde ON CRM_naw.portefeuille = laatstePortefeuilleWaarde.portefeuille ";
	    $beperktToegankelijk = " AND CRM_naw.Portefeuille <> '' AND (Portefeuilles.beperktToegankelijk = '0' OR  GebruikersRechten.beperkingOpheffen = '1' OR Portefeuilles.Portefeuille IS NULL) ";
		}

  if(strpos($_SESSION['lastListQuery'],'ORDER BY') > 0)
    $query=substr($_SESSION['lastListQuery'],0,strpos($_SESSION['lastListQuery'],"ORDER BY"));
  else
    $query=substr($_SESSION['lastListQuery'],0,strpos($_SESSION['lastListQuery'],"LIMIT"));


$query="SELECT CRM_naw.Portefeuille,CRM_naw.rapportageVinkSelectie,CRM_naw.naam,CRM_naw.VerzendPaAanhef,CRM_naw.zoekveld,CRM_naw.memo,
  naam1,verzendAdres,verzendPc,verzendPlaats,verzendLand,
  CRM_naw.maandrapportage, CRM_naw.kwartaalrapportage, CRM_naw.halfjaarrapportage, CRM_naw.jaarrapportage,
   if(Portefeuilles.Client is NULL,CRM_naw.zoekveld, Portefeuilles.Client) as Client, if(length(CRM_naw.email)>2,'X','') as email,if(length(CRM_naw.wachtwoord)>5,'X','') as wachtwoord
  FROM CRM_naw 
  LEFT JOIN Portefeuilles ON CRM_naw.Portefeuille=Portefeuilles.Portefeuille 
  LEFT JOIN Vermogensbeheerders on Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder  $join
  ".substr($query,strpos($query,"WHERE"))." $beperktToegankelijk 
  ORDER BY CRM_naw.zoekveld";

  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $portefeuilles[$data['Portefeuille']]=$data;
  }
  foreach ($portefeuilles as $portefeuille=>$pdata)
  {
    $rapportageData[$portefeuille]=unserialize($pdata['rapportageVinkSelectie']);
    $clientData[$portefeuille]=array('Client'=>$pdata['Client'],'email'=>$pdata['email'],'wachtwoord'=>$pdata['wachtwoord']);
   // $rapportTonen['rap_k'][$portefeuille]=1;
  }





if($_POST)
{
  foreach($_POST as $key=>$value)
  {
    if(substr($key,0,4)=='rap_')
    {
      $subkeys=explode('_@_',$key);
      $postData[$subkeys[3]][$subkeys[2]][$subkeys[1]]=$value;
    }
  }

  foreach($postData as $portefeuille=>$portefeuilleOpties )
  {
    if(!isset($rapportageData[$portefeuille]))
    {
      echo "Geen beginwaarden voor $portefeuille gevonden.";
      continue;
    }
    $vinkOpties=$rapportageData[$portefeuille];
    /*
     $vinkOpties=array('rap_d'=>array(),//$rapportageData[$porefeuille]['rap_d'],
                       'rap_m'=>array(),//$rapportageData[$porefeuille]['rap_m'],
                       'rap_k'=>array(),//$rapportageData[$porefeuille]['rap_k'],
                       'rap_h'=>array(),//$rapportageData[$porefeuille]['rap_h'],
                       'rap_j'=>array(),//$rapportageData[$porefeuille]['rap_j'],
                       'verzending'=>array('rap_d'=>array(),
                                           'rap_m'=>array(),
                                           'rap_k'=>array(),
                                           'rap_h'=>array(),//$rapportageData[$porefeuille]['verzending']['rap_h'],
                                           'rap_j'=>array()),//$rapportageData[$porefeuille]['verzending']['rap_j']),
                       'aantal'=>array('m'=>'',
                                       'k'=>'',
                                       'h'=>'',//$rapportageData[$porefeuille]['aantal']['h'],
                                       'j'=>''),//$rapportageData[$porefeuille]['aantal']['j']),
                       'opties'=>$rapportageData[$porefeuille]['opties']);
    */
     foreach($portefeuilleOpties as $periode=>$periodeData)
     {
       $vinkOpties['rap_'.$periode]=array();
       $vinkOpties['verzending']['rap_'.$periode]=array();
       $vinkOpties['aantal'][$periode]='';

        foreach($periodeData as $optie=>$waarde)
        {
          if($optie=='rapport')
            $vinkOpties['rap_'.$periode]=$waarde;
          if($optie=='verzend')
          {
            foreach($waarde as $type)
              $vinkOpties['verzending']['rap_'.$periode][$type]=1;      
          }
          if($optie=='aantal')
            $vinkOpties['aantal'][$periode]=$waarde; 
        }
        
     }
     $nieuwePortefeuilleOpties[$portefeuille]=$vinkOpties;
  }
  
  // listarray($rapportageData['702258']) ;
 //  echo "<br>|<br>";
 // listarray($nieuwePortefeuilleOpties['702258']);
  
  foreach($nieuwePortefeuilleOpties as $portefeuille=>$vinkOpties)
  {
    $query="SELECT id,Portefeuille FROM CRM_naw WHERE Portefeuille='$portefeuille'";
    if($db->QRecords($query)==1)
    {
      $idData=$db->nextRecord();
      $query="UPDATE CRM_naw SET rapportageVinkSelectie='".mysql_real_escape_string(serialize($vinkOpties))."' WHERE id='".$idData['id']."'";
      $db->SQL($query);
      if($db->Query())
      {
        echo "Instellingen voor '$portefeuille' aangepast.<br>\n";
      }
      $rapportageData[$portefeuille]=$vinkOpties;
    }
    else
    {
      echo "Portefeuille '$portefeuille' niet gevonden.<br>\n";
    }
  }
  echo "<br>\n<br>\n";
}

$html=maakTabel($frontOfficeData,$rapportageData,$clientData);


}


?>
Instellen van rapportages<br>
<br>
<form action="<?=$PHP_SELF?>" method="POST">
<input type="hidden" name="show" value="1">

<?
echo '<input type="submit" value="Opslaan" />';
if(GetModuleAccess('alleenNAW') == 1)
  echo '<a href="CRM_nawOnlyList.php">Terug naar clientselectie.</a><br><br>';
else
  echo '<a href="CRM_nawList.php">Terug naar clientselectie.</a><br><br>';

$perioden=array('d'=>'Dag','m'=>'Maand','k'=>'Kwartaal','h'=>'Halfjaar','j'=>'Jaar');
foreach($perioden as $letter=>$periode)
{
  echo "<br><a href='CRM_rapportageSelectie.php?periode=$letter'><b>$periode</b></a><div id='form_$letter'>";
  echo $html[$letter];
  echo "</div>";
}


?>

</form>
<?
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);
?>