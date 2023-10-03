<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.7 $

$Log: RapportFRONT_L64.php,v $
Revision 1.7  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.6  2018/07/23 17:29:56  rvv
*** empty log message ***

Revision 1.5  2018/07/22 07:08:42  rvv
*** empty log message ***

Revision 1.4  2018/07/21 15:54:40  rvv
*** empty log message ***

Revision 1.3  2016/01/27 17:08:53  rvv
*** empty log message ***

Revision 1.2  2016/01/21 06:43:39  rvv
*** empty log message ***

Revision 1.1  2015/11/29 13:13:22  rvv
*** empty log message ***

Revision 1.2  2015/09/13 11:32:29  rvv
*** empty log message ***

Revision 1.1  2015/09/05 16:48:04  rvv
*** empty log message ***




*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L64
{
	function RapportFront_L64($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$this->pdf->rapportCounter = count($this->pdf->page);

		$this->DB = new DB();

	}



	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT
		            Clienten.Naam,
                Clienten.Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            Portefeuilles, Clienten , Accountmanagers, Vermogensbeheerders
		          WHERE
		            Portefeuille = '".$this->portefeuille."' AND
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
			$factor=0.06;
			$xSize=1200*$factor;
			$ySize=377*$factor;
        $logopos=4;//(297/2)-($xSize/2);
	      $this->pdf->Image($this->pdf->rapport_logo, $logopos, 3, $xSize, $ySize);
		}

   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = 10; //$this->pdf->rapport_fontsize

    


    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);

    $this->pdf->SetY(40);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
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
		$this->pdf->row(array(' ',vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal),':',formatPortefeuille($portefeuilledata['Portefeuille'])));
    $this->pdf->ln();
    $this->pdf->row(array(' ',vertaalTekst('Mandaat',$this->pdf->rapport_taal),':',vertaalTekst($this->pdf->portefeuilledata['Risicoklasse'],$this->pdf->rapport_taal)));
    $this->pdf->ln();
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8); 



		$this->pdf->SetY(133);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		$this->pdf->row(array('',''));

		if($this->pdf->selectData['periode']=='Kwartaalrapportage' || $this->pdf->selectData['type'] == 'Kwartaalrapportage') // backoffice afdruk
		{
			$this->voorBrief();
		}

	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;


	}

	function voorBrief()
  {
		if($this->pdf->portefeuilledata['txtKoppeling'] !='')
		{
			$koppeling = stripslashes($this->pdf->portefeuilledata[$this->pdf->portefeuilledata['txtKoppeling']]);
			$koppeling = stripslashes($koppeling);
			$query = "SELECT * FROM custom_txt WHERE
  type = '".$this->pdf->portefeuilledata['txtKoppeling']."' AND
  field = '".$this->pdf->rapport_type."_".$koppeling."' AND
  Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  ";
			$this->DB->SQL($query);
			$txtData = $this->DB->lookupRecord();
			$this->pdf->rapport_titel = $txtData['title'];
			$briefData = $txtData['txt'];// html_entity_decode(strip_tags($txtData['txt']));

			if($briefData<>'')
			{
				$this->pdf->rapport_type ='FRONTBRIEF';
				$this->pdf->AddPage();
			//	$this->pdf->SetY(50);
			//	$this->pdf->SetWidths(array(5,297-$this->pdf->marge*2-10));
		//		$this->pdf->row(array('', $briefData));
				$brief = new htmlBrief_L64($this->pdf);
				$brief->WriteHTML($briefData);
			}
		}
  }
}

if(!class_exists('htmlBrief_L64'))
{
	global $__appvar;
	include_once($__appvar["basedir"]."/classes/AE_cls_html2fpdfRapport.php");
	class htmlBrief_L64 extends html2fpdfRapport
	{
		function htmlBrief_L64($pdf)
		{
			$this->pdf = &$pdf;
			$this->html2fpdfRapport();//'L','mm','A4');
			$this->pdf->pgwidth=297-$this->pdf->marge*2;
		}
	}
}
?>
