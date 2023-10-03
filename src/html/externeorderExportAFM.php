<?php
//$disable_auth = true;
//$_POST['id_6690']=1;
include_once("wwwvars.php");


function regelsToevoegen($body,$extraWhere,$totaal)
{
  
  $leeg=array();
  for ($i=0;$i<123;$i++)
    $leeg[$i]='';
  
  if($totaal==true)
  {
    $select='abs(SUM(Rekeningmutaties.aantal)) as uitvoeringsAantal,
    sum(Rekeningmutaties.Bedrag) as RekeningmutatiesBedrag,
    if(SUBSTR(externeOrders.transactieCode,1,1)=\'A\',1,2) as side,';
    $group='GROUP BY externeOrders.id';
  }
  else
  {
    $select='abs(Rekeningmutaties.aantal) as uitvoeringsAantal,
    Rekeningmutaties.Bedrag as RekeningmutatiesBedrag,
    if(SUBSTR(Rekeningmutaties.transactieType,1,1)=\'A\',1,2) as side,';
    $group='';
  }
  
  $query = "SELECT 'E' as orderSoort,
  abs(externeOrders.aantal) as regelAantal,
  abs(externeOrders.aantal) as orderAantal,
  externeOrders.externOrderId as orderid,
  Rekeningmutaties.id as orderregelId,
  externeOrders.id as orderregelPositie,
  Depotbanken.LEInrDepBank,
  ISOLanden.landCodeKort  AS depotLandcodeKort,
  externeOrders.datum as uitvoeringsDatum,
  0 as opgelopenrente,
  externeOrders.uitvoeringskoers as uitvoeringsPrijs,
  externeOrders.valuta as fondsValuta,
  externeOrders.executor as externeOrdersExecutor,
  if(Fondsen.optieType<>'',if(SUBSTR(Rekeningmutaties.transactieType,1,1)='O',1,2),'') AS DNID,
  $select
Fondsen.fondssoort,
Fondsen.fondseenheid,
externeOrders.fonds,
externeOrders.ISIN as ISINCode,
Fondsen.Omschrijving as fondsOmschrijving,
Fondsen.optieUitoefenprijs,
Fondsen.optieExpDatum,
Rekeningen.depotbank as RekeningDepot,
Fondsen.Lossingsdatum,
Rekeningen.portefeuille,
Vermogensbeheerders.LEInrVBH,
Vermogensbeheerders.orderTransRepDecisionMaker,
Vermogensbeheerders.jaarafsluitingPerBewaarder,
Rekeningen.Depotbank,
DecisionMakerVM.voornamen as DecisionMakerVmVoornamen,
DecisionMakerVM.achternaam as DecisionMakerVmAchternaam,
DecisionMakerVM.paspoortNummer AS DecisionMakerVmpaspoortNummer,
Portefeuilles.Client,
Clienten.extraInfo,
Clienten.Land as clientenLand
FROM
  externeOrders
JOIN Rekeningmutaties ON externeOrders.externOrderId=Rekeningmutaties.orderId
JOIN Fondsen ON Rekeningmutaties.fonds = Fondsen.Fonds
JOIN Rekeningen ON Rekeningmutaties.rekening = Rekeningen.rekening AND Rekeningen.consolidatie=0
JOIN Portefeuilles ON Rekeningen.portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0
JOIN Clienten ON Portefeuilles.Client = Clienten.Client
JOIN Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
LEFT JOIN ISOLanden ON Depotbanken.landCode = ISOLanden.landCode
LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
INNER JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
LEFT JOIN Gebruikers as DecisionMakerVM ON Vermogensbeheerders.orderTransRepDecisionMaker = DecisionMakerVM.Gebruiker
WHERE Rekeningmutaties.orderId <> '' AND externeOrders.verwerkt=0 AND Rekeningmutaties.aantal<>0  $extraWhere
$group";
  
  $db=new DB();
  $db->SQL($query);
  $db->Query();
  $transactievertaling=array('A'=>'S','V'=>'P');
  $orders=array();
  while($data=$db->nextRecord())
  {
    $orders[]=$data;
  }
  
  $query = "SELECT  Depotbanken.LEInrDepBank as LEInrDepBank,
Depotbanken.landCode AS depotLandcodeLang,
ISOLanden.landCodeKort  AS depobankRekeningLandcodeKort
FROM
Depotbanken
LEFT JOIN ISOLanden ON Depotbanken.landCode = ISOLanden.landCode
WHERE Depotbank='BIN'";
  $db->SQL($query);
  $db->Query();
  $binrekeningDepot = $db->nextRecord();
  
  
  $paspoortVelden=array('DecisionMakerVmpaspoortNummer','nummerID','part_nummerID');
  foreach($orders as $orderData)
  {
    $clientInfo=explode('|',$orderData['extraInfo']);
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
Clienten.naam,
'titel' as titel,
'voorvoegsel' as voorvoegsel,
'voorletters' as voorletters,
'voornamen' as voornamen,
'achternaam' as achternaam,
'tussenvoegsel' as tussenvoegsel,
'geboortedatum' as geboortedatum,
'legitimatie' as legitimatie,
'nummerID' as nummerID,
Clienten.Land as landID,
Clienten.Naam1 as part_naam,
'part_titel' as part_titel,
'part_voorvoegsel' as part_voorvoegsel,
'part_voorletters' as part_voorletters,
'part_voornamen' as part_voornamen,
'part_tussenvoegsel' as part_tussenvoegsel,
'part_achternaam' as part_achternaam,
'part_nationaliteit' as part_nationaliteit,
'part_nummerID' as part_nummerID,
'part_landID' as part_landID,
'part_geboortedatum' as part_geboortedatum,
'ondernemingsvorm' as ondernemingsvorm,
'LEInr' as LEInr,
Clienten.Land as land,
Clienten.extraInfo
FROM Clienten WHERE Client='" . mysql_real_escape_string($orderData['Client']) . "'";
    
    $db->SQL($query);
    $db->Query();
    $crmData= $db->nextRecord();
    $extra=explode("|",$crmData['extraInfo']);
    $extraVertaling=array('geboortedatum','legitimatie');
    foreach($extra as $index=>$waarde)
    {
      $crmData[$extraVertaling[$index]]=$waarde;
    }
    
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
    foreach($clientInfo as $key=>$value)
    {
      $value=str_replace('`','',$value);
      $clientInfo[$key] = mb_strtoupper($value);
    }
    $orderData['clientenLand']=mb_strtoupper($orderData['clientenLand']);
    
    $regel=$leeg;
    $regel[0] = 1;
    $regel[1] = $orderData['side'];
    $regel[2] = 1;
    
    if($totaal==false)
    {
      $regel[3] = $orderData['orderid'] . sprintf("%05d",$orderData['orderregelId']+0);
    }
    else
    {
      $regel[3] = $orderData['orderid'];
    }
    
    $regel[5] = $orderData['LEInrVBH'];
    $regel[6] = 0;
    $regel[7] = $orderData['LEInrVBH'];
    $regel[8] = 0;
    $regel[11] = date('Y-m-d\TH:i:s',db2jul($orderData['uitvoeringsDatum'])).".000000";
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
    
    $regel[20] = abs(round($orderData['RekeningmutatiesBedrag'],2));
    $regel[22] = '';//NL
    $regel[21] = 'XOFF';
    $regel[33] = $orderData['ISINCode'];
    $regel[34] = '';//$orderData['fondsOmschrijving'];
    
    if($FondsExtraInformatie['CFIcode'] <> '')
      $regel[35] = $FondsExtraInformatie['CFIcode'];
    else
      $regel[35] = 'XXXXXX';
    $regel[35] = '';
    
    $regel[36] = '';//$orderData['fondsValuta'];
    $regel[37] = '';// $orderData['fondsValuta']; "Notional Currency 2" leeg.
    $regel[38] = '';//$orderData['fondseenheid'];
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
    $regel[50] = '';
    
    $regel[51] = '';//$orderData['DecisionMakerVmpaspoortNummer'];
    $regel[52] = '';//'NL';
    $regel[53] = 1;
    $regel[54] = $orderData['externeOrdersExecutor'];//$orderData['LEInrVBH'];
    $regel[55] = 'NL';
    if($orderData['side']==2)
      $regel[57] = '';//'UNDI';
    $regel[58] = '';//PostTrade
    $regel[60] = 0;
    if($orderData['side']==1)//buy
    {
      //if($crmData['ondernemingsvorm'] <> '')
      //{
      //  $regel[61] = 1;
     //   $regel[62] = $crmData['LEInr'] ;
      //  $regel[63] = 'NL';//$crmData['land'] ;
     // }
      //else
      //{
        if($totaal==false)
        {
          $regel[61] = 3;
          $regel[62] = $clientInfo[1]; //$crmData['nummerID'];
          $regel[63] = $orderData['clientenLand'];//'NL';//$crmData['land'] ;
          $regel[64] = $clientInfo[2];//strtoupper(str_replace(' ',',',$crmData['voornamen']));
          $regel[65] = $clientInfo[3];//strtoupper(trim($crmData['tussenvoegsel'].' '.$crmData['achternaam']));
          $regel[66] = $clientInfo[0];//$crmData['geboortedatum'];
        }
        else
        {
          $regel[61] = 4;
          $regel[62] = 'INTC';
          $regel[63] = 'NL';
          $regel[64] = '';
          $regel[65] = '';
          $regel[66] = '';
        }
        /*
              if($crmData['part_achternaam']<>'')
              {
                $regel[67] = 3;
                $regel[68] = $crmData['part_nummerID'];
                $regel[69] = 'NL';//$crmData['land'];
                $regel[70] = strtoupper(str_replace(' ',',',$crmData['part_voornamen']));
                $regel[71] = strtoupper(trim($crmData['part_tussenvoegsel'].' '.$crmData['part_achternaam']));
                $regel[72] = $crmData['part_geboortedatum'];
              }
        */
      //}
      if($totaal==false)
      {
        $regel[73] = 4;
        $regel[74] = 'INTC';//$orderData['LEInrDepBank'];
        $regel[75] = 'NL';//$orderData['depotLandcodeKort'];
      }
      else
      {
        $regel[73] = 1;
        $regel[74] = $binrekeningDepot['LEInrDepBank'];
        $regel[75] = 'NL';//$orderData['depotLandcodeKort'];
      }
      $regel[85] = '';
      $regel[86] = '';//$orderData['LEInrVBH'];
      
      
    }
    else
    {
      if($totaal==false)
      {
        $regel[61] = 4;
        $regel[62] = 'INTC';//$orderData['LEInrDepBank'];
        $regel[63] = 'NL';// $orderData['depotLandcodeKort'];
      }
      else
      {
        $regel[61] = 1;
        $regel[62] = $orderData['LEInrDepBank'];
        $regel[63] = 'NL';
      }
      
      
      //if($crmData['ondernemingsvorm'] <> '')
      //{
      //  $regel[73] = 1;
      //  $regel[74] = $crmData['LEInr'] ;
      //  $regel[75] = 'NL';//$crmData['land'] ;
      //}
      //else
      //{
        if($totaal==false)
        {
          $regel[73] = 3;
          $regel[74] = $clientInfo[1];
          $regel[75] = $orderData['clientenLand'];//'NL';//$crmData['land'] ;
          $regel[76] = $clientInfo[2];
          $regel[77] = $clientInfo[3];
          $regel[78] = $clientInfo[0];//$crmData['geboortedatum'];
        }
        else
        {
          $regel[73] = 4;
          $regel[74] = 'INTC';
          $regel[75] = 'NL';
          $regel[76] = '';
          $regel[77] = '';
          $regel[78] = '';
        }

        /*
              if($crmData['part_achternaam']<>'')
              {
                $regel[79] = 3;
                $regel[80] = $crmData['part_nummerID'];
                $regel[81] = 'NL';//$crmData['land'];
                $regel[82] = strtoupper(str_replace(' ',',',$crmData['part_voornamen']));
                $regel[83] = strtoupper(trim($crmData['part_tussenvoegsel'].' '.$crmData['part_achternaam']));
                $regel[84] = $crmData['part_geboortedatum'];
              }
        */
      //}
      
      $regel[95] = '';//1;
      $regel[96] = '';//$orderData['LEInrVBH'];
      
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
  
    $conversie=array('nat_id'=>1,'concat'=>3,'passport'=>2);
    $idType=strtolower($clientInfo[4]);
    if($regel[61] == 3)
    {
      if(isset($conversie[$idType]))
        $regel[112] = $conversie[$idType];
      else
        $regel[112] = 0;
    }
    elseif($regel[61] == 4)
      $regel[112] = '';
    
    if($regel[73] == 3)
    {
      if(isset($conversie[$idType]))
        $regel[114] = $conversie[$idType];
      else
        $regel[114] = 0;
    }
    elseif($regel[61] == 4)
      $regel[114] = '';
  
  
    $regel[120] = '';//2;
    $regel[121] = 2;
    
    if($regel[18]==2)
      $regel[122] = 3;
    else
      $regel[122] = 1;
  
    if($totaal==true)
    {
      $regel[114] = '';
    }
    
    $body[]=$regel;
  }
  return $body;
}

$header=array('ARM APA Indicator','Side','Action','Transaction Reference Number','Trading Venue Transaction ID','Executing Entity ID','Investment Firm Director Indicator','Submitting Entity ID','Transmission Of Order Indicator','Transmitting Firm ID For The Buyer','Transmitting Firm ID For The Seller','Trading Date Time','Trading Capacity 1','Trading Capacity 2','Quantity','Quantity Currency','Derivative Notional Increase Decrease','Price','Price Notation','Price Currency','Net Amount','Venue','Country Branch Membership','Up-Front Payment','Up-Front Payment Currency','ComplexTradeComponentID','ByPassControlFlag','DeferralIndicator','FreeText1','FreeText2','FreeText3','FreeText4','FreeText5','Security ID','Full Name','Classification Type','Notional Currency 1','Notional Currency 2','Price Multiplier','Underlying Security ID','Underlying Index Name','Underlying Instrument Term','Option Type','Strike Price','Strike Price Notation','Strike Price Currency','Option Exercise Style','Maturity Date','Expiry Date','Delivery Type','Investment Decision Within Firm Type','Investment Decision Within Firm','Country Of Investor','Execution Within Firm Type','Execution Within Firm','Country Of Executor','Waiverindicator','ShortSellingIndicator','OTCPostTradeIndicator','CommodityDerivativeIndicator','SecuritiesFinancingTransactionIndicator','Buyer Identification Type','Buyer Identification Code','Buyer Country Branch','Buyer First Name','Buyer Surname','Buyer BirthDate','Buyer Identification Type2','Buyer Identification Code2','Buyer Country Branch2','Buyer First Name2','Buyer Surname2','Buyer BirthDate2','Seller Identification Type','Seller Identification Code','Seller Country Branch','Seller First Name','Seller Surname','Seller BirthDate','Seller Identification Type2','Seller Identification Code2','Seller Country Branch2','Seller First Name2','Seller Surname2','Seller BirthDate2','Buyer Decision Maker Type','Buyer Decision Maker Code','Buyer Decision Maker First Name','Buyer Decision Maker Surname','Buyer Decision Maker BirthDate','Buyer Decision Maker Type2','Buyer Decision Maker Code2','Buyer Decision Maker First Name2','Buyer Decision Maker Surname2','Buyer Decision Maker BirthDate2','Seller Decision Maker Type','Seller Decision Maker Code','Seller Decision Maker First Name','Seller Decision Maker Surname','Seller Decision Maker BirthDate','Seller Decision Maker Type2','Seller Decision Maker Code2','Seller Decision Maker First Name2','Seller Decision Maker Surname2','Seller Decision Maker BirthDate2','Business Unit','Settlement Flag','Client ID','Clearing Firm ID','Guarantee Flag','Settlement Period','Account Number',
  'Buyer National ID Type','Buyer National ID Type2','Seller National ID Type','Seller National ID Type2','Buyer Decision Maker National ID Type',
  'Buyer Decision Maker National ID Type2','Seller Decision Maker National ID Type','Seller Decision Maker National ID Type2','Investment Decision Within Firm National ID Type','Execution Within Firm National ID Type','Quantity Notation');

$body=array();
$body[]=$header;


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
    if (substr($key, 0, 5) == "vink_")
    {
      $ids[] = substr($key, 5);
    }
  }
  if (count($ids) > 0)
  {
    $extraWhere .= " AND externeOrders.externOrderId IN('" . implode("','", $ids) . "')";
  }

$body=regelsToevoegen($body,$extraWhere,false);
$body=regelsToevoegen($body,$extraWhere,true);


if(count($ids)>0 && $_POST["action"]=='definitef')
{
  $db=new DB();
  $query = "UPDATE externeOrders SET verwerkt=1,change_date=now(),change_user='$USR' WHERE 1 $extraWhere";
  $db->SQL($query);
  $db->Query();
}


$filename='orderReport.csv';
header('Content-type: ' . "text/comma-separated-values; charset=utf8");
//header("Content-Length: ".strlen($outputStr));
header("Content-Disposition: inline; filename=\"".$filename."\"");
header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
foreach($body as $line)
{
  $outputStr = implode(';', $line) . "\n";
  echo utf8_encode($outputStr);
}


?>