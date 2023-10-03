<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/06/02 11:48:35 $
File Versie					: $Revision: 1.1 $

$Log: Factuur_L77.php,v $
Revision 1.1  2019/06/02 11:48:35  rvv
*** empty log message ***

Revision 1.17  2019/04/17 11:21:04  rvv
*** empty log message ***

Revision 1.16  2018/07/11 16:15:22  rvv
*** empty log message ***

Revision 1.15  2018/07/07 17:34:05  rvv
*** empty log message ***

Revision 1.14  2018/04/21 17:57:30  rvv
*** empty log message ***

Revision 1.13  2017/09/09 18:15:36  rvv
*** empty log message ***

Revision 1.12  2017/04/23 12:50:36  rvv
*** empty log message ***

Revision 1.11  2016/07/16 15:15:15  rvv
*** empty log message ***

Revision 1.10  2016/01/18 20:31:28  rvv
*** empty log message ***

Revision 1.9  2016/01/18 19:34:52  rvv
*** empty log message ***

Revision 1.8  2016/01/18 19:15:18  rvv
*** empty log message ***

Revision 1.7  2016/01/18 06:57:40  rvv
*** empty log message ***

Revision 1.6  2016/01/17 18:17:14  rvv
*** empty log message ***

Revision 1.5  2015/01/24 19:53:41  rvv
*** empty log message ***

Revision 1.4  2014/10/19 08:53:58  rvv
*** empty log message ***

Revision 1.3  2014/10/15 16:07:30  rvv
*** empty log message ***

Revision 1.2  2014/10/08 15:44:12  rvv
*** empty log message ***

Revision 1.1  2014/04/10 06:02:04  rvv
*** empty log message ***

Revision 1.5  2013/12/23 16:43:32  rvv
*** empty log message ***

Revision 1.4  2013/07/19 07:11:17  rvv
*** empty log message ***

Revision 1.3  2013/07/18 17:46:37  rvv
*** empty log message ***

Revision 1.2  2013/07/17 08:13:15  rvv
*** empty log message ***

Revision 1.1  2013/06/15 15:55:44  rvv
*** empty log message ***

Revision 1.3  2013/04/27 16:28:55  rvv
*** empty log message ***

*/


    global $__appvar;
		$this->pdf->rapport_type = "FACTUUR";
    

    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=5;

	
		$this->pdf->AddPage('P');


$logo=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAcoAAABkCAMAAAAxI4DEAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjU3MjlGRjdDNUJGQjExRTg4RDNDRDE3REFENjZDRjcwIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjU3MjlGRjdENUJGQjExRTg4RDNDRDE3REFENjZDRjcwIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NTcyOUZGN0E1QkZCMTFFODhEM0NEMTdEQUQ2NkNGNzAiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NTcyOUZGN0I1QkZCMTFFODhEM0NEMTdEQUQ2NkNGNzAiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7vwjEPAAADAFBMVEWEjJSQmJ8YKDdsy9xMWWSWnaSKkporOUYdLTseLjxueIE5R1MLHCtpytv8/P39/v8vPkvl9vnO7vNZZW/JzdD7+/z9/f3S1djz8/QNHi309fZqdH0aKjgRITAWJjQMHSzx8vPw8fIWJzV+h4/T1tkUJDIpOEXZ297j5ectPEl9ho7O0tUnNkPy8/N5govu7/BncXu/w8f29/hSXmmnrbPQ09bY292boqlFUl5zfYYyQE26v8NLWGN/iJD5+vpUYGq7wMQOHy18hY3r7O5caHLFycza3N/P0tXt7u9XYm2xt7z3+PgPIC4/TFjq6+2SmqCorrM6SFQQIC9ibXchMD76+/vs7e4bKzm+wsbd3+GZoKYXKDb4+fnp6+zv8PETIzLe4OJaZnD19vc2RFAVJTPIzNBQXGfh4+XU19oSIjH09PXk5ugiMT9bZ3E8SVXf4uRWYWx7hIxYY241Q1BATVni5OZKV2IsOkcoN0QmNULBxcmBiZHW2ds+S1dpc3zN0dREUV0wP0tJVmEgMD3R1Nfb3d9OWmXn6eqNlZwzQU7M0NMsO0iOlp3g4+RyfIW2u8C8wcXEyMu3vMGIkZiYn6VGU19HVF+ssre5vsLo6uvX2txdaXIjMkDl5+nc3uCDi5PV2Nqjqq/AxMjDx8tocnx0foY3RVE9Slaiqa5CUFtDUVxPW2bm6OmqsLWwtbq0ur7Iy8+co6leaXNtd4C4vcHGys07SVXHys7KztElNEGpr7RVYWt2gIguPUqvtLk0Qk9ga3W9wsazub2us7hhbHa1ur9CT1qkqrB3gYlveYJkb3jLz9KTmqGHkJersbafpqxRXWiaoacxQEyAiZCgp62xtrskM0GMlJtTX2mFjpWmrLJIVWDCxsqyuLyVnKOPl55weoNrdX5fanR1f4fe4eNsdn9xe4SepatjbneboqidpKqlq7F4gYqGj5ZZZG6CipKtsriJkpllcHlBTlpmcXqUm6J6g4vN7fOhqK7k9fnY8fbR7/SJ1ePP7vR2z97///8gTswjAAABAHRSTlP///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////8AU/cHJQAAGatJREFUeNrsnXdcFMm2gHUeCKy187wPZoSRDApKUlwRURAEAQVRiSJRcpKMREUMiChREBQVRcGcc84BXXPOaY3r5nD3vtS3qxkmdk/NTE/f392V84/YXVNdU1/XqXNOnarphf055W///z9Yj4hJrz9ns//zH1/+vQfeXwPl3778rx54fxGU/9GD8nNF+c1Cihvha3R0dMav4UjfMam0FPztflBH59DBNlfXWtfa9Tolq1MoakupjNc5tOtJW9uT9YfMo919SIqwLffjda13bR4tlGbX1WS1bVtoLOMr2ZXs2pXk/tmhjLGds4j8zqIhuZNyY/c8kb6z6/FNwd/7Tubm5s7Onevo6Dj3sdqEAa136hzs2NKYksak4+WmHjjQd+ovs2Ye+7o5yZsrUUZ3ulNu7qQts9TGCGWe2miytv2UOYzqCwWF7sjySBh+bsjFUpvPC2Xzcc075HeiEwCU4d5SdybGDRH8vQmW0To6xHmIme2GCelaBiDql9Z4G8mxzNnPwsvpO2YPn9fYcc/QDej1vW0VLl7G2xbWNfehem+hLO29lqRpxmWAig93/FEQtfnXl9l9A2MrMj4rlLZGYA/5ncQWAmXYJak7o02dBX9/BctoO3QPvojTnVoAsIZaSY5KHX283Dyib4O8S/vEugGQtixSXG+bwboe8pBtdjkRCG70J7/3YyGYvR++SC7j94DMkn9XlGyeDc+FrVKUixoBiK0kH5UtHWFGeN8OauKgUB41Fw4/3aSfTYGRxjIXEpSNnvz/hXuWGwBgEBApjdIsEtloXY8owPpAemvnSdB3YVDXQ3TfsdRK5EF5+uaHkbKk4dskkk9ZPu1THaCthGQnbHY893xf/1RdlaLcbY2PIVvSWxYexyoGwc7NKeEiUI41F71t8zAQ17nnUqVROjoI/r9gMBzx7dIox9khG22OD3zw3p7kjncASGsQPrQP65McKCPGahZay5AC04RDkp+xuds+q5/WQCOgjLhttD07IjGY56JSlPNg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');
$this->pdf->memImage($logo, 15,8,70);
    $font=$this->pdf->rapport_font;//'times';
//$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->pdf->SetY(85);
  	$this->pdf->SetFont($font,"",11);

   $this->DB = new DB();


    
		$this->pdf->SetY(55);
		$this->pdf->SetWidths(array(25,100+180));
		$this->pdf->SetAligns(array("L","L","L"));
		$this->pdf->row(array('',$this->waarden['CRM_naam']));
		if ($this->waarden['CRM_naam1'] !='')
		  $this->pdf->row(array('',$this->waarden['CRM_naam1']));
		$this->pdf->row(array('',$this->waarden['CRM_verzendAdres']));
		$plaats='';
    $plaats=$this->waarden['CRM_verzendPc'];
    if($this->waarden['CRM_verzendPlaats']!= '') 
      $plaats.=" ".$this->waarden['CRM_verzendPlaats'];
		$this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$this->waarden['CRM_verzendLand']));
		
    $this->pdf->SetWidths(array(25,100,80));

  //  $this->pdf->SetFont($font,"B",10);
  //  $this->pdf->row(array('',"Factuurnummer: ".sprintf("%06d",$this->waarden['factuurNummer'])));
//    $this->pdf->SetFont($font,"",10);
 //   $this->pdf->ln(6);
    
    $vanjul=db2jul($this->waarden['datumVan']);
		if(substr($this->waarden['datumVan'],5,5) != '01-01')
		  $vanjul+=86400;
   	$vanDatum=date("j",$vanjul)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$vanjul)],$this->pdf->rapport_taal)." ".date("Y",$vanjul);
    $totDatum=date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot']));
    $nu=date("j")." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y");
 

    $this->pdf->SetWidths(array(25,35,100));
    $this->pdf->SetAligns(array("L","L",'L','L'));
    $this->pdf->SetY(100);
    $telStatY=$this->pdf->GetY();
    $this->pdf->SetFont($font,"",10);
    $this->pdf->row(array('',vertaalTekst('Factuurnummer:',$this->pdf->rapport_taal),$this->factuurnummer));
    $this->pdf->row(array('',vertaalTekst('Factuurdatum:',$this->pdf->rapport_taal),$totDatum));
    $this->pdf->row(array('',vertaalTekst('Cliëntcode:',$this->pdf->rapport_taal),$this->waarden['client']));
    $this->pdf->row(array('',vertaalTekst('Uw BTW nummer:',$this->pdf->rapport_taal),$this->waarden['btwnr']));
    $this->pdf->SetY(140);
        $this->pdf->SetFont($font,"",10);

$this->pdf->SetAligns(array("L","L",'R','R'));
$this->pdf->SetWidths(array(25,95,10,20));

$this->pdf->row(array('',vertaalTekst("Hierbij zenden wij u onze nota inzake vermogensbeheer",$this->pdf->rapport_taal)));
$this->pdf->CellBorders = array('','U','','U');
$this->pdf->ln();
$this->pdf->row(array('',vertaalTekst("Omschrijving",$this->pdf->rapport_taal),'',vertaalTekst('Bedrag',$this->pdf->rapport_taal)));
unset($this->pdf->CellBorders);
$this->pdf->ln();
//listarray($this->waarden['basisRekenvermogen']);
if($this->waarden['performancefee'] <> 0)
{
  $this->pdf->row(array('',vertaalTekst('De high watermark bedraagt',$this->pdf->rapport_taal).' € '.$this->formatGetal($this->waarden['basisRekenvermogen']-$this->waarden['performancefeeRekenbedrag'],2)));
}
if($this->waarden['BeheerfeeMethode']==3)
{
  if ($this->pdf->portefeuilledata['Vermogensbeheerder'] == 'HAV')
  {
    $tmp=explode('.',round($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],6));
    $decimalen=strlen($tmp[1]);
    if($decimalen<2)
      $decimalen=2;
    $this->pdf->row(array('', $this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'], $decimalen) . "% x € " . $this->formatGetal($this->waarden['basisRekenvermogen'], 2)));
  }
}

$this->pdf->row(array('', "Advies-/beheerloon over het " . $this->waarden['kwartaal'] . "e kwartaal " . substr($this->waarden['datumVan'], 0, 4), '€', $this->formatGetal($this->waarden['beheerfeeBetalen'] - $this->waarden['administratieBedrag'] - $this->waarden['performancefee'], 2)));

if($this->waarden['performancefee'] <> 0)
  $this->pdf->row(array('',"Performance fee ".substr($this->waarden['datumVan'],0,4)." over € ".
  $this->formatGetal($this->waarden['performancefeeRekenbedrag'],2)."",'€',$this->formatGetal($this->waarden['performancefee'],2)));
if($this->waarden['administratieBedrag'] <> 0)
  $this->pdf->row(array('',"Kosten toezichthouders",'€',$this->formatGetal($this->waarden['administratieBedrag'],2)));
$this->pdf->row(array('',"BTW percentage ".$this->formatGetal($this->waarden['btwTarief'],0)."%",'€',$this->formatGetal($this->waarden['btw'],2)));
$this->pdf->ln(2);
$this->pdf->CellBorders = array('','T','','T');
$this->pdf->row(array('',' ',' ',' '));
unset($this->pdf->CellBorders);
$this->pdf->row(array('','Totaal','€',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
$this->pdf->ln(2);
$this->pdf->row(array('',' ',' ','---------------'));
$this->pdf->ln();
$this->pdf->SetWidths(array(25,150));
if($this->waarden['BetalingsinfoMee']==1)
  $this->pdf->row(array('','Wij verzoeken u bij betaling uw klantnummer en het factuurnummer te vermelden. Betaling dient binnen 15 dagen na factuurdatum te geschieden.'));
else
  $this->pdf->row(array('','Het factuurbedrag wordt conform afspraak binnen enkele dagen van uw rekening geïncasseerd.'));
//  listarray($this->waarden);

if($this->waarden['highwatermark']['hoogsteWaarde'] > 0 && $this->waarden['performancefee']<>0 &&  substr($this->waarden['datumTot'],5,5)=='12-31')
  $this->pdf->row(array('','De high watermark is vastgesteld op € '.$this->formatGetal($this->waarden['highwatermark']['hoogsteWaarde'],2)));
$this->pdf->ln();


//
$this->pdf->SetAutoPageBreak(false);
$this->pdf->setY(270);

$this->pdf->SetAligns(array("L","L","L",'L'));
$this->pdf->SetWidths(array(15,40,50,100));
$yPage=$this->pdf->getY();
$this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
$this->pdf->SetDrawColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
$this->pdf->line($this->pdf->marge+50,$yPage,$this->pdf->marge+50,$yPage+$this->pdf->rowHeight*4);
$this->pdf->line($this->pdf->marge+100,$yPage,$this->pdf->marge+100,$yPage+$this->pdf->rowHeight*4);
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$this->pdf->row(array('','','T'));
$this->pdf->row(array('','','F'));
$this->pdf->row(array('','',''));
$this->pdf->row(array('','','www.andreas-capital.com'));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$this->pdf->setY($yPage);
$this->pdf->SetTextColor(0);
$this->pdf->row(array('','','    (+352) 878297 1','Bank : BGL BNP Paribas S.A.: LU85 0030 5713 5761 0000'));
$this->pdf->row(array('','One on One','    (+352) 878297 26','BIC: BGLLLULL'));
$this->pdf->row(array('','1, route d\'Esch','info@andreas-capital.com','VAT N°:LU15646705 / RCSL: B43522'));
$this->pdf->row(array('','L-1470 Luxembourg','','Authorisation Ministry: N° 00116305/1'));
$this->pdf->getY($yPage);
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
$this->pdf->setY($yPage);
$this->pdf->row(array('','Andreas Capital S.A.','',''));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

$this->pdf->SetTextColor(0);
$this->pdf->SetAutoPageBreak(true,8);


$this->pdf->SetTextColor(0,0,0);
$this->pdf->rowHeight=$rowHeightBackup;
?>
