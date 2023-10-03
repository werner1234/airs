<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/01 14:01:44 $
 		File Versie					: $Revision: 1.79 $

 		$Log: portefeuilleSelectieClass.php,v $
 		Revision 1.79  2020/07/01 14:01:44  rvv
 		*** empty log message ***
 		
 		Revision 1.78  2020/05/20 17:07:40  rvv
 		*** empty log message ***
 		
 		Revision 1.77  2020/04/22 15:37:22  rvv
 		*** empty log message ***
 		
 		Revision 1.76  2019/09/14 17:05:43  rvv
 		*** empty log message ***
 		
 		Revision 1.75  2019/07/20 16:27:23  rvv
 		*** empty log message ***
 		
 		Revision 1.74  2019/07/10 15:40:18  rvv
 		*** empty log message ***
 		
 		Revision 1.73  2019/06/12 15:18:49  rvv
 		*** empty log message ***
 		
 		Revision 1.72  2018/12/08 18:26:48  rvv
 		*** empty log message ***
 		
 		Revision 1.71  2018/10/13 17:13:51  rvv
 		*** empty log message ***
 		
 		Revision 1.70  2018/09/19 17:20:00  rvv
 		*** empty log message ***
 		
 		Revision 1.69  2018/08/29 16:12:24  rvv
 		*** empty log message ***
 		
 		Revision 1.68  2018/08/27 17:17:56  rvv
 		*** empty log message ***
 		
 		Revision 1.67  2018/08/18 12:40:13  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.66  2018/02/11 13:22:26  rvv
 		*** empty log message ***
 		
 		Revision 1.65  2018/02/10 18:05:28  rvv
 		*** empty log message ***
 		
 		Revision 1.64  2018/01/14 12:36:30  rvv
 		*** empty log message ***
 		
 		Revision 1.63  2017/10/21 17:27:55  rvv
 		*** empty log message ***
 		
 		Revision 1.62  2017/07/26 09:53:43  rvv
 		*** empty log message ***
 		
 		Revision 1.61  2017/07/24 10:51:10  rvv
 		*** empty log message ***
 		
 		Revision 1.60  2017/07/19 19:21:31  rvv
 		*** empty log message ***
 		
 		Revision 1.59  2017/07/15 16:07:17  rvv
 		*** empty log message ***
 		
 		Revision 1.58  2017/07/09 11:59:04  rvv
 		*** empty log message ***
 		
 		Revision 1.57  2017/05/24 15:54:10  rvv
 		*** empty log message ***
 		
 		Revision 1.56  2017/05/20 18:10:52  rvv
 		*** empty log message ***
 		
 		Revision 1.55  2017/02/25 17:55:02  rvv
 		*** empty log message ***
 		
 		Revision 1.54  2017/02/23 06:30:56  rvv
 		*** empty log message ***
 		
 		Revision 1.53  2017/02/22 17:12:41  rvv
 		*** empty log message ***
 		
 		Revision 1.52  2017/02/05 16:25:08  rvv
 		*** empty log message ***
 		
 		Revision 1.51  2016/12/24 16:29:40  rvv
 		*** empty log message ***
 		
 		Revision 1.50  2016/12/15 08:01:24  rvv
 		*** empty log message ***
 		
 		Revision 1.49  2016/10/24 06:35:57  rvv
 		*** empty log message ***
 		
 		Revision 1.48  2016/10/23 11:36:21  rvv
 		*** empty log message ***
 		
 		Revision 1.47  2015/12/27 16:25:56  rvv
 		*** empty log message ***
 		
 		Revision 1.46  2015/12/09 20:12:39  rvv
 		*** empty log message ***
 		
 		Revision 1.45  2015/11/22 14:20:53  rvv
 		*** empty log message ***
 		
 		Revision 1.44  2015/11/18 17:02:11  rvv
 		*** empty log message ***
 		
 		Revision 1.43  2015/10/21 16:10:49  rvv
 		*** empty log message ***
 		
 		Revision 1.42  2015/10/11 16:55:38  rvv
 		*** empty log message ***
 		
 		Revision 1.41  2015/09/13 11:28:21  rvv
 		*** empty log message ***
 		
 		Revision 1.40  2015/07/25 08:57:35  rvv
 		*** empty log message ***
 		
 		Revision 1.39  2015/05/16 09:33:00  rvv
 		*** empty log message ***
 		
 		Revision 1.38  2015/02/11 16:45:08  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2014/12/17 15:54:40  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2014/12/13 19:06:51  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2014/08/06 15:36:03  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2014/04/30 16:01:19  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2013/11/23 17:18:39  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2013/11/13 15:51:14  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2013/08/07 17:12:53  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2013/05/12 11:13:40  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2013/03/06 16:56:24  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2012/12/02 10:52:58  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2012/04/04 16:06:53  rvv
 		*** empty log message ***

 		Revision 1.26  2011/12/31 18:15:22  rvv
 		*** empty log message ***

 		Revision 1.25  2011/12/11 10:54:38  rvv
 		*** empty log message ***

 		Revision 1.24  2011/08/31 14:44:46  rvv
 		*** empty log message ***

 		Revision 1.23  2011/06/29 16:58:27  rvv
 		*** empty log message ***

 		Revision 1.22  2011/06/22 08:23:54  rvv
 		*** empty log message ***

 		Revision 1.21  2011/04/30 16:21:55  rvv
 		*** empty log message ***

 		Revision 1.20  2011/04/17 09:12:53  rvv
 		*** empty log message ***

 		Revision 1.19  2011/04/13 14:18:38  rvv
 		*** empty log message ***

 		Revision 1.18  2011/01/23 08:47:37  rvv
 		*** empty log message ***

 		Revision 1.17  2011/01/08 14:20:12  rvv
 		*** empty log message ***

 		Revision 1.16  2010/11/21 13:04:55  rvv
 		*** empty log message ***

 		Revision 1.15  2010/11/17 17:20:00  rvv
 		*** empty log message ***

 		Revision 1.14  2010/11/14 10:52:20  rvv
 		*** empty log message ***

 		Revision 1.13  2010/10/17 09:28:02  rvv
 		Rapportagevaluta toegevoegd

 		Revision 1.12  2010/07/28 17:19:49  rvv
 		*** empty log message ***

 		Revision 1.11  2009/11/11 14:54:50  rvv
 		*** empty log message ***

 		Revision 1.10  2009/06/21 09:39:43  rvv
 		*** empty log message ***

 		Revision 1.9  2009/03/25 17:46:18  rvv
 		*** empty log message ***

 		Revision 1.8  2008/10/06 12:21:39  rvv
 		*** empty log message ***

 		Revision 1.7  2008/06/30 06:55:29  rvv
 		*** empty log message ***

 		Revision 1.6  2008/01/23 07:29:32  rvv
 		*** empty log message ***

 		Revision 1.5  2007/09/26 15:09:14  rvv
 		*** empty log message ***

 		Revision 1.4  2007/08/13 14:20:28  rvv
 		*** empty log message ***

 		Revision 1.3  2007/08/03 12:45:14  rvv
 		*** empty log message ***

 		Revision 1.2  2007/08/02 15:56:27  rvv
 		*** empty log message ***

 		Revision 1.1  2007/08/02 14:13:02  rvv
 		*** empty log message ***


*/



class portefeuilleSelectie
{

  function portefeuilleSelectie($selectData,$orderby='Portefeuilles.Client',$allFields=false,$metEinddatum=false)
  {
    if($selectData['testset']==1)
      $selectData['selectedPortefeuilles']=array('AMB_Defensief','506389','546143','607665','44551','400658','400489','70771','907464','30882','43027','591645','798681','207164','611298','222778342','561371','236561','916323','718882','562181','140667','45350','587554576','238053','409007','603805','595836909','400837','597880','288038','288124','293091','209073','281362','281233','166037','42828','276296','211747','268021','40229','233524','221528','45267','265828','265769','475139216','515892','B556104','B298950','0835-1565484-75-000','236560','206521','229746','407491465','SALA','166218','71520','478147','526798270','462271897','44925','35717','938106','511889','539937','140708','78566','44739','227592','409009','594970','30383','400825','265541','270028','207098','571725','1030224','541391755','281348','554081','42235','206551','609986','609951','609943','609978','30213','30214','30215','281160','42992','71560','43674','155077','155078','155131','535863004','417927797','446499579','486912205','71580','MARIS0','MARIS3','71561','155037','42431','550175','30216','MARIS','236566','236110','44677','565458','281238','251534','251527','288092','288075','57774','57546','21323','288093','288465','529990','281482','43527','281307','281531','276314','276304','254340','235217','44616','43940','227037','227020','43517','574678','261544','206547','473808137','265645','288139','209080','211773','220542','564237442','564237442a','420260218','575559160','575559160a','474544869','548707243','222646675','222650729','30448','30465','40228','44341','30859','30087','30098');
    elseif($selectData['testset']==2)
      $selectData['selectedPortefeuilles']=array('43988','515892','44925','BIN_399248','235218','593834','237526','33229589','418807051','587102','0835-1119709-35-000','63004','987158','339781','13466534','43269DVE','44863','227551','227050','476119FCT','42079','42079','42225','13477566','265519','CASDEFKL','563072','LBCM-NEU-GR','057610','555231','Guillion','Icco_Investments','421795','43269','336251','CAVALIER1','281478','166070','40042','211726','511579314','478648','0947-0984693-55-000','223217042','254501','553921','542646919','242175','220516','44026','534641','236331','224200','586404','140514','13516405','261567','484382489','0835-1755867-55-000','SLV VR','579963','223667471','11596414','43269DVE','227537','227021','476119FCT','222750162','570788','42225','A1648700','603799043','LBCM-DEF-GR','057545','576216887','43739','296512','40072','209997966','281668','166208','233305','211703','544511905','548215626','553247379','254508','553999','071561','13259620','GS 013-029780');

    global $USR;
    if ($orderby == '')
      $orderby = 'Portefeuilles.Client';
    $this->selectData = $selectData;
    $this->virtuelePortefeuilles=array();
    $this->virtuelePortefeuillesVerwijderen=array();
    $this->uitSelectie           = array();
    $this->gebruiktePortefeuilles= array();
    $this->selected=array();

    $this->orderby = $orderby;
    $this->db  = new DB();
    $this->db2 = new DB();

    if(checkAccess('portefeuille'))
		{
			$join = "";
			$beperktToegankelijk = "";
		}
		else
		{
			$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
		           JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
	    $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
		}

  	// controle op einddatum portefeuille
    $extraquery='';
    if($metEinddatum==false)
    {
      $extraquery .= " Portefeuilles.Einddatum > '" . jul2db($selectData['datumTm']) . "' AND";
      $extraquery .= " Vermogensbeheerders.Einddatum > '" . jul2db($selectData['datumTm']) . "' AND";
    }
		$extraquery  .= " Portefeuilles.StartDatum < '".jul2db($selectData['datumTm'])."' AND";

    if($selectData['selectedFields'])
      $selectData['selectedPortefeuilles']=$selectData['selectedFields'];
      
    if(isset($selectData['depotbank']) && $selectData['depotbank'] <> '')  
      $extraquery.= " Portefeuilles.Depotbank='".$selectData['depotbank']."' AND ";
  
  
    if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
      $AcountmanagerInternDepotToegang = "OR Portefeuilles.interndepot=1";

    if(isset($selectData['portefeuilleIntern']))
    {
      if($selectData['portefeuilleIntern']=='0')
      {
        $extraquery .= " Portefeuilles.interndepot=0 AND ";
        $AcountmanagerInternDepotToegang='';
      }
      elseif($selectData['portefeuilleIntern'] == "1")
      {
        $extraquery .= " Portefeuilles.interndepot=1 AND ";
      }
    }
    
  

    if(isset($selectData['metConsolidatie']))
    {
      if($selectData['metConsolidatie']=='0')
        $extraquery  .= " Portefeuilles.consolidatie=0 AND ";
      elseif($selectData['metConsolidatie'] == "1")
        $extraquery  .= " Portefeuilles.consolidatie=1 AND ";

    }
    elseif(!$this->selectData['geconsolideerd'])
      $extraquery .= " Portefeuilles.consolidatie=0 AND ";

  if($selectData['PortefeuilleClustersVan'] <> '' && $selectData['PortefeuilleClustersTm'] <> '' && $selectData['PortefeuilleClustersVan'] <> 'alles' && $selectData['PortefeuilleClustersTm'] <> 'alles')
  {
  
    if (isset($selectData['Vermogensbeheerder']) && count($selectData['Vermogensbeheerder']) > 0)// Vink selectie
    {
      $selectie=array();
      $vermFilter = "";
      foreach ($selectData['Vermogensbeheerder'] as $key=>$value)
        if($value==1)
          $selectie[]=$key;
      if(count($selectie) > 0)
        $vermFilter= "AND Vermogensbeheerder IN('". implode('\',\'',$selectie)."')  ";
    }
    else
    {
      $vermFilter="AND (Vermogensbeheerder>='".mysql_real_escape_string($selectData['VermogensbeheerderVan'])."' AND Vermogensbeheerder<='".mysql_real_escape_string($selectData['VermogensbeheerderTm'])."') ";
    }
    
    $query="SELECT * FROM portefeuilleClusters WHERE (cluster>='".mysql_real_escape_string($selectData['PortefeuilleClustersVan'])."' AND cluster<='".mysql_real_escape_string($selectData['PortefeuilleClustersTm'])."')  $vermFilter ";
    $this->db->SQL($query);
    $this->db->query();
    $clusterPortefeuilles=array();
    while($clusterData=$this->db->nextRecord())
    {
      for($i=1;$i<31;$i++)
      {
        if($clusterData['portefeuille'.$i] <> '')
        {
          $clusterPortefeuille=$clusterData['portefeuille'.$i];
          $clusterPortefeuilles[$clusterPortefeuille]=$clusterPortefeuille;
        }
      }
    }
    $clusterPortefeuilleSelectie = implode('\',\'',$clusterPortefeuilles);
    $extraquery .= " Portefeuilles.Portefeuille IN('$clusterPortefeuilleSelectie') AND ";
  }

	  if (count($selectData['selectedPortefeuilles']) > 0)
		{
		 $portefeuilleSelectie = implode('\',\'',$selectData['selectedPortefeuilles']);
	   $extraquery .= " Portefeuilles.Portefeuille IN('$portefeuilleSelectie') AND ";
		}
		else
		{
		  $opties=array('Vermogensbeheerder','Accountmanager','TweedeAanspreekpunt','Client','Portefeuille','Depotbank','Risicoklasse','AFMprofiel','SoortOvereenkomst','Remisier','selectieveld1','selectieveld2','ModelPortefeuille');
		  foreach ($opties as $option)
		  {
		    if($selectData[$option.'Tm'])  // Van/Tm selectie
	  		  $extraquery .= " (Portefeuilles.$option >= '".mysql_real_escape_string($selectData[$option.'Van'])."' AND Portefeuilles.$option <= '".mysql_real_escape_string($selectData[$option.'Tm'])."') AND";

	  		if (isset($selectData[$option]) && count($selectData[$option]) > 0)// Vink selectie
	  		{
	  		  $selectie=array();
	  		  foreach ($selectData[$option] as $key=>$value)
	  		    if($value==1)
	  		      $selectie[]=$key;
	  		  if(count($selectie) > 0)
            $extraquery .= " Portefeuilles.$option IN('". implode('\',\'',$selectie)."') AND  ";
	  		}
		  }
    }
    if($selectData['bedrijf'])
    {
      $extraquery .= "  VermogensbeheerdersPerBedrijf.Bedrijf = '".$selectData['bedrijf']."' AND";
      //$extraTable .= ', VermogensbeheerdersPerBedrijf  ';
      $join = " JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder ";
      $beperktToegankelijk='';
    }

    if($_POST['facturatiemaand'] != '')
    {
      $selectieMaand=substr($_POST['facturatiemaand'],5,2);
      $typen=array('halfjaar'=>array(6),
                   'kwartalen'=>array(3,6,9));
      foreach($typen as $type=>$maanden)
      {
        foreach($maanden as $maand)
        {
          $filterMaand=$selectieMaand+$maand;
          if($filterMaand > 12)
            $filterMaand=$filterMaand-12;
          $filterMaanden[$type][]=$filterMaand;
        }
      }             
        
      $extraquery .= "( (MONTH(Portefeuilles.BeheerfeeFacturatieVanaf) = '$selectieMaand')
            OR
               (Portefeuilles.BeheerfeeAantalFacturen=4 AND MONTH(Portefeuilles.BeheerfeeFacturatieVanaf) IN('".implode("','",$filterMaanden['kwartalen'])."'))
            OR
               (Portefeuilles.BeheerfeeAantalFacturen=2 AND MONTH(Portefeuilles.BeheerfeeFacturatieVanaf) IN('".implode("','",$filterMaanden['halfjaar'])."'))) AND ";
    }

    if($selectData['modelcontrole_portefeuille'] <> '' && $this->selectData['geconsolideerd']==0)
    {
      if($selectData['modelcontrole_portefeuille']=='Allemaal')
        $extraquery .= "  Portefeuilles.ModelPortefeuille <> '' AND ";
    	elseif($selectData['modelcontrole_filter'] == "gekoppeld")
	 	  	$extraquery .= "  Portefeuilles.ModelPortefeuille = '".$selectData['modelcontrole_portefeuille']."' AND ";
    //	if($selectData["modelcontrole_rapport"] == "vastbedrag")
   	//    $extraquery = " Portefeuilles.Portefeuille = '".$selectData['modelcontrole_portefeuille']."' AND ";
    }

		if($selectData['periode'] == 'Kwartaalrapportage')
	  {
	 // 	$extraquery .= " Portefeuilles.kwartaalAfdrukken > 0 AND ";
	  	$aantalQuery = " Portefeuilles.kwartaalAfdrukken as aantal, ";
	  }
	  elseif($selectData['periode'] == 'Maandrapportage')
	  {
	 //   $extraquery .= " Portefeuilles.MaandAfdrukken > 0 AND ";
	    $aantalQuery = " Portefeuilles.maandAfdrukken as aantal, ";
	  }
	  elseif($selectData['periode'] == 'Clienten')
	  {
	    $aantalQuery = " '1' as aantal, ";
	  }
    

  
    $extraquery .= " Portefeuilles.Portefeuille NOT IN(SELECT ModelPortefeuilles.Portefeuille FROM ModelPortefeuilles WHERE ModelPortefeuilles.Fixed=1) AND ";
    
    $join .= " JOIN Vermogensbeheerders on Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ";

    if(is_array($allFields))
      $select=" Portefeuilles.id as PortefeuillesID,Vermogensbeheerders.id as VermogensbeheerdersID,CRM_naw.id as CRM_nawID, Portefeuilles.Portefeuille, ".implode(",",$allFields);
    elseif($allFields==true)
      $select=" Portefeuilles.id as PortefeuillesID,Vermogensbeheerders.id as VermogensbeheerdersID,CRM_naw.id as CRM_nawID, Portefeuilles.*,Vermogensbeheerders.Vermogensbeheerder,Vermogensbeheerders.Naam,Vermogensbeheerders.Adres,Vermogensbeheerders.Woonplaats,Vermogensbeheerders.Telefoon,Vermogensbeheerders.Fax,Vermogensbeheerders.Email,Vermogensbeheerders.Contactpersoon,Vermogensbeheerders.Exportpad,Vermogensbeheerders.NAWPad,Vermogensbeheerders.Layout,Vermogensbeheerders.Index1,Vermogensbeheerders.Index2,Vermogensbeheerders.PerformanceBerekening,Vermogensbeheerders.VraagOmNAWImport,Vermogensbeheerders.FactuurBeheerfeeBerekening,Vermogensbeheerders.BasisVoorRisicoMeting,Vermogensbeheerders.AfdrukSortering,Vermogensbeheerders.csvSeperator,Vermogensbeheerders.Export_kwartaal_pad,Vermogensbeheerders.Export_maand_pad,Vermogensbeheerders.Export_dag_pad,Vermogensbeheerders.order_controle,Vermogensbeheerders.Export_data_kwartaal,Vermogensbeheerders.Export_data_maand,Vermogensbeheerders.Export_data_dag,Vermogensbeheerders.Logo,Vermogensbeheerders.grafiek_kleur,Vermogensbeheerders.OptieTools,Vermogensbeheerders.Attributie,Vermogensbeheerders.attributieInPerformance,Vermogensbeheerders.grafiek_sortering,Vermogensbeheerders.Export_data_frontOffice,Vermogensbeheerders.naamInExport,Vermogensbeheerders.txtKoppeling,Vermogensbeheerders.maandRapportageYTD,Vermogensbeheerders.kwartaalRapportageYTD,Vermogensbeheerders.CrmPortefeuilleInformatie,Vermogensbeheerders.CrmClientNaam,Vermogensbeheerders.rekening,Vermogensbeheerders.bank,Vermogensbeheerders.FactuurMinimumBedrag,Vermogensbeheerders.website,Vermogensbeheerders.VerouderdeKoersDagen,Vermogensbeheerders.verrekeningBestandsvergoeding,Vermogensbeheerders.bestandsvergoedingBtw,Vermogensbeheerders.bestandsvergoedingNiveau,Vermogensbeheerders.OrderLoggingOpNota,Vermogensbeheerders.OrderCheck,Vermogensbeheerders.kwartaalCheck,Vermogensbeheerders.module_bestandsvergoeding,Vermogensbeheerders.OrderStandaardType,Vermogensbeheerders.OrderStandaardMemo,Vermogensbeheerders.OrderOrderdesk,Vermogensbeheerders.OrderStatusKeuze,Vermogensbeheerders.transactiemeldingWaarde,Vermogensbeheerders.transactiemeldingEmail,Vermogensbeheerders.check_module_CRM_eigenVelden,Vermogensbeheerders.IndexRisicovrij,Vermogensbeheerders.CRM_alleenNAW,Vermogensbeheerders.check_afmCategorie,Vermogensbeheerders.orderCheckMaxAge,Vermogensbeheerders.fondsenmeldingEmail,Vermogensbeheerders.geenStandaardSector,Vermogensbeheerders.orderControleEmail,Vermogensbeheerders.OrderStandaardTijdsSoort,Vermogensbeheerders.transactieMeldingType,Vermogensbeheerders.TransactiefeeBtw,Vermogensbeheerders.CrmAutomatischVerzenden,Vermogensbeheerders.FactuurMinimumPerTransactie,Vermogensbeheerders.autoPortaalVulling,Vermogensbeheerders.ScenarioMinimaleKans,Vermogensbeheerders.OrderStandaardTransactieType,Vermogensbeheerders.OrderCheckClientNaam,Vermogensbeheerders.BeheerfeeAdministratieVergoedingVast,Vermogensbeheerders.check_module_VRAGEN,Vermogensbeheerders.ScenarioGewenstProfiel,Vermogensbeheerders.frontofficeClientExcel,Vermogensbeheerders.OrderuitvoerBewaarder,Vermogensbeheerders.portaalPeriode,Vermogensbeheerders.check_portaalDocumenten,CRM_naw.*";
    else
      $select=" Portefeuilles.Vermogensbeheerder, ".
						 " Portefeuilles.Risicoklasse, ".
						 " Portefeuilles.Portefeuille, ".
						 " Portefeuilles.Startdatum, ".
						 " Portefeuilles.Client, ".
						 " Portefeuilles.Depotbank, ".
						 " Portefeuilles.Remisier, ".
						 " Portefeuilles.Accountmanager, ".
					   " Portefeuilles.Einddatum, ".
					   " Portefeuilles.RapportageValuta, ".
					   " Portefeuilles.SoortOvereenkomst, ".
					   " Portefeuilles.CrmPortefeuilleInformatie, ".
             " Portefeuilles.ZpMethode, ".
             " Portefeuilles.ModelPortefeuille, ".
             " Portefeuilles.TijdelijkUitsluitenZp, Portefeuilles.consolidatie,".
						 " Vermogensbeheerders.PerformanceBerekening, ".
						 " Vermogensbeheerders.CrmClientNaam,".
						 " Vermogensbeheerders.Layout,".
						 " Clienten.Naam,  ".
						 " Portefeuilles.ClientVermogensbeheerder,
						   CRM_naw.Portefeuille as CRM_Portefeuille,CRM_naw.naam as crmNaam, CRM_naw.naam1 as crmNaam1, CRM_naw.verzendAanhef, CRM_naw.id as crmId ";

    if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	       $extraAccountmanagerFilter = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $AcountmanagerInternDepotToegang ) ";

    $query = " SELECT $aantalQuery $select".
					 " FROM (Portefeuilles, Clienten) LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.Portefeuille  ".$join." WHERE
					 Portefeuilles.Startdatum <> '0000-00-00' AND ".$extraquery.
					 " Portefeuilles.Client = Clienten.Client $beperktToegankelijk $extraAccountmanagerFilter".
					 " ORDER BY ".$this->orderby;

		$query=str_replace("'Leeg'","''",$query);
		$this->db->SQL($query);
    logAccess($query,'RapportSelectie');
		$this->db->Query();

		while($pdata = $this->db->nextRecord())
		{
		  if($pdata['CrmClientNaam'] && $pdata['crmNaam'])
		  {
         $pdata['Naam'] = $pdata['crmNaam'];
         $pdata['Naam1'] = $pdata['crmNaam1'];
		  }

      if($pdata['consolidatie']>0)
      {
        $query = "SELECT GeconsolideerdePortefeuilles.*
     FROM GeconsolideerdePortefeuilles WHERE VirtuelePortefeuille='".$pdata['Portefeuille']."'  GROUP BY VirtuelePortefeuille";
        $this->db2->SQL($query);
        $this->db2->Query();
        $PortefeuilleNummers=array();
        while($cdata = $this->db2->nextRecord())
        {
          for($i=1;$i<41;$i++)
          {
            if($cdata['Portefeuille'.$i] <> '')
            {
              $PortefeuilleNummers[$cdata['Portefeuille' . $i]] = $cdata['Portefeuille' . $i];
            }
          }
        }
  
        $query = "SELECT Portefeuille FROM PortefeuillesGeconsolideerd WHERE VirtuelePortefeuille='".$pdata['Portefeuille']."' ORDER BY Portefeuille";
        $this->db2->SQL($query);
        $this->db2->Query();
        while ($vpdata = $this->db2->nextRecord())
        {
          $PortefeuilleNummers[$vpdata['Portefeuille']] = $vpdata['Portefeuille'];
        }
  
  
        $pdata['portefeuilles']=array_keys($PortefeuilleNummers);
      }




	  	$this->selected[$pdata['Portefeuille']]=$pdata;
		}

		if($this->selectData['geconsolideerd'])
      $this->addConsolidatiePortefeuilles();
    elseif($this->selectData['consolidatieToevoegen'])
      $this->addConsolidatiePortefeuilles(true);
		//  $this->consolidatieAanmaken();

  }
  
  function getAllFields($portefeuille)
  {
    
    if($this->selectData['periode'] == 'Kwartaalrapportage')
	  {
	  	$aantalQuery = " Portefeuilles.kwartaalAfdrukken as aantal, ";
	  }
	  elseif($this->selectData['periode'] == 'Maandrapportage')
	  {
	    $aantalQuery = " Portefeuilles.maandAfdrukken as aantal, ";
	  }
	  elseif($this->selectData['periode'] == 'Clienten')
	  {
	    $aantalQuery = " '1' as aantal, ";
	  }
    

		$join = " JOIN Vermogensbeheerders on Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ";
    
    $select=" Portefeuilles.id as PortefeuillesID,
              Vermogensbeheerders.id as VermogensbeheerdersID,
              CRM_naw.id as CRM_nawID, Portefeuilles.*,Vermogensbeheerders.*,CRM_naw.*";

    $query = " SELECT $aantalQuery $select".
					 " FROM (Portefeuilles)  LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.Portefeuille  ".$join." WHERE
					 Portefeuilles.Portefeuille='$portefeuille'";
           
    $this->db->SQL($query);
		$this->db->Query();
    if($this->db->records()==0)
    {

      $query = "SELECT '1' as aantal,GeconsolideerdePortefeuilles.id as GeconsolideerdePortefeuillesID,
              Vermogensbeheerders.id as VermogensbeheerdersID,
              CRM_naw.id as CRM_nawID, GeconsolideerdePortefeuilles.*,Vermogensbeheerders.*,CRM_naw.*".
        " FROM (GeconsolideerdePortefeuilles)  JOIN CRM_naw ON GeconsolideerdePortefeuilles.VirtuelePortefeuille=CRM_naw.portefeuille 
					  JOIN Vermogensbeheerders on GeconsolideerdePortefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder WHERE
					 GeconsolideerdePortefeuilles.VirtuelePortefeuille='$portefeuille'";
      $this->db->SQL($query);
      $this->db->Query();
    }

		$pdata = $this->db->nextRecord();
    return $pdata;
  }

  function addConsolidatiePortefeuilles($extraPortefeuilles=false,$onlyInCrm=false)
  {
    $PortefeuilleNummers = array();
    foreach ($this->selected as $port => $pdata)
      $PortefeuilleNummers[$port] = $port;
    if ($onlyInCrm == true)
      $extraCRMJoin = "";
    else
      $extraCRMJoin = 'LEFT';

    $consolidatieWhere = "(";
    $colsolidatieSelect = '';
    for ($i = 1; $i < 41; $i++)
    {
      $colsolidatieSelect .= ",GeconsolideerdePortefeuilles.Portefeuille" . $i;
      $consolidatieWhere .= "GeconsolideerdePortefeuilles.Portefeuille" . $i . "  =  Portefeuilles.Portefeuille";
      if ($i < 40)
        $consolidatieWhere .= " OR \n";
    }
    $consolidatieWhere .= ")";

    if ($extraPortefeuilles == true)
    {
      $portefeuilleSelectie = implode('\',\'', $PortefeuilleNummers);
      $query = "SELECT GeconsolideerdePortefeuilles.VirtuelePortefeuille $colsolidatieSelect
     FROM (GeconsolideerdePortefeuilles, Portefeuilles) 
     JOIN CRM_naw ON GeconsolideerdePortefeuilles.VirtuelePortefeuille=CRM_naw.portefeuille 
     WHERE $consolidatieWhere AND Portefeuilles.Portefeuille IN('$portefeuilleSelectie')
     GROUP BY VirtuelePortefeuille";
      $this->db->SQL($query);
      $this->db->Query();
      while ($cdata = $this->db->nextRecord())
      {
        for ($i = 1; $i < 41; $i++)
        {
          if ($cdata['Portefeuille' . $i] <> '')
          {
            $PortefeuilleNummers[$cdata['Portefeuille' . $i]] = $cdata['Portefeuille' . $i];
          }
        }
      }
    }



    $PortefeuilleNummers = array_keys($PortefeuilleNummers);
    $portefeuilleSelectie = implode('\',\'',$PortefeuilleNummers);
    $query = "SELECT
          CRM_naw.id as crmId,
        CRM_naw.Naam as crmNaam,
        CRM_naw.Naam1 as crmNaam1,
               GeconsolideerdePortefeuilles.VirtuelePortefeuille
             FROM
               (GeconsolideerdePortefeuilles, Portefeuilles) 
               JOIN Vermogensbeheerders on GeconsolideerdePortefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
               $extraCRMJoin JOIN CRM_naw ON GeconsolideerdePortefeuilles.VirtuelePortefeuille=CRM_naw.portefeuille
             WHERE $consolidatieWhere AND Portefeuilles.Portefeuille IN('$portefeuilleSelectie')
             ORDER BY VirtuelePortefeuille";

    $this->db->SQL($query);
    $this->db->Query();

    $this->virtuelePortefeuillesVerwijderen=$this->virtuelePortefeuilles;
    $this->virtuelePortefeuilles = array();
    $this->uitSelectie           = array();
    $this->gebruiktePortefeuilles= array();
    $portefeuilles=array();
    while($cdata = $this->db->nextRecord())
    {
      if($cdata['CrmClientNaam'] && $cdata['crmNaam'])
      {
        $cdata['Naam'] = $cdata['crmNaam'];
        $cdata['Naam1'] = $cdata['crmNaam1'];
      }
      $cdata['portefeuilles']=$PortefeuilleNummers;
      $portefeuilles[]=$cdata;
      if(!isset($this->selected[$cdata['VirtuelePortefeuille']]))
        $this->selected[$cdata['VirtuelePortefeuille']]=$cdata;
    }

    return $portefeuilles;
  }

/*  function consolidatieAanmaken($extraPortefeuilles=false,$onlyInCrm=false,$enkelVoorbereiding=false)
  {
    $PortefeuilleNummers=array();
    foreach($this->selected as $port=>$pdata)
      $PortefeuilleNummers[$port]=$port;
    if($onlyInCrm==true)
      $extraJoin="JOIN CRM_naw ON GeconsolideerdePortefeuilles.VirtuelePortefeuille=CRM_naw.portefeuille";
    else
      $extraJoin='';
     
    $consolidatieWhere = "(";
    $colsolidatieSelect='';
    for($i=1;$i<41;$i++)
    {
      $colsolidatieSelect.=",GeconsolideerdePortefeuilles.Portefeuille".$i;
      $consolidatieWhere .= "GeconsolideerdePortefeuilles.Portefeuille".$i."  =  Portefeuilles.Portefeuille";
      if($i < 40)
        $consolidatieWhere .= " OR \n"; 
    }        
    $consolidatieWhere .= ")";    

   if($extraPortefeuilles==true)
   {
    $portefeuilleSelectie = implode('\',\'',$PortefeuilleNummers);
     $query = "SELECT GeconsolideerdePortefeuilles.VirtuelePortefeuille $colsolidatieSelect
     FROM (GeconsolideerdePortefeuilles, Portefeuilles) 
     JOIN CRM_naw ON GeconsolideerdePortefeuilles.VirtuelePortefeuille=CRM_naw.portefeuille 
     WHERE $consolidatieWhere AND Portefeuilles.Portefeuille IN('$portefeuilleSelectie')
     GROUP BY VirtuelePortefeuille";
    $this->db->SQL($query);
 		$this->db->Query();
		while($cdata = $this->db->nextRecord())
		{
		  for($i=1;$i<41;$i++)
      {
        if($cdata['Portefeuille'.$i] <> '')
        {
          $PortefeuilleNummers[$cdata['Portefeuille' . $i]] = $cdata['Portefeuille' . $i];
        }
      }
    }
   }
   $PortefeuilleNummers=array_keys($PortefeuilleNummers);


   $portefeuilleSelectie = implode('\',\'',$PortefeuilleNummers);
   $query = "SELECT
               Portefeuilles.Portefeuille,
               Portefeuilles.Startdatum,
               Portefeuilles.Einddatum,
               Portefeuilles.Depotbank,
               Portefeuilles.AEXVergelijking,
               Portefeuilles.Accountmanager,
               Portefeuilles.tweedeAanspreekpunt,
               Portefeuilles.ClientVermogensbeheerder,
               Portefeuilles.Taal,
               Portefeuilles.RapportageValuta,
               GeconsolideerdePortefeuilles.VirtuelePortefeuille,
               GeconsolideerdePortefeuilles.Vermogensbeheerder,
               GeconsolideerdePortefeuilles.Client,
               GeconsolideerdePortefeuilles.Naam,
               GeconsolideerdePortefeuilles.Naam1,
               GeconsolideerdePortefeuilles.Risicoprofiel,
               GeconsolideerdePortefeuilles.SoortOvereenkomst,
               GeconsolideerdePortefeuilles.SpecifiekeIndex,
               GeconsolideerdePortefeuilles.ModelPortefeuille,
               GeconsolideerdePortefeuilles.ZpMethode,
               date(GeconsolideerdePortefeuilles.Startdatum) as consolidatieStartdatum,
               if(Portefeuilles.Portefeuille=GeconsolideerdePortefeuilles.Portefeuille1,0,1) as volgorde
             FROM
               (GeconsolideerdePortefeuilles, Portefeuilles) $extraJoin 
             WHERE $consolidatieWhere AND Portefeuilles.Portefeuille IN('$portefeuilleSelectie')
             ORDER BY volgorde";

    $this->db->SQL($query);
 		$this->db->Query();

    $this->virtuelePortefeuillesVerwijderen=$this->virtuelePortefeuilles;
 		$this->virtuelePortefeuilles = array();
 		$this->uitSelectie           = array();
    $this->gebruiktePortefeuilles= array();
    $portefeuilles=array();
		while($cdata = $this->db->nextRecord())
		{
		  $portefeuilles[]=$cdata;
    }

    foreach($portefeuilles as $cdata)
    {
      if($enkelVoorbereiding==true)
      {
        $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['consolidatieToevoegen']=1;
      }
		  $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['portefeuilles'][] = $cdata['Portefeuille'];
      $this->gebruiktePortefeuilles[$cdata['Portefeuille']][]=$cdata['VirtuelePortefeuille'];

      if(empty($this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Startdatum']) && $cdata['consolidatieStartdatum'] <> '0000-00-00')
        $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Startdatum'] =  $cdata['consolidatieStartdatum'];
		  elseif(empty($this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Startdatum']))
		    $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Startdatum'] =  $cdata['Startdatum'];
		  elseif ($cdata['consolidatieStartdatum'] == '0000-00-00' && db2jul($cdata['Startdatum']) < db2jul($this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Startdatum']))
		    $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Startdatum'] =  $cdata['Startdatum'];

		  if(empty($this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Einddatum']))
		    $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Einddatum'] =  $cdata['Einddatum'];
		  elseif (db2jul($this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Einddatum']) > db2jul($cdata['Einddatum']))
		    $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Einddatum'] =  $cdata['Einddatum'];

		  if(empty($this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['client']))
		  {
		  $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Client'] = $cdata['Client'];
		  $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['ClientVermogensbeheerder'] = $cdata['Naam'];//$cdata['ClientVermogensbeheerder'];
		  $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Naam'] = $cdata['Naam'];
		  $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Naam1'] = $cdata['Naam1'];
		  $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Vermogensbeheerder'] = $cdata['Vermogensbeheerder'];
	    $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Depotbank'] = $cdata['Depotbank'];
		  $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['AEXVergelijking'] = $cdata['AEXVergelijking'];
		  $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Accountmanager'] = $cdata['Accountmanager'];
      $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['tweedeAanspreekpunt'] = $cdata['tweedeAanspreekpunt'];
      $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Risicoklasse'] = $cdata['Risicoprofiel'];
      $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['SoortOvereenkomst'] = $cdata['SoortOvereenkomst'];
      $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['SpecifiekeIndex'] = $cdata['SpecifiekeIndex'];
      if(!isset($this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['RapportageValuta']))
        $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['RapportageValuta'] = $cdata['RapportageValuta'];
      if(!isset($this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Taal']))
        $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Taal'] = $cdata['Taal'];
      $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['ModelPortefeuille'] = $cdata['ModelPortefeuille'];
      $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['ZpMethode'] = $cdata['ZpMethode'];
		  }
      if($this->selectData['orderDepotbank']==$cdata['Depotbank'])
		    $this->virtuelePortefeuilles[$cdata['VirtuelePortefeuille']]['Depotbank'] = $cdata['Depotbank'];
      if($extraPortefeuilles==false)
      {  
		    $this->uitSelectie[]=$cdata['Portefeuille'];
		    unset($this->selected[$cdata['Portefeuille']]);
      }
		}

    $berichtPopup='';
    foreach($this->gebruiktePortefeuilles as $portefeulle=>$gebruiktVoor)
    {
      if(count($gebruiktVoor) > 1)
      {
        $bericht="Portefeulle $portefeulle wordt gebruikt in (".count($gebruiktVoor).") virtuele portefeuilles. (".implode(",",$gebruiktVoor).")";
        if(isset($this->selectData['periode']))
          logScherm($bericht);
        $berichtPopup.="\\n".$bericht;
      }
    }
    if(isset($this->selectData['periode']))
      if($berichtPopup<>'')
         echo "<script>alert('$berichtPopup');</script>";

    foreach ($this->virtuelePortefeuilles as $portefeuille => $data)
    {
       if($enkelVoorbereiding==true)
       {
         $data['Portefeuille']=$portefeuille;
         $this->selected[$portefeuille] = $data;
       }
       else
          $this->createVirtuelePortefeuille($portefeuille, $data);
    }
  }
*/

/*  function createVirtuelePortefeuille($virtuelePortefeuille,$data,$aanwezigheidsCheck=false)
  {
    global $USR,$__appvar;
    $message='';
    $afbreken = false;
    
    if($aanwezigheidsCheck==true)
    {
      $afbreken=true;
      $portefeuilles=array();
      $this->db->SQL("SELECT * FROM GeconsolideerdePortefeuilles WHERE VirtuelePortefeuille='".$virtuelePortefeuille."'");
      $this->db->Query();
      $pdata = $this->db->nextRecord();
      for($i=1;$i<41;$i++)
        if($pdata['Portefeuille'.$i] <> '')
          $portefeuilles[] = $pdata['Portefeuille'.$i];
      if(count($portefeuilles)>0)
        $portefeuilles[]=$virtuelePortefeuille;


      for ($i = 0; $i < 24; $i++)
      {
        if(count($portefeuilles)>0)
          $query = "SELECT id,add_date,add_user,Portefeuille FROM Rekeningen WHERE consolidatie = 2 AND Rekening IN (SELECT Rekening FROM Rekeningen WHERE portefeuille IN('".implode("','",$portefeuilles)."')) limit 1";//Portefeuille = '".$portefeuille."' AND
        else
          $query = "SELECT id,Portefeuille,add_date,add_user FROM Rekeningen WHERE consolidatie = 2";

        $this->db->SQL($query);
        $this->db->Query();
        $oldRecord = $this->db->nextRecord();
        if ($oldRecord['id'] > 0)
        {
          logScherm("Consolidatie aanwezig voor " . $oldRecord['Portefeuille'] . ", wachten tot deze verwijderd wordt. (" . $oldRecord['add_date'] . "/" . $oldRecord['add_user'].")");
          $message .= logTxt("Consolidatie aanwezig voor " . $oldRecord['Portefeuille'] . ", wachten tot deze verwijderd wordt. (" . $oldRecord['add_date'] . "/" . $oldRecord['add_user'] . ")<br>");
          sleep(5);
        }
        else
        {
          $afbreken = false;
          $message .= logTxt("Geen consolidatie aanwezig. Nieuwe aanmaken voor $virtuelePortefeuille .<br>");
          break;
        }
      }
    }

    if($afbreken==true)
    {
      $cfg=new AE_config();
      $mailserver=$cfg->getData('smtpServer');
      $notifyEmail='info@airs.nl';//$cfg->getData('fondsEmail');
      if($notifyEmail !="" && $mailserver !='')
      {
        include_once('../classes/AE_cls_phpmailer.php');
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->From = $notifyEmail;
        $mail->FromName = "Airs";
        $mail->Body = $message;
        $mail->AltBody = html_entity_decode(strip_tags($message));
        $mail->AddAddress($notifyEmail, $notifyEmail);
        $mail->Subject = "Consolidatie $virtuelePortefeuille afgebroken bij " . $__appvar["bedrijf"];
        $mail->Host = $mailserver;
        if (!$mail->Send())
        {
          logIt("Verzenden van 'Consolidatie afgebroken' e-mail mislukt.");
        }
      }
      return array('afgebroken'=>true);
    }
    else
    {

      $portefeuilleSelectie = implode('\',\'', $data['portefeuilles']);
      $query = "SELECT
                Rekening, Valuta, Memoriaal, Termijnrekening, Tenaamstelling , RenteBerekenen, Rente30_360,Beleggingscategorie,AttributieCategorie,Depotbank,Inactief
              FROM
                Rekeningen
              WHERE Portefeuille IN('$portefeuilleSelectie') ";
      $this->db->SQL($query);
      $this->db->Query();

      $queries = array();
      while ($rekening = $this->db->NextRecord())
      {
        $queries[] = "INSERT INTO Rekeningen SET Portefeuille  = '" . $virtuelePortefeuille . "',
	                                     Rekening          = '" . $rekening['Rekening'] . "',
	                                     Valuta            = '" . $rekening['Valuta'] . "',
	                                     Memoriaal         = '" . $rekening['Memoriaal'] . "',
	                                     Tenaamstelling    = '" . $rekening['Tenaamstelling'] . "',
	                                     Termijnrekening   = '" . $rekening['Termijnrekening'] . "',
	                                     RenteBerekenen    = '" . $rekening['RenteBerekenen'] . "',
	                                     Rente30_360       = '" . $rekening['Rente30_360'] . "' ,
                                       Beleggingscategorie       = '" . $rekening['Beleggingscategorie'] . "' ,
                                       AttributieCategorie       = '" . $rekening['AttributieCategorie'] . "' ,
                                       Depotbank                 = '" . $rekening['Depotbank'] . "' ,
                                       Inactief                  = '" . $rekening['Inactief'] . "' ,
                                       consolidatie      = 2,
                                       add_user          = '$USR' ,
	                                     add_date          =  NOW()";

      }

      $queries[] = "DELETE FROM Clienten WHERE Client = '" . $data['Client'] . "' AND consolidatie = 2";
      $queries[] = "INSERT INTO Clienten SET consolidatie = 2, Client = '" . $data['Client'] . "' , Naam = '" . $data['Naam'] . "', Naam1 = '" . $data['Naam1'] . "' , add_user = '$USR' ,add_date = NOW() ";

      $queries[] = "DELETE FROM Portefeuilles WHERE Client = '" . $data['Client'] . "' AND Portefeuille = '" . $virtuelePortefeuille . "' AND consolidatie = 2 ";
      $queries[] = "INSERT INTO Portefeuilles SET
                                          consolidatie       = 2,
	                                        Client             = '" . $data['Client'] . "' ,
	                                        Portefeuille       = '" . $virtuelePortefeuille . "' ,
	                                        Depotbank          = '" . $data['Depotbank'] . "' ,
	                                        Accountmanager     = '" . $data['Accountmanager'] . "' ,
	                                        tweedeAanspreekpunt= '" . $data['tweedeAanspreekpunt'] . "' ,
	                                        AEXVergelijking    = '" . $data['AEXVergelijking'] . "' ,
                                          Risicoklasse       = '" . $data['Risicoprofiel'] . "' ,
	                                        Vermogensbeheerder = '" . $data['Vermogensbeheerder'] . "' ,
	                                        Startdatum         = '" . $data['Startdatum'] . "' ,
	                                        Einddatum          = '" . $data['Einddatum'] . "' ,
                                          SoortOvereenkomst  = '" . $data['SoortOvereenkomst'] . "' ,
                                          SpecifiekeIndex    = '" . $data['SpecifiekeIndex'] . "' ,
                                          RapportageValuta   = '" . $data['RapportageValuta'] . "' ,
                                          Taal               = '" . $data['Taal'] . "' ,
                                          ModelPortefeuille  = '" . $data['ModelPortefeuille'] . "' ,
                                          ZpMethode          = '" . $data['ZpMethode'] . "' ,
	                                        add_user           = '$USR' ,
	                                        add_date           =  NOW()";

      for ($a = 0; $a < count($queries); $a++)
      {
        $this->db->SQL($queries[$a]);
        $this->db->Query();
      }

      $data['Portefeuille'] = $virtuelePortefeuille;
      $this->selected[$virtuelePortefeuille] = $data;
    }
  }
*/
  
  function consolidatieVerwijderen($virtuelePortefeuille=''){logScherm("consolidatieVerwijderen moet nog uit de code.");}
/*  function consolidatieVerwijderen($virtuelePortefeuille='')
  {
    if($this->selectData['geconsolideerd'] || $this->selectData['consolidatieToevoegen'])
    {
      $queries = array();
      if($virtuelePortefeuille<>'')
      {
        $data=array();
        if(isset($this->virtuelePortefeuilles[$virtuelePortefeuille]))
           $data=$this->virtuelePortefeuilles[$virtuelePortefeuille];
        elseif(isset($this->virtuelePortefeuillesVerwijderen[$virtuelePortefeuille]))
           $data=$this->virtuelePortefeuillesVerwijderen[$virtuelePortefeuille];
        if(count($data) <> 0)
        {
          $queries[] = "DELETE FROM Rekeningen WHERE consolidatie = 2 AND Portefeuille = '" . $virtuelePortefeuille . "'";
          $queries[] = "DELETE FROM Clienten WHERE consolidatie = 2 AND Client = '" . $data['Client'] . "' AND Naam = '" . $data['Naam'] . "'";
          $queries[] = "DELETE FROM Portefeuilles WHERE consolidatie = 2 AND Client = '" . $data['Client'] . "' AND Portefeuille = '" . $virtuelePortefeuille . "'";
        }
      }
      else
      {
        foreach ($this->virtuelePortefeuilles as $virtuelePortefeuille => $data)
        {
          $queries[] = "DELETE FROM Rekeningen WHERE consolidatie = 2 AND (Portefeuille = '" . $virtuelePortefeuille . "' OR add_date < (now() - interval 1 hour) )";
          $queries[] = "DELETE FROM Clienten WHERE consolidatie = 2 AND ((Client = '" . $data['Client'] . "' AND Naam = '" . $data['Naam'] . "') OR add_date < (now() - interval 1 hour))";
          $queries[] = "DELETE FROM Portefeuilles WHERE consolidatie = 2 AND ((Client = '" . $data['Client'] . "' AND Portefeuille = '" . $virtuelePortefeuille . "') OR add_date < (now() - interval 1 hour))";
        }
        foreach ($this->virtuelePortefeuillesVerwijderen as $virtuelePortefeuille => $data)
        {
          $queries[] = "DELETE FROM Rekeningen WHERE consolidatie = 2 AND (Portefeuille = '" . $virtuelePortefeuille . "' OR add_date < (now() - interval 1 hour) )";
          $queries[] = "DELETE FROM Clienten WHERE consolidatie = 2 AND ((Client = '" . $data['Client'] . "' AND Naam = '" . $data['Naam'] . "') OR add_date < (now() - interval 1 hour))";
          $queries[] = "DELETE FROM Portefeuilles WHERE consolidatie = 2 AND ((Client = '" . $data['Client'] . "' AND Portefeuille = '" . $virtuelePortefeuille . "') OR add_date < (now() - interval 1 hour))";
        }
      }
	    for ($a=0; $a < count($queries); $a++)
      {
	      $this->db->SQL($queries[$a]);
	      $this->db->Query();
      }
    }
  }
*/
  function getSelectie($sort=true)
  {
    if($sort)
      $this->sortRecords($this->orderby);
    return $this->selected;
  }

  function sortRecords($veld='Client')
  {
    $veld=trim($veld);
    if($veld == 'Portefeuilles.Vermogensbeheerder')
    {
      $veld = 'Vermogensbeheerder';
    }
    elseif($veld == 'Portefeuilles.Accountmanager')
    {
      $veld = 'Accountmanager';
    }
    elseif($veld == 'Portefeuilles.Remisier')
    {
      $veld = 'Remisier';
    }
    elseif ($veld == 'Portefeuilles.Portefeuille')
    {
      $veld = 'Portefeuille';
    }
	  else
		{
			$veld  = "Client";
		}

    reset($this->selected);
    foreach ($this->selected as $Portefeuille=>$data)
		{
		  $temp[$data[$veld]][] = $data ;
		}

    ksort($temp);

    $this->selected = array();
    foreach ($temp as $veld=>$data)
		{
		  //listarray($data);
		  for($i=0; $i<count($data); $i++)
      {
         $this->selected[$data[$i]['Portefeuille']] = $data[$i];
      }
		}

  }


  function getRecords()
  {
    return count($this->selected);
  }


}





?>