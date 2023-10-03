<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/10/18 17:38:41 $
File Versie					: $Revision: 1.52 $

$Log: rapportATTberekening.php,v $
Revision 1.52  2019/10/18 17:38:41  rvv
*** empty log message ***

Revision 1.51  2016/10/23 11:31:17  rvv
*** empty log message ***

Revision 1.50  2015/01/19 14:52:07  rvv
*** empty log message ***

Revision 1.49  2015/01/14 20:17:14  rvv
*** empty log message ***

Revision 1.48  2014/10/08 15:41:26  rvv
*** empty log message ***

Revision 1.47  2014/10/04 15:20:48  rvv
*** empty log message ***

Revision 1.46  2014/09/27 16:05:40  rvv
*** empty log message ***

Revision 1.45  2014/09/27 16:04:29  rvv
*** empty log message ***

Revision 1.44  2014/01/26 15:07:37  rvv
*** empty log message ***

Revision 1.43  2013/07/26 06:06:58  rvv
*** empty log message ***

Revision 1.42  2013/07/24 15:48:04  rvv
*** empty log message ***

Revision 1.41  2013/07/17 15:52:10  rvv
*** empty log message ***

Revision 1.40  2013/06/09 17:59:33  rvv
*** empty log message ***

Revision 1.39  2013/02/10 10:05:24  rvv
*** empty log message ***

Revision 1.38  2011/07/23 17:36:38  rvv
*** empty log message ***

Revision 1.37  2011/03/17 09:10:02  rvv
*** empty log message ***

Revision 1.36  2011/01/16 11:20:42  rvv
*** empty log message ***

Revision 1.35  2010/11/21 13:09:43  rvv
*** empty log message ***

Revision 1.34  2010/11/14 10:42:06  rvv
WWO ATT berekening

Revision 1.33  2010/10/09 14:52:56  rvv
>= aangepast naar > voor WAT toegerekende kosten

Revision 1.32  2010/09/01 08:54:25  rvv
*** empty log message ***

Revision 1.31  2010/07/30 07:14:30  rvv
*** empty log message ***

Revision 1.30  2010/07/30 06:56:50  rvv
*** empty log message ***

Revision 1.29  2010/06/12 09:12:39  rvv
*** empty log message ***

Revision 1.28  2010/06/09 18:46:15  rvv
*** empty log message ***

Revision 1.27  2010/02/21 12:36:46  rvv
*** empty log message ***

Revision 1.26  2010/02/14 12:35:46  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
class rapportATTberekening
{

	function RapportATTberekening( $pdata )
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
        if($this->pdata['Vermogensbeheerder'] == 'RCN' || $this->pdata['Vermogensbeheerder'] == 'WAT1' || $this->pdata['Vermogensbeheerder'] == 'WAT' || $this->pdata['Vermogensbeheerder'] == 'THB' || $this->pdata['Vermogensbeheerder'] == 'BCS' || $this->pdata['Vermogensbeheerder'] == 'EVO' || $this->pdata['Vermogensbeheerder'] == 'LBC')
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
      $fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($this->pdata['Portefeuille'],$periode['start'],(substr($periode['start'], 5, 5) == '01-01')?true:false,$valuta,$periode['start']);
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
      $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($this->pdata['Portefeuille'],$periode['stop'],(substr($periode['stop'], 5, 5) == '01-01')?true:false,$valuta,$periode['start']);
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

}



?>