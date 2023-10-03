<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/12/12 12:39:40 $
    File Versie         : $Revision: 1.3 $

    $Log: bathReconVerwerk.php,v $
    Revision 1.3  2018/12/12 12:39:40  cvs
    call 3503

    Revision 1.2  2018/03/28 12:37:00  cvs
    call 3503

    Revision 1.1  2018/03/09 12:45:39  cvs
    call 3503



*/
$disable_auth = true;
$logfile = "batchReconVerwerk.log";
//unlink($logfile);
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
mb_internal_encoding("UTF-8");

include_once "wwwvars.php";

include_once "../../classes/AIRS_cls_reconJob.php";

logconsole("starting daemon");

$db = new DB();
$job = new AIRS_cls_reconJob();
$cronRun = true;  // onderdruk scherm uitvoer
$USR = "jobMan";  // overschrijf de ingelogde gebruiker
while (true)
{

  if ($job->jobRunning())
  {
    logconsole($job->returnMessage);
    sleep(10);
//    continue;
  }

  if ($nextJob = $job->nextJob())
  {
//    debug($nextJob, "nextjob");
    $uitvoer = $job->currentJob["uitvoer"];
    $job->setStatus("verwerken");
    $depot = $nextJob["depotbank"];
    logconsole($depot);
    switch ($depot)
    {
      case "BIN":
        include_once("binck_reconFuncties.php");
        $functionPrefix = "binck_";
        break;
      case "FVL":
        include_once("fvl_reconFuncties.php");
        $functionPrefix = "fvl_";
        break;
      case "TGB":
        include_once("tgb_reconFuncties.php");
        $functionPrefix = "tgb_";
        break;
      case "GIRO":
        include_once("degirov2_reconFuncties.php");
        $functionPrefix = "";
        break;
//      case "AAB":
//        include_once("abn_reconFuncties.php");
//        $soort = "2file";
//        break;
      default:
    }

    //debug($depot);

    $job->removeTijdelijkeReconRows();
    $startSec = $job->nowSeconds();
    $subBatches = array();
    $batch = $job->currentJob["batchnr"];
    $voortgang = $job->nowSeconds();

    if ($soort == "2file")
    {
      $file = $job->currentFileset[0];
      $file2 = $job->currentFileset[1];
      if (!validateFile($file, $file2))
      {
        $job->addToLog("validatie mislukt zie foutlog " . $basename);
        $job->errorLog($error, $basename);

      }
      else
      {
        $recon = new reconcilatieClass($depot, $job->currentJob["reconDatum"]);
        $recon->batch = $job->currentJob["batchnr"];
        $recon->batchverwerking = true;
        $bankRecords = recon_readBank($file, $file2);
        $vb = implode(", ", $recon->vbArray);
        if (count($job->currentJob["vermogenbeheerders"]) > 0)
        {
          $tArray = array_merge(explode(",", $job->currentJob["vermogenbeheerders"]), $recon->vbArray);
        }
        else
        {
          $tArray = $recon->vbArray;
        }

        $job->updateJob(array("vermogenbeheerders" => implode(",", $tArray)));
        $job->addToLog("bestanden verwerkt in " . ($job->nowSeconds() - $voortgang) . " sec.");
        $job->addToLog("gevonden VB: " . $vb);
        $job->addToLog("het bankbestanden bevatte " . $bankRecords . " dataregels");
        $job->addToLog($airsOnly . " AIRS rekeningen zonder bankposities");
      }
    }
    else  // enkele bestanden
    {
      $subCount = 0;
      foreach ($job->currentFileset as $file)
      {
        logconsole($file);
        if (file_exists($file))
        {
          logconsole("file gevonden");
        }
        else
        {
          logconsole("file NIET gevonden");
        }
        if ($uitvoer == "meer")
        {
          $subCount++;
          $subJob = new AIRS_cls_reconJob($job->batch . "-" . $subCount);   // sub Job aanmaken
          $subJob->getJob();
          $subJob->updateJob($job->getCopyValues());
          $basename = basename($file);
          $subJob->updateJob(array("bestanden" => $file)); // copy file to job
//          debug($subJob->currentJob, "subjob");
          $job->addToLog("$basename uitvoer naar batch  " . $subJob->batch);
          $subJob->addToLog("Onderdeel van hoofd batch " . $job->batch );
          $subJob->addToLog("start validatie " . $basename);
          $func = $functionPrefix."validateFile";
          if (! call_user_func($func,$file))
          {
            $subJob->addToLog("validatie mislukt zie foutlog " . $basename);
            logconsole(var_export($error,true));
            $subJob->errorLog($error, $basename);
            $subJob->setStatus("afgekeurd");
            continue;
          }
          $recon = new reconcilatieClass($depot, $subJob->currentJob["reconDatum"]);
          $recon->batch = $subJob->currentJob["batchnr"];
          $recon->batchverwerking = true;
          $bankRecords = call_user_func($functionPrefix."recon_readBank",$file);
          $vb = implode(", ", $recon->vbArray);
          if (trim($subJob->currentJob["vermogenbeheerders"]) != "")
          {
            $tArray = array_merge(explode(",", $subJob->currentJob["vermogenbeheerders"]), $recon->vbArray);
          }
          else
          {
            $tArray = $recon->vbArray;
          }

          $subJob->updateJob(array("vermogenbeheerders" => implode(",", $tArray)));
          $subJob->addToLog("bestand verwerkt in " . ($subJob->nowSeconds() - $voortgang) . " sec.");
          $subJob->addToLog("gevonden VB: " . $vb);
          $subJob->addToLog("het bankbestand bevatte " . $bankRecords . " dataregels");
          $subJob->addToLog($airsOnly . " AIRS rekeningen zonder bankposities");
          //$subJob->updateJob(array("vermogenbeheerders"=>array_merge($subJob->currentJob["vermogenbeheerders"]),$vb));
          $subJob->setStatus("klaar");
        }
        else
        {

//        $basename = basename($file);
//        $job->addToLog("--------------------------------");
//        $job->addToLog("start validatie " . $basename);
//        if (!validateFile($file))
//        {
//          $job->addToLog("validatie mislukt zie foutlog " . $basename);
//          $job->errorLog($error, $basename);
//          continue;
//        }
//        $recon = new reconcilatieClass($depot, $job->currentJob["reconDatum"]);
//        $recon->batch = $job->currentJob["batchnr"];
//        $recon->batchverwerking = true;
//        $recon->AirsVerwerkingIntern = false;
//        $bankRecords = recon_readBank($file);
//        $vb = implode(", ", $recon->vbArray);
//        if (trim($job->currentJob["vermogenbeheerders"]) != "")
//        {
//          $tArray = array_merge(explode(",", $job->currentJob["vermogenbeheerders"]), $recon->vbArray);
//        }
//        else
//        {
//          $tArray = $recon->vbArray;
//        }
//
//        // ontbrekende AIRS portefeuilles maar één keer aanroepen
//        $airsOnly = $recon->getAirsPortefeuilles();
//        $airsOnly = $recon->getAirsCashRekeningen();
//
//        $job->updateJob(array("vermogenbeheerders" => implode(",", $tArray)));
//        $job->addToLog("bestand verwerkt in " . ($job->nowSeconds() - $voortgang) . " sec.");
//        $job->addToLog("gevonden VB: " . $vb);
//        $job->addToLog("het bankbestand bevatte " . $bankRecords . " dataregels");
//        $job->addToLog($airsOnly . " AIRS rekeningen zonder bankposities");

          $subCount++;
          $subJob = new AIRS_cls_reconJob($job->batch . "-" . $subCount);   // sub Job aanmaken

          $subJob->getJob();
          $subJob->updateJob($job->getCopyValues());
          $basename = basename($file);
          $subJob->updateJob(array("bestanden" => $file)); // copy file to job
//          debug($subJob->currentJob, "subjob");
          $job->addToLog("$basename uitvoer naar batch  " . $subJob->batch);
          $subJob->addToLog("Onderdeel van hoofd batch " . $job->batch . "------------------");
          $subJob->addToLog("start validatie " . $basename);
//          if (!validateFile($file))
          $func = $functionPrefix."validateFile";
          if (! call_user_func($func,$file))
          {
            $subJob->addToLog("vatlog " . $basename);
            logconsole(var_export($error,true));
            $subJob->errorLog($error, $basename);
            $subJob->setStatus("afgekeurd");
            continue;
          }
          $subBatches[] = $subJob->batch;
          $recon = new reconcilatieClass($depot, $subJob->currentJob["reconDatum"]);
          $recon->batch = $subJob->currentJob["batchnr"];
          $recon->batchverwerking = true;
//          $bankRecords = recon_readBank($file);
          $bankRecords = call_user_func($functionPrefix."recon_readBank",$file);
          $vb = implode(", ", $recon->vbArray);
          if (trim($subJob->currentJob["vermogenbeheerders"]) != "")
          {
            $tArray = array_merge(explode(",", $subJob->currentJob["vermogenbeheerders"]), $recon->vbArray);
          }
          else
          {
            $tArray = $recon->vbArray;
          }

          $subJob->updateJob(array("vermogenbeheerders" => implode(",", $tArray)));
          $subJob->addToLog("bestand verwerkt in " . ($subJob->nowSeconds() - $voortgang) . " sec.");
          $subJob->addToLog("gevonden VB: " . $vb);
          $subJob->addToLog("het bankbestand bevatte " . $bankRecords . " dataregels");
          $subJob->addToLog($airsOnly . " AIRS rekeningen zonder bankposities");
          //$subJob->updateJob(array("vermogenbeheerders"=>array_merge($subJob->currentJob["vermogenbeheerders"]),$vb));
          $subJob->setStatus("combined");

        }
      }
      if ($uitvoer != "meer")  // enkelvoudige uitvoer
      {
//        debug($subBatches, "samenvoegen");
        $job->combineSubjobs($subBatches);
      }
    }


    //$job->updateJob(array("vermogenbeheerders"=>array_merge($job->currentJob["vermogenbeheerders"]),$vb));
    $job->setStatus("klaar");
    logconsole($job->returnMessage);
    sleep(10);

  }
  else
  {
    logconsole($job->returnMessage);
    sleep(10);
  }

}

function logconsole($str)
{
  global $prevMsg, $logfile;

  if ($prevMsg != $str)
  {

    $prevMsg = $str;
    $fileHandle = fopen($logfile,"a") or die("Kan logfile $logfile niet openen voor schrijven");
    $timestamp = date("Ymd H:i:s")." >> ".$str;

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
}


