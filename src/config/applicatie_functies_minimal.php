<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/07/25 15:34:14 $
    File Versie         : $Revision: 1.12 $

    $Log: applicatie_functies_minimal.php,v $
    Revision 1.12  2020/07/25 15:34:14  rvv
    *** empty log message ***

    Revision 1.11  2020/06/22 13:19:10  cvs
    call 3205

    Revision 1.10  2020/06/22 12:17:01  cvs
    call 3205

    Revision 1.9  2020/06/22 11:37:55  cvs
    call 3205

    Revision 1.8  2020/06/19 10:13:33  cvs
    call 3205

    Revision 1.7  2020/06/12 10:02:30  cvs
    call 3205

    Revision 1.6  2020/05/06 17:33:58  rvv
    *** empty log message ***

    Revision 1.5  2020/04/04 12:00:31  rvv
    *** empty log message ***

    Revision 1.4  2019/03/23 17:08:51  rvv
    *** empty log message ***

    Revision 1.3  2018/09/12 12:40:49  cvs
    no message

    Revision 1.2  2017/08/23 15:21:22  cvs
    call 5933

    Revision 1.1  2017/08/23 14:10:57  cvs
    file splitsen tbv API interface



*/


function getRenteParameters($fonds,$datum)
{
  global $USR;
  $DB=new DB();
  
  $query="SELECT callableDatumGebruiken FROM Vermogensbeheerders INNER JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'  ORDER BY Vermogensbeheerders.id limit 1";
  $DB->SQL($query);
  $callable=$DB->lookupRecord();

  $q = "SELECT Fonds,Rente30_360,forward,forwardReferentieKoers,Renteperiode,Lossingsdatum,EindDatum,Rentedatum,Rentepercentage,EersteRentedatum,callabledatum FROM Fondsen WHERE Fonds = '".mysql_real_escape_string($fonds)."'  LIMIT 1";
  $DB->SQL($q);
  $DB->Query();
  $rente = $DB->NextRecord();
  
  if($rente['callabledatum'] <> '0000-00-00' && $callable['callableDatumGebruiken']==1 && db2jul($rente['callabledatum']) > db2jul($datum))
    $rente['Lossingsdatum']=$rente['callabledatum'];

  $q = "SELECT * FROM FondsParameterHistorie WHERE Fonds = '".mysql_real_escape_string($fonds)."' AND GebruikTot > '$datum' ORDER BY GebruikTot ASC LIMIT 1";
  $DB->SQL($q);
  $DB->Query();
  if($DB->records()>0)
  {
    $tmp = $DB->NextRecord();// echo $q."<br>\n"; listarray($tmp);
    foreach($tmp as $key=>$value)
    {
      $rente[$key] = $value;
    }
    if($tmp['callabledatum'] <> '0000-00-00' && $callable['callableDatumGebruiken']==1)
      $rente['Lossingsdatum']=$tmp['callabledatum'];
  }
  
  if($rente['Rente30_360']>0)
    $rente['rentemethodiek']=$rente['Rente30_360']+1;
  else
    $rente['rentemethodiek']=1;
  return $rente;
}

function form2jul($date)
{
  // day - month - year
  global $__appvar;
  if ($date != "")
  {
    $D = explode($__appvar['date_seperator'],$date);
    $date = mktime(0,0,0,$D[1],$D[0],$D[2]);
    //mktime ( [int hour [, int minute [, int second [, int month [, int day [, int year [, int is_dst]]]]]]] )
  }
  else
  {
    $date = -1;
  }
  return $date;
}

function jul2form($jul)
{
  // day - month - year
  global $__appvar;
  if ($jul != "" && $jul > 0)
  {
    $date = date("d".$__appvar[date_seperator]."m".$__appvar[date_seperator]."Y",$jul);
  }
  else
  {
    $date = "";
  }
  return $date;
}

function jul2sql($jul, $long=false)
{
  if ($jul < 0)
  {
    if(!$long) $date = "0000-00-00";
    else $date = "0000-00-00 00:00:00";
  }
  else
  {
    if(!$long) $date = date("Y-m-d",$jul);
    else $date = date("Y-m-d H:i:s",$jul);
  }
  return $date;
}

//if (!function_exists("jul2db"))
//{
//  function jul2db ($indat=0)
//  {
//    if ($indat == 0)
//    {
//      $indat = time();
//    }
//    return date('Y',$indat) ."-". date('m',$indat) ."-". date('d',$indat) ." ".
//      date('H',$indat) .":". date('i',$indat) .":". date('s',$indat) ;
//  }
//}

function debug($de_array,$title="",$showButton=false)
{
  global $__debug;
  if ($__debug)
  {
    $output = "
    
    <fieldset>
      
    ";
    $data = stripslashes(var_export($de_array,true));  // slashes uit uitvoer verwijderen
    if (!is_array($de_array) AND !is_object($de_array) )
    {
      $start = substr($data, 0,1);
      $end = substr($data, -1);
      if ( $start == '"' || $start == '\'')
      {
        $data  = substr($data,1,-1);  // quotes om strings verwijderen
      }
    }

    $output .= "\n<div style='background:#D2B48C; padding:3px;  color:#000; font-family:monospace; line-height: 1rem'><span style='background:#333; color:white;'> Backtrace&nbsp;:</span>&nbsp;";
    $bt = debug_backtrace();
    if ($showButton)
    {
      $output .= " <button onclick=\"document.getElementById('srvSESSION').style.display = (document.getElementById('srvSESSION').style.display == 'none') ? 'block' : 'none';\"> SESSION </button> 
                   <button onclick=\"document.getElementById('srvSERVER').style.display = (document.getElementById('srvSERVER').style.display == 'none') ? 'block' : 'none';\"> SERVER </button>
      ";
    }

    $output .= "&raquo;&raquo; ".$bt[0]["file"].":".$bt[0]["line"]."\n";
    $output .= "\n<br/><span style='background:#333; color:white;'>VAR type &nbsp;:</span>&nbsp;";
    $output .= "&raquo;&raquo; ".gettype($de_array)."\n";
    if ($showButton)
    {
      $output .= "<br /><pre id='srvSESSION' style='display:none;'>\$_SESSION<br />".var_export($_SESSION,true)."</pre>\n";
      $output .= "<br /><pre id='srvSERVER' style='display:none;'>\$_SERVER<br />".var_export($_SERVER,true)."</pre>\n";
      $output .= "</div><hr />";
    }
    $output .= "<div style='background:#8B4513; padding:3px; margin-bottom:0; color:white; font-weight: bold; font-size:16px; font-family:monospace; '>$title</div>";
    $output .= "<pre style='background:#FAEBD7; color: #333; padding:3px; margin-top:0; border: 1px solid maroon; font-family:monospace; '>\n";
    $output .= $data;
    $output .= "</pre></fieldset>\n";

    echo $output;
  }
}

function BepaalRecordIdsForIntCheck($query,$bedrijf, $table, $queryValues, $exportId)
{
  // hoort bij call 3205
  global $USR;
  return;
  $tablesArray = array(
    "Fondsen",
    "Fondskoersen",
    "Portefeuilles",
    "Rekeningafschriften",
    "Rekeningen",
    "Rekeningmutaties"
  );

  if (!in_array($table, $tablesArray))
  {
    return true;
  }


  // change_date in de query omzetten naar $d dagen geleden
  $d = 5;

//  debug($query, "query org $table");

  switch ($table)
  {
    case "Fondskoersen":
      $s = explode(".change_date", $query);
      $sEnd = explode("AND", $s[1]);
      $query = $s[0].".change_date >= '".date("Y-m-d", mktime()-($d*86400))." 00:00:00' AND ". $sEnd[1];
      break;
    case "Fondsen":
      $s = explode(".change_date", $query);
      $query = $s[0].".change_date >= '".date("Y-m-d", mktime()-($d*86400))." 00:00:00' )";
      break;
    case "Portefeuilles":
    case "Rekeningen":
    case "Rekeningafschriften":
    case "Rekeningmutaties":
      $s = explode(".change_date", $query);
      $query = $s[0].".change_date >= '".date("Y-m-d", mktime()-($d*86400))." 00:00:00' ";
      break;
    default:
  }

//  __debugLog("updateQueryIntCheck::".$query);
//  debug($query, "cnv $table");


 // $change_date = $queryValues["lastUpdate"];
  $change_date = date("Y-m-d", mktime()-($d*86400))." 00:00:00";
  $ids = array();
  $db = new DB();
  $db->executeQuery($query);

  while($rec = $db->nextRecord())
  {
    $ids[] = $rec["id"];
  }

  if (count($ids) > 0)
  {
    $query = "INSERT INTO `integrityCheckHome` SET 
      add_date = NOW()
    , add_user = '{$USR}'
    , change_date = NOW()
    , change_user = '{$USR}'
    , `exportId` = '{$exportId}'
    , `bedrijf` = '{$bedrijf}'
    , `updatesAfter` = '{$change_date}'
    , `table`        = '{$table}'
    , `ids`          = '".implode(",",$ids)."'
  ";
//    debug($query);
  $db->executeQuery($query);
  }
}

function __debugLog($txt,$memo="")
{

  $logfile = realpath(dirname(__FILE__))."/debugLog.txt";
  $fileHandle = fopen($logfile,"a") or die("Kan logfile $logfile niet openen voor schrijven");
  $timestamp = date("j-m-Y H:i:s  ")."//".$_SERVER["REMOTE_ADDR"].$memo."/ ";

  if (is_writable($logfile))
  {
    if (!$fileHandle = fopen($logfile, 'a'))
    {
      echo "Kan het bestand niet openen ($logfile)";
      exit;
    }
    if (!fwrite($fileHandle, $timestamp.$txt."\n"))
    {
      echo "Kan niet schrijven naar bestand ($logfile)";
      exit;
    }
    fclose($fileHandle);

  }
  else
  {
    echo "Het bestand $logfile is niet schrijfbaar";
  }
}

function cnvFilename($filename)
{
  $ts = array("[Р-Х]","[Ч]","[Ш-Ы]","/[Ь-Я]/","/а/","/б/","/[в-жи]/","/з/","/[й-м]/","/[н-п]/","/[р-х]/","/ц/","/ч/","/[ш-ы]/","/[ь-я]/","/№/","/ё/","/[ђ-іј]/","/ї/","/[љ-ќ]/","/[§-џ]/","[ ]","[:]");
  $tn = array("A"    ,"C"  ,"E"    ,"I"      ,"D"  ,"N"  ,"O"       ,"X"  ,"U"      ,"Y"      ,"a"      ,"ae" ,"c"  ,"e"      ,"i"      ,"d"   ,"n"  ,"o"      ,"x"  ,"u"      ,"y"      ,"_"  ,"");

  $file = preg_replace($ts, $tn, $filename);

  return $file;
}
