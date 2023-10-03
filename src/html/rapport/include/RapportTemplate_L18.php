<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2011/06/29 16:52:23 $
File Versie					: $Revision: 1.3 $

$Log: RapportTemplate_L18.php,v $
Revision 1.3  2011/06/29 16:52:23  rvv
*** empty log message ***

Revision 1.2  2008/07/01 07:12:34  rvv
*** empty log message ***

Revision 1.1  2008/05/16 08:13:46  rvv
*** empty log message ***

Revision 1.2  2008/03/18 12:39:08  rvv
*** empty log message ***

Revision 1.1  2008/03/18 09:56:48  rvv
*** empty log message ***

Revision 1.6  2008/01/23 07:37:03  rvv
*** empty log message ***

Revision 1.5  2007/11/16 11:22:27  rvv
*** empty log message ***

Revision 1.4  2007/10/04 11:57:04  rvv
*** empty log message ***

Revision 1.3  2007/09/26 15:30:33  rvv
*** empty log message ***

Revision 1.2  2007/07/05 12:28:39  rvv
*** empty log message ***

Revision 1.1  2007/06/29 11:38:56  rvv
L14 aanpassingen




*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L18
{
	function RapportTemplate_L18($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIS_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
		else
			$this->pdf->rapport_titel = "Titel pagina";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->extraPage =0;
		$this->DB = new DB();

		$this->letters=array(0=>'A',1=>'B',2=>'C',3=>'D');

	}

	function RowCell($data)
	{
	    //Calculate the height of the row
	    $nb=0;
	//    for($i=0;$i<count($data);$i++)
	 //       $nb=max($nb,$this->pdf->NbLines($this->pdf->widths[$i],$data[$i]));
	    $h=$this->pdf->rowHeight;//*$nb;
	    //Issue a page break first if needed
	    $this->pdf->CheckPageBreak($h);
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++)
	    {
	        $w=$this->pdf->widths[$i];
	        $a=isset($this->pdf->aligns[$i]) ? $this->pdf->aligns[$i] : 'L';
	        //Save the current position
	        $x=$this->pdf->GetX();
	        $y=$this->pdf->GetY();
	        //Draw the border
	        //$this->Rect($x,$y,$w,$h);
	        //Print the text
	        $lines = $this->pdf->NbLines($this->pdf->widths[$i],$data[$i]);
	        // fill lines
	//function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='',$currentx=0)
	        $this->pdf->Cell($w,$this->pdf->rowHeight,$data[$i],0,$line,$a,$this->pdf->fillCell[$i]);

	        if($this->pdf->CellBorders[$i])
	        {
	          if($this->pdf->CellBorders[$i] == 'U')
	            $this->pdf->Line($x,$y+$h,$x+$w,$y+$h);
	        }
	        //Put the position to the right of the cell
	        $this->pdf->SetXY($x+$w,$y);
	    }
	    //Go to the next line
	    $this->pdf->Ln($h);
	}


	function writeRapport()
	{
		global $__appvar;
		$this->pdf->AddPage();
		$this->pdf->IndexPage = $this->pdf->page;

		$this->pdf->SetXY(15+$this->pdf->marge,50);
		$this->pdf->switchFont('fonds');
	  $this->pdf->SetFont($this->pdf->rapport_font,'b',16);
	  $this->pdf->MultiCell(150,6,vertaalTekst('Inhoud',$this->pdf->rapport_taal),0,'L');


	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

	  $this->pdf->widthA = array(15,180,75);
		$this->pdf->alignA = array('L','L','R');
		$this->pdf->CellBorders = array('','U','U');
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

	$kopHoogte = 10;
	$this->pdf->switchFont('fonds');

  $this->pdf->switchFont('rodelijn');
  $this->pdf->rowHeight=$kopHoogte;

  $telKop = 0;

  if($this->pdf->templateVars['OIBPaginas'] <> '')
  {
	  $this->RowCell(array('',$this->letters[$telKop]."  Overzicht",""));
	  $this->pdf->switchFont('fonds');
  	$this->pdf->rowHeight=$kopHoogte;
	  $this->RowCell(array('',"Vermogensverdeling ",$this->pdf->templateVars['OIBPaginas'],''));
  	$telKop++;
  }

$breedte = 250 ;
  if($this->pdf->templateVars['PERFPaginas'] <> ''||$this->pdf->templateVars['ATTPaginas'] <> ''||$this->pdf->templateVars['CASHPaginas'] <> '')
  {
	$this->pdf->switchFont('rodelijn');
	$this->pdf->rowHeight=$kopHoogte;

	$this->pdf->SetDrawColor($this->pdf->rapport_style['fonds']['line']['color'][0],$this->pdf->rapport_style['fonds']['line']['color'][1],$this->pdf->rapport_style['fonds']['line']['color'][2]);
	$this->pdf->Line(15+$this->pdf->marge,$this->pdf->getY(),20+$this->pdf->marge+$breedte,$this->pdf->getY());//grijs
	$this->RowCell(array('',$this->letters[$telKop]."  Waardering",""));
	$this->pdf->SetDrawColor($this->pdf->rapport_style['rodelijn']['line']['color'][0],$this->pdf->rapport_style['rodelijn']['line']['color'][1],$this->pdf->rapport_style['rodelijn']['line']['color'][2]);
		$this->pdf->Line(15+$this->pdf->marge,$this->pdf->getY(),20+$this->pdf->marge+$breedte,$this->pdf->getY());//rood
	$this->pdf->ln(1);

	$this->pdf->CellBorders = array();
	$this->pdf->switchFont('fonds');
	$this->pdf->rowHeight=6;
	if($this->pdf->templateVars['PERFPaginas'] <> '')
	  $this->RowCell(array('',"Rendementsverdeling",	$this->pdf->templateVars['PERFPaginas']));
	if($this->pdf->templateVars['ATTPaginas'] <> '')
	  $this->RowCell(array('',"Rendementsoverzicht ",$this->pdf->templateVars['ATTPaginas']));
	if($this->pdf->templateVars['CASHPaginas'] <> '')
	  $this->RowCell(array('',"Cashflow Overzicht",$this->pdf->templateVars['CASHPaginas']));
	$this->pdf->ln(1);
	$telKop++;
  }

    if($this->pdf->templateVars['OIVPaginas'] <> ''||$this->pdf->templateVars['VHOPaginas'] <> ''||
       $this->pdf->templateVars['VHO2Paginas'] <> '' ||$this->pdf->templateVars['TRANSPaginas'] <> '')
    {
   // $this->pdf->switchFont('rodelijn');
  	$this->pdf->rowHeight=$kopHoogte;
  	$this->pdf->Line(15+$this->pdf->marge,$this->pdf->getY(),20+$this->pdf->marge+$breedte,$this->pdf->getY());//grijs
	  $this->RowCell(array('',$this->letters[$telKop]."  Gedetailleerde posities",""));
    $this->pdf->SetDrawColor($this->pdf->rapport_style['rodelijn']['line']['color'][0],$this->pdf->rapport_style['rodelijn']['line']['color'][1],$this->pdf->rapport_style['rodelijn']['line']['color'][2]);
		  $this->pdf->Line(15+$this->pdf->marge,$this->pdf->getY(),20+$this->pdf->marge+$breedte,$this->pdf->getY());//rood
		$this->pdf->CellBorders = array();
	  $this->pdf->switchFont('fonds');
		$this->pdf->rowHeight=6;
		$this->pdf->ln(1);

		if($this->pdf->templateVars['OIVPaginas'] <> '')
	    $this->RowCell(array('',"Liquiditeiten & Geldmarkt Beleggingen",$this->pdf->templateVars['OIVPaginas']));
	  if($this->pdf->templateVars['VHOPaginas'] <> '')
	    $this->RowCell(array('',"Obligaties & Vergelijkbare Beleggingen",$this->pdf->templateVars['VHOPaginas']));
	  if($this->pdf->templateVars['VHO2Paginas'] <> '')
	    $this->RowCell(array('',"Aandelen & Vergelijkbare Beleggingen",$this->pdf->templateVars['VHO2Paginas']));
	  if($this->pdf->templateVars['TRANSPaginas'] <> '')
	    $this->RowCell(array('',"Transacties",$this->pdf->templateVars['TRANSPaginas']));
	  if($this->pdf->templateVars['GRAFIEKPaginas'] <> '')
	    $this->RowCell(array('',"Risico Verdeling",$this->pdf->templateVars['GRAFIEKPaginas']));
    }
		$this->pdf->Line(15+$this->pdf->marge,$this->pdf->getY()+2,20+$this->pdf->marge+$breedte,$this->pdf->getY()+2);//grijs

		$this->pdf->SetAutoPageBreak(false);
    $this->pdf->geenBasisFooter = true;
    $titel="Inhoud";
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetY(-12);
    $y = $this->pdf->getY();
    $this->pdf->MultiCell(260,4,vertaalTekst($titel,$this->pdf->rapport_taal),0,'R');

    $cw=&$this->pdf->CurrentFont['cw'];
    $titleWidth=0;

    for($i=0;$i<strlen($titel);$i++)
      $titleWidth+=$cw[$titel[$i]];
    $titleWidth=($titleWidth*$this->pdf->FontSize/1000);

   $this->pdf->Rect(8,199,252-$titleWidth,2,'F','F',$this->pdf->rapport_voet_bgcolor);
   $this->pdf->Rect(272,$y-1,6,6,'F','F',$this->pdf->rapport_voet_bgcolor);
   $this->pdf->SetXY(270,$y);
   $this->pdf->SetTextColor(255,255,255);
   $this->pdf->MultiCell(10,4,vertaalTekst(2,$this->pdf->rapport_taal),0,'C');
   $this->pdf->last_rapport_type = $this->pdf->rapport_type;
   $this->pdf->last_rapport_titel = $this->pdf->rapport_titel;
	}




}
?>