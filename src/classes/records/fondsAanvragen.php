<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 10 januari 2015
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2017/12/13 15:45:46 $
    File Versie         : $Revision: 1.27 $
 		
    $Log: fondsAanvragen.php,v $
    Revision 1.27  2017/12/13 15:45:46  rm
    6388

    Revision 1.26  2017/05/20 18:12:09  rvv
    *** empty log message ***

    Revision 1.25  2017/05/17 15:59:31  rvv
    *** empty log message ***

    Revision 1.24  2017/05/06 17:20:13  rvv
    *** empty log message ***

    Revision 1.23  2017/05/03 14:30:20  rvv
    *** empty log message ***

    Revision 1.22  2017/04/23 12:44:19  rvv
    *** empty log message ***

    Revision 1.21  2016/12/03 19:25:41  rvv
    *** empty log message ***

    Revision 1.20  2016/10/19 15:32:16  rvv
    *** empty log message ***

    Revision 1.19  2016/07/06 16:01:42  rvv
    *** empty log message ***

    Revision 1.18  2016/07/03 07:58:27  rvv
    *** empty log message ***

    Revision 1.17  2016/07/03 07:31:56  rvv
    *** empty log message ***

    Revision 1.16  2016/07/02 09:22:56  rvv
    *** empty log message ***

    Revision 1.15  2015/11/30 07:26:45  rvv
    *** empty log message ***

    Revision 1.14  2015/11/29 13:05:52  rvv
    *** empty log message ***

    Revision 1.13  2015/09/15 12:09:54  rvv
    *** empty log message ***

    Revision 1.12  2015/04/29 15:19:33  rvv
    *** empty log message ***

    Revision 1.11  2015/04/27 10:18:59  rvv
    *** empty log message ***

    Revision 1.10  2015/04/27 10:06:12  rvv
    *** empty log message ***

    Revision 1.9  2015/04/26 12:23:49  rvv
    *** empty log message ***

    Revision 1.8  2015/02/07 20:34:20  rvv
    *** empty log message ***

    Revision 1.7  2015/01/28 20:01:31  rvv
    *** empty log message ***

    Revision 1.6  2015/01/26 08:27:15  rvv
    *** empty log message ***

    Revision 1.5  2015/01/24 19:37:58  rvv
    *** empty log message ***

    Revision 1.4  2015/01/17 19:07:06  rvv
    *** empty log message ***

    Revision 1.3  2015/01/12 11:19:21  rvv
    *** empty log message ***

    Revision 1.2  2015/01/11 13:22:57  rvv
    *** empty log message ***

    Revision 1.1  2015/01/11 12:34:54  rvv
    *** empty log message ***

 		
 	
*/

class FondsAanvragen extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();


  /*
  * Constructor
  */
  function FondsAanvragen()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

	/*
	 * Veldvalidatie
	 */
	function validate()
	{
	  global $__appvar;
    if($this->dbId==2)
    {
      $db=new DB();
			if($this->get('OptieSymbool')<>'' || $this->get("Fonds")<>'' )
			{
				($this->get("OptieType")=="")?$this->setError("OptieType", vt("OptieType mag niet leeg zijn!")):true;
				($this->get("OptieExpDatum")=="")?$this->setError("OptieExpDatum", vt("OptieExpDatum mag niet leeg zijn!")):true;
				($this->get("OptieUitoefenPrijs")=="" || $this->get("OptieUitoefenPrijs") == 0)?$this->setError("OptieUitoefenPrijs", vt("OptieUitoefenPrijs mag niet leeg zijn!")):true;
				($this->get("Fondseenheid")=="")?$this->setError("Fondseenheid", vt("Fondseenheid mag niet leeg zijn!")):true;
				($this->get("OptieBovenliggendFonds")=="")?$this->setError("OptieBovenliggendFonds", vt("OptieBovenliggendFonds mag niet leeg zijn!")):true;

				$query = "SELECT Fonds FROM Fondsen WHERE Fonds='" . $this->get("Fonds") . "'";
				if ($db->QRecords($query))
				{
					$fonds = $db->nextRecord();
					$this->setError("Fonds", vtb("Fonds (%s) is al aanwezig.", array($fonds['Fonds'])));
				}

	  		$query = "SELECT * FROM `fondsOptieSymbolen` WHERE `key` = '" .$this->get("OptieSymbool") . "';";
				if ( $db->QRecords($query) == 0 )
				{
					$this->setError("fondsOptieSymbolen", vt("Symbool is onbekend!"));
				}

			}
			else
			{
				$query = "SELECT Fonds FROM Fondsen WHERE ISINCode='" . $this->get("ISINCode") . "' AND Valuta='" . $this->get("Valuta") . "'";
				if ($db->QRecords($query))
				{
					$fonds = $db->nextRecord();
					$this->setError("ISINCode", vtb("Fonds (%s) is aanwezig met de opgegeven ISIN code.", array($fonds['Fonds'])));
				}
			}
    }
    ($this->get("verwerkt")==1)?$this->setError("verwerkt",vt("Record is niet automatisch verwerkt.")):true;
    ($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
		if($this->get('OptieSymbool')=='')
      ($this->get("ISINCode")=="")?$this->setError("ISINCode",vt("Mag niet leeg zijn!")):true;
    ($this->get("Valuta")=="")?$this->setError("Valuta",vt("Mag niet leeg zijn!")):true;
    ($this->get("Beleggingscategorie")=="")?$this->setError("Beleggingscategorie",vt("Mag niet leeg zijn!")):true;
    
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	 global $__appvar;
	 $db=new DB();
	 if($__appvar['bedrijf']=='HOME')
   { 
	 	if(($type=='delete' || $type=='edit') && $this->get('verwerkt')==1)
      return false;
    if(($type=='delete' || $type=='edit') && $db->QRecords("SELECT id FROM fondsAanvragen WHERE verwerkt=-1 AND id='".$this->get('id')."'")==1)
      return false;
   }
   if($_SESSION['usersession']['gebruiker']['fondsaanvragenAanleveren']==1)
     return true;
     
	 return checkAccess();
	}
  
  
  function sendFondsaanvraagEmail()
  {
    $cfg=new AE_config();
    $mailserver=$cfg->getData('smtpServer');
    $fondsEmail=$cfg->getData('fondsEmail');
    include_once('../AE_cls_phpmailer.php');
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->From     = $fondsEmail;
    $mail->FromName = "Airs";
    if($mail->ValidateAddress($this->get('emailAdres')))
    {
			if($this->get('ISINCode') == '')
			{
				$body = "Fondsaanvraag voor " . $this->get('Fonds') . " " . $this->get('Valuta') . " heeft op " . date('d-m-Y H:i:s') . " de status " . strtolower($this->data['fields']['verwerkt']['form_options'][$this->get('verwerkt')]) . " gekregen.<br>\n";
				$subject="Fondsaanvraag ".$this->get('Fonds');
			}
			else
			{
				$body = "Fondsaanvraag voor " . $this->get('fondsnaam') . " " . $this->get('ISINCode') . " " . $this->get('Valuta') . " heeft op " . date('d-m-Y H:i:s') . " de status " . strtolower($this->data['fields']['verwerkt']['form_options'][$this->get('verwerkt')]) . " gekregen.<br>\n";
				$subject="Fondsaanvraag ".$this->get('fondsnaam');
			}
			if($this->get('verwerkt') > 0)
        $body.="<br>\nDeze mutatie zal in een volgende update worden opgenomen.";
          
      $mail->Body    = $body;
      $mail->AltBody = html_entity_decode(strip_tags($body));
			storeControleMail('fondsaanvraag',$subject,$body);
      $mail->AddAddress($this->get('emailAdres'),$this->get('emailAdres'));
      $mail->Subject = $subject;
      $mail->Host=$mailserver;
      if(!$mail->Send())
      {
        echo "Verzenden van e-mail mislukt.";
      }
    } 
  }

	function createQueueUpdates($recordId,$fonds)
	{
		$log=array();
		global $__appvar,$USR,$ftpSettings;
		if($USR=='')
			$updateUser='systeem';
		else
			$updateUser=$USR;

		$queueDB = new DB(2);
		$queueDB->SQL("SHOW TABLE STATUS like '%updates%'");
		$status=$queueDB->lookupRecord();
		$lastId=$status['Auto_increment'];

		$db = new DB();

		$query = "SELECT
fondsAanvragen.id,
fondsAanvragen.Vermogensbeheerder,
VermogensbeheerdersPerBedrijf.Bedrijf
FROM
fondsAanvragen
INNER JOIN VermogensbeheerdersPerBedrijf ON fondsAanvragen.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder 
WHERE fondsAanvragen.id='".$recordId."'";
		$db->SQL($query);
		$db->Query();
		while($data = $db->nextRecord())
		{
			$dataMutaties[$data['Bedrijf']][] = $data;
		}

		foreach ($dataMutaties as $bedrijf=>$mutatieData)
		{
			$exportId = date("YmdHis").'_'.$lastId;
			$tofile= 	"export_".$bedrijf."_".$exportId.".sql";
			$vulling="select '93eca1cd0a52f27c8ef5c721c8d562cd8bfb8f2b6f7096ff784e3a51003a38d354dea9844717261e38eef39bbc6f973b8ae832743921ec875c4c7b0fcd5b450b35cd4b8ce4618b0d57ea8fcde22859085add417cc9b8d8f623c8b36f1dbe94cafa119f5b9cac9c1b4588e955a1224dfc0f8f78b938319f5c0a52ab07f13fd2db416216de0eb2e9442bc289dcf4d7c8e457218d8f632ccb5d7f76456a6084bf76a62c56905aead315126fe460eb72a55d8a7d52bd49794406bea4acc6d941133598a3908176a594e589f4d0fb992d85167f415f86d69ff2bfeabd2cf4ff402996e3b916595eab3de79ede2373a664dbaa8ad407d4c323c36ef6929eda952828cb99e569442250e23031196b986625401266be5e98fb948763be73cff6991b8f7e84d3a1d4b5d25cf3c9e3461de47c79ca65f963ae6faf859674f9f22df1da6b2be7a668f8035cc1e692dfdb1f49856522d90382788912d8fe0cdb2beea586fa8d3d63752a271742f6d242357b7632bae5a933f87221f53d4dd3d2b4ce5fe221c502c1ec1a14e2ec5723340e9664b97c0ded6460f69ad4d2d71001ebbfe40d74e6cf6fd443ed2b77111a0d75d36973bcdd3b177fd44dff28d31e05b9ed56bab19a97dd1198f70f1eca78726b36462b5db718828807a308dacfe94ae553f6fefd73cf1513e6eee58e1498f2774ccde8d5f5684f25f6fb34de4518313a162dade543af23499d7d7b29fc928ec9610c08a78fb06e9e44b4ffd7c8912aa36f8ede61a368322d34c2d65dad6b6d6df6e67ca55386899fd5851b5e98c5948fb27aaae8fdccf5318dcb8f3a35fda72be323d135a959d57edef0dc75b570466eaaf4ee77a43187da74143a4e96e86b3fbcb5989032767fdec347df09b679ee94e182b661f39d617cb149eb375529f39c2823b371482235c612d12db0b97bc9e5e2e81ef0499aa19285d8e7afcaf0714b2ba84d74cb833add53678dce355f1c53deb486a955182e2c100def6f04e411af78e8ec111c99d642f2a70127f61d8bd708c4318e15b47c5b2424a73446569d3f42ae4bcc747ecf2b7fe7017d52d876070a041cbe84d25c50707f94d6e966c9124984da9070cb476e3b5e4268e6a3d886e4a36d31c9798239f815ee17cd7f3c55bcd55cb979c38d3fb7b3ae11e12662b5d3347e4cb6b744ccc524479c16bf7e361c27afefb4cf97587d994993f974e12cbcd5e8083a24b48c692e4dd9edac01009c39fd9686f6bccf9fa6d35f51f0c17e81431fcd8ce65e6fc3fd4982fde5bcf97a41ee864ebc25f5ca5be9ea5ba967dced87a8786d6e82f330b2100146df98fafe715fea33af26c95db55f084bdcc704474e9b17ad1e17e0927d1510076a5ae5cbaacfff49d6a8978c078fc7d446cc1b85572322553b025e52d' as niets ;\n";
			if($fp = fopen($__appvar['tempdir'].$tofile, 'w'))
				fwrite($fp, $vulling);
			else
				$error[]= "<br>\n FOUT: openen van ".$__appvar['tempdir'].$tofile." mislukt.";
			

			foreach ($mutatieData as $fondsAaanvraag) //data verzamelen
			{

        if($fondsAaanvraag['id']>0)
				{
					/*
					$query="SELECT * FROM fondsAanvragen WHERE id='".$aanvraag['id']."'";
					$db->SQL($query);
					$db->Query();
					$fondsAaanvraag = $db->nextRecord();
					$rec = serialize($fondsAaanvraag);
					$q2 =  "INSERT INTO importdata SET Bedrijf = '".$bedrijf."', tableName = 'fondsAanvragen', tableId = '".$fondsAaanvraag['id']."', tableData = '".mysql_escape_string($rec)."', exportId = '".$exportId."', ";
					$q2 .= " add_user = '".$USR."', add_date = NOW() , change_user = '".$USR."', change_date = NOW() ;\n";
					fwrite($fp, $q2);
          */
					$export=array('Fondsen','Rentepercentages','Fondskoersen','BeleggingscategoriePerFonds','ZorgplichtPerFonds','BeleggingssectorPerFonds');

					foreach($export as $index=>$tabel)
					{
						if($index > 2)
							$vermogensbeheerderFilter="AND Vermogensbeheerder='".$fondsAaanvraag['Vermogensbeheerder']."'";
						else
							$vermogensbeheerderFilter='';
						$query = "SELECT * FROM $tabel WHERE Fonds = '" . $fonds . "' $vermogensbeheerderFilter";
						$db->SQL($query);
						$db->Query();
						if($db->records())
						{
							while($data = $db->nextRecord())
							{
								$rec = serialize($data);
								$q2 = "INSERT INTO importdata SET Bedrijf = '" . $bedrijf . "', tableName = '" . $tabel . "', tableId = '" . $data['id'] . "', tableData = '" . mysql_escape_string($rec) . "', exportId = '" . $exportId . "', ";
								$q2 .= " add_user = '" . $updateUser . "', add_date = NOW() , change_user = '" . $updateUser . "', change_date = NOW() ;\n";
								fwrite($fp, $q2);
							}
						}
					}
				}
			}
			fclose($fp);

			if(!$this->gzcompressfile($__appvar['tempdir'].$tofile))
				$error[] = "Fout: zippen van bestand mislukt!";

			unlink($__appvar['tempdir'].$tofile);
			if(empty($error))
			{
				if($conn_id = ftp_connect($ftpSettings['server']))// login with username and password
				{
					if($login_result = ftp_login($conn_id, $ftpSettings['user'], $ftpSettings['password']))
					{
						if (ftp_put($conn_id,$tofile.".gz",$__appvar['tempdir'].$tofile.".gz", FTP_BINARY))
							$log[] = "<br>\n successfully uploaded $tofile\n";
						else
							$error[] = "There was a problem while uploading $tofile.gz";
					}
					ftp_close($conn_id);
				}
				else
					$error[] = "Could not connect";
			}

			if(empty($error))
			{
				$filesize = filesize($__appvar['tempdir'].$tofile.".gz");
				$query = "INSERT INTO updates SET exportId = '".$exportId."', Bedrijf = '".$bedrijf."', type = 'userqueue', jaar = '".date('Y')."', filename = '".$tofile.".gz', filesize = '".$filesize."',
	                  server = '".$ftpSettings['server']."', username = '".$ftpSettings['user']."', password = '".$ftpSettings['password']."', consistentie = '', add_date = NOW(), add_user = '".$updateUser."',
	                  change_date = NOW(), change_user = '".$updateUser."' ";
				$queueDB = new DB(2);
				$queueDB->SQL($query);
				if($queueDB->Query())
				{
					$log[]="<br>\nUpdate in queue geplaatst om ".date('d-m-y H:i').".";
					unlink($__appvar['tempdir'].$tofile.".gz");
				}
			}
		}

		if(count($error) > 0)
		{
			echo "<br>\n<b>Error:</b><br>\n";
			foreach($error as $melding)
				echo $melding."<br>\n";
		}
		if(count($log) > 0)
		{
			echo "<br>\n<b>UpdateLog:</b><br>\n";
			foreach($log as $melding)
				echo $melding."<br>\n";
		}
		//return array('log'=>$log,'error'=>$error);
	}
	function gzcompressfile($source,$level=false)
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

  
  function exportToPdf()
  {
    global $__appvar,$USR;
    define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
    include_once("../classes/AE_cls_fpdf.php");
    include_once("../html/rapport/rapportVertaal.php");
    include_once("../html/rapport/PDFOverzicht.php");
    $pdf = new PDFOverzicht('P','mm');
    
    $pdf->SetFont('Arial','B',8);
    $pdf->AddPage();
    $pdf->SetWidths(array(200));
    $pdf->SetAligns(array('L','L'));
    $pdf->Row(array("Fondsaanvraag print door $USR op ".date('d-m-Y H:i:s')));
    $pdf->ln();
    $pdf->SetFont('Arial','B',8);
    $pdf->SetAligns(array('L','L'));
    $pdf->SetWidths(array(60,120));
    $pdf->Row(array('Veld','Waarde'));
    $pdf->SetFont('Arial','',8);
    foreach($this->data['fields'] as $veld=>$waarden)
    { 
       if(is_array($waarden['form_options']) && $waarden['form_options'][$waarden['value']] <> '' )
       {
          $value=$waarden['form_options'][$waarden['value']];
       }
       else
         $value=$waarden['value'];
    
      $pdf->Row(array($waarden['description'],$value));
    }

   // header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	//	$pdf->Output("fondsaanvraag_".$this->get('Vermogensbeheerder').".pdf","D");
    $pdf->Output("fondsaanvraag.pdf",'I');
    //exit;
  }
	
	/*
  * Table definition
  */
  function defineData()
  {
    global $__appvar;
    $this->data['name']  = "";
    $this->data['table']  = "fondsAanvragen";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Vermogensbeheerder',
													array("description"=>"Vermogensbeheerder",
													"default_value"=>$__appvar['bedrijf'],
													"db_size"=>"10",
													"db_type"=>"varchar",
                          "form_select_option_notempty"=>true,
													"select_query"=>"SELECT Vermogensbeheerder, Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder",
													"form_type"=>"selectKeyed",
													"form_size"=>"10",
													"form_visible"=>true,
                          "form_extra"=>" onChange=\"javascript:vermogensbeheerderChanged();\" ",
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders"));
                          
		$this->addField('ISINCode',
													array("description"=>"ISINCode",
													"default_value"=>"",
													"db_size"=>"26",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"26",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Valuta',
													array("description"=>"Valuta",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"varchar",
													//'form_options'=>array('AUD','BRL','CAD','CHF','CLP','CNY','DEM','DKK','EUR','GBP','GBPF','HKD','HUF','IDR','INR','ISK','ISL','ITL','JPY','JPYF','KRW','LUF','MXN','MYR','NLG','NOK','NZD','PEN','PHP','PLN','RUB','SEK','SGD','THB','TRY','USD','USDF','ZAR'),
													"select_query"=>"SELECT Valuta, Valuta FROM Valutas ORDER BY Valuta",
													"form_type"=>"selectKeyed",
													//"form_type"=>"select",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Beleggingscategorie',
													array("description"=>"Beleggingscategorie",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"select_query"=>"SELECT waarde, waarde FROM KeuzePerVermogensbeheerder WHERE categorie='Beleggingscategorien' GROUP BY waarde  ORDER BY waarde",
													//"form_options"=>array(),
                          "form_type"=>"selectKeyed",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Beleggingssector',
													array("description"=>"Beleggingssector",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"select_query"=>"SELECT waarde, waarde FROM KeuzePerVermogensbeheerder WHERE categorie='Beleggingssectoren' GROUP BY waarde  ORDER BY waarde",
													//"form_options"=>array(),
                          "form_type"=>"selectKeyed",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Regio',
													array("description"=>"Regio",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"select_query"=>"SELECT waarde, waarde FROM KeuzePerVermogensbeheerder WHERE categorie='Regios' GROUP BY waarde  ORDER BY waarde",
													//"form_options"=>array(),
                          "form_type"=>"selectKeyed",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AttributieCategorie',
													array("description"=>"AttributieCategorie",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
                          "select_query"=>"SELECT waarde, waarde FROM KeuzePerVermogensbeheerder WHERE categorie='AttributieCategorien' GROUP BY waarde  ORDER BY waarde",
													//"form_options"=>array(),
                          "form_type"=>"selectKeyed",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('afmCategorie',
													array("description"=>"afmCategorie",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"select_query"=>"SELECT afmCategorie, omschrijving FROM afmCategorien  ORDER BY afmCategorie",
													//"form_options"=>array(),
                          "form_type"=>"selectKeyed",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('Zorgplicht',
													array("description"=>"Zorgplicht",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Zorgplicht, Zorgplicht FROM Zorgplichtcategorien GROUP BY Zorgplicht ORDER BY Zorgplicht",
													//"form_options"=>array(),
                          "form_type"=>"selectKeyed",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('Duurzaamheid',
													array("description"=>"Duurzaamheid",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Fonds',
										array("description"  => "Fonds",
													"db_size"      => "25",
													"db_type"      => "varchar",
													"form_size"    => "25",
													"form_type"    => "text",
													"form_visible" => true,
													"form_extra" => 'READONLY',
													"list_width" => "150",
													"list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => true,
													"list_order"   => "true",
													"keyIn"        =>"Fondsen"));

		$this->addField('OptieSymbool',
										array("description"  => "Symbool",
													"db_size"      => "5",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_size"    => "5",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('OptieType',
										array("description"  => "[P]ut/[C]all",
													"db_size"      => "1",
													"db_type"      => "varchar",
													"form_options" => array('P', 'C'),
													"form_type"    => "select",
													"form_size"    => "1",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('OptieExpDatum',
										array("description"  => "Expiratie datum",
													"db_size"      => "6",
													"db_type"      => "varchar",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('OptieUitoefenPrijs',
										array("description"  => "Uitoefenprijs",
													"db_size"      => "0",
													"db_type"      => "double",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"form_format"  => "%01.2f",
													"list_format"  => "%01.2f",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('standaardSector',
										array("description"   => "standaard sector",
													"db_size"       => "15",
													"default_value" => '',
													"db_type"       => "varchar",
													"form_size"     => "15",
													"form_type"     => "text",
													"form_extra"=>'READONLY',
												//	"select_query"  => "SELECT Beleggingssector,Omschrijving FROM Beleggingssectoren WHERE standaard=1 ORDER BY Beleggingssector",
													"form_visible"  => true, "list_width" => "150",
													"list_visible"  => true,
													"list_align"    => "left",
													"list_search"   => false,
													"list_order"    => "true",
													"keyIn"         => "Beurzen"));

		$this->addField('identifierVWD',
										array("description"  => "Identifier VWD",
													"db_size"      => "80",
													"db_type"      => "varchar",
													"form_size"    => "50",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('Fondseenheid',
										array("description"  => "Fondseenheid",
													"db_size"      => "0",
													"db_type"      => "double",
													"form_extra"=>'READONLY',
													"form_size"    => "8",
													"form_type"    => "text",
													"form_visible" => true, "list_width" => "150",
													"list_visible" => true,
													"list_align"   => "right",
													"list_search"  => false,
													"list_order"   => "true"));

		$this->addField('Beurs',
										array("description"   => "Beurs",
													"db_size"       => "4",
													"db_type"       => "varchar",
													"form_size"     => "12",
													"form_type"     => "text",
										//			"select_query"  => "SELECT Beurs,Beurs FROM Beurzen",
													"form_visible"  => true, "list_width" => "150",
													"form_extra"=>'READONLY',
													"default_value" => '',
													"list_visible"  => true,
													"list_align"    => "left",
													"list_search"   => false,
													"list_order"    => "true",
													"keyIn"         => "Beurzen"));

		$this->addField('OptieBovenliggendFonds',
										array("description"  => "Bovenliggend fonds",
													"db_size"      => "25",
													"db_type"      => "varchar",
													"form_size"    => "16",
													"form_visible" => true, "list_width" => "150",
													"form_type"    => "text",
												//	"select_query" => "SELECT Fonds,Fonds FROM Fondsen WHERE HeeftOptie > 0 order by Fonds",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => true,
													"list_order"   => "true",
													"keyIn"        => "Fondsen"));

		$this->addField('fondsnaam',
													array("description"=>"Fondsnaam",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                     
   $this->addField('Koers',
													array("description"=>"Koers",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_format"=>"%01.8f",
													"list_format"=>"%01.4f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('koersdatum',
													array("description"=>"Koersdatum",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"default_value"=>"lastworkday",
													"form_type"=>"calendar",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('overigeInfo',
													array("description"=>"Overige info",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
 
 if($__appvar['bedrijf']=='HOME')
   $verwerktZichtbaar=true;
 else
   $verwerktZichtbaar=false;  
                      
		$this->addField('verwerkt',
													array("description"=>"verwerkt",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
                          "form_options"=>array(-1=>'Verwijderd',0=>'Onverwerkt',1=>'Automatisch verwerkt',2=>'Handmatig verwerkt'),
													"form_size"=>"3",
													"form_visible"=>$verwerktZichtbaar,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('emailAdres',
													array("description"=>"emailAdres",
													"default_value"=>$_SESSION['usersession']['gebruiker']['emailAdres'],
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
                          "form_extra"=>'READONLY',
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>