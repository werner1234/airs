<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/01/11 12:48:50 $
File Versie					: $Revision: 1.12 $

$Log: RapportFRONT_L42.php,v $
Revision 1.12  2015/01/11 12:48:50  rvv
*** empty log message ***

Revision 1.11  2014/12/31 18:09:06  rvv
*** empty log message ***

*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L42
{
	function RapportFront_L42($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_FRONT_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->extraPage =0;
		$this->DB = new DB();

		$this->rapportMaand 	= date("n",$this->rapportageDatumJul);
		$this->rapportDag 		= date("d",$this->rapportageDatumJul);
		$this->rapportJaar 		= date("Y",$this->rapportageDatumJul);

		$this->pdf->brief_font = $this->pdf->rapport_font;
  }
  
  function addFront()
  {
    
    global $__appvar;
    $this->pdf->addPage();
    $this->pdf->frontPage = true;
    $this->pdf->SetFont($this->pdf->rapport_font,'B',12);
    //listarray($this->pdf->portefeuilledata);
    $this->pdf->SetFillColor($this->pdf->bruinLicht[0],$this->pdf->bruinLicht[1],$this->pdf->bruinLicht[2]);
    $this->pdf->Rect(0,0,$this->pdf->w,$this->pdf->h,'F');
    
    
    
    $centerX=225;
    $centerY=85;
    $schaling=3.2;
    $this->pdf->SetFillColor(255,255,255);
    $this->pdf->Rect($centerX,0,$this->pdf->w-$centerX,$centerY+3*$schaling,'F');
    $this->pdf->Sector($centerX, $centerY+3*$schaling, 30*$schaling, 90, 180,'F'); //sector
    
    $polly= array($centerX-30*$schaling,$centerY-4*$schaling,
      $centerX-6*$schaling,$centerY-28*$schaling,
      $centerX,$centerY-28*$schaling,
      $centerX,$centerY-14*$schaling,
      $centerX-14*$schaling,$centerY,
      $centerX,$centerY,
      $centerX,$centerY+14*$schaling,
      $centerX-30*$schaling,$centerY+14*$schaling,
      
      $centerX-30*$schaling,$centerY-4*$schaling);
    $this->pdf->Polygon($polly,'F');
    
    
    $this->pdf->SetFillColor($this->pdf->bruinLicht[0],$this->pdf->bruinLicht[1],$this->pdf->bruinLicht[2]);
    $polly= array($centerX+30*$schaling,$centerY-4*$schaling,
      $centerX+6*$schaling,$centerY-28*$schaling,
      $centerX,$centerY-28*$schaling,
      $centerX,$centerY-14*$schaling,
      $centerX+14*$schaling,$centerY,
      $centerX,$centerY,
      $centerX,$centerY+14*$schaling,
      $centerX+30*$schaling,$centerY+14*$schaling,
      $centerX+30*$schaling,$centerY-4*$schaling);
    
    $this->pdf->Polygon($polly,'F');
    
    $hoogte=6;
    $this->pdf->SetFillColor($this->pdf->blauwLicht[0],$this->pdf->blauwLicht[1],$this->pdf->blauwLicht[2]);
    $this->pdf->Rect($this->pdf->w/2,$this->pdf->h-$hoogte,$this->pdf->w,$hoogte,'F');
    
    
    $this->pdf->SetFillColor($this->pdf->blauwDonker[0],$this->pdf->blauwDonker[1],$this->pdf->blauwDonker[2]);
    $this->pdf->Rect(30,0,130,135,'F');
    
    $this->pdf->Rect(0,$this->pdf->h-$hoogte,$this->pdf->w/2,$hoogte,'F');
    
    
    
    $breedte=18;
    if(isset($this->pdf->beeldMerk))
      $this->pdf->memImage($this->pdf->beeldMerk,$this->pdf->w/2-$breedte/2,$this->pdf->h-33,$breedte);
    
    $this->pdf->SetWidths(array(33,140));
    $extraY=-5;
    $this->pdf->setY(75+$extraY);
    $this->pdf->SetFont($this->pdf->rapport_font,'',19);
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['VermogensbeheerderNaam']));
    $this->pdf->ln(8);
    $this->pdf->row(array('',"Rapportage per ".date("d",$this->rapportageDatumJul)." ".
      vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
      date("Y",$this->rapportageDatumJul)));
    
    $this->pdf->SetFillColor($this->pdf->bruinDonker[0],$this->pdf->bruinDonker[1],$this->pdf->bruinDonker[2]);
    $this->pdf->Rect(41,102+$extraY,42,1.5,'F');
    
    
    $this->pdf->SetAligns(array('L','L','L'));
    
    $this->pdf->setY(112+$extraY);
    $this->pdf->SetWidths(array(33,140,140));
    $this->pdf->SetFont($this->pdf->rapport_font,'',12);
    /*
    if($this->pdf->rapport_clientVermogensbeheerderReal<>'')
      $portefeuille=$this->pdf->rapport_clientVermogensbeheerderReal;
    else
      $portefeuille=$this->portefeuille;
    if($this->consolidatie==true)
    {
      $this->pdf->row(array('', implode(',',$this->pdf->portefeuilles)));//$this->portefeuille.
    }
    else
    {
      $this->pdf->row(array('', $portefeuille));//$this->portefeuille.
    }
    $this->pdf->ln(4);
    */
  
    if($this->consolidatie==true)
    {
      /*
      $query = "SELECT
 if(CRM_naw.naam <> '',CRM_naw.naam,Clienten.naam) as naam,
 if(CRM_naw.naam1 <> '',CRM_naw.naam1,Clienten.naam1) as naam1
FROM Portefeuilles
JOIN Clienten ON Portefeuilles.client=Clienten.client
LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille
 WHERE Portefeuilles.Portefeuille IN ('" . implode("','", $this->pdf->portefeuilles) . "')  ";
      $this->DB->SQL($query);
      $this->DB->Query();
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize + 2);
      //$this->pdf->SetWidths(array(19, 100 + 100));
      //$this->pdf->SetAligns(array('L', 'L', 'L'));
      //$this->pdf->SetY(18);
      */
  
      $this->pdf->Ln(-2);
      $this->pdf->row(array('', 'Geconsolideerd overzicht'));
      $this->pdf->Ln(4);
   /*
      while ($data = $this->DB->nextRecord())
      {
        $this->pdf->row(array('', $data['naam'] . ', ' . $data['naam1']));
        $this->pdf->Ln(2);
      }
   */
    }
    $this->pdf->row(array('', $this->pdf->portefeuilledata['Naam']));//$this->portefeuille.
    $this->pdf->ln(4);
    $this->pdf->row(array('', $this->pdf->portefeuilledata['Naam1']));
   
    // $this->pdf->memImage(base64_decode($img),231-30,94-28,60);
    
    $this->pdf->SetFillColor(255,255,255);
    
    $this->pdf->SetTextColor(0,0,0);

  }


	function writeRapport()
	{
	  global $__appvar;

	  $this->addFront();
	  
	  $this->pdf->frontPage=true;
    $this->pdf->last_rapport_type="FRONT";
	 
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	  $this->pdf->rapport_type = "OIB";
	  $this->pdf->rapport_titel = "";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
	  
	}
}
?>