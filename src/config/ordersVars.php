<?PHP
/*
    AE-ICT source module
    Author  						: $Author: rm $
 	Laatste aanpassing	: $Date: 2018/06/27 09:03:43 $
 	File Versie					: $Revision: 1.46 $
*/
session_start();

//$__modules[]= "ordering";

if(!isset($_SESSION['__ORDERvar']["transactieType"]))
{
  if(GetModuleAccess('ORDER')==2)
  {
    $__ORDERvar["transactieType"]  = array("B"=>"Bestens","L"=>"Limiet");
    $__ORDERvar["tijdsSoort"]      = array("DAT"=>"Datum (limiet)","GTC"=>"Tot annulering");
  }
  else                                       
  {
    $__ORDERvar["transactieType"]  = array("B"=>"Bestens","L"=>"Limiet"); //"SL"=>"Stoploss" "IN"=>"Inline with market",
    $__ORDERvar["tijdsSoort"]      = array("DAT"=>"Datum limiet","GTC"=>"Tot annulering");
  }
  $_SESSION['__ORDERvar']["transactieType"]=$__ORDERvar["transactieType"];
  $_SESSION['__ORDERvar']["tijdsSoort"]    =$__ORDERvar["tijdsSoort"];
}
else
{
  $__ORDERvar["transactieType"]=$_SESSION['__ORDERvar']["transactieType"];
  $__ORDERvar["tijdsSoort"]    =$_SESSION['__ORDERvar']["tijdsSoort"];
}
/*
$__ORDERvar["transactieType"]  = array("B"=>"Bestens",
                                       "IN"=>"Inline with market",
                                       "L"=>"Limiet",
                                       "SL"=>"Stoploss");
*/

$__ORDERvar["transactieSoort"] = array(
  "A"   => vtb("Aankoop"),
  "V"   => vtb("Verkoop"),
  "AO"  => vtb("Aankoop / Openen"),
  "VO"  => vtb("Verkoop / Openen"),
  "AS"  => vtb("Aankoop / Sluiten"),
  "VS"  => vtb("Verkoop / Sluiten"),
  "I"   => vtb("Inschrijving")
);

$__ORDERvar["transactieSoortKleur"] = array("A" =>"green",
                                            "V" =>"red",
                                            "AO"=>"green",
                                            "VO"=>"green",
                                            "AS"=>"red",
                                            "VS"=>"red",
                                            "I" =>"green");
/*
$__ORDERvar["tijdsSoort"]      = array("DAT"=>"Datum limiet",
                                       "GTC"=>"Tot annulering");
*/
$__ORDERvar["status"]          = array("ingevoerd",//0
                                       "doorgegeven",//1
                                       "uitgevoerd",//2
                                       "uitgevoerd/gecontroleerd",//3
                                       "uitgevoerd/verwerkt",//4
                                       "vervallen",//5
                                       "geannuleerd",//6
                                       "geweigerd");//7
                                       
$__ORDERvar["orderStatus"]          = array(
  -1=>"in aanmaak",
  0=>"ingevoerd",//0
  1=>"doorgegeven",//1
  2=>"uitgevoerd",//2
  3=>"uitgevoerd/gecontroleerd",//3
  4=>"uitgevoerd/verwerkt",//4
  5=>"vervallen",//5
  6=>"geannuleerd",//6
  7=>"geweigerd"
);//7

$__ORDERvar["orderSoort"]  = array(
  'M' => 'Meervoudig	(1 fonds; meerdere portefeuilles)',
  'E' => 'Enkelvoudig (1 portefeuille; 1 fonds)',
  'C' => 'Combinatie (1 portefeuille; meerdere fondsen)',
  'N' => 'Nominaal - Bel. fondsen (1 portefeuille; 1 fonds)',
  'F'=>'FX-transacties Enkelvoudig (1 portefeuille; 1 fonds)',
  'X'=>'FX-transacties Meervoudig (1 fonds; meerdere portefeuilles)'
);


$__ORDERvar["laatsteStatus"]          = $__ORDERvar["status"];                                      
                                       
$__ORDERvar["orderControles"] = array(
  "aanw"    => vtb("Aanwezigheidscontrole"),
  "short"   => vtb("Short-posities"),
  "liqu"    => vtb("Voldoende liquiditeiten"),
  "zorg"    => vtb("Zorgplichtcriteria"),
  "risi"    => vtb("Risicometing"),
  "groot"   => vtb('Grootte'),
  "vbep"    => vtb('Validatie Beperkingen'),
  "akkam"   => vtb('Akkoord Accountmanager'),
  "optie"   => vtb('Optievalidaties'),
  "rest"    => vtb('Restricties')
);

$__ORDERvar['orderRechten']=array('handmatig'=>array('description'=>'Handmatige orderinvoer',
                                      'opties'=>array('opslaan'=>'Invoeren/opslaan order',
                                                      'verzenden'=>'Verzenden fix order',
                                                      'volgendeStatus'=>'Naar volgende status zetten (non-fix)',
                                                      'uitvoeringenMuteren'=>'Uitvoeringen muteren')),
                   'handmatigBulk'=>array('description'=>'Handmatige bulk invoer',
                                          'opties'=>array('opslaan'=>'Invoeren/opslaan bulkorderregel',
                                                          'verwerken'=>'Rechten tot bulkorder verwerk scherm')),
                   'rapportages'=>array('description'=>'Order toegang vanuit rapportages',
                                        'opties'=>array('aanmaken'=>'Orders aanmaken vanuit rapport')),
                   'verwerkenBulk'=>array('description'=>'Verwerken tijdelijke bulkorders',
                                          'opties'=>array('bewerken'=>'Regels aanpassen',
                                                          'valideren'=>'Validaties uitvoeren',
                                                          'genereren'=>'Omzetten naar orders',
                                                          'verzenden'=>'Verzenden fix order')));


$__ORDERvar['orderAdviesNotificaties'] = array (
  0  => 'Uit',
  1  => 'Alleen advies (verplicht)',
  2  => 'Advies incl. negeren',
  3  => 'Advies verplicht + overige',
  4  => 'Advies + negeren + overige',
  5  => 'Alles optioneel',
);

function getActieveControles($vermogensbeheerder='',$portefeuille='')
{
  global $__ORDERvar,$USR;
  $where=''; 
  $join='';
  if($vermogensbeheerder<>'')
    $where.="AND vermogensbeheerder='$vermogensbeheerder'";
  if($portefeuille<>'')
  {
    $join.="JOIN Portefeuilles ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder";
    $where.="AND Portefeuilles.portefeuille='$portefeuille'";    
  }
  if($vermogensbeheerder=='' && $portefeuille=='')
  {
    $join.="JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
    $where.="AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
  }
  $db=new DB();  
  $query="SELECT Vermogensbeheerders.order_controle, Vermogensbeheerders.vermogensbeheerder FROM Vermogensbeheerders $join WHERE 1 $where";
  $tmp=$db->lookupRecordByQuery($query);
  $tmp=unserialize($tmp['order_controle']);
  $newChecks=array();

  foreach($__ORDERvar['orderControles'] as $check=>$omschrijving)
  {
    if($tmp[$check]['checked']==1)
      $newChecks[$check]=$omschrijving;
  }
  
  if(count($newChecks)==0)
    $newChecks=$__ORDERvar['orderControles'];
        
  return $newChecks;
}                            

function Orderkosten($portefeuille,$fonds,$brutoBedrag,$valuta='')
{
  $db=new DB();
  
  $query="SELECT Vermogensbeheerder,InternDepot  FROM Portefeuilles WHERE Portefeuilles.Portefeuille='$portefeuille'";
  $db->SQL($query);
  $catData=$db->lookupRecord();
  $vermogensbeheerder=$catData['Vermogensbeheerder'];
  $internDepot=$catData['InternDepot'];
  
  $query="SELECT fondssoort FROM Fondsen WHERE Fonds='$fonds'";
  $db->SQL($query);
  $catData=$db->lookupRecord();
  $fondssoort=$catData['fondssoort'];

  
  $query="SELECT
orderkosten.kostenpercentage,
orderkosten.kostenminimumbedrag,
orderkosten.brokerkostenpercentage,
orderkosten.brokerkostenminimumbedrag
FROM
orderkosten
WHERE ((orderkosten.vermogensbeheerder='$vermogensbeheerder' AND orderkosten.Portefeuille='') OR orderkosten.Portefeuille='$portefeuille')
AND (orderkosten.fondssoort='$fondssoort')
ORDER BY orderkosten.Portefeuille desc LIMIT 1";
  $db->SQL($query);
  $kostenData=$db->lookupRecord();
  
  $kosten=$brutoBedrag*($kostenData['kostenpercentage']/100);
  if($kosten < $kostenData['kostenminimumbedrag'])
    $kosten=$kostenData['kostenminimumbedrag'];
  $brokerKosten=$brutoBedrag*($kostenData['brokerkostenpercentage']/100);
  if($brokerKosten < $kostenData['brokerkostenminimumbedrag'])
    $brokerKosten=$kostenData['brokerkostenminimumbedrag']; 
    
  if($internDepot==1)
  {
    $kosten=0;
    $brokerKosten=0;
  }  

  $tmp=array('kosten'=>round($kosten,2),'brokerKosten'=>round($brokerKosten,2));
  return $tmp;
}

function OrderkostenV2($orderdata,$brutoBedrag)
{
  $db = new DB();


  $query = "SELECT Vermogensbeheerder,InternDepot  FROM Portefeuilles WHERE Portefeuilles.Portefeuille='" . $orderdata['portefeuille'] . "'";
  $db->SQL($query);
  $catData = $db->lookupRecord();
  $vermogensbeheerder = $catData['Vermogensbeheerder'];
  $internDepot = $catData['InternDepot'];

  if ($orderdata['fondssoort'] == '')
  {
    $query="SELECT fondssoort,Beurzen.beursregio FROM Fondsen LEFT JOIN Beurzen ON Fondsen.beurs=Beurzen.beurs WHERE Fonds='".$orderdata['fonds']."'";
    $db->SQL($query);
    $fondsData=$db->lookupRecord();
    $orderdata['fondssoort'] = $fondsData['fondssoort'];
  }


   $transactievorm=substr($orderdata['transactieSoort'],1,1);
   $beursregio='';

  $orderBy=",orderkosten.fondssoort desc ";

  if(trim($transactievorm)=='')
    $orderBy.=",orderkosten.transactievorm asc ";
  else
    $orderBy.=",orderkosten.transactievorm desc ";

  if(trim($beursregio)=='')
    $orderBy.=",orderkosten.beursregio asc ";
  else
    $orderBy.=",orderkosten.beursregio desc ";

  $query="SELECT
orderkosten.kostenpercentage,
orderkosten.kostenminimumbedrag,
orderkosten.brokerkostenpercentage,
orderkosten.brokerkostenminimumbedrag,
orderkosten.prijsPerStuk,
orderkosten.transactievorm,
orderkosten.beursregio,
orderkosten.berekenwijze,
orderkosten.staffel1,
orderkosten.staffel2,
orderkosten.staffel3,
orderkosten.staffel4,
orderkosten.staffel5,
orderkosten.staffelPercentage1,
orderkosten.staffelPercentage2,
orderkosten.staffelPercentage3,
orderkosten.staffelPercentage4,
orderkosten.staffelPercentage5
FROM
orderkosten
WHERE 
(
 (orderkosten.vermogensbeheerder='".$vermogensbeheerder."' AND orderkosten.Portefeuille='' AND orderkosten.beursregio='".$orderdata['beursregio']."' AND orderkosten.valuta='".$orderdata['rekeningValuta']."') OR 
 (orderkosten.vermogensbeheerder='".$vermogensbeheerder."' AND orderkosten.Portefeuille='' AND orderkosten.beursregio='".$orderdata['beursregio']."' AND orderkosten.valuta='') OR 
 (orderkosten.vermogensbeheerder='".$vermogensbeheerder."' AND orderkosten.Portefeuille='' AND orderkosten.beursregio='' AND orderkosten.valuta='".$orderdata['rekeningValuta']."') OR 
 (orderkosten.vermogensbeheerder='".$vermogensbeheerder."' AND orderkosten.Portefeuille='' AND orderkosten.beursregio='' AND orderkosten.valuta='') OR 
 (orderkosten.Portefeuille='".$orderdata['portefeuille']."' AND orderkosten.beursregio='".$orderdata['beursregio']."' AND orderkosten.valuta='".$orderdata['rekeningValuta']."') OR
 (orderkosten.Portefeuille='".$orderdata['portefeuille']."' AND orderkosten.beursregio='".$orderdata['beursregio']."' AND orderkosten.valuta='') OR
 (orderkosten.Portefeuille='".$orderdata['portefeuille']."' AND orderkosten.beursregio='' AND orderkosten.valuta='".$orderdata['rekeningValuta']."') OR
 (orderkosten.Portefeuille='".$orderdata['portefeuille']."' AND orderkosten.beursregio='' AND orderkosten.valuta='') 
)
AND (orderkosten.fondssoort='".$orderdata['fondssoort']."' OR orderkosten.fondssoort='')
AND (orderkosten.transactievorm='".$transactievorm."' OR orderkosten.transactievorm='')
ORDER BY orderkosten.Portefeuille desc $orderBy ,orderkosten.valuta desc  LIMIT 1";

  $db->SQL($query);
  $kostenData=$db->lookupRecord();

  if($kostenData['berekenwijze']==0)
  {
    if ($kostenData['prijsPerStuk'] <> 0)
    {
      $kosten = abs($orderdata['aantal']) * ($kostenData['prijsPerStuk']);
    }
    else
    {
      $kosten = $brutoBedrag * ($kostenData['kostenpercentage'] / 100);
    }
  }
  elseif($kostenData['berekenwijze']==1)//staffel
  {
     for($i=1;$i<6;$i++)
     {
       if(isset($kostenData['staffel'.($i-1)]))
         $vorigeStaffel= $kostenData['staffel'.($i-1)];
       else
         $vorigeStaffel=0;
       if($brutoBedrag>=$kostenData['staffel'.$i] && $kostenData['staffel'.$i] >$vorigeStaffel)
       {
         $kosten = $brutoBedrag * ($kostenData['staffelPercentage'.$i] / 100);
         //echo "$kosten = $brutoBedrag * (".$kostenData['staffelPercentage'.$i]." / 100) $i <br>\n";
       }
     }
  }
  elseif($kostenData['berekenwijze']==2)//schijven
  {
     $kostenRest=$brutoBedrag;
     $kosten=0;
     for($i=1;$i<6;$i++)
     {
       if(isset($kostenData['staffel'.($i-1)]))
         $vorigeSchijf= $kostenData['staffel'.($i-1)];
       else
         $vorigeSchijf=0;
       $huidigeSchijfCapaciteit=$kostenData['staffel'.$i]-$vorigeSchijf;
       if($kostenRest>0)
       {
         if($kostenRest>=$huidigeSchijfCapaciteit)
         {
           //echo "$i kosten=".($huidigeSchijfCapaciteit * ($kostenData['staffelPercentage'.$i] / 100))."= $huidigeSchijfCapaciteit * (".$kostenData['staffelPercentage'.$i]." / 100); $kostenRest<br>\n";
           $kosten += $huidigeSchijfCapaciteit * ($kostenData['staffelPercentage'.$i] / 100);
           $kostenRest-=$huidigeSchijfCapaciteit;
         }
         else
         {
           //echo "$i kosten=".($kostenRest * ($kostenData['staffelPercentage'.$i] / 100))."= $kostenRest * (".$kostenData['staffelPercentage'.$i]." / 100); $kostenRest <br>\n";
           $kosten += $kostenRest * ($kostenData['staffelPercentage'.$i] / 100);
           $kostenRest=0;
         }
       }
     }
  }

  if($kosten < $kostenData['kostenminimumbedrag'])
    $kosten=$kostenData['kostenminimumbedrag'];

  $brokerKosten=$brutoBedrag*($kostenData['brokerkostenpercentage']/100);
  if($brokerKosten < $kostenData['brokerkostenminimumbedrag'])
    $brokerKosten=$kostenData['brokerkostenminimumbedrag'];

  if($internDepot==1)
  {
    $kosten=0;
    $brokerKosten=0;
  }

  $tmp=array('kosten'=>round($kosten,2),'brokerKosten'=>round($brokerKosten,2));
  return $tmp;
}

function updateBrutoWaarde($id,$uitvoering=false)
{
  global $USR;
  $db=new DB();
  $query="SELECT Orders.orderid,Orders.fonds,OrderRegels.aantal, OrderRegels.brutoBedrag,Orders.laatsteStatus,OrderRegels.portefeuille,opgelopenRente FROM OrderRegels Inner Join Orders ON OrderRegels.orderid = Orders.orderid where OrderRegels.id='$id'";
  $db->SQL($query);
  $orderRegel=$db->lookupRecord();
  
  $query="SELECT transactieSoort, laatsteStatus FROM Orders WHERE orderid='".$orderRegel['orderid']."'";
  $db->SQL($query);
  $order = $db->lookupRecord();

    
  $query="SELECT Fonds as fonds, Valuta, Fondseenheid FROM Fondsen WHERE fonds='".$orderRegel['fonds']."'";
  $db->SQL($query);
  $fonds = $db->lookupRecord();

  if($order['laatsteStatus'] < 1)
  {
    $query = "SELECT koers,Fonds,Datum FROM Fondskoersen WHERE Fonds = '".$fonds['fonds']."' ORDER BY Datum DESC LIMIT 1";
    $db->SQL($query);
    $fondsKoers = $db->lookupRecord();

    $query = "SELECT koers,Valuta,Datum FROM Valutakoersen WHERE Valuta = '".$fonds['Valuta']."' ORDER BY Datum DESC LIMIT 1";
    $db->SQL($query);
    $valutaKoers = $db->lookupRecord();
    $valutaKoers =$valutaKoers['koers'];
  
    $brutoBedragValuta=$orderRegel['aantal']*$fonds['Fondseenheid']*$fondsKoers['koers'];
    $brutoBedrag=$brutoBedragValuta*$valutaKoers;
  }
  
  if($uitvoering==true)
  {
    $query="SELECT uitvoeringsAantal,uitvoeringsDatum,uitvoeringsPrijs,nettokoers,opgelopenrente FROM OrderUitvoering WHERE orderid='".$orderRegel['orderid']."'";
    $db->SQL($query);
    $db->Query(); 
    $uitvoeringsAantal=0;
    while($data=$db->nextRecord())
      $uitvoeringen[]=$data;
  
    foreach($uitvoeringen as $data)
    {
      $query = "SELECT koers,Valuta FROM Valutakoersen WHERE Valuta = '".$fonds['Valuta']."' AND Datum<='".$data['uitvoeringsDatum']."' ORDER BY Datum DESC LIMIT 1";
      $db->SQL($query);
      $valutaKoers = $db->lookupRecord();
      $valutaKoers =$valutaKoers['koers'];
    
      $uitvoeringsAantal+=$data['uitvoeringsAantal'];
      $uitvoeringsPrijs+=$data['uitvoeringsAantal']*$data['uitvoeringsPrijs'];
      $uitvoeringsPrijsEur+=$data['uitvoeringsAantal']*$data['uitvoeringsPrijs']*$valutaKoers;
      $opgelopenrente+=$data['opgelopenrente'];
    
      if($data['nettokoers'] > 0)
        $nettokoers=true;
    }
    $uitvoeringsPrijs=$uitvoeringsPrijs/$uitvoeringsAantal;
    $uitvoeringsPrijsEur=$uitvoeringsPrijsEur/$uitvoeringsAantal;
    $valutaKoers=$uitvoeringsPrijsEur/$uitvoeringsPrijs;

    $rente=$opgelopenrente*($orderRegel['aantal']/$uitvoeringsAantal);
  
    if($uitvoeringsAantal == $orderRegel['aantal'] && $uitvoeringsAantal > 0)
    {
      $brutoBedragValuta=$uitvoeringsAantal*$fonds['Fondseenheid']*$uitvoeringsPrijs;
      $brutoBedrag=$brutoBedragValuta*$valutaKoers;
    }

    $Orderkosten=Orderkosten($orderRegel['portefeuille'],$orderRegel['fonds'],$brutoBedrag);
    $kosten=$Orderkosten['kosten'] + $Orderkosten['brokerkosten'];

    if($order['transactieSoort'] == 'A')
      $nettoBedrag = round((($brutoBedrag + $rente) * $valutaKoers)+$kosten,2);
    else
      $nettoBedrag = round((($brutoBedrag + $rente) * $valutaKoers)-$kosten,2);
    
    if($nettokoers)
      $Orderkosten['kosten']=0;
  }
  
  $query="UPDATE OrderRegels SET change_date=NOW(),change_user='$USR'";
  if( isset ($brutoBedrag) )
    $query.=",brutoBedrag='$brutoBedrag'";
  if( isset ($brutoBedragValuta) )
    $query.=",brutoBedragValuta='$brutoBedragValuta'";
  if( isset ($nettoBedrag) )
    $query.=",nettoBedrag='$nettoBedrag'";
  if( isset ($valutaKoers) )
    $query.=",valutakoers='$valutaKoers'";
  if( isset ($rente) )
    $query.=",opgelopenRente='$rente'";
  if( isset ($Orderkosten['kosten']) )
    $query.=",kosten='".$Orderkosten['kosten']."'";
  if( isset ($Orderkosten['brokerKosten']) )
    $query.=",brokerkosten='".$Orderkosten['brokerKosten']."' ";
  $query.=" WHERE id='$id'";

  $db->SQL($query);
  $db->Query();
 // echo $query;exit;
}

function updateBrutoWaardeV2($orderId,$orderregelId='',$notaValutakoers='',$settlementdatum='',$allOptions=array())
{
  global $USR;
  $melding='';
  $db=new DB();
  $db2=new DB();

  if($orderId=='' || $orderId<1)
  {
    echo "orderId missing";
    exit;
  }

  $query="SELECT OrdersV2.fonds,OrdersV2.orderStatus,OrdersV2.transactieSoort,OrdersV2.fondsValuta,
OrdersV2.fondseenheid,OrdersV2.fondssoort,OrdersV2.notaValutakoers,OrdersV2.settlementdatum,
 BbLandcodes.settlementDays
FROM OrdersV2 
LEFT JOIN Fondsen ON OrdersV2.fonds = Fondsen.Fonds
LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
WHERE OrdersV2.id='$orderId'";
  $db->SQL($query);
  $orderData=$db->lookupRecord();

  if($orderData['fondsValuta']=='' ||  $orderData['fondseenheid']=='')
  {
    $query = "SELECT Valuta, Fondseenheid FROM Fondsen WHERE fonds='" . $orderData['fonds'] . "'";
    $db->SQL($query);
    $fonds = $db->lookupRecord();
    if($orderData['fondsValuta']=='')
      $orderData['fondsValuta']=$fonds['Valuta'];
    if($orderData['fondseenheid']=='')
      $orderData['fondseenheid']=$fonds['Fondseenheid'];
  }

  $query = "SELECT koers,Fonds,Datum FROM Fondskoersen WHERE Fonds = '".$orderData['fonds']."' ORDER BY Datum DESC LIMIT 1";
  $db->SQL($query);
  $fondsKoers = $db->lookupRecord();

  $query = "SELECT koers,Valuta,Datum FROM Valutakoersen WHERE Valuta = '".$orderData['fondsValuta']."' ORDER BY Datum DESC LIMIT 1";
  $db->SQL($query);
  $valutaKoers = $db->lookupRecord();
  $fondsValutaKoers =$valutaKoers['koers'];


  // Uitvoeringen ophalen.
  $query="SELECT uitvoeringsAantal,uitvoeringsDatum,uitvoeringsPrijs,nettokoers,opgelopenrente,brokerkostenTotaal FROM OrderUitvoeringV2 WHERE orderid='".$orderId."' ORDER BY uitvoeringsDatum";
  $db->SQL($query);
  $db->Query();
  $uitvoeringsAantal=0;
  $uitvoeringsWaarde=0;
  $uitvoeringsPrijsEur=0;
  $opgelopenrente=0;
  $brokerkostenTotaal=0;
  while($data=$db->nextRecord())
    $uitvoeringen[]=$data;

  $nettokoers=false;
  foreach($uitvoeringen as $data)
  {
    $uitvoeringsAantal+=$data['uitvoeringsAantal'];
    $uitvoeringsWaarde+=$data['uitvoeringsAantal']*$data['uitvoeringsPrijs'];
    $opgelopenrente+=$data['opgelopenrente'];
    $uitvoeringsDatum=$data['uitvoeringsDatum'];
    $brokerkostenTotaal+=$data['brokerkostenTotaal'];


    if($data['nettokoers'] > 0)
      $nettokoers=true;
  }

  $query = "SELECT koers,Valuta FROM Valutakoersen WHERE Valuta = '".$orderData['fondsValuta']."' AND Datum<='".$uitvoeringsDatum."' ORDER BY Datum DESC LIMIT 1";
  $db->SQL($query);
  $valutaKoers = $db->lookupRecord();
  $fondsValutaKoers =$valutaKoers['koers'];
  if($notaValutakoers<>'' && $notaValutakoers<>0)
  {
    $fondsValutaKoers = $notaValutakoers;
  }
  $uitvoeringsPrijsEur=$uitvoeringsWaarde*$fondsValutaKoers;

  $uitvoeringsPrijs=$uitvoeringsWaarde/$uitvoeringsAantal;
  $uitvoeringsPrijsEur=$uitvoeringsPrijsEur/$uitvoeringsAantal;


  $query="SELECT SUM(OrderRegelsV2.aantal) as aantal , SUM(OrderRegelsV2.bedrag) as bedrag FROM OrderRegelsV2 WHERE OrderRegelsV2.orderid='$orderId'";
  $db->SQL($query);
  $db->query();
  $totaal=$db->NextRecord();

  $query="SELECT OrderRegelsV2.id ,OrderRegelsV2.orderid ,OrderRegelsV2.aantal,OrderRegelsV2.bedrag, OrderRegelsV2.brutoBedrag,
OrderRegelsV2.portefeuille,OrderRegelsV2.opgelopenRente,OrderRegelsV2.rekening,Rekeningen.Valuta as rekeningValuta
FROM OrderRegelsV2 
LEFT JOIN Rekeningen ON OrderRegelsV2.rekening = Rekeningen.Rekening AND consolidatie=0
WHERE OrderRegelsV2.orderid='$orderId' AND OrderRegelsV2.notaDefinitief=0";
  $db->SQL($query);
  $db->query();
  $aantalRecords=$db->records();
  while($orderRegel=$db->nextRecord())
  {
    
    if($orderRegel['rekeningValuta'] == $orderData['fondsValuta'])
      $fondsValutaKoers=1;
    elseif($notaValutakoers<>'' && $notaValutakoers<>0)
      $fondsValutaKoers = $notaValutakoers;
    else
      $fondsValutaKoers=$valutaKoers['koers'];

    $geenBerekening=false;
    if($orderData['orderStatus'] < 1)
    {
      if($totaal['aantal']==0 &&  $totaal['bedrag']<>0)//nominaal
      {
        $aandeelUitgevoerd = $orderRegel['bedrag'] / $totaal['bedrag'];
        $brutoBedraginFondsvaluta=$orderRegel['bedrag'];
      }
      else
      {
        $aandeelUitgevoerd = $orderRegel['aantal'] / $totaal['aantal'];
        $brutoBedraginFondsvaluta=$orderRegel['aantal']*$orderData['fondseenheid']*$fondsKoers['koers'];
      }
      
      if($orderRegel['rekeningValuta'] == $orderData['fondsValuta'])
        $brutoBedrag=$brutoBedraginFondsvaluta*$aandeelUitgevoerd;
      elseif($orderRegel['rekeningValuta']=='EUR' || $orderData['rekeningValuta']=='')
        $brutoBedrag=$brutoBedraginFondsvaluta*$aandeelUitgevoerd;//*$fondsValutaKoers
      else
      {
        $brutoBedrag=0;
        $nettoBedrag=0;
        $melding[$orderRegel['id']]="Fondsvaluta en rekeningvaulta ongelijk. Automatische berekening overgeslagen.";
        $geenBerekening=true;
      }
    }

    if($uitvoeringsAantal > 0)
    {
      if($totaal['aantal']==0 && $totaal['bedrag']<>0)//nominaal
      {
        $aandeelUitgevoerd = $orderRegel['bedrag'] / $totaal['bedrag'];
      }
      else
      {
        $aandeelUitgevoerd = $orderRegel['aantal'] / $uitvoeringsAantal;
      }
      $rente = $opgelopenrente * $aandeelUitgevoerd;
      $brokerkostenDeel = $brokerkostenTotaal * $aandeelUitgevoerd;
      $brutoBedraginFondsvaluta = $uitvoeringsAantal * $orderData['fondseenheid'] * $uitvoeringsPrijs * $aandeelUitgevoerd;
  
      if($orderRegel['rekeningValuta'] == $orderData['fondsValuta'])
      {
        $brutoBedrag = $brutoBedraginFondsvaluta;
        $melding='';
      }
      elseif($orderRegel['rekeningValuta']=='EUR' )//|| $orderRegel['rekeningValuta']=='')
      {
        $brutoBedrag = $brutoBedraginFondsvaluta;// * $fondsValutaKoers;
        $melding = '';
      }
      else
      {
        $geenBerekening=true;
        $melding[$orderRegel['id']]="Fondsvaluta en rekeningvaluta ongelijk. Automatische berekening overgeslagen.";

      }
    }
    $brutoBedrag=round($brutoBedrag,2);
   // echo $orderRegel['rekeningValuta']." == ".$orderRegel['fondsValuta']."<br>\n";
    //listarray($melding[$orderregelId]);exit;

    if($nettokoers)
    {
      $kosten=0;
      $orderkosten=array();
    }
    else
    {
      $orderkosten = OrderkostenV2(array_merge($orderData, $orderRegel), $brutoBedrag);
      $orderkosten['brokerKosten'] +=$brokerkostenDeel;
      $kosten = $orderkosten['kosten'] + $orderkosten['brokerKosten'];
    }
    if(substr($orderData['transactieSoort'],0,1) == 'A')
      $nettoBedrag = round((($brutoBedraginFondsvaluta + $rente) * $fondsValutaKoers)+$kosten,2);
    else
      $nettoBedrag = round((($brutoBedraginFondsvaluta + $rente) * $fondsValutaKoers)-$kosten,2);

    if($geenBerekening==true)
    {
      $brutoBedrag=0;
      $nettoBedrag=0;
    }


  $query="UPDATE OrderRegelsV2 SET change_date=NOW(),change_user='$USR'";
  if( isset ($brutoBedrag) )
    $query.=",brutoBedrag='$brutoBedrag'";
  if( isset ($nettoBedrag) )
    $query.=",nettoBedrag='$nettoBedrag'";
  if( isset ($rente) )
    $query.=",opgelopenRente='$rente'";
  if( isset ($orderkosten['kosten']) )
    $query.=",kosten='".$orderkosten['kosten']."'";
  if( isset ($allOptions['voorkeursOrderReden']))
    $query.=",orderReden='".mysql_real_escape_string($allOptions['voorkeursOrderReden'])."'";
  if( isset ($allOptions['voorkeursPSET']))
    $query.=",PSET='".mysql_real_escape_string($allOptions['voorkeursPSET'])."'";
  if( isset ($allOptions['voorkeursPSAF']))
    $query.=",PSAF='".mysql_real_escape_string($allOptions['voorkeursPSAF'])."'";
  if( isset ($fondsValutaKoers) )
    $query.=",regelNotaValutakoers='$fondsValutaKoers'";
  if( isset ($orderkosten['brokerKosten']) )
    $query.=",brokerkosten='".$orderkosten['brokerKosten']."' ";
  $query.=" WHERE id='".$orderRegel['id']."'";

  $db2->SQL($query);
  $db2->Query();
  // echo $query."<br>\n";
  }

  if($settlementdatum=='' || $settlementdatum=='0000-00-00' || $settlementdatum=='1970-01-01')
  {
    $baseDays = 2;
    if ($orderData['settlementDays'] > 0)
    {
      $baseDays = $orderData['settlementDays'];
    }
    $uitvoeringsJul = db2jul($uitvoeringsDatum);
    $dagvanweek = date('N', $uitvoeringsJul);
    if ($dagvanweek <= (5 - $baseDays) && $dagvanweek < 6)
    {
      $extraDagen = 0;
    }
    elseif ($dagvanweek <= (10 - $baseDays) && $dagvanweek < 6)
    {
      $extraDagen = 2;
    }
    else
    {
      $extraDagen = 4;
    }
    $newJul = $uitvoeringsJul + (($baseDays + $extraDagen) * 86400) + 3605;
  }
  else
  {
    $newJul=db2jul($settlementdatum);
  }

  $settleDatum = date('d-m-Y', $newJul);
  $query="UPDATE OrdersV2 SET change_date=NOW(),change_user='$USR',notaValutakoers='$notaValutakoers',settlementdatum='".date('Y-m-d',$newJul)."' WHERE id='".$orderId."'";
  $db->SQL($query);
  $db->Query();

  $orderLogs = new orderLogs();
  if($aantalRecords > 1)
    $orderLogs->addToLog($orderId, null, $aantalRecords." nota's herrekend.");
  else
    $orderLogs->addToLog($orderId, null, "Nota herrekening.");

  $query="SELECT brutoBedrag,nettoBedrag,opgelopenRente,kosten,brokerkosten,regelNotaValutakoers FROM OrderRegelsV2 WHERE id='$orderregelId' ";
  $db->SQL($query);
  $db->Query();
  $regelsNew=$db->NextRecord();
  $regelsNew['settlementdatum']=$settleDatum;
  if(isset($melding[$orderregelId]))
    $regelsNew['notaMelding']=$melding[$orderregelId];
  elseif($aantalRecords==1)
    $regelsNew['notaMelding']="Nota herrekend.";
  else
    $regelsNew['notaMelding']=$aantalRecords." nota's herrekend.";

  $regelsNew['notaFondskoers']=$fondsKoers['koers'];

  return $regelsNew;


}

?>