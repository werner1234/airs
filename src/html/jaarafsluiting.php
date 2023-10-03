<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/02/06 05:41:53 $
 		File Versie					: $Revision: 1.50 $

 		$Log: jaarafsluiting.php,v $
 		Revision 1.50  2020/02/06 05:41:53  rvv
 		*** empty log message ***
 		
 		Revision 1.49  2020/02/05 17:24:35  rvv
 		*** empty log message ***
 		
 		Revision 1.48  2020/01/15 16:27:09  rvv
 		*** empty log message ***
 		
 		Revision 1.47  2020/01/11 14:35:57  rvv
 		*** empty log message ***
 		
 		Revision 1.46  2019/01/05 18:40:23  rvv
 		*** empty log message ***
 		
 		Revision 1.45  2018/08/27 17:17:09  rvv
 		*** empty log message ***
 		
 		Revision 1.44  2018/01/10 16:24:43  rvv
 		*** empty log message ***
 		
 		Revision 1.43  2018/01/06 18:18:38  rvv
 		*** empty log message ***
 		
 		Revision 1.42  2018/01/03 14:19:00  rvv
 		*** empty log message ***
 		
 		Revision 1.41  2018/01/03 10:05:16  rvv
 		*** empty log message ***
 		
 		Revision 1.40  2017/11/29 16:16:06  rvv
 		*** empty log message ***
 		
 		Revision 1.39  2017/08/19 18:16:21  rvv
 		*** empty log message ***
 		
 		Revision 1.38  2017/03/18 20:38:57  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2017/01/07 16:21:02  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2016/02/24 17:11:10  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2016/02/08 07:05:41  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2016/02/06 16:44:49  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2016/01/23 17:54:58  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2016/01/10 08:51:53  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2016/01/09 18:57:05  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2015/12/31 06:32:22  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2015/02/07 20:32:39  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2015/01/07 17:27:14  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2015/01/03 16:08:27  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2015/01/02 08:21:16  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2014/12/31 18:12:34  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2014/04/23 16:17:09  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2014/01/04 17:04:20  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2013/02/03 09:03:11  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2012/01/04 16:27:30  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2011/02/10 19:55:49  rvv
 		*** empty log message ***

 		Revision 1.18  2011/01/26 17:17:25  rvv
 		*** empty log message ***

 		Revision 1.17  2011/01/16 12:10:21  rvv
 		*** empty log message ***

 		Revision 1.16  2010/03/03 20:02:41  rvv
 		*** empty log message ***

 		Revision 1.15  2010/01/09 13:04:53  rvv
 		*** empty log message ***

 		Revision 1.14  2009/03/14 11:42:06  rvv
 		*** empty log message ***

 		Revision 1.13  2008/12/30 15:33:21  rvv
 		*** empty log message ***

 		Revision 1.12  2008/01/23 07:36:26  rvv
 		*** empty log message ***

 		Revision 1.11  2008/01/07 09:19:19  rvv
 		*** empty log message ***

 		Revision 1.10  2006/11/03 11:22:47  rvv
 		Na user update

 		Revision 1.9  2006/10/31 11:53:19  rvv
 		Voor user update.


*/

include_once("wwwvars.php");
include_once("rapport/rapportRekenClass.php");
include_once('../classes/AE_cls_progressbar.php');

if($_GET['lookup']==1)
{

	  $query = "SELECT
Portefeuilles.Portefeuille,
ModelPortefeuilles.Fixed
FROM
Portefeuilles 
JOIN VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND VermogensbeheerdersPerBedrijf.Bedrijf = '".$_GET['bedrijf']."'
LEFT JOIN ModelPortefeuilles ON Portefeuilles.Portefeuille = ModelPortefeuilles.Portefeuille
WHERE
    Portefeuilles.Einddatum > now() AND (ModelPortefeuilles.Fixed <> 1 OR ModelPortefeuilles.Fixed is null) AND Portefeuilles.startdatum <> '0000-00-00' AND Portefeuilles.consolidatie=0
ORDER BY Portefeuilles.Portefeuille";

	$DBp = new DB();
	$DBp->SQL($query);
	$DBp->Query();
	while($pdata = $DBp->nextRecord())
	{
    $portefeuilles[]=$pdata['Portefeuille'];
  }
  echo json_encode(array('portefeuilles' => $portefeuilles));
  exit;
}

$content['jsincludes'].="<script type=\"text/javascript\" src=\"javascript/jquery.multiselect.js\"></script>";

$content['style'].='<link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen"><link type="text/css" href="style/jquery.multiselect.css" rel="stylesheet" />';
$content['javascript'] .= '

function bedrijfChanged()
{
  if($(\'#Bedrijf\').val()==\'Geen\' || $(\'#Bedrijf\').val()==\'Alles\')
  {
    $(\'#portefeuilleSelectie\').hide();
  }
  else
  {
    $(\'#portefeuilleSelectie\').show();
  }

  var $select = $("#portefeuilles").multiselect({noneSelectedText: "Selecteer portefeuilles"});//apply the plugin

  $.ajax({
    type: "GET",
    url: "jaarafsluiting.php?lookup=1&bedrijf="+$(\'#Bedrijf\').val(),
    dataType: "json",
    async: false,
    data: "",
    success: function(data, textStatus, jqXHR)
    {
      $(\'select[name="inFields"]\').html(\'\');
      if (data.portefeuilles.length > 0) 
      {
        $("#portefeuilles").html(\'\');
        $select.multiselect(\'enable\');
        
        $.each(data.portefeuilles, function(index, value) {
        $("#portefeuilles").append($("<option></option>").val(value).html(value));
        });
      }
      $("#portefeuilles").multiselect(\'refresh\'); //refresh the select here
    
    },
    error: function(jqXHR, textStatus, errorThrown)
    {
    }
  });
  
}

function submitCheck(alleenSaldi)
{
   var $select = $("#portefeuilles").multiselect();//apply the plugin
   var values = $select.val();
   
   $(\'#selectedPortefeuilles\').val(values);


   if($(\'#Bedrijf\').val()==\'Alles\')
   {
     var test = confirm("Weet u zeker dat u een jaarafsluiting voor alle bedrijven wilt starten?");
     if (test == false)
     {
       return 0;
     }
   }
   if($(\'#Bedrijf\').val()==\'Geen\')
   {
     alert("Geen bedrijf geselecteerd.");
     return 0;
   }
   if(alleenSaldi==3)
   {
     $(\'#alleenSaldi\').val(0);
     $(\'#viaRekeningmutaties\').val(1);
   }
   else
   {
     $(\'#alleenSaldi\').val(alleenSaldi);
     $(\'#viaRekeningmutaties\').val(0);
   }
document.selectForm.submit();

}

';


  
echo template($__appvar["templateContentHeader"],$content);

flush();


if(!checkAccess("superapp"))
{
	exit;
}

$newDatabase = 1;
$fondsOmschrijving = "Inbreng begingegevens";

$cfg=new AE_config();
$laatsteJaarafsluiting=$cfg->getData('laatsteMuatieJaarafsluiting');
$nieuweStartJaarafsluiting=date('Y-m-d H:i:s');
if($laatsteJaarafsluiting=='')
{
  $laatsteJaarafsluiting=date('Y-m-d H:i:s',time()-3600*24*10);
  $cfg->addItem('laatsteMuatieJaarafsluiting',$laatsteJaarafsluiting);
}

if($_POST['posted'] == true)
{
	$fp = fopen($__appvar["tempdir"].'/jaarafsluiting_'.date('Ymd_Hi').'.txt', 'w');
	// build progressbar
	// maak progressbar
	$prb = new ProgressBar(536,8);
	$prb->color = 'maroon';
	$prb->bgr_color = '#ffffff';
	$prb->brd_color = 'Silver';
	$prb->left = 0;
	$prb->top = 	0;
	$prb->show();

	$prb->moveStep(0);
	$pro_step = 0;

	// check op dagen ??
	$laatsteDag = jul2sql(form2jul($_POST['laatsteDag']));
	$eersteDag  = jul2sql(form2jul($_POST['eersteDag']));

	$DB = new DB();
	$DB2 = new DB($newDatabase);


	$query = "SELECT Bewaarder FROM Bewaarders";
	$DB->SQL($query);
	$DB->Query();
	while ($data=$DB->nextRecord())
	{
		$bewaarderRecords[] = $data['Bewaarder'];
	}

	$afschriftNummer = $_POST['openenJaar']."000";

	// selecteer portefeuilles!

	if ($_POST['Bedrijf'] != 'Alles')
  {
    
    if ($_POST['Bedrijf'] == 'Geen')
    {
      exit;
    }
    
    if ($_POST['selectedPortefeuilles'] <> '')
    {
      $portefeuilles = explode(',', $_POST['selectedPortefeuilles']);
    }
    
    $portefeuilleFilter = '';
    if (count($portefeuilles) > 0)
    {
      $portefeuilleFilter = "AND Portefeuilles.Portefeuille IN('" . implode("','", $portefeuilles) . "')";
    }
    
  }
  
  if ($_POST['viaRekeningmutaties'] == 1)
  {
    $query = "SELECT
Rekeningen.Portefeuille,
max(Rekeningmutaties.change_date),
max(Rekeningmutaties.Boekdatum)
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE Rekeningmutaties.Boekdatum>='" . $_POST['afsluitenJaar'] . "-01-01' AND Rekeningmutaties.Boekdatum<'" . $_POST['openenJaar'] . "-01-01' AND Rekeningmutaties.change_date>'$laatsteJaarafsluiting' ".str_replace('Portefeuilles.Portefeuille','Rekeningen.Portefeuille',$portefeuilleFilter)."
GROUP BY Rekeningen.Portefeuille";

    if ($portefeuilleFilter == '' && $_POST['Bedrijf'] == 'Alles')
    {
      $updateLaatsteJaarafsluiting = true;
    }
    else
    {
      $updateLaatsteJaarafsluiting = false;
    }

    $DB->SQL($query);
    $DB->Query();
    $portefeuilles = array();
    while ($pData = $DB->nextRecord())
    {
      $portefeuilles[] = $pData['Portefeuille'];
    }
    $portefeuilleFilter = "AND Portefeuilles.Portefeuille IN('" . implode("','", $portefeuilles) . "')";
    
  }
  
  
  if ($_POST['Bedrijf'] != 'Alles')
  {
	  $query = "SELECT
Portefeuilles.Portefeuille,
ModelPortefeuilles.Fixed, 
Vermogensbeheerders.jaarafsluitingPerBewaarder,
Portefeuilles.Vermogensbeheerder
FROM
Portefeuilles 
JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder
JOIN VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND VermogensbeheerdersPerBedrijf.Bedrijf = '".$_POST['Bedrijf']."'
LEFT JOIN ModelPortefeuilles ON Portefeuilles.Portefeuille = ModelPortefeuilles.Portefeuille
WHERE
    Portefeuilles.Einddatum > '$laatsteDag' AND (ModelPortefeuilles.Fixed <> 1 OR ModelPortefeuilles.Fixed is null) $portefeuilleFilter AND Portefeuilles.startdatum <> '0000-00-00' AND Portefeuilles.consolidatie=0 ";
	}
	else
  {
    $query = "SELECT Portefeuilles.Portefeuille, Vermogensbeheerders.jaarafsluitingPerBewaarder,Portefeuilles.Vermogensbeheerder
    FROM Portefeuilles 
    JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder
    LEFT JOIN ModelPortefeuilles ON Portefeuilles.Portefeuille = ModelPortefeuilles.Portefeuille
    WHERE Portefeuilles.Einddatum > '" . $laatsteDag . "' AND (ModelPortefeuilles.Fixed <> 1 OR ModelPortefeuilles.Fixed is null) $portefeuilleFilter AND Portefeuilles.startdatum <> '0000-00-00' AND Portefeuilles.consolidatie=0 ";
  }

	$DBp = new DB();
	$DBp->SQL($query);
	$DBp->Query();
	$records = $DBp->records();

	$pro_multiplier = 100 / $records;
  
  if($_POST['alleenSaldi']==1)
  {
    while($pdata = $DBp->nextRecord())
	  {
    	flush();
     $totaalWaardeOUD=0;
     $totaalWaardeNIEUW=0;
  	  $pro_step += $pro_multiplier;
		  $prb->moveStep($pro_step);

		  $portefeuille = $pdata['Portefeuille'];
		  $portefeuilleData = berekenPortefeuilleWaardeBewaarders($portefeuille, $laatsteDag);
      foreach($portefeuilleData as $fondsData)
        if($fondsData['type'] <> 'rente')
          $totaalWaardeOUD+=round($fondsData['actuelePortefeuilleWaardeEuro'],2);
   
      if(substr($eersteDag,5,5)=='01-01')
        $minDag=true;
      else
        $minDag=false;  
	  	$nieuw = berekenPortefeuilleWaardeBewaarders($portefeuille,$eersteDag,$minDag);
      foreach($nieuw as $fondsData)
        if($fondsData['type'] <> 'rente')
          $totaalWaardeNIEUW+=round($fondsData['actuelePortefeuilleWaardeEuro'],2);
          
		  if(round($totaalWaardeNIEUW,2) <> round($totaalWaardeOUD,2))
	  	{
		    verwijderTijdelijkeTabel($portefeuille,$eersteDag);
				$msg="<br><b>verschil in ".$portefeuille."! oud($laatsteDag) = ".round($totaalWaardeOUD,2).", nieuw($eersteDag) = ".round($totaalWaardeNIEUW,2)." (".$pdata['Vermogensbeheerder'] .")</b>";
				echo $msg;
				fwrite($fp, $msg."\n");
		  }
	  	else
	  	{
		  	$msg="<br> ".$portefeuille." : OK, oud($laatsteDag) = ".round($totaalWaardeOUD,2).", nieuw($eersteDag) = ".round($totaalWaardeNIEUW,2)."";
				echo $msg;
				fwrite($fp, $msg."\n");
		  }   
    }
  }
  else
  {
	while($pdata = $DBp->nextRecord())
	{
		flush();

  	$pro_step += $pro_multiplier;
		$prb->moveStep($pro_step);

		$portefeuille = $pdata['Portefeuille'];

		if($pdata['jaarafsluitingPerBewaarder']==1)
  		$portefeuilleData = berekenPortefeuilleWaardeBewaarders($portefeuille, $laatsteDag);
		else
			$portefeuilleData = berekenPortefeuilleWaarde($portefeuille, $laatsteDag);

		$fondsWaardenOud=array();
		$fondsWaardenNieuw=array();
	  foreach($portefeuilleData as $regel)
			$fondsWaardenOud[$regel['fondsOmschrijving']]+=$regel['actuelePortefeuilleWaardeEuro'];
		//	echo $regel['fondsOmschrijving']." ".$regel['actuelePortefeuilleWaardeEuro']."<br>\n";
   // echo "<hr>";
//		verwijderTijdelijkeTabel($portefeuille,$laatsteDag);
		vulTijdelijkeTabel($portefeuilleData,$portefeuille,$laatsteDag);
		// select SUM laatstedag
		$query = "SELECT ROUND(SUM(actuelePortefeuilleWaardeEuro),2) AS totaal ".
						 " FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$laatsteDag."' AND ".
						 " type <> 'rente' AND ".
						 " portefeuille = '".$portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaardeOUD = $totaalWaarde[totaal];

		// selecteer memoriaal rekening bij portefeuille
		$query = "SELECT Rekening,Depotbank FROM Rekeningen WHERE Portefeuille = '".$portefeuille."' AND Memoriaal > 0 AND inactief=0 AND consolidatie=0 ORDER BY id desc";

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();
		$standaardRekening='';
		$rekeningen=array();
		$rekeningenGebruikt=array();
		$rekeningenFondsTotaal=array();
		$bewaardersPerFonds=array();
		$bewaardersFondsMem=array();
		if($DB1->records() > 0)
		{
			while($rekening = $DB1->nextRecord())
			{
				$rekeningen[$rekening['Depotbank']]=$rekening['Rekening'];
				$standaardRekening=$rekening['Rekening'];
			}
			$rekening = $standaardRekening;


			foreach($portefeuilleData as $id=>$fondsRegel)
			{
				if($fondsRegel['type']=='fondsen')
				{
			  	if($fondsRegel['Bewaarder']=='')
				  	$bewaarder='leeg';
				  else
				  	$bewaarder=$fondsRegel['Bewaarder'];

				  $bewaardersPerFonds[$fondsRegel['fonds']][$fondsRegel['Bewaarder']]=$fondsRegel['totaalAantal'];

  				if(!$rekeningen[$bewaarder])
	  		    $bewaardersFondsMem[$fondsRegel['fonds']]['zonder']=$bewaarder;
					else
						$bewaardersFondsMem[$fondsRegel['fonds']]['met']=$bewaarder;
				}
			}

			foreach($bewaardersPerFonds as $fonds=>$bewaarders)
			{
				if(count($bewaarders)>1)
				{

					echo "$portefeuille| $fonds | ";
					foreach($bewaarders as $bewaarder=>$aantal)
						echo "$bewaarder:$aantal|";
					echo "<br>\n";
				}
			}
/*
			foreach($portefeuilleData as $id=>$fondsRegel)
			{
				if($fondsRegel['type']=='fondsen')
				{
					if($fondsRegel['Bewaarder']=='')
						$bewaarder='leeg';
					else
						$bewaarder=$fondsRegel['Bewaarder'];

					if(!$rekeningen[$bewaarder])
					{
						 if(isset($bewaardersFondsMem[$fondsRegel['fonds']]['met']))
						 {
						//	 echo "$portefeuille|".$fondsRegel['fonds']."| bewaarder aangepast van ".$portefeuilleData[$id]['Bewaarder']." naar ".$bewaardersFondsMem[$fondsRegel['fonds']]['met']."<br>\n";
						//	 $portefeuilleData[$id]['Bewaarder']=$bewaardersFondsMem[$fondsRegel['fonds']]['met'];

						 }
					}
				}
			}
*/
			//listarray($bewaardersFondsMem);

			$fondsTotaal = 0;
			$volgNummer = 1;
			// loopje over array en insert FONDSEN
			foreach($portefeuilleData as $a=>$fondsRegel)
			{
				if($fondsRegel['type'] == "fondsen")
				{
					if($rekeningen[$portefeuilleData[$a]['Bewaarder']])
						$rekening=$rekeningen[$portefeuilleData[$a]['Bewaarder']];
					else
					  $rekening = $standaardRekening;

					$rekeningenGebruikt[$rekening]=$rekening;
					$credit=0;
					$debet = round(($fondsRegel['fondsEenheid'] * $fondsRegel['totaalAantal'] * $fondsRegel['historischeWaarde']),2);
					$bedrag = round((-1 * $fondsRegel['historischeValutakoers'] * $debet),2);
/* Nog niet geactiveerd.
					if($debet<0)
					{
						$credit=abs($debet);
						$debet=0;
					}
*/
					$fondsTotaal += $bedrag;
					$rekeningenFondsTotaal[$rekening]+=$bedrag;

				  $query = "SELECT id FROM Rekeningmutaties WHERE  Rekening IN ('".implode("','",$rekeningen) ."')  AND Afschriftnummer = '$afschriftNummer' AND Volgnummer = '$volgNummer' AND Grootboekrekening = 'FONDS'";
				  $rekeningmutatieAantal = $DB2->QRecords($query);

				  if($rekeningmutatieAantal > 1)
				  {
				    $n=0;
				    $msg="<br>Dubbele fonds Volgnummer gevonden voor Rekening IN ('".implode("','",$rekeningen) ."') , Afschriftnummer '$afschriftNummer' en Volgnummer = '$volgNummer'. Volgnummer ophogen. (".$pdata['Vermogensbeheerder'] .") ";
						echo $msg;
						fwrite($fp, $msg."\n");
				    while($data = $DB2->nextRecord())
				    {
				      $update = "UPDATE Rekeningmutaties SET Volgnummer = Volgnummer + 1 WHERE id = '".$data['id']."'";
				      if($n>0)
				      {
				      $DB3=new DB();
				      $DB3->SQL($update);
				      $DB3->Query();
				      }
				      $n++;
				    }
				    $rekeningmutatieAantal = $DB2->QRecords($query);


				  }

				  if(!in_array($fondsRegel['Bewaarder'],$bewaarderRecords) || $pdata['jaarafsluitingPerBewaarder']==0)
						$fondsRegel['Bewaarder'] = "";

				  if($rekeningmutatieAantal != 0)
          {
           $data = $DB2->nextRecord();
           $query = "UPDATE Rekeningmutaties SET ".
           					"Rekening = '".$rekening."', ".
										"Afschriftnummer = '".$afschriftNummer."', ".
										"Volgnummer = '".$volgNummer."', ".
										"Omschrijving = '".$fondsOmschrijving."', ".
										"Boekdatum = '".$eersteDag."', ".
										"Grootboekrekening = 'FONDS', ".
										"Valuta = '".$fondsRegel['valuta']."', ".
										"Valutakoers = '".$fondsRegel['historischeValutakoers']."', ".
										"Fonds = '".$fondsRegel['fonds']."', ".
										"Aantal = ROUND('".$fondsRegel['totaalAantal']."',6), ".
										"Fondskoers = '".$fondsRegel['historischeWaarde']."', ".
										"Debet = ROUND('".$debet."',2), ".
										"Credit = ROUND('".$credit."',2), ".
										"Bedrag = ROUND('".$bedrag."',2), ".
										"Transactietype = 'B', ".
										"Bewaarder = '".$fondsRegel['Bewaarder']."', ".
										"Verwerkt = '1', Memoriaalboeking = '1', ".
										"change_date = NOW(), ".
										"change_user = '".$USR."'
										WHERE id = '".$data['id']."' ";
          }
          else
          {
					$query = "INSERT INTO Rekeningmutaties SET ".
										"Rekening = '".$rekening."', ".
										"Afschriftnummer = '".$afschriftNummer."', ".
										"Volgnummer = '".$volgNummer."', ".
										"Omschrijving = '".$fondsOmschrijving."', ".
										"Boekdatum = '".$eersteDag."', ".
										"Grootboekrekening = 'FONDS', ".
										"Valuta = '".$fondsRegel['valuta']."', ".
										"Valutakoers = '".$fondsRegel['historischeValutakoers']."', ".
										"Fonds = '".$fondsRegel['fonds']."', ".
										"Aantal = ROUND('".$fondsRegel['totaalAantal']."',6), ".
										"Fondskoers = '".$fondsRegel['historischeWaarde']."', ".
										"Debet = ROUND('".$debet."',2), ".
										"Credit = ROUND('".$credit."',2), ".
										"Bedrag = ROUND('".$bedrag."',2), ".
										"Transactietype = 'B', ".
										"Bewaarder = '".$fondsRegel['Bewaarder']."', ".
										"Verwerkt = '1', Memoriaalboeking = '1', ".
										"add_date = NOW(), ".
										"add_user = '".$USR."', ".
										"change_date = NOW(), ".
										"change_user = '".$USR."' ";
          }
					$DB2->SQL($query);
					if(!$DB2->Query())
					{
						$msg="<br> fout in query : " . $query;
						echo $msg;
						fwrite($fp, $msg."\n");
					}
					$volgNummer++ ;
				}
			}

				$query ="SELECT id FROM Rekeningmutaties WHERE Rekening IN ('".implode("','",$rekeningenGebruikt) ."') AND Afschriftnummer = '$afschriftNummer' AND Volgnummer >= '".$volgNummer."' AND Grootboekrekening = 'FONDS'";
			  if($DB2->QRecords($query) != 0)
			  {
			    $query = "DELETE FROM Rekeningmutaties WHERE Rekening IN ('".implode("','",$rekeningenGebruikt) ."') AND Afschriftnummer = '$afschriftNummer' AND Volgnummer >= '".$volgNummer."' AND Grootboekrekening = 'FONDS' ";
			    $DB2->SQL($query);
			    if(!$DB2->Query())
					{
			      $msg="<br>fout in query : ".$query;
						echo $msg;
						fwrite($fp, $msg."\n");
					}
			    else
					{
			      $msg="<br>Enkele Fonds rekeningmutaties verwijderd voor '".implode("','",$rekeningenGebruikt)."' en Afschriftnummer '$afschriftNummer'. Exporteer correctie updates Rekeningafschriften/mutaties. (".$pdata['Vermogensbeheerder'] .")";
						echo $msg;
						fwrite($fp, $msg."\n");
					}
			  }

			if($volgNummer > 1)
			{
				foreach($rekeningenGebruikt as $rekening)
				{
			    $query ="SELECT id FROM Rekeningafschriften WHERE Rekening = '$rekening' AND Afschriftnummer = '$afschriftNummer'AND Datum = '$eersteDag'";
			    if($DB2->QRecords($query) != 0)
          {
           $data = $DB2->nextRecord();
           $query = "UPDATE Rekeningafschriften SET ".
           	 "Rekening = '".$rekening."', ".
						 "Afschriftnummer = '".$afschriftNummer."', ".
						 "Datum = '".$eersteDag."', ".
						 "Saldo = '0', ".
						 "NieuwSaldo = '0', ".
						 "Verwerkt = '1', ".
						 "change_date = NOW(), ".
						 "change_user = '".$USR."' WHERE id = '".$data['id']."'";
          }
          else
          {
				// er zijn 1 of meerdere regels toegevoegd, maak een afschrift!
			  	$query = "INSERT INTO Rekeningafschriften SET ".
						 "Rekening = '".$rekening."', ".
						 "Afschriftnummer = '".$afschriftNummer."', ".
						 "Datum = '".$eersteDag."', ".
						 "Saldo = '0', ".
						 "NieuwSaldo = '0', ".
						 "Verwerkt = '1', ".
						 "add_date = NOW(), ".
						 "add_user = '".$USR."', ".
						 "change_date = NOW(), ".
						 "change_user = '".$USR."' ";

          }
			  	$DB2->SQL($query);
			  	if(!$DB2->Query())
				  {
					  $msg="<br> fout in query : " . $query;
				  	echo $msg;
				  	fwrite($fp, $msg."\n");
				  }

				// alle fondsen toegevoegd, doe nu ook een tegenboeking om op 0 uit te komen!
			  	$fondsTotaal = -1 * $rekeningenFondsTotaal[$rekening];
			//	$volgNummer ++;
			    $query ="SELECT id, Volgnummer FROM Rekeningmutaties WHERE Rekening = '$rekening' AND Afschriftnummer = '$afschriftNummer'  AND Grootboekrekening = 'VERM'";
			    if($DB2->QRecords($query) != 0)
          {
            $data = $DB2->nextRecord();
        //  $volgNummer = $data['Volgnummer'];

            $query = "UPDATE Rekeningmutaties SET ".
									"Rekening = '".$rekening."', ".
									"Afschriftnummer = '".$afschriftNummer."', ".
									"Volgnummer = '".$volgNummer."', ".
									"Omschrijving = '".$fondsOmschrijving."', ".
									"Boekdatum = '".$eersteDag."', ".
									"Grootboekrekening = 'VERM', ".
									"Valuta = 'EUR', ".
									"Valutakoers = '1', ".
									"Fonds = '', ".
									"Aantal = '0', ".
									"Fondskoers = '', ".
									"Debet = '0', ".
									"Credit = ROUND('".$fondsTotaal."',2), ".
									"Bedrag = ROUND('".$fondsTotaal."',2), ".
									"Transactietype = 'B', ".
									"Verwerkt = '1', Memoriaalboeking = '1',".
									"change_date = NOW(), ".
									"change_user = '".$USR."' WHERE id = '".$data['id']."'";
          }
          else
          {//Memoriaalboeking = '0',
				  $query = "INSERT INTO Rekeningmutaties SET ".
									"Rekening = '".$rekening."', ".
									"Afschriftnummer = '".$afschriftNummer."', ".
									"Volgnummer = '".$volgNummer."', ".
									"Omschrijving = '".$fondsOmschrijving."', ".
									"Boekdatum = '".$eersteDag."', ".
									"Grootboekrekening = 'VERM', ".
									"Valuta = 'EUR', ".
									"Valutakoers = '1', ".
									"Fonds = '', ".
									"Aantal = '0', ".
									"Fondskoers = '', ".
									"Debet = '0', ".
									"Credit = ROUND('".$fondsTotaal."',2), ".
									"Bedrag = ROUND('".$fondsTotaal."',2), ".
									"Transactietype = 'B', ".
									"Verwerkt = '1', Memoriaalboeking = '1', ".
									"add_date = NOW(), ".
									"add_user = '".$USR."', ".
									"change_date = NOW(), ".
									"change_user = '".$USR."' ";
          }
  		  	$DB2->SQL($query);
			  	if(!$DB2->Query())
					{
					  $msg="<br>fout in query : ".$query;
					  echo $msg;
					  fwrite($fp, $msg."\n");
			  	}

				  $query ="SELECT id FROM Rekeningmutaties WHERE Rekening = '$rekening' AND Afschriftnummer = '$afschriftNummer' AND Volgnummer > '".$volgNummer."' AND Grootboekrekening = 'VERM'";
			    if($DB2->QRecords($query) > 0)
			    {
			      $query = "DELETE FROM Rekeningmutaties WHERE Rekening = '$rekening' AND Afschriftnummer = '$afschriftNummer' AND Volgnummer > '".$volgNummer."' AND Grootboekrekening = 'VERM' ";
			      $DB2->SQL($query);
			      if(!$DB2->Query())
				  	{
					  	$msg = "<br> fout in query : " . $query;
				  	}
				  	else
				  	{
					  	$msg="<br>Rekeningmutatie met grootboek VERM verwijderd voor '$rekening' en Afschriftnummer '$afschriftNummer'. Exporteer correctie updates Rekeningafschriften/mutaties. (".$pdata['Vermogensbeheerder'] .")";
					  }
					  echo $msg;
					  fwrite($fp, $msg."\n");
			    }
				}

			}
			else
			{
				$msg="<br>".$portefeuille." : geen fondsen in portefeuille";
				echo $msg;
				fwrite($fp, $msg."\n");
        $query ="SELECT id FROM Rekeningmutaties WHERE Rekening = '$rekening' AND Afschriftnummer = '$afschriftNummer' AND Volgnummer >= '".$volgNummer."' AND Grootboekrekening = 'VERM'";
			  if($DB2->QRecords($query) > 0)
			  {
			    $query = "DELETE FROM Rekeningmutaties WHERE Rekening = '$rekening' AND Afschriftnummer = '$afschriftNummer' AND Volgnummer >= '".$volgNummer."' AND Grootboekrekening = 'VERM' ";
			    $DB2->SQL($query);
			    if(!$DB2->Query())
						$msg="<br> fout in query : ".$query;
			    else
			      $msg="<br>Rekeningmutatie met grootboek VERM verwijderd voor '$rekening' en Afschriftnummer '$afschriftNummer'. Exporteer correctie updates Rekeningafschriften/mutaties. (".$pdata['Vermogensbeheerder'] .")";
					echo $msg;
					fwrite($fp, $msg."\n");
			  }
			}
		}
		else
		{
			$msg="<br> ".$portefeuille." : geen memoriaal rekening, kan beginstorting regels nergens plaatsen! (".$pdata['Vermogensbeheerder'] .")<br>";
			echo $msg;
			fwrite($fp, $msg."\n");
		}

		// vul nu andere rekeningen!
		$query = "SELECT * FROM Rekeningen WHERE Portefeuille = '".$portefeuille."' AND Memoriaal < 1 ";
		$DB1->SQL($query);
		$DB1->Query();
		if($DB1->records() > 0)
		{
			//selecteer boekjaar
			$jaar = date("Y",db2jul($laatsteDag));
			$_beginJaar = substr($laatsteDag,0,4)."-01-01";

			while($rekening = $DB1->nextRecord())
			{
		    $subquery = "SELECT SUM(Bedrag) as totaal FROM Rekeningmutaties WHERE ".
		    						" boekdatum >= '".$_beginJaar."' AND ".
		    						" boekdatum <= '".$laatsteDag."' AND ".
		                " Rekening = '".$rekening['Rekening']."' ".
		                " GROUP BY Rekeningmutaties.Rekening ";

				$DB0 = new DB();
				$DB0->SQL($subquery);
				$DB0->Query();


				$totaal = $DB0->nextRecord();
				$totaal = $totaal[totaal];

				if($totaal < 0)
				{
					$debet  = -1 * $totaal;
					$credit = 0;
				}
				else
				{
					$debet  = 0;
					$credit = $totaal;
				}

				// get valutakoers op laatsteDag.
				$vquery = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '".$rekening['Valuta']."' AND Datum <= '".$laatsteDag."' ORDER BY Datum DESC LIMIT 1";
				$DB0->SQL($vquery);
				$DB0->Query();
				$vdata = $DB0->nextRecord();

				// INSERT 1e boeking voor rekening
				// $valutakoers //
			  $query ="SELECT id FROM Rekeningmutaties WHERE Rekening = '".$rekening['Rekening']."' AND Afschriftnummer = '$afschriftNummer' AND Volgnummer = '1' ";
			  if($DB2->QRecords($query) != 0)
        {
          $data = $DB2->nextRecord();
          $query = "UPDATE Rekeningmutaties SET ".
									"Rekening 				= '".$rekening['Rekening']."', ".
									"Afschriftnummer 	= '".$afschriftNummer."', ".
									"Volgnummer 			= '1', ".
									"Omschrijving 		= '".$fondsOmschrijving."', ".
									"Boekdatum 				= '".$eersteDag."', ".
									"Grootboekrekening = 'VERM', ".
									"Valuta 			= '".$rekening['Valuta']."', ".
									"Valutakoers 	= '".$vdata['Koers']."', ".
									"Fonds 				= '', ".
									"Aantal 			= '0', ".
									"Fondskoers 	= '', ".
									"Debet 				= ROUND('".$debet."',2), ".
									"Credit 			= ROUND('".$credit."',2), ".
									"Bedrag 			= ROUND('".$totaal."',2), ".
									"Transactietype = 'B', ".
									"Verwerkt 		= '1', ".
									"change_date 	= NOW(), ".
									"change_user 	= '".$USR."' WHERE id = '".$data['id']."' ";
        }
				else
				{
				$query = "INSERT INTO Rekeningmutaties SET ".
									"Rekening 				= '".$rekening['Rekening']."', ".
									"Afschriftnummer 	= '".$afschriftNummer."', ".
									"Volgnummer 			= '1', ".
									"Omschrijving 		= '".$fondsOmschrijving."', ".
									"Boekdatum 				= '".$eersteDag."', ".
									"Grootboekrekening = 'VERM', ".
									"Valuta 			= '".$rekening['Valuta']."', ".
									"Valutakoers 	= '".$vdata['Koers']."', ".
									"Fonds 				= '', ".
									"Aantal 			= '0', ".
									"Fondskoers 	= '', ".
									"Debet 				= ROUND('".$debet."',2), ".
									"Credit 			= ROUND('".$credit."',2), ".
									"Bedrag 			= ROUND('".$totaal."',2), ".
									"Transactietype = 'B', ".
									"Verwerkt 		= '1', ".
									"add_date 		= NOW(), ".
									"add_user 		= '".$USR."', ".
									"change_date 	= NOW(), ".
									"change_user 	= '".$USR."' ";
				}

				$DB2->SQL($query);

				if($DB2->Query())
				{
					// insert Rekeningafschrift
					$query ="SELECT id FROM Rekeningafschriften WHERE Rekening = '".$rekening['Rekening']."' AND Afschriftnummer = '$afschriftNummer' AND Datum = '$eersteDag' AND Saldo = '0' ";
			    if($DB2->QRecords($query) != 0)
          {
            $data = $DB2->nextRecord();
            $query = "UPDATE Rekeningafschriften SET ".
							 "Rekening 				= '".$rekening['Rekening']."', ".
							 "Afschriftnummer = '".$afschriftNummer."', ".
							 "Datum = '".$eersteDag."', ".
							 "Saldo = '0', ".
							 "NieuwSaldo = ROUND('".$totaal."',2), ".
							 "Verwerkt = '1', ".
							 "change_date = NOW(), ".
							 "change_user = '".$USR."' WHERE id = '".$data['id']."'";
          }
          else
          {
					$query = "INSERT INTO Rekeningafschriften SET ".
							 "Rekening 				= '".$rekening['Rekening']."', ".
							 "Afschriftnummer = '".$afschriftNummer."', ".
							 "Datum = '".$eersteDag."', ".
							 "Saldo = '0', ".
							 "NieuwSaldo = ROUND('".$totaal."',2), ".
							 "Verwerkt = '1', ".
							 "add_date = NOW(), ".
							 "add_user = '".$USR."', ".
							 "change_date = NOW(), ".
							 "change_user = '".$USR."' ";
          }
					$DB2->SQL($query);
					if($DB2->Query())
					{

					}
					else
					{
						$msg="<br> fout in query : ".$insert;
						echo $msg;
						fwrite($fp, $msg."\n");
					}
				}
			}
		}
		// controle ?

		// op nieuwe DB
		if(substr($eersteDag,5,5)=='01-01')
			$minDag=true;
		else
			$minDag=false;

		if($pdata['jaarafsluitingPerBewaarder']==1)
			$nieuw = berekenPortefeuilleWaardeBewaarders($portefeuille, $eersteDag,$minDag);
		else
			$nieuw = berekenPortefeuilleWaarde($portefeuille, $eersteDag,$minDag);

		//foreach($nieuw as $regel)
		//  echo $regel['fondsOmschrijving']." ".$regel['actuelePortefeuilleWaardeEuro']."<br>\n";
		vulTijdelijkeTabel($nieuw,$portefeuille,$eersteDag);
		// select SUM laatstedag
		$query = "SELECT ROUND(SUM(actuelePortefeuilleWaardeEuro),2) AS totaal ".
						 " FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$eersteDag."' AND ".
						 " type <> 'rente' AND ".
						 " portefeuille = '".$portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaardeNIEUW = $totaalWaarde['totaal'];

		if(round($totaalWaardeNIEUW,2) <> round($totaalWaardeOUD,2))
		{
			$fondsWaardenNew=array();
		  verwijderTijdelijkeTabel($portefeuille,$eersteDag);
			$msg="<br><b>verschil in ".$portefeuille."! oud = ".round($totaalWaardeOUD,2).", nieuw = ".round($totaalWaardeNIEUW,2)." (".$pdata['Vermogensbeheerder'] .")</b>";
			foreach($nieuw as $regel)
				$fondsWaardenNieuw[$regel['fondsOmschrijving']]+=$regel['actuelePortefeuilleWaardeEuro'];
			foreach($fondsWaardenOud as $fonds=>$waarde)
			{
				if($fondsWaardenOud[$fonds] <> $fondsWaardenNieuw[$fonds])
					$msg.=" $fonds €".$fondsWaardenOud[$fonds]." <> €".$fondsWaardenNieuw[$fonds]." ";
			}
		}
		else
		{
			$msg="<br> ".$portefeuille." | ".$pdata['Vermogensbeheerder']." : OK";
		}
		echo $msg;
		fwrite($fp, $msg."\n");
	}
  }
	$prb->hide();
	if($fp)
  {
    fwrite($fp, "Klaar.\n");
    fclose($fp);
  }
  if($updateLaatsteJaarafsluiting==true)
    $cfg->addItem('laatsteMuatieJaarafsluiting',$nieuweStartJaarafsluiting);
}
else
{
	// selecteer laatst bekende valutadatum

	$ditJaar = date("Y",mktime());
	$vorigJaar = $ditJaar-1;

	// test of er al is afgesloten in dat jaar.
	$query = "SELECT COUNT(id) AS aantal FROM Rekeningafschriften WHERE Afschriftnummer = '".$ditJaar."000'";
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$afschrift = $DB->nextRecord();

	$datum = date("d-m-Y",mktime(0,0,0,12,31,$vorigJaar));
	$dbdatum = form2jul($datum);
	// controlleer of er valutakoersen zijn ingevoerd op de afsluitdatum!!
	$q = "SELECT Datum FROM Valutakoersen WHERE Valuta = 'EUR' AND Datum = '".jul2sql($dbdatum)."' ;";
	$DB = new DB();
	$DB->SQL($q);
	$DB->Query();
	$valutarecord = $DB->records();
?>
<form action="" method="POST" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="selectedPortefeuilles" id="selectedPortefeuilles" value="true" />
<input type="hidden" name="alleenSaldi" id="alleenSaldi" value="" />
<input type="hidden" name="viaRekeningmutaties" id="viaRekeningmutaties" value="" />



<iframe width="538" height="15" name="generateFrame" frameborder="0" scrolling="No" marginwidth="0" marginheight="0"></iframe>

<div class="formblock">
	<div class="formlinks"> </div>
	<div class="formrechts">
		<?($afschrift['aantal']>0)?"<br>Er heeft al een jaarafsluiting plaatsgevonden dit jaar! (".$afschrift['aantal']." afschriften met afschriftnummer ".$ditJaar."000 gevonden!":""?>
		<?($valutarecord<1)?"<br>Er is geen EUR koers gevonden op ".$datum." ! Controlleer of alle data in ".$vorigJaar." goed is ingevoerd!":""?>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"> </div>
	<div class="formrechts">
		<b>Jaar afsluiten</b><br>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"> Jaar </div>
	<div class="formrechts">
		<input type="text" name="afsluitenJaar" size="10" value="<?=$vorigJaar?>">
	</div>
</div>

<div class="formblock">
	<div class="formlinks"> Laatste dag  </div>
	<div class="formrechts">
		<input type="text" name="laatsteDag" size="10" value="<?=date("d-m-Y",mktime(0,0,0,12,31,$vorigJaar))?>">
	</div>
</div>

<div class="formblock">
	<div class="formlinks"> </div>
	<div class="formrechts">
		<b>Openen in jaar</b><br>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"> Jaar  </div>
	<div class="formrechts">
		<input type="text" name="openenJaar" size="10" value="<?=$ditJaar?>">
	</div>
</div>

<div class="formblock">
	<div class="formlinks"> Eerste dag  </div>
	<div class="formrechts">
		<input type="text" name="eersteDag" size="10" value="<?=date("d-m-Y",mktime(0,0,0,1,1,$ditJaar))?>">
	</div>
</div>

<?
$DB = new DB();
$DB->SQL("SELECT * FROM Bedrijfsgegevens ORDER BY Bedrijf");
$DB->Query();

$bedrijven = array();

while($bedrijfdata = $DB->NextRecord())
{
	$bedrijven[] = $bedrijfdata['Bedrijf'];
}


?>

<div class="formblock">
	<div class="formlinks"> Voor bedrijf </div>
	<div class="formrechts">
		<select id="Bedrijf" name="Bedrijf" onchange="javascript:bedrijfChanged();">
    <OPTION VALUE="Geen" selected>Geen
		<OPTION VALUE="Alles" >Alles
<?=SelectArray("",$bedrijven)?>
    </select>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"> &nbsp; </div>
	<div class="formrechts">
		<br>
		
    <div class="buttonDiv" onclick="javascript:submitCheck('0');">Verwerken</div><br>
    <div class="buttonDiv" onclick="javascript:submitCheck('1');">Alleen saldicontrole</div><br>
    <div class="buttonDiv" onclick="javascript:submitCheck('3');">Via rek.mut. na <?=$laatsteJaarafsluiting?></div><br>
	</div>
</div>

<div class="formblock" id="portefeuilleSelectie" style="display:none">
	<div class="formlinks"> &nbsp; </div>
	<div class="formrechts">
	  <select id='portefeuilles' name="portefeuilles" multiple="multiple"  >
  </div>
</div>



</form>

<?
}
echo template($__appvar["templateRefreshFooter"],$content);
?>