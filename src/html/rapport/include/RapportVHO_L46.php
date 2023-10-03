<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/02/10 14:26:19 $
File Versie					: $Revision: 1.7 $

$Log: RapportVHO_L46.php,v $
Revision 1.7  2019/02/10 14:26:19  rvv
*** empty log message ***

Revision 1.6  2019/02/09 18:40:17  rvv
*** empty log message ***

Revision 1.5  2018/09/15 17:45:24  rvv
*** empty log message ***

Revision 1.4  2017/05/10 14:44:58  rvv
*** empty log message ***

Revision 1.3  2016/01/13 17:11:59  rvv
*** empty log message ***

Revision 1.2  2013/05/08 15:40:21  rvv
*** empty log message ***

Revision 1.1  2013/04/17 16:00:15  rvv
*** empty log message ***

Revision 1.6  2012/04/14 16:51:17  rvv
*** empty log message ***

Revision 1.5  2011/11/16 19:22:09  rvv
*** empty log message ***

Revision 1.4  2011/10/12 17:57:09  rvv
*** empty log message ***

Revision 1.3  2011/09/25 16:23:28  rvv
*** empty log message ***

Revision 1.2  2011/09/10 17:54:37  rvv
*** empty log message ***

Revision 1.1  2011/05/08 09:42:27  rvv
*** empty log message ***

Revision 1.11  2011/04/13 14:58:34  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/rapportATTberekening.php");
include_once("rapport/include/ATTberekening_L34.php");

class RapportVHO_L46
{
	function RapportVHO_L46($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Vermogensoverzicht";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
	  if($waarde==0)
	    return '';
	  else
  		return number_format($waarde,$dec,",",".");
	}
  
	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
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
	  else
	   return number_format($waarde,$dec,",",".");
	}
  
  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

  function addHeader($categorie)
  {
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $tmpWidths=$this->pdf->widths;
    $tmpAligns=$this->pdf->aligns;
    $this->pdf->SetWidths(array(100));
    $this->pdf->SetAligns(array('L'));
    $this->pdf->row(array($categorie));
    $this->pdf->widths=$tmpWidths;
    $this->pdf->aligns=$tmpAligns;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  }



	function writeRapport()
	{
		global $__appvar;

		$this->pdf->AddPage();
		$this->pdf->templateVars['VHOPaginas'] = $this->pdf->customPageNo;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);
		 $totalen['rente']=0;
    $valutas=array();


		

		$query = "SELECT Vermogensbeheerders.VerouderdeKoersDagen , Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM (Portefeuilles, Clienten)  Join Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$maxDagenOud=$portefeuilledata['VerouderdeKoersDagen'];
		$rapDatumTekst=date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);


		$query="SELECT SUM(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE
		TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."'";
		$DB->SQL($query); //echo $query."<br>\n";
		$DB->Query();
		$actueleWaarde = $DB->nextRecord();
		$portefeuilleWaarde=$actueleWaarde['actuelePortefeuilleWaardeEuro'];





$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
	  $query="SELECT
Sum(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.historischeWaarde,0)) AS historischeWaarde,
TijdelijkeRapportage.historischeValutakoers,
SUM(IF(TijdelijkeRapportage.type = 'rente' , (actuelePortefeuilleWaardeEuro),0)) AS rente,
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',actuelePortefeuilleWaardeInValuta,0)) AS actuelePortefeuilleWaardeInValuta,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.historischeValutakoers),0 )) AS historischeWaardeEuro,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid ),0 )) AS historischeWaardeValuta,
IF(TijdelijkeRapportage.type = 'rekening' ,actuelePortefeuilleWaardeInValuta, totaalAantal) as totaalAantal,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving,
TijdelijkeRapportage.beleggingscategorieVolgorde as Afdrukvolgorde,
TijdelijkeRapportage.type,
round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
TijdelijkeRapportage.Bewaarder,
Fondsen.ISINCode,
Fondsen.Lossingsdatum,
IF(TijdelijkeRapportage.type='rekening',1,0) as volgorde
FROM
TijdelijkeRapportage
LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.fonds
WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.Bewaarder,TijdelijkeRapportage.rekening
ORDER BY volgorde,TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.fondsOmschrijving";

		$DB->SQL($query);
		$DB->Query();
    $renteVanafJul = adodb_db2jul($this->rapportageDatum); 
    
    $this->pdf->SetFillColor($this->pdf->regelKleur['r'],$this->pdf->regelKleur['g'],$this->pdf->regelKleur['b']);
    $totaalBelegd=false;
    $n=0;
    while($data = $DB->nextRecord())
    {
  
      if($this->pdf->rapportageValuta<>$data['valuta'])
        $valutas[$data['valuta']]=$data['valuta'];
      
      if($data['Lossingsdatum'] <> '0000-00-00' && $data['Lossingsdatum'] <> '')
      {
        $lossingsJul = adodb_db2jul($data['Lossingsdatum']);
        $restLooptijd=($lossingsJul-$renteVanafJul)/31556925.96;
      }
      else
        $restLooptijd=0;
      
      
      
      if($data['rekening'] <> '')
        $data['fondsOmschrijving'].=' '.substr($data['rekening'],0,strlen($data['rekening'])-3);

      $data['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro']-$data['rente'];
      if($data['type']=='rekening')
      {
        $ongerealiseerdResultaat=0;
        $ongerealiseerdResultaatValuta=0;
      }
      else
      {
        $ongerealiseerdResultaat=$data['actuelePortefeuilleWaardeEuro']-$data['historischeWaardeEuro'];
        $ongerealiseerdResultaatValuta=$data['actuelePortefeuilleWaardeInValuta']-$data['historischeWaardeValuta'];
      }
      $aandeel=$data['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde*100;
      $ongerealiseerdResultaatProcent=($ongerealiseerdResultaat)/ABS($data['historischeWaardeEuro']) *100;
      $ongerealiseerdResultaatProcentValuta=($ongerealiseerdResultaatValuta)/ABS($data['historischeWaardeValuta']) *100;



      if($data['categorieOmschrijving'] <> $lastcategorieOmschrijving)
      {
        $this->pdf->fillCell=array();
        if(!empty($lastcategorieOmschrijving))
        {
          $this->pdf->CellBorders = array('','','','','','','','','','','TS','TS','TS','TS','TS','TS');
          $this->pdf->row(array('','','','','','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],2),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['historischeWaardeEuro'],2),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'],2),
          $this->formatGetal(($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'])/ABS($totalenCat[$lastcategorieOmschrijving]['historischeWaardeEuro'])*100,2),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['rente'],2),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],2),''));
          unset($this->pdf->CellBorders);
          $this->pdf->ln();
        }
        
        
        
      if($data['volgorde']==1 && $totaalBelegd==false)
      {
        $totaalBelegd=true;
        $this->pdf->cell(100,$this->pdf->rowHeight,'Totaal belegd');
        $this->pdf->SetX($this->pdf->marge);
        $this->pdf->row(array('','','','','','','','','','',$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],2),
          $this->formatGetal($totalen['historischeWaardeEuro'],2),
          $this->formatGetal($totalen['ongerealiseerdResultaat'],2),
          $this->formatGetal(($totalen['ongerealiseerdResultaat'])/ABS($totalen['historischeWaardeEuro'])*100,2),'',
          $this->formatGetal($totalen['aandeel'],2),''));
          $this->pdf->ln();
      }  
        
        
        
        
        if($this->pdf->getY() > 180)
          $this->pdf->addPage();
        $this->addHeader($data['categorieOmschrijving']);
      }
      
      $totalenCat[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['historischeWaardeEuro'] += $data['historischeWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalenCat[$data['categorieOmschrijving']]['aandeel'] += $aandeel;
      $totalenCat[$data['categorieOmschrijving']]['rente'] += $data['rente'];

      $totalen['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalen['historischeWaardeEuro'] += $data['historischeWaardeEuro'];
      $totalen['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalen['aandeel'] += $aandeel;
      
      $totalen['rente'] += $data['rente'];
      
      
      

      

      if($data['koersLeeftijd'] > $maxDagenOud && $data['actueleFonds'] <> 0)
			  $markering="*";
			else
			  $markering="";
        
        
      if($n % 2 == 0)
		   $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		  else
		   $this->pdf->fillCell=array();
	      
      $n++;
      
      if(strlen($data['fondsOmschrijving']) > 34)
      {
        $data['fondsOmschrijving']=substr($data['fondsOmschrijving'],0,31)."...";
      }
      
      if($data['categorieOmschrijving']=='Liquiditeiten')
        $vierDecimalen=false;
      else
        $vierDecimalen=true;  
      
      $this->pdf->row(array($this->formatAantal($data['totaalAantal'],0,$vierDecimalen),
                            $data['fondsOmschrijving'],
                            $data['ISINCode'],
                            $data['valuta'],
                            $this->formatGetal($data['actueleFonds'],2).$markering,
                            $this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],2),
                            $this->formatGetal($data['historischeWaarde'],2),
                            $this->formatGetal($data['historischeWaardeValuta'],2),
                            $this->formatGetal($ongerealiseerdResultaatValuta,2),
                            $this->formatGetal($ongerealiseerdResultaatProcentValuta,2),
                            $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],2),
                            $this->formatGetal($data['historischeWaardeEuro'],2),
                            $this->formatGetal($ongerealiseerdResultaat,2),
                            $this->formatGetal($ongerealiseerdResultaatProcent,2),
                            $this->formatGetal($data['rente'],2),
                            $this->formatGetal($aandeel,2),
                            $this->formatGetal($restLooptijd,2)
                            ));
                            
                    
                            
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];
    }

    $this->pdf->fillCell=array();
    if(!empty($lastcategorieOmschrijving))
    {
          $this->pdf->CellBorders = array('','','','','','','','','','','TS','TS','TS','TS','TS','TS');
          $this->pdf->row(array('','','','','','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],2),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['historischeWaardeEuro'],2),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'],2),
          $this->formatGetal(($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'])/ABS($totalenCat[$lastcategorieOmschrijving]['historischeWaardeEuro'])*100,2),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['rente'],2),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],2),''));
          unset($this->pdf->CellBorders);
          $this->pdf->ln();
    }

/*
    $this->pdf->CellBorders = array('','','','','','','','','','','TS','TS','TS','TS','TS','TS');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
          $this->pdf->row(array('','','','','','','','','','',$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],2),
          $this->formatGetal($totalen['historischeWaardeEuro'],2),
          $this->formatGetal($totalen['ongerealiseerdResultaat'],2),
          $this->formatGetal(($totalen['ongerealiseerdResultaat'])/ABS($totalen['historischeWaardeEuro'])*100,2),'',
          $this->formatGetal($totalen['aandeel'],2),''));
*/          
    unset($this->pdf->CellBorders);
    $this->pdf->cell(100,$this->pdf->rowHeight,'Opgelopen rente');
    $this->pdf->SetX($this->pdf->marge);
    $this->pdf->row(array('','','','','','','','','','',$this->formatGetal($totalen['rente'],2),'','','','',$this->formatGetal($totalen['rente']/$portefeuilleWaarde*100,2).''));
    $this->pdf->Ln();
    $this->pdf->cell(100,$this->pdf->rowHeight,'Totaal');
    $this->pdf->SetX($this->pdf->marge);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','','','','','','','','','',$this->formatGetal($totalen['rente']+$totalen['actuelePortefeuilleWaardeEuro'],2),'','','','',
    $this->formatGetal(($totalen['rente']/$portefeuilleWaarde*100)+($totalen['aandeel']),2)));

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
   $kleurAchter=$this->pdf->rapport_kop_bgcolor;
   $kleurText=$this->pdf->rapport_kop_fontcolor;
    $fontsize=$this->pdf->rapport_fontsize;
    if(count($valutas)>0 && $this->pdf->getY()>$this->pdf->pagebreak+(8+count($valutas)*4))
      $this->pdf->addPage();
    $this->pdf->rapport_fontsize=$this->pdf->rapport_fontsize-1;
    $this->pdf->rapport_kop_bgcolor=array('r'=>255,'g'=>255,'b'=>255);
    $this->pdf->rapport_kop_fontcolor=array('r'=>0,'g'=>0,'b'=>0);
    $this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
    $this->pdf->rapport_kop_bgcolor=$kleurAchter;
    $this->pdf->rapport_kop_fontcolor=$kleurText;
    $this->pdf->rapport_fontsize=$fontsize;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
    $query = "SELECT
OrderRegels.aantal,
Orders.fondsomschrijving,
Orders.transactieSoort,
Fondsen.fondsEenheid,
OrderRegels.valutakoers,
(SELECT OrderUitvoering.uitvoeringsPrijs FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as Fondskoers
FROM
OrderRegels
JOIN Orders ON OrderRegels.orderid = Orders.orderid
INNER JOIN Fondsen ON Orders.fonds = Fondsen.Fonds
WHERE OrderRegels.portefeuille = '".$this->portefeuille."' AND OrderRegels.Status < 3
ORDER BY Orders.fonds"; 
		$DB1 = new DB();
		$DB1->SQL($query); 
		$DB1->Query();
    $regels=$DB1->records();
		if($regels > 0)
		{
		  global $__ORDERvar;
      if($this->pdf->getY()+($regels*5+10) > 180)
        $this->pdf->addPage();
        
      
    $this->pdf->SetWidths(array(60,15,20,23));
    $this->pdf->SetAligns(array('L','L','R','R'));
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);  
	  $this->pdf->row(array("Lopende orders"));
    $this->pdf->row(array('Fonds','Transactie','Aantal','Transactiewaarde'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		while($data = $DB1->NextRecord())
		{
				$waardeEuro = $data['aantal'] * $data['Fondskoers'] * $data['fondsEenheid'] * $data['valutakoers'];
		   $this->pdf->Row(array($data['fondsomschrijving'],
                              $__ORDERvar["transactieSoort"][$data['transactieSoort']],
			                        $this->formatGetal($data['aantal'],0),
			                        $this->formatGetal($waardeEuro,2)			          
			                        ));
		               
			}
		$this->pdf->ln();		
	//	$this->printCol(5,vertaalTekst("Geschatte liquiditeiten na lopende orders"),"tekst");
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
	//	$this->printCol(7,$this->formatGetal($totaalLiquiditeitenEuro-$geschatteLiquiditeitenEuro,2),"totaal");
		$this->pdf->ln();	
		}


  }
}
?>
