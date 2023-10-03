<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/06/29 18:24:12 $
File Versie					: $Revision: 1.15 $

$Log: RapportATT_L70.php,v $
Revision 1.15  2019/06/29 18:24:12  rvv
*** empty log message ***

Revision 1.14  2019/06/19 15:59:09  rvv
*** empty log message ***

Revision 1.13  2018/03/25 10:16:55  rvv
*** empty log message ***

Revision 1.12  2018/01/18 13:36:02  rvv
*** empty log message ***

Revision 1.11  2016/09/18 08:49:02  rvv
*** empty log message ***

Revision 1.10  2016/08/13 16:55:26  rvv
*** empty log message ***

Revision 1.9  2016/07/08 07:10:51  rvv
*** empty log message ***

Revision 1.8  2016/07/08 06:48:18  rvv
*** empty log message ***

Revision 1.7  2016/07/08 06:37:58  rvv
*** empty log message ***

Revision 1.6  2016/07/07 19:20:50  rvv
*** empty log message ***

Revision 1.5  2016/07/07 15:37:35  rvv
*** empty log message ***

Revision 1.4  2016/06/29 16:04:07  rvv
*** empty log message ***

Revision 1.3  2016/06/08 15:40:53  rvv
*** empty log message ***

Revision 1.2  2016/05/29 10:19:26  rvv
*** empty log message ***

Revision 1.1  2016/05/22 18:49:26  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportATT_L70
{
	function RapportATT_L70($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Beleggingsresultaat lopend jaar";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->tweedeStart();
    $this->indexHistorie =array();


	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";

	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     exit;
	 }
	}

	function tweedeStart()
	{
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) == db2jul($this->rapportageDatumVanaf))
	  {
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  }
	  else
	  {
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
	   if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01" && $this->pdf->engineII == false)
	   {
	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,"$RapStartJaar-01-01",true);
      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,"$RapStartJaar-01-01");
      $this->extraVulling = true;
	   }
	  }
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}



	function writeRapport()
	{
	  global $__appvar;

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

	 $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));


	 	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];
	 // $this->categorieOmschrijving=array('LIQ'=>'Liquiditeiten','ZAK'=>'Zakelijke waarden','VAR'=>'Vastrentende waarden','Liquiditeiten'=>'Liquiditeiten');




//listarray($this->categorieVolgorde);
		// voor data
		$this->pdf->widthA = array(0.8725,82.8922,21.8137,4.3627,21.8137,4.3627,21.8137,4.3627,21.8137,4.3627,21.8137,4.3627,21.8137,4.3627,21.8137,4.3627);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(0.9502,90.2669,28.5053,9.5018,28.5053,109.2705);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();
    $this->pdf->templateVars['ATTPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['ATTPaginas']=$this->pdf->rapport_titel;

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];


  $index=new indexHerberekening();
  $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
  
  $this->indexHistorie = $index->getWaarden($this->pdf->PortefeuilleStartdatum ,$this->rapportageDatum ,$this->portefeuille);

//  $indexData = $this->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
//listarray($indexData);
//exit;
$i=0;
foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $rendamentWaarden[] = $data;
    $grafiekData['Datum'][] = $data['datum'];
    $grafiekData['Index'][] = $data['index']-100;
    $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
  //  foreach ($data['categorieVerdeling'] as $categorie=>$waarde)
  
  $barGraph['Index'][$data['datum']]['leeg']=0;
    foreach ($data['extra']['cat'] as $categorie=>$waarde)
    {
      if($categorie=='LIQ')
        $categorie='Liquiditeiten';

      $barGraph['Index'][$data['datum']][$categorie] = $waarde/$data['waardeHuidige']*100;
      if($waarde <> 0)
        $categorien[$categorie]=$categorie;
    }
  }
}


		$q="SELECT Beleggingscategorie,BeleggingscategorieOmschrijving as Omschrijving,beleggingscategorieVolgorde FROM TijdelijkeRapportage WHERE Portefeuille='".$this->portefeuille."' AND Beleggingscategorie <>'' GROUP BY Beleggingscategorie  ORDER BY beleggingscategorieVolgorde asc"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')
    /*
    $q="SELECT Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerVermogensbeheerder.Vermogensbeheerder
FROM
CategorienPerVermogensbeheerder
Inner Join Beleggingscategorien ON CategorienPerVermogensbeheerder.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE CategorienPerVermogensbeheerder.Vermogensbeheerder='$beheerder' AND Beleggingscategorien.Beleggingscategorie IN('".implode("','",$categorien)."')
ORDER BY Beleggingscategorien.Afdrukvolgorde desc"; */
		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		{
		  $this->categorieVolgorde[$data['Beleggingscategorie']]=$data['Beleggingscategorie'];
		  $this->categorieOmschrijving[$data['Beleggingscategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
		}


$grafiekData['Datum'][]="$RapStartJaar-12-01";
   
   if(count($rendamentWaarden) > 0)
   {
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->underlinePercentage=0.8;
        $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);


        $totaalRendament=100;
        $totaalRendamentIndex=100;
		    foreach ($rendamentWaarden as $row)
		    {
			//		echo $this->rapportageDatumVanaf ." -> ". $row['datum']."<br>\n";

					$perf=$this->performance($this->portefeuille, $this->tweedePerformanceStart ,$row['datum']);
		      $datum = db2jul($row['datum']);

          $this->pdf->CellBorders = array();
          
           $this->pdf->fillCell = array($this->pdf->cellColorLight,
                                        $this->pdf->cellColorDark,
                                        $this->pdf->cellColorLight,
                                        $this->pdf->cellColorDark,
                                        $this->pdf->cellColorLight,
                                        $this->pdf->cellColorDark,
                                        $this->pdf->cellColorLight,
                                        $this->pdf->cellColorDark,
                                        $this->pdf->cellColorLight,
                                        $this->pdf->cellColorDark,
                                        $this->pdf->cellColorLight,
                                        $this->pdf->cellColorDark,
                                        $this->pdf->cellColorLight);
      
		      $this->pdf->row(array(date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal) ,
		                           $this->formatGetal($row['waardeBegin'],2),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],2),
		                           $this->formatGetal($row['gerealiseerd']+$row['ongerealiseerd'],2),
		                           $this->formatGetal($row['opbrengsten'],2),
		                           $this->formatGetal($row['kosten'],2),
		                           $this->formatGetal($row['rente'],2),
		                           $this->formatGetal($row['resultaatVerslagperiode'],2),
		                           $this->formatGetal($row['waardeHuidige'],2),
		                           $this->formatGetal($perf,2).' %'));
                unset($this->pdf->fillCell);
          $this->pdf->ln(0.5);

		                           if(!isset($waardeBegin))
		                             $waardeBegin=$row['waardeBegin'];
		                           $totaalWaarde = $row['waardeHuidige'];
		                           $totaalResultaat += $row['resultaatVerslagperiode'];
		                           $totaalGerealiseerd += $row['gerealiseerd'];
		                           $totaalOngerealiseerd += $row['ongerealiseerd'];
		                           $totaalOpbrengsten += $row['opbrengsten'];
		                           $totaalKosten += $row['kosten'];
		                           $totaalRente += $row['rente'];
		                           $totaalStortingenOntrekkingen += $row['stortingen']-$row['onttrekkingen'];
		                           $totaalRendament = $perf;

		    $n++;
        $i++;
		    }
		    $this->pdf->fillCell=array();


            
            $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','TS');
            $this->pdf->row(array('','','','','','','','','','','','')); 
            $this->pdf->SetY($this->pdf->GetY()-4);


        $this->pdf->ln(3);
        
        //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','UU');
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array();
		    $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
		                           $this->formatGetal($waardeBegin,2),
		                           $this->formatGetal($totaalStortingenOntrekkingen,2),
		                           $this->formatGetal($totaalGerealiseerd+$totaalOngerealiseerd,2),
		                           $this->formatGetal($totaalOpbrengsten,2),
		                           $this->formatGetal($totaalKosten,2),
		                           $this->formatGetal($totaalRente,2),
		                           $this->formatGetal($totaalResultaat,2),
		                           $this->formatGetal($totaalWaarde,2),
		                           $this->formatGetal($totaalRendament,2).' %'
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		  }
    $this->addPerfGrafiek();
    $this->addVermogensverloop();
	}


function formatGetalLength ($getal,$decimaal,$gewensteLengte)
{
 $lengte = strlen(round($getal));
 if($getal < 0)
  $lengte --;
 $mogelijkeDecimalen = $gewensteLengte - $lengte;
 if($lengte >$gewensteLengte)
   $decimaal = 0;
 elseif ($decimaal > $mogelijkeDecimalen)
   $decimaal = $mogelijkeDecimalen;
 return number_format($getal,$decimaal,',','');
}

	function performance($portefeuille,$datumBegin,$datumEind)
	{
    $beginwaarde=0;
    $eindwaarde=0;
    if(substr($datumBegin,5,5)=='01-01')
      $startJaar=true;
    else
      $startJaar=false;
    $gegevens=berekenPortefeuilleWaarde($portefeuille,$datumBegin,$startJaar);
    foreach($gegevens as $waarde)
      $beginwaarde+=$waarde['actuelePortefeuilleWaardeEuro'];
    $gegevens=berekenPortefeuilleWaarde($portefeuille,$datumEind,false);
    foreach($gegevens as $waarde)
      $eindwaarde+=$waarde['actuelePortefeuilleWaardeEuro'];

    if($datumBegin == $this->pdf->PortefeuilleStartdatum)
      $weegDatum=date("Y-m-d",db2jul($datumBegin)+24*3600);
    else
      $weegDatum=$datumBegin;


    $DB=new DB();
		$query = "SELECT ".
			"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS totaal1, ".
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal2 ".
			"FROM  (Rekeningen, Portefeuilles)
	     Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
			"WHERE ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
			"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
		$DB->SQL($query);
		$DB->Query();
		$weging = $DB->NextRecord();
		
		$gemiddelde = $beginwaarde + $weging['totaal1'];
		if($gemiddelde <> 0)
			$performance = ((($eindwaarde - $beginwaarde) - $weging['totaal2']) / $gemiddelde) * 100;

    return $performance;
	}

  function addPerfGrafiek()
  {
   // return '';
    global $__appvar;
    include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");
    $portIndex=1;
    $indexIndex=1;
  

   // $stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum);
   // listarray($stdev);
   // $stdev->addReeks('totaal');
    $maanden=array(0,'jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    foreach($this->indexHistorie  as $maandData)
    {
      $juldate=db2jul($maandData['datum']);
      $portIndex=(1+$maandData['performance']/100)*$portIndex;
      $perfGrafiek['portefeuille'][]=($portIndex-1)*100;
      $perfGrafiek['datum'][]= $maanden[date('n',$juldate)].' '.date("y",$juldate);
    }
    /*
    foreach($stdev->reeksen['totaal'] as $datum=>$perfData)
    {
      $juldate=db2jul($datum);
      $portIndex=(1+$perfData['perf']/100)*$portIndex;
      $perfGrafiek['portefeuille'][]=($portIndex-1)*100;
      $perfGrafiek['datum'][]= $maanden[date('n',$juldate)].' '.date("y",$juldate);
    }

    //echo serialize($perfGrafiek);
    $perfGrafiek=unserialize('a:2:{s:12:"portefeuille";a:140:{i:0;d:0.1791319671794244783313843072392046451568603515625;i:1;d:0.8818242943890997054268154897727072238922119140625;i:2;d:1.45453247223488180139838732429780066013336181640625;i:3;d:1.7680334693187749195431024418212473392486572265625;i:4;d:1.8132321487193348019673067028634250164031982421875;i:5;d:3.26346058715127895766272558830678462982177734375;i:6;d:4.17739377357266850054884343990124762058258056640625;i:7;d:4.9667170528619575264883678755722939968109130859375;i:8;d:5.12504855792144109472019408713094890117645263671875;i:9;d:5.8616750061962985540731096989475190639495849609375;i:10;d:4.84976039303928185830727670690976083278656005859375;i:11;d:5.465467988577632496571823139674961566925048828125;i:12;d:6.80352632071035134941894284565933048725128173828125;i:13;d:7.41450587531844451660845152218826115131378173828125;i:14;d:7.82131710986586625722338794730603694915771484375;i:15;d:7.89096725650841879229346886859275400638580322265625;i:16;d:7.27261850761362627082462495309300720691680908203125;i:17;d:5.2952399645926018223462961032055318355560302734375;i:18;d:5.3272875633763039360246693831868469715118408203125;i:19;d:5.6289802505879560357016089255921542644500732421875;i:20;d:6.577923957324127712809058721177279949188232421875;i:21;d:8.387123160372556895936213550157845020294189453125;i:22;d:9.115915671224893657154098036698997020721435546875;i:23;d:9.319785832549797532919910736382007598876953125;i:24;d:9.870138060312317662692294106818735599517822265625;i:25;d:10.94167031686146174251916818320751190185546875;i:26;d:9.659077635540459283447489724494516849517822265625;i:27;d:9.3525103145414067995488949236460030078887939453125;i:28;d:10.0749021234144198189142116461880505084991455078125;i:29;d:11.58117746884739318602441926486790180206298828125;i:30;d:13.1715682756843310841077254735864698886871337890625;i:31;d:12.337384603808576599703883402980864048004150390625;i:32;d:11.545193977242075078493144246749579906463623046875;i:33;d:10.673865425351692692856886424124240875244140625;i:34;d:11.39869451518169540804592543281614780426025390625;i:35;d:7.429825193904893154694946133531630039215087890625;i:36;d:6.82122229460435658410233372705988585948944091796875;i:37;d:2.3844255284672044581384398043155670166015625;i:38;d:1.3112645471149875220362446270883083343505859375;i:39;d:0.061492285798436796540045179426670074462890625;i:40;d:2.93590949187458871705302954069338738918304443359375;i:41;d:3.38170385205203150036368242581374943256378173828125;i:42;d:-0.1302499930833977970223713782615959644317626953125;i:43;d:-1.89625888979778611798110432573594152927398681640625;i:44;d:0.5821590825700884153093284112401306629180908203125;i:45;d:-6.460270768764065252298678387887775897979736328125;i:46;d:-12.0731917623796309868566822842694818973541259765625;i:47;d:-13.867960598546357431359865586273372173309326171875;i:48;d:-13.7942514400325677570435800589621067047119140625;i:49;d:-15.1196513882604026690614773542620241641998291015625;i:50;d:-19.20291228922901183295834925957024097442626953125;i:51;d:-17.226496413310176336608492420054972171783447265625;i:52;d:-14.6942594904371777175811075721867382526397705078125;i:53;d:-10.76070049857309385288317571394145488739013671875;i:54;d:-10.026571951096062917940798797644674777984619140625;i:55;d:-6.19371197746751978030488317017443478107452392578125;i:56;d:-2.20930882404470896318571249139495193958282470703125;i:57;d:-0.013146817499853913346896661096252501010894775390625;i:58;d:-0.9381381373601893614022628753446042537689208984375;i:59;d:-0.95234872033145290259881221572868525981903076171875;i:60;d:0.2652317275152693554218785720877349376678466796875;i:61;d:3.63526877906623635539062888710759580135345458984375;i:62;d:3.79085010993456261729761536116711795330047607421875;i:63;d:7.88731852255859511302560349577106535434722900390625;i:64;d:9.27991227040170230111471028067171573638916015625;i:65;d:6.062773050975156508002328337170183658599853515625;i:66;d:5.43507616519900427221045902115292847156524658203125;i:67;d:7.022834775006625562809858820401132106781005859375;i:68;d:7.47816023953309372274134148028679192066192626953125;i:69;d:10.793578309685614158297539688646793365478515625;i:70;d:11.9803917680044502702685349504463374614715576171875;i:71;d:8.124159026444122133625569404102861881256103515625;i:72;d:9.079208212375110775838038534857332706451416015625;i:73;d:10.2776970583382531998495323932729661464691162109375;i:74;d:10.5646703639795891405128713813610374927520751953125;i:75;d:11.182167627536632181772802141495048999786376953125;i:76;d:12.6287032075552030363496669451706111431121826171875;i:77;d:12.57050902139784653854803764261305332183837890625;i:78;d:9.773993818666927069216399104334414005279541015625;i:79;d:10.08028038385615587912980117835104465484619140625;i:80;d:2.08227681079140580777675495482981204986572265625;i:81;d:-1.753655092629824441274877244723029434680938720703125;i:82;d:1.3630247069222445333025461877696216106414794921875;i:83;d:-3.115952582810954663017355414922349154949188232421875;i:84;d:-0.177601315455488961703167660743929445743560791015625;i:85;d:6.63851512389863618324170602136291563510894775390625;i:86;d:8.775559834428815264573131571523845195770263671875;i:87;d:9.370105303474485225478929351083934307098388671875;i:88;d:7.4911302717105332504843318019993603229522705078125;i:89;d:4.24347869251093801068464017589576542377471923828125;i:90;d:4.674190822826052027494370122440159320831298828125;i:91;d:6.623287938973110300366897718049585819244384765625;i:92;d:8.946256989526890635033851140178740024566650390625;i:93;d:10.50450243669818206626587198115885257720947265625;i:94;d:12.058942796513004935832213959656655788421630859375;i:95;d:13.531271366045306336900466703809797763824462890625;i:96;d:15.554527885115621899103643954731523990631103515625;i:97;d:17.63210568312509707311619422398507595062255859375;i:98;d:17.811688173426109216279655811376869678497314453125;i:99;d:17.74225333441221863495229627005755901336669921875;i:100;d:18.93758927003867853500196360982954502105712890625;i:101;d:20.678347820160869474648279719986021518707275390625;i:102;d:17.74942869792734967404612689279019832611083984375;i:103;d:19.252113024235086413682438433170318603515625;i:104;d:20.22398750965630398468420025892555713653564453125;i:105;d:23.4657422742186696495991782285273075103759765625;i:106;d:25.93066711867566453975086915306746959686279296875;i:107;d:29.0481225572344357033216510899364948272705078125;i:108;d:28.969658769972728151742558111436665058135986328125;i:109;d:29.404438521860232214066854794509708881378173828125;i:110;d:30.856416054580581231903124717064201831817626953125;i:111;d:32.1071875926241858678622520528733730316162109375;i:112;d:35.01782211664872335177278728224337100982666015625;i:113;d:36.58205666076208473214137484319508075714111328125;i:114;d:38.253239039264343546165036968886852264404296875;i:115;d:38.35427663046338153662873082794249057769775390625;i:116;d:38.6728274823842781415805802680552005767822265625;i:117;d:38.12773686562485409012879244983196258544921875;i:118;d:35.6741205821896159022799110971391201019287109375;i:119;d:35.36316739815467968810480670072138309478759765625;i:120;d:36.52299063631480890990133048035204410552978515625;i:121;d:26.074838754588025580005705705843865871429443359375;i:122;d:42.57160065152923067444135085679590702056884765625;i:123;d:44.9541967749499207229746389202773571014404296875;i:124;d:46.45369449842993248012135154567658901214599609375;i:125;d:47.03487558231562815080906148068606853485107421875;i:126;d:43.6457115731667641966851078905165195465087890625;i:127;d:42.5877812073030241890592151321470737457275390625;i:128;d:38.30759945544522082627736381255090236663818359375;i:129;d:36.16277158601948116256608045659959316253662109375;i:130;d:37.84138968370478295355496811680495738983154296875;i:131;d:39.00009054928015217456049867905676364898681640625;i:132;d:35.9565846233875134885238367132842540740966796875;i:133;d:29.78130967499266290587911498732864856719970703125;i:134;d:24.88680718519111678688204847276210784912109375;i:135;d:28.126779796450506410110392607748508453369140625;i:136;d:30.438526979127967564409118494950234889984130859375;i:137;d:31.7452051208780829938405076973140239715576171875;i:138;d:28.5451556879923629139739205129444599151611328125;i:139;d:26.84284897484850063165140454657375812530517578125;}s:5:"datum";a:140:{i:0;s:6:"dec 04";i:1;s:6:"jan 05";i:2;s:6:"feb 05";i:3;s:6:"mrt 05";i:4;s:6:"apr 05";i:5;s:6:"mei 05";i:6;s:6:"jun 05";i:7;s:6:"jul 05";i:8;s:6:"aug 05";i:9;s:6:"sep 05";i:10;s:6:"okt 05";i:11;s:6:"nov 05";i:12;s:6:"dec 05";i:13;s:6:"jan 06";i:14;s:6:"feb 06";i:15;s:6:"mrt 06";i:16;s:6:"apr 06";i:17;s:6:"mei 06";i:18;s:6:"jun 06";i:19;s:6:"jul 06";i:20;s:6:"aug 06";i:21;s:6:"sep 06";i:22;s:6:"okt 06";i:23;s:6:"nov 06";i:24;s:6:"dec 06";i:25;s:6:"jan 07";i:26;s:6:"feb 07";i:27;s:6:"mrt 07";i:28;s:6:"apr 07";i:29;s:6:"mei 07";i:30;s:6:"jun 07";i:31;s:6:"jul 07";i:32;s:6:"aug 07";i:33;s:6:"sep 07";i:34;s:6:"okt 07";i:35;s:6:"nov 07";i:36;s:6:"dec 07";i:37;s:6:"jan 08";i:38;s:6:"feb 08";i:39;s:6:"mrt 08";i:40;s:6:"apr 08";i:41;s:6:"mei 08";i:42;s:6:"jun 08";i:43;s:6:"jul 08";i:44;s:6:"aug 08";i:45;s:6:"sep 08";i:46;s:6:"okt 08";i:47;s:6:"nov 08";i:48;s:6:"dec 08";i:49;s:6:"jan 09";i:50;s:6:"feb 09";i:51;s:6:"mrt 09";i:52;s:6:"apr 09";i:53;s:6:"mei 09";i:54;s:6:"jun 09";i:55;s:6:"jul 09";i:56;s:6:"aug 09";i:57;s:6:"sep 09";i:58;s:6:"okt 09";i:59;s:6:"nov 09";i:60;s:6:"dec 09";i:61;s:6:"jan 10";i:62;s:6:"feb 10";i:63;s:6:"mrt 10";i:64;s:6:"apr 10";i:65;s:6:"mei 10";i:66;s:6:"jun 10";i:67;s:6:"jul 10";i:68;s:6:"aug 10";i:69;s:6:"sep 10";i:70;s:6:"okt 10";i:71;s:6:"nov 10";i:72;s:6:"dec 10";i:73;s:6:"jan 11";i:74;s:6:"feb 11";i:75;s:6:"mrt 11";i:76;s:6:"apr 11";i:77;s:6:"mei 11";i:78;s:6:"jun 11";i:79;s:6:"jul 11";i:80;s:6:"aug 11";i:81;s:6:"sep 11";i:82;s:6:"okt 11";i:83;s:6:"nov 11";i:84;s:6:"dec 11";i:85;s:6:"jan 12";i:86;s:6:"feb 12";i:87;s:6:"mrt 12";i:88;s:6:"apr 12";i:89;s:6:"mei 12";i:90;s:6:"jun 12";i:91;s:6:"jul 12";i:92;s:6:"aug 12";i:93;s:6:"sep 12";i:94;s:6:"okt 12";i:95;s:6:"nov 12";i:96;s:6:"dec 12";i:97;s:6:"jan 13";i:98;s:6:"feb 13";i:99;s:6:"mrt 13";i:100;s:6:"apr 13";i:101;s:6:"mei 13";i:102;s:6:"jun 13";i:103;s:6:"jul 13";i:104;s:6:"aug 13";i:105;s:6:"sep 13";i:106;s:6:"okt 13";i:107;s:6:"nov 13";i:108;s:6:"dec 13";i:109;s:6:"jan 14";i:110;s:6:"feb 14";i:111;s:6:"mrt 14";i:112;s:6:"apr 14";i:113;s:6:"mei 14";i:114;s:6:"jun 14";i:115;s:6:"jul 14";i:116;s:6:"aug 14";i:117;s:6:"sep 14";i:118;s:6:"okt 14";i:119;s:6:"nov 14";i:120;s:6:"dec 14";i:121;s:6:"jan 15";i:122;s:6:"feb 15";i:123;s:6:"mrt 15";i:124;s:6:"apr 15";i:125;s:6:"mei 15";i:126;s:6:"jun 15";i:127;s:6:"jul 15";i:128;s:6:"aug 15";i:129;s:6:"sep 15";i:130;s:6:"okt 15";i:131;s:6:"nov 15";i:132;s:6:"dec 15";i:133;s:6:"jan 16";i:134;s:6:"feb 16";i:135;s:6:"mrt 16";i:136;s:6:"apr 16";i:137;s:6:"mei 16";i:138;s:6:"jun 16";i:139;s:6:"jul 16";}}');
    for($i=1;$i<20;$i++)
    {
      $tmp['datum'][]=$perfGrafiek['datum'][$i];
      $tmp['portefeuille'][]=$perfGrafiek['portefeuille'][$i];
      //unset($perfGrafiek['portefeuille'][$i]);
    }
    $tmp['datum'][]=9;
    $tmp['portefeuille'][]=11;

    $perfGrafiek=$tmp;
   // listarray($perfGrafiek);exit;
*/
    $perfGrafiek['legenda']=array(vertaalTekst('Portefeuille',$this->pdf->rapport_taal));
    $this->pdf->setXY(20,120);
    $portKleur=array(0,0,0);
    $perfGrafiek['titel']=vertaalTekst('Portefeuille rendement',$this->pdf->rapport_taal);
    $this->LineDiagram(120, 55, $perfGrafiek,array($portKleur),0,0,5,5);//50


  }

  function addVermogensverloop()
  {
  
    foreach($this->indexHistorie  as $maandData)
    {
      $jaar=substr($maandData['datum'],0,4);
      $barData[$jaar]['Totaal']=($maandData['waardeHuidige']);
    }
/*
    $index=new indexHerberekening();
    $indexData = $index->getWaarden($this->pdf->PortefeuilleStartdatum ,$this->rapportageDatum ,$this->portefeuille,'','jaar');
 //listarray($indexData);
    foreach ($indexData as $data)
    {
      $jaar=substr($data['datum'],0,4);
      $barData[$jaar]['Totaal']=($data['waardeHuidige']);
    }
*/
    $this->VBarDiagram(165,170,110,50,$barData,vertaalTekst('Ontwikkeling vermogen',$this->pdf->rapport_taal));
//listarray($barData);
  }

  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4)
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['specifiekeIndex'];
    $data = $data['portefeuille'];


    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY()+1;
    $margin = 2;
    $YDiag = $YPage + $margin*2;
    $hDiag = floor($h - $margin * 5);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2);
    $this->pdf->Cell($w,0,$titel,0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

    //$this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'D');

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.01);


    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
    }

    $minVal = floor(($minVal-1) * 1.25);
    if($minVal > 0)
      $minVal=0;
    $maxVal = ceil(($maxVal+1) * 1.25);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
 //   $verInterval = ($lDiag / $verDiv);
  //  $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);



    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      $xpos = $XDiag + $verInterval * $i;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = floor(abs($maxVal - $minVal)/$horDiv);
    if(abs($minVal)<$stapgrootte)
    {
      $minVal=$stapgrootte*-1.25;
      $legendYstep = ($maxVal - $minVal) / $horDiv;
      $waardeCorrectie = $hDiag / ($maxVal - $minVal);
      $stapgrootte = floor(abs($maxVal - $minVal)/$horDiv);
    }

    $unith = $hDiag / (-1 * $stapgrootte*$horDiv);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    //echo "LineDiagram step: $stapgrootte <br>\n";
    $nulpunt = $YDiag + ($maxVal * $waardeCorrectie);
    $n=0;

    $this->pdf->TextWithRotation($XDiag-9,$YDiag+$h/2+5,vertaalTekst('Rendement in %',$this->pdf->rapport_taal),90);
    for($i=$nulpunt; round($i)<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      //echo "1e $i -> $bodem via $absUnit <br>\n";
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
        break;
    }

    $n=0;
    for($i=$nulpunt; round($i) >= $top+1; $i-= $absUnit*$stapgrootte)
    {
      //echo "2e $i -> $top via $stapgrootte<br>\n";
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");

      $n++;
      if($n >20)
        break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;

      if ($i>=0)
      {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      }

      $yval = $yval2;
    }

    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;

        if ($i>0)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        $yval = $yval2;
      }
    }


    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.3,'cap' => 'butt'));
    $step=5;
    foreach ($legendaItems as $index=>$item)
    {
      if($index==0)
        $kleur=$color;
      else
        $kleur=$color1;
      $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
      $this->pdf->Rect($XPage+$step, $YPage+$h+10, 3, 3, 'DF','',$kleur);
      $this->pdf->SetXY($XPage+3+$step,$YPage+$h+10);
      $this->pdf->Cell(0,3,$item);
      $step+=($w/2);
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }


  function VBarDiagram($x,$y,$w, $h, $data,$title='')
  {
    global $__appvar;
    //  $this->pdf->SetXY($x,$y)		;//112
    $legendaWidth = 0;

   // $this->pdf->Rect($x-12,$y-$h-5,$w+17, $h+30);

    $this->pdf->setXY($x,$y-$h);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,4,$title,'','C');
    $this->pdf->setXY($x,$y+8);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);



    $grafiekPunt = array();
    $verwijder=array();
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0,'width'=>0.01));
    $maxVal=100;
    foreach ($data as $datum=>$waarden)
    {
      $legenda[$datum] = $datum;
      $n=0;
      foreach ($waarden as $categorie=>$waarde)
      {
        $grafiek[$datum][$categorie]=$waarde;
        $grafiekCategorie[$categorie][$datum]=$waarde;
        $categorien[$categorie] = $n;
        $categorieId[$n]=$categorie ;

        if($waarde < 0)
        {
          $verwijder[$datum]=$datum;
          $grafiek[$datum][$categorie]=0;
          $grafiekCategorie[$categorie][$datum]=0;
        }
        $maxVal=max($maxVal,$waarde);

        if(!isset($colors[$categorie]))
        {
          if($this->attKleuren[$categorie])
            $colors[$categorie]=array($this->attKleuren[$categorie]['R']['value'],$this->attKleuren[$categorie]['G']['value'],$this->attKleuren[$categorie]['B']['value']);
          else
            $colors[$categorie]=array(rand(20,80),rand(20,80),rand(20,250));//array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
        }
        $n++;
      }
    }
    $colors['Totaal']=array(195,14,46);
    foreach ($verwijder as $datum)
    {
      foreach ($data[$datum] as $categorie=>$waarde)
      {
        $grafiek[$datum][$categorie]=0;
        $grafiekCategorie[$categorie][$datum]=0;
      }
    }

    $numBars = count($grafiek);
    // $numBars=12;

    if($color == null)
    {
      $color=array(155,155,155);
    }


   // foreach ($this->jaarWaarden['Totaal'] as $jaar=>$waarden)
  //    $maxVal=max($maxVal,$waarden['waarde']);

    $maxVal=round(ceil($maxVal/5000))*5000;

    // listarray();



    $minVal = 0;

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda
    /*
    $n=0;

    $categorieVolgorde=array('Totaal');
    foreach ($categorieVolgorde as $categorie)
    {
      if(is_array($grafiekCategorie[$categorie]))
      {
      //  $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*10+2, 2, 2, 'DF',null,$colors[$categorie]);
        $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*10+1.5 );
        $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
        $n++;
      }
    }
*/
    $unit = $hGrafiek / $maxVal * -1;
    $nulYpos =0;

    $horDiv = 5;
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);

    $stapgrootte = (abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);

    $nulpunt = $YstartGrafiek + $nulYpos;

    $n=0;
//echo "vbar step: $stapgrootte <br>\n";exit;
    $this->pdf->TextWithRotation($XPage-12,$YPage-$h/2+5,vertaalTekst('Vermogen in EUR',$this->pdf->rapport_taal),90);
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)."",0,0,'R');
      $n++;
    }

    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

    $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
    $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
    $eBaton = ($vBar * 50 / 100);


    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;
//listarray($grafiek);
    foreach ($grafiek as $datum=>$data)
    {
      $data=array_reverse($data,true);
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
        $hval = ($val * $unit );//* $this->jaarWaarden['Totaal'][$datum]['waarde']/100

        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
        $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
      /*
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,0,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor(0,0,0);
*/
        if($legendaPrinted[$datum] != 1)
          $this->pdf->TextWithRotation($xval+1,$YstartGrafiek+4,$legenda[$datum],0);

        if($grafiekPunt[$categorie][$datum])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
          if($lastX)
            $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
          $lastX = $xval+.5*$eBaton;
          $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
        }
        $legendaPrinted[$datum] = 1;
      }
      $i++;
    }
    $xval=$x+10;
    $yval=$y+15;
    $colors=array_reverse($colors,true);
    foreach ($colors as $cat=>$color)
    {
      $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$colors[$cat]);
      $this->pdf->TextWithRotation($xval+7,$yval+2.5,vertaalTekst($cat,$this->pdf->rapport_taal),0);
      $xval=$xval+22;
    }

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}
?>