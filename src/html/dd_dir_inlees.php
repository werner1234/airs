<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/09/01 13:31:16 $
 		File Versie					: $Revision: 1.1 $

 		$Log: dd_dir_inlees.php,v $
 		Revision 1.1  2013/09/01 13:31:16  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/08/04 10:47:37  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/02/19 16:11:27  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2011/04/19 16:42:23  rvv
 		*** empty log message ***

 		Revision 1.9  2011/01/15 10:02:59  rvv
 		*** empty log message ***

 		Revision 1.8  2010/07/25 14:38:14  rvv
 		*** empty log message ***

 		Revision 1.7  2010/04/14 17:01:15  rvv
 		*** empty log message ***

 		Revision 1.6  2010/01/10 10:34:42  rvv
 		*** empty log message ***

 		Revision 1.5  2009/11/29 15:16:59  rvv
 		*** empty log message ***

 		Revision 1.4  2009/11/25 16:08:30  rvv
 		*** empty log message ***

 		Revision 1.3  2009/11/22 14:46:46  rvv
 		*** empty log message ***

 		Revision 1.2  2009/11/22 14:21:24  rvv
 		*** empty log message ***

 		Revision 1.1  2009/11/22 14:07:41  rvv
 		*** empty log message ***


*/

$disable_auth = true;
include("wwwvars.php");
include_once("../classes/AE_cls_digidoc.php");

$db=new DB();
$query="SELECT Vermogensbeheerders.ddInleesLocatie,Vermogensbeheerders.ddInleesPortefeuillePreg FROM Vermogensbeheerders $join WHERE ddInleesLocatie <> '' $where limit 1";
$db->SQL($query);
$settings=$db->lookupRecord();
$locatie=$settings['ddInleesLocatie'];
$portefeuillePreg=$settings['ddInleesPortefeuillePreg'];

//$locatie='/develop/php/rvv/AIRS/tmp';

$deleteFiles=array();
if (is_dir($locatie))
{
    if ($dh = opendir($locatie))
    {
        while ($file = readdir($dh))
        {
          $fullFile=$locatie ."/". $file;
          if(filetype($fullFile)=='file')
          {
            $deleteFile=checkFile($fullFile,$portefeuillePreg);
			      if($deleteFile)
			        $deleteFiles[]=$deleteFile;
          }
          
          if(filetype($fullFile)=='dir' && $file<>'.' && $file <> '..' )
          {
             preg_match('/id?([0-9]+)/im',$file, $matches);
            if($matches[0] <> '')
              $idKoppeling=$matches[1];
            else
              $idKoppeling=0;  
              
            $portefeuille = preg_replace('/[^0-9]/','',$file);

            
            if($portefeuille <> '' || $idKoppeling <> 0)
            {
              echo vtb('Documenten voor %s of id %s uit %s', array($portefeuille, $idKoppeling, $file)) . " <br>\n";
              $crmId=0;
              if($db->QRecords("SELECT id FROM CRM_naw WHERE id = '".$idKoppeling."'")>0)
              {
                $id=$db->nextRecord();
                $crmId=$id['id'];
              }             
              elseif($db->QRecords("SELECT id FROM CRM_naw WHERE portefeuille = '".$portefeuille."'")>0)
              {
                $id=$db->nextRecord();
                $crmId=$id['id'];
              }
    
               if($crmId > 0)
               {       
                 if ($dh2 = opendir($locatie."/".$file))
                 {
                   while ($file2 = readdir($dh2))
                   {
                    $fullFile2=$locatie ."/". $file."/".$file2;
                    if(filetype($fullFile2)=='file')
                    { 
                      $deleteFile=checkFile($fullFile2,$portefeuillePreg,$crmId);
	                    if($deleteFile)
			                  removeFile($deleteFile);
                    }                
                   }
                  }
                }
                else
                {
                  echo vtb('Geen relatie ID gevonden voor portefeuille %s', array()) . " '".$portefeuille."' <br>\n";
                }
              echo vtb('Klaar met %s uit %s', array($portefeuille, $file)) . " <br>\n";
            }
          }
        }
	  	foreach ($deleteFiles as $file)
	  	{
		    removeFile($file);
		  }
      echo vtb('Klaar met inlezen %s.', array($locatie)) . " <br>\n";
      closedir($dh);
    }
    else
    {
      echo vtb('Geen rechten om %s te openen..', array($locatie)) . " <br>\n";
    }
}
else
{
  echo vtb('Kan locatie: %s niet openen..', array($locatie)) . " <br>\n";
}

function checkFile($filename,$portefeuillePreg='',$crmId='')
{
  $db=new DB();
  $table='CRM_naw';
  $store=false;
  $file=basename($filename);

  if($crmId=='')
  {
    preg_match("/^id[\d]*/i",$file, $matches);
    if(count($matches) >0 && $matches[0] !='')
    {
      $id=substr($matches[0],2);
      if($db->QRecords("SELECT id FROM $table WHERE id = '$id'")>0)
        $store=true;
    }

    preg_match("/^[\d]*/i",$file, $matches);
    if(count($matches) >0 && $matches[0] !='')
    {
      if($db->QRecords("SELECT id FROM $table WHERE portefeuille = '".$matches[0]."'")>0)
      {
        $store=true;
        $portefeuille=$matches[0];
        $id=$db->nextRecord();
        $id=$id['id'];
      }
    }
    if($portefeuillePreg <> '')
    {
      preg_match($portefeuillePreg,$file, $matches);
      if(count($matches) >0 && $matches[0] !='')
      {
        if($db->QRecords("SELECT id FROM $table WHERE portefeuille = '".$matches[0]."'")>0)
        {
          $store=true;
          $id=$db->nextRecord();
          $id=$id['id'];
        }
      }
    }
    if(substr($file,0,2)=='id' || substr($file,0,2)=='ID')
      $file=substr($file,strpos($file,$id)+strlen($id));
  }
  else
  {
    $id=$crmId;
    $store=true;
  }

  $query="SELECT id FROM dd_reference WHERE module_id='$id' AND (description='".mysql_escape_string($file)."' OR keywords='".mysql_escape_string($file)."')";
  if($db->QRecords($query)>0)
  {
    echo vtb('Document %s is al gekoppeld aan relatieId %s.', array($file, $id)) . " <br>\n";
    return false;
  }
  
  if(!is_writable($filename))
  {
    $store=false;
    echo vtb('Kan document %s niet verwijderen.', array($file)) . " <br>\n";
    echo perm($file)."<br>\n";
  }
  elseif($store==true && $id > 0)
  {
    $filesize = filesize($filename);
    $filetype = mime_content_type($filename);
    $fileHandle = fopen($filename, "r");
    $docdata = fread($fileHandle, $filesize);
    fclose($fileHandle);
    $dd = new digidoc();
    $rec ["filename"] = $file;
    $rec ["filesize"] = "$filesize";
    $rec ["filetype"] = "$filetype";
    $rec ["description"] = str_replace($portefeuille,'',str_replace($portefeuille.'_','',$file));
    $rec ["blobdata"] = $docdata;
    $rec ["keywords"] =$file;
    $rec ["module"] = $table;
    $rec ["module_id"] = $id;
    $dd->useZlib = true;
    if($dd->addDocumentToStore($rec) == false)
      return false;
    else
      return $filename;
  }
  else
  {
    echo vtb('Kan geen relatie vinden bij document: %s.', array($file)) . " \n<br>";
  }
}

function removeFile($filename)
{
	if(!unlink($filename))
	{
	    echo vtb('Kan document %s niet verwijderen.', array($filename)) . " <br>\n";
      echo perm($filename)."<br>\n";
	}
	else
	{
	  echo vtb('Document %s toegevoegd en verwijderd.', array($filename)) . " <br>\n";
	}
}


function perm($file)
{
  $perms = fileperms($file);

if (($perms & 0xC000) == 0xC000) {
    // Socket
    $info = 's';
} elseif (($perms & 0xA000) == 0xA000) {
    // Symbolic Link
    $info = 'l';
} elseif (($perms & 0x8000) == 0x8000) {
    // Regular
    $info = '-';
} elseif (($perms & 0x6000) == 0x6000) {
    // Block special
    $info = 'b';
} elseif (($perms & 0x4000) == 0x4000) {
    // Directory
    $info = 'd';
} elseif (($perms & 0x2000) == 0x2000) {
    // Character special
    $info = 'c';
} elseif (($perms & 0x1000) == 0x1000) {
    // FIFO pipe
    $info = 'p';
} else {
    // Unknown
    $info = 'u';
}

// Owner
$info .= (($perms & 0x0100) ? 'r' : '-');
$info .= (($perms & 0x0080) ? 'w' : '-');
$info .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x' ) :
            (($perms & 0x0800) ? 'S' : '-'));

// Group
$info .= (($perms & 0x0020) ? 'r' : '-');
$info .= (($perms & 0x0010) ? 'w' : '-');
$info .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x' ) :
            (($perms & 0x0400) ? 'S' : '-'));

// World
$info .= (($perms & 0x0004) ? 'r' : '-');
$info .= (($perms & 0x0002) ? 'w' : '-');
$info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x' ) :
            (($perms & 0x0200) ? 'T' : '-'));

echo $info;

}





?>
