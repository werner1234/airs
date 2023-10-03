<?php

class backofficeAfdrukken2
{

  function backofficeAfdrukken2($selectie)
  {

    $this->selectie=$selectie;

    $jointmp = "INNER JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$_SESSION['USR']."'";
    $query="SELECT Vermogensbeheerders.AfdrukSortering,Vermogensbeheerders.CrmPortefeuilleInformatie,check_module_FACTUURHISTORIE FROM Vermogensbeheerders ".$jointmp." ORDER BY Vermogensbeheerders.Vermogensbeheerder LIMIT 1";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $data = $DB->nextRecord();
    $this->vermogensbeheerderData=$data;
    $this->paginas=array();
    $this->preRun=false;
    
    //$this->verzending=array();
  }

  function validate()
  {
    global $__appvar;
    if(!empty($this->selectie['datumVan']) && !empty($this->selectie['datumTm']))
	  {
	  	$dd = explode("-",$this->selectie['datumVan']);
	  	if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
	  	{
	  		logScherm("<b>Fout: ongeldige datum opgegeven!</b>");
	  		exit;
	  	}
	  	$dd = explode("-",$this->selectie['datumTm']);
	  	if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
	  	{
	  		logScherm("<b>Fout: ongeldige datum opgegeven!</b>");
	  		exit;
	  	}
	  }
	  else
  	{
  		logScherm("<b>Fout: geen datum opgegeven!</b>");
  		exit;
  	}

  	$this->selectie['datumVan'] 							= form2jul($this->selectie['datumVan']);
  	$this->selectie['datumTm'] 								= form2jul($this->selectie['datumTm']);
  	$this->selectie['backoffice'] 						= true;
    $valutaDatum = getLaatsteValutadatum();
    $valutaJul = db2jul($valutaDatum);

	  if($this->selectie['datumTm'] > $valutaJul + 86400 && !$this->vermogensbeheerderData['check_module_FACTUURHISTORIE'])
	  {
	  	logScherm("<b>Fout: kan niet in de toekomst rapporteren.</b>");
	  	exit;
	  }
	  if($this->selectie['datumVan'] > $this->selectie['datumTm'])
	  {
	  	logScherm("<b>Fout: Van datum kan niet groter zijn dan  T/m datum! </b>");
	  	exit;
	  }
	  if( strlen($this->selectie['rapport_types']) <= 1 && $this->selectie['inclFactuur'] != 1 && $this->selectie['CRM_rapport_vink'] !=1)
	  {
	  	logScherm("<b>Fout: geen rapport type opgegeven </b>");
	  	exit;
	  }
    
	  $rapport_type = explode("|",$this->selectie['rapport_types']);
	  if(count($rapport_type) < 1 && $this->selectie['CRM_rapport_vink'] !=1)
	  {
	  	logScherm("<b>Fout: geen rapport type opgegeven </b>");
	  	exit;
	  }
	  $this->selectie['rapport_type']=$rapport_type;
	  if($this->selectie['voorbladWeergeven'] )
	  {
	    $this->selectie['rapport_type'][]    = 'FRONT';
	    $this->selectie['volgorde']['FRONT'] = 0;
      $this->selectie['volgorde']['FRONTC'] = 0;
	  }
    $this->checkSystem($__appvar['tempdir']);
    
	  return $this->selectie;
  }

  function initPrb($aantal)
  {
		// maak progressbar
  	$this->prb = new ProgressBar(536,8);
  	$this->prb->color = 'maroon';
	  $this->prb->bgr_color = '#ffffff';
	  $this->prb->brd_color = 'Silver';
   	$this->prb->left = 0;
	  $this->prb->top = 	0;
  	$this->prb->show();
  	$this->prb->moveStep(0);
  	$this->pro_step = 0;
  	$this->pro_multiplier = 100 / $aantal;
  }
  
  function addTemplate($pdf,$pageNo)
  {
     $templateId = $pdf->importPage($pageNo);//importPage
     $size =   $pdf->getTemplateSize($templateId);
     if ($size['w'] > $size['h']) 
       $pdf->AddPage('L', array($size['w'], $size['h']));
     else 
       $pdf->AddPage('P', array($size['w'], $size['h']));
     $pdf->useTemplate($templateId);    
    
  }

  function initPdf()
  {
    global $__appvar;
    /*
    if(isset($this->pdf))
    {
      //logScherm("Gebruikt geheugen voor unset pdf : ".round(memory_get_usage()/1024/1024,3)." MB.");
     // unset($this->pdf);
      //logScherm("Gebruikt geheugen na unset pdf : ".round(memory_get_usage()/1024/1024,3)." MB.");
      //exit;
    }
    */
    
	  $this->pdf = new PDFRapport('L','mm');
	  $this->pdf->SetAutoPageBreak(true,15);
	  $this->pdf->pagebreak = 190;
	  $this->pdf->__appvar = $__appvar;
	  $this->pdf->selectData = $this->selectie;
	  $this->pdf->FactuurDrempelPercentage = $this->selectie['drempelPercentage'];
	  $this->pdf->memoOnderdrukken = $this->selectie[ 'memoOnderdrukken'];
	  $this->pdf->lastPOST = $this->selectie;
    $this->pdf->selectData['allInOne']=true; 
    $this->pdf->oddEvenCheck=array();
  }

  function getPortefeuilles($consolilidatieAlleenVoorbereiden)
  {
     if($this->vermogensbeheerderData['AfdrukSortering'] != "")
     {
       if($this->vermogensbeheerderData['AfdrukSortering']=='Postcode')
       {
          if($this->vermogensbeheerderData['CrmPortefeuilleInformatie'] == 1)
             $order = "CRM_naw.verzendPc";
          else
             $order = "Clienten.Pc";
        }
        else
           $order = "Portefeuilles.".$this->vermogensbeheerderData['AfdrukSortering'];
      }
      else
        $order = "Portefeuilles.Client";
    $order .= ", Portefeuilles.Portefeuille";
    $this->portefeuilleSelectie= new portefeuilleSelectie($this->selectie,$order); //$afdrukSortering['AfdrukSortering']
    if($this->selectie['consolidatieToevoegen']>0)
      $this->portefeuilleSelectie->addConsolidatiePortefeuilles();
     // $this->portefeuilleSelectie->consolidatieAanmaken(true,true,$consolilidatieAlleenVoorbereiden);
      
    $this->portefeuilles=$this->portefeuilleSelectie->getSelectie(false);
    if(count($this->portefeuilles) < 1)
    {
		  logScherm("<b>Fout: geen portefeuilles binnen selectie!</b>");
		  exit;
	  }
  }
  
  function getClusters($portefeuilles,$selectData)
  {
    if($selectData['PortefeuilleClustersVan'] <> '' && $selectData['PortefeuilleClustersTm'] <> '' && $selectData['PortefeuilleClustersVan'] <> 'alles' && $selectData['PortefeuilleClustersTm'] <> 'alles')
    {
      $extraWhere=" AND (portefeuilleClusters.cluster>='" . mysql_real_escape_string($selectData['PortefeuilleClustersVan']) . "' AND portefeuilleClusters.cluster<='" . mysql_real_escape_string($selectData['PortefeuilleClustersTm']) . "')";
    }
    else
    {
      $extraWhere='';
    }
    $db=new DB();
    $queryWhere='(';
    for($i=1;$i<31;$i++)
    {
      if($queryWhere<>'(')
        $queryWhere.=" OR ";
      $queryWhere.="Portefeuille".$i." IN('".implode("','",$portefeuilles)."')";
    }
    $queryWhere.=")";
    $query="SELECT * FROM portefeuilleClusters WHERE $queryWhere $extraWhere";
    $db->SQL($query);
    $db->Query();
    $clusters=array();
    while($data=$db->nextRecord())
    {
      $port=array();
      for($i=1;$i<31;$i++)
      {
        if($data['portefeuille'.$i] <> '')
        {
          $port[]=$data['portefeuille'.$i];
        }
        unset($data['portefeuille'.$i]);
      }
      $data['portefeulles']=$port;
      $clusters[] = $data;
    }
    return $clusters;
  }

  function getExtraAdres($portefeuille)
  {
    $db=new DB();
    $query="SELECT CRM_naw_adressen.* 
            FROM 
            CRM_naw_adressen 
            JOIN CRM_naw ON CRM_naw.id=CRM_naw_adressen.rel_id 
            WHERE CRM_naw.portefeuille='$portefeuille' AND (CRM_naw_adressen.rapportage=1 OR CRM_naw_adressen.evenement='rapportage') ";
    $db->SQL($query);
    $db->Query();
    $this->extraAdres=array();
    while($data=$db->nextRecord())
      $this->extraAdres[]=$data;
  }

  function getCrmRapport($portefeuille)
  {
     $this->pdf->lastPOST = $this->selectie;
     $pdata=$this->portefeuilles[$portefeuille];
     $rapport_type=$this->selectie['rapport_type'];
     $vink_rapport_type=$this->selectie['rapport_type'];
     $dagen=($this->selectie['datumTm']-$this->selectie['datumVan'])/86400;
     
     if($pdata['CrmPortefeuilleInformatie'] > 0 && $this->selectie['CRM_rapport_vink'] > 0)
	   {
	     if($this->selectie['periode']=='Kwartaalrapportage')
	       $selectField='k';
	     elseif($this->selectie['periode']=='Maandrapportage')
	       $selectField='m';
	     elseif($this->selectie['periode']=='Dagrapportage' && $this->selectie['type'] =='portaal')
	       $selectField='d';
	     elseif($this->selectie['periode']=='Halfjaarrapportage')
	       $selectField='h';
	     elseif($this->selectie['periode']=='Jaarrapportage')
	       $selectField='j';
	     elseif($dagen < 32)
	       $selectField='m';
	     elseif ($dagen < 100)
	       $selectField='k';
	     elseif ($dagen < 200)
	       $selectField='h';
	     else
	       $selectField='j';

	     if($this->portefeuilleSelectie->selectData['periode']=='Maandrapportage')
	       $selectField='m';
	     if($this->portefeuilleSelectie->selectData['periode']=='Kwartaalrapportage')
	       $selectField='k';
	     if($this->portefeuilleSelectie->selectData['periode']=='Halfjaarrapportage')
	       $selectField='h';
	     if($this->portefeuilleSelectie->selectData['periode']=='Jaarrapportage')
	       $selectField='j';

	     $query="SELECT rapportageVinkSelectie FROM CRM_naw WHERE portefeuille='".$pdata['Portefeuille']."'";
	     $db=new DB();
       $db->SQL($query); 
       $rapportage = $db->lookupRecord();
       if($db->records())
       {
         $rapport_typeDb=unserialize($rapportage['rapportageVinkSelectie']);

         $rapport_typen=$rapport_typeDb;
         if(is_array($rapport_typen['opties']))
         {
           foreach(array('vvgl','perc','opbr','kost','GB_STORT_ONTTR','GB_overige','TRANS_RESULT','PERFG_totaal','PERFG_perc','perfBm','perfPstart') as $var)
             if(isset($this->pdf->lastPOST[$var]))
               unset($this->pdf->lastPOST[$var]);
           foreach($this->pdf->lastPOST as $var=>$data)
             if(substr($var,0,4)=='MUT_')
               unset($this->pdf->lastPOST[$var]);
               
           if(isset($rapport_typen['opties'][$selectField]))
           {
             foreach ($rapport_typen['opties'][$selectField] as $rapport=>$opties)
               $this->pdf->lastPOST = array_merge($this->pdf->lastPOST,$opties);         
           }
           else
           {
            foreach ($rapport_typen['opties'] as $rapport=>$opties)
               $this->pdf->lastPOST = array_merge($this->pdf->lastPOST,$opties);
           }
         }
         if(is_array($rapport_typen['aantal']))
           $this->portefeuilles[$portefeuille]['aantal']=$rapport_typen['aantal'][$selectField];

         if(is_array($rapport_typen['verzending']['rap_'.$selectField]))
           $this->portefeuilles[$portefeuille]['verzending']=$rapport_typen['verzending']['rap_'.$selectField];
                    
         if($this->portefeuilles[$portefeuille]['aantal']==='')
           $this->portefeuilles[$portefeuille]['aantal']=1;  
           
         if(count($rapport_typen['rap_'.$selectField]) > 0) 
          $rapport_type=$rapport_typen['rap_'.$selectField];
          
         if($this->portefeuilles[$portefeuille]['verzending']['geen']==1)
           $rapport_type=array();
            
        // $this->verzending[$portefeuille]=$rapport_typen['verzending']['rap_'.$selectField]; 
       }

       $this->rapport_type=$rapport_type;
       if(count($rapport_type) > 0 && $this->selectie['inclFactuur'])
           $this->factuurToevoegen=true; 
       else
         $this->factuurToevoegen=false;
         

	   }
     else
     {
       $this->rapport_type=$rapport_type;
       if(count($rapport_type) > 0 && $this->selectie['inclFactuur'])
         $this->factuurToevoegen=true;
       else
         $this->factuurToevoegen=false;
     }
     if($this->selectie['type'] =='alleenFactuur')
       $this->factuurToevoegen=true;

    $this->portefeuilles[$portefeuille]['naarPortaalViaAantal']=false;
    if(strtoupper($this->portefeuilles[$portefeuille]['aantal'])=='P')
    {
      $this->portefeuilles[$portefeuille]['aantal'] = 0;
      $this->portefeuilles[$portefeuille]['naarPortaalViaAantal']=true;
    }
    elseif($this->portefeuilles[$portefeuille]['aantal']==0)
      $this->portefeuilles[$portefeuille]['aantal']=1;
    elseif($this->selectie['testset']==2)
      $this->portefeuilles[$portefeuille]['aantal']=1;
  
  }

  function vulTijdelijkeRapportage($portefeuille)
  {
    $pdata=$this->portefeuilles[$portefeuille];
    if($this->selectie['datumVan'] < db2jul($this->pdf->PortefeuilleStartdatum))
			$this->startdatum = $this->pdf->PortefeuilleStartdatum;
		else
			$this->startdatum = jul2sql($this->selectie['datumVan']);

		$julrapport 		= db2jul($this->startdatum);
		$this->pdf->rapport_datumvanaf=$julrapport;
    $this->pdf->rapport_datum=db2jul($this->einddatum);

		$rapportMaand 	= date("m",$julrapport);
		$rapportDag 		= date("d",$julrapport);
		$rapportJaar 		= date("Y",$julrapport);

		if($rapportMaand == 1 && $rapportDag == 1)
		{
			$this->extrastart = false;
		}
		else
		{
			$this->extrastart = mktime(0,0,0,1,1,$rapportJaar);
			if($this->extrastart < 	db2jul($this->pdf->PortefeuilleStartdatum))
				$this->extrastart = $this->pdf->PortefeuilleStartdatum;
			else
				$this->extrastart = jul2db($this->extrastart);
		}

    if($pdata['layout'] == 13)
    {
      $beginjaar = substr($startdatum,0,4);
      $eindjaar = substr($einddatum,0,4);
  	  if($beginjaar < 2008 && $eindjaar > 2007)
  	  {
  	    $fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille,$beginjaar.'-01-01',1,$pdata['RapportageValuta'],$beginjaar.'-01-01');
  	    $rapportageDatum['a'] = $beginjaar.'-01-01';
  	    $fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $this->einddatum,(substr($this->einddatum, 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],'2008-01-01');
  	  }
  	  else
    	{
    		$fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille, $this->startdatum, (substr($this->startdatum, 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$this->startdatum);
    		$fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $this->einddatum,(substr($this->einddatum, 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$this->startdatum);
    	}
      vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$startdatum);
      vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$einddatum);
    }
    else
    {
	  	$fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille, $this->startdatum, (substr($this->startdatum, 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$this->startdatum);
	  	$fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $this->einddatum,(substr($this->einddatum, 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$this->startdatum);
	  	if($this->extrastart)
	  	{
	  		verwijderTijdelijkeTabel($portefeuille,$this->extrastart);
	  		$fondswaarden['c'] =  berekenPortefeuilleWaarde($portefeuille, $this->extrastart,(substr($this->extrastart, 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$this->startdatum);
	  		vulTijdelijkeTabel($fondswaarden['c'] ,$portefeuille,$this->extrastart);
	  	}
	  	verwijderTijdelijkeTabel($portefeuille,$this->startdatum);
	  	verwijderTijdelijkeTabel($portefeuille,$this->einddatum);
	  	vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$this->startdatum);
	  	vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$this->einddatum);
	  	$this->laatsteStartdatum=$this->startdatum;
  
      $this->pdf->portefeuilles=$this->appvarConsolidatieUpdate($portefeuille);
      $this->runPreprocessor($portefeuille);
    }
  }
  
  function runPreprocessor($portefeuille)
  {
    $pdata=$this->portefeuilles[$portefeuille];
    $preprocessorGevonden=false;
    if (file_exists("rapport/include/layout_".$this->pdf->rapport_layout."/PreProcessor_L".$pdata['Layout'].".php"))
    {
      include_once("rapport/include/layout_".$this->pdf->rapport_layout."/PreProcessor_L" . $pdata['Layout'] . ".php");
      $preprocessorGevonden=true;
    }
    elseif(file_exists("rapport/include/PreProcessor_L".$pdata['Layout'].".php"))
    {
      include_once("rapport/include/PreProcessor_L" . $pdata['Layout'] . ".php");
      $preprocessorGevonden=true;
    }
    if($preprocessorGevonden==true)
    {
      $classString = 'PreProcessor_L'.$pdata['Layout'];
      logScherm("Starten PreProcessor voor $portefeuille");
      $processor= new $classString($portefeuille,'',$this->pdf);
    }
  }

  function vulTijdelijkeRapportageAfwijkend($portefeuille,$afwijkendeStart)
  {
    if($this->laatsteStartdatum != $afwijkendeStart)
    {
      $pdata=$this->portefeuilles[$portefeuille];
      //echo "$portefeuille, ".$this->einddatum.",false,".$pdata['RapportageValuta'].",$afwijkendeStart) <br>\n";

      logScherm("Afwijkende periode $afwijkendeStart -> ".$this->einddatum);
      $fondswaarden = berekenPortefeuilleWaarde($portefeuille, $afwijkendeStart, (substr($afwijkendeStart, 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$afwijkendeStart);
      vulTijdelijkeTabel($fondswaarden ,$portefeuille,$afwijkendeStart);
      $fondswaarden = berekenPortefeuilleWaarde($portefeuille, $this->einddatum,(substr($this->einddatum, 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$afwijkendeStart);
      vulTijdelijkeTabel($fondswaarden ,$portefeuille,$this->einddatum);
      $this->laatsteStartdatum=$afwijkendeStart;
      $this->runPreprocessor($portefeuille);
    }
  }

  function setVolgorde($portefeuille)
  {
    if($this->selectie['CRM_rapport_vink'] > 0)
      $selectField='Export_data_frontOffice';
    elseif($this->selectie['periode']=='Kwartaalrapportage')
	    $selectField='Export_data_kwartaal';
    elseif($this->selectie['periode']=='Maandrapportage')
	    $selectField='Export_data_maand';
    elseif($this->selectie['periode']=='Dagrapportage')
	    $selectField='Export_data_dag';      
	  else
	    $selectField='Export_data_frontOffice';

    $rapportVolgorde=unserialize($this->portefeuilles[$portefeuille][$selectField]);

    $volgordeSet=false;
    foreach ($rapportVolgorde as $rapport=>$data)
      if($data['volgorde'] <> '')
        $volgordeSet=true;
    if($volgordeSet==false)
      $rapportVolgorde=unserialize($this->portefeuilles[$portefeuille]['Export_data_frontOffice']);
    elseif ($this->selectie['periode'] <> 'Clienten')
      $this->selectie['rapport_type']=array();

	  foreach ($rapportVolgorde as $rapport=>$instellingen)
	  {
	    if($volgordeSet == true && $instellingen['checked'] == 1 && $this->selectie['periode'] <> 'Clienten')
	      $this->selectie['rapport_type'][]=$rapport;
	    $this->volgorde[$rapport]=$instellingen['volgorde'];
	    $this->portefeuilles[$portefeuille]['Afdrukvolgorde'.$rapport]=$instellingen['volgorde'];
	    if($rapport=='FRONT')
        $this->portefeuilles[$portefeuille]['AfdrukvolgordeFRONTC']=$instellingen['volgorde'];
	    $this->portefeuilles[$portefeuille]['AfwijkendeStart_'.$rapport]=$instellingen['jaar']."-".$instellingen['maand']."-".$instellingen['dag'];
	  }

	  if($this->selectie['type']=='factuur' || (count($this->selectie['rapport_type'])==0 && $this->selectie['inclFactuur']==1))
	    $this->selectie['rapport_type']=array('factuur');

  }

  function loadPdfSettings($portefeuille)
  {
    $pdata=$this->portefeuilles[$portefeuille];
    if($this->selectie['logoOnderdrukken'])
	    $this->pdf->rapport_logo='';

  	if($pdata['RapportageValuta'] != "EUR" && $pdata['RapportageValuta'] != "")
  	{
	    $this->pdf->rapportageValuta = $pdata['RapportageValuta'];
	    $this->pdf->ValutaKoersBegin  = getValutaKoers($this->pdf->rapportageValuta,$this->startdatum);
	    $this->pdf->ValutaKoersEind  = getValutaKoers($this->pdf->rapportageValuta,$this->einddatum);
	    $this->pdf->ValutaKoersStart = getValutaKoers($this->pdf->rapportageValuta,substr($this->einddatum,0,4)."-01-01");//$rapportageDatumVanaf);
  	}
  	else
  	{
  	  $this->pdf->rapportageValuta = "EUR";
  	  $this->pdf->ValutaKoersEind  = 1;
  	  $this->pdf->ValutaKoersStart = 1;
  	  $this->pdf->ValutaKoersBegin = 1;
  	}

  	$this->pdf->PortefeuilleStartdatum = substr($pdata['Startdatum'],0,10);
  	$this->pdf->rapport_datumvanaf=db2jul($this->startdatum);
  	$this->pdf->rapport_datum=db2jul($this->einddatum);

		if($pdata['attributieInPerformance'])
		{
		  $volgorde["ATT"]=$volgorde["PERF"];
		  unset($volgorde["PERF"]);
		}
  }
  
  function setPaginaStats($portefeuille,$locatie,$pagina,$rapport='',$extraAdres='')
  {
    $this->paginas[$portefeuille]['portefeuille']=$portefeuille;
    $this->paginas[$portefeuille]['aantal']=$this->portefeuilles[$portefeuille]['aantal'];
    $this->paginas[$portefeuille]['verzending']=$this->portefeuilles[$portefeuille]['verzending'];
    if(is_array($extraAdres))
    {
      $id=$extraAdres['id'].'_'.$extraAdres['naam'];
      if(!is_array($this->paginas[$portefeuille]['extra'][$id]['adres']))
        $this->paginas[$portefeuille]['extra'][$id]['adres']=$extraAdres;
    }
    if($rapport=='')
    {
      if(!isset($this->paginas[$portefeuille][$locatie]))
        $this->paginas[$portefeuille][$locatie]=count($this->pdf->pages);
      else
      { 
        if(is_array($extraAdres))
          $this->paginas[$portefeuille]['extra'][$id][$locatie]=count($this->pdf->pages);
      }  
    }
    else
    {
      if(!isset($this->paginas[$portefeuille]['rap'][$rapport][$locatie]))
        $this->paginas[$portefeuille]['rap'][$rapport][$locatie]=count($this->pdf->pages);
      else  
      {
        if(is_array($extraAdres))
          $this->paginas[$portefeuille]['extra'][$id]['rap'][$rapport][$locatie]=count($this->pdf->pages);
      }
    }
  }
  
  function appvarConsolidatieUpdate($portefeuille)
  {
    global $__appvar;
    if($this->portefeuilles[$portefeuille]['consolidatie']==1 || count($this->portefeuilles[$portefeuille]['portefeuilles']) > 1)
    {
      $__appvar['consolidatie']['rekeningOnderdrukken'] = true;
      $__appvar['consolidatie']['portefeuillenaam1']='';
      $__appvar['consolidatie']['portefeuillenaam2']='';
      $DB = new DB();
      $DB->SQL("SELECT * FROM GeconsolideerdePortefeuilles WHERE VirtuelePortefeuille='".$portefeuille."'");
      $DB->Query();
      $pdata = $DB->nextRecord();
      $consolidatiePaar=$pdata;
      $portefeuilles=array();
      for($i=1;$i<41;$i++)
        if($pdata['Portefeuille'.$i] <> '')
          $portefeuilles[] = $pdata['Portefeuille'.$i];
  
      $query = "SELECT Portefeuille FROM PortefeuillesGeconsolideerd WHERE VirtuelePortefeuille='" . $portefeuille . "' ORDER BY Portefeuille";
      $DB->SQL($query);
      $DB->Query();
      while ($vpdata = $DB->nextRecord())
      {
        $portefeuilles[] = $vpdata['Portefeuille'];
      }
      if(isset($consolidatiePaar))
      {
        $naam=getCrmNaam($portefeuille);
        if($naam['naam'] <>'')
        {
          $__appvar['consolidatie']['portefeuillenaam1']=$naam['naam'];
          $__appvar['consolidatie']['portefeuillenaam2']=$naam['naam1'];
        }
        else
        {
          $DB->SQL("SELECT Portefeuilles.Client,Clienten.Naam,Clienten.Naam1 FROM Portefeuilles JOIN Clienten ON Portefeuilles.Client=Clienten.Client WHERE Portefeuilles.Portefeuille='".$portefeuille."'");
          $DB->Query();
          $client = $DB->nextRecord();
          $__appvar['consolidatie']['portefeuillenaam1']=$client['Naam'];
          $__appvar['consolidatie']['portefeuillenaam2']=$client['Naam1'];
        }
      }
      if($pdata['Portefeuille1'] <> '')
      {
        $DB->SQL("SELECT accountmanager,tweedeAanspreekpunt FROM Portefeuilles WHERE Portefeuille='".$pdata['Portefeuille1']."'");
        $DB->Query();
        $hoofdPdata = $DB->nextRecord();
        $__appvar['consolidatie']['accountmanager']=$hoofdPdata['accountmanager'];
        $__appvar['consolidatie']['tweedeAanspreekpunt']=$hoofdPdata['tweedeAanspreekpunt'];
      }
      return $portefeuilles;
    }
    else
    {
      unset($__appvar['consolidatie']);
      return array($portefeuille);
    }
  }
  
  function addReports($portefeuille,$extraAdres='',$factuurOnly=false)
  {
    global $__appvar;

    $this->factuurAangemaakt=false;
    $pdata=$this->portefeuilles[$portefeuille];
		asort($this->volgorde, SORT_NUMERIC);
    reset($this->volgorde);
		$this->pdf->rapportCounter = $this->teller;

		if($pdata['aantal']=='')
		  $pdata['aantal']=1;

	  if($pdata['aantal']==0)
	    logScherm("Aantal rapport afdrukken voor $portefeuille is 0.");

    if($this->selectie['type']=='xlsRapport')
    {
      $xlsFile=$__appvar['tempdir']."/".$portefeuille.'_'.$this->einddatum.'.xls';
      $workbook = new Spreadsheet_Excel_Writer($xlsFile);
    }
    	  
    $this->setPaginaStats($portefeuille,'begin',count($this->pdf->pages),'',$extraAdres);
    $this->pdf->extraAdres=$extraAdres;
    $this->pdf->factuurInXls=false;

    loadLayoutSettings($this->pdf, $portefeuille,$extraAdres);

	  if($this->selectie['logoOnderdrukken'])
	    $this->pdf->rapport_logo='';

	   if($this->factuurToevoegen==1 || count($this->rapport_type) >0)
	   {
	     logScherm("Portefeuillewaarde berekenen voor $portefeuille");
	     $this->vulTijdelijkeRapportage($portefeuille);

	   }
	   else
	     logScherm("Portefeuillewaardeberekening overslaan voor $portefeuille");


     if($this->selectie['type']=='alleenFactuur')
       $this->rapport_type=array();
     if($this->factuurToevoegen==1 && !in_array('factuur',$this->rapport_type) && !in_array('FACTUUR',$this->rapport_type))
       $this->rapport_type[]='FACTUUR';
	   $this->pdf->customPageNo = 0;
	   $this->pdf->volgorde=$this->volgorde;
	   $this->pdf->rapport_typen=$this->rapport_type;

     foreach ($this->volgorde as $key=>$value)
	   {
		   if(in_array($key,$this->rapport_type))
		   {
		    	$this->teller++;
		    	$key= ereg_replace("[^A-Za-z0-9]", "", $key);

          if($key=='CASHFLOWY')
            $key='CASHY';
      
          $this->pdf->portefeuilles=$this->appvarConsolidatieUpdate($portefeuille);
          $this->pdf->__appvar = $__appvar;
          if($pdata['consolidatie']==1 && $key=='FRONT')
          {
            $key='FRONTC';
          }
  
          $classString='';
		    	if (strtoupper($key) == 'FACTUUR')
		    	{
            loadLayoutSettings($this->pdf, $portefeuille);
            $maandDag=substr($this->einddatum,5,5);
            if($pdata['BeheerfeeFacturatieVanaf'] <> '0000-00-00' && $pdata['BeheerfeeAantalFacturen'] == 1 && substr($pdata['BeheerfeeFacturatieVanaf'],5,2)==substr($this->einddatum,5,2))
              $jaarFactuurToevoegen=true;
            else
              $jaarFactuurToevoegen=false;
            if($this->factuurAangemaakt==false && $this->factuurToevoegen == 1 &&
              (
                $pdata['BeheerfeeAantalFacturen'] == 12 || $this->pdf->rapport_layout == 13 || ($pdata['BeheerfeeAantalFacturen'] == 4 && $this->pdf->kwartaalFactuurEindKwartaal==false) ||
                ($pdata['BeheerfeeAantalFacturen'] == 4 && ($maandDag == '03-31' || $maandDag == '06-30' || $maandDag == '09-30' || $maandDag == '12-31')) ||
                ($pdata['BeheerfeeAantalFacturen'] == 1 && $maandDag == '12-31' || $jaarFactuurToevoegen) ||
                ($pdata['BeheerfeeAantalFacturen'] == 2 && ($maandDag == '06-30' || $maandDag == '12-31'))
              ))
		         {
		           
               $this->pdf->extraAdres=$extraAdres;
               $rapport = new Factuur($this->pdf, $portefeuille, date("Y-m-d",$this->selectie['datumVan']), $this->einddatum, $this->extrastart);
		           if($rapport->waarden['portefeuille'])
	             {
		             $rapport->factuurnummer = $this->getFactuurNummer($portefeuille);//$this->selectie['factuurnummer'];
		             $rapport->__appvar = $__appvar;
                 $this->setPaginaStats($portefeuille,'begin',count($this->pdf->pages),'FACTUUR',$extraAdres);
                 $this->pdf->templateVars['FACTUURpaginasBegin']=$this->pdf->page;
			           $rapport->writeRapport();
                 logScherm("rapportage FACTUUR voor $portefeuille aangemaakt");
                 $this->setPaginaStats($portefeuille,'eind',count($this->pdf->pages),'FACTUUR',$extraAdres);
                 $this->pdf->templateVars['FACTUURpaginasEind']=$this->pdf->page;
                 if($rapport->waarden['portefeuille'])
                   $factuur=true;
                 $this->factuurAangemaakt=true;
                 if(count($rapport->waardenDb)>0)
		               $this->factuurWaarden[]=$rapport->waardenDb;
                 $this->pdf->factuurInXls=true;
	             }
               elseif($this->pdf->factuurInXls==false)
                 $this->pdf->factuurInXls=true;
		         }

		    	}
          elseif (file_exists("rapport/include/layout_".$this->pdf->rapport_layout."/Rapport".$key."_L".$this->pdf->rapport_layout.".php"))
          {
             include_once("rapport/include/layout_".$this->pdf->rapport_layout."/Rapport".$key."_L".$this->pdf->rapport_layout.".php");
             $classString = 'Rapport'.$key.'_L'.$this->pdf->rapport_layout;
          }
		    	elseif (file_exists("rapport/include/Rapport".$key."_L".$this->pdf->rapport_layout.".php"))
		    	{
		  	     include_once("rapport/include/Rapport".$key."_L".$this->pdf->rapport_layout.".php");
		  	     $classString = 'Rapport'.$key.'_L'.$this->pdf->rapport_layout;
		    	}
		    	else
		    	{
		    	  if(file_exists("rapport/Rapport".$key.".php"))
            {
		    	   include_once("rapport/Rapport".$key.".php");
		  	     $classString = 'Rapport'.$key;
            }
		    	}
		    	if($pdata['AfwijkendeStart_'.$key] && $pdata['AfwijkendeStart_'.$key] <> "--")
		    	{
		    	  $startdatum='';
		    	  $nieuweStart=explode("-",$pdata['AfwijkendeStart_'.$key]);
		    	  if(trim($nieuweStart[0]) == '')
		    	    $nieuweStart[0] = date("Y",$this->selectie['datumVan']);
		    	  if($nieuweStart[1] == '')
		    	    $nieuweStart[1]=date("m",$this->selectie['datumVan']);
		    	  if($nieuweStart[2] == '')
		    	    $nieuweStart[2]=date("d",$this->selectie['datumVan']);
		     	  $statdatum=implode("-",$nieuweStart);

		     	  if(db2jul($statdatum) < 	db2jul($this->pdf->PortefeuilleStartdatum))
			        	$statdatum = $this->pdf->PortefeuilleStartdatum;
		    	}
		    	else
		    	  $statdatum=$this->startdatum;

		    	$this->vulTijdelijkeRapportageAfwijkend($portefeuille,$statdatum);
          //echo "$portefeuille $classString $statdatum ->".$this->einddatum."<br>\n";
          if($classString <> '')
          {
            if($this->selectie['type']=='xlsRapport')
              $this->pdf->excelData = array();
              
		    	  $rapport = new $classString($this->pdf, $portefeuille, $statdatum, $this->einddatum);
		    	  logScherm("rapportage $key voor $portefeuille aangemaakt");
            $this->setPaginaStats($portefeuille,'begin',count($this->pdf->pages),$key,$extraAdres);
		      	$rapport->writeRapport();
            $this->setPaginaStats($portefeuille,'eind',count($this->pdf->pages),$key,$extraAdres);

            if($this->selectie['type']=='xlsRapport')
            {   
              if(isset($rapport->rapport_xls_titel))
                $xlsTitel=$rapport->rapport_xls_titel;
              else
                $xlsTitel=$key;
              $worksheet[$i] =& $workbook->addWorksheet($xlsTitel); 			
              $rapport->pdf->fillXlsSheet($worksheet[$i],$workbook);
            }
          }
		    }
    	}
      
      if($this->selectie['type']=='xlsRapport')
      {
        $workbook->close();
        $this->xlsFiles[]=$xlsFile;
      }
    
	  	if (count($this->rapport_type) > 0 )
      {
        $templateGevonden=false;
        if(file_exists("rapport/include/layout_".$this->pdf->rapport_layout."/RapportTemplate_L".$this->pdf->rapport_layout.".php"))
        {
          include_once("rapport/include/layout_".$this->pdf->rapport_layout."/RapportTemplate_L" . $this->pdf->rapport_layout . ".php");
          $templateGevonden=true;
        }
        elseif(file_exists("rapport/include/RapportTemplate_L".$this->pdf->rapport_layout.".php"))
        {
          include_once("rapport/include/RapportTemplate_L" . $this->pdf->rapport_layout . ".php");
          $templateGevonden=true;
        }
       
        if($templateGevonden==true)
        {
   	      $classString = 'RapportTemplate_L'.$this->pdf->rapport_layout;
          if($this->pdf->IndexPage > 0)
          {
          $template = new PDFRapport('L','mm');
          $template->SetAutoPageBreak(true,15);
        	$template->pagebreak = 190;
	        $template->__appvar = $__appvar;
	        loadLayoutSettings($template, $portefeuille);
	        $template->templateVars=$this->pdf->templateVars;
	        $rapport = new $classString($template, $portefeuille, $this->startdatum, $this->einddatum);
	        $rapport->writeRapport();
          $this->pdf->pages[$this->pdf->IndexPage] = $template->pages[$template->IndexPage];
          }
          else
          {
            $rapport = new $classString($this->pdf, $portefeuille, $statdatum, $this->einddatum);
          }
        }
      }
    
	  // FACTUUR ?
    $maandDag=substr($this->einddatum,5,5);
    if($pdata['BeheerfeeFacturatieVanaf'] <> '0000-00-00' && $pdata['BeheerfeeAantalFacturen'] == 1 && substr($pdata['BeheerfeeFacturatieVanaf'],5,2)==substr($this->einddatum,5,2))
      $jaarFactuurToevoegen=true;
    else
      $jaarFactuurToevoegen=false;
    loadLayoutSettings($this->pdf, $portefeuille);
    if($this->factuurAangemaakt==false && $this->factuurToevoegen == 1 &&
      (
        $pdata['BeheerfeeAantalFacturen'] == 12 || $this->pdf->rapport_layout == 13 || ($pdata['BeheerfeeAantalFacturen'] == 4 && $this->pdf->kwartaalFactuurEindKwartaal==false) ||
        ($pdata['BeheerfeeAantalFacturen'] == 4 && ($maandDag == '03-31' || $maandDag == '06-30' || $maandDag == '09-30' || $maandDag == '12-31')) ||
        ($pdata['BeheerfeeAantalFacturen'] == 1 && $maandDag == '12-31' || $jaarFactuurToevoegen ) ||
        ($pdata['BeheerfeeAantalFacturen'] == 2 && ($maandDag == '06-30' || $maandDag == '12-31'))
      )
    )
		{
		  
      $this->pdf->extraAdres=$extraAdres;
      $rapport = new Factuur($this->pdf, $portefeuille, date("Y-m-d",$this->selectie['datumVan']), $this->einddatum, $this->extrastart);
		  if($rapport->waarden['portefeuille'])
	    {
		    $rapport->factuurnummer = $this->getFactuurNummer($portefeuille);//$this->selectie['factuurnummer'];
		    $rapport->__appvar = $__appvar;
        $this->setPaginaStats($portefeuille,'begin',count($this->pdf->pages),'FACTUUR',$extraAdres);
        $this->pdf->templateVars['FACTUURpaginasBegin']=$this->pdf->page;
		    $rapport->writeRapport();
        $this->setPaginaStats($portefeuille,'eind',count($this->pdf->pages),'FACTUUR',$extraAdres);
        $this->pdf->templateVars['FACTUURpaginasEind']=$this->pdf->page;
        if($rapport->waarden['portefeuille'])
          $factuur=true;
        $this->factuurAangemaakt=true;
        if(count($rapport->waardenDb)>0)
		      $this->factuurWaarden[]=$rapport->waardenDb;
        $this->pdf->factuurInXls=true;
	    }
      elseif($this->pdf->factuurInXls==false)
          $this->pdf->factuurInXls=true;
		}
 
    if(!isset($_SESSION['factuurNummers'][$portefeuille]))
    {
      $_SESSION['factuurNummers'][$portefeuille]=$this->selectie['factuurnummer'];
      if($factuur==true)
		    $this->selectie['factuurnummer']++;
    }
    else
    {
    if($factuur==true)
      $this->selectie['factuurnummer']=max(array_values($_SESSION['factuurNummers']))+1;
    }

    $this->setPaginaStats($portefeuille,'eind',count($this->pdf->pages),'',$extraAdres);
    
  }
  
  function getFactuurNummer($portefeuille)
  {
    if(isset($_SESSION['factuurNummers'][$portefeuille]))
    {
      logScherm("Hergebruik factuurnummer (".$_SESSION['factuurNummers'][$portefeuille].") voor $portefeuille");
      return $_SESSION['factuurNummers'][$portefeuille];
    }
    else
    {
      logScherm("Gebruik factuurnummer (".$this->selectie['factuurnummer'].") voor $portefeuille");
      return $this->selectie['factuurnummer'];
    }
  }

  function verwijderTijdelijkeRapportage($portefeuille)
  {
    if($this->extrastart)
	 	  verwijderTijdelijkeTabel($portefeuille,$this->extrastart);
	  verwijderTijdelijkeTabel($portefeuille,$this->startdatum);
	  verwijderTijdelijkeTabel($portefeuille,$this->einddatum);
  }

  function pushPdf()
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
		var doc = window.open("",target,'toolbar=no,status=yes,scrollbars=yes,location=no,menubar=yes,resizable=yes,directories=no,width=' + width + ',height=' + height);
		doc.document.location = location;
	}
}
pushpdf('<?=$this->filename?>',<?=$this->selectie['save']?>);
</script>
<?
  }

  function fixFilename($naam)
  {
    //$naam=str_replace(array('\\','/',':','?','"','<','>','|'),array('_','_','_','_','_','_','_','_'),$naam);
    $naam = preg_replace('/[^A-Za-z0-9#_.-]/', "_", $naam);
    if(strlen($naam)>60)
    {
      $parts=explode(".",$naam);
      $lastPart=count($parts)-1;
      if($lastPart>0)
      {
        $extention = $parts[$lastPart];
        unset($parts[$lastPart]);
        $naam=substr(implode(".",$parts),0,55).".".$extention;
      }
    }
    return $naam;
  }

  function getFilename($portefeuille,$factuurOnly=false,$factuurNr=false)
  {
    global $ftpClient;
    $pdata=$this->portefeuilles[$portefeuille];

    $begin='';
    if(is_array($this->selectie['bestandsnaamBegin']))
    {
      if(count($this->selectie['bestandsnaamBegin'])>1)
      {
        foreach($this->selectie['bestandsnaamBegin'] as $naamKey)
        {
          if($begin<>'')
            $begin.='_';
          $begin.=$pdata[$naamKey];
        }
      }
      else
        $begin=$portefeuille;
    }
    else
      $begin=$portefeuille;
  
    if(isset($ftpClient['VoorzetNulTekens']))
    {
      $tekens=strlen($begin);
      //logscherm("rvv $tekens ".$ftpClient['VoorzetNulTekens']);
      if($tekens < $ftpClient['VoorzetNulTekens'])
        $begin=str_pad($begin, $ftpClient['VoorzetNulTekens'], '0', STR_PAD_LEFT);
    }

    if($factuurOnly==true)
      $eind='factuur';
    else
      $eind=$this->selectie['bestandsnaamEind'];   
      
    if($eind<>'' && (substr($eind,0,1)<>'#') && substr($eind,0,1) <> '.' && substr($eind,0,1) <> '_')
      $begin.="_";
    
    if($factuurNr==2)
      $eind.='_'.$this->getFactuurNummer($portefeuille);
    elseif($factuurNr==true)
      $begin=$this->getFactuurNummer($portefeuille).'_';
  
    $middle='';
    if(isset($ftpClient['RappOmschr']))
    {
      $middle=$ftpClient['RappOmschr'];
    }
    
      
    $bestandnaam=$this->fixFilename($begin."".$middle."".$eind.".pdf");
    
    if($bestandnaam=='.pdf')
      $bestandnaam='rapport.pdf';
    
    return $bestandnaam;
 
  }

  function getFilePath($portefeuille,$type='')
  {
    global $__appvar;
    $pdata=$this->portefeuilles[$portefeuille];

    if($type == 'export')
    {
      if( $this->selectie['periode'] == 'Clienten')
        $path=$pdata['Export_dag_pad'];
      elseif( $this->selectie['periode'] == 'Maandrapportage')
        $path=$pdata['Export_maand_pad'];
      elseif( $this->selectie['periode'] == 'Kwartaalrapportage')
        $path=$pdata['Export_kwartaal_pad'];
      else
        $path=$pdata['Export_kwartaal_pad'];

    }
    else
    {
      $path=$__appvar['tempdir']."/$type/";
      if(!is_writable($path))
      {
        mkdir($path);
      }
    }

    if (is_writable($path))
    {
      return $path;
    }
    else
    {
      if(substr(php_uname('n'),-8)!='.airs.nl' &&  substr(php_uname('n'),-12) !='.airshost.nl' )
        logScherm("<b>ongeldig export pad ($path) </b>");
      $path=$__appvar['tempdir'];
      return $path;
    }
  }

  function checkSystem($path)
  {
    global $__debug;
    $status=1;
    if($__debug == true)
    {
      $space=disk_free_space($path);
      if(function_exists('sys_getloadavg'))
        $load = sys_getloadavg();
      else
        $load=array(0,0,0);  
      if($space !==false && $space < 5000000000)
      {
        $status=0;
        logscherm('<b>Er is minder dan 5GB aan diskruimte beschikbaar. ('.$space.' bytes)</b>');
      }
      else
        $status=1; 
      
      if($load[1] > 5)
        logscherm('<b>Het is druk op de server, de rapportage kan langer duren. ('.$load[2].')</b>');
    }
    return $status;
  }
  
  function pdfBriefAanmaken($portefeuille,$path)
  {
    if($this->preRun==true)
      return '';
    $pdata=$this->portefeuilles[$portefeuille];
    if($this->selectie['pdfBrief']=='' && $pdata['begeleidendeBrief']<>'')
      $brief=$pdata['begeleidendeBrief'];
    else
      $brief=$this->selectie['pdfBrief'];
      
    $begin='';
    if(is_array($this->selectie['bestandsnaamBegin']))
    {
      if(count($this->selectie['bestandsnaamBegin'])>0)
      {
        foreach($this->selectie['bestandsnaamBegin'] as $naamKey)
        {
          if($begin<>'')
            $begin.='_';
          $begin.=$pdata[$naamKey];
        }
      }
      else
        $begin=$portefeuille;
    }
    else
      $begin=$portefeuille;
    if($this->selectie['bestandsnaamEind']<>'')
      $begin.="_"; 

    if($this->selectie['inclBrief'] == 1 && $brief <> '')
    {
      $mailing = new pdfMailing($pdata['CRM_nawID'],$brief);
      $name=$this->fixFilename($begin.$this->selectie['bestandsnaamEind'].'_'.$brief);
      $mailing->putPdf($path.'/'.$name,'F');
      if(is_file($path.'/'.$name))
      {
        logScherm("Brief voor $portefeuille aangemaakt.");
        return $path.'/'.$name;
      }  
        
    }
  }

  function createXlsZip()
  {
     global $__appvar;
     include_once($__appvar["basedir"]."/classes/pclzip.lib.php");
     $zipfile=$__appvar['tempdir']."/export.zip";
     $zip=new PclZip($zipfile);
     $zip->create($this->xlsFiles,PCLZIP_OPT_REMOVE_ALL_PATH);
     foreach($this->xlsFiles as $file)
       unlink($file);
     return $zipfile;  
  }
  
  function sendByEmail($portefeuille,$pdfFile,$extraAdres,$extraPdf)
  {
    if($this->preRun==true)
      return '';
    global $USR;
    $pdata=$this->portefeuilles[$portefeuille];

    $template=new templateEmail($this->selectie['email'],$this->selectie['onderwerp']);
    $allPdata=$template->getPortefeuileValues($portefeuille);

    if(is_array($extraAdres))
    {
      $pdata=array_merge($pdata,$extraAdres);
      $allPdata=array_merge($allPdata,$extraAdres);
    }
    
    $email=$template->templateData($allPdata);
    
    if(!$this->valid_email_quick($this->selectie['afzenderEmail']))
    {
      logScherm("Klaarzetten emails mislukt. Geen geldig afzender adres ingesteld.");
      exit;
    }

    if (!$this->valid_email_quick($pdata['email']))
    {
      logScherm("Klaarzetten van email voor " .$pdata['email']." mislukt. Geen geldig emailadres ingesteld.");
      $afbreken = true;
    }

    if($afbreken == false)
    {
	     $db=new DB();
        $fields=array('crmId'=>$allPdata['CRM_id'],
            'status'=>'aangemaakt',
            'senderName'=>$this->selectie['afzender'],
            'senderEmail'=>$this->selectie['afzenderEmail'],
            'ccEmail'=>$this->selectie['ccEmail'],
            'bccEmail'=>$this->selectie['bccEmail'],
            'receiverName'=>$pdata['naam'],
            'receiverEmail'=>$pdata['email'],
            'subject'=>$email['subject'],
            'bodyHtml'=>$email['body']);
        $query="INSERT INTO emailQueue SET add_date=now(),add_user='$USR',change_date=now(),change_user='$USR'";
        foreach ($fields as $key=>$value)
          $query.=",$key='".mysql_escape_string($value)."'";

	      $db->SQL($query);
	      $db->Query();
	      $lastId=$db->last_id();
     
       if($pdfFile<>'')
       {
        $handle = fopen($pdfFile, "r");
        $contents = fread($handle, filesize($pdfFile));
        fclose($handle);
        unlink($pdfFile);
        $blobData = bin2hex($contents);
        //$db->SQL($blobData);
     	  //$blobData = pack("H*" , $blobData);
        $query="INSERT INTO emailQueueAttachments SET add_date=now(),add_user='$USR',change_date=now(),change_user='$USR', emailQueueId='$lastId',filename='".basename($pdfFile)."', Attachment=unhex('$blobData')";
	      $db->SQL($query);
	      $db->Query();
 	      logScherm("Rapportage voor $portefeuille (" .$pdata['email'].") in wachtrij geplaatst.");
       }
        if($this->selectie['pdfBrief']=='' && $pdata['begeleidendeBrief']<>'')
          $brief=$pdata['begeleidendeBrief'];
        else
          $brief=$this->selectie['pdfBrief'];
          
          
    $begin='';
    if(is_array($this->selectie['bestandsnaamBegin']))
    {
      if(count($this->selectie['bestandsnaamBegin'])>0)
      {
        foreach($this->selectie['bestandsnaamBegin'] as $naamKey)
        {
          if($begin<>'')
            $begin.='_';
          $begin.=$pdata[$naamKey];
        }
      }
      else
        $begin=$portefeuille;
    }
    else
      $begin=$portefeuille;

    if($this->selectie['bestandsnaamEind']<>'' && substr($this->selectie['bestandsnaamEind'],0,1) <> '#')
      $begin.="_"; 
          
        if($this->selectie['inclBrief'] == 1 && $brief <> '')
        {
          $mailing = new pdfMailing($allPdata['CRM_id'],$brief);
          $blobData = bin2hex($mailing->putPdf($brief,'S'));
          $query="INSERT INTO emailQueueAttachments SET add_date=now(),add_user='$USR',change_date=now(),change_user='$USR', emailQueueId='$lastId',filename='".mysql_real_escape_string($begin.$this->selectie['bestandsnaamEind'].'_'.basename($brief))."', Attachment=unhex('$blobData')";
	        $db->SQL($query);
	        $db->Query();
 	        logScherm("Brief voor $portefeuille (" .$pdata['email'].") in wachtrij geplaatst.");
        }

     if(is_file($extraPdf))
     {
        $handle = fopen($extraPdf, "r");
        $contents = fread($handle, filesize($extraPdf));
        fclose($handle);
        unlink($extraPdf);
        $blobData = bin2hex($contents);
        $query="INSERT INTO emailQueueAttachments SET add_date=now(),add_user='$USR',change_date=now(),change_user='$USR', emailQueueId='$lastId',filename='".basename($extraPdf)."', Attachment=unhex('$blobData')";
	      $db->SQL($query);
	      $db->Query();
 	      logScherm("Factuur voor $portefeuille (" .$pdata['email'].") in wachtrij geplaatst.");
     }
    }    
  }

  function sendToPortaal($portefeuille,$pdfFile,$extraAdres,$factuurPdfData='')
  {
    if($this->preRun==true)
      return '';
    
    global $USR;
    $afbreken = false;
    $pdata=$this->portefeuilles[$portefeuille];

    if(is_array($extraAdres))
      $pdata=array_merge($pdata,$extraAdres);

    if (!$this->valid_email_quick($pdata['email']))
    {
      logScherm("Klaarzetten rapportage voor " .$pdata['email']." mislukt. Geen geldig emailadres ingesteld.");
      $afbreken = true;
    }
    
    if ($pdata['wachtwoord']=='')
    {
      logScherm("Klaarzetten rapportage voor " .$pdata['email']." mislukt. Geen geldig wachtwoord ingesteld.");
      $afbreken = true;
    }    

    if($afbreken == false)
    {
	     $db=new DB();
       $query="SELECT Gebruikers.Naam as accountmanagerGebruikerNaam,Gebruikers.emailAdres as accountmanagerEmail,Accountmanagers.Naam as accountmanagerNaam, Gebruikers.mobiel as accountmanagerTelefoon FROM
               Portefeuilles LEFT JOIN Gebruikers ON Portefeuilles.Accountmanager = Gebruikers.Accountmanager LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
               WHERE Portefeuilles.Portefeuille='".$pdata['portefeuille']."'";
       $db->SQL($query);
       $accountManager=$db->lookupRecord(); 
       
       $fields=array('crmId'=>$pdata['CRM_nawID'],
            'status'=>'aangemaakt',
            'naam'=>$pdata['naam'],
            'naam1'=>$pdata['naam1'],
            'email'=>$pdata['email'],
            'verzendAanhef'=>$pdata['verzendAanhef'],
            'accountmanagerNaam'=>$accountManager['accountmanagerNaam'],
            'accountmanagerGebruikerNaam'=>$accountManager['accountmanagerGebruikerNaam'],
            'accountmanagerEmail'=>$accountManager['accountmanagerEmail'],
            'accountmanagerTelefoon'=>$accountManager['accountmanagerTelefoon'],
            'portefeuille'=>$pdata['portefeuille'],
            'depotbank'=>$pdata['Depotbank'],
            'periode'=>substr($this->selectie['periode'],0,1),
            'raportageDatum'=>$this->einddatum,
            'crmWachtwoord'=>$pdata['wachtwoord']);

       $handle = fopen($pdfFile, "r");
       $contents = fread($handle, filesize($pdfFile));
       if($this->portefeuilleSelectie->selectData['portaallosseFactuurZonderRapportage']==true)
          $contents='';
       $blobData = bin2hex($contents);
       fclose($handle);

       $query="DELETE FROM portaalQueue WHERE periode='C' AND portefeuille='".$pdata['portefeuille']."'";
       $db->SQL($query);
	     $db->Query();
  
       $query="INSERT INTO portaalQueue SET filename='".basename($pdfFile)."', pdfData=unhex('$blobData'), pdfFactuurData=unhex('".bin2hex($factuurPdfData)."'), add_date=now(),add_user='$USR',change_date=now(),change_user='$USR'";
       foreach ($fields as $key=>$value)
         $query.=",$key='".mysql_escape_string($value)."'";

       if($factuurPdfData<>'' || $blobData<>'')
	     $db->SQL($query);
	     if($db->Query())
         logScherm("Rapportage voor $portefeuille in de portaal wachtrij geplaatst.");
         
       if($this->selectie['portaalMail']==1)
       {  
         $this->sendByEmail($portefeuille);
         logScherm("Email voor $portefeuille in de email wachtrij geplaatst.");
       }
     }


  }
  
  function sendToBeheerfeeHistorie()
  {
    global $USR;
    $DB=new DB();
    foreach ($this->pdf->concept as $portefeuille=>$factuurData)
    {
      $query="SELECT id FROM FactuurBeheerfeeHistorie WHERE portefeuille = '".$portefeuille."' AND periodeDatum = '".$this->einddatum."'";
      if($DB->QRecords($query))
      {
        $idData=$DB->nextRecord();
        $query="UPDATE FactuurBeheerfeeHistorie SET ";
        $where ="WHERE id = '".$idData['id']."' ";
      }
      else
      {
        $query= "INSERT INTO FactuurBeheerfeeHistorie SET ";
        $query.="add_date = NOW(),add_user = '$USR',";
        $where='';
      }
      //listarray($factuurData);
      $query.="portefeuille = '".$portefeuille."'";
      $query.=",factuurNr = '".$factuurData['factuurNummer']."'";
      $query.=",periodeDatum = '".$this->einddatum."'";
      $query.=",grondslag = '".$factuurData['rekenvermogen']."'";
      $query.=",beheerfee = '".$factuurData['beheerfeeBetalen']."'";
      $query.=",btw = '".$factuurData['btw']."'";
      $query.=",bedragBuitenBtw = '".$factuurData['BeheerfeeBedragBuitenBTW']."'";
      $query.=",bedragVerrekendeHuisfondsen = '".round($factuurData['huisfondsFeeJaar']*$factuurData['periodeDeelVanJaar'],2)."'";
      $query.=",bedragTotaal = '".$factuurData['beheerfeeBetalenIncl']."'";
      $query.=",change_user = '$USR',change_date = NOW()";
      $query.=$where;
      $DB->SQL($query);
      //echo $query;exit;
      $DB->Query();
    
    }
  }

  function valid_email_quick($address)
  {
    $multipleEmail=explode(";",$address);
    foreach ($multipleEmail as $address)
    {
      $address=trim($address);
      if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,20})$", $address) || (strlen($address)==0))
        return false;
    }
    return true;
  }

  function sendToDossier($portefeuille,$pdfFile,$unlink=true)
  {
    global $USR;
    if($this->preRun==true)
      return '';
      
    $pdata=$this->portefeuilles[$portefeuille];
    $db=new DB();
    $table='CRM_naw';
    if($db->QRecords("SELECT id FROM $table WHERE portefeuille = '".$pdata['Portefeuille']."'")>0)
    {
      $store=true;
      $id=$db->nextRecord();
      $id=$id['id'];
    }
    else
      $store=false;

    if($store)
    {
      //$filename = $exportPath.$filename;
      $filesize = filesize($pdfFile);
      $filetype = mime_content_type($pdfFile);
      $fileHandle = fopen($pdfFile, "r");
      $docdata  = fread($fileHandle, $filesize);
      fclose($fileHandle);
      /*
      $dd = new digidoc();
      $rec ["filename"] = basename($pdfFile);
      $rec ["filesize"] = "$filesize";
      $rec ["filetype"] = "$filetype";
      $rec ["categorie"] = "rapportage";
      $rec ["description"] = $this->selectie['documentOmschrijving'];
      $rec ["blobdata"] = $docdata;
      $rec ["keywords"] = basename($pdfFile);
      $rec ["module"] = $table;
      $rec ["module_id"] = $id;
      $dd->useZlib = false;
      $dd->addDocumentToStore($rec);
      */
      $query="INSERT INTO eDossierQueue SET add_date=now(),change_date=now(),
  add_user='".mysql_real_escape_string($USR)."',change_user='".mysql_real_escape_string($USR)."',
  `portefeuille` = '".mysql_real_escape_string($pdata['Portefeuille'])."',
  `filename` = '".mysql_real_escape_string(basename($pdfFile))."',
  `filesize` = '".mysql_real_escape_string($filesize)."',
  `filetype` = '".mysql_real_escape_string($filetype)."',
  `categorie` = 'rapportage',
  `description` = '".mysql_real_escape_string($this->selectie['documentOmschrijving'])."',
  `keywords` = '".mysql_real_escape_string(basename($pdfFile))."',
  `module` = '".mysql_real_escape_string($table)."',
  `module_id` = '".mysql_real_escape_string($id)."',
  `blobdata` = unhex('".bin2hex($docdata)."')";
      $db->SQL($query);
      $db->Query();
      if($unlink==true)
        unlink($pdfFile);
    }
    else
    {
      logscherm('Geen CRM record gevonden bij '.$portefeuille.'.');
    }
  }
  
  function sendByEmailLosseFactuur($portefeuille,$extraAdres)
  {
    if($this->preRun==true)
      return '';
    $this->initPdf();
    $this->loadPdfSettings($portefeuille);
    $this->addReports($portefeuille,$extraAdres,true);
    $this->factuurFilename=$this->getFilename($portefeuille,true);
    $this->pdf->Output($this->filePath.$this->factuurFilename,"F");
    //echo $this->filePath.$this->filename." , ".$this->filePath.$this->factuurFilename."<br>\n";exit;
    $this->sendByEmail($portefeuille,$this->filePath.$this->filename,$extraAdres,$this->filePath.$this->factuurFilename);
  }
  
  function cloneInit($cloneName,$password='')
  {
    if($this->preRun==false || !isset($this->pdfclone[$cloneName]))
    {
      $this->pdfclone[$cloneName] = clone($this->pdf);
      $this->pdfclone[$cloneName]->pages=array();
      $this->pdfclone[$cloneName]->PageSizes=array();
      $this->pdfclone[$cloneName]->OrientationChanges=array();
      if($password<>'' && $this->preRun==false)
        $this->pdfclone[$cloneName]->SetProtection(array('print'),$password,'!airs2011!a');//
    }
  }

  function checkOddEven($cloneName,$n,$type='begin')
  {
    logscherm('Even/oneven pagina controlle.');
    if($cloneName=='totaalPdf' || $cloneName=='lossePdf' )
    {
      if($n%2==0)
      {
        $this->pdfclone[$cloneName]->pages[$n]='';
        $this->pdfclone[$cloneName]->OrientationChanges[$n]='';
        $n++;
        logscherm('Extra witpagina toegevoegd op pagina ('.$n.' voor '.$type. ') .');
      }
    }
    return $n;
  }

  function resetVoet($cloneName)
  {
    $this->pdfclone[$cloneName]->rapport_voettext='';
    $this->pdfclone[$cloneName]->rapport_voettext_rechts='';
  }

  function cloneAddPage($cloneName,$pages)
  {
    $n=count($this->pdfclone[$cloneName]->pages)+1;
    if(isset($this->pdf->oddEvenCheck[$this->currentPortefeuille]))
      $n=$this->checkOddEven($cloneName,$n);

    $pagesToCheck=array();
    if(isset($this->pdf->oddPageReportStart[$this->currentPortefeuille]))
    {
      foreach($this->pdf->oddPageReportStart[$this->currentPortefeuille] as $rapport=>$checkPage)
        $pagesToCheck[$checkPage]=$rapport;
    }

    if(in_array($page,$pagesToCheck))
      $n=$this->checkOddEven($cloneName,$n);

    if(is_array($pages))
    {
      foreach($pages as $page)  
      {
        if(isset($pagesToCheck[$page]))
          $n=$this->checkOddEven($cloneName,$n,$pagesToCheck[$page]);
        $this->pdfclone[$cloneName]->pages[$n]=$this->pdf->pages[$page];
        $this->pdfclone[$cloneName]->OrientationChanges[$n]=$this->pdf->OrientationChanges[$page];     
        $n++; 
      }
    }
    else
    {
      if(isset($pagesToCheck[$page]))
        $n=$this->checkOddEven($cloneName,$n,$pagesToCheck[$page]);
      $this->pdfclone[$cloneName]->pages[$n]=$this->pdf->pages[$pages];
      $this->pdfclone[$cloneName]->OrientationChanges[$n]=$this->pdf->OrientationChanges[$pages];    
    }

  }
  
  function cloneWriteFile($cloneName,$type='',$portefeuille='',$prefix='',$factuurNr=false)
  {
     if($this->preRun==true)
     {
       logScherm("PreRun $cloneName ".$portefeuille.".");
       return '';
     }
     global $__appvar;
     $dateString=date('Y\mmd\tHis_');
     if($cloneName=='facturenPdf')
       $file=$__appvar['tempdir']."/".$dateString.'facturen.pdf';
     elseif(($cloneName=='lossePdf' || $cloneName=='totaalPdf') && $type=='rapportage')
       $file=$__appvar['tempdir']."/".$dateString.'rapportage.pdf';
     elseif($portefeuille=='')
       $file=$__appvar['tempdir']."/".$dateString.$cloneName.'.pdf';
     else
       $file=$this->getFilePath($portefeuille,$type).$prefix.$this->getFilename($portefeuille,false,$factuurNr);
     $this->pdfclone[$cloneName]->page=count($this->pdfclone[$cloneName]->pages);
     logScherm("schrijven pdf $cloneName ".$portefeuille);
     if(strtolower(get_class ($this->pdfclone[$cloneName]))=='pdfrapport')
       $this->pdfclone[$cloneName]->Output($file,"F");
     else
     {
       
       logscherm("<b>Geen pdf object om pdf van te maken.</b> (".get_class ($this->pdfclone[$cloneName]).")");
     }
     return $file;
  }
  
  function cloneGetPages($paginaData,$rapport,$filter='',$uitvoerType='normaal')
  {
    $pages=array();
    if($uitvoerType=='email' || $rapport=='FACTUUR')
      $overslaan=$this->pdf->emailSkipPages;
    else
      $overslaan=array();

    if($rapport=='all')
    {
        if($filter <> '')
        {
          for($i=$paginaData['rap'][$filter]['begin']+1;$i<=$paginaData['rap'][$filter]['eind'];$i++)
          {
            $overslaan[]=$i;
          }
        }
        for ($pageNo=$paginaData['begin']+1; $pageNo <= $paginaData['eind']; $pageNo++)
        {
          if(!in_array($pageNo,$overslaan))
            $pages[]=$pageNo;
        }
    }
    else
    {
        for ($pageNo=$paginaData['rap'][$rapport]['begin']+1; $pageNo <= $paginaData['rap'][$rapport]['eind']; $pageNo++)
        {
          if(!in_array($pageNo,$overslaan))
            $pages[]=$pageNo;
        }
    }

   // if($filter=='FACTUUR')
   //   listarray($pages);
    return $pages; 
  }
  
  function transferToSftp($bronFile,$doelfile)
  {
    global $ftpClient,$__appvar;
    if($con = ftp_connect($ftpClient['server']))
    {
      logScherm(vt('Verbonden met sftp server'));
      if ($login_result = ftp_login($con, $ftpClient['user'], $ftpClient['password']))
      {
        logScherm(vt('Ingelogd'));
        if ($__appvar["ftpPasv"])
        {
          ftp_pasv($con, true);
        }
        if(is_file($bronFile))
        {
          if (ftp_put($con, $doelfile.'.tmp',$bronFile, FTP_BINARY))
          {
            logScherm(vt('File verzonden'));
            if(!ftp_rename($con, $doelfile.'.tmp', $doelfile))
            {
              logScherm(vt('File hernoemen mislukt'));
            }
          }
          else
          {
            logScherm(vt('File verzenden mislukt'));
          }
        }
        else
        {
          logScherm(vt('File niet gevonden.'));
        }
        ftp_close($con);
        logScherm(vt('Connectie verbroken.'));
      }
      else
      {
        logScherm(vt('Inloggen op sftp server mislukt'));
      }
    }
    
  
  }

}
?>
