<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/11/25 20:22:26 $
 		File Versie					: $Revision: 1.3 $

 		$Log: CRM_rapportageSelectieDetail.php,v $
 		Revision 1.3  2017/11/25 20:22:26  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2017/11/22 17:00:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/11/15 17:11:00  rvv
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


$vermOpties=array();

$layout=13;
$DB=new DB();
$query="SELECT Grootboekrekeningen.Grootboekrekening, Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Onttrekking='1' OR Grootboekrekeningen.Opbrengst = '1' OR  Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1')";
$DB->SQL($query);
$DB->Query();
while($gb=$DB->nextRecord())
{
  $vermOpties['L'.$layout]['MUT']['MUT_'.$gb['Grootboekrekening']]='';
}
$vermOpties['L'.$layout]['PERF']['vvgl']='';
$vermOpties['L'.$layout]['PERF']['perc']='';
$vermOpties['L'.$layout]['PERF']['opbr']='';
$vermOpties['L'.$layout]['PERF']['kost']='';
$vermOpties['L'.$layout]['PERF']['kostPerc']='';
$vermOpties['L'.$layout]['PERFG']['PERFG_totaal']='';
$vermOpties['L'.$layout]['PERFG']['PERFG_perc']='';
$vermOpties['L'.$layout]['SMV']['GB_STORT_ONTTR']='';
$vermOpties['L'.$layout]['SMV']['GB_overige']='';
$vermOpties['L'.$layout]['TRANS']['TRANS_RESULT']='';



//$content = array();
echo template($__appvar["templateContentHeader"],$content);

function maakTabel($frontOfficeData,$portefeuilleData,$clientData,$vermOpties)
{
  global $__appvar,$USR;
  $db = new DB();
  $query="SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder,Vermogensbeheerders.kwartaalCheck, 
Vermogensbeheerders.CrmPortefeuilleInformatie,Vermogensbeheerders.Layout,Vermogensbeheerders.CRM_eigenTemplate,Vermogensbeheerders.check_portaalCrmVink
FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
  $db->SQL($query);
  $gebruikPortefeuilleInformatie = $db->lookupRecord();

  $alleRapporten = array();
 
  foreach($frontOfficeData as $rapport=>$rapData)
    if($rapData['toon']==1)
      $alleRapporten[$rapport]=$__appvar['Rapporten'][$rapport];

  $periodenAll=array('d'=>'Dag','m'=>'Maand','k'=>'Kwartaal','h'=>'Halfjaar','j'=>'Jaar'); //
  $perioden=array();
  foreach($periodenAll as $key=>$value)
  {
    if($_GET['periode']==$key)
      $perioden[$key]=$value;
  }


  foreach ($perioden as $periodeLetter=>$periode)
  {
    foreach($portefeuilleData[$periodeLetter] as $rapport=>$portefeuilles)
    {
      $teller=0;
      $rapportageMatrix[$periodeLetter][$rapport]['header']='<tr><td colspan="40"> <hr></td> </tr>';
      foreach($portefeuilles as $portefeuille=>$rapportData)
      {
        if ($teller % 20 == 0)
        {

          $header='<tr>';
          $header.='<td><b><label title="Portefeuille">'.$rapport.'</label> </b></td>';
          $header.='<td><b><label title="email">eMail</label> </b></td>';
          $header.='<td><b><label title="ww">ww</label> </b></td>';
          $header.='<td><b><label title="Client">Client</label> </b></td>';
          foreach ($vermOpties[$rapport] as $omschrijving=>$default)
            $header.='<td><b><label title="'.$omschrijving.'">'.str_replace($rapport."_",'', $omschrijving).'</label> </b></td>';
          $header.="<tr>\n";
          $rapportageMatrix[$periodeLetter][$rapport]['header' . $teller]= $header;
        }
        $teller++;

        $regel = '<tr>';
        $regel .= '<td><b><label title="Portefeuille">' . $portefeuille . '</label> </b></td>';
        $regel .= '<td><b><label title="email">' . $clientData[$portefeuille]['email'] . '</label> </b></td>';
        $regel .= '<td><b><label title="wachtwoord">' . $clientData[$portefeuille]['wachtwoord'] . '</label> </b></td>';
        $regel .= '<td><b><label title="Client">' . $clientData[$portefeuille]['Client'] . '</label> </b></td>';

        foreach ($vermOpties[$rapport] as $veld=>$default)
        {
          $veldnaam="rap_@_" . $periodeLetter . '_@_' . $portefeuille . "_@_". $rapport . "_@_".$veld;

          if($rapportData[$veld]==1)
            $checked='checked';
          else
            $checked='';

          if($rapport<>'MUT')
            $hidden='<input type="hidden" name="'.$veldnaam.'" value="">';
          else
            $hidden='';

          $regel .= "<td>$hidden <input type='checkbox' $checked name='".$veldnaam."' value='1'></td>";
        }
        $regel .= "<tr>\n";

        $rapportageMatrix[$periodeLetter][$rapport][] = $regel;
      }


    }
  }
  //listarray($frontOfficeData);
  //listarray();
  foreach($rapportageMatrix as $periode=>$rapportData)
  {
    foreach($rapportData as $rapport=>$regels)
    {
      $html[$periode][$rapport] = "<table>";
      foreach ($regels as $portefeuille => $regeldata)
      {
        $html[$periode][$rapport] .= "$regeldata";

      }
      unset($rapportageMatrix[$periode][$rapport]);
      $html[$periode][$rapport] .= "</table>";

    }
  }

 // listarray($html);
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
		           LEFT JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
	    $beperktToegankelijk = " AND CRM_naw.Portefeuille <> '' AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' OR Portefeuilles.Portefeuille IS NULL) ";
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

  //listarray($vermOpties['L'.$layout]);
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $portefeuilles[$data['Portefeuille']]=$data;
  }

  $perioden=array('d','m','k','h','j');
  foreach ($portefeuilles as $portefeuille=>$pdata)
  {
    $tmp=unserialize($pdata['rapportageVinkSelectie']);
    $oldData[$portefeuille]=$tmp;
    //listarray($tmp);
    foreach($perioden as $periode)
    {
      foreach($vermOpties['L'.$layout] as $rapport=>$instellingen)
      {
        if(in_array($rapport,$tmp['rap_'.$periode]))
        {
          $rapportageData[$periode][$rapport][$portefeuille] = $instellingen;
        }
      }
    }


    foreach($perioden as $letter)
    {
      foreach ($tmp['opties'] as $periode=>$periodeData)
      {
        foreach($periodeData as $rapport=>$instellingen)
        {
          if(in_array($rapport,$tmp['rap_'.$periode]))
          {
            foreach ($instellingen as $key => $value)
              $rapportageData[$periode][$rapport][$portefeuille][$key] = $value;
          }
        }
      }
    //$rapportageData[$portefeuille]
      // $rapportageData[$portefeuille][$letter]
    }
    $clientData[$portefeuille]=array('Client'=>$pdata['Client'],'email'=>$pdata['email'],'wachtwoord'=>$pdata['wachtwoord']);
    $rapportTonen['rap_k'][$portefeuille]=1;
  }
//listarray($rapportageData);




if($_POST)
{
  foreach($_POST as $key=>$value)
  {
    if(substr($key,0,4)=='rap_')
    {
      $subkeys=explode('_@_',$key);
      $postData[$subkeys[2]][$subkeys[1]][$subkeys[3]][$subkeys[4]]=$value;
    }
  }

  
  foreach($postData as $portefeuille=>$portefeuilleOpties )
  {

    $nieuweWaarde=$oldData[$portefeuille];
    foreach($portefeuilleOpties as $periode=>$periodeData)
    {
      foreach($periodeData as $rapport=>$waarden)
      {
        $nieuweWaarde['opties'][$periode][$rapport] = $waarden;
        $rapportageData[$periode][$rapport][$portefeuille]=$waarden;
      }
    }
    $nieuwePortefeuilleOpties[$portefeuille]=$nieuweWaarde;


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
    }
    else
    {
      echo "Portefeuille '$portefeuille' niet gevonden.<br>\n";
    }
  }
  echo "<br>\n<br>\n";
}

$html=maakTabel($frontOfficeData,$rapportageData,$clientData,$vermOpties['L'.$layout]);


}


?>
Instellen van rapportages<br>
<br>
<form action="<?=$PHP_SELF?>" method="POST">
<input type="hidden" name="show" value="1">

<?
echo '<input type="submit" value="Opslaan" /> <a href="CRM_nawList.php">Terug naar clientselectie.</a><br><br>';

$perioden=array('d'=>'Dag','m'=>'Maand','k'=>'Kwartaal','h'=>'Halfjaar','j'=>'Jaar');
foreach($perioden as $letter=>$periode)
{
  echo "<br><a href='CRM_rapportageSelectieDetail.php?periode=$letter'><b>$periode</b></a><div id='form_$letter'>";
  foreach($html[$letter] as $htmlData)
    echo $htmlData;
  echo "</div>";
}



?>

</form>
<?
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);
?>