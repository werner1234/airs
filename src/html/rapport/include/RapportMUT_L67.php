<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/06 16:11:20 $
File Versie					: $Revision: 1.5 $

$Log: RapportMUT_L67.php,v $
Revision 1.5  2019/11/06 16:11:20  rvv
*** empty log message ***

Revision 1.4  2017/05/28 09:58:52  rvv
*** empty log message ***

Revision 1.3  2016/04/10 15:48:34  rvv
*** empty log message ***

Revision 1.2  2016/04/03 10:58:02  rvv
*** empty log message ***

Revision 1.1  2016/03/06 18:17:00  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

class RapportMUT_L67
{
	function RapportMUT_L67($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Mutatie overzicht";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->rapportCounter = count($this->pdf->page);

		$this->DB = new DB();

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
	 	
    $this->paginaEen();
    $this->paginaTwee();
	}
  
  function paginaEen()
  {
    
    $this->pdf->rapport_titel = "Mutatie overzicht";
    $this->pdf->SetWidths(array(20,70,20,20,20,20,40));
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    $this->pdf->AddPage();
    $this->pdf->templateVars['MUTPaginas']=$this->pdf->page;//$this->pdf->page;
    $db=new DB();


     
      $query="SELECT (credit*valutakoers)-(debet*valutakoers) as bedrag ,
      Rekeningmutaties.Boekdatum ,
      Rekeningmutaties.Aantal ,
      Rekeningmutaties.Valuta ,
      Rekeningmutaties.Rekening ,
      Rekeningmutaties.Grootboekrekening,
      Rekeningmutaties.valutakoers,
      Grootboekrekeningen.Omschrijving as GB_omschrijving,
      Rekeningmutaties.Omschrijving
      FROM Rekeningmutaties INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
      INNER JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
      WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND Rekeningmutaties.Grootboekrekening IN('DIV','RENOB','RENTE') AND
      Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' 
      ORDER BY Grootboekrekeningen.Afdrukvolgorde,Rekeningmutaties.Boekdatum,Rekeningmutaties.Omschrijving";
      $db->SQL($query);
      $db->Query();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      while($data=$db->nextRecord())
      {
        if($data['Grootboekrekening']<>$lastGB)
        {
          $this->pdf->widths[0]=50;
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          $this->pdf->Row(array($data['GB_omschrijving']));
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
          $this->pdf->widths[0]=20;
        }
        $divB=$this->getDivB($data['Rekening'],$data['Boekdatum'],$data['Omschrijving']);
        $this->pdf->Row(array(date('d-m-Y',db2jul($data['Boekdatum'])),
                              $data['Omschrijving'],
                              $data['Valuta'],
                              $this->formatGetal($data['bedrag'],2),
                              $this->formatGetal($data['valutakoers'],2),
                              $this->formatGetal($divB,2)));
        $grootboeken[$data['Grootboekrekening']]=$data['Omschrijving'];
        $grootboekWaarden[$data['Grootboekrekening']]=$data['bedrag'];
        $lastGB=$data['Grootboekrekening'];
      }
  }
  
  function getDivB($rekening,$datum,$omschrijving)
  {
    $db=new DB();
    $query="SELECT (credit*valutakoers)-(debet*valutakoers) as bedrag FROM Rekeningmutaties WHERE Grootboekrekening='DIVBE' AND rekening='$rekening' AND Boekdatum='$datum' AND Omschrijving='$omschrijving'";
    $db->SQL($query);
    $db->Query();
    $tmp=$db->lookupRecord();
    return $tmp['bedrag'];
  }
   
  function paginaTwee()
  {
    global $__appvar;
    $this->pdf->rapport_titel = "Kosten en belastingen";
    $this->pdf->AddPage();
    $this->pdf->templateVars['MUT2Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['MUT2Paginas']=$this->pdf->rapport_titel;
    
    $index=new indexHerberekening();
    $db=new DB();
    $kwartalen=$index->getKwartalen($this->rapportageDatumVanafJul,$this->rapportageDatumJul);
    $kwartalen[]=array('start'=>$this->rapportageDatumVanaf,'stop'=>$this->rapportageDatum);
    $periodeData=array();
    $grootboeken=array();
    foreach($kwartalen as $periode)
    {
      $query="SELECT sum((credit*valutakoers)-(debet*valutakoers)) as bedrag ,Rekeningmutaties.Grootboekrekening,Grootboekrekeningen.Omschrijving
      FROM Rekeningmutaties INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
      INNER JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
      WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND Grootboekrekeningen.Kosten=1 AND
      Rekeningmutaties.Boekdatum > '".$periode['start']."' AND Rekeningmutaties.Boekdatum <= '".$periode['stop']."' 
      GROUP by Rekeningmutaties.Grootboekrekening ORDER BY Grootboekrekeningen.Afdrukvolgorde, Rekeningmutaties.id";
      $db->SQL($query);
      $db->Query();
      while($data=$db->nextRecord())
      {
        $grootboeken[$data['Grootboekrekening']]=$data['Omschrijving'];
        $grootboekWaarden[$data['Grootboekrekening']]=$data['bedrag'];
      }
      $periodeData[]=array('periode'=>$periode,'grootboekWaarden'=>$grootboekWaarden);
      
    }
    $kop=array(vertaalTekst('Kostenpost',$this->pdf->rapport_taal));
    $totalen=array(vertaalTekst('Totaal kosten',$this->pdf->rapport_taal));
    $rows=array();
    $rowNr=0;
    foreach($grootboeken as $gootboek=>$omschrijving)
    {
      $rows[$rowNr][0]=$omschrijving;
      $rowNr++;
    }
    
    foreach($periodeData as $periodeIndex=>$kolData)
    {
      $startJul=db2jul($kolData['periode']['start']);
      $stopJul =db2jul($kolData['periode']['stop']);
      $kop[]=date("d",$startJul)." ".vertaalTekst($__appvar["Maanden"][date("n",$startJul)],$this->pdf->rapport_taal)." ".                                         date("Y",$startJul).
		         ' '.vertaalTekst('t/m',$this->pdf->rapport_taal)."\n".
		         date("d",$stopJul)." ".vertaalTekst($__appvar["Maanden"][date("n",$stopJul)],$this->pdf->rapport_taal)." ".date("Y",$stopJul);

      $rowNr=0;
      $tmp=0;
      foreach($grootboeken as $gootboek=>$omschrijving)
      {
        $rows[$rowNr][$periodeIndex+1]=$this->formatGetal($kolData['grootboekWaarden'][$gootboek],2);;
        $rowNr++;
        $tmp+=$kolData['grootboekWaarden'][$gootboek];
      }
      $totalen[]=$this->formatGetal($tmp,2);
    }
    $this->pdf->Ln();
    $this->pdf->SetWidths(array(40,40,40,40,40,40));
    $this->pdf->SetAligns(array('L','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('U','U','U','U','U','U');
    $this->pdf->Row($kop);
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($rows as $row)
      $this->pdf->Row($row);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);  
    
    $this->pdf->ln(2);
    $this->pdf->SetFillColor($this->pdf->rapport_totaal_fillcolor[0],$this->pdf->rapport_totaal_fillcolor[1],$this->pdf->rapport_totaal_fillcolor[2]);
    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY()-1,count($totalen)*40,6,'F'); 
    $this->pdf->SetTextColor($this->pdf->rapport_totaal_textcolor[0],$this->pdf->rapport_totaal_textcolor[1],$this->pdf->rapport_totaal_textcolor[2]);
    $this->pdf->Row($totalen);
    $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
 
  }
}
?>
