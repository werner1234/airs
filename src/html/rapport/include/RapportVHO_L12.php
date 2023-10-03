<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/25 15:36:13 $
File Versie					: $Revision: 1.20 $

$Log: RapportVHO_L12.php,v $
Revision 1.20  2020/07/25 15:36:13  rvv
*** empty log message ***

Revision 1.19  2020/03/07 14:41:15  rvv
*** empty log message ***

Revision 1.18  2020/02/26 16:12:54  rvv
*** empty log message ***

Revision 1.17  2020/02/22 18:46:19  rvv
*** empty log message ***

Revision 1.16  2020/02/10 11:36:55  cvs
call 8190

Revision 1.15  2020/02/05 17:12:14  rvv
*** empty log message ***

Revision 1.14  2020/02/01 18:11:55  rvv
*** empty log message ***

Revision 1.13  2020/01/29 17:36:42  rvv
*** empty log message ***

Revision 1.12  2018/10/10 15:50:56  rvv
*** empty log message ***

Revision 1.11  2018/08/07 05:58:05  rvv
*** empty log message ***

Revision 1.10  2018/08/01 17:56:09  rvv
*** empty log message ***

Revision 1.9  2018/05/26 17:24:24  rvv
*** empty log message ***

Revision 1.8  2015/11/25 21:36:58  rvv
*** empty log message ***

Revision 1.5  2015/11/18 17:08:02  rvv
*** empty log message ***

Revision 1.4  2014/01/15 15:03:06  rvv
*** empty log message ***

Revision 1.29  2011/06/25 16:51:45  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVHO_L12
{
	function RapportVHO_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_VHO_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VHO_titel;
		else
			$this->pdf->rapport_titel = "Vergelijkend historisch overzicht vanaf start portefeuille";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->totaalWaarde=0;
    $this->pdf->rapport_VHO_decimaal=0;
	}

	function formatGetal($waarde, $dec)
	{
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

	function printSubTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF,$totaalG)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);

		$begin = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];
		//$totaal4 = $actueel + $this->pdf->widthB[9]+ $this->pdf->widthB[10]+ $this->pdf->widthB[11];
		//$totaal5 = $totaal4 + $this->pdf->widthB[12];

		$totaal4 = $actueel + $this->pdf->widthB[9];
		$totaal5 = $totaal4 + $this->pdf->widthB[11]+ $this->pdf->widthB[10] ;
		$totaal6 = $totaal5 +  $this->pdf->widthB[12];
		$totaal7 = $totaal6 + $this->pdf->widthB[13];

		$extra=0;//2;
		$this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);

		if(!empty($totaalA))
		{

			$totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_VHO_decimaal);
		}

		if(!empty($totaalB))
    {
      $totaalBtxt = $this->formatGetal($totaalB, $this->pdf->rapport_VHO_decimaal);

    }
		if(!empty($totaalC))
    {
      $totaalCtxt = $this->formatGetal($totaalC, $this->pdf->rapport_VHO_decimaal_proc) . "%";
    }

		if(!empty($totaalD))
		{
			$totaalDtxt = $this->formatGetal($totaalD,$this->pdf->rapport_VHO_decimaal);

		}

		if(!empty($totaalD2))
		{
			$totaalD2txt = $this->formatGetal($totaalD2,$this->pdf->rapport_VHO_decimaal_proc)."%";

		}

		if(!empty($totaalE))
		{
			$totaalEtxt = $this->formatGetal($totaalE,$this->pdf->rapport_VHO_decimaal);

		}

		if(!empty($totaalF))
		{
			$totaalFtxt =  $this->formatGetal($totaalF,$this->pdf->rapport_VHO_decimaal_proc);

		}
		if(!empty($totaalG))
		{
			$totaalGtxt = $this->formatGetal($totaalG,$this->pdf->rapport_VHO_decimaal);

		}
		$this->pdf->SetX(0);


		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->Cell($begin,4, $title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[10],4,$totaalCtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[11],4,$totaalDtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[12],4,$totaalEtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[13],4,$totaalGtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[14],4,$totaalFtxt, 0,1, "R");

    
    $volledigeLijn=false;
    if($volledigeLijn==true)
    {
      $this->pdf->Line($actueel + $extra, $this->pdf->GetY(), $actueel + $this->pdf->widthB[9] + $this->pdf->widthB[10] + $this->pdf->widthB[11] + $this->pdf->widthB[12] + $this->pdf->widthB[13] + $this->pdf->widthB[14], $this->pdf->GetY());
      $this->pdf->Line($actueel + $extra, $this->pdf->GetY() + 4, $actueel + $this->pdf->widthB[9] + $this->pdf->widthB[10] + $this->pdf->widthB[11] + $this->pdf->widthB[12] + $this->pdf->widthB[13] + $this->pdf->widthB[14], $this->pdf->GetY() + 4);
    }
    if(!empty($totaalA))
    {
      $this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
      $this->pdf->Line($begin+$extra,$this->pdf->GetY()-4,$begin + $this->pdf->widthB[5],$this->pdf->GetY()-4);
    }
    
    if(!empty($totaalB))
    {
      if($volledigeLijn==false)
      {
        $this->pdf->Line($actueel + $extra, $this->pdf->GetY(), $actueel + $this->pdf->widthB[9], $this->pdf->GetY());
        $this->pdf->Line($actueel + $extra, $this->pdf->GetY() - 4, $actueel + $this->pdf->widthB[9], $this->pdf->GetY() - 4);
      }
    }
    
    if(!empty($totaalD))
    {
      if($volledigeLijn==false)
      {
        $this->pdf->Line($totaal4 + $extra, $this->pdf->GetY(), $totaal4 + $this->pdf->widthB[10]+ $this->pdf->widthB[11], $this->pdf->GetY());
        $this->pdf->Line($totaal4 + $extra, $this->pdf->GetY() - 4, $totaal4 + $this->pdf->widthB[10]+ $this->pdf->widthB[11], $this->pdf->GetY() - 4);
      }
    }
    
    
    if(!empty($totaalE))
    {
      if($volledigeLijn==false)
      {
        $this->pdf->Line($totaal5 + $extra, $this->pdf->GetY(), $totaal5 + $this->pdf->widthB[13], $this->pdf->GetY());
        $this->pdf->Line($totaal5 + $extra, $this->pdf->GetY() - 4, $totaal5 + $this->pdf->widthB[13], $this->pdf->GetY() - 4);
      }
    }
    
    if(!empty($totaalF))
    {
      if($volledigeLijn==false)
      {
        $this->pdf->Line($totaal7+$extra,$this->pdf->GetY(),$totaal7 + $this->pdf->widthB[13],$this->pdf->GetY());
        $this->pdf->Line($totaal7+$extra,$this->pdf->GetY()-4,$totaal7 + $this->pdf->widthB[13],$this->pdf->GetY()-4);
      }
    }
    if(!empty($totaalG))
    {
      if($volledigeLijn==false)
      {
        $this->pdf->Line($totaal6+$extra,$this->pdf->GetY(),$totaal6 + $this->pdf->widthB[13],$this->pdf->GetY());
        $this->pdf->Line($totaal6+$extra,$this->pdf->GetY()-4,$totaal6 + $this->pdf->widthB[13],$this->pdf->GetY()-4);
      }
    }

	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF=0, $grandtotaal=false,$totaalG=0)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);

			$this->pdf->ln();

		$begin 	 = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];
		$totaal4 = $actueel + $this->pdf->widthB[9]+ $this->pdf->widthB[10];
		$totaal5 = $totaal4 + $this->pdf->widthB[11] ;//+ $this->pdf->widthB[12]
    $totaal6 = $totaal5 + $this->pdf->widthB[12] ;//+ $this->pdf->widthB[12]

		$extra=0;
		if(!empty($totaalA))
		{
			$totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_VHO_decimaal);
			if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
			{
//				$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
			}
			else
			{
	//			$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
			}
		}
		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_VHO_decimaal);

	//		if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
	//			$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
	//		else
	//			$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
		}


		if(!empty($totaalC))
			$totaalCtxt = $this->formatGetal($totaalC,$this->pdf->rapport_VHO_decimaal_proc)."%";

		if(!empty($totaalD))
		{
			$totaalDtxt = $this->formatGetal($totaalD,$this->pdf->rapport_VHO_decimaal);
	//		$this->pdf->Line($totaal4+$extra,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
	//		$this->pdf->Line($totaal4+$extra,$this->pdf->GetY()+4,$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY()+4);
		}

		if(!empty($totaalE))
		{
			$totaalEtxt = $this->formatGetal($totaalE,$this->pdf->rapport_VHO_decimaal);
		//	$this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[12],$this->pdf->GetY());
	//		$this->pdf->Line($totaal5+$extra,$this->pdf->GetY()+4,$totaal5 + $this->pdf->widthB[12],$this->pdf->GetY()+4);
		}

		if(!empty($totaalF))
		{
			$totaalFtxt = $this->formatGetal($totaalF,$this->pdf->rapport_VHO_decimaal_proc);
		}

		if(!empty($totaalG))//divident
		{
		  $totaalGtxt = $this->formatGetal($totaalG,$this->pdf->rapport_VHO_decimaal);
  //    $this->pdf->Line($totaal6+$extra,$this->pdf->GetY(),$totaal6 + $this->pdf->widthB[13],$this->pdf->GetY());
		}

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetX(0);



	//	$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->Cell($begin-$this->pdf->widthB[4],4, "", 0,0, "R");
      //if($grandtotaal==true)
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
			$this->pdf->Cell($this->pdf->widthB[4],4,$title, 0,0, "R");
      //if($grandtotaal==true)
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->Cell($this->pdf->widthB[5],4,$totaalAtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[10],4,$totaalCtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[11],4,$totaalDtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[12],4,$totaalEtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[13],4,$totaalGtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[14],4,$totaalFtxt, 0,1, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);



		if($grandtotaal)
		{
      /*
              if(!empty($totaalB))
              {
                $this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
                $this->pdf->Line($actueel+$extra,$this->pdf->GetY()-$this->pdf->rowHeight,$actueel + $this->pdf->widthB[9],$this->pdf->GetY()-$this->pdf->rowHeight);
              }
              if(!empty($totaalA))
              {
                $this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
                $this->pdf->Line($begin+$extra,$this->pdf->GetY()-$this->pdf->rowHeight,$begin + $this->pdf->widthB[5],$this->pdf->GetY()-$this->pdf->rowHeight);
              }
              if(!empty($totaalE))
              {
                $this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[12],$this->pdf->GetY());
                $this->pdf->Line($totaal5+$extra,$this->pdf->GetY()-$this->pdf->rowHeight,$totaal5 + $this->pdf->widthB[12],$this->pdf->GetY()-$this->pdf->rowHeight);
              }
              if(!empty($totaalD))
              {
                $this->pdf->Line($totaal4+$extra,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
                $this->pdf->Line($totaal4+$extra,$this->pdf->GetY()-$this->pdf->rowHeight,$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY()-$this->pdf->rowHeight);
              }
              if(!empty($totaalG))
              {
                $this->pdf->Line($totaal6+$extra,$this->pdf->GetY(),$totaal6 + $this->pdf->widthB[13],$this->pdf->GetY());
                $this->pdf->Line($totaal6+$extra,$this->pdf->GetY()-$this->pdf->rowHeight,$totaal6 + $this->pdf->widthB[13],$this->pdf->GetY()-$this->pdf->rowHeight);
              }
      
      */
          }
          else
          {
          //	$this->pdf->setDash(1,1);
      /*
              if(!empty($totaalB))
                $this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
              if(!empty($totaalA))
                $this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
              if(!empty($totaalE))
                $this->pdf->Line($totaal5,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[12],$this->pdf->GetY());
              if(!empty($totaalD))
                $this->pdf->Line($totaal4,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
              if(!empty($totaalG))
                $this->pdf->Line($totaal6,$this->pdf->GetY(),$totaal6 + $this->pdf->widthB[13],$this->pdf->GetY());
      
            $this->pdf->setDash();
      */
		}

		$this->pdf->ln();
		return $totaalB;
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
    if($type=='b')
      $this->pdf->line($this->pdf->marge,$this->pdf->getY(),$this->pdf->marge+$this->pdf->widthA[0],$this->pdf->getY(),array('color'=>array($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2])));
	}



	function getDividend($fonds)
	{
		global $__appvar;

		if($fonds=='')
			return 0;
   
    if ($this->pdf->portefeuilledata['RapportageValuta'] != "EUR" && $this->pdf->portefeuilledata['RapportageValuta'] != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->portefeuilledata['RapportageValuta']."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQuery = "";

		$query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE 
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$totaal=0;
		while($data = $DB->nextRecord())
		{
			if($data['type']=='rente')
				$rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
			elseif($data['type']=='fondsen')
				$aantal[$data['rapportageDatum']]=$data['totaalAantal'];
		}

		$totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
		$totaalCorrected=$totaal;

		$query="SELECT Boekdatum,(Debet*Valutakoers) $koersQuery as Debet,(Credit*valutakoers) $koersQuery as Credit,Bedrag,Rekeningmutaties.Omschrijving
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND ".
//     " Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND ".
			" Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND 
     Grootboekrekeningen.Opbrengst=1";
		$DB->SQL($query);
		$DB->Query();
		//echo "$query <br>\n";
		while($data = $DB->nextRecord())
		{
			$boekdatum=substr($data['Boekdatum'],0,10);
			if(!isset($aantal[$data['Boekdatum']]))
			{
				$fondsAantal=fondsAantalOpdatum($this->portefeuille,$fonds,$data['Boekdatum']);
				$aantal[$boekdatum]=$fondsAantal['totaalAantal'];
			}
			$aandeel=1;

			if($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
			{
				$aandeel=$aantal[$this->rapportageDatum]/$aantal[$boekdatum];
			}
			// echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
			$totaal+=($data['Credit']-$data['Debet']);
			$totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
		}

		return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
	}

	function addRente($categorie)
	{
	  global $__appvar;
		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
			" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersStart." subtotaalbegin, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersEind." subtotaalactueel FROM ".
			" TijdelijkeRapportage  ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND  ".
			" TijdelijkeRapportage.type = 'rente' AND TijdelijkeRapportage.Beleggingscategorie='$categorie' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.valuta ".
			" ORDER BY TijdelijkeRapportage.valutaVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		if($DB->records() > 0)
		{

			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),"b");

			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{
				if(!$this->pdf->rapport_HSE_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					$subtotaalPercentageVanTotaal = 0;

					if($this->pdf->rapport_VHO_geenvaluta == 1) {
					}
					else
						$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien['valuta'],"");

					// print detail (select from tijdelijkeRapportage)

					$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
						" TijdelijkeRapportage.actueleValuta , ".
						" TijdelijkeRapportage.rentedatum, ".
						" TijdelijkeRapportage.renteperiode, ".
						" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
						" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro, ".
						" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
						" FROM TijdelijkeRapportage WHERE ".
						" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
						" TijdelijkeRapportage.type = 'rente' AND TijdelijkeRapportage.Beleggingscategorie='$categorie' AND ".
						" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."'".
						" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
						.$__appvar['TijdelijkeRapportageMaakUniek'].
						" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
					debugSpecial($subquery,__FILE__,__LINE__);
					$DB2 = new DB();
					$DB2->SQL($subquery);
					$DB2->Query();
					while($subdata = $DB2->NextRecord())
					{

						if($this->pdf->rapport_HSE_rentePeriode)
						{
							$rentePeriodetxt = "  ".date("d-m",db2jul($subdata['rentedatum']));
							if($subdata['renteperiode'] <> 12 && $subdata['renteperiode'] <> 0)
								$rentePeriodetxt .= " / ".$subdata['renteperiode'];
						}

						$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($this->totaalWaarde/100);

						if($this->pdf->rapport_VHO_percentageTotaal == 1)
							$percentageTotaalTekst = $this->formatGetal($percentageVanTotaal,1)."%";
						else
							$percentageTotaalTekst = "";



						$subtotaalRenteInValuta += $subdata['actuelePortefeuilleWaardeEuro'];

						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						// print fondsomschrijving appart ivm met apparte fontkleur
						$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
						$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
						$this->pdf->setX($this->pdf->marge);

						$this->pdf->Cell($this->pdf->widthB[0],4,"");
						$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving'].$rentePeriodetxt );

						$this->pdf->setX($this->pdf->marge);

						$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

						if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
						{
							$this->pdf->row(array("","","","","","","","",
																$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VHO_decimaal),
																$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),
																$percentageTotaalTekst));
						}
						else
						{
							$this->pdf->row(array("","","","",
																$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VHO_decimaal),
																$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),
																"","","", "",
																$percentageTotaalTekst));
						}
					}

					// print subtotaal
					//$this->printSubTotaal("Subtotaal:", "", $subtotaalRenteInValuta);
					if($this->pdf->rapport_VHO_percentageTotaal ==1)
					{
						$percentageVanTotaal = ($subtotaalRenteInValuta) / ($this->totaalWaarde/100);
					}
					else
						$percentageVanTotaal = 0;

					if($this->pdf->rapport_VHO_geensubtotaal == 1)
					{
					}
					else
						$this->printSubTotaal('',"", $subtotaalRenteInValuta, $percentageVanTotaal, "", "");

					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien[subtotaalactueel];
				}
			}

			// totaal op rente
			if($this->pdf->rapport_VHO_percentageTotaal ==1)
			{
				$percentageVanTotaal = ($totaalRenteInValuta) / ($this->totaalWaarde/100);
			}
			else
				$percentageVanTotaal = 0;

			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal),"", $totaalRenteInValuta, $percentageVanTotaal,"");
		}
		return $actueleWaardePortefeuille;
	}


	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();


		$this->pdf->widthB = array(7,50+5,18,18,22,22,1,15,23,23,15-7,18,18,18,15);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(60,15+5,18,22,22,1,15,25,22,12-5,18,18,18,15);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');


		$this->pdf->AddPage();
    $this->pdf->SetLineWidth(0.2);

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];
		$this->totaalWaarde=$totaalWaarde;

		$actueleWaardePortefeuille = 0;

		$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM( if(TijdelijkeRapportage.type='rekening',actuelePortefeuilleWaardeEuro,(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.historischeValutakoers / TijdelijkeRapportage.historischeRapportageValutakoers))) AS subtotaalhistorisch, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersEind." AS subtotaalactueel ".
		" FROM TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type IN('fondsen','rekening') AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'" .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc,  TijdelijkeRapportage.valutaVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$lastBeleggingscategorie='leeg';
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if(!empty($lastCategorie) && $lastCategorie <> $categorien['Omschrijving']  )
			{
				if($this->pdf->rapport_VHO_percentageTotaal == 1)
				{
					$percentageVanTotaal = ($totaalactueel) / ($totaalWaarde/100);
				}
				else
				{
					$percentageVanTotaal = "";
				}

        $procentResultaat = (($totaalactueel - $totaalhistorisch + $totaaldividendCorrected) / ($totaalhistorisch /100));
				if($totaalhistorisch < 0)
					$procentResultaat = -1 * $procentResultaat;
				// attica ?
				//$procentResultaat = ($totaalvalutaresultaat / $totaalhistorisch) *100;
        if(trim($lastCategorie)<>'')
				{
//					$title = vertaalTekst("Subtotaal", $this->pdf->rapport_taal) . " " . vertaalTekst($lastCategorie, $this->pdf->rapport_taal);
					$title = vertaalTekst("Subtotaal", $this->pdf->rapport_taal);
					//function $this->printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE)

//echo $lastCategorie." ".$totaaldividend."<br>\n";
					$actueleWaardePortefeuille += $this->printTotaal($title, $totaalhistorisch, $totaalactueel, $percentageVanTotaal, $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat,false, $totaaldividend);
					                          ////function printTotaal($title, $totaalA,          $totaalB,        $totaalC,            $totaalD,              $totaalE,                 $totaalF=0,         $grandtotaal=false,$totaalG=0)
					
				}
				$totaalhistorisch = 0;
				$totaalactueel = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$procentResultaat = 0 ;
				$totaalGecombeneerdResultaat =0;
        $totaaldividend=0;
				$actueleWaardePortefeuille+=$this->addRente($lastBeleggingscategorie);
			}


			if($lastCategorie <> $categorien['Omschrijving'])
			{
				$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal), "b");
			}
			// subkop (valuta)
			if($this->pdf->rapport_VHO_geenvaluta == 1)
			{
			}
			else
			{
				$tekst = vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien['valuta'];
				$this->printKop($tekst, "");
			}

			// print detail (select from tijdelijkeRapportage)
			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.fonds,TijdelijkeRapportage.type,TijdelijkeRapportage.rekening, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.historischeWaarde, ".
			" TijdelijkeRapportage.historischeValutakoers, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
			TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro,
			TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
			" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."' AND ".
			" TijdelijkeRapportage.type IN('fondsen','rekening') AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery); 
			$DB2->Query();

			while($subdata = $DB2->NextRecord())
			{
				$dividend=$this->getDividend($subdata['fonds']);
				
				$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['historischeWaardeTotaal']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
				$fondsResultaatprocent = ($fondsResultaat / $subdata['historischeWaardeTotaal']) * 100;
//if($subdata['fondsOmschrijving']=='Apple Computer')
//echo "$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - ".$subdata[historischeWaardeTotaal].") * ".$subdata[actueleValuta]." / ".$this->pdf->ValutaKoersEind."<br>\n";
//if($subdata['fondsOmschrijving']=='Apple Computer')
//  echo "$fondsResultaatprocent = ($fondsResultaat / ".$subdata[historischeWaardeTotaal].") * 100 <br>\n";
				if($subdata['historischeWaardeTotaal'] < 0 && $fondsResultaat > 0)
				  $fondsResultaatprocent = -1 * $fondsResultaatprocent;

				$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VHO_decimaal_proc);
				$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['historischeWaardeTotaalValuta'] - $fondsResultaat;
 //if($subdata['fondsOmschrijving']=='Apple Computer')
 //  listarray($subdata);
  //echo "$valutaResultaat = ".$subdata['actuelePortefeuilleWaardeEuro']." - ".$subdata[historischeWaardeTotaalValuta]." - $fondsResultaat;<br>\n";       
        
				//$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
				//$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata[historischeWaardeTotaalValuta]) / ($subdata[historischeWaardeTotaalValuta] /100));
        $procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['historischeWaardeTotaalValuta'] + $dividend['corrected']) / ($subdata['historischeWaardeTotaalValuta'] /100));
     
        $gecombeneerdResultaat = $fondsResultaat + $valutaResultaat;

				if($subdata['historischeWaardeTotaalValuta'] < 0)
					$procentResultaat = -1 * $procentResultaat;

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VHO_decimaal_proc);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";
        $dividendtxt='';
        
				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VHO_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VHO_decimaal,$this->pdf->rapport_VHO_decimaal_proc);

 				if($dividend['totaal'] <> 0)
					$dividendtxt = $this->formatGetal($dividend['totaal'],$this->pdf->rapport_VHO_decimaal);

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,trim($subdata['fondsOmschrijving'].' '.$subdata['rekening']));

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);

				if($this->pdf->rapport_VHO_percentageTotaal == 1)
					$percentageTotaalTekst = $this->formatGetal($percentageVanTotaal,1)."%";
				else
					$percentageTotaalTekst = "";

				if($subdata['type']=='rekening')
        {
          $percentageTotaalTekst='';
          $fondsResultaattxt='';
          $valutaResultaattxt='';
          $dividendtxt='';
          $procentResultaattxt='';
          $fondsResultaat=0;
          $valutaResultaat=0;
          if($categorien['beleggingscategorie'] <> 'Liquiditeiten')
            $subdata['historischeWaardeTotaalValuta']=$subdata['actuelePortefeuilleWaardeEuro'];
          else
            $categorien['subtotaalhistorisch']=0;
        }
				  $this->pdf->row(array("",
												"",
                        ($subdata['type']=='rekening'?'':$this->formatAantal($subdata['totaalAantal'],0,$this->pdf->rapport_VHO_aantalVierDecimaal)),
                        ($subdata['type']=='rekening'?'':$this->formatGetal($subdata['historischeWaarde'],2)),
                        ($subdata['type']=='rekening'?'':$this->formatGetal($subdata['historischeWaardeTotaal'],$this->pdf->rapport_VHO_decimaal)),
                        ($subdata['type']=='rekening' && $subdata['historischeWaardeTotaalValuta']==0?'':$this->formatGetal($subdata['historischeWaardeTotaalValuta'],$this->pdf->rapport_VHO_decimaal)),
												"",
                        ($subdata['type']=='rekening'?'':$this->formatGetal($subdata['actueleFonds'],2)),
												$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VHO_decimaal),
												$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),
												$percentageTotaalTekst,
												$fondsResultaattxt,
												$valutaResultaattxt,
                        $dividendtxt,
												$procentResultaattxt)
												);
	


				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];

				$subtotaal['fondsResultaat'] = $subtotaal['fondsResultaat'] + $fondsResultaat;
				$subtotaal['valutaResultaat'] = $subtotaal['valutaResultaat'] + $valutaResultaat;
				$subtotaal['gecombeneerdResultaat'] += $gecombeneerdResultaat;
                $subtotaal['totaalDividend'] += $dividend['totaal'];
                $subtotaal['totaalDividendCorrected'] += $dividend['corrected'];
			}
			$lastBeleggingscategorie=$categorien['beleggingscategorie'];

			if($this->pdf->rapport_VHO_percentageTotaal == 1)
			{
				$percentageVanTotaal = ($categorien['subtotaalactueel']) / ($totaalWaarde/100);
			}
			else {
				$percentageVanTotaal = "";
			}



  	$procentResultaat = (($categorien['subtotaalactueel'] - $categorien['subtotaalhistorisch'] + $subtotaal['totaalDividendCorrected'] ) / ($categorien['subtotaalhistorisch'] /100));

	if($categorien['subtotaalhistorisch'] < 0)
		$procentResultaat = -1 * $procentResultaat;

      $this->printSubTotaal('', $categorien['subtotaalhistorisch'], $categorien['subtotaalactueel'],$percentageVanTotaal, $subtotaal['fondsResultaat'], $subtotaal['valutaResultaat'], $procentResultaat,$subtotaal['totaalDividend']);

			// totaal op categorie tellen
			$totaalhistorisch += $categorien['subtotaalhistorisch'];
			$totaalactueel += $categorien['subtotaalactueel'];

			$totaalfondsresultaat += $subtotaal['fondsResultaat'];
			$totaalvalutaresultaat += $subtotaal['valutaResultaat'];

      $totaaldividend        += $subtotaal['totaalDividend'];
      $totaaldividendCorrected        += $subtotaal['totaalDividendCorrected'];

		  $totaalGecombeneerdResultaat += $subtotaal['gecombeneerdResultaat'];

      $grandtotaaldividend  += $subtotaal['totaalDividend'];
      $grandtotaaldividendCorrected  += $subtotaal['totaalDividendCorrected'];


			$lastCategorie = $categorien['Omschrijving'];
			$subtotaal = array();
		}

		if($this->pdf->rapport_VHO_percentageTotaal == 1)
		{
			$percentageVanTotaal = ($totaalactueel) / ($totaalWaarde/100);
		}
		else {
			$percentageVanTotaal = "";
		}

		// totaal voor de laatste categorie
		$procentResultaat = (($totaalactueel - $totaalhistorisch + $totaaldividendCorrected) / ($totaalhistorisch /100));
	
		if($totaalhistorisch < 0)
			$procentResultaat = -1 * $procentResultaat;

		if($lastCategorie<>'')
		  $actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalhistorisch, $totaalactueel,$percentageVanTotaal ,$totaalfondsresultaat,$totaalvalutaresultaat, $procentResultaat,false,$totaaldividend);


		$actueleWaardePortefeuille+=$this->addRente($lastBeleggingscategorie);


		// Liquiditeiten
/*
		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , TijdelijkeRapportage.beleggingscategorie,".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro , ".
			" TijdelijkeRapportage.rekening, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		if($DB1->records() >0)
		{
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"b");
			$totaalLiquiditeitenInValuta = 0;

			while($data = $DB1->NextRecord())
			{

				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data['rekening'],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data['fondsOmschrijving'],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data['valuta'],$omschrijving),$this->pdf->rapport_taal);

				$totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];

				if($this->pdf->rapport_VHO_percentageTotaal ==1)
				{
					$percentageVanTotaal  = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
					$percentageVanTotaalTekst = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VHO_decimaal_proc)."%";
				}
				else
					$percentageVanTotaalTekst = "";

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$omschrijving);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				 $this->pdf->row(array("",
												"",
												"",
												"",
												"",
												"",
												"",
												"",
												$this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VHO_decimaal),
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),
												$percentageVanTotaalTekst));
				$lastBeleggingscategorie=$data['beleggingscategorie'];
			}
		}


		if($this->pdf->rapport_VHO_percentageTotaal ==1)
		{
			$percentageVanTotaal = ($totaalLiquiditeitenEuro) / ($totaalWaarde/100);
		}
		else
			$percentageVanTotaal = 0;

		// totaal liquiditeiten
		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), "", $totaalLiquiditeitenEuro,$percentageVanTotaal,"","");
*/
		$actueleWaardePortefeuille+=$this->addRente($lastBeleggingscategorie);

		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			  alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}

		if($this->pdf->rapport_VHO_percentageTotaal ==1)
		{
			$percentageVanTotaal = 100;
		}
		else
			$percentageVanTotaal = 0;


		// print grandtotaal
		$this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille, $percentageVanTotaal,"","","",true,$grandtotaaldividend);


		$this->pdf->ln();
		$this->pdf->setDrawColor(0,0,0);

		// index vergelijking afdrukken

   // printIndex($this);
	}
  
    function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}
}
?>