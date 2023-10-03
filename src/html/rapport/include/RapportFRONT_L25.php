<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/18 17:06:34 $
File Versie					: $Revision: 1.28 $

$Log: RapportFRONT_L25.php,v $
Revision 1.28  2020/04/18 17:06:34  rvv
*** empty log message ***

Revision 1.27  2020/02/26 16:12:54  rvv
*** empty log message ***

Revision 1.26  2020/02/22 18:46:19  rvv
*** empty log message ***

Revision 1.25  2020/02/19 15:02:02  rvv
*** empty log message ***

Revision 1.24  2020/02/15 18:29:05  rvv
*** empty log message ***

Revision 1.23  2020/02/02 12:05:21  rvv
*** empty log message ***

Revision 1.22  2020/02/01 18:11:55  rvv
*** empty log message ***

Revision 1.21  2020/01/08 16:35:02  rvv
*** empty log message ***

Revision 1.20  2019/07/05 16:42:29  rvv
*** empty log message ***

Revision 1.19  2019/06/15 20:53:26  rvv
*** empty log message ***

Revision 1.18  2019/05/18 16:29:36  rvv
*** empty log message ***

Revision 1.17  2017/08/30 15:03:56  rvv
*** empty log message ***

Revision 1.16  2017/02/04 19:11:39  rvv
*** empty log message ***

Revision 1.15  2016/07/24 09:50:58  rvv
*** empty log message ***

Revision 1.14  2016/02/04 11:53:49  rvv
*** empty log message ***

Revision 1.13  2016/01/30 16:22:58  rvv
*** empty log message ***

Revision 1.12  2016/01/24 09:52:26  rvv
*** empty log message ***

Revision 1.11  2013/12/21 18:31:53  rvv
*** empty log message ***

Revision 1.10  2013/11/04 08:56:05  rvv
*** empty log message ***

Revision 1.9  2012/12/02 11:05:56  rvv
*** empty log message ***

Revision 1.8  2012/11/28 17:04:42  rvv
*** empty log message ***

Revision 1.7  2012/10/07 14:57:17  rvv
*** empty log message ***

Revision 1.6  2012/09/09 17:35:27  rvv
*** empty log message ***

Revision 1.5  2012/02/09 12:15:46  cvs
adreswijziging

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

class RapportFront_L25
{
	function RapportFront_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$this->pdf->frontPage = true;

	}


  
  function addBrief()
  {
    global $__appvar;
    $this->pdf->addPage('P');
    $this->pdf->emailSkipPages[]=$this->pdf->page;
    $this->pdf->frontPage = true;
	  $this->pdf->SetWidths(array(25,140));
	  $this->pdf->SetAligns(array('R','L'));
    $this->pdf->rowHeight = 5;
    
    if(is_file($this->pdf->rapport_logo))
		{
			//$this->pdf->Image($this->pdf->rapport_logo, 10, 10, 48);
			$w=48;
      $this->pdf->Image($this->pdf->rapport_logo, $this->pdf->w/2-$w/2, 10, 48);
		}

	  $query = "SELECT
		            Clienten.Naam,
                Clienten.Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Clienten.Land,
                Portefeuilles.selectieveld1,
                Portefeuilles.Portefeuille,
                Portefeuilles.Risicoklasse,
                Accountmanagers.Naam as accountManager,
                Accountmanagers.Handtekening as handtekening,
                Accountmanagers.Titel as titel,
                Vermogensbeheerders.Naam as VermogensbeheerderNaam,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                tweedeAanspreekpunt.naam as tweedeAanspreekpunt,
                tweedeAanspreekpunt.Handtekening as tweedeHandtekening
		          FROM
		            (Portefeuilles, Clienten , Accountmanagers, Vermogensbeheerders)
                LEFT JOIN Accountmanagers as tweedeAanspreekpunt ON Portefeuilles.tweedeAanspreekpunt= tweedeAanspreekpunt.Accountmanager
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
        
    if(isset($this->pdf->extraAdres['naam']))
      $portefeuilledata['Naam']=$this->pdf->extraAdres['naam'];
    if(isset($this->pdf->extraAdres['naam1']))
      $portefeuilledata['Naam1']=$this->pdf->extraAdres['naam1'];
    if(isset($this->pdf->extraAdres['adres']))
      $portefeuilledata['Adres']=$this->pdf->extraAdres['adres'];
    if(isset($this->pdf->extraAdres['pc']))
      $portefeuilledata['Woonplaats']=$this->pdf->extraAdres['pc'].' '.$this->pdf->extraAdres['plaats'];           
    if(isset($this->pdf->extraAdres['land']))
      $portefeuilledata['Land']=$this->pdf->extraAdres['land'];  

	  $query = "SELECT verzendAanhef FROM CRM_naw WHERE portefeuille = '".$this->portefeuille."' ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
    $portefeuilledata['aanhef']=$crmData['verzendAanhef'];

	  $extraDagen = 0; //2
	  $this->pdf->SetY(42);
	  $this->pdf->SetFont($this->pdf->brief_font,'B',11);
	  $this->pdf->row(array('',"Vertrouwelijk"));
	  $this->pdf->SetFont($this->pdf->brief_font,'',11);
		$this->pdf->row(array('',$portefeuilledata['Naam']));
    if ($portefeuilledata['Naam1'] != '')
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
    $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->row(array('',$portefeuilledata['Woonplaats']));
    $this->pdf->row(array('',$portefeuilledata['Land']));


    $this->pdf->SetY(105);
    $this->pdf->SetFont($this->pdf->brief_font,'',11);
    $this->pdf->row(array('','Datum: '.(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
    $this->pdf->ln(8);

    $rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' t/m '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);

    $this->pdf->row(array('','Betreft: Rapportage over de periode '.$rapportagePeriode));
    $this->pdf->SetFont($this->pdf->brief_font,'',11);
    $this->pdf->SetY(140);
    $this->pdf->row(array('','Geachte '.$portefeuilledata['aanhef'].",\n\n"));

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
		  }

    $this->pdf->ln();
    $this->pdf->row(array('',$briefData));

//    if($portefeuilledata['handtekening'] <> '')
//      $this->pdf->MemImage(base64_decode($portefeuilledata['handtekening']), $this->pdf->getX()+20, $this->pdf->getY(), 60);
//    if($portefeuilledata['tweedeHandtekening'] <> '')
//      $this->pdf->MemImage(base64_decode($portefeuilledata['tweedeHandtekening']), $this->pdf->getX()+95, $this->pdf->getY(), 60);
    //$this->pdf->ln(4);
    $this->pdf->SetWidths(array(25,75,75));
    if($portefeuilledata['selectieveld1']=='AMS')
      $this->pdf->row(array('',$portefeuilledata['accountManager'])); //
    else
      $this->pdf->row(array('',$portefeuilledata['accountManager'],$portefeuilledata['tweedeAanspreekpunt'])); //
   // $this->pdf->row(array('',$portefeuilledata['titel']));
    //$portefeuilledata['accountManager'] $portefeuilledata['handtekening'] $portefeuilledata['titel']
/*
     $this->pdf->AutoPageBreak=false;
    $this->pdf->SetY(270);
    $this->pdf->SetWidths(array(10,45,45,45,45));
    $this->pdf->SetAligns(array('L','L','L','L','L'));

    $this->pdf->SetFont($this->pdf->brief_font,'B',8);
    $this->pdf->SetWidths(array(10,90,45,45));
    $this->pdf->SetTextColor(60,60,60);
    $this->pdf->row(array('','Auréus Vermogen'));
    $this->pdf->SetWidths(array(10,45,45,45,45));
    $this->pdf->SetFont($this->pdf->brief_font,'',8);
    $this->pdf->row(array('','Postadres:','Bezoekadres:','t: +31 (0)77 - 351 78 58','IBAN NL72RAB00150098499'));
    $this->pdf->row(array('','Postbus 8024','Nedinscoplein 7','I: www.aureusvermogen.nl','BTW NL821586075B01'));
    $this->pdf->row(array('','5901 AA VENLO','5912 AP VENLO','e:  info@aureusvermogen.nl','KvK Venlo 14124784'));
    $this->pdf->AutoPageBreak=true;
    $this->pdf->SetTextColor(0,0,0);

*/


   $this->pdf->rowHeight = 4;

   $this->pdf->SetFont($this->pdf->rapport_font,'',11);
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
    $this->pdf->Rect(30,0,110,135,'F');
   
    $this->pdf->Rect(0,$this->pdf->h-$hoogte,$this->pdf->w/2,$hoogte,'F');
  
  
  
  
  
  
    /*
     
        $this->pdf->SetFillColor($this->pdf->blauwDonker[0],$this->pdf->blauwDonker[1],$this->pdf->blauwDonker[2]);
    $this->pdf->Rect(201,66,231-201,94-66,'F'); //30,28 logo vierkant donderblauw
    
   $this->pdf->SetFillColor($this->pdf->blauwLicht[0],$this->pdf->blauwLicht[1],$this->pdf->blauwLicht[2]);
   
   $this->pdf->Rect(231,66,261-231,94-66,'F'); //30,28 logo vierkant lichtblauw
   $this->pdf->Sector(231, 94+3, 30, 90, 180,'F'); //sector
 
   

   $this->pdf->SetFillColor($this->pdf->bruinDonker[0],$this->pdf->bruinDonker[1],$this->pdf->bruinDonker[2]);
   $this->pdf->Rect(31,140,77-31,2,'F');
   $polly= array(231-30,94-4,
   231-6,94-28,
   231,94-28,
   231,94-14,
   231-14,94,
   231,94,
   231,94+14,
   231-28,94+14,
   231-29,94+11,
   231-30,94+5,
   231-30,94-4);
 
   $this->pdf->Polygon($polly,'F');
 
 
   $this->pdf->SetFillColor(255,255,255);
   $polly= array(231+30,94-4,
     231+6,94-28,
     231,94-28,
     231,94-14,
     231+14,94,
     231,94,
     231,94+14,
     231+28,94+14,
     231+29,94+11,
     231+30,94+5,
     231+30,94-4);
 
   $this->pdf->Polygon($polly,'F');
 */
    $breedte=18;
    $this->pdf->memImage($this->pdf->beeldMerk,$this->pdf->w/2-$breedte/2,$this->pdf->h-33,$breedte);
  
    $this->pdf->SetWidths(array(33,140));
    $extraY=-5;
    $this->pdf->setY(75+$extraY);
    $this->pdf->SetFont($this->pdf->rapport_font,'',19);
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->row(array('',"Auréus"));
    $this->pdf->ln(8);
    $this->pdf->row(array('',"Rapportage per ".date("d",$this->rapportageDatumJul)." ".
      vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
      date("Y",$this->rapportageDatumJul)));
  
    $this->pdf->SetFillColor($this->pdf->bruinDonker[0],$this->pdf->bruinDonker[1],$this->pdf->bruinDonker[2]);
    $this->pdf->Rect(41,102+$extraY,42,1.5,'F');
  
  
    $this->pdf->SetAligns(array('L','L','L'));

    $this->pdf->setY(112+$extraY);
    $this->pdf->SetWidths(array(33,140,140));
    $this->pdf->SetFont($this->pdf->rapport_font,'',14);
    if($this->pdf->rapport_clientVermogensbeheerderReal<>'')
      $portefeuille=$this->pdf->rapport_clientVermogensbeheerderReal;
    else
      $portefeuille=$this->portefeuille;
    
    if($this->consolidatie==true)
    {
      if($this->pdf->rapport_clientVermogensbeheerderReal<>'')
        $this->pdf->row(array('', $this->pdf->rapport_clientVermogensbeheerderReal));
      else
        $this->pdf->row(array('', implode(',',$this->pdf->portefeuilles)));//$this->portefeuille.
    }
    else
    {
      $this->pdf->row(array('', $portefeuille));//$this->portefeuille.
    }
    $this->pdf->ln(4);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));//$this->portefeuille.
    $this->pdf->ln(4);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam1']));
    
   // $this->pdf->memImage(base64_decode($img),231-30,94-28,60);
  
    $this->pdf->SetFillColor(255,255,255);
  
    $this->pdf->SetTextColor(0,0,0);
  
    /*
    $this->pdf->line(231-30,94-4,231-6,94-28);
    $this->pdf->line(231-6,94-28,231,94-28);
    $this->pdf->line(231,94-28,231,94-14);
    $this->pdf->line(231,94-14,231-14,94);
    $this->pdf->line(231-14,94,231,94);
    $this->pdf->line(231,94,231,94+14);
    $this->pdf->line(231,94+14,231-28,94+14);
    $this->pdf->line(231-28,94+14, 231-29,94+11);
    $this->pdf->line(231-29,94+11, 231-30,94+5);
    $this->pdf->line(231-30,94+5,231-30,94-4);
  */

  
    


  }


	function writeRapport()
	{
	  global $__appvar;
    if(isset($this->pdf->selectData['type']))
      $this->addBrief();
 //   listarray($this->pdf);
    $this->addFront();
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->frontPage = true;
    $this->pdf->rapport_type = "INHOUD";
    $this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
    $this->pdf->addPage('L');
    $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
	 
	}
}
?>