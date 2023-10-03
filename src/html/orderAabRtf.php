<?php

include_once("wwwvars.php");
include_once("rapport/rapportRekenClass.php");


class orderRTF
{
  function orderRTF($orderId)
  {
    global $__ORDERvar,$__appvar;
    $this->db = new DB();
    $query = "SELECT OrdersV2.id,OrdersV2.fonds,OrdersV2.beurs,OrdersV2.ISINCode,OrdersV2.fondsOmschrijving,OrdersV2.transactieType,OrdersV2.transactieSoort,
    OrdersV2.tijdsLimiet,OrdersV2.tijdsSoort,OrdersV2.koersLimiet,OrdersV2.fondseenheid,OrdersV2.fondsValuta,
    OrdersV2.orderStatus,OrdersV2.memo,OrdersV2.depotbank,OrdersV2.batchId,OrdersV2.orderSoort,OrdersV2.giraleOrder,OrdersV2.fixOrder,OrdersV2.fixVerzenddatum,OrdersV2.fixAnnuleerdatum,
    OrdersV2.add_date,OrdersV2.add_user,OrdersV2.change_date,OrdersV2.change_user, BbLandcodes.settlementDays, OrdersV2.settlementdatum
    FROM OrdersV2 
    LEFT JOIN Fondsen ON OrdersV2.fonds = Fondsen.Fonds
    LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
    WHERE OrdersV2.id='$orderId'";
    $this->db->SQL($query);
    $this->orderRecord=$this->db->lookupRecord();

    $this->order=array();
    $this->order['order']=$this->orderRecord;
    $this->order['uitvoeringen']=$this->getUitvoeringen($orderId);
    $this->getFondsInfo($orderId,$this->orderRecord['fonds']);
    $this->orderRecordRegels=$this->getOrderRegels($orderId);

    $baseDays=2;
    if($this->order['order']['settlementDays'] > 0)
      $baseDays=$this->order['order']['settlementDays'];

    $uitvoeringsJul=db2jul($this->order['uitvoeringen']['uitvoeringsDatum']);
    $dagvanweek=date('N',$uitvoeringsJul);

    if($dagvanweek<=(5-$baseDays) && $dagvanweek<6)
      $extraDagen=0;
    elseif($dagvanweek<=(10-$baseDays) && $dagvanweek<6)
      $extraDagen=2;
    else
      $extraDagen=4;

    if($this->orderRecord['settlementdatum']=='0000-00-00' || $this->orderRecord['settlementdatum']=='')
      $settleDatum=date('d-m-Y',$uitvoeringsJul+(($baseDays+$extraDagen)*86400)+3605);
    else
      $settleDatum=date('d-m-Y',db2jul($this->orderRecord['settlementdatum']));

    $this->variables=array();
    $this->variables['Gebruiker naam']=$_SESSION['usersession']['gebruiker']['Naam'];
    $this->variables['datum']=date('d-m-Y');
    $this->variables['tijd']=date('H:i:s');
    $this->variables['aantal transacties']=count($this->order['orderregels']);
    $this->variables['transactiesoort volledig']=$__ORDERvar['transactieSoort'][$this->order['order']['transactieSoort']];
    $this->variables['ISIN']=$this->order['order']['ISINCode'];
    if(substr($this->order['order']['transactieSoort'],0,1)=='A')
    {
      $this->variables['transactiesoort kort'] = 'DVP';
      $this->variables['betreft']='Ontvangst tegen betaling';
      $this->variables['teLeverenDoor']=$__appvar["bedrijf"].': '.$_POST['settlementVB'];
      $this->variables['teOntvangenDoor']='ABN AMRO: '.$_POST['settlementAAB'];
      $this->variables['betreftOrderregels']='Delivery against payment';
      $this->variables['crediterendebiteren']='debiteren';
    }
    else
    {
      $this->variables['transactiesoort kort'] = 'RVP';
      $this->variables['betreft']='Levering tegen betaling';
      $this->variables['teLeverenDoor']='ABN AMRO: '.$_POST['settlementAAB'];
      $this->variables['teOntvangenDoor']=$__appvar["bedrijf"].': '.$_POST['settlementVB'];
      $this->variables['betreftOrderregels']='Receive against payment';
      $this->variables['crediterendebiteren']='crediteren';
    }
    $this->variables['fondsomschrijving']=$this->order['order']['fondsOmschrijving'];
    $this->variables['transactiedatum']=date('d-m-Y',$uitvoeringsJul);
    $this->variables['settlementdatum']=$settleDatum;
    $this->variables['aantal']=$this->order['order']['aantal'];
    $this->variables['fondsvaluta']=$this->order['order']['fondsValuta'];
    $this->variables['som nettobedrag']=number_format($this->order['order']['nettoBedrag']/$this->order['uitvoeringen']['fondsValutaKoers'],2,",",".");

    $this->variables['tlvEffDepot']='leeg';
    $this->variables['tlvRekening']='leeg';
    $brokerTmp = $this->getBrokerinstructies($__appvar["bedrijf"],'AAB');
    if($brokerTmp['portefeuille'] <> '' && $brokerTmp['iban'] <> '')
    {
      $broker=$brokerTmp;
      $this->variables['tlvEffDepot']= $broker['portefeuille'];
      $this->variables['tlvRekening'] = $broker['iban'];
    }
    if($this->order['order']['fondsValuta']<>'EUR') //$data['USDsettlement']==1
    {
      $brokerTmp = $this->getBrokerinstructies($__appvar["bedrijf"],'AAB', $this->order['order']['fondsValuta']);
      if($brokerTmp['portefeuille'] <> '' && $brokerTmp['iban'])
      {
        $this->variables['tlvEffDepot']= $broker['portefeuille'];
        $this->variables['tlvRekening'] = $broker['iban'];
      }
    }


//listarray($__ORDERvar);
  }


  function formatGetal($waarde, $dec, $VierDecimalenZonderNullen=false)
  {
    if($waarde==0)
      return '';

    if ($VierDecimalenZonderNullen)
    {
      $getal = explode('.',$waarde);
      $decimaalDeel = $getal[1];
      if ($decimaalDeel != '0000' )
      {
        for ($i = strlen($decimaalDeel); $i >=0; $i--)
        {
          $decimaal = $decimaalDeel[$i-1];
          if ($decimaal != '0' && !$newDec)
          {
            $newDec = $i;
          }
        }
        return number_format($waarde,$newDec,",",".");
      }
      else
        return number_format($waarde,$dec,",",".");
    }
    else
      return number_format($waarde,$dec,",",".");
  }

  function template($rtfTemplate,$clientRtf)
  {

    $rtfClient='\par }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid4291646 -------------------------------------------------<ordernummer client 1>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450  }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid4291646 
------------------------------------------
\par }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid791664 Onze referentie\tab \tab <ordernummer client 1>\line Client\tab \tab \tab 
<portefeuille> <client>\line Transactiedatum\tab <transactiedatum>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450  }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid791664 \line Client }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450  }{\rtlch\fcs1 
\af0 \ltrch\fcs0 \insrsid15424450\charrsid791664 ontvangt }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid7168094 \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid791664 <aantal>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 \line }{\rtlch\fcs1 \af0 
\ltrch\fcs0 \insrsid15424450\charrsid791664 ISIN\tab \tab \tab <isin>\line Naam\tab \tab \tab <fondsomschrijving>\line Koers\tab \tab \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 <uitvoeringsprijs>}{\rtlch\fcs1 \af0 \ltrch\fcs0 
\insrsid15424450\charrsid791664 \line Bedrag\tab \tab \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 <afrekenvaluta> }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid791664 <netto bedrag>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450  \line 
}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid791664 Instructie\tab \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid7168094 <instructie>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 \line }{\rtlch\fcs1 \af0 \ltrch\fcs0 
\insrsid15424450\charrsid791664 Settlementdatum\tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 <settlementdatum>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid791664';


    $this->variables['clienten'] = '';
      foreach($this->order['orderregels']  as $regelId=>$orderregel)
      {
        $tmp=$clientRtf;


        $variabelen=array('ordernummer client 1'=>$this->order['order']['id'].'-'.$orderregel['positie'],
                          'portefeuille'=>$orderregel['portefeuille'],
                          'client'=>$orderregel['client'],
                          'aantal'=>$orderregel['aantal'],
                          'transactiedatum'=>$this->variables['transactiedatum'],
                         'isin'=>$this->variables['ISIN'],
                         'fondsomschrijving'=>$this->variables['fondsomschrijving'],
                         'uitvoeringsprijs'=>$this->formatGetal($this->order['uitvoeringen']['gemiddeldePrijsValuta'],4,true),
                         'afrekenvaluta'=>$orderregel['rekeningValuta'],
                         'netto bedrag'=>number_format($orderregel['nettoBedrag'],2,",","."),
                         'instructie'=>$this->variables['betreftOrderregels'],
                         'settlementdatum'=>$this->variables['settlementdatum']);

        foreach($variabelen as $key=>$value)
          $tmp = str_replace('<' . $key . '>', $value, $tmp);

        $this->variables['clienten'] .= $tmp;
      }

    foreach($this->variables as $key=>$value)
    {
      $rtfTemplate = str_replace('<' . $key . '>', $value, $rtfTemplate);
    }
    return $rtfTemplate;
  }

  function getBrokerinstructies($vermogensbeheerder='',$depotbank='KAS',$valuta='EUR')
  {
    $db=new DB();
    $query="SELECT portefeuille,iban FROM Brokerinstructies WHERE vermogensbeheerder='$vermogensbeheerder' AND vvSettlement='$valuta' AND depotbank='$depotbank'";
    $db->SQL($query);
    $db->Query();
    $data=$db->nextRecord();
    return $data;
  }

  function getOrderRegels($orderId)
  {
    $query="SELECT OrderRegelsV2.id,OrderRegelsV2.positie,OrderRegelsV2.portefeuille,OrderRegelsV2.client,OrderRegelsV2.rekening,
   OrderRegelsV2.aantal,OrderRegelsV2.bedrag,OrderRegelsV2.orderregelStatus,OrderRegelsV2.nettoBedrag,
   Rekeningen.Valuta as rekeningValuta
   FROM OrderRegelsV2 
   LEFT JOIN Portefeuilles ON OrderRegelsV2.Portefeuille=Portefeuilles.Portefeuille
   LEFT JOIN Rekeningen ON OrderRegelsV2.Rekening=Rekeningen.Rekening WHERE orderid='$orderId' ORDER BY positie";
    $this->db->SQL($query);
    $this->db->Query();
    $this->order['orderregels']=array();
    while($data=$this->db->nextRecord())
    {
      $crmNaam = getCrmNaam($data['portefeuille'],true);
      if ($crmNaam['naam'] <> '')
      {
        $data["client"] = $crmNaam['naam'];
      }
      $this->order['orderregels'][$data['id']]=$data;
      $this->order['order']['aantal']+=$data['aantal'];
      $this->order['order']['nettoBedrag']+=$data['nettoBedrag'];

      if(!isset($eersteOrderRegel))
        $eersteOrderRegel=$data;
    }
    if(isset($eersteOrderRegel))
      return $eersteOrderRegel;
  }

  function getUitvoeringen($orderId)
  {
    $db=new DB();
    $query = "SELECT * FROM OrderUitvoeringV2 WHERE orderid='".$orderId."'  ";
    $db->SQL($query);
    $db->Query();
    $uitvoering=array();
    while($data=$db->nextRecord())
    {
      $data['valutaKoers']=getValutaKoers($this->order['order']['fondsValuta'],$data['uitvoeringsDatum']);
      $uitvoering['Waarde'] +=$data['uitvoeringsAantal']*$data['uitvoeringsPrijs'];
      $uitvoering['WaardeEur'] += $data['uitvoeringsAantal']*$data['uitvoeringsPrijs']*$data['valutaKoers'];
      $uitvoering['Aantal'] +=$data['uitvoeringsAantal'];
      $uitvoering['uitvoeringsDatum'] =$data['uitvoeringsDatum'];
      $uitvoering['fondsValutaKoers'] =$data['valutaKoers'];
      $uitvoering[]=$data;
    }
    if($uitvoering['Aantal'] <> 0)
    {
      $uitvoering['gemiddeldePrijsValuta']=$uitvoering['Waarde']/$uitvoering['Aantal'];
      $uitvoering['gemiddeldePrijsEur']=$uitvoering['WaardeEur']/$uitvoering['Aantal'];
    }
    else
    {
      $uitvoering['gemiddeldePrijsValuta']=0;
      $uitvoering['gemiddeldePrijsEur']=0;
    }
    return $uitvoering;
  }
  function getFondsInfo($orderId,$fonds)
  {
    $query="SELECT Fondseenheid,Omschrijving,ISINCode,Valuta,Lossingsdatum FROM Fondsen WHERE Fonds='$fonds'";
    $this->db->SQL($query);
    $this->order['fonds']=$this->db->lookupRecord();

    $query = "SELECT Fonds,Datum,Koers FROM Fondskoersen WHERE fonds='".$fonds."' Order by datum desc limit 1 ";
    $this->db->SQL($query);
    $this->order['fondskoers'] = $this->db->lookupRecord();

    $query = "SELECT Valuta,Datum,Koers FROM Valutakoersen WHERE Valuta='".$this->orders[$orderId]['order']['fondsValuta']."' Order by datum desc limit 1 ";
    $this->db->SQL($query);
    $this->order['valutakoers'] = $this->db->lookupRecord();
  }

}

$rtfTemplate='{\rtf1\adeflang1025\ansi\ansicpg1252\uc1\adeff0\deff0\stshfdbch0\stshfloch31506\stshfhich31506\stshfbi31506\deflang1043\deflangfe1043\themelang1043\themelangfe0\themelangcs0{\fonttbl{\f0\fbidi \froman\fcharset0\fprq2{\*\panose 02020603050405020304}Times New Roman;}{\f34\fbidi \froman\fcharset0\fprq2{\*\panose 02040503050406030204}Cambria Math;}
{\f37\fbidi \fswiss\fcharset0\fprq2{\*\panose 020f0502020204030204}Calibri;}{\flomajor\f31500\fbidi \froman\fcharset0\fprq2{\*\panose 02020603050405020304}Times New Roman;}
{\fdbmajor\f31501\fbidi \froman\fcharset0\fprq2{\*\panose 02020603050405020304}Times New Roman;}{\fhimajor\f31502\fbidi \froman\fcharset0\fprq2{\*\panose 02040503050406030204}Cambria;}
{\fbimajor\f31503\fbidi \froman\fcharset0\fprq2{\*\panose 02020603050405020304}Times New Roman;}{\flominor\f31504\fbidi \froman\fcharset0\fprq2{\*\panose 02020603050405020304}Times New Roman;}
{\fdbminor\f31505\fbidi \froman\fcharset0\fprq2{\*\panose 02020603050405020304}Times New Roman;}{\fhiminor\f31506\fbidi \fswiss\fcharset0\fprq2{\*\panose 020f0502020204030204}Calibri;}
{\fbiminor\f31507\fbidi \froman\fcharset0\fprq2{\*\panose 02020603050405020304}Times New Roman;}{\f41\fbidi \froman\fcharset238\fprq2 Times New Roman CE;}{\f42\fbidi \froman\fcharset204\fprq2 Times New Roman Cyr;}
{\f44\fbidi \froman\fcharset161\fprq2 Times New Roman Greek;}{\f45\fbidi \froman\fcharset162\fprq2 Times New Roman Tur;}{\f46\fbidi \froman\fcharset177\fprq2 Times New Roman (Hebrew);}{\f47\fbidi \froman\fcharset178\fprq2 Times New Roman (Arabic);}
{\f48\fbidi \froman\fcharset186\fprq2 Times New Roman Baltic;}{\f49\fbidi \froman\fcharset163\fprq2 Times New Roman (Vietnamese);}{\f381\fbidi \froman\fcharset238\fprq2 Cambria Math CE;}{\f382\fbidi \froman\fcharset204\fprq2 Cambria Math Cyr;}
{\f384\fbidi \froman\fcharset161\fprq2 Cambria Math Greek;}{\f385\fbidi \froman\fcharset162\fprq2 Cambria Math Tur;}{\f388\fbidi \froman\fcharset186\fprq2 Cambria Math Baltic;}{\f389\fbidi \froman\fcharset163\fprq2 Cambria Math (Vietnamese);}
{\f411\fbidi \fswiss\fcharset238\fprq2 Calibri CE;}{\f412\fbidi \fswiss\fcharset204\fprq2 Calibri Cyr;}{\f414\fbidi \fswiss\fcharset161\fprq2 Calibri Greek;}{\f415\fbidi \fswiss\fcharset162\fprq2 Calibri Tur;}
{\f418\fbidi \fswiss\fcharset186\fprq2 Calibri Baltic;}{\f419\fbidi \fswiss\fcharset163\fprq2 Calibri (Vietnamese);}{\flomajor\f31508\fbidi \froman\fcharset238\fprq2 Times New Roman CE;}
{\flomajor\f31509\fbidi \froman\fcharset204\fprq2 Times New Roman Cyr;}{\flomajor\f31511\fbidi \froman\fcharset161\fprq2 Times New Roman Greek;}{\flomajor\f31512\fbidi \froman\fcharset162\fprq2 Times New Roman Tur;}
{\flomajor\f31513\fbidi \froman\fcharset177\fprq2 Times New Roman (Hebrew);}{\flomajor\f31514\fbidi \froman\fcharset178\fprq2 Times New Roman (Arabic);}{\flomajor\f31515\fbidi \froman\fcharset186\fprq2 Times New Roman Baltic;}
{\flomajor\f31516\fbidi \froman\fcharset163\fprq2 Times New Roman (Vietnamese);}{\fdbmajor\f31518\fbidi \froman\fcharset238\fprq2 Times New Roman CE;}{\fdbmajor\f31519\fbidi \froman\fcharset204\fprq2 Times New Roman Cyr;}
{\fdbmajor\f31521\fbidi \froman\fcharset161\fprq2 Times New Roman Greek;}{\fdbmajor\f31522\fbidi \froman\fcharset162\fprq2 Times New Roman Tur;}{\fdbmajor\f31523\fbidi \froman\fcharset177\fprq2 Times New Roman (Hebrew);}
{\fdbmajor\f31524\fbidi \froman\fcharset178\fprq2 Times New Roman (Arabic);}{\fdbmajor\f31525\fbidi \froman\fcharset186\fprq2 Times New Roman Baltic;}{\fdbmajor\f31526\fbidi \froman\fcharset163\fprq2 Times New Roman (Vietnamese);}
{\fhimajor\f31528\fbidi \froman\fcharset238\fprq2 Cambria CE;}{\fhimajor\f31529\fbidi \froman\fcharset204\fprq2 Cambria Cyr;}{\fhimajor\f31531\fbidi \froman\fcharset161\fprq2 Cambria Greek;}{\fhimajor\f31532\fbidi \froman\fcharset162\fprq2 Cambria Tur;}
{\fhimajor\f31535\fbidi \froman\fcharset186\fprq2 Cambria Baltic;}{\fhimajor\f31536\fbidi \froman\fcharset163\fprq2 Cambria (Vietnamese);}{\fbimajor\f31538\fbidi \froman\fcharset238\fprq2 Times New Roman CE;}
{\fbimajor\f31539\fbidi \froman\fcharset204\fprq2 Times New Roman Cyr;}{\fbimajor\f31541\fbidi \froman\fcharset161\fprq2 Times New Roman Greek;}{\fbimajor\f31542\fbidi \froman\fcharset162\fprq2 Times New Roman Tur;}
{\fbimajor\f31543\fbidi \froman\fcharset177\fprq2 Times New Roman (Hebrew);}{\fbimajor\f31544\fbidi \froman\fcharset178\fprq2 Times New Roman (Arabic);}{\fbimajor\f31545\fbidi \froman\fcharset186\fprq2 Times New Roman Baltic;}
{\fbimajor\f31546\fbidi \froman\fcharset163\fprq2 Times New Roman (Vietnamese);}{\flominor\f31548\fbidi \froman\fcharset238\fprq2 Times New Roman CE;}{\flominor\f31549\fbidi \froman\fcharset204\fprq2 Times New Roman Cyr;}
{\flominor\f31551\fbidi \froman\fcharset161\fprq2 Times New Roman Greek;}{\flominor\f31552\fbidi \froman\fcharset162\fprq2 Times New Roman Tur;}{\flominor\f31553\fbidi \froman\fcharset177\fprq2 Times New Roman (Hebrew);}
{\flominor\f31554\fbidi \froman\fcharset178\fprq2 Times New Roman (Arabic);}{\flominor\f31555\fbidi \froman\fcharset186\fprq2 Times New Roman Baltic;}{\flominor\f31556\fbidi \froman\fcharset163\fprq2 Times New Roman (Vietnamese);}
{\fdbminor\f31558\fbidi \froman\fcharset238\fprq2 Times New Roman CE;}{\fdbminor\f31559\fbidi \froman\fcharset204\fprq2 Times New Roman Cyr;}{\fdbminor\f31561\fbidi \froman\fcharset161\fprq2 Times New Roman Greek;}
{\fdbminor\f31562\fbidi \froman\fcharset162\fprq2 Times New Roman Tur;}{\fdbminor\f31563\fbidi \froman\fcharset177\fprq2 Times New Roman (Hebrew);}{\fdbminor\f31564\fbidi \froman\fcharset178\fprq2 Times New Roman (Arabic);}
{\fdbminor\f31565\fbidi \froman\fcharset186\fprq2 Times New Roman Baltic;}{\fdbminor\f31566\fbidi \froman\fcharset163\fprq2 Times New Roman (Vietnamese);}{\fhiminor\f31568\fbidi \fswiss\fcharset238\fprq2 Calibri CE;}
{\fhiminor\f31569\fbidi \fswiss\fcharset204\fprq2 Calibri Cyr;}{\fhiminor\f31571\fbidi \fswiss\fcharset161\fprq2 Calibri Greek;}{\fhiminor\f31572\fbidi \fswiss\fcharset162\fprq2 Calibri Tur;}
{\fhiminor\f31575\fbidi \fswiss\fcharset186\fprq2 Calibri Baltic;}{\fhiminor\f31576\fbidi \fswiss\fcharset163\fprq2 Calibri (Vietnamese);}{\fbiminor\f31578\fbidi \froman\fcharset238\fprq2 Times New Roman CE;}
{\fbiminor\f31579\fbidi \froman\fcharset204\fprq2 Times New Roman Cyr;}{\fbiminor\f31581\fbidi \froman\fcharset161\fprq2 Times New Roman Greek;}{\fbiminor\f31582\fbidi \froman\fcharset162\fprq2 Times New Roman Tur;}
{\fbiminor\f31583\fbidi \froman\fcharset177\fprq2 Times New Roman (Hebrew);}{\fbiminor\f31584\fbidi \froman\fcharset178\fprq2 Times New Roman (Arabic);}{\fbiminor\f31585\fbidi \froman\fcharset186\fprq2 Times New Roman Baltic;}
{\fbiminor\f31586\fbidi \froman\fcharset163\fprq2 Times New Roman (Vietnamese);}}{\colortbl;\red0\green0\blue0;\red0\green0\blue255;\red0\green255\blue255;\red0\green255\blue0;\red255\green0\blue255;\red255\green0\blue0;\red255\green255\blue0;
\red255\green255\blue255;\red0\green0\blue128;\red0\green128\blue128;\red0\green128\blue0;\red128\green0\blue128;\red128\green0\blue0;\red128\green128\blue0;\red128\green128\blue128;\red192\green192\blue192;
\cbackgroundone\ctint255\cshade127\red127\green127\blue127;\caccenttwo\ctint255\cshade255\red192\green80\blue77;}{\*\defchp \f31506\fs22\lang1043\langfe1033\langfenp1033 }{\*\defpap \ql \li0\ri0\sa200\sl276\slmult1
\widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0 }\noqfpromote {\stylesheet{\ql \li0\ri0\sa200\sl276\slmult1\widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0 \rtlch\fcs1 \af0\afs22\alang1025 \ltrch\fcs0 
\f31506\fs22\lang1043\langfe1033\cgrid\langnp1043\langfenp1033 \snext0 \sqformat \spriority0 \styrsid15424450 Normal;}{\*\cs10 \additive \ssemihidden \sunhideused \spriority1 Default Paragraph Font;}{\*
\ts11\tsrowd\trftsWidthB3\trpaddl108\trpaddr108\trpaddfl3\trpaddft3\trpaddfb3\trpaddfr3\tblind0\tblindtype3\tsvertalt\tsbrdrt\tsbrdrl\tsbrdrb\tsbrdrr\tsbrdrdgl\tsbrdrdgr\tsbrdrh\tsbrdrv \ql \li0\ri0\sa200\sl276\slmult1
\widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0 \rtlch\fcs1 \af31506\afs22\alang1025 \ltrch\fcs0 \f31506\fs22\lang1043\langfe1033\cgrid\langnp1043\langfenp1033 \snext11 \ssemihidden \sunhideused Normal Table;}{
\s15\ql \li0\ri0\widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0 \rtlch\fcs1 \af0\afs20\alang1025 \ltrch\fcs0 \f31506\fs20\lang1043\langfe1033\cgrid\langnp1043\langfenp1033 
\sbasedon0 \snext15 \slink16 \ssemihidden \sunhideused \styrsid1324745 footnote text;}{\*\cs16 \additive \rtlch\fcs1 \af0\afs20 \ltrch\fcs0 \fs20 \sbasedon10 \slink15 \slocked \ssemihidden \styrsid1324745 Voetnoottekst Char;}{\*\cs17 \additive 
\rtlch\fcs1 \af0 \ltrch\fcs0 \super \sbasedon10 \ssemihidden \sunhideused \styrsid1324745 footnote reference;}{\s18\ql \li0\ri0\widctlpar\tqc\tx4536\tqr\tx9072\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0 \rtlch\fcs1 
\af0\afs22\alang1025 \ltrch\fcs0 \f31506\fs22\lang1043\langfe1033\cgrid\langnp1043\langfenp1033 \sbasedon0 \snext18 \slink19 \sunhideused \styrsid1324745 header;}{\*\cs19 \additive \rtlch\fcs1 \af0 \ltrch\fcs0 
\sbasedon10 \slink18 \slocked \styrsid1324745 Koptekst Char;}{\s20\ql \li0\ri0\widctlpar\tqc\tx4536\tqr\tx9072\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0 \rtlch\fcs1 \af0\afs22\alang1025 \ltrch\fcs0 
\f31506\fs22\lang1043\langfe1033\cgrid\langnp1043\langfenp1033 \sbasedon0 \snext20 \slink21 \sunhideused \styrsid1324745 footer;}{\*\cs21 \additive \rtlch\fcs1 \af0 \ltrch\fcs0 \sbasedon10 \slink20 \slocked \styrsid1324745 Voettekst Char;}}{\*\pgptbl 
{\pgp\ipgp0\itap0\li0\ri0\sb0\sa0}{\pgp\ipgp0\itap0\li0\ri0\sb0\sa0}}{\*\rsidtbl \rsid279893\rsid791664\rsid871659\rsid1255297\rsid1324745\rsid2512742\rsid3019055\rsid4085325\rsid4291646\rsid6175622\rsid6361787\rsid7168094\rsid7412843\rsid15424450}
{\mmathPr\mmathFont34\mbrkBin0\mbrkBinSub0\msmallFrac0\mdispDef1\mlMargin0\mrMargin0\mdefJc1\mwrapIndent1440\mintLim0\mnaryLim1}{\info{\author AIRS}{\operator Robert van Versendaal}{\creatim\yr2017\mo4\dy12\hr12\min1}{\revtim\yr2017\mo4\dy12\hr12\min1}
{\version2}{\edmins0}{\nofpages1}{\nofwords121}{\nofchars669}{\nofcharsws789}{\vern49273}}{\*\xmlnstbl {\xmlns1 http://schemas.microsoft.com/office/word/2003/wordml}}\paperw11906\paperh16838\margl1417\margr1417\margt1417\margb1417\gutter0\ltrsect 
\deftab708\widowctrl\ftnbj\aenddoc\hyphhotz425\trackmoves0\trackformatting1\donotembedsysfont1\relyonvml0\donotembedlingdata0\grfdocevents0\validatexml1\showplaceholdtext0\ignoremixedcontent0\saveinvalidxml0
\showxmlerrors1\noxlattoyen\expshrtn\noultrlspc\dntblnsbdb\nospaceforul\formshade\horzdoc\dgmargin\dghspace180\dgvspace180\dghorigin1417\dgvorigin1417\dghshow1\dgvshow1
\jexpand\viewkind1\viewscale100\pgbrdrhead\pgbrdrfoot\splytwnine\ftnlytwnine\htmautsp\nolnhtadjtbl\useltbaln\alntblind\lytcalctblwd\lyttblrtgr\lnbrkrule\nobrkwrptbl\snaptogridincell\allowfieldendsel\wrppunct
\asianbrkrule\rsidroot15424450\newtblstyruls\nogrowautofit\usenormstyforlist\noindnmbrts\felnbrelev\nocxsptable\indrlsweleven\noafcnsttbl\afelev\utinl\hwelev\spltpgpar\notcvasp\notbrkcnstfrctbl\notvatxbx\krnprsnet\cachedcolbal \nouicompat \fet0
{\*\wgrffmtfilter 2450}\nofeaturethrottle1\ilfomacatclnup0{\*\ftnsep \ltrpar \pard\plain \ltrpar\ql \li0\ri0\widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0\pararsid1324745 \rtlch\fcs1 \af0\afs22\alang1025 \ltrch\fcs0 
\f31506\fs22\lang1043\langfe1033\cgrid\langnp1043\langfenp1033 {\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid7412843 \chftnsep 
\par }}{\*\ftnsepc \ltrpar \pard\plain \ltrpar\ql \li0\ri0\widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0\pararsid1324745 \rtlch\fcs1 \af0\afs22\alang1025 \ltrch\fcs0 \f31506\fs22\lang1043\langfe1033\cgrid\langnp1043\langfenp1033 {
\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid7412843 \chftnsepc 
\par }}{\*\aftnsep \ltrpar \pard\plain \ltrpar\ql \li0\ri0\widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0\pararsid1324745 \rtlch\fcs1 \af0\afs22\alang1025 \ltrch\fcs0 \f31506\fs22\lang1043\langfe1033\cgrid\langnp1043\langfenp1033 {
\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid7412843 \chftnsep 
\par }}{\*\aftnsepc \ltrpar \pard\plain \ltrpar\ql \li0\ri0\widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0\pararsid1324745 \rtlch\fcs1 \af0\afs22\alang1025 \ltrch\fcs0 \f31506\fs22\lang1043\langfe1033\cgrid\langnp1043\langfenp1033 {
\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid7412843 \chftnsepc 
\par }}\ltrpar \sectd \ltrsect\linex0\headery708\footery708\colsx708\endnhere\sectlinegrid360\sectdefaultcl\sftnbj {\footerr \ltrpar \pard\plain \ltrpar\s20\ql \li0\ri0\widctlpar
\tqc\tx4536\tqr\tx9072\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0 \rtlch\fcs1 \af0\afs22\alang1025 \ltrch\fcs0 \f31506\fs22\lang1043\langfe1033\cgrid\langnp1043\langfenp1033 {\rtlch\fcs1 \af0 \ltrch\fcs0 
\lang1024\langfe1024\noproof\insrsid1324745 {\shp{\*\shpinst\shpleft0\shptop0\shpright891\shpbottom302\shpfhdr0\shpbxmargin\shpbxignore\shpbymargin\shpbyignore\shpwr3\shpwrk0\shpfblwtxt0\shpz0\shplid2049
{\sp{\sn shapeType}{\sv 1}}{\sp{\sn fFlipH}{\sv 1}}{\sp{\sn fFlipV}{\sv 0}}{\sp{\sn rotation}{\sv 11796480}}{\sp{\sn fLockRotation}{\sv 0}}{\sp{\sn fLockAspectRatio}{\sv 0}}{\sp{\sn fLockPosition}{\sv 0}}{\sp{\sn fLockAgainstSelect}{\sv 0}}
{\sp{\sn fLockVerticies}{\sv 0}}{\sp{\sn fLockText}{\sv 0}}{\sp{\sn fLockAdjustHandles}{\sv 0}}{\sp{\sn fLockAgainstGrouping}{\sv 0}}{\sp{\sn lTxid}{\sv 65536}}{\sp{\sn dyTextTop}{\sv 0}}{\sp{\sn dyTextBottom}{\sv 0}}{\sp{\sn fRotateText}{\sv 0}}
{\sp{\sn fFitShapeToText}{\sv 0}}{\sp{\sn fillColor}{\sv 5066944}}{\sp{\sn fFilled}{\sv 0}}{\sp{\sn lineColor}{\sv 11830108}}{\sp{\sn lineWidth}{\sv 28575}}{\sp{\sn fArrowheadsOK}{\sv 0}}{\sp{\sn fLine}{\sv 0}}{\sp{\sn fLockShapeType}{\sv 0}}
{\sp{\sn wzName}{\sv Rechthoek 650}}{\sp{\sn posh}{\sv 2}}{\sp{\sn posrelh}{\sv 5}}{\sp{\sn posv}{\sv 2}}{\sp{\sn posrelv}{\sv 5}}{\sp{\sn metroBlob}{\sv {\*\svb 
504b030414000600080000002100b6833892fe000000e1010000130000005b436f6e74656e745f54797065735d2e786d6c9491414ec3301045f748dcc1f216254ebb400825e982b44b40a81c60644f128b646c794c686f8f93b61b449158da33ffbf27bbdc1cc6414c18d83aaae42a2fa440d2ce58ea2af9bedf650f527004
323038c24a1e91e5a6bebd29f7478f2c529ab8927d8cfe5129d63d8ec0b9f34869d2ba30424cc7d0290ffa033a54eba2b857da51448a599c3b645d36d8c2e710c5f690ae4f26010796e2e9b438b32a09de0f56434ca66a22f383929d09794a2e3bdc5bcf774943aa5f09f3e43ae09c7b494f13ac41f10a213ec398349409ac
70ed1aa7f3bf3b66c99133d7b65663de04de2ea98bd3b56ee3be28e0f4dff226c5de70bab4abe583ea6f000000ffff0300504b03041400060008000000210038fd21ffd6000000940100000b0000005f72656c732f2e72656c73a490c16ac3300c86ef83bd83d17d719ac318a34e2fa3d06be91ec0d88a631a5b4632d9faf6
3383c1327adb51bfd0f7897f7ff84c8b5a91255236b0eb7a50981df9988381f7cbf1e90594549bbd5d28a3811b0a1cc6c787fd19175bdb91ccb1886a942c06e65acbabd6e2664c563a2a98db66224eb6b691832ed65d6d403df4fdb3e6df0c18374c75f206f8e40750975b69e63fec141d93d0543b4749d33445778faa3d7d
e433ae8d62396035e059be43c6b56bcf81beefddfdd31bd89639ba23db846fe4b67e1ca8653f7abde972fc020000ffff0300504b03041400060008000000210068ffc5d9bf020000ad0500000e0000006472732f65326f446f632e786d6cac546d6fd33010fe8ec47fb0fc3d4bd239cd8b964e5bd202d28089c10f7013a7b1
96d8c1769b16c47fe7ec766db77d41403e58f6f9fcdc3d774feeea7adb7768c394e652e438bc083062a2923517ab1c7ffbbaf0128cb4a1a2a69d142cc73ba6f1f5eced9bab71c8d844b6b2ab9942002274360e396e8d1932dfd755cb7aaa2fe4c0045c3652f5d4c051adfc5ad111d0fbce9f04c1d41fa5aa07252ba63558cb
fd259e39fca66195f9dc349a19d4e51872336e556e5ddad59f5dd16ca5e8d0f2ea9006fd8b2c7aca05043d4295d450b456fc1554cf2b25b56ccc45257b5f360daf98e3006cc2e0059b87960ecc7181e2e8e15826fdff60ab4f9b7b85789de36904f511b487267d61556b5ac91e91354289c64167e0f930dc2b4b520f77b27a
d448c8a2a562c56e949263cb680d8985d6df7ff6c01e343c45cbf1a3ac019fae8d74d5da36aa474a4257c22009ec8751d3f1e1bdc5b191a04068ebbab53b768b6d0daac0184da3388930aae02a4cc33876a9fa34b3a8f6f1a0b479c7648fec26c70ac4e040e9e64e1b9be5c9c5ba0bb9e05de7040121c0c51a6d30d7c79f69
90ce9379423c3299ce3d1294a577b32888375d8471545e96455186bf2c7e48b296d7351316ee495321f9b39e1dd4bd57c351555a76bcb6703625ad56cba253684341d3451005a47435879b939bff3c0d4716b8bca0144e48703b49bdc534893db2209197c641e205617a9b4e03929272f19cd21d17ecdf29a131c793248a23
d78eb3ac5f908b8ae4f296bc2647b39e1b181b1def737cd08deb9bd5e05cd46e6f28eff6fbb35ad8fc4fb500013c75da29d68a742f76b35d6e01c52a7729eb1d68d7a914e409b30eb4d44af503a311e6468ef5f735550ca3ee8300fda7212176d0b8036cd4b975f964a5a202881c1b8cf6dbc2ec87d27a507cd54284bdfe85
bc817fa5e14eaea76c0e7f18cc0447e630bfecd0393f3bafd3949dfd060000ffff0300504b03041400060008000000210023e57af1db000000030100000f0000006472732f646f776e7265762e786d6c4c8f4f4bc34010c5ef42bfc332056f76d356a4a699141104f14fa3553c6fb3d324989d8dd96d1bbf7d472f7a1978bc
c77bbfc956836bd581fad07846984e1250c4a5b70d5708ef6f77170b50211ab6a6f54c08df1460958fce32935a7fe4573a6c62a5a484436a10ea18bb54eb50d6e44c98f88e58bc9def9d8922fb4adbde1ca5dcb57a962457da998665a1361dddd6547e6ef60ec17f7c3dda62ed9eb52ed64fe5fde5fce5a160c4f3f170b304
1569887f61f8c11774c88569ebf76c836a11e491f87bc55b5c4f416d11e6c90c749ee9ffecf9090000ffff0300504b01022d0014000600080000002100b6833892fe000000e10100001300000000000000000000000000000000005b436f6e74656e745f54797065735d2e786d6c504b01022d001400060008000000210038
fd21ffd6000000940100000b000000000000000000000000002f0100005f72656c732f2e72656c73504b01022d001400060008000000210068ffc5d9bf020000ad0500000e000000000000000000000000002e0200006472732f65326f446f632e786d6c504b01022d001400060008000000210023e57af1db000000030100
000f00000000000000000000000000190500006472732f646f776e7265762e786d6c504b05060000000004000400f3000000210600000000}}}{\sp{\sn dhgt}{\sv 251659264}}{\sp{\sn fLayoutInCell}{\sv 1}}{\sp{\sn fAllowOverlap}{\sv 1}}
{\sp{\sn fBehindDocument}{\sv 0}}{\sp{\sn fHidden}{\sv 0}}{\sp{\sn sizerelv}{\sv 3}}{\sp{\sn fLayoutInCell}{\sv 1}}{\shptxt \ltrpar \pard\plain \ltrpar\qc \li0\ri0\sa200\sl276\slmult1\widctlpar\brdrt\brdrs\brdrw10\brsp20\brdrcf17 
\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0 \rtlch\fcs1 \af0\afs22\alang1025 \ltrch\fcs0 \f31506\fs22\lang1043\langfe1033\cgrid\langnp1043\langfenp1033 {\field{\*\fldinst {\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid1324745 PAGE   \\* 
MERGEFORMAT}}{\fldrslt {\rtlch\fcs1 \af0 \ltrch\fcs0 \cf18\lang1024\langfe1024\noproof\insrsid1324745\charrsid1324745 1}}}\sectd \ltrsect\linex0\headery708\footery708\colsx708\endnhere\sectdefaultcl\sftnbj {\rtlch\fcs1 \af0 \ltrch\fcs0 
\cf18\insrsid1324745 
\par }}}{\shprslt{\*\do\dobxmargin\dobymargin\dodhgt8192\dptxbx\dptxlrtb{\dptxbxtext\ltrpar \pard\plain \ltrpar\qc \li0\ri0\sa200\sl276\slmult1\widctlpar\brdrt\brdrs\brdrw10\brsp20\brdrcf17 \wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0 
\rtlch\fcs1 \af0\afs22\alang1025 \ltrch\fcs0 \f31506\fs22\lang1043\langfe1033\cgrid\langnp1043\langfenp1033 {\field{\*\fldinst {\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid1324745 PAGE   \\* MERGEFORMAT}}{\fldrslt {\rtlch\fcs1 \af0 \ltrch\fcs0 
\cf18\lang1024\langfe1024\noproof\insrsid1324745\charrsid1324745 1}}}\sectd \ltrsect\linex0\headery708\footery708\colsx708\endnhere\sectdefaultcl\sftnbj {\rtlch\fcs1 \af0 \ltrch\fcs0 \cf18\insrsid1324745 
\par }}\dpx0\dpy0\dpxsize891\dpysize302\dpfillfgcr255\dpfillfgcg255\dpfillfgcb255\dpfillbgcr192\dpfillbgcg80\dpfillbgcb77\dpfillpat0\dplinehollow}}}}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid1324745 
\par }}{\*\pnseclvl1\pnucrm\pnstart1\pnindent720\pnhang {\pntxta .}}{\*\pnseclvl2\pnucltr\pnstart1\pnindent720\pnhang {\pntxta .}}{\*\pnseclvl3\pndec\pnstart1\pnindent720\pnhang {\pntxta .}}{\*\pnseclvl4\pnlcltr\pnstart1\pnindent720\pnhang {\pntxta )}}
{\*\pnseclvl5\pndec\pnstart1\pnindent720\pnhang {\pntxtb (}{\pntxta )}}{\*\pnseclvl6\pnlcltr\pnstart1\pnindent720\pnhang {\pntxtb (}{\pntxta )}}{\*\pnseclvl7\pnlcrm\pnstart1\pnindent720\pnhang {\pntxtb (}{\pntxta )}}{\*\pnseclvl8
\pnlcltr\pnstart1\pnindent720\pnhang {\pntxtb (}{\pntxta )}}{\*\pnseclvl9\pnlcrm\pnstart1\pnindent720\pnhang {\pntxtb (}{\pntxta )}}\pard\plain \ltrpar\ql \li0\ri0\sa200\sl276\slmult1
\widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0\pararsid15424450 \rtlch\fcs1 \af0\afs22\alang1025 \ltrch\fcs0 \f31506\fs22\lang1043\langfe1033\cgrid\langnp1043\langfenp1033 {\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 
emailadres\tab \tab :\tab So.OrderdeskVermogensbeheer@nl.ABNAMRO.com\line Bestemd voor\tab \tab :\tab ABN AMRO Bank\line Ter attentie van\tab \tab:\tab Orderdesk\line Verzonden door\tab \tab:\tab <Gebruiker naam>\line Datum\tab \tab \tab :\tab <datum>}{
\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid3019055  }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 <tijd>
\par <aantal transacties> transactie(s)
\par ========= <transactiesoort volledig>        <ISIN>  ================ }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid3019055 <transactiesoort kort>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450  ======================
\par Instrument\tab \tab <fondsomschrijving>\line Transactiedatum\tab \tab <transactiedatum>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 \line SettlementDatum\tab <settlementdatum>\line Levering Totaal}{\rtlch\fcs1 
\af0 \ltrch\fcs0 \insrsid3019055 \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 \tab <aantal> \line Bedrag Totaal\tab \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid4085325 <fondsvaluta> <som nettobedrag>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 
\line Betreft\tab \tab \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid4085325 <betreft>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 \line Te leveren door\tab \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid4085325 <teLeverenDoor> }{\rtlch\fcs1 \af0 
\ltrch\fcs0 \insrsid15424450 \line Provisie\tab \tab \tab Nee \line Te ontvangen door\tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid4085325 <teOntvangenDoor> }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 \line }{\rtlch\fcs1 \af0 \ltrch\fcs0 
\insrsid15424450\charrsid6175622 Tlv Eff Depot\tab \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 <tlvEffDepot>\line }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid6175622 Tgv <fondsvaluta> Rek\tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 
\insrsid1255297 \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 <tlvRekening>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid4085325 
\par }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid2512742\charrsid2512742 Effecten franco overboeken en rekening van onderstaande <aantal transacties> clienten <crediterendebiteren> in Euro\'s}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid2512742 
\par }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 <clienten>
\par }\pard \ltrpar\ql \li0\ri0\sa200\sl276\slmult1\widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0 {\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid871659 
\par }{\*\themedata 504b030414000600080000002100e9de0fbfff0000001c020000130000005b436f6e74656e745f54797065735d2e786d6cac91cb4ec3301045f748fc83e52d4a
9cb2400825e982c78ec7a27cc0c8992416c9d8b2a755fbf74cd25442a820166c2cd933f79e3be372bd1f07b5c3989ca74aaff2422b24eb1b475da5df374fd9ad
5689811a183c61a50f98f4babebc2837878049899a52a57be670674cb23d8e90721f90a4d2fa3802cb35762680fd800ecd7551dc18eb899138e3c943d7e503b6
b01d583deee5f99824e290b4ba3f364eac4a430883b3c092d4eca8f946c916422ecab927f52ea42b89a1cd59c254f919b0e85e6535d135a8de20f20b8c12c3b0
0c895fcf6720192de6bf3b9e89ecdbd6596cbcdd8eb28e7c365ecc4ec1ff1460f53fe813d3cc7f5b7f020000ffff0300504b030414000600080000002100a5d6
a7e7c0000000360100000b0000005f72656c732f2e72656c73848fcf6ac3300c87ef85bd83d17d51d2c31825762fa590432fa37d00e1287f68221bdb1bebdb4f
c7060abb0884a4eff7a93dfeae8bf9e194e720169aaa06c3e2433fcb68e1763dbf7f82c985a4a725085b787086a37bdbb55fbc50d1a33ccd311ba548b6309512
0f88d94fbc52ae4264d1c910d24a45db3462247fa791715fd71f989e19e0364cd3f51652d73760ae8fa8c9ffb3c330cc9e4fc17faf2ce545046e37944c69e462
a1a82fe353bd90a865aad41ed0b5b8f9d6fd010000ffff0300504b0304140006000800000021006b799616830000008a0000001c0000007468656d652f746865
6d652f7468656d654d616e616765722e786d6c0ccc4d0ac3201040e17da17790d93763bb284562b2cbaebbf600439c1a41c7a0d29fdbd7e5e38337cedf14d59b
4b0d592c9c070d8a65cd2e88b7f07c2ca71ba8da481cc52c6ce1c715e6e97818c9b48d13df49c873517d23d59085adb5dd20d6b52bd521ef2cdd5eb9246a3d8b
4757e8d3f729e245eb2b260a0238fd010000ffff0300504b030414000600080000002100ab322f0ca8060000a71b0000160000007468656d652f7468656d652f
7468656d65312e786d6cec594d6f1b4518be23f11f467b6f6327761a4775aad8b11b4853a2d82dea71bc3bde9d66766735334eea1b6a8f484888823850891b07
04546a252ee5d7048aa048fd0bbc33b3bbde89d72469235a417d48bcb3cfbcdfef331fbe7aed5eccd0211192f2a4edd52fd73c44129f073409dbdead61ffd29a
87a4c24980194f48db9b12e95ddb78ffbdab785d45242608e627721db7bd48a9747d6949fa308ce5659e9204de8db988b18247112e05021f81dc982d2dd76aab
4b31a68987121c83d81d9c28ce851689bd8d5c788f818644493de03331d0a2893bc3808383ba86c8a9ec32810e316b7ba028e04743724f798861a9e045dbab99
8fb7b4717509af6793985a30b734af6f3ed9bc6c4270b06c748a705428adf71bad2b5b857c03606a1ed7ebf5babd7a21cf00b0ef83abd696b2cc467faddec965
9640f6ebbcec6ead596bb8f892fc95399b5b9d4ea7d9ca6cb1420dc87e6dcce1d76aab8dcd65076f4016df9cc3373a9bddeeaa8337208b5f9dc3f7afb4561b2e
de802246938339b44e68bf9f492f2063ceb62be16b005fab65f0190aaaa1282fad62cc13b5b0d8627c978b3e203492614513a4a62919631feab88be391a0586b
c0eb0497ded8215fce0d696548fa82a6aaed7d9862e88999bc97cf7e78f9ec093abefff4f8fecfc70f1e1cdfffc90a72666de3242ccf7af1dde77f3dfa04fdf9
e4db170fbfacc6cb32feb71f3ffdf5972faa81d03f33739e7ff5f8f7a78f9f7ffdd91fdf3fac806f0a3c2ac387342612dd2447689fc7e098898a6b391989f3cd
18469896676c26a1c409d65a2ae4f754e4a06f4e31cbb2e3d8d1216e046f0be08f2ae0f5c95dc7e04124268a5668de896207b8cb39eb705119851dadab14e6e1
2409ab958b4919b78ff16195ee2e4e9cfcf6262930675e968ee3dd883866ee3160621c928428a4dff103422abcbb43a913d75dea0b2ef958a13b147530ad0cc9
908e9c6a9a4ddaa631e4655ae533e4db89cdee6dd4e1accaeb2d72e822a12b30ab307e489813c6eb78a2705c25728863560ef80daca22a230753e197713da920
d321611cf5022265d59c8f04f85b4afa0e06caaa4cfb2e9bc62e52287a5025f306e6bc8cdce207dd08c76915764093a88cfd401e408962b4c755157c97bb1da2
9f210f385998eedb9438e93e9d0d6ed1d031695620facd4454e4f23ae14efd0ea66c8c89a11a607587ab639afc1371330acc6d355c1c7103553effe65185dd6f
2b656fc2ea55d533db27887a11ee243d77b908e8dbcfce5b7892ec116888f925ea1d39bf2367389ffcc7c979513f5f3c25cf5818085aef45ec4edbecbbe3c5db
ee31656ca0a68cdc9066e72d61f109fa30a8279a432729ce6169045f752b830607170a6ce620c1d5c754458308a7b06baf7b5a482833d1a1442997705c34c395
b2351e76feca1e369bfa1862a94362b5cb033bbca287f3d34621c658159a336dae68450b38abb2952b9950f0ed5594d5b55167d65637a6195674b4152eeb109b
733984bc700d068b6842e320d80b419457e1d8af55c369073312e8b8db1ce5693159b8c814c9080724cb91f67b3e477593a4bc56e61cd17ed862d047c753a256
d2d6d2625f43db59925456d758a02ecfdeeb6429afe0599640dac9766449b93959828eda5eabb9dcf4908fd3b6378683327c8d53c8bad41b49cc42b86ff295b0
657f6a339b2e9f65b3953be636411d2e3f6cdce71c76782015526d6119d9d230afb2126089d664ed5f6e42582fca810a363a9b152b6b500c6fcc0a88a39b5a32
1e135f95935d1ad1b1b38f1995f289226210054768c426621f43fa75a9823f019570df6118413fc0ed9c8eb679e59273d674e53b3183b3e398a511cee856b768
dec9166e08a9b0c13c95cc03df2a6d37ce9ddf15d3f217e44ab98cff67aee8f504ae1f56029d011f6e870546ba53da9ebe1fe6c0426944fdbe809d83e10ea816
b8e185d7505470476dfe0b72a8ffdb9eb3324c5bc32952edd310090aeb918a04217b404ba6fa4e1156cfd62e2b9265824c4595cc95a9357b440e091b6a0e5cd5
6bbb87222875c326190d18dcc9fa739fb30e1a857a9353ee3787c98ab5d7f6c0bfbdf3b1cd0c4eb93c6c363479fc0b138bedc16c55b5f3cdf47ced2d3ba25fcc
b6598dbc2b4059692968656dff8a269c73a9b58c35e7f17233370eb238ef310c161ba2142e9190fe03eb1f153e23a68cf5823ae4fbc0ad087ebed0c2a06ca0aa
2fd98d07d204690747b071b283b698b4281bda6ceba4a3962fd617bcd32df49e08b6b6ec2cf93e67b08bcd99abcee9c58b0c76166127d6766c61a821b3275b14
86c6f949c624c6fc5256fe318b8fee42a2b7e047830953d21413fc522530eca107a60fa0f9ad463375e36f000000ffff0300504b030414000600080000002100
0dd1909fb60000001b010000270000007468656d652f7468656d652f5f72656c732f7468656d654d616e616765722e786d6c2e72656c73848f4d0ac2301484f7
8277086f6fd3ba109126dd88d0add40384e4350d363f2451eced0dae2c082e8761be9969bb979dc9136332de3168aa1a083ae995719ac16db8ec8e4052164e89
d93b64b060828e6f37ed1567914b284d262452282e3198720e274a939cd08a54f980ae38a38f56e422a3a641c8bbd048f7757da0f19b017cc524bd62107bd500
1996509affb3fd381a89672f1f165dfe514173d9850528a2c6cce0239baa4c04ca5bbabac4df000000ffff0300504b01022d0014000600080000002100e9de0f
bfff0000001c0200001300000000000000000000000000000000005b436f6e74656e745f54797065735d2e786d6c504b01022d0014000600080000002100a5d6
a7e7c0000000360100000b00000000000000000000000000300100005f72656c732f2e72656c73504b01022d00140006000800000021006b799616830000008a
0000001c00000000000000000000000000190200007468656d652f7468656d652f7468656d654d616e616765722e786d6c504b01022d00140006000800000021
00ab322f0ca8060000a71b00001600000000000000000000000000d60200007468656d652f7468656d652f7468656d65312e786d6c504b01022d001400060008
00000021000dd1909fb60000001b0100002700000000000000000000000000b20900007468656d652f7468656d652f5f72656c732f7468656d654d616e616765722e786d6c2e72656c73504b050600000000050005005d010000ad0a00000000}
{\*\colorschememapping 3c3f786d6c2076657273696f6e3d22312e302220656e636f64696e673d225554462d3822207374616e64616c6f6e653d22796573223f3e0d0a3c613a636c724d
617020786d6c6e733a613d22687474703a2f2f736368656d61732e6f70656e786d6c666f726d6174732e6f72672f64726177696e676d6c2f323030362f6d6169
6e22206267313d226c743122207478313d22646b3122206267323d226c743222207478323d22646b322220616363656e74313d22616363656e74312220616363
656e74323d22616363656e74322220616363656e74333d22616363656e74332220616363656e74343d22616363656e74342220616363656e74353d22616363656e74352220616363656e74363d22616363656e74362220686c696e6b3d22686c696e6b2220666f6c486c696e6b3d22666f6c486c696e6b222f3e}
{\*\latentstyles\lsdstimax267\lsdlockeddef0\lsdsemihiddendef1\lsdunhideuseddef1\lsdqformatdef0\lsdprioritydef99{\lsdlockedexcept \lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority0 \lsdlocked0 Normal;
\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority9 \lsdlocked0 heading 1;\lsdqformat1 \lsdpriority9 \lsdlocked0 heading 2;\lsdqformat1 \lsdpriority9 \lsdlocked0 heading 3;\lsdqformat1 \lsdpriority9 \lsdlocked0 heading 4;
\lsdqformat1 \lsdpriority9 \lsdlocked0 heading 5;\lsdqformat1 \lsdpriority9 \lsdlocked0 heading 6;\lsdqformat1 \lsdpriority9 \lsdlocked0 heading 7;\lsdqformat1 \lsdpriority9 \lsdlocked0 heading 8;\lsdqformat1 \lsdpriority9 \lsdlocked0 heading 9;
\lsdpriority39 \lsdlocked0 toc 1;\lsdpriority39 \lsdlocked0 toc 2;\lsdpriority39 \lsdlocked0 toc 3;\lsdpriority39 \lsdlocked0 toc 4;\lsdpriority39 \lsdlocked0 toc 5;\lsdpriority39 \lsdlocked0 toc 6;\lsdpriority39 \lsdlocked0 toc 7;
\lsdpriority39 \lsdlocked0 toc 8;\lsdpriority39 \lsdlocked0 toc 9;\lsdqformat1 \lsdpriority35 \lsdlocked0 caption;\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority10 \lsdlocked0 Title;\lsdpriority1 \lsdlocked0 Default Paragraph Font;
\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority11 \lsdlocked0 Subtitle;\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority22 \lsdlocked0 Strong;\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority20 \lsdlocked0 Emphasis;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority59 \lsdlocked0 Table Grid;\lsdunhideused0 \lsdlocked0 Placeholder Text;\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority1 \lsdlocked0 No Spacing;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority60 \lsdlocked0 Light Shading;\lsdsemihidden0 \lsdunhideused0 \lsdpriority61 \lsdlocked0 Light List;\lsdsemihidden0 \lsdunhideused0 \lsdpriority62 \lsdlocked0 Light Grid;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority63 \lsdlocked0 Medium Shading 1;\lsdsemihidden0 \lsdunhideused0 \lsdpriority64 \lsdlocked0 Medium Shading 2;\lsdsemihidden0 \lsdunhideused0 \lsdpriority65 \lsdlocked0 Medium List 1;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority66 \lsdlocked0 Medium List 2;\lsdsemihidden0 \lsdunhideused0 \lsdpriority67 \lsdlocked0 Medium Grid 1;\lsdsemihidden0 \lsdunhideused0 \lsdpriority68 \lsdlocked0 Medium Grid 2;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority69 \lsdlocked0 Medium Grid 3;\lsdsemihidden0 \lsdunhideused0 \lsdpriority70 \lsdlocked0 Dark List;\lsdsemihidden0 \lsdunhideused0 \lsdpriority71 \lsdlocked0 Colorful Shading;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority72 \lsdlocked0 Colorful List;\lsdsemihidden0 \lsdunhideused0 \lsdpriority73 \lsdlocked0 Colorful Grid;\lsdsemihidden0 \lsdunhideused0 \lsdpriority60 \lsdlocked0 Light Shading Accent 1;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority61 \lsdlocked0 Light List Accent 1;\lsdsemihidden0 \lsdunhideused0 \lsdpriority62 \lsdlocked0 Light Grid Accent 1;\lsdsemihidden0 \lsdunhideused0 \lsdpriority63 \lsdlocked0 Medium Shading 1 Accent 1;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority64 \lsdlocked0 Medium Shading 2 Accent 1;\lsdsemihidden0 \lsdunhideused0 \lsdpriority65 \lsdlocked0 Medium List 1 Accent 1;\lsdunhideused0 \lsdlocked0 Revision;
\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority34 \lsdlocked0 List Paragraph;\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority29 \lsdlocked0 Quote;\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority30 \lsdlocked0 Intense Quote;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority66 \lsdlocked0 Medium List 2 Accent 1;\lsdsemihidden0 \lsdunhideused0 \lsdpriority67 \lsdlocked0 Medium Grid 1 Accent 1;\lsdsemihidden0 \lsdunhideused0 \lsdpriority68 \lsdlocked0 Medium Grid 2 Accent 1;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority69 \lsdlocked0 Medium Grid 3 Accent 1;\lsdsemihidden0 \lsdunhideused0 \lsdpriority70 \lsdlocked0 Dark List Accent 1;\lsdsemihidden0 \lsdunhideused0 \lsdpriority71 \lsdlocked0 Colorful Shading Accent 1;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority72 \lsdlocked0 Colorful List Accent 1;\lsdsemihidden0 \lsdunhideused0 \lsdpriority73 \lsdlocked0 Colorful Grid Accent 1;\lsdsemihidden0 \lsdunhideused0 \lsdpriority60 \lsdlocked0 Light Shading Accent 2;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority61 \lsdlocked0 Light List Accent 2;\lsdsemihidden0 \lsdunhideused0 \lsdpriority62 \lsdlocked0 Light Grid Accent 2;\lsdsemihidden0 \lsdunhideused0 \lsdpriority63 \lsdlocked0 Medium Shading 1 Accent 2;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority64 \lsdlocked0 Medium Shading 2 Accent 2;\lsdsemihidden0 \lsdunhideused0 \lsdpriority65 \lsdlocked0 Medium List 1 Accent 2;\lsdsemihidden0 \lsdunhideused0 \lsdpriority66 \lsdlocked0 Medium List 2 Accent 2;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority67 \lsdlocked0 Medium Grid 1 Accent 2;\lsdsemihidden0 \lsdunhideused0 \lsdpriority68 \lsdlocked0 Medium Grid 2 Accent 2;\lsdsemihidden0 \lsdunhideused0 \lsdpriority69 \lsdlocked0 Medium Grid 3 Accent 2;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority70 \lsdlocked0 Dark List Accent 2;\lsdsemihidden0 \lsdunhideused0 \lsdpriority71 \lsdlocked0 Colorful Shading Accent 2;\lsdsemihidden0 \lsdunhideused0 \lsdpriority72 \lsdlocked0 Colorful List Accent 2;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority73 \lsdlocked0 Colorful Grid Accent 2;\lsdsemihidden0 \lsdunhideused0 \lsdpriority60 \lsdlocked0 Light Shading Accent 3;\lsdsemihidden0 \lsdunhideused0 \lsdpriority61 \lsdlocked0 Light List Accent 3;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority62 \lsdlocked0 Light Grid Accent 3;\lsdsemihidden0 \lsdunhideused0 \lsdpriority63 \lsdlocked0 Medium Shading 1 Accent 3;\lsdsemihidden0 \lsdunhideused0 \lsdpriority64 \lsdlocked0 Medium Shading 2 Accent 3;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority65 \lsdlocked0 Medium List 1 Accent 3;\lsdsemihidden0 \lsdunhideused0 \lsdpriority66 \lsdlocked0 Medium List 2 Accent 3;\lsdsemihidden0 \lsdunhideused0 \lsdpriority67 \lsdlocked0 Medium Grid 1 Accent 3;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority68 \lsdlocked0 Medium Grid 2 Accent 3;\lsdsemihidden0 \lsdunhideused0 \lsdpriority69 \lsdlocked0 Medium Grid 3 Accent 3;\lsdsemihidden0 \lsdunhideused0 \lsdpriority70 \lsdlocked0 Dark List Accent 3;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority71 \lsdlocked0 Colorful Shading Accent 3;\lsdsemihidden0 \lsdunhideused0 \lsdpriority72 \lsdlocked0 Colorful List Accent 3;\lsdsemihidden0 \lsdunhideused0 \lsdpriority73 \lsdlocked0 Colorful Grid Accent 3;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority60 \lsdlocked0 Light Shading Accent 4;\lsdsemihidden0 \lsdunhideused0 \lsdpriority61 \lsdlocked0 Light List Accent 4;\lsdsemihidden0 \lsdunhideused0 \lsdpriority62 \lsdlocked0 Light Grid Accent 4;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority63 \lsdlocked0 Medium Shading 1 Accent 4;\lsdsemihidden0 \lsdunhideused0 \lsdpriority64 \lsdlocked0 Medium Shading 2 Accent 4;\lsdsemihidden0 \lsdunhideused0 \lsdpriority65 \lsdlocked0 Medium List 1 Accent 4;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority66 \lsdlocked0 Medium List 2 Accent 4;\lsdsemihidden0 \lsdunhideused0 \lsdpriority67 \lsdlocked0 Medium Grid 1 Accent 4;\lsdsemihidden0 \lsdunhideused0 \lsdpriority68 \lsdlocked0 Medium Grid 2 Accent 4;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority69 \lsdlocked0 Medium Grid 3 Accent 4;\lsdsemihidden0 \lsdunhideused0 \lsdpriority70 \lsdlocked0 Dark List Accent 4;\lsdsemihidden0 \lsdunhideused0 \lsdpriority71 \lsdlocked0 Colorful Shading Accent 4;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority72 \lsdlocked0 Colorful List Accent 4;\lsdsemihidden0 \lsdunhideused0 \lsdpriority73 \lsdlocked0 Colorful Grid Accent 4;\lsdsemihidden0 \lsdunhideused0 \lsdpriority60 \lsdlocked0 Light Shading Accent 5;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority61 \lsdlocked0 Light List Accent 5;\lsdsemihidden0 \lsdunhideused0 \lsdpriority62 \lsdlocked0 Light Grid Accent 5;\lsdsemihidden0 \lsdunhideused0 \lsdpriority63 \lsdlocked0 Medium Shading 1 Accent 5;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority64 \lsdlocked0 Medium Shading 2 Accent 5;\lsdsemihidden0 \lsdunhideused0 \lsdpriority65 \lsdlocked0 Medium List 1 Accent 5;\lsdsemihidden0 \lsdunhideused0 \lsdpriority66 \lsdlocked0 Medium List 2 Accent 5;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority67 \lsdlocked0 Medium Grid 1 Accent 5;\lsdsemihidden0 \lsdunhideused0 \lsdpriority68 \lsdlocked0 Medium Grid 2 Accent 5;\lsdsemihidden0 \lsdunhideused0 \lsdpriority69 \lsdlocked0 Medium Grid 3 Accent 5;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority70 \lsdlocked0 Dark List Accent 5;\lsdsemihidden0 \lsdunhideused0 \lsdpriority71 \lsdlocked0 Colorful Shading Accent 5;\lsdsemihidden0 \lsdunhideused0 \lsdpriority72 \lsdlocked0 Colorful List Accent 5;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority73 \lsdlocked0 Colorful Grid Accent 5;\lsdsemihidden0 \lsdunhideused0 \lsdpriority60 \lsdlocked0 Light Shading Accent 6;\lsdsemihidden0 \lsdunhideused0 \lsdpriority61 \lsdlocked0 Light List Accent 6;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority62 \lsdlocked0 Light Grid Accent 6;\lsdsemihidden0 \lsdunhideused0 \lsdpriority63 \lsdlocked0 Medium Shading 1 Accent 6;\lsdsemihidden0 \lsdunhideused0 \lsdpriority64 \lsdlocked0 Medium Shading 2 Accent 6;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority65 \lsdlocked0 Medium List 1 Accent 6;\lsdsemihidden0 \lsdunhideused0 \lsdpriority66 \lsdlocked0 Medium List 2 Accent 6;\lsdsemihidden0 \lsdunhideused0 \lsdpriority67 \lsdlocked0 Medium Grid 1 Accent 6;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority68 \lsdlocked0 Medium Grid 2 Accent 6;\lsdsemihidden0 \lsdunhideused0 \lsdpriority69 \lsdlocked0 Medium Grid 3 Accent 6;\lsdsemihidden0 \lsdunhideused0 \lsdpriority70 \lsdlocked0 Dark List Accent 6;
\lsdsemihidden0 \lsdunhideused0 \lsdpriority71 \lsdlocked0 Colorful Shading Accent 6;\lsdsemihidden0 \lsdunhideused0 \lsdpriority72 \lsdlocked0 Colorful List Accent 6;\lsdsemihidden0 \lsdunhideused0 \lsdpriority73 \lsdlocked0 Colorful Grid Accent 6;
\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority19 \lsdlocked0 Subtle Emphasis;\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority21 \lsdlocked0 Intense Emphasis;
\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority31 \lsdlocked0 Subtle Reference;\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority32 \lsdlocked0 Intense Reference;
\lsdsemihidden0 \lsdunhideused0 \lsdqformat1 \lsdpriority33 \lsdlocked0 Book Title;\lsdpriority37 \lsdlocked0 Bibliography;\lsdqformat1 \lsdpriority39 \lsdlocked0 TOC Heading;}}{\*\datastore 010500000200000018000000
4d73786d6c322e534158584d4c5265616465722e362e30000000000000000000000e0000
d0cf11e0a1b11ae1000000000000000000000000000000003e000300feff0900060000000000000000000000010000000100000000000000001000000200000001000000feffffff0000000000000000ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
fffffffffffffffffdffffff04000000feffffff05000000fefffffffeffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
ffffffffffffffffffffffffffffffff52006f006f007400200045006e00740072007900000000000000000000000000000000000000000000000000000000000000000000000000000000000000000016000500ffffffffffffffff010000000c6ad98892f1d411a65f0040963251e500000000000000000000000090d2
f7cc73b3d2010300000080020000000000004d0073006f004400610074006100530074006f0072006500000000000000000000000000000000000000000000000000000000000000000000000000000000001a000101ffffffffffffffff020000000000000000000000000000000000000000000000605df7cc73b3d201
605df7cc73b3d201000000000000000000000000da004b005000dd0046004100cf005600d900c4005700dc00ce005700d8003100cd005400dc00db00490041003d003d000000000000000000000000000000000032000101ffffffffffffffff030000000000000000000000000000000000000000000000605df7cc73b3
d201605df7cc73b3d2010000000000000000000000004900740065006d0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000a000201ffffffff04000000ffffffff000000000000000000000000000000000000000000000000
00000000000000000000000000000000cd00000000000000010000000200000003000000feffffff0500000006000000070000000800000009000000feffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff3c623a536f757263657320786d6c6e733a623d22687474703a2f2f736368656d61732e6f70656e786d6c666f726d6174732e6f72672f6f6666696365446f63756d656e742f323030362f6269626c696f6772617068792220786d6c6e733d
22687474703a2f2f736368656d61732e6f70656e786d6c666f726d6174732e6f72672f6f6666696365446f63756d656e742f323030362f6269626c696f677261706879222053656c65637465645374796c653d225c4150412e58534c22205374796c654e616d653d22415041222f3e000000000000000000000000000000
0000000000000000000000000000000000000000000000000000000000000000000000003c3f786d6c2076657273696f6e3d22312e302220656e636f64696e673d225554462d3822207374616e64616c6f6e653d226e6f223f3e0d0a3c64733a6461746173746f72654974656d2064733a6974656d49443d227b31344644
413345382d443530422d343545362d424342392d3645314242353346334232307d2220786d6c6e733a64733d22687474703a2f2f736368656d61732e6f70656e786d6c666f726d6174732e6f72672f6f6666696365446f63756d656e742f323030362f637573746f6d586d6c223e3c64733a736368656d61526566733e3c
64733a736368656d615265662064733a7572693d22687474703a2f2f736368656d61732e6f70656e500072006f007000650072007400690065007300000000000000000000000000000000000000000000000000000000000000000000000000000000000000000016000200ffffffffffffffffffffffff000000000000
0000000000000000000000000000000000000000000000000000000000000400000055010000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000ffffffffffffffffffffffff00000000
00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000ffffffffffffffffffffffff0000
000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000ffffffffffffffffffffffff
000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000786d6c666f726d6174732e6f72672f6f6666696365446f63756d656e742f323030362f6269626c696f677261706879222f3e3c2f64733a736368656d61526566733e3c2f64733a6461746173746f
72654974656d3e0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000105000000000000}}';

$rtfClient='\par }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid4291646 -------------------------------------------------<ordernummer client 1>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450  }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid4291646 
------------------------------------------
\par }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid791664 Onze referentie\tab \tab <ordernummer client 1>\line Client\tab \tab \tab 
<portefeuille> <client>\line Transactiedatum\tab <transactiedatum>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450  }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid791664 \line Client }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450  }{\rtlch\fcs1 
\af0 \ltrch\fcs0 \insrsid15424450\charrsid791664 ontvangt }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid7168094 \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid791664 <aantal>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 \line }{\rtlch\fcs1 \af0 
\ltrch\fcs0 \insrsid15424450\charrsid791664 ISIN\tab \tab \tab <isin>\line Naam\tab \tab \tab <fondsomschrijving>\line Koers\tab \tab \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 <uitvoeringsprijs>}{\rtlch\fcs1 \af0 \ltrch\fcs0 
\insrsid15424450\charrsid791664 \line Bedrag\tab \tab \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 <afrekenvaluta> }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid791664 <netto bedrag>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450  \line 
}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid791664 Instructie\tab \tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid7168094 <instructie>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 \line }{\rtlch\fcs1 \af0 \ltrch\fcs0 
\insrsid15424450\charrsid791664 Settlementdatum\tab }{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450 <settlementdatum>}{\rtlch\fcs1 \af0 \ltrch\fcs0 \insrsid15424450\charrsid791664';

$rtf=new orderRTF($_POST['orderId']);

$rtfTemplate=$rtf->template($rtfTemplate,$rtfClient);

//listarray($rtf);


//file_put_contents('test.rtf',$rtfTemplate );
$exportStamp=$__appvar['bedrijf'].date('Ymd_Hi');
$filename='Order'.$exportStamp.'.rtf';
$appType = "application/rtf";
header('Content-type: ' . $appType);
header("Content-Length: ".strlen($rtfTemplate));
header("Content-Disposition: inline; filename=\"".$filename."\"");
header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
echo $rtfTemplate;


?>