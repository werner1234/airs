<?php
/*
    AE-ICT sourcemodule created 09 feb. 2022
    Author              : Chris van Santen
    Filename            : documentVerwerking.php


*/

class documentVerwerking
{
  function documentVerwerking()
  {
  
  }
  
  function createUpdate($vermogensbeheerder, $documenten)
  {
    global $__appvar, $USR, $ftpSettings;

    $updateUser = ($USR == '')?'systeem':$USR;

    $db       = new DB();
    $query    = "
      SELECT VermogensbeheerdersPerBedrijf.Bedrijf
      FROM VermogensbeheerdersPerBedrijf
      WHERE VermogensbeheerdersPerBedrijf.Vermogensbeheerder = '$vermogensbeheerder'
      ORDER BY VermogensbeheerdersPerBedrijf.Bedrijf";
    $db->executeQuery($query);

    $bedrijven = array();
    while ($data = $db->nextRecord())
    {
      $bedrijven[$data['Bedrijf']] = $documenten;
    }

    foreach ($bedrijven as $bedrijf => $documentData)
    {
      $exportId   = date("YmdHis") . '_' . rand(1000, 9999);
      $tofile     = "export_" . $bedrijf . "_" . $exportId . ".sql";
      $vulling    = "select '93eca1cd0a52f27c8ef5c721c8d562cd8bfb8f2b6f7096ff784e3a51003a38d354dea9844717261e38eef39bbc6f973b8ae832743921ec875c4c7b0fcd5b450b35cd4b8ce4618b0d57ea8fcde22859085add417cc9b8d8f623c8b36f1dbe94cafa119f5b9cac9c1b4588e955a1224dfc0f8f78b938319f5c0a52ab07f13fd2db416216de0eb2e9442bc289dcf4d7c8e457218d8f632ccb5d7f76456a6084bf76a62c56905aead315126fe460eb72a55d8a7d52bd49794406bea4acc6d941133598a3908176a594e589f4d0fb992d85167f415f86d69ff2bfeabd2cf4ff402996e3b916595eab3de79ede2373a664dbaa8ad407d4c323c36ef6929eda952828cb99e569442250e23031196b986625401266be5e98fb948763be73cff6991b8f7e84d3a1d4b5d25cf3c9e3461de47c79ca65f963ae6faf859674f9f22df1da6b2be7a668f8035cc1e692dfdb1f49856522d90382788912d8fe0cdb2beea586fa8d3d63752a271742f6d242357b7632bae5a933f87221f53d4dd3d2b4ce5fe221c502c1ec1a14e2ec5723340e9664b97c0ded6460f69ad4d2d71001ebbfe40d74e6cf6fd443ed2b77111a0d75d36973bcdd3b177fd44dff28d31e05b9ed56bab19a97dd1198f70f1eca78726b36462b5db718828807a308dacfe94ae553f6fefd73cf1513e6eee58e1498f2774ccde8d5f5684f25f6fb34de4518313a162dade543af23499d7d7b29fc928ec9610c08a78fb06e9e44b4ffd7c8912aa36f8ede61a368322d34c2d65dad6b6d6df6e67ca55386899fd5851b5e98c5948fb27aaae8fdccf5318dcb8f3a35fda72be323d135a959d57edef0dc75b570466eaaf4ee77a43187da74143a4e96e86b3fbcb5989032767fdec347df09b679ee94e182b661f39d617cb149eb375529f39c2823b371482235c612d12db0b97bc9e5e2e81ef0499aa19285d8e7afcaf0714b2ba84d74cb833add53678dce355f1c53deb486a955182e2c100def6f04e411af78e8ec111c99d642f2a70127f61d8bd708c4318e15b47c5b2424a73446569d3f42ae4bcc747ecf2b7fe7017d52d876070a041cbe84d25c50707f94d6e966c9124984da9070cb476e3b5e4268e6a3d886e4a36d31c9798239f815ee17cd7f3c55bcd55cb979c38d3fb7b3ae11e12662b5d3347e4cb6b744ccc524479c16bf7e361c27afefb4cf97587d994993f974e12cbcd5e8083a24b48c692e4dd9edac01009c39fd9686f6bccf9fa6d35f51f0c17e81431fcd8ce65e6fc3fd4982fde5bcf97a41ee864ebc25f5ca5be9ea5ba967dced87a8786d6e82f330b2100146df98fafe715fea33af26c95db55f084bdcc704474e9b17ad1e17e0927d1510076a5ae5cbaacfff49d6a8978c078fc7d446cc1b85572322553b025e52d' as niets ;\n";
      if ($fp = fopen($__appvar['tempdir'] . $tofile, 'w'))
      {
        fwrite($fp, $vulling);
      }
      else
      {
        $errorArray[] = "<br>\n FOUT: openen van " . $__appvar['tempdir'] . $tofile . " mislukt.";
      }

      foreach ($documentData as $velden)
      {
        if ($velden['filename'] <>'')
        {
          $rec = serialize($velden);
          // insert Into Queue
          $q2  = "INSERT INTO importdata SET Bedrijf = '" . $bedrijf . "', tableName = 'document', tableId = '-1', tableData = '" . mysql_escape_string($rec) . "', exportId = '" . $exportId . "', ";
          $q2 .= " add_user = '" . $updateUser . "', add_date = NOW() , change_user = '" . $updateUser . "', change_date = NOW() ;\n";
          fwrite($fp, $q2);
        }
      }
      fclose($fp);
      
      if (!$this->gzcompressdatafile($__appvar['tempdir'] . $tofile))
      {
        $errorArray[] = "Fout: zippen van bestand mislukt!";
      }
      unlink($__appvar['tempdir'] . $tofile);

      if (empty($errorArray))
      {
        if ($conn_id = ftp_connect($ftpSettings['server']))// login with username and password
        {
          if ($login_result = ftp_login($conn_id, $ftpSettings['user'], $ftpSettings['password']))
          {
            if ($__appvar["ftpPasv"])
            {
              echo logTxt("Ftp pasv set.");
              ftp_pasv($conn_id, true);
            }
            if (ftp_put($conn_id, $tofile . ".gz", $__appvar['tempdir'] . $tofile . ".gz", FTP_BINARY))
            {
              echo "<br>\n successfully uploaded $tofile\n";
            }
            else
            {
              $errorArray[] = "There was a problem while uploading $tofile.gz";
            }
          }
          ftp_close($conn_id);
        }
        else
        {
          $errorArray[] = "Could not connect to ftp server";
        }
      }
      
      if (empty($errorArray))
      {
        $filesize = filesize($__appvar['tempdir'] . $tofile . ".gz");
        $query    = "INSERT INTO updates SET exportId = '" . $exportId . "', Bedrijf = '" . $bedrijf . "', type = 'documenten', jaar = '" . date('Y') . "', filename = '" . $tofile . ".gz', filesize = '" . $filesize . "',
	                  server = '" . $ftpSettings['server'] . "', username = '" . $ftpSettings['user'] . "', password = '" . $ftpSettings['password'] . "', consistentie = '', add_date = NOW(), add_user = '" . $updateUser . "',
	                  change_date = NOW(), change_user = '" . $updateUser . "' ";
        $queueDB = new DB(2);
        $queueDB->SQL($query);
        if ($queueDB->Query())
        {
          echo "<br>\nUpdate in queue geplaatst om " . date('d-m-y H:i') . ".";
          unlink($__appvar['tempdir'] . $tofile . ".gz");
        }
      }
      else
      {
        listarray($errorArray);
        exit;
      }
    }
    
    
  }
  
  function gzcompressdatafile($source, $level = false)
  {
    $dest = $source . '.gz';
    $mode = 'wb' . $level;
    $error = false;
    if ($fp_out = gzopen($dest, $mode))
    {
      if ($fp_in = fopen($source, 'r'))
      {
        while (!feof($fp_in))
        {
          gzwrite($fp_out, fread($fp_in, 1024 * 512));
        }
        fclose($fp_in);
      }
      else
      {
        $error = true;
      }
      gzclose($fp_out);
    }
    else
    {
      $error = true;
    }
    if ($error)
    {
      return false;
    }
    else
    {
      return $dest;
    }
  }
  
  function importUpdate($Bedrijf,$exportId,$queueData)
  {
    global $USR, $__appvar;
    $insertUser   = ($USR == '')?'systeem':$USR;
    $melding      = '';
    $verslag      = array();
    $emailVerslag = "teamairs@useblanco.com";
//    $emailVerslag = "cvs@aeict.nl";
    $db           = new DB();
    $dbCrm        = new DB();
    $query        = "SELECT * FROM importdata WHERE Bedrijf = '".$Bedrijf."' AND exportId = '".$exportId."'";
    $db->executeQuery($query);
    $aantal       = $db->Records();
    $melding     .= "\nImport documenten ( '$aantal' records). $Bedrijf $exportId";
    $dirName      = $__appvar["basedir"]."/temp/importDoc";
    if (!is_dir("$dirName"))
    {
      mkdir($dirName, 0777, true);
    }

    while($update = $db->NextRecord())
    {
      $data       = unserialize($update['tableData']);

      $localFile  = $dirName."/".$data["filename"];

      file_put_contents($localFile, base64_decode($data["docdata"]));



      $input = strip_tags($data["portefeuille"]);
      $input = preg_replace('/[^a-zA-Z0-9-_, ]+/', '', $input);
      $portefeuille = substr($input,0,26);
      $filename     = $localFile;
      $filesize     = filesize($localFile);
      $filetype     = mime_content_type($localFile);
      $fileHandle   = fopen($filename, "r");
      $docdata      = fread($fileHandle, $filesize);
      fclose($fileHandle);

      if ($filesize == 0)
      {
        logit("Digidoc::document overgeslagen (leeg bestand) bij relatie CRM_naw id {$rel_id}" );
        echo "<li> document <b>$file</b> overgeslagen (leeg bestand)</li>";
        continue;
      }

      $query = "SELECT id, naam FROM `CRM_naw` WHERE `portefeuille` = '{$portefeuille}' ";
      if (!$crmRec = $dbCrm->lookupRecordByQuery($query))
      {
        $verslag["failed"][] = "Portefeuille {$portefeuille} niet gekoppeld aan CRM_NAW, bestand: {$file}";
        continue;
      }



      $fsize  = round($filesize/1024,1)."Kb";
      $dd     = new digidoc();
      $rec    = array();
      $rec ["filename"]     = cnvFilename($data["filename"]);
      $rec ["filesize"]     = "$filesize";
      $rec ["filetype"]     = "$filetype";
      $rec ["description"]  = $data["desc"];
      $rec ["blobdata"]     = $docdata;
      $rec ["keywords"]     = "";
      $rec ["module"]       = 'CRM_naw';
      $rec ["module_id"]    = $crmRec["id"];
      $rec ["categorie"]    = $data["categorie"];
      $rec ["keywords"]     = "";
      $rel_id               = $crmRec["id"];
      $dd->useZlib = false;
      if (!$dd->addDocumentToStore($rec))
      {
        logit("Digidoc::afgebroken door foutmelding bij relatie CRM_naw id {$rel_id}" );
        echo "<br> afgebroken door foutmelding..";
        exit;
      }
      $ddRefId = $dd->referenceId;
      logit("Digidoc::document {$file} (id={$ddRefId}, {$fsize}) toegevoegd aan relatie CRM_naw id {$rel_id}" );
      echo "<li> document <b>$file ($fsize)</b> toegevoegd aan relatie</li>";
      $verslag["success"][] = "Portefeuille {$portefeuille} <b>$file ($fsize)</b> toegevoegd aan relatie";

      if($data['portaal'] == 1)
      {
        $dbHost = new DB();
        $query  = "SELECT portefeuille, CRMGebrNaam FROM CRM_naw WHERE id='".$rel_id."'";
        $CRMRec = $dbHost->lookupRecordByQuery($query);

        if(trim($CRMRec['portefeuille']) == '' AND $CRMRec['CRMGebrNaam'] != '')
        {
          $CRMRec['portefeuille'] ='P'.str_pad($CRMRec['CRMGebrNaam'], 6, '0', STR_PAD_LEFT);
        }

        $airsRefId  = $dd->referenceId;
        $dbPort     = new DB(DBportaal);
        $query      = "SELECT id FROM clienten WHERE portefeuille='".$CRMRec["portefeuille"]."'";

        if ($clntRec = $dbPort->lookupRecordByQuery($query))
        {
          $dd                 = new digidoc(DBportaal);
          $dd->useZlib        = false;
          $rec ["module_id"]  = $clntRec["id"];
          $rec ["module"]     = 'clienten';
          $extraVelden        = array('portaalKoppelId'=>$airsRefId,'reportDate'=>date('Y-m-d'),'clientID'=>$clntRec["id"]);
          if($dd->addDocumentToStore($rec,$extraVelden) == false)
          {
            logit("Digidoc::Niet gelukt om document in de portaal te plaatsen bij relatie CRM_naw id {$rel_id}" );
            echo "Niet gelukt om document in de portaal te plaatsen.<br>\n";flush(); ob_flush();
            $verslag["success"][] = "Portefeuille {$portefeuille} Niet gelukt om document in de portaal te plaatsen, bestand: {$file}";
          }
          else
          {
            logit("Digidoc::document {$file}  toegevoegd aan portaal relatie CRM_naw id {$rel_id}" );
            echo "<li> document <b>$file</b> toegevoegd in het portaal</li>";
            $verslag["success"][] = "Portefeuille {$portefeuille} document <b>$file</b> toegevoegd in het portaal";
          }

          $query = "UPDATE dd_reference SET  portaalKoppelId ='{$dd->referenceId}' WHERE id = {$airsRefId}";
          $dbU->executeQuery($query);
        }
        else
        {
          logit("Digidoc::{$file}  Client/Portefeuille onbekend in portaal, document niet toegevoegd CRM_naw id {$rel_id}" );
        }


      }

      unlink($filename);
    }
    $cfg        = new AE_config();
    $mailserver = $cfg->getData('smtpServer');
    $body       = "
    Verslag Documentenupdate voor {$__appvar["bedrijf"]}
    Tijdstip: ".date("d-m-Y om H:i")."u.
    <b>succesvolle acties:</b>
    <ul>
    ".implode(",\n", $verslag["success"])."
    </ul>
    <b>mislukte acties:</b>
    <ul>
    ".implode(",\n", $verslag["failed"])."
    </ul>
    
    ";

    echo nl2br($body);
    if($mailserver <> '')
    {
      include_once('../classes/AE_cls_phpmailer.php');
      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->From     = "teamairs@useblanco.com";
      $mail->FromName = "Airs update ";
      $mail->Body     = nl2br($body);
      $mail->AltBody  = html_entity_decode(strip_tags($body));
      $mail->AddAddress($emailVerslag);
      $mail->Subject  = "Verslag Documentenupdate voor {$__appvar["bedrijf"]}";
      $mail->Host     = $mailserver;

      if(!$mail->Send())
      {
        echo "Verzenden van e-mail mislukt.";
      }
      else
      {
        echo "E-mail verzonden.";
      }
    }
    return $melding;
  }
  
  
//  function testExport()
//  {
//    $vermogensbeheerder='ANO';
//    $documenten=array();
//
//    $documenten[]=array('portaal'=>1, 'filename'=>'test1.pdf','portefeuille'=>'TGB_1122333','docdata'=>base64_decode('JVBERi0xLjQKJcfsj6IKNSAwIG9iago8PC9MZW5ndGggNiAwIFIvRmlsdGVyIC9GbGF0ZURlY29kZT4+CnN0cmVhbQp4nK1TTW8UMQy951f4OHNosJ3YSTgiIaTeWuaGOEDbXYFYRLdFlfj1OPORjNBeKrGjmfU8x45f3ptHQE8MWK8tuDu5N7cJjk+OoF7noyMS0BzYa4KYOIMWOD+4g5sL64pHl32ovxnYx3cneDdZxwzJU1ErnqzOl1JiXlbYJtFHyExeYTq5T8PPkZNHDTw8jMzeth9eKsQcaYCRyKMkGcKW5FFEP0/X7v3kbvrYEam0uTMHsL//OXbM7C2xG/zPSOpJVYcvy2xBhueKIaa8kUk6nEfxnNA43C9UWbYKyx4btnKNaZCW7djhQuevrbbv8fvCHj37rfXrnbmtwxbxhahX0IWKt22+Pn2uUhkYdnK92H1t9/dVmNsPq+NIqnInF3MM7fWH+/iPMxPq7ExBKLwoLNrxHaz75cypJV7hCLJ2vmjzQ8i62Th4cwMme85ueB6v2KeMZPps0dMcpVJoTc8h9PDXGko0bzT0sDZQsZNWxHZ6IaIxEqqM5jOpbNrxcAwF7Ny9qk1GkOT1dNlgFNkxjvZZLYy52LeVwYQpjLywvjJnEZJkmzR6CaEEw7rcJgGba+pEq7Lba1X2xv0F+SLr+2VuZHN0cmVhbQplbmRvYmoKNiAwIG9iago0NDkKZW5kb2JqCjQgMCBvYmoKPDwvVHlwZS9QYWdlL01lZGlhQm94IFswIDAgNTk1IDg0Ml0KL1JvdGF0ZSAwL1BhcmVudCAzIDAgUgovUmVzb3VyY2VzPDwvUHJvY1NldFsvUERGIC9UZXh0XQovRXh0R1N0YXRlIDE0IDAgUgovRm9udCAxNSAwIFIKPj4KL0NvbnRlbnRzIDUgMCBSCj4+CmVuZG9iagozIDAgb2JqCjw8IC9UeXBlIC9QYWdlcyAvS2lkcyBbCjQgMCBSCl0gL0NvdW50IDEKL1JvdGF0ZSAwPj4KZW5kb2JqCjEgMCBvYmoKPDwvVHlwZSAvQ2F0YWxvZyAvUGFnZXMgMyAwIFIKL01ldGFkYXRhIDE5IDAgUgo+PgplbmRvYmoKNyAwIG9iago8PC9UeXBlL0V4dEdTdGF0ZQovT1BNIDE+PmVuZG9iagoxNCAwIG9iago8PC9SNwo3IDAgUj4+CmVuZG9iagoxNSAwIG9iago8PC9SOAo4IDAgUi9SMTAKMTAgMCBSL1IxMgoxMiAwIFI+PgplbmRvYmoKOCAwIG9iago8PC9CYXNlRm9udC9BWVRCT0YrSGVsdmV0aWNhLUJvbGQvRm9udERlc2NyaXB0b3IgOSAwIFIvVHlwZS9Gb250Ci9GaXJzdENoYXIgMzIvTGFzdENoYXIgMTIyL1dpZHRoc1sKMjc4IDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwCjU1NiA1NTYgNTU2IDU1NiAwIDU1NiAwIDAgNTU2IDAgMzMzIDAgMCAwIDAgMAowIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwCjAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAKMCA1NTYgNjExIDAgNjExIDU1NiAzMzMgNjExIDAgMjc4IDAgMCAwIDAgNjExIDAKMCAwIDM4OSAwIDMzMyA2MTEgMCA3NzggMCAwIDUwMF0KL0VuY29kaW5nL1dpbkFuc2lFbmNvZGluZy9TdWJ0eXBlL1R5cGUxPj4KZW5kb2JqCjEwIDAgb2JqCjw8L0Jhc2VGb250L1NJUEVIRytDb3VyaWVyL0ZvbnREZXNjcmlwdG9yIDExIDAgUi9UeXBlL0ZvbnQKL0ZpcnN0Q2hhciAzMi9MYXN0Q2hhciAxMTYvV2lkdGhzWwo2MDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAKMCA2MDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwCjAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAKMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMAowIDAgMCAwIDYwMCA2MDAgNjAwIDAgMCAwIDAgMCAwIDAgMCAwCjYwMCAwIDAgNjAwIDYwMF0KL0VuY29kaW5nL1dpbkFuc2lFbmNvZGluZy9TdWJ0eXBlL1R5cGUxPj4KZW5kb2JqCjEyIDAgb2JqCjw8L0Jhc2VGb250L0NOTE1ZQytIZWx2ZXRpY2EvRm9udERlc2NyaXB0b3IgMTMgMCBSL1R5cGUvRm9udAovRmlyc3RDaGFyIDQ1L0xhc3RDaGFyIDQ5L1dpZHRoc1sgMzMzIDAg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OnJkZj0naHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIycgeG1sbnM6aVg9J2h0dHA6Ly9ucy5hZG9iZS5jb20vaVgvMS4wLyc+CjxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSd1dWlkOmJkMGM2YWU3LTg4YzUtMTFlYy0wMDAwLWUyMDhiYTA3NmI1YicgeG1sbnM6cGRmPSdodHRwOi8vbnMuYWRvYmUuY29tL3BkZi8xLjMvJyBwZGY6UHJvZHVjZXI9J0dQTCBHaG9zdHNjcmlwdCA5LjA2Jy8+CjxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSd1dWlkOmJkMGM2YWU3LTg4YzUtMTFlYy0wMDAwLWUyMDhiYTA3NmI1YicgeG1sbnM6eG1wPSdodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvJz48eG1wOk1vZGlmeURhdGU+MjAyMi0wMi0wNVQxMDo1ODo1OSswMTowMDwveG1wOk1vZGlmeURhdGU+Cjx4bXA6Q3JlYXRlRGF0ZT4yMDIyLTAyLTA1VDEwOjU4OjU5KzAxOjAwPC94bXA6Q3JlYXRlRGF0ZT4KPHhtcDpDcmVhdG9yVG9vbD5QU2NyaXB0NS5kbGwgVmVyc2lvbiA1LjIuMjwveG1wOkNyZWF0b3JUb29sPjwvcmRmOkRlc2NyaXB0aW9uPgo8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0ndXVpZDpiZDBjNmFlNy04OGM1LTExZWMtMDAwMC1lMjA4YmEwNzZiNWInIHhtbG5zOnhhcE1NPSdodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vJyB4YXBNTTpEb2N1bWVudElEPSd1dWlkOmJkMGM2YWU3LTg4YzUtMTFlYy0wMDAwLWUyMDhiYTA3NmI1YicvPgo8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0ndXVpZDpiZDBjNmFlNy04OGM1LTExZWMtMDAwMC1lMjA4YmEwNzZiNWInIHhtbG5zOmRjPSdodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLycgZGM6Zm9ybWF0PSdhcHBsaWNhdGlvbi9wZGYnPjxkYzp0aXRsZT48cmRmOkFsdD48cmRmOmxpIHhtbDpsYW5nPSd4LWRlZmF1bHQnPm5ldyAzMjwvcmRmOmxpPjwvcmRmOkFsdD48L2RjOnRpdGxlPjxkYzpjcmVhdG9yPjxyZGY6U2VxPjxyZGY6bGk+cnZ2PC9yZGY6bGk+PC9yZGY6U2VxPjwvZGM6Y3JlYXRvcj48L3JkZjpEZXNjcmlwdGlvbj4KPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAo8P3hwYWNrZXQgZW5kPSd3Jz8+CmVuZHN0cmVhbQplbmRvYmoKMiAwIG9iago8PC9Qcm9kdWNlcihHUEwgR2hvc3RzY3JpcHQgOS4wNikKL0NyZWF0aW9uRGF0ZShEOjIwMjIwMjA1MTA1ODU5KzAxJzAwJykKL01vZERhdGUoRDoyMDIyMDIwNTEwNTg1OSswMScwMCcpCi9UaXRsZShuZXcgMzIpCi9DcmVhdG9yKFBTY3JpcHQ1LmRsbCBWZXJzaW9uIDUuMi4yKQovQXV0aG9yKHJ2dik+PmVuZG9iagp4cmVmCjAgMjAKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwNzgxIDAwMDAwIG4gCjAwMDAwMDc1NDggMDAwMDAgbiAKMDAwMDAwMDcxMyAwMDAwMCBuIAowMDAwMDAwNTUzIDAwMDAwIG4gCjAwMDAwMDAwMTUgMDAwMDAgbiAKMDAwMDAwMDUzNCAwMDAwMCBuIAowMDAwMDAwODQ2IDAwMDAwIG4gCjAwMDAwMDA5NjkgMDAwMDAgbiAKMDAwMDAwMTg1OCAwMDAwMCBuIAowMDAwMDAxMzUxIDAwMDAwIG4gCjAwMDAwMDQwOTcgMDAwMDAgbiAKMDAwMDAwMTY5MCAwMDAwMCBuIAowMDAwMDA1NDQ3IDAwMDAwIG4gCjAwMDAwMDA4ODcgMDAwMDAgbiAKMDAwMDAwMDkxNyAwMDAwMCBuIAowMDAwMDAyMjAwIDAwMDAwIG4gCjAwMDAwMDQ0MTQgMDAwMDAgbiAKMDAwMDAwNTcxMSAwMDAwMCBuIAowMDAwMDA2MDU3IDAwMDAwIG4gCnRyYWlsZXIKPDwgL1NpemUgMjAgL1Jvb3QgMSAwIFIgL0luZm8gMiAwIFIKL0lEIFs8QTBCNTg3RkY4MkZFOTY0Qjk4QkE3OEUyRkE4QkNDNTc+PEEwQjU4N0ZGODJGRTk2NEI5OEJBNzhFMkZBOEJDQzU3Pl0KPj4Kc3RhcnR4cmVmCjc3MzYKJSVFT0YK'));
//    $documenten[]=array('portaal'=>0, 'filename'=>'test2.pdf','portefeuille'=>'TGB_1122333','docdata'=>base64_decode('JVBERi0xLjQKJcfsj6IKNSAwIG9iago8PC9MZW5ndGggNiAwIFIvRmlsdGVyIC9GbGF0ZURlY29kZT4+CnN0cmVhbQp4nK1TTW8UMQy951f4OHNosJ3YSTgiIaTeWuaGOEDbXYFYRLdFlfj1OPORjNBeKrGjmfU8x45f3ptHQE8MWK8tuDu5N7cJjk+OoF7noyMS0BzYa4KYOIMWOD+4g5sL64pHl32ovxnYx3cneDdZxwzJU1ErnqzOl1JiXlbYJtFHyExeYTq5T8PPkZNHDTw8jMzeth9eKsQcaYCRyKMkGcKW5FFEP0/X7v3kbvrYEam0uTMHsL//OXbM7C2xG/zPSOpJVYcvy2xBhueKIaa8kUk6nEfxnNA43C9UWbYKyx4btnKNaZCW7djhQuevrbbv8fvCHj37rfXrnbmtwxbxhahX0IWKt22+Pn2pUhkYdnK92H1t9/dVmNsPq+NIqnInF3MM7fWH+/iPMxPq7ExBKLwoLNrxHaz75cypJV7hCLJ2vmjzQ8i62Th4cwMme85ueB6v2KeMZPps0dMcpVJoTc8h9PDXGko0bzT0sDbQanZFbKcXIhojocpoPpPKph0Px1DAzt2r2mQESV5Plw1GkR3jaJ/VwpiLfVsZTJjCyAvrK3MWIUk2T0QvIZRgWJfbJGBzTZ1oVXZ7rcreuL/8JOv9ZW5kc3RyZWFtCmVuZG9iago2IDAgb2JqCjQ0OAplbmRvYmoKNCAwIG9iago8PC9UeXBlL1BhZ2UvTWVkaWFCb3ggWzAgMCA1OTUgODQyXQovUm90YXRlIDAvUGFyZW50IDMgMCBSCi9SZXNvdXJjZXM8PC9Qcm9jU2V0Wy9QREYgL1RleHRdCi9FeHRHU3RhdGUgMTQgMCBSCi9Gb250IDE1IDAgUgo+PgovQ29udGVudHMgNSAwIFIKPj4KZW5kb2JqCjMgMCBvYmoKPDwgL1R5cGUgL1BhZ2VzIC9LaWRzIFsKNCAwIFIKXSAvQ291bnQgMQovUm90YXRlIDA+PgplbmRvYmoKMSAwIG9iago8PC9UeXBlIC9DYXRhbG9nIC9QYWdlcyAzIDAgUgovTWV0YWRhdGEgMTkgMCBSCj4+CmVuZG9iago3IDAgb2JqCjw8L1R5cGUvRXh0R1N0YXRlCi9PUE0gMT4+ZW5kb2JqCjE0IDAgb2JqCjw8L1I3CjcgMCBSPj4KZW5kb2JqCjE1IDAgb2JqCjw8L1I4CjggMCBSL1IxMAoxMCAwIFIvUjEyCjEyIDAgUj4+CmVuZG9iago4IDAgb2JqCjw8L0Jhc2VGb250L0dCTllKQytIZWx2ZXRpY2EtQm9sZC9Gb250RGVzY3JpcHRvciA5IDAgUi9UeXBlL0ZvbnQKL0ZpcnN0Q2hhciAzMi9MYXN0Q2hhciAxMjIvV2lkdGhzWwoyNzggMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAKNTU2IDU1NiA1NTYgNTU2IDAgNTU2IDAgMCAwIDU1NiAzMzMgMCAwIDAgMCAwCjAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAKMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMAowIDU1NiA2MTEgMCA2MTEgNTU2IDMzMyA2MTEgMCAyNzggMCAwIDAgMCA2MTEgMAowIDAgMzg5IDAgMzMzIDYxMSAwIDc3OCAwIDAgNTAwXQovRW5jb2RpbmcvV2luQW5zaUVuY29kaW5nL1N1YnR5cGUvVHlwZTE+PgplbmRvYmoKMTAgMCBvYmoKPDwvQmFzZUZvbnQvWUxLTUhDK0NvdXJpZXIvRm9udERlc2NyaXB0b3IgMTEgMCBSL1R5cGUvRm9udAovRmlyc3RDaGFyIDMyL0xhc3RDaGFyIDExNi9XaWR0aHNbCjYwMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMAowIDAgNjAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAKMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMAowIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwCjAgMCAwIDAgNjAwIDYwMCA2MDAgMCAwIDAgMCAwIDAgMCAwIDAKNjAwIDAgMCA2MDAgNjAwXQovRW5jb2RpbmcvV2luQW5zaUVuY29kaW5nL1N1YnR5cGUvVHlwZTE+PgplbmRvYmoKMTIgMCBvYmoKPDwvQmFzZUZvbnQvQ05MTVlDK0hlbHZldGljYS9Gb250RGVzY3JpcHRvciAxMyAwIFIvVHlwZS9Gb250Ci9GaXJzdENoYXIgNDUvTGFzdENoYXIgNDkvV2lkdGhzWyAzMzMgMCAw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YW1ld29yayAxLjYnPgo8cmRmOlJERiB4bWxuczpyZGY9J2h0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMnIHhtbG5zOmlYPSdodHRwOi8vbnMuYWRvYmUuY29tL2lYLzEuMC8nPgo8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0ndXVpZDpkZGQ0YzA2Ny04OGM1LTExZWMtMDAwMC05MzllM2Y2NjdkNmYnIHhtbG5zOnBkZj0naHR0cDovL25zLmFkb2JlLmNvbS9wZGYvMS4zLycgcGRmOlByb2R1Y2VyPSdHUEwgR2hvc3RzY3JpcHQgOS4wNicvPgo8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0ndXVpZDpkZGQ0YzA2Ny04OGM1LTExZWMtMDAwMC05MzllM2Y2NjdkNmYnIHhtbG5zOnhtcD0naHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyc+PHhtcDpNb2RpZnlEYXRlPjIwMjItMDItMDVUMTA6NTk6NTQrMDE6MDA8L3htcDpNb2RpZnlEYXRlPgo8eG1wOkNyZWF0ZURhdGU+MjAyMi0wMi0wNVQxMDo1OTo1NCswMTowMDwveG1wOkNyZWF0ZURhdGU+Cjx4bXA6Q3JlYXRvclRvb2w+UFNjcmlwdDUuZGxsIFZlcnNpb24gNS4yLjI8L3htcDpDcmVhdG9yVG9vbD48L3JkZjpEZXNjcmlwdGlvbj4KPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9J3V1aWQ6ZGRkNGMwNjctODhjNS0xMWVjLTAwMDAtOTM5ZTNmNjY3ZDZmJyB4bWxuczp4YXBNTT0naHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLycgeGFwTU06RG9jdW1lbnRJRD0ndXVpZDpkZGQ0YzA2Ny04OGM1LTExZWMtMDAwMC05MzllM2Y2NjdkNmYnLz4KPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9J3V1aWQ6ZGRkNGMwNjctODhjNS0xMWVjLTAwMDAtOTM5ZTNmNjY3ZDZmJyB4bWxuczpkYz0naHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8nIGRjOmZvcm1hdD0nYXBwbGljYXRpb24vcGRmJz48ZGM6dGl0bGU+PHJkZjpBbHQ+PHJkZjpsaSB4bWw6bGFuZz0neC1kZWZhdWx0Jz5uZXcgMzI8L3JkZjpsaT48L3JkZjpBbHQ+PC9kYzp0aXRsZT48ZGM6Y3JlYXRvcj48cmRmOlNlcT48cmRmOmxpPnJ2djwvcmRmOmxpPjwvcmRmOlNlcT48L2RjOmNyZWF0b3I+PC9yZGY6RGVzY3JpcHRpb24+CjwvcmRmOlJERj4KPC94OnhtcG1ldGE+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKPD94cGFja2V0IGVuZD0ndyc/PgplbmRzdHJlYW0KZW5kb2JqCjIgMCBvYmoKPDwvUHJvZHVjZXIoR1BMIEdob3N0c2NyaXB0IDkuMDYpCi9DcmVhdGlvbkRhdGUoRDoyMDIyMDIwNTEwNTk1NCswMScwMCcpCi9Nb2REYXRlKEQ6MjAyMjAyMDUxMDU5NTQrMDEnMDAnKQovVGl0bGUobmV3IDMyKQovQ3JlYXRvcihQU2NyaXB0NS5kbGwgVmVyc2lvbiA1LjIuMikKL0F1dGhvcihydnYpPj5lbmRvYmoKeHJlZgowIDIwCjAwMDAwMDAwMDAgNjU1MzUgZiAKMDAwMDAwMDc4MCAwMDAwMCBuIAowMDAwMDA3NTc2IDAwMDAwIG4gCjAwMDAwMDA3MTIgMDAwMDAgbiAKMDAwMDAwMDU1MiAwMDAwMCBuIAowMDAwMDAwMDE1IDAwMDAwIG4gCjAwMDAwMDA1MzMgMDAwMDAgbiAKMDAwMDAwMDg0NSAwMDAwMCBuIAowMDAwMDAwOTY4IDAwMDAwIG4gCjAwMDAwMDE4NTcgMDAwMDAgbiAKMDAwMDAwMTM1MCAwMDAwMCBuIAowMDAwMDA0MDcyIDAwMDAwIG4gCjAwMDAwMDE2ODkgMDAwMDAgbiAKMDAwMDAwNTQ3NSAwMDAwMCBuIAowMDAwMDAwODg2IDAwMDAwIG4gCjAwMDAwMDA5MTYgMDAwMDAgbiAKMDAwMDAwMjE5OCAwMDAwMCBuIAowMDAwMDA0Mzg5IDAwMDAwIG4gCjAwMDAwMDU3MzkgMDAwMDAgbiAKMDAwMDAwNjA4NSAwMDAwMCBuIAp0cmFpbGVyCjw8IC9TaXplIDIwIC9Sb290IDEgMCBSIC9JbmZvIDIgMCBSCi9JRCBbPEJCRkE3RTEzQzE2RkFFNzFCMUNENzdEMzBEQzhFQTM4PjxCQkZBN0UxM0MxNkZBRTcxQjFDRDc3RDMwREM4RUEzOD5dCj4+CnN0YXJ0eHJlZgo3NzY0CiUlRU9GCg=='));
//    $this->createUpdate($vermogensbeheerder, $documenten);
//  }
  
}