<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/25 15:34:14 $
 		File Versie					: $Revision: 1.362 $
*/

include_once ('../html/rapport/rapportVertaal.php');

function formatRekeningNummer($nummer)
{
	// format ##.##.##.##.###
  if(substr($nummer,0,1) == 0)
    $nummer = substr($nummer,1);

	$nummer = strrev($nummer);

  $res = $nummer;

	while(strlen($res) >0)
	{
    $t++;
    if($t == 1)
    {
      $newstr = substr($res,0,3);
      $res = substr($res,3);
    }
    else
    {
      $newstr .= ".".substr($res,0,2);
      $res = substr($res,2);
    }
	}

  return strrev($newstr);
}


function loadLayoutSettings($pdf, $portefeuille,$extraAdres=array(),$crmId=0)
{
	global $__appvar,$USR;

	

	// laad layout nummer bij vermogensbeheerder.
  if($portefeuille=='' && $crmId > 0)
  {
    $filter='';
    if($USR <> '')
      $filter="JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder=VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
  	$query = "SELECT Vermogensbeheerders.Layout, ".
           " Vermogensbeheerders.Vermogensbeheerder, ".
					 " Vermogensbeheerders.Logo, ".
					 " Vermogensbeheerders.rapportLink, ".
					 " Vermogensbeheerders.rapportLinkUrl, ".
					 " Vermogensbeheerders.Naam as VermogensbeheerderNaam, ".
           " Vermogensbeheerders.Adres as VermogensbeheerderAdres, ".
           " Vermogensbeheerders.Woonplaats as VermogensbeheerderWoonplaats, ".
					 " Vermogensbeheerders.Telefoon as VermogensbeheerderTelefoon, ".
           " Vermogensbeheerders.Email as VermogensbeheerderEmail, ".
           " Vermogensbeheerders.website as VermogensbeheerderWebsite, ".
					 " Vermogensbeheerders.PerformanceBerekening as PerformanceBerekening, ".
					 " Vermogensbeheerders.txtKoppeling, ".
					 " Vermogensbeheerders.VerouderdeKoersDagen, ".
					 " Vermogensbeheerders.CrmClientNaam ".
					 " FROM Vermogensbeheerders $filter limit 1";
  }
  else
    $query = "SELECT Vermogensbeheerders.Layout, ".
					 " Vermogensbeheerders.Logo, ".
					 " Vermogensbeheerders.rapportLink, ".
					 " Vermogensbeheerders.rapportLinkUrl, ".
					 " Vermogensbeheerders.Naam as VermogensbeheerderNaam, ".
           " Vermogensbeheerders.Adres as VermogensbeheerderAdres, ".
           " Vermogensbeheerders.Woonplaats as VermogensbeheerderWoonplaats, ".
					 " Vermogensbeheerders.Telefoon as VermogensbeheerderTelefoon, ".
           " Vermogensbeheerders.Email as VermogensbeheerderEmail, ".
           " Vermogensbeheerders.website as VermogensbeheerderWebsite, ".
					 " Vermogensbeheerders.PerformanceBerekening as PerformanceBerekening, ".
					 " Vermogensbeheerders.txtKoppeling, ".
					 " Vermogensbeheerders.VerouderdeKoersDagen, ".
					 " Vermogensbeheerders.CrmClientNaam, ".
					 " Portefeuilles.Taal, ".
					 " Clienten.id, ".
					 " Clienten.Naam, ".
					 " Clienten.Naam1, ".
					 " Clienten.Adres, ".
           " Clienten.pc, ".
					 " Clienten.Woonplaats, ".
					 " Clienten.Land, ".
					 " Depotbanken.Omschrijving AS DepotbankOmschrijving, ".
					 " Portefeuilles.Depotbank, ".
           " date(Portefeuilles.Startdatum) as Startdatum, ".
					 " Portefeuilles.AEXVergelijking, ".
					 " Portefeuilles.SpecifiekeIndex, ".
					 " Portefeuilles.Vermogensbeheerder, ".
					 " Portefeuilles.ClientVermogensbeheerder, ".
					 " Portefeuilles.startdatumMeerjarenrendement, ".
					 " Portefeuilles.Risicoklasse, ".
					 " Portefeuilles.Risicoprofiel, ".
					 " Portefeuilles.PortefeuilleVoorzet, ".
					 " Portefeuilles.Portefeuille, ".
					 " Portefeuilles.Client, ".
					 " Portefeuilles.SoortOvereenkomst, ".
					 " Portefeuilles.Accountmanager,
					   Portefeuilles.Remisier,
					   Portefeuilles.RapportageValuta,
					   Accountmanagers.Naam as AccountmanagerNaam,
					   Accountmanagers2.Naam as AccountmanagerNaam2, ".
					 " Portefeuilles.ModelPortefeuille, ".
					 " Portefeuilles.Memo ".
					 " FROM (Vermogensbeheerders, Portefeuilles, Clienten, Depotbanken)
					   LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager=Accountmanagers.Accountmanager
					   LEFT JOIN Accountmanagers as Accountmanagers2 ON Portefeuilles.tweedeAanspreekpunt=Accountmanagers2.Accountmanager ".
					 " WHERE Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
					 " Portefeuilles.Client = Clienten.Client AND ".
					 " Portefeuilles.Depotbank = Depotbanken.Depotbank AND ".
					 " Portefeuilles.Portefeuille = '".$portefeuille."'";           
//echo "$query <br>\n";
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$data = $DB->nextRecord();
	if($data['RapportageValuta']=='')
    $data['RapportageValuta']='EUR';
//listarray($data);
	if($_POST['layout'])
	  $data['Layout']=$_POST['layout'];
	$pdf->portefeuilledata = $data;

	if($data['CrmClientNaam'] == '1' || $crmId > 0)
	{
	  $query = "SELECT
	  CRM_naw.id,
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.verzendAdres,
CRM_naw.verzendPc,
CRM_naw.verzendPlaats,
CRM_naw.verzendLand,
CRM_naw.Adres,
CRM_naw.Pc,
CRM_naw.Plaats,
CRM_naw.Land,
CRM_naw.verzendAanhef,
CRM_naw.verzendPaAanhef,
CRM_naw.ondernemingsvorm,
CRM_naw.titel,
CRM_naw.email,
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
FROM CRM_naw WHERE ";
 if($portefeuille=='' && $crmId > 0)
   $query .= " CRM_naw.id = '$crmId'"; 
 else
   $query .= " Portefeuille = '$portefeuille'";

	  $DB->SQL($query);
	  $CRM_naw = $DB->lookupRecord();

	  $query="SELECT Naam,Naam1,adres,pc,woonplaats FROM historischeTenaamstelling WHERE geldigTot > '".date("Y-m-d",$pdf->rapport_datum)."' AND crmId='".$CRM_naw['id']."' AND crmId <> '' ORDER BY geldigTot desc";
	  $DB->SQL($query);
	  $oudeNaam = $DB->lookupRecord();
	  if($oudeNaam['Naam'] <> '' || $oudeNaam['Naam1'] <> '')
    {
	    $CRM_naw['naam']=$oudeNaam['Naam'];
	    $CRM_naw['naam1']=$oudeNaam['Naam1'];
	  }
    if($oudeNaam['adres'] <> '')
	    $CRM_naw['verzendAdres']=$oudeNaam['adres'];
	  if($oudeNaam['pc'] <> '')
	    $CRM_naw['verzendPc']=$oudeNaam['pc'];
	  if($oudeNaam['woonplaats'] <> '')
	    $CRM_naw['verzendPlaats']=$oudeNaam['woonplaats'];

	  $pdf->portefeuilledata['verzendAanhef']=$CRM_naw['verzendAanhef'];
	  $pdf->portefeuilledata['verzendPaAanhef']=$CRM_naw['verzendPaAanhef'];
	  $pdf->portefeuilledata['crm.naam']   = $CRM_naw['naam'];
		$pdf->portefeuilledata['Naam']       = $CRM_naw['naam'];
	  $pdf->portefeuilledata['Naam1']      = $CRM_naw['naam1'];
	  if(trim($CRM_naw['verzendAdres'].$CRM_naw['verzendPc'].$CRM_naw['verzendPlaats'])=='')
    {
      $pdf->portefeuilledata['Adres'] = $CRM_naw['Adres'];
      $pdf->portefeuilledata['Woonplaats'] = $CRM_naw['Pc'] . " " . $CRM_naw['Plaats'];
      $pdf->portefeuilledata['verzendPc'] = $CRM_naw['Pc'];
      $pdf->portefeuilledata['verzendPlaats'] = $CRM_naw['Plaats'];
      $pdf->portefeuilledata['Land'] = $CRM_naw['Land'];
    }
    else
    {
      $pdf->portefeuilledata['Adres'] = $CRM_naw['verzendAdres'];
      $pdf->portefeuilledata['Woonplaats'] = $CRM_naw['verzendPc'] . " " . $CRM_naw['verzendPlaats'];
      $pdf->portefeuilledata['verzendPc'] = $CRM_naw['verzendPc'];
      $pdf->portefeuilledata['verzendPlaats'] = $CRM_naw['verzendPlaats'];
      $pdf->portefeuilledata['Land'] = $CRM_naw['verzendLand'];
    }
    $pdf->portefeuilledata['email']       = $CRM_naw['email'];
		$pdf->portefeuilledata['ondernemingsvorm']       = $CRM_naw['ondernemingsvorm'];

	    if($pdf->portefeuilledata['Layout'] == 27)
	    {
	      if($CRM_naw['ondernemingsvorm']<>'PART')
	      {
           $pdf->portefeuilledata['Naam'] = $CRM_naw['verzendAanhef'];
           $pdf->portefeuilledata['Naam1'] = '';
	      }
	      else
	      {
	         $pdf->portefeuilledata['Naam'] =       $CRM_naw['titel'];
	         if($CRM_naw['voorletters']!='')        $pdf->portefeuilledata['Naam'] .= " ".$CRM_naw['voorletters'];
	         if($CRM_naw['tussenvoegsel']!='')      $pdf->portefeuilledata['Naam'] .= " ".$CRM_naw['tussenvoegsel'];
	         if($CRM_naw['achternaam']!='')         $pdf->portefeuilledata['Naam'] .= " ".$CRM_naw['achternaam'];
	         if($CRM_naw['achtervoegsel']!='')      $pdf->portefeuilledata['Naam'] .= " ".$CRM_naw['achtervoegsel'];
	         if($CRM_naw['part_voorvoegsel']!='')   $pdf->portefeuilledata['Naam'] .= " ".$CRM_naw['part_voorvoegsel'];
	         if($CRM_naw['enOfRekening'] == 0)
	         {
	           $pdf->portefeuilledata['Naam1']='';
	         }
	         else
	         {
             $pdf->portefeuilledata['Naam1'] =      $CRM_naw['part_titel'];
             if($CRM_naw['part_voorletters']!='')   $pdf->portefeuilledata['Naam1'] .= " ".$CRM_naw['part_voorletters'];
             if($CRM_naw['part_tussenvoegsel']!='') $pdf->portefeuilledata['Naam1'] .= " ".$CRM_naw['part_tussenvoegsel'];
             if($CRM_naw['part_achternaam']!='')    $pdf->portefeuilledata['Naam1'] .= " ".$CRM_naw['part_achternaam'];
             if($CRM_naw['part_achtervoegsel']!='') $pdf->portefeuilledata['Naam1'] .= " ".$CRM_naw['part_achtervoegsel'];
	         }
	      }
	    }

	}
	else
	{
	  $query="SELECT Naam,Naam1,adres,pc,woonplaats  FROM historischeTenaamstelling WHERE geldigTot > '".date("Y-m-d",$pdf->rapport_datum)."' AND clientId='".$pdf->portefeuilledata['id']."' ORDER BY geldigTot desc";
	  $DB->SQL($query);
	  $oudeNaam = $DB->lookupRecord();
	  if($oudeNaam['Naam'] <> '' || $oudeNaam['Naam1'] <> '')
    {
	    $pdf->portefeuilledata['Naam']=$oudeNaam['Naam'];
	    $pdf->portefeuilledata['Naam1']=$oudeNaam['Naam1'];
	  }
    if($oudeNaam['adres'] <> '')
	    $pdf->portefeuilledata['Adres']=$oudeNaam['adres'];
	  if($oudeNaam['pc'] <> '' && $oudeNaam['woonplaats'] <> '')
	    $pdf->portefeuilledata['Woonplaats']=$oudeNaam['pc'].' '.$oudeNaam['woonplaats'];

	}

	if($pdf->lastPOST['anoniem'])
	{
	  $pdf->portefeuilledata['Naam']='Anoniem';
	  if($pdf->portefeuilledata['Naam1'] <> '')
	    $pdf->portefeuilledata['Naam1']='Anoniem';
    $pdf->portefeuilledata['PortefeuilleOrigineel']=$pdf->portefeuilledata['Portefeuille'];  
	  $pdf->portefeuilledata['Portefeuille']='000000';
	  $portefeuille=$pdf->portefeuilledata['Portefeuille'];
	  $pdf->portefeuilledata['Client']='';
	  $pdf->portefeuilledata['Adres']='';
	  $pdf->portefeuilledata['Woonplaats']='';
	  $pdf->portefeuilledata['Land']='';
	  $pdf->portefeuilledata['verzendPaAanhef']='Anoniem';
    if(isset($pdf->__appvar['consolidatie']['portefeuillenaam1']))
    {
      $pdf->__appvar['consolidatie']['portefeuillenaam1']='Anoniem';
      $pdf->__appvar['consolidatie']['portefeuillenaam2']='';
    }
    
	}

	$pdf->rapport_portefeuille 	= $portefeuille;
	$pdf->rapport_portefeuilleVoorzet 	= $pdf->portefeuilledata['PortefeuilleVoorzet'];
	$pdf->rapport_portefeuilleFormat 	= formatRekeningNummer($pdf->portefeuilledata['PortefeuilleVoorzet'].$portefeuille);
	$pdf->rapport_client 				= $pdf->portefeuilledata['Client'];
	$pdf->rapport_clientVermogensbeheerder 				= $pdf->portefeuilledata['Naam'];
	$pdf->rapport_clientVermogensbeheerderReal= $pdf->portefeuilledata['ClientVermogensbeheerder'];
	$pdf->rapport_risicoklasse 	= vertaalTekst($pdf->portefeuilledata['Risicoklasse'],$data['Taal']);
	$pdf->rapport_risicoprofiel 	= vertaalTekst($pdf->portefeuilledata['Risicoprofiel'],$data['Taal']);
	$pdf->rapport_depotbank 		= $pdf->portefeuilledata['Depotbank'];
	$pdf->rapport_depotbankOmschrijving 		= $pdf->portefeuilledata['DepotbankOmschrijving'];
	$pdf->rapport_naam1 				= $pdf->portefeuilledata['Naam'];
	$pdf->rapport_naam2 				= $pdf->portefeuilledata['Naam1'];
	$pdf->rapport_accountmanager		= $pdf->portefeuilledata['Accountmanager'];
	$pdf->rapport_TRANS_decimaal2 = 2;
	$pdf->lineWidth = 0.1;
  $pdf->extraAdres=$extraAdres;
  $pdf->kwartaalFactuurEindKwartaal=false;
 
  if(file_exists($__appvar["basedir"].'/html/rapport/include/layout_'.$data['Layout'].'/rapportage_L'.$data['Layout'].'.php'))
  {
    include($__appvar["basedir"].'/html/rapport/include/layout_'.$data['Layout'].'/rapportage_L'.$data['Layout'].'.php');
  }
  else
  {
    switch ($data['Layout'])
    {
      case "1" :
        // HAR
        $pdf->customPieColors = array(
          "col1" => array(153, 255, 153),    // licht groen
          "col2" => array(255, 255, 153),    // licht geel
          "col3" => array(153, 204, 255),    // licht blauw
          "col4" => array(255, 153, 102),    // licht oranje
          "col5" => array(255, 255, 0), // geel
          "col6" => array(255, 0, 255), // paars
          "col7" => array(128, 128, 128), // grijs
          "col8" => array(128, 64, 64), // bruin
          "col9" => array(255, 255, 255), // wit
          "col0" => array(0, 0, 0) //zwart
        );
        $pdf->rapport_layout = 1;
        $pdf->marge = 8;
        $pdf->rowHeight = 4.5;
        $pdf->lineWidth = 0.4;
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 1;
        $pdf->rapport_OIB_rendementKort = 1;
        $pdf->rapport_OIB_valutaoverzicht = 1;
        $pdf->rapport_OIB_rentebijobligaties = 1;
      
        $pdf->rapport_OIV_rendement = 1;
        $pdf->rapport_OIV_rendementKort = 1;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIH_titel = "Samenstelling portefeuille";
        $pdf->rapport_OIH_rendement = 0;
        $pdf->rapport_OIH_valutaoverzicht = 0;
        $pdf->rapport_OIH_decimaal = 0;
        $pdf->rapport_OIH_onderverdelingAandeel = 1;
        $pdf->rapport_OIH_spaceAfterSector = 1;
        $pdf->rapport_OIH_liquiditeiten_omschr = "{Tenaamstelling} {Valuta}";
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_OIS_titel = "Samenstelling portefeuille";
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_decimaal = 0;
        $pdf->rapport_OIS_onderverdelingAandeel = 1;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_titel = "Samenstelling portefeuille";
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 0;
        $pdf->rapport_OIR_onderverdelingAandeel = 1;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 0;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
        if(!isset($pdf->fonts['verdana']))
        {
          $pdf->AddFont('Verdana');
          $pdf->AddFont('Verdana','B','verdanab.php');
          $pdf->AddFont('verdana','I','verdanai.php');
          $pdf->AddFont('Verdana','BI','verdanaib.php');
        }
  
        $pdf->rapport_font = 'Verdana';
        $pdf->rapport_fontsize = '8.3';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "\n\nR-{Risicoprofiel} / {Depotbank} / {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
        $pdf->rapport_kop2_fontstyle = 'b';
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
      
        //		$pdf->rapport_logo_tekst = "~ Hartfort & Co Asset Management B.V. ~";
        $pdf->rapport_logo_font = "Times";
        $pdf->rapport_logo_fontcolor = array('r' => 0, 'g' => 0, 'b' => 128);
        $pdf->rapport_logo_fontstyle = "";
        $pdf->rapport_logo_fontsize = "12";
      
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "2" :
        // BVS layout
        $pdf->rapport_layout = 2;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " . vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 245, 'g' => 240, 'b' => 155);
        $pdf->rapport_kop_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "3" :
        // TPA layout
        $pdf->rapport_layout = 2;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_procent = 1;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
      
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 245, 'g' => 240, 'b' => 155);
        $pdf->rapport_kop_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "4" :
        // VLC layout
        $pdf->rapport_layout = 4;
      
        $pdf->marge = 8;
        $pdf->rapport_VOLK_procent = 1;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_rentePeriode = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_rentePeriode = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_rentePeriode = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
        $pdf->rapport_OIB_rentePeriode = 1;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
        $pdf->rapport_OIV_rentePeriode = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_rentePeriode = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_rentePeriode = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
      
        $pdf->rapport_OIH_rentePeriode = 1;
      
        $pdf->rapport_PERF_liquiditeiten = 1;
        $pdf->rapport_PERF_jaarRendement = 1;
      
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Depotnummer", $pdf->rapport_taal) . " {PortefeuilleFormat}\n{Naam1}\n{Naam2}";
        $pdf->rapport_consolidatieKoptext = vertaalTekst("Geconsolideerd", $pdf->rapport_taal) . "\n{Naam1}\n{Naam2}";
        $pdf->rapport_rendementText = 'Rendement over verslagperiode';

//			$pdf->rapport_kop_bgcolor = array('r'=>245,'g'=>240,'b'=>155);
        $pdf->rapport_kop_bgcolor = array('r' => 204, 'g' => 51, 'b' => 5);
//			$pdf->rapport_kop_fontcolor = array('r'=>38,'g'=>73,'b'=>156);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);//array('r'=>38,'g'=>73,'b'=>156);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 204, 'g' => 51, 'b' => 5); //array('r'=>38,'g'=>73,'b'=>156);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);//array('r'=>38,'g'=>73,'b'=>156);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);// array('r'=>38,'g'=>73,'b'=>156);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);//array('r'=>38,'g'=>73,'b'=>156);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0); //array('r'=>38,'g'=>73,'b'=>156);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        //$pdf->rapport_logo = $__appvar['basedir']."/html/rapport/logo/".$data[Logo];
        $pdf->rapport_logo_tekst = "Van Lawick & Co.";// "Van Lawick & Co.\nINVESTMENT STRATEGIES";
        $pdf->rapport_logo_font = "Times";
        $pdf->rapport_logo_fontcolor = array('r' => 204, 'g' => 51, 'b' => 5);//array('r'=>0,'g'=>0,'b'=>128);
        $pdf->rapport_logo_fontstyle = "b";
        $pdf->rapport_logo_fontsize = "14";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
      
        $pdf->rapport_logo_tekst2 = "Vermogensbeheer";
        $pdf->rapport_logo_font2 = "Times";
        $pdf->rapport_logo_fontcolor2 = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_logo_fontstyle2 = "";
        $pdf->rapport_logo_fontsize2 = "10";
      
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "5" :
        // GPZ
        $pdf->rapport_layout = 5;
        $pdf->marge = 8;
        $pdf->rapport_dontsortpie = true;
      
        $pdf->rapport_printAEXVergelijkingEur = 1;
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        //$pdf->rapport_VOLK_titel = "Vergelijkend overzicht verslagperiode";
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_geenIndex = 0;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
        $pdf->rapport_HSE_geenIndex = 0;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_valutaoverzicht = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_aantalVierDecimaal = 1;
        $pdf->rapport_OIS_geenIndex = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
        $pdf->rapport_VHO_geenIndex = 1;
      
        $pdf->rapport_PERF_liquiditeiten = 1;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_rendementText = 'Rendement over verslagperiode';
      
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank} / {Risicoklasse}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->rapport_factuurHeader = $__appvar['basedir'] . "/html/rapport/logo/factuurHeader.png";
        $pdf->rapport_factuurFooter = $__appvar['basedir'] . "/html/rapport/logo/factuurFooter.png";
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "6" :
        // MAA
        $pdf->rapport_layout = 5;
        $pdf->marge = 8;
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 0;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 0;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank} / {Risicoklasse}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "7" :
        //WIS
        $pdf->rapport_layout = 7;
        $pdf->marge = 8;
        $pdf->top_marge = 8;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_font = 'Times';
        if (file_exists(FPDF_FONTPATH . 'MyriadPro-R.php'))
        {
          if (!isset($pdf->fonts['myriadpro']))
          {
            $pdf->AddFont('myriadpro', '', 'MyriadPro-R.php');
            $pdf->AddFont('myriadpro', 'B', 'MyriadPro-B.php');
            $pdf->AddFont('myriadpro', 'I', 'MyriadPro-I.php');
            $pdf->AddFont('myriadpro', 'BI', 'MyriadPro-BI.php');
          }
          $pdf->rapport_font = 'myriadpro';
        }
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_TRANS_disclaimerText = "
Hoewel door Wilton Investment Services B.V. de grootst mogelijke zorgvuldigheid is betracht bij het samenstellen van de inhoud van deze rapportage, kan niet worden ingestaan voor de juistheid en volledigheid van deze informatie.
U kunt geen rechten ontlenen aan de inhoud van deze rapportage.
Bij een constatering van een onjuistheid of onvolledigheid in deze rapportage, verzoeken wij u vriendelijk om binnen 30 dagen na verzending dit schriftelijk aan ons kenbaar te maken.
Na deze termijn wordt de client geacht Wilton Investment Services B.V. te hebben gedechargeerd terzake van de advisering door Wilton Investment Services B.V. gedurende de betreffende periode.
De inhoud van deze rapportage is strikt persoonlijk en vertrouwelijk. Het heeft uitsluitend een informatieve functie en mag niet gelezen worden als een beleggingsadvies.
Aan alle vormen van beleggen zijn risico’s verbonden. De risico’s zijn afhankelijk van de belegging. Een belegging kan in meer of mindere mate risicodragend zijn.
Meestal geldt dat een belegging met een hoger verwacht rendement grotere risico’s met zich brengt.
Zeker bij het beleggen in buitenlandse effecten kan de overheidspolitiek in het desbetreffende land gevolgen hebben voor de waarde van de belegging.
Daarnaast dient bij het beleggen in buitenlandse effecten rekening te worden gehouden met het valutasrisico.
Voor meer informatie vraag naar: KENMERKEN VAN EFFECTEN EN DAARAAN VERBONDEN SPECIFIEKE RISICO’S van Wilton Investment Services B.V.
Wilton Investment Services is opgenomen in het register van de Autoriteit Financiële Markten in het kader van de Wet Financieel Toezicht ( WFT )
Zie voor meer informatie www.afm.nl.
Algemene voorwaarden die van toepassing zijn op alle diensten die Wilton Investment Services B.V. verleent zijn op  te vragen bij Wilton Investment Services B.V., Postbus 4667 4803 ER Breda, Nederland
";
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
      
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = '';//' vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = '';//vertaalTekst("Productiedatum ",$pdf->rapport_taal).date("d",mktime())." ".vertaalTekst($pdf->__appvar["Maanden"][date("n",mktime())],$pdf->rapport_taal)." ".date("Y",mktime());
        $pdf->rapport_koptext = "{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1} {Naam2}\n";
        $pdf->rapport_consolidatieKoptext = vertaalTekst("Geconsolideerd", $pdf->rapport_taal) . "\n{Naam1} {Naam2}\n";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, 20);
        break;
      case "8" :
        // attica layout
      
        $pdf->rapport_riscoPerfonds = 1;
      
        $pdf->rapport_layout = 8;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 0;
        $pdf->rapport_VOLK_geensubtotaal = 1;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_geenvaluta = 1;
        $pdf->rapport_VOLK_rendement = 2;
        $pdf->rapport_VOLK_valutaoverzicht = 0;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VOLKD_volgorde_beginwaarde = 0;
        $pdf->rapport_VOLKD_geensubtotaal = 1;
        $pdf->rapport_VOLKD_decimaal = 0;
        $pdf->rapport_VOLKD_decimaal_proc = 1;
        $pdf->rapport_VOLKD_geenvaluta = 1;
        $pdf->rapport_VOLKD_rendement = 1;
        $pdf->rapport_VOLKD_valutaoverzicht = 1;
      
        $pdf->rapport_VHO_geenvaluta = 1;
        $pdf->rapport_VHO_geensubtotaal = 1;
        $pdf->rapport_VHO_volgorde_beginwaarde = 0;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 0;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_rendement = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 0;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 1;
        $pdf->rapport_OIB_valutaoverzicht = 1;
      
        $pdf->rapport_OIV_rendement = 1;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
        $pdf->rapport_OIV_valutaoverzicht = 0;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_titel = "Onderverdeling naar regio";
        $pdf->rapport_OIS_decimaal = 2;
      
        $pdf->rapport_OIR_titel = "Onderverdeling in Regio";
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_PERF_koptext = 1;
        $pdf->rapport_PERF_titel = 'Performancemeting';
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_MUT_kwartaal = 1;
        $pdf->rapport_TRANS_kwartaal = 1;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 0;
      
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '7';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal) . ". " .
          vertaalTekst("sCOREvalue is een handelsnaam van Attica Vermogensbeheer B.V.", $pdf->rapport_taal);
      
        $pdf->rapport_koptext = vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} - " .
          vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleFormat} - {Risicoklasse} - {Rapportagedatum}";
        $pdf->rapport_naamtext = "{Naam1}";
      
        $pdf->skipRestoreColor = true; //draw kleur setten in header mogelijk;
        $pdf->rapport_kop_bgcolor = array('r' => 245, 'g' => 245, 'b' => 245);
        $pdf->rapport_kop_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
        $pdf->rapport_kop_fontstyle = 'B';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
      
        $pdf->rapport_kop3_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
        $pdf->rapport_kop4_fontstyle = 'b';
      
      
        $pdf->rapport_default_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
        $pdf->rapport_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
      
      
        $pdf->rapport_fonds_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
        $pdf->rapport_fondsVerdiept_fontcolor = array('r' => 120, 'g' => 120, 'b' => 120);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {Valuta}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
      
      
        if ($pdf->rapport_datumvanaf < db2jul('2008-01-01'))
        {
          $pdf->portefeuilledata['PerformanceBerekening'] = 1;
        }
      
        break;
      case "9" :
        // MJM layout ?
        $pdf->rapport_layout = 2;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{ClientVermogensbeheerder} \n\n{Naam1} {PortefeuilleVoorzet}{Portefeuille}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 245, 'g' => 240, 'b' => 155);
        $pdf->rapport_kop_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "10" :
        // BCS
        $pdf->rapport_layout = 10;
        $pdf->marge = 8;
        $pdf->rapport_dontsortpie = true;
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_aantal_decimaal = 4;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_aantalVierDecimaal=true;
        //$pdf->rapport_VOLK_titel = "Vergelijkend overzicht verslagperiode";
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_valutaoverzicht = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
/*
        if(!isset($pdf->fonts['opensans']) && file_exists(FPDF_FONTPATH.'OpenSans-Regular.php'))
        {
          $pdf->AddFont('opensans','','OpenSans-Regular.php');
          $pdf->AddFont('opensans','B','OpenSans-Bold.php');
          $pdf->AddFont('opensans','I','OpenSans-Italic.php');
          $pdf->AddFont('opensans','BI','OpenSans-BoldItalic.php');
          $pdf->rapport_font = 'opensans';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
  */
        if (file_exists(FPDF_FONTPATH . 'DINOT-Regular.php'))
        {
          if (!isset($pdf->fonts['dinot']))
          {
            $pdf->AddFont('dinot', '', 'DINPro-Light.php');
          //  $pdf->AddFont('dinot', '', 'DINOT-Regular.php');
            $pdf->AddFont('dinot', 'B', 'DINOT-Bold.php');
            $pdf->AddFont('dinot', 'I', 'DINOT-RegularItalic.php');
            $pdf->AddFont('dinot', 'BI', 'DINOT-BoldItalic.php');
          }
          $pdf->rapport_font = 'dinot';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
        
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank} / {Risicoklasse}\n{Naam1}\n{Naam2}";
   
        $pdf->rapport_kop_bgcolor = array('r' => 35, 'g' => 35, 'b' => 35);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_background_fill = array(215,185,153);//198.156.109
        $pdf->rapport_logoKleur = array(58, 60, 57);
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
  
        $pdf->rapport_grafiek_pcolor=array(106,100,122);//array(rand(0,255),rand(0,255),rand(0,255));
        $pdf->rapport_grafiek_icolor=array(176,160,122);//array(rand(0,255),rand(0,255),rand(0,255));
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "11" :
        // HEK
        $pdf->rapport_riscoPerfonds = 1;
      
        $pdf->rapport_layout = 11;
        $pdf->marge = 8;
        $pdf->rapport_dontsortpie = true;
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        //$pdf->rapport_VOLK_titel = "Vergelijkend overzicht verslagperiode";
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_valutaoverzicht = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1; //toevoeging 02-10-06
        $pdf->rapport_OIS_risico = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
      
        if ($pdf->portefeuilledata['ModelPortefeuille'] != '')
        {
          $pdf->portefeuilledata['ModelPortefeuille'] = '/ ' . $pdf->portefeuilledata['ModelPortefeuille'];
        }
      
        $pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank} / {Accountmanager} / {Risicoklasse} {ModelPortefeuille}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
    
      case "12" :
        // nieuwe layout
        $pdf->lineWidth = 0.2;
        $pdf->rapport_layout = 12;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_valutaoverzicht = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_rentebijobligaties = 1;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 0;
        $pdf->rapport_OIS_rendement = 0;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      /*
        if (file_exists(FPDF_FONTPATH . 'calibri.php'))
        {
          if (!isset($pdf->fonts['calibri']))
          {
            $pdf->AddFont('calibri', '', 'calibri.php');
            $pdf->AddFont('calibri', 'B', 'calibrib.php');
            $pdf->AddFont('calibri', 'I', 'calibrii.php');
            $pdf->AddFont('calibri', 'BI', 'calibribi.php');
          }
          $pdf->rapport_font = 'calibri';
        }
      */
        
                  if(file_exists(FPDF_FONTPATH.'HelveticaNeue.php'))
                  {
                    if(!isset($pdf->fonts['helveticaneue']))
                    {
                      $pdf->AddFont('HelveticaNeue','','HelveticaNeue.php');
                      $pdf->AddFont('HelveticaNeue','B','HelveticaNeueb.php');
                      $pdf->AddFont('HelveticaNeue','I','HelveticaNeuei.php');
                      $pdf->AddFont('HelveticaNeue','BI','HelveticaNeuebi.php');
                    }
                    $pdf->rapport_font = 'HelveticaNeue';
                  }
        $pdf->rapport_fontEur = 'times';
        //$pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '8';
        //
  
        //
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '7';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
//        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum: ", $pdf->rapport_taal) . date("d") . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n")], $pdf->rapport_taal) . " " . date("Y").
//         " / ".vertaalTekst("Rapportagedatum:",$pdf->rapport_taal)." ".date("j",$pdf->rapport_datum)." ".vertaalTekst($pdf->__appvar["Maanden"][date("n",$pdf->rapport_datum)],$pdf->rapport_taal)." ".date("Y",$pdf->rapport_datum);
      
//        $pdf->rapport_koptext = "{Naam1} {Naam2}\n{SoortOvereenkomst}\n{Risicoklasse}";
//oude voet en kop.
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum: ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n{Naam1}{Naam2}\n{SoortOvereenkomst}\n{Risicoklasse}";
  
  
        $pdf->rapport_consolidatieKoptext = "{Naam1} {Naam2}\nGeconsolideerd";
        $pdf->rapport_rendementText = 'Rendement over verslagperiode';
        $pdf->rapport_perfIndexJanuari = true;
        $pdf->rapport_valutaPerformanceJanuari = true;
  
        $pdf->rapport_kop_kleur = array(0,45,71);
        $pdf->rapport_donker_kleur = array(128,150,171);
        $pdf->rapport_licht_kleur = array(194,207,218);
        $pdf->rapport_grijs_kleur = array(194,207,218);
        
        $pdf->rapport_kop_bgcolor = array('r' => 199, 'g' => 202, 'b' => 219);
        $pdf->rapport_kop_fontcolor = array('r' => $pdf->rapport_kop_kleur[0], 'g' => $pdf->rapport_kop_kleur[1], 'b' => $pdf->rapport_kop_kleur[2]);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = $pdf->rapport_kop_fontcolor;
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = $pdf->rapport_kop_fontcolor;
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_kop4_fontcolor = $pdf->rapport_kop_fontcolor;
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_header_fontcolor = array('r' => 134, 'g' => 154, 'b' => 172); //...
        $pdf->rapport_totaalLijnenColor=array(206,215,222);
        
      
        $pdf->rapport_fonds_fontcolor = $pdf->rapport_default_fontcolor;
        $pdf->rapport_fontcolor = $pdf->rapport_default_fontcolor;
      
        $pdf->rapport_subtotaal_omschr_fontcolor = $pdf->rapport_kop_fontcolor;
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = $pdf->rapport_kop_fontcolor;
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = $pdf->rapport_kop_fontcolor;
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = $pdf->rapport_kop_fontcolor;
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
    
      case "13" :
        // RCN
        $pdf->rapport_layout = 13;
        $pdf->marge = 8;
        $pdf->rapport_dontsortpie = true;
        //$pdf->forceOneRow = true;
        if ($data['Vermogensbeheerder'] == 'RCN')
        {
          $pdf->rapportToonRente = false;
        }
        else
        {
          $pdf->rapportToonRente = true;
        }
      
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        //$pdf->rapport_VOLK_titel = "Vergelijkend overzicht verslagperiode";
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_valutaoverzicht = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
        $pdf->rapport_VHO_lijnenKorter = 5;

        $pdf->rapport_MUT2_decimaal = 2;

        $pdf->rapport_ATT_decimaal = 0;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rowHeight = 3.25;
        $pdf->rapport_fontsize = '8';
        $pdf->rapport_fontstyle = 'B';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank} / {Risicoklasse}\n{Naam1}\n{Naam2}";
        /*
			if(file_exists(FPDF_FONTPATH.'crystal--.php'))
		  {
  	    if(!isset($pdf->fonts['crystal']))
	      {
		      $pdf->AddFont('crystal','','crystal.php');
		      $pdf->AddFont('crystal','B','crystal.php');
		      $pdf->AddFont('crystal','I','crystal.php');
		      $pdf->AddFont('crystal','BI','crystal.php');
	      }
			  $pdf->rapport_font = 'crystal';
		  }
			else

			if(file_exists(FPDF_FONTPATH.'letterGothic.php'))
		  {
  	    if(!isset($pdf->fonts['lettergothic']))
	      {
	        $pdf->AddFont('letterGothic','','letterGothic.php');
	        $pdf->AddFont('letterGothic','B','letterGothicB.php');
	        $pdf->AddFont('letterGothic','I','letterGothicI.php');
	        $pdf->AddFont('letterGothic','BI','letterGothicI.php');

		  //    $pdf->AddFont('letterGothic','','LG.php');
		  //    $pdf->AddFont('letterGothic','B','LGb.php');
		  //    $pdf->AddFont('letterGothic','I','LGi.php');
		  //    $pdf->AddFont('letterGothic','BI','LGi.php');
	      }
			  $pdf->rapport_font = 'letterGothic';
		  }
*/
        /*
      if(file_exists(FPDF_FONTPATH.'orator.php'))
		  {
  	    if(!isset($pdf->fonts['orator']))
	      {
		      $pdf->AddFont('orator','','orator.php');
		      $pdf->AddFont('orator','B','orator.php');
		      $pdf->AddFont('orator','I','orator.php');
		      $pdf->AddFont('orator','BI','orator.php');
	      }
			  $pdf->rapport_font = 'orator';
		  }
*/
        /*
      if(file_exists(FPDF_FONTPATH.'OCRB.php'))
		  {
  	    if(!isset($pdf->fonts['ocrb']))
	      {
		      $pdf->AddFont('ocrb','','OCRB.php');
		      $pdf->AddFont('ocrb','B','OCRB.php');
		      $pdf->AddFont('ocrb','I','OCRB.php');
		      $pdf->AddFont('ocrb','BI','OCRB.php');
	      }
			  $pdf->rapport_font = 'ocrb';
		  }
		  else
  			$pdf->rapport_font = 'courier';
      */
      
        $pdf->rapport_fontsize = '10';
        $pdf->rapport_fontstyle = '';
        if (file_exists(FPDF_FONTPATH . 'calibri.php'))
        {
          if (!isset($pdf->fonts['calibri']))
          {
            $pdf->AddFont('calibri', '', 'calibri.php');
            $pdf->AddFont('calibri', 'B', 'calibrib.php');
            $pdf->AddFont('calibri', 'I', 'calibrii.php');
            $pdf->AddFont('calibri', 'BI', 'calibribi.php');
          }
          $pdf->rapport_font = 'calibri';
        }
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_bgcolor2 = array('r' => 39, 'g' => 62, 'b' => 102);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';

        $pdf->rapport_grafiek_color = array(39, 62, 102);
        $pdf->rapport_row_bg = array(255,255,255);

        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->rapport_factuurHeader = $__appvar['basedir'] . "/html/rapport/logo/factuurHeader.png";
        $pdf->rapport_factuurFooter = $__appvar['basedir'] . "/html/rapport/logo/factuurFooter.png";
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
    
      case "14" :
        //AFS
        $pdf->rapport_layout = 14;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
        $pdf->printAEXVergelijkingProcentTeken = 1;
        $pdf->printValutaPerformanceOverzichtProcentTeken = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_aantalVierDecimaal = true;
      
        //	$pdf->rapport_VHO_titel = "Huidige samenstelling effectenportefeuille";
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_lijnenKorter = 5;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Onderverdeling in beleggingscategorie";
        $pdf->rapport_OIB_specificatie = 0;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 0;
        $pdf->rapport_TRANS_decimaal = 2;
        $pdf->rapport_TRANS_decimaal2 = 2;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
        $pdf->rapport_PERF_rendement = 1;
        $pdf->rapport_PERF_portefeuilleIndex = 0;
        $pdf->rapport_PERF_lijnenKorter = 10;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_perfIndexJanuari = true;
        $pdf->rapport_valutaPerformanceJanuari = true;
        $pdf->printValutaPerformanceOverzichtProcentTeken = true;
      
        $pdf->rapport_rendementText = "Rendement over verslagperiode";
      
        $pdf->rapport_font = 'Times';
        if (file_exists(FPDF_FONTPATH . 'calibri.php'))
        {
          if (!isset($pdf->fonts['calibri']))
          {
            $pdf->AddFont('calibri', '', 'calibri.php');
            $pdf->AddFont('calibri', 'B', 'calibrib.php');
            $pdf->AddFont('calibri', 'I', 'calibrii.php');
            $pdf->AddFont('calibri', 'BI', 'calibribi.php');
          }
          $pdf->rapport_font = 'calibri';
        }
        //$pdf->rapport_font = 'verdana';
      
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        //$pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend. Bedragen worden afgerond op twee decimalen. Hierdoor kunnen minimale afrondingsverschillen ontstaan.", $pdf->rapport_taal);
      
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n{VermogensbeheerderNaam} / " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\nProfiel: {SoortOvereenkomst} {Risicoklasse}\n{Naam1}";//\n{Naam2}
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
    
      case "15" :
        //VJV
        $pdf->rapport_layout = 15;
        $pdf->marge = 20;
        $pdf->top_marge = 25;
      
        $pdf->rapport_valutaoverzicht_rev = 1;

//			$pdf->rapport_VHO_titel = "Huidige samenstelling effectenportefeuille";
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_lijnenKorter = 5;
      
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
//			$pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
//			$pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ",$pdf->rapport_taal).date("d",mktime())." ".vertaalTekst($pdf->__appvar["Maanden"][date("n",mktime())],$pdf->rapport_taal)." ".date("Y",mktime());
//			$pdf->rapport_koptext  = "\n{VermogensbeheerderNaam} / ".vertaalTekst("Depot",$pdf->rapport_taal)." {PortefeuilleVoorzet}{Portefeuille}\nProfiel: {SoortOvereenkomst} {Risicoklasse}\n{Naam1}";//\n{Naam2}
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 128);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 128);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_afbeelding = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
    
      case "16" :
        //KEI
        $pdf->rapport_layout = 16;
        $pdf->marge = 8;
        //	$pdf->top_marge = 25;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_titel = "Vermogensoverzicht";
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_lijnenKorter = 5;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Onderverdeling in beleggingscategorie";
        $pdf->rapport_OIB_specificatie = 0;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 0;
        $pdf->rapport_TRANS_decimaal = 2;
        $pdf->rapport_TRANS_decimaal2 = 2;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
        $pdf->rapport_PERF_rendement = 1;
        $pdf->rapport_PERF_portefeuilleIndex = 0;
        $pdf->rapport_PERF_lijnenKorter = 10;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n{VermogensbeheerderNaam} / " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\nProfiel: {SoortOvereenkomst} {Risicoklasse}\n{Naam1}";//\n{Naam2}
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "17" :
        // HEK TEST
        $pdf->rapport_riscoPerfonds = 1;
      
        $pdf->rapport_layout = 17;
        $pdf->marge = 8;
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        //$pdf->rapport_VOLK_titel = "Vergelijkend overzicht verslagperiode";
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_valutaoverzicht = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIH_valutaoverzicht = 1;
        $pdf->rapport_OIH_rendement = 0;
        $pdf->rapport_OIH_decimaal = 2;
        $pdf->rapport_OIH_geenrentespec = 1; //toevoeging 02-10-06
        $pdf->rapport_OIH_risico = 0;
        $pdf->rapport_OIH_titel = "Portefeuille overzicht";
      
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1; //toevoeging 02-10-06
        $pdf->rapport_OIS_risico = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
      
        $pdf->rapport_PERF_displayType = 0;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
      
        if (file_exists(FPDF_FONTPATH . 'Frutiger.php'))
        {
          if (!isset($pdf->fonts['frutiger']))
          {
            $pdf->AddFont('frutiger', '', 'Frutigerl.php');
            $pdf->AddFont('frutiger', 'B', 'Frutigerb.php');
            $pdf->AddFont('frutiger', 'R', 'Frutiger.php');
            $pdf->AddFont('frutiger', 'BI', 'Frutigerbi.php');
          }
          $pdf->rapport_font = 'frutiger';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
      
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = "\n" . vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
      
        if ($pdf->portefeuilledata['ModelPortefeuille'] != '')
        {
          $pdf->portefeuilledata['ModelPortefeuille'] = '/ ' . $pdf->portefeuilledata['ModelPortefeuille'];
        }
      
        $pdf->rapport_koptext_old = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank} / {Accountmanager} / {Risicoklasse} {ModelPortefeuille}\n{Naam1}\n{Naam2}";
        $pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255); //wit
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0); //zwart
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_style[1]['bgcolor'] = array('r' => 27, 'g' => 74, 'b' => 20); //donker groen
        $pdf->rapport_style[1]['fontcolor'] = array('r' => 225, 'g' => 202, 'b' => 1); //geel
        $pdf->rapport_style[1]['font'] = array('style' => '', 'fontSize' => 11);
        $pdf->rapport_style[1]['rowHeight'] = 8;
      
        $pdf->rapport_style['rapportKop']['bgcolor'] = array('r' => 27, 'g' => 74, 'b' => 20); //donker groen
        $pdf->rapport_style['rapportKop']['fontcolor'] = array('r' => 225, 'g' => 202, 'b' => 1); //geel
        $pdf->rapport_style['rapportKop']['font'] = array('style' => '', 'fontSize' => 8);
        $pdf->rapport_style['rapportKop']['rowHeight'] = 4;
      
        $pdf->rapport_style[2]['bgcolor'] = array('r' => 225, 'g' => 202, 'b' => 1);//geel
        $pdf->rapport_style[2]['fontcolor'] = array('r' => 27, 'g' => 74, 'b' => 20);//donker groen
        $pdf->rapport_style[2]['font'] = array('style' => '', 'fontSize' => 8);
        $pdf->rapport_style[2]['rowHeight'] = 4;
      
        $pdf->rapport_style[3]['bgcolor'] = array('r' => 255, 'g' => 255, 'b' => 255);//wit
        $pdf->rapport_style[3]['fontcolor'] = array('r' => 0, 'g' => 139, 'b' => 161);//blauw
        $pdf->rapport_style[3]['font'] = array('style' => '', 'fontSize' => 12);
        $pdf->rapport_style[3]['rowHeight'] = 8;
      
        $pdf->rapport_style[4]['bgcolor'] = array('r' => 255, 'g' => 255, 'b' => 255);//wit
        $pdf->rapport_style[4]['fontcolor'] = array('r' => 0, 'g' => 139, 'b' => 161);//blauw
        $pdf->rapport_style[4]['font'] = array('style' => '', 'fontSize' => 8);
        $pdf->rapport_style[4]['line'] = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 139, 161));
        $pdf->rapport_style[4]['rowHeight'] = 5;
      
        $pdf->rapport_style['fonds']['bgcolor'] = array();//blank
        $pdf->rapport_style['fonds']['fontcolor'] = array('r' => 0, 'g' => 0, 'b' => 0);//zwart
        $pdf->rapport_style['fonds']['font'] = array('style' => '', 'fontSize' => 8);
        $pdf->rapport_style['fonds']['rowHeight'] = 4.5;
        $pdf->rapport_style['fonds']['line'] = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(225, 202, 1));
      
        $pdf->rapport_style['totaal']['bgcolor'] = array('r' => 146, 'g' => 163, 'b' => 133);//licht groen
        $pdf->rapport_style['totaal']['fontcolor'] = array('r' => 255, 'g' => 255, 'b' => 255);//wit
        $pdf->rapport_style['totaal']['font'] = array('style' => '', 'fontSize' => 8);
        $pdf->rapport_style['totaal']['rowHeight'] = 4;
      
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_bgcolor = array('r' => 146, 'g' => 163, 'b' => 133);//licht groen
        $pdf->rapport_subtotaal_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);//wit
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, 20);
        break;
      case "18" :
        //VIP
        $pdf->rapport_layout = 18;
        $pdf->marge = 5;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_titel = "Vermogensoverzicht";
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_lijnenKorter = 5;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Vermogensverdeling";
        $pdf->rapport_OIB_specificatie = 0;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 1;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 0;
        $pdf->rapport_TRANS_decimaal = 2;
        $pdf->rapport_TRANS_decimaal2 = 2;
      
      
        $pdf->rapport_PERF_displayType = 1;
        $pdf->rapport_PERF_rendement = 1;
        $pdf->rapport_PERF_portefeuilleIndex = 0;
        $pdf->rapport_PERF_lijnenKorter = 10;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '12';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 100, 'g' => 100, 'b' => 100);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop2_fontcolor = array('r' => 75, 'g' => 75, 'b' => 75);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_voet_bgcolor = array(136, 62, 37); //rood
      
      
        $pdf->rapport_style['koptekstRechts']['font'] = array('style' => '', 'fontSize' => 12);
        $pdf->rapport_style['koptekstRechts']['rowHeight'] = 8;
      
        $pdf->rapport_style['fonds']['bgcolor'] = array();//blank
        $pdf->rapport_style['fonds']['fontcolor'] = array('r' => 109, 'g' => 108, 'b' => 88);//grijs
        $pdf->rapport_style['fonds']['font'] = array('style' => '', 'fontSize' => 12);

//new 		$pdf->rapport_style['fonds']['rowHeight'] = 6;
        $pdf->rapport_style['fonds']['rowHeight'] = 8;
//new			$pdf->rapport_style['fonds']['line'] = array('width' => .3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(135+80,135+80,120+80));
        $pdf->rapport_style['fonds']['line'] = array('width' => .3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(135, 135, 120));
      
        $pdf->rapport_style['fondsLaag'] = $pdf->rapport_style['fonds'];
        $pdf->rapport_style['fondsLaag']['rowHeight'] = 6;
      
        $pdf->rapport_style['rodelijn']['rowHeight'] = 8;
        $pdf->rapport_style['rodelijn']['line'] = array('width' => .3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(136, 62, 37));
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 100, 'g' => 100, 'b' => 100);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 100, 'g' => 100, 'b' => 100);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin(13);
        $pdf->SetTopMargin(13);
        $pdf->SetAutoPageBreak(true, 15);
        break;
      case "19" :
        // KYA layout
      
        $pdf->rapport_riscoPerfonds = 1;
      
        $pdf->rapport_layout = 19;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 0;
        $pdf->rapport_VOLK_geensubtotaal = 1;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_geenvaluta = 1;
        $pdf->rapport_VOLK_rendement = 2;
        $pdf->rapport_VOLK_valutaoverzicht = 0;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VOLKD_volgorde_beginwaarde = 0;
        $pdf->rapport_VOLKD_geensubtotaal = 1;
        $pdf->rapport_VOLKD_decimaal = 0;
        $pdf->rapport_VOLKD_decimaal_proc = 1;
        $pdf->rapport_VOLKD_geenvaluta = 1;
        $pdf->rapport_VOLKD_rendement = 1;
        $pdf->rapport_VOLKD_valutaoverzicht = 1;
      
        $pdf->rapport_VHO_geenvaluta = 1;
        $pdf->rapport_VHO_geensubtotaal = 1;
        $pdf->rapport_VHO_volgorde_beginwaarde = 0;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 0;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 0;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 1;
        $pdf->rapport_OIB_valutaoverzicht = 1;
      
        $pdf->rapport_OIV_rendement = 1;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
        $pdf->rapport_OIV_valutaoverzicht = 0;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_titel = "Onderverdeling naar regio";
        $pdf->rapport_OIS_decimaal = 2;
      
        $pdf->rapport_OIR_titel = "Onderverdeling in Regio";
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_PERF_koptext = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_MUT_kwartaal = 1;
        $pdf->rapport_TRANS_kwartaal = 1;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 0;
        if(!isset($pdf->fonts['verdana']))
        {
          $pdf->AddFont('Verdana');
          $pdf->AddFont('Verdana','B','verdanab.php');
          $pdf->AddFont('verdana','I','verdanai.php');
          $pdf->AddFont('Verdana','BI','verdanaib.php');
        }
  
        $pdf->rapport_font = 'Verdana';
        $pdf->rapport_fontsize = '7';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_consolidatieKoptext = "\n \n{Naam1} {Naam2}\nGeconsolideerd {Portefeuille}";
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "Portefeuille Overzicht\n" . vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " .
          vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleFormat} / {Risicoklasse}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 204, 'b' => 0);//array('r'=>102,'g'=>204,'b'=>51);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 75, 'g' => 75, 'b' => 75);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_kop4_fontcolor = array(0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 75, 'g' => 75, 'b' => 75);
        $pdf->rapport_fondsVerdiept_fontcolor = array('r' => 120, 'g' => 120, 'b' => 120);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 75, 'g' => 75, 'b' => 75);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 75, 'g' => 75, 'b' => 75);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {Valuta}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "20" :
        // WWO
        $pdf->rapport_layout = 20;
        $pdf->marge = 8;
        $pdf->rapport_dontsortpie = true;
      
        $pdf->rapport_printAEXVergelijkingEur = 1;
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        //$pdf->rapport_VOLK_titel = "Vergelijkend overzicht verslagperiode";
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_geenIndex = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
        $pdf->rapport_HSE_geenIndex = 0;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_valutaoverzicht = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_aantalVierDecimaal = 1;
        $pdf->rapport_OIS_geenIndex = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
        $pdf->rapport_VHO_geenIndex = 1;
      
        $pdf->rapport_PERF_liquiditeiten = 1;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
      
        if (file_exists(FPDF_FONTPATH . 'Frutiger.php'))
        {
          if (!isset($pdf->fonts['frutiger']))
          {
            $pdf->AddFont('frutiger', '', 'Frutigerl.php');
            $pdf->AddFont('frutiger', 'B', 'Frutigerb.php');
            $pdf->AddFont('frutiger', 'R', 'Frutiger.php');
            $pdf->AddFont('frutiger', 'BI', 'Frutigerbi.php');
            $pdf->AddFont('frutiger', 'I', 'Frutigerbi.php');
          }
          $pdf->rapport_font = 'frutiger';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
      
      
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend. Productiedatum: ", $pdf->rapport_taal) . date("d-m-Y");
      
      
        $pdf->rapport_koptext = "{crm.naam}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = 'B';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = 'B';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->rapport_factuurHeader = $__appvar['basedir'] . "/html/rapport/logo/factuurHeader.png";
        $pdf->rapport_factuurFooter = $__appvar['basedir'] . "/html/rapport/logo/factuurFooter.png";
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "21" :
        // I4Y
        $pdf->rapport_layout = 21;
        $pdf->marge = 8;
        $pdf->rapport_dontsortpie = true;
      
        $pdf->rapport_printAEXVergelijkingEur = 0;
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        //$pdf->rapport_VOLK_titel = "Vergelijkend overzicht verslagperiode";
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        //$pdf->rapport_VOLK_geenIndex = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
        $pdf->rapport_HSE_geenIndex = 0;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_valutaoverzicht = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_aantalVierDecimaal = 1;
        $pdf->rapport_OIS_geenIndex = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
        $pdf->rapport_VHO_geenIndex = 1;
      
        $pdf->rapport_PERF_liquiditeiten = 1;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_rendementText = 'Rendement over verslagperiode';
      
        $pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank} / {Risicoklasse}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->rapport_factuurHeader = $__appvar['basedir'] . "/html/rapport/logo/factuurHeader.png";
        $pdf->rapport_factuurFooter = $__appvar['basedir'] . "/html/rapport/logo/factuurFooter.png";
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "22" :
        //SEQ
        $pdf->rapport_layout = 22;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
        $pdf->rapport_PERF_jaarRendement = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
        /*
			if(file_exists(FPDF_FONTPATH.'tahoma.php'))
		  {
  	    if(!isset($pdf->fonts['tahoma']))
	      {
		      $pdf->AddFont('tahoma','','tahoma.php');
		      $pdf->AddFont('tahoma','B','tahomab.php');
		      $pdf->AddFont('tahoma','I','tahoma.php');
		      $pdf->AddFont('tahoma','BI','tahomab.php');
	      }
			  $pdf->rapport_font = 'tahoma';
		  }
		  else
		  */
        $pdf->rapport_font = 'Times';
        /*
			if(file_exists(FPDF_FONTPATH.'calibri.php'))
		  {
  	    if(!isset($pdf->fonts['calibri']))
	      {
		      $pdf->AddFont('calibri','','calibri.php');
		      $pdf->AddFont('calibri','B','calibrib.php');
		      $pdf->AddFont('calibri','I','calibrii.php');
		      $pdf->AddFont('calibri','BI','calibribi.php');
	      }
			  $pdf->rapport_font = 'calibri';
		  }
*/
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 69, 'b' => 132);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 69, 'b' => 132);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        if (file_exists($__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png"))
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png";
          /*
			  if($data['Remisier']=='topcapital')
			  {
			    $pdf->rapport_kop2_fontcolor = array('r'=>236,'g'=>0,'b'=>140);
			    $pdf->rapport_kop_fontcolor = array('r'=>236,'g'=>0,'b'=>140);
			  }
			  */
        }
        else
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        }
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "23" :
        $pdf->rapport_layout = 23;
        $pdf->marge = 8;
        $pdf->top_marge = 15;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geenvaluta = 1;
        $pdf->rapport_VOLK_geensubtotaal = 1;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_geenrentespec = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "24" :
        //PWM
        $pdf->rapport_layout = 24;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = false;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "25" :
        //AUR
        $pdf->rapport_layout = 25;
        $pdf->marge = 6;
        $pdf->top_marge = 15;
        $pdf->rapport_dontsortpie = true;
        $pdf->kwartaalFactuurEindKwartaal=true;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
        $pdf->rapport_resultaatText = "Resultaat lopend kalenderjaar";
        $pdf->rapport_rendementText = "Rendement lopend kalenderjaar";
      
      
        $pdf->rapport_VOLK_titel = "Portefeuilleoverzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '9';
        //$pdf->rapport_fontsize = '10';
      
        if (file_exists(FPDF_FONTPATH . 'calibril.php'))
        {
          if (!isset($pdf->fonts['calibri']))
          {
            $pdf->AddFont('calibri', '', 'calibri.php');
            $pdf->AddFont('calibri', 'B', 'calibrib.php');
            $pdf->AddFont('calibri', 'I', 'calibrii.php');
            $pdf->AddFont('calibri', 'BI', 'calibribi.php');
          }
          $pdf->rapport_font = 'calibri';
        }
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime()) . " | " . vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = '';
        $pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}\n{Risicoklasse}";
  
        $pdf->blauwDonker=array(10,59,76);
        $pdf->blauwLicht=array(112,149,171);
        $pdf->bruinDonker=array(203,170,121);
        $pdf->bruinLicht=array(239,237,231);
        $pdf->grijsBlauw=array(168,189,201); //122.153.172 met doorzichtigheid 65%
        
        $pdf->rapport_kopvoet_bg = array(175, 175, 175);
        $pdf->rapport_titel_fontcolor = array(122, 153, 172);//array(163, 145, 97);//array(147,124,78); //array(191,143,0);
        $pdf->rapport_grafiek_color = array(163, 145, 97);
        $pdf->rapport_koptrans_color = array(163, 145, 97);
        $pdf->rapport_row_bg = array(235, 238, 241);
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor =  array('r' => 0);// array('r'=>0,'g'=>49,'b'=>60);//array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
        $pdf->rapport_grafiek_icolor = $pdf->blauwDonker;
        $pdf->rapport_grafiek_afm = $pdf->grijsBlauw;
        $pdf->rapport_grafiek_pcolor =  $pdf->rapport_grafiek_color;
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array('r'=>0,'g'=>49,'b'=>60);//$pdf->rapport_kop_fontcolor;// array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, 30);
        $pdf->pagebreak = $pdf->PageBreakTrigger;
        break;
      case "26" :
        //HENS
        $pdf->rapport_layout = 26;
        $pdf->marge = 8;
        $pdf->top_marge = 10;
        //$pdf->rowHeight=3.5;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        if (file_exists(FPDF_FONTPATH . 'VistaSansAltLight.php'))
        {
          if (!isset($pdf->fonts['vistasansalt']))
          {
            $pdf->AddFont('vistasansalt', '', 'VistaSansAltLight.php');
            $pdf->AddFont('vistasansalt', 'B', 'VistaSansAltReg.php');
            $pdf->AddFont('vistasansalt', 'I', 'VistaSansAltLightIt.php');
            $pdf->AddFont('vistasansalt', 'BI', 'VistaSansAltRegIt.php');
          }
          $pdf->rapport_font = 'vistasansalt';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
        $pdf->rapport_fontsize = '8';
        //$pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
        $pdf->rapport_font_rood = array('r' => 255, 'g' => 25, 'b' => 25);
        //$pdf->rapport_font_groen = array('r'=>93,'g'=>206,'b'=>74);
        $pdf->rapport_font_groen = array('r' => 57, 'g' => 151, 'b' => 41);
      
        $pdf->rapport_lijn_rood = array('r' => 100, 'g' => 0, 'b' => 0);
      
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "27" :
        // FIN
        $pdf->rapport_layout = 27;
        $pdf->marge = 8;
        $pdf->rapport_dontsortpie = true;
      
        $pdf->rapport_printAEXVergelijkingEur = 1;
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        //$pdf->rapport_VOLK_titel = "Vergelijkend overzicht verslagperiode";
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_geenIndex = 0;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
        $pdf->rapport_HSE_geenIndex = 0;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_valutaoverzicht = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_aantalVierDecimaal = 1;
        $pdf->rapport_OIS_geenIndex = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
        $pdf->rapport_VHO_geenIndex = 1;
      
        $pdf->rapport_PERF_liquiditeiten = 1;
        $pdf->rapport_perfIndexJanuari = 1;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_rendementText = 'Rendement over verslagperiode';
      
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend.\nDe totale waarde van uw portefeuille kan afwijken van de gegevens die u wellicht online bekeken heeft door kleine verschillen in gebruikte koersen.", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank} / {Risicoklasse}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_grafiek_color = array(0, 69, 124);
        $pdf->rapport_kop_bgcolor = array('r' => 0, 'g' => 173, 'b' => 239);
      
        //$pdf->rapport_kop_bgcolor = array('r'=>255,'g'=>255,'b'=>255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->rapport_factuurHeader = $__appvar['basedir'] . "/html/rapport/logo/factuurHeader.png";
        $pdf->rapport_factuurFooter = $__appvar['basedir'] . "/html/rapport/logo/factuurFooter.png";
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "28" :
        // MCP layout
        $pdf->rapport_riscoPerfonds = 1;
        $pdf->rapport_layout = 28;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 0;
        $pdf->rapport_VOLK_geensubtotaal = 1;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_geenvaluta = 1;
        $pdf->rapport_VOLK_rendement = 2;
        $pdf->rapport_VOLK_valutaoverzicht = 0;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VOLKD_volgorde_beginwaarde = 0;
        $pdf->rapport_VOLKD_geensubtotaal = 1;
        $pdf->rapport_VOLKD_decimaal = 0;
        $pdf->rapport_VOLKD_decimaal_proc = 1;
        $pdf->rapport_VOLKD_geenvaluta = 1;
        $pdf->rapport_VOLKD_rendement = 1;
        $pdf->rapport_VOLKD_valutaoverzicht = 1;
      
        $pdf->rapport_VHO_geenvaluta = 1;
        $pdf->rapport_VHO_geensubtotaal = 1;
        $pdf->rapport_VHO_volgorde_beginwaarde = 0;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 0;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_rendement = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 0;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 1;
        $pdf->rapport_OIB_valutaoverzicht = 1;
      
        $pdf->rapport_OIV_rendement = 1;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
        $pdf->rapport_OIV_valutaoverzicht = 0;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_titel = "Onderverdeling naar regio";
        $pdf->rapport_OIS_decimaal = 2;
      
        $pdf->rapport_OIR_titel = "Onderverdeling in Regio";
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_PERF_koptext = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_MUT_kwartaal = 1;
        $pdf->rapport_TRANS_kwartaal = 1;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 0;
        if(!isset($pdf->fonts['verdana']))
        {
          $pdf->AddFont('Verdana');
          $pdf->AddFont('Verdana','B','verdanab.php');
          $pdf->AddFont('verdana','I','verdanai.php');
          $pdf->AddFont('Verdana','BI','verdanaib.php');
        }
  
        $pdf->rapport_font = 'Verdana';
        $pdf->rapport_fontsize = '7';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "Poretefeuille Overzicht\n" . vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " .
          vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleFormat} / {Risicoklasse}\n{Naam1}\n{Naam2}";
      
        // $pdf->rapport_kop_bgcolor 	 = array('r'=>91,'g'=>186,'b'=>250);
        $pdf->rapport_kop_bgcolor = array('r' => 51, 'g' => 102, 'b' => 153);//
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 75, 'g' => 75, 'b' => 75);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_kop4_fontcolor = array(0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 75, 'g' => 75, 'b' => 75);
        $pdf->rapport_fondsVerdiept_fontcolor = array('r' => 120, 'g' => 120, 'b' => 120);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 75, 'g' => 75, 'b' => 75);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 75, 'g' => 75, 'b' => 75);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {Valuta}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "29" :
        // ECO layout
        $pdf->rapport_layout = 29;
        $pdf->marge = 8;
        $pdf->top_marge = 10;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_geenrentespec = 0;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
        $pdf->rapport_PERF_liquiditeiten = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '9';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "30" :
        //CAS
        $pdf->rapport_layout = 30;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 0;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
        if (file_exists(FPDF_FONTPATH . 'trebuc.php'))
        {
          if (!isset($pdf->fonts['trebuc']))
          {
            $pdf->AddFont('trebuc', '', 'trebuc.php');
            $pdf->AddFont('trebuc', 'B', 'trebucbd.php');
            $pdf->AddFont('trebuc', 'I', 'trebucit.php');
            $pdf->AddFont('trebuc', 'BI', 'trebucbi.php');
          }
          $pdf->rapport_font = 'trebuc';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}\nRisicoprofiel van uw portefeuille: {Risicoklasse}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "31" :
        //THB
        $pdf->rapport_layout = 31;
        $pdf->marge = 8;
        $pdf->top_marge = 15;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "32" :
        //FCM
        $pdf->rapport_layout = 32;
        $pdf->marge = 8;
        $pdf->top_marge = 8;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_titel = "Huidige samenstelling vermogen";
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = false;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 0;
      
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '9';
        //$pdf->rapport_fontsize = '10';
        /*
        if(file_exists(FPDF_FONTPATH.'aleo.php'))
        {
          if(!isset($pdf->fonts['aleo']))
          {
            $pdf->AddFont('aleo','','aleo.php');
            $pdf->AddFont('aleo','B','aleoB.php');
            $pdf->AddFont('aleo','I','aleoI.php');
            $pdf->AddFont('aleo','BI','aleoBI.php');
          }
          $pdf->rapport_font = 'aleo';
        }
        */
        if(file_exists(FPDF_FONTPATH.'HelveticaNeue.php'))
        {
          if(!isset($pdf->fonts['helveticaneue']))
          {
            $pdf->AddFont('HelveticaNeue','','HelveticaNeue.php');
            $pdf->AddFont('HelveticaNeue','B','HelveticaNeueb.php');
            $pdf->AddFont('HelveticaNeue','I','HelveticaNeuei.php');
            $pdf->AddFont('HelveticaNeue','BI','HelveticaNeuebi.php');
          }
          $pdf->rapport_font = 'HelveticaNeue';
        }
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = '';//vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend.",$pdf->rapport_taal)." Gebruikte koersen in deze rapportage gemarkeerd met een * zijn ouder dan ".	$pdf->portefeuilledata['VerouderdeKoersDagen']." dagen ten opzichte van de rapportagedatum.";
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
      
      
        $pdf->rapport_kop_bgcolor = array('r' => 83, 'g' => 106, 'b' => 146);
        $pdf->rapport_voet_bgcolor = array(204,224,245);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
        $pdf->rapport_fontcolor = array('r' => 64, 'g' => 64, 'b' => 64);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet} {Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "33" :
        //DOO
        $pdf->rapport_layout = 33;
        $pdf->marge = 8;
        $pdf->top_marge = 15;
        $pdf->rowHeight = 5;
        $pdf->kopkleur = array(98, 144, 128);
      
        //$pdf->rapport_valutaoverzicht_rev =1 ;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Arial';
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '8';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}\n{Risicoklasse}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, $pdf->marge);
        break;
      case "34" :
        //Alpha
        $pdf->rapport_layout = 34;
        $pdf->rowHeight = 4.3;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend. Koersen met een * gemarkeerd zijn minstens 1 dag oud.", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " . vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, $pdf->marge + 4);
        break;
      case "35" :
        $pdf->rapport_layout = 35;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_geenrentespec = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / Valuta: {RapportageValuta}\n{Naam1} {Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "36" :
        // BIR layout
        $pdf->rapport_layout = 36;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 0;
        $pdf->rapport_TRANS_decimaal = 2;
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_PERFG_portefeuilleKleur = array(87, 165, 25);
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " . vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 220, 'g' => 220, 'b' => 204);
        $pdf->rapport_kop_fontcolor = array('r' => 116, 'g' => 140, 'b' => 28);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 104, 'g' => 136, 'b' => 24);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 104, 'g' => 120, 'b' => 24);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 228, 'g' => 228, 'b' => 220);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 116, 'g' => 140, 'b' => 28);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "37" :
        //DBW
        $pdf->rapport_layout = 37;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
        $pdf->rowHeight = 4.3;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_geenrentespec = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '11';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = '';//vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = '';//vertaalTekst("Productiedatum ",$pdf->rapport_taal).date("d",mktime())." ".vertaalTekst($pdf->__appvar["Maanden"][date("n",mktime())],$pdf->rapport_taal)." ".date("Y",mktime());
        $pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "38" :
        // STE
        $pdf->rapport_layout = 38;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " . vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 194, 'g' => 179, 'b' => 157);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        if (file_exists($__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png"))
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png";
        }
        else
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        }
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "39" :
        // Capitael
        $pdf->rapport_layout = 39;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        if($__appvar['bedrijf']=='TEST')
        {
          if (file_exists(FPDF_FONTPATH . 'SFNSText.php'))
          {
            if (!isset($pdf->fonts['sfns']))
            {
              $pdf->AddFont('sfns', '', 'SFNSText.php');
              $pdf->AddFont('sfns', 'B', 'SFNSTextB.php');
              $pdf->AddFont('sfns', 'I', 'SFNSTextI.php');
              $pdf->AddFont('sfns', 'BI', 'SFNSTextBI.php');
            }
            $pdf->rapport_font = 'sfns';
          }
        }
        else
        {
          if (file_exists(FPDF_FONTPATH . 'baskvil.php'))
          {
            if (!isset($pdf->fonts['baskvil']))
            {
              $pdf->AddFont('baskvil', '', 'baskvil.php');
              $pdf->AddFont('baskvil', 'B', 'baskvilb.php');
              $pdf->AddFont('baskvil', 'I', 'baskvil.php');
              $pdf->AddFont('baskvil', 'BI', 'baskvilb.php');
            }
            $pdf->rapport_font = 'baskvil';
          }
        }
        /*
			if(file_exists(FPDF_FONTPATH.'calibri.php'))
		  {
  	    if(!isset($pdf->fonts['calibri']))
	      {
		      $pdf->AddFont('calibri','','calibri.php');
		      $pdf->AddFont('calibri','B','calibrib.php');
		      $pdf->AddFont('calibri','I','calibrii.php');
		      $pdf->AddFont('calibri','BI','calibribi.php');
	      }
			  $pdf->rapport_font = 'calibri';
		  }
*/
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}\n{DepotbankOmschrijving} {PortefeuilleVoorzet}{Portefeuille}";
        $pdf->rapport_rendementText = 'Rendement over verslagperiode';
      
        $pdf->rapport_kop_bgcolor = array('r' => 194, 'g' => 179, 'b' => 157);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        if (file_exists($__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png"))
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png";
        }
        else
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        }
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "40" :
        // Groenstate Vermogensbeheer
        $pdf->rapport_layout = 40;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
      
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Arial';
        if (file_exists(FPDF_FONTPATH . 'UniversLT55.php'))
        {
          if (!isset($pdf->fonts['univers']))
          {
            $pdf->AddFont('univers', '', 'UniversLT55.php');
            $pdf->AddFont('univers', 'B', 'UniversLT55.php');
            $pdf->AddFont('univers', 'I', 'UniversLT55.php');
            $pdf->AddFont('univers', 'BI', 'UniversLT55.php');
          }
          $pdf->rapport_font = 'univers';
        }
      
      
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        //$pdf->rapport_koptext  = vertaalTekst("Client",$pdf->rapport_taal)." {ClientVermogensbeheerder} / ".vertaalTekst("Rekening",$pdf->rapport_taal)." {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
        $pdf->rapport_koptext = "{Namen}\n{DepotbankOmschrijving}\n" . vertaalTekst("Depotnummer", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}";
        $pdf->rapport_kop_bgcolor = array('r' => 0, 'g' => 78, 'b' => 58);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        if (file_exists($__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png"))
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png";
        }
        else
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        }
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "41" :
        // Deloitte
        $pdf->rapport_layout = 41;
        $pdf->marge = 15;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Arial';
        if (file_exists(FPDF_FONTPATH . 'FrutigerNextPro-Regular.php'))
        {
          if (!isset($pdf->fonts['frutiger']))
          {
            $pdf->AddFont('frutiger', '', 'FrutigerNextPro-Light.php');
            $pdf->AddFont('frutiger', 'B', 'FrutigerNextPro-Bold.php');
            $pdf->AddFont('frutiger', 'R', 'FrutigerNextPro-Regular.php');
            $pdf->AddFont('frutiger', 'BI', 'FrutigerNextPro-BoldIta.php');
            //LightIta Italic MediumIta Medium
          }
          $pdf->rapport_font = 'frutiger';
        }
      
        if (file_exists(FPDF_FONTPATH . 'Garamond3LTStd.php'))
        {
          if (!isset($pdf->fonts['garmond']))
          {
            $pdf->AddFont('garmond', '', 'Garamond3LTStd.php');
            $pdf->AddFont('garmond', 'B', 'Garamond3LTStd-Bold.php');
            $pdf->AddFont('garmond', 'BI', 'Garamond3LTStd-BoldItalic.php');
            $pdf->AddFont('garmond', 'I', 'Garamond3LTStd-Italic.php');
          }
        
        }
      
      
        $pdf->rowHeight = 4.233;
        $pdf->rapport_fontsize = '8.5';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '7';
      
        $jaar = date("Y", $pdf->rapport_datum);
        $kwartaal = ceil(date("n", $pdf->rapport_datum) / 3);
        $kwartalen = array('', 'eerste', 'tweede', 'derde', 'vierde');
        if (is_array($pdf->__appvar['consolidatie']))
        {
          $pdf->rapport_voettext = vertaalTekst("Rapportage over", $pdf->rapport_taal) . " " . $kwartalen[$kwartaal] . " kwartaal $jaar - " . (date("d")) . " " . vertaalTekst($__appvar["Maanden"][date("n")], $pdf->rapport_taal) . " " . date("Y") .
            " - Consolidatie";
        }
        else
        {
          $pdf->rapport_voettext = vertaalTekst("Rapportage over", $pdf->rapport_taal) . " " . $kwartalen[$kwartaal] . " kwartaal $jaar - " . (date("d")) . " " . vertaalTekst($__appvar["Maanden"][date("n")], $pdf->rapport_taal) . " " . date("Y") .
            " - " . $pdf->portefeuilledata['Naam'] . " - " . $pdf->portefeuilledata['Portefeuille'];
        }
      
      
        $pdf->rapport_koptext = vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " . vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
        $pdf->blue = array(0, 39, 118);
        $pdf->midblue = array(0, 161, 222);
        $pdf->lightblue = array(114, 119, 231);
        $pdf->green = array(146, 212, 0);
        $pdf->darkgreen = array(60, 138, 46);
        $pdf->lightgreen = array(201, 221, 3);
        $pdf->kopkleur = $pdf->midblue;
      
        // $this->pdf->darkgreen
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = $pdf->blue;//array('r'=>0,'g'=>0,'b'=>0);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        if (file_exists($__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png"))
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png";
        }
        else
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        }
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "42" :
        // RRP layout
        $pdf->rapport_layout = 42;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
      
        if (file_exists(FPDF_FONTPATH . 'DINOT-Regular.php'))
        {
          if (!isset($pdf->fonts['dinot']))
          {
            $pdf->AddFont('dinot', '', 'DINOT-Regular.php');
            $pdf->AddFont('dinot', 'B', 'DINOT-Bold.php');
            $pdf->AddFont('dinot', 'I', 'DINOT-RegularItalic.php');
            $pdf->AddFont('dinot', 'BI', 'DINOT-BoldItalic.php');
          }
          $pdf->rapport_font = 'dinot';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
        /*
      if(file_exists(FPDF_FONTPATH.'garmond.php'))
		  {
  	    if(!isset($pdf->fonts['garmond']))
	      {
		      $pdf->AddFont('garmond','','garmond.php');
		      $pdf->AddFont('garmond','B','garmondb.php');
		      $pdf->AddFont('garmond','BI','garmondi.php');
	      }
			  $pdf->rapport_kopfont = 'garmond';
        $pdf->rapport_font = 'garmond';
        $pdf->rapport_fontsize = '10';
		  }
*/
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        //$pdf->rapport_koptext  = vertaalTekst("Client",$pdf->rapport_taal)." {ClientVermogensbeheerder} / ".vertaalTekst("Rekening",$pdf->rapport_taal)." {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
        $pdf->rapport_koptext = "{Naam1} \nRapportagedatum {RapportageDatum}";
  
        $pdf->blauwDonker=array(10,59,76);
        $pdf->blauwLicht=array(112,149,171);
        $pdf->bruinDonker=array(203,170,121);
        $pdf->bruinLicht=array(239,237,231);
        $pdf->grijsBlauw=array(168,189,201); //122.153.172 met doorzichtigheid 65%
  
        $pdf->rapport_kopvoet_bg = array(175, 175, 175);
        $pdf->rapport_titel_fontcolor = array(122, 153, 172);//array(163, 145, 97);//array(147,124,78); //array(191,143,0);
        $pdf->rapport_grafiek_color = array(163, 145, 97);
        $pdf->rapport_koptrans_color = array(163, 145, 97);
        $pdf->rapport_row_bg = array(235, 238, 241);
        
        $pdf->rapport_kop_kleur = array(74, 76, 89);
        $pdf->rapport_kop_kleur = array(0, 49, 60);
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "43" :
        // ORC layout
        $pdf->rapport_layout = 43;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
        $pdf->printAEXVergelijkingProcentTeken = 1;
        $pdf->printValutaPerformanceOverzichtProcentTeken = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_aantalVierDecimaal = true;
      
        //	$pdf->rapport_VHO_titel = "Huidige samenstelling effectenportefeuille";
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_lijnenKorter = 5;
        $pdf->rapport_VHO_aantalVierDecimaal = true;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Onderverdeling in beleggingscategorie";
        $pdf->rapport_OIB_specificatie = 0;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 0;
        $pdf->rapport_TRANS_decimaal = 2;
        $pdf->rapport_TRANS_decimaal2 = 2;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
        $pdf->rapport_PERF_rendement = 1;
        $pdf->rapport_PERF_portefeuilleIndex = 0;
        $pdf->rapport_PERF_lijnenKorter = 10;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_perfIndexJanuari = true;
        $pdf->rapport_valutaPerformanceJanuari = true;
        $pdf->printValutaPerformanceOverzichtProcentTeken = true;
      
        $pdf->rapport_rendementText = "Rendement over verslagperiode";
      
        $pdf->rapport_font = 'Times';
        if (file_exists(FPDF_FONTPATH . 'calibri.php'))
        {
          if (!isset($pdf->fonts['calibri']))
          {
            $pdf->AddFont('calibri', '', 'calibri.php');
            $pdf->AddFont('calibri', 'B', 'calibrib.php');
            $pdf->AddFont('calibri', 'I', 'calibrii.php');
            $pdf->AddFont('calibri', 'BI', 'calibribi.php');
          }
          $pdf->rapport_font = 'calibri';
        }
        //$pdf->rapport_font = 'verdana';
      
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        //$pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend. Bedragen worden afgerond op twee decimalen. Hierdoor kunnen minimale afrondingsverschillen ontstaan.", $pdf->rapport_taal);
      
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n{VermogensbeheerderNaam} / " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\nProfiel: {SoortOvereenkomst} {Risicoklasse}\n{Naam1}";//\n{Naam2}
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "44" :
        //TOP & IBE
        $pdf->rapport_layout = 44;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
        $pdf->printValutaPerformanceOverzichtProcentTeken = true;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 1;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        $pdf->rapport_VOLK_geenvaluta = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
        $pdf->rapport_PERF_jaarRendement = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Arial';
        /*
			if(file_exists(FPDF_FONTPATH.'calibri.php'))
		  {
  	    if(!isset($pdf->fonts['calibri']))
	      {
		      $pdf->AddFont('calibri','','calibri.php');
		      $pdf->AddFont('calibri','B','calibrib.php');
		      $pdf->AddFont('calibri','I','calibrii.php');
		      $pdf->AddFont('calibri','BI','calibribi.php');
	      }
			  $pdf->rapport_font = 'calibri';
		  }
*/
        $pdf->rapport_fontsize = '9';
        //$pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
      
        if ($data['Vermogensbeheerder'] == 'IBE')
        {
          $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
          $pdf->rapport_kop_fontcolor = array('r' => 88, 'g' => 171, 'b' => 39);//array('r'=>236,'g'=>0,'b'=>140);
          $pdf->rapport_kop_fontstyle = '';
          $pdf->rapport_kop2_fontcolor = array('r' => 88, 'g' => 171, 'b' => 39);//array('r'=>236,'g'=>0,'b'=>140);
        
        }
        else
        {
          $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
          $pdf->rapport_kop_fontcolor = array('r' => 236, 'g' => 0, 'b' => 140);//array('r'=>0,'g'=>69,'b'=>132);
          $pdf->rapport_kop_fontstyle = '';
          $pdf->rapport_kop2_fontcolor = array('r' => 236, 'g' => 0, 'b' => 140);//array('r'=>0,'g'=>69,'b'=>132);
        }
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        if (file_exists($__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png"))
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png";
          /*
			  if($data['Remisier']=='topcapital')
			  {
			    $pdf->rapport_kop2_fontcolor = array('r'=>236,'g'=>0,'b'=>140);
			    $pdf->rapport_kop_fontcolor = array('r'=>236,'g'=>0,'b'=>140);
			  }
			  */
        }
        else
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        }
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "45" :
        //ANT
        $pdf->rapport_layout = 45;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 1;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        $pdf->rapport_VOLK_geenvaluta = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
        $pdf->rapport_PERF_jaarRendement = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
      
        if (file_exists(FPDF_FONTPATH . 'calibri.php'))
        {
          if (!isset($pdf->fonts['calibri']))
          {
            $pdf->AddFont('calibri', '', 'calibri.php');
            $pdf->AddFont('calibri', 'B', 'calibrib.php');
            $pdf->AddFont('calibri', 'I', 'calibrii.php');
            $pdf->AddFont('calibri', 'BI', 'calibribi.php');
          }
          $pdf->rapport_font = 'calibri';
        }
      
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '8';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
        $pdf->rapport_koptextParticipants = "{Naam1}\n{Naam2}";
      
        $pdf->rapport_paars = array(125, 41, 145);
        $pdf->rapport_blauw = array(25, 50, 95);
        $pdf->rapport_kop_bgcolor = array('r' => $pdf->rapport_paars[0], 'g' => $pdf->rapport_paars[1], 'b' => $pdf->rapport_paars[2]);
        //$pdf->rapport_kop_bgcolor = array('r'=>255,'g'=>255,'b'=>255);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);//array('r'=>0,'g'=>69,'b'=>132);
        //$pdf->rapport_kop_fontcolor = array('r'=>$pdf->rapport_paars[0],'g'=>$pdf->rapport_paars[1],'b'=>$pdf->rapport_paars[2]);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);//array('r'=>0,'g'=>69,'b'=>132);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, 20);
        break;
      case "46" :
        // FDX layout
        $pdf->rapport_layout = 46;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_aantalVierDecimaal = true;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->underlinePercentage = 0.8;
        $pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '7.5';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend.
CFS Investments is een verbonden agent van HJCO Capital Partners B.V., een beleggingsonderneming onder toezicht van de AFM en DNB.", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("{VermogensbeheerderNaam}, {huidigeDatum} adm per {rapportageDatum}\n Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " . vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
      
        $pdf->regelKleur = array('r' => 204, 'g' => 228, 'b' => 240);
        $pdf->kop_fontcolor = array('r' => 0, 'g' => 157, 'b' => 224);
        $pdf->rapport_kop_bgcolor = array('r' => 35, 'g' => 45, 'b' => 104);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 35, 'g' => 45, 'b' => 104);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 35, 'g' => 45, 'b' => 104);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "47" :
        // FCT layout
        $pdf->rapport_layout = 47;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
        /*
      if(file_exists(FPDF_FONTPATH.'garmond.php'))
		  {
  	    if(!isset($pdf->fonts['garmond']))
	      {
		      $pdf->AddFont('garmond','','garmond.php');
		      $pdf->AddFont('garmond','B','garmondb.php');
		      $pdf->AddFont('garmond','BI','garmondi.php');
	      }
			  $pdf->rapport_kopfont = 'garmond';
        $pdf->rapport_font = 'garmond';
		  }
      else
      */
        $pdf->rapport_font = 'Times';
      
        $pdf->rapport_fontsize = '10';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " . vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "48" :
        // IDC layout
        $pdf->rapport_layout = 48;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 0;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 0;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 0;
        /*
      if(file_exists(FPDF_FONTPATH.'helvetica.php'))
		  {
  	    if(!isset($pdf->fonts['helvetica']))
	      {
		      $pdf->AddFont('helvetica','','helvetica.php');
          $pdf->AddFont('helvetica','I','helveticai.php');
		      $pdf->AddFont('helvetica','B','helveticab.php');
		      $pdf->AddFont('helvetica','BI','helveticabi.php');
	      }
		  }
			$pdf->rapport_font = 'helvetica';
*/
        $pdf->rapport_font = 'arial';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {SoortOvereenkomst} / {Risicoklasse}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 23, 'g' => 55, 'b' => 94);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "49" :
        //FCT
        $pdf->rapport_layout = 49;
        $pdf->marge = 20;
        $pdf->margeOnder = 15;
        $pdf->top_marge = 25;
        $pdf->rapportYstart = 15;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 1;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        $pdf->rapport_VOLK_geenvaluta = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
        $pdf->rapport_PERF_jaarRendement = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Arial';
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rowHeight = 6;
        $pdf->witCell = 0.75;
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = '';//'vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = '';//'vertaalTekst("Productiedatum ",$pdf->rapport_taal).date("d",mktime())." ".vertaalTekst($pdf->__appvar["Maanden"][date("n",mktime())],$pdf->rapport_taal)." ".date("Y",mktime());
        //$pdf->rapport_koptext  = "\n\n{DepotbankOmschrijving} ".vertaalTekst("Depot",$pdf->rapport_taal)." {PortefeuilleVoorzet}{Portefeuille}";//\n{Naam1}\n{Naam2}
        $pdf->rapport_koptext = '';
        $pdf->achtergrondKop = array(189, 199, 220);
        $pdf->achtergrondLicht = array(235, 239, 245);
        $pdf->achtergrondDonker = array(219, 224, 235);
        $pdf->achtergrondTotaal = array(251, 224, 207);
        $pdf->achtergrondlijn = array(1, 75, 126);
        $pdf->koplijn = array(236, 120, 74);
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 2, 'g' => 75, 'b' => 126);//array('r'=>0,'g'=>69,'b'=>132);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 2, 'g' => 75, 'b' => 126);//array('r'=>0,'g'=>69,'b'=>132);
      
        $pdf->rapport_kop3_fontcolor = array('r' => 2, 'g' => 75, 'b' => 126);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 2, 'g' => 75, 'b' => 126);
        $pdf->rapport_fontcolor = array('r' => 2, 'g' => 75, 'b' => 126);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 2, 'g' => 75, 'b' => 126);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 2, 'g' => 75, 'b' => 126);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 2, 'g' => 75, 'b' => 126);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 2, 'g' => 75, 'b' => 126);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 2, 'g' => 75, 'b' => 126);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, 14);
        break;
      case "50" :
        // DTI layout (op basis van L35)
        $pdf->rapport_layout = 50;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_geenrentespec = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '10';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1} {Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "51" :
        // CSG layout capital support group
        $pdf->rapport_layout = 51;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_indexUit = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_legenda = 1;
  
        $pdf->rapport_MUT_leegNietTonen = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Arial';
      
        if (file_exists(FPDF_FONTPATH . 'calibri.php'))
        {
          if (!isset($pdf->fonts['calibri']))
          {
            $pdf->AddFont('calibri', '', 'calibri.php');
            $pdf->AddFont('calibri', 'B', 'calibrib.php');
            $pdf->AddFont('calibri', 'I', 'calibrii.php');
            $pdf->AddFont('calibri', 'BI', 'calibribi.php');
          }
          $pdf->rapport_font = 'calibri';
        }
      
        $pdf->rapport_fontsize = '8';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        //$pdf->rapport_koptext  = vertaalTekst("Client",$pdf->rapport_taal)." {ClientVermogensbeheerder} / ".vertaalTekst("Rekening",$pdf->rapport_taal)." {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
        $pdf->rapport_koptext = "{Naam1} \nRapportagedatum {RapportageDatum}";
      
        //$pdf->rapport_kop_kleur = array(117,139,153);
        $pdf->rapport_regelKleur = array(233, 239, 246);
        $pdf->rapport_kop_kleur = array(233, 239, 246);//array(0,68,106);
        $pdf->rapport_kop_bgcolor = array('r' => 233, 'g' => 239, 'b' => 246);

        //$pdf->rapport_kop_fontcolor = array('r'=>147,'g'=>155,'b'=>161);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 68, 'b' => 106);
        $pdf->rapport_kop_fontstyle = 'B';
        $pdf->rapport_kop_fontsize = $pdf->rapport_fontsize + 0.5;
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "52" :
        // MER layout
        $pdf->rapport_layout = 52;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " . vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 186, 'g' => 135, 'b' => 72);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = 'b';
        $pdf->rapport_background_fill = array(200, 200, 200);
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "53" :
        // ICP ISIS layout
        $pdf->rapport_layout = 53;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        if (file_exists(FPDF_FONTPATH . 'calibri.php'))
        {
          if (!isset($pdf->fonts['calibri']))
          {
            $pdf->AddFont('calibri', '', 'calibri.php');
            $pdf->AddFont('calibri', 'B', 'calibrib.php');
            $pdf->AddFont('calibri', 'I', 'calibrii.php');
            $pdf->AddFont('calibri', 'BI', 'calibribi.php');
          }
          $pdf->rapport_font = 'calibri';
        }
      
      
        $pdf->rapport_fontsize = '10';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " . vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);//array('r'=>153,'g'=>153,'b'=>153); array('r'=>245,'g'=>240,'b'=>155);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
        $pdf->rapport_fontcolor = array('r' => 51, 'g' => 51, 'b' => 51);
        $pdf->rapport_balkKleur = array(255, 204, 0);
        $pdf->rapport_regelKleur = array(230, 230, 230);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "54" :
        // Schraauwers
        $pdf->rapport_layout = 54;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        $pdf->rapport_HSE_geenrentespec = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
       
			if(file_exists(FPDF_FONTPATH.'dax.php'))
		  {
  	    if(!isset($pdf->fonts['dax']))
	      {
		      $pdf->AddFont('dax','','dax.php');
		      $pdf->AddFont('dax','B','daxb.php');
		      $pdf->AddFont('dax','I','daxi.php');
		      $pdf->AddFont('dax','BI','daxbi.php');
	      }
			  $pdf->rapport_font = 'dax';
		  }

      
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}\n{DepotbankOmschrijving} {PortefeuilleVoorzet}{Portefeuille}";
        $pdf->rapport_koptext = "\n \n{PortefeuilleVoorzet}{Portefeuille}";
        $pdf->rapport_rendementText = 'Rendement over verslagperiode';
      
        $pdf->rapport_kop_bgcolor = array('r' => 52, 'g' => 52, 'b' => 105);//array('r'=>194,'g'=>179,'b'=>157);
        $pdf->rapport_kop_logocolor = array('r' => 141, 'g' => 198, 'b' => 63);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        if (file_exists($__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png"))
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png";
        }
        else
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        }
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "55" :
        // EFI
        $pdf->rapport_layout = 55;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_HSE_geenrentespec = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        if (file_exists(FPDF_FONTPATH . 'calibri.php'))
        {
          if (!isset($pdf->fonts['calibri']))
          {
            $pdf->AddFont('calibri', '', 'calibri.php');
            $pdf->AddFont('calibri', 'B', 'calibrib.php');
            $pdf->AddFont('calibri', 'I', 'calibrii.php');
            $pdf->AddFont('calibri', 'BI', 'calibribi.php');
          }
          $pdf->rapport_font = 'calibri';
        }
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "";
        //$pdf->rapport_koptext  = "\n \n{PortefeuilleVoorzet}{Portefeuille}";
        $pdf->rapport_rendementText = 'Rendement over verslagperiode';
      
        $pdf->rapport_kop_bgcolor = array('r' => 230, 'g' => 230, 'b' => 230);//array('r'=>194,'g'=>179,'b'=>157);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = 'b';
        $pdf->grafiekAchtergrondKleur = array(255, 255, 255);
        $pdf->regelFillKleur = array(221, 235, 247);//221 / 235 / 247.
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, 15);
        break;
      case "56" :
        // TPA layout
        $pdf->rapport_layout = 56;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        /*
		  if(file_exists(FPDF_FONTPATH.'Frutiger.php'))
		  {
  	    if(!isset($pdf->fonts['frutiger']))
	      {
		      $pdf->AddFont('frutiger','','Frutigerl.php');
		      $pdf->AddFont('frutiger','B','Frutigerb.php');
		      $pdf->AddFont('frutiger','R','Frutiger.php');
		      $pdf->AddFont('frutiger','BI','Frutigerbi.php');
	      }
			  $pdf->rapport_font = 'frutiger';
		  }
		  else
*/
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '8';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext2 = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 35, 'g' => 45, 'b' => 104); //35/45/104 //33/43/105 //'r'=>88,'g'=>119,'b'=>255
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);;//array('r'=>127,'g'=>128,'b'=>132);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        //$pdf->rapport_fonds_fontcolor = array('r'=>0,'g'=>73,'b'=>156);
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, 20);
        break;
      case "57" :
        // LBC layout
        $pdf->rapport_layout = 57;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->kwartaalFactuurEindKwartaal=true;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 0;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 0, 'g' => 149, 'b' => 219);//array('r'=>245,'g'=>240,'b'=>155);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_background_fill = array(181,231,249);
        $pdf->rapport_lichtblauw = array(0, 176, 202);
        $pdf->rapport_donkerblauw = array(11, 28, 43);
        $pdf->rapport_grafiek_pcolor =  array($pdf->rapport_kop_bgcolor['r'], $pdf->rapport_kop_bgcolor['g'], $pdf->rapport_kop_bgcolor['b']) ;
        $pdf->rapport_grafiek_icolor = $pdf->rapport_donkerblauw;
        $pdf->rapport_grafiek_afm = array(181, 231, 249);
  
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = $pdf->rapport_kop_bgcolor;//array('r' => 23, 'g' => 91, 'b' => 126);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = $pdf->rapport_kop_bgcolor;//array('r' => 23, 'g' => 91, 'b' => 126);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = $pdf->rapport_kop_bgcolor;//array('r' => 23, 'g' => 91, 'b' => 126);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);//$pdf->rapport_kop_bgcolor;//array('r' => 23, 'g' => 91, 'b' => 126);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);//array('r' => 23, 'g' => 91, 'b' => 126);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);//array('r' => 23, 'g' => 91, 'b' => 126);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "58" :
        // EVO layout
        $pdf->rapport_layout = 58;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        if (file_exists(FPDF_FONTPATH . 'calibri.php'))
        {
          if (!isset($pdf->fonts['calibri']))
          {
            $pdf->AddFont('calibri', '', 'calibri.php');
            $pdf->AddFont('calibri', 'B', 'calibrib.php');
            $pdf->AddFont('calibri', 'I', 'calibrii.php');
            $pdf->AddFont('calibri', 'BI', 'calibribi.php');
          }
          $pdf->rapport_font = 'calibri';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = '';//'vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
        $pdf->rapport_koptext = " \n{Naam1}\n{Naam2}";
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "59" :
        // DVE  layout
        $pdf->rapport_layout = 59;
        $pdf->marge = 8;
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        if (file_exists(FPDF_FONTPATH . 'trebuc.php'))
        {
          if (!isset($pdf->fonts['trebuc']))
          {
            $pdf->AddFont('trebuc', '', 'trebuc.php');
            $pdf->AddFont('trebuc', 'B', 'trebucbd.php');
            $pdf->AddFont('trebuc', 'I', 'trebucit.php');
            $pdf->AddFont('trebuc', 'BI', 'trebucbi.php');
          }
          $pdf->rapport_font = 'trebuc';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = '';//'vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
        $pdf->rapport_koptext = "";
      
        //$pdf->rapport_default_fontcolor = array('r'=>0,'g'=>0,'b'=>0);
        //$pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, 20);
        break;
      case "60" :
        //IBE
        $pdf->rapport_layout = 60;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 1;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        $pdf->rapport_VOLK_geenvaluta = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
        $pdf->rapport_PERF_jaarRendement = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '9';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 88, 'g' => 171, 'b' => 39);//array('r'=>236,'g'=>0,'b'=>140);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 88, 'g' => 171, 'b' => 39);//array('r'=>236,'g'=>0,'b'=>140);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        if (file_exists($__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png"))
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Remisier'] . ".png";
        }
        else
        {
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        }
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "61" :
        // TEI TE Inmaxxa
        $pdf->rapport_layout = 61;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
      
        if (file_exists(FPDF_FONTPATH . 'TCM_____.php'))
        {
          if (!isset($pdf->fonts['twcenmt']))
          {
            $pdf->AddFont('twcenmt', '', 'TCM_____.php');
            $pdf->AddFont('twcenmt', 'B', 'TCB_____.php');
            $pdf->AddFont('twcenmt', 'I', 'TCMI____.php');
            $pdf->AddFont('twcenmt', 'BI', 'TCBI____.php');
          }
          $pdf->rapport_font = 'twcenmt';
        }
        else
        {
          $pdf->rapport_font = 'Arial';
        }
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}\nPortefeuillenummer: {Portefeuille}";
      
        $tekstkleur = array('r' => 30, 'g' => 53, 'b' => 104);
        $pdf->rapport_kop_bgcolor = array('r' => 30, 'g' => 53, 'b' => 104);//'r'=>0,'g'=>38,'b'=>84
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = $tekstkleur;
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = $tekstkleur;
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = $tekstkleur;
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = $tekstkleur;
      
        //$pdf->rapport_fonds_fontcolor = array('r'=>0,'g'=>73,'b'=>156);
        $pdf->rapport_fonds_fontcolor = $tekstkleur;
      
        $pdf->rapport_fontcolor = $tekstkleur;
        $pdf->rapport_subtotaal_omschr_fontcolor = $tekstkleur;
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = $tekstkleur;
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "62" :
        // AMB layout
        $pdf->rapport_layout = 62;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
      
        if (file_exists(FPDF_FONTPATH . 'Frutiger.php'))
        {
          if (!isset($pdf->fonts['frutiger']))
          {
            $pdf->AddFont('frutiger', '', 'Frutigerl.php');
            $pdf->AddFont('frutiger', 'B', 'Frutigerb.php');
            $pdf->AddFont('frutiger', 'R', 'Frutiger.php');
            $pdf->AddFont('frutiger', 'BI', 'Frutigerbi.php');
          }
          $pdf->rapport_font = 'frutiger';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 102, 'b' => 0);  //'r'=>88,'g'=>119,'b'=>255
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);;//array('r'=>127,'g'=>128,'b'=>132);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        //$pdf->rapport_fonds_fontcolor = array('r'=>0,'g'=>73,'b'=>156);
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "63" :
        $pdf->rapport_layout = 63;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
        $pdf->rapport_valutaoverzicht_rev = 1;
      
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_geenrentespec = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Arial';
        //$pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsize = '9';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '5';
        $pdf->rapport_voettext = '';//vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = '';//vertaalTekst("Productiedatum ",$pdf->rapport_taal).date("d",mktime())." ".vertaalTekst($pdf->__appvar["Maanden"][date("n",mktime())],$pdf->rapport_taal)." ".date("Y",mktime());
        $pdf->rapport_koptext = "\n\n{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1} {Naam2}";
      
        $pdf->rapport_kop_lijn = array('r' => 54, 'g' => 55, 'b' => 139);
        $pdf->rapport_kop_bgcolor = array('r' => 204, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_bgcolor = array('r' => 255, 'g' => 255, 'b' => 204);
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "64" :
        // BES layout
        $pdf->rapport_layout = 64;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
      
        if (file_exists(FPDF_FONTPATH . 'SourceSansPro-Regular.php'))
        {
          if (!isset($pdf->fonts['sanspro']))
          {
            $pdf->AddFont('sanspro', '', 'SourceSansPro-Regular.php');
            $pdf->AddFont('sanspro', 'B', 'SourceSansPro-Bold.php');
            $pdf->AddFont('sanspro', 'I', 'SourceSansPro-Italic.php');
            $pdf->AddFont('sanspro', 'BI', 'SourceSansPro-BoldItalic.php');
          }
          $pdf->rapport_font = 'sanspro';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 64, 'g' => 183, 'b' => 176);  //'r'=>88,'g'=>119,'b'=>255
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);;//array('r'=>127,'g'=>128,'b'=>132);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        //$pdf->rapport_fonds_fontcolor = array('r'=>0,'g'=>73,'b'=>156);
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "65" :
        // DOU layout
        $pdf->rapport_layout = 65;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
      
        if (file_exists(FPDF_FONTPATH . 'Frutiger.php'))
        {
          if (!isset($pdf->fonts['frutiger']))
          {
            $pdf->AddFont('frutiger', '', 'Frutigerl.php');
            $pdf->AddFont('frutiger', 'B', 'Frutigerb.php');
            $pdf->AddFont('frutiger', 'R', 'Frutiger.php');
            $pdf->AddFont('frutiger', 'BI', 'Frutigerbi.php');
          }
          $pdf->rapport_font = 'frutiger';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Portefeuille", $pdf->rapport_taal).": {Portefeuille}";
      
        $pdf->rapport_grafiek_color = array(84, 118, 0);;//array(75,84,98);
        $pdf->rapport_kop_bgcolor = array('r' => 81, 'g' => 84, 'b' => 96);//81/84/96  //'r'=>75,'g'=>84,'b'=>98 'r'=>84,'g'=>118,'b'=>0
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);;//array('r'=>127,'g'=>128,'b'=>132);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        //$pdf->rapport_fonds_fontcolor = array('r'=>0,'g'=>73,'b'=>156);
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "66" :
        // ERC
        $pdf->rapport_layout = 66;
        $pdf->marge = 8;
        $pdf->rapport_dontsortpie = true;
      
        $pdf->rapport_printAEXVergelijkingEur = 1;
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        //$pdf->rapport_VOLK_titel = "Vergelijkend overzicht verslagperiode";
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_geenIndex = 0;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
        $pdf->rapport_HSE_geenIndex = 0;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_valutaoverzicht = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_aantalVierDecimaal = 1;
        $pdf->rapport_OIS_geenIndex = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
        $pdf->rapport_VHO_geenIndex = 1;
      
        $pdf->rapport_PERF_liquiditeiten = 1;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_rendementText = 'Rendement over verslagperiode';
      
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}\n{PortefeuilleVoorzet}{Portefeuille}";
      
        $pdf->rapport_row_bg = array(242, 242, 242);
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->rapport_factuurHeader = $__appvar['basedir'] . "/html/rapport/logo/factuurHeader.png";
        $pdf->rapport_factuurFooter = $__appvar['basedir'] . "/html/rapport/logo/factuurFooter.png";
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "67" :
        //ASN
        $pdf->rapport_layout = 67;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
        if(!isset($pdf->fonts['verdana']))
        {
          $pdf->AddFont('Verdana');
          $pdf->AddFont('Verdana','B','verdanab.php');
          $pdf->AddFont('verdana','I','verdanai.php');
          $pdf->AddFont('Verdana','BI','verdanaib.php');
        }
  
        $pdf->rapport_font = 'Verdana';
        $pdf->rapport_fontsize = '8';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);//array('r'=>196,'g'=>38,'b'=>46);//array('r'=>227,'g'=>27,'b'=>35);
        $pdf->rapport_kop_blauw = array('r' => 0, 'g' => 169, 'b' => 224);
        $pdf->rapport_totaal_fillcolor = array(129, 138, 16);//=array(196,38,46);
        $pdf->rapport_totaal_textcolor = array(255, 255, 255);
        
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);//array('r'=>255,'g'=>255,'b'=>255);
        $pdf->rapport_kop_fontstyle = 'B';
      
        $pdf->rapport_kop2_fontcolor = array('r' => $pdf->rapport_totaal_fillcolor[0] , 'g' => $pdf->rapport_totaal_fillcolor[1], 'b' => $pdf->rapport_totaal_fillcolor[2]);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = $pdf->rapport_kop2_fontcolor;//array('r' => 227, 'g' => 27, 'b' => 35);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = $pdf->rapport_kop2_fontcolor;// array('r' => 227, 'g' => 27, 'b' => 35);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 227, 'g' => 27, 'b' => 35);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      

        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 227, 'g' => 27, 'b' => 35);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        if($data['Taal']<>0)
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" .str_replace('.png','_en.png',$data['Logo']);
        else
          $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "68" :
        // REN layout
        $pdf->rapport_layout = 68;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 0;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 0;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 0;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 0;
      
        $pdf->rapport_font = 'arial';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}\n{PortefeuilleVoorzet}{Portefeuille}";
        $pdf->rapport_consolidatieKoptext = "{Naam1}\n{Naam2}";
      
        $pdf->rapport_grafiek_pcolor = array(0, 40, 58);
        $pdf->rapport_grafiek_icolor = array(176, 160, 122);
        $pdf->rapport_regelAchtergrond = array(242, 234, 188);
        $pdf->rapport_kop_bgcolor = array('r' => 23, 'g' => 55, 'b' => 94);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "69" :
        // Centramin B.V.
        $pdf->rapport_layout = 69;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 0;
        $pdf->rapport_OIS_rendement = 0;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        if (file_exists(FPDF_FONTPATH . 'baskvil.php'))
        {
          if (!isset($pdf->fonts['baskvil']))
          {
            $pdf->AddFont('baskvil', '', 'baskvil.php');
            $pdf->AddFont('baskvil', 'B', 'baskvilb.php');
            $pdf->AddFont('baskvil', 'I', 'baskvil.php');
            $pdf->AddFont('baskvil', 'BI', 'baskvilb.php');
          }
          $pdf->rapport_font = 'baskvil';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
        $pdf->rapport_fontsize = '9';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum: ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
      
        $pdf->rapport_koptext = "\n{Client}\n{Naam1} {Naam2}\n{SoortOvereenkomst}";
        $pdf->rapport_consolidatieKoptext = "\n \n{Naam1} {Naam2}\nGeconsolideerd";
        $pdf->rapport_rendementText = 'Rendement over verslagperiode';
        $pdf->rapport_perfIndexJanuari = true;
        $pdf->rapport_valutaPerformanceJanuari = true;
      
        $pdf->rapport_kop_bgcolor = array('r' => 194, 'g' => 179, 'b' => 157);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, 11);
        break;
      case "70" :
        // SLV2
        $pdf->rapport_layout = 70;
        $pdf->marge = 15;
        $pdf->rapport_dontsortpie = true;
      
        $pdf->rapport_printAEXVergelijkingEur = 1;
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        //$pdf->rapport_VOLK_titel = "Vergelijkend overzicht verslagperiode";
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_geenIndex = 0;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
        $pdf->rapport_HSE_geenIndex = 0;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_valutaoverzicht = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 0;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_aantalVierDecimaal = 1;
        $pdf->rapport_OIS_geenIndex = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
        $pdf->rapport_VHO_geenIndex = 1;
      
        $pdf->rapport_PERF_liquiditeiten = 1;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_rendementText = 'Rendement over verslagperiode';
        /*
		  if(file_exists(FPDF_FONTPATH.'AgendaLight.php'))
		  {
  	    if(!isset($pdf->fonts['agenda']))
	      {
		      $pdf->AddFont('agenda','','AgendaLight.php');
		      $pdf->AddFont('agenda','B','AgendaBold.php');
		      $pdf->AddFont('agenda','I','AgendaMediumItalic.php');
          $pdf->AddFont('agenda','BI','AgendaMediumItalic.php');
	      }
			  $pdf->rapport_font = 'agenda';
		  }

		  if(file_exists(FPDF_FONTPATH.'OpenSans-Regular.php'))
		  {
  	    if(!isset($pdf->fonts['opensans']))
	      {
		      $pdf->AddFont('opensans','','OpenSans-Regular.php');
		      $pdf->AddFont('opensans','B','OpenSans-Bold.php');
		      $pdf->AddFont('opensans','I','OpenSans-Italic.php');
          $pdf->AddFont('opensans','BI','OpenSans-BoldItalic.php');
	      }
			//  $pdf->rapport_font = 'opensans';
		  }

  //    else
*/
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '8';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = '';//vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank} / {Risicoklasse}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->frontBlauw = array(40, 35, 80);//10,37,62);
        $pdf->frontRood = array(195, 14, 46);
        $pdf->cellColorDark = array(228, 229, 234);
        $pdf->cellColorLight = array(241, 243, 244);
        $pdf->rapport_grafiek_pcolor = array(0, 40, 58);
        $pdf->rapport_grafiek_icolor = array(176, 160, 122);
        $pdf->rapport_grafiek_afm = $pdf->frontRood;
      
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->rapport_factuurHeader = $__appvar['basedir'] . "/html/rapport/logo/factuurHeader.png";
        $pdf->rapport_factuurFooter = $__appvar['basedir'] . "/html/rapport/logo/factuurFooter.png";
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, 20);
        break;
      case "71" :
        // VEC
        $pdf->rapport_layout = 71;
        $pdf->marge = 8;
        $pdf->rapport_dontsortpie = true;
      
        $pdf->rapport_printAEXVergelijkingEur = 1;
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        //$pdf->rapport_VOLK_titel = "Vergelijkend overzicht verslagperiode";
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_geenIndex = 0;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
        $pdf->rapport_HSE_geenIndex = 0;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_valutaoverzicht = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_aantalVierDecimaal = 1;
        $pdf->rapport_OIS_geenIndex = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
        $pdf->rapport_VHO_geenIndex = 1;
      
        $pdf->rapport_PERF_liquiditeiten = 1;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_rendementText = 'Rendement over verslagperiode';
      
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}\n{PortefeuilleVoorzet}{Portefeuille}";
      
        $pdf->rapport_row_bg = array(242, 242, 242);
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->rapport_factuurHeader = $__appvar['basedir'] . "/html/rapport/logo/factuurHeader.png";
        $pdf->rapport_factuurFooter = $__appvar['basedir'] . "/html/rapport/logo/factuurFooter.png";
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "72" :
        //Box Consultants
        $pdf->rapport_layout = 72;
        $pdf->rowHeight = 4.3;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend. Koersen met een * gemarkeerd zijn minstens 1 dag oud.", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " . vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kaderkleur = array(12, 37, 119);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_mut_headercolor = array('r' => 0, 'g' => 120, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, $pdf->marge + 4);
        break;
      case "73" :
        // ACM
        $pdf->rapport_layout = 73;
        $pdf->marge = 8;
      
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_geenIndex = 0;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
        $pdf->rapport_HSE_geenIndex = 0;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_valutaoverzicht = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_aantalVierDecimaal = 1;
        $pdf->rapport_OIS_geenIndex = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_percentageTotaal = 1;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
        $pdf->rapport_VHO_geenIndex = 1;
      
        $pdf->rapport_PERF_liquiditeiten = 1;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_rendementText = 'Rendement over verslagperiode';
      
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontsizeSmall = 8;
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}\n{PortefeuilleVoorzet}{Portefeuille}";
      
        $pdf->rapport_regelAchtergrond = array(221, 221, 221);
        $pdf->rapport_kop_bgcolor = array('r' => 77, 'g' => 115, 'b' => 138);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
      
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "74" :
        // Findex layout
        $pdf->rapport_layout = 74;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        if (file_exists(FPDF_FONTPATH . 'calibri.php'))
        {
          if (!isset($pdf->fonts['calibri']))
          {
            $pdf->AddFont('calibri', '', 'calibri.php');
            $pdf->AddFont('calibri', 'B', 'calibrib.php');
            $pdf->AddFont('calibri', 'I', 'calibrii.php');
            $pdf->AddFont('calibri', 'BI', 'calibribi.php');
          }
          $pdf->rapport_font = 'calibri';
        }
        $pdf->rapport_fontsize = '8';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}";

        $pdf->rapport_kop_bgcolor = array('r' => 0, 'g' => 0, 'b' => 78);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
      
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_background_fill = array(200, 200, 200);
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        //$pdf->rapport_fonds_fontcolor = array('r'=>0,'g'=>73,'b'=>156);
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "75" :
        // Valyoux Family Office
        $pdf->rapport_layout = 75;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
        if(!isset($pdf->fonts['verdana']))
        {
          $pdf->AddFont('Verdana');
          $pdf->AddFont('Verdana','B','verdanab.php');
          $pdf->AddFont('verdana','I','verdanai.php');
          $pdf->AddFont('Verdana','BI','verdanaib.php');
        }
  
        $pdf->rapport_font = 'Verdana';
        $pdf->rapport_fontsize = '8';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = '';//vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
        $pdf->rapport_koptext = "{Client}";
      
        $pdf->rapport_grafiek_pcolor = array(0, 72, 107);
        $pdf->rapport_grafiek_icolor = array(176, 160, 122);
        //$pdf->rapport_grafiek_drie = array(160, 127, 64);
        $pdf->rapport_grafiek_drie = array(118, 185, 212);
        $pdf->rapport_grafiek_vier = array(167, 210, 227);
      
      
        $pdf->rapport_kop_bgcolor = array('r' => 218, 'g' => 228, 'b' => 234); //35/45/104 //33/43/105 //'r'=>88,'g'=>119,'b'=>255
        $pdf->rapport_kop_fontcolor = array('r' => 3, 'g' => 25, 'b' => 56);//array('r'=>127,'g'=>128,'b'=>132);
        $pdf->rapport_gold = array(187, 163, 111);
        $pdf->rapport_regelAchtergrond = array(249, 244, 235);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_background_fill = array(200, 200, 200);
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        //$pdf->rapport_fonds_fontcolor = array('r'=>0,'g'=>73,'b'=>156);
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->underlinePercentage = 0.8;
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "76" :
        //ANT&AMB
        $pdf->rapport_layout = 76;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
      
        $pdf->rapport_valutaoverzicht_rev = 1;
        $pdf->rapport_VOLK_titel = "Portefeuille-overzicht";
        $pdf->rapport_VOLK_volgorde_beginwaarde = 2;
        $pdf->rapport_VOLK_geensubtotaal = 1;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 1;
        $pdf->rapport_VOLK_rendement = 1;
        $pdf->rapport_VOLK_valutaoverzicht = 2;
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
        $pdf->rapport_VOLK_geenvaluta = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_indexUit = 1;
        $pdf->rapport_VHO_rendement = 0;
        $pdf->rapport_VHO_aantalVierDecimaal = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_valutaoverzicht = 2;
        $pdf->rapport_HSE_geenrentespec = 1;
        $pdf->rapport_HSE_aantalVierDecimaal = 1;
      
        $pdf->rapport_OIH_geenrentespec = 1;
      
        $pdf->rapport_MOD_valutaoverzicht = 1;
      
        $pdf->rapport_OIB_titel = "Verdeling naar vermogenscategorie";
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 0;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_titel = "Valutaverdeling";
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 0;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 2;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_geenrentespec = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 2;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_TRANS_legenda = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_decimaal2 = 0;
      
        $pdf->rapport_PERF_titel = "Vermogensontwikkeling";
        $pdf->rapport_PERF_displayType = 1;
        $pdf->rapport_PERF_jaarRendement = 1;
      
        $pdf->rapport_MUT2_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
      
        if (file_exists(FPDF_FONTPATH . 'Frutiger.php'))
        {
          if (!isset($pdf->fonts['frutiger']))
          {
            $pdf->AddFont('frutiger', '', 'Frutigerl.php');
            $pdf->AddFont('frutiger', 'B', 'Frutigerb.php');
            $pdf->AddFont('frutiger', 'R', 'Frutiger.php');
            $pdf->AddFont('frutiger', 'BI', 'Frutigerbi.php');
          }
          $pdf->rapport_font = 'frutiger';
        }
        else
        {
          $pdf->rapport_font = 'Times';
        }
      
        $pdf->rapport_fontsize = '9';
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '8';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = vertaalTekst("Productiedatum ", $pdf->rapport_taal) . date("d", mktime()) . " " . vertaalTekst($pdf->__appvar["Maanden"][date("n", mktime())], $pdf->rapport_taal) . " " . date("Y", mktime());
        $pdf->rapport_koptext = "{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
        $pdf->rapport_koptextParticipants = "{Naam1}\n{Naam2}";
      
        $pdf->rapport_paars = array(125, 41, 145);
        $pdf->rapport_oranje = array(243, 109, 56);
        //$pdf->rapport_blauw=array(25,50,95);
        $pdf->rapport_blauw = array(68, 78, 105);
        $pdf->rapport_kop_bgcolor = array('r' => $pdf->rapport_oranje[0], 'g' => $pdf->rapport_oranje[1], 'b' => $pdf->rapport_oranje[2]);
        //$pdf->rapport_kop_bgcolor = array('r'=>255,'g'=>255,'b'=>255);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);//array('r'=>0,'g'=>69,'b'=>132);
        //$pdf->rapport_kop_fontcolor = array('r'=>$pdf->rapport_paars[0],'g'=>$pdf->rapport_paars[1],'b'=>$pdf->rapport_paars[2]);
        $pdf->rapport_kop_fontstyle = '';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);//array('r'=>0,'g'=>69,'b'=>132);
      
        $pdf->rapport_kop3_fontcolor = array(0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, 20);
        break;
      case "77" :
        //Andreas Capital
        $pdf->rapport_layout = 77;
        $pdf->marge = 8;
        $pdf->top_marge = 25;
        $pdf->rapport_lichtblauw = array(0, 176, 202);
        $pdf->rapport_donkerblauw = array(11, 28, 43);
        $pdf->rapport_blauw = array(2, 75, 120);
      
        $pdf->rapport_grafiek_pcolor = $pdf->rapport_lichtblauw;
        $pdf->rapport_grafiek_icolor = $pdf->rapport_donkerblauw;
        $pdf->rapport_grafiek_afm = array(128, 128, 128);
      
        $pdf->rapport_kop_bgcolor = array('r' => $pdf->rapport_lichtblauw[0], 'g' => $pdf->rapport_lichtblauw[1], 'b' => $pdf->rapport_lichtblauw[2]);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
      
      
        if (file_exists(FPDF_FONTPATH . 'Audrey-Normal.php'))
        {
          if (!isset($pdf->fonts['audrey']))
          {
            $pdf->AddFont('audrey', '', 'Audrey-Normal.php');
            $pdf->AddFont('audrey', 'B', 'Audrey-Bold.php');
            $pdf->AddFont('audrey', 'I', 'Audrey-NormalOblique.php');
            $pdf->AddFont('audrey', 'BI', 'Audrey-BoldOblique.php');
          }
          $pdf->rapport_kopFont = 'audrey';
        }
        else
        {
          $pdf->rapport_kopFont = 'Arial';
        }
      
      
        if (file_exists(FPDF_FONTPATH . 'OpenSans-Regular.php'))
        {
          if (!isset($pdf->fonts['opensans']))
          {
            $pdf->AddFont('opensans', '', 'OpenSans-Regular.php');
            $pdf->AddFont('opensans', 'B', 'OpenSans-Bold.php');
            $pdf->AddFont('opensans', 'I', 'OpenSans-Italic.php');
            $pdf->AddFont('opensans', 'BI', 'OpenSans-BoldItalic.php');
          }
          $pdf->rapport_font = 'opensans';
        }
        else
        {
          $pdf->rapport_font = 'Arial';
        }
      
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '8';
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_voettext = '';// vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
        $pdf->rapport_voettext_rechts = '';
        $pdf->rapport_koptext = "{DepotbankOmschrijving} " . vertaalTekst("Depot", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille}\n{Naam1}\n{Naam2}";
        $pdf->rapport_koptextParticipants = "{Naam1}\n{Naam2}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        $pdf->SetAutoPageBreak(true, 20);
        break;
      case "78" :
        // care 4 capital layout
        $pdf->rapport_layout = 78;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
      
        if (file_exists(FPDF_FONTPATH . 'calibri.php'))
        {
          if (!isset($pdf->fonts['calibri']))
          {
            $pdf->AddFont('calibri', '', 'calibri.php');
            $pdf->AddFont('calibri', 'B', 'calibrib.php');
            $pdf->AddFont('calibri', 'I', 'calibrii.php');
            $pdf->AddFont('calibri', 'BI', 'calibribi.php');
          }
          $pdf->rapport_font = 'calibri';
        }
        $pdf->rapport_fontsize = '8';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 0, 'g' => 105, 'b' => 50); //35/45/104 //33/43/105 //'r'=>88,'g'=>119,'b'=>255
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);//array('r'=>127,'g'=>128,'b'=>132);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_background_fill = array(200, 200, 200);
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        //$pdf->rapport_fonds_fontcolor = array('r'=>0,'g'=>73,'b'=>156);
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "79" : //JAN
        $pdf->rapport_layout = 79;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
      
        if (file_exists(FPDF_FONTPATH . 'calibri.php'))
        {
          if (!isset($pdf->fonts['calibri']))
          {
            $pdf->AddFont('calibri', '', 'calibri.php');
            $pdf->AddFont('calibri', 'B', 'calibrib.php');
            $pdf->AddFont('calibri', 'I', 'calibrii.php');
            $pdf->AddFont('calibri', 'BI', 'calibribi.php');
          }
          $pdf->rapport_font = 'calibri';
        }
        else
        {
          $pdf->rapport_font = 'Arial';
        }
      
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = '';//vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdf->rapport_taal);
        $pdf->rapport_koptext = '';//vertaalTekst("Client",$pdf->rapport_taal)." {ClientVermogensbeheerder} / ".vertaalTekst("Rekening",$pdf->rapport_taal)." {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 89, 'g' => 89, 'b' => 89);//array('r'=>0,'g'=>51,'b'=>102);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 127, 'g' => 127, 'b' => 127);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      case "80" :
        // RDE Layout
        $pdf->rapport_layout = 80;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
        $pdf->rapport_VOLK_aantalVierDecimaal = 1;
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 0;
        $pdf->rapport_TRANS_legenda = 1;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_background_fill = array(200, 200, 200);
      
        /*
            if(file_exists(FPDF_FONTPATH.'Frutiger.php'))
            {
              if(!isset($pdf->fonts['frutiger']))
              {
                $pdf->AddFont('frutiger','','Frutigerl.php');
                $pdf->AddFont('frutiger','B','Frutigerb.php');
                $pdf->AddFont('frutiger','R','Frutiger.php');
                $pdf->AddFont('frutiger','BI','Frutigerbi.php');
              }
              $pdf->rapport_font = 'frutiger';
            }
            else
      */
        $pdf->rapport_font = 'Arial';
        $pdf->rapport_fontsize = '8';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = "{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 35, 'g' => 45, 'b' => 104); //35/45/104 //33/43/105 //'r'=>88,'g'=>119,'b'=>255
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);;//array('r'=>127,'g'=>128,'b'=>132);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        //$pdf->rapport_fonds_fontcolor = array('r'=>0,'g'=>73,'b'=>156);
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        //$pdf->SetAutoPageBreak(true,20);
        break;
      case "81" :
        // Boer & Olij / BEO layout
        $pdf->rapport_layout = 81;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 0;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data['rapportLink'];
        $pdf->rapport_VOLK_url = $data['rapportLinkUrl'];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 0;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 0;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 1;
        $pdf->rapport_taal = $data['Taal'];
        $pdf->rapport_decimaal = 2;
        if (file_exists(FPDF_FONTPATH . 'tahoma.php'))
        {
          if (!isset($pdf->fonts['tahoma']))
          {
            $pdf->AddFont('tahoma', '', 'tahoma.php');
            $pdf->AddFont('tahoma', 'B', 'tahomab.php');
            $pdf->AddFont('tahoma', 'I', 'tahoma.php');
            $pdf->AddFont('tahoma', 'BI', 'tahomab.php');
          }
          $pdf->rapport_font = 'tahoma';
          $pdf->rapport_fontsize = '9';
        }
        else
        {
          $pdf->rapport_font = 'Times';
          $pdf->rapport_fontsize = '9';
          $pdf->rapport_fontstyle = '';
        }
      
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " . vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 173, 'g' => 32, 'b' => 8);//array('r'=>245,'g'=>240,'b'=>155);
        $pdf->rapport_kop_fontcolor = array('r' => 255, 'g' => 255, 'b' => 255);//array('r'=>23,'g'=>91,'b'=>126);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = $pdf->rapport_kop_fontcolor;//array('r'=>23,'g'=>91,'b'=>126);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
        $pdf->rapport_background_fill = array(240, 240, 240);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 23, 'g' => 91, 'b' => 126);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 23, 'g' => 91, 'b' => 126);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data['Logo'];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
      default :
        $pdf->rapport_layout = 2;
        $pdf->marge = 8;
        $pdf->rapport_VOLK_volgorde_beginwaarde = 1;
        $pdf->rapport_VOLK_geensubtotaal = 0;
        $pdf->rapport_VOLK_decimaal = 2;
        $pdf->rapport_VOLK_decimaal_proc = 2;
        $pdf->rapport_VOLK_rendement = 0;
        $pdf->rapport_VOLK_valutaoverzicht = 1;
        $pdf->rapport_VOLK_link = $data[rapportLink];
        $pdf->rapport_VOLK_url = $data[rapportLinkUrl];
      
        $pdf->rapport_VHO_geenvaluta = 0;
        $pdf->rapport_VHO_geensubtotaal = 0;
        $pdf->rapport_VHO_volgorde_beginwaarde = 1;
        $pdf->rapport_VHO_decimaal_proc = 1;
        $pdf->rapport_VHO_decimaal = 2;
        $pdf->rapport_VHO_valutaoverzicht = 1;
        $pdf->rapport_VHO_rendement = 0;
      
        $pdf->rapport_OIB_specificatie = 1;
        $pdf->rapport_OIB_decimaal = 2;
        $pdf->rapport_OIB_rendement = 0;
        $pdf->rapport_OIB_valutaoverzicht = 0;
      
        $pdf->rapport_OIV_rendement = 0;
        $pdf->rapport_OIV_decimaal = 2;
        $pdf->rapport_OIV_decimaal_proc = 1;
      
        $pdf->rapport_OIS_valutaoverzicht = 1;
        $pdf->rapport_OIS_rendement = 1;
        $pdf->rapport_OIS_decimaal = 2;
        $pdf->rapport_OIS_zorgplichtpercentage = 1;
      
        $pdf->rapport_OIR_rendement = 1;
        $pdf->rapport_OIR_valutaoverzicht = 1;
        $pdf->rapport_OIR_decimaal = 2;
        $pdf->rapport_OIR_geenrentespec = 1;
      
        $pdf->rapport_HSE_volgorde_beginwaarde = 1;
        $pdf->rapport_HSE_rendement = 1;
        $pdf->rapport_HSE_valutaoverzicht = 1;
      
        $pdf->rapport_TRANS_procent = 1;
        $pdf->rapport_TRANS_decimaal = 2;
      
        $pdf->rapport_inprocent = 0;
        $pdf->rapport_taal = $data[Taal];
        $pdf->rapport_decimaal = 2;
      
        $pdf->rapport_font = 'Times';
      
        $pdf->rapport_fontsize = '9';
        $pdf->rapport_fontstyle = '';
        $pdf->rapport_voetfontsize = '6';
        $pdf->rapport_voettext = vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend", $pdf->rapport_taal);
        $pdf->rapport_koptext = vertaalTekst("Client", $pdf->rapport_taal) . " {ClientVermogensbeheerder} / " . vertaalTekst("Rekening", $pdf->rapport_taal) . " {PortefeuilleVoorzet}{Portefeuille} / {Depotbank}\n{Naam1}\n{Naam2}";
      
        $pdf->rapport_kop_bgcolor = array('r' => 245, 'g' => 240, 'b' => 155);
        $pdf->rapport_kop_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop_fontstyle = 'b';
      
        $pdf->rapport_kop2_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop2_fontstyle = '';
      
        $pdf->rapport_kop3_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop3_fontstyle = 'bi';
      
        $pdf->rapport_kop4_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_kop4_fontstyle = 'b';
      
        $pdf->rapport_default_fontcolor = array('r' => 0, 'g' => 0, 'b' => 0);
      
        $pdf->rapport_fonds_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
      
        $pdf->rapport_subtotaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_subtotaal_omschr_fontstyle = '';
        $pdf->rapport_subtotaal_fontcolor = array('r' => 0);
        $pdf->rapport_subtotaal_fontstyle = 'b';
      
        $pdf->rapport_totaal_omschr_fontcolor = array('r' => 38, 'g' => 73, 'b' => 156);
        $pdf->rapport_totaal_omschr_fontstyle = '';
        $pdf->rapport_totaal_fontcolor = array('r' => 0);
        $pdf->rapport_totaal_fontstyle = 'b';
      
        $pdf->rapport_valuta_voorzet = "Waarden ";
        $pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling} {PortefeuilleVoorzet}{Rekening}";
      
        $pdf->rapport_logo = $__appvar['basedir'] . "/html/rapport/logo/" . $data[Logo];
        $pdf->SetLeftMargin($pdf->marge);
        $pdf->SetRightMargin($pdf->marge);
        $pdf->SetTopMargin($pdf->marge);
        break;
    }
  }
	if($_POST['anoniem']==1)
  	$pdf->rapport_liquiditeiten_omschr = "{Tenaamstelling}";
	return $pdf->rapport_layout;
}

?>