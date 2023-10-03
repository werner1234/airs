<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/10/09 15:13:20 $
File Versie					: $Revision: 1.6 $

$Log: RapportFRONT_L79.php,v $
Revision 1.6  2019/10/09 15:13:20  rvv
*** empty log message ***

Revision 1.5  2019/09/28 17:20:17  rvv
*** empty log message ***

Revision 1.4  2019/04/10 15:50:36  rvv
*** empty log message ***

Revision 1.3  2019/01/20 12:14:00  rvv
*** empty log message ***

Revision 1.2  2018/12/29 13:57:23  rvv
*** empty log message ***

Revision 1.1  2018/11/24 19:10:45  rvv
*** empty log message ***

Revision 1.7  2018/11/21 16:48:32  rvv
*** empty log message ***

Revision 1.6  2018/09/05 15:53:27  rvv
*** empty log message ***

Revision 1.5  2018/03/11 10:53:28  rvv
*** empty log message ***

Revision 1.4  2018/03/04 10:14:13  rvv
*** empty log message ***

Revision 1.3  2013/11/23 17:23:24  rvv
*** empty log message ***

Revision 1.2  2013/03/17 10:58:29  rvv
*** empty log message ***

Revision 1.1  2013/03/13 17:01:08  rvv
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

class RapportFront_L79
{
	function RapportFront_L79($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
    
    $rapport_kop_bgcolor=$this->pdf->rapport_kop_bgcolor;
    $this->pdf->rapport_kop_bgcolor = array('r' => 49, 'g' => 133, 'b' => 156);// array('r' => 89, 'g' => 89, 'b' => 89);//array('r'=>0,'g'=>51,'b'=>102);
		
		$query = "SELECT
    CRM_naw.Adres,
CRM_naw.Pc,
CRM_naw.Plaats,
CRM_naw.Land,
                Portefeuilles.Portefeuille,
                Portefeuilles.Client,
                 Portefeuilles.ClientVermogensbeheerder,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            Portefeuilles
                JOIN Accountmanagers ON Accountmanagers.Accountmanager = Portefeuilles.Accountmanager 
                JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
                LEFT JOIN  CRM_naw on Portefeuilles.portefeuille=CRM_naw.Portefeuille 
		          WHERE
		            Portefeuilles.Portefeuille = '".$this->portefeuille."' ";
		$this->DB->SQL($query);
		$this->DB->Query();
		$portefeuilledata = $this->DB->nextRecord();
    if($portefeuilledata['Pc']<>'')
      $portefeuilledata['Plaats']=$portefeuilledata['Pc'].' '.$portefeuilledata['Plaats'];

	
    $this->pdf->AddPage('L');
    
    
    $this->pdf->setFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->rect(30,25,40,210-50,'F');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',62);
    $this->pdf->setTextColor(255);
    $this->pdf->textWithRotation(58,165,date("Y",$this->rapportageDatumJul),90);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',32);
    $this->pdf->setTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    
    $this->pdf->widthA = array(80,200);
    $this->pdf->alignA = array('L','L','L');
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->setAligns($this->pdf->alignA);
    
    $this->pdf->SetY(58);
    $kwartaal=intval(ceil(date("n",$this->rapportageDatumJul)/3));
    $kwartalen=array('1'=>'eerste','2'=>'tweede','3'=>'derde','4'=>'vierde');
    $this->pdf->row(array('','Beheerrapportage '.$kwartalen[$kwartaal].' kwartaal'));
    
    $this->pdf->SetY(70);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',20);
    $portNr=preg_replace('/[^0-9]/','',$portefeuilledata['Client']);
    if($portefeuilledata['ClientVermogensbeheerder']<>'')
      $portNr.=' / '.$portefeuilledata['ClientVermogensbeheerder'];
		$this->pdf->row(array(' ',$portNr)); //
		/*
    $this->pdf->SetY(165);
    $this->pdf->setAligns(array('L','R'));
    $this->pdf->SetWidths(array(80,180));
     $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderNaam']));
    */
    if(is_file($this->pdf->rapport_logo))
    {
      
      $factor=0.055;
      $xSize=1200*$factor;
      $ySize=458*$factor;
      //$logopos=(297/2)-($xSize/2);
      $this->pdf->Image($this->pdf->rapport_logo, 180, 150, $xSize, $ySize);
    }
    
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;
    
    
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
      $titel = $txtData['title'];
      $briefData = html_entity_decode(strip_tags($txtData['txt']));
      
      if($txtData['extraKoppeling'] <> '')
      {
        include_once("../classes/AE_cls_fpdf.php");
        include_once('../classes/fpdi/fpdi.php');
        //$pdf=&new FPDI('P');

        $pagecount = $this->pdf->setSourceFile($txtData['extraKoppeling']);
 
        for($n=1; $n<=$pagecount; $n++)
        {
          $tplidx = $this->pdf->importPage($n);//importPage
          //$size = $this->pdf->getTemplateSize($tplidx);
          
          $this->pdf->addPage();
          
          //$this->pdf->Rotate(-90);
          //$this->pdf->useTemplate($tplidx,0,-297+$this->pdf->marge*2,$size['w'],$size['h']);
  
          $this->pdf->useTemplate($tplidx);

        }
        $this->pdf->customPageNo+=$pagecount;
  
      }
      //exit;
    }
    $this->pdf->rapport_kop_bgcolor=$rapport_kop_bgcolor;
//    $this->pdf->AutoPageBreak=false;
//    $this->pdf->SetY(-10);
//    $this->pdf->MultiCell(290,4,"Via onze website kunt u dagelijks uw portefeuille inzien.",0,'C');
//    $this->pdf->AutoPageBreak=true;


	}
}
?>
