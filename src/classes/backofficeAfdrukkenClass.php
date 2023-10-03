<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/16 15:53:07 $
 		File Versie					: $Revision: 1.127 $

 		$Log: backofficeAfdrukkenClass.php,v $
 		Revision 1.127  2020/05/16 15:53:07  rvv
 		*** empty log message ***
 		
 		Revision 1.126  2020/02/09 13:34:48  rvv
 		*** empty log message ***
 		
 		Revision 1.125  2019/10/23 13:24:03  rvv
 		*** empty log message ***
 		
 		Revision 1.124  2019/04/20 17:02:39  rvv
 		*** empty log message ***
 		
 		Revision 1.123  2019/01/19 13:51:39  rvv
 		*** empty log message ***
 		
 		Revision 1.122  2019/01/16 16:33:01  rvv
 		*** empty log message ***
 		
 		Revision 1.121  2018/12/05 16:30:57  rvv
 		*** empty log message ***
 		
 		Revision 1.120  2018/10/13 17:13:51  rvv
 		*** empty log message ***
 		
 		Revision 1.119  2018/09/12 15:55:59  rvv
 		*** empty log message ***
 		
 		Revision 1.118  2018/08/18 12:40:13  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.117  2018/08/01 17:52:32  rvv
 		*** empty log message ***
 		
 		Revision 1.116  2018/04/21 17:53:00  rvv
 		*** empty log message ***
 		
 		Revision 1.115  2018/04/11 15:16:00  rvv
 		*** empty log message ***
 		
 		Revision 1.114  2018/03/18 10:53:33  rvv
 		*** empty log message ***
 		
 		Revision 1.113  2018/03/17 18:43:11  rvv
 		*** empty log message ***
 		
 		Revision 1.112  2017/12/11 12:24:43  rvv
 		*** empty log message ***
 		
 		Revision 1.111  2017/12/11 10:19:02  rvv
 		*** empty log message ***
 		
 		Revision 1.110  2017/12/09 17:48:18  rvv
 		*** empty log message ***
 		
 		Revision 1.109  2017/12/03 10:31:39  rvv
 		*** empty log message ***
 		
 		Revision 1.108  2017/08/23 16:05:55  rvv
 		*** empty log message ***
 		
 		Revision 1.107  2017/07/22 18:23:28  rvv
 		*** empty log message ***
 		
 		Revision 1.106  2016/10/29 15:36:57  rvv
 		*** empty log message ***
 		
 		Revision 1.105  2016/10/12 19:19:57  rvv
 		*** empty log message ***
 		
 		Revision 1.104  2016/06/19 15:19:45  rvv
 		*** empty log message ***
 		
 		Revision 1.103  2016/01/25 07:45:53  rvv
 		*** empty log message ***
 		
 		Revision 1.102  2016/01/23 17:56:49  rvv
 		*** empty log message ***
 		
 		Revision 1.101  2016/01/11 12:56:42  rvv
 		*** empty log message ***
 		
 		Revision 1.100  2016/01/09 18:54:21  rvv
 		*** empty log message ***
 		
 		Revision 1.99  2015/12/23 17:05:29  rvv
 		*** empty log message ***
 		
 		Revision 1.98  2015/12/09 20:12:39  rvv
 		*** empty log message ***
 		
 		Revision 1.97  2015/11/01 17:20:34  rvv
 		*** empty log message ***
 		
 		Revision 1.96  2015/10/25 13:30:30  rvv
 		*** empty log message ***
 		
 		Revision 1.95  2015/10/21 16:10:49  rvv
 		*** empty log message ***
 		
 		Revision 1.94  2015/10/18 13:37:37  rvv
 		*** empty log message ***
 		
 		Revision 1.93  2015/10/11 16:55:38  rvv
 		*** empty log message ***
 		
 		Revision 1.92  2015/09/20 17:27:52  rvv
 		*** empty log message ***
 		
 		Revision 1.91  2015/09/13 11:28:21  rvv
 		*** empty log message ***
 		
 		Revision 1.90  2015/08/23 11:35:08  rvv
 		*** empty log message ***
 		
 		Revision 1.89  2015/05/16 09:33:00  rvv
 		*** empty log message ***
 		
 		Revision 1.88  2015/04/19 08:40:22  rvv
 		*** empty log message ***
 		
 		Revision 1.87  2015/03/22 10:35:28  rvv
 		*** empty log message ***
 		
 		Revision 1.86  2015/03/18 16:41:44  rvv
 		*** empty log message ***
 		
 		Revision 1.85  2015/02/11 14:18:41  rvv
 		*** empty log message ***
 		
 		Revision 1.84  2015/02/04 16:15:54  rvv
 		*** empty log message ***
 		
 		Revision 1.83  2015/01/24 19:39:15  rvv
 		*** empty log message ***
 		
 		Revision 1.82  2014/10/13 06:02:46  rvv
 		*** empty log message ***
 		
 		Revision 1.81  2014/10/11 16:30:33  rvv
 		*** empty log message ***
 		
 		Revision 1.80  2014/07/27 11:26:00  rvv
 		*** empty log message ***
 		
 		Revision 1.79  2014/06/04 13:12:40  rvv
 		*** empty log message ***
 		
 		Revision 1.78  2014/05/07 15:41:35  rvv
 		*** empty log message ***
 		
 		Revision 1.77  2014/04/26 16:39:26  rvv
 		*** empty log message ***
 		
 		Revision 1.76  2014/03/29 16:23:55  rvv
 		*** empty log message ***
 		
 		Revision 1.75  2014/02/09 11:06:14  rvv
 		*** empty log message ***
 		
 		Revision 1.74  2014/01/15 14:53:49  rvv
 		*** empty log message ***
 		
 		Revision 1.73  2014/01/11 16:14:04  rvv
 		*** empty log message ***
 		
 		Revision 1.72  2014/01/08 16:50:10  rvv
 		*** empty log message ***
 		
 		Revision 1.71  2014/01/04 17:01:41  rvv
 		*** empty log message ***
 		
 		Revision 1.70  2013/12/24 13:12:27  rvv
 		*** empty log message ***
 		
 		Revision 1.69  2013/12/24 12:27:18  rvv
 		*** empty log message ***
 		
 		Revision 1.68  2013/12/24 11:51:55  rvv
 		*** empty log message ***
 		
 		Revision 1.67  2013/12/14 17:13:16  rvv
 		*** empty log message ***
 		
 		Revision 1.66  2013/11/30 14:18:53  rvv
 		*** empty log message ***
 		
 		Revision 1.65  2013/11/23 17:18:39  rvv
 		*** empty log message ***
 		
 		Revision 1.64  2013/11/13 15:51:14  rvv
 		*** empty log message ***
 		
 		Revision 1.63  2013/11/02 16:58:55  rvv
 		*** empty log message ***
 		
 		Revision 1.62  2013/10/12 15:48:00  rvv
 		*** empty log message ***
 		
 		Revision 1.61  2013/10/07 17:23:41  rvv
 		*** empty log message ***
 		
 		Revision 1.60  2013/10/07 12:32:57  rvv
 		*** empty log message ***
 		
 		Revision 1.58  2013/10/05 15:55:40  rvv
 		*** empty log message ***
 		
 		Revision 1.57  2013/07/15 17:04:03  rvv
 		*** empty log message ***
 		
 		Revision 1.56  2013/07/06 15:57:35  rvv
 		*** empty log message ***
 		
 		Revision 1.55  2013/05/29 15:46:36  rvv
 		*** empty log message ***
 		
 		Revision 1.54  2013/05/19 10:57:43  rvv
 		*** empty log message ***
 		
 		Revision 1.53  2013/05/04 15:52:50  rvv
 		*** empty log message ***
 		
 		Revision 1.52  2013/04/10 09:35:25  rvv
 		*** empty log message ***
 		
 		Revision 1.51  2013/03/24 09:39:05  rvv
 		*** empty log message ***
 		
 		Revision 1.50  2013/03/17 10:57:39  rvv
 		*** empty log message ***
 		
 		Revision 1.49  2013/02/17 10:58:03  rvv
 		*** empty log message ***
 		
 		Revision 1.48  2013/02/03 09:00:45  rvv
 		*** empty log message ***
 		
 		Revision 1.47  2013/01/30 16:52:40  rvv
 		*** empty log message ***
 		
 		Revision 1.46  2012/10/21 10:01:01  rvv
 		*** empty log message ***
 		
 		Revision 1.45  2012/08/08 15:34:14  rvv
 		*** empty log message ***
 		
 		Revision 1.44  2012/08/01 16:53:50  rvv
 		*** empty log message ***

 		Revision 1.43  2012/07/09 10:48:49  rvv
 		*** empty log message ***

 		Revision 1.42  2012/06/27 16:04:51  rvv
 		*** empty log message ***

 		Revision 1.41  2012/06/20 18:07:57  rvv
 		*** empty log message ***

 		Revision 1.40  2012/06/09 13:40:42  rvv
 		*** empty log message ***

 		Revision 1.39  2012/06/06 15:48:48  rvv
 		*** empty log message ***

 		Revision 1.38  2012/06/03 09:41:10  rvv
 		*** empty log message ***

 		Revision 1.37  2012/05/30 15:54:36  rvv
 		*** empty log message ***

 */



class backofficeAfdrukken
{

  function backofficeAfdrukken($selectie)
  {

    if($selectie['testset']==1)
      $selectie['selectedPortefeuilles']=array('AMB_Defensief','506389','546143','607665','44551','400658','400489','70771','907464','30882','43027','591645','798681','207164','611298','222778342','561371','236561','916323','718882','562181','140667','45350','587554576','238053','409007','603805','595836909','400837','597880','288038','288124','293091','209073','281362','281233','166037','42828','276296','211747','268021','40229','233524','221528','45267','265828','265769','475139216','515892','B556104','B298950','0835-1565484-75-000','236560','206521','229746','407491465','SALA','166218','71520','478147','526798270','462271897','44925','35717','938106','511889','539937','140708','78566','44739','227592','409009','594970','30383','400825','265541','270028','207098','571725','1030224','541391755','281348','554081','42235','206551','609986','609951','609943','609978','30213','30214','30215','281160','42992','71560','43674','155077','155078','155131','535863004','417927797','446499579','486912205','71580','MARIS0','MARIS3','71561','155037','42431','550175','30216','MARIS','236566','236110','44677','565458','281238','251534','251527','288092','288075','57774','57546','21323','288093','288465','529990','281482','43527','281307','281531','276314','276304','254340','235217','44616','43940','227037','227020','43517','574678','261544','206547','473808137','265645','288139','209080','211773','220542','564237442','564237442a','420260218','575559160','575559160a','474544869','548707243','222646675','222650729','30448','30465','40228','44341','30859','30087','30098');
 
    $this->selectie=$selectie;

    $jointmp = "INNER JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$_SESSION['USR']."'";
    $query="SELECT Vermogensbeheerders.AfdrukSortering,Vermogensbeheerders.CrmPortefeuilleInformatie,check_module_FACTUURHISTORIE FROM Vermogensbeheerders ".$jointmp." ORDER BY Vermogensbeheerders.Vermogensbeheerder LIMIT 1";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $data = $DB->nextRecord();
    $this->vermogensbeheerderData=$data;
    $this->xlsFiles=array();
  }

  function validate()
  {
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
	  }
 
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
	  $this->pdf->memoOnderdrukken = $this->selectie['memoOnderdrukken'];
	  $this->pdf->lastPOST = $this->selectie;
	  
  }

  function getPortefeuilles()
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
    $this->portefeuilleSelectie= new portefeuilleSelectie($this->selectie,$order,false); //$afdrukSortering['AfdrukSortering']
    if($this->selectie['consolidatieToevoegen']>0)
    {
      //$this->portefeuilleSelectie->consolidatieAanmaken(true, true, $consolilidatieAlleenVoorbereiden);
      $this->portefeuilleSelectie->addConsolidatiePortefeuilles();
    }
    $this->portefeuilles=$this->portefeuilleSelectie->getSelectie(false);
    if(count($this->portefeuilles) < 1)
    {
		  logScherm("<b>Fout: geen portefeuilles binnen selectie!</b>");
		  exit;
	  }
  }

  function getExtraAdres($portefeuille)
  {
    $db=new DB();
    $query="SELECT CRM_naw_adressen.* FROM CRM_naw_adressen JOIN CRM_naw ON CRM_naw.id=CRM_naw_adressen.rel_id 
            WHERE CRM_naw.portefeuille='$portefeuille' AND (CRM_naw_adressen.rapportage=1 OR CRM_naw_adressen.evenement='rapportage') ";
    $db->SQL($query);
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

	     $query="SELECT rapportageVinkSelectie FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
	     $db=new DB();
       $db->SQL($query); 
       $rapportage = $db->lookupRecord();
       if($db->records())
       {
         $rapport_typeDb=unserialize($rapportage['rapportageVinkSelectie']);
         $rapport_type=$rapport_typeDb;
         if(is_array($rapport_type['opties']))
         {
           foreach(array('vvgl','perc','opbr','kost','GB_STORT_ONTTR','GB_overige','TRANS_RESULT','PERFG_totaal','PERFG_perc','perfBm','perfPstart') as $var)
             if(isset($this->pdf->lastPOST[$var]))
               unset($this->pdf->lastPOST[$var]);
           foreach($this->pdf->lastPOST as $var=>$data)
             if(substr($var,0,4)=='MUT_')
               unset($this->pdf->lastPOST[$var]);
               
           if(isset($rapport_type['opties'][$selectField]))
           {
             foreach ($rapport_type['opties'][$selectField] as $rapport=>$opties)
               $this->pdf->lastPOST = array_merge($this->pdf->lastPOST,$opties);         
           }
           else
           {
            foreach ($rapport_type['opties'] as $rapport=>$opties)
               $this->pdf->lastPOST = array_merge($this->pdf->lastPOST,$opties);
           }    
         }    
         if(is_array($rapport_type['aantal']))
           $this->portefeuilles[$portefeuille]['aantal']=$rapport_type['aantal'][$selectField];
         if(($this->selectie['type'] =='eMail' && $rapport_type['verzending']['rap_'.$selectField]['email'])  ||
            ($this->selectie['type'] =='pdf'   && $rapport_type['verzending']['rap_'.$selectField]['papier']) ||
             $this->selectie['type'] =='eDossier'  && (($rapport_type['verzending']['rap_'.$selectField]['papier'] && $this->selectie['eDossierPdf']) || ($rapport_type['verzending']['rap_'.$selectField]['email'] && $this->selectie['eDossierEmail']))  ||
             $this->selectie['type'] =='export' && (($rapport_type['verzending']['rap_'.$selectField]['papier'] && $this->selectie['eDossierPdf']) || ($rapport_type['verzending']['rap_'.$selectField]['email'] && $this->selectie['eDossierEmail']) || ($this->selectie['eDossierPdf']==0 && $this->selectie['eDossierEmail']==0) ) || 
             $this->selectie['type'] =='portaal' )
         {
           if($this->selectie['type']=='portaal' && $pdata['check_portaalCrmVink']==1)
           {
//             if($pdata['check_portaalCrmVink']==1)
//             {
             if($rapport_type['verzending']['rap_'.$selectField]['portaal']==0)
               $rapport_type=array();
             elseif(count($rapport_type['rap_'.$selectField]) > 0)
               $rapport_type=$rapport_type['rap_'.$selectField];
             else
               $rapport_type=$this->selectie['rapport_type'];
//             }
//             else
//             {
//              $rapport_type=$this->selectie['rapport_type'];
//             }  
           }
           else
           {
             if(count($rapport_type['rap_'.$selectField]) > 0)
               $rapport_type=$rapport_type['rap_'.$selectField];
             else
               $rapport_type=$this->selectie['rapport_type'];
           }
         }
         elseif($this->selectie['type'] =='factuur')
         {
           if(($this->selectie['media'] =='email' && $rapport_type['verzending']['rap_'.$selectField]['email'])  ||
            ($this->selectie['media'] =='pdf'   && $rapport_type['verzending']['rap_'.$selectField]['papier']))
            {
              if($this->selectie['media'] =='email' && ($rapport_type['verzending']['rap_'.$selectField]['email'] && $rapport_type['verzending']['rap_'.$selectField]['papier']))
              {
                $rapport_type=array();
                $this->factuurToevoegen=false;
              }
              else
                $rapport_type=array('factuur');
            }
            else
              $rapport_type=array();
         }
         else
           $rapport_type=array();

         if($rapport_typeDb['verzending']['rap_'.$selectField]['geen']==1)
           $rapport_type=array();  
       }
       
      // echo "<br>\n<br>\n";
       //listarray($this->selectie['eDossierPdf']);exit;//eDossierEmail eDossierPdf
       $this->rapport_type=$rapport_type;
       if(count($rapport_type) > 0 && $this->selectie['inclFactuur'])
       { 
         if(($this->selectie['type'] =='eMail' && ($rapport_typeDb['verzending']['rap_'.$selectField]['email'] && $rapport_typeDb['verzending']['rap_'.$selectField]['papier'])))
           $this->factuurToevoegen=false;
         else
           $this->factuurToevoegen=true; 
       }
       else
         $this->factuurToevoegen=false;
         
       if( $this->selectie['type'] =='eDossier')
       { 
          if(($rapport_typeDb['verzending']['rap_'.$selectField]['email'] && $rapport_typeDb['verzending']['rap_'.$selectField]['papier']) &&
           $this->selectie['eDossierEmail']==1 && $this->selectie['eDossierPdf']==0)
           {
             $this->factuurToevoegen=false;
             $this->rapport_type=array();
           }
       }
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
    elseif (file_exists("rapport/include/PreProcessor_L".$pdata['Layout'].".php"))
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

//listarray($this->selectie);
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
	    $this->portefeuilles[$portefeuille]['AfwijkendeStart_'.$rapport]=$instellingen['jaar']."-".$instellingen['maand']."-".$instellingen['dag'];
	  }

	  if($this->selectie['type']=='factuur')
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
    $factuur=false;
    $this->factuurVulling=$factuur;
    $skipReports=false;
    if($this->selectie['losseFactuur']==true)
    {
      if($factuurOnly==false)
      {
        $factuur=true;
        $skipReports=false;
      }
      else
      {
        $factuur=false; 
        $skipReports=true; 
      }
    } 
  
   // $this->selectie['xlsUitvoer']=true;
    if($this->selectie['type']=='xlsRapport')
    {
      $xlsFile=$__appvar['tempdir']."/".$portefeuille.'_'.$this->einddatum.'.xls';
      $workbook = new Spreadsheet_Excel_Writer($xlsFile);
    }
    //logScherm("f: $factuur | r: $skipReports | l:".$this->selectie['losseFactuur']);
    $this->factuurAangemaakt=false;
    $pdata=$this->portefeuilles[$portefeuille];
    if(isset($extraAdres['wachtwoord']))
      $pdata['wachtwoord']=$extraAdres['wachtwoord'];
    if($this->selectie['type']=='eMail')
    {
      if(strlen($pdata['wachtwoord']) < 6)
        $this->afbreken=true;
      else
      {
        if($this->selectie['testrun']==false)
          $this->pdf->SetProtection(array('print'),$pdata['wachtwoord'],'!airs2011!a');//
        $this->afbreken=false;
        logScherm("$portefeuille ww: ".$pdata['wachtwoord']."");
      }
    }

		asort($this->volgorde, SORT_NUMERIC);
    reset($this->volgorde);
		$this->pdf->rapportCounter = $this->teller;

		if($pdata['aantal']=='')
		  $pdata['aantal']=1;

	if($pdata['aantal']==0)
	  logScherm("Aantal rapport afdrukken voor $portefeuille is 0.");

	 
   $this->pdf->factuurInXls=false;
	 for($i=0; $i<$pdata['aantal']; $i++)
	 {
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

	   $this->pdf->customPageNo = 0;
	   $this->pdf->volgorde=$this->volgorde;
	   $this->pdf->rapport_typen=$this->rapport_type;

    if($skipReports==false)
    {
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
          if(($pdata['consolidatie']==1 || count($pdata['portefeuilles'])>1) && $key=='FRONT')
          {
              if (file_exists("rapport/include/layout_".$this->pdf->rapport_layout."/RapportFRONTC_L".$this->pdf->rapport_layout.".php") ||
                file_exists("rapport/include/RapportFRONTC_L".$this->pdf->rapport_layout.".php"))
              $key='FRONTC';
          } 
           

          $classString='';
		    	if (strtoupper($key) == 'FACTUUR')
		    	{
            if($pdata['BeheerfeeFacturatieVanaf'] <> '0000-00-00' && $pdata['BeheerfeeAantalFacturen'] == 1 && substr($pdata['BeheerfeeFacturatieVanaf'],5,2)==substr($this->einddatum,5,2))
              $jaarFactuurToevoegen=true;
            else
              $jaarFactuurToevoegen=false;
            loadLayoutSettings($this->pdf, $portefeuille);
            $maandDag=substr($this->einddatum,5,5);
            if($factuur==false && $this->factuurToevoegen == 1 &&
		            (
                  $pdata['BeheerfeeAantalFacturen'] == 12 || $this->pdf->rapport_layout == 13 || ($pdata['BeheerfeeAantalFacturen'] == 4 && $this->pdf->kwartaalFactuurEindKwartaal==false) ||
                  ($pdata['BeheerfeeAantalFacturen'] == 4 && ($maandDag == '03-31' && $maandDag == '06-30' && $maandDag == '09-30' || $maandDag == '12-31')) ||
		              ($pdata['BeheerfeeAantalFacturen'] == 1 && $maandDag == '12-31' || $jaarFactuurToevoegen) ||
		              ($pdata['BeheerfeeAantalFacturen'] == 2 && ($maandDag == '06-30' || $maandDag == '12-31'))
		            )
              )
		         {
               $rapport = new Factuur($this->pdf, $portefeuille, date("Y-m-d",$this->selectie['datumVan']), $this->einddatum, $this->extrastart);
		           if($rapport->waarden['portefeuille'])
	             {
		             $rapport->factuurnummer = $this->getFactuurNummer($portefeuille);//$this->selectie['factuurnummer'];
		             $rapport->__appvar = $__appvar;
                 $this->pdf->templateVars['FACTUURpaginasBegin']=$this->pdf->page;
			           $rapport->writeRapport();
                 $this->pdf->templateVars['FACTUURpaginasEind']=$this->pdf->page;
                 if($rapport->waarden['portefeuille'])
                 {
                   $factuur = true;

                 }
                 $this->factuurAangemaakt = true;
                 $this->pdf->factuurInXls = true;
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
		      	$rapport->writeRapport();
            
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
        //$pdf->OutputXLS($rapportnaam,'S');//,"F"
        //$workbook->send();
        $workbook->close();
        $this->xlsFiles[]=$xlsFile;
        
      }
    
	  	if (count($this->rapport_type) > 0 )//&& file_exists("rapport/include/RapportTemplate_L".$this->pdf->rapport_layout.".php")
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
          $classString = 'RapportTemplate_L' . $this->pdf->rapport_layout;
          if ($this->pdf->IndexPage > 0)
          {
            $template = new PDFRapport('L', 'mm');
            $template->SetAutoPageBreak(true, 15);
            $template->pagebreak = 190;
            $template->__appvar = $__appvar;
            loadLayoutSettings($template, $portefeuille);
            $template->templateVars = $this->pdf->templateVars;
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
    }
	  // FACTUUR ?
     //unset($_SESSION['factuurNummers']);
     $maandDag=substr($this->einddatum,5,5);
     if($pdata['BeheerfeeFacturatieVanaf'] <> '0000-00-00' && $pdata['BeheerfeeAantalFacturen'] == 1 && substr($pdata['BeheerfeeFacturatieVanaf'],5,2)==substr($this->einddatum,5,2))
       $jaarFactuurToevoegen=true;
     else
       $jaarFactuurToevoegen=false;
     loadLayoutSettings($this->pdf, $portefeuille);
     if($factuur==false && $this->factuurToevoegen == 1 &&
       (
         $pdata['BeheerfeeAantalFacturen'] == 12 || $this->pdf->rapport_layout == 13 || ($pdata['BeheerfeeAantalFacturen'] == 4 && $this->pdf->kwartaalFactuurEindKwartaal==false) ||
         ($pdata['BeheerfeeAantalFacturen'] == 4 && ($maandDag == '03-31' || $maandDag == '06-30' || $maandDag == '09-30' || $maandDag == '12-31')) ||
         ($pdata['BeheerfeeAantalFacturen'] == 1 && $maandDag == '12-31' || $jaarFactuurToevoegen) ||
         ($pdata['BeheerfeeAantalFacturen'] == 2 && ($maandDag == '06-30' || $maandDag == '12-31'))
       )
     )
		{
      $rapport = new Factuur($this->pdf, $portefeuille, date("Y-m-d",$this->selectie['datumVan']), $this->einddatum, $this->extrastart);
		  if($rapport->waarden['portefeuille'])
	    {
		    $rapport->factuurnummer = $this->getFactuurNummer($portefeuille);//$this->selectie['factuurnummer'];
		    $rapport->__appvar = $__appvar;
        $this->pdf->templateVars['FACTUURpaginasBegin']=$this->pdf->page;
        $rapport->writeRapport();
        $this->pdf->templateVars['FACTUURpaginasEind']=$this->pdf->page;
        if($rapport->waarden['portefeuille'])
        {
          $factuur = true;
          $this->factuurWaarden[] = $rapport->waardenDb;
        }
        $this->factuurAangemaakt = true;
        $this->pdf->factuurInXls=true;
	    }
      elseif($this->pdf->factuurInXls==false)
          $this->pdf->factuurInXls=true;
      
     
		}
		}
    
    $maandDag=substr($this->einddatum,5,5);
    if($pdata['BeheerfeeFacturatieVanaf'] <> '0000-00-00' && $pdata['BeheerfeeAantalFacturen'] == 1 && substr($pdata['BeheerfeeFacturatieVanaf'],5,2)==substr($this->einddatum,5,2))
      $jaarFactuurToevoegen=true;
    else
      $jaarFactuurToevoegen=false;
   // echo $pdata['BeheerfeeFacturatieVanaf'] ." ".substr($pdata['BeheerfeeFacturatieVanaf'],5,2)." ".substr($this->einddatum,5,2)." ($jaarFactuurToevoegen) ".$pdata['BeheerfeeAantalFacturen']."<br>\n";exit;
    loadLayoutSettings($this->pdf, $portefeuille);
    if($this->factuurAangemaakt == false && $this->factuurToevoegen == 1 &&
      (
        $pdata['BeheerfeeAantalFacturen'] == 12 || $this->pdf->rapport_layout == 13 || ($pdata['BeheerfeeAantalFacturen'] == 4 &&  $this->pdf->kwartaalFactuurEindKwartaal==false) ||
        ($pdata['BeheerfeeAantalFacturen'] == 4 && ($maandDag == '03-31' || $maandDag == '06-30' || $maandDag == '09-30' || $maandDag == '12-31')) ||
        ($pdata['BeheerfeeAantalFacturen'] == 1 && ($maandDag == '12-31' || $jaarFactuurToevoegen)) ||
        ($pdata['BeheerfeeAantalFacturen'] == 2 && ($maandDag == '06-30' || $maandDag == '12-31'))
      )
      )
		{
     
		  
      $rapport = new Factuur($this->pdf, $portefeuille, date("Y-m-d",$this->selectie['datumVan']), $this->einddatum, $this->extrastart);
		  if($rapport->waarden['portefeuille'])
	    {
		    $rapport->factuurnummer = $this->getFactuurNummer($portefeuille);//$this->selectie['factuurnummer'];
		    $rapport->__appvar = $__appvar;
			  $rapport->writeRapport();
        if($rapport->waarden['portefeuille'])
        {
          $factuur = true;
          $this->factuurAangemaakt = true;
          $this->factuurWaarden[] = $rapport->waardenDb;
        }
          $this->pdf->factuurInXls = true;
      }
      elseif($this->pdf->factuurInXls==false)
          $this->pdf->factuurInXls=true;
		}

		if($factuur==true && !is_array($extraAdres))
    {
      if(!isset($_SESSION['factuurNummers'][$portefeuille]))
      {
        $_SESSION['factuurNummers'][$portefeuille]=$this->selectie['factuurnummer'];
		    $this->selectie['factuurnummer']++;
      }
      else
      {
        $this->selectie['factuurnummer']=max(array_values($_SESSION['factuurNummers']))+1;
      }
    }
    $this->factuurVulling=$factuur;
    //loadLayoutSettings($this->pdf,'Geen instellingen');
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
    $naam = preg_replace('/[^A-Za-z0-9_.-]/', "_", $naam);
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
  
  function getFilename($portefeuille,$factuurOnly=false)
  {
    $pdata=$this->portefeuilles[$portefeuille];
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
    if($factuurOnly==true)
      $eind='factuur';
    else
      $eind=$this->selectie['bestandsnaamEind'];   
      
    if($eind<>'')
      $begin.="_"; 

    $naam=$begin."".$eind.".pdf";
    $naam=$this->fixFilename($naam);

    if($naam=='.pdf')
      $naam='rapport.pdf';

    return $naam;
 
  }

  function getFilePath($portefeuille)
  {
    global $__appvar;
    $pdata=$this->portefeuilles[$portefeuille];

    if($this->selectie['type'] == 'export')
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
      $path=$__appvar['tempdir'];
    }

    if (is_writable($path))
      return $path;
    else
    {
      if(substr(php_uname('n'),-8)!='.airs.nl' &&  substr(php_uname('n'),-12) !='.airshost.nl' )
        logScherm("<b>ongeldig export pad ($path) </b>");
      $path=$__appvar['tempdir'];
      return $path;
    }

  }
  
  function pdfBriefAanmaken($portefeuille,$path)
  {
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
      $this->afbreken = true;
    }
    //else
    //  $this->afbreken = false;

    if($this->afbreken == false)
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

  function sendToPortaal($portefeuille,$pdfFile,$extraAdres)
  {
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
       $blobData = bin2hex($contents);
       fclose($handle);
       
       $query="DELETE FROM portaalQueue WHERE periode='C' AND portefeuille='".$pdata['portefeuille']."'";
       $db->SQL($query);
	     $db->Query();
       
       $query="INSERT INTO portaalQueue SET filename='".basename($pdfFile)."', pdfData=unhex('$blobData'), add_date=now(),add_user='$USR',change_date=now(),change_user='$USR'";
       foreach ($fields as $key=>$value)
         $query.=",$key='".mysql_escape_string($value)."'";

	     $db->SQL($query);
	     if($db->Query())
         logScherm("Rapportage voor $portefeuille in de portaal wachtrij geplaatst.<br>\n");
     }

     unlink($pdfFile);
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

  function sendToDossier($portefeuille,$pdfFile)
  {
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
      unlink($pdfFile);
    }
  }
  
  function sendByEmailLosseFactuur($portefeuille,$extraAdres)
  {
    $this->initPdf();
    $this->loadPdfSettings($portefeuille);
    $this->addReports($portefeuille,$extraAdres,true);
    $this->factuurFilename=$this->getFilename($portefeuille,true);
    $this->pdf->Output($this->filePath.$this->factuurFilename,"F");
    //echo "|".$this->factuurVulling."| ".$this->filePath.$this->filename." , ".$this->filePath.$this->factuurFilename."<br>\n";
    if($this->factuurVulling==true)
      $this->sendByEmail($portefeuille,$this->filePath.$this->filename,$extraAdres,$this->filePath.$this->factuurFilename);
    else
      $this->sendByEmail($portefeuille,$this->filePath.$this->filename,$extraAdres);  
  }


}
?>
