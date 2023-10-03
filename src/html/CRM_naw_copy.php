<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/02/13 17:04:56 $
 		File Versie					: $Revision: 1.10 $
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_digidoc.php");

if($__appvar["crmOnly"] && $_POST['naarAIRS'])
  $doelDatabaseId=6;
else
  $doelDatabaseId=1;

$data=array_merge($_GET,$_POST);
$fout=false;
$deb_id=$data['deb_id'];

$DB=new DB();
$DB->SQL("SELECT * FROM CRM_naw WHERE id ='".$data['deb_id']."'");
$nawRec = $DB->lookupRecord();
unset($nawRec['portefeuille']);

$categorienPerTab=array('Algemeen'=>'Algemeen','Adres'=>'Algemeen','Verzendadres'=>'Algemeen','Telefoon'=>'Algemeen','Internetgegevens'=>'Algemeen','Bedrijfinfo'=>'Algemeen',
'Persoonsinfo'=>'Persoon','Legitimatie'=>'Persoon','Informatie partner'=>'Persoon','Legitimatie partner'=>'Persoon','geen'=>'Persoon','Contract'=>'Contract','Recordinfo'=>'Recordinfo',
'Adviseurs'=>'Adviseurs','Extra algemeen'=>'Contact','Beleggen'=>'Beleggen','Profiel'=>'Profiel','Rapportage'=>'Rapportage','Relatie geschenk'=>'Relatie');
$checkedTabs=array('Algemeen','Persoon','Adviseurs','Memo');
$categorien=array();
$velden=array();
$naw=new Naw();

foreach ($naw->data['fields'] as $field=>$fieldData)
{
  if($fieldData['categorie'] != '' && $fieldData['categorie'] <> 'Recordinfo')
  {
    $categorien[$fieldData['categorie']]=$fieldData['categorie'];
    $veldenPerCategorie[$fieldData['categorie']][]=$field;
    if($categorienPerTab[$fieldData['categorie']]=='')
      $categorienPerTab[$fieldData['categorie']]='Niet gekoppeld';

    $veldenPerTab[$categorienPerTab[$fieldData['categorie']]][$field]=$field;
  }
}
$veldenPerTab['Memo']['memo']='memo';
unset($veldenPerTab['Algemeen']['memo']);

$doelVelden=array();
if(isset($_POST['deb_id']))
{

  //if($__appvar["crmOnly"] && $_POST['naarAIRS'] == 1)
  //{
    $DBA=new DB($doelDatabaseId);
    $query="DESC CRM_naw";
    $DBA->SQL($query);
    $DBA->Query();
    while($doelData=$DBA->nextRecord())
      $doelVelden[]=$doelData['Field'];
  //}

  if(count($_POST['tabs']) > 0)
  {
    foreach ($_POST['tabs'] as $tab=>$value)
    {
      foreach ($veldenPerTab[$tab] as $veld)
      {
        if(count($doelVelden) > 0 && !in_array($veld,$doelVelden))
        {
          echo "" . vt('Veld') . " " . $veld . " " . vt('niet in doel database') . ". " . vt('Veld') . " " . $veld . " " . vt('overgeslagen') . ".<br>\n";
          $fout=true;
        }
        else
          $insertFields .= " $veld = '".addslashes($nawRec[$veld])."',";
      }
    }
    $insertFields .=" aktief='1', add_date=NOW(), change_date=NOW(), add_user='$USR', change_user='$USR'";

    //if($__appvar["crmOnly"] && $_POST['naarAIRS'] == 1)
    //{

      $query="INSERT INTO CRM_naw SET $insertFields ";
      //echo $query."<br>\n";
      $DBA->SQL($query);
      if(!$DBA->Query())
      {
         echo "" . vt('Opslaan van CRM_naw in doel database mislukt.') . "<br>\n";
         $fout=true;
      }
      $recId=$DBA->last_id();

      if(isset($_POST['tabellen']))
      {
        foreach ($_POST['tabellen'] as $tabel)
        {
          echo "" . vt('Kopieeren van records uit') . " $tabel <br>\n";
          $query="SELECT * FROM $tabel WHERE rel_id='".$deb_id."'";
          //echo "$query <br>\n";
          $DB=new DB();
          $DB->SQL($query);
          $DB->Query();
          while($velden=$DB->nextRecord())
          { 
            echo "Tabel $tabel record ".$velden['id']." ophalen.<br>\n";
            $unsetFields=array('id','change_date','change_user','rel_id');
            foreach ($unsetFields as $veld)
              unset($velden[$veld]);

            $insertFields='';
            foreach ($velden as $veld=>$waarde)
               $insertFields .= " $veld = '".addslashes($waarde)."',";
            $insertFields .="rel_id='$recId', change_date=NOW(), change_user='$USR'";

            $query="INSERT INTO $tabel SET $insertFields ";

           // echo "$query <br>\n";
            $DBA=new DB($doelDatabaseId);
            $DBA->SQL($query);
            if(!$DBA->Query())
            {
               echo "Opslaan record uit $tabel in doel database mislukt.<br>\n";
               $fout=true;
            }
            else
              echo "" . vt('Record opgeslagen in AIRS database') . " $tabel <br>\n";
          }
        }
      }
      if(isset($_POST['documenten']) && $_POST['documenten'] == 1)
      {
        $dd_referenceData=array();
        $DB=new DB();
        $query = "SELECT * FROM dd_reference WHERE module='CRM_naw'  AND module_id='".$_POST['deb_id']."'";
        $DB->SQL($query);
        $DB->Query();
        while($data=$DB->nextRecord())
          $dd_referenceData[]=$data;
        foreach($dd_referenceData as $refData)
        {
          $DB=new DB();
          $query = "SELECT * FROM ".$refData['datastore']." WHERE referenceId='".$refData['id']."'";
          $DB->SQL($query);  
          $document=$DB->lookupRecord();
          foreach($refData as $veld=>$data)
            $document[$veld]=$data;
          if($document['blobCompressed']==1)
            $document['blobdata']=gzuncompress($document['blobdata']);
          $document['module_id']=$recId;

          $doc=new digidoc($doelDatabaseId);
          $doc->addDocumentToStore($document);
          echo "Bestand ".$document['filename']." gekopieerd.<br>\n";
        }
        
      }
     echo vt('Kopieer actie voltooid') . ".<br>" . vt('Nieuw record id') . ":$recId.<br>\n";
     exit;
     //

   // }
    /*
    else
    {
      $query="INSERT INTO CRM_naw SET $insertFields ";
      $DB->SQL($query);
      if(!$DB->Query())
      {
        echo "Opslaan in doel database mislukt.<br>\n";
        $fout=true;
      }
      $recId=$DB->last_id();
      if($fout==false)
      {
        echo "Kopieer actie voltooid.<br>\n";
        //header("Location: CRM_nawEdit.php?action=edit&id=$recId&useSavedUrl=1");
      }
      exit;
    }
    */
  }
  else
  {
    $error=vt("Geen tabbladen geselecteerd?");
  }
}

$_SESSION['NAV']='';
$_SESSION['submenu'] = New Submenu();
//$_SESSION['submenu']->addItem("Terug naar NAW ","CRM_nawEdit.php?action=edit&id=".$data['deb_id']."&useSavedUrl=1");

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>" . vt('CRM kopieëer relatie') . "</b> '".$nawRec['naam']."'
</div><br><br>";

echo template($__appvar["templateContentHeader"],$content);
echo "<form method=\"POST\" name=\"copyForm\">
<input type=\"hidden\" name=\"deb_id\" value=\"".$data['deb_id']."\"
<b>" . vt('Selecteer de te kopieëren tabbladen.') . "</b><br><br>";
foreach ($veldenPerTab as $tab=>$data)
{
  if(in_array($tab,$checkedTabs))
    $checked="checked";
  else
    $checked='';
  echo "<input type=\"checkbox\" name=\"tabs[$tab]\" value=\"1\" $checked> ".vt($tab)." <br>";
}

if($__appvar["crmOnly"])
  $kopieInfo=vt("Kopieer naar AIRS");
else
  $kopieInfo=vt("Kopieer gekoppelde records");
//if($__appvar["crmOnly"] && isset($_DB_resources[6]['db']))
//{
  $tabellen=array('CRM_naw_kontaktpersoon'=>'relaties','CRM_naw_dossier'=>'gespreksverslagen','CRM_evenementen'=>'evenementen','CRM_naw_adressen'=>'adressen','CRM_naw_rekeningen'=>'rekeningen');

  echo "<br> <script language=\"JavaScript\" TYPE=\"text/javascript\">function checkCopy()
  {
    
    if(document.getElementById('naarAIRS').checked == true)
    {
      var statusChecks=true;
      var status=false;
    }
    else
    {
      status=true;
      statusChecks=false;
    }

    for(i=0;i<document.copyForm.elements['tabellen[]'].length;i++)
    {
       document.copyForm.elements['tabellen[]'][i].disabled=status;
       if(statusChecks==false)
       {
         document.copyForm.elements['tabellen[]'][i].checked=statusChecks;
       }
    }
    document.copyForm.documenten.disabled=status;
    
  } </script>";
  echo "<input type=\"checkbox\" id=\"naarAIRS\" name=\"naarAIRS\" value=\"1\" onclick=\"checkCopy()\"> <b>$kopieInfo</b> <br>";
  foreach ($tabellen as $tabel=>$omschrijving)
    echo "<input type=\"checkbox\" name=\"tabellen[]\" disabled value=\"$tabel\">" . vt('Kopieer') . " $omschrijving<br>";
    
  echo "<br><input type=\"checkbox\" name=\"documenten\" disabled value=\"1\">" . vt('Kopieer Documenten') . "<br>";


//}
echo "<br><button type=\"submit\" value=\"Kopieer velden\">" . vt('Kopieer velden') . "</button> </form> <br> $error";
//echo "<br><input type=\"submit\" value=\"Kopieer velden\"> </form> <br> $error";
echo template($__appvar["templateRefreshFooter"],$content);
?>
