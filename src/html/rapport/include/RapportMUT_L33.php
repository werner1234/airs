<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/18 17:44:11 $
File Versie					: $Revision: 1.32 $

$Log: RapportMUT_L33.php,v $
Revision 1.32  2020/03/18 17:44:11  rvv
*** empty log message ***

Revision 1.31  2019/07/03 15:37:22  rvv
*** empty log message ***

Revision 1.30  2019/06/22 16:31:44  rvv
*** empty log message ***

Revision 1.29  2019/02/07 11:44:42  rvv
*** empty log message ***

Revision 1.28  2019/02/07 07:19:42  rvv
*** empty log message ***

Revision 1.27  2019/02/06 16:07:12  rvv
*** empty log message ***

Revision 1.26  2018/01/11 06:20:46  rvv
*** empty log message ***

Revision 1.25  2018/01/10 16:26:03  rvv
*** empty log message ***

Revision 1.24  2018/01/06 18:10:41  rvv
*** empty log message ***

Revision 1.23  2017/05/28 09:58:52  rvv
*** empty log message ***

Revision 1.22  2016/06/12 10:28:58  rvv
*** empty log message ***

Revision 1.21  2015/06/07 08:11:44  rvv
*** empty log message ***

Revision 1.20  2015/06/06 10:13:06  rvv
*** empty log message ***

Revision 1.19  2015/05/20 16:04:40  rvv
*** empty log message ***

Revision 1.18  2014/11/19 16:42:27  rvv
*** empty log message ***

Revision 1.17  2014/05/03 15:47:40  rvv
*** empty log message ***

Revision 1.16  2013/03/09 16:22:24  rvv
*** empty log message ***

Revision 1.15  2013/02/17 11:00:30  rvv
*** empty log message ***

Revision 1.14  2013/02/13 17:06:12  rvv
*** empty log message ***

Revision 1.13  2012/12/05 16:45:29  rvv
*** empty log message ***

Revision 1.12  2012/11/17 16:02:20  rvv
*** empty log message ***

Revision 1.11  2012/04/21 15:38:14  rvv
*** empty log message ***

Revision 1.10  2012/02/26 15:17:43  rvv
*** empty log message ***

Revision 1.9  2012/02/19 16:13:11  rvv
*** empty log message ***

Revision 1.8  2011/11/12 18:33:59  rvv
*** empty log message ***

Revision 1.7  2011/07/17 14:52:22  rvv
*** empty log message ***

Revision 1.6  2011/04/09 14:35:27  rvv
*** empty log message ***

Revision 1.5  2011/04/03 08:35:46  rvv
*** empty log message ***

Revision 1.4  2011/03/26 16:52:07  rvv
*** empty log message ***

Revision 1.3  2011/03/18 15:02:38  rvv
*** empty log message ***

Revision 1.2  2011/03/13 18:36:37  rvv
*** empty log message ***

Revision 1.1  2011/02/13 17:50:29  rvv
*** empty log message ***

Revision 1.1  2011/02/06 14:36:59  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/rapportATTberekening.php");

class RapportMUT_L33
{
	function RapportMUT_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Mutatieoverzicht";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec, $toonNul=false)
	{
	  if($waarde==0 && $toonNul==false)
	    return '';
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

  function printSubTotaal($title, $totaalA, $totaalB)
	{
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$totaal1 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5];
		$totaal2 = $totaal1 + $this->pdf->widthA[6];
	  $this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
	 	$this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());

		if(!empty($totaalA))
		{
			$totaalAtxt = $this->formatGetal($totaalA,2);
		}

		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,2);
		}

		$this->pdf->SetX($totaal1 - $this->pdf->widthA[5]);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthA[5],4,$title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[6],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[7],4,$totaalBtxt, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();
		return true;
	}

	function printKop($title, $type="default")
	{
		switch($type)
		{
			case "b" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
			break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
			break;
			case "i" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'i';
			break;
			default :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
			break;
		}

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
	}

	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$rapDatumTekst=date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
		$this->pdf->AddPage();
		$this->pdf->templateVars['MUTPaginas'] = $this->pdf->customPageNo;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);

		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != "" )
	    $koersQuery =	"  (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "1";

		foreach ($this->pdf->lastPOST as $key=>$value)
		{
		  if(substr($key,0,4)=='MUT_' && $value==1)
		  {
		    $grootboeken[]=substr($key,4);
		    $filter = 1;
		  }
		}

		if($filter == 1)
		{
		 $grootboekSelectie = implode('\',\'',$grootboeken);
	   $extraquery .= "AND Rekeningmutaties.Grootboekrekening IN('$grootboekSelectie')  ";
		}

		$query = "SELECT ".
			"Rekeningmutaties.Boekdatum,
Rekeningmutaties.Omschrijving,
ABS(Rekeningmutaties.Aantal) AS Aantal,
Rekeningmutaties.Debet AS Debet,
Rekeningmutaties.Credit AS Credit,
$koersQuery as rapportageKoers,
Rekeningmutaties.Valutakoers,
Rekeningmutaties.Rekening,
Rekeningmutaties.Valuta,
Rekeningmutaties.Grootboekrekening,
Rekeningmutaties.Afschriftnummer,
Grootboekrekeningen.Omschrijving AS gbOmschrijving,
Grootboekrekeningen.Opbrengst,
Grootboekrekeningen.Kosten,
Grootboekrekeningen.Afdrukvolgorde,
if(Rekeningmutaties.Grootboekrekening='RENOB',
 if(fondstrans.Fonds <> '',1,0),0) as fondskoppeling,
 if(Rekeningmutaties.Grootboekrekening='STORT',10,
 if(Rekeningmutaties.Grootboekrekening='ONTTR',20,
 if(Rekeningmutaties.Grootboekrekening='VMAR',30,
 if(Rekeningmutaties.Grootboekrekening='DIV',31,
 if(Rekeningmutaties.Grootboekrekening='VKSTO',45,
 if(Rekeningmutaties.Grootboekrekening='DIVBE',40,
 if(Rekeningmutaties.Grootboekrekening='TVDIV',43,
 if(Rekeningmutaties.Grootboekrekening='RENME',50,
 if(Rekeningmutaties.Grootboekrekening='RENOB',51,
 if(Rekeningmutaties.Grootboekrekening='RENTE',52,
 if(Rekeningmutaties.Grootboekrekening='ROER',52,
 if(Rekeningmutaties.Grootboekrekening='TOB',85,
 if(Rekeningmutaties.Grootboekrekening='KOST',70,
 if(Rekeningmutaties.Grootboekrekening='KOBU',71,
 if(Rekeningmutaties.Grootboekrekening='VALK',72,
 if(Rekeningmutaties.Grootboekrekening='KOBU',80,
 if(Rekeningmutaties.Grootboekrekening='Kruis',100,
90) ) ) ) ) ) ) ) ) ) ) ) ) ) ) ) ) as volgorde
".
			"FROM Rekeningmutaties 
      JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening 
      LEFT JOIN (SELECT Rekeningmutaties.Fonds,Rekeningmutaties.Boekdatum 
FROM Rekeningmutaties JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening  
WHERE Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND
 Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND  Rekeningmutaties.Grootboekrekening  IN('FONDS') GROUP BY Fonds,Boekdatum) as fondstrans 
ON Rekeningmutaties.Fonds=fondstrans.Fonds AND Rekeningmutaties.Boekdatum=fondstrans.Boekdatum      
      ".
			"WHERE  ".
			" Rekeningen.Portefeuille = '".$this->portefeuille."' ".
			"AND Rekeningmutaties.Verwerkt = '1' ".
			"AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
			"AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".$extraquery.
			"AND Grootboekrekeningen.Afdrukvolgorde IS NOT NULL ".
			"AND (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1')
ORDER BY volgorde,fondskoppeling desc,Rekeningmutaties.Grootboekrekening, Rekeningmutaties.Boekdatum, Rekeningmutaties.id";//Grootboekrekeningen.Afdrukvolgorde,

		$DB->SQL($query);
		$DB->Query();
		$directeOpbrengst=0;
		$stortingenOntrekkingen=0;
    $kruisAanwezig=0;
    $kruisposten=0;
		$overigKosten=0;
		$transKosten=0;
    $totalen=array();
		while($mutaties = $DB->nextRecord())
		{
		 // if($mutaties['Valuta']==$this->pdf->rapportageValuta)
		 //   $mutaties['rapportageKoers']=$mutaties['Valutakoers'];
      if($mutaties['volgorde'] == 51 && $mutaties['fondskoppeling'] == 1 )
        $mutaties['gbOmschrijving']='Meeverkochte rente';

			if($mutaties['gbOmschrijving']=='Kosten buitenland')
				$mutaties['gbOmschrijving']='Buitenlandkosten';
			
			if($mutaties['Grootboekrekening']=='BEW')
        $mutaties['gbOmschrijving']='Bankkosten effecten gerelateerd';
        
      if($mutaties['volgorde'] == 90)
      {
       // $mutaties['gbOmschrijving']='Overige kosten';
        $overigKosten +=($mutaties['Credit']-$mutaties['Debet'])* $mutaties['Valutakoers'] / $mutaties['rapportageKoers'];
      }
      if($mutaties['volgorde'] == 100)
      {
        $kruisAanwezig++;
        //$mutaties['gbOmschrijving']='Overige kosten';
        $kruisposten +=($mutaties['Credit']-$mutaties['Debet'])* $mutaties['Valutakoers'] / $mutaties['rapportageKoers'];
      }

      if($mutaties['volgorde'] == 70 ||$mutaties['volgorde'] == 71 ||$mutaties['volgorde'] == 72 || $mutaties['volgorde'] == 80 || $mutaties['volgorde'] == 85)
        $transKosten +=($mutaties['Credit']-$mutaties['Debet'])* $mutaties['Valutakoers'] / $mutaties['rapportageKoers'];

      if($mutaties['volgorde'] > 20 && $mutaties['volgorde'] < 70)
      {
        $directeOpbrengst+=($mutaties['Credit']-$mutaties['Debet'])* $mutaties['Valutakoers'] / $mutaties['rapportageKoers'];
      }

      if($mutaties['volgorde'] < 30)
      {
        $stortingenOntrekkingen+=($mutaties['Credit']-$mutaties['Debet'])* $mutaties['Valutakoers'] / $mutaties['rapportageKoers'];
      }

			if($lastCategorie <> $mutaties['gbOmschrijving'])
			{
			  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
			   if(isset($lastCategorie))
         {
           if($this->pdf->getY() > 175)
             $this->pdf->addPage();
           $this->pdf->CellBorders = array('','','','','','','T','T');
           $this->pdf->row(array('','','','','','',$this->formatGetal($totalen[$lastCategorie]['bedragEur'],2),''));
		       unset($this->pdf->CellBorders);
         }

         if($mutaties['volgorde'] > 20 && $stortingenOntrekkingen <> 0)
         {
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          $this->pdf->Cell(100,5,vertaalTekst('Totaal stortingen en onttrekkingen',$this->pdf->rapport_taal),0,0,'L');

          $this->pdf->setX($this->pdf->marge);
          $this->pdf->row(array('','','','','','','',$this->formatGetal($stortingenOntrekkingen,2)));
          $this->pdf->ln(10);
          $stortingenOntrekkingen=0;
         }

         if($mutaties['volgorde'] > 60 && $directeOpbrengst <> 0)
         {
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          $this->pdf->Cell(100,5,vertaalTekst('Totaal directe opbrengsten',$this->pdf->rapport_taal),0,0,'L');

          $this->pdf->setX($this->pdf->marge);
          $this->pdf->row(array('','','','','','','',$this->formatGetal($directeOpbrengst,2)));
          $this->pdf->ln(10);
          $directeOpbrengst=0;
         }

				if($mutaties['volgorde'] > 85 && $transKosten <> 0)
				{
					$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
					$this->pdf->Cell(100,5,vertaalTekst('Totaal transactiekosten',$this->pdf->rapport_taal),0,0,'L');
					$this->pdf->setX($this->pdf->marge);
					$this->pdf->row(array('','','','','','','',$this->formatGetal($transKosten,2)));
					$this->pdf->ln();
					$transKosten=0;
				}
         
         if($mutaties['volgorde'] > 90 && $overigKosten <> 0)
         {
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          $this->pdf->Cell(100,5,vertaalTekst('Totaal overige kosten',$this->pdf->rapport_taal),0,0,'L');
          $this->pdf->setX($this->pdf->marge);
          $this->pdf->row(array('','','','','','','',$this->formatGetal($overigKosten,2)));
          $this->pdf->ln();
					 $overigKosten=0;
         }
         
         $this->pdf->MultiCell(280,4,vertaalTekst($mutaties['gbOmschrijving'],$this->pdf->rapport_taal), 0, "L");
         $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			}

//listarray($this->pdf->widths);
			   if($this->pdf->GetStringWidth($mutaties['Omschrijving']) > 75 )
			   {
			     $newOmschrijving='';
			     $widthOmschrijving=0;
			     for($i=0; $i<strlen($mutaties['Omschrijving']); $i++)
			     {
             $char = $mutaties['Omschrijving'][$i];
			       $widthOmschrijving += ($this->pdf->CurrentFont['cw'][$char]*$this->pdf->FontSize/1000);
			       $newOmschrijving .= $char;
			       //echo "$newOmschrijving <br>\n";
			       if($widthOmschrijving > 72)
			       {
			         $mutaties['Omschrijving']= $newOmschrijving.'...';
			         break;
			       }
  		     }

			   }
	//
      //      $dots=round((120-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));


			$this->pdf->row(array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
											vertaalTekst($mutaties['Omschrijving'],$this->pdf->rapport_taal),
                      $mutaties['Valuta'],
                      $this->formatGetal($mutaties['Valutakoers'],4),
											$this->formatGetal(($mutaties['Credit']-$mutaties['Debet']),2),
											"",
											$this->formatGetal(($mutaties['Credit']-$mutaties['Debet']) * $mutaties['Valutakoers'] / $mutaties['rapportageKoers'],2)
											));

			$totalen[$mutaties['gbOmschrijving']]['bedragEur']+=($mutaties['Credit']-$mutaties['Debet'])*$mutaties['Valutakoers'] / $mutaties['rapportageKoers'];
      $lastCategorie= $mutaties['gbOmschrijving'];
		}

		if(isset($lastCategorie))
    {
     	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
     	$this->pdf->CellBorders = array('','','','','','','T','T');
     	$this->pdf->row(array('','','','','','',$this->formatGetal($totalen[$lastCategorie]['bedragEur'],2,true),''));
	  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  unset($this->pdf->CellBorders);
    }

		if($transKosten <> 0)
		{
			$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
			$this->pdf->Cell(100,5,vertaalTekst('Totaal transactiekosten',$this->pdf->rapport_taal),0,0,'L');
			$this->pdf->setX($this->pdf->marge);
			$this->pdf->row(array('','','','','','','',$this->formatGetal($transKosten,2)));
			$this->pdf->ln();
			$transKosten=0;
		}

		if($overigKosten <> 0)
		{
			$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
			$this->pdf->Cell(100,5,vertaalTekst('Totaal overige kosten',$this->pdf->rapport_taal),0,0,'L');
			$this->pdf->setX($this->pdf->marge);
			$this->pdf->row(array('','','','','','','',$this->formatGetal($overigKosten,2)));
			$this->pdf->ln();
			$overigKosten=0;
		}

    $this->pdf->pageBottom=array($this->pdf->pageTop[0],$this->pdf->GetY());

	}
}
?>