<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2012/08/05 10:41:34 $
 		File Versie					: $Revision: 1.10 $

 		$Log: AE_cls_rtfBase.php,v $
 		Revision 1.10  2012/08/05 10:41:34  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2011/05/25 17:23:06  rvv
 		*** empty log message ***

 		Revision 1.8  2011/05/18 16:52:38  rvv
 		*** empty log message ***

 		Revision 1.7  2009/12/13 15:41:47  rvv
 		*** empty log message ***

 		Revision 1.6  2009/12/08 18:18:12  rvv
 		*** empty log message ***

 		Revision 1.5  2009/08/19 09:17:04  rvv
 		*** empty log message ***

 		Revision 1.4  2009/07/12 09:26:45  rvv
 		*** empty log message ***

 		Revision 1.3  2009/07/06 07:15:25  cvs
 		*** empty log message ***

 		Revision 1.2  2006/01/05 16:00:09  cvs
 		*** empty log message ***

 		Revision 1.1  2005/12/16 14:43:09  jwellner
 		classes aangepast

 		Revision 1.1  2005/12/14 08:33:16  cvs
 		*** empty log message ***



*/
class rtfBase
{
  var $filename;
	var $items = array();
  var $outputFilename;

	function rtfbase($filename)
  {
	  $this->filename = $filename;
  }

  function addItem($key,$field="")
  {
    if (is_array($key))  // if array merge it into $items
    {
      while ( list( $theKey, $theVal ) = each( $key ) )
      {
        $this->items[$theKey] = $theVal;
      }
    }
    else
	   $this->items[$key] = $field;
  }

  function parseRTF()
  {
 	  $db=new DB();
    $db->SQL("SELECT template FROM CRM_naw_RtfTemplates WHERE naam ='".$this->filename."'");
	  $tmp=$db->lookupRecord();
	  $message=$tmp['template'];

    while ( list( $key, $val ) = each( $this->items ) )
    {
//      $val=str_replace("  ",'',$val);
      $message = str_replace( "<<".$key.">>", $val, $message);
      $message = str_replace( "\{".$key."\}", $val, $message);
    }
    $message = eregi_replace( "\<<[a-zA-Z0-9_-]+\>>", "", $message);   // delete empty tags
//    $message = str_replace("  "," ",$message);
$leegNietTonen=0;
$leegNietTonen=strpos($message,'leegNietTonen');
if($leegNietTonen > 0)
{
    $parts=explode("\par ",$message);
    foreach ($parts as $nr=>$line)
    {
      $strippedLine=trim($line,chr(160).chr(32));
      $strippedLine=trim($strippedLine);

      if(strpos($strippedLine,'leegNietTonen') >= 1)
      {
        $lines=explode("\line ",$strippedLine);
        foreach ($lines as $lineNr=>$linePart)
        {
          if(strpos($linePart,'leegNietTonen') == 3)
            unset($lines[$lineNr]);
        }
        $parts[$nr]=implode("\line ",$lines);
      }
      if(strpos($strippedLine,'leegNietTonen') == 1 || strpos($strippedLine,'leegNietTonen') == 2)
          unset($parts[$nr]);
    }
    $message=implode("\par ",$parts);
    $message = str_replace('\{leegNietTonen\}', '', $message);
    $message = str_replace('<<leegNietTonen>>', '', $message);
}
    return $message;
  }

  function getRTF()
  {
    if ($this->outputFilename)
      $outputFilename = $this->outputFilename;
    else
      $outputFilename = date("YmdHi")."-".$this->filename;
    $data = $this->parseRTF();
    header("Content-Type: application/rtf");
    header("Content-Length: ".strlen($data));
    header("Content-disposition: attachment; filename=".$outputFilename);
    echo $data;
  }
}

?>