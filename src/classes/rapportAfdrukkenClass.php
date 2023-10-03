<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:13 $
 		File Versie					: $Revision: 1.3 $
 		
 		$Log: rapportAfdrukkenClass.php,v $
 		Revision 1.3  2018/08/18 12:40:13  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.2  2008/06/30 06:55:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/03/18 09:27:35  rvv
 		*** empty log message ***
 		
 	
*/

class rapportAfdrukken
{
  function rapportAfdrukken()
  {
    global $__appvar,$USR;
    include_once("rapport/rapportVertaal.php");
    include_once("rapport/PDFRapport.php");
    include_once("rapport/RapportFront.php");
    include_once("rapport/RapportHSE.php");
    include_once("rapport/RapportMUT.php");
    include_once("rapport/RapportMUT2.php");
    include_once("rapport/RapportOIH.php");
    include_once("rapport/RapportOIB.php");
    include_once("rapport/RapportOIBS.php");
    include_once("rapport/RapportOIBS2.php");
    include_once("rapport/RapportOIV.php");
    include_once("rapport/RapportPERF.php");
    include_once("rapport/RapportPERFD.php");
    include_once("rapport/RapportTRANS.php");
    include_once("rapport/RapportVHO.php");
    include_once("rapport/RapportVOLK.php");
    include_once("rapport/RapportHSEP.php");
    include_once("rapport/RapportVOLKD.php");
    include_once("rapport/RapportOIR.php");
    include_once("rapport/RapportGRAFIEK.php");
    include_once("rapport/RapportATT.php");
    include_once("rapport/factuur/PDFFactuur.php");
    include_once("rapport/factuur/Factuur.php"); 
    include_once("rapport/rapportBrief.php");

    $this->DB = new DB();
    $this->superUser = checkAccess();
    if(!$this->superUser)
      $this->queryPortefeuilleJoin = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' ";

    $this->selectieData = $_POST;
    $this->type = $this->selectieData['type'];
    
    $this->rapportageDatumVanaf = jul2sql(form2jul($this->selectieData['datumVan']));
  	$this->rapportageDatum      = jul2sql(form2jul($this->selectieData['datumTm']));
	  $this->rapportageJaar       = date('Y',form2jul($this->selectieData['datumTm']));
	  
	  if($this->selectieData['portefeuilleVan'])
	    $this->backoffice = true;
	  if($this->selectieData['inclBrief'])
	    $this->inclBrief = true;

    $this->checkInput();
    $this->genereerPortefeuilleLijst();
    $this->startNewPdf();
 
  }
  
  function startNewPdf()
  {
    global $__appvar;
    $this->pdf = new PDFRapport('L','mm');
	  $this->pdf->SetAutoPageBreak(true,15);
	  $this->pdf->pagebreak = 190;
	  $this->pdf->__appvar = $__appvar;
	  $this->pdf->selectData = $this->selectieData;
	  $this->pdf->engineII = true;
  }
  
  function checkInput()
  {
    global $__appvar, $rapport_types;
    if(!empty($this->selectieData['datumVan']) && !empty($this->selectieData['datumTm']))
	  {
		  $dd = explode($__appvar["date_seperator"],$this->selectieData['datumVan']);
		  if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		  {
			  echo "<b>Fout: ongeldige datum opgegeven!</b>";
			  exit;
		  }

		  $dd = explode($__appvar["date_seperator"],$this->selectieData['datumTm']);
		  if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		  {
			  echo "<b>Fout: ongeldige datum opgegeven!</b>";
			  exit;
		  }
		  
		  if(db2jul($this->rapportageDatumVanaf) > db2jul($this->rapportageDatum))  
	    {
		    echo "<b>Fout: Van datum kan niet groter zijn dan  T/m datum! </b>";
		    exit;
	    }
	  }
	  else
	  {
		  echo "<b>Fout: geen datum opgegeven!</b>";
		  exit;
	  }

	  if( strlen($rapport_types) <= 1 && $this->type != 'factuur')
	  {
		  echo "<b>Fout: geen rapport type opgegeven </b>";
		  exit;
	  }
	  $this->rapport_type = explode("|",$rapport_types);
	  
	  if(count($this->rapport_type) < 1 && $this->type != 'factuur')
	  {
		  echo "<b>Fout: geen rapport type opgegeven </b>";
		  exit;
	  }

	  if($this->type == 'frontConsolidatie')
	  {
      if(count($_POST['selectedFields']) < 1)
      {
	      echo "<b>Fout: geen portefeuilles opgegeven </b>";
	      exit;
      }  
	  }
	  elseif(empty($this->selectieData['Portefeuille']) && empty($this->backoffice))	
	  {
		echo "<b>Fout: geen portefeuille opgegeven </b>";
		exit;
	  }
	  
  }
  
  function genereerPortefeuilleLijst()
  {
    global $USR;
    $this->portefeuilles = array();

    if($this->type == 'frontConsolidatie')
	  {
      $this->consolidatieAanmaken();
	  }  
    
    if($this->selectieData['Portefeuille'])
    {
      $data['rapportageDatumVanaf'] = $this->rapportageDatumVanaf;
      $data['rapportageDatum']      = $this->rapportageDatum;
	    $data['rapportageJaar']       = $this->rapportageJaar; 
      $data['portefeuille']         = $this->selectieData['Portefeuille']; 
    
      $this->portefeuilles[]=$data;
    }
    else //meerdere portefeuilles
    {
    	if(!$this->superUser)
	  	  $join = $this->queryPortefeuilleJoin;
	    // controle op einddatum portefeuille
	    $extraquery  .= " Portefeuilles.Einddatum > '".$this->rapportageDatum."' AND";
	
	    // selectie scherm.
	    if($this->selectieData['portefeuilleTm'])
	    	$extraquery .= " (Portefeuilles.Portefeuille >= '".$this->selectieData['portefeuilleVan']."' AND Portefeuilles.Portefeuille <= '".$this->selectieData['portefeuilleTm']."') AND";
	    if($this->selectieData['vermogensbeheerderTm'])
	    	$extraquery .= " (Portefeuilles.Vermogensbeheerder >= '".$this->selectieData['vermogensbeheerderVan']."' AND Portefeuilles.Vermogensbeheerder <= '".$this->selectieData['vermogensbeheerderTm']."') AND ";
    	if($this->selectieData['accountmanagerTm'])			
	    	$extraquery .= " (Portefeuilles.Accountmanager >= '".$this->selectieData['accountmanagerVan']."' AND Portefeuilles.Accountmanager <= '".$this->selectieData['accountmanagerTm']."') AND ";
    	if($this->selectieData['depotbankTm'])			
	    	$extraquery .= " (Portefeuilles.Depotbank >= '".$this->selectieData['depotbankVan']."' AND Portefeuilles.Depotbank <= '".$this->selectieData['depotbankTm']."') AND ";
    	if($this->selectieData['AFMprofielTm'])			
	    	$extraquery .= " (Portefeuilles.AFMprofiel >= '".$this->selectieData['AFMprofielVan']."' AND Portefeuilles.AFMprofiel <= '".$this->selectieData['AFMprofielTm']."') AND ";
	    if($this->selectieData['RisicoklasseTm'])			
	    	$extraquery .= " (Portefeuilles.Risicoklasse >= '".$this->selectieData['RisicoklasseVan']."' AND Portefeuilles.Risicoklasse <= '".$this->selectieData['RisicoklasseTm']."') AND ";
    	if($this->selectieData['SoortOvereenkomstTm'])			
	    	$extraquery .= " (Portefeuilles.SoortOvereenkomst >= '".$this->selectieData['SoortOvereenkomstVan']."' AND Portefeuilles.SoortOvereenkomst <= '".$this->selectieData['SoortOvereenkomstTm']."') AND ";
	    if($this->selectieData['RemisierTm'])			
		    $extraquery .= " (Portefeuilles.Remisier >= '".$this->selectieData['RemisierVan']."' AND Portefeuilles.Remisier <= '".$this->selectieData['RemisierTm']."') AND ";
	    if($this->selectieData['clientTm'])			
		    $extraquery .= " (Portefeuilles.Client >= '".$this->selectieData['clientVan']."' AND Portefeuilles.Client <= '".$this->selectieData['clientTm']."') AND ";

	    if($this->type == 'maandRapportage' || $this->type == 'kwartaalRapportage')
		    $extraquery .= " Portefeuilles.kwartaalAfdrukken > 0 AND ";
		
	    if($this->selectieData['orderNaam'])
		    $orderQuery = " ORDER BY Portefeuilles.Client" ;
	    else 
      {
       	if($this->superUser)
        {
	        $orderQuery = " ORDER BY Portefeuilles.Portefeuille ";
        }
        else 
        {
          $join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'";

       	  $this->DB->SQL("SELECT Vermogensbeheerders.AfdrukSortering FROM Vermogensbeheerders ".$join." ORDER BY Vermogensbeheerders.Vermogensbeheerder LIMIT 1");
	        $this->DB->Query();
	        $afdrukSortering = $this->DB->nextRecord();
	        $afdrukSortering['AfdrukSortering'];
	        if($afdrukSortering['AfdrukSortering'] != "")
	        {
		       $orderQuery = " ORDER BY ".$afdrukSortering['AfdrukSortering'];
	        }
	        else 
	        {
		       $orderQuery = " ORDER BY Portefeuilles.Client ";
	        }
        } 
      }

	     // check begin datum rapportage!
	    $query = " SELECT Portefeuilles.Portefeuille FROM Portefeuilles, Vermogensbeheerders $join 
	               WHERE $extraquery Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder $orderQuery ";

      $this->DB->SQL($query);
      $this->DB->Query();	
      while($dbData=$this->DB->nextRecord())
      {
        $data['rapportageDatumVanaf'] = $this->rapportageDatumVanaf;
        $data['rapportageDatum']      = $this->rapportageDatum;
	      $data['rapportageJaar']       = $this->rapportageJaar; 
        $data['portefeuille']         = $dbData['Portefeuille']; 
    
        $this->portefeuilles[]=$data;
      }
    }
  }
  
  function getPortefeuilleInstellingen($portefeuille)
  {
    global $USR;
   	if(!$this->superUser)
		  $join = $this->queryPortefeuilleJoin;

		if($this->backoffice)
		{
		  if($this->type == 'dagRapportage')
		    $select = 'Vermogensbeheerders.Export_dag_pad as path, Vermogensbeheerders.Export_data_dag as data, ';
		  elseif ($this->type == 'maandRapportage')
		    $select = 'Vermogensbeheerders.Export_maand_pad as path,  Vermogensbeheerders.Export_data_maand as data, ';
		  elseif ($this->type == 'kwartaalRapportage')
		    $select = 'Vermogensbeheerders.Export_kwartaal_pad as path, Vermogensbeheerders.Export_data_kwartaal as data, ';
		    
		  $unserialize = true; 
		}
		else 
		{
		  $select = "Vermogensbeheerders.AfdrukvolgordeOIH as OIH,	Vermogensbeheerders.AfdrukvolgordeOIS as OIS, Vermogensbeheerders.AfdrukvolgordeOIR as OIR, 	
				  	     Vermogensbeheerders.AfdrukvolgordeHSE as HSE, Vermogensbeheerders.AfdrukvolgordeOIB as OIB, Vermogensbeheerders.AfdrukvolgordeOIV as OIV, 	
				  	     Vermogensbeheerders.AfdrukvolgordePERF as PERF, Vermogensbeheerders.AfdrukvolgordeVOLK as VOLK, Vermogensbeheerders.AfdrukvolgordeVHO as VHO, 	
				  	     Vermogensbeheerders.AfdrukvolgordeTRANS as TRANS, Vermogensbeheerders.AfdrukvolgordeMUT as MUT, Vermogensbeheerders.AfdrukvolgordeGRAFIEK as GRAFIEK,
				  	     Vermogensbeheerders.Export_data_frontOffice as data, ";
		}

	  $query = "SELECT Clienten.naam,	Portefeuilles.Startdatum, Portefeuilles.portefeuille, Portefeuilles.Einddatum,	Portefeuilles.BeheerfeeAantalFacturen, Portefeuilles.RapportageValuta, $select Vermogensbeheerders.attributieInPerformance, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Vermogensbeheerders.Vermogensbeheerder
				  	 FROM Portefeuilles, Vermogensbeheerders, Clienten $join 
				  	 WHERE Portefeuilles.Portefeuille = '$portefeuille' AND Portefeuilles.Client = Clienten.Client AND Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ";

	$this->DB->SQL($query); 
	$this->DB->Query();
	$pdata = $this->DB->nextRecord();

  if(strlen($pdata['data']) > 0)
    $unserialise = true; 
  
	if($unserialise == true)
  {
    $preData = unserialize($pdata['data']); 
    listarray($preData);
    foreach ($preData as $key=>$value)
    {

      $pdata['volgorde'][$key]=$value['volgorde'];
      if ($value['dag'] || $value['maand'])
      {
      
        if ($value['jaar'])
          $jaar = $value['jaar'];
        else 
          $jaar = $this->rapportageJaar;
          
        $pdata['periode'][$key]['start']= jul2sql(mktime(0,0,0,$value['maand'],$value['dag'],$jaar));
      }
      else 
        $pdata['periode'][$key]['start'] = $this->rapportageDatumVanaf;
      
      $pdata['periode'][$key]['stop'] = $this->rapportageDatum;  
    }
    unset($pdata['data']);
  }

	return $pdata;
  }
    
  function genereerRapport()
  {
    global $__appvar;
    $aantalPortefeuilles = count($this->portefeuilles);

	  foreach ($this->portefeuilles as $pdata)
	  {
	    if($this->backoffice)
	    {	  
	      if(empty($progressBarSet))
	      {
         echo template($__appvar["templateContentHeader"],$content);
	      	// maak progressbar
	       $this->prb = new ProgressBar(536,8);
	       $this->prb->color = 'maroon';
	       $this->prb->bgr_color = '#ffffff';
	       $this->prb->brd_color = 'Silver';
	       $this->prb->left = 0;
	       $this->prb->top = 	0;	 
	       $this->prb->show();

	       $this->prb->moveStep(0);
	       $pro_step = 0;
	      
	       $pro_multiplier = 100 / $aantalPortefeuilles;
	       $progressBarSet = true;
	      }
	     $pro_step += $pro_multiplier;
		   $this->prb->moveStep($pro_step);	
	    }
	    
      $PortefeuilleInstellingen = $this->getPortefeuilleInstellingen($pdata['portefeuille']);

      if(is_array($PortefeuilleInstellingen['volgorde']))
        $volgordeData = $PortefeuilleInstellingen['volgorde'];
      else 
        $volgordeData = $PortefeuilleInstellingen;
        
      if(!is_array($PortefeuilleInstellingen['periode']))    
      {
        foreach ($volgordeData as $key=>$value)
        {
        $periode[$key]['start']= $pdata['rapportageDatumVanaf'];
        $periode[$key]['stop']= $pdata['rapportageDatum'];
        }
        reset($volgordeData);
      }
      else 
      {
        $periode = $PortefeuilleInstellingen['periode'];
      }
     
      $pdata=array_merge($pdata,$PortefeuilleInstellingen);

      $this->pdf->PortefeuilleStartdatum = $pdata['Startdatum'];
      if (db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($pdata['rapportageDatumVanaf']))
      {
        $pdata['rapportageDatumVanaf'] = $this->pdf->PortefeuilleStartdatum;
      }
      
      $this->Portefeuille=$pdata['portefeuille'];
      
      if($pdata['RapportageValuta'] != "EUR" && $pdata['RapportageValuta'] != "")
	    {
	      $this->pdf->rapportageValuta  = $pdata['RapportageValuta'];
  	    $this->pdf->ValutaKoersBegin  = getValutaKoers($pdata['RapportageValuta'],$pdata['rapportageDatumVanaf']);
  	    $this->pdf->ValutaKoersEind   = getValutaKoers($pdata['RapportageValuta'],$pdata['rapportageDatum']);
	      $this->pdf->ValutaKoersStart  = getValutaKoers($pdata['RapportageValuta'],$pdata['rapportageJaar']."-01-01");//$rapportageDatumVanaf);
	    }
  	  else 
  	  {
  	    $this->pdf->rapportageValuta = "EUR";
  	    $this->pdf->ValutaKoersEind  = 1;
	      $this->pdf->ValutaKoersStart = 1;
	      $this->pdf->ValutaKoersBegin = 1;
	    }
    
	    if (db2jul($this->pdf->PortefeuilleStartdatum) > mktime(0,0,0,1,1,$this->rapportageJaar))
      {
        $this->extrastart = $this->pdf->PortefeuilleStartdatum;
        $startData['rapJan'] = $this->pdf->PortefeuilleStartdatum;
      }
      else 
      {
        $this->extrastart = jul2db(mktime(0,0,0,1,1,$this->rapportageJaar));
        $startData['rapJan'] = jul2db(mktime(0,0,0,1,1,$this->rapportageJaar));
      }    
	    
      $startData['rapStart'] = $pdata['rapportageDatumVanaf'];
      $startData['rapDatum'] = $pdata['rapportageDatum'];
   
      $this->vulTijdelijkeRapportage($pdata['portefeuille'],$startData,$this->pdf->rapportageValuta);
      
	    // set volgorde
	    reset ($this->rapport_type);
  
	    $volgorde = $volgordeData;
	    foreach ($this->rapport_type as $rapporttype)
	    {
	      if(isset($volgordeData[$rapporttype]))
	        $volgorde[$rapporttype] = $volgordeData[$rapporttype];
	    }

	    loadLayoutSettings($this->pdf, $pdata['portefeuille']);	
 
	    if($this->selectieData['logoOnderdrukken'])
	      $this->pdf->rapport_logo='';
	      
	    $rapporten = $this->rapport_type;
	        
	    if($this->inclBrief == true)
	    {
	      $rapporten[]    = 'BRIEF';
	      if(!isset( $volgorde['BRIEF']))
	        $volgorde['BRIEF'] = 0; 
	    }
    
	    if($this->selectieData['voorbladWeergeven'] == true && ($this->pdf->rapport_layout == 14 || $this->pdf->rapport_layout == 16 ||  $this->pdf->rapport_layout == 17) )
	    {
	      $rapporten[]    = 'FRONT'; 
	      if(!isset( $volgorde['FRONT']))
	        $volgorde['FRONT'] = 0; 
	    }
	    	  //factuur toevoegen?
	    if($this->selectieData['inclFactuur'] == 1 && 
		    ($pdata['BeheerfeeAantalFacturen'] == 4 || 
		    ($pdata['BeheerfeeAantalFacturen'] == 1 &&  substr($pdata['rapportageDatum'],5,5) == '12-31') ||
		    ($pdata['BeheerfeeAantalFacturen'] == 2 && (substr($pdata['rapportageDatum'],5,5) == '06-30') || (substr($pdata['rapportageDatum'],5,5) == '12-31') )
		    )|| $this->type == 'factuur')			
	  	{
	      $rapporten[]    = 'FACTUUR';
	      if(!isset($volgorde['FACTUUR']))
	        $volgorde['FACTUUR'] = 20;
		  }

	    asort($volgorde, SORT_NUMERIC);
 
	    while (list($key, $value) = each($volgorde))
	    {
		    if(in_array($key,$rapporten))
		    {
		    if(!is_array($vorigePeriode))
		      $vorigePeriode=$periode[$key];
		      
		    if($vorigePeriode['start'] != $periode[$key]['start'] || $vorigePeriode['stop'] != $periode[$key]['stop'])  
		    {
		 	    $this->updateTijdelijkeRapportage($pdata['portefeuille'],$periode[$key],$this->pdf->rapportageValuta);   
		    }
		    if (isset($periode[$key]['start']))
		      $startDatum = $periode[$key]['start'];
		    else 
		      $startDatum = $pdata['rapportageDatumVanaf'];
		      
		    if (isset($periode[$key]['stop']))  
		      $rapportDatum = $periode[$key]['stop'];
		    else 
		      $rapportDatum = $pdata['rapportageDatum'];
		     
			  switch($key)
			  { 
			    case 'BRIEF' :
            $rapport = new kwartaalBrief($this->pdf);
            $rapport->maakBrief('kwartaalBrief',$pdata); 
			    break;
			    case "FRONT" :
					  $rapport = new RapportFront($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					  $rapport->writeRapport();
				  break;
				  case "HSE" :
				    if($this->pdf->rapport_layout == 13)
					  {
					    $rapport = new RapportHSEP($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					    $rapport->writeRapport();
					  }
					  else 
					  {
					    $rapport = new RapportHSE($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					    $rapport->writeRapport();
					  }
				  break;
				  case "MUT" :
					  if($this->pdf->rapport_layout == 7)
				  	{
				  		$rapport = new RapportMUT2($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
				  	}
				  	else
					  {
				  		$rapport = new RapportMUT($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					  }
					  $rapport->writeRapport();
				  break;
				  case "OIB" :
				     if (file_exists("rapport/include/RapportOIB_L".$this->pdf->rapport_layout.".php"))
				     {
				       include_once("rapport/include/RapportOIB_L".$this->pdf->rapport_layout.".php");
				       $classString = 'RapportOIB_L'.$this->pdf->rapport_layout;
	             $rapport = new $classString($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
				     }
				     else 
				     {
					    $rapport = new RapportOIB($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
				     }
					   $rapport->writeRapport();
				  break;
				  case "OIH"  :
					     $rapport = new RapportOIH($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					     $rapport->writeRapport();
				  break;
				  case "OIS"  :
				    if (file_exists("rapport/include/RapportOIBS_L".$this->pdf->rapport_layout.".php"))
				    {
				       include_once("rapport/include/RapportOIBS_L".$this->pdf->rapport_layout.".php");
				       $classString = 'RapportOIBS_L'.$this->pdf->rapport_layout;
	             $rapport = new $classString($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
				    }
				    elseif($this->pdf->rapport_layout == 11 || $this->pdf->rapport_layout == 17) // Geen speciale layout meer voor HEK maar zelfde als Layout 7
					  {
					  	$rapport = new RapportOIBS2($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					  }
					  else
					  {
					  	$rapport = new RapportOIBS($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					  }
					  $rapport->writeRapport();
				  break;
			   	case "OIV" :
					  $rapport = new RapportOIV($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					  $rapport->writeRapport();
				  break;
				  case "PERF" :
					  if ($pdata['attributieInPerformance'] == 1)
					  {
					    $rapport = new RapportATT($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					  }
					  else  
					  {
					    if (file_exists("rapport/include/RapportPERF_L".$this->pdf->rapport_layout.".php"))
			        {
  			        include_once("rapport/include/RapportPERF_L".$this->pdf->rapport_layout.".php");
				        $classString = 'RapportPERF_L'.$this->pdf->rapport_layout;
	              $rapport = new $classString($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
			        }
			        else 
					    $rapport = new RapportPERF($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					  }
					  $rapport->writeRapport();
				  break;
          case "PERFD" :
 					  $rapport = new RapportPERFD($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					  $rapport->writeRapport();
				  break;			
				  case "TRANS" :
				    if (file_exists("rapport/include/RapportTRANS_L".$this->pdf->rapport_layout.".php"))
			      {
  			      include_once("rapport/include/RapportTRANS_L".$this->pdf->rapport_layout.".php");
  			      $classString = 'RapportTRANS_L'.$this->pdf->rapport_layout;
	            $rapport = new $classString($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
			      }
  			    else
					    $rapport = new RapportTRANS($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					  $rapport->writeRapport();
				  break;
				  case "VHO" :
			      if (file_exists("rapport/include/RapportVHO_L".$this->pdf->rapport_layout.".php"))
			      {
  			      include_once("rapport/include/RapportVHO_L".$this->pdf->rapport_layout.".php");
				      $classString = 'RapportVHO_L'.$this->pdf->rapport_layout;
	            $rapport = new $classString($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
			      }
			      else 
			      {
				     $rapport = new RapportVHO($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
				    }
					  $rapport->writeRapport();
				  break;
				  case "VOLK" :
			      if (file_exists("rapport/include/RapportVOLK_L".$this->pdf->rapport_layout.".php"))
			      {
  			      include_once("rapport/include/RapportVOLK_L".$this->pdf->rapport_layout.".php");
				      $classString = 'RapportVOLK_L'.$this->pdf->rapport_layout;
	            $rapport = new $classString($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
			      }
			      else 
			      {
				      $rapport = new RapportVOLK($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);   
			      }
					  $rapport->writeRapport();
				  break;
				  case "HSEP" :
					  $rapport = new RapportHSEP($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					  $rapport->writeRapport();
				  break;
				  case "VOLKD" :
					  $rapport = new RapportVOLKD($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					  $rapport->writeRapport();
				  break;
				  case "OIR" :
					  $rapport = new RapportOIR($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
					  $rapport->writeRapport();
				  break;
				  case "GRAFIEK" :
				   if (file_exists("rapport/include/RapportGRAFIEK_L".$this->pdf->rapport_layout.".php"))
			     {
  			     include_once("rapport/include/RapportGRAFIEK_L".$this->pdf->rapport_layout.".php");
				     $classString = 'RapportGRAFIEK_L'.$this->pdf->rapport_layout;
	           $rapport = new $classString($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
			     }
			     else 
			     {
					  $rapport = new RapportGRAFIEK($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
			     }
					 $rapport->writeRapport();
				  break;
				  case "ATT" :
				   if (file_exists("rapport/include/RapportATT_L".$this->pdf->rapport_layout.".php"))
			     {
  			     include_once("rapport/include/RapportATT_L".$this->pdf->rapport_layout.".php");
				     $classString = 'RapportATT_L'.$this->pdf->rapport_layout;
	           $rapport = new $classString($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
			     }
			     else 
			     {
					  $rapport = new RapportATT($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
			     }
				  $rapport->writeRapport();
				  break;
				  case "CASH" :
				   if (file_exists("rapport/include/RapportCASH_L".$this->pdf->rapport_layout.".php"))
			     {
  			     include_once("rapport/include/RapportCASH_L".$this->pdf->rapport_layout.".php");
				     $classString = 'RapportCASH_L'.$this->pdf->rapport_layout;
	           $rapport = new $classString($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
			     }
			     else 
			     {
					  $rapport = new RapportATT($this->pdf, $pdata['portefeuille'], $startDatum, $rapportDatum);
			     }
				  $rapport->writeRapport();
				  break;				  
				  case "FACTUUR" :
				    $rapport = new Factuur($this->pdf, $pdata['portefeuille'],$startDatum, $rapportDatum,$this->selectieData);	
			
		        if($rapport->waarden['portefeuille'] == $pdata['portefeuille'])
	          { 
		          $rapport->factuurnummer = $this->selectieData['factuurnummer'];
		          $rapport->__appvar = $__appvar;
			        $rapport->writeRapport();
			        $this->selectieData['factuurnummer']++;
	          }
				  break;  
			  }
			  $vorigePeriode=$periode[$key]; 
		  } 
	  }


	  unset($this->pdf->customPageNo);
	  
	  if($this->selectieData['exportToFiles'] == 1)
	  {
	    $this->pdfFilename = $this->createExportNaam($pdata['portefeuille'],$pdata['rapportageDatum']);//$pdata['portefeuille'].".pdf";
	    $this->pdfUitvoer();
	    $this->startNewPdf();
	  }
	  
	  $this->verwijderTypelijkeRapportage($pdata['portefeuille'],$pdata['rapportageDatumVanaf'],$pdata['rapportageDatum']);
  }
  
  if($this->backoffice && is_object($this->prb))
	  $this->prb->hide();
	  
	if($aantalPortefeuilles == 0)
	{
	  echo "<b>Geen portefeuilles binnen selectie.</b><br>";
	}
	  
	if(empty($this->selectieData['exportToFiles']))
    $this->pdfUitvoer();
  else 
   echo "Export voltooid.<br>";  
}

  function createExportNaam($portefeuille,$rapportageDatum)
  {
    if($this->backoffice)
    {
      if(substr($portefeuille,0,3) == 211)
      {
	  	  $voorzet = str_replace("211","",$portefeuille);
	    }
	    else
	    {
		    $voorzet = $portefeuille;
	    }

  		switch($this->type)
		  {
		    case "dagRapportage" :
          $extra = "";
	      break;  
	      case "maandRapportage" :
			  	$jr = date("y",db2jul($rapportageDatum));
			  	$mm = date("m",db2jul($rapportageDatum));
			  	$extra = $jr.$mm;
	      break;  
	      case "kwartaalRapportage" :
				  //yyqq
			  	$jr = date("y",db2jul($rapportageDatum));
			  	$qq = floor((date("m",db2jul($rapportageDatum)) / 4))  + 1;
			  	$qq = "Q".$qq;
		  	  $extra = $jr.$qq;
        break;
		  }
		  $naam = $voorzet.'VOLK'.$extra.'.pdf';
    }
    else 
    {
      $naam = 'test.pdf';
      
    }
	  return $naam;
  }
  
  function updateTijdelijkeRapportage($portefeuille,$data=array(),$rapportageValuta = 'EUR')
  {
      $julStart = db2jul($data['stop']);
  	  $rapportMaand = date("m",$julStart);
	    $rapportDag = date("d",$julStart);
	    $rapportJaar = date("Y",$julStart);
	    
	    if($rapportMaand == 1 && $rapportDag == 1)
	    	$startjaar = true;
  	  else
	  	  $startjaar = false;
    
      $fondswaarden =  berekenPortefeuilleWaarde($portefeuille,$data['stop'],$startjaar,$rapportageValuta,$data['start']); 
 //echo "update $portefeuille,".$data['stop'].",$startjaar,$rapportageValuta,".$data['start']." <br>\n";    
  	  verwijderTijdelijkeTabel($portefeuille,$data['stop']);
  	  vulTijdelijkeTabel($fondswaarden ,$portefeuille,$data['stop']);
  	  $this->rapData[$data['stop']]=$data['stop'];
    
  }
  
  function vulTijdelijkeRapportage($portefeuille,$rapData=array(),$rapportageValuta = 'EUR')
  {
  	foreach ($rapData as $key=>$value)
  	{
  	  $datumClean = jul2sql(db2jul($value)); 
      
  	  if($key=='rapStart')
 	     $rapStart = $datumClean;
 
  	   $this->rapData[$datumClean]=$datumClean;
  	}
  	
  	foreach ($this->rapData as $datum)
  	{
  	  $julStart = db2jul($datum);
  	  $rapportMaand = date("m",$julStart);
	    $rapportDag = date("d",$julStart);
	    $rapportJaar = date("Y",$julStart);
	  
	    if($rapportMaand == 1 && $rapportDag == 1)
	    	$startjaar = true;
  	  else
	  	  $startjaar = false;
 //echo "eerste vulling $portefeuille,$datum,$startjaar,$rapportageValuta,$rapStart <br>\n";
  	  $fondswaarden =  berekenPortefeuilleWaarde($portefeuille,$datum,$startjaar,$rapportageValuta,$rapStart); 
  	  verwijderTijdelijkeTabel($portefeuille,$datum);
  	  vulTijdelijkeTabel($fondswaarden ,$portefeuille,$datum);
  	  
  	}
  	
  	//listarray($rapStartData);
  	
   }
  
  function verwijderTypelijkeRapportage($portefeuille,$startDatum,$rapportageDatum)
  {
    verwijderTijdelijkeTabel($portefeuille,$startDatum);
	  verwijderTijdelijkeTabel($portefeuille,$rapportageDatum);
	
    if(isset($this->extrastart))
      verwijderTijdelijkeTabel($portefeuille,$this->extrastart);  
      
    foreach ($this->rapData as $datum)
    {
      verwijderTijdelijkeTabel($portefeuille,$datum);
    }
      
    if($this->type == 'frontConsolidatie')
	  {
	   $this->consolidatieVerwijderen(); 
	  }
  }
  
  function pdfUitvoer()
  {
    global $__appvar,$USR;
    if($this->backoffice)
    {    
      if(empty($this->pdfFilename))
        $this->pdfFilename =  $USR."BACKOF.pdf";
    
      if($this->selectieData['save'])
         $this->pdfSave = '1';
      else 
         $this->pdfSave = '0';   
      
      if($this->type == 'factuur')   
        $this->pdfFilename = $USR.mktime()."FAC";
        
      if ($this->selectieData['nullenOnderdrukken'])
			  $this->pdf->nullenOnderdrukken = 1;
      
     switch($this->selectieData['filetype'])
     {
		  case "cvs" :
			 	$filetype = "csv";
			 	$this->pdfFilename = $filename.'.'.$filetype;
			  $this->pdf->OutputCSV($__appvar['tempdir'].$this->pdfFilename,"F");
		  break;
		  case "xls" :
		  	$filetype = "xls";
		  	$this->pdfFilename = $filename.'.'.$filetype;
		    $this->pdf->OutputXls($__appvar['tempdir'].$this->pdfFilename,"S");
	  	break;		
	  	default:
		  	$filetype = "pdf";
		    $this->pdf->Output($__appvar['tempdir'].$this->pdfFilename,"F");
		  break;
	   }

     if(empty($this->selectieData['exportToFiles']))
     {
       $this->pdfPush($this->pdfFilename,$this->pdfSave);
       echo template($__appvar["templateContentFooter"],$content);
     }
    }
    else 
    {
    
      if($this->selectieData['save'] == 1)
	    {
		    if(count($this->rapport_type) == 2)
		    {
			    $rapportnaam = $this->rapport_type[1];
		    }
		    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	      $this->pdf->Output($this->Portefeuille.$rapportnaam.".pdf","D");
	    }
	    else
	    {
		    $this->pdf->Output();
	    }
    }
  }
  
  function pdfPush($file='rapport.pdf',$save='0')
  {
   
?>    
<script type="text/javascript">
function pushpdf(file,save)
{
 var width='800'; 
 var height='600'; 
 var target = '_blank'; 
 var location = 'pushFile.php?file=' + file;
 if(save == 1)
 {
   // opslaan als bestand
   document.location = location + '&action=attachment';
 }
 else
 {
   // pushen naar PDF reader
   var doc = window.open("",target,'toolbar=no,status=yes,scrollbars=yes,location=no,menubar=yes,resizable=yes,directories=no,width=' + width + ',height= ' + height);
   doc.document.location = location;
 }
}
pushpdf('<?=$file?>',<?=$save?>);
</script>
<?
  }
  
  function consolidatieAanmaken()
  {
   global $USR;
   $n=0;
   foreach ($this->selectieData['selectedFields'] 	 as $portefeuille)
   {	
	  // controle of gebruiker bij vermogensbeheerder mag
     $pdata = $this->getPortefeuilleInstellingen($portefeuille);

	
	   if ($n==0)
      $hoofdPdata= $pdata;
      
     
     if (db2jul($pdata['Startdatum']) > db2jul($this->rapportageDatumVanaf) && db2jul($pdata['rapportageDatumVanaf']) > db2jul($this->rapportageDatumVanaf))
     {
       $hoofdPdata['rapportageDatumVanaf'] = $pdata['Startdatum'];
     }

	   if(db2jul($this->rapportageDatum) > db2jul($pdata['Einddatum']))
	   {
		  echo "<b>Fout: portefeille $portefeuille heeft een einddatum  (".date("d-m-Y",db2jul($pdata['Einddatum'])).")</b>";
		  exit;
	   }


    $query = "SELECT Rekening, Valuta, Memoriaal, Termijnrekening, Tenaamstelling , RenteBerekenen, Rente30_360 FROM Rekeningen WHERE Portefeuille = '".$portefeuille."'"; 
	  $this->DB->SQL($query);
	  $this->DB->Query();
    while($rekening = $this->DB->NextRecord())
      $rekeningen[] = array('rekening'=>$rekening['Rekening'],
                            'valuta'=>$rekening['Valuta'],
                            'memoriaal'=>$rekening['Memoriaal'],
                            'tenaamstelling'=>$rekening['Tenaamstelling'], 
                            'termijnrekening'=>$rekening['Termijnrekening'],
                            'RenteBerekenen'=>$rekening['RenteBerekenen'],
                            'Rente30_360'=>$rekening['Rente30_360'] )  ;
    $n++;  
  }

  $portefeuille = 'C_'.$USR;
  $pdata = $hoofdPdata;    
  
  $queries = array();  
  $queries[] = "DELETE FROM Clienten WHERE Client = '".$USR."' AND Naam = 'Consolidatie(".$USR.")' "; 
	$queries[] = "INSERT INTO Clienten SET Client = '".$USR."' , Naam = 'Consolidatie(".$USR.")' "; 

	$queries[] = "DELETE FROM Portefeuilles WHERE Client = '".$USR."' AND Portefeuille = '".$portefeuille."' "; 
	$queries[] = "INSERT INTO Portefeuilles SET Client = '".$USR."' ,
	                                        Portefeuille = '".$portefeuille."' , 
	                                        Startdatum = '".$pdata['Startdatum'] ."',
	                                        Depotbank = '".$pdata['Depotbank'] ."' ,
	                                        AEXVergelijking = '".$pdata['AEXVergelijking'] ."' ,
	                                        Vermogensbeheerder = '".$pdata['Vermogensbeheerder'] ."' "; 
  $queries[] = "DELETE FROM Rekeningen WHERE Portefeuille = '".$portefeuille."'";   
  
	for ($a=0; $a < count($queries); $a++)
  {
	$this->DB->SQL($queries[$a]);
	$this->DB->Query();
  } 
	
	foreach ($rekeningen as $rekening)
	{ 
	  $query = "INSERT INTO Rekeningen SET Portefeuille = '".$portefeuille."',
	                                     Rekening = '".$rekening['rekening'] ."',
	                                     Valuta = '".$rekening['valuta'] ."',
	                                     Memoriaal = '".$rekening['memoriaal'] ."',
	                                     Tenaamstelling = '".$rekening['tenaamstelling'] ."',
	                                     Termijnrekening = '".$rekening['termijnrekening'] ."',
	                                     RenteBerekenen = '".$rekening['RenteBerekenen'] ."',
	                                     Rente30_360 = '".$rekening['Rente30_360'] ."' "; 
	  $this->DB->SQL($query); 
	  $this->DB->Query();
	}

	$this->selectieData['Portefeuille'] = $portefeuille;
    
  }
  
  function consolidatieVerwijderen()
  {
    global $USR;
    $portefeuille = 'C_'.$USR;
    $queries = array();  
    $queries[] = "DELETE FROM Rekeningen WHERE Portefeuille = '".$portefeuille."'"; 
	  $queries[] = "DELETE FROM Clienten WHERE Client = '".$USR."' AND Naam = 'Consolidatie(".$USR.")'"; 
	  $queries[] = "DELETE FROM Portefeuilles WHERE Client = '".$USR."' AND Portefeuille = '".$portefeuille."' "; 

	  for ($a=0; $a < count($queries); $a++)
    {
	   $this->DB->SQL($queries[$a]);
	   $this->DB->Query();
    } 
  }
  
  
}


?>