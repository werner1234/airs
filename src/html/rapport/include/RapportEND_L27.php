<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/12/14 17:46:24 $
File Versie					: $Revision: 1.3 $

$Log: RapportEND_L27.php,v $
Revision 1.3  2019/12/14 17:46:24  rvv
*** empty log message ***

Revision 1.2  2019/01/23 07:45:51  rvv
*** empty log message ***

Revision 1.1  2019/01/19 13:54:10  rvv
*** empty log message ***

Revision 1.1  2019/01/16 08:41:15  rvv
*** empty log message ***

Revision 1.6  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.5  2016/04/30 15:33:27  rvv
*** empty log message ***

Revision 1.4  2015/02/15 10:36:54  rvv
*** empty log message ***

Revision 1.3  2015/02/07 20:37:51  rvv
*** empty log message ***

Revision 1.2  2015/01/17 18:32:01  rvv
*** empty log message ***

Revision 1.1  2015/01/11 12:48:50  rvv
*** empty log message ***

Revision 1.1  2014/05/05 15:52:25  rvv
*** empty log message ***

Revision 1.3  2014/04/26 16:43:08  rvv
*** empty log message ***

Revision 1.2  2014/04/23 16:18:44  rvv
*** empty log message ***

Revision 1.1  2014/01/22 17:01:30  rvv
*** empty log message ***

Revision 1.9  2014/01/18 17:27:23  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");

class RapportEND_L27
{
	function RapportEND_L27($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
    $this->rapportageDatum = $rapportageDatum;
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "";
    $this->portefeuille=$portefeuille;

    $this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTdDQTYzMjZDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTdDQTYzMjdDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo5N0NBNjMyNEM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo5N0NBNjMyNUM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PqN37VQAAAMAUExURQAAAP///wCQAACMAACLAACIAACHAACEAACDAACAAAB+AAB6AAB5AAB2AAB0AABzAABxAABvAABtAABoAABlAABfAABdAABbAABZAABWAABUAABRAABNAAAoAAAnAAAfAAASAAaRAgaOAgaKAwaJAweHAwiOBAeMBAeLBAyLBQqHBROSCRaYCxWWCxOZBxSSCRiXChiZCxeXCxqZDBmYDBubDRybDB+dDh2dDiCgDx6fDySsESKhECWtEiWrEiOiESKkDSKiDSGiDSGhDSOlDiKjDiKiDyKhDyasESaqESWqESatEiirEiarEiWkESeuEyiuEyesEyisEyWjEimvFCmtFCqtFCelEyywFSuwFSimFCuvFiqnFS2wF3HKYnnLa3vMbSuvEiutEyuuFCytFC+xFS6vFSyuFSyqFSypFS2vFi6vFi6rFjCxFy6wFzGxGDKxGC+wGDOyGTSxHTezHj63JT22JV3BSWDETF7BTGPET2TGUWPEUGXFU3bLZ3rMaXnMaXnLaHrMa4PPdYXQdonSe4vTfYzTfovTfozUf4/UgY/VgpDVgi6rFC+sFTazGTSyGTWzGjazGji0Gze0GzizGzm1HDm2HTazHDqyHjy3IUq8L17DSF/ESWPGTWPGTmLETWTGT47Ufzq1HDu2HTy2HT23Hj63HkC4Hz+4H0G5IEK4IkO7HUG3H0K5IEa7IUS6IUO6IUW7Ik2/KlPAMVbDNWHEQmnITG/KUofTcHDMT3DMUYDRZoXTaYnVb6LejqLdjaLdj6PdkHrQWQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbA7uAAAAKNSURBVHjaYmAkETCMasANuLj5SNLAZe6nyUOCBi732XNmmXETrYHLeW5PT0upMS+RGric5/U0lSUW52rJEKWB021BT11qdnZ6tqkwMRo4HaHqo2P1RYnQwOlY0dOQm52VHh1hzCdPWAOX00KI+hSgern/BDUA1Xc3pmanp4dFQtQT0MDlNL+rMS07PRmuHr8GiPosoPpkEz7Z/4iYZmJhYcXmX9f5XfW5WelhYSEmvFD1YA0sejNXK/Jgmu9Qtag+LyU9LASoXu4/IrUy9x0+sH+bEi+6escFixrygM4JQ1YP1MDSt/fQni2b16nxo7l/QSdQfRiaekYGVr1dB3esWb9h01p1flT17Qmx6WGhQajqGRnYevftXLNpw8Z1G5drCCCpr25vyksGqg9FVQ/U4Ld7+8YN69atWLlyqTJMB6dTdUd9YVhYUFCAAT+KeqAfWHO2bly3asXK5UuXTVURhIZnFVB9UliQf4ABH6p6UCixZqzfuGL58qVLFk+fpCoIVd8UGxoKVi/7H6MQEGPP3Lh8GVD95ClTJqgK8XE51HQkFALV+2BRD444cfaYVUsXT5s8ZdKEibnami61bUD1QYG+/kbo7oElDQmOqGXTJ0+aNKGooCR1RlxbfGEALvXQxCfBGb5k0kSg+vyJ5a3NlWFA9T6BOtjUw1KrBGfwFKD63Nzs5OSAgKAgHzsPHUz3IydvCU77/oLcvGxQ2gQ6x9Ybh3pEfpDgsikoBGUtkHc9capHykCS3Ja56aDE4OPrZYtTPXKOk+S2igUmBl87L1tdrP7FKFuluC3C/IHq7XT5cZmPlqeleEwDPKytdfCoRy8E+LR0DLXwqccoNaRFRGQU/o/WooNeA0CAAQAWlObj4AgonwAAAABJRU5ErkJggg==');
    $this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpDOTFCRkQ2ODM5QzVFMjExOTZBRThFRDMyQTE4OUYwRSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFOEY4RkE4NUM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFOEY4RkE4NEM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNBMUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM5MUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIi8+IDwvcmRmOkRlc2NyaXB0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');

	}

  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }


  function writeRapport()
	{
	  global $__appvar;

    $this->pdf->rapport_titel = "";
    $this->pdf->AddPage('P');
    $this->pdf->templateVars['ENDPaginas']=$this->pdf->page;
    $this->pdf->frontPage=true;
    $this->DB=new DB();
    $fontsize=11;
    $extraMarge=15;
    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight = 5;
    
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
    $this->pdf->rowHeight = 5;
    $this->pdf->SetWidths(array($extraMarge,(210-($this->pdf->marge+$extraMarge)*2)));
		$this->pdf->SetAligns(array("L","L"));
    $this->pdf->ln();
    
    $this->pdf->SetFont($this->pdf->rapport_font,"",$fontsize);
    
    
    $query = "SELECT CRM_naw.* FROM CRM_naw WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'  ";

    $this->DB->SQL($query);
    $crmData = $this->DB->lookupRecord();
    /*
    foreach($crmData as $key=>$value)
    {
      if($crmData[$key]=='')
        $crmData[$key]=$key;
    }
    */
   // listarray($crmData);
    //listarray();exit;
    
    $this->pdf->SetY(42);
    $this->pdf->row(array('',$crmData['verzendAanhef']));
    $this->pdf->row(array('',$crmData['verzendAdres']));
    $plaats=$crmData['verzendPc'];
    if($crmData['verzendPlaats'] != '') $plaats.=" ".$crmData['verzendPlaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$crmData['verzendLand']));
    
    $this->pdf->SetY(80);
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$fontsize);
    $this->pdf->row(array('',"Belangrijke informatie:
Beleggen wij nog op de juiste wijze voor u?"));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$fontsize);

    $this->pdf->SetWidths(array($extraMarge,50,150));
    $this->pdf->ln();
    $this->pdf->ln();
    $this->pdf->row(array('',"Rekeninghouder(s):",trim($crmData['titel'].' '.$crmData['voorletters'].' '.$crmData['tussenvoegsel'].' '.$crmData['achternaam'])));
    $part=trim($crmData['part_titel'].' '.$crmData['part_voorletters'].' '.$crmData['part_tussenvoegsel'].' '.$crmData['part_achternaam']);
    if($part<>'')
      $this->pdf->row(array('',"",$part));
    $this->pdf->ln();
    $this->pdf->row(array('','Rekening:',$crmData['custodianRekeningNr']));
    $this->pdf->row(array('','','Vermogensbeheer'));
    $this->pdf->ln();
    $this->pdf->row(array('','Datum:',date("d")." ". vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ". date("Y")));
    $this->pdf->ln();
    $this->pdf->SetWidths(array($extraMarge,(210-($this->pdf->marge+$extraMarge)*2)));
    
    $this->pdf->ln(12);
    $this->pdf->row(array('','Geachte '.$crmData['verzendPaAanhef'].',

Deze brief bevat belangrijke informatie over uw beleggingsrekening en de manier waarop wij voor u beleggen. Het is in uw belang om uw persoonlijke informatie te controleren en ons te informeren als bepaalde informatie niet (meer) klopt.  Doet u dat niet en klopt de informatie niet meer dan is de kans groot dat voor u wordt belegd op een wijze die niet (meer) bij u past en dat u daardoor schade lijdt.'));
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$fontsize);
    $this->pdf->ln();
    $this->pdf->row(array('','Wat moet u doen?'));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$fontsize);
    $this->pdf->row(array('','Zijn uw persoonlijke omstandigheden veranderd? Heeft u andere doelstellingen of is uw financiële situatie anders geworden? Geef dat dan zo snel mogelijk door aan uw accountmanager. Wij zullen dan laten weten of het huidige risicoprofiel nog steeds bij u past of juist moet worden bijgesteld.

Klopt uw persoonlijke informatie nog? Dan hoeft u niets te doen. Wij blijven dan volgens het huidige risicoprofiel voor u beleggen.'));


    $this->pdf->addPage('P');
    $this->pdf->frontPage=true;
    $this->pdf->ln(10);

    $this->pdf->SetFont($this->pdf->rapport_font,"B",$fontsize);
    $this->pdf->ln();
    $this->pdf->row(array('','Controleer uw persoonlijke informatie'));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,"U",$fontsize);
    $this->pdf->row(array('','U heeft ons persoonlijke informatie gegeven'));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$fontsize);
    $this->pdf->row(array('','Uw risicoprofiel is gebaseerd op uw antwoorden op onze vragenlijst. Dat is uw persoonlijke informatie. Zo heeft u informatie gegeven over uw beleggingsdoel, uw financiële situatie en uw risicobereidheid.
Klopt deze informatie niet meer? Zijn uw persoonlijke omstandigheden gewijzigd?
Geef dat dan zo snel mogelijk aan uw accountmanager door.'));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,"U",$fontsize);
    $this->pdf->row(array('','Voorkom dat verkeerd wordt belegd'));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$fontsize);
    $this->pdf->row(array('','Als er iets structureel verandert in uw situatie, dan is de kans aanwezig dat uw huidige risicoprofiel niet langer bij u past en dat uw doelstellingen niet langer kunnen worden gerealiseerd. Daarom is het belangrijk dat u het aan ons doorgeeft als er iets is veranderd. Als u dit niet op tijd doet, komt eventuele schade voor uw eigen rekening. Wij kunnen zonder uw medewerking namelijk niet voor u controleren of uw persoonlijke informatie volledig en actueel is.'));

    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$fontsize);
    $this->pdf->row(array('','Uw risicoprofiel'));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,"",$fontsize);
    $this->pdf->row(array('','In overleg met u hebben wij besloten dat uw risicoprofiel '.$this->pdf->portefeuilledata['Risicoklasse'].' is.

Hieronder ziet u de standaard verdeling van uw risicoprofiel. De verdeling van uw eigen portefeuille zal meestal (licht) afwijken van deze standaard verdeling. De meest recente verdeling van uw portefeuille kunt u terugvinden in de kolom werkelijk en in  uw kwartaalrapportage.
'));
    $this->pdf->ln();
    $this->toonZorgplicht($extraMarge);
    $this->pdf->SetAligns(array('R','L','L'));
    $this->pdf->SetWidths(array($extraMarge,(210-($this->pdf->marge+$extraMarge)*2)));
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$fontsize);
    $this->pdf->ln();
    $this->pdf->row(array('','Deze beleggingsverdeling past bij uw risicoprofiel'));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,"",$fontsize);
    $this->pdf->row(array('','De bovenstaande verdeling past bij uw beleggingsdoelstellingen, beleggingshorizon, financiële situatie, risicobereidheid, beleggingskennis en -ervaring. Wij gaan dan wel uit van normale marktomstandigheden.

Wij vertrouwen erop u hiermee voldoende te hebben geïnformeerd.'));

    $this->pdf->ln();
    $logo=$__appvar['basedir']."/html/rapport/logo/ondertekening.png";
    if(is_file($logo))
    {
      $factor=0.04;
      $this->pdf->Image($logo, $this->pdf->getX()+20, $this->pdf->getY()+10, 768*$factor, 728*$factor);
    }
    $this->pdf->row(array('','Met vriendelijke groet,








Mark Sombekke
Directeur operationele zaken
'));

    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    $this->pdf->rowHeight=$rowHeightBackup;
	}

  function toonZorgplicht($extraMarge)
  {
    global $__appvar;
    $DB=new DB();

    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];

    $this->totaalWaarde=$totaalWaarde;
    $categorieWaarden=array();
    /*
    if(round($this->totaalWaarde,2) <> 0)
    {
      // listarray();exit;
      $query = "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / " . $this->totaalWaarde . ") as percentage,
Zorgplichtcategorien.Omschrijving,
ZorgplichtPerBeleggingscategorie.Zorgplicht
FROM TijdelijkeRapportage
JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'
INNER JOIN Zorgplichtcategorien ON ZorgplichtPerBeleggingscategorie.Zorgplicht = Zorgplichtcategorien.Zorgplicht AND Zorgplichtcategorien.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE TijdelijkeRapportage.Portefeuille =  '" . $this->portefeuille . "' AND
 TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "
GROUP BY ZorgplichtPerBeleggingscategorie.Zorgplicht
ORDER BY ZorgplichtPerBeleggingscategorie.Zorgplicht";
      $DB->SQL($query); //echo $query;exit;
      $DB->Query();
      while ($data = $DB->nextRecord())
      {
        $categorieWaarden[$data['Zorgplicht']] = $data['percentage'] * 100;
        $categorieOmschrijving[$data['Zorgplicht']] = $data['Omschrijving'];
      }
    }
  */
    if(round($this->totaalWaarde,2) <> 0)
    {
      $query = "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / " . $this->totaalWaarde . " as percentage,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.beleggingscategorie,
ZorgplichtPerBeleggingscategorie.Zorgplicht
FROM TijdelijkeRapportage
JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'
WHERE TijdelijkeRapportage.Portefeuille =  '" . $this->portefeuille . "' AND
 TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "
GROUP BY ZorgplichtPerBeleggingscategorie.Zorgplicht
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde";
      $DB->SQL($query);
      $DB->Query();
      $categoriePerZorgplicht=array();
      while ($data = $DB->nextRecord())
      {
        $categorieWaarden[$data['Zorgplicht']] = $data['percentage'] * 100;
        $categorieOmschrijving[$data['Zorgplicht']] = $data['beleggingscategorieOmschrijving'];
        $categoriePerZorgplicht[$data['Zorgplicht']] = $data['beleggingscategorie'];
      }
    }

    $zorgplicht = new Zorgplichtcontrole();
    $zpwaarde=$zorgplicht->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);

    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;

   // krsort($tmp);

//listarray($zpwaarde['conclusie']);
    //listarray($tmp);exit;

    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    $beginY=$this->pdf->getY();

    $this->pdf->SetWidths(array($extraMarge,40,20,20,20,20));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('beleggingscategorie',$this->pdf->rapport_taal),vertaalTekst('minimaal',$this->pdf->rapport_taal),
                      vertaalTekst('maximaal',$this->pdf->rapport_taal),vertaalTekst("werkelijk",$this->pdf->rapport_taal),vertaalTekst("conclusie",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    //foreach ($tmp as $index=>$regelData)

    //  $this->pdf->MemImage($this->checkImg,100,$this->pdf->getY(),10,10);
    foreach ($categorieWaarden as $cat=>$percentage)
    {
      if($tmp[$cat][2])
        $risicogewogen=$tmp[$cat][2]."%";
      else
        $risicogewogen='';
      //if($zpwaarde['categorien'][$cat]['Minimum'])
      $min=$this->formatGetal($zpwaarde['categorien'][$cat]['Minimum'],0)."%";
      //else
      //   $min='';
      //if($zpwaarde['categorien'][$cat]['Maximum'])
      $max=$this->formatGetal($zpwaarde['categorien'][$cat]['Maximum'],0)."%";
      // else
      //   $max='';

      if($tmp[$cat][5]=='Voldoet')
        $this->pdf->MemImage($this->checkImg,120+$extraMarge,$this->pdf->getY(),3.9,3.9);
      else
        $this->pdf->MemImage($this->deleteImg,120+$extraMarge,$this->pdf->getY(),3.9,3.9);


      $this->pdf->row(array('',$categorieOmschrijving[$cat],$min,$max,$this->formatGetal($categorieWaarden[$cat],1)."%"));//$risicogewogen
    }
    $this->pdf->Rect($this->pdf->marge+$extraMarge,$beginY,120,count($categorieWaarden)*$this->pdf->rowHeight+$this->pdf->rowHeight);
  }

}
?>