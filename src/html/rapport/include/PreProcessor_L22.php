<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/03/01 09:53:45 $
 		File Versie					: $Revision: 1.5 $

 		$Log: PreProcessor_L22.php,v $
 		Revision 1.5  2020/03/01 09:53:45  rvv
 		*** empty log message ***
 		
 	


*/

class PreProcessor_L22
{
	function PreProcessor_L22($portefeuille,$datum='',$pdf)
  {
    global $__appvar;
    $this->portefeuille = $portefeuille;
	  $this->db = new DB();
    $this->categorieData = array();
    $this->pdf = $pdf;
    $dagen=array();
    if($datum <> '')
    {
      $dagen[]=$datum;
      $query="SELECT rapportageDatum,round(sum(actuelePortefeuilleWaardeEuro)) as waardeEUR,round(sum(totaalAantal)) as aantal
              FROM TijdelijkeRapportage WHERE portefeuille = '$portefeuille' ".$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY rapportageDatum";
      $this->db->SQL($query);
      $this->db->Query();
      while($data=$this->db->nextRecord())
      {
        $waardenPerDag[$data['rapportageDatum']]=$data;
      }
    }
    else
    {
	    $query="SELECT rapportageDatum,round(sum(actuelePortefeuilleWaardeEuro)) as waardeEUR,round(sum(totaalAantal)) as aantal
              FROM TijdelijkeRapportage WHERE portefeuille = '$portefeuille' ".$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY rapportageDatum";
	    $this->db->SQL($query);
	    $this->db->Query();
	    while($data=$this->db->nextRecord())
	    {
        $waardenPerDag[$data['rapportageDatum']]=$data;
	      $dagen[]=$data['rapportageDatum'];
	    }
    }

	  foreach ($dagen as $dag)
	  {
	    $this->optieCategorienBepalen($dag);
      $this->update($dag);
     // $this->verwijderKoppelingen($dag);
	    
	  }


      $query = "SELECT rapportageDatum,round(sum(actuelePortefeuilleWaardeEuro)) as waardeEUR,round(sum(totaalAantal)) as aantal
              FROM TijdelijkeRapportage WHERE portefeuille = '$portefeuille' " . $__appvar['TijdelijkeRapportageMaakUniek'] . " GROUP BY rapportageDatum";
      $this->db->SQL($query);
      $this->db->Query();
      while ($data = $this->db->nextRecord())
      {
        $waardenPerDagAfter[$data['rapportageDatum']] = $data;
      }

    foreach($waardenPerDag as $datum=>$waarden)
    {
      if($waarden['waardeEUR'] <> $waardenPerDagAfter[$datum]['waardeEUR'])
      {
        echo "$portefeuille $datum waardeEUR ".$waarden['waardeEUR']." <> ".$waardenPerDagAfter[$datum]['waardeEUR']."<br>\n";
      }
      if($waarden['aantal'] <> $waardenPerDagAfter[$datum]['aantal'])
      {
        echo "$portefeuille $datum aantal ".$waarden['aantal']." <> ".$waardenPerDagAfter[$datum]['aantal']."<br>\n";
      }
    }
  //listarray($waardenPerDag);
  //listarray($waardenPerDagAfter);

//exit;
  }

  function optieCategorienBepalen($dag)
  {
    global $__appvar;
    $optieCategorien=array('AAND','BEW');//'AAND-KO','AAND-LO',
    $query="SELECT Beleggingscategorie,Omschrijving,Afdrukvolgorde FROM Beleggingscategorien WHERE Beleggingscategorie IN('".implode("','",$optieCategorien)."')";
    $this->db->SQL($query);
	  $this->db->Query();
	  while($data=$this->db->nextRecord())
      $categorieData[$data['Beleggingscategorie']]=$data;
    $this->categorieData=$categorieData;
    
    $vasteWhere="TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$dag' AND beleggingscategorie='BEW' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    $query="SELECT TijdelijkeRapportage.id,
Fondsen.OptieExpDatum,
Fondsen.OptieBovenliggendFonds,TijdelijkeRapportage.fonds,
TijdelijkeRapportage.totaalAantal
FROM
TijdelijkeRapportage
Inner Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
WHERE Fondsen.OptieBovenliggendFonds <> '' AND $vasteWhere";
	  $this->db->SQL($query);
	  $this->db->Query(); 
    $queries=array();
	  while($data=$this->db->nextRecord())
	  {
	   //if(db2jul(substr($data['OptieExpDatum'],0,4)."-".substr($data['OptieExpDatum'],4,2)."-15") < (db2jul($dag)+(365*24*3600)))
     //  $cat="AAND-KO";
     //else
     //  $cat="AAND-LO";

      $cat="BEW";
     //echo $data['fonds']." $cat <br>\n";
     $queries[]="UPDATE TijdelijkeRapportage SET 
     beleggingscategorie='".$categorieData[$cat]['Beleggingscategorie']."',
     beleggingscategorieOmschrijving='".$categorieData[$cat]['Omschrijving']."',
     beleggingscategorieVolgorde='".$categorieData[$cat]['Afdrukvolgorde']."',
     fondspaar=100
     WHERE id='".$data['id']."'";
    }
    foreach($queries as $query)
    {
   	  $this->db->SQL($query); 
	    $this->db->Query();    
    }
    unset($queries);
  }
  
  function verwijderKoppelingen($dag)
  {
    global $__appvar;

    $cat='AAND';
    $query="UPDATE TijdelijkeRapportage SET 
     beleggingscategorie='". $this->categorieData[$cat]['Beleggingscategorie']."',
     beleggingscategorieOmschrijving='". $this->categorieData[$cat]['Omschrijving']."',
     beleggingscategorieVolgorde='". $this->categorieData[$cat]['Afdrukvolgorde']."',
     TijdelijkeRapportage.add_date=now() WHERE fondspaar=100 AND
    TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$dag' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    $this->db->SQL($query);
    $this->db->Query();

    $velden=array('regio','beleggingssector');
    $veldenSql='';
    foreach ($velden as $veld)
      $veldenSql.="TijdelijkeRapportage.".$veld."='', TijdelijkeRapportage.".$veld."Volgorde='', TijdelijkeRapportage.".$veld."Omschrijving='', ";
    
    $query="UPDATE TijdelijkeRapportage SET $veldenSql TijdelijkeRapportage.add_date=now() WHERE beleggingscategorie <> 'AAND' AND
    TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$dag' ".$__appvar['TijdelijkeRapportageMaakUniek'];
	  $this->db->SQL($query);
	  $this->db->Query();


  }

  function update($dag)
  {
    global $__appvar;
    $velden=array('beleggingscategorie','hoofdcategorie','regio','beleggingssector');
    $select='';
    foreach ($velden as $veld)
      $select.="TijdelijkeRapportage.".$veld.", TijdelijkeRapportage.".$veld."Volgorde, TijdelijkeRapportage.".$veld."Omschrijving, ";

    $vasteWhere="TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$dag' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    $query="SELECT TijdelijkeRapportage.id, $select
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.portefeuille,
TijdelijkeRapportage.Fonds,
TijdelijkeRapportage.rapportageDatum,
TijdelijkeRapportage.beleggingscategorie,
Fondsen.Fondseenheid,
Fondsen.OptieType,
Fondsen.OptieExpDatum,
Fondsen.OptieUitoefenPrijs,
Fondsen.OptieBovenliggendFonds,
TijdelijkeRapportage.totaalAantal,
if(Fondsen.OptieType='C',if(TijdelijkeRapportage.beleggingscategorie='AAND-KO',1,4),2) as volgorde
FROM
TijdelijkeRapportage
Inner Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
WHERE $vasteWhere
ORDER BY volgorde,beleggingscategorie,OptieType,OptieExpDatum,OptieUitoefenPrijs ";
	  $this->db->SQL($query);
	  $this->db->Query();

	  while($data=$this->db->nextRecord())
	  {
	    $toevoegen=true;
	    if($data['OptieBovenliggendFonds'] <> '')
      {
        //echo $dag." ".$data['OptieBovenliggendFonds']." ".$data['OptieExpDatum']."<br>\n";
        $volgorde=$data['volgorde'].'|'.$data['OptieType'].'|'.$data['beleggingscategorie'];
        if(isset($opties[$data['OptieBovenliggendFonds']][$volgorde][$data['OptieUitoefenPrijs']."|".$data['OptieExpDatum']]))
        {
          foreach($opties[$data['OptieBovenliggendFonds']][$volgorde][$data['OptieUitoefenPrijs']."|".$data['OptieExpDatum']] as $vorigeOptie=>$vorigeOptieData)
          {
            if(abs($vorigeOptieData['totaalAantal'])== abs($data['totaalAantal']))
              $toevoegen=false; //Gelijk aantal op dezelfde expDatum niet toevoegen.
            //echo "$dag ".$data['OptieBovenliggendFonds']." ".$data['Fonds']." $vorigeOptie ".$vorigeOptieData['totaalAantal']." = ".$data['totaalAantal']." ? <br>\n";
          }
        }

        if($toevoegen==true)
          $opties[$data['OptieBovenliggendFonds']][$volgorde][$data['OptieUitoefenPrijs']."|".$data['OptieExpDatum']][$data['Fonds']]=$data;
          
      }
	    else
	      $fondsen[$data['Fonds']]=$data;
	  }


    $aandeelPutCall=array();
    $gewensteKoppelingen=array();
    $paarId=1;
    foreach($fondsen as $fonds=>$fondsData)
    {
      if(isset($opties[$fonds]))
      {
       // echo $fonds."<br>\n";
      //  listarray($opties[$fonds]);
        foreach ($opties[$fonds] as $volgorde =>$uitoefenPrijsData)
        {
            $volgordeParts=explode('|',$volgorde);
            $optieType=$volgordeParts[1];
            $beleggingscategorie=$volgordeParts[2];
            if($beleggingscategorie=='AAND-LO' && $optieType=='P')
              krsort($uitoefenPrijsData);
            else
              ksort($uitoefenPrijsData);
            foreach ($uitoefenPrijsData as $uitoefenPrijs=>$optieRecords)
            {
              foreach ($optieRecords as $optie => $optieData)
              {
                //listarray($optieData);
                $aantal=round(abs($optieData['totaalAantal']*$optieData['Fondseenheid']));
                if(($fondsData['totaalAantal'] - $aantal) >= 0)
                {
                  if($beleggingscategorie=='AAND-KO' && $optieType=='C') // kortlopende calls gaan voor. Gelijk bij aandelen in mindering brengen.
                  {
                    $gewensteKoppelingen[$fonds][1][$paarId]=array($fondsData['fondsOmschrijving'],$fondsData['totaalAantal'],$optieData['fondsOmschrijving'],$optieData['totaalAantal']);
                    $this->updateRegel($fondsData,$optieData,$paarId,$dag);
                    $paarId++;
                    $fondsData['totaalAantal'] -= $aantal;
                    $fondsen[$fonds]['totaalAantal']= $fondsData['totaalAantal'];
                    unset($opties[$fonds][$volgorde][$uitoefenPrijs][$optie]);

                  }
                  elseif($beleggingscategorie=='AAND-LO')
                  {
                    $aandeelPutCall[$fonds."|".$optieData['OptieExpDatum']]['fonds']=$fondsData['totaalAantal'];
                    $aandeelPutCall[$fonds."|".$optieData['OptieExpDatum']][$optieType]+=$aantal;
                  }
                }
              }
            }
          }
      }
    }
//listarray($aandeelPutCall);

    foreach($aandeelPutCall as $fondsExp=>$koppelingen)
    {
      if(count($koppelingen)==3)
      {
        $min=min($koppelingen);
        foreach($koppelingen as $type=>$aantal)
           $aandeelPutCall[$fondsExp][$type]=$min;
      }
      else
      {
        unset($aandeelPutCall[$fondsExp]);
      }
    }

    if(count($aandeelPutCall)>0)
    {
       foreach($aandeelPutCall as $fondsExp=>$members)
       {
         $tmp=explode('|',$fondsExp);
         $fonds=$tmp[0];
         $exp=$tmp[1];
         //$opties['forceerFondsAantal']=true;
         $fondsData=$fondsen[$fonds];

         $gewensteKoppelingen[$fondsExp][2][$paarId]['F'][$fondsData['id']]=array('Fonds'=>$fonds,
                                                                               'totaalAantal'=>$fondsData['totaalAantal'],
                                                                               'totaalAantalGebruikt'=>$members['fonds']);
         $fondsData['totaalAantal']=$members['fonds'];
         $aantal=$fondsData['totaalAantal']*$fondsData['Fondseenheid'];
         $fondsData['totaalAantal'] -= $aantal;
         $fondsen[$fonds]['totaalAantal']-= $aantal;


         foreach ($opties[$fonds] as $volgorde =>$uitoefenPrijsData)
         {
          // $setting=array();
           $volgordeParts=explode('|',$volgorde);
           $optieType=$volgordeParts[1];
           $beleggingscategorie=$volgordeParts[2];
           if($beleggingscategorie=='AAND-LO' && $optieType=='P')
             ksort($uitoefenPrijsData);
           else
             krsort($uitoefenPrijsData);

         //  echo $optieType." ".$beleggingscategorie;
         //  listarray($uitoefenPrijsData);
           foreach ($uitoefenPrijsData as $uitoefenPrijs=>$optieRecords)
           {
             foreach ($optieRecords as $optie => $optieData)
             {
               if($exp<> $optieData['OptieExpDatum'])
                 continue;
               if($members[$optieType]>0) // hebben we nog aandelen en opties nodig voor dit optiepaar?
               {
                 $aantal=round(abs($optieData['totaalAantal']*$optieData['Fondseenheid']));
                 if($aantal > $members[$optieType])
                 {
                   $restantOptie=($aantal-$members[$optieType])/$optieData['Fondseenheid'];
                   $optieGebruiktAantal=$members[$optieType]/$optieData['Fondseenheid'];
                   if($optieData['totaalAantal'] < 0)
                   {
                     $restantOptie = $restantOptie * -1;
                     $optieGebruiktAantal = $optieGebruiktAantal * -1;

                   }
                   $aantal = $members[$optieType];
                  // echo "1 $fonds | $optie | $aantal | $restantOptie<br>\n";
                 }
                 else
                 {
                   $optieGebruiktAantal = $optieData['totaalAantal'];
                   $restantOptie=0;
                 }
                 $optieData['totaalAantalGebruikt']=$optieGebruiktAantal;
                 $gewensteKoppelingen[$fondsExp][2][$paarId]['O'][$optieData['id']]=array('Fonds'=>$optieData['Fonds'],
                                                                                       'totaalAantal'=>$optieData['totaalAantal'],
                                                                                       'totaalAantalGebruikt'=>$optieData['totaalAantalGebruikt']);

                // $this->updateRegel($fondsen[$fonds],$optieData,$paarId,$dag,$setting);
                // $setting['skipFondsAantal']=true;
                 //$fondsData['totaalAantal'] -= $aantal; // is in het begin van de loop al gedaan.
                 //$fondsen[$fonds]['totaalAantal'] -= $aantal; //

                 $members[$optieType]=$members[$optieType]-$aantal;
                 if($restantOptie<>0)
                 {
                   $optieData['totaalAantal']=$restantOptie;
                   $opties[$fonds][$volgorde][$uitoefenPrijs][$optie]=$optieData;
                 }
                 else
                 {
                   //echo "unset (opties[$fonds][$volgorde][$uitoefenPrijs][$optie] <br>\n";
                   unset($opties[$fonds][$volgorde][$uitoefenPrijs][$optie]);
                 }

               }
             }
           }
         }

         $this->koppelDrie($gewensteKoppelingen[$fondsExp][2]);
         $paarId++;

       }
    }
//    listarray($fondsen);
//listarray($opties);

    foreach($fondsen as $fonds=>$fondsData)
    {

      if(isset($opties[$fonds]))
      {
        // echo $fonds."<br>\n";
        //  listarray($opties[$fonds]);
        foreach ($opties[$fonds] as $volgorde =>$uitoefenPrijsData)
        {
          $volgordeParts=explode('|',$volgorde);
          $optieType=$volgordeParts[1];
          $beleggingscategorie=$volgordeParts[2];
          if($beleggingscategorie=='AAND-LO' && $optieType=='P')
            krsort($uitoefenPrijsData);
          else
            ksort($uitoefenPrijsData);
          foreach ($uitoefenPrijsData as $uitoefenPrijs=>$optieRecords)
          {
            foreach ($optieRecords as $optie => $optieData)
            {
              $aantal=round(abs($optieData['totaalAantal']*$optieData['Fondseenheid']));
              //echo "check $volgorde Koppelen $fonds ".$optieData['Fonds']." (".$fondsData['totaalAantal']." - $aantal) <br>\n";
              if(($fondsData['totaalAantal'] - $aantal) >= 0)
              {
              //   echo "$volgorde Koppelen $fonds | ".$optieData['Fonds']." (".$fondsData['totaalAantal']." - $aantal) ".$optieData['totaalAantal']." <br>\n";
               // $optieData['totaalAantalGebruikt']+=($fondsData['totaalAantal'] - $aantal)/$optieData['Fondseenheid'];
                $optieData['totaalAantalGebruikt']=0;
                $this->updateRegel($fondsData,$optieData,$paarId,$dag);

                $paarId++;
                if(($fondsData['totaalAantal'] - $aantal) == 0)
                  unset($opties[$fonds][$volgorde][$uitoefenPrijs]);
                else
                  $opties[$fonds][$volgorde][$uitoefenPrijs]['totaalAantal']-=($aantal/$optieData['Fondseenheid']);

                $fondsData['totaalAantal'] -= $aantal;
                $fondsen[$fonds]['totaalAantal']= $fondsData['totaalAantal'];
              }
            }
          }
        }
      }
    }
  }

  function koppelDrie($koppelData)
  {// listarray($koppelData);
    foreach($koppelData as $paarId=>$paarData)
    {
      $fondsDataArray=$paarData['F'];
      $optieDataArray=$paarData['O'];
      foreach($optieDataArray as $id=>$optieData)
      {
        if($optieData['totaalAantal']<>$optieData['totaalAantalGebruikt'])
        {
          $newOptie=array('totaalAantal'=>$optieData['totaalAantalGebruikt']);
         // listarray($optieData);
          $tmp=$this->splitsRegel($id,$newOptie,$paarId);
         // listarray($tmp);
        }
        else
        {
          $tmp=$this->KoppelPaarId($id,$paarId);
        }
      }
      foreach($fondsDataArray as $id=>$fondsData)
      {
        if($fondsData['totaalAantal']<>$fondsData['totaalAantalGebruikt'])
        {
          $newFonds=array('totaalAantal'=>$fondsData['totaalAantalGebruikt']);
          $velden=array('beleggingscategorie','hoofdcategorie','beleggingssector');
          $veldDetail=array('','Volgorde','Omschrijving');
          foreach ($velden as $veld)
          {
            foreach($veldDetail as $detail)
                $newFonds[$veld.$detail]=$tmp[$veld.$detail];
          }
        //  listarray($newFonds);
          $this->splitsRegel($id,$newFonds,$paarId);
        }
        else
        {
          $this->KoppelPaarId($id, $paarId, $tmp);
        }
      }


    }
  }

  function KoppelPaarId($id,$paarId,$extraData='')
  {
     $set='';
     if(is_array($extraData))
     {
       $velden=array('beleggingscategorie','hoofdcategorie','regio','beleggingssector');

       foreach ($velden as $veld)
       {
         $set .= "TijdelijkeRapportage." . $veld . "='" . $extraData[$veld] . "',
                       TijdelijkeRapportage." . $veld . "Volgorde='" . $extraData[$veld . 'Volgorde'] . "',
                       TijdelijkeRapportage." . $veld . "Omschrijving='" . $extraData[$veld . 'Omschrijving'] . "', ";
       }
     }


    $query="UPDATE TijdelijkeRapportage SET $set add_date=now(),fondspaar='$paarId' WHERE  id='".$id."'";
    $this->db->SQL($query); //echo $query."<br>\n<br>\n";
    $this->db->Query();
    $query="SELECT beleggingscategorie,beleggingscategorieVolgorde,beleggingscategorieOmschrijving,hoofdcategorie,hoofdcategorieVolgorde,hoofdcategorieOmschrijving,regio,regioVolgorde,regioOmschrijving ,beleggingssector ,beleggingssectorVolgorde,beleggingssectorOmschrijving ,fondsOmschrijving,portefeuille,Fonds,rapportageDatum,Fondseenheid,totaalAantal FROM TijdelijkeRapportage WHERE id='$id'";
    $this->db->SQL($query);
    $data=$this->db->lookupRecord();
    return $data;

  }

  function splitsRegel($oldId,$newData,$paarId)
  {
    $query="SELECT * FROM TijdelijkeRapportage WHERE id='".$oldId."'";
    $this->db->SQL($query);
    $this->db->Query();
    $fondsRecord=$this->db->lookupRecord();
    unset($fondsRecord['id']);
    unset($fondsRecord['add_date']);


    $beginValutaKoers=$fondsRecord['beginPortefeuilleWaardeInValuta']/$fondsRecord['beginwaardeLopendeJaar']/$fondsRecord['totaalAantal']/$fondsRecord['fondsEenheid'];
    $fondsRecord['totaalAantal']=$fondsRecord['totaalAantal']-$newData['totaalAantal'];
    $fondsRecord['beginPortefeuilleWaardeInValuta']=$fondsRecord['totaalAantal']*$fondsRecord['beginwaardeLopendeJaar']*$fondsRecord['fondsEenheid'];
    $fondsRecord['beginPortefeuilleWaardeEuro']=$fondsRecord['beginPortefeuilleWaardeInValuta']*$beginValutaKoers;
    $fondsRecord['actuelePortefeuilleWaardeInValuta']=$fondsRecord['totaalAantal']*$fondsRecord['actueleFonds']*$fondsRecord['fondsEenheid'];
    $fondsRecord['actuelePortefeuilleWaardeEuro']=$fondsRecord['actuelePortefeuilleWaardeInValuta']*$fondsRecord['actueleValuta'];

    $set='';
    foreach($fondsRecord as $key=>$value)
      $set.="".$key." = '$value',";
    $query="UPDATE TijdelijkeRapportage SET $set add_date=now() WHERE id='$oldId'";
    $this->db->SQL($query);//echo $query."<br>\n";
    $this->db->Query();

    foreach ($newData as $key=>$waarde)
    {
        $fondsRecord[$key]=$waarde;
    }

    $fondsRecord['beginPortefeuilleWaardeInValuta']=$fondsRecord['totaalAantal']*$fondsRecord['beginwaardeLopendeJaar']*$fondsRecord['fondsEenheid'];
   // echo $fondsRecord['beginPortefeuilleWaardeInValuta']."=".$fondsRecord['totaalAantal']."*".$fondsRecord['beginwaardeLopendeJaar']."*".$fondsRecord['fondsEenheid']."<br>\n";
    $fondsRecord['beginPortefeuilleWaardeEuro']=$fondsRecord['beginPortefeuilleWaardeInValuta']*$beginValutaKoers;
    $fondsRecord['actuelePortefeuilleWaardeInValuta']=$newData['totaalAantal']*$fondsRecord['actueleFonds']*$fondsRecord['fondsEenheid'];
    //echo $fondsRecord['actuelePortefeuilleWaardeInValuta']."=".$fondsRecord['totaalAantal']."*".$fondsRecord['actueleFonds']."*".$fondsRecord['fondsEenheid']."<br>\n";
    $fondsRecord['actuelePortefeuilleWaardeEuro']=$fondsRecord['actuelePortefeuilleWaardeInValuta']*$fondsRecord['actueleValuta'];

    $set='';
    unset($fondsRecord['fondspaar']);
    foreach($fondsRecord as $key=>$value)
      $set.="".$key." = '$value',";

    $query="INSERT INTO TijdelijkeRapportage SET $set add_date=now(),fondspaar='$paarId' ";
    $this->db->SQL($query);//echo $query."<br>\n";
    $this->db->Query();
    $lastId=$this->db->last_id();

    $query="SELECT beleggingscategorie,beleggingscategorieVolgorde,beleggingscategorieOmschrijving,hoofdcategorie,hoofdcategorieVolgorde,hoofdcategorieOmschrijving,regio,regioVolgorde,regioOmschrijving ,beleggingssector ,beleggingssectorVolgorde,beleggingssectorOmschrijving ,fondsOmschrijving,portefeuille,Fonds,rapportageDatum,Fondseenheid,totaalAantal FROM TijdelijkeRapportage WHERE id='$lastId'";
    $this->db->SQL($query);
    $data=$this->db->lookupRecord();
    return $data;

  }

  function updateRegel($fondsData,$optieData,$paarId,$dag)
  {
    global $__appvar;

   // if($opties['skipFondsAantal']==true)
    //  $fondsAantalCorrectie=false;
   // else
   //   $fondsAantalCorrectie=true;

    $vasteWhere="TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$dag' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    $velden=array('beleggingscategorie','hoofdcategorie','regio','beleggingssector');
    foreach ($velden as $veld)
    {
      $nieuweWaarden[$veld]=$optieData[$veld];
      $nieuweWaarden[$veld.'Volgorde']=$optieData[$veld.'Volgorde'];
      $nieuweWaarden[$veld.'Omschrijving']=$optieData[$veld.'Omschrijving'];
    }

    if($optieData['totaalAantalGebruikt']<>0 && $optieData['totaalAantalGebruikt'] <> $optieData['totaalAantal'])
    {

      $newOptie=array('totaalAantal'=>$optieData['totaalAantalGebruikt']);
      $tmp=$this->splitsRegel($optieData['id'],$newOptie,$paarId);

      foreach($optieData as $key=>$value)
      {
        if($tmp[$key] <> '')
          $newOptie[$key] = $tmp[$key];
        else
          $newOptie[$key] = $value;
      }
      $optieData=$newOptie;

      echo $fondsData['Fonds']." | ".$optieData['Fonds'].",".$optieData['totaalAantalGebruikt']."<br>\n";ob_flush();
    }

    //} extra loop
    $aantal=round(abs($optieData['totaalAantal']*$optieData['Fondseenheid']));
    if($fondsData['totaalAantal'] == $aantal) //100% match
    {
     // echo "$paarId $dag $bovenliggendFonds ".$fondsData['totaalAantal']." == $aantal ".$optieData['fondsOmschrijving']." !!!!!<br>\n";
      $set='';
      foreach ($velden as $veld)
        $set.="TijdelijkeRapportage.".$veld."='".$nieuweWaarden[$veld]."',
                       TijdelijkeRapportage.".$veld."Volgorde='".$nieuweWaarden[$veld.'Volgorde']."',
                       TijdelijkeRapportage.".$veld."Omschrijving='".$nieuweWaarden[$veld.'Omschrijving']."', ";
      //echo $bovenliggendFonds." aantal gelijk. <br>\n";
      $query = "UPDATE TijdelijkeRapportage SET $set add_date=now(),fondspaar='$paarId' WHERE $vasteWhere AND Fonds='" . $fondsData['Fonds'] . "' AND id='" . $fondsData['id'] . "'";
      $this->db->SQL($query);
      $this->db->Query();

      $query="UPDATE TijdelijkeRapportage SET add_date=now(),fondspaar='$paarId' WHERE $vasteWhere AND Fonds='".$optieData['Fonds']."' AND id='".$optieData['id']."'";
      $this->db->SQL($query); //echo $query."<br>\n<br>\n";
      $this->db->Query();
     // echo "$query <br>\n";
    }
    elseif(($fondsData['totaalAantal'] - $aantal) > 0)
    {

      //echo "$paarId $dag $bovenliggendFonds ".$fondsData['totaalAantal']." == $aantal ".$optieData['fondsOmschrijving']." ------<br>\n";
      $query="SELECT historischeWaarde,historischeValutakoers,beginwaardeLopendeJaar,fondsOmschrijving,actueleFonds,valuta,rapportageDatum,beginwaardeValutaLopendeJaar,
              add_user,sessionId,portefeuille,Fonds,totaalAantal,beginPortefeuilleWaardeInValuta,actueleValuta,type FROM TijdelijkeRapportage WHERE id='".$fondsData['id']."'";
      $this->db->SQL($query);
      $this->db->Query();
      $fondsRecord=$this->db->lookupRecord();

      $set='';
      foreach ($velden as $veld)
      {
        $fondsRecord[$veld]=$nieuweWaarden[$veld];
        $fondsRecord[$veld."Volgorde"]=$nieuweWaarden[$veld.'Volgorde'];
        $fondsRecord[$veld."Omschrijving"]=$nieuweWaarden[$veld.'Omschrijving'];
      }
      $beginValutaKoers=$fondsRecord['beginPortefeuilleWaardeInValuta']/$fondsRecord['beginwaardeLopendeJaar']/$fondsRecord['totaalAantal'];

      $fondsRecord['beginPortefeuilleWaardeInValuta']=$aantal*$fondsRecord['beginwaardeLopendeJaar'];
      $fondsRecord['beginPortefeuilleWaardeEuro']=$fondsRecord['beginPortefeuilleWaardeInValuta']*$beginValutaKoers;
      $fondsRecord['actuelePortefeuilleWaardeInValuta']=$aantal*$fondsRecord['actueleFonds'];
      $fondsRecord['actuelePortefeuilleWaardeEuro']=$fondsRecord['actuelePortefeuilleWaardeInValuta']*$fondsRecord['actueleValuta'];
      $fondsRecord['totaalAantal']=$aantal;

      foreach($fondsRecord as $key=>$value)
        $set.="".$key." = '$value',";

      $query="INSERT INTO TijdelijkeRapportage SET $set add_date=now(),fondspaar='$paarId' ";
      $this->db->SQL($query);
      $this->db->Query();

       //  echo "$query <br>\n";
      //if($fondsAantalCorrectie==true)
        $fondsData['totaalAantal']-=$aantal;

      foreach ($velden as $veld)
      {
        $fondsUpdate[$veld]=$fondsData[$veld];
        $fondsUpdate[$veld."Volgorde"]=$fondsData[$veld.'Volgorde'];
        $fondsUpdate[$veld."Omschrijving"]=$fondsData[$veld.'Omschrijving'];
      }
      $fondsUpdate['beginPortefeuilleWaardeInValuta']=$fondsData['totaalAantal']*$fondsRecord['beginwaardeLopendeJaar'];
      $fondsUpdate['beginPortefeuilleWaardeEuro']=$fondsUpdate['beginPortefeuilleWaardeInValuta']*$beginValutaKoers;
      $fondsUpdate['actuelePortefeuilleWaardeInValuta']=$fondsData['totaalAantal']*$fondsRecord['actueleFonds'];
      $fondsUpdate['actuelePortefeuilleWaardeEuro']=$fondsUpdate['actuelePortefeuilleWaardeInValuta']*$fondsRecord['actueleValuta'];

      $set='';
      foreach($fondsUpdate as $key=>$value)
        $set.="".$key." = '$value',";

     // if($fondsAantalCorrectie==true)
     // {
        $query = "UPDATE TijdelijkeRapportage SET $set totaalAantal='" . $fondsData['totaalAantal'] . "', add_date=now(),fondspaar='0' WHERE $vasteWhere AND Fonds='" . $fondsData['Fonds'] . "'  AND id='" . $fondsData['id'] . "'";
        $this->db->SQL($query); //echo $query."<br>\n<br>\n";
        $this->db->Query();
    //  }
    //  echo "$query <br>\n";
      $query="UPDATE TijdelijkeRapportage SET add_date=now(),fondspaar='$paarId' WHERE $vasteWhere AND Fonds='".$optieData['Fonds']."' AND id='".$optieData['id']."'";
      $this->db->SQL($query); //echo $query."<br>\n<br>\n";
      $this->db->Query();


    }

  }

}


?>