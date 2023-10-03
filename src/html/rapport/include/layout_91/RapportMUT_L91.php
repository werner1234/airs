<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/15 16:39:40 $
File Versie					: $Revision: 1.2 $

$Log: RapportMUT_L91.php,v $
Revision 1.2  2020/07/15 16:39:40  rvv
*** empty log message ***

Revision 1.1  2020/07/01 16:22:28  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportMUT_L91
{
	function RapportMUT_L91($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT2";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Mutatieoverzicht";

		if ($this->pdf->rapportageValuta != 'EUR' || $this->pdf->rapportageValuta != '')
		 $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	// type = totaal / subtotaal / tekst
	function printCol($row, $data, $type = "tekst")
	{
		$y = $this->pdf->getY();
		// draw lines
		// calculate positions
		$start = $this->pdf->marge;
		for($tel=0;$tel <$row;$tel++)
		{
			$start += $this->pdf->widthB[$tel];
		}

		$writerow = $this->pdf->widthB[($tel)];
		$end = $start + $writerow;

		// print cell , 1
		$this->pdf->Cell($start-$this->pdf->marge,4,"",0,0,"R");
		$this->pdf->Cell($writerow,4,$data, 0,0, "R");

		if($type == "totaal" || $type == "subtotaal" || $type == "grandtotaal")
		{
			$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
			$this->pdf->ln();
			if($type == "totaal")
			{
				$this->pdf->setDash(1,1);
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->setDash();
			}
			else if($type == "grandtotaal")
			{
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->Line($start+2,$this->pdf->GetY()+1,$end,$this->pdf->GetY()+1);
			}
		}
		$this->pdf->setY($y);
	}

	//function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE)
	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE,  $totaalF, $grandtotal=false)
	{
		$hoogte = 16;
    $this->pdf->setDrawColor($this->pdf->rapportLineColor[0],$this->pdf->rapportLineColor[1],$this->pdf->rapportLineColor[2]);

		if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		if(!$grandtotal)
			$totType = "totaal";
		else
			$totType = "grandtotaal";


		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->printCol(1,$title,"tekst");
		if($totaalA <>0)
		  $this->printCol(2,$this->formatGetal($totaalA,$this->pdf->rapport_MUT2_decimaal),$totType);
		if($totaalB <>0)
		  $this->printCol(3,$this->formatGetal($totaalB,$this->pdf->rapport_MUT2_decimaal),$totType);

		if($totaalC <>0)
			$this->printCol(4,$this->formatGetal($totaalC,$this->pdf->rapport_MUT2_decimaal),$totType);
		if($totaalD <>0)
			$this->printCol(5,$this->formatGetal($totaalD,$this->pdf->rapport_MUT2_decimaal),$totType);
		if($totaalE <>0)
			$this->printCol(6,$this->formatGetal($totaalE,$this->pdf->rapport_MUT2_decimaal),$totType);
		if($totaalF <>0)
			$this->printCol(7,$this->formatGetal($totaalF,$this->pdf->rapport_MUT2_decimaal),$totType);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();
		return $totaalA;
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
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
	}

	function writeRapport()
	{
	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$DB = new DB();
		// voor data
		$this->pdf->widthA = array(25,100,25,25,25,25,25,25);
		$this->pdf->alignA = array('R','L','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(25,100,25,25,25,25,25,25);
		$this->pdf->alignB = array('R','L','R','R','R','R','R','R');


		// loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT ".
			"Rekeningmutaties.Boekdatum, ".
			"Rekeningmutaties.Omschrijving ,".
			"ABS(Rekeningmutaties.Aantal) AS Aantal, ".
			"Rekeningmutaties.Debet $koersQuery as Debet, ".
			"Rekeningmutaties.Credit $koersQuery as Credit, ".
			"Rekeningmutaties.Valutakoers, ".
			"Rekeningmutaties.Rekening, ".
			"Rekeningmutaties.Grootboekrekening, ".
			"Rekeningmutaties.Afschriftnummer, ".
			"Grootboekrekeningen.Omschrijving AS gbOmschrijving, ".
			"Grootboekrekeningen.Opbrengst, ".
			"Grootboekrekeningen.Kosten, ".
			"Grootboekrekeningen.Afdrukvolgorde ".
			"FROM Rekeningmutaties, Rekeningen,  Grootboekrekeningen ".
			"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
			"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
			"AND Rekeningmutaties.Verwerkt = '1' ".
			"AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
			"AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
			"AND Grootboekrekeningen.Afdrukvolgorde IS NOT NULL ".
			"AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
			"AND (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1') ".
			"ORDER BY Grootboekrekeningen.Afdrukvolgorde, Rekeningmutaties.Boekdatum, Rekeningmutaties.id";
			//"AND Grootboekrekeningen.Grootboekrekening <> 'KNBA' ".
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$buffer=array();
    $header=array('Boekdatum'=>1,'Omschrijving'=>1,'Uitgaven'=>1,'Bruto'=>1,'Provisie'=>0,'Kosten'=>0,'Belasting'=>0,'Netto'=>1);
		while($mutaties = $DB->nextRecord())
    {
      $skip = false;
      // skip bankkosten en Belasting records die al verrekend zijn in DIV overzicht.
      if ($mutaties['Grootboekrekening'] == "KNBA" || $mutaties['Grootboekrekening'] == "DIVBE")
      {
        $query = "SELECT " .
          "SUM(Rekeningmutaties.Debet) $koersQuery AS Debet " .
          "FROM Rekeningmutaties, Rekeningen " .
          "WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening " .
          "AND Rekeningen.Portefeuille = '" . $this->portefeuille . "' " .
          "AND Rekeningen.Rekening = '" . $mutaties['Rekening'] . "' " .
          "AND Rekeningmutaties.Afschriftnummer = '" . $mutaties['Afschriftnummer'] . "' " .
          "AND Rekeningmutaties.Boekdatum = '" . $mutaties['Boekdatum'] . "' " .
          "AND Rekeningmutaties.Omschrijving = '" . mysql_escape_string($mutaties['Omschrijving']) . "' " .
          "AND Rekeningmutaties.Verwerkt = '1' " .
          "AND Rekeningmutaties.Grootboekrekening = 'DIV' " .
          "GROUP BY Rekeningmutaties.Grootboekrekening ";
        
        $DBx = new DB();
        $DBx->SQL($query);
        $DBx->Query();
        if ($DBx->records() > 0)
        {
          $skip = true;
        }
        else
        {
          $skip = false;
        }
      }
      
      
      if (!$skip)
      {
        $query = "SELECT ".
          "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) $koersQuery AS Debet ".
          "FROM Rekeningmutaties, Rekeningen ".
          "WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
          "AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
          "AND Rekeningen.Rekening = '".$mutaties['Rekening']."' ".
          "AND Rekeningmutaties.Afschriftnummer = '".$mutaties['Afschriftnummer']."' ".
          "AND Rekeningmutaties.Boekdatum = '".$mutaties['Boekdatum']."' ".
          "AND Rekeningmutaties.Omschrijving = '".mysql_escape_string($mutaties['Omschrijving'])."' ".
          "AND Rekeningmutaties.Verwerkt = '1' ".
          "AND Rekeningmutaties.Grootboekrekening = 'KNBA' ".
          "GROUP BY Rekeningmutaties.Grootboekrekening ";
  
        $DBx = new DB();
        $DBx->SQL($query);
        $DBx->Query();
        $knba = $DBx->nextRecord();
  
        $query = "SELECT ".
          "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) $koersQuery AS Debet ".
          "FROM Rekeningmutaties, Rekeningen ".
          "WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
          "AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
          "AND Rekeningen.Rekening = '".$mutaties['Rekening']."' ".
          "AND Rekeningmutaties.Afschriftnummer = '".$mutaties['Afschriftnummer']."' ".
          "AND Rekeningmutaties.Boekdatum = '".$mutaties['Boekdatum']."' ".
          "AND Rekeningmutaties.Omschrijving = '".mysql_escape_string($mutaties['Omschrijving'])."' ".
          "AND Rekeningmutaties.Verwerkt = '1' ".
          "AND Rekeningmutaties.Grootboekrekening = 'DIVBE' ".
          "GROUP BY Rekeningmutaties.Grootboekrekening ";
  
        $DBx = new DB();
        $DBx->SQL($query);
        $DBx->Query();
        $divbe = $DBx->nextRecord();
  
        $mutaties['knba']['Debet']	= abs($knba['Debet']) ;
        $mutaties['divbe']['Debet']	= abs($divbe['Debet']);
  
        $credit = 0;
        $debet = 0;
        if($mutaties['Kosten'] > 0)
        {
          if($mutaties['Credit'])
            $debet	= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * -1;
          else
            $debet	= abs($mutaties['Debet']) * $mutaties['Valutakoers'];
        }
        else if($mutaties['Opbrengs'] > 0)
        {
          if($mutaties['Debet'])
            $credit	= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * -1;
          else
            $credit	= abs($mutaties['Credit']) * $mutaties['Valutakoers'];
        }
        else
        {
          if($mutaties['Grootboekrekening'] == "ONTTR")
          {
            if($mutaties['Credit'])
              $debet	= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * -1;
            else
              $debet	= abs($mutaties['Debet']) * $mutaties['Valutakoers'];
          }
          else
          {
            if($mutaties['Debet'])
              $credit	= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * -1;
            else
              $credit	= abs($mutaties['Credit']) * $mutaties['Valutakoers'];
          }
        }
        $mutaties['debet']=$debet;
        $mutaties['credit']=$credit;
        

        if($mutaties['knba']['Debet']<>0 && $mutaties['Grootboekrekening'] != "KNBA")
        {
          $mutaties['Debet']+=$mutaties['knba']['Debet'];
          $mutaties['debet']+=$mutaties['knba']['Debet'];
          unset($mutaties['knba']);
          $header['Provisie'] = 1;
        }
        elseif($mutaties['Grootboekrekening'] == "KNBA")
          $header['Kosten']=1;
        if($mutaties['divbe']['Debet']<>0)
          $header['Belasting']=1;
  
        $buffer[] = $mutaties;
       // listarray($mutaties);
      }
    }
    
    
    $this->pdf->customHeader=array();
		foreach($header as $veld=>$aanUit)
		  if($aanUit==1)
        $this->pdf->customHeader[]=vertaalTekst($veld,$this->pdf->rapport_taal);
		  else
        $this->pdf->customHeader[]='';//vertaalTekst($veld,$this->pdf->rapport_taal);
   
    
    $this->pdf->AddPage();
    $subtotalen=array();
    $subdebet = 0;
    $subcredit = 0;
    $subknba = 0;
    $subkost = 0;
    $subbel = 0;
    $subnetto= 0;
    foreach($buffer as $mutaties)
    {
				// print totaal op hele categorie.
				if(!empty($lastCategorie) && $lastCategorie <> $mutaties['gbOmschrijving'])
				{


					$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal),
											$subdebet, $subcredit, $subknba, $subkost, $subbel, $subnetto);
          
          $subtotalen=array();
					$subdebet = 0;
					$subcredit = 0;
					$subknba = 0;
					$subkost = 0;
					$subbel = 0;
					$subnetto = 0;
				}

				if($lastCategorie <> $mutaties['gbOmschrijving'])
				{
					$this->printKop(vertaalTekst($mutaties['gbOmschrijving'],$this->pdf->rapport_taal), $this->pdf->rapport_kop3_fontstyle);
					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				}


				

				// als grootboek is Transactie kosten, zet alles onder Debet .

				if($mutaties['debet'] <> 0)
					$debettxt = $this->formatGetal($mutaties['debet'],2);
				else
					$debettxt = "";

				if($mutaties['credit'] <> 0)
					$credittxt = $this->formatGetal($mutaties['credit'],2);
				else
					$credittxt = "";

				$netto = $mutaties['credit'] - $mutaties['knba']['Debet'] - $mutaties['divbe']['Debet'];

				if($mutaties['Grootboekrekening'] == "DIV")
				{
						$nettotxt = 	$this->formatGetal($netto,2);
						if($mutaties['knba']['Debet']<>0)
						  $knbatxt = $this->formatGetal($mutaties['knba']['Debet'],2);
						else
              $knbatxt='';
						//if($mutaties['divbe']['Debet'] <> 0)
						  $beltxt = $this->formatGetal($mutaties['divbe']['Debet'],2);
						//else
            //  $beltxt='';
						$kosttxt = '';// $this->formatGetal(0,2);
						$omschrijving = str_replace("Dividend","",$mutaties['Omschrijving']);
				}
				else if($mutaties['Grootboekrekening'] == "KNBA")
				{
				 
					// bankkosten optellen bij kosten
					$mutaties['knba']['Debet']	= 0;
					$mutaties['divbe']['Debet'] = 0;
					$netto = 0;
					$kost = 0; //$mutaties['debet'];
					//$mutaties['debet'] = 0;

					$debettxt = $this->formatGetal($mutaties['debet'],2);//"";
					$knbatxt = "";
					$kosttxt = '';//$this->formatGetal($kost,2);
					$beltxt = "";
					$nettotxt = "";
					$omschrijving = $mutaties['Omschrijving'];
				}
				else if($mutaties['Grootboekrekening'] == "DIVBE")
				{
					// bankkosten optellen bij belasting
					$mutaties['knba']['Debet']	= 0;
					$mutaties['divbe']['Debet'] = $mutaties['credit'] * -1;
					$netto = 0;
					$kost = 0;
					$mutaties['credit'] = 0;

					$credittxt = "";
					$knbatxt = "";
					$kosttxt = "";
					$beltxt = $this->formatGetal($mutaties['divbe']['Debet'],2);
					$nettotxt = "";
					$omschrijving = $mutaties['Omschrijving'];
				}
				else
				{
					$mutaties['knba']['Debet']	= 0;
					$mutaties['divbe']['Debet'] = 0;
					$netto = 0;

					$knbatxt = "";
					$kosttxt = "";
					$beltxt = "";
					$nettotxt = "";
					$omschrijving = $mutaties['Omschrijving'];
				}
					// selecteer KNBA
					// selecteer DIVBE

				$subdebet += $mutaties['debet'];
				$subcredit += $mutaties['credit'];
				$subknba += $mutaties['knba']['Debet'];
				$subkost += $kost;
				$subbel += $mutaties['divbe']['Debet'];
				$subnetto += $netto;
				//echo $mutaties['divbe']['Debet']." ".$subbel."<br>";


				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

				$this->pdf->row(array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
												vertaalTekst($omschrijving,$this->pdf->rapport_taal),
												$debettxt,
												$credittxt,
												$knbatxt,
												$kosttxt,
												$beltxt,
												$nettotxt));

				$totaalcredit += $mutaties['credit'];
				$totaaldebet += $mutaties['debet'];
				$totaalknba += $mutaties['knba']['Debet'];
				$totaalkost += $kost;
				$totaalbel += $mutaties['divbe']['Debet'];
				$totaalnetto += $netto;
				$lastCategorie = $mutaties['gbOmschrijving'];
			
		}


		$actueleWaardePortefeuille +=
		$this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal),
		$subdebet,
		$subcredit,
		$subknba,
		$subkost,
		$subbel,
		$subnetto);

		$this->pdf->ln();

		$totaal = $actueleWaardePortefeuille;
		$actueleWaardePortefeuille +=
		$this->printTotaal(vertaalTekst("Totaal Generaal",$this->pdf->rapport_taal),
		$totaaldebet,
		$totaalcredit,
		$totaalknba,
		$totaalkost,
		$totaalbel,
		$totaalnetto,true);
		unset($this->pdf->customHeader);
	}
}
?>