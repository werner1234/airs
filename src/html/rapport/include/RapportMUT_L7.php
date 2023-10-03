<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/18 17:44:11 $
File Versie					: $Revision: 1.19 $

$Log: RapportMUT_L7.php,v $
Revision 1.19  2020/03/18 17:44:11  rvv
*** empty log message ***

Revision 1.18  2017/05/28 09:58:52  rvv
*** empty log message ***

Revision 1.17  2016/05/24 04:14:25  rvv
*** empty log message ***

Revision 1.16  2016/05/21 19:00:02  rvv
*** empty log message ***

Revision 1.15  2016/05/16 16:46:34  rvv
*** empty log message ***

Revision 1.14  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.13  2016/05/05 18:16:14  rvv
*** empty log message ***

Revision 1.12  2016/05/04 16:01:30  rvv
*** empty log message ***

Revision 1.11  2016/05/01 18:44:12  rvv
*** empty log message ***

Revision 1.10  2016/04/30 15:33:27  rvv
*** empty log message ***

Revision 1.9  2016/04/20 15:46:31  rvv
*** empty log message ***

Revision 1.8  2016/03/27 17:35:07  rvv
*** empty log message ***

Revision 1.7  2016/03/17 07:15:39  rvv
*** empty log message ***

Revision 1.6  2016/03/16 14:24:20  rvv
*** empty log message ***

Revision 1.5  2015/11/22 14:31:46  rvv
*** empty log message ***

Revision 1.4  2014/10/29 16:47:19  rvv
*** empty log message ***

Revision 1.3  2013/07/06 16:00:55  rvv
*** empty log message ***

Revision 1.2  2011/10/02 08:37:20  rvv
*** empty log message ***

Revision 1.1  2010/11/14 10:37:47  rvv
*** empty log message ***

Revision 1.7  2010/06/09 18:46:35  rvv
*** empty log message ***

Revision 1.6  2010/01/13 11:05:20  rvv
*** empty log message ***

Revision 1.5  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.4  2005/12/28 07:40:57  jwellner
no message

Revision 1.3  2005/11/17 07:25:02  jwellner
no message

Revision 1.2  2005/11/11 16:13:50  jwellner
bufix in MUT2 , PERF en Rekenclass

Revision 1.1  2005/09/30 14:05:13  jwellner
- rapport OIH
- rapport MUT2
- Layout 5
- selectieschermen

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

// alleen voor layout 2
class RapportMUT_L7
{
	function RapportMUT_L7($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT2";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Mutatie-overzicht";

		if ($this->pdf->rapportageValuta != 'EUR' || $this->pdf->rapportageValuta != '')
		 $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

    if(!is_array($this->pdf->excelData))
      $this->pdf->excelData=array();
  
  	$this->pdf->excelData[]=array('Boekdatum','Grootboekrekening','Omschrijving','Bruto','Kosten','Belastingen','Netto');
                            
    $this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->rapport_kop3_fontstyle='b';
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
    if($type == "tekst")
      $this->pdf->Cell($writerow,4,$data, 0,0, "L");
    else
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
	function printTotaal($title, $subtotaal)
	{
		$hoogte = 16;

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
		if(isset($subtotaal['bruto']))
		  $this->printCol(2,$this->formatGetal($subtotaal['bruto'],$this->pdf->rapport_MUT2_decimaal),$totType);
		if(isset($subtotaal['kosten']))
		  $this->printCol(3,$this->formatGetal($subtotaal['kosten'],$this->pdf->rapport_MUT2_decimaal),$totType);

		if(isset($subtotaal['belasting']))
			$this->printCol(4,$this->formatGetal($subtotaal['belasting'],$this->pdf->rapport_MUT2_decimaal),$totType);
		if(isset($subtotaal['netto']))
			$this->printCol(5,$this->formatGetal($subtotaal['netto'],$this->pdf->rapport_MUT2_decimaal),$totType);
	
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();
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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}
  
  function getknba($mutaties)
  {
   				$query = "SELECT count(*) as aantal, ".
          "SUM(Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers $koersQuery ) - (Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers $koersQuery ) as waarde ".
					"FROM Rekeningmutaties, Rekeningen ".
					"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
					"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
					"AND Rekeningen.Rekening = '".$mutaties['Rekening']."' ".
					"AND Rekeningmutaties.Afschriftnummer = '".$mutaties['Afschriftnummer']."' ".
					"AND Rekeningmutaties.Boekdatum = '".$mutaties['Boekdatum']."' ".
					"AND Rekeningmutaties.Omschrijving = '".mysql_escape_string($mutaties['Omschrijving'])."' ".
					"AND Rekeningmutaties.Verwerkt = '1' ".
					"AND Rekeningmutaties.Grootboekrekening = 'KNBA' AND Rekeningmutaties.bankTransactieId ='".$mutaties['bankTransactieId']."' ".
					"GROUP BY Rekeningmutaties.Grootboekrekening ";

				$DBx = new DB();
				$DBx->SQL($query);
				$DBx->Query();
				$knba = $DBx->nextRecord();
        return $knba;
  }

  function getdivbe($mutaties)
  {
 				$query = "SELECT count(*) as aantal,".
          "SUM((Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers $koersQuery ) - (Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers $koersQuery )) as waarde ".
					"FROM Rekeningmutaties, Rekeningen ".
					"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
					"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
					"AND Rekeningen.Rekening = '".$mutaties['Rekening']."' ".
					"AND Rekeningmutaties.Afschriftnummer = '".$mutaties['Afschriftnummer']."' ".
					"AND Rekeningmutaties.Boekdatum = '".$mutaties['Boekdatum']."' ".
					"AND Rekeningmutaties.Omschrijving = '".mysql_escape_string($mutaties['Omschrijving'])."' ".
					"AND Rekeningmutaties.Verwerkt = '1' ".
					"AND Rekeningmutaties.Grootboekrekening IN('DIVBE','ROER') AND Rekeningmutaties.bankTransactieId ='".$mutaties['bankTransactieId']."' ".
					"";//GROUP BY Rekeningmutaties.Grootboekrekening 

				$DBx = new DB();
				$DBx->SQL($query);
				$DBx->Query();
				$divbe = $DBx->nextRecord();
        if($divbe['aantal'] >1)
        {
          $query = "SELECT count(*) as aantal,".
          "SUM((Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers $koersQuery ) - (Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers $koersQuery )) as waarde ".
					"FROM Rekeningmutaties, Rekeningen ".
					"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
					"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
					"AND Rekeningen.Rekening = '".$mutaties['Rekening']."' ".
					"AND Rekeningmutaties.Afschriftnummer = '".$mutaties['Afschriftnummer']."' ".
					"AND Rekeningmutaties.Boekdatum = '".$mutaties['Boekdatum']."' ".
					"AND Rekeningmutaties.Omschrijving = '".mysql_escape_string($mutaties['Omschrijving'])."' ".
					"AND Rekeningmutaties.Verwerkt = '1' ".
					"AND Rekeningmutaties.Grootboekrekening IN('DIV','RENOB') AND Rekeningmutaties.bankTransactieId ='".$mutaties['bankTransactieId']."' ".
					"GROUP BY Rekeningmutaties.Grootboekrekening ";    
				  $DBx->SQL($query);
				  $DBx->Query();
				  $div = $DBx->nextRecord();
          $divbe['waarde']=$divbe['waarde']/$div['aantal'];
        }
        //if($mutaties['Fonds']<>'')
        //  $this->allDivBe['waarde']+=$divbe['waarde'];
        return $divbe;
  }
  
  function belastingSpec()
  {
    $db=new DB();
 		$query = "SELECT count(*) as aantal,
           SUM((Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers $koersQuery ) - (Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers $koersQuery )) as waarde,
           Grootboekrekeningen.Omschrijving ".
					"FROM Rekeningmutaties
           JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
           INNER JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening 
           WHERE Rekeningen.Portefeuille = '".$this->portefeuille."' ".
			    "AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
		    	"AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
					"AND Rekeningmutaties.Verwerkt = '1' ".
					"AND Rekeningmutaties.Grootboekrekening IN('ROER','BTLBR','DIVBE') ".
					"GROUP BY Rekeningmutaties.Grootboekrekening ";
 		$db->SQL($query);
		$db->Query();

   	$this->printKop(vertaalTekst('Specificatie ingehouden belastingen',$this->pdf->rapport_taal), $this->rapport_kop3_fontstyle);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totalen=array();
    //if($this->allDivBe['waarde'])
    //{
 		 // $this->pdf->row(array('',vertaalTekst('Dividendbelasting',$this->pdf->rapport_taal),$this->formatGetal($this->allDivBe['waarde'],2))); 
    //  $totalen['bruto']+=  $this->allDivBe['waarde'];
		//}
    while($data = $db->nextRecord())
    {
      $this->pdf->row(array('',vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal),$this->formatGetal($data['waarde'],2)));   
      $totalen['bruto']+= $data['waarde'];
    }
    $this->printTotaal('Totaal ingehouden belastingen',$totalen);
            
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
    $eigenGrootboekOmschrijving=array('DIV'=>'Dividenden',
                                      'RENOB'=>'Ontvangen rente obligaties',
                                      'RENME'=>'Meegekochte en verkochte rente op obligaties',
                                      'RENTE'=>'Rente op banktegoeden',
                                      'STORT'=>'Stortingen',
                                      'ONTTR'=>'Onttrekkingen',
                                      'KRUIS'=>'Interne overboekingen');

		// loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT if(Grootboekrekeningen.Opbrengst=1,1,
       if(Grootboekrekeningen.Kosten=1,2,
       if(Grootboekrekeningen.Storting=1,3,
       if(Grootboekrekeningen.Onttrekking=1,3,
       if(Grootboekrekeningen.Kruispost=1,3,
       100))))) as Volgorde,
       if(Rekeningmutaties.Grootboekrekening='RENTE',Grootboekrekeningen.Afdrukvolgorde+2,
       if(Rekeningmutaties.Grootboekrekening='KRUIS',Grootboekrekeningen.Afdrukvolgorde+2,
        Grootboekrekeningen.Afdrukvolgorde)) as afdrukvolgorde2,
       Rekeningmutaties.Boekdatum, ".
			"Rekeningmutaties.Omschrijving ,".
      "(Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers $koersQuery ) - (Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers $koersQuery ) as waarde, ".
			"Rekeningmutaties.Valutakoers, ".
			"Rekeningmutaties.Rekening, ".
      "Rekeningmutaties.Fonds, ".
			"Rekeningmutaties.Grootboekrekening, ".
			"Rekeningmutaties.Afschriftnummer, ".
			"Grootboekrekeningen.Omschrijving AS gbOmschrijving, ".
			"Grootboekrekeningen.Opbrengst, ".
			"Grootboekrekeningen.Kosten, ".
			"Grootboekrekeningen.Afdrukvolgorde, ".
      "Rekeningmutaties.bankTransactieId ".
			"FROM Rekeningmutaties, Rekeningen,  Grootboekrekeningen ".
			"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
			"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
			"AND Rekeningmutaties.Verwerkt = '1' ".
			"AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
			"AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
			"AND Grootboekrekeningen.Afdrukvolgorde IS NOT NULL $extraquery ".
			"AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
			"AND (Grootboekrekeningen.Kosten = '1' OR 
            Grootboekrekeningen.Opbrengst = '1' OR 
            Grootboekrekeningen.Storting = '1' OR 
            Grootboekrekeningen.Onttrekking = '1' OR 
            Grootboekrekeningen.Kruispost = '1') ".
			"ORDER BY Volgorde,afdrukvolgorde2, Rekeningmutaties.Grootboekrekening, Rekeningmutaties.Boekdatum, Rekeningmutaties.id";
			//"AND Grootboekrekeningen.Grootboekrekening <> 'KNBA' ".

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    $mutatiesAantalPerGrootboek=array();
    $volgordeOmschrijving=array(1=>'Opbrengsten',2=>'Kosten',3=>'Stortingen en onttrekkingen');
    //$volgordeOmschrijving=array(1=>'Opbrengsten',2=>'Kosten',3=>'Stortingen en onttrekkingen');
		while($mutaties = $DB->nextRecord())
		{
		  $skip = false;
      $grootboekUpper=strtoupper($mutaties['Grootboekrekening']);
      if(isset($eigenGrootboekOmschrijving[$grootboekUpper]))
        $mutaties['gbOmschrijving']=$eigenGrootboekOmschrijving[$grootboekUpper];
			
      // skip bankkosten en Belasting records die al verrekend zijn in DIV overzicht.
			if($mutaties['Grootboekrekening'] == "KNBA" ||$mutaties['Grootboekrekening'] == "DIVBE" ||$mutaties['Grootboekrekening'] == "ROER")
			{
					$query = "SELECT ".
          "SUM((Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers $koersQuery ) - (Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers $koersQuery )) as waarde ".
					"FROM Rekeningmutaties, Rekeningen ".
					"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
					"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
					"AND Rekeningen.Rekening = '".$mutaties['Rekening']."' ".
					"AND Rekeningmutaties.Afschriftnummer = '".$mutaties['Afschriftnummer']."' ".
					"AND Rekeningmutaties.Boekdatum = '".$mutaties['Boekdatum']."' ".
					"AND Rekeningmutaties.Omschrijving = '".mysql_escape_string($mutaties['Omschrijving'])."' ".
					"AND Rekeningmutaties.Verwerkt = '1' ".
					"AND Rekeningmutaties.Grootboekrekening IN('DIV','RENOB') AND Rekeningmutaties.bankTransactieId ='".$mutaties['bankTransactieId']."' ".
					"GROUP BY Rekeningmutaties.Grootboekrekening ";

				$DBx = new DB();
				$DBx->SQL($query);
				$DBx->Query();
				if($DBx->records() > 0)
					$skip = true;
				else
					$skip = false;
			}
      
      if($skip==false)
      {
		    $mutatieArray[]=$mutaties;
        if(!isset($mutatiesAantalPerGrootboek[$mutaties['Grootboekrekening']]))
          $mutatiesAantalPerGrootboek[$mutaties['Grootboekrekening']]=1;
        else  
          $mutatiesAantalPerGrootboek[$mutaties['Grootboekrekening']]++;
      }
    }
    

    $n=1;
    foreach($mutatieArray as $mutaties)
    {  
      

      
				// print totaal op hele categorie.
				if($lastCategorie <> $mutaties['gbOmschrijving'] && !empty($lastCategorie) )
				{
					$this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal),$subtotaal);
          $subtotaal=array();
				  
				}

      if($lastHcatVolgorde<>$mutaties['Volgorde'])
      {
        if($lastHcatVolgorde==1)
				   $this->belastingSpec();
           
        $this->pdf->rapport_titel = "Mutatie-overzicht: ".$volgordeOmschrijving[$mutaties['Volgorde']];
	      $this->pdf->AddPage();
        if(!isset($lastHcatVolgorde))
          $this->pdf->templateVars['MUTPaginas']=$this->pdf->page; 
        $lastHcatVolgorde =$mutaties['Volgorde'];
      }  
      
				if($lastCategorie <> $mutaties['gbOmschrijving'])
				{
				  if($this->pdf->GetY()>166)
            $this->pdf->AddPage();
					$this->printKop(vertaalTekst($mutaties['gbOmschrijving'],$this->pdf->rapport_taal), $this->rapport_kop3_fontstyle);
					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
          $n=1;
				}

      $n++;
      if($n>=$mutatiesAantalPerGrootboek[$mutaties['Grootboekrekening']] && $this->pdf->GetY()>170)
      { 
         $this->pdf->AddPage();
      } 

        $knba = $this->getknba($mutaties);
        $divbe = $this->getdivbe($mutaties);
				$netto = $mutaties['waarde'] + $knba['waarde'] + $divbe['waarde'];
        $mutaties['Omschrijving']=html_entity_decode($mutaties['Omschrijving']);
				$omschrijving = $mutaties['Omschrijving'];
        if($mutaties['Grootboekrekening'] == "DIV")
						$omschrijving = str_replace("Dividend","",$mutaties['Omschrijving']);
				elseif($mutaties['Grootboekrekening'] == "RENOB")
						$omschrijving = str_replace("Rente","",$mutaties['Omschrijving']);
				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

      if($this->pdf->rapport_taal <> 0)
      {
        $omschrijving=vertaalTekst($mutaties['Omschrijving'],$this->pdf->rapport_taal);
        if($omschrijving==$mutaties['Omschrijving'])
        {
          $omschrijvingDelen=explode(" ",$omschrijving);
          $omschrijvingDelen[0]=vertaalTekst($omschrijvingDelen[0],$this->pdf->rapport_taal);
          $omschrijving=implode(" ",$omschrijvingDelen);
          
        }
      }
  
	    	if($mutaties['Grootboekrekening'] == "DIV" || $mutaties['Grootboekrekening'] == "RENOB")
        {
  			  $this->pdf->row(array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
												vertaalTekst($omschrijving,$this->pdf->rapport_taal),
												$this->formatGetal($mutaties['waarde'],2),
												$this->formatGetal($knba['waarde'],2),
												$this->formatGetal($divbe['waarde'],2), 
												$this->formatGetal($netto,2)));
          $this->pdf->excelData[]=array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
                        $mutaties['Grootboekrekening'], 
          	            vertaalTekst($omschrijving,$this->pdf->rapport_taal),
												round($mutaties['waarde'],2),
												round($knba['waarde'],2),
												round($divbe['waarde'],2), 
												round($netto,2));            
          $subtotaal['bruto']+=$mutaties['waarde'];
          $subtotaal['kosten']+=$knba['waarde'];
          $subtotaal['belasting']+=$divbe['waarde'];
          $subtotaal['netto']+=$netto;              
        }
        elseif($mutaties['Grootboekrekening'] == "DIVBE")
        {
           $divbe['waarde']= $mutaties['waarde'];
           $mutaties['waarde']=0;
  			  $this->pdf->row(array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
												vertaalTekst($omschrijving,$this->pdf->rapport_taal),
												$this->formatGetal($mutaties['waarde'],2),
												$this->formatGetal($knba['waarde'],2),
												$this->formatGetal($divbe['waarde'],2), 
												$this->formatGetal($divbe['waarde'],2)));
          $this->pdf->excelData[]=array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
                        $mutaties['Grootboekrekening'], 
												vertaalTekst($omschrijving,$this->pdf->rapport_taal),
												round($mutaties['waarde'],2),
												round($knba['waarde'],2),
												round($divbe['waarde'],2), 
												round($divbe['waarde'],2));                
          $subtotaal['bruto']+=$mutaties['waarde'];
          $subtotaal['kosten']+=$knba['waarde'];
          $subtotaal['belasting']+=$divbe['waarde'];
          $subtotaal['netto']+=$divbe['waarde'];  
        }
        else
        {

			   	$this->pdf->row(array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
												vertaalTekst($omschrijving,$this->pdf->rapport_taal),
												$this->formatGetal($mutaties['waarde'],2)));
          $this->pdf->excelData[]=array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
                        $mutaties['Grootboekrekening'], 
												vertaalTekst($omschrijving,$this->pdf->rapport_taal),
												round($mutaties['waarde'],2));            
          $subtotaal['bruto']+=$mutaties['waarde'];             
        }
      //  $this->pdf->excelData[]=array($mutaties['gbOmschrijving'],date("d-m-Y",db2jul($mutaties['Boekdatum'])),vertaalTekst($omschrijving,$this->pdf->rapport_taal),
      //  round($debet,2),round($credit,2),round($knba['Debet'],2),round($kost,2),round($divbe['Debet'],2),round($netto,2));

       $lastCategorie =$mutaties['gbOmschrijving'];
       
		}
    	$this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal),$subtotaal);
          $subtotaal=array();
				 
	}
}
?>