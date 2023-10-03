<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/20 16:17:59 $
 		File Versie					: $Revision: 1.41 $

 		$Log: CRM_naw_dossierPrint.php,v $
 		Revision 1.41  2019/11/20 16:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.40  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.39  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.38  2018/05/05 19:15:14  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2018/05/02 16:06:03  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2017/09/02 07:40:10  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2017/08/31 05:46:05  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2017/08/30 15:01:24  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2016/06/19 15:24:05  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2016/06/01 19:48:06  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2015/11/22 14:29:16  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2015/11/14 13:26:59  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2015/11/07 16:42:34  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2015/08/30 11:42:24  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2014/06/11 15:44:58  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2014/05/03 15:48:27  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2014/04/19 16:15:26  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2014/04/12 16:30:45  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2014/02/22 18:42:25  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2014/01/22 14:22:42  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2014/01/22 13:14:07  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2013/12/24 09:34:58  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2013/12/22 16:04:27  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2013/11/23 17:21:24  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2013/07/31 15:44:40  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2013/07/20 16:24:34  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2012/09/30 11:15:25  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2012/09/05 18:10:27  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2012/06/20 18:08:47  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2012/06/17 13:01:42  rvv
 		*** empty log message ***

 		Revision 1.11  2011/07/27 16:26:05  rvv
 		*** empty log message ***

 		Revision 1.10  2011/05/04 16:28:41  rvv
 		*** empty log message ***

 		Revision 1.9  2011/04/30 16:23:58  rvv
 		*** empty log message ***

*/

include_once("wwwvars.php");
include_once($__appvar["basedir"]."/classes/AE_cls_fpdf.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/classes/AE_cls_html2fpdfRapport.php");
include_once("rapport/PDFRapport.php");

class gespreksverslagenPDF extends html2fpdfRapport
{
        function gespreksverslagenPDF($pdf)
        {
          $this->pdf = &$pdf;
          $this->html2fpdfRapport('P','mm','A4');
          $this->pdf->rapport_type = "BRIEF";
          $this->pdf->excelData=array();
        }

        function addVerslagen($relid)
        {
          $this->pdf->AddPage('P');
          $db=new DB();
          $db2=new DB();
          $query = "SELECT Portefeuille,Naam FROM CRM_naw WHERE id = '$relid'";
          $db->SQL($query);
          $data = $db->lookupRecord();

          if($data['Portefeuille'] <> '')
            $portefeuilleHtml="portefeuille <b>".$data['Portefeuille']."</b>, ";

          $html = "Gespreksverslagen bij $portefeuilleHtml <b>".$data['Naam']."</b><br><br>";

          if(isset($_GET['id']))
            $extra="AND CRM_naw_dossier.id='".$_GET['id']."'";
          else
            $extra='';

          if(isset($_GET['typeFilter']) && $_GET['typeFilter'] <>'')
          {
            if($_GET['typeFilter']=='Leeg')
              $filterWaarde='';
            else
              $filterWaarde=mysql_real_escape_string($_GET['typeFilter']);

            $extra .= "AND `type`='" .$filterWaarde . "'";
          }

          $query = "SELECT DATE_FORMAT(datum,\"%d-%m-%Y\") as datum,kop,txt,`type`,duur ,aanwezig, Gebruikers.Naam,dd_reference_id,CRM_naw_dossier.add_date,CRM_naw_dossier.add_user
                    FROM CRM_naw_dossier LEFT JOIN Gebruikers ON CRM_naw_dossier.add_user = Gebruikers.Gebruiker WHERE rel_id = '$relid' $extra ORDER BY CRM_naw_dossier.add_date desc";
          $db->SQL($query);
          $db->Query();
          while($data=$db->nextRecord())
          {
            $document=array();
            if($data['dd_reference_id'] <> 0)
            {
              $query="SELECT filename FROM dd_reference WHERE id='".$data['dd_reference_id']."'";//
              $db2->SQL($query);
              $document=$db2->lookupRecord();
            }
            $html.=" <hr> ";
            $html.="<table>";
            $html.="<tr>";
            $html.="<td><b>Datum</b></td><td>".$data['datum']."  <span>(".date('d-m-Y H:i',db2jul($data['add_date']))." / ".$data['add_user'].")</span></td>";
            if($data['duur']<> '00:00:00')
              $html.="<td><b>Duur</b></td><td>".$data['duur']."</td>";
            $html.="</tr>";
            if($data['aanwezig']<> '')
              $html.="<tr><td><b>Aanwezig</b></td><td colspan=\"3\">".$data['aanwezig']."</td></tr>";
            if($data['type']<> '')
              $html.="<tr><td><b>".$data['type']."</b></td></tr>";
            $html.="<tr><td><b>Betreft:</b></td><td>".$data['kop']."</td></tr>";
            if($document['filename']<>'')
              $html.="<tr><td><b>Document:</b></td><td>".$document['filename']."</td></tr>";
            $html.="</table>";
            //$html.="Op ".$data['datum']." duur ".$data['duur']." - ".$data['type']." - ".$data['kop']." door ".$data['Naam']."  <br>";
            $data['txt'] = preg_replace("/< ?img[^>]*>/i", '', $data['txt']);
            $html.="<b>Gespreksverslag</b><br>";
            $html.=$data['txt'];
          }
          $this->WriteHTML($html);
          $this->pdf->skipFooter = true;
        }
        function addLaasteVerslag()
        {
          global $USR;
          $this->pdf->AddPage('P');
          $db=new DB();
          $db2=new DB();
          $html = "<b>Laatste gespreksverslag per client gesorteerd op datum.</b><br><br>";
          $query="SELECT CRM_naw.id, CRM_naw.naam, CRM_naw.portefeuille,
                  (SELECT datum FROM CRM_naw_dossier WHERE CRM_naw_dossier.rel_id = CRM_naw.id ORDER BY datum desc limit 1) as datum
            FROM CRM_naw
LEFT JOIN Portefeuilles ON CRM_naw.portefeuille = Portefeuilles.Portefeuille
LEFT JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR'
LEFT JOIN Gebruikers ON VermogensbeheerdersPerGebruiker.Gebruiker =  Gebruikers.Gebruiker
WHERE 1 ".$this->extraWhere." AND ((Portefeuilles.beperktToegankelijk = '0' OR Gebruikers.beperkingOpheffen = '1' ) OR Portefeuilles.Portefeuille is null) ORDER BY datum desc";
          $db->SQL($query);
          $db->Query();
          while($data=$db->nextRecord())
          {

            $query = "SELECT DATE_FORMAT(datum,\"%d-%m-%Y\") as datum,kop,txt,duur,aanwezig,type,Gebruikers.Naam,dd_reference_id,CRM_naw_dossier.add_date,CRM_naw_dossier.add_user FROM CRM_naw_dossier LEFT JOIN Gebruikers ON CRM_naw_dossier.add_user = Gebruikers.Gebruiker WHERE rel_id = '".$data['id']."' ORDER BY CRM_naw_dossier.add_date desc";
            $db2->SQL($query);
            $verslag = $db2->lookupRecord();
            $document=array();
            if($verslag['dd_reference_id'] <> 0)
            {
              $query="SELECT filename FROM dd_reference WHERE id='".$verslag['dd_reference_id']."'";//
              $db2->SQL($query);
              $document=$db2->lookupRecord();
            }
            $html .= "Gespreksverslagen bij portefeuille <b>".$data['portefeuille']."</b>, naam <b>".$data['naam']."</b> <br><br>";
           // $html.=" <hr> ";
            $html.="<table>";
            $html.="<tr>";
            $html.="<td><b>Datum</b></td><td>".$verslag['datum']."  <span>(".date('d-m-Y H:i',db2jul($verslag['add_date']))." / ".$verslag['add_user'].")</span></td>";
            if($verslag['duur']<> '00:00:00')
              $html.="<td><b>Duur</b></td><td>".$verslag['duur']."</td>";
            $html.="</tr>";
            if($verslag['aanwezig']<> '')
              $html.="<tr><td><b>Aanwezig</b></td><td colspan=\"3\">".$verslag['aanwezig']."</td></tr>";
            if($verslag['type']<> '')
              $html.="<tr><td><b>".$verslag['type']."</b></td></tr>";
            $html.="<tr><td><b>Betreft:</b></td><td>".$verslag['kop']."</td></tr>";
            if($document['filename']<>'')
              $html.="<tr><td><b>Document:</b></td><td>".$document['filename']."</td></tr>";
            $html.="</table>";

            $verslag['txt'] = preg_replace("/< ?img[^>]*>/i", '', $verslag['txt']);
            $html.=$verslag['txt'];
            $html.=" <hr> ";
          }

          // workaround call 10719
          // probleem in AWS lijkt de conversie van html -> pdf onvollediog te zijn
          // als workaround
          print_r($html);

          // bovenstaande toegevoegd hierdoor krijg de klant een html dump die via de browser te printen is
          // einde workaround

          $this->WriteHTML($html);
          $this->pdf->skipFooter = true;//listarray($html);
        }

        function addLaasteActiviteit()
        {
          $this->pdf->AddPage('L');
          $db=new DB();
          $db2=new DB();
          $html = "<b>Laatste gespreksverslag per client gesorteerd op datum.</b><br><br>";
          $query="SELECT CRM_naw.id, CRM_naw.naam, CRM_naw.portefeuille, Portefeuilles.Accountmanager,
                  (SELECT datum FROM CRM_naw_dossier WHERE CRM_naw_dossier.rel_id = CRM_naw.id ORDER BY datum desc limit 1) as datum
            FROM CRM_naw
LEFT JOIN Portefeuilles ON CRM_naw.portefeuille = Portefeuilles.Portefeuille
LEFT JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR'
LEFT JOIN Gebruikers ON VermogensbeheerdersPerGebruiker.Gebruiker =  Gebruikers.Gebruiker
WHERE 1 ".$this->extraWhere." AND ( (Portefeuilles.beperktToegankelijk = '0' OR Gebruikers.beperkingOpheffen = '1' ) OR Portefeuilles.Portefeuille is null) ORDER BY datum desc";
          $db->SQL($query);
          $db->Query();
          $this->pdf->row(array("Laatste gespreksverslag per client gesorteerd op datum."));
          $this->pdf->row(array(""));
          $this->pdf->setWidths(array(22,60,30,30,25,30,60,30));
          $this->pdf->row(array("Portefeuille","Naam","Acc.Man.","Datum","Gesproken",'Type',"Onderwerp","Door"));
          $this->pdf->row(array(""));
          while($data=$db->nextRecord())
          {
            $query = "SELECT DATE_FORMAT(datum,\"%d-%m-%Y\") as datum,kop,txt, Gebruikers.Naam,clientGesproken,type FROM CRM_naw_dossier LEFT JOIN Gebruikers ON CRM_naw_dossier.add_user = Gebruikers.Gebruiker WHERE rel_id = '".$data['id']."' ORDER BY CRM_naw_dossier.add_date desc";
            $db2->SQL($query);
            $verslag = $db2->lookupRecord();
            if(strlen($data['naam']) > 30)
              $data['naam']=substr($data['naam'],0,30).'...';
            if($verslag['clientGesproken'] == 1)
              $verslag['clientGesproken']='Ja';
            else
              $verslag['clientGesproken']='Nee';    
              
            $this->pdf->row(array($data['portefeuille'],$data['naam'],$data['Accountmanager'],$verslag['datum'],$verslag['clientGesproken'],$verslag['type'],$verslag['kop'],$verslag['Naam']));
          }
          $this->pdf->skipFooter = true;
        }

        function addVergetenContact($type)
        {
          global $USR;
          $this->pdf->AddPage('P');
          $categorieFilter=$this->getCategorieFilter();
          if($categorieFilter=='')
            $categorieFilter=' AND CRM_naw_dossier.ClientGesproken=1 ';
           
          if($type=='planning')
            $queryHaving="HAVING aantalRecenteGesprekken >= 1";    
          else
            $queryHaving="HAVING aantalRecenteGesprekken < 1";    
            
          $db=new DB();
          $db2=new DB();
          $html = "<b>Laatste gespreksverslag per client gesorteerd op datum.</b><br><br>";
          $query="SELECT CRM_naw.id, CRM_naw.naam, CRM_naw.zoekveld, CRM_naw.portefeuille, CRM_naw.contactTijd,Portefeuilles.Accountmanager,Portefeuilles.tweedeAanspreekpunt,CRM_naw.tel1,CRM_naw.tel2,CRM_naw.email,
          (SELECT count(id) FROM CRM_naw_dossier WHERE CRM_naw_dossier.rel_id = CRM_naw.id $categorieFilter AND CRM_naw_dossier.ClientGesproken=1 AND datum > (now() - interval CRM_naw.contactTijd day)) as aantalRecenteGesprekken ,
          UNIX_TIMESTAMP((SELECT datum FROM CRM_naw_dossier WHERE CRM_naw_dossier.rel_id = CRM_naw.id $categorieFilter AND CRM_naw_dossier.ClientGesproken=1 ORDER BY datum desc limit 1) + interval CRM_naw.contactTijd day) as verwachteDag
          FROM CRM_naw
          LEFT Join Portefeuilles ON CRM_naw.portefeuille = Portefeuilles.Portefeuille
          LEFT JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR'
LEFT JOIN Gebruikers ON VermogensbeheerdersPerGebruiker.Gebruiker =  Gebruikers.Gebruiker
WHERE CRM_naw.contactTijd > 0 ".$this->extraWhere." AND (Portefeuilles.beperktToegankelijk is NULL OR Portefeuilles.beperktToegankelijk = '0' OR Gebruikers.beperkingOpheffen = '1' )
$queryHaving ORDER BY verwachteDag";

          $db->SQL($query);
          $db->Query();
          if($type=='planning')
          {
            $this->pdf->row(array("Te plannen afspraken gesorteerd op datum."));
            $this->pdf->excelData[]=array("Te plannen afspraken gesorteerd op datum.");
          }
          else
          {
            $this->pdf->row(array("Gemiste contacten gesorteerd op datum."));
            $this->pdf->excelData[]=array("Gemiste contacten gesorteerd op datum.");
          }
          $this->pdf->row(array(""));
          $this->pdf->setWidths(array(30,110,60));
          $this->pdf->row(array("Portefeuille","Naam","verwachte contact op"));
          $this->pdf->excelData[]=array("Portefeuille","Zoekveld","Naam","Accountmanager","tweedeAanspreekpunt","verwachte contact op",'Telefoon 1','Telefoon 2','eMailadres');
          $this->pdf->row(array(""));
          while($data=$db->nextRecord())
          {
            $this->pdf->row(array($data['portefeuille'],$data['naam'],date("d-m-Y",$data['verwachteDag'])));
            $this->pdf->excelData[]=array($data['portefeuille'],$data['zoekveld'],$data['naam'],$data['Accountmanager'],$data['tweedeAanspreekpunt'],date("d-m-Y",$data['verwachteDag']),$data['tel1'],$data['tel2'],$data['email']);
          }
          $this->pdf->skipFooter = true;
        }
        
        function addAfspraken()
        {
          $this->pdf->AddPage('P');
          $db=new DB();
          $db2=new DB();
          
          $categorieFilter=$this->getCategorieFilter();
          if($categorieFilter=='')
            $categorieFilter=' AND CRM_naw_dossier.ClientGesproken=1 ';

          $query="SELECT CRM_naw.id, CRM_naw.naam, CRM_naw.portefeuille, CRM_naw.contactTijd,Portefeuilles.Accountmanager
           FROM CRM_naw
          Join Portefeuilles ON CRM_naw.portefeuille = Portefeuilles.Portefeuille
          LEFT JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR'
LEFT JOIN Gebruikers ON VermogensbeheerdersPerGebruiker.Gebruiker =  Gebruikers.Gebruiker
WHERE 1 ".$this->extraWhere." AND (Portefeuilles.beperktToegankelijk = '0' OR Gebruikers.beperkingOpheffen = '1' )
ORDER BY naam";
          $db->SQL($query);  
          $db->Query();
          while($data=$db->nextRecord())
            $relaties[$data['id']]=$data;
 
          $vanaf=date('Y-m-d',mktime(1,0,0,date('m')-14,1,date('Y')));
          $tot=date('Y-m-d',mktime(1,0,0,date('m')+13,1,date('Y'))); 
          foreach($relaties as $id=>$relatieData)
          {
             $query="SELECT datum,kop FROM CRM_naw_dossier WHERE rel_id='$id' $categorieFilter AND datum > '$vanaf' AND datum < '$tot' ORDER BY datum";
            $db->SQL($query);
            $db->Query();
            while($data=$db->nextRecord())
            {
             $gesprekken[$id][substr($data['datum'],0,7)][]=$data; 
            }
            //if($data['verwachteDag'] <> '')
            //{
            //  $data['datum']=jul2db($data['verwachteDag']);
            //  $gesprekken[$id][substr($data['datum'],0,7)][]=$data;
            //}
          }

          $beginParts=explode('-',$vanaf);
          $maanden=array();
          $startJul=db2jul($vanaf);
          $totJul=db2jul($tot);
          $n=0;
          while($teller<=$totJul)
          {
             $teller=mktime(1,1,1,$beginParts[1]+$n,$beginParts[2],$beginParts[0]);
            $maanden[]=date('Y-m',$teller);
            $n++;
  
            if($n>1000)
             break;
          }
 
          $header=array('naam','dagen volgend contact');
          foreach($maanden as $maand)
             $header[]=$maand;
          $this->pdf->excelData[]=$header;

          foreach($relaties as $relId=>$relatieData)
          {
            $tmp=array($relatieData['naam'],$relatieData['contactTijd']);
            foreach($maanden as $maand)
            {
              $gesprek='';
              foreach($gesprekken[$relId][$maand] as $gesprekData)
              {
                 $gesprek.="".substr($gesprekData['datum'],0,10)."";
              }
              $tmp[]=$gesprek;
            }
            $this->pdf->excelData[]=$tmp;
          }
        }

        function setCategorie($categorie)
        {
          global $USR;
          $db = new DB();
          switch ($categorie)
          {
           	case "debiteur":
          		$this->extraWhere="AND debiteur = 1 AND aktief=1";
	          	break;
            case "crediteur":
          		$this->extraWhere="AND crediteur = 1 AND aktief=1";
	          	break;
            case "prospect":
          		$this->extraWhere="AND prospect = 1 AND aktief=1";
          		break;
            case "overige":
          		$this->extraWhere="AND overige = 1 AND aktief=1";
          		break;
	          case "inaktief":
          		$this->extraWhere="AND aktief <> 1";
	          	break;
          	default:
              if($categorie <> '')
                $this->extraWhere="AND aktief = 1 AND `".mysql_real_escape_string($categorie)."`=1 ";
              else
              {
                $query="SELECT CRM_relatieSoorten FROM Gebruikers WHERE Gebruiker='$USR'";
                $db->SQL($query);
                $CRM_relatieSoorten=$db->lookupRecord();
                $CRM_relatieSoorten=unserialize($CRM_relatieSoorten['CRM_relatieSoorten']);
                $filter='';
                if(is_array($CRM_relatieSoorten))
                {
                  $query="DESC CRM_naw";
                  $db->SQL($query);
                  $db->Query();
                  $crmVelden=array();
                  while($data=$db->nextRecord('num'))
                   $crmVelden[]=$data[0];
        
                  $allArray=array();
                  foreach($CRM_relatieSoorten as $key=>$value)
                  {
                    if($value<>'all' && $value<>'inaktief' && $value<>'aktief' && in_array($value,$crmVelden))
                      $allArray[]='CRM_naw.'.$value;
                  }
                  $filter="AND ((".implode('=1 OR ',$allArray)."=1) or ( ".implode('=0 AND ',$allArray)."=0)      )";
       
                }
	              $this->extraWhere="AND aktief = 1 $filter";
              }
	          	break;
    
          }
          
          if($_SESSION['lastListQuery'] <> '' && (!isset($_GET['id']) && !isset($_GET['relid'])))
          {
            
            if(strpos($_SESSION['lastListQuery'],'laatsteDossier'))
            {
              $query="CREATE TEMPORARY TABLE laatsteDossier
                      SELECT CRM_naw.id as rel_id,
                      lastDossier.id,
                      CRM_naw_dossier.datum,
                      CRM_naw_dossier.type,
                      CRM_naw_dossier.add_user,
                      lastSpoken.clientGesproken
                      FROM CRM_naw 
                      LEFT JOIN (SELECT max(CRM_naw_dossier.id) as id,CRM_naw_dossier.rel_id FROM CRM_naw_dossier GROUP BY CRM_naw_dossier.rel_id ) as lastDossier ON CRM_naw.id=lastDossier.rel_id
                      LEFT JOIN CRM_naw_dossier ON lastDossier.id= CRM_naw_dossier.id
                      LEFT JOIN (SELECT MAX(if(CRM_naw_dossier.clientGesproken =1,CRM_naw_dossier.datum,null)) as clientGesproken ,CRM_naw_dossier.rel_id FROM CRM_naw_dossier GROUP BY CRM_naw_dossier.rel_id ) as lastSpoken ON CRM_naw.id= lastSpoken.rel_id";
               $db->SQL($query);
               $db->Query();
               $query="ALTER TABLE laatsteDossier ADD INDEX( rel_id ); ";
               $db->SQL($query);
               $db->Query();
            }
            if(strpos($_SESSION['lastListQuery'],'laatsteTaak'))
            {
              $query="CREATE TEMPORARY TABLE laatsteTaak
                      SELECT CRM_naw.id as rel_id,
                      taken.id,
                      taken.add_date,
                      taken.kop,
                      taken.soort,
                      taken.afgewerkt,
                      taken.spoed,
                      taken.gebruiker,
                      taken.relatie,
                      taken.zichtbaar,
                      taken.add_user,
                      taken.change_user,
                      taken.change_date
                      FROM CRM_naw 
                      LEFT JOIN (SELECT max(taken.id) as id, taken.rel_id FROM taken GROUP BY taken.rel_id ) as lastTaak ON CRM_naw.id=lastTaak.rel_id
                      LEFT JOIN taken ON taken.id= lastTaak.id";
              $db->SQL($query);
              $db->Query();
              $query="ALTER TABLE laatsteTaak ADD INDEX( rel_id ); ";
              $db->SQL($query);
              $db->Query();
            }
            
            $tmp=explode("LIMIT",$_SESSION['lastListQuery']);
            $ids=array();
            $db->SQL($tmp[0]);
            $db->Query();
            while($data=$db->nextRecord())
            {
              $ids[]=$data['id'];
            }
            $this->extraWhere.= " AND CRM_naw.id IN('".implode("','",$ids)."')";

          }
        }
        
        function getCategorieFilter()
        {
          $db=new DB();
          $query="SELECT Omschrijving FROM CRM_selectievelden WHERE module='gesprekstypen' AND waarde='1'";
          $db->SQL($query); 
          $db->Query();
          while($data=$db->nextRecord())
            $relaties[]=$data['Omschrijving'];
            
          if(count($relaties)==0)
            return '';
              
          $categorieFilter="AND CRM_naw_dossier.type IN('".implode("','",$relaties)."')";
          return $categorieFilter;
        }


}

$pdf = new PDFRapport('P','mm');
$pdf->SetAutoPageBreak(true,15);
$pdf->pagebreak = 270;
$pdf->__appvar = $__appvar;

$gespreksverslagenPDF = new gespreksverslagenPDF($pdf);
$gespreksverslagenPDF->setCategorie($_GET['categorie']);

if($_GET['relid'])
  $gespreksverslagenPDF->addVerslagen($_GET['relid']);
else
{
  if($_GET['type'] == 'afspraken')
    $gespreksverslagenPDF->addAfspraken();
  elseif($_GET['type'] == 'activiteit')
    $gespreksverslagenPDF->addLaasteActiviteit();
  elseif($_GET['type'] == 'vergeten')
    $gespreksverslagenPDF->addVergetenContact();
  elseif($_GET['type'] == 'planning')    
    $gespreksverslagenPDF->addVergetenContact('planning');
  else
    $gespreksverslagenPDF->addLaasteVerslag();
}

if($_GET['type'] == 'vergeten' || $_GET['type'] == 'planning' || $_GET['type'] == 'afspraken')
  $pdf->OutputXls('xls_'.date('Ymd').'.xls','S');
else
{
  if($_GET['outputFilename'])
    $pdf->Output($_GET['outputFilename'],'F');
  else
    $pdf->Output();
}
?>