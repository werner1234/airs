<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/05/26 16:45:07 $
 		File Versie					: $Revision: 1.9 $

 		$Log: RapportVAR_L40.php,v $
 		Revision 1.9  2017/05/26 16:45:07  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/09/06 15:24:17  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/08/16 15:31:50  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/05/25 14:38:33  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2013/03/09 16:22:24  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/02/17 11:00:30  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/02/13 17:06:12  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/02/10 10:06:07  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/11/14 16:48:28  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/09/05 18:19:11  rvv
 		*** empty log message ***
 		
 	
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/PDFOverzicht.php");

//ini_set('max_execution_time',60);
class RapportVAR_L40
{
	function RapportVAR_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VAR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

    $this->pdf->rapport_titel = "Kenmerken van het vastrentende deel van uw portefeuille";//Rendement & Risicokenmerken\n
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";

		$this->perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;

    $this->pdf->addPage();
    $this->vastrentendeDeel();


	}


  function vastrentendeDeel()
  {
    
    $this->fillAll=array(1,1,1,1,1,1,1,1,1,1,1);
    $this->subtotaalCatBorders=array(array('T','L','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U','R'));
    $this->subtotaalVerBorders=array(array('L','U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U','R'));
    $this->kopVerBorders=array(array('L','T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T','R'));
    $this->subtotaalFondsBorders=array(array('L'),'','','','','','','','','',array('R'));
    $maanden=array(0,'jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');


  	$this->pdf->templateVars['VOLKVPaginas'] = $this->pdf->customPageNo;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);

		$this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$this->cashfow->genereerTransacties();
		$this->cashfow->genereerRows();
 
    $this->pdf->ln();

		$DB = new DB();
		$this->db = new DB();
		$this->vastWhere=" AND (Fondsen.Lossingsdatum <> '0000-00-00') AND TijdelijkeRapportage.beleggingscategorie <> 'OBL-SPE'";


   $query="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro
			  FROM TijdelijkeRapportage
			  Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds
			  WHERE TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND
			   TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere."";
    $DB->SQL($query);
    $waarde=$DB->lookupRecord();
    $waarde=$waarde['actuelePortefeuilleWaardeEuro'];

    $this->actueleWaardePortefeuille=$waarde;

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
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieVolgorde as Afdrukvolgorde,
TijdelijkeRapportage.type,
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving,
Fondsen.rating as fondsRating,
Fondsen.Lossingsdatum,
Fondsen.variabeleCoupon,
Fondsen.Renteperiode,
Fondsen.Rentedatum,
Fondsen.EersteRentedatum,
emittentPerFonds.emittent,
TijdelijkeRapportage.fonds,
emittenten.rating as emittentRating,
TijdelijkeRapportage.fondsEenheid
FROM
TijdelijkeRapportage
Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds
Left Join emittentPerFonds ON emittentPerFonds.Fonds = TijdelijkeRapportage.Fonds  AND emittentPerFonds.vermogensbeheerder='$beheerder'
LEFT Join emittenten ON emittentPerFonds.emittent = emittenten.emittent AND emittentPerFonds.vermogensbeheerder = '$beheerder'
WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."'
".$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening
ORDER BY 
TijdelijkeRapportage.beleggingscategorieVolgorde,
Fondsen.Lossingsdatum,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening";
		$DB->SQL($query); 
		$DB->Query();

		while ($data=$DB->nextRecord())
		{
      $rente=getRenteParameters($data['fonds'], $this->rapportageDatum);
      foreach($rente as $key=>$value)
        $data[$key]=$value;

      if($_POST['anoniem'] !=1 && $data['rekening'] <> '')
        $data['fondsOmschrijving'].=' '.substr($data['rekening'],0,strlen($data['rekening'])-3);
        
      if($data['categorieOmschrijving'] <> $lastcategorieOmschrijving)
      {
        if($lastcategorieOmschrijving <> '')
          $this->printTotaal($lastcategorieOmschrijving,'beleggingscategorie',$lastcategorie);
        $this->printKop($data['categorieOmschrijving'],'beleggingscategorie');
      }  


      $Hcategorie=$data['HcategorieOmschrijving'];
      if($Hcategorie=='')
        $Hcategorie='Hcat';

      //$data['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro']-$data['rente'];
      if($data['type']=='rekening')
        $ongerealiseerdResultaat=0;
      else
        $ongerealiseerdResultaat=$data['actuelePortefeuilleWaardeEuro']-$data['beginPortefeuilleWaardeEuro'];

      $aandeel=$data['actuelePortefeuilleWaardeEuro']/$this->actueleWaardePortefeuille;


      $totalen['rente'] += $data['rente'];
     // listarray($waarden);
     if($data['Lossingsdatum'] <> '')
        $lossingsJul = adodb_db2jul($data['Lossingsdatum']);
     else
        $lossingsJul=0;
        $rentedatumJul = adodb_db2jul($data['Rentedatum']);
        $renteVanafJul = adodb_db2jul(jul2sql($this->pdf->rapport_datum));

      $koers=getRentePercentage($data['fonds'],$this->rapportageDatum);

			  $renteDag=0;
			  if($data['variabeleCoupon'] == 1)
			  {
			    $rapportJul=adodb_db2jul($this->rapportageDatum);
			    $renteJul=adodb_db2jul($data['Rentedatum']);
          $renteStap=($data['Renteperiode']/12)*31556925.96;
          $renteDag=$renteJul;
          if($renteStap > 100000)
            while($renteDag<$rapportJul)
            {
              $renteDag+=$renteStap;
            }
			  }
     

$ytm=0;
$duration=0;
$modifiedDuration=0;

        if($lossingsJul > 0)
	      {

       $q = "SELECT Boekdatum as Boekdatum,Rekeningmutaties.Grootboekrekening,
        (Rekeningmutaties.Credit-Rekeningmutaties.Debet)*Valutakoers as waarde,
       Rekeningmutaties.Aantal*Fondsen.Fondseenheid as Aantal,
       Rekeningmutaties.Aantal*Rekeningmutaties.Fondskoers*Rekeningmutaties.Valutakoers*Fondsen.Fondseenheid AS fondsWaarde
       FROM
Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.Portefeuille='".$this->portefeuille."'
INNER JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
INNER JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE Rekeningmutaties.Fonds = '".$data['fonds']."' AND Transactietype <> 'B' AND Grootboekrekeningen.Kosten=0
ORDER BY Boekdatum ASC,Rekeningmutaties.Transactietype ASC";


        $this->db->SQL($q);
        $this->db->Query();
        $totalenFonds=0;
        $totalenFondsAlles=0;
        $fondsAantal=0;
        $minimaleBoekdatumFilter='';
			  while($transactie=$this->db->NextRecord())
        {
          $totalenFondsAlles+=$transactie['waarde']*-1;  
          $totalenFonds+=$transactie['fondsWaarde']; 
          $fondsAantal+=$transactie['Aantal']; 
          if($fondsAantal==0 && $transactie['Grootboekrekening']=='FONDS')
          {
            $totalenFonds=0;
            $totalenFondsAlles=0;
            $minimaleBoekdatumFilter="AND Boekdatum > '".$transactie['Boekdatum']."'";
          }
        }   
        
        
        $q = "SELECT Boekdatum, Bedrag as waarde, fondskoers
        From Rekeningmutaties 
       JOIN Rekeningen on Rekeningmutaties.Rekening=Rekeningen.Rekening AND Rekeningen.Portefeuille='".$this->portefeuille."'
       WHERE Fonds = '".$data['fonds']."' AND Grootboekrekening='FONDS' $minimaleBoekdatumFilter ORDER BY Boekdatum ASC LIMIT 1";
  	    $this->db->SQL($q);
        $this->db->Query();
			  $historie = $this->db->NextRecord(); 

        
        $data['historischeWaarde']=$totalenFonds/$fondsAantal;
        $jarenVanafBegin = ($lossingsJul-db2jul($historie['Boekdatum']))/31556925.96;
        $jarenInBezit = (time()-db2jul($historie['Boekdatum']))/31556925.96;
        if($jarenInBezit <1)
          $jarenInBezit=1;
      /// echo round($data['actuelePortefeuilleWaardeEuro'],2)." ".round($totalenFonds)." ".$data['fonds']."<br>\n";
        $rendementBijVerkoop=(($data['actuelePortefeuilleWaardeEuro']-$totalenFondsAlles)/$totalenFondsAlles)*100/$jarenInBezit;
     // echo $data['fondsOmschrijving']."<br>\n $rendementBijVerkoop =((".$data['actuelePortefeuilleWaardeEuro']."-$totalenFondsAlles)/$totalenFondsAlles)*100/$jarenInBezit; <br>\n";  
		  	  $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;
          
		  	  $p = $data['actueleFonds'];
	        $r = $koers['Rentepercentage']/100;
	        $b = $this->cashfow->fondsDataKeyed[$data['fonds']]['lossingskoers'];
	        $y = $jaar;

	        $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100;
          $ytmBegin=  $this->cashfow->bondYTM($historie['fondskoers'],$r,$b,$jarenVanafBegin)*100; //nog weging toevoegen voor aan/verkopen.
          
          
        //  echo $data['fonds']."  <br>\n $ytmBegin=  $this->cashfow->bondYTM(".$historie['fondskoers'].",$r,$b,$jarenVanafBegin)*100; <br>\n <br>\n";
	        $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;
      
      

          
    $this->cashfowFonds = new Cashflow($this->portefeuille,$historie['Boekdatum'],$this->pdf->rapport_datum,$this->pdf->debug,$data['fonds']);
		$this->cashfowFonds->genereerTransacties(db2jul($historie['Boekdatum']));
		$this->cashfowFonds->genereerRows();
    $durationAankoop=$this->cashfowFonds->waardePerFonds[$data['fonds']]['ActueelWaardeJaar']/$this->cashfowFonds->waardePerFonds[$data['fonds']]['ActueelWaarde'];

$data['rentePerJaar']=$this->cashfow->fondsDataKeyed[$data['fonds']]['lossingsWaarde']*($koers['Rentepercentage']/100);

	         $duration=$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaarde'];
	         if($data['variabeleCoupon'] == 1 && $renteDag <> 0)
	           $modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
	         else
	           $modifiedDuration=$duration/(1+$ytm/100);
	         $aandeel=$data['actuelePortefeuilleWaardeEuro']/$this->actueleWaardePortefeuille;

           $totalen['totaalAantal']+=$data['totaalAantal'];
           $totalen['yield']+=$koers['Rentepercentage']*$aandeel;
	         $totalen['ytm']+=$ytm*$aandeel;
           $totalen['ytmBegin']+=$ytmBegin*$aandeel;
	         $totalen['duration']+=$duration*$aandeel;
           $totalen['durationAankoop']+=$durationAankoop*$aandeel;
	         $totalen['modifiedDuration']+=$modifiedDuration*$aandeel;
	         $totalen['restLooptijd']+=$restLooptijd*$aandeel;
           
           $totalen['actuelePortefeuilleWaardeEuro']+=$data['actuelePortefeuilleWaardeEuro'];
           $totalen['historischeWaardeEuro']+=$data['historischeWaardeEuro'];
           $totalen['rentePerJaar']+=$data['rentePerJaar'];
           $totalen['rendementVerkoop']+=$rendementBijVerkoop*$aandeel;
           
            $this->totalen['beleggingscategorie']['yield']+=($koers['Rentepercentage']*$aandeel);
            $this->totalen['beleggingscategorie']['ytm']+=($ytm*$aandeel);
            $this->totalen['beleggingscategorie']['ytmBegin']+=($ytmBegin*$aandeel);
            $this->totalen['beleggingscategorie']['duration']+=($duration*$aandeel);
            $this->totalen['beleggingscategorie']['durationAankoop']+=($durationAankoop*$aandeel);
            $this->totalen['beleggingscategorie']['modifiedDuration']+=($modifiedDuration*$aandeel);
            $this->totalen['beleggingscategorie']['restLooptijd']+=($restLooptijd*$aandeel);
            $this->totalen['beleggingscategorie']['actuelePortefeuilleWaardeEuro']+=($data['actuelePortefeuilleWaardeEuro']);
            $this->totalen['beleggingscategorie']['historischeWaardeEuro']+=($data['historischeWaardeEuro']);
            $this->totalen['beleggingscategorie']['rentePerJaar']+=($data['rentePerJaar']);
            $this->totalen['beleggingscategorie']['rendementVerkoop']+=$rendementBijVerkoop*$aandeel;
            
            
            
	      }
	      else
	      {
	        $ytm=0;
	        $restLooptijd=0;
	        $duration=0;
	        $modifiedDuration=0;
	      }
        
        if($this->pdf->GetY() > 194)  
          $this->printKop(vertaalTekst($data['categorieOmschrijving'],$this->pdf->rapport_taal),'beleggingscategorie');   

        if($this->pdf->GetY() > 190)
          $this->pdf->CellBorders=$this->subtotaalVerBorders;
        else
          $this->pdf->CellBorders=$this->subtotaalFondsBorders; 
          
  
     
     if($renteDag==0)
       $renteDag=$rentedatumJul;
     
      if($data['beleggingscategorie']=='OBL-PERP')
      {
        $this->pdf->row(array('  '.$data['fondsOmschrijving'],
        $this->formatGetal($data['totaalAantal'],0),
        $this->formatGetal($data['historischeWaarde'],2),
        $this->formatGetal($data['actueleFonds'],2),
        $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
        date("d",$renteDag).'-'.$maanden[date("n",$renteDag)],
        $this->formatGetal($data['rentePerJaar'],0),'','','',''));  
      }
      else
        $this->pdf->row(array('  '.$data['fondsOmschrijving'],
        $this->formatGetal($data['totaalAantal'],0),
        $this->formatGetal($data['historischeWaarde'],2),
        $this->formatGetal($data['actueleFonds'],2),
        $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
        date("d",$renteDag).'-'.$maanden[date("n",$renteDag)],
        $this->formatGetal($data['rentePerJaar'],0),
        $this->formatGetal($ytmBegin,2),
        $this->formatGetal($ytm,2),
        $this->formatGetal($rendementBijVerkoop,2),
        $this->formatGetal($duration,2)));  
      
 
      
      
      
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];
      $lastcategorie=$data['beleggingscategorie'];
    }
    
    if($lastcategorieOmschrijving <> '')
      $this->printTotaal($lastcategorieOmschrijving,'beleggingscategorie',$lastcategorie);
    
   // $this->totalen['alles']=$totalen;
   // $this->printTotaal($lastcategorieOmschrijving,'alles');
    

    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->underlinePercentage);

  }
  
  
	function printTotaal($title, $type,$categorie='')
	{
	  if($type=='hoofdcategorie')
	  {
	    $space='';
      $this->pdf->SetFillColor(0,78,58); 
      $this->pdf->SetTextColor(255,255,255);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=$this->fillAll;
      $this->pdf->row(array("","",'','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
      $extraln=1;
	  }
	  if($type=='beleggingscategorie')
	  {
	    $space='  ';
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
/*
      $this->pdf->SetFillColor(200,200,200);
      $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
      */
	  }
	  if($type=='verdeling')
	  {
	    // echo $this->pdf->GetY()." $title <br>\n";
	    $space='    ';
      if($this->pdf->GetY() < 50 || $this->pdf->GetY() > 190)
        $this->pdf->CellBorders=$this->subtotaalCatBorders;
      else  
        $this->pdf->CellBorders=$this->subtotaalVerBorders;
	  }
    if($categorie=='OBL' || $categorie=='OBL-PERP')
    {
         $space='';
      $this->pdf->SetFillColor(0,78,58); 
      $this->pdf->SetTextColor(255,255,255);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=$this->fillAll;
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
      $extraln=1;
    }
    if($type=='alles')
	  {
	    $space='';
      $this->pdf->SetFillColor(0,78,58); 
      $this->pdf->SetTextColor(255,255,255);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=$this->fillAll;
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
      $extraln=1;
      $this->totalen[$type]['beginPortefeuilleWaardeEuro']=0;
      $this->totalen[$type]['eurResultaat']=0;
      $this->totalen[$type]['procentResultaat']=0;
      $title="portefeuile gemiddeld";  
	  }


	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell(150,4, $space.'Totaal '.$title, 0, "L");
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->setX($this->pdf->marge);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    
    if($title=='Liquiditeiten')
      $this->totalen[$type]['eurResultaat']=0;
    
    
    $factor=($this->actueleWaardePortefeuille/$this->totalen[$type]['actuelePortefeuilleWaardeEuro']);
    if($categorie=='OBL-PERP')
        $this->pdf->row(array('','','','',
      $this->formatGetal($this->totalen[$type]['actuelePortefeuilleWaardeEuro'],0),
      '',
      $this->formatGetal($this->totalen[$type]['rentePerJaar'],0),'','','',''));  
    else
    $this->pdf->row(array('','','','',
      $this->formatGetal($this->totalen[$type]['actuelePortefeuilleWaardeEuro'],0),
      '',
      $this->formatGetal($this->totalen[$type]['rentePerJaar'],0),
      $this->formatGetal($this->totalen[$type]['ytmBegin']*$factor,2)."",
      $this->formatGetal($this->totalen[$type]['ytm']*$factor,2)."",
      $this->formatGetal($this->totalen[$type]['rendementVerkoop']*$factor,2),
      $this->formatGetal($this->totalen[$type]['duration']*$factor,2)));  
    
    
    //$this->pdf->row(array("","",'','','','','','','','','','',''));
    
	
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders); 
    if($extraln==1) 
      $this->pdf->Ln();                  
		$this->totalen[$type]=array();
    $this->totalenRente[$type]=array();
	}

	function printKop($title, $type, $fontStyle="")
	{
	  $fill=0;
	  if($type=='hoofdcategorie')
	  {
	    $space='';
      
      if($this->pdf->GetY() > 185)
        $this->pdf->addPage();
      
      $this->pdf->SetFillColor(0,78,58); 
      $this->pdf->SetTextColor(255,255,255);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=$this->fillAll;
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
	  }
	  if($type=='beleggingscategorie')
	  {
	    $space='  ';
      if($this->pdf->GetY() > 190)
        $this->pdf->addPage();
      $this->pdf->SetFillColor(200,200,200);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=$this->fillAll;
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
	  }
	  if($type=='verdeling')
	  {
	   	//   echo $title." ".$this->pdf->GetY()."<br>\n";
	    $space='    ';
      if($this->pdf->GetY() < 50 || $this->pdf->GetY() > 190 )
        $this->pdf->CellBorders=$this->kopVerBorders;
      else
        $this->pdf->CellBorders=$this->subtotaalFondsBorders;
      $this->pdf->row(array("","",'','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
	  }
		$this->pdf->SetFont($this->pdf->rapport_font,$fontStyle,$this->pdf->rapport_fontsize);
		
		$this->pdf->SetX($this->pdf->marge);
    $width=array_sum($this->pdf->widthB);
		$this->pdf->MultiCell($width,4, $space.$title, 0, "L",$fill);
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

	}

}
?>