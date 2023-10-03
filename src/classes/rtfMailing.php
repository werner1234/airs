<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/12/20 06:45:10 $
 		File Versie					: $Revision: 1.39 $

 		$Log: rtfMailing.php,v $
 		Revision 1.39  2017/12/20 06:45:10  rvv
 		*** empty log message ***
 		
 		Revision 1.38  2017/12/03 10:31:39  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2017/10/04 16:06:28  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2017/09/16 17:53:02  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2017/09/13 09:57:58  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2017/03/27 06:30:10  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2016/01/23 17:56:49  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2015/04/19 08:40:22  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2015/02/22 09:51:14  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2014/10/02 05:48:38  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2014/10/01 16:03:39  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2014/07/02 16:01:36  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2013/12/04 16:26:12  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2013/08/24 15:45:43  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2013/05/12 11:13:40  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2012/12/16 09:56:32  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2012/10/07 14:53:02  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2012/08/05 10:41:34  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2012/06/03 09:41:10  rvv
 		*** empty log message ***

 		Revision 1.20  2012/05/23 10:47:24  rvv
 		*** empty log message ***

 		Revision 1.19  2011/09/18 15:47:08  rvv
 		*** empty log message ***

 		Revision 1.18  2011/06/29 16:58:27  rvv
 		*** empty log message ***

 		Revision 1.17  2011/05/25 17:23:06  rvv
 		*** empty log message ***

 		Revision 1.16  2011/03/31 06:53:09  rvv
 		*** empty log message ***

 		Revision 1.15  2011/03/31 06:46:42  rvv
 		*** empty log message ***

 		Revision 1.14  2011/03/30 20:23:03  rvv
 		*** empty log message ***

 		Revision 1.13  2011/02/24 17:23:43  rvv
 		*** empty log message ***

 		Revision 1.12  2010/11/14 10:52:20  rvv
 		*** empty log message ***

 		Revision 1.11  2010/10/21 16:06:57  rvv
 		rtf templates toegevoegd.

 		Revision 1.10  2010/07/31 16:03:06  rvv
 		*** empty log message ***

 		Revision 1.9  2009/12/08 18:18:12  rvv
 		*** empty log message ***

 		Revision 1.8  2009/12/06 15:54:34  rvv
 		*** empty log message ***

 		Revision 1.7  2009/11/22 14:03:00  rvv
 		*** empty log message ***

 		Revision 1.6  2009/11/15 17:12:37  rvv
 		*** empty log message ***

 		Revision 1.5  2009/11/15 16:44:20  rvv
 		*** empty log message ***

 		Revision 1.4  2009/11/08 14:04:56  rvv
 		*** empty log message ***

 		Revision 1.3  2009/10/25 08:58:11  rvv
 		*** empty log message ***

 		Revision 1.2  2009/10/17 12:49:55  rvv
 		*** empty log message ***

*/

class rtfMailing
{
  var $colour_table = array();
  var $info_table = array();

	function rtfMailing($xlsData,$extra)
	{
	  global $__appvar,$USR;
    $db=new DB();

	  $idsToUse=array();
	  foreach ($extra as $key=>$value)
	  {
	    if(substr($key,0,6)=='check_')
	    {
	      $idsToUse[]=$value;
	    }
	  }


	  foreach ($xlsData as $relatie=>$row)
	  {
	    $selectie=array();
      $tmp=array();
	    foreach ($row as $colId=>$colData)
	    {
	      switch ($colData[1])
	      {
	        case "header":
	          if(!isset($header[$colId]) && !in_array($header,$colData[0]))
	            $header[$colId]=$colData[0];
	        break;
	        case "body":
            if($tmp[$header[$colId]]=='')  
              $tmp[$header[$colId]]=$colData[0];
	        break;
	      }
 	    }
      
      if($tmp['id'] > 0)
      {
        $query="SELECT CRM_naw.id as crm_id, CRM_naw.* ,Portefeuilles.* FROM CRM_naw LEFT JOIN Portefeuilles ON CRM_naw.portefeuille=Portefeuilles.Portefeuille WHERE CRM_naw.id='".$tmp['id']."'";
        $db->SQL($query); 
        $tmp=$db->lookupRecord();
        $tmp['id']=$tmp['crm_id'];
      }
      $selectie=unserialize($tmp['rapportageVinkSelectie']);
    
   
      if(count($tmp) > 0)
      { 
        if($extra['rapportFilter']==1)
        {
          $toevoegen=false;
          if($extra['rapportEmail']==1 && $selectie['verzending']['rap_k']['email']==1)
            $toevoegen=true;
          if($extra['rapportPapier']==1 && $selectie['verzending']['rap_k']['papier']==1)
            $toevoegen=true;
          if(($extra['rapportPapier']==0 && $selectie['verzending']['rap_k']['papier']==0)&&($extra['rapportEmail']==0 && $selectie['verzending']['rap_k']['email']==0))
            $toevoegen=true;            
            
          if($toevoegen==true)
          {
            $templateData[$relatie]=$tmp;
						if($extra['extraAdressen']==1)
						{
							$extraAddressen=$this->AddExtraAdres($tmp);
							if(is_array($extraAddressen))
							{
								foreach($extraAddressen as $index=>$adres)
								{
									$newId=$relatie . "." . $index;
									$templateData[$newId] = $adres;
								}
							}
						}
          }
        }
        else
				{
					if($extra['extraAdressen']==1)
					{
						$extraAddressen=$this->AddExtraAdres($tmp);
						if(is_array($extraAddressen))
						{
							foreach($extraAddressen as $index=>$adres)
							{
								$newId=$relatie . "." . $index;
								$templateData[$newId] = $adres;
							}
						}
					}
					$templateData[$relatie] = $tmp;
				}
      }
	  }


	  if(count($idsToUse) > 0)
	  {
			$newTemplateData=array();
	    foreach ($templateData as $tmpId=>$relatieData)
	    {
	      if(in_array($relatieData['id'],$idsToUse))
	      {
	        $newTemplateData[$tmpId]=$relatieData;
	      }
	    }
	    $templateData=$newTemplateData;
			unset($newTemplateData);
	  }

	  $this->_POST=$extra;
	  $this->templateData = $templateData;

	  if($extra['evenement'])
	  {
	    $db=new DB();
	    foreach ($templateData as $relatieData)
	    {
	      $query="SELECT id FROM CRM_evenementen WHERE rel_id='".$relatieData['id']."' AND evenement='".$extra['evenement']."'";
	      $db->SQL($query);
	      $evenementData=$db->lookupRecord($query);
	      if($evenementData['id'] > 0)
	      {
	        $query="UPDATE CRM_evenementen SET change_date=NOW(),change_user='$USR' WHERE id='".$evenementData['id']."'";
	      }
	      else
	      {
	        $query="INSERT INTO CRM_evenementen SET rel_id='".$relatieData['id']."' , evenement='".$extra['evenement']."', add_date=NOW(), add_user='$USR', change_date=NOW(),change_user='$USR'";
	      }

	      $db->SQL($query);
	      $db->Query();
	    }
	  }

	  $db=new DB();
	  if(isset($this->_POST['mailing']) && $db->QRecords("SELECT id FROM CRM_naw_RtfTemplates WHERE naam ='".$this->_POST['mailing']."'"))
	  {
       include_once("../classes/AE_cls_rtfBase.php");
       $rtfMailing=true;
       $mailindData=$this->createRtfMailing();
	  }
	  elseif($db->QRecords("SELECT template FROM CRM_naw_RtfTemplates WHERE naam ='mailing.rtf'"))
	  {
	    //$tmp = file_get_contents ($__appvar['basedir']."/html/RTF_templates/mailing.rtf");
	    $db->SQL("SELECT template FROM CRM_naw_RtfTemplates WHERE naam ='mailing.rtf'");
	    $tmp=$db->lookupRecord();
	    $tmp=$tmp['template'];

	    $this->template=$tmp;
	    $this->template = substr($tmp,1,-1);
	    $mailindData=$this->createMailing();
	  }
	  else
	  {
	    $this->setPaperSize(5);
	    $tmp= $this->generateTemplate();
	    $this->template = substr($tmp,1,-1);
	    $mailindData=$this->createMailing();
	  }

		if(function_exists('mb_detect_encoding') && mb_detect_encoding($mailindData,'UTF-8',true))
		  $encoding='UTF-8';
		else
		  $encoding='cp1252';

//$encoding='ISO-8859-1';
		$mailindData=html_entity_decode($mailindData,ENT_QUOTES, $encoding);


	  if($extra['storeDD']) //Losse RTF bestanden opslaan.
	  {
	    include_once("../classes/AE_cls_digidoc.php");
	    foreach ($templateData as $templateId=>$relatieData)
	    {
	     if($rtfMailing)
	       $losseRTF=$this->createRtfMailing($templateId);
	     else
	       $losseRTF=$this->createMailing($templateId);

				$losseRTF=html_entity_decode($losseRTF,ENT_QUOTES, 'cp1252');

  	   $dd = new digidoc();
  
       if($extra['DDnaam']<>'')
         $extraFileName="_".$extra['DDnaam'];
       else
         $extraFileName=''; 

  	   if($relatieData['portefeuille'])
  	     $filename=$relatieData['portefeuille'].date("_Y-m-d_H:i").$extraFileName.".rtf";
  	   elseif($relatieData['id'])
  	     $filename="id".$relatieData['id'].date("_Y-m-d_H:i").$extraFileName.".rtf";
      
       $rec ["filename"] = $filename;
       $rec ["filesize"] = strlen($losseRTF);
       $rec ["filetype"] = "application/rtf";
       $rec ["description"] = rtrim("Mailing ".$extra['DDnaam'])." aangemaakt op ".date("d-m-Y H:i");
       $rec ["blobdata"] = $losseRTF;
       $rec ["keywords"] = rtrim("mailing ".$extra['DDnaam']);
       $rec ["module"] = "CRM_naw";
       $rec ["module_id"] = $relatieData['id'];
       $dd->useZlib = true;
       $dd->addDocumentToStore($rec);
	    }
	  }

	  $outputFilename='mailing.rtf';
	  header("Content-Type: application/rtf");
    header("Content-Length: ".strlen($mailindData));
    header("Content-disposition: attachment; filename=".$outputFilename);
	  echo $mailindData;
	  exit;
	}

	function AddExtraAdres($data)
	{
		$db=new DB();
		$query="SELECT CRM_naw_adressen.* FROM CRM_naw_adressen WHERE CRM_naw_adressen.rapportage=1 AND CRM_naw_adressen.rel_id='".$data['crm_id']."'";
		$db->SQL($query);
		$db->Query();
		if($db->records())
		{
			$adresData=array();
			while($extra=$db->nextRecord())
			{

				$extra['id']=$data['id'];
				$adresData[]=array_merge($data,$extra);
			}
			return $adresData;
		}
		else
			return 0;
		$tmp=$db->lookupRecord();
		$tmp['id']=$tmp['crm_id'];
	}

	function createRtfMailing($templateId='')
	{
		if($templateId == '')
	    $processData=$this->templateData;
	  else
	    $processData[$templateId]=$this->templateData[$templateId];

	  $rtfBase = new rtfBase($this->_POST['mailing']);
	  $rtfBody='';
	  foreach ($processData as $rel=>$keyValue)
	  {
	    $rtf->items=$this->getAllFields($keyValue);
	    $rtfBody .=$rtfBase->parseRTF();
    }
 	  return $rtfBody;

	}

	function createMailing($templateId='')
	{
	  $cfg=new AE_config();
	  if(!$this->newBody)
      if($this->_POST['action']=='preview')
      {
        $this->newBody=strip_tags($this->parseDocument($this->html_entity_decode_extra($this->_POST['body'])));
        $this->naw=strip_tags($this->parseDocument($this->html_entity_decode_extra($this->_POST['naw'])));
        $this->kop=strip_tags($this->parseDocument($this->html_entity_decode_extra($this->_POST['kop'])));
        $this->voet=strip_tags($this->parseDocument($this->html_entity_decode_extra($this->_POST['voet'])));
      }
	    elseif($this->_POST['mailing'])
        $this->newBody=strip_tags($this->parseDocument($this->html_entity_decode_extra($cfg->getData($this->_POST['mailing']))));
	    else
	      $this->newBody=strip_tags($this->parseDocument($this->html_entity_decode_extra($cfg->getData('mailingBody'))));

	  $nawRegelHoogte=$cfg->getData('nawRegelHoogte');
	  if(!$this->naw)
	  {
	    $tmp=$cfg->getData('mailingNaw_'.substr($this->_POST['mailing'],8));
	    if($tmp <> '')
	      $this->naw=strip_tags($this->parseDocument($this->html_entity_decode_extra(($tmp))));
	    else
	      $this->naw=strip_tags($this->parseDocument($this->html_entity_decode_extra($cfg->getData('mailingNaw'))));
	  }
	  if(!$this->kop)
	    $this->kop=strip_tags($this->parseDocument($this->html_entity_decode_extra($cfg->getData('mailingKop'))));
	  if(!$this->voet)
	    $this->voet=strip_tags($this->parseDocument($this->html_entity_decode_extra($cfg->getData('mailingVoet'))));
	  $mailingPart=$this->template;
	  $mailingPart = str_replace( "<<kop>>", $this->kop, $mailingPart);
	  $mailingPart = str_replace( "<<voet>>", $this->voet, $mailingPart);
//listarray($this->kop);
//listarray($this->_POST); listarray($this->newBody);
	  $pages=count($this->templateData);
	  $n=0;

	  if($templateId == '')
	    $processData=$this->templateData;
	  else
	  {
	    $processData[$templateId]=$this->templateData[$templateId];
	    $pages=1;
	  }

	  foreach ($processData as $rel=>$keyValue)
	  {
	    $keyValue=$this->getAllFields($keyValue);
	    $n++;

	    $newNaw=$this->naw;
	    $newBody=$this->newBody;

 	    foreach ($keyValue as $key=>$val)
      {
        $newNaw  = str_replace("{".$key."}", $val, $newNaw);
        $newBody = str_replace( "{".$key."}d0", number_format($val,0,',','.'), $newBody);
        $newBody = str_replace( "{".$key."}d2", number_format($val,2,',','.'), $newBody);
        $newBody = str_replace("{".$key."}", $val, $newBody);
      }

      $parts=explode("\par",$newNaw);
      $extraRegels=0;
      foreach ($parts as $nr=>$line)
      {
        $strippedLine=trim($line,chr(160).chr(32));
        $strippedLine=trim($strippedLine);
        if(strpos($strippedLine,'leegNietTonen}') == 1)
        {
          unset($parts[$nr]);
          $extraRegels++;
        }
      }
      for($x=0;$x<$extraRegels;$x++)
        $parts[]='';

      $newNaw=implode("\par",$parts);
      $parts=explode("\par",$newBody);
      foreach ($parts as $nr=>$line)
      {
        $strippedLine=trim($line,chr(160).chr(32));
        $strippedLine=trim($strippedLine);
        if(strpos($strippedLine,'leegNietTonen}') == 1)
          unset($parts[$nr]);
      }
      $newBody=implode("\par",$parts);


	    if($nawRegelHoogte=='1.5')
	      $body ="\pard \ltrpar\ql \sl360\slmult1\widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright {".$newNaw."} \par ";
	    else
	      $body ="\pard \ltrpar\ql \slmult1\widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright {".$newNaw."} \par ";
      $body.="\pard \ltrpar\ql \widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright {".$newBody."}";

      $body = str_replace("{leegNietTonen}", '', $body);
      $mailingBody .=  $body."\n";
      if($n <> $pages)
        $mailingBody.="\n\page\n";
	  }
	  $keyValue['body']=$mailingBody;
	 // listarray($mailingPart);
	  foreach ($keyValue as $key=>$val)
    {
      $mailing = str_replace( "<<".$key.">>", $val, $mailingPart);
      //$mailing = str_replace( "{".$key."}", $val, $mailing);
    }
    $mailing .= eregi_replace( "\<<[a-zA-Z0-9_-]+\>>","", $mailingPart);

	  $mailing.="}";
	  return '{'.$mailing;

	}

		// Convert special characters to ASCII
	function escapeCharacter($character)
	{
		$escaped = "";
		if(ord($character) >= 0x00 && ord($character) < 0x20)
			$escaped = "\\'".dechex(ord($character));

		if ((ord($character) >= 0x20 && ord($character) < 0x80) || ord($character) == 0x09 || ord($character) == 0x0A)
			$escaped = $character;

		if (ord($character) >= 0x80 and ord($character) < 0xFF)
			$escaped = "\\'".dechex(ord($character));

		switch(ord($character)) {
			case 0x5C:
			case 0x7B:
			case 0x7D:
				$escaped = "\\".$character;
				break;
		}

		return $escaped;
	}

	function specialCharacters($text)
	{
		$text_buffer = "";
		for($i = 0; $i < strlen($text); $i++)
			$text_buffer .= $this->escapeCharacter($text[$i]);
		return $text_buffer;
	}

	function nl2par($text)
	{
		$text = str_replace("\n", "\\par ", $text);
		return $text;
	}

	function parseDocument($bodyHtml)
	{
		$regels=count(explode("\n",$bodyHtml));
	  $doc_buffer = $bodyHtml;
		//$doc_buffer = $this->specialCharacters($bodyHtml);
		if(preg_match("/<UL>(.*?)<\/UL>/mi", $doc_buffer))
		{
			$doc_buffer = str_replace("<UL>", "", $doc_buffer);
			$doc_buffer = str_replace("</UL>", "", $doc_buffer);
			$doc_buffer = preg_replace("/<LI>(.*?)<\/LI>/mi", "\\f3\\'B7\\tab\\f{$this->font_face} \\1\\par", $doc_buffer);
		}

		$doc_buffer = preg_replace("/<P>(.*?)<\/P>/mi", "\\1\\par ", $doc_buffer);
		$doc_buffer = preg_replace("/<STRONG>(.*?)<\/STRONG>/mi", "\\b \\1\\b0 ", $doc_buffer);
		$doc_buffer = preg_replace("/<EM>(.*?)<\/EM>/mi", "\\i \\1\\i0 ", $doc_buffer);
		$doc_buffer = preg_replace("/<U>(.*?)<\/U>/mi", "\\ul \\1\\ul0 ", $doc_buffer);
		$doc_buffer = preg_replace("/<STRIKE>(.*?)<\/STRIKE>/mi", "\\strike \\1\\strike0 ", $doc_buffer);
		$doc_buffer = preg_replace("/<SUB>(.*?)<\/SUB>/mi", "{\\sub \\1}", $doc_buffer);
		$doc_buffer = preg_replace("/<SUP>(.*?)<\/SUP>/mi", "{\\super \\1}", $doc_buffer);
		$doc_buffer = preg_replace("/<H1>(.*?)<\/H1>/mi", "\\fs48\\b \\1\\b0\\fs{$this->font_size}\\par ", $doc_buffer);
		$doc_buffer = preg_replace("/<H2>(.*?)<\/H2>/mi", "\\fs36\\b \\1\\b0\\fs{$this->font_size}\\par ", $doc_buffer);
		$doc_buffer = preg_replace("/<H3>(.*?)<\/H3>/mi", "\\fs27\\b \\1\\b0\\fs{$this->font_size}\\par ", $doc_buffer);

		for($i=8;$i<33;$i++)
	  	$doc_buffer = preg_replace("/<span.*font-size: ".$i."pt.*\">(.*?)<\/span>/mi", "\\fs".($i*2)."\ \\1\\fs{$this->font_size} ", $doc_buffer);

		$doc_buffer = preg_replace("/<HR(.*?)>/i", "\\brdrb\\brdrs\\brdrw30\\brsp20 \\pard\\par ", $doc_buffer);
//		$doc_buffer = str_replace("<BR>", "\\par ", $doc_buffer);
//		$doc_buffer = str_replace("<br />", "\\par ", $doc_buffer);
		$doc_buffer = str_replace("<TAB>", "\\tab ", $doc_buffer);
		$doc_buffer = str_replace("&nbsp;", " ", $doc_buffer);


		$doc_buffer = $this->nl2par($doc_buffer);
  	return $doc_buffer;
	}

	function setPaperSize($size=0)
	{
		$inch = 1440;
    $cm = 567;
    $mm = 56.7;

		// 1 => Letter (8.5 x 11 inch)
		// 2 => Legal (8.5 x 14 inch)
		// 3 => Executive (7.25 x 10.5 inch)
		// 4 => A3 (297 x 420 mm)
		// 5 => A4 (210 x 297 mm)
		// 6 => A5 (148 x 210 mm)
		// Orientation considered as Portrait

		switch($size) {
			case 1:
				$this->page_width = floor(8.5*$inch);
				$this->page_height = floor(11*$inch);
				$this->page_size = 1;
				break;
			case 2:
				$this->page_width = floor(8.5*$inch);
				$this->page_height = floor(14*$inch);
				$this->page_size = 5;
				break;
			case 3:
				$this->page_width = floor(7.25*$inch);
				$this->page_height = floor(10.5*$inch);
				$this->page_size = 7;
				break;
			case 4:
				$this->page_width = floor(297*$mm);
				$this->page_height = floor(420*$mm);
				$this->page_size = 8;
				break;
			case 5:
			default:
				$this->page_width = floor(210*$mm);
				$this->page_height = floor(297*$mm);
				$this->page_size = 9;
				break;
			case 6:
				$this->page_width = floor(148*$mm);
				$this->page_height = floor(210*$mm);
				$this->page_size = 10;
				break;
		}
	}

	function getHeader()
	{
	  $rtf_version = 1;
	  $tab_width = 360;
		$header_buffer = "\\rtf{$rtf_version}\\ansi\\deff0\\deftab{$tab_width}\n\n";
		return $header_buffer;
	}

	// Font table
	function getFontTable()
	{
		global $fonts_array;
		$fonts_array = array();
    $fonts_array[] = array("name"		=>	"Arial","family"	=>	"swiss","charset"	=>	0);
    $fonts_array[] = array("name"		=>	"Times New Roman","family"	=>	"roman","charset"	=>	0);
    $fonts_array[] = array("name"		=>	"Verdana","family"	=>	"swiss","charset"	=>	0);
    $fonts_array[] = array("name"		=>	"Symbol","family"	=>	"roman","charset"	=>	2);
		$font_buffer = "{\\fonttbl\n";
		foreach($fonts_array AS $fnum => $farray)
		{
			$font_buffer .= "{\\f{$fnum}\\f{$farray['family']}\\fcharset{$farray['charset']} {$farray['name']}}\n";
		}
		$font_buffer .= "}\n\n";
		return $font_buffer;
	}

	// Colour table
	function getColourTable()
	{
		$colour_buffer = "";
		if(sizeof($this->colour_table) > 0)
		{
			$colour_buffer = "{\\colortbl;\n";
			foreach($this->colour_table AS $cnum => $carray) {
				$colour_buffer .= "\\red{$carray['red']}\\green{$carray['green']}\\blue{$carray['blue']};\n";
			}
			$colour_buffer .= "}\n\n";
		}
		return $colour_buffer;
	}

	// Information
	function getInformation()
	{
		$info_buffer = "";
		if(sizeof($this->info_table) > 0)
		{
			$info_buffer = "{\\info\n";
			foreach($this->info_table AS $name => $value)
			{
				$info_buffer .= "{\\{$name} {$value}}";
			}
			$info_buffer .= "}\n\n";
		}

		return $info_buffer;
	}

	// Default font settings
	function getDefaultFont()
	{
	  $font_face = 0;
    $font_size = 24;
		$font_buffer = "\\f{$font_face}\\fs{$font_size}\n";
		return $font_buffer;
	}

	// Page display settings
	function getPageSettings()
	{
	  $page_orientation = 1;

		if($page_orientation == 1)
			$page_buffer = "\\paperw{$this->page_width}\\paperh{$this->page_height}\n";
		else
			$page_buffer = "\\paperw{$this->page_height}\\paperh{$this->page_width}\\landscape\n";

		$page_buffer .= "\\pgncont\\pgndec\\pgnstarts1\\pgnrestart\n";

		return $page_buffer;
	}

	function generateTemplate()
	{
		$buffer .= "{";
		// Header
		$buffer .= $this->getHeader();
		// Font table
		$buffer .= $this->getFontTable();
		// Colour table
		$buffer .= $this->getColourTable();
		// File Information
		$buffer .= $this->getInformation();
		// Default font values
		$buffer .= $this->getDefaultFont();
		// Page display settings
		$buffer .= $this->getPageSettings();
		// Parse the text into RTF
		//$buffer .= $this->parseDocument();
		$buffer .= "\par <<body>>\n";
		$buffer .= "}";
		return $buffer;
	}

	function getAllFields($keyValue)
	{
	  $db=new DB();
	  $data=array();
	  global $__appvar,$USR;
		$velden=array('Vermogensbeheerder','Client','Depotbank','Accountmanager','tweedeAanspreekpunt','Remisier','RapportageValuta','accountEigenaar');
		foreach($velden as $veld)
			$keyValue['*'.$veld]='';
	  if($keyValue['Vermogensbeheerder'])
	  {
	    $query="SELECT Naam as `*Vermogensbeheerder` FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$keyValue['Vermogensbeheerder']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Vermogensbeheerder']=$data['*Vermogensbeheerder'];
	  }
	  if($keyValue['Client'])
	  {
	    $query="SELECT Naam as `*Client` FROM Clienten WHERE Client='".$keyValue['Client']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Client']=$data['*Client'];
	  }
	  if($keyValue['Depotbank'])
	  {
	    $query="SELECT Omschrijving as `*Depotbank` FROM Depotbanken WHERE Depotbank='".$keyValue['Depotbank']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Depotbank']=$data['*Depotbank'];
	  }
	  if($keyValue['Accountmanager'])
	  {
	    $query="SELECT Naam as `*Accountmanager` FROM Accountmanagers WHERE Accountmanager='".$keyValue['Accountmanager']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Accountmanager']=$data['*Accountmanager'];
	  }
	  if($keyValue['tweedeAanspreekpunt'])
	  {
	    $query="SELECT Naam as `*tweedeAanspreekpunt` FROM Accountmanagers WHERE Accountmanager='".$keyValue['tweedeAanspreekpunt']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*tweedeAanspreekpunt']=$data['*tweedeAanspreekpunt'];
	  }
	  if($keyValue['Remisier'])
	  {
	    $query="SELECT Naam as `*Remisier` FROM Remisiers WHERE Remisier='".$keyValue['Remisier']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Remisier']=$data['*Remisier'];
	  }
	  if($keyValue['RapportageValuta'])
	  {
	    $query="SELECT Omschrijving as `*RapportageValuta` FROM Valutas WHERE Valuta='".$keyValue['RapportageValuta']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*RapportageValuta']=$data['*RapportageValuta'];
	  }
 	  if($keyValue['accountEigenaar'])
	  {
	    $query="SELECT Naam as `*accountEigenaar` FROM Gebruikers WHERE Gebruiker='".$keyValue['accountEigenaar']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*accountEigenaar']=$data['*accountEigenaar'];
	  }    
	  $keyValue['huidigeDatum']=date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y");
	  $keyValue['huidigeGebruiker']=$USR;
    
  	$query="SELECT Naam,titel FROM Gebruikers WHERE Gebruiker='".$USR."'";
	  $db->SQL($query);
	  $data=$db->lookupRecord();
	  $keyValue['GebruikerNaam']=$data['Naam'];  
    $keyValue['GebruikerTitel']=$data['titel'];  
  
	  return $keyValue;
	}

	function html_entity_decode_extra($data)
	{
	  $data=html_entity_decode($data);
	  $data=str_replace(array('&rsquo;','&rsquo;','&ndash;','&hellip;','&ldquo;','&rdquo;'),array('’','’','–','…','“','”' ),$data);
	  return $data;
 	}
}
?>