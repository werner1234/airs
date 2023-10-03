<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/09/11 15:46:54 $
File Versie					: $Revision: 1.5 $

$Log: importOptiestatistieken.php,v $
Revision 1.5  2019/09/11 15:46:54  rvv
*** empty log message ***

Revision 1.4  2019/09/08 06:46:59  rvv
*** empty log message ***

Revision 1.3  2019/09/07 16:06:15  rvv
*** empty log message ***

Revision 1.2  2019/01/26 19:31:35  rvv
*** empty log message ***

Revision 1.1  2019/01/02 16:15:10  rvv
*** empty log message ***

*/

if (empty($_error))
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
  $prb->setLabelValue('txt1','Verwerken tijdelijke tabel');
  $pro_step = 0;
  $importfile = $_FILES['importfile']['tmp_name'];

    $csvRegels = count(file($importfile));
    $pro_multiplier = 100 / $csvRegels;
    $prb->setLabelValue('txt1','Importeren uit CSV bestand ('.$csvRegels.' records)');
    $html="Optiestatistieken<br>";
    $html.="<table>";
    
    $row=0;
    $tel=0;
    $handle = fopen($importfile, "r");
    
    $DB = new DB();

    $xlsData=array(array('Fonds','imporcode','ingelezen'));
    
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
    {
      $row++;
      if($row==1)
      {
        continue;
      }
     
      $pro_step += $pro_multiplier;
      $prb->moveStep($pro_step);
     
      $data[0] = trim($data[0]); // Instrument
      $data[1] = trim($data[1]); // Delta
      $data[2] = trim($data[2]); // Omega
      $data[3] = trim($data[3]); // Theta
      $data[4] = trim($data[4]); // Vega
      $data[5] = trim($data[5]); // Gamma
      $data[6] = trim($data[6]); // Rho
      $data[7] = trim($data[7]); // Commentary
      $data[8] = trim($data[8]); // fondsimportcode
  
      $importeren=false;
      $veldConversie=array(1=>'delta',2=>'omega',3=>'theta',4=>'vega',5=>'gamma',6=>'rho');
  
  /*
      `id` int(11) NOT NULL AUTO_INCREMENT,
  `fonds` varchar(25) NOT NULL,
  `datum` date DEFAULT '0000-00-00',
  `delta` double NOT NULL DEFAULT '0',
  `omega` double NOT NULL DEFAULT '0',
  `` double NOT NULL DEFAULT '0',
  `` double NOT NULL DEFAULT '0',
  `` double NOT NULL DEFAULT '0',
  `` double NOT NULL DEFAULT '0',
  `change_user` varchar(10) DEFAULT NULL,
  `change_date` datetime DEFAULT NULL,
  `add_user` varchar(10) DEFAULT NULL,
  `add_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fonds` (`fonds`)
  
 [optiestatistieken] => 1
    [overschrijven] => 1
   
)
      */
      
      $query="SELECT fonds,identifierVWD,identifierFactSet,FondsImportCode FROM Fondsen WHERE FondsImportCode='".$data[7]."'";
      $DB->SQL($query);
      $DB->Query();
      $aantalRecords=$DB->records();
      $fondsdata=$DB->nextRecord();
      if($fondsdata['fonds']=='')
      {
        echo "Geen fonds bij FondsImportCode '".$data[7]."' gevonden.<br>\n";
        $xlsData[]=array('',$data[7],'niet ingelezen','Geen fonds gevonden');
        continue;
      }
      if($aantalRecords>1)
      {
        $eersteRecord=$fondsdata;
        echo "Meerdere fondsen met importcode '".$data[7]."' gevonden.";
        echo " (".$fondsdata['fonds'].") ";
        while($fondsdata=$DB->nextRecord())
        {
          echo "(".$fondsdata['fonds'].") ";
        }
        echo "<br>\n";
        $fondsdata=$eersteRecord;
        //exit;
      }
      $importDatum=date('Y-m-d',form2jul($_POST['optiestatistiekenDatum']));

      $query="SELECT id,fonds,datum FROM fondsenOptiestatistieken WHERE fonds='".mysql_real_escape_string($fondsdata['fonds'])."' and datum='$importDatum'";
      $DB->SQL($query);
      $aanwezig=$DB->lookupRecord();
      if($aanwezig['fonds'] <> '')
      {
        
        if($_POST['overschrijven']==1)
        {
          $optieQuery="UPDATE fondsenOptiestatistieken SET fonds='".mysql_real_escape_string($fondsdata['fonds'])."',datum='".$importDatum ."',change_date=now(),change_user='$USR' ";
          $optieWhere="WHERE id='".$aanwezig['id']."'";
        }
        else
        {
          $prb->hide();
          echo "Al een record voor fonds ".$aanwezig['fonds'] ." (import code ".$data[7].")  op ".$aanwezig['datum'] ." gevonden.<br>\n";
         // echo "(Gebruik het \"Aanwezige koersen overschrijven vinkje om de records bij te werken.\")<br>\n";
          $xlsData[]=array('',$data[7],'niet ingelezen','Record al aanwezig.');
          //exit;
          continue;
        }

      }
      else
      {
        $optieQuery = "INSERT INTO fondsenOptiestatistieken SET fonds='".mysql_real_escape_string($fondsdata['fonds'])."',datum='".$importDatum."',change_date=now(),change_user='$USR',add_date=now(),add_user='$USR' ";
        $optieWhere='';
      }
      
      

      for($i=1;$i<7;$i++)
      {
        if(round($data[$i],8) <> 0.0)
        {
          $importeren=true;
        }
        $optieQuery.=",".$veldConversie[$i]."='".$data[$i]."'";
      }
    
        
      if($importeren==true)
      {
        $xlsData[]=array($fondsdata['fonds'],$data[7],'ingelezen');
        $DB->SQL("$optieQuery $optieWhere");
        $DB->query();
        $tel++;
      }
      else
      {
        $xlsData[]=array($fondsdata['fonds'],$data[7],'niet ingelezen','Overgeslagen. (Geen data.)');
        echo "Import van '".$fondsdata['fonds']."' (".$data[7].") overgeslagen. (Geen data.)<br>\n";
      }
       // $query="SELECT Datum,Koers FROM Fondskoersen WHERE Fonds='".mysql_real_escape_string($fondsdata['Fonds'])."' AND Datum<='".date("Y-m-d",form2jul($data[3]))."' order by Datum desc limit 1";
      //  $DB->SQL($query);
       // $importKoers=$DB->lookupRecord();
      
    }
    fclose($handle);
    $prb->hide();
    echo "Klaar. $tel van de $row records geimporteerd. <br>\n";
    writeXlsx($xlsData,'optieStatImport.xlsx');
    echo "<a href='optieStatImport.xlsx'><b>Download optieStatImport.xlsx</b></a>";
  
}
echo $_error;
exit;


?>