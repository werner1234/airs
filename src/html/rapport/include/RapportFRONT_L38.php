<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/02 15:20:30 $
File Versie					: $Revision: 1.21 $

$Log: RapportFRONT_L38.php,v $
Revision 1.21  2019/11/02 15:20:30  rvv
*** empty log message ***

Revision 1.20  2017/03/29 15:57:04  rvv
*** empty log message ***

Revision 1.19  2016/10/30 13:02:59  rvv
*** empty log message ***

Revision 1.18  2016/10/29 15:41:46  rvv
*** empty log message ***

Revision 1.17  2016/10/26 13:52:39  rvv
*** empty log message ***

Revision 1.16  2016/10/26 12:29:07  rvv
*** empty log message ***

Revision 1.15  2016/10/05 16:18:42  rvv
*** empty log message ***

Revision 1.14  2015/11/08 16:35:01  rvv
*** empty log message ***

Revision 1.13  2014/03/22 15:47:14  rvv
*** empty log message ***

Revision 1.12  2013/11/02 17:04:05  rvv
*** empty log message ***

Revision 1.11  2013/07/20 16:26:07  rvv
*** empty log message ***

Revision 1.10  2013/04/06 16:16:30  rvv
*** empty log message ***

Revision 1.9  2013/04/03 14:58:34  rvv
*** empty log message ***

Revision 1.8  2013/02/20 15:12:14  rvv
*** empty log message ***

Revision 1.7  2012/10/31 10:06:01  rvv
*** empty log message ***

Revision 1.6  2012/10/10 13:37:12  cvs
update 10-10-2012

Revision 1.5  2012/08/01 16:57:55  rvv
*** empty log message ***

Revision 1.4  2012/07/11 10:38:43  rvv
*** empty log message ***

Revision 1.3  2012/07/11 10:20:44  rvv
*** empty log message ***

Revision 1.2  2012/06/09 13:43:40  rvv
*** empty log message ***

Revision 1.1  2012/05/27 08:33:11  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L38
{
	function RapportFront_L38($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
  
	function voorBriefNAW()
	{
	  global $__appvar;
    if($this->pdf->selectData['allInOne']==1)
    {
    
			//if (count($this->pdf->pages) % 2)
			//	$this->pdf->AddPage($this->pdf->CurOrientation);

    }
    else
    {
		 // if (count($this->pdf->pages) % 2)
  	//	  $this->pdf->AddPage($this->pdf->CurOrientation);
    }

		$this->pdf->AddPage('P');
  	$this->pdf->frontPage = true;
		$this->pdf->emailSkipPages[]=$this->pdf->page;
   	$this->pdf->underlinePercentage=0.8;
    $this->pdf->brief_font='Times';
    $this->pdf->SetFont($this->pdf->brief_font,'',8);



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
CRM_naw.enOfRekening,
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'  ";

	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
	  		$this->pdf->SetWidths(array(20-$this->pdf->marge,140));

	  $this->pdf->SetAligns(array('R','L','L','R','R'));

	  $this->pdf->SetY(50);
	  $this->pdf->SetFont($this->pdf->brief_font,'B',11);
	  $this->pdf->row(array('',""));//Vertrouwelijk
	  $this->pdf->SetFont($this->pdf->brief_font,'',11);
		$this->pdf->row(array('',$crmData['naam']));
    if (trim($crmData['naam1']) <> "")  $this->pdf->row(array('',$crmData['naam1']));
    $this->pdf->row(array('',$crmData['verzendAdres']));
    $plaats=$crmData['verzendPc'];
    if($crmData['verzendPlaats'] != '') $plaats.=" ".$crmData['verzendPlaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$crmData['verzendLand']));

		$this->pdf->AddPage($this->pdf->CurOrientation);
		$this->pdf->emailSkipPages[]=$this->pdf->page;


	}

	function voorBrief()
	{
	  global $__appvar;

	//	if (count($this->pdf->pages) % 2)
  //		$this->pdf->AddPage($this->pdf->CurOrientation);
		
		$this->pdf->AddPage('P');
		$this->pdf->emailSkipPages[]=$this->pdf->page;
  	$this->pdf->frontPage = true;

   	$this->pdf->underlinePercentage=0.8;
    $this->pdf->brief_font='Times';
    $this->pdf->SetFont($this->pdf->brief_font,'',8);

		if(file_exists($__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Remisier'].".png"))
		  $logo = $__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Remisier'].".png";
		else
		  $logo = $__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];

		if(is_file($logo))
		{
		  $factor=0.08;
		  $xSize=1500*$factor; 
		  $ySize=372*$factor;
	    $this->pdf->Image($logo, 45, 10, $xSize, $ySize);
		}

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
CRM_naw.enOfRekening,
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'  ";

	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
	  		$this->pdf->SetWidths(array(25-$this->pdf->marge,140));

	  $this->pdf->SetAligns(array('R','L','L','R','R'));

	  $this->pdf->SetY(72);
	  $this->pdf->SetFont($this->pdf->brief_font,'B',11);
	  $this->pdf->row(array('',""));//Vertrouwelijk
	  $this->pdf->SetFont($this->pdf->brief_font,'',11);
		$this->pdf->row(array('',$crmData['naam']));
    if (trim($crmData['naam1']) <> "")  $this->pdf->row(array('',$crmData['naam1']));
    $this->pdf->row(array('',$crmData['verzendAdres']));
    $plaats=$crmData['verzendPc'];
    if($crmData['verzendPlaats'] != '') $plaats.=" ".$crmData['verzendPlaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$crmData['verzendLand']));



$this->pdf->SetY(110);
		$this->pdf->SetWidths(array(25-$this->pdf->marge,140));
$this->pdf->row(array('','Bussum, '.(date("j"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
$this->pdf->ln(1);
$this->pdf->SetWidths(array(25-$this->pdf->marge,40,70));


//$this->pdf->SetFont($this->pdf->brief_font,'B',11);
$this->pdf->SetWidths(array(25-$this->pdf->marge,160,10,30));
$this->pdf->SetAligns(array('L','L','C','R'));

$this->pdf->ln(8);


$txt=$crmData['verzendAanhef'].",

";

//Bijgaand treft u aan de overzichten per ultimo ".vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$pdf->rapport_taal)." ".date("Y").". Indien u vragen en/of opmerkingen heeft horen wij dat graag van u.
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
  $txt .= $briefData;//."\n".$this->pdf->portefeuilledata['AccountmanagerNaam'];
}
$this->pdf->row(array('',$txt));
$this->pdf->ln();


if($this->pdf->portefeuilledata['Remisier']=='Tostrams')
{
  $this->pdf->rowHeight = 3.6;
  $trigger=$this->pdf->PageBreakTrigger;
  $this->pdf->PageBreakTrigger=$this->pdf->PageBreakTrigger+10;
  $this->pdf->setY(273);
  $this->pdf->SetAligns(array('R','C'));
  $this->pdf->SetFont($this->pdf->brief_font,'I',8);
  $this->pdf->SetWidths(array(0,195));
  $this->pdf->row(array('',"\n\nTostrams Vermogensbeheer is een handelsnaam van Steentjes Vermogensbeheer\nPostbus 1359, 1400 BJ Bussum\nvermogensbeheer@tostrams.nl "));
  $this->pdf->SetFont($this->pdf->brief_font,'BI',8);
  $this->pdf->PageBreakTrigger=$trigger;
}
else
{
  //$this->pdf->Rect(20,280,4,12,'F','F',array(140,94,44));
  //$this->pdf->Rect(185,280,4,12,'F','F',array(140,94,44));
  $this->pdf->rowHeight = 3.6;
  $trigger=$this->pdf->PageBreakTrigger;
  $this->pdf->PageBreakTrigger=$this->pdf->PageBreakTrigger+10;
  $this->pdf->setY(273);
  $this->pdf->SetAligns(array('R','C'));
  $this->pdf->SetFont($this->pdf->brief_font,'',8);
  $this->pdf->SetWidths(array(0,195));
  $this->pdf->row(array('',"Vergunninghouder AFM\nZwarteweg 10, Postbus 1359, 1400 BJ Gooise Meren\n tel: 035-692 17 00\nABN AMRO.IBAN NL26 ABNA 0516 690 671, K.v.K. 20078649"));
  $this->pdf->SetFont($this->pdf->brief_font,'B',8);
  $this->pdf->row(array('',"www.steentjes-vermogensbeheer.nl"));
  $this->pdf->PageBreakTrigger=$trigger;
}


 $this->pdf->SetTextColor(0,0,0);
 $this->pdf->geenBasisFooter=true;
		$this->pdf->AddPage($this->pdf->CurOrientation);
		$this->pdf->emailSkipPages[]=$this->pdf->page;

	}


	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT
		            CRM_naw.naam as Naam,
                CRM_naw.naam1 as Naam1,
                CRM_naw.verzendAdres as Adres,
                CRM_naw.verzendPc as Pc,
                CRM_naw.verzendPlaats as Woonplaats,
                CRM_naw.verzendLand as Land,
                Portefeuilles.Portefeuille,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            (Portefeuilles, Clienten , Accountmanagers, Vermogensbeheerders)
		            LEFT JOIN CRM_naw ON Portefeuilles.portefeuille = CRM_naw.Portefeuille
		          WHERE
		            Portefeuilles.Portefeuille = '".$this->portefeuille."' AND
		            Portefeuilles.Client = Clienten.Client AND
                Accountmanagers.Accountmanager = Portefeuilles.Accountmanager AND
                Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder";
		$this->DB->SQL($query);
		$this->DB->Query();
		$portefeuilledata = $this->DB->nextRecord();
		
		$this->pdf->oddPageReportStart[$this->portefeuille][$this->pdf->rapport_type]=$this->pdf->page;
		
		if($this->pdf->selectData['type'] != 'eMail')
    {
      $this->voorBriefNAW();
		  $this->voorBrief();
    }

		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');
		$this->pdf->oddPageReportStart[$this->portefeuille][$this->pdf->rapport_type]=$this->pdf->page;


		if(is_file($this->pdf->rapport_logo))
		{
			 // $pdfObject->Image($pdfObject->rapport_logo, 18, 3.5, 52, 20.6);
		  $factor=0.056;
		  $xSize=1500*$factor; 
		  $ySize=372*$factor;
			 $this->pdf->Image($this->pdf->rapport_logo, 18, 13, $xSize, $ySize);
		}


   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = 10; //$this->pdf->rapport_fontsize
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->SetY(48);
		$this->pdf->SetWidths(array(30,120));
		$this->pdf->row(array('',$portefeuilledata['Naam']));
		$this->pdf->ln(2);
    if ($portefeuilledata['Naam1'] != '')
    {
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
      $this->pdf->ln(2);
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->ln(2);

    $plaats=$portefeuilledata['Pc'];
    if($portefeuilledata['Woonplaats'] != '') $plaats.=" ".$portefeuilledata['Woonplaats'];
    $this->pdf->row(array('',$plaats));

    if ($portefeuilledata['Land'] != '')
    {
      $this->pdf->ln(2);
      $this->pdf->row(array('',$portefeuilledata['Land']));
    }

    $this->pdf->SetWidths($this->pdf->widthA);

		$this->pdf->SetY(80);

		$rapportagePeriode = vertaalTekst('Verslagperiode',$this->pdf->rapport_taal).' '.date("j",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                                          date("j",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
		$this->pdf->row(array('',$rapportagePeriode));
		$this->pdf->ln(6);

		$consolidatiePortefeuilledata=array();
		foreach ($this->pdf->portefeuilles as $portefeuille)
		{
			$query = "SELECT
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam,
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam1,Clienten.Naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
                          FROM
                            Portefeuilles
                            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
                            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
                            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
                            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
                          WHERE
                            Portefeuilles.Portefeuille = '".$portefeuille."'";

			$this->DB->SQL($query);
			$this->DB->Query();
			$consolidatiePortefeuilledata[] = $this->DB->nextRecord();

		}


    $extraY=0;
		if(count($consolidatiePortefeuilledata)>1)
		{
			$this->pdf->SetWidths(array(30,120));
			$this->pdf->row(array(' ',vertaalTekst('Geconsolideerde vermogensrapportage',$this->pdf->rapport_taal)));
			$this->pdf->ln();
			$this->pdf->SetWidths(array(30,40,25,120));
			if($_POST['anoniem']!=1)
			{
				foreach($consolidatiePortefeuilledata as $pdata)
				{
					$this->pdf->row(array('',$pdata['Portefeuille'],$pdata['Depotbank'],$pdata['Naam']));
					$this->pdf->Ln(1);
				}
			}
			$this->pdf->SetWidths($this->pdf->widthA);
			$extraY=12;
		}
		else
		{
  		$oldPortefeuilleString = $portefeuilledata['Portefeuille'];
  	  $i=1;
  		$portefeuilleString='';
  		for($j=0;$j<strlen($oldPortefeuilleString);$j++)
  		{
	  	 if($i>3)
		   {
		    $portefeuilleString.='.';
		    $i=1;
		   }
		   $portefeuilleString.= $oldPortefeuilleString[$j];
		   $i++;
		  }
  		$rapportageRekening = vertaalTekst('Rapportage rekening',$this->pdf->rapport_taal).' : '.$portefeuilleString;
	  	$this->pdf->row(array(' ',$rapportageRekening));
		}


		$this->pdf->SetY(113+$extraY);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		$this->pdf->row(array('',''));

		$this->pdf->SetY(140);

    $explodedName=explode(" ",$portefeuilledata['vermogensbeheerderNaam']);
    foreach ($explodedName as $key=>$word)
      $explodedName[$key]=vertaalTekst($word,$this->pdf->rapport_taal);
		$portefeuilledata['vermogensbeheerderNaam']=implode(" ",$explodedName);

		$this->pdf->row(array('',$portefeuilledata['vermogensbeheerderNaam']));
	  $this->pdf->ln(1);
	  $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderAdres']));
		$this->pdf->ln(1);
	  $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderWoonplaats']));
	  $this->pdf->ln(1);
	  $this->pdf->row(array('',$portefeuilledata['Email']));
	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;
	  $this->pdf->AddPage();
		$this->pdf->emailSkipPages[]=$this->pdf->page;
		//$this->pdf->frontPage = true;

	}
}
?>