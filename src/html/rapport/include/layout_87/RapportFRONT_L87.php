<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/01/12 14:02:20 $
File Versie					: $Revision: 1.2 $

$Log: RapportFRONT_L87.php,v $
Revision 1.2  2020/01/12 14:02:20  rvv
*** empty log message ***

Revision 1.1  2019/12/11 17:07:39  rvv
*** empty log message ***

Revision 1.1  2017/11/18 18:58:17  rvv
*** empty log message ***

Revision 1.10  2017/07/19 19:29:27  rvv
*** empty log message ***

Revision 1.9  2017/06/24 16:31:57  rvv
*** empty log message ***

Revision 1.8  2016/02/03 17:00:18  rvv
*** empty log message ***

Revision 1.7  2015/12/31 06:42:46  rvv
*** empty log message ***

Revision 1.6  2015/12/23 16:24:41  rvv
*** empty log message ***

Revision 1.5  2015/12/19 14:27:30  rvv
*** empty log message ***

Revision 1.4  2015/05/31 10:15:24  rvv
*** empty log message ***

Revision 1.3  2015/03/01 14:08:16  rvv
*** empty log message ***

Revision 1.2  2015/02/18 17:09:13  rvv
*** empty log message ***

Revision 1.1  2015/02/15 10:26:57  rvv
*** empty log message ***

Revision 1.9  2014/01/22 17:01:30  rvv
*** empty log message ***

Revision 1.8  2014/01/18 17:27:23  rvv
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

class RapportFront_L87
{
	function RapportFront_L87($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$rowHeight=$this->pdf->rowHeight;

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


		if($this->pdf->selectData['allInOne']==1 || $this->pdf->selectData['type'] <>'') // backoffice afdruk
		{
		//	$this->pdf->AddPage('P');
		//	$this->pdf->emailSkipPages[]=$this->pdf->page;
		//	$this->voorBrief();
		//	listarray($this->pdf->selectData);
		}
		$this->pdf->rowHeight=$rowHeight;
		//if($this->pdf->selectData['type'] != 'eMail')
		//
   //background

		///if ((count($this->pdf->pages) % 2))
		//{
		//  $this->pdf->frontPage=true;
  	//	$this->pdf->AddPage($this->pdf->CurOrientation);
		//}
		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);


		if(is_file($this->pdf->rapport_logo))
		{
      $factor=0.05;
      $xSize=1095*$factor;
      $ySize=300*$factor;
      $logopos=($this->pdf->w - $xSize - $this->pdf->marge);
	    $this->pdf->Image($this->pdf->rapport_logo, $logopos, $this->pdf->marge, $xSize, $ySize);
		}

   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = 10; //$this->pdf->rapport_fontsize

    


    $this->pdf->SetWidths($this->pdf->widthA);

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
		$this->pdf->row(array(' ',vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal),':',formatPortefeuille($this->pdf->portefeuilledata['Portefeuille'])));
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


	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;

   
    $this->pdf->rapport_type = "INHOUD";
	  $this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

    $this->voorBrief();
    
  }
  
  function voorBrief()
  {

    if($this->pdf->portefeuilledata['txtKoppeling'] !='')
    {
      $koppeling = stripslashes($this->pdf->portefeuilledata[$this->pdf->portefeuilledata['txtKoppeling']]);
      $koppeling = stripslashes($koppeling);
      $query = "SELECT * FROM custom_txt WHERE
  type = '".$this->pdf->portefeuilledata['txtKoppeling']."' AND
  field = 'FRONT_".$koppeling."' AND
  Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  ";
      $this->DB->SQL($query);
      $txtData = $this->DB->lookupRecord();
      $this->pdf->rapport_titel = '';//$txtData['title'];
      $briefData = html_entity_decode(strip_tags($txtData['txt']));//$txtData['txt'];//
      $this->pdf->SetFont($this->pdf->rapport_font,'',10);
      if($briefData<>'')
      {
        $this->pdf->rapport_type ='FRONTBRIEF';
        $this->pdf->AddPage();
       	$this->pdf->SetY(40);
        $this->pdf->SetWidths(array(5,297-$this->pdf->marge*2-10));
        $this->pdf->SetFont($this->pdf->rapport_font,'B',11);
        $this->pdf->row(array('', $txtData['title']));
        $this->pdf->ln();
        $this->pdf->SetFont($this->pdf->rapport_font,'',11);
        $this->pdf->row(array('', $briefData));
        //$brief = new htmlBrief_L64($this->pdf);
        //$brief->WriteHTML($briefData);
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
