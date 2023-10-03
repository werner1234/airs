<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.2 $

$Log: rapportATTberekening_L51.php,v $
Revision 1.2  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.1  2015/09/13 11:32:51  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
class rapportATTberekening_L51
{

	function RapportATTberekening_L51( $pdata )
	{
	  global $__appvar;
	  $this->db = new DB();

	  if(!is_array($pdata))
	  {
	    		$query =  "SELECT Portefeuilles.Vermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Portefeuille, Portefeuilles.Startdatum, ".
						 " Portefeuilles.Einddatum, Portefeuilles.Client, Portefeuilles.Depotbank, Vermogensbeheerders.attributieInPerformance, ".
						 " Clienten.Naam, Portefeuilles.ClientVermogensbeheerder FROM (Portefeuilles, Clienten ,Vermogensbeheerders)  WHERE ".
					 " Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
					 " AND Portefeuilles.Portefeuille = '$pdata' ";
					 $this->db->SQL($query);
					$pdata = $this->db->lookupRecord();
	  }

	  			$query ="SELECT Rekeningen.Rekening, Rekeningen.Portefeuille, Rekeningen.AttributieCategorie
                       FROM Rekeningen WHERE Rekeningen.Portefeuille = '".$pdata['Portefeuille']."' AND Rekeningen.Memoriaal = '0'";
					$this->db->SQL($query);
          $this->db->Query();
					while($categorie = $this->db->nextRecord())
					{
					  if($categorie['AttributieCategorie'] == '')
					    $categorie['AttributieCategorie'] = 'Liquiditeiten';
            if(!empty($this->liquiditeitenCategorie) && $categorie['AttributieCategorie'] != $this->liquiditeitenCategorie)
              echo "<script>alert('Niet alle rekeningen aan dezelfde Attributiecategorie gekoppeld voor Portefeuille:".$pdata['Portefeuille']."');</script>";

					  $this->liquiditeitenCategorie = $categorie['AttributieCategorie'];
					}

					if(empty($this->liquiditeitenCategorie))
				  	$this->liquiditeitenCategorie = 'Liquiditeiten';

		$this->pdata = $pdata;
		$this->pdata['pdf']=true;
		$this->debug = $pdata['debug'];
		$this->__appvar = $__appvar;

		if($this->pdata['Vermogensbeheerder'] == 'WWO')
	  	$this->totaalOK = true;
	  else
	    $this->totaalOK = false;

	}

	function getAttributieCategorien()
	{
	  $query = "SELECT  BeleggingssectorPerFonds.AttributieCategorie,  AttributieCategorien.Omschrijving
              FROM BeleggingssectorPerFonds  ,AttributieCategorien
              WHERE BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdata['Vermogensbeheerder']."' AND
              BeleggingssectorPerFonds.AttributieCategorie =  AttributieCategorien.AttributieCategorie
              GROUP BY BeleggingssectorPerFonds.AttributieCategorie ORDER BY AttributieCategorien.AfdrukVolgorde";
		$this->db->SQL($query);
		$this->db->Query();
		$this->categorien[] = 'Totaal';

		while($categorie = $this->db->nextRecord())
		{
		  $this->categorien[]=$categorie['AttributieCategorie'];
		  $this->categorieOmschrijving[$categorie['AttributieCategorie']]=$categorie['Omschrijving'];
		}

		if(count($this->categorien) >1)
		{
		  if(!array_search($this->liquiditeitenCategorie,$this->categorien))
		  {
		    $this->categorien[]=$this->liquiditeitenCategorie;
		    $this->categorieOmschrijving[$this->liquiditeitenCategorie]=$this->liquiditeitenCategorie;
		  }
		}

		return $this->categorien;
	}

	function attributieViaBoekdatumEnFonds($van,$tot,$grootboek)
  {
    if($this->pdata['Vermogensbeheerder'] == 'WWO')
      return 0;

  if ($this->pdata['RapportageValuta'] != "EUR" && $this->pdata['RapportageValuta'] != '')
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdata['RapportageValuta']."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	   $koersQuery = "";

  $waarden = array();
  $db= new DB();
  $db2= new DB();
  $query = "SELECT
  (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) $koersQuery AS waarde ,
  ((TO_DAYS('$tot') - TO_DAYS(Rekeningmutaties.Boekdatum))/ (TO_DAYS('$tot') - TO_DAYS('$van')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) )) AS weging,
  Rekeningmutaties.Rekening,
  Rekeningmutaties.Omschrijving,
  Rekeningmutaties.Boekdatum
  FROM (Rekeningen, Portefeuilles , Rekeningmutaties)
  WHERE
  Rekeningmutaties.Rekening = Rekeningen.Rekening AND
 	Rekeningen.Portefeuille = '".$this->pdata['Portefeuille']."' AND
 	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
  Grootboekrekening = '$grootboek' AND Boekdatum > '$van' AND Boekdatum <= '$tot' ";
  $db->SQL($query);
  $db->Query();
  while ($data = $db->nextRecord())
  {
    if(db2jul($data['Boekdatum']) < 1356994800) //voor "2013-01-01"
    {
    $query = "SELECT
    BeleggingssectorPerFonds.AttributieCategorie
    FROM (Rekeningmutaties , BeleggingssectorPerFonds)
    WHERE
    Boekdatum = '".$data['Boekdatum']."' AND
    Rekening = '".$data['Rekening']."' AND
    BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdata['Vermogensbeheerder']."' AND
    Omschrijving ='".addslashes($data['Omschrijving'])."' AND
    Grootboekrekening = 'FONDS' AND
    Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds LIMIT 1";
    $db2->SQL($query);
    $db2->Query();
    $AttributieData = $db2->nextRecord();

    $totaalWaarde += $data['waarde'];
    if($AttributieData['AttributieCategorie'] == '')
      $AttributieData['AttributieCategorie'] = $this->liquiditeitenCategorie;

    $waarden['mutaties'][]=array('waarde'=>$data['waarde'],'weging'=>$data['weging'],'boekdatum'=>$data['Boekdatum'],'AttributieCategorie'=>$AttributieData['AttributieCategorie']);
    $waarden['categorieWaarde'][$AttributieData['AttributieCategorie']] += $data['waarde'];
    $waarden['categorieWeging'][$AttributieData['AttributieCategorie']] += $data['weging'];

    $waarden['categorieWaarde']['Totaal'] += $data['waarde'];
    $waarden['categorieWeging']['Totaal'] += $data['weging'];
    }
  }


  if($this->pdata['Vermogensbeheerder'] != 'WWO')
  {
  foreach ($this->categorien as $categorie)
  {
    if(round($this->performance[$this->periodeId]['totaalWaarde'][$categorie]['begin'] ,2) == 0)
    {
      $waarden['categorieWeging'][$categorie] = $waarden['categorieWaarde'][$categorie];
    }
    elseif(round($this->performance[$this->periodeId]['totaalWaarde'][$categorie]['eind'] ,2) == 0)
	  {
      $waarden['categorieWeging'][$categorie] = 0;
    }
  }
  }


  $waarden['grootboek'] = $grootboek;
  $waarden['totaalWaarde'] = $totaalWaarde;
  $this->attributieViaBoekdatumEnFonds = $waarden;
  return $waarden;
  }

  function attributiewaardenViaGrootboek($vanaf,$tot)
  {
    $attributieCategorieGrootboek = array();
    if ($this->pdata['RapportageValuta'] != "EUR" && $this->pdata['RapportageValuta'] != '')
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdata['RapportageValuta']."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

    if(substr($vanaf,5,6) == '01-01')
	    $datumBeginWeging=date("Y-m-d",db2jul($vanaf)-86400);
	  else
	    $datumBeginWeging=$vanaf;
  	  if($this->pdata['Vermogensbeheerder'] == 'WAT' || $this->pdata['Vermogensbeheerder'] == 'WAT1')
  	  {
	  $query ="
    SELECT
      SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)AS subcredit,
      SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)  AS subdebet,
      SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers  $koersQuery )- SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers  $koersQuery )AS waarde ,
      SUM(((TO_DAYS('$tot') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$tot') - TO_DAYS('$datumBeginWeging')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery))) AS gewogenWaarde,
      AttributiePerGrootboekrekening.AttributieCategorie as AttributieCategorieViaGB, Rekeningmutaties.Grootboekrekening, Grootboekrekeningen.Kosten , Grootboekrekeningen.Opbrengst ,
      if(BeleggingssectorPerFonds.AttributieCategorie Is  Null ,AttributiePerGrootboekrekening.AttributieCategorie,BeleggingssectorPerFonds.AttributieCategorie) AS  AttributieCategorie
    FROM
       (Rekeningen, Portefeuilles , Rekeningmutaties  )
       LEFT JOIN AttributiePerGrootboekrekening ON Rekeningmutaties.Grootboekrekening = AttributiePerGrootboekrekening.Grootboekrekening  AND AttributiePerGrootboekrekening.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
       LEFT JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
       Left Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND Portefeuilles.Vermogensbeheerder = BeleggingssectorPerFonds.Vermogensbeheerder
    WHERE
      Rekeningmutaties.Rekening = Rekeningen.Rekening AND
 	    Rekeningen.Portefeuille = '".$this->pdata['Portefeuille']."' AND
 	    Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	    Rekeningmutaties.Verwerkt = '1' AND
	    Rekeningmutaties.Boekdatum > '$vanaf' AND
 	    Rekeningmutaties.Boekdatum <= '$tot' AND
 	    Rekeningmutaties.GrootboekRekening <> 'FONDS'
	  GROUP BY
	    Rekeningmutaties.Grootboekrekening ,AttributieCategorie	";
		}
    elseif($this->pdata['Vermogensbeheerder'] == 'THB')
    {
      $query ="SELECT
    SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)AS subcredit,
      SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)  AS subdebet,
      SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers  $koersQuery )- SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers  $koersQuery )AS waarde ,
      SUM(((TO_DAYS('$tot') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$tot') - TO_DAYS('$datumBeginWeging')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery))) AS gewogenWaarde,
Rekeningmutaties.Grootboekrekening,
Grootboekrekeningen.Kosten,
Grootboekrekeningen.Opbrengst,
BeleggingssectorPerFonds.AttributieCategorie
FROM
 Rekeningmutaties
 INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.Portefeuille = '".$this->pdata['Portefeuille']."'
INNER JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening 
INNER JOIN BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdata['Vermogensbeheerder']."'
WHERE
Rekeningmutaties.Verwerkt = '1' AND
	    Rekeningmutaties.Boekdatum > '$vanaf' AND
 	    Rekeningmutaties.Boekdatum <= '$tot' AND
Rekeningmutaties.Grootboekrekening <> 'FONDS'
GROUP BY Rekeningmutaties.Grootboekrekening,BeleggingssectorPerFonds.AttributieCategorie";

    }
		else
		{
		    $query ="
    SELECT
      SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)AS subcredit,
      SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)  AS subdebet,
      SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers  $koersQuery )- SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers  $koersQuery )AS waarde ,
      SUM(((TO_DAYS('$tot') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$tot') - TO_DAYS('$datumBeginWeging')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery))) AS gewogenWaarde,
      AttributiePerGrootboekrekening.AttributieCategorie, Rekeningmutaties.Grootboekrekening, Grootboekrekeningen.Kosten , Grootboekrekeningen.Opbrengst
    FROM
       (Rekeningen, Portefeuilles , Rekeningmutaties  )
       LEFT JOIN AttributiePerGrootboekrekening ON Rekeningmutaties.Grootboekrekening = AttributiePerGrootboekrekening.Grootboekrekening  AND AttributiePerGrootboekrekening.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
       LEFT JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
    WHERE
      Rekeningmutaties.Rekening = Rekeningen.Rekening AND
 	    Rekeningen.Portefeuille = '".$this->pdata['Portefeuille']."' AND
 	    Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	    Rekeningmutaties.Verwerkt = '1' AND
	    Rekeningmutaties.Boekdatum > '$vanaf' AND
 	    Rekeningmutaties.Boekdatum <= '$tot' AND
 	    Rekeningmutaties.GrootboekRekening <> 'FONDS'
	  GROUP BY
	    Rekeningmutaties.Grootboekrekening	";
		}

	  $this->db->SQL($query);// echo "<br>\n $query <br>\n"; ob_flush();
	  $this->db->Query();
	  while($attributieGrootboek = $this->db->nextRecord())
    {
      if($attributieGrootboek['AttributieCategorie']=='' && ($this->pdata['Vermogensbeheerder'] == 'WAT' || $this->pdata['Vermogensbeheerder'] == 'WAT1'))
        $attributieGrootboek['AttributieCategorie']=$this->liquiditeitenCategorie;
      $attributieCategorie = $attributieGrootboek['AttributieCategorie'];
      if ($attributieCategorie == '')
      {
         $kosten = $this->attributieViaBoekdatumEnFonds($vanaf,$tot,$attributieGrootboek['Grootboekrekening']);
      }

      if($attributieGrootboek['Kosten'] == 1)
      {
        if($attributieCategorie != '')
        {
          $attributieCategorieGrootboek['Kosten'][$attributieGrootboek['AttributieCategorie']] += $attributieGrootboek['waarde'];
          $attributieCategorieGrootboek['Gewogen'][$attributieCategorie] += $attributieGrootboek['gewogenWaarde'];
          $attributieCategorieGrootboek['Kosten']['Totaal'] += $attributieGrootboek['waarde'];
        }
        else
        {
          foreach ($kosten['categorieWaarde'] as $categorie=>$waarde)
            $attributieCategorieGrootboek['Kosten'][$categorie] += $waarde;
          foreach ($kosten['categorieWeging'] as $categorie=>$waarde)
            $attributieCategorieGrootboek['Gewogen'][$categorie] +=$waarde;

        }
      }
      elseif($attributieGrootboek['Opbrengst'] == 1)
      {
        if($attributieCategorie != '')
        {
          $attributieCategorieGrootboek['Opbrengst'][$attributieGrootboek['AttributieCategorie']] += $attributieGrootboek['waarde'];
          $attributieCategorieGrootboek['Gewogen'][$attributieCategorie] += $attributieGrootboek['gewogenWaarde'];
          $attributieCategorieGrootboek['Opbrengst']['Totaal'] += $attributieGrootboek['waarde'];
        }
        else
        {
          foreach ($kosten['categorieWaarde'] as $categorie=>$waarde)
            $attributieCategorieGrootboek['Opbrengst'][$categorie] +=$waarde;
          foreach ($kosten['categorieWeging'] as $categorie=>$waarde)
            $attributieCategorieGrootboek['Gewogen'][$categorie] +=$waarde;
        }

      }
    }

//  if($this->totaalOK == false)// -$attributieCategorieGrootboek['Kosten']['Totaal'] omdat we geen kosten willen in de bovenkaders om de balans weer op 0 te krijgen.
//    $attributieCategorieGrootboek['Kosten'][$this->liquiditeitenCategorie] -= $attributieCategorieGrootboek['Kosten']['Totaal'];

  if($this->liquiditeitenCategorie == 'Liquiditeiten') // WAT methode
  {
   // $attributieCategorieGrootboek['Kosten'][$this->liquiditeitenCategorie] =0;
   // $attributieCategorieGrootboek['Opbrengst'][$this->liquiditeitenCategorie] =0;
     foreach ($this->categorien as $categorie)
     {
       if($categorie != 'Totaal' && $categorie != $this->liquiditeitenCategorie)
       {
       //  $attributieCategorieGrootboek['Kosten'][$this->liquiditeitenCategorie] += $attributieCategorieGrootboek['Opbrengst'][$categorie];
       //  $attributieCategorieGrootboek['Opbrengst'][$this->liquiditeitenCategorie] += $attributieCategorieGrootboek['Kosten'][$categorie];
       }
     }
  }

   foreach ($this->categorien as $categorie)
   {
     $attributieCategorieGrootboek['Kosten'][$categorie] = $attributieCategorieGrootboek['Kosten'][$categorie] * -1;
   }



  return array('opbrengst'=>$attributieCategorieGrootboek['Opbrengst'],
               "kosten"   =>$attributieCategorieGrootboek['Kosten'],
               "gewogen"  =>$attributieCategorieGrootboek['Gewogen']);
  }

  function getAttributieStortingen($van, $tot, $valuta = 'EUR' )
  {
    if(substr($van,5,6) == '01-01')
	    $datumBeginWeging=date("Y-m-d",db2jul($van)-86400);
	  else
	    $datumBeginWeging=$van;

    $selectTotaal = "SELECT ";
	  $attributieQueryTotaal = " AND Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Onttrekking=1 OR Storting=1)";

	  $select = "SELECT BeleggingssectorPerFonds.AttributieCategorie,";
	  $attributieQuery .= " AND BeleggingssectorPerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
	                       GROUP BY  BeleggingssectorPerFonds.AttributieCategorie ";

	  if ($valuta != "EUR" && $valuta != "")
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

	  $vanOrigineel = $van;

   foreach ($this->categorien as $categorie)
   {
     $van = $vanOrigineel;

         $DB = new DB();


      if($categorie != 'Totaal' )
      {
        if($this->pdata['Vermogensbeheerder'] == 'WAT1' || $this->pdata['Vermogensbeheerder'] == 'WAT' || $this->pdata['Vermogensbeheerder'] == 'THB' || $this->pdata['Vermogensbeheerder'] == 'BCS' || $this->pdata['Vermogensbeheerder'] == 'EVO' || $this->pdata['Vermogensbeheerder'] == 'LBC')
          $extra = " BeleggingssectorPerFonds.AttributieCategorie = '$categorie' AND Rekeningmutaties.Grootboekrekening = 'Fonds' AND";
        elseif($this->pdata['Vermogensbeheerder'] == 'WWO')
          $extra = " (BeleggingssectorPerFonds.AttributieCategorie = '$categorie'  OR  (BeleggingssectorPerFonds.AttributieCategorie is null AND Rekeningen.AttributieCategorie='$categorie')) AND " ;
        else
          $extra = " BeleggingssectorPerFonds.AttributieCategorie = '$categorie' AND ";
      }
      else
        $extra = "";

/*
       if($this->pdata['Vermogensbeheerder'] == 'WWO')
       {
        if(round($this->performance[$this->periodeId]['totaalWaarde'][$categorie]['begin'] ,2) == 0)
        {
           $query = "SELECT Rekeningmutaties.Boekdatum - INTERVAL 1 DAY as Boekdatum
                  	   FROM (Rekeningmutaties ,  Rekeningen, Portefeuilles)
	          LEFT JOIN BeleggingssectorPerFonds  on Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds
	        WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	       Rekeningen.Portefeuille = '".$this->pdata['Portefeuille']."' AND
	       Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
 	       Rekeningmutaties.Verwerkt = '1' AND $extra
	       Rekeningmutaties.Boekdatum > '".$van."' AND
	       Rekeningmutaties.Boekdatum <= '".$tot."' ORDER BY Rekeningmutaties.Boekdatum asc LIMIT 1 ";
	        $DB->SQL($query);
	        $DB->Query();
	        $start = $DB->NextRecord();
	        if($start['Boekdatum']!='')
	          $van = $start['Boekdatum'];
         }
       }
*/

   	 $query = "
	     SUM(((TO_DAYS('$tot') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$tot') - TO_DAYS('$datumBeginWeging')) *
	     ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))$koersQuery AS gewogenTotaal,
	     SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery) AS totaal,
       SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers) $koersQuery) AS subcredit ,
	     SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) $koersQuery) AS subdebet
 	   FROM
	    (Rekeningmutaties ,  Rekeningen, Portefeuilles)
	    LEFT JOIN BeleggingssectorPerFonds  on Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds
	   WHERE
	     Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	     Rekeningen.Portefeuille = '".$this->pdata['Portefeuille']."' AND
	     Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
 	     Rekeningmutaties.Verwerkt = '1' AND $extra
	     Rekeningmutaties.Boekdatum > '".$van."' AND
	     Rekeningmutaties.Boekdatum <= '".$tot."'" ;

	  if($categorie == 'Totaal')
	  {
	    $DB->SQL($selectTotaal.$query.$attributieQueryTotaal);//echo "$categorie $selectTotaal $query $attributieQueryTotaal \n<br>\n";
  	  $DB->Query();
	    $totaal = $DB->nextRecord();
  	  $totaal['AttributieCategorie'] = 'Totaal';
  	  $tmp['Totaal']=$totaal;

  	  //rvv test
  	  if($this->pdata['Vermogensbeheerder'] == 'WAT' || $this->pdata['Vermogensbeheerder'] == 'WAT1')
  	  {
  	   if(round($this->performance[$this->periodeId]['totaalWaarde'][$categorie]['begin'] ,2) == 0)
  	     $tmp[$categorie]['gewogenTotaal'] = $totaal['totaal'];
  	   elseif(round($this->performance[$this->periodeId]['totaalWaarde'][$categorie]['eind'] ,2) == 0)
  	     $tmp[$categorie]['gewogenTotaal'] = 0;
  	  }
	  }
	  else
	  {
      $DB->SQL($select.$query.$attributieQuery);//echo $categorie." <br>\n $select $query $attributieQuery \n<br><br>\n";
	    $DB->Query();
	    while ($data = $DB->nextRecord())
	    {
	      if(round($this->performance[$this->periodeId]['totaalWaarde'][$categorie]['begin'] ,2) == 0)
	      {
	        $data['gewogenTotaal'] = $data['totaal'];
	        $tmp[$data['AttributieCategorie']]=$data;
	        $VOLKMethode=1;

	      }
	      elseif(round($this->performance[$this->periodeId]['totaalWaarde'][$categorie]['eind'] ,2) == 0)
	      {
	        $data['gewogenTotaal'] = 0;
	        $tmp[$data['AttributieCategorie']]=$data;
	        $VOLKMethode=1;
	      }
	      else
	        $tmp[$data['AttributieCategorie']]=$data;
	     }
     }
  }

	foreach($tmp as $data)
	{
	  if($data['AttributieCategorie'] == 'Totaal')
		{
		  $stortingen[$data['AttributieCategorie']]	   = $data['subcredit'];
	    $stortingen[$this->liquiditeitenCategorie]   -= $data['subdebet'];
		  $onttrekkingen[$data['AttributieCategorie']] = $data['subdebet'];
	    $onttrekkingen[$this->liquiditeitenCategorie] -= $data['subcredit'];

	    $gewogen[$data['AttributieCategorie']]	= $data['gewogenTotaal'];
	    $gewogen[$this->liquiditeitenCategorie]	-= $data['gewogenTotaal'];
		}
		else
		{
		  $stortingen[$data['AttributieCategorie']]	  += 	$data['subdebet'];
	    $onttrekkingen[$this->liquiditeitenCategorie]	+= 	$data['subdebet'];

		  $onttrekkingen[$data['AttributieCategorie']]	+= $data['subcredit'];
	    $stortingen[$this->liquiditeitenCategorie]	+= $data['subcredit'];

      $gewogen[$data['AttributieCategorie']]	+= $data['gewogenTotaal'];
      $gewogen[$this->liquiditeitenCategorie]	-= $data['gewogenTotaal'];

		}
	}


	foreach ($this->categorien as $categorie)
	{
	  $tmp[$categorie]['stortingen']+=$stortingen[$categorie];
	  $tmp[$categorie]['onttrekkingen']+=$onttrekkingen[$categorie];
	  $tmp[$categorie]['gewogenLiq']+=$gewogen[$categorie];
	}

	if($VOLKMethode == 1)
	{
		foreach ($this->categorien as $categorie)
  	{
  	    if($categorie <> 'Totaal')
  	      if($categorie <> $this->liquiditeitenCategorie)
  	      {
            $tmp[$categorie]['gewogenTotaal'] = $this->getAttributieStortingenVOLK($categorie,$vanOrigineel, $tot,$valuta);
  	      }
	        else
	          $tmp[$categorie]['gewogenLiq'] = $this->getAttributieStortingenVOLK($categorie,$vanOrigineel, $tot,$valuta);
	  }
	}

  return $tmp;
}

function getKwartalen($julBegin, $julEind)
{
   if($julBegin > $julEind )
     return array();
   $beginjaar = date("Y",$julBegin);
   $eindjaar = date("Y",$julEind);
   $maandenStap=3;
   $stap=1;
   $n=0;
   $teller=$julBegin;
   $kwartaalGrenzen=array();
   $datum=array();
   while ($teller < $julEind)
   {
     $teller = mktime (0,0,0,$stap,0,$beginjaar);
     $stap +=$maandenStap;
     if($teller > $julBegin && $teller < $julEind)
     {
     $grensDatum=date("d-m-Y",$teller);
     $kwartaalGrenzen[] = $teller;
     }
   }
   if(count($kwartaalGrenzen) > 0)
   {
     $datum[$n]['start']=date('Y-m-d',$julBegin);
     foreach ($kwartaalGrenzen as $grens)
     {
       $datum[$n]['stop']=date('Y-m-d',$grens);
       $n++;
       $start=date('Y-m-d',$grens);
       if(substr($start,-5)=='12-31')
        $start=(substr($start,0,4)+1).'-01-01';

       $datum[$n]['start']=$start;
     }
     $datum[$n]['stop']=date('Y-m-d',$julEind);
   }
   else
   {
     $datum[]=array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$julEind));
   }
 	 return $datum;
}

function getMaanden($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
}

  function attributiePerformance($portefeuille, $datumBegin, $datumEind,$periodeNaam='waarden',$valuta='EUR',$blokPeriode='maand')
  {
    global $USR;
    if ($valuta != "EUR" && $valuta != "")
	  {
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  }
	  else
	  {
	    $koersQuery = "";
	    $startValutakoers = 1;
	    $eindValutaKoers = 1;
	  }

    $julBegin = db2jul($datumBegin);
    $julEind = db2jul($datumEind);
    $julStart = db2jul($this->pdata['Startdatum']);
    if ($julStart > $julBegin)
      $julBegin = $julStart;

   	$eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $ready = false;
	  $i=0;

	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
	  $datum == array();

	  foreach ($this->categorien as $categorie)
	    $totaalPerf[$categorie] = 100;

	  if($blokPeriode == 'periode')
	      $datum=array(array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$julEind)));
	  elseif($blokPeriode == 'kwartaal')
      $datum = $this->getKwartalen($julBegin, $julEind);
    else
    {
      $datum = $this->getMaanden($julBegin, $julEind);
  	  foreach ($datum as $periode) //31-12 vervangen voor 1-1 bij startdatum
	    {
	      if(substr($periode['start'],5) == '12-31')
	      {
	        $jaar = date('Y',db2jul($periode['start'])) +1;;
	        $periode['start'] = "$jaar-01-01";
	      }
	      $tmp[] = $periode;
	    }
	    $datum=$tmp;
    } 
	  foreach ($datum as $periode)
	  {

	    $stopJul = db2jul($periode['stop']);
	    $startJul = db2jul($periode['start']);

	    $this->dbWaarden = array();

	    if($blokPeriode == 'kwartaal')
	    {
	   //   echo "-> ".jul2form(mktime (0,0,0,date("m",$stopJul)+1,0,date("Y",$stopJul)))." ".$periode['stop']." -> start ".jul2form(mktime (0,0,0,date("m",$stopJul)-2,0,date("Y",$stopJul)))." ".$periode['start']."<br>\n";
	      if((db2jul($periode['stop']) ==  mktime (0,0,0,date("m",$stopJul)+1,0,date("Y",$stopJul)) && db2jul($periode['start']) ==  mktime (0,0,0,date("m",$stopJul)-2,0,date("Y",$stopJul))) ||
	        ($periode['start'] == date("Y",$stopJul).'-01-01' && $periode['stop'] == date("Y",$stopJul).'-03-31') ||
	        ($periode['start'] == date("Y-m-d",$julStart) && db2jul($periode['stop']) ==  mktime (0,0,0,date("m",$stopJul)+1,0,date("Y",$stopJul))) )
	        $noUpdate = false;
	      else
	        $noUpdate = true;
	    }
	    else
	    {
      //echo $periode['start']." == ".date("Y-m-d",$julStart)."<br>\n";
	    //Alleen database update wanneer de startdatum de laatste van de vorige maand is en de einddatum de laatste van de rapportage maand is
	    //  Of als de startdatum 1-jan is en eind 31-jan.
	    // Of portefuille startdatum en eind is eind maand.

	    if((db2jul($periode['stop']) ==  mktime (0,0,0,date("m",$stopJul)+1,0,date("Y",$stopJul)) && db2jul($periode['start']) ==  mktime (0,0,0,date("m",$stopJul),0,date("Y",$stopJul))) ||
	      ($periode['start'] == date("Y",$stopJul).'-01-01' && $periode['stop'] == date("Y",$stopJul).'-01-31') ||
	      ($periode['start'] == date("Y-m-d",$julStart) && db2jul($periode['stop']) ==  mktime (0,0,0,date("m",$stopJul)+1,0,date("Y",$stopJul))) )
	      $noUpdate = false;
	    else
	      $noUpdate = true;
	    }

	   if($blokPeriode == 'maand' && (
	      db2jul($periode['start']) ==  mktime (0,0,0,date("m",$stopJul),0,date("Y",$stopJul)) ||  ($periode['start'] == date("Y",$stopJul).'-01-01') ||
	      $periode['start'] == date("Y-m-d",$julStart) && db2jul($periode['stop']) ==  mktime (0,0,0,date("m",$stopJul)+1,0,date("Y",$stopJul)) )
	      ||
	      (
	       $blokPeriode == 'kwartaal'

	      )
	     )
	   {
	     $cat = " AND (categorie = '".implode('\' OR categorie = \'',$this->categorien)."' )";
	     $query = "SELECT * FROM HistorischePortefeuilleIndex WHERE periode='m' AND Portefeuille = '".$this->pdata['Portefeuille']."' AND Datum = '".$periode['stop']."' $cat ";
	     $this->db->SQL($query);
	     $this->db->Query();
	     $records = $this->db->records();

	     while ($data = $this->db->nextRecord())
		     $this->dbWaarden[$data['Categorie']] = $data;

	     if ($records == count($this->categorien))
	     {
		     if($this->pdata['aanvullen'])
		       $action = 'skip';
	      //	 echo "Database waarden gevonden.<br>\n";
	     }
	     elseif($records > count($this->categorien))
	     {
	       echo "<script type=\"text/JavaScript\">alert('$records waarden gevonden?. Verwachte aantal ". count($this->categorien)." op ".$periode['stop'].". Bewerking afgebroken.');</script>";
	       $action = 'skip';
	       flush();
	       exit;
	     }
	     else
	       $action = '';
     }

	   if($valuta <> 'EUR' && $valuta != "")
	   {
	     $this->dbWaarden=array();
	     $noUpdate = true;
	   }

	   if($this->pdata['pdf'] == true)
	   {
	     $noUpdate = true;
	     $action = 'skip';
	   }

	  if ($valuta != "EUR" && $valuta != "")
	  {
	    $startValutakoers = getValutaKoers($valuta,$periode['start']);
	    $eindValutaKoers = getValutaKoers($valuta,$periode['stop']);
	  }

/*
	  if(db2jul($periode['start']) == mktime (0,0,0,1,1,$beginjaar) )
    	$startjaar = true;
    else
			$startjaar = false;
*/
	  if(substr($periode['start'],5,5 ) == '01-01')
			$startjaar = true;
	  else
			$startjaar = false;
//	echo $periode['start']." ".substr($periode['start'],5,5 )." $startjaar<br>\n";

		$periodeId = substr(jul2db(db2jul($periode['start'])),0,10)."-".substr(jul2db(db2jul($periode['stop'])),0,10);
		if(!isset($eerstePeriodeId))
		  $eerstePeriodeId = $periodeId;
		$this->periodeId =$periodeId;

    if($action == 'skip')
		{
		   foreach ($this->dbWaarden as $categorie=>$waarden)
		   {
		     $this->performance[$periodeId]['totaalWaarde'][$categorie]['begin']=$waarden['PortefeuilleBeginWaarde'];
		     $this->performance[$periodeId]['totaalWaarde'][$categorie]['eind']=$waarden['PortefeuilleWaarde'];
		   }
  	}
    if(!is_array($this->performance[$periodeId]['totaalWaarde']))
    {
      $fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($this->pdata['Portefeuille'],$periode['start'],$startjaar,$valuta,$periode['start']);
      foreach ($fondswaarden['beginmaand'] as $regel)
      {
        if($regel['AttributieCategorie'] == '')
          $regel['AttributieCategorie'] = $this->liquiditeitenCategorie;
         $this->performance[$periodeId]['totaalWaarde'][$regel['AttributieCategorie']]['begin'] += $regel['actuelePortefeuilleWaardeEuro'] /$startValutakoers;
         $this->performance[$periodeId]['totaalWaarde']['Totaal']['begin'] += $regel['actuelePortefeuilleWaardeEuro'] /$startValutakoers;
         if($regel['fonds'])
           $this->performance[$periodeId]['fondsen'][$regel['AttributieCategorie']][$regel['fonds']]=$regel['fonds'];
         if($regel['rekening'])
           $this->performance[$periodeId]['rekeningen'][$regel['AttributieCategorie']][$regel['rekening']]=$regel['rekening'];
      }
      $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($this->pdata['Portefeuille'],$periode['stop'],false,$valuta,$periode['start']);
	    foreach ($fondswaarden['eindmaand'] as $regel)
	    {
	      if($regel['AttributieCategorie'] == '')
          $regel['AttributieCategorie'] = $this->liquiditeitenCategorie;
        $this->performance[$periodeId]['totaalWaarde'][$regel['AttributieCategorie']]['eind'] += $regel['actuelePortefeuilleWaardeEuro'] / $eindValutaKoers;
        $this->performance[$periodeId]['totaalWaarde']['Totaal']['eind'] += $regel['actuelePortefeuilleWaardeEuro'] /$eindValutaKoers;
        $this->performance[$periodeId]['ongerealiseerdResultaat'][$regel['AttributieCategorie']]['eind'] += ($regel['actuelePortefeuilleWaardeEuro'] /$eindValutaKoers - $regel['beginPortefeuilleWaardeEuro']/$startValutakoers);
        if($regel['fonds'])
          $this->performance[$periodeId]['fondsen'][$regel['AttributieCategorie']][$regel['fonds']]=$regel['fonds'];
        if($regel['rekening'])
          $this->performance[$periodeId]['rekeningen'][$regel['AttributieCategorie']][$regel['rekening']]=$regel['rekening'];
	    }
    }

	if($action == 'skip')
	{
	   foreach ($this->dbWaarden as $categorie=>$waarden)
		 {
		   $this->performance[$periodeId]['attributiewaardenViaGrootboek']['opbrengst'][$categorie]=$waarden['Opbrengsten'];
		   $this->performance[$periodeId]['attributiewaardenViaGrootboek']['kosten'][$categorie]=$waarden['Kosten'];
		 }

  }
	if(!is_array($this->performance[$periodeId]['attributiewaardenViaGrootboek']))
	{
	   $this->performance[$periodeId]['attributiewaardenViaGrootboek'] = $this->attributiewaardenViaGrootboek($periode['start'],$periode['stop']);
	}
	$grootboekOpbrengstTotaal=array();
	$grootboekKostenTotaal=array();
	$grootboekKostenOpbrengstTotaalGewogen=array();

	 foreach ($this->categorien as $categorie)
	 {
    if($categorie == "Totaal" )
	  {
	    //in de performance meeting geen directe kosten en opbrengsten meenemen.
	    $grootboekKostenTotaal[$categorie] =0;$grootboekOpbrengstTotaal[$categorie] =0;$grootboekKostenOpbrengstTotaalGewogen[$categorie] =0;
 	    $grootboekKostenOpbrengstTotaalGewogen[$this->liquiditeitenCategorie]  -= $this->performance[$periodeId]['attributiewaardenViaGrootboek']['gewogen'][$categorie];
	  }
    else
	  {
      $grootboekOpbrengstTotaal[$categorie]  += $this->performance[$periodeId]['attributiewaardenViaGrootboek']['opbrengst'][$categorie]; //*-1
	    $grootboekKostenTotaal[$categorie] += $this->performance[$periodeId]['attributiewaardenViaGrootboek']['kosten'][$categorie] ;
	    $grootboekKostenOpbrengstTotaalGewogen[$categorie] += $this->performance[$periodeId]['attributiewaardenViaGrootboek']['gewogen'][$categorie];
	  }
	 }
	 if($this->liquiditeitenCategorie != 'Liquiditeiten' ) //WWO performance voor liquiditeitencategorie gelijk aan Totaal
	 {
  	 $grootboekOpbrengstTotaal[$this->liquiditeitenCategorie] =0;
	   $grootboekKostenTotaal[$this->liquiditeitenCategorie] =0; //echo $this->liquiditeitenCategorie."<br> \n";
	 }
//echo $this->liquiditeitenCategorie."<br>\n";listarray($grootboekOpbrengstTotaal);listarray($grootboekKostenTotaal);listarray($grootboekKostenOpbrengstTotaalGewogen);exit;

	if($action == 'skip')
	{
    foreach ($this->dbWaarden as $categorie=>$waarden)
    {
      $this->performance[$periodeId]['AttributieStortingenOntrekkingen'][$categorie]['stortingen']=$waarden['Stortingen'];
      $this->performance[$periodeId]['AttributieStortingenOntrekkingen'][$categorie]['onttrekkingen']=$waarden['Onttrekkingen'];
    }
	}
	if(!is_array($this->performance[$periodeId]['AttributieStortingenOntrekkingen']))
	{
	   $this->performance[$periodeId]['AttributieStortingenOntrekkingen'] = $this->getAttributieStortingen($periode['start'],$periode['stop'],$valuta);
  }



	if($action == 'skip')
  {
		foreach ($this->dbWaarden as $categorie=>$waarden)
		{
		  $this->performance[$periodeId]['totaal']['performance'][$categorie]=$waarden['IndexWaarde'];
		  $this->performance[$periodeId]['totaal']['gemiddelde'][$categorie]=$waarden['gemiddelde'];
	  }
	}

  foreach ($this->categorien as $categorie)
  {
      if($categorie == $this->liquiditeitenCategorie)
      {
        $this->performance[$periodeId]['AttributieStortingenOntrekkingen'][$categorie]['stortingen'] -= $this->performance[$periodeId]['attributiewaardenViaGrootboek']['kosten']['Totaal'];
        $this->performance[$periodeId]['AttributieStortingenOntrekkingen'][$categorie]['onttrekkingen'] -= $this->performance[$periodeId]['attributiewaardenViaGrootboek']['opbrengst']['Totaal'];
      }


      $AttributieStortingen = $this->performance[$periodeId]['AttributieStortingenOntrekkingen'][$categorie]['stortingen'];
	    $AttributieOntrekkingen = $this->performance[$periodeId]['AttributieStortingenOntrekkingen'][$categorie]['onttrekkingen'];

      if($categorie ==  'Totaal')
      {
	     $AttributieStortingenOntrekkingenGewogen = $this->performance[$periodeId]['AttributieStortingenOntrekkingen'][$categorie]['gewogenTotaal'] ;
	     $AttributieStortingenOntrekkingen = $AttributieStortingen-$AttributieOntrekkingen;
      }
	    else
	    {
	      $AttributieStortingenOntrekkingenGewogen = $this->performance[$periodeId]['AttributieStortingenOntrekkingen'][$categorie]['gewogenTotaal']*-1 ;
	      $AttributieStortingenOntrekkingen = $AttributieStortingen-$AttributieOntrekkingen;
	    }
		  if($categorie == $this->liquiditeitenCategorie)
	    {
	      $AttributieStortingenOntrekkingen = ($AttributieStortingen-$AttributieOntrekkingen);
	      $AttributieStortingenOntrekkingenGewogen = $this->performance[$periodeId]['AttributieStortingenOntrekkingen'][$categorie]['gewogenLiq'] *-1;
	    }


	$beginWaarde = $this->performance[$periodeId]['totaalWaarde'][$categorie]['begin'];
	$eindWaarde = $this->performance[$periodeId]['totaalWaarde'][$categorie]['eind'];
  $gemiddelde =  $beginWaarde + $AttributieStortingenOntrekkingenGewogen - $grootboekKostenOpbrengstTotaalGewogen[$categorie];
  if(!isset($this->performance[$periodeId]['totaal']['performance'][$categorie]))
  {
    if($gemiddelde < 0)
      $gemiddelde=$gemiddelde*-1;
   $performance = ((($eindWaarde - $beginWaarde) - $AttributieStortingenOntrekkingen + $grootboekOpbrengstTotaal[$categorie] - $grootboekKostenTotaal[$categorie]) / $gemiddelde) * 100;
   //$this->debug = true;
   $this->performance[$periodeId]['berekening']['periodeId']= $periodeId;
   $this->performance[$periodeId]['berekening']['gemiddeldeO']= "beginWaarde + AttributieStortingenOntrekkingenGewogen - grootboekKostenOpbrengstTotaalGewogen";
   $this->performance[$periodeId]['berekening']['gemiddelde'][$categorie]= "$beginWaarde + ".$AttributieStortingenOntrekkingenGewogen." - ".$grootboekKostenOpbrengstTotaalGewogen[$categorie]."";
   $this->performance[$periodeId]['berekening']['performanceO']= "(((eindWaarde - beginWaarde) - AttributieStortingenOntrekkingen + grootboekOpbrengstTotaal + grootboekKostenTotaal) / gemiddelde) * 100";
   $this->performance[$periodeId]['berekening']['performance'][$categorie]= "$performance = ((($eindWaarde - $beginWaarde) - ".$AttributieStortingenOntrekkingen." + ".$grootboekOpbrengstTotaal[$categorie]." - ".$grootboekKostenTotaal[$categorie].") / $gemiddelde) * 100";
   $this->performance[$periodeId]['totaal']['performance'][$categorie]=$performance;
  }


   $this->performance[$periodeId]['totaal']['stortingen'][$categorie]=$AttributieStortingen;
   $this->performance[$periodeId]['totaal']['onttrekkingen'][$categorie]=$AttributieOntrekkingen;
   $this->performance[$periodeId]['totaal']['opbrengsten'][$categorie]=$grootboekOpbrengstTotaal[$categorie];
   $this->performance[$periodeId]['totaal']['kosten'][$categorie]=$grootboekKostenTotaal[$categorie];

  if($action != 'skip' && $noUpdate == false)
  {
   if($this->dbWaarden[$categorie]['id'] >0)
   {
      if($this->indexSuperUser==false && date("Y",db2jul($periode['stop'])) != date('Y'))
      {
        $query="select 1";
        echo "Geen rechten om records in het verleden te vernieuwen. ".$this->pdata['Portefeuille']." ".$periode['stop']." <br>\n";
      }
      else
    $query = "UPDATE HistorischePortefeuilleIndex SET
  `Portefeuille` = '".$this->pdata['Portefeuille']."' ,
  `Categorie` = '$categorie',
  `periode`='m', 
  `Datum` = '".$periode['stop']."',
  `IndexWaarde` = '".round($performance,4)."',
  `PortefeuilleBeginWaarde` = '".round($beginWaarde,2)."',
  `PortefeuilleWaarde` = '".round($eindWaarde,2)."',
  `Stortingen` = '".round($AttributieStortingen,2)."',
  `Onttrekkingen` = '".round($AttributieOntrekkingen,2)."',
  `Opbrengsten` = '".round($grootboekOpbrengstTotaal[$categorie],2)."',
  `Kosten` = '".round($grootboekKostenTotaal[$categorie],2)."',
  `gemiddelde` = '".round($gemiddelde,2)."',
  `change_date` = NOW(),
  `change_user` = '$USR'
  WHERE id = '".$this->dbWaarden[$categorie]['id']."'";
 //echo $query;

  }
  else
  {
  $query = "INSERT INTO HistorischePortefeuilleIndex SET
  `Portefeuille` = '".$this->pdata['Portefeuille']."' ,
  `Categorie` = '$categorie',
  `periode`='m',
  `Datum` = '".$periode['stop']."',
  `IndexWaarde` = '".round($performance,4)."',
  `PortefeuilleBeginWaarde` = '".round($beginWaarde,2)."',
  `PortefeuilleWaarde` = '".round($eindWaarde,2)."',
  `Stortingen` = '".round($AttributieStortingen,2)."',
  `Onttrekkingen` = '".round($AttributieOntrekkingen,2)."',
  `Opbrengsten` = '".round($grootboekOpbrengstTotaal[$categorie],2)."',
  `Kosten` = '".round($grootboekKostenTotaal[$categorie],2)."',
  `gemiddelde` = '".round($gemiddelde,2)."',
  `add_date` = NOW(),
  `add_user` = '$USR',
  `change_date` = NOW(),
  `change_user` = '$USR';";
   }
	 $this->db->SQL($query);
	 $this->db->Query();
   }

      $totaalPerf[$categorie] = ($totaalPerf[$categorie]  * (100+$this->performance[$periodeId]['totaal']['performance'][$categorie])/100) ;
      $this->performance[$periodeNaam]['eindWaarde'][$categorie]=$this->performance[$periodeId]['totaalWaarde'][$categorie]['eind'] ;
      $this->performance[$periodeNaam]['stortingen'][$categorie]+=$AttributieStortingen;
      $this->performance[$periodeNaam]['onttrekkingen'][$categorie]+=$AttributieOntrekkingen;
      $this->performance[$periodeNaam]['opbrengsten'][$categorie]+=$grootboekOpbrengstTotaal[$categorie];
      $this->performance[$periodeNaam]['kosten'][$categorie]+=$grootboekKostenTotaal[$categorie];
      $this->performance[$periodeNaam]['ongerealiseerdResultaat'][$categorie]+= $this->performance[$periodeId]['ongerealiseerdResultaat'][$categorie]['eind'];
      $this->performance[$periodeNaam]['gemiddelde'][$categorie]+=$gemiddelde;
    }
    if($this->debug==true)
      listarray($this->performance[$periodeId]);
	}

	$this->performance[$periodeNaam]['periode'] = $datumBegin.'-'.$datumEind;
	foreach ($this->categorien as $categorie)
  {
    $this->performance[$periodeNaam]['beginWaarde'][$categorie]=$this->performance[$eerstePeriodeId]['totaalWaarde'][$categorie]['begin'];
    $this->performance[$periodeNaam]['mutatie'][$categorie] = $this->performance[$periodeNaam]['eindWaarde'][$categorie] - $this->performance[$periodeNaam]['beginWaarde'][$categorie];
    $this->performance[$periodeNaam]['resultaat'][$categorie] = $this->performance[$periodeNaam]['mutatie'][$categorie] - $this->performance[$periodeNaam]['stortingen'][$categorie]+$this->performance[$periodeNaam]['onttrekkingen'][$categorie];
    $this->performance[$periodeNaam]['performance'][$categorie] = $totaalPerf[$categorie] -100;
    $weging=$this->performance[$periodeNaam]['gemiddelde'][$categorie]/$this->performance[$periodeNaam]['gemiddelde']['Totaal'];
    $this->performance[$periodeNaam]['bijdrage'][$categorie]=$this->performance[$periodeNaam]['performance'][$categorie] * $weging;
  }

	return $totaalPerf;
}


	function Bereken()
	{
		$this->getAttributieCategorien();

		if($this->pdata['PerformanceBerekening'] == 6)
		  $periode = 'kwartaal';
		else
		  $periode = 'maand';

	//	  echo $this->pdata['Portefeuille']." ".$this->totaalOK." <br>\n";
	  $this->attributiePerformance($this->pdata['Portefeuille'],$this->pdata['rapportageDatumVanaf'],$this->pdata['rapportageDatum'],'all',$this->pdata['RapportageValuta'],$periode);
	}



	function getAttributieStortingenVOLK($categorie, $datumBegin, $datumEind,$valuta='EUR')
  { 
    global $__appvar;
	  $DB=new DB();

    $fondsQuery = 'Fonds';
    
    if ($valuta != "EUR" && $valuta != '')
    {
	    $koersQueryBoekdatum =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
      $koersQueryDatumBegin =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '$datumBegin' ORDER BY Datum DESC LIMIT 1 ) ";
      $koersQueryDatumEind =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '$datumEind' ORDER BY Datum DESC LIMIT 1 ) ";
	  }
    else
    {
	    $koersQueryBoekdatum = "";
      $koersQueryDatum = "";
      $koersQueryDatumEind ='';
    }
    
    $fonds    = array_keys($this->performance[$this->periodeId]['fondsen'][$categorie]);
    $rekening = array_keys($this->performance[$this->periodeId]['rekeningen'][$categorie]);

    if (!is_array($fonds))
      $fonds=array();

    if (!is_array($rekening))
      $rekening=array();


    $fondsRekening = array_merge($fonds,$rekening);

    $fondsenWhere = " IN('".implode('\',\'',$fonds)."') ";
    $rekeningWhere = " IN('".implode('\',\'',$rekening)."') ";
    $fondsRekeningWhere =" IN('".implode('\',\'',$fondsRekening)."') ";


    $beginwaarde = $this->performance[$this->periodeId]['totaalWaarde'][$categorie]['begin'];
    $eindwaarde  = $this->performance[$this->periodeId]['totaalWaarde'][$categorie]['eind'];

    $beginCorrectie=false;
	    if($beginwaarde == 0)
	    {
	      $query = "SELECT Rekeningmutaties.Boekdatum - INTERVAL 1 DAY as Boekdatum FROM  (Rekeningen, Portefeuilles)
	                LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE (Rekeningmutaties.Fonds $fondsenWhere OR Rekeningmutaties.Rekening $rekeningWhere ) AND
	                Rekeningen.Portefeuille = '".$this->pdata['Portefeuille']."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum asc LIMIT 1 ";
	      $DB->SQL($query);
	      $DB->Query();
	      $start = $DB->NextRecord();

	      if($start['Boekdatum'] != '')
	        $datumBegin = $start['Boekdatum'];
	      $beginCorrectie=true;

	    }

	    $eindCorrectie=false;
 	    if($eindwaarde == 0)
 	    {
 	      $query = "SELECT Rekeningmutaties.Boekdatum + INTERVAL 1 DAY as Boekdatum FROM  (Rekeningen, Portefeuilles)
	                LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE (Rekeningmutaties.Fonds $fondsenWhere OR Rekeningmutaties.Rekening $fondsenWhere ) AND
	                Rekeningen.Portefeuille = '".$this->pdata['Portefeuille']."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum desc LIMIT 1 ";
	      $DB->SQL($query);
	      $DB->Query();
	      $eind = $DB->NextRecord();
	      if($eind['Boekdatum'] != '')
	        $datumEind = $eind['Boekdatum'];
	      $eindCorrectie=true;
 	    }
      $datumBeginWeging=$datumBegin;

       $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBeginWeging."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 $koersQueryBoekdatum AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))*-1 $koersQueryBoekdatum  AS totaal ".
	              "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Grootboekrekeningen.Opbrengst = 0 AND Grootboekrekeningen.Kosten = 0 ) AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               Rekeningmutaties.Rekening $fondsRekeningWhere ";          
	     $DB->SQL($queryAttributieStortingenOntrekkingenRekening);//echo "$fonds $queryAttributieStortingenOntrekkingenRekening  <br>\n";
	     $DB->Query();
	     $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();

       $queryRekeningDirecteKostenOpbrengsten = "SELECT
                 SUM(((TO_DAYS('$datumEind') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$datumEind') - TO_DAYS('$datumBeginWeging')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) $koersQueryBoekdatum AS gewogen,
                 SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) $koersQueryBoekdatum AS totaal
                 FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                 JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                 WHERE
                 (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND   Rekeningmutaties.Fonds = '' AND
                 Rekeningen.Portefeuille = '".$this->pdata['Portefeuille']."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                 Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                 Rekeningmutaties.Boekdatum <= '$datumEind' AND
                 Rekeningmutaties.Rekening $fondsRekeningWhere  ";
                 
       $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
       $DB->Query();
       $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();


      $queryFondsDirecteKostenOpbrengsten = "SELECT SUM(((TO_DAYS('$datumEind') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$datumEind') - TO_DAYS('$datumBeginWeging')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) $koersQueryBoekdatum AS gewogen,
                       SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) $koersQueryBoekdatum AS totaal
                FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->pdata['Portefeuille']."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                Rekeningmutaties.Fonds $fondsRekeningWhere"; //      $DB->SQL($queryFondsDirecteKostenOpbrengsten); //echo "$fonds $query  <br>\n";
       $DB->Query();
       $FondsDirecteKostenOpbrengsten = $DB->NextRecord();


	     $queryAttributieStortingenOntrekkingen = "SELECT ".
	              "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBeginWeging."')) ".
	              "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) $koersQueryBoekdatum AS gewogen, ".
	              " SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum) AS totaal ".
                " FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	              "WHERE ".
	              "Rekeningen.Portefeuille = '".$this->pdata['Portefeuille']."' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	              "Rekeningmutaties.Grootboekrekening = 'FONDS' AND Rekeningmutaties.Fonds $fondsRekeningWhere ";
	     $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$query <br>\n";
	     $DB->Query();
	     $AttributieStortingenOntrekkingen = $DB->NextRecord();

       $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];

   	  $query = "SELECT SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) $koersQueryBoekdatum as totaal
 	              FROM Rekeningmutaties,Rekeningen
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	              Rekeningen.Rekening $fondsRekeningWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind'";

	     $DB->SQL($query);
	     $DB->Query();
	     $data = $DB->nextRecord();
	     $AttributieStortingenOntrekkingen['totaal'] -=$data['totaal'];

       $directeKostenOpbrengsten['totaal'] = $RekeningDirecteKostenOpbrengsten['totaal'] + $FondsDirecteKostenOpbrengsten['totaal'];
       $directeKostenOpbrengsten['gewogen'] = $RekeningDirecteKostenOpbrengsten['gewogen'] + $FondsDirecteKostenOpbrengsten['gewogen'];

      if($beginCorrectie && ($this->pdata['Vermogensbeheerder'] == 'WAT' || $this->pdata['Vermogensbeheerder'] == 'WAT1'))
      {
         $AttributieStortingenOntrekkingen['gewogen']=$AttributieStortingenOntrekkingen['totaal'];
         $directeKostenOpbrengsten['gewogen']=$directeKostenOpbrengsten['totaal'];
      }
      if($eindCorrectie && ($this->pdata['Vermogensbeheerder'] == 'WAT' || $this->pdata['Vermogensbeheerder'] == 'WAT1'))
      {
        $AttributieStortingenOntrekkingen['gewogen']=0;
        $directeKostenOpbrengsten['gewogen']=0;
      }

 	    $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'] - $directeKostenOpbrengsten['gewogen'] ;
      $performance = ((($eindwaarde - $beginwaarde) + $AttributieStortingenOntrekkingen['totaal'] + $directeKostenOpbrengsten['totaal'] ) / $gemiddelde) * 100;

  	  $debug=false; 
      //$debug=true;
      if($debug)
      {
       echo "<br>\n<br>\n$queryAttributieStortingenOntrekkingenRekening <br>\n $queryRekeningDirecteKostenOpbrengsten <br>\n " ;
       echo "$queryRekeningDirecteKostenOpbrengsten <br>\n $queryFondsDirecteKostenOpbrengsten <br>\n";
       echo "$queryAttributieStortingenOntrekkingenRekening <br>\n $queryAttributieStortingenOntrekkingen <br>\n";
       listarray($directeKostenOpbrengsten);
       listarray($AttributieStortingenOntrekkingen);
       echo "    <br>\n" ;
       echo "$fondsenWhere $datumBegin -> $datumEind <br>\n";
       echo "gemiddelde= 	 $gemiddelde = begin $beginwaarde -  gewogenSo ".$AttributieStortingenOntrekkingen['gewogen']." - gewogenDko ".$directeKostenOpbrengsten['gewogen']."<br>\n " ;
       echo "   $performance = ((($eindwaarde - $beginwaarde) + ".$AttributieStortingenOntrekkingen['totaal']." + ".$directeKostenOpbrengsten['totaal']." ) / $gemiddelde) * 100;	<br>\n";
       ob_flush();
      }


      return $AttributieStortingenOntrekkingen['gewogen'];

	}
  
  
function performanceMeting($portefeuille, $datumBegin, $datumEind, $type = "1", $valuta = 'EUR')
{
	global $__appvar;
  $DB = new DB();
  $query="SELECT layout FROM Vermogensbeheerders JOIN Portefeuilles on Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder WHERE Portefeuille='$portefeuille'";
  $DB->SQL($query);
  $layout=$DB->lookupRecord();
  if(file_exists($__appvar["basedir"]."/html/rapport/include/ATTberekening_L".$layout['layout'].".php"))
  {  
    include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L".$layout['layout'].".php");
    $attObject="ATTberekening_L".$layout['layout'];
    $att=new $attObject();
    if(method_exists("ATTberekening_L".$layout['layout'],'getPerf'))
    {
      return $att->getPerf($portefeuille, $datumBegin, $datumEind);
    }
  }

	if($type == 6)//Attributie kwartaalwaardering
	{
	  $index=new rapportATTberekening($portefeuille);
	  $index->categorien[] = 'Totaal';
	  $performance = $index->attributiePerformance($portefeuille, $datumBegin, $datumEind,'all',$valuta,'kwartaal');
	  return $performance['Totaal'] -100;
	}
	elseif($type == 5)//Maandelijkse waardering realtime?
	{
	  $index=new indexHerberekening();
    $indexData = $index->getWaardenATT($datumBegin, $datumEind,$portefeuille,'Totaal','maand',$valuta);
		foreach ($indexData as $data)
		{
		  $performance =  $data['index'] -100;
		}
	  return $performance;
	}
	elseif($type == 3)//TWR
	{
	  $index=new indexHerberekening();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','TWR');
		foreach ($indexData as $data)
		  $performance =  $data['index'] -100;
	  return $performance;
	}
	elseif($type == 4)//Maandelijkse waardering
	{
	  $index=new indexHerberekening_L51();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','maanden',$valuta);
		foreach ($indexData as $data)
		{
		  $performance =  $data['index'] -100;
		}
	  return $performance;

	}
	elseif($type == 7)//Dagelijkse YtD waardering
	{
	  $index=new indexHerberekening();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','dagYTD');
		foreach ($indexData as $data)
		{
		  $performance =  $data['index'] -100;
		}
	  return $performance;

	}
	elseif($type == 8)//Kwartaal waardering
	{
	  $index=new indexHerberekening();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','kwartaal',$valuta);
		foreach ($indexData as $data)
		{
		  $performance =  $data['index'] -100;
		}
	  return $performance;
	}  

	if ($valuta != "EUR" )
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	  $koersQuery = "";

  if(substr($datumBegin,0,4)==substr($datumEind,0,4) || ((substr($datumBegin,5,5)=='31-12') && substr($datumEind,5,5)=='01-01') )
  {
	// haal beginwaarde op.
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				 "FROM TijdelijkeRapportage WHERE ".
				 " rapportageDatum = '".$datumBegin."' AND ".
				 " portefeuille = '".$portefeuille."' "
				 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$beginwaarde = $DB->NextRecord();
	//echo $beginwaarde." = ".$beginwaarde[totaal]." / ".getValutaKoers($valuta,$datumBegin)."<br>";
	$beginwaarde = $beginwaarde['totaal'] / getValutaKoers($valuta,$datumBegin);

	// haal eindwaarde op.
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				 "FROM TijdelijkeRapportage WHERE ".
				 " rapportageDatum ='".$datumEind."' AND ".
				 " portefeuille = '".$portefeuille."' "
				 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query); 
	$DB->Query();
	$eindwaarde = $DB->NextRecord();
	$eindwaarde = $eindwaarde['totaal']  / getValutaKoers($valuta,$datumEind);

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles)
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query);
	$DB->Query();
	$weging = $DB->NextRecord();

  $gemiddelde = $beginwaarde + $weging['totaal1'];
  if($gemiddelde <> 0)
    $performance = ((($eindwaarde - $beginwaarde) - $weging['totaal2']) / $gemiddelde) * 100;
  }
  else
  {
    $index=new indexHerberekening();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','jaar',$valuta);
    foreach($indexData as $index)
      $performance=$index['index']-100;
  }
//echo "gemiddelde $gemiddelde = $beginwaarde + ".$weging[totaal1]."\n<br>\n";
//echo "$datumBegin - $datumEind -> performance = $performance = ((($eindwaarde - $beginwaarde) - ".$weging[totaal2].") / $gemiddelde) * 100";flush();
//echo "<br>$performance<br>";
return $performance;
}


}


class indexHerberekening_L51
{
	function indexHerberekening_L51( $selectData )
	{
		$this->selectData = $selectData;
	}

	function formatGetal($waarde, $dec=2)
	{
		return number_format($waarde,$dec,",",".");
	}

	function BerekenMutaties($beginDatum,$eindDatum,$portefeuille)
	{
		$totaalWaarde =array();
		$db = new DB();

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;


		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,'EUR',$beginDatum);

	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,'EUR',$beginDatum);

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }
	  $DB=new DB();

  	$query = "SELECT SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
 	  "  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$beginDatum."')) ".
	  "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	  "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	  "FROM  (Rekeningen, Portefeuilles ) Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
  	"WHERE ".
  	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
  	"Rekeningmutaties.Verwerkt = '1' AND ".
  	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
  	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND ".
	  "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
  	$DB->SQL($query);
  	$DB->Query();
  	$weging = $DB->NextRecord();
    $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
  	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;

    $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
	  $stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum);
  	$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum);
  	$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

	  $query = "SELECT SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) - SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();

    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($resultaatVerslagperiode+$kosten['totaalkosten'],2);
    $data['performance'] =$performance;
    return $data;

	}


	function BerekenMutaties2($beginDatum,$eindDatum,$portefeuille,$valuta='EUR')
	{
	  if(substr($beginDatum,5,5)=='12-31')
	   $beginDatum=(substr($beginDatum,0,4)+1).'-01-01';

	  if ($valuta != "EUR" )
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$totaalWaarde =array();
		$db = new DB();

		$query="SELECT Portefeuilles.Startdatum FROM Portefeuilles WHERE Portefeuilles.Portefeuille='$portefeuille'";
		$db->SQL($query);
		$startDatum=$db->lookupRecord();

		$query="SELECT
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Afdrukvolgorde,
BeleggingscategoriePerFonds.Vermogensbeheerder,
Portefeuilles.Portefeuille
FROM
Beleggingscategorien
Inner Join BeleggingscategoriePerFonds ON Beleggingscategorien.Beleggingscategorie = BeleggingscategoriePerFonds.Beleggingscategorie
Inner Join Portefeuilles ON BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE Portefeuilles.Portefeuille='$portefeuille'
GROUP BY Beleggingscategorien.Beleggingscategorie
ORDER BY Afdrukvolgorde desc";
  		$db->SQL($query);
			$db->Query();
     $this->categorieVolgorde['LIQ']=0;
			while($data=$db->nextRecord())
				  $this->categorieVolgorde[$data['Beleggingscategorie']]=0;


    if(db2jul($beginDatum) <= db2jul($startDatum['Startdatum']))
      $wegingsDatum=date('Y-m-d',db2jul($startDatum['Startdatum'])+86400); //$startDatum['Startdatum'];
    else
      $wegingsDatum=$beginDatum;

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;

		$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,$valuta,true);
		//echo "att $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);<br>\n";

		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,$valuta,$beginDatum);

		if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$beginDatum);
		else
		  $valutaKoers=1;
	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
	    $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,$valuta,$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;

   // listarray($categorieVerdeling);
   	if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$eindDatum);
		else
		  $valutaKoers=1;

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
	    $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];

      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['VAR'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
	  }


	  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
	  $DB=new DB();

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles,Grootboekrekeningen )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND
	Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query);
	$DB->Query();
	$weging = $DB->NextRecord();

  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
  if($gemiddelde < 0)
    $gemiddelde=$gemiddelde*-1;
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;

//echo "perf $eindDatum $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100;<br>\n";
	  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum,$valuta);
		$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum,$valuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery)  AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaalOpbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $opbrengsten = $db->lookupRecord();

    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;

    foreach ($categorieVerdeling as $cat=>$waarde)
      $categorieVerdeling[$cat]=$waarde."";

    $data['valuta']=$valuta;
    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['gemiddelde'] = $gemiddelde;
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat;
    $data['extra']['cat']=$categorieVerdeling;
    return $data;

	}


	function getWaarden($datumBegin,$datumEind,$portefeuille,$specifiekeIndex='',$methode='maanden',$valuta='EUR',$output='')
	{
	  if(is_array($portefeuille))
	  {
	    $portefeuilles=$portefeuille[1];
	    $portefeuille=$portefeuille[0];
	  }
		$db=new DB();
    $julBegin = db2jul($datumBegin);
    $beginDatum=date("Y-m-d",$julBegin);
    $julEind = db2jul($datumEind);

   	$eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);
    $begindag = date("d",$julBegin);

    $vorigeIndex = 100;
    $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
    $datum == array();

  if($methode=='maanden')
  {
     $datum=$this->getMaanden($julBegin,$julEind);
     $type='m';
  }
  elseif($methode=='dagKwartaal')
  {
    $datum=$this->getDagen($julBegin,$julEind);
    $type='dk';
  }
  elseif($methode=='kwartaal')
  {
    $datum=$this->getKwartalen($julBegin,$julEind);
    $type='k';
  }
  elseif($methode=='jaar')
  {
    $datum=$this->getJaren($julBegin,$julEind);
    $type='j';
  }  
  elseif($methode=='TWR')
  {
    $datum=$this->getTWRstortingsdagen($portefeuille,$julBegin,$julEind);
    $type='t';
  }  
  elseif($methode=='dagYTD')
  {
     //$datum=$this->getDagen($julBegin,$julEind,'jaar');
     $datum=array();
      $newJul=$julBegin;
      while($newJul < $julEind)
      {
        $newJul=$newJul+86400;
        $datum[]=array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$newJul));
      }
     $type='dy';
  }
  elseif ($methode=='halveMaanden')
  {
    $datum=$this->getHalveMaanden($julBegin,$julEind);
    $type='2w';
  }
  elseif($methode=='weken')
  {
    $datum=$this->getWeken($julBegin,$julEind);
    $type='w';
  }
  elseif($methode=='dagen')
  {
    $datum=$this->getDagen2($julBegin,$julEind);
    $type='d';
  }
/*
	if($i==0)
    $datum[$i]['start']=$datumBegin;
	else
	  $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
	$datum[$i]['stop']=$datumEind;
*/

	$i=1;
	$indexData['index']=100;
	$indexData['specifiekeIndex']=100;
	$kwartaalBegin=100;

	$huidigeIndex=$specifiekeIndex;
  $jsonOutput=array('label'=>$portefeuille,'data'=>array());
	foreach ($datum as $periode)
	{
	    if($specifiekeIndex != '')
	    {
	      //if($specifiekeIndex )
        /*
//	      $query="SELECT specifiekeIndex FROM HistorischeSpecifiekeIndex WHERE portefeuille='$portefeuille' AND tot > '".$periode['stop']."' ORDER BY tot desc limit 1";
	      $db->SQL($query);
        $oldIndex=$db->lookupRecord();
        if($oldIndex['specifiekeIndex'] <> '')
        {
          $specifiekeIndex=$oldIndex['specifiekeIndex'];
          unset($startSpecifiekeIndexKoers);
        }
        else
        {
          if($huidigeIndex <> $specifiekeIndex)
            unset($startSpecifiekeIndexKoers);
          $specifiekeIndex=$huidigeIndex;
        }
        */
	      if(empty($startSpecifiekeIndexKoers))
	      {
	        $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum <= '".$periode['start']."' ORDER BY Datum DESC limit 1 ";
	        $db->SQL($query);
	        $specifiekeIndexData = $db->lookupRecord();
	        $startSpecifiekeIndexKoers=$specifiekeIndexData['Koers'];
	      }
	      $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum <= '".$periode['stop']."' ORDER BY Datum DESC limit 1 ";
	      $db->SQL($query);
	      $specifiekeIndexData = $db->lookupRecord();
	      $specifiekeIndexKoers = $specifiekeIndexData['Koers'];
	    }
      $specifiekeIndexWaarden[$i] =($specifiekeIndexKoers/$startSpecifiekeIndexKoers)*100;

	  	$query = "SELECT indexWaarde, Datum, PortefeuilleWaarde, PortefeuilleBeginWaarde, Stortingen, Onttrekkingen, Opbrengsten, Kosten ,Categorie, gerealiseerd,ongerealiseerd,rente,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='$type' AND
		            portefeuille = '".$portefeuille."' AND
		            Datum = '".substr($periode['stop'],0,10)."' ";

	  	if(db2jul($periode['start']) == db2jul($periode['stop']))
	  	{

	  	}
	  	elseif($db->QRecords($query) > 0 && ($valuta == 'EUR' || $valuta == ''))
	  	{
	  	  $dbData = $db->nextRecord();
	  	  $indexData['periodeForm'] = jul2form(db2jul($periode['start']))." - ".jul2form(db2jul($periode['stop']));
	  	  $indexData['periode']= $periode['start']."->".$periode['stop'];
	  	  $indexData['waardeMutatie'] = $dbData['PortefeuilleWaarde']-$dbData['PortefeuilleBeginWaarde'];
        $indexData['waardeBegin'] = $dbData['PortefeuilleWaarde']-$indexData['waardeMutatie'];
	  	  $indexData['waardeHuidige'] = $dbData['PortefeuilleWaarde'];
	  	  $indexData['stortingen'] = $dbData['Stortingen'];
	  	  $indexData['onttrekkingen'] = $dbData['Onttrekkingen'];
	      $indexData['resultaatVerslagperiode'] =  $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
	  	  $indexData['kosten'] = $dbData['Kosten'];
	  	  $indexData['opbrengsten'] = $dbData['Opbrengsten'];
	  	  $indexData['performance'] = $dbData['indexWaarde'];
  	    //$indexData['resultaatVerslagperiode'] = $dbData['Opbrengsten']-$dbData['Kosten'];
  	    $indexData['gerealiseerd'] = $dbData['gerealiseerd'];
  	    $indexData['ongerealiseerd'] = $dbData['ongerealiseerd'];
  	    $indexData['rente'] = $dbData['rente'];
  	    $indexData['extra'] = unserialize($dbData['extra']);
	  	}
	  	else
	  	{
	  	  if(isset($portefeuilles) && ($valuta == 'EUR' || $valuta == ''))
	  	  {
	  	    $query = "SELECT  Datum, sum(PortefeuilleWaarde) as PortefeuilleWaarde, sum(PortefeuilleBeginWaarde) as PortefeuilleBeginWaarde,
	  	    sum(Stortingen) as Stortingen, sum(Onttrekkingen) as Onttrekkingen, sum(Opbrengsten) as Opbrengsten, sum(Kosten) as Kosten ,Categorie, SUM(gerealiseerd) as gerealiseerd,
	  	    sum(ongerealiseerd) as ongerealiseerd, sum(rente) as rente, sum(gemiddelde) as gemiddelde,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='$type' AND
		            portefeuille IN ('".implode("','",$portefeuilles)."') AND
		            Datum = '".substr($periode['stop'],0,10)."' GROUP BY Datum";

	  	    if($db->QRecords($query) > 0)
	  	    {
	  	    $dbData = $db->nextRecord();
	  	    $indexData['periodeForm'] = jul2form(db2jul($periode['start']))." - ".jul2form(db2jul($periode['stop']));
	  	    $indexData['periode']= $periode['start']."->".$periode['stop'];
	  	    $indexData['waardeMutatie'] = $dbData['PortefeuilleWaarde']-$dbData['PortefeuilleBeginWaarde'];
          $indexData['waardeBegin'] = $dbData['PortefeuilleWaarde']-$indexData['waardeMutatie'];
	  	    $indexData['waardeHuidige'] = $dbData['PortefeuilleWaarde'];
	  	    $indexData['stortingen'] = $dbData['Stortingen'];
	  	    $indexData['onttrekkingen'] = $dbData['Onttrekkingen'];
	        $indexData['resultaatVerslagperiode'] =  $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
	  	    $indexData['kosten'] = $dbData['Kosten'];
	  	    $indexData['opbrengsten'] = $dbData['Opbrengsten'];
	  	    $indexData['performance'] = $indexData['resultaatVerslagperiode']/$dbData['gemiddelde']*100;
  	    //$indexData['resultaatVerslagperiode'] = $dbData['Opbrengsten']-$dbData['Kosten'];
  	      $indexData['gerealiseerd'] = $dbData['gerealiseerd'];
  	      $indexData['ongerealiseerd'] = $dbData['ongerealiseerd'];
  	      $indexData['rente'] = $dbData['rente'];
  	      $indexData['extra'] = unserialize($dbData['extra']);
  	      //listarray($indexData);
	    	  }
	    	  else
	  	      $indexData = array_merge($indexData,$this->BerekenMutaties2($periode['start'],$periode['stop'],$portefeuille));
	  	  }
        else
	  	    $indexData = array_merge($indexData,$this->BerekenMutaties2($periode['start'],$periode['stop'],$portefeuille,$valuta));
	  	}

	  	$indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'],-10,10)));
//          echo $indexData['periode']." ".$indexData['performance']."<br>\n";
	  	if($methode=='dagKwartaal')
	  	{
	  	  if($periode['blok'] <> $lastBlok)
	  	    $kwartaalBegin=$indexData['index'];
	  	  $indexData['index'] = ($kwartaalBegin  * (100+$indexData['performance'])/100);
	  	  $lastBlok=$periode['blok'];
        $data[$i] = array('index'=>$indexData['index'],'performance'=>$indexData['performance'],'datum'=>$indexData['datum'],'performance'=>$indexData['performance'],'periodeForm'=>$indexData['periodeForm']);
	  	}
	  	if($methode=='dagYTD')
	  	{
	  	  $indexData['index']=$indexData['performance']+100;
        $data[$i] = array('index'=>$indexData['index'],'performance'=>$indexData['performance'],'datum'=>$indexData['datum'],'periodeForm'=>$indexData['periodeForm']);
	  	}
	  	else
	  	{

        if(empty($specifiekeIndexWaarden[$i-1]))
	    	  $indexData['specifiekeIndexPerformance'] = $specifiekeIndexWaarden[$i]-100;
	    	else
	    	  $indexData['specifiekeIndexPerformance'] =($specifiekeIndexWaarden[$i]/$specifiekeIndexWaarden[$i-1])*100 -100;
	      $indexData['specifiekeIndex'] = ($indexData['specifiekeIndex']  * (100+$indexData['specifiekeIndexPerformance'])/100) ;
	      if(empty($indexData['index']))
	        $indexData['index']=100;
	  	  $indexData['index'] = ($indexData['index']  * (100+$indexData['performance'])/100);
	      $data[$i] = $indexData;
	  	}
      /*)
      if($output=='html')
      {
        
        $jsonOutput['data'][]=array(adodb_db2jul($data[$i]['datum'])*1000,$data[$i]['index']);
        //
        $dbData=mysql_real_escape_string(serialize($data[$i]));
        $query="INSERT INTO CRM_htmlData SET 
        portefeuille='$portefeuille',
        datum='".$data[$i]['datum']."',
        dataType='perf',
        data='".$dbData."',
        add_user='$USR',change_user='$USR',add_date=NOW(),change_date=NOW()";
        $db->SQL($query);
        $db->Query();
        //
        file_put_contents('../tmp/perf.json',json_encode($jsonOutput));
      }
      */

  $i++;
	}

	return $data;
	}

	function getWaardenATT($datumBegin,$datumEind,$portefeuille,$categorie='Totaal',$periodeBlok='maand',$valuta='EUR')
	{
	  $this->berekening = new rapportATTberekening($portefeuille);
	  if(is_array($categorie))
	    $this->berekening->categorien = $categorie;
	  else
      $this->berekening->categorien[] = $categorie;
    $this->berekening->pdata['pdf']=true;
    $this->berekening->attributiePerformance($portefeuille,$datumBegin,$datumEind,'rapportagePeriode',$valuta,$periodeBlok);

    foreach ($this->berekening->categorien as $categorie)
    {
      $indexData['index'] = 100;
      foreach ($this->berekening->performance as $periode=>$data)
      {
        if($periode != 'rapportagePeriode')
        {
    	  $indexData['periodeForm']    = jul2form(db2jul(substr($periode,0,10)))." - ".jul2form(db2jul(substr($periode,11)));
  	    $indexData['waardeMutatie']  = $data['totaalWaarde'][$categorie]['eind']-$data['totaalWaarde'][$categorie]['begin'];
        $indexData['waardeBegin']    = $data['totaalWaarde'][$categorie]['begin'];
	  	  $indexData['waardeHuidige']  = $data['totaalWaarde'][$categorie]['eind'];
	  	  $indexData['stortingen']     = $data['AttributieStortingenOntrekkingen'][$categorie]['stortingen'];
	  	  $indexData['onttrekkingen']  = $data['AttributieStortingenOntrekkingen'][$categorie]['onttrekkingen'];
	  	  $indexData['resultaatVerslagperiode'] = $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
	   	  $indexData['kosten']         = $data['totaal']['kosten'][$categorie];
	   	  $indexData['opbrengsten']    = $data['totaal']['opbrengsten'][$categorie];
	   	  $indexData['performance']    = $data['totaal']['performance'][$categorie];
	   	  $indexData['index']          = ($indexData['index']  * (100+$indexData['performance'])/100);
	   	  $indexData['datum']          = substr($periode,11);
	   	  if(count($this->berekening->categorien)>1)
	   	  $tmp[$categorie][] = $indexData;
	   	  else
	  	  $tmp[] = $indexData;
        }
      }
    }
	  return $tmp;
	}




	function Bereken()
	{
	  $einddatum = jul2sql($this->selectData[datumTm]);

		$jaar = date("Y",$this->datumTm);

		// controle op einddatum portefeuille
		$extraquery  .= " Portefeuilles.Einddatum > '".jul2db($this->selectData[datumTm])."' AND";

		// selectie scherm.
		if($this->selectData[portefeuilleTm])
			$extraquery .= " (Portefeuilles.Portefeuille >= '".$this->selectData[portefeuilleVan]."' AND Portefeuilles.Portefeuille <= '".$this->selectData[portefeuilleTm]."') AND";
		if($this->selectData[vermogensbeheerderTm])
			$extraquery .= " (Portefeuilles.Vermogensbeheerder >= '".$this->selectData[vermogensbeheerderVan]."' AND Portefeuilles.Vermogensbeheerder <= '".$this->selectData[vermogensbeheerderTm]."') AND ";
		if($this->selectData[accountmanagerTm])
			$extraquery .= " (Portefeuilles.Accountmanager >= '".$this->selectData[accountmanagerVan]."' AND Portefeuilles.Accountmanager <= '".$this->selectData[accountmanagerTm]."') AND ";
		if($this->selectData[depotbankTm])
			$extraquery .= " (Portefeuilles.Depotbank >= '".$this->selectData[depotbankVan]."' AND Portefeuilles.Depotbank <= '".$this->selectData[depotbankTm]."') AND ";
		if($this->selectData[AFMprofielTm])
			$extraquery .= " (Portefeuilles.AFMprofiel >= '".$this->selectData[AFMprofielVan]."' AND Portefeuilles.AFMprofiel <= '".$this->selectData[AFMprofielTm]."') AND ";
		if($this->selectData[RisicoklasseTm])
			$extraquery .= " (Portefeuilles.Risicoklasse >= '".$this->selectData[RisicoklasseVan]."' AND Portefeuilles.Risicoklasse <= '".$this->selectData[RisicoklasseTm]."') AND ";
		if($this->selectData[SoortOvereenkomstTm])
			$extraquery .= " (Portefeuilles.SoortOvereenkomst >= '".$this->selectData[SoortOvereenkomstVan]."' AND Portefeuilles.SoortOvereenkomst <= '".$this->selectData[SoortOvereenkomstTm]."') AND ";
		if($this->selectData[RemisierTm])
			$extraquery .= " (Portefeuilles.Remisier >= '".$this->selectData[RemisierVan]."' AND Portefeuilles.Remisier <= '".$this->selectData[RemisierTm]."') AND ";
		if($this->selectData['clientTm'])
		  $extraquery .= " (Portefeuilles.Client >= '".$this->selectData['clientVan']."' AND Portefeuilles.Client <= '".$this->selectData['clientTm']."') AND ";
		if (count($this->selectData['selectedPortefeuilles']) > 0)
		{
		 $portefeuilleSelectie = implode('\',\'',$this->selectData['selectedPortefeuilles']);
	   $extraquery .= " Portefeuilles.Portefeuille IN('$portefeuilleSelectie') AND ";
		}

		if(checkAccess($type))
			$join = "";
		else
			$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$this->USR."'";

		$query = " SELECT ".
						 " Portefeuilles.Vermogensbeheerder, ".
						 " Portefeuilles.Risicoklasse, ".
						 " Portefeuilles.Portefeuille, ".
						 " Portefeuilles.Startdatum, ".
						 " Portefeuilles.Einddatum, ".
						 " Portefeuilles.Client, ".
						 " Portefeuilles.Depotbank, ".
			//			 " Portefeuilles.RapportageValuta, ".
						 " Vermogensbeheerders.attributieInPerformance,
						   Vermogensbeheerders.PerformanceBerekening, ".
						 " Clienten.Naam,  ".
						 " Portefeuilles.ClientVermogensbeheerder  ".
					 " FROM (Portefeuilles, Clienten ,Vermogensbeheerders) ".$join." WHERE ".$extraquery.
					 " Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
					 " ORDER BY Portefeuilles.Portefeuille ";

		$DBs = new DB();
		$DBs->SQL($query);
		$DBs->Query();

		$DB2 = new DB();
		$records = $DBs->records();
		if($records <= 0)
		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			if($this->progressbar)
			$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}


  	while($pdata = $DBs->nextRecord())
		{
		 	if($this->progressbar)
		  {
		  	$pro_step += $pro_multiplier;
		  	$this->progressbar->moveStep($pro_step);
		  }

	 	if($pdata['Vermogensbeheerder'] == 'WAT' || $pdata['Vermogensbeheerder'] == 'WAT1' || $pdata['Vermogensbeheerder'] == 'WWO')
    {
      $pdata['rapportageDatum']=jul2sql($this->selectData['datumTm']);
      $pdata['rapportageDatumVanaf']=jul2sql($this->selectData['datumVan']);
      $pdata['aanvullen']=$this->selectData['aanvullen'];
      $pdata['debug']=$this->selectData['debug'];
      $berekening = new rapportATTberekening($pdata);
      $berekening->pdata['pdf']=false;
      $berekening->indexSuperUser=$this->indexSuperUser;
      $berekening->Bereken();
      // listarray($berekening->performance);
      //  exit;
    }
    else
    {

      $pstartJul = db2jul($pdata['Startdatum']);
	    if($pstartJul > $this->selectData['datumVan'])
	      $julBegin= $pstartJul;
      else
        $julBegin = $this->selectData['datumVan'];

      $julEind = $this->selectData['datumTm'];
      if($pdata['Vermogensbeheerder'] == 'SEQ')
      {
    	  $datum = $this->getKwartalen($julBegin,$julEind);
        $type='k';
    	}
      else
    	{
    	  $datum = $this->getMaanden($julBegin,$julEind);
        $type='m';
      }
      $portefeuille = $pdata['Portefeuille'];

		$indexAanwezig = array();
	  if ($this->selectData['aanvullen'] == 1)
	  {
	    $query = "SELECT Datum FROM HistorischePortefeuilleIndex WHERE Portefeuille = '$portefeuille' AND periode='$type' AND Categorie = 'Totaal' ";
	    $DB2->SQL($query);
	    $DB2->Query();
      while ($data = $DB2->nextRecord())
	    {
         $indexAanwezig[] = $data['Datum'];
	    }
    }

    //rvv debug
    if($pdata['Vermogensbeheerder'] == "HEN" || $pdata['PerformanceBerekening'] == 7)
    {
      $datum=array();
      $newJul=$julBegin;
      $type='dy';
      while($newJul < $julEind)
      {
        $newJul=$newJul+86400;
        $datum[]=array('start'=>date('Y',$julBegin)."-01-01",'stop'=>date('Y-m-d',$newJul));
      }
    }
    //echo $portefeuille."<br>\n";
//listarray($datum);
			for ($i=0; $i < count($datum); $i++) //Bereken Performance voor data
		  {
		    $done=false;
	      $startjaar = date("Y",db2jul($datum[$i]['start']))+1;
   	    if(db2jul($datum[$i]['start']) == mktime (0,0,0,1,0,$startjaar))
	        $datum[$i]['start']= $startjaar.'-01-01';

	      if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['start']))
	        $datum[$i]['start'] = $pdata['Startdatum'];

			   if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['stop'])) //Wanneer de portefeuille nog niet bestond geen performance.
			   {
			     $datum[$i]['performance']=0;
			     $done = true;
			   }
			   elseif(in_array(substr($datum[$i]['stop'],0,10),$indexAanwezig))
		     {
           $done = true;
  		   }
  		   elseif(db2jul($datum[$i]['start']) == db2jul($datum[$i]['stop']))
	  	   {
	  	    //echo "overslaan<br>";
	  	   }
			   else // Normale berekening.
			   {
			     if($pdata['Vermogensbeheerder'] == "HEN")
			     {
             include_once("../classes/AE_cls_fpdf.php");
             include_once("rapport/PDFRapport.php");
             include_once("rapport/include/RapportPERF_L26.php");

			       $pdf = new PDFRapport('L','mm');
             $pdf->rapportageValuta = "EUR";
	           $pdf->ValutaKoersEind  = 1;
             $pdf->ValutaKoersStart = 1;
             $pdf->ValutaKoersBegin = 1;
             loadLayoutSettings($pdf, $portefeuille);
             if(substr($datum[$i]['start'],5,5)=='01-01')
               $startjaar=true;
             else
               $startjaar=false;
             $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$datum[$i]['start'],$startjaar,$pdata['RapportageValuta'],$datum[$i]['start']);
             vulTijdelijkeTabel($fondswaarden ,$portefeuille,$periode['start']);
             $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$datum[$i]['stop'],$startjaar,$pdata['RapportageValuta'],$datum[$i]['start']);
             vulTijdelijkeTabel($fondswaarden ,$portefeuille,$datum[$i]['stop']);

             $pdf->PortefeuilleStartdatum=$pdata['Startdatum'];
             $pdf->HENIndex=true;
             $rapport = new RapportPERF_L26($pdf, $portefeuille, $datum[$i]['start'], $datum[$i]['stop']);
	           $rapport->writeRapport();

             foreach ($datum as $periode)
             {
               verwijderTijdelijkeTabel($portefeuille,$datum[$i]['start']);
               verwijderTijdelijkeTabel($portefeuille,$datum[$i]['stop']);
             }
             $PerformanceMeting=$rapport->pdf->excelData;

             $performance= number_format($PerformanceMeting[0][37],4) ;
             $data['waardeHuidige']=$PerformanceMeting[0][29];
			       $data['waardeBegin']=$PerformanceMeting[0][28];
 		         $data['stortingen']=$PerformanceMeting[0][30];
			       $data['onttrekkingen']=0;
			       $data['opbrengsten']=$PerformanceMeting[0][32];
			       $data['kosten']=$PerformanceMeting[1][13];
			     }
			     else
			     {
             $data = $this->berekenMutaties2($datum[$i]['start'],$datum[$i]['stop'],$portefeuille);
		         $performance = number_format($data['performance'],4) ;
			     }
		    $query = "SELECT id FROM HistorischePortefeuilleIndex WHERE periode='$type' AND Portefeuille = '$portefeuille' AND Datum = '".substr($datum[$i]['stop'],0,10)."' ";
		    $DB2->SQL($query);
		    $DB2->Query();
		    $records = $DB2->records();
		    if($records > 1)
		    {
		      echo "<script  type=\"text/JavaScript\">alert('Dubbele record gevonden voor portefeuille $portefeuille en datum ".substr($datum[$i]['stop'],0,10)."'); </script>";
		    }
		    $qBody=	    " Portefeuille = '$portefeuille' ,
			                Categorie = 'Totaal',
			                PortefeuilleWaarde = '".round($data['waardeHuidige'],2)."' ,
			                PortefeuilleBeginWaarde = '".round($data['waardeBegin'],2)."' ,
 		                  Stortingen = '".round($data['stortingen'],2)."' ,
			                Onttrekkingen = '".round($data['onttrekkingen'],2)."' ,
			                Opbrengsten = '".round($data['opbrengsten'],2)."' ,
			                Kosten = '".round($data['kosten'],2)."' ,
			                Datum = '".$datum[$i]['stop']."',
			                IndexWaarde = '$performance' ,
                      periode='$type',
			                gerealiseerd = '".round($data['gerealiseerd'],2)."',
			                ongerealiseerd = '".round($data['ongerealiseerd'],2)."',
			                rente = '".round($data['rente'],2)."',
			                extra = '".addslashes(serialize($data['extra']))."',
			                gemiddelde = '".round($data['gemiddelde'],2)."',
			                ";

		    if ($records > 0)
		    {
		      $id = $DB2->lookupRecord();
		      $id = $id['id'];


          if($this->indexSuperUser==false && date("Y",db2jul($datum[$i]['stop'])) != date('Y'))
          {
            $query="select 1";
            echo "Geen rechten om records in het verleden te vernieuwen. $portefeuille ".$datum[$i]['stop']."<br>\n";
          }
          else
		        $query = "UPDATE
			                HistorischePortefeuilleIndex
			              SET
                      $qBody
			                change_date = NOW(),
			                change_user = '$this->USR'
			               WHERE id = $id ";
		    }
		    else
		    {
			    $query = "INSERT INTO
			                HistorischePortefeuilleIndex
			              SET
                      $qBody
			                change_date = NOW(),
			                change_user = '$this->USR',
			                add_date = NOW(),
			                add_user = '$this->USR' ";
		    }
			  if((db2jul($pdata['Startdatum']) < db2jul($datum[$i]['stop'])) && $done == false)
			  {
			    $DB2->SQL($query);
			    $DB2->Query();
			  }
		  }
		}
	}
		}
	if($this->progressbar)
	{
	  $this->progressbar->hide();
  	exit;
	}
}

function getKwartalen($julBegin, $julEind)
{
   if($julBegin > $julEind )
     return array();
   $beginjaar = date("Y",$julBegin);
   $eindjaar = date("Y",$julEind);
   $maandenStap=3;
   $stap=1;
   $n=0;
   $teller=$julBegin;
   $kwartaalGrenzen=array();
   $datum=array();

   while ($teller < $julEind)
   {
     $teller = mktime (0,0,0,$stap,0,$beginjaar);
     $stap +=$maandenStap;
     if($teller > $julBegin && $teller < $julEind)
     {
     $grensDatum=date("d-m-Y",$teller);
     $kwartaalGrenzen[] = $teller;
     }
   }
   if(count($kwartaalGrenzen) > 0)
   {
     $datum[$n]['start']=date('Y-m-d',$julBegin);
     foreach ($kwartaalGrenzen as $grens)
     {
       $datum[$n]['stop']=date('Y-m-d',$grens);
       $n++;
       $start=date('Y-m-d',$grens);
       if(substr($start,-5)=='12-31')
        $start=(substr($start,0,4)+1).'-01-01';

       $datum[$n]['start']=$start;
     }
     $datum[$n]['stop']=date('Y-m-d',$julEind);
   }
   else
   {
     $datum[]=array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$julEind));
   }
 	 return $datum;
}

function getMaanden($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
}

function getJaren($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,1,0,$beginjaar+$i);
	    $counterEnd   = mktime (0,0,0,1,0,$beginjaar+1+$i);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);
      
      if(db2jul($datum[$i]['stop']) < db2jul($datum[$i]['start']))
         unset($datum[$i]);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
}

function getHalveMaanden($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
    $i=0;
	  $j=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$j,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$j+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $tusenCounter= mktime (0,0,0,$beginmaand+$j,15,$beginjaar);
	    if($tusenCounter > $counterEnd)
	    {
	      $datum[$i]['stop']=date('Y-m-d',$julEind);
        break;
	    }
      if($tusenCounter > $julBegin)
      {
	      $datum[$i]['stop']=date('Y-m-d',$tusenCounter);
	      $i++;
	      $datum[$i]['start']=date('Y-m-d',$tusenCounter);
      }
	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
        
        
      $i++;
      $j++;
	  }
	  return $datum;
}

  function getDagen2($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $einddag= date("d",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
	  $begindag = date("d",$julBegin);
	  $counterStart=$julBegin;
	  $i=0;
    while ($counterEnd < $julEind)
	  {
       $counterStart = mktime (0,0,0,$beginmaand,$begindag+$i,$beginjaar);
       $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+1,$beginjaar);
       $datum[]=array('start'=>date('Y-m-d',$counterStart),'stop'=>date('Y-m-d',$counterEnd));
       $i++;
	  }
    return $datum;
  }
  
function getDagen($julBegin, $julEind,$periode='kwartaal')
{

  if($periode=='kwartaal')
    $blokken=$this->getKwartalen($julBegin, $julEind);
  elseif($periode=='maanden')
    $blokken=$this->getMaanden($julBegin, $julEind);
  elseif($periode=='jaar')
    $blokken=$this->getJaren($julBegin, $julEind);
  else
    $blokken=array('start'=>date("Y-m-d",$julBegin),'stop'=>date("Y-m-d",$julEind));

  foreach ($blokken as $blok=>$periode)
  {
    $julBegin=db2jul($periode['start']);
    $julEind=db2jul($periode['stop']);
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $einddag= date("d",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
	  $begindag = date("d",$julBegin);
	  $counterStart=$julBegin;
	  $i=0;
    while ($counterEnd < $julEind)
	  {
       $counterStart = mktime (0,0,0,$beginmaand,$begindag,$beginjaar);
       $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+1,$beginjaar);
       $datum[]=array('start'=>date('Y-m-d',$counterStart),'stop'=>date('Y-m-d',$counterEnd),'blok'=>$blok);
       $i++;
	  }
  }
  return $datum;
}

  function getTWRstortingsdagen($portefeuille,$julBegin, $julEind)
  {
    $query="SELECT DATE(Rekeningmutaties.Boekdatum) as datum
    FROM Rekeningen Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
    WHERE Rekeningen.Portefeuille='$portefeuille'  AND
    Rekeningmutaties.Boekdatum >= '".date('Y-m-d',$julBegin)."' AND  Rekeningmutaties.Boekdatum <= '".date('Y-m-d',$julEind)."' AND  Rekeningmutaties.Grootboekrekening IN('STORT','ONTTR')
    GROUP BY Rekeningmutaties.Boekdatum
    ORDER BY Boekdatum";

    $DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$i=0;
		$start =date('Y-m-d',$julBegin);
		$eind =date('Y-m-d',$julEind);
		$lastdatum=$start;
	  while($mutaties = $DB->nextRecord())
		{
		  if($lastdatum <> $mutaties['datum'])
		  {
		    $datum[$i]['start'] = $lastdatum;
		    $datum[$i]['stop']  =$mutaties['datum'];
		  }
		  $lastdatum=$mutaties['datum'];
		  $i++;
		}

		if($lastdatum <> $eind)
		{
		  $datum[$i]['start'] = $lastdatum;
		  $datum[$i]['stop']  =$eind;
		}
		return $datum;
  }

	function getWeken($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
    $einddag = date("d",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
    $begindag = date("d",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,$einddag,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand,$begindag+$i,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+7,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	    {
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);
	      if(substr($datum[$i]['start'],5,5)=='12-31')
	        $datum[$i]['start']=(date('Y',$counterStart)+1)."-01-01";
	    }

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i=$i+7;
	  }

	  return $datum;
  }


}




?>