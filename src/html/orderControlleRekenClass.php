<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/09/05 16:50:21 $
 		File Versie					: $Revision: 1.30 $

 		$Log: orderControlleRekenClass.php,v $
 		Revision 1.30  2015/09/05 16:50:21  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2015/07/08 15:36:42  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2015/06/21 11:59:00  rvv
 		*** empty log message ***

 		Revision 1.22  2013/10/01 14:48:38  rvv
 		*** empty log message ***

 		Revision 1.21  2013/09/22 15:23:37  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2013/09/18 15:37:28  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2012/07/04 16:04:11  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2012/05/30 16:01:27  rvv
 		*** empty log message ***

 		Revision 1.17  2012/01/28 16:13:06  rvv
 		*** empty log message ***

 		Revision 1.16  2012/01/03 13:04:24  rvv
 		*** empty log message ***

 		Revision 1.15  2011/12/28 18:45:14  rvv
 		*** empty log message ***

 		Revision 1.14  2011/12/18 14:25:43  rvv
 		*** empty log message ***

 		Revision 1.13  2011/12/04 12:55:26  rvv
 		*** empty log message ***

 		Revision 1.12  2011/11/19 15:41:14  rvv
 		*** empty log message ***

 		Revision 1.11  2011/11/12 18:32:28  rvv
 		*** empty log message ***

 		Revision 1.10  2011/11/05 15:49:04  rvv
 		*** empty log message ***

 		Revision 1.9  2011/11/03 19:26:01  rvv
 		*** empty log message ***

 		Revision 1.8  2011/10/30 13:31:24  rvv
 		*** empty log message ***

 		Revision 1.7  2011/09/14 09:26:56  rvv
 		*** empty log message ***

 		Revision 1.6  2011/03/23 16:57:38  rvv
 		*** empty log message ***

 		Revision 1.5  2008/06/30 06:53:04  rvv
 		*** empty log message ***

 		Revision 1.4  2006/11/03 11:29:01  rvv
 		Na user update

 		Revision 1.3  2006/10/31 12:17:57  rvv
 		Voor user update

 		Revision 1.2  2006/10/17 08:31:09  rvv
 		Vermogensbeheerder ophalen uit portefeuille

 		Revision 1.1  2006/10/17 06:16:11  rvv
 		ordercontrole


*/

include_once("../classes/AE_cls_fpdf.php");
include_once("./rapport/Zorgplichtcontrole.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");

Class orderControlleBerekening
{
  var $__ORDERvar ;

  function orderControlleBerekening($bulk=false)
  {
	$this->data   = array();
	$this->checks = array();
	$this->allchecks = array();
	$this->vermogensbeheerderchecks = array();
  $this->bulk=$bulk;
  }

  function setallchecks($data = array())
  {
  	$this->allchecks = $data;
  }


  function setchecks($data = array())
  {
  	while (list($key, $value) = each($data))
	  {
	  if ($value['checked']==1)
  	  $this->checks[$key]['checked']=($__ORDERvar["orderControles"][$key]='1');
   	else
  	  $this->checks[$key]['checked']=($__ORDERvar["orderControles"][$key]='0');
  	}
  }

  function checkMaxGetal()
  {
  	$resultaat=$this->check();
  	$hoogste = 0;
  	foreach($resultaat as $keyname => $value)
	  {
	    if ($this->checksKort[$keyname] > $hoogste ) 
        $hoogste = $this->checksKort[$keyname];
	  }

	  return $hoogste;
  }

  function checkmax()
  {
  	$resultaat=$this->check();
  	$hoogste = 0;
  	foreach($resultaat as $keyname => $value)
	  {
	    if ($value > $hoogste ) 
        $hoogste = $value;
	  }
	return $hoogste;
  }

  function check()
  {

  	if ($this->checks['aanw']['checked'] == 1 && $this->checks['aanw']['negeren'] == 0)
  	{
  		$resultaat['aanw'] = $this->aanwezigheidsCheck();
 	}
 	if ($this->checks['short']['checked'] == 1 && $this->checks['short']['negeren'] == 0)
  	{
  		$resultaat['short'] =  $this->ShortPositiesCheck();
  	}
 	if ($this->checks['liqu']['checked'] == 1 && $this->checks['liqu']['negeren'] == 0)
  	{
   		$resultaat['liqu'] = $this->LiquiditeitenCheck();
 	}
 	if ($this->checks['zorg']['checked'] == 1 && $this->checks['zorg']['negeren'] == 0)
  	{
  		$resultaat['zorg'] = $this->ZorgplichtCheck();

 	}
 	if ($this->checks['risi']['checked'] == 1 && $this->checks['risi']['negeren'] == 0)
  	{
  		$resultaat['risi'] = $this->RisicoCheck();
 	}

  	if ($this->data['tijdelijkeTabel'] == 1)
  	  $this->VerwijderTijdelijkeTabel();

 	return $resultaat;

  }

  function getchecks($vermogensbeheerder='')
  {
    if($this->data['fonds'] == '')
    {
      $this->vermogensbeheerderchecks=array();
      return 0;
    }
  	$db=new DB();
    
    if($vermogensbeheerder<>'')
      $vermogensbeheerderFilter="WHERE Vermogensbeheerder='$vermogensbeheerder'";
    
  	$query = "SELECT Vermogensbeheerders.Vermogensbeheerder, Vermogensbeheerders.order_controle FROM Vermogensbeheerders $vermogensbeheerderFilter";
	  $db->SQL($query);
	  $db->Query();
  	while ($checks = $db->nextRecord())
    {
      $this->vermogensbeheerderchecks[$checks['Vermogensbeheerder']] = $checks['order_controle'];
	  }

    return $this->vermogensbeheerderchecks;
  }

  function setregels($regels=array())
  {
    if (count($regels) >0)
    {
    while (list($key, $value) = each($regels))
	  {
	    if ($value['checked']==1)
	    {
  	    $this->checks[$key]['negeren']='1';
	    }
  	    else
  	    {
  	    $this->checks[$key]['negeren']='0';
	    }
	  }
    }
    else
    {
		foreach ($this->allchecks as $key => $value)
		{
			$this->checks[$key]['negeren']='0';
		}
    }

  }


  function setdata($orderid,$portefeuille,$valuta,$aantal,$silent=false)
  {

	$this->data['eigenOrderid']=$orderid;
	$this->data['portefeuille']=$portefeuille;
	$this->data['valuta']=$valuta;
	$this->data['transactieAantal'] = $aantal;
	$this->data['silent']=$silent;
  $this->data['bulk']=$this->bulk;
	$this->data['rapportageDatum']=substr(getLaatsteValutadatum(),0,10);
	$this->data['tijdelijkeTabel'] = 0;

	$db = new db();
  
  if($this->bulk==true)
  {
    $query="SELECT TijdelijkeBulkOrders.id, TijdelijkeBulkOrders.Fonds,transactieSoort, '' as transactieType,
    Portefeuilles.vermogensBeheerder,Fondsen.Omschrijving as FondsNaam,Valuta,koersLimiet 
    FROM TijdelijkeBulkOrders 
    JOIN Portefeuilles ON TijdelijkeBulkOrders.Portefeuille=Portefeuilles.Portefeuille
    JOIN Fondsen ON TijdelijkeBulkOrders.Fonds=Fondsen.Fonds WHERE TijdelijkeBulkOrders.id='$orderid'";
  	$db->SQL($query);
    $huidige = $db->lookupRecord();
   // $this->data['eigenOrderid']=$huidige['id'];
  }
  else
  {
  	$query="SELECT orderid FROM Orders WHERE id='$orderid'";
  	$db->SQL($query);
    $orderid = $db->lookupRecord();
    if($orderid['orderid'] <> '')
      $this->data['eigenOrderid']=$orderid['orderid'];

	  $eigenOrderid = $this->data['eigenOrderid'];

    $query = "SELECT Orders.Fonds,
  				   Orders.transactieSoort,
  				   Orders.transactieType,
  				   Orders.Vermogensbeheerder,
  				   Fondsen.Fonds as FondsNaam,
  				   Fondsen.Valuta,
  				   Orders.koersLimiet
  			FROM Orders LEFT JOIN Fondsen ON Orders.Fonds = Fondsen.Fonds
  			WHERE orderid = '".$eigenOrderid."'";
    $db=new DB();
    $db->SQL($query);
    $db->Query();
    $huidige = $db->nextRecord();
  }
    
    $this->data['fonds'] = $huidige['Fonds'];//=> Greater Europe Fund
    $this->data['transactieSoort'] = $huidige['transactieSoort']; //=> V
    $this->data['transactieType'] = $huidige['transactieType'];//=>
    $this->data['vermogensBeheerder'] = $huidige['vermogensBeheerder']; //=> dgc
    $this->data['fondsNaam'] = $huidige['FondsNaam']; //=> Greater Europe
    $this->data['fondsValuta'] = $huidige['Valuta'] ;//=> USD
    $this->data['koersLimiet'] = $huidige['koersLimiet'] ;//=> 0.00000

  }

  function vulTijdelijkeTabel()
  {
	$fondswaarden =  berekenPortefeuilleWaarde($this->data['portefeuille'],  $this->data['rapportageDatum']);
	vulTijdelijkeTabel($fondswaarden ,$this->data['portefeuille'], $this->data['rapportageDatum']);
	$this->data['tijdelijkeTabel'] = 1;
  }

  function VerwijderTijdelijkeTabel()
  {
  	verwijderTijdelijkeTabel($this->data['portefeuille'],  $this->data['rapportageDatum']);
  	$this->data['tijdelijkeTabel'] = 0;
  }


  function aanwezigheidsCheck()
  {
	  $portefeuille = $this->data['portefeuille'];
	  $eigenOrderid = $this->data['eigenOrderid'];
	  $txt = "";
	  $txtSilent = "0";

  	$db = new DB();
    if($this->bulk==true)
    {
	    $query =   "SELECT id as orderid, aantal, Fonds, transactieSoort
		  		FROM TijdelijkeBulkOrders
			  	WHERE
		  		Fonds = '".$this->data['fonds']."'
			  	AND Portefeuille = '".$portefeuille."'
			  	AND id <> '".$eigenOrderid."' ";
	    $db->SQL($query);
	    $db->Query();
      while ($actieveTransacties = $db->nextRecord())
      {
	      $txt .= vt('In bulkorder') . " ". $actieveTransacties['orderid']." "
				 . $actieveTransacties['transactieSoort']." "
				 . $actieveTransacties['aantal']." "
				 . $actieveTransacties['Fonds']."<br>";
	      $txtSilent = '1';
      }
      $eigenOrderid=-1;
    }
	  $query =   "SELECT OrderRegels.orderid, OrderRegels.aantal, Orders.Fonds, Orders.transactieSoort
				FROM Orders, OrderRegels
				WHERE OrderRegels.orderid = Orders.orderid
				AND Orders.Fonds = '".$this->data['fonds']."'
				AND OrderRegels.Portefeuille = '".$portefeuille."'
				AND OrderRegels.status < '4'
				AND OrderRegels.orderid <> '".$eigenOrderid."' ";
	  $db->SQL($query);
	  $db->Query();
    while ($actieveTransacties = $db->nextRecord())
    {
	    $txt .= vt('In') . " ". $actieveTransacties['orderid']." "
				 . $actieveTransacties['transactieSoort']." "
				 . $actieveTransacties['aantal']." "
				 . $actieveTransacties['Fonds']."<br>";
	    $txtSilent = '1';
    }
    if ($txt == "")
      $txt=vt("Geen niet-uitgevoerde orders voor dit fonds gevonden.");
    $this->checksKort['aanw']=$txtSilent;

    if ($this->data['silent'] == true)
      return $txtSilent;
    else
	  return $txt;
  }

  function ShortPositiesCheck()
  {
	$db = new DB();
	$portefeuille = $this->data['portefeuille'];
	$eigenOrderid = $this->data['eigenOrderid'];
	$transactieAantal 	= $this->data['transactieAantal'];
	$txt ="";
	$txtSilent = "0";

   if ($this->data['transactieSoort'] == "V")
   {
    $txt = "Verkoop check<br>";
    if($this->bulk==true)
    {
      $query = "SELECT id as orderid, aantal, Fonds, transactieSoort
		  		FROM TijdelijkeBulkOrders
			  	WHERE
		  		Fonds = '".$this->data['fonds']."'
			  	AND Portefeuille = '".$portefeuille."'
			  	AND id <> '".$eigenOrderid."' ";
      $db->SQL($query);
      $db->Query();
      while ($actieveTransacties = $db->nextRecord())
      {
        if ($actieveTransacties['transactieSoort']=="A" || $actieveTransacties['transactieSoort']=="AO")
          $totaalAantal += $actieveTransacties['aantal'];
        if ($actieveTransacties['transactieSoort']=="V")
          $totaalAantal -= $actieveTransacties['aantal'];
      }
      $eigenOrderid=-1;  
    }
    $query =   "SELECT OrderRegels.orderid, OrderRegels.aantal, Orders.Fonds, Orders.transactieSoort
	  		   	FROM Orders, OrderRegels
				WHERE OrderRegels.orderid = Orders.orderid
				AND (Orders.Fonds = '".$this->data['fonds']."' OR Orders.Fonds='')
				AND OrderRegels.Portefeuille = '".$portefeuille."'
				AND OrderRegels.status < '4'
				AND OrderRegels.orderid <> '".$eigenOrderid."' ";
    $db->SQL($query);
    $db->Query();
    while ($actieveTransacties = $db->nextRecord())
    {
      if ($actieveTransacties['transactieSoort']=="A" || $actieveTransacties['transactieSoort']=="AO")
        $totaalAantal += $actieveTransacties['aantal'];
      if ($actieveTransacties['transactieSoort']=="V")
        $totaalAantal -= $actieveTransacties['aantal'];
    }
    $aantalAanwezig = fondsAantalOpdatum($portefeuille, $this->data['fondsNaam'], $this->data['rapportageDatum']);
    $totaalAantal += $aantalAanwezig['totaalAantal']; //Het al in Portefeuille aanwezige aantal ophalen.
    $totaalAantal -= $transactieAantal; // Deze transactie erafhalen
    if (round($totaalAantal,4) < 0)
    {
      $txt = vtb('Na verkoop aantal < 0 ! (%s)', array(round($totaalAantal,4))) . " <br>";
      $txtSilent = '2';
    }
    else
    {
      $txt = "Na verkoop: ". round($totaalAantal,4). "<br>";
      $txtSilent = '0';
    }
   }
   else
     $txt=vt("Geen Verkoop transactie.");// ".$this->data['transactieSoort'];

     $this->checksKort['short']=$txtSilent;

    if ($this->data['silent'] == true)
      return $txtSilent;
    else
	  return $txt;
  }

  function LiquiditeitenCheck()
  {
	$db = new DB();
	$portefeuille 		= $this->data['portefeuille'];
	$eigenOrderid 		= $this->data['eigenOrderid'];
	$transactieAantal 	= $this->data['transactieAantal'];
	$rekeningValuta		= $this->data['valuta'];
	$rapportageDatum 	= $this->data['rapportageDatum'];

	$query = "SELECT Rekening, if(Rekeningen.Valuta='$rekeningValuta',-1,Valutas.Afdrukvolgorde) as volgorde , Rekeningen.Valuta
FROM Rekeningen JOIN Valutas ON Rekeningen.Valuta=Valutas.Valuta WHERE Rekeningen.Inactief=0 AND Rekeningen.Memoriaal=0 AND Rekeningen.Deposito=0 AND Portefeuille = '".$portefeuille."' order by volgorde limit 1";
	$db->SQL($query); 
	$db->Query();
	$rekening = $db->lookupRecord();
  if($rekeningValuta=='')
    $rekeningValuta= $rekening['Valuta'];
	$rekening = $rekening['Rekening'];


	$query="SELECT FondsEenheid FROM Fondsen WHERE Fonds='".$this->data['fondsNaam']."'";
  $db->SQL($query);
  $fonds=$db->lookupRecord();

   $txt ="";
  	$txtSilent = "0";

  	if ($this->data['transactieSoort'] == "A" || $this->data['transactieSoort'] == "AO" )
  	  {
      // haal actuele stand rekening op.
      $_beginJaar = substr($rapportageDatum,0,4)."-01-01";
      $query =   "SELECT SUM(Bedrag) as totaal FROM Rekeningmutaties
    			  WHERE boekdatum >= '".$_beginJaar."' AND
    			  boekdatum <= '".$rapportageDatum."'  AND
    		      Rekening = '".$rekening."'
    			  Group By Rekeningmutaties.Rekening";
      $db->SQL($query);  
 	  $rekeningSaldo = $db->lookupRecord();

	  if ($this->data['koersLimiet'] != 0 ) //Indien Limiet dan deze koers gebruiken
		{
	    $aankoopBedrag = $transactieAantal * $this->data['koersLimiet'] * $fonds['FondsEenheid'];
		}
	  else //Koersen ophalen.
		{
 	    $query = "SELECT Koers,Datum FROM Fondskoersen
 	  			  WHERE Fonds = '".$this->data['fondsNaam']."' AND
 	  			  Datum <= '".$this->data['rapportageDatum']."'
 	  			  ORDER BY Datum DESC LIMIT 1";
	    $db->SQL($query);
	    $huidigeFondsKoers = $db->lookupRecord();
	    $aankoopBedrag = $transactieAantal * $huidigeFondsKoers['Koers'] * $fonds['FondsEenheid'];

		}
		$query =   "SELECT Koers,Datum FROM Valutakoersen
				    WHERE Valuta = '".$this->data['fondsValuta']."' AND
				    Datum <= '".$this->data['rapportageDatum']."'
				    ORDER BY Datum DESC LIMIT 1";

    	$db->SQL($query);
 		$FondsValutaKoers = $db->lookupRecord();

 		$query =   "SELECT Koers,Datum FROM Valutakoersen
					WHERE Valuta = '".$rekeningValuta."' AND
					Datum <= '".$this->data['rapportageDatum']."'
					ORDER BY Datum DESC LIMIT 1";
    	$db->SQL($query);
 		$RegekningValutaKoers = $db->lookupRecord();

 		$newSaldo = ( ( $rekeningSaldo['totaal'] * $RegekningValutaKoers['Koers'] ) - ( $aankoopBedrag * $FondsValutaKoers['Koers'] )) / $RegekningValutaKoers['Koers'] ;
  	  	if ($newSaldo < 0) //Salso na te plannen aankoop (Nog zonder overige lopende transacties)
  	  	{
 		  $txt = "Saldo na huidige order $rekeningValuta ". number_format($newSaldo,2,",",".")." <br> " ;
		  $txtSilent = '2';
  	  	}
		// Overige lopende transacties op huidige rekening.
    if($this->bulk==true)
    {
      $query = "SELECT id as orderid, aantal, fonds, transactieSoort, koersLimiet
		  		FROM TijdelijkeBulkOrders
			  	WHERE
		  		Fonds = '".$this->data['fonds']."'
			  	AND Portefeuille = '".$portefeuille."'
			  	AND id <> '".$eigenOrderid."' ";
          
      $db->SQL($query); //echo " $newSaldo <br>\n $query <br>\n";
      $db->Query();
 		  $db2 = new DB();
      while ($overigeOrders = $db->nextRecord())
      {
		    $query = "SELECT Fondskoersen.Koers, Fondskoersen.Datum, Fondsen.Valuta, Fondsen.Fondseenheid  FROM Fondskoersen ,Fondsen
 	  			    WHERE Fondskoersen.Fonds = Fondsen.Fonds AND
 	  			    Fondsen.Fonds = '".$overigeOrders['fonds']."' AND
 	  			    Fondskoersen.Datum <= '".$this->data['rapportageDatum']."'
 	  			    ORDER BY Datum DESC LIMIT 1";
	    	$db2->SQL($query);
	  	  $overigeFondsKoers = $db2->lookupRecord();
 		    $query =   "SELECT Koers,Datum FROM Valutakoersen
					  WHERE Valuta = '".$overigeFondsKoers['Valuta']."' AND
					  Datum <= '".$this->data['rapportageDatum']."'
					  ORDER BY Datum ASC LIMIT 1";
    	  $db2->SQL($query);
 		    $overigeFondsValutaKoers = $db2->lookupRecord();

			  if ($overigeOrders['koersLimiet'] != 0)
			  {
		     	$overigeFondsKoers['Koers']= $overigeOrders['koersLimiet'];
			  }
 			  if ($overigeOrders['transactieSoort'] == "A" || $overigeOrders['transactieSoort'] == "AO" ) //aankopen aftrekken van Saldo
 			  {
 			    $newSaldo -= ($overigeOrders['aantal'] * $overigeFondsKoers['Koers'] * $overigeFondsKoers['Fondseenheid'] * $overigeFondsValutaKoers['Koers']) / $RegekningValutaKoers['Koers'] ;
 		  	}
 			  if ($overigeOrders['transactieSoort'] == "V" || $overigeOrders['transactieSoort'] == "VS"  ) //Verkopen optellen bij saldo
 			  {
 		  	  $newSaldo += ($overigeOrders['aantal'] * $overigeFondsKoers['Koers'] * $overigeFondsKoers['Fondseenheid'] * $overigeFondsValutaKoers['Koers']) / $RegekningValutaKoers['Koers'] ;
 			  }
      }
      $eigenOrderid=-1;
    }
 		$query = 	"SELECT OrderRegels.aantal, Orders.transactieSoort, Orders.orderid, Orders.fonds, Orders.koersLimiet FROM OrderRegels, Orders
 					WHERE OrderRegels.orderid = Orders.orderid AND
 					((OrderRegels.status < 4 AND (Orders.transactieSoort = 'A' OR Orders.transactieSoort = 'AO')) OR
 					(OrderRegels.status = 2 OR OrderRegels.status = 3) AND (Orders.transactieSoort = 'V' OR Orders.transactieSoort = 'VS'))  AND
 					OrderRegels.Valuta = '".$rekeningValuta."' AND
 					OrderRegels.Portefeuille = '".$portefeuille."'
 					AND Orders.orderid <> '".$eigenOrderid."' ";
    $db->SQL($query); //echo " $newSaldo <br>\n $query <br>\n";
    $db->Query();
 		$db2 = new DB();
    while ($overigeOrders = $db->nextRecord())
    {
		  $query = "SELECT Fondskoersen.Koers, Fondskoersen.Datum, Fondsen.Valuta, Fondsen.Fondseenheid  FROM Fondskoersen ,Fondsen
 	  			    WHERE Fondskoersen.Fonds = Fondsen.Fonds AND
 	  			    Fondsen.Fonds = '".$overigeOrders['fonds']."' AND
 	  			    Fondskoersen.Datum <= '".$this->data['rapportageDatum']."'
 	  			    ORDER BY Datum DESC LIMIT 1";
	  	$db2->SQL($query);
	  	$overigeFondsKoers = $db2->lookupRecord();
 		  $query =   "SELECT Koers,Datum FROM Valutakoersen
					  WHERE Valuta = '".$overigeFondsKoers['Valuta']."' AND
					  Datum <= '".$this->data['rapportageDatum']."'
					  ORDER BY Datum ASC LIMIT 1";
    	$db2->SQL($query);
 		  $overigeFondsValutaKoers = $db2->lookupRecord();

			if ($overigeOrders['koersLimiet'] != 0)
			{
		   	$overigeFondsKoers['Koers']= $overigeOrders['koersLimiet'];
			}
 			if ($overigeOrders['transactieSoort'] == "A" || $overigeOrders['transactieSoort'] == "AO" ) //aankopen aftrekken van Saldo
 			{
 			  // echo $overigeOrders['fonds']." ".$overigeOrders['transactieSoort']." (".$overigeOrders['aantal']." *
 			  // ".$overigeFondsKoers['Koers']." * ".$overigeFondsKoers['Fondseenheid']."*".$overigeFondsValutaKoers['Koers'].") / ".$RegekningValutaKoers['Koers']."=".(($overigeOrders['aantal'] * $overigeFondsKoers['Koers'] *$overigeFondsKoers['Fondseenheid']* $overigeFondsValutaKoers['Koers']) / $RegekningValutaKoers['Koers'])."<br>\n" ;
		    $newSaldo -= ($overigeOrders['aantal'] * $overigeFondsKoers['Koers'] * $overigeFondsKoers['Fondseenheid'] * $overigeFondsValutaKoers['Koers']) / $RegekningValutaKoers['Koers'] ;
 			}
 			if ($overigeOrders['transactieSoort'] == "V" || $overigeOrders['transactieSoort'] == "VS"  ) //Verkopen optellen bij saldo
 			  {
 		  	  $newSaldo += ($overigeOrders['aantal'] * $overigeFondsKoers['Koers'] * $overigeFondsKoers['Fondseenheid'] * $overigeFondsValutaKoers['Koers']) / $RegekningValutaKoers['Koers'] ;
 			  }
      		}
      		//echo " $newSaldo <br>\n<br>\n";
     	$txt .= vtb( "Saldo na alle openstaande orders %s %s" , array($rekeningValuta, number_format($newSaldo,2,",","."))) . "  <br>";
     	if ($newSaldo < 0) //Salso na te plannen aankoop (Nog zonder overige lopende transacties)
  	  	{
		  $txtSilent = '2';
  	  	}
  	  	else
  	  	{
  	  	  if ($txtSilent == '2')
  	  	   $txtSilent = '1';
  	  	  else
  	  	   $txtSilent = '0';
  	  	}
  	  }
  	  else
  	  {
  	   $txtSilent = '0';
  	   $txt = vt("Geen aankoop order.");
  	  }
  	  $this->checksKort['liqu']=$txtSilent;

    if ($this->data['silent'] == true)
      return $txtSilent;
    else
	  return $txt;
  }

  function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}

  function ZorgplichtCheck()
	{
	global $__appvar,$USR;

	$db = new DB();
	$portefeuille 		= $this->data['portefeuille'];
	$eigenOrderid 		= $this->data['eigenOrderid'];
	$transactieAantal 	= $this->data['transactieAantal'];
	$rekeningValuta		= $this->data['valuta'];
	$rapportageDatum 	= substr($this->data['rapportageDatum'],0,10);
	$zorgMeting = "Voldoet ";
	$zorgMetingReden = "";
	$Vermogensbeheerder = $this->data['vermogensBeheerder'];

	if ($this->data['tijdelijkeTabel'] == 0)
	{
  	$fondswaarden =  berekenPortefeuilleWaarde($portefeuille,  $rapportageDatum);
  	vulTijdelijkeTabel($fondswaarden ,$portefeuille, $rapportageDatum);
  	$this->data['tijdelijkeTabel'] = 1;
	}


	 $orders=array();
	 $orders[]=array('aantal'=>$this->data['transactieAantal'],'transactieSoort'=>$this->data['transactieSoort'],'orderid'=>$eigenOrderid,'fonds'=>$this->data['fonds'],'koersLimiet'=>$this->data['koersLimiet']);
	 if($this->bulk==true)
   {
      $query = "SELECT id as orderid, aantal, fonds, transactieSoort, koersLimiet
		  		FROM TijdelijkeBulkOrders
			  	WHERE
		  		Fonds = '".$this->data['fonds']."'
			  	AND Portefeuille = '".$portefeuille."'
			  	AND id <> '".$eigenOrderid."' ";
         
     $db->SQL($query);// echo " $query <br>\n";
     $db->Query();
 	   while ($overigeOrders = $db->nextRecord())
       $orders[]=$overigeOrders;
     
     $eigenOrderid=-1;
   } 
    
     $query = 	"SELECT OrderRegels.aantal, Orders.transactieSoort, Orders.orderid, Orders.fonds, Orders.koersLimiet FROM OrderRegels, Orders
 							WHERE OrderRegels.orderid = Orders.orderid AND OrderRegels.status < 4 AND OrderRegels.Portefeuille = '".$portefeuille."' AND Orders.orderid <> '".$eigenOrderid."'";
   	$db->SQL($query);// echo " $query <br>\n";
   	$db->Query();
 		while ($overigeOrders = $db->nextRecord())
      $orders[]=$overigeOrders;


    $query="SELECT Portefeuille,Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='$portefeuille' ";
    $db->SQL($query);
    $pdata=$db->lookupRecord();

      foreach ($orders as $order)
      {
        $aantal=$order['aantal'];
        if(in_array($order['transactieSoort'],array('V','VO','VS')))
          $aantal=$aantal*-1;

        $query="SELECT Valuta,FondsEenheid FROM Fondsen WHERE Fonds='".$order['fonds']."'";
        $db->SQL($query);
    		$fonds=$db->lookupRecord();
    		$valutaKoers=getValutaKoers($fonds['Valuta'],$rapportageDatum);
    		$fondsKoers=$this->getFondsKoers($order['fonds'],$rapportageDatum);
    		$aankoopWaarde=$aantal*$valutaKoers*$fondsKoers*$fonds['FondsEenheid'];
    		//echo "$aankoopWaarde = ".$aantal." * $valutaKoers * $fondsKoers * ".$fonds['FondsEenheid']."<br>\n ";

    		$query="SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.type = 'fondsen' AND ".
									 " TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."' AND ".
									 " TijdelijkeRapportage.portefeuille = '$portefeuille'  AND ".
									 " TijdelijkeRapportage.Fonds = '".$order['fonds']."' ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
				$db->SQL($query);
				$db->Query();
        if($db->records() > 0)
          $query="UPDATE TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro=actuelePortefeuilleWaardeEuro+$aankoopWaarde
					          WHERE TijdelijkeRapportage.type = 'fondsen' AND ".
									 " TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."' AND ".
									 " TijdelijkeRapportage.portefeuille = '$portefeuille'  AND ".
									 " TijdelijkeRapportage.Fonds = '".$order['fonds']."' ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
        else
        {
          $query="SELECT Fonds,Beleggingscategorie FROM BeleggingscategoriePerFonds WHERE Vermogensbeheerder='".$pdata['Vermogensbeheerder']."' AND Fonds = '".mysql_real_escape_string($order['fonds'])."' ";
          $db->SQL($query);
    		  $Beleggingscategorie=$db->lookupRecord();
    		  $Beleggingscategorie=$Beleggingscategorie['Beleggingscategorie'];

          $query="INSERT INTO TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro='$aankoopWaarde', add_user='$USR', TijdelijkeRapportage.sessionId = '".$_SESSION['usersession']['sessionId']."',
                    TijdelijkeRapportage.type = 'fondsen', Beleggingscategorie='$Beleggingscategorie',
                    rapportageDatum ='".$rapportageDatum."',
                    portefeuille = '$portefeuille',
                    Fonds='".mysql_real_escape_string($order['fonds'])."' ";
        }
				$db->SQL($query);
				$db->Query();
        $query="UPDATE TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro=actuelePortefeuilleWaardeEuro-$aankoopWaarde
					          WHERE TijdelijkeRapportage.type = 'Rekening' AND ".
									 " TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."' AND ".
									 " TijdelijkeRapportage.portefeuille = '$portefeuille' ".$__appvar['TijdelijkeRapportageMaakUniek']." LIMIT 1 ";
				$db->SQL($query);
				$db->Query();

      }

   $zorgplicht = new Zorgplichtcontrole();

   $zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$rapportageDatum);

   if($zpwaarde['voldoet']=='Nee')
      $txtSilent=1;


   $txt= "Conclusie: ".$zpwaarde['zorgMeting']."<br>\n";
   $txt .=$zpwaarde['zorgMetingReden'];
   foreach ($zpwaarde['conclusie'] as $regel)
   {
  //  $txt .=$regel[4]."<br>\n";
   }

   $this->checksKort['zorg']=$txtSilent;
    if ($this->data['silent'] == true)
      return $txtSilent;
    else
	  return $txt;
}

  function RisicoCheck()
	{
	global $__appvar;

	$db = new DB();
	$db2 = new DB();
	$portefeuille 		= $this->data['portefeuille'];
	$eigenOrderid 		= $this->data['eigenOrderid'];
	$transactieAantal 	= $this->data['transactieAantal'];
	$rekeningValuta		= $this->data['valuta'];
	$rapportageDatum 	= $this->data['rapportageDatum'];

	if ($this->data['tijdelijkeTabel'] == 0 )
	{
	$fondswaarden =  berekenPortefeuilleWaarde($portefeuille,  $rapportageDatum);
	vulTijdelijkeTabel($fondswaarden ,$portefeuille, $rapportageDatum);
	$this->data['tijdelijkeTabel'] = 1;
	}
	$DB3 = new DB();
	// haal totaalwaarde op om % te berekenen
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$rapportageDatum."' AND ".
							 " portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB3->SQL($query);
	$DB3->Query();
	$totaalWaarde = $DB3->nextRecord();
	$totaalWaarde = $totaalWaarde[totaal];
//	$totaalWaarde += $this->WaardeAankopen();
	$query ="SELECT Beleggingscategorien.Omschrijving, ".
			" BeleggingscategoriePerFonds.RisicoPercentageFonds, ".
			" Valutas.Omschrijving AS ValutaOmschrijving, ".
			" Fondsen.Fonds AS Fonds, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
			" FROM (TijdelijkeRapportage, Portefeuilles, BeleggingscategoriePerFonds)  ".
			" LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  ".
			" LEFT JOIN Fondsen on (TijdelijkeRapportage.fonds = Fondsen.Fonds)  ".
			" LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
			" WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
			" Portefeuilles.Portefeuille = TijdelijkeRapportage.portefeuille AND ".
			" Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder AND ".
			" BeleggingscategoriePerFonds.Fonds = TijdelijkeRapportage.fonds  AND ".
			" TijdelijkeRapportage.type = 'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.fonds, TijdelijkeRapportage.valuta ".
			" ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";
	debugSpecial($query,__FILE__,__LINE__);
	$DB3 = new DB();
	$DB3->SQL($query);
	$DB3->Query();
//echo $query;
	while($categorien = $DB3->NextRecord())
	{
	$risico = $categorien[RisicoPercentageFonds];
	$risicoBedrag = (ABS($categorien[subtotaalactueel]) / 100) * $risico;
	$risicoTotaal += $risicoBedrag;
	}
// huidige orders ophalen

	 if($this->bulk==true)
   {
      $query = "SELECT TijdelijkeBulkOrders.id as orderid, aantal, TijdelijkeBulkOrders.fonds, transactieSoort, koersLimiet,
      							BeleggingscategoriePerFonds.RisicoPercentageFonds,
							Fondsen.valuta,
							Fondsen.fonds as fondskort
TijdelijkeBulkOrders 
INNER JOIN Fondsen ON TijdelijkeBulkOrders.fonds = Fondsen.Fonds
INNER JOIN Portefeuilles ON TijdelijkeBulkOrders.portefeuille = Portefeuilles.Portefeuille 
INNER JOIN BeleggingscategoriePerFonds ON BeleggingscategoriePerFonds.fonds = Fondsen.fonds AND Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder
			  	WHERE 
		  		TijdelijkeBulkOrders.Fonds = '".$this->data['fonds']."'
			  	AND TijdelijkeBulkOrders.Portefeuille = '".$portefeuille."'
			  	AND TijdelijkeBulkOrders.id <> '".$eigenOrderid."' ";
         
     $DB3->SQL($query);// echo " $query <br>\n";
     $DB3->Query();
 	   while ($overigeOrders = $DB3->nextRecord())
       $orderdata[]=$overigeOrders;
     
     $eigenOrderid=-1;
   } 
   
			$query= "SELECT OrderRegels.aantal,
							Orders.transactieSoort,
							Orders.orderid,
							Orders.fonds,
							Orders.koersLimiet,
							BeleggingscategoriePerFonds.RisicoPercentageFonds,
							Fondsen.valuta,
							Fondsen.fonds as fondskort
FROM 	OrderRegels
INNER JOIN  Orders ON OrderRegels.orderid = Orders.orderid 
INNER JOIN Fondsen ON 	Orders.fonds = Fondsen.Fonds 
INNER JOIN Portefeuilles ON OrderRegels.portefeuille = Portefeuilles.Portefeuille 
INNER JOIN BeleggingscategoriePerFonds ON BeleggingscategoriePerFonds.fonds = Fondsen.fonds AND Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder
					WHERE 
 							OrderRegels.status < 4 AND
 							OrderRegels.Portefeuille =  '".$portefeuille."'";
			$DB3->SQL($query);
			$DB3->Query();
			while($orders = $DB3->NextRecord())
			{
			  $orderdata[]=$orders;
      }
       
      foreach($orderdata as $orders)
      { 
			$query =   "SELECT Koers,Datum FROM Valutakoersen
						WHERE Valuta = '".$orders['valuta']."' AND
						Datum <= '".$rapportageDatum."'
						ORDER BY Datum ASC LIMIT 1";
    		$db2->SQL($query);
 			$fondsValutaKoers = $db2->lookupRecord();

 			$query = "SELECT Koers,Datum FROM Fondskoersen
 	  			WHERE Fonds = '".$orders['fondskort']."' AND
 	  			Datum <=  '".$rapportageDatum."'
 	  			ORDER BY Datum DESC LIMIT 1";
	  		$db2->SQL($query);
	  		$fondsKoers = $db2->lookupRecord();

	  		if ($orders['koersLimiet'] != 0)
	  		{
	  		$fondsKoers['Koers'] = $orders['koersLimiet'];
	  		}

 		if ($orders['transactieSoort'] == "A" ) //aankopen aftrekken van Saldo
 		{
		  $bedrag = -1 * ($orders['aantal'] * $fondsKoers['Koers'] * $fondsValutaKoers['Koers'] ) ;
 		}
 		else if ($orders['transactieSoort'] == "V" ) //Verkopen optellen bij saldo
 		{
 		  $bedrag = ($orders['aantal'] * $fondsKoers['Koers'] * $fondsValutaKoers['Koers']) ;
 		}
 			$risicoBedrag = ($bedrag / 100) * $orders['RisicoPercentageFonds'];
			$risicoTotaal += $risicoBedrag;
			$totaalWaarde += $bedrag; //Totaal waarde portefeuille ook aanpassen.
			}

//	eind huidige orders toevoegen

			$risicoPercentage = $risicoTotaal / ($totaalWaarde/100);

			// print risico klasse portefeuille.
			$query = "SELECT  ".
			" Risicoklassen.Risicoklasse, ".
			" Risicoklassen.Minimaal, ".
			" Risicoklassen.Maximaal ".
			" FROM Risicoklassen, Portefeuilles WHERE ".
			" Risicoklassen.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
			" Portefeuilles.Portefeuille = '".$portefeuille."' AND ".
			" Portefeuilles.Risicoklasse = Risicoklassen.Risicoklasse " ;

			$DB3->SQL($query);
			$DB3->Query();
			$risicodata = $DB3->nextRecord();

			if($risicoPercentage < $risicodata['Minimaal'])
			{
			$txt = number_format($risicoPercentage,2,",",".")." is minder dan ". $risicodata['Minimaal'];
			$txtSilent = '1';
			}
			elseif ($risicoPercentage > $risicodata['Maximaal'] )
			{
			$txt = number_format($risicoPercentage,2,",",".")." is meer dan ".$risicodata['Maximaal'];
			$txtSilent = '1';
			}
			else
			{
			$txt = number_format($risicoPercentage,2,",",".")." ligt tussen ".$risicodata['Minimaal'] . " en " .$risicodata['Maximaal'];
			$txtSilent = '0';
			}

		$this->checksKort['risi']=$txtSilent;

    if ($this->data['silent'] == true)
      return $txtSilent;
    else
	  return $txt;
}

function WaardeAankopen()
{
$db = new DB();
$portefeuille 		= $this->data['portefeuille'];
$eigenOrderid 		= $this->data['eigenOrderid'];
$transactieAantal 	= $this->data['transactieAantal'];
$rekeningValuta		= $this->data['valuta'];
$rapportageDatum 	= $this->data['rapportageDatum'];
$transactiesoort 	= $this->data['transactieSoort'];
$orderData=array();
    if($this->bulk==true)
    {
      $query = "SELECT id as orderid, aantal, fonds, transactieSoort, koersLimiet
		  		FROM TijdelijkeBulkOrders
			  	WHERE
			  	Portefeuille = '".$portefeuille."'
			  	AND id <> '".$eigenOrderid."' ";
          
      $db->SQL($query); //echo " $newSaldo <br>\n $query <br>\n";
      $db->Query();
      while ($overigeOrders = $db->nextRecord())
      {
        $orderData[]=$overigeOrders;
      }
    }  

 	$query = 	"SELECT OrderRegels.aantal, Orders.transactieSoort, Orders.orderid, Orders.fonds, Orders.koersLimiet FROM OrderRegels, Orders
 				WHERE OrderRegels.orderid = Orders.orderid AND
 				OrderRegels.status < 1 AND
 				OrderRegels.orderid <> '".$eigenOrderid."' AND
 				OrderRegels.Portefeuille = '".$portefeuille."'";
    $db->SQL($query);
    $db->Query();
 	$db2 = new DB();
    while ($overigeOrders = $db->nextRecord())
      {
        $orderData[]=$overigeOrders;
      }
      foreach($orderData as $overigeOrders)
      {
      	      $query = "SELECT 	Fondskoersen.Koers,
      				  	Fondskoersen.Datum,
      					Fondsen.Valuta,
      					Fondsen.Fonds FROM Fondskoersen, Fondsen
 	  			  WHERE Fondskoersen.Fonds = Fondsen.Fonds AND
 	  			  Fondsen.Fonds = '".$overigeOrders['fonds']."' AND
 	  			  Fondskoersen.Datum <= '".$rapportageDatum."'
 	  			  ORDER BY Datum DESC LIMIT 1";
	  	$db2->SQL($query);
	  	$overigeFonds = $db2->lookupRecord();

 		$query =   "SELECT Koers,Datum FROM Valutakoersen
					WHERE Valuta = '".$overigeFonds['Valuta']."' AND
					Datum <= '".$rapportageDatum."'
					ORDER BY Datum DESC LIMIT 1";
    	$db2->SQL($query);
 		$overigeRegekningValutaKoers = $db2->lookupRecord();

 		if($overigeOrders['koersLimiet]'] != 0)
 		  $overigeFonds['koers']= $overigeOrders['koersLimiet'];
 		if ($overigeOrders['transactieSoort'] == "A" ) //aankopen
 		  {
		  $waarde -= ($overigeOrders['aantal'] * $overigeFonds['Koers'] * $overigeFonds['Koers']) / $overigeRegekningValutaKoers['Koers'] ;
 		  }
 		if ($overigeOrders['transactieSoort'] == "V" ) //Verkopen
 		  {
 		  $waarde += ($overigeOrders['aantal'] * $overigeFonds['Koers'] * $overigeFonds['Koers']) / $overigeRegekningValutaKoers['Koers'] ;
 		  }
      }
//huidige aankoop

      	      $query = "SELECT 	Fondskoersen.Koers,
      				  	Fondskoersen.Datum,
      					Fondsen.Valuta,
      					Fondsen.Fonds FROM Fondskoersen, Fondsen
 	  			  WHERE Fondskoersen.Fonds = Fondsen.Fonds AND
 	  			  Fondsen.Fonds = '".$this->data['fondsNaam']."' AND
 	  			  Fondskoersen.Datum <= '".$rapportageDatum."'
 	  			  ORDER BY Datum DESC LIMIT 1";
	  	$db2->SQL($query);
	  	$overigeFonds = $db2->lookupRecord();

 		$query =   "SELECT Koers,Datum FROM Valutakoersen
					WHERE Valuta = '".$overigeFonds['Valuta']."' AND
					Datum <= '".$rapportageDatum."'
					ORDER BY Datum DESC LIMIT 1";
    	$db2->SQL($query);
 		$overigeRegekningValutaKoers = $db2->lookupRecord();

 		if($overigeOrders['koersLimiet]'] != 0)
 		  $overigeFonds['koers']= $overigeOrders['koersLimiet'];
 		if ($transactiesoort == "A" ) //aankopen
 		  {
		  $waarde -= ($transactieAantal * $overigeFonds['Koers'] ) * $overigeRegekningValutaKoers['Koers'] ;
 		  }
 		if ($transactiesoort == "V" ) //Verkopen
 		  {
 		  $waarde += ($transactieAantal * $overigeFonds['Koers'] ) * $overigeRegekningValutaKoers['Koers'] ;
 		  }
 		 return $waarde; //in Euro
}


}

?>