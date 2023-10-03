<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/02/21 07:19:30 $
File Versie					: $Revision: 1.21 $

$Log: RapportOIS_L33.php,v $
Revision 1.21  2019/02/21 07:19:30  rvv
*** empty log message ***

Revision 1.20  2019/02/20 16:50:39  rvv
*** empty log message ***

Revision 1.19  2019/02/16 19:23:35  rvv
*** empty log message ***

Revision 1.18  2019/02/13 14:50:15  rvv
*** empty log message ***

Revision 1.17  2019/02/11 06:55:48  rvv
*** empty log message ***

Revision 1.16  2019/02/10 14:26:19  rvv
*** empty log message ***

Revision 1.15  2019/02/09 18:40:17  rvv
*** empty log message ***

Revision 1.14  2018/09/06 15:32:16  rvv
*** empty log message ***

Revision 1.13  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.12  2018/02/21 17:15:09  rvv
*** empty log message ***

Revision 1.11  2018/02/17 19:18:57  rvv
*** empty log message ***

Revision 1.10  2016/06/29 16:02:20  rvv
*** empty log message ***

Revision 1.9  2016/06/25 16:57:02  rvv
*** empty log message ***

Revision 1.8  2015/07/05 07:44:49  rvv
*** empty log message ***

Revision 1.7  2015/06/27 15:52:41  rvv
*** empty log message ***

Revision 1.6  2013/11/09 16:20:41  rvv
*** empty log message ***

Revision 1.5  2013/04/03 14:58:34  rvv
*** empty log message ***

Revision 1.4  2013/03/23 16:19:36  rvv
*** empty log message ***

Revision 1.3  2013/03/02 17:14:06  rvv
*** empty log message ***

Revision 1.2  2013/02/27 17:04:41  rvv
*** empty log message ***

Revision 1.1  2013/01/30 16:58:23  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportHSE_L33.php");
include_once("rapport/rapportATTberekening.php");

class RapportOIS_L33
{
	function RapportOIS_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
  	$this->pdf = &$pdf;
	  $this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	  $this->db=new DB();
    $this->vastWhere=" AND TijdelijkeRapportage.hoofdcategorie='G-RISD' ";
    $this->vastWhereAtt=" AND HoofdBeleggingscategorien.Beleggingscategorie='G-RISD' ";
	  $this->hse=new RapportHSE_L33($this->pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->berekenTotaal();
    
    $this->verdeling='beleggingscategorie';
    $this->verdeling='beleggingssector';
    $this->metHoofdcategorie=true;
    //$this->verdeling='regio';
	  $this->pdf->rapport_type = "OIS";
		$this->pdf->rapport_titel = "Overzicht risicodragende beleggingen per sector (met beginkoers)";//"Vermogensoverzicht sectoren (met beginkoers)";



	}

	function formatGetal($waarde, $dec, $procent=false)
	{
    if($procent==true)
      $extra='%';
    else
      $extra='';
        
	  if($waarde==0)
	    return '';
	  else
  		return number_format($waarde,$dec,",",".").$extra;
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

  function formatAantal($waarde, $dec )
  {
    if($dec==0)
      return number_format($waarde,$dec,",",".");

    $getal = explode('.',$waarde);
    $decimaalDeel = $getal[1];
    if ($decimaalDeel != str_repeat('0',$dec))
    {
      for ($i = strlen($decimaalDeel); $i >=0; $i--)
      {
        $decimaal = $decimaalDeel[$i-1];
        if ($decimaal != '0' && !$newDec)
        {
          $newDec = $i;
        }
      }
      return number_format($waarde,$newDec,",",".");
    }
    else
      return number_format($waarde,$dec,",",".");
  }


	function writeRapport()
	{
	 

    $this->rapport();
	}

	function rapport()
	{
		global $__appvar;
    global $USR;
		$query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

				$query="SELECT SUM(if(TijdelijkeRapportage.`type` <> 'rente',actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind.",0)) as actuelePortefeuilleWaardeEuro,
				SUM(actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind.") as actuelePortefeuilleWaardeEuroMetRente
				FROM
				TijdelijkeRapportage
				Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds WHERE
		TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND 
    TijdelijkeRapportage.portefeuille='".$this->portefeuille."'  ".$this->vastWhere.$__appvar['TijdelijkeRapportageMaakUniek']."";
		$DB->SQL($query); //echo $query."<br>\n";
		$DB->Query();
		$actueleWaarde = $DB->nextRecord();
	
    if($this->pdf->rapport_type == "OIS")
		  $portefeuilleWaarde=$actueleWaarde['actuelePortefeuilleWaardeEuro'];
    else
      $portefeuilleWaarde=$actueleWaarde['actuelePortefeuilleWaardeEuroMetRente'];


//		else
//		{



		$this->pdf->AddPage();
    if($this->verdeling=='regio')
    {
      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->customPageNo;
    }
    else
	  	$this->pdf->templateVars['OISPaginas'] = $this->pdf->customPageNo;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);
    
//SUM(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.actueleValuta) AS historischeWaardeEuro,

$type=$this->verdeling;
$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
	  $query="SELECT
TijdelijkeRapportage.hoofdcategorieOmschrijving AS HcategorieOmschrijving,
TijdelijkeRapportage.historischeWaarde,
TijdelijkeRapportage.historischeValutakoers,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',(beginPortefeuilleWaardeEuro),0 )) / ".$this->pdf->ValutaKoersStart." AS beginPortefeuilleWaardeEuro,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.beginwaardeLopendeJaar,0))  as beginwaardeLopendeJaar,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.historischeWaarde,0)) as historischeWaarde,
SUM(IF(TijdelijkeRapportage.type = 'rente' , (actuelePortefeuilleWaardeEuro),0)) / ".$this->pdf->ValutaKoersEind." AS rente,
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro ,
 SUM(IF(TijdelijkeRapportage.type = 'fondsen',(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.actueleValuta),0
 )) AS historischeWaardeEuro,
IF(TijdelijkeRapportage.type = 'rekening' ,actuelePortefeuilleWaardeInValuta, totaalAantal) as totaalAantal,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.fonds,
TijdelijkeRapportage.".$type.",
TijdelijkeRapportage.".$type."Volgorde as afdrukvolgorde,
TijdelijkeRapportage.type,
TijdelijkeRapportage.".$type."Omschrijving as categorieOmschrijving
FROM
TijdelijkeRapportage
WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."'
".$this->vastWhere.$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening
ORDER BY afdrukvolgorde,TijdelijkeRapportage.".$type.",TijdelijkeRapportage.fondsOmschrijving";
		$DB->SQL($query);
		$DB->Query();

		$DB2=new DB();
    while($data = $DB->nextRecord())
    {
      if($data['rekening']<>'')
        $perf=array();//$this->hse->fondsPerformance(array('rekeningen'=>array($data['rekening'])));
      else
        $perf=$this->hse->fondsPerformance(array('fondsen'=>array($data['fonds'])));
      //echo $data['fonds']."<br>\n"; listarray($perf);
      if($_POST['anoniem'] !=1 && $data['rekening'] <> '')
      {
        //$data['fondsOmschrijving'].=' '.substr($data['rekening'],0,strlen($data['rekening'])-3);
        $query="SELECT Rekeningen.Rekening,Rekeningen.Valuta,
                if(Rekeningen.Depotbank <> '',rekeningBank.Omschrijving, Depotbanken.Omschrijving) as Omschrijving
                FROM
                Rekeningen
                Inner Join Portefeuilles ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
                Inner Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
                LEFT Join Depotbanken as rekeningBank ON Rekeningen.Depotbank = rekeningBank.Depotbank
                WHERE Rekeningen.Rekening='".$data['rekening']."' AND Portefeuilles.Portefeuille <> 'C_$USR'";
        $DB2->SQL($query);
		    $depot=$DB2->lookupRecord();
		    $data['fondsOmschrijving'] = $depot['Omschrijving'].' '.substr($data['rekening'],0,strlen($data['rekening'])-3);


      }
      if($this->metHoofdcategorie==true)
        $Hcategorie=$data['HcategorieOmschrijving'];
      else
        $Hcategorie='';

  
      if($this->pdf->rapport_type == "OIS")
         $data['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro']-$data['rente'];
      if($data['type']=='rekening')
        $resultaat=0;
      else
        $resultaat=$perf['resultaat'];//$data['actuelePortefeuilleWaardeEuro']-$data['beginPortefeuilleWaardeEuro'];

      $aandeel=$data['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde*100;

      $totalenCat[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['beginPortefeuilleWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['resultaat'] += $resultaat;
      $totalenCat[$data['categorieOmschrijving']]['aandeel'] += $aandeel;
      $totalenCat[$data['categorieOmschrijving']]['bijdrage'] += $perf['bijdrage'];

      $totalenHcat[$Hcategorie]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalenHcat[$Hcategorie]['historischeWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalenHcat[$Hcategorie]['resultaat'] += $resultaat;
      $totalenHcat[$Hcategorie]['aandeel'] += $aandeel;
      $totalenHcat[$Hcategorie]['bijdrage'] += $perf['bijdrage'];

      $totalen['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalen['beginPortefeuilleWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalen['resultaat'] += $resultaat;
      $totalen['aandeel'] += $aandeel;
      $totalen['bijdrage'] += $perf['bijdrage'];

      if($data['categorieOmschrijving'] <> $lastcategorieOmschrijving)
      {
        if(!empty($lastcategorieOmschrijving))
        {
          $this->pdf->CellBorders = array('','','','','','','T','T','T','T','T');
          if($this->pdf->rapport_type=='HUIS')
          {
            $this->pdf->row(array('','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),
                              $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['resultaat'],0),
                              '',
                              '',
                              $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],2,true),''));
          }
          else
          {
          $this->pdf->row(array('','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['resultaat'],0),
          '',
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['bijdrage']*100,2,true),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],2,true),''));
          }
          unset($this->pdf->CellBorders);
        }
        if($this->pdf->getY() > 185)
          $this->pdf->addPage();

        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

        if($Hcategorie <> $lastHcategorie)
        {
          if(!empty($lastHcategorie))
          {
            $this->pdf->ln(5);
            $this->pdf->CellBorders = array('','','','','','','T','T','T','T','T');
            $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastHcategorie,$this->pdf->rapport_taal),'','','','','',
            $this->formatGetal($totalenHcat[$lastHcategorie]['actuelePortefeuilleWaardeEuro'],0),
            $this->formatGetal($totalenHcat[$lastHcategorie]['resultaat'],0),
            '',
            $this->formatGetal($totalenHcat[$lastHcategorie]['bijdrage']*100,2,true),
            $this->formatGetal($totalenHcat[$lastHcategorie]['aandeel'],2,true),''));
            unset($this->pdf->CellBorders);
            $this->pdf->ln(10);
          }
          if($this->pdf->getY() > 185)
            $this->pdf->addPage();
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          $this->pdf->row(array(vertaalTekst($Hcategorie,$this->pdf->rapport_taal)));
          $this->pdf->ln(2);
        }
        $lastHcategorie=$Hcategorie;

        $this->pdf->row(array('  '.vertaalTekst($data['categorieOmschrijving'],$this->pdf->rapport_taal)));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      }
      
      $totalen['rente'] += $data['rente'];
      if($data['type']=='rekening')
        $afronding=0;
      else
        $afronding=6;
      if($this->pdf->rapport_type=='HUIS')
      {
        $this->pdf->row(array('  '.$data['fondsOmschrijving'],$data['valuta'],$this->formatAantal($data['totaalAantal'],$afronding),$this->formatGetal($data['actueleFonds'],2),$this->formatGetal($data['beginwaardeLopendeJaar'],2),
                          '',$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
                          $this->formatGetal($resultaat,0),
                          '','',
                          $this->formatGetal($aandeel,2,true)));//$aandeel  //$perf['weging']*100
      }
      else
      {
     $this->pdf->row(array('  '.$data['fondsOmschrijving'],$data['valuta'],$this->formatAantal($data['totaalAantal'],$afronding),$this->formatGetal($data['actueleFonds'],2),$this->formatGetal($data['beginwaardeLopendeJaar'],2),
      '',$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
      $this->formatGetal($resultaat,0),
      $this->formatGetal($perf['procent'],2,true),
      $this->formatGetal($perf['bijdrage']*100,2,true),
      $this->formatGetal($aandeel,2,true)));//$aandeel  //$perf['weging']*100
      }
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];

    }
//listarray($totalenCat);
    if(!empty($lastcategorieOmschrijving))
    {
      $this->pdf->CellBorders = array('','','','','','','T','T','T','T','T');
      if($this->pdf->rapport_type=='HUIS')
      {
        $this->pdf->row(array('','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),
                          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['resultaat'],0),
                          '',
                          '',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],1,true),''));
      }
      else
      {
      $this->pdf->row(array('','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['resultaat'],0),
      '',
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['bijdrage']*100,2,true),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],1,true),''));
      }
      unset($this->pdf->CellBorders);
    }

    if(!empty($lastHcategorie))
    {
      $this->pdf->ln(5);
      $this->pdf->CellBorders = array('','','','','','','T','T','T','T','T');
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastHcategorie,$this->pdf->rapport_taal),'','','','','',
      $this->formatGetal($totalenHcat[$lastHcategorie]['actuelePortefeuilleWaardeEuro'],0),
      $this->formatGetal($totalenHcat[$lastHcategorie]['resultaat'],0),
      '',
      $this->formatGetal($totalenHcat[$lastHcategorie]['bijdrage']*100,2,true),
      $this->formatGetal($totalenHcat[$lastHcategorie]['aandeel'],1,true),''));
      unset($this->pdf->CellBorders);
      $this->pdf->ln(10);
      if($this->pdf->getY() > 185)
        $this->pdf->addPage();
    }

    $this->pdf->CellBorders = array('','','','','','','T','T','T','T','T');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
    if($this->pdf->rapport_type=='HUIS')
    {
      $this->pdf->row(array(vertaalTekst("Totaal", $this->pdf->rapport_taal), '', '', '', '', '',
                        $this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'], 0),
                        $this->formatGetal($totalen['resultaat'], 0),
                        '',
                        '',$this->formatGetal($totalen['aandeel'], 1, true), ''));
    }
    else
    {
      $this->pdf->row(array(vertaalTekst("Totaal", $this->pdf->rapport_taal), '', '', '', '', '',
                        $this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'], 0),
                        $this->formatGetal($totalen['resultaat'], 0),
                        '',
                        $this->formatGetal($totalen['bijdrage'] * 100, 2, true),
                        $this->formatGetal($totalen['aandeel'], 1, true), ''));
    }
    unset($this->pdf->CellBorders);

    $query="SELECT valuta FROM TijdelijkeRapportage WHERE valuta <> '".$this->pdf->rapportageValuta."' AND portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatum."' GROUP BY valuta ";
    $regels = $DB2->QRecords($query);
//    echo $regels*4+12+$this->pdf->getY();exit;
    if($regels*4+12+$this->pdf->getY() > 185)
    {
      $this->pdf->pageBottom=array($this->pdf->pageTop[0],$this->pdf->GetY());
      $this->pdf->addPage();
      unset($this->pdf->pageTop);
    }
//listarray($this->pdf->pageTop);exit;
    if(isset($this->pdf->pageTop))
      $this->pdf->pageBottom=array($this->pdf->pageTop[0],$this->pdf->GetY());
    //$this->pdf->Line($this->pdf->pageTop[1],$this->pdf->pageTop[1],$this->pdf->pageTop[0],$this->pdf->GetY());

    //$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
    $this->pdf->Ln(-4);
		$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);

		//printSamenstellingResultaat_L33($this->pdf,$this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		//printAEXVergelijking_L33($this->pdf,$this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);

//		}
//$this->pdf->Ln(4);
	//	$this->pdf->pageBottom=array($this->pdf->pageTop[0],$this->pdf->GetY());
  //  if($this->pdf->getY() > 180)


    $this->pdf->SetWidths(array(280));
    $this->pdf->setY(190);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-1);
    $rowHeight=$this->pdf->rowHeight;
    $this->pdf->rowHeight=3.0;
    if($this->pdf->rapport_taal==1)
      $this->pdf->row(array('The prices used in this report are all from sources that are deemed reliable. To be able to report as quickly as possible, especially at quarter end, we will use estimates of valuations, whenever a definitive valuation is not available. As soon as a final valuation is available, this value will be entered in our systems. As a result, the value of the investment portfolio and the calculated performance may, especially at quarter end, be slightly different in subsequent quarterly reports.'));
    else
      $this->pdf->row(array('De in deze rapportage gebruikte effectenkoersen worden verkregen uit door ons betrouwbaar geachte bronnen. Omwille van de snelheid van rapporteren kan, met name op de kwartaalultimo, gebruik gemaakt worden van schatting van koersen, daar waar niet direct na de kwartaalultimo een definitieve koers beschikbaar is. Zodra de definitieve koers beschikbaar is, zal deze in onze systemen worden ingevoerd. Derhalve kan de waarde van de effectenportefeuille en daarmee ook het berekende beleggingsresultaat, met name per kwartaalultimo, in de opeenvolgende kwartaalrapportages enigszins van elkaar verschillen.'));
    $this->pdf->rowHeight=$rowHeight;
  }
  
  function berekenTotaal()
  {
    
    $DB=new DB();
		$query="SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
BeleggingssectorPerFonds.Regio,
Regios.Omschrijving as regioOmschrijving,
Regios.Afdrukvolgorde,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Inner Join Regios ON BeleggingssectorPerFonds.Regio = Regios.Regio
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Inner Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->pdf->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
AND Rekeningmutaties.Fonds <> ''  ".$this->vastWhereAtt."
GROUP BY Rekeningmutaties.Fonds
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Regios.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde,Fondsen.Omschrijving ";
			$DB->SQL($query); 
		  $DB->Query();
		  while($data = $DB->NextRecord())
		  {
		    $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		    $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
		    $perRegio[$data['Hoofdcategorie']]['omschrijving']=$data['regioOmschrijving']; //$data['Regio']
		    $perRegio[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];  //$data['Regio']
		    $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];//[$data['Regio']]
		    $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];//[$data['Regio']]
		    $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];//[$data['Regio']]
		    $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsValuta'][]=$data['Valuta'];//[$data['Regio']]
		    $alleData['fondsen'][]=$data['Fonds'];
		  }


		$query="SELECT
Rekeningmutaties.rekening,
Rekeningen.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving AS hoofdCategorieOmschrijving,
ValutaPerRegio.Regio,
Regios.Omschrijving as regioOmschrijving,
Regios.Afdrukvolgorde
FROM
Rekeningmutaties
Inner Join Rekeningen ON Rekeningmutaties.rekening = Rekeningen.Rekening
Inner Join CategorienPerHoofdcategorie ON Rekeningen.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Inner Join Beleggingscategorien ON Rekeningen.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Left Join Beleggingscategorien AS HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join ValutaPerRegio ON Rekeningen.Valuta = ValutaPerRegio.Valuta AND ValutaPerRegio.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Regios ON ValutaPerRegio.Regio = Regios.Regio
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."'  AND Rekeningen.Memoriaal=0  ".$this->vastWhereAtt." AND
Rekeningmutaties.Boekdatum >= '".$this->pdf->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
GROUP BY Rekeningen.rekening
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Regios.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde";

			$DB->SQL($query);
		  $DB->Query();
		  while($data = $DB->NextRecord())
		  {
		    $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		    $perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][]=$data['rekening'];
		    $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		    $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
		    $alleData['rekeningen'][]=$data['rekening'];
		  }

$this->totalen['gemiddeldeWaarde']=0;
$perfTotaal=$this->hse->fondsPerformance($alleData,true);
$this->totalen['gemiddeldeWaarde']=$perfTotaal['gemWaarde'];

    
  }
}
?>
