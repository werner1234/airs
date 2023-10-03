<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/18 17:44:11 $
File Versie					: $Revision: 1.8 $

$Log: RapportMUT_L63.php,v $
Revision 1.8  2020/03/18 17:44:11  rvv
*** empty log message ***

Revision 1.7  2019/12/14 17:46:24  rvv
*** empty log message ***

Revision 1.6  2018/03/25 10:16:55  rvv
*** empty log message ***

Revision 1.5  2017/05/28 09:58:52  rvv
*** empty log message ***

Revision 1.4  2016/02/13 14:02:39  rvv
*** empty log message ***

Revision 1.3  2016/01/11 06:50:38  rvv
*** empty log message ***

Revision 1.2  2016/01/09 18:58:30  rvv
*** empty log message ***

Revision 1.1  2015/09/20 17:32:28  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportMutatieoverzichtLayout.php");

class RapportMUT_L63
{
	function RapportMUT_L63($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Mutaties";

		if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
		  $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
        $this->pdf->excelData[]=array("Grootboek","Periode","Bank Afschrift","Omschrijving","Boekdatum","Rekening","Debet","Credit");

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	


	function writeRapport()
	{

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	"  (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "1";
    
    $extraquery='';

		$DB = new DB();
		// voor data
		$this->pdf->widthA = array(30,25,90,25,30,20,31,31);
		$this->pdf->alignA = array('L','R','L','L','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = $this->pdf->widthA;
		$this->pdf->alignB = $this->pdf->alignA;

		if($this->pdf->rapport_MUT_kwartaal == 1 && ($this->pdf->selectData['maandrapportage'] == 1 || $this->pdf->selectData['kwartaalrapportage'] == 1) )
		{
			$maand = date("n",db2jul($this->rapportageDatum));
			$kwartaal = floor(($maand / 4)+1);
			switch($kwartaal)
			{
				case 1 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-01-01";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 2 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-03-31";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 3 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-06-31";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 4 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-09-30";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
			}
		}

		$this->pdf->AddPage();
		$this->pdf->templateVars['MUTPaginas']=$this->pdf->page;

		// loopje over Grootboekrekeningen Opbrengsten = 1
		if($this->pdf->selectData[GrootboekTm])
			$extraquery .= " AND (Rekeningmutaties.Grootboekrekening >= '".$this->pdf->selectData[GrootboekVan]."' AND Rekeningmutaties.Grootboekrekening  <= '".$this->pdf->selectData[GrootboekTm]."') ";


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
    
    $this->pdf->SetFillColor($this->pdf->rapport_kop2_bgcolor['r'],$this->pdf->rapport_kop2_bgcolor['g'],$this->pdf->rapport_kop2_bgcolor['b']);
    $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);


		$query = "SELECT ".
			"Rekeningmutaties.Boekdatum, ".
			"Rekeningmutaties.Omschrijving ,".
			"ABS(Rekeningmutaties.Aantal) AS Aantal, ".
			"Rekeningmutaties.Debet as Debet, ".
			"Rekeningmutaties.Credit as Credit, $koersQuery as boekdatumRapportageValutaKoers, ".
			"Rekeningmutaties.Valutakoers, ".
      "Rekeningmutaties.Valuta, ".
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
			"AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".$extraquery.
			"AND Grootboekrekeningen.Afdrukvolgorde IS NOT NULL ".
			"AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
			"AND (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1') ".
			"ORDER BY Grootboekrekeningen.Afdrukvolgorde, Rekeningmutaties.Boekdatum, Rekeningmutaties.id";

		$DB->SQL($query);
		$DB->Query();
    $aantal=$DB->records(); 
    $n=0;
		while($mutaties = $DB->nextRecord())
		{
      $n++;

			if($lastCategorie <> $mutaties['gbOmschrijving'])
			{
		    if($lastCategorie <> '')
        {
          $this->preRow();
          $this->pdf->row(array('','','','','','','',''));
        }	 

        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->preRow();
     		//$this->pdf->row(array('','','','','','','',''));
        $colWidths=array_sum($this->pdf->widthB);
				//echo $this->pdf->getY().' '.$mutaties['gbOmschrijving']."<br>\n";

      	$this->pdf->Cell($colWidths,$this->pdf->rowHeight,vertaalTekst($mutaties['gbOmschrijving'],$this->pdf->rapport_taal),0,1,'L',true);
        $this->pdf->Line($this->pdf->marge,$this->pdf->GetY()-$this->pdf->rowHeight,$this->pdf->marge,$this->pdf->GetY());
        $this->pdf->Line($this->pdf->marge+$colWidths,$this->pdf->GetY()-$this->pdf->rowHeight,$this->pdf->marge+$colWidths,$this->pdf->GetY());
        if($lastCategorie=='')
          $this->pdf->Line($this->pdf->marge,$this->pdf->GetY()-$this->pdf->rowHeight,$this->pdf->marge+$colWidths,$this->pdf->GetY()-$this->pdf->rowHeight);

        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			}

			if($mutaties['Valuta']==$this->pdf->rapportageValuta)
      {
        $debet = abs($mutaties['Debet']);
        $credit = abs($mutaties['Credit']) ;
      }
      else
      {
        //listarray($mutaties);
        $debet = abs($mutaties['Debet']) * $mutaties['Valutakoers'] / $mutaties['boekdatumRapportageValutaKoers'];
        $credit = abs($mutaties['Credit']) * $mutaties['Valutakoers'] / $mutaties['boekdatumRapportageValutaKoers'];
      }
			$subdebet += round($debet,2);
			$subcredit += round($credit,2);

			if($debet <> 0)
				$debettxt = $this->formatGetal($debet,2);
			else
				$debettxt = "";

			if($credit <> 0)
				$credittxt = $this->formatGetal($credit,2);
			else
				$credittxt = "";


			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

      if($n==$aantal)
        $extra='E';
      else
        $extra='';
			if($this->pdf->GetY()> 189)
			{
				$this->pdf->Line($this->pdf->marge,$this->pdf->GetY(),297-$this->pdf->marge+1,$this->pdf->GetY());
				$this->pdf->addPage();
			}
      $this->preRow($extra);
			$this->pdf->row(array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
                      $mutaties['Aantal'],
											vertaalTekst($mutaties['Omschrijving'],$this->pdf->rapport_taal),
                      $mutaties['Valuta'],
                      $this->formatGetal($mutaties['FondsKoers'],2),
                      $this->formatGetal($mutaties['Valutakoers'],2),
											$debettxt,
											$credittxt));
      $this->pdf->excelData[]=array($mutaties['gbOmschrijving'],date("n",db2jul($mutaties['Boekdatum'])),$mutaties['Afschriftnummer'],vertaalTekst($mutaties['Omschrijving'],$this->pdf->rapport_taal),	date("d-m-Y",db2jul($mutaties['Boekdatum'])),$mutaties['Rekening'],round($debet,2),round($credit,2));
			$totaalcredit += $credit;
			$totaaldebet += $debet;
			$lastCategorie = $mutaties[gbOmschrijving];
		}




	}
  
  function preRow($extra)
  {
    $this->pdf->CheckPageBreak($this->pdf->rowHeight);
    if($this->pdf->GetY()< 51)
    {
      $this->pdf->CellBorders = array(array('L','T'),'T','T','T','T','T','T',array('R','T'));
    }
    elseif($this->pdf->GetY()> 188 || $extra=='E')
    {
      $this->pdf->CellBorders = array(array('L','U'),'U','U','U','U','U','U',array('R','U'));
    }
    else
      $this->pdf->CellBorders = array('L','','','','','','','R');
  }
}
?>