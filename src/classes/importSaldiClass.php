<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/01/20 08:26:10 $
 		File Versie					: $Revision: 1.7 $

 		$Log: importSaldiClass.php,v $
 		Revision 1.7  2010/01/20 08:26:10  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2009/03/14 11:39:57  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2009/01/14 12:50:22  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2009/01/14 10:53:56  cvs
 		boekjaar controle eruit op verzoek van Theo

 		Revision 1.3  2008/07/10 12:11:02  rvv
 		Rekening opzoeken ipv portefeuilleEUR

 		Revision 1.2  2008/06/30 07:38:58  rvv
 		*** empty log message ***

 		Revision 1.1  2008/06/30 06:53:52  rvv
 		*** empty log message ***


*/

if (!function_exists('array_intersect_key'))
{
  function array_intersect_key ($isec, $arr2)
  {
   $argc = func_num_args();
   for ($i = 1; !empty($isec) && $i < $argc; $i++)
   {
     $arr = func_get_arg($i);
     foreach ($isec as $k => $v)
     if (!isset($arr[$k]))
     unset($isec[$k]);
   }
   return $isec;
  }
}

class importSaldi
{
  function importSaldi()
  {
    $this->PortefeuilleVoorzetToevoegen = true;
    $this->datum ='';
  }

  function readXLS($xlsFile)
  {
    global $__appvar;
    include_once($__appvar["basedir"].'/classes/excel/XLSreader.php');
    $xls = new Spreadsheet_Excel_Reader();
    $xls->setOutputEncoding('CP1252');
    $xls->read($xlsFile);
    return $xls->sheets[0]['cells'];
  }

  function cleanXLSdata($data)
  {
    foreach ($data as $regelNr=>$regel)
    {
      if($regelNr <10)
      {
        if(strpos($regel[1],'van'))
        {
          $datum = trim($regel[2]);

          $cleanData['datum']= substr($datum,6,4).'-'.substr($datum,3,2).'-'.substr($datum,0,2);
          $cleanData['datum'] = jul2sql((db2jul($cleanData['datum'])-1000));
        }
      }

      if($regel[1] >0)
      {
        $cleanData['portefeuilles'][$regel[1]]['waarde']  =str_replace(',','.',$regel[4]);
        $cleanData['portefeuilles'][$regel[1]]['naam']    =$regel[2];
      }
    }
    return $cleanData;
  }

  function getPortefeuilles($eindDatum)
  {
    $db = new DB();
    if(!checkAccess())
      $extra = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR'";
    $query = "SELECT Portefeuille,Client , PortefeuilleVoorzet FROM Portefeuilles $extra WHERE Portefeuilles.Einddatum > '$eindDatum'";
    $db->SQL($query);
    $db->Query();
    while($data = $db->nextRecord())
    {
     if($this->PortefeuilleVoorzetToevoegen == true)
        $data['Portefeuille'] = $data['PortefeuilleVoorzet'].$data['Portefeuille'];
     $portefeuilles[$data['Portefeuille']] = $data;
    }
    return $portefeuilles;
  }

  function vergelijkPortefeuilles($invoerXLS,$invoerAirs)
  {

  $verschillen['aantalXLS']   = count($invoerXLS);
  $verschillen['aantalAirs']  = count($invoerAirs);

  foreach (array_diff_assoc($invoerAirs,$invoerXLS) as $portefeuille=>$data)
    $verschillen['alleenInXls'][] = $portefeuille;
  foreach ((array_diff_assoc($invoerXLS,$invoerAirs)) as $portefeuille=>$data)
    $verschillen['alleenInAirs'][] = $portefeuille;

  $verschillen['XlsWaardenBeiden']= array_intersect_key($invoerXLS,$invoerAirs) ;
   return $verschillen;
  }

  function vergelijkWaarden($invoerXLS,$invoerAirs)
  {
  $verschillen['aantalXLS']   = count($invoerXLS);
  $verschillen['aantalAirs']  = count($invoerAirs);

  foreach (array_diff_assoc($invoerAirs,$invoerXLS) as $portefeuille=>$data)
    $verschillen['alleenInXls'][] = $portefeuille;
  foreach ((array_diff_assoc($invoerXLS,$invoerAirs)) as $portefeuille=>$data)
    $verschillen['alleenInAirs'][] = $portefeuille;

  $verschillen['XlsWaarden']= array_intersect_key($invoerXLS,$invoerAirs) ;



    $verschillen['airs_invoer']=$invoerAirs;//;
    $verschillen['invoerXLS']= $invoerXLS;//
    return $verschillen;
  }

  function genereerToonMutaties($data)
  {
    $html .= "<table class=\"list_tabel\" cellspacing=\"0\">
    <tr class=\"list_kopregel\"><td><b>portefeuille</b></td><td>Laatste boekdatum</td><td>airsWaarde</td><td>xlsWaarde</td><td>mutatie</td><td>xlsNaam</td><td>airsnaam</td></tr>\n";
    foreach ($data as $portefeuille =>$waarden)
    {
      $waarden['LaatsteRekeningmutatie']['Boekdatum'] = substr($waarden['LaatsteRekeningmutatie']['Boekdatum'],0,10);
      if(db2jul($waarden['laatsteBoekdatum']) >= db2jul($this->datum))
        $waarden['laatsteBoekdatum'] = "<div style=\"color=#FF0000\">".$waarden['laatsteBoekdatum']."</div>";

     $html .= "\n<tr class=\"list_dataregel\">
                   <td class=\"listTableData\"> <b>$portefeuille</b></td>
                   <td class=\"listTableData\">".$waarden['LaatsteRekeningmutatie']['Boekdatum']."</td>
                   <td class=\"listTableData\">".round($waarden['airsWaarde'],2)."</td>
                   <td class=\"listTableData\">".round($waarden['xlsWaarde'],2)."</td>
                   <td class=\"listTableData\"><b>".round(($waarden['xlsWaarde']-$waarden['airsWaarde']),2)."</b></td>
                   <td class=\"listTableData\">".$waarden['xlsNaam']."</td>
                   <td class=\"listTableData\">".$waarden['airsnaam']."</td>
               </tr>\n";
    }
    $html .= "</table>";
    return $html;
  }

  function genereerMutaties($data)
  {
    $db = new DB();
    $html .= "<table class=\"list_tabel\" cellspacing=\"0\">
    <tr class=\"list_kopregel\">
      <td class=\"list_kopregel_data\"><b>portefeuille</b></td>
      <td class=\"list_kopregel_data\">Laatste boekdatum</td>
      <td class=\"list_kopregel_data\">airsWaarde</td>
      <td class=\"list_kopregel_data\">xlsWaarde</td>
      <td class=\"list_kopregel_data\">mutatie</td>
      <td class=\"list_kopregel_data\">xlsNaam</td>
      <td class=\"list_kopregel_data\">airsnaam</td>
      <td class=\"list_kopregel_data\">status</td></tr>\n";


    foreach ($data as $portefeuille =>$waarden)
    {
      $mutatieWaarde = round(($waarden['xlsWaarde']-$waarden['airsWaarde']),4);
      if($mutatieWaarde > 0)
      {
       $credit = $mutatieWaarde;
       $debet = '';
      }
      else
      {
       $credit = '';
       $debet = $mutatieWaarde * -1;
      }

      if($this->PortefeuilleVoorzetToevoegen)
          $portefeuille = substr($portefeuille,strlen($waarden['PortefeuilleVoorzet']));

      if($waarden['LaatsteRekeningmutatie']['Afschriftnummer'])
        $afschiftnummer = $waarden['LaatsteRekeningmutatie']['Afschriftnummer'] +1;
      else
        $afschiftnummer = date('Y')."001";

      if($waarden['LaatsteRekeningmutatie']['Boekdatum'])
      {
       $boekdatumJul=db2jul($waarden['LaatsteRekeningmutatie']['Boekdatum']);
       $boekdatumJaar = date('Y',$boekdatumJul);
      }

      /*
      // TODO nette boekjaar vraag maken en onderstaande weer inschakelen
      // cvs 14 jan 2009
      if($boekdatumJaar != date('Y') && $waarden['LaatsteRekeningmutatie']['Boekdatum'])
      {
        $fout[$portefeuille][]= "Laatste boekjaar komt niet overeen met huidige jaar.(Jaarafsluiting nodig?)";
      }
      *?
      /*
      if($waarden['LaatsteRekeningmutatie']['Rekening'])
      {
        if($portefeuille."EUR" == $waarden['LaatsteRekeningmutatie']['Rekening'])
          $rekening = $waarden['LaatsteRekeningmutatie']['Rekening'];
        else
        {
          $fout[$portefeuille][]= "Laatste boeking was niet naar rekening ".$portefeuille."EUR  maar ".$waarden['LaatsteRekeningmutatie']['Rekening'];
          $rekening = $portefeuille."EUR";
        }
      }
      else
      */
    //  {
        $db = new DB();
        $query = "SELECT Rekening FROM Rekeningen WHERE Valuta = 'EUR' AND Portefeuille = '$portefeuille' AND Rekening  like '%EUR%'";
        $db->SQL($query);
        $rekeningData = $db->lookupRecord();
        if(count($rekeningData)>0)
          $rekening = $rekeningData['Rekening'];
        else
        {
          $fout[$portefeuille][]= "Geen EUR rekening gevonden voor $portefeuille";
        }
    //  }


      if($boekdatumJul >= db2jul($this->datum))
      {
        $fout[$portefeuille][]= " Er is al een boeking op ".$waarden['LaatsteRekeningmutatie']['Boekdatum']." voor deze portefeuille.";
        $boekdatumFout = substr($waarden['LaatsteRekeningmutatie']['Boekdatum'],0,10);
      }
      elseif ($this->datum == '')
      {
        $fout[$portefeuille][]= "Ongeldige boekdatum.";
        $boekdatumFout = ' &nbsp Ongeldige boekdatum.';
      }
      else
        $boekdatumFout = ' &nbsp '.$waarden['LaatsteRekeningmutatie']['Boekdatum'] ;


     $query = "SELECT id FROM  Rekeningen WHERE Rekening = '$rekening'";
     if($db->QRecords($query) == 0)
      $fout[$portefeuille][]= "Rekening ".$portefeuille."EUR is niet aanwezig.";

      if(count($fout[$portefeuille]) > 0)
       $status = '<b>afgewezen</b>';
      else
       $status = 'Oké';

if(is_array($fout[$portefeuille]))
  $foutTekst = implode("\n",$fout[$portefeuille]);

        $html .= "\n<tr class=\"list_dataregel\">
                   <td class=\"listTableData\"> <b>$portefeuille</b></td>
                   <td class=\"listTableData\">".$boekdatumFout."</td>
                   <td class=\"listTableData\">".round($waarden['airsWaarde'],2)."</td>
                   <td class=\"listTableData\">".round($waarden['xlsWaarde'],2)."</td>
                   <td class=\"listTableData\"><b>".round(($waarden['xlsWaarde']-$waarden['airsWaarde']),2)."</b></td>
                   <td class=\"listTableData\">".$waarden['xlsNaam']."</td>
                   <td class=\"listTableData\">".$waarden['airsnaam']."</td>
                   <td class=\"listTableData\" title=\"$foutTekst\">".$status."</td>
               </tr>\n";

      $queries[$portefeuille][] = "INSERT INTO Rekeningmutaties SET
                 Rekening = '".$rekening."',
                 Afschriftnummer = '$afschiftnummer',
                 Volgnummer = '1',
                 Omschrijving = 'Saldo mutatie ".dbdate2form($this->datum)."',
                 Boekdatum = '".$this->datum."',
                 Grootboekrekening = 'MUT',
                 Valuta = 'EUR',
                 Aantal = '0',
                 Fondskoers = '0',
                 Debet = ROUND('$debet',2),
                 Credit = ROUND('$credit',2),
                 Bedrag = ROUND('$mutatieWaarde',2),
                 Transactietype = '' ,
                 Verwerkt = '1',
                 Memoriaalboeking = '0',
                 add_date = NOW(),
                 add_user = 'import',
                 change_date  = NOW(),
                 change_user = 'import';"."\n";


      $queries[$portefeuille][] = "INSERT INTO Rekeningafschriften SET
                 Rekening = '".$rekening."',
                 Saldo = '".$waarden['airsWaarde']."',
                 NieuwSaldo = '".$waarden['xlsWaarde']."',
                 Afschriftnummer = '$afschiftnummer',
                 Datum = '".$this->datum."',
                 Verwerkt = '1',
                 add_date = NOW(),
                 add_user = 'import',
                 change_date  = NOW(),
                 change_user = 'import';"."\n";
    }

    $html .= "</table>";

    $query = "SELECT id FROM Valutakoersen WHERE Valuta = 'EUR' AND Datum = '".$this->datum."'";
    if($db->QRecords($query) < 1)
    {
      $queries['Valutakoersen'][] = "INSERT INTO Valutakoersen SET Valuta = 'EUR', Datum = '".$this->datum."', Koers='1', add_date = NOW(), add_user = 'import',change_date  = NOW(),change_user = 'import';"."\n";
    }

    return array('fouten'=>$fout,'queries'=>$queries,'html'=>$html);

  }

}
?>