<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/02 15:20:30 $
File Versie					: $Revision: 1.12 $

$Log: RapportFRONT_L51.php,v $
Revision 1.12  2019/11/02 15:20:30  rvv
*** empty log message ***

Revision 1.11  2019/03/23 17:05:54  rvv
*** empty log message ***

Revision 1.10  2018/04/12 06:07:38  rvv
*** empty log message ***

Revision 1.9  2018/04/11 15:20:41  rvv
*** empty log message ***

Revision 1.8  2017/04/03 10:56:21  rvv
*** empty log message ***

Revision 1.7  2017/03/25 16:01:09  rvv
*** empty log message ***

Revision 1.6  2015/12/20 16:46:36  rvv
*** empty log message ***

Revision 1.5  2015/07/11 14:20:20  rvv
*** empty log message ***

Revision 1.4  2015/07/01 15:34:25  rvv
*** empty log message ***

Revision 1.3  2014/04/26 16:43:08  rvv
*** empty log message ***

Revision 1.2  2014/04/16 15:51:22  rvv
*** empty log message ***

Revision 1.1  2013/11/13 15:47:34  rvv
*** empty log message ***

Revision 1.7  2013/07/10 16:01:24  rvv
*** empty log message ***

Revision 1.6  2013/06/09 18:01:53  rvv
*** empty log message ***

Revision 1.5  2012/10/24 15:45:39  rvv
*** empty log message ***

Revision 1.4  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.3  2012/10/07 14:57:17  rvv
*** empty log message ***

Revision 1.2  2012/09/23 08:51:44  rvv
*** empty log message ***

Revision 1.1  2012/06/17 13:04:11  rvv
*** empty log message ***

Revision 1.2  2012/06/09 13:43:40  rvv
*** empty log message ***

Revision 1.1  2012/05/27 08:33:11  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L51
{
	function RapportFront_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$this->pdf->rapportCounter = count($this->pdf->page);

		$this->DB = new DB();

	}

	
	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT
	            	if(isnull(CRM_naw.naam),Clienten.Naam,CRM_naw.naam) as Naam,
                if(isnull(CRM_naw.naam),Clienten.Naam1,CRM_naw.naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Selectieveld1,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
                LEFT JOIN Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		          WHERE
		            Portefeuilles.Portefeuille = '".$this->portefeuille."' AND
		            Portefeuilles.Client = Clienten.Client AND
                Accountmanagers.Accountmanager = Portefeuilles.Accountmanager AND
                Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder";
		$this->DB->SQL($query);
		$this->DB->Query();
		$portefeuilledata = $this->DB->nextRecord();

		//if($this->pdf->selectData['type'] != 'eMail')
		//  $this->voorBrief();
   //background

		///if ((count($this->pdf->pages) % 2))
		//{
		//  $this->pdf->frontPage=true;
  	//	$this->pdf->AddPage($this->pdf->CurOrientation);
		//}
		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');


		if(is_file($this->pdf->rapport_logo))
		{
	    $factor=0.031;
		  $xSize=1417*$factor;
		  $ySize=591*$factor;
			$this->pdf->Image($this->pdf->rapport_logo, 230, 180, $xSize, $ySize);
		}
    

		$fontsize = 16; //$this->pdf->rapport_fontsize
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetAligns(array('C'));
    $this->pdf->SetWidths(array(297-2*$this->pdf->marge));
		$this->pdf->SetY(45);

		$rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumVanafJul).
		                     ' - '.
		                     date("d",$this->rapportageDatumJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumJul);
    //$this->pdf->row(array('Capital Support'));
    $this->pdf->row(array($portefeuilledata['Naam']));
    $this->pdf->ln(5);
    //$this->pdf->row(array('Vermogensregierapportage'));
    $this->pdf->row(array($portefeuilledata['Naam1']));
    $this->pdf->ln(5);
		$this->pdf->row(array($rapportagePeriode));
		$this->pdf->ln(6);

    $this->pdf->SetWidths(array(15,50,150));
    $this->pdf->SetAligns(array('L','L','L'));
     
    $this->pdf->SetY(150);
    $this->pdf->SetFont($this->pdf->rapport_font,'',11);
    $this->pdf->underline=true;
		$this->pdf->row(array('',vertaalTekst('Samenstelling vermogen',$this->pdf->rapport_taal)));
    $this->pdf->underline=false;
    $this->pdf->ln(1);
    if($this->pdf->portefeuilledata['Depotbank']=='TGB')
      $depotbank='InsingerGilissen';
    else
    	$depotbank=$this->pdf->portefeuilledata['DepotbankOmschrijving'];

		//if($this->pdf->portefeuilledata['ClientVermogensbeheerder']<>'')
		//	$depotbank=$this->pdf->portefeuilledata['ClientVermogensbeheerder'];

    $this->pdf->row(array('',$this->portefeuille,($portefeuilledata['Selectieveld1']<>''?$portefeuilledata['Selectieveld1']:$depotbank)));
    $this->pdf->ln();
    
    

	$this->pdf->SetY(170);
    $this->pdf->SetWidths(array(223,50));	
    $this->pdf->row(array('','Den Haag'));
    $this->pdf->ln(1);
		$this->pdf->row(array('',date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));

  
	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;

/*    
    $this->pdf->AutoPageBreak=false;
    $this->pdf->SetY(-10);
    $this->pdf->MultiCell(290,4,"Via onze website kunt u dagelijks uw portefeuille inzien.",0,'C');
    $this->pdf->AutoPageBreak=true;
*/
   // $this->pdf->AddPage();
	 // $this->pdf->frontPage = true;
   
   	$this->pdf->rapport_type = "OIB";
	  $this->pdf->rapport_titel = "";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

	}
}
?>
