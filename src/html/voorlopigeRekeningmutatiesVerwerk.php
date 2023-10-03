<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/03 15:39:06 $
 		File Versie					: $Revision: 1.34 $

 		$Log: voorlopigeRekeningmutatiesVerwerk.php,v $
 		Revision 1.34  2020/06/03 15:39:06  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2020/04/18 17:04:33  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2020/04/15 16:05:18  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2020/04/12 11:47:16  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2020/04/11 16:35:24  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2020/04/08 15:40:17  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2019/07/06 16:38:33  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2018/05/05 19:40:19  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2018/04/25 16:36:36  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2017/11/30 06:43:12  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2017/11/29 16:16:06  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2017/04/10 06:42:22  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2017/04/10 06:29:21  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2017/04/09 10:12:56  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2016/12/03 19:23:15  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2015/05/27 14:33:23  rm
 		rekeningmutaties
 		
 		Revision 1.18  2014/12/28 14:28:13  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2014/09/20 17:23:59  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2014/03/24 15:41:41  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2014/01/13 09:53:38  cvs
 		*** empty log message ***
 		
 		Revision 1.14  2013/12/23 16:38:50  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/05/08 15:39:45  rvv
 		*** empty log message ***

 		Revision 1.12  2013/05/04 15:58:08  rvv
 		*** empty log message ***

 		Revision 1.11  2013/05/01 15:50:07  rvv
 		*** empty log message ***

 		Revision 1.10  2013/01/27 13:59:54  rvv
 		*** empty log message ***

 		Revision 1.9  2013/01/09 17:06:04  rvv
 		*** empty log message ***

 		Revision 1.8  2011/11/23 18:54:09  rvv
 		*** empty log message ***

 		Revision 1.7  2011/07/13 08:48:55  rvv
 		*** empty log message ***

 		Revision 1.6  2011/05/22 11:45:49  rvv
 		*** empty log message ***

 		Revision 1.5  2009/10/31 09:48:57  rvv
 		*** empty log message ***

 		Revision 1.4  2009/05/30 07:51:10  rvv
 		*** empty log message ***

 		Revision 1.3  2009/05/13 08:20:08  rvv
 		$afschriftenData['NieuwSaldo'] = $afschriftenData['Saldo'] + $totaalBedrag;

 		Revision 1.2  2009/05/13 06:08:30  rvv
 		*** empty log message ***

 		Revision 1.1  2009/04/25 15:47:21  rvv
 		*** empty log message ***


*/

include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

function createInsert($data)
{
  $insert = "";
	foreach ($data as $key=>$value)
	{
	  if($key <> 'id')
	  {
	    $value=addslashes($value);
		  if($insert == '')
		    $insert .= " $key = '$value' ";
		  else
		    $insert .= ", $key = '$value' ";
		}
  }
	return $insert;
}

function createQueueUpdate($mutatieIds,$afschiftIds)
{
  global $ftpSettings,$USR,$__appvar;
	if($USR=='')
		$updateUser='systeem';
	else
		$updateUser=$USR;
  $db=new DB();
  $query="SELECT
VermogensbeheerdersPerBedrijf.Bedrijf,
Rekeningmutaties.id as recordId,
'Rekeningmutaties' as tabel
FROM
Rekeningmutaties
Inner Join Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
Inner Join VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE Rekeningmutaties.Id IN('".implode("','",$mutatieIds)."')";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $bedrijf=$data['Bedrijf'];
    unset($data['Bedrijf']);
    $dataMutaties[$bedrijf][]=$data;
  }
    $query="SELECT
VermogensbeheerdersPerBedrijf.Bedrijf,
Rekeningafschriften.id as recordId,
'Rekeningafschriften' as tabel
FROM
Rekeningafschriften
Inner Join Rekeningen ON Rekeningafschriften.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
Inner Join VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE Rekeningafschriften.Id IN('".implode("','",$afschiftIds)."')";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $bedrijf=$data['Bedrijf'];
    unset($data['Bedrijf']);
    $dataMutaties[$bedrijf][]=$data;
  }
  foreach ($dataMutaties as $bedrijf=>$mutatieData)
  {
    $exportId = date("YmdHis");
    $tofile= 	"export_".$bedrijf."_".$exportId.".sql";
    
    $vulling="select '93eca1cd0a52f27c8ef5c721c8d562cd8bfb8f2b6f7096ff784e3a51003a38d354dea9844717261e38eef39bbc6f973b8ae832743921ec875c4c7b0fcd5b450b35cd4b8ce4618b0d57ea8fcde22859085add417cc9b8d8f623c8b36f1dbe94cafa119f5b9cac9c1b4588e955a1224dfc0f8f78b938319f5c0a52ab07f13fd2db416216de0eb2e9442bc289dcf4d7c8e457218d8f632ccb5d7f76456a6084bf76a62c56905aead315126fe460eb72a55d8a7d52bd49794406bea4acc6d941133598a3908176a594e589f4d0fb992d85167f415f86d69ff2bfeabd2cf4ff402996e3b916595eab3de79ede2373a664dbaa8ad407d4c323c36ef6929eda952828cb99e569442250e23031196b986625401266be5e98fb948763be73cff6991b8f7e84d3a1d4b5d25cf3c9e3461de47c79ca65f963ae6faf859674f9f22df1da6b2be7a668f8035cc1e692dfdb1f49856522d90382788912d8fe0cdb2beea586fa8d3d63752a271742f6d242357b7632bae5a933f87221f53d4dd3d2b4ce5fe221c502c1ec1a14e2ec5723340e9664b97c0ded6460f69ad4d2d71001ebbfe40d74e6cf6fd443ed2b77111a0d75d36973bcdd3b177fd44dff28d31e05b9ed56bab19a97dd1198f70f1eca78726b36462b5db718828807a308dacfe94ae553f6fefd73cf1513e6eee58e1498f2774ccde8d5f5684f25f6fb34de4518313a162dade543af23499d7d7b29fc928ec9610c08a78fb06e9e44b4ffd7c8912aa36f8ede61a368322d34c2d65dad6b6d6df6e67ca55386899fd5851b5e98c5948fb27aaae8fdccf5318dcb8f3a35fda72be323d135a959d57edef0dc75b570466eaaf4ee77a43187da74143a4e96e86b3fbcb5989032767fdec347df09b679ee94e182b661f39d617cb149eb375529f39c2823b371482235c612d12db0b97bc9e5e2e81ef0499aa19285d8e7afcaf0714b2ba84d74cb833add53678dce355f1c53deb486a955182e2c100def6f04e411af78e8ec111c99d642f2a70127f61d8bd708c4318e15b47c5b2424a73446569d3f42ae4bcc747ecf2b7fe7017d52d876070a041cbe84d25c50707f94d6e966c9124984da9070cb476e3b5e4268e6a3d886e4a36d31c9798239f815ee17cd7f3c55bcd55cb979c38d3fb7b3ae11e12662b5d3347e4cb6b744ccc524479c16bf7e361c27afefb4cf97587d994993f974e12cbcd5e8083a24b48c692e4dd9edac01009c39fd9686f6bccf9fa6d35f51f0c17e81431fcd8ce65e6fc3fd4982fde5bcf97a41ee864ebc25f5ca5be9ea5ba967dced87a8786d6e82f330b2100146df98fafe715fea33af26c95db55f084bdcc704474e9b17ad1e17e0927d1510076a5ae5cbaacfff49d6a8978c078fc7d446cc1b85572322553b025e52d' as niets ;\n";
    if($fp = fopen($__appvar['tempdir'].$tofile, 'w'))
       fwrite($fp, $vulling);
    
    foreach ($mutatieData as $regel) //data verzamelen
    {
      $query = "SELECT * FROM ".$regel['tabel']." WHERE id = '".$regel['recordId']."' ";
      $db->SQL($query);
      $db->Query();
      $data = $db->nextRecord();

   	  $rec = serialize($data);
		  // insert Into Queue
		  $q2 =  "INSERT INTO importdata SET Bedrijf = '".$bedrijf."', tableName = '".$regel['tabel']."', tableId = '".$data['id']."', tableData = '".mysql_escape_string($rec)."', exportId = '".$exportId."', ";
		  $q2 .= " add_user = '".$updateUser."', add_date = NOW() , change_user = '".$updateUser."', change_date = NOW() ;\n";
		  fwrite($fp, $q2);
    }
    fclose($fp);


	  if(!gzcompressfile($__appvar['tempdir'].$tofile))
	  {
		  echo "Fout: zippen van bestand mislukt!";
		  $error=1;
	  }
    unlink($__appvar['tempdir'].$tofile);
	  if($error==false)
	  {
   		if($conn_id = ftp_connect($ftpSettings['server']))// login with username and password
	   	{
			  if($login_result = ftp_login($conn_id, $ftpSettings['user'], $ftpSettings['password']))
			  {
			    if (ftp_put($conn_id,$tofile.".gz",$__appvar['tempdir'].$tofile.".gz", FTP_BINARY))
			      echo "<br>\n successfully uploaded $tofile\n";
				  else
				  {
				    $error=1;
					  echo "There was a problem while uploading $tofile.gz";
				  }
			  }
			  ftp_close($conn_id);
	  	}
	   	else
	   	{
	   	  $error=1;
		    echo "Could not connect";
	   	}
		}
		if($error==false)
    {
			  $filesize = filesize($__appvar['tempdir'].$tofile.".gz");
        $query = "INSERT INTO updates SET exportId = '".$exportId."', Bedrijf = '".$bedrijf."', type = 'userqueue', jaar = '".date('Y')."', filename = '".$tofile.".gz', filesize = '".$filesize."',
	                server = '".$ftpSettings['server']."', username = '".$ftpSettings['user']."', password = '".$ftpSettings['password']."', consistentie = '', add_date = NOW(), add_user = '".$updateUser."',
	                change_date = NOW(), change_user = '".$updateUser."' ";
	      $queueDB = new DB(2);
	      $queueDB->SQL($query);
	      if($queueDB->Query())
	      {
	        echo "<br>\nUpdate in queue geplaatst om ".date('d-m-y H:i').".";
	      }
	   }
	   if(file_exists($__appvar['tempdir'].$tofile.".gz"))
	     unlink($__appvar['tempdir'].$tofile.".gz");
	}
}

function gzcompressfile($source,$level=false)
{
	global $__appvar;
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

session_start();
$_SESSION['submenu'] = "";
//clear navigatie
$_SESSION['NAV'] = "";
$_SESSION['rapportData']=array();

$content = array();
$mutatieIds =array();
$afschriftIds =array();
$oudeDatum=date('Y-m-d',time()-172800);

echo template($__appvar["templateContentHeader"],$content);
if ($_POST['action'] == 'do')
{
  $DB = new DB();
	$DB2 = new DB();
	$DB3 = new DB();
	$afschriftCounter=0;
	$oudeAfschriftenCounter=0;
	$mutatieCounter=0;
  if($__appvar['master'] == 1)
  {
		$query = "SELECT VoorlopigeRekeningafschriften.* 
      FROM VoorlopigeRekeningafschriften
      JOIN Rekeningen ON VoorlopigeRekeningafschriften.Rekening = Rekeningen.Rekening
      JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
      WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND
		    VoorlopigeRekeningafschriften.verwerkt = '0' AND Vermogensbeheerders.CrmTerugRapportage NOT IN(2,4)
		    ORDER BY VoorlopigeRekeningafschriften.Rekening,VoorlopigeRekeningafschriften.Afschriftnummer";
    
    if ( isset ($_POST['memoriaal']) && ! empty($_POST['memoriaal']) ) {
      $query .= " AND Rekeningen.Memoriaal = '" . $_POST['memoriaal'] . "'";
    }
    
		$DB = new DB();
    $DB->SQL($query);
		$DB->Query();
		while($afschriftenData = $DB->NextRecord())
		{
		  $TijdelijkeafschriftenData=$afschriftenData;
			$dat = db2jul($afschriftenData['Datum']);
		  $jaar = date("Y",$dat);
			$query = "SELECT Afschriftnummer, NieuwSaldo AS Saldo FROM Rekeningafschriften WHERE Rekening = '".$afschriftenData['Rekening']."' AND YEAR(Rekeningafschriften.Datum) = '".$jaar."' ORDER BY Afschriftnummer DESC LIMIT 1";
			$DB2 = new DB();
      $DB2->SQL($query);
			$DB2->Query();
			if($DB2->Records() > 0)
			{
				$afschriftenDataOld = $DB2->NextRecord();
				$afschriftenData['Afschriftnummer'] = $afschriftenDataOld['Afschriftnummer'] + 1;
				$afschriftenData['Saldo'] = $afschriftenDataOld['Saldo'];
			}
			else
			{
				$afschriftenData['Afschriftnummer'] = $jaar."001";
				$afschriftenData['Saldo'] = 0;
			}

		  $totaalBedrag=0;
			$query = "SELECT * FROM VoorlopigeRekeningmutaties WHERE
			          Rekening = '".$TijdelijkeafschriftenData['Rekening']."' AND Afschriftnummer = '".$TijdelijkeafschriftenData['Afschriftnummer']."' AND verwerkt = '0' ORDER BY VoorlopigeRekeningmutaties.Volgnummer	";
			$DB2->SQL($query);
			$DB2->Query();
			while($rekeningmutatieData = $DB2->NextRecord())
			{
		    $totaalBedrag += round($rekeningmutatieData['Bedrag'],2);
		    $rekeningmutatieData['Verwerkt']=1;
		    $rekeningmutatieData['Afschriftnummer']=$afschriftenData['Afschriftnummer'];
        if($rekeningmutatieData['Fonds'] <> '')
        {
          $query="SELECT Fonds FROM Fondsen WHERE Fonds='".mysql_real_escape_string($rekeningmutatieData['Fonds'])."'";
          if($DB3->QRecords($query)<>1)
          {
            echo "Fonds ".$rekeningmutatieData['Fonds']." niet gevonden in de Fondsen tabel.<br>\n";
          }
        }        
		    $insert = createInsert($rekeningmutatieData);
		  	$query= "INSERT INTO Rekeningmutaties SET $insert";
		  	$DB3->SQL($query);
		  	if($DB3->Query())
		  	{
		  	  $mutatieIds[]=$DB3->last_id();
		  	  $mutatieCounter++;
		  	  $query = "UPDATE VoorlopigeRekeningmutaties SET Verwerkt='1' WHERE id='".$rekeningmutatieData['id']."'";
		  	  $DB3->SQL($query);
		  	  $DB3->Query();
		  	}
			}

			$afschriftenData['NieuwSaldo'] = $afschriftenData['Saldo'] + $totaalBedrag;
			$afschriftenData['Verwerkt']=1;
			$insert = createInsert($afschriftenData);
			$query= "INSERT INTO Rekeningafschriften SET $insert";
			$DB3->SQL($query);
		  if($DB3->Query())
		  {
		    $afschriftIds[]=$DB3->last_id();
		    $afschriftCounter++;
		    $query = "UPDATE VoorlopigeRekeningafschriften SET Verwerkt='1' WHERE id='".$afschriftenData['id']."'";
		    $DB3->SQL($query);
		    $DB3->Query();
		  }
		}

		echo "<br><br><br>$afschriftCounter afschrift(en) en $mutatieCounter rekeningmutatie(s) verwerkt. <br>\n";
		createQueueUpdate($mutatieIds,$afschriftIds);
  }
  else
  {
		$prb = new ProgressBar();	// create new ProgressBar
		$prb->pedding = 2;	// Bar Pedding
		$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
		$prb->setFrame();          	                // set ProgressBar Frame
		$prb->frame['left'] = 50;	                  // Frame position from left
		$prb->frame['top'] = 	80;	                  // Frame position from top
		$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
		$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
		$prb->show();	                              // show the ProgressBar

		$prb->moveStep(0);
		$prb->setLabelValue('txt1','Verzenden voorlopige rekeningmutaties');
		$pro_step = 0;
    $cfg=new AE_config();
    $lastSync=$cfg->getData('LastSync_'.$USR);
    if($lastSync > time()-60)
    {
      echo 'Het is minder dan één minuut geleden dat er gegevens zijn verzonden. Verzenden afgebroken.';
      exit;
    }
    $cfg->addItem('LastSync_'.$USR,time());
		$ids=array();
		$idFilter='';
    $idOudFilter='';

		if($_POST['ids']<>'')
		{
			$ids = explode(',', $_POST['ids']);
			$idFilter = " VoorlopigeRekeningafschriften.id IN('".implode("','",$ids)."')";
		}
    if($_POST['verzenden']=='alle' && $_POST['idsOud']<>'')
    {
      $ids = explode(',', $_POST['idsOud']);
      if($idFilter<>'')
        $idOudFilter.=" OR ";
      $idOudFilter .= " VoorlopigeRekeningafschriften.id IN('".implode("','",$ids)."')";
    }

		$query = "SELECT VoorlopigeRekeningafschriften.*,
 if(DATE(VoorlopigeRekeningafschriften.add_date) < '".$oudeDatum."',1,0) as oudRecord
 FROM VoorlopigeRekeningafschriften,Rekeningen WHERE
		         ( $idFilter  $idOudFilter ) AND
		          VoorlopigeRekeningafschriften.verwerkt = '0' AND VoorlopigeRekeningafschriften.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0 ";

    if ( isset ($_POST['memoriaal']) && ! empty($_POST['memoriaal']) ) {
      $query .= " AND Rekeningen.Memoriaal = '" . $_POST['memoriaal'] . "'";
    }


	  $DB = new DB();
    $DB->SQL($query);
		$DB->Query();
    $records=$DB->Records();
    if($records==0)
    {
      echo "Geen records gevonden voor gebruiker ($USR) met ($query)<br>\n";
    }

		$pro_multiplier = (100 / $records);
		while($afschriftenData = $DB->NextRecord())
		{
      $mutatieBedrag = round(($afschriftenData["NieuwSaldo"] - $afschriftenData["Saldo"]),2);
      $DB2 = new DB();
      $DB2->SQL("SELECT SUM(Bedrag) AS Totaal FROM VoorlopigeRekeningmutaties WHERE Afschriftnummer = '".$afschriftenData["Afschriftnummer"]."' AND Rekening = '".$afschriftenData["Rekening"]."'");
      $DB2->Query();
      $totaal = $DB2->NextRecord();
      $mutatieVerschil = $mutatieBedrag - round($totaal['Totaal'],2);

      if($mutatieVerschil == 0)
      {
  			$query = "SELECT * FROM VoorlopigeRekeningmutaties WHERE
	  		          Rekening = '".$afschriftenData['Rekening']."' AND Afschriftnummer = '".$afschriftenData['Afschriftnummer']."' AND verwerkt = '0'	";
		  	$DB2->SQL($query);
		  	$DB2->Query();

  			while($rekeningmutatieData = $DB2->NextRecord())
	  		{
		  	  $mutatieCounter++;
			    $insert = createInsert($rekeningmutatieData);
			    $query="INSERT INTO VoorlopigeRekeningmutaties SET $insert";
          $DB4 = new DB(2);
  		    $DB4->SQL($query);
			    if($DB4->Query())
          {
			      $query="UPDATE VoorlopigeRekeningmutaties SET verwerkt='2' WHERE id ='".$rekeningmutatieData['id']."' ";
            $DB3 = new DB();
			      $DB3->SQL($query);
			      if(!$DB3->Query())
			      {
			       echo "Fout bij het verwerken locale record.: ($query) <br>\n";
			       exit;
			      }
			      else
			      {
			        $rapportData['mutaties'][$rekeningmutatieData['Afschriftnummer']][]=$rekeningmutatieData;
			      }
          }
          else
          {
            echo "Fout bij wegschrijven: ($query). Probeer opnieuw de mutaties te verzenden. <br>\n";
            exit;
          }
  			}
			  $afschriftCounter++;
				if($afschriftenData['oudRecord']==1)
			  	$oudeAfschriftenCounter++;
				unset($afschriftenData['oudRecord']);
		    $insert = createInsert($afschriftenData);
		    $query="INSERT INTO VoorlopigeRekeningafschriften SET $insert";
        $DB4 = new DB(2);
    		$DB4->SQL($query);
	  		if($DB4->Query())
        {
			    $query="UPDATE VoorlopigeRekeningafschriften SET verwerkt='2' WHERE id ='".$afschriftenData['id']."' ";
          $DB3 = new DB();
			    $DB3->SQL($query);
			    if(!$DB3->Query())
			    {
			      echo "Fout bij het verwerken locale record.: ($query) <br>\n";
			      exit;
			    }
        }
        else
        {
          echo "Fout bij wegschrijven: ($query). Probeer opnieuw de mutaties te verzenden. <br>\n";
          exit;
        }
      }
      else
      {
        echo "Afschrift ".$afschriftenData['Afschriftnummer']." bij ".$afschriftenData['Rekening']." is onvolledig. (Verschil van $mutatieVerschil)<br>\n";
        exit;
      }
			$pro_step += $pro_multiplier;
   		$prb->moveStep($pro_step);
		}
		$prb->hide();

		$rapportData['verzendTijd']=date('d-m-Y H:i:s');
		$_SESSION['rapportData']=$rapportData;

    session_write_close();
    /*
if($oudeAfschriftenCounter>0)
	$oudeAfschriftenMelding=" (waarvan $oudeAfschriftenCounter enkele dagen oud waren en geforceerd zijn verzonden.)";
else
	$oudeAfschriftenMelding='';
    */
echo "<br><br><br>$afschriftCounter afschrift(en) en $mutatieCounter rekeningmutatie(s) verzonden <br>\n<br>\n";

echo '<a href="genereerVerzendRapport.php" TARGET="_blank"><b> Genereer Verzendrapport. </b></a>';
  }
}
else
{
?>
<br>
<form action="<?=$PHP_SELF?>" method="POST">
<input type="hidden" name="action" value="do">
<input type="hidden" name="memoriaal" value="<?=$_GET['memoriaal']?>">
<table border="0">
<tr>
<?if($__appvar['master'] == 1){?>
<td> Nog niet verwerkte <?if($_GET['memoriaal']==1)echo 'memoriaal';?> rekeningmutaties verwerken? <br><br></td>
	</tr>
<tr><td><input type="submit" value=" Verwerken ">	</td></tr>
<?}else
{
	$ids=array();
	foreach($_POST as $key=>$value)
	{
		if(substr($key,0,10)=='mutatieId_')
		{
			$id=substr($key,10);
			$ids[]=$id;
		}
	}
	//if(count($ids)>0)
  $idFilter = "AND VoorlopigeRekeningafschriften.id IN('".implode("','",$ids)."')";
  $query = "SELECT VoorlopigeRekeningafschriften.id,VoorlopigeRekeningafschriften.add_user,
 if(DATE(VoorlopigeRekeningafschriften.add_date) < '".$oudeDatum."',1,0) as oudRecord
 FROM VoorlopigeRekeningafschriften,Rekeningen WHERE
		         ((VoorlopigeRekeningafschriften.add_user = '$USR' ) OR DATE(VoorlopigeRekeningafschriften.add_date) < '".$oudeDatum."' )  $idFilter AND
		          VoorlopigeRekeningafschriften.verwerkt = '0' AND VoorlopigeRekeningafschriften.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0 ";
  $DB = new DB();
  $DB->SQL($query);
  $DB->Query();
  $records=$DB->Records();
  $ids=array();
  $idsOud=array();
  $gebruikers=array();
  $eigen=array();
  if($records==0)
  {
    echo "Geen oude of eigen records gevonden om te verzenden. <br>\n";
  }
    while($afschriftenData = $DB->NextRecord())
    {
      if($afschriftenData['oudRecord']==1 && $afschriftenData['add_user']<>$USR)
      {
        $idsOud[] = $afschriftenData['id'];
        $gebruikers[$afschriftenData['add_user']]=$afschriftenData['add_user'];
      }
      else
      {
        $ids[] = $afschriftenData['id'];
        $eigen[$afschriftenData['add_user']]=$afschriftenData['add_user'];
      }
    }
    $aantal=count($ids);
	  $verzenden="Alle:<input type='radio' name='verzenden' value='alle' checked> &nbsp;&nbsp;&nbsp;";
    echo '<input type="hidden" name="ids" value="' . implode(",", $ids) . '">';
    if($aantal>0)
    {
      $verzenden.="Alleen eigen:<input type='radio' name='verzenden' value='eigen' >";
    }
    $aantalOut=count($idsOud);
    echo '<input type="hidden" name="idsOud" value="'.implode(",",$idsOud).'">';

  $verzenden.="<br>\n";
  if(count($gebruikers)>0)
    $oudeTxt=" van ".implode(",",$gebruikers);
  else
    $oudeTxt='';

?>
<td> <?=$aantal?> nog niet verwerkte eigen (<?=$USR?>) <?if($_GET['memoriaal']==1)echo 'memoriaal';?> rekeningmutatie(s) verzenden en <?=$aantalOut?>  oude mutatie(s) <?=$oudeTxt?> verzenden?<br><?=$verzenden?><br></td>
</tr><tr><td>
  
    <?
    if($aantal>0 || $aantalOut>0)
      echo '<input type="submit" value=" Verzenden ">';
    ?>
  
  </td></tr>
<?
}
?>
</table>
</form>
<?
}
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);
?>