<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2012/12/09 16:29:32 $
File Versie					: $Revision: 1.17 $

$Log: RapportFront.php,v $
Revision 1.17  2012/12/09 16:29:32  rvv
*** empty log message ***

Revision 1.16  2011/04/09 14:32:04  rvv
*** empty log message ***

Revision 1.15  2011/03/23 16:59:47  rvv
*** empty log message ***

Revision 1.14  2010/10/13 11:36:09  rvv
L14 naam uit CRM

Revision 1.13  2010/04/15 09:59:27  cvs
*** empty log message ***

Revision 1.12  2010/01/20 12:28:18  rvv
*** empty log message ***

Revision 1.11  2010/01/13 11:05:20  rvv
*** empty log message ***

Revision 1.10  2009/04/24 10:36:46  cvs
Pagebreak marge bij L14 aangepast

Revision 1.9  2009/04/22 13:10:50  rvv
*** empty log message ***

Revision 1.8  2008/05/16 08:12:57  rvv
*** empty log message ***

Revision 1.7  2008/03/18 09:30:24  rvv
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

class RapportFront
{
	function RapportFront($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
                Clienten.pc,
                Portefeuilles.Portefeuille,
                Accountmanagers.Naam as accountManager,
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

 if($this->pdf->rapport_layout == 16)
 {
   $this->pdf->AddPage();
   $background = $__appvar['basedir']."/html/rapport/logo/background.jpg";
   if(file_exists($background))
    $this->pdf->Image($background, 0, 0, 300, 210);

		if(is_file($this->pdf->rapport_logo))
		{
			  $this->pdf->Image($this->pdf->rapport_logo, 230, 35, 40, 30);
		}

	 	$this->pdf->widthA = array(20,180);
		$this->pdf->alignA = array('L','L');

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);


		$this->pdf->SetFont($this->pdf->rapport_font,'B',20);

		$kwartaal = floor(date("n",$this->rapportageDatumJul)/3).'e';
		$jaar = date("Y",$this->rapportageDatumJul);
		$this->pdf->SetY(40);

		$this->pdf->row(array(' ',"Vermogensrapportage $kwartaal kwartaal $jaar"));

		$oldPortefeuilleString = $portefeuilledata['Portefeuille'];
	  $i=1;
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
		$rapportageRekening = 'Depot: '.$portefeuilleString;
		$this->pdf->ln(8);
			$this->pdf->row(array(' ',$rapportageRekening));



		$this->pdf->SetFont($this->pdf->rapport_font,'',12);

		$this->pdf->SetY(80);

		$this->pdf->row(array('',$portefeuilledata['Naam']));
		$this->pdf->ln(2);
    if ($portefeuilledata['Naam1'] != '')
    {
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
      $this->pdf->ln(2);
    }
    $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->ln(2);
    $this->pdf->row(array('',$portefeuilledata['Woonplaats']));


		$this->pdf->SetY(100);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',12);

		$this->pdf->ln(4);


		$this->pdf->SetY(150);
		$this->pdf->SetFont($this->pdf->rapport_font,'',12);


		$this->pdf->row(array('','Datum: '.date("d")." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
	  $this->pdf->row(array('','Accountmanager: '.$portefeuilledata['accountManager']));
	  $this->pdf->ln(2);
		$this->pdf->row(array('','Telefoon: '.$portefeuilledata['Telefoon']));
	  $this->pdf->ln(2);
	  $this->pdf->row(array('','E-mail: '.$portefeuilledata['Email']));
	  $this->pdf->ln(2);
		$this->pdf->frontPage = true;

 }
 elseif($this->pdf->rapport_layout == 14)
 {
   //background
   $this->pdf->SetAutoPageBreak(false);
   $this->pdf->AddPage('L');

   $this->pdf->widthA = array(20,150);
	 $this->pdf->alignA = array('L','L');

	 $this->pdf->SetAligns($this->pdf->alignA);

	 //$this->pdf->Rotate(-90,148.5,148.5);

		if(is_file($this->pdf->rapport_logo))
		{
      //$factor=0.09;
		//  $xSize=492*$factor;
		//  $ySize=211*$factor;
		  $factor=0.05;
		  $xSize=983*$factor;
		  $ySize=288*$factor;
		  $this->pdf->Image($this->pdf->rapport_logo, 235, 10, $xSize, $ySize);
		}

		$portefeuilledata['Naam']=$this->pdf->portefeuilledata['Naam'];
		$portefeuilledata['Naam1']=$this->pdf->portefeuilledata['Naam1'];
		$portefeuilledata['Adres']=$this->pdf->portefeuilledata['Adres'];
		$portefeuilledata['pc']=$this->pdf->portefeuilledata['pc'];
		$portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['Woonplaats'];

		$this->pdf->SetFont($this->pdf->rapport_font,'',12);
		$this->pdf->SetY(35);
		$this->pdf->SetWidths(array(30,120));
		$this->pdf->row(array('',$portefeuilledata['Naam']));
		$this->pdf->ln(2);
    if ($portefeuilledata['Naam1'] != '')
    {
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
      $this->pdf->ln(2);
    }
    $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->ln(2);

    $plaats='';
		if($portefeuilledata['pc'] != '')
		  $plaats .= $portefeuilledata['pc']." ";
		$plaats .= $portefeuilledata['Woonplaats'];
		$this->pdf->row(array('',$plaats));


    $this->pdf->SetWidths($this->pdf->widthA);

		$this->pdf->SetY(85);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',12);

		$rapportagePeriode = 'Rapportageperiode '.date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' t/m '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
		$this->pdf->row(array('',$rapportagePeriode));
		$this->pdf->ln(4);

		$oldPortefeuilleString = $portefeuilledata['Portefeuille'];
	  $i=1;
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

		$rapportageRekening = 'Rapportage rekening nr.: '.$portefeuilleString;
		$this->pdf->row(array(' ',$rapportageRekening));

		$this->pdf->SetFont($this->pdf->rapport_font,'',12);

		$this->pdf->SetY(170);
		$this->pdf->SetFont($this->pdf->rapport_font,'',12);

		$this->pdf->row(array('','Datum: '.date("d")." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		$this->pdf->row(array('','Telefoon: '.$portefeuilledata['Telefoon']));
	  $this->pdf->ln(2);
	  $this->pdf->row(array('','E-mail: '.$portefeuilledata['Email']));
	  $this->pdf->ln(2);
		$this->pdf->frontPage = true;
		$this->pdf->Rotate(0);
		$this->pdf->SetAutoPageBreak(true, 15);
	}
	if($this->pdf->rapport_layout == 17)
	{
	  $this->pdf->AddPage();
	  $this->pdf->rapport_koptext = "Rekening {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";



	  $txt = "naam en achternaam\nPrinsengracht 563a\n1029 AX Den Haag";
	  $this->pdf->SetFont($this->pdf->rapport_font,'',12);

 $x = 50;
 $y = 185;
 $regelhoogte = 6;
 $this->pdf->TextWithDirection($x,$y,$portefeuilledata['Naam'],'U');
 $x += $regelhoogte;
 if($portefeuilledata['Naam1'] != '')
 {
   $this->pdf->TextWithDirection($x,$y,$portefeuilledata['Naam1'],'U');
   $x += $regelhoogte;
 }
 $this->pdf->TextWithDirection($x,$y,$portefeuilledata['Adres'],'U');
 $x += $regelhoogte;
 $this->pdf->TextWithDirection($x,$y,$portefeuilledata['Woonplaats'],'U');

	  $this->pdf->SetXY(112,20);
	  $this->pdf->SetTextColor(27,74,20);//donker groen
	  $this->pdf->SetFont($this->pdf->rapport_font,'B',16);
	  $this->pdf->cell(100,8,'VERMOGENS RAPPORTAGE');
	  $this->pdf->SetTextColor(0,0,0);

	  $this->pdf->SetFont($this->pdf->rapport_font,'',10);

	  $this->pdf->widthA = array(185,35,80);
		$this->pdf->alignA = array('L','L','L');

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);


	  $this->pdf->SetY(21);

	  $rapportageDatum = date("d",$this->rapportageDatumJul)." ".
		                   vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$pdf->rapport_taal)." ".
		                   date("Y",$this->rapportageDatumJul);

		$rapportagePeriode = ''.date("d",$this->rapportageDatumVanafJul)." ".
		                      vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$pdf->rapport_taal)." ".
		                      date("Y",$this->rapportageDatumVanafJul).
		                      ' t/m '.
		                      date("d",$this->rapportageDatumJul)." ".
		                      vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$pdf->rapport_taal)." ".
		                      date("Y",$this->rapportageDatumJul);

		$this->pdf->Row(array('','rapportage datum',$rapportageDatum));
		$this->pdf->Row(array('','rapportageperiode',$rapportagePeriode));
		$this->pdf->Row(array('','portefeuille',$portefeuilledata['Portefeuille']));

    $imagefile = $__appvar['basedir']."/html/rapport/logo/front.jpg";

	  if(is_file($imagefile))
		{
		  $breedte = 175;
		  $hoogte = $breedte/(650/550);
		  $x = 288 - $breedte;
			$this->pdf->Image($imagefile, $x , 38, $breedte, $hoogte);
		}

		$this->pdf->SetXY(200,190);
  	$this->pdf->AutoPageBreak = false;
  	$logoFile = $this->pdf->rapport_logo;
		if(is_file($logoFile))
		{
			  $this->pdf->Image($this->pdf->rapport_logo, 242, 191, 45, 10);
		}

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
			$this->pdf->SetXY($x-1,194.5);
	    $this->pdf->MultiCell(100,4,$this->pdf->rapport_voettext,'0','L');
	    $this->pdf->Cell(25,4,$this->pdf->rapport_voettext_rechts,'0','L');

	 $this->pdf->AutoPageBreak  = true;
	 		$this->pdf->frontPage = true;

	}
	elseif($this->pdf->rapport_layout == 18)
 {
   //background
$this->pdf->AddPage();

   	$this->pdf->widthA = array(20,120);
		$this->pdf->alignA = array('L','L');

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);


		if(is_file($this->pdf->rapport_logo))
		{
			  $this->pdf->Image($this->pdf->rapport_logo, 220, 5, 65, 20);
		}


		$this->pdf->SetY(80);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',16);

		$this->pdf->row(array('',"Vermogensrapportage"));
				$this->pdf->ln(2);
		$rapportagePeriode = 'Rapportage per '.   date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
		$this->pdf->row(array('',$rapportagePeriode));
		$this->pdf->ln(20);


		$this->pdf->SetFont($this->pdf->rapport_font,'B',12);
		$this->pdf->row(array('',$portefeuilledata['Naam']));
		$this->pdf->ln(2);
    if ($portefeuilledata['Naam1'] != '')
    {
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
      $this->pdf->ln(2);
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',12);



		$this->pdf->ln(2);
		$rapportageRekening = 'deportnummer '.$portefeuilledata['Portefeuille'];
		$this->pdf->row(array(' ',$rapportageRekening));


		$this->pdf->SetY(150);
		$this->pdf->SetFont($this->pdf->rapport_font,'',12);
		$this->pdf->row(array('','Uw beleggingsprofiel:'));
	  $this->pdf->ln(2);
	  $this->pdf->row(array('','Model '.$portefeuilledata['profiel']));

/*
		$this->pdf->row(array('','Datum: '.date("d")." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(2);
		$this->pdf->row(array('','Telefoon: '.$portefeuilledata['Telefoon']));
	  $this->pdf->ln(2);
	  $this->pdf->row(array('','Telefax: '.$portefeuilledata['Fax']));
	  $this->pdf->ln(2);
	  $this->pdf->row(array('','E-mail: '.$portefeuilledata['Email']));
	  $this->pdf->ln(2);
*/

$this->pdf->Rect(8,200,280,2,'F','F',$this->pdf->rapport_voet_bgcolor);
		$this->pdf->frontPage = true;
			}



	}
}
?>