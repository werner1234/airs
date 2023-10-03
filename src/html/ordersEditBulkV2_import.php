<?php

/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/06/06 15:46:39 $
    File Versie         : $Revision: 1.3 $

    $Log: ordersEditBulkV2_import.php,v $
    Revision 1.3  2020/06/06 15:46:39  rvv
    *** empty log message ***

    Revision 1.2  2020/02/26 16:10:59  rvv
    *** empty log message ***

    Revision 1.1  2020/02/12 16:41:25  rvv
    *** empty log message ***



*/

include_once("wwwvars.php");
include_once("../classes/AE_cls_FIXtransport.php");
include_once('orderControlleRekenClassV2.php');
include_once('../config/ordersVars.php');

if($_POST['import']=='true')
{
  
  function getalCheck($getal)
  {
    $melding='';
    if(substr_count($getal,'.')>1)
    {
      $melding="Ongeldig getal, meer dan één . gevonden in $getal";
    }
    if(substr_count($getal,',')>1)
    {
      $melding="Ongeldig getal, meer  dan één , gevonden in $getal";
    }
    if(substr_count($getal,'.')>=1 && substr_count($getal,',')>=1)
    {
      $melding="Ongeldig getal, meer dan één . of , gevonden in $getal";
    }
    
    $fixedGetal =str_replace(",",'.',$getal);
    
    return array($fixedGetal,$melding);
  }
  
  $fix=new AE_FIXtransport();
  if (!$handle = @fopen($_FILES['importfile']['tmp_name'], "r"))
  {
    echo "FOUT bestand is niet leesbaar";exit;
  }
  $csvData=array();
  $i=0;
  while ($data = fgetcsv($handle, 10000, ";"))
  {
    if($i==0)
    {
      $header = $data;
      $headerLookup=array();
      foreach ($header as $index=>$veld)
        $headerLookup[$index]=trim($veld);
    }
    else
    {
      $csvData[] = $data;
    }
    $i++;
  
  }
  $headerVelden=array('Portefeuille','ISIN','Fondsvaluta','Transactiesoort','Aantal','Limietkoers');//,'Bedrag','Optie');
  $transactieSoorten=$__ORDERvar["transactieSoort"] ;

  $afbreken=false;
  foreach($headerVelden as $headerVeld)
  {
    if(!in_array($headerVeld,$headerLookup))
    {
      echo "Afwijking in verwachte velden. Veld $headerVeld niet gevonden.<br>\n";
      $afbreken=true;
    }
  }
  if($afbreken==true)
    exit;
  $db=new DB();
  foreach($csvData as $dataRegel)
  {
    $data=array();
    foreach ($headerLookup as $index=>$veld)
      $data[$veld]=trim($dataRegel[$index]);

    $query="SELECT accountmanager,client,portefeuille,depotbank FROM Portefeuilles where Portefeuille='".mysql_real_escape_string($data['Portefeuille'])."' AND einddatum>now() AND consolidatie=0";
    $db->SQL($query);
    $db->Query();
    $aantal=$db->records();
    $portefeuille=$db->nextRecord();
    if($aantal <> 1)
    {
      echo "Portefeuille ".$data['Portefeuille']." niet gevonden. Aantal=$aantal <br>\n";
      continue;
    }
  
    $data['Bedrag']=str_replace(",",'.',$data['Bedrag']);
    $bedrag=round($data['Bedrag'],2);
    if(round($data['Aantal'],4)<>0.0 && $bedrag <> 0.0)
    {
      echo "Zowel Aantal als Bedrag zijn gevuld, record overgeslagen.<br>\n";
      continue;
    }
    
    if($data['ISIN']<>'' && $data['Optie']<>'')
    {
      echo "Zowel ISIN als Optie zijn gevuld, record overgeslagen.<br>\n";
      continue;
    }
    if($data['Optie']<>'')
      $fondsenWhere="Fondsen.Fonds='".mysql_real_escape_string($data['Optie'])."'";
    else
      $fondsenWhere="ISINCode ='".mysql_real_escape_string($data['ISIN'])."' AND Valuta='".mysql_real_escape_string($data['Fondsvaluta'])."'";
    $query="SELECT fonds,ISINCode,Omschrijving as fondsOmschrijving,beurs,fondssoort,fondseenheid,valuta as fondsValuta,orderinlegInBedrag FROM Fondsen WHERE $fondsenWhere ";
    $db->SQL($query);
    $db->Query();
    $aantal=$db->records();
    $fonds=$db->nextRecord();
    if($aantal == 0)
    {
      echo "Fonds ".($data['ISIN']<>''?$data['ISIN']:$data['Optie'])." niet gevonden. Aantal=$aantal <br>\n";
      continue;
    }
    elseif($aantal > 1)
    {
      echo "Meerdere fondsen met ".$data['ISIN']."/".$data['Fondsvaluta']."/".$data['Optie']." gevonden. Aantal=$aantal . Fonds ".$fonds['fonds']." wordt gebruikt. <br>\n";
    }
    else
    {
      if($bedrag<>0)
      {
        if($fonds['orderinlegInBedrag']==0)
        {
          echo "Fonds " . $fonds['fonds'] . " kan niet via een bedrag worden ingelegd. <br>\n";
          continue;
        }
        elseif($data['Transactiesoort']!='A')
        {
          echo "Transactiesoort ".$data['Transactiesoort']." niet toegegstaand bij inleg via een bedrag. <br>\n";
          continue;
        }
      }
    }

    if(!isset($transactieSoorten[$data['Transactiesoort']]))
    {
      echo "Onbekende transactiesoort ".$data['Transactiesoort'].". (Mogelijke codes: ".implode(",",array_keys($transactieSoorten)).")<br>\n";
      continue;
    }
    elseif($fonds['fondssoort']=='OPT')
    {
      $optTransactieSoorten=array('AS','AO','VO','VS');
      if(!in_array($data['Transactiesoort'],$optTransactieSoorten))
      {
        echo "Voor een optie is transactietype ".$data['Transactiesoort']." niet toegestaan. (Mogelijke codes: ".implode(",",$optTransactieSoorten).")<br>\n";
        continue;
      }
    }
  
    $query="SELECT MAX(regelNr) as regelNr, max(depotbank) as depotbank FROM TijdelijkeBulkOrdersV2 WHERE bron='bulkInvoer' AND change_user='$USR' ";//AND pagina='$selectedPagina'
    $db->SQL($query);//echo $query;exit;
    $regelNr=$db->lookupRecord();
    $fonds['regelNr']=$regelNr['regelNr']+1;
    
    $aantal=str_replace(",",'.',$data['Aantal']);
    $koers =str_replace(",",'.',$data['Limietkoers']);
  
    $tmp=getalCheck($data['Aantal']);
    if($tmp[1]<>'')
    {
      echo $tmp[1]." Regel wordt niet geimporteerd.<br>\n";
      continue;
    }
    else
    {
      $aantal = $tmp[0];
    }
    
    $tmp=getalCheck($data['Limietkoers']);
    if($tmp[1]<>'')
    {
      echo $tmp[1]." Regel wordt niet geimporteerd.<br>\n";
      continue;
    }
    else
    {
      $koers = $tmp[0];
    }
    $check=new orderControlleBerekeningV2();
  
    $q="SELECT
        Portefeuilles.Portefeuille,
        SUM(Rekeningmutaties.aantal) as aantal,
        max(if(Rekeningmutaties.Bewaarder='',Rekeningen.Depotbank,
        if(Rekeningmutaties.Bewaarder is null,Rekeningen.Depotbank,Rekeningmutaties.Bewaarder))) as Depotbank
        FROM
        Portefeuilles
        INNER JOIN Clienten ON Portefeuilles.Client = Clienten.Client
        INNER JOIN Rekeningen ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille AND Rekeningen.inactief=0
        LEFT JOIN Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening AND  year(Rekeningmutaties.Boekdatum)='".date('Y')."' AND
        Rekeningmutaties.Fonds='".mysql_real_escape_string($fonds['fonds'])."' AND Grootboekrekening='FONDS'
        WHERE Portefeuilles.Portefeuille = '".trim($portefeuille['portefeuille'])."'
         GROUP BY Bewaarder,Rekeningen.Depotbank
         having aantal <> 0
        ORDER BY aantal desc";
    $db->SQL($q);
    $db->Query();
    $mutDepot='';
    while($rekData 	= $db->nextRecord())
    {
      if ($rekData['Depotbank'] <> '')
      {
        $mutDepot = $rekData['Depotbank'];
        $aantalInPositie=$rekData['aantal'];
      }
      if($mutDepot<>'')
        break;
    }
    if($mutDepot<>'')
    {
      $depot = $mutDepot;
    }
    else
    {
      $depot = '';
      $q="SELECT SUM(Rekeningmutaties.aantal) as aantal FROM Rekeningmutaties
        INNER JOIN Rekeningen ON Rekeningen.Rekening = Rekeningmutaties.Rekening AND Rekeningen.inactief=0
        WHERE year(Rekeningmutaties.Boekdatum)='".date('Y')."' AND Rekeningmutaties.Fonds='".mysql_real_escape_string($fonds['fonds'])."' AND Grootboekrekening='FONDS'
        AND Rekeningen.Portefeuille = '".trim($portefeuille['portefeuille'])."'";
      $db->SQL($q);
      $db->Query();
      $aantalAanwezig	= $db->nextRecord();
      $aantalInPositie=$aantalAanwezig['aantal'];
    }
  
    if(substr($data['Transactiesoort'],0,1)=='V')
      $nieuwAantal=$aantalInPositie-$data['Aantal'];
    else
      $nieuwAantal=$aantalInPositie+$data['Aantal'];
    
    $velden=$check->getPortefeuilleOpties($portefeuille['portefeuille'],$fonds['fonds'],$depot);
    $portefeuille['rekening']=$velden['Rekening'];
    $portefeuille['depotbank']=$velden['Depotbank'];
    $portefeuille['accountmanager']=$velden['accountmanager'];

    $fondsBankcode=$fix->getFondscode($portefeuille["depotbank"],$fonds['fonds']);
    $fonds['beurs']=$fix->getBeurs($portefeuille["depotbank"],$fonds['fonds']);
    $fonds['fondsValuta']=$fix->getFondsValuta($portefeuille["depotbank"],$fonds['fonds']);
    
    unset($fonds['orderinlegInBedrag']);
    $insertQuery="INSERT INTO TijdelijkeBulkOrdersV2 SET ";
    foreach($portefeuille as $portefeuilleVeld=>$waarde)
       $insertQuery.="$portefeuilleVeld='".mysql_real_escape_string($waarde)."',";
    foreach($fonds as $fondsVeld=>$waarde)
      $insertQuery.="$fondsVeld='".mysql_real_escape_string($waarde)."',";
    $insertQuery.="fondsBankcode='".mysql_real_escape_string($fondsBankcode)."',
      transactieSoort='".mysql_real_escape_string($data['Transactiesoort'])."',
      aantalInPositie='".mysql_real_escape_string($aantalInPositie)."',
      nieuwAantal='".mysql_real_escape_string($nieuwAantal)."',
      aantal='".mysql_real_escape_string($aantal)."',
      bedrag='".mysql_real_escape_string($bedrag)."',
      koersLimiet='".mysql_real_escape_string($koers)."',
      add_date=now(),change_date=now(),add_user='$USR',change_user='$USR',bron='bulkInvoer'";
//listarray($insertQuery);
    $db->SQL($insertQuery);
    if($db->Query())
    {
      echo "Order regel toegevoegd: Portefeuille=".$portefeuille['portefeuille'].", Fonds=".$fonds['fonds'].", ".($aantal<>0?"Aantal=$aantal":"Bedrag=$bedrag")."<br>\n";
    }

  }
  echo "<button onclick=\"window.location.href = 'ordersEditBulkV2.php';\">Terug naar invoer</button>";
  exit;

  
}


elseif($action=='new')
{
  $_SESSION['submenu'] = New Submenu();
  $_SESSION['submenu']->addItem('Import from file','ordersEditBulkV2_import.php?action=select');
}
elseif($action=='select')
{
  echo template($__appvar["templateContentHeader"],$content);
  ?>
  
  <form enctype="multipart/form-data" action="ordersEditBulkV2_import.php" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="16777216" />
    <input type="hidden" name="import" value="true" />
    <input type="hidden" name="action" value="new" />
    <b>Importeren uit bestand</b><br><br>
    
    
    <div class="form">
      <div class="formblock">
        <div class="formlinks"> </div>
        <div class="formrechts">
          <input type="file" name="importfile" size="50">
        </div>
      </div>
      
      <div class="formblock">
        <div class="formlinks"> &nbsp;</div>
        <div class="formrechts">
          <input type="submit" value="importeren">
        </div>
      </div>
  
  </form>
  <?
  echo template($__appvar["templateRefreshFooter"],$content);
  exit;
}


?>