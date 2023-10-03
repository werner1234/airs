<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/05/04 18:22:49 $
File Versie					: $Revision: 1.13 $

$Log: RapportFRONT_L27.php,v $
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

class RapportFront_L27
{
	function RapportFront_L27($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
    $this->pdf->frontPage=true;
	  $this->pdf->addPage('P');
    
    if($this->pdf->selectData['allInOne']==1)
    {
      $this->pdf->oddEvenCheck[$this->portefeuille]=count($this->pdf->pages);
    }
    else
    {
	  	if (!(count($this->pdf->pages) % 2))
	  	{
	  	  $this->pdf->frontPage=true;
    		$this->pdf->AddPage('P');
	  	}
    }
	  $this->pdf->frontPage = true;


	  $logo=$__appvar['basedir']."/html/rapport/logo/logo_fintessa.jpg";

    if(is_file($logo))
		{
			$this->pdf->Image($logo, 144, 10, 54, 15);

			$this->pdf->SetY(28);
		$this->pdf->SetWidths(array(135,15,50));
	  $this->pdf->SetAligns(array('R','R','L'));
	  $this->pdf->rowHeight = 3.5;
	  $this->pdf->SetFont($this->pdf->brief_font,'',8);
	  $this->pdf->row(array('',"Telefoon","+31 (0)35 543 1450"));
	  $this->pdf->row(array('',"Fax","+31 (0)35 542 6006"));
	  $this->pdf->row(array('',"Adres","Amsterdamsestraatweg 37\n3744 MA Baarn"));
	  //
		}


		$this->pdf->SetWidths(array(25-$this->pdf->marge,140));
	  $this->pdf->SetAligns(array('R','L'));
    $this->pdf->rowHeight = 5;

	  $query = "SELECT
		            Clienten.Naam,
                Clienten.Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Clienten.Land,
                Portefeuilles.Portefeuille,
                Portefeuilles.Risicoklasse,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Naam as VermogensbeheerderNaam,
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
		$portefeuilledata['Naam']=$this->pdf->portefeuilledata['Naam'];
		$portefeuilledata['Naam1']=$this->pdf->portefeuilledata['Naam1'];
		$portefeuilledata['Adres']=$this->pdf->portefeuilledata['Adres'];
		$portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['Woonplaats'];
		$portefeuilledata['Land']=$this->pdf->portefeuilledata['Land'];


			  $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.verzendAdres,
CRM_naw.verzendPc,
CRM_naw.verzendPlaats,
CRM_naw.verzendLand,
CRM_naw.verzendAanhef,
CRM_naw.ondernemingsvorm,
CRM_naw.titel,
CRM_naw.voorletters,
CRM_naw.tussenvoegsel,
CRM_naw.achternaam,
CRM_naw.achtervoegsel,
CRM_naw.part_naam,
CRM_naw.part_voorvoegsel,
CRM_naw.part_titel,
CRM_naw.part_voorletters,
CRM_naw.part_tussenvoegsel,
CRM_naw.part_achternaam,
CRM_naw.part_achtervoegsel,
CRM_naw.enOfRekening
FROM CRM_naw WHERE Portefeuille = '".$this->portefeuille."'  ";

	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();

	  $this->pdf->SetY(42);
	  $this->pdf->SetFont($this->pdf->brief_font,'B',11);
	  $this->pdf->row(array('',"Vertrouwelijk"));
	  $this->pdf->SetFont($this->pdf->brief_font,'',11);
		$this->pdf->row(array('',$crmData['verzendAanhef']));
    $this->pdf->row(array('',$crmData['verzendAdres']));
    $plaats=$crmData['verzendPc'];
    if($crmData['verzendPlaats'] != '') $plaats.=" ".$crmData['verzendPlaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$crmData['verzendLand']));


    $this->pdf->SetY(105);
    $this->pdf->SetFont($this->pdf->brief_font,'',11);
//    $this->pdf->row(array('','Datum: '.(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
    $this->pdf->ln(8);

    $rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' t/m '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);

//    $this->pdf->row(array('','Betreft: Rapportage over de periode '.$rapportagePeriode));
    $this->pdf->SetFont($this->pdf->brief_font,'',11);
    $this->pdf->SetY(140);
    $geachte="Geachte";
    if($crmData['titel'] != '') $geachte.=" ".$crmData['titel'];
    if($crmData['achtervoegsel'] != '') $geachte.=" ".$crmData['achtervoegsel'];
    if($crmData['part_titel'] != '' && $crmData['enOfRekening'] == 1) $geachte.=" e/o ".$crmData['part_titel'];
    if($crmData['tussenvoegsel'] != '') $geachte.=" ".$crmData['tussenvoegsel'];
    if($crmData['achternaam'] != '') $geachte.=" ".$crmData['achternaam'];

//    $this->pdf->row(array('',"$geachte,\n\n"));

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
        //include_once("../classes/AE_cls_fpdf.php");
        //include_once('../classes/fpdi/fpdi.php');
        //$pdf =&  new FPDI();
          /*
        $pdf->addPage();
        $pagecount = $pdf->setSourceFile($txtData['extraKoppeling']);
        for($n=1; $n<=1; $n++)
        {
          $tplidx = $pdf->importPage($n);//importPage
          $this->pdf->addPage();
          $pdf->useTemplate($tplidx);
          $this->pdf->pages[count($this->pdf->pages)]=$pdf->tpls[1]['buffer'];
        }
          */
          $pagecount = $this->pdf->setSourceFile($txtData['extraKoppeling']);
          for($n=1; $n<=$pagecount; $n++)
          {
            $tplidx = $this->pdf->importPage($n);//importPage
            $this->pdf->addPage();
            $this->pdf->useTemplate($tplidx);
          }

        }
		  }

    $this->pdf->row(array('',$briefData));
    $this->pdf->last_rapport_type="FRONT";
	  $this->pdf->addPage('P');
    $this->pdf->frontPage=true;
    $this->pdf->rowHeight = 4;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}
}
?>