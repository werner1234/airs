<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/29 09:44:54 $
File Versie					: $Revision: 1.6 $

$Log: RapportFRONT_L82.php,v $
Revision 1.6  2020/04/29 09:44:54  rvv
*** empty log message ***

Revision 1.5  2020/03/19 13:26:51  rvv
*** empty log message ***

Revision 1.4  2020/03/18 17:45:34  rvv
*** empty log message ***

Revision 1.3  2020/03/14 19:11:26  rvv
*** empty log message ***

Revision 1.13  2019/05/04 18:22:49  rvv
*** empty log message ***

Revision 1.12  2015/11/08 16:35:01  rvv
*** empty log message ***

Revision 1.11  2014/04/05 15:33:48  rvv
*** empty log message ***

Revision 1.10  2012/01/11 19:17:11  rvv
*** empty log message ***

Revision 1.9  2011/01/15 12:11:41  rvv
*** empty log message ***

Revision 1.8  2011/01/12 12:28:26  rvv
*** empty log message ***

Revision 1.7  2011/01/08 14:27:56  rvv
*** empty log message ***

Revision 1.5  2010/12/22 18:45:30  rvv
*** empty log message ***

Revision 1.4  2010/07/04 15:24:39  rvv
*** empty log message ***

Revision 1.3  2010/06/30 16:11:12  rvv
*** empty log message ***

Revision 1.2  2010/06/23 09:37:31  rvv
*** empty log message ***

Revision 1.1  2010/06/23 08:39:02  rvv
*** empty log message ***

Revision 1.4  2010/06/06 14:11:21  rvv
*** empty log message ***

Revision 1.3  2010/06/02 16:57:23  rvv
*** empty log message ***

Revision 1.2  2010/05/19 16:24:10  rvv
*** empty log message ***

Revision 1.1  2010/05/05 18:37:43  rvv
*** empty log message ***

Revision 1.1  2010/03/31 17:26:12  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONT_L82
{
	function RapportFRONT_L82($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_FRONT_titel)
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

		$this->rapportMaand 	= date("n",$this->rapportageDatumJul);
		$this->rapportDag 		= date("d",$this->rapportageDatumJul);
		$this->rapportJaar 		= date("Y",$this->rapportageDatumJul);

		$this->pdf->brief_font = $this->pdf->rapport_font;

	}


	function kopEnVoet()
	{
	  if(is_file($this->pdf->rapport_factuurHeader))
		{
			$this->pdf->Image($this->pdf->rapport_factuurHeader, 0, 10, 210, 34);
		}
		if(is_file($this->pdf->rapport_factuurFooter))
		{
			$this->pdf->Image($this->pdf->rapport_factuurFooter, 5, 255, 200, 37);
		}
	}
  
  
  
  function writeRapport()
  {
    global $__appvar;
    
    if($this->pdf->selectData['allInOne']==1)
    {
      $this->pdf->oddEvenCheck[$this->portefeuille]=count($this->pdf->pages);
    }
    else
    {
      if ((count($this->pdf->pages) % 2) && $this->pdf->selectData['type'] != 'eMail')
      {
        $this->pdf->frontPage=true;
        $this->pdf->AddPage($this->pdf->CurOrientation);
        $this->pdf->emailSkipPages[]=$this->pdf->page;
      }
    }
    

    
    $this->pdf->frontPage = true;
    $this->pdf->AddPage('L');
    
    
    if(is_file($this->pdf->rapport_logo))
    {
      $factor=0.07;//0.09;//0.522
      $xSize=837*$factor;//1500 837
      $ySize=400*$factor;//261 400
      $logopos=(297/2)-($xSize/2);
      $this->pdf->Image($this->pdf->rapport_logo, $logopos, 5, $xSize, $ySize);
    }
    
    $this->pdf->widthA = array(30,180);
    $this->pdf->alignA = array('L','L','L');
    
    $fontsize = $this->pdf->rapport_fontsize;//$this->fontsize;//10; //
    
    
    
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    
    $this->pdf->SetY(40);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->row(array(' ',vertaalTekst('PERSOONLIJK EN VERTROUWELIJK',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));
    if($this->pdf->portefeuilledata['Naam1'] <> '')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam1']));
    }
    $this->pdf->ln(1);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Adres']));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Woonplaats']));
    
    $this->pdf->SetY(75);
    
    $rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
      vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
      date("Y",$this->rapportageDatumVanafJul).
      ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
      date("d",$this->rapportageDatumJul)." ".
      vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
      date("Y",$this->rapportageDatumJul);
    
    
    $this->pdf->SetWidths(array(30,40,5,120));
    $this->pdf->row(array('',vertaalTekst('Verslagperiode',$this->pdf->rapport_taal),":",$rapportagePeriode));
    $this->pdf->ln();
    if($this->consolidatie==true)
    {
      foreach($this->pdf->portefeuilles as $i=>$portefeuille)
      {
        
        $query = "SELECT
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet
		          FROM
		            Portefeuilles
		          WHERE
		            Portefeuilles.Portefeuille = '".$portefeuille."'";
        
        $this->DB->SQL($query);
        $this->DB->Query();
        $portefeuilledata = $this->DB->nextRecord();
        
        
        
        if($i==0)
          $this->pdf->row(array(' ', vertaalTekst('Portefeuillenr.', $this->pdf->rapport_taal), ':', $portefeuille.' / '.$portefeuilledata['Depotbank']));
        else
          $this->pdf->row(array(' ','', '', $portefeuille.' / '.$portefeuilledata['Depotbank']));
      }
    }
    else
    {
      $this->pdf->row(array(' ', vertaalTekst('Portefeuillenr.', $this->pdf->rapport_taal), ':', $this->pdf->portefeuilledata['Portefeuille']));
    }

    $this->pdf->ln();
    $this->pdf->row(array(' ',vertaalTekst('Product',$this->pdf->rapport_taal),':',vertaalTekst($this->pdf->portefeuilledata['SoortOvereenkomst'],$this->pdf->rapport_taal)));
    $this->pdf->ln();
    $this->pdf->row(array(' ',vertaalTekst('Profiel',$this->pdf->rapport_taal),':',vertaalTekst($this->pdf->portefeuilledata['Risicoklasse'],$this->pdf->rapport_taal)));
    $this->pdf->ln();
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8);
    
    
    
    
    $this->pdf->SetY(133);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
    $this->pdf->ln(16);
    
    
    $query="SELECT Vermogensbeheerders.adres,
Vermogensbeheerders.woonplaats,
Vermogensbeheerders.telefoon,
Vermogensbeheerders.email,
Vermogensbeheerders.website FROM Vermogensbeheerders WHERE Vermogensbeheerders.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $this->DB->SQL($query);
    $verm = $this->DB->lookupRecord();
    foreach($verm as $key=>$value)
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('', $value));
    }
    
    
 /*
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->frontPage = true;
    $this->pdf->rapport_type = "INHOUD";
    $this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
    $this->pdf->addPage('L');
    $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
   */
  }
}
?>