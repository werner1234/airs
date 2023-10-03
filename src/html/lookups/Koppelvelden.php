<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/29 11:37:45 $
 		File Versie					: $Revision: 1.28 $

 		$Log: Koppelvelden.php,v $
 		Revision 1.28  2020/04/29 11:37:45  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2020/02/22 18:59:54  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2020/01/04 18:55:07  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2019/05/18 16:32:40  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.23  2018/07/22 05:46:17  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2017/10/22 11:13:03  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2017/08/16 18:31:35  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2017/02/04 19:09:23  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2016/12/21 16:34:30  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2016/08/27 16:44:14  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2016/07/16 16:54:01  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2016/04/13 16:28:44  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2016/02/18 05:16:24  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2015/11/25 17:04:56  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2015/11/18 17:44:35  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2015/11/18 17:40:18  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2015/04/26 12:28:41  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2014/11/08 12:35:09  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2014/10/15 16:04:19  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/10/11 16:25:31  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/09/18 15:30:59  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/09/13 19:04:41  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2013/10/05 16:00:27  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/08/21 11:43:42  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/12/18 14:28:50  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2010/12/05 09:53:22  rvv
 		*** empty log message ***

 		Revision 1.1  2010/12/01 19:25:25  rvv
 		*** empty log message ***

 		Revision 1.1  2009/03/25 17:41:59  rvv
 		*** empty log message ***


*/

$veld=array('Beleggingscategorien'=>'Beleggingscategorie',
           'Beleggingssectoren'=>'Beleggingssector',
           'Regios'=>'Regio',
           'Valutas'=>'Valuta',
           'Rekeningen'=>'Rekening',
           'AttributieCategorien'=>'AttributieCategorie',
           'DuurzaamCategorien'=>'DuurzaamCategorie',
           'afmCategorien'=>'afmCategorie',
           'SoortOvereenkomsten'=>'SoortOvereenkomst',
           'toelichtingStortOnttr'=>'toelichting',
           'Zorgplichtcategorien'=>'Zorgplicht',
           'Orderredenen'=>'Orderreden',
           'Grootboekrekeningen'=>'Grootboekrekening');

//$searchParts[0]= $vermogensbeheerder
//$searchParts[1]= $tabel
//$searchParts[2]= 'vermogensbeheerder'
$extraFilter='';
$orderBy='ORDER BY waarde';

if($searchParts[1]=='SoortOvereenkomsten')
  $omschrijvingVeld="SoortOvereenkomst as omschrijving";
elseif($searchParts[1]=='toelichtingStortOnttr')
  $omschrijvingVeld="toelichting as omschrijving";
else
  $omschrijvingVeld="omschrijving";

if($searchParts[1]=='Attributiecategorieen')
  $searchParts[1]='AttributieCategorien';

if(count($searchParts) > 1)
{
  if(isset($searchParts[2]) && $searchParts[2]=='vermogensbeheerder') // voor vastleggen beleggingscategorien in keuzePerVermogensbeheerder
  {
      if($searchParts[1]=='Beleggingscategorien')
        $extraFilter="AND Beleggingscategorie NOT IN(SELECT hoofdcategorie FROM CategorienPerHoofdcategorie WHERE vermogensbeheerder='".$searchParts[0]."')";
//logIt('Beleggingscategorien|'.$searchParts[1].'|'.$veld[$searchParts[1]]);
      if($searchParts[1]=='Zorgplichtcategorien')
        $extraFilter="AND vermogensbeheerder='".$searchParts[0]."'";

      $theQuery = "SELECT ".$veld[$searchParts[1]]." as waarde ,$omschrijvingVeld FROM ".$searchParts[1]." WHERE 1 $extraFilter $orderBy";

  }
  elseif(isset($searchParts[2]) && $searchParts[2]=='portefeuille')
  {
     if($searchParts[1]=='Beleggingscategorien')
       $extraFilter="AND waarde NOT IN(SELECT CategorienPerHoofdcategorie.Hoofdcategorie FROM CategorienPerHoofdcategorie INNER JOIN Portefeuilles ON CategorienPerHoofdcategorie.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Portefeuilles.Portefeuille='".$searchParts[0]."' )";
    
     $theQuery = "SELECT waarde,".$searchParts[1].".$omschrijvingVeld FROM KeuzePerVermogensbeheerder 
     JOIN Portefeuilles ON Portefeuilles.Vermogensbeheerder=KeuzePerVermogensbeheerder.Vermogensbeheerder
     JOIN ".$searchParts[1]." ON KeuzePerVermogensbeheerder.waarde=".$searchParts[1].'.'.$veld[$searchParts[1]]."
     WHERE Portefeuille='".$searchParts[0]."' AND categorie='".$searchParts[1]."' $extraFilter $orderBy";
     logit($theQuery);
  }
  elseif($searchParts[0]=='rekeningen')
  {
    $theQuery = "SELECT rekening as waarde, valuta as omschrijving  FROM Rekeningen  WHERE Portefeuille='".$searchParts[1]."' AND memoriaal=0 AND inactief=0  ORDER BY rekening";
    //logit($theQuery);
  }
  else //normale lookup
  {
    if($searchParts[1]=='Rapportcategorieen')
    {
      $theQuery="SELECT begrippenCategorie.id as waarde ,begrippenCategorie.categorie as omschrijving
FROM begrippenCategorie
WHERE begrippenCategorie.vermogensbeheerder='".$searchParts[0]."' GROUP BY waarde";
    }
    elseif($searchParts[1]=='Hoofdcategorien2')
    {
      $theQuery="SELECT CategorienPerHoofdcategorie.Hoofdcategorie as waarde ,Beleggingscategorien.omschrijving
FROM CategorienPerHoofdcategorie 
JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='".$searchParts[0]."' GROUP BY waarde";
    }
    elseif($searchParts[1]=='Hoofdcategorien')
    {
      $theQuery="SELECT Beleggingscategorie as waarde,$omschrijvingVeld FROM Beleggingscategorien WHERE
      Beleggingscategorie NOT IN(SELECT waarde FROM KeuzePerVermogensbeheerder WHERE categorie='Beleggingscategorien' AND vermogensbeheerder='".$searchParts[0]."')";
    }
    elseif($searchParts[1]=='BeleggingsEnHoofdcategorien')
    {
        $theQuery=" (SELECT waarde, Beleggingscategorien.omschrijving FROM KeuzePerVermogensbeheerder 
    JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde=Beleggingscategorien.Beleggingscategorie
      WHERE vermogensbeheerder='".$searchParts[0]."' AND categorie='Beleggingscategorien'  ORDER BY waarde )
  UNION(
  SELECT
CategorienPerHoofdcategorie.Hoofdcategorie as waarde,
concat(Beleggingscategorien.Omschrijving,' (HC)') as omschrijving
FROM
CategorienPerHoofdcategorie
JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie=Beleggingscategorien.Beleggingscategorie
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='".$searchParts[0]."' GROUP BY Hoofdcategorie) 
";
    }
    elseif($searchParts[1]=='Rating')
    {
      $theQuery="SELECT rating as waarde, omschrijving FROM Rating ORDER BY rating";
    }
    elseif($searchParts[1]=='Risicoklasse')
    {
      $theQuery="SELECT Risicoklasse as waarde, Risicoklasse as omschrijving FROM Risicoklassen WHERE vermogensbeheerder='".$searchParts[0]."' ORDER BY Risicoklasse";
    }
    elseif($searchParts[1]=='Fondkoerssaanvraag')
    {
      $theQuery = "SELECT Fondsen.Fonds as waarde, Fondsen.Omschrijving as omschrijving FROM Fondsen WHERE Fondsen.KoersVBH='".$searchParts[0]."' AND Fondsen.koersmethodiek=5";
    }
    else
    {    
    if($searchParts[1]=='Beleggingscategorien')
      $extraFilter="AND waarde NOT IN(SELECT hoofdcategorie FROM CategorienPerHoofdcategorie WHERE vermogensbeheerder='".$searchParts[0]."')";
    
    $theQuery = "SELECT waarde, ".$searchParts[1].".$omschrijvingVeld FROM KeuzePerVermogensbeheerder 
    JOIN ".$searchParts[1]." ON KeuzePerVermogensbeheerder.waarde=".$searchParts[1].'.'.$veld[$searchParts[1]]."
      WHERE vermogensbeheerder='".$searchParts[0]."' AND categorie='".$searchParts[1]."' $extraFilter $orderBy";
    }  
  }
  $velden = array('waarde','omschrijving');
}
else
{
  //if($search=='Beleggingscategorien')
  //  $extraFilter="WHERE Beleggingscategorie NOT IN(SELECT hoofdcategorie FROM CategorienPerHoofdcategorie)";

  $theQuery = "SELECT ".$veld[$search]." ,omschrijving FROM $search $extraFilter ORDER BY ".$veld[$search]." ";
  $velden = array($veld[$search],'omschrijving');
}
?>