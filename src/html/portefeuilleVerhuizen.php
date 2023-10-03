<?php

include_once("wwwvars.php");
include_once("rapport/rapportRekenClass.php");

if(checkAccess()==false)
  exit;

if($_GET['lookup']==1)
{

	  $query = "SELECT
Portefeuilles.Portefeuille
FROM
Portefeuilles 
WHERE
    Portefeuilles.startdatum <> '0000-00-00' AND Portefeuilles.Vermogensbeheerder='".mysql_real_escape_string($_GET['vermogensbeheerder'])."'
ORDER BY Portefeuilles.Portefeuille";

	$DBp = new DB();
	$DBp->SQL($query);
	$DBp->Query();
	while($pdata = $DBp->nextRecord())
	{
    $portefeuilles[]=$pdata['Portefeuille'];
  }
  echo json_encode(array('portefeuilles' => $portefeuilles));
  exit;
}

$content['jsincludes'].="<script type=\"text/javascript\" src=\"javascript/jquery.multiselect.js\"></script>";
$content['style'].='<link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen"><link type="text/css" href="style/jquery.multiselect.css" rel="stylesheet" />';
$content['javascript'] .= '

function vermogensbeheerderChanged()
{
  if($(\'#Bedrijf\').val()==\'Geen\' || $(\'#Bedrijf\').val()==\'Alles\')
  {
    $(\'#portefeuilleSelectie\').hide();
  }
  else
  {
    $(\'#portefeuilleSelectie\').show();
  }

  var $select = $("#portefeuilles").multiselect({noneSelectedText: "Selecteer bron portefeuilles"});//apply the plugin

  $.ajax({
    type: "GET",
    url: "portefeuilleVerhuizen.php?lookup=1&vermogensbeheerder="+$(\'#bronVermogensbeheerder\').val(),
    dataType: "json",
    async: false,
    data: "",
    success: function(data, textStatus, jqXHR)
    {
      $(\'select[name="inFields"]\').html(\'\');
      if (data.portefeuilles.length > 0) 
      {
        $("#portefeuilles").html(\'\');
        $select.multiselect(\'enable\');
        
        $.each(data.portefeuilles, function(index, value) {
        $("#portefeuilles").append($("<option></option>").val(value).html(value));
        });
      }
      $("#portefeuilles").multiselect(\'refresh\'); //refresh the select here
    
    },
    error: function(jqXHR, textStatus, errorThrown)
    {
    }
  });
  
}

function submitCheck(methode)
{
   var $select = $("#portefeuilles").multiselect();//apply the plugin
   var values = $select.val();
   
   $(\'#selectedPortefeuilles\').val(values);


   if($(\'#bronVermogensbeheerder\').val()==\'Geen\')
   {
     alert("Geen bron vermogensbeheerder geselecteerd.");
     return 0;
   }
   if($(\'#doelVermogensbeheerder\').val()==\'Geen\')
   {
     alert("Geen doel vermogensbeheerder geselecteerd.");
     return 0;
   }
   
   $(\'#methode\').val(methode);
   
document.selectForm.submit();

}

';


  
echo template($__appvar["templateContentHeader"],$content);

flush();


if(!checkAccess("superapp"))
{
	exit;
}

$newDatabase = 1;
$fondsOmschrijving = "Inbreng begingegevens";

$cfg=new AE_config();
$laatsteJaarafsluiting=$cfg->getData('laatsteMuatieJaarafsluiting');
$nieuweStartJaarafsluiting=date('Y-m-d H:i:s');
if($laatsteJaarafsluiting=='')
{
  $laatsteJaarafsluiting=date('Y-m-d H:i:s',time()-3600*24*10);
  $cfg->addItem('laatsteMuatieJaarafsluiting',$laatsteJaarafsluiting);
}

if($_POST['posted'] == true)
{
  $doelVermogensbeheerder=$_POST['doelVermogensbeheerder'];
  //$doelVermogensbeheerder='ANO';
  $portefeuilles=explode(',',$_POST['selectedPortefeuilles']);
  $portefeuilleFilter="Portefeuille IN('".implode("','",$portefeuilles). "')";
  $virtuelePortefeuilleFilter="VirtuelePortefeuille IN('".implode("','",$portefeuilles). "')";
  $queries=array(
    'Rekeningen'=>"SELECT Rekeningen.* FROM Rekeningen WHERE $portefeuilleFilter GROUP BY Rekeningen.id",
    'Portefeuilles'=>"SELECT Portefeuilles.* FROM Portefeuilles WHERE $portefeuilleFilter GROUP BY Portefeuilles.id",
    'GeconsolideerdePortefeuilles'=>"SELECT GeconsolideerdePortefeuilles.* FROM GeconsolideerdePortefeuilles WHERE $virtuelePortefeuilleFilter GROUP BY GeconsolideerdePortefeuilles.id",
    'Clienten'=>"SELECT Clienten.* FROM Clienten JOIN Portefeuilles ON Clienten.Client=Portefeuilles.Client WHERE $portefeuilleFilter GROUP BY Clienten.id",
    'Rekeningmutaties'=>"SELECT Rekeningmutaties.* FROM Rekeningmutaties JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening JOIN Portefeuilles ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille WHERE Rekeningen.$portefeuilleFilter",
    'Rekeningafschriften'=>"SELECT Rekeningafschriften.* FROM Rekeningafschriften JOIN Rekeningen ON Rekeningafschriften.Rekening = Rekeningen.Rekening JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille WHERE Portefeuilles.$portefeuilleFilter GROUP BY Rekeningafschriften.id",
    'Fondsen'=>"SELECT Fondsen.* FROM Fondsen JOIN Rekeningmutaties ON Fondsen.Fonds=Rekeningmutaties.Fonds JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening JOIN Portefeuilles ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille
   LEFT JOIN FondsenPerVermogensbeheerder ON Fondsen.Fonds=FondsenPerVermogensbeheerder.Fonds AND FondsenPerVermogensbeheerder.Vermogensbeheerder='".$doelVermogensbeheerder."' WHERE Rekeningen.$portefeuilleFilter AND FondsenPerVermogensbeheerder.Fonds is null GROUP BY Fondsen.id",
    'uitsluitingenModelcontrole'=>"SELECT uitsluitingenModelcontrole.* FROM uitsluitingenModelcontrole WHERE $portefeuilleFilter",
    'contractueleUitsluitingen'=>"SELECT contractueleUitsluitingen.* FROM contractueleUitsluitingen WHERE $portefeuilleFilter AND Portefeuille<>'' ",
    'Fondskoersen'=>"",
    'Valutas'=>"",
    'Valutakoersen'=>"",
  
  );
  $doelQueries=array('Valutas'=>"SELECT Fondsen.Valuta as veld FROM Fondsen JOIN FondsenPerVermogensbeheerder ON Fondsen.Fonds = FondsenPerVermogensbeheerder.Fonds WHERE FondsenPerVermogensbeheerder.Vermogensbeheerder='$doelVermogensbeheerder' GROUP BY Fondsen.Valuta",
                     'Fondsen'=>"SELECT Fondsen.Fonds as veld FROM Fondsen JOIN FondsenPerVermogensbeheerder ON Fondsen.Fonds = FondsenPerVermogensbeheerder.Fonds WHERE FondsenPerVermogensbeheerder.Vermogensbeheerder='$doelVermogensbeheerder'"
  );
  //listarray($queries);
  $db=new DB();
  $exportRecords=array();
  $fondsen=array();
  $valutas=array();
  $aanwezigeRecords=array();
  foreach($doelQueries as $tabel=>$query)
  {
    $db->SQL($query);
    $db->Query();
    while ($dbData = $db->nextRecord())
    {
      $aanwezigeRecords[$tabel][] = $dbData['veld'];
    }
  }
  foreach($queries as $tabel=>$query)
  {
    if($tabel=='Fondskoersen')
    {
      $query="SELECT Fondskoersen.* FROM Fondskoersen WHERE Fondskoersen.Fonds IN('".implode("','",$fondsen) ."') AND Fondskoersen.Fonds NOT IN('".implode("','",$aanwezigeRecords['Fondsen']) ."')";
    }
    elseif($tabel=='Valutas')
    {
      $query="SELECT Valutas.* FROM Valutas WHERE Valutas.Valuta IN('".implode("','",$valutas) ."') AND Valutas.Valuta NOT IN('".implode("','",$aanwezigeRecords['Valutas'])."')";
    }
    elseif($tabel=='Valutakoersen')
    {
      $query="SELECT Valutakoersen.* FROM Valutakoersen WHERE Valutakoersen.Valuta IN('".implode("','",$valutas) ."') AND Valutakoersen.Valuta NOT IN('".implode("','",$aanwezigeRecords['Valutas'])."')";
    }
    $db->SQL($query);
    $db->Query();
    while($dbData=$db->nextRecord())
    {
      if($tabel=='Portefeuilles' || $tabel=='GeconsolideerdePortefeuilles')
      {
        $dbData['Vermogensbeheerder']=$doelVermogensbeheerder;
      }
      elseif($tabel=='contractueleUitsluitingen')
      {
        $dbData['vermogensbeheerder']=$doelVermogensbeheerder;
      }
      elseif($tabel=='Rekeningmutaties')
      {
        if($dbData['Fonds']<>'')
          $fondsen[$dbData['Fonds']]=$dbData['Fonds'];
      }
      elseif($tabel=='Fondsen')
      {
        if($dbData['Valuta']<>'')
          $valutas[$dbData['Valuta']]=$dbData['Valuta'];
      }
      $exportRecords[$tabel][]=$dbData;
    }
    $aantal=count($exportRecords[$tabel]);
    echo "$tabel $aantal <br>\n";flush();ob_flush();
  }
  if($_POST['methode'] == "1")
  {
    createVerhuisUpdate($doelVermogensbeheerder, $exportRecords);
    $queries=array('Portefeuilles'=>"SELECT id,Vermogensbeheerder as Vermogensbeheerder FROM Portefeuilles WHERE $portefeuilleFilter AND Portefeuille<>''",
                   'contractueleUitsluitingen'=>"SELECT id, vermogensbeheerder as Vermogensbeheerder FROM contractueleUitsluitingen WHERE $portefeuilleFilter AND Portefeuille<>''",
                   'GeconsolideerdePortefeuilles'=>"SELECT id, Vermogensbeheerder as Vermogensbeheerder FROM GeconsolideerdePortefeuilles WHERE $virtuelePortefeuilleFilter AND VirtuelePortefeuille<>''");
    foreach($queries as $tabel=>$select)
    {
      $db->SQL($select);
      $db->Query();
      $ids=array();
      while($dbData=$db->nextRecord())
      {
        $ids[$dbData['id']]=array('oud'=>$dbData['Vermogensbeheerder'],'nieuw'=>$doelVermogensbeheerder);
      }
      foreach ($ids as $id=>$verm)
      {
        addTrackAndTrace($tabel, $id, 'Vermogensbeheerder', $verm['oud'], $verm['nieuw'], $USR);
      }
      $update="UPDATE $tabel SET Vermogensbeheerder='$doelVermogensbeheerder' WHERE id IN('".implode("','",array_keys($ids)) ."')";
      $db->SQL($update);
      $db->Query();
      echo "Vermogensbeheerder in $tabel omgezet.<br>\n";
    }
  }
  else
  {
    echo "Geen aanpassingen uitgevoerd.<br>\n";
  }
}
else
{

?>
<form action="" method="POST" name="selectForm" target="generateFrame">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="selectedPortefeuilles" id="selectedPortefeuilles" value="true" />
<input type="hidden" name="methode" id="methode" value="" />
<?
$DB = new DB();
$DB->SQL("SELECT Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder");
$DB->Query();

$vermogensbeheerders = array();

while($vermdata = $DB->NextRecord())
{
  $vermogensbeheerders[] = $vermdata['Vermogensbeheerder'];
}

?>
  <div class="formblock">
    <iframe width="800" height="300" name="generateFrame" frameborder="1" scrolling="Yes" marginwidth="0" marginheight="0"></iframe>
  </div>
<div class="formblock">
	<div class="formlinks"> Bron vermogensbeheerder </div>
	<div class="formrechts">
		<select id="bronVermogensbeheerder" name="bronVermogensbeheerder" onchange="javascript:vermogensbeheerderChanged();">
    <OPTION VALUE="Geen" selected>Geen
<?=SelectArray("",$vermogensbeheerders)?>
    </select>
	</div>
</div>
  <div class="formblock">
    <div class="formlinks"> Doel vermogensbeheerder </div>
    <div class="formrechts">
      <select id="doelVermogensbeheerder" name="doelVermogensbeheerder">
        <OPTION VALUE="Geen" selected>Geen
          <?=SelectArray("",$vermogensbeheerders)?>
      </select>
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks"> &nbsp; </div>
    <div class="formrechts">
      <br>
      
      <div class="buttonDiv" onclick="javascript:submitCheck('0');">Test</div><br>
      <div class="buttonDiv" onclick="javascript:submitCheck('1');">Verwerken</div><br>
    </div>
  <div class="formblock" id="portefeuilleSelectie" style="display:none">
    <div class="formlinks"> &nbsp; </div>
    <div class="formrechts">
      <select id='portefeuilles' name="portefeuilles" multiple="multiple"  >
    </div>
  </div>
</form>

<?
}

function createVerhuisUpdate($vermogensbeheerder,$exportData)
{
  global $__appvar,$USR,$ftpSettings;
  
  if($USR=='')
    $updateUser='systeem';
  else
    $updateUser=$USR;
  
  $db = new DB();
  $query = "SELECT VermogensbeheerdersPerBedrijf.Bedrijf
    FROM VermogensbeheerdersPerBedrijf
    WHERE VermogensbeheerdersPerBedrijf.Vermogensbeheerder = '$vermogensbeheerder'
    ORDER BY VermogensbeheerdersPerBedrijf.Bedrijf";
  $db->SQL($query);
  $db->Query();
  $bedrijven=array();
  while($data = $db->nextRecord())
  {
    $bedrijven[$data['Bedrijf']]=$exportData;
  }

  foreach ($bedrijven as $bedrijf=>$mutatieData)
  {
    $exportId = date("YmdHis").'_'.rand(1000,9999);
    $tofile= 	"export_".$bedrijf."_".$exportId.".sql";
    $vulling="select '93eca1cd0a52f27c8ef5c721c8d562cd8bfb8f2b6f7096ff784e3a51003a38d354dea9844717261e38eef39bbc6f973b8ae832743921ec875c4c7b0fcd5b450b35cd4b8ce4618b0d57ea8fcde22859085add417cc9b8d8f623c8b36f1dbe94cafa119f5b9cac9c1b4588e955a1224dfc0f8f78b938319f5c0a52ab07f13fd2db416216de0eb2e9442bc289dcf4d7c8e457218d8f632ccb5d7f76456a6084bf76a62c56905aead315126fe460eb72a55d8a7d52bd49794406bea4acc6d941133598a3908176a594e589f4d0fb992d85167f415f86d69ff2bfeabd2cf4ff402996e3b916595eab3de79ede2373a664dbaa8ad407d4c323c36ef6929eda952828cb99e569442250e23031196b986625401266be5e98fb948763be73cff6991b8f7e84d3a1d4b5d25cf3c9e3461de47c79ca65f963ae6faf859674f9f22df1da6b2be7a668f8035cc1e692dfdb1f49856522d90382788912d8fe0cdb2beea586fa8d3d63752a271742f6d242357b7632bae5a933f87221f53d4dd3d2b4ce5fe221c502c1ec1a14e2ec5723340e9664b97c0ded6460f69ad4d2d71001ebbfe40d74e6cf6fd443ed2b77111a0d75d36973bcdd3b177fd44dff28d31e05b9ed56bab19a97dd1198f70f1eca78726b36462b5db718828807a308dacfe94ae553f6fefd73cf1513e6eee58e1498f2774ccde8d5f5684f25f6fb34de4518313a162dade543af23499d7d7b29fc928ec9610c08a78fb06e9e44b4ffd7c8912aa36f8ede61a368322d34c2d65dad6b6d6df6e67ca55386899fd5851b5e98c5948fb27aaae8fdccf5318dcb8f3a35fda72be323d135a959d57edef0dc75b570466eaaf4ee77a43187da74143a4e96e86b3fbcb5989032767fdec347df09b679ee94e182b661f39d617cb149eb375529f39c2823b371482235c612d12db0b97bc9e5e2e81ef0499aa19285d8e7afcaf0714b2ba84d74cb833add53678dce355f1c53deb486a955182e2c100def6f04e411af78e8ec111c99d642f2a70127f61d8bd708c4318e15b47c5b2424a73446569d3f42ae4bcc747ecf2b7fe7017d52d876070a041cbe84d25c50707f94d6e966c9124984da9070cb476e3b5e4268e6a3d886e4a36d31c9798239f815ee17cd7f3c55bcd55cb979c38d3fb7b3ae11e12662b5d3347e4cb6b744ccc524479c16bf7e361c27afefb4cf97587d994993f974e12cbcd5e8083a24b48c692e4dd9edac01009c39fd9686f6bccf9fa6d35f51f0c17e81431fcd8ce65e6fc3fd4982fde5bcf97a41ee864ebc25f5ca5be9ea5ba967dced87a8786d6e82f330b2100146df98fafe715fea33af26c95db55f084bdcc704474e9b17ad1e17e0927d1510076a5ae5cbaacfff49d6a8978c078fc7d446cc1b85572322553b025e52d' as niets ;\n";
    if($fp = fopen($__appvar['tempdir'].$tofile, 'w'))
      fwrite($fp, $vulling);
    else
      $errorArray[]= "<br>\n FOUT: openen van ".$__appvar['tempdir'].$tofile." mislukt.";
    
    foreach ($mutatieData as $tabel=>$records) //data verzamelen
    {
      $normalUpdate='';
      $aantal=count($records);
      $n=0;
      foreach($records as $velden)
      {
        $n++;
        $rec = serialize($velden);
        
        $normalSQLlen=strlen($normalUpdate);
        if($normalSQLlen==0)
          $normalUpdate.="('".$bedrijf."','".$tabel."','".$velden['id']."','".mysql_escape_string($rec)."',  '".$exportId."',  '".$updateUser."',NOW() ,'".$updateUser."',NOW()) ";
        else
          $normalUpdate.=",('".$bedrijf."','".$tabel."','".$velden['id']."','".mysql_escape_string($rec)."',  '".$exportId."',  '".$updateUser."',NOW() ,'".$updateUser."',NOW()) ";
        if($normalSQLlen > 1000000 || $n==$aantal)
        {
          $q2 = "INSERT INTO importdata (Bedrijf,tableName,tableId,tableData,exportId,add_user,add_date,change_user,change_date) VALUES $normalUpdate ;\n";
          //echo $q2."<br>\n<br>\n";
          fwrite($fp, $q2);
          $normalUpdate='';
        }
        
/*
        if ($velden['id'] > 0)
        {
          // insert Into Queue
          $q2 = "INSERT INTO importdata SET Bedrijf = '" . $bedrijf . "', tableName = '" . $tabel . "', tableId = '" . $velden['id'] . "', tableData = '" . mysql_escape_string($rec) . "', exportId = '" . $exportId . "', ";
          $q2 .= " add_user = '" . $updateUser . "', add_date = NOW() , change_user = '" . $updateUser . "', change_date = NOW() ;\n";
          fwrite($fp, $q2);
        }
*/
      }
    }
    fclose($fp);
   
    if(!gzcompressdatafile($__appvar['tempdir'].$tofile))
      $errorArray[] = "Fout: zippen van bestand mislukt!";
    unlink($__appvar['tempdir'].$tofile);
    if(empty($errorArray))
    {
      if($conn_id = ftp_connect($ftpSettings['server']))// login with username and password
      {
        if($login_result = ftp_login($conn_id, $ftpSettings['user'], $ftpSettings['password']))
        {
          if ($__appvar["ftpPasv"])
          {
            echo logTxt("Ftp pasv set.");
            ftp_pasv($conn_id, true);
          }
          if (ftp_put($conn_id,$tofile.".gz",$__appvar['tempdir'].$tofile.".gz", FTP_BINARY))
            echo "<br>\n successfully uploaded $tofile\n";
          else
            $errorArray[] = "There was a problem while uploading $tofile.gz";
        }
        ftp_close($conn_id);
      }
      else
        $errorArray[] = "Could not connect to ftp server";
    }
    
    if(empty($errorArray))
    {
      $filesize = filesize($__appvar['tempdir'].$tofile.".gz");
      $query = "INSERT INTO updates SET exportId = '".$exportId."', Bedrijf = '".$bedrijf."', type = 'dataqueue', jaar = '".date('Y')."', filename = '".$tofile.".gz', filesize = '".$filesize."',
	                  server = '".$ftpSettings['server']."', username = '".$ftpSettings['user']."', password = '".$ftpSettings['password']."', consistentie = '', add_date = NOW(), add_user = '".$updateUser."',
	                  change_date = NOW(), change_user = '".$updateUser."' ";
      $queueDB = new DB(2);
      $queueDB->SQL($query);
      if($queueDB->Query())
      {
        echo "<br>\nUpdate in queue geplaatst om ".date('d-m-y H:i').".";
        unlink($__appvar['tempdir'].$tofile.".gz");
      }
    }
    else
    {
      listarray($errorArray);
      exit;
    }
  }
  

  
}
function gzcompressdatafile($source,$level=false)
{
  $dest=$source.'.gz';
  $mode='wb'.$level;
  $error=false;
  if($fp_out=gzopen($dest,$mode)){
    if($fp_in=fopen($source,'r')){
      while(!feof($fp_in))
        gzwrite($fp_out,fread($fp_in,1024*512));
      fclose($fp_in);
    }
    else $error=true;
    gzclose($fp_out);
  }
  else $error=true;
  if($error) return false;
  else return $dest;
}


echo template($__appvar["templateRefreshFooter"],$content);
?>