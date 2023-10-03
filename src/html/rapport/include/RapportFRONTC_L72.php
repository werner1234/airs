<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/29 13:56:12 $
File Versie					: $Revision: 1.9 $

$Log: RapportFRONTC_L72.php,v $
Revision 1.9  2020/07/29 13:56:12  rvv
*** empty log message ***

Revision 1.8  2018/08/25 17:11:27  rvv
*** empty log message ***

Revision 1.7  2018/06/16 17:42:56  rvv
*** empty log message ***

Revision 1.6  2018/02/17 19:18:57  rvv
*** empty log message ***

Revision 1.5  2017/08/30 15:03:56  rvv
*** empty log message ***

Revision 1.4  2017/08/26 17:37:43  rvv
*** empty log message ***

Revision 1.3  2017/08/19 18:18:00  rvv
*** empty log message ***

Revision 1.2  2017/02/15 11:25:53  rvv
*** empty log message ***

Revision 1.1  2017/02/08 12:32:32  rvv
*** empty log message ***

Revision 1.5  2017/01/29 10:25:25  rvv
*** empty log message ***

Revision 1.4  2016/11/23 13:06:18  rvv
*** empty log message ***

Revision 1.3  2016/11/19 19:03:08  rvv
*** empty log message ***

Revision 1.2  2016/10/30 13:02:59  rvv
*** empty log message ***

Revision 1.1  2016/10/09 14:45:08  rvv
*** empty log message ***

Revision 1.1  2016/06/15 15:58:41  rvv
*** empty log message ***

Revision 1.3  2016/04/10 15:48:34  rvv
*** empty log message ***

Revision 1.2  2016/04/03 10:58:02  rvv
*** empty log message ***

Revision 1.1  2016/03/27 17:32:03  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONTC_L72
{
	function RapportFRONTC_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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


		$query="SELECT Naam,Naam1,CRM_naw.ondernemingsvorm,CRM_naw.verzendPaAanhef,CRM_naw.verzendAanhef  FROM CRM_naw WHERE CRM_naw.portefeuille='".$this->portefeuille."'";
		$this->DB->SQL($query);
		$this->DB->Query();
		$crm = $this->DB->nextRecord();
		$portefeuilledata['Naam']=$crm['Naam'];

		if($portefeuilledata['ondernemingsvorm']=='Persoon' && $portefeuilledata['Naam1']<>'')
		{
			$portefeuilledata['Naam1']=$crm['Naam1'];
		}
		elseif($portefeuilledata['verzendPaAanhef']<>'')
		{
			$portefeuilledata['verzendPaAanhef']=$crm['verzendPaAanhef'];
		}

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

		$stappen=100;
		$stap=(297/2-$this->pdf->marge*2)/$stappen;
		$kleurStart=array(12,37,119);
		$kleurEind=array(255,255,255);
		$kleur=array();
		$this->pdf->SetFillColor($kleurStart[0],$kleurStart[1],$kleurStart[2]);
		$this->pdf->rect($this->pdf->marge,180,297/2,20,'F');
		for($i=0;$i<$stappen;$i++)
		{
			for($j=0;$j<3;$j++)
				$kleur[$j]=$kleurStart[$j]+($kleurEind[$j]-$kleurStart[$j])*($i/$stappen);
			$this->pdf->SetFillColor($kleur[0],$kleur[1],$kleur[2]);
			$this->pdf->rect($this->pdf->marge+297/2+$stap*$i,180,$stap,20,'F');
		}


$this->pdf->setXY($this->pdf->marge,188);
		$this->pdf->SetTextColor(255,255,255);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',10);
		$this->pdf->MultiCell(297-2*$this->pdf->marge	,4,'Family Office since 1976',0, "C");
		$this->pdf->SetTextColor(0,0,0);



		if(is_file($this->pdf->rapport_logo))
		{
	     $factor=0.04;
		   $xSize=1519*$factor;
		   $ySize=1188*$factor;
       $logopos=(297/2)-($xSize/2);
	     $this->pdf->Image($this->pdf->rapport_logo, $logopos, 3, $xSize, $ySize);
		}

   	$this->pdf->widthA = array(99.5-$this->pdf->marge,180);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = 10; //$this->pdf->rapport_fontsize

    


    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);

    $this->pdf->SetY(88);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
    
    
    $this->pdf->row(array(' ',vertaalTekst('PERSOONLIJK EN VERTROUWELIJK',$this->pdf->rapport_taal)));

		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));
    if($this->pdf->portefeuilledata['Naam1'] <> '')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam1']));
    }
		/*
    if($this->pdf->portefeuilledata['Adres']<>'')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Adres']));
    }
    if($this->pdf->portefeuilledata['Adres']<>'')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Woonplaats']));
    }
		*/
    $this->pdf->SetWidths(array(99.5-$this->pdf->marge,30,5,120));

		if(isset($this->pdf->selectData['type']))
			$rapportagePeriode = DatumFull_L72($this->pdf,db2jul(date("Y",$this->rapportageDatumVanafJul).'-01-01')).' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.DatumFull_L72($this->pdf,$this->rapportageDatumJul);
		else
			$rapportagePeriode = DatumFull_L72($this->pdf,$this->rapportageDatumVanafJul).' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.DatumFull_L72($this->pdf,$this->rapportageDatumJul);


    $this->pdf->ln(5);
    $this->pdf->row(array('',vertaalTekst('Verslagperiode',$this->pdf->rapport_taal),":",$rapportagePeriode));
    //$this->pdf->SetWidths(array(30,40,5,120));

		//$this->pdf->ln(40);
		//if($this->pdf->lastPOST['anoniem']==1)
	//		$portefeuilledata['Portefeuille']='';
    $this->pdf->SetY(148);
		//$this->pdf->row(array(' ',vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal),':',$portefeuilledata['Portefeuille']));
   // $this->pdf->ln();
   // $this->pdf->row(array(' ',vertaalTekst('Mandaat',$this->pdf->rapport_taal),':',vertaalTekst($this->pdf->portefeuilledata['Risicoklasse'],$this->pdf->rapport_taal)));
   // $this->pdf->ln();
    
    //$this->pdf->SetWidths(array(30,120));

 //   $this->pdf->ln(8);


		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal),':',DatumFull_L72($this->pdf,time())));
		//$this->pdf->ln(2);
	//	$this->pdf->row(array('',''));

/*
$this->pdf->AutoPageBreak=false;
$this->pdf->SetY(195);
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize-2);
$this->pdf->Cell(297,5,'Eemnesserweg 11-3, 1251 NA Laren NH - www.ambassadorinvestments.nl - info@ambassadorinvestments.nl - 035-2031035',0,1,'C');
$this->pdf->Cell(297,5,'IBAN: NL59 ABNA 0516 0106 89 - KvK: 28087987 - BTW: NL8092.88.722 B01',0,1,'C');
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
$this->pdf->AutoPageBreak=true;
*/

	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;

  
	}
}
?>
