<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/09/18 14:51:00 $
    File Versie         : $Revision: 1.20 $

    $Log: orderExportAFM.php,v $
    Revision 1.20  2019/09/18 14:51:00  rvv
    *** empty log message ***

    Revision 1.19  2019/08/10 17:26:01  rvv
    *** empty log message ***

    Revision 1.18  2019/06/26 15:08:52  rvv
    *** empty log message ***

    Revision 1.17  2019/06/22 16:30:27  rvv
    *** empty log message ***

    Revision 1.16  2019/03/23 07:27:34  rvv
    *** empty log message ***

    Revision 1.15  2018/05/12 15:38:38  rvv
    *** empty log message ***

    Revision 1.14  2018/03/15 06:54:33  rvv
    *** empty log message ***

    Revision 1.13  2018/03/14 17:14:53  rvv
    *** empty log message ***

    Revision 1.12  2018/01/31 17:20:04  rvv
    *** empty log message ***

    Revision 1.11  2018/01/13 18:56:16  rvv
    *** empty log message ***

    Revision 1.10  2018/01/10 16:24:43  rvv
    *** empty log message ***

    Revision 1.9  2018/01/06 18:18:38  rvv
    *** empty log message ***

    Revision 1.8  2018/01/03 14:19:00  rvv
    *** empty log message ***

    Revision 1.7  2017/12/23 18:13:40  rvv
    *** empty log message ***

    Revision 1.6  2017/12/21 08:25:48  rvv
    *** empty log message ***

    Revision 1.5  2017/12/21 06:56:49  rvv
    *** empty log message ***

    Revision 1.4  2017/12/20 16:59:57  rvv
    *** empty log message ***

    Revision 1.3  2017/12/16 18:42:38  rvv
    *** empty log message ***

    Revision 1.2  2017/12/13 16:43:38  rvv
    *** empty log message ***

    Revision 1.1  2017/12/02 19:12:00  rvv
    *** empty log message ***


*/    
//$disable_auth = true;
//$_POST['id_6690']=1;
include_once("wwwvars.php");

$header=array('ARM APA Indicator','Side','Action','Transaction Reference Number','Trading Venue Transaction ID','Executing Entity ID','Investment Firm Director Indicator','Submitting Entity ID','Transmission Of Order Indicator','Transmitting Firm ID For The Buyer','Transmitting Firm ID For The Seller','Trading Date Time','Trading Capacity 1','Trading Capacity 2','Quantity','Quantity Currency','Derivative Notional Increase Decrease','Price','Price Notation','Price Currency','Net Amount','Venue','Country Branch Membership','Up-Front Payment','Up-Front Payment Currency','ComplexTradeComponentID','ByPassControlFlag','DeferralIndicator','FreeText1','FreeText2','FreeText3','FreeText4','FreeText5','Security ID','Full Name','Classification Type','Notional Currency 1','Notional Currency 2','Price Multiplier','Underlying Security ID','Underlying Index Name','Underlying Instrument Term','Option Type','Strike Price','Strike Price Notation','Strike Price Currency','Option Exercise Style','Maturity Date','Expiry Date','Delivery Type','Investment Decision Within Firm Type','Investment Decision Within Firm','Country Of Investor','Execution Within Firm Type','Execution Within Firm','Country Of Executor','Waiverindicator','ShortSellingIndicator','OTCPostTradeIndicator','CommodityDerivativeIndicator','SecuritiesFinancingTransactionIndicator','Buyer Identification Type','Buyer Identification Code','Buyer Country Branch','Buyer First Name','Buyer Surname','Buyer BirthDate','Buyer Identification Type2','Buyer Identification Code2','Buyer Country Branch2','Buyer First Name2','Buyer Surname2','Buyer BirthDate2','Seller Identification Type','Seller Identification Code','Seller Country Branch','Seller First Name','Seller Surname','Seller BirthDate','Seller Identification Type2','Seller Identification Code2','Seller Country Branch2','Seller First Name2','Seller Surname2','Seller BirthDate2','Buyer Decision Maker Type','Buyer Decision Maker Code','Buyer Decision Maker First Name','Buyer Decision Maker Surname','Buyer Decision Maker BirthDate','Buyer Decision Maker Type2','Buyer Decision Maker Code2','Buyer Decision Maker First Name2','Buyer Decision Maker Surname2','Buyer Decision Maker BirthDate2','Seller Decision Maker Type','Seller Decision Maker Code','Seller Decision Maker First Name','Seller Decision Maker Surname','Seller Decision Maker BirthDate','Seller Decision Maker Type2','Seller Decision Maker Code2','Seller Decision Maker First Name2','Seller Decision Maker Surname2','Seller Decision Maker BirthDate2','Business Unit','Settlement Flag','Client ID','Clearing Firm ID','Guarantee Flag','Settlement Period','Account Number',
'Buyer National ID Type','Buyer National ID Type2','Seller National ID Type','Seller National ID Type2','Buyer Decision Maker National ID Type',
'Buyer Decision Maker National ID Type2','Seller Decision Maker National ID Type','Seller Decision Maker National ID Type2','Investment Decision Within Firm National ID Type','Execution Within Firm National ID Type','Quantity Notation');

for ($i=0;$i<123;$i++)
  $leeg[$i]='';

$body=array();
$body[]=$header;

$versie=GetModuleAccess("ORDER");
if($_GET['versie']==1)
  $versie=1;

if($versie==1)
{
  $db=new DB();
  if (strpos($_SESSION['lastListQuery'], 'Orders.id as id') > 0)
  {
    if (strpos($_SESSION['lastListQuery'], 'enkeleOrderRegels') > 0)
    {
      $query = "CREATE TEMPORARY TABLE enkeleOrderRegels
        SELECT OrderRegels.*
        FROM Orders INNER JOIN OrderRegels ON Orders.orderid = OrderRegels.orderid 
        WHERE Orders.OrderSoort <> 'M'
        GROUP BY Orders.orderid  ";
      $db->SQL($query);
      $db->Query();
      $query = "ALTER TABLE enkeleOrderRegels ADD INDEX( orderid ); ";
      $db->SQL($query);
      $db->Query();
    }
    $tmp = explode("LIMIT", $_SESSION['lastListQuery']);
    $db->SQL($tmp[0]);
    $db->Query();
    $ids=array();
    while ($data = $db->nextRecord())
    {
      $ids[] = $data['id'];
    }
    $extraWhere = " AND Orders.id IN('" . implode("','", $ids) . "')";
  }
  $ids = array();
  foreach ($_POST as $key => $value)
  {
    if (substr($key, 0, 3) == 'id_')
    {
      $ids[] = substr($key, 3);
    }
  }
  if (count($ids) > 0)
  {
    $extraWhere .= " AND Orders.id IN('" . implode("','", $ids) . "')";
  }

  $query="SELECT Orders.orderSoort,
  OrderRegels.aantal as regelAantal,
  (SELECT sum(OrderRegels.aantal) FROM OrderRegels WHERE OrderRegels.orderid=Orders.orderid) AS orderAantal,
  if(SUBSTR(Orders.transactieSoort,1,1)='A',1,2) as side,
  Orders.id as orderid,
  OrderRegels.id as orderregelId,
  OrderRegels.positie as orderregelPositie,
  Depotbanken.LEInrDepBank,
  (SELECT OrderUitvoering.uitvoeringsDatum FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as uitvoeringsDatum,
  (SELECT OrderUitvoering.uitvoeringsAantal FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as uitvoeringsAantal,
  (SELECT SUM(OrderUitvoering.uitvoeringsPrijs*OrderUitvoering.uitvoeringsAantal)/SUM(OrderUitvoering.uitvoeringsAantal) FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as uitvoeringsPrijs,
  Fondsen.valuta as fondsValuta,
  if(Fondsen.optieType<>'',if(SUBSTR(Orders.transactieSoort,2,1)='O',1,2),'') AS DNID,
Fondsen.fondssoort,
Fondsen.fondseenheid,
Fondsen.fonds,
Fondsen.ISINCode,
Fondsen.Omschrijving as fondsOmschrijving,
Fondsen.optieUitoefenprijs,
Fondsen.optieExpDatum,
Fondsen.Lossingsdatum,
OrderRegels.portefeuille,
Vermogensbeheerders.LEInrVBH,
Vermogensbeheerders.orderTransRepDecisionMaker,
DecisionMakerVM.voornamen as DecisionMakerVmVoornamen,
DecisionMakerVM.achternaam as DecisionMakerVmAchternaam,
DecisionMakerVM.paspoortNummer AS DecisionMakerVmpaspoortNummer
FROM
  Orders
INNER JOIN OrderRegels ON Orders.orderid = OrderRegels.orderid
LEFT JOIN Fondsen ON Orders.fonds = Fondsen.Fonds
INNER JOIN Portefeuilles ON OrderRegels.portefeuille = Portefeuilles.Portefeuille
INNER JOIN Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
INNER JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
LEFT JOIN Gebruikers as DecisionMakerVM ON Vermogensbeheerders.orderTransRepDecisionMaker = DecisionMakerVM.Gebruiker
WHERE 1 $extraWhere
";
}
else
{
  if (strpos($_SESSION['lastListQuery'], 'OrdersV2.id as id') > 0)
  {
    if (strpos($_SESSION['lastListQuery'], 'enkeleOrderRegels') > 0)
    {
      $query = "CREATE TEMPORARY TABLE enkeleOrderRegels
        SELECT OrderRegelsV2.*
        FROM OrdersV2 INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid 
        WHERE OrdersV2.OrderSoort <> 'M'
        GROUP BY OrdersV2.id  ";
      $db->SQL($query);
      $db->Query();
      $query = "ALTER TABLE enkeleOrderRegels ADD INDEX( orderid ); ";
      $db->SQL($query);
      $db->Query();
    }
    $tmp = explode("LIMIT", $_SESSION['lastListQuery']);
    $ids = array();
    $db->SQL($tmp[0]);
    $db->Query();
    while ($data = $db->nextRecord())
    {
      $ids[] = $data['id'];
    }
    $extraWhere = " AND OrdersV2.id IN('" . implode("','", $ids) . "')";
  }
  $ids = array();
  foreach ($_POST as $key => $value)
  {
    if (substr($key, 0, 3) == 'id_')
    {
      $ids[] = substr($key, 3);
    }
  }
  if (count($ids) > 0)
  {
    $extraWhere .= " AND OrdersV2.id IN('" . implode("','", $ids) . "')";
  }
  
  $query = "SELECT OrdersV2.orderSoort,
  OrderRegelsV2.aantal as regelAantal,
  (SELECT sum(OrderRegelsV2.aantal) FROM OrderRegelsV2 WHERE OrderRegelsV2.orderid=OrdersV2.id) AS orderAantal,
  if(SUBSTR(OrdersV2.transactieSoort,1,1)='A',1,2) as side,
  OrdersV2.id as orderid,
  OrderRegelsV2.id as orderregelId,
  OrderRegelsV2.positie as orderregelPositie,
  Depotbanken.LEInrDepBank,
  ISOLanden.landCodeKort  AS depotLandcodeKort,
  (SELECT OrderUitvoeringV2.uitvoeringsDatum FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as uitvoeringsDatum,
  round((SELECT SUM(OrderUitvoeringV2.uitvoeringsAantal) FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid),6) as uitvoeringsAantal,
  round((SELECT SUM(OrderUitvoeringV2.opgelopenrente) FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid),6) as opgelopenrente,
  round((SELECT SUM(OrderUitvoeringV2.uitvoeringsPrijs*OrderUitvoeringV2.uitvoeringsAantal)/SUM(OrderUitvoeringV2.uitvoeringsAantal) FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1),8) as uitvoeringsPrijs,
  OrdersV2.fondsValuta,
  if(OrdersV2.optieType<>'',if(SUBSTR(OrdersV2.transactieSoort,2,1)='O',1,2),'') AS DNID,
OrdersV2.fondssoort,
OrdersV2.fondseenheid,
OrdersV2.fonds,
OrdersV2.ISINCode,
OrdersV2.fondsOmschrijving,
OrdersV2.optieUitoefenprijs,
OrdersV2.optieExpDatum,
Rekeningen.depotbank as RekeningDepot,
Fondsen.Lossingsdatum,
OrderRegelsV2.portefeuille,
Vermogensbeheerders.LEInrVBH,
Vermogensbeheerders.orderTransRepDecisionMaker,
Vermogensbeheerders.jaarafsluitingPerBewaarder,
Rekeningen.Depotbank,
DecisionMakerVM.voornamen as DecisionMakerVmVoornamen,
DecisionMakerVM.achternaam as DecisionMakerVmAchternaam,
DecisionMakerVM.paspoortNummer AS DecisionMakerVmpaspoortNummer
FROM
  OrdersV2
INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid
LEFT JOIN Fondsen ON OrdersV2.fonds = Fondsen.Fonds
INNER JOIN Portefeuilles ON OrderRegelsV2.portefeuille = Portefeuilles.Portefeuille
INNER JOIN Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
LEFT JOIN ISOLanden ON Depotbanken.landCode = ISOLanden.landCode
LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
LEFT JOIN Rekeningen ON OrderRegelsV2.rekening = Rekeningen.rekening AND Rekeningen.consolidatie=0
INNER JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
LEFT JOIN Gebruikers as DecisionMakerVM ON Vermogensbeheerders.orderTransRepDecisionMaker = DecisionMakerVM.Gebruiker
WHERE 1 $extraWhere AND OrdersV2.orderStatus < 5";
}

$db=new DB();
$db->SQL($query);
$db->Query();
$transactievertaling=array('A'=>'S','V'=>'P');
$orders=array();
while($data=$db->nextRecord())
{
  if($data['orderSoort']=='M')
  {
    $uitvoeringsAandeel=$data['regelAantal']/$data['orderAantal'];
    $data['uitvoeringsAantal'] = $uitvoeringsAandeel* $data['uitvoeringsAantal'];
    $data['opgelopenrente'] = $uitvoeringsAandeel* $data['opgelopenrente'];
  }
  $orders[]=$data;

}

$now=time();
global $__appvar;
$paspoortVelden=array('DecisionMakerVmpaspoortNummer','nummerID','part_nummerID');
foreach($orders as $orderData)
{
  $optieBovenliggendFonds=array();
  if($orderData['fondssoort']=='OPT')
  {
    $query = "SELECT fondssoort,fondseenheid,fonds,ISINCode,Omschrijving,Valuta FROM Fondsen WHERE fonds IN (SELECT optieBovenliggendFonds FROM Fondsen WHERE fonds='" . mysql_real_escape_string($orderData['fonds']) . "')";
    $db->SQL($query);
    $db->Query();
    $optieBovenliggendFonds = $db->nextRecord();
  }

  $query = "SELECT  Depotbanken.LEInrDepBank as LEInrDepBankRekening,
Depotbanken.landCode AS depotLandcodeLang,
ISOLanden.landCodeKort  AS depobankRekeningLandcodeKort
FROM
Depotbanken
LEFT JOIN ISOLanden ON Depotbanken.landCode = ISOLanden.landCode
WHERE Depotbank='".mysql_real_escape_string($orderData['RekeningDepot'])."'";
  $db->SQL($query);
  $db->Query();
  $rekeningDepot = $db->nextRecord();
  $orderData['LEInrDepBankRekening']=$rekeningDepot['LEInrDepBankRekening'];
  $orderData['depobankRekeningLandcodeKort']=$rekeningDepot['depobankRekeningLandcodeKort'];

  $query = "SELECT * FROM FondsExtraInformatie WHERE fonds='".mysql_real_escape_string($orderData['fonds'])."'";
  $db->SQL($query);
  $db->Query();
  $FondsExtraInformatie = $db->nextRecord();


  $query = "SELECT
CRM_naw.naam,
CRM_naw.titel,
CRM_naw.voorvoegsel,
CRM_naw.voorletters,
CRM_naw.voornamen,
CRM_naw.achternaam,
CRM_naw.tussenvoegsel,
CRM_naw.geboortedatum,
CRM_naw.legitimatie,
CRM_naw.nummerID,
CRM_naw.landID,
CRM_naw.part_naam,
CRM_naw.part_titel,
CRM_naw.part_voorvoegsel,
CRM_naw.part_voorletters,
CRM_naw.part_voornamen,
CRM_naw.part_tussenvoegsel,
CRM_naw.part_achternaam,
CRM_naw.part_nationaliteit,
CRM_naw.part_nummerID,
CRM_naw.part_landID,
CRM_naw.part_geboortedatum,
CRM_naw.ondernemingsvorm,
CRM_naw.LEInr,
CRM_naw.land
FROM CRM_naw WHERE portefeuille='" . mysql_real_escape_string($orderData['portefeuille']) . "'";

  $db->SQL($query);
  $db->Query();
  $crmData= $db->nextRecord();


  foreach($paspoortVelden as $paspoortveld)
  {
    if(isset($orderData[$paspoortveld]))
    {
      $orderData[$paspoortveld] = trim($orderData[$paspoortveld]);
      $aantalNullen = 20 - strlen('NL' . $orderData[$paspoortveld]);
      $orderData[$paspoortveld] = 'NL' . str_repeat('0', $aantalNullen) . $orderData[$paspoortveld];
    }
    elseif(isset($crmData[$paspoortveld]))
    {
      $crmData[$paspoortveld] = trim($crmData[$paspoortveld]);
      $aantalNullen = 20 - strlen('NL' . $crmData[$paspoortveld]);
      $crmData[$paspoortveld] = 'NL' . str_repeat('0', $aantalNullen) . $crmData[$paspoortveld];
    }
  }

  $regel=$leeg;
  $regel[0] = 1;
  $regel[1] = $orderData['side'];
  $regel[2] = 1;
  $regel[3] = $__appvar["bedrijf"]."".$orderData['orderid']."".$orderData['orderregelPositie']."".$now;
  $regel[5] = $orderData['LEInrVBH'];
  $regel[6] = 0;
  $regel[7] = $orderData['LEInrVBH'];
  $regel[8] = 0;
  $regel[11] = $orderData['uitvoeringsDatum'].".000000";
  $regel[12] = 3;
  $regel[13] = 3;
  $regel[14] = $orderData['uitvoeringsAantal'];
  $regel[15] = $orderData['fondsValuta'];
  if($orderData['optieType']<>'')
    $regel[16] = $orderData['DNID'];
  $regel[17] = $orderData['uitvoeringsPrijs'];
  if(($orderData['fondssoort']=='OBL'||$orderData['fondssoort']=='OVERIG') && $orderData['fondseenheid']==0.01)
    $regel[18] = 2;
  else
    $regel[18] = 1;
  
  if($orderData['fondseenheid']==0.01)
    $regel[19] = '';
  else
    $regel[19] = $orderData['fondsValuta'];
  
  $regel[20] = round($orderData['uitvoeringsAantal'] *$orderData['uitvoeringsPrijs']*$orderData['fondseenheid'],2)+round($orderData['opgelopenrente'],2);
  $regel[21] = 'XOFF';
  $regel[33] = $orderData['ISINCode'];
  $regel[34] = $orderData['fondsOmschrijving'];

  if($FondsExtraInformatie['CFIcode'] <> '')
    $regel[35] = $FondsExtraInformatie['CFIcode'];
  else
    $regel[35] = 'XXXXXX';

  $regel[36] = $orderData['fondsValuta'];
  $regel[37] = '';// $orderData['fondsValuta']; "Notional Currency 2" leeg.
  $regel[38] = $orderData['fondseenheid'];
  if($orderData['fondssoort']=='OPT')
  {
    $regel[39] = $optieBovenliggendFonds['ISINCode'];
    if($optieBovenliggendFonds['fondssoort']=='INDEX')
      $regel[40] = $optieBovenliggendFonds['Omschrijving'];

    if($orderData['optieType']=='C')
      $regel[42] = 'CALL';
    else
      $regel[42] = 'PTUO';

    $regel[43] = $orderData['optieUitoefenprijs'];
    $regel[44] = 1;
    $regel[45] = $optieBovenliggendFonds['Valuta'];
    $regel[46] = 'Am/Eur';
  }
  if($orderData['fondssoort']=='OBL'||$orderData['fondssoort']=='OVERIG')
  {
     if($orderData['Lossingsdatum']<>'0000-00-00' && $orderData['Lossingsdatum']<>'')
     {
       $regel[47] = $orderData['Lossingsdatum'];
     }
  }

  if($orderData['fondssoort']=='OPT')
  {
    if($orderData['optieExpDatum']<>'0000-00-00' && $orderData['optieExpDatum']<>'')
    {
      $regel[48] = $orderData['optieExpDatum'];
    }
    if($optieBovenliggendFonds['fondssoort']=='INDEX')
      $regel[49] = 'CASH';
    else
      $regel[49] = 'OPTN';
  }
  $regel[50] = 1;

  $regel[51] = $orderData['DecisionMakerVmpaspoortNummer'];
  $regel[52] = 'NL';
  $regel[53] = 1;
  $regel[54] = $orderData['DecisionMakerVmpaspoortNummer'];//$orderData['LEInrVBH'];
  $regel[55] = 'NL';
  if($orderData['side']==2)
    $regel[57] = '';//'UNDI';
  $regel[58] = '';//PostTrade
  $regel[60] = 0;
  if($orderData['side']==1)//buy
  {
    if($crmData['ondernemingsvorm'] <> '')
    {
      $regel[61] = 1;
      $regel[62] = $crmData['LEInr'] ;
      $regel[63] = 'NL';//$crmData['land'] ;
    }
    else
    {
      $regel[61] = 3;
      $regel[62] = $crmData['nummerID'];
      $regel[63] = 'NL';//$crmData['land'] ;
      $regel[64] = strtoupper(str_replace(' ',',',$crmData['voornamen']));
      $regel[65] = strtoupper(trim($crmData['tussenvoegsel'].' '.$crmData['achternaam']));
      $regel[66] = $crmData['geboortedatum'];

      if($crmData['part_achternaam']<>'')
      {
        $regel[67] = 3;
        $regel[68] = $crmData['part_nummerID'];
        $regel[69] = 'NL';//$crmData['land'];
        $regel[70] = strtoupper(str_replace(' ',',',$crmData['part_voornamen']));
        $regel[71] = strtoupper(trim($crmData['part_tussenvoegsel'].' '.$crmData['part_achternaam']));
        $regel[72] = $crmData['part_geboortedatum'];
      }
    }
    $regel[73] = 1;
    if($orderData['jaarafsluitingPerBewaarder'] == 1 && $orderData['LEInrDepBankRekening']<>'')
    {
      $regel[74] = $orderData['LEInrDepBankRekening'];
      $regel[75] = $orderData['depobankRekeningLandcodeKort'];
    }
    else
    {
      $regel[74] = $orderData['LEInrDepBank'];
      $regel[75] = $orderData['depotLandcodeKort'];
    }

    $regel[85] = 1;
    $regel[86] = $orderData['LEInrVBH'];


  }
  else
  {
    $regel[61] = 1;
    if($orderData['jaarafsluitingPerBewaarder'] == 1 && $orderData['LEInrDepBankRekening']<>'')
    {
      $regel[62] = $orderData['LEInrDepBankRekening'];
      $regel[63] = $orderData['depobankRekeningLandcodeKort'];
    }
    else
    {
      $regel[62] = $orderData['LEInrDepBank'];
      $regel[63] = $orderData['depotLandcodeKort'];
    }
    
    if($crmData['ondernemingsvorm'] <> '')
    {
      $regel[73] = 1;
      $regel[74] = $crmData['LEInr'] ;
      $regel[75] = 'NL';//$crmData['land'] ;
    }
    else
    {
      $regel[73] = 3;
      $regel[74] = $crmData['nummerID'];
      $regel[75] = 'NL';//$crmData['land'] ;
      $regel[76] = strtoupper(str_replace(' ',',',$crmData['voornamen']));
      $regel[77] = strtoupper(trim($crmData['tussenvoegsel'].' '.$crmData['achternaam']));
      $regel[78] = $crmData['geboortedatum'];

      if($crmData['part_achternaam']<>'')
      {
        $regel[79] = 3;
        $regel[80] = $crmData['part_nummerID'];
        $regel[81] = 'NL';//$crmData['land'];
        $regel[82] = strtoupper(str_replace(' ',',',$crmData['part_voornamen']));
        $regel[83] = strtoupper(trim($crmData['part_tussenvoegsel'].' '.$crmData['part_achternaam']));
        $regel[84] = $crmData['part_geboortedatum'];
      }
    }

    $regel[95] = 1;
    $regel[96] = $orderData['LEInrVBH'];

  }

  /*
'Buyer National ID Type'  (gekoppeld veld Kolom 61)
'Buyer National ID Type2' (kolom 67)
'Seller National ID Type' (kolom 73)
'Seller National ID Type2' (kolom 79)
'Buyer Decision Maker National ID Type' (kolom 85)
'Buyer Decision Maker National ID Type2' (kolom 90)
'Seller Decision Maker National ID Type' (kolom 95)
'Seller Decision Maker National ID Type2' (kolom 100)
  [112] => Buyer National ID Type
    [113] => Buyer National ID Type2
    [114] => Seller National ID Type
    [115] => Seller National ID Type2
    [116] => Buyer Decision Maker National ID Type
    [117] => Buyer Decision Maker National ID Type2
    [118] => Seller Decision Maker National ID Type
    [119] => Seller Decision Maker National ID Type2
*/
  $checks=array(112=>61, 113=>67, 114=>73, 115=>79, 116=>85 , 117=>90 , 118=>95 , 119=>100 );
  foreach($checks as $target=>$source)
  {
    if($regel[$source] <> '')
    {
      if($regel[$source]==1)
        $regel[$target]=1;
      elseif($regel[$source]==3)
        $regel[$target]=2;
    }
  }
  $regel[120] = 2;
  $regel[121] = 2;

  if($regel[18]==2)
    $regel[122] = 3;
  else
    $regel[122] = 1;

 $body[]=$regel;
}

$outputStr='';
foreach($body as $line)
  $outputStr.=implode(';',$line)."\n";

$filename='orderReport.csv';
header('Content-type: ' . "text/comma-separated-values");
header("Content-Length: ".strlen($outputStr));
header("Content-Disposition: inline; filename=\"".$filename."\"");
header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
echo $outputStr;


?>
