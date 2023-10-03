<?php
/*
    AE-ICT CODEX source module versie 1.2, 6 december 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/03/10 18:18:16 $
    File Versie         : $Revision: 1.37 $

    $Log: scenarioBerekening.php,v $
    Revision 1.37  2018/03/10 18:18:16  rvv
    *** empty log message ***

    Revision 1.36  2018/03/03 17:21:17  rvv
    *** empty log message ***

    Revision 1.35  2018/02/24 18:29:58  rvv
    *** empty log message ***

    Revision 1.34  2018/01/31 17:18:43  rvv
    *** empty log message ***

    Revision 1.33  2017/08/17 10:27:59  rvv
    *** empty log message ***

    Revision 1.32  2017/08/17 09:13:04  rvv
    *** empty log message ***

    Revision 1.31  2017/08/17 09:07:06  rvv
    *** empty log message ***

    Revision 1.30  2017/08/12 12:00:33  rvv
    *** empty log message ***

    Revision 1.29  2017/06/12 06:30:11  rvv
    *** empty log message ***

    Revision 1.28  2017/05/10 16:01:26  rvv
    *** empty log message ***

    Revision 1.27  2016/12/12 07:47:30  rvv
    *** empty log message ***

    Revision 1.26  2016/12/10 19:25:50  rvv
    *** empty log message ***

    Revision 1.25  2016/09/21 18:29:20  rvv
    *** empty log message ***

    Revision 1.24  2016/09/04 14:39:40  rvv
    *** empty log message ***

    Revision 1.23  2015/09/20 17:27:52  rvv
    *** empty log message ***

    Revision 1.22  2015/02/25 17:23:45  rvv
    *** empty log message ***

    Revision 1.21  2015/02/22 09:51:14  rvv
    *** empty log message ***

    Revision 1.20  2014/09/13 14:39:14  rvv
    *** empty log message ***

    Revision 1.19  2014/07/12 15:21:06  rvv
    *** empty log message ***

    Revision 1.18  2014/06/08 07:51:41  rvv
    *** empty log message ***

    Revision 1.17  2014/05/29 12:14:27  rvv
    *** empty log message ***

    Revision 1.16  2014/05/25 14:34:21  rvv
    *** empty log message ***

    Revision 1.15  2014/05/14 09:25:39  rvv
    *** empty log message ***

    Revision 1.14  2014/03/29 16:23:55  rvv
    *** empty log message ***

    Revision 1.13  2014/03/12 15:11:00  rvv
    *** empty log message ***

    Revision 1.12  2014/03/08 17:00:11  rvv
    *** empty log message ***

    Revision 1.11  2014/01/19 11:06:27  rvv
    *** empty log message ***

    Revision 1.10  2014/01/18 17:22:00  rvv
    *** empty log message ***

    Revision 1.9  2013/12/21 18:27:24  rvv
    *** empty log message ***

    Revision 1.8  2013/12/08 13:49:03  rvv
    *** empty log message ***

    Revision 1.7  2013/12/08 13:00:14  rvv
    *** empty log message ***

    Revision 1.6  2013/12/04 17:22:08  rvv
    *** empty log message ***

    Revision 1.5  2013/12/04 17:17:25  rvv
    *** empty log message ***

    Revision 1.3  2013/11/30 14:18:53  rvv
    *** empty log message ***

    Revision 1.2  2013/11/17 13:44:46  rvv
    *** empty log message ***

    Revision 1.1  2013/11/16 16:07:53  rvv
    *** empty log message ***

    Revision 1.62  2013/09/28 14:40:56  rvv
    *** empty log message ***


*/

class scenarioBerekening
{
  /*
  * Object vars
  */

  var $matrix = array();
  var $simulatie = array();
  var $gesorteerdeResultaten = array();
  var $debug=false;
  var $doelKans=0;
  var $gemiddelde=0;
  var $adviseren=false;  
  var $cashflow=array();
  var $cashflowText=array();
  var $rendementsheffing=array();
  var $vrhMethode=0;
  var $gebruikHandmatigeOpties=false;
  var $inflatiePercentages=array();
  var $cumulatieveInflatie=array();
  var $grensMarge=0; #2.5
  /*
  * Constructor
  */
  function scenarioBerekening($crm_id,$gewenstRisicoprofiel='',$forceerAfwijkendProfiel=false)
  {
    $this->db=new DB();
    $query="SELECT naam,startvermogen,startdatum,doeldatum,doelvermogen,gewenstRisicoprofiel,maximaalRisicoprofiel,portefeuille FROM CRM_naw WHERE id='$crm_id'";
    $this->db->SQL($query);
    $this->CRMdata=$this->db->lookupRecord();
    
    if($gewenstRisicoprofiel <> '')
      $this->CRMdata['gewenstRisicoprofiel']=$gewenstRisicoprofiel;
    
    if($this->CRMdata['gewenstRisicoprofiel'] <> '')
    {
      $risicoklasseWhere = " Risicoklasse='" . $this->CRMdata['gewenstRisicoprofiel'] . "' AND";
      $this->huidigeRisicoklasse=$this->CRMdata['gewenstRisicoprofiel'];
    }
    else
      $risicoklasseWhere='';
      
    if($this->CRMdata['portefeuille'] <> '')
    {
      $query="SELECT Vermogensbeheerder FROM Portefeuilles WHERE portefeuille='".$this->CRMdata['portefeuille']."'";
      $this->db->SQL($query);
      $tmp=$this->db->lookupRecord();
      $this->CRMdata['Vermogensbeheerder']=$tmp['Vermogensbeheerder'];
      $risicoklasseWhere.="  Vermogensbeheerder='".$tmp['Vermogensbeheerder']."' AND";
    }

    $query="SELECT Risicoklasse,verwachtRendement,klasseStd,Vermogensbeheerder FROM Risicoklassen WHERE $risicoklasseWhere verwachtRendement <> 0  AND uitsluitenScenario=0 limit 1";
    $this->db->SQL($query);
    $this->profieldata=$this->db->lookupRecord();

    if($this->profieldata['Vermogensbeheerder'] <> '')
      $vermogensbeheerderWhere=" WHERE Vermogensbeheerder='".$this->profieldata['Vermogensbeheerder']."'";
    else
      $vermogensbeheerderWhere='';

    $query="SELECT ScenarioMinimaleKans,ScenarioGewenstProfiel,ScenarioAfwijkendProfielPDF FROM Vermogensbeheerders $vermogensbeheerderWhere";
    $this->db->SQL($query);
    $vermogensbeheerderInstellingen=$this->db->lookupRecord();
    $this->profieldata['ScenarioMinimaleKans']=$vermogensbeheerderInstellingen['ScenarioMinimaleKans'];
    $this->profieldata['ScenarioGewenstProfiel']=$vermogensbeheerderInstellingen['ScenarioGewenstProfiel'];
    $this->profieldata['ScenarioAfwijkendProfielPDF']=$vermogensbeheerderInstellingen['ScenarioAfwijkendProfielPDF'];

    if(($this->profieldata['ScenarioAfwijkendProfielPDF']==1 || $forceerAfwijkendProfiel) && $_GET['verwachtRendement'] <> '' && $_GET['klasseStd'] <> '')
    {
      $this->gebruikHandmatigeOpties=true;
      $this->CRMdata['gewenstRisicoprofiel']='Afwijkend Profiel';
      $this->afwijkendProfiel = array('verwachtRendement'  => $_GET['verwachtRendement'], 'klasseStd' => $_GET['klasseStd'],
                                      'Vermogensbeheerder' => $this->profieldata['Vermogensbeheerder'], 'Risicoklasse' => 'Afwijkend Profiel', 'afkorting' => 'AFW');
    }

    if(isset($this->afwijkendProfiel)) //&& $gewenstRisicoprofiel=='Afwijkend Profiel'
       $this->profieldata=$this->afwijkendProfiel;

    $this->profieldata['verwachtRendement']=$this->profieldata['verwachtRendement']/100+1;
    $this->profieldata['klasseStd']=$this->profieldata['klasseStd']/100;
    if($this->huidigeRisicoklasse=='')
      $this->huidigeRisicoklasse=$this->profieldata['Risicoklasse'];

    $query="SELECT verwachtRendement,klasseStd,Vermogensbeheerder,Risicoklasse,afkorting FROM Risicoklassen 
      WHERE Vermogensbeheerder='".$this->profieldata['Vermogensbeheerder']."' AND uitsluitenScenario=0 AND verwachtRendement <> 0 AND klasseStd <> 0 order by klasseStd";
    $this->db->SQL($query);
    $this->db->Query();
    $toegevoegd=false;
    while($data=$this->db->NextRecord())
    {
      if(isset($this->afwijkendProfiel) && $this->afwijkendProfiel['klasseStd'] <= $data['klasseStd'] && $toegevoegd==false)
      {
        $this->risicoklassen['Afwijkend Profiel'] = $this->afwijkendProfiel;
        $toegevoegd=true;
      }
      $this->risicoklassen[$data['Risicoklasse']] = $data;
    }
    if(isset($this->afwijkendProfiel) && $toegevoegd==false)
    {
      $this->risicoklassen['Afwijkend Profiel'] = $this->afwijkendProfiel;
    }
 

    $query="SELECT scenario,percentage,kleurcode FROM scenariosPerVermogensbeheerder $vermogensbeheerderWhere ORDER BY percentage";
    $this->db->SQL($query);
    $this->db->Query();
    while($data=$this->db->nextRecord())
    {
      $this->scenarios[$data['scenario']]=$data['percentage'];
      $this->scenarioKleur[$data['scenario']]=unserialize($data['kleurcode']);
    }


    $query="SELECT jaarVanaf,risicoklasse,standaarddeviatie/100 as standaarddeviatie,(verwachtRendement/100)+1 as verwachtRendement FROM scenarioInstellingen $vermogensbeheerderWhere ORDER BY risicoklasse,jaarVanaf";
    $this->db->SQL($query);
    $this->db->Query();
    if($this->db->records() > 0)
    {
      $jarenData=array();
      $startJaar=substr($this->CRMdata['startdatum'],0,4)-1;
      $doelJaar =substr($this->CRMdata['doeldatum'],0,4);
      $bepaaldeJaren=$doelJaar-$startJaar;

      while ($data = $this->db->nextRecord())
      {
        if(!isset($eersteWaarde[$data['risicoklasse']]))
          $eersteWaarde[$data['risicoklasse']]=$data;
        $jarenData[$data['risicoklasse']][$data['jaarVanaf']] = $data;//array($data['verwachtRendement'], $data['standaarddeviatie']);
      }

      foreach($this->risicoklassen as $risicoklasse=>$risicoklasseDefaults)
      {
        $this->rendementsverloopTxt[$risicoklasse]=array();
        $totSet=false;
        if(isset($eersteWaarde[$risicoklasse]))
        {
          foreach($jarenData[$risicoklasse] as $jaarVanaf=>$jaarWaarden)
          {
            if ($jaarVanaf > $startJaar && $totSet==false)
            {
              $this->rendementsverloopTxt[$risicoklasse][] = array('txt' => 'Tot ' . $jaarVanaf, 'verwachtRendement' => $this->risicoklassen[$risicoklasse]['verwachtRendement'], 'standaarddeviatie' => $this->risicoklassen[$risicoklasse]['klasseStd']);
              $totSet=true;
            }
            $this->rendementsverloopTxt[$risicoklasse][] = array('txt' => 'Vanaf ' . $jaarVanaf, 'verwachtRendement' => ($jaarWaarden['verwachtRendement']-1)*100, 'standaarddeviatie' => $jaarWaarden['standaarddeviatie']*100);
          }
        }
      }

      foreach($this->risicoklassen as $risicoklasse=>$risicoklasseDefaults)
      {
        $tmp = array($this->risicoklassen[$risicoklasse]['verwachtRendement']/100+1, $this->risicoklassen[$risicoklasse]['klasseStd']/100);
        if($bepaaldeJaren > 0)
        {
          for($simulatieJaar=$startJaar;$simulatieJaar<=$doelJaar;$simulatieJaar++)
          {
            if(isset($eersteWaarde[$risicoklasse]) && $simulatieJaar >=$eersteWaarde[$risicoklasse]['jaarVanaf'])
            {
              $tmp = array($eersteWaarde[$risicoklasse]['verwachtRendement'], $eersteWaarde[$risicoklasse]['standaarddeviatie']);
              foreach($jarenData[$risicoklasse] as $jaarVanaf=>$jaarWaarden )
              {
                if ($simulatieJaar >= $jaarVanaf)
                {
                  $tmp = array($jaarWaarden['verwachtRendement'], $jaarWaarden['standaarddeviatie']);
                }
              }
            }
            $this->rendementsConfiguratiePerDatum[$risicoklasse][$simulatieJaar] = $tmp;
          }
        }
      }
    }
//listarray( $this->rendementsConfiguratiePerDatum['Neutraal']);exit;

    $query="SELECT jaar, vrijstellingEenP, vrijstellingTweeP, percentage1,percentage2,percentage3,percentage4,vermogen1,vermogen2,vermogen3,vermogen4 FROM Rendementsheffing ORDER BY jaar";
    $this->db->SQL($query); 
    $this->db->Query();
    while($data=$this->db->nextRecord())
    {
      $beginJaar=substr($data['jaar'],0,4);
      for($i=$beginJaar;$i<$beginJaar+80;$i++)
        $this->rendementsheffing[$i]=$data;
    }

    $doelJaar =substr($this->CRMdata['doeldatum'],0,4);
    $query="SELECT jaar, percentage FROM inflatiepercentages $vermogensbeheerderWhere ORDER BY jaar";
    $this->db->SQL($query);
    $this->db->Query();
    while($data=$this->db->nextRecord())
    {
      $beginJaar=substr($data['jaar'],0,4);
      for($i=$beginJaar;$i<=$doelJaar;$i++)
        $this->inflatiePercentages[$i]=$data;
    }
    //listarray( $this->inflatiePercentages);
    $vorigeWaarde=0;
    foreach($this->inflatiePercentages as $jaar=>$percentageData)
    {
      $nieuweWaarde=((1+$vorigeWaarde/100)*(1+$percentageData['percentage']/100)-1)*100;
      $this->cumulatieveInflatie[$jaar]=$nieuweWaarde;
      $vorigeWaarde=$nieuweWaarde;

    }
    $this->inflatieCorrectie=1+($this->cumulatieveInflatie[$doelJaar]/100);


    $query="SELECT datum,bedrag,totDatum,indexatie FROM CRM_naw_cashflow WHERE rel_id='$crm_id' ORDER BY datum";
    $this->db->SQL($query);
    $this->db->Query();
    $totDoelBedragen=array();
    $doeljaar=substr($this->CRMdata['doeldatum'],0,4);
    while($data=$this->db->nextRecord())
    {
      $iText='';
      $cashflowReeks=0;
      if($data['indexatie'] <> '')
        $iText=$data['indexatie']."%";
      
      if($data['totDatum']!='0000-00-00')
      {
        $beginJaar=substr($data['datum'],0,4);
        $eindJaar=substr($data['totDatum'],0,4);
        if($eindJaar>$doeljaar)
          $eindJaar=$doeljaar;
        if($beginJaar <= $eindJaar)
        {
          for($i=$beginJaar;$i<=$eindJaar;$i++)
          {
            $x=$i-$beginJaar;
            $indexatie=pow(1+($data['indexatie']/100),$x);
            $this->cashflow[$i]+=$data['bedrag']*$indexatie;
            $cashflowReeks+=$data['bedrag']*$indexatie;
          }  
        }
        $this->cashflowText[]=array($beginJaar.'-'.$eindJaar,$data['bedrag'],$iText,$cashflowReeks);
      }
      else
      {
        $this->cashflow[substr($data['datum'],0,4)]+=$data['bedrag'];
        $this->cashflowText[]=array(substr($data['datum'],0,4),$data['bedrag'],$data['bedrag']);
      } 

    }

          
    if($this->debug)
    {
     echo "Berkeningen met startvermogen ".$this->CRMdata['startvermogen']." en doelvermogen ".$this->CRMdata['doelvermogen'].".<br>\n";
     echo "Verwacht rendement ".$this->profieldata['verwachtRendement']." en een standaarddeviatie van ".$this->profieldata['klasseStd'].".<br>\n";
    }
  }
  
  function ophalenHistorie($portefeuille)
  {
    $query="SELECT Startdatum FROM Portefeuilles WHERE portefeuille='$portefeuille'";
    $this->db->SQL($query);
    $start=$this->db->lookupRecord();
    $startJaar=substr($start['Startdatum'],0,4);
    $huidigeJaar=date('Y');
    for($jaar=$startJaar;$jaar<$huidigeJaar;$jaar++)
    { 
      $waardeEur=0;
      $regels=berekenPortefeuilleWaarde($portefeuille,$jaar.'-12-31',false,'EUR',$jaar.'-12-31');
      foreach($regels as $waarden)
        $waardeEur+=$waarden['actuelePortefeuilleWaardeEuro'];
      $stortingen=getStortingen($portefeuille,$jaar.'-01-01',$jaar.'-12-31')-getOnttrekkingen($portefeuille,$jaar.'-01-01',$jaar.'-12-31');
      $werkelijkVerloop[$jaar]=array('waarde'=>$waardeEur,'stortingen'=>$stortingen);
    }
    $this->werkelijkVerloop=$werkelijkVerloop;
  }
  
  function overigeRisicoklassen()
  {
    $gebruikVolgende=false;
    $maximaalRisicoprofielStdev=0;
    foreach($this->risicoklassen as $data)
    {
      if($gebruikVolgende)
      {
        $gebruikVolgende=false;
        $maximaalRisicoprofielStdev=($maximaalRisicoprofielStdev+$data['klasseStd'])/2;
      }
      if(strtolower($this->CRMdata['maximaalRisicoprofiel'])==strtolower($data['Risicoklasse']))
      {
        $maximaalRisicoprofielStdev=$data['klasseStd'];
        $gebruikVolgende=true;
      }
     // listarray($data);
     // echo $this->CRMdata['maximaalRisicoprofiel']."|".$data['Risicoklasse']."|".$data['klasseStd']."|$maximaalRisicoprofielStdev|<br>\n";
     //Offensief Laag|Offensief laag|13.1||
    }
    
  //  if($maximaalRisicoprofielStdev==0)
  //    $maximaalRisicoprofielStdev=25;
   //echo " $maximaalRisicoprofielStdev <br>\n $query";exit;
    $this->profieldata['maximaalRisicoprofielStdev']=$maximaalRisicoprofielStdev;

  }
  
  function brekenRisicoklasseKans($risicoklasse)
  {
    $this->profieldata['verwachtRendement']=$this->risicoklassen[$risicoklasse]['verwachtRendement']/100+1;
    $this->huidigeRisicoklasse=$risicoklasse;
    $this->profieldata['klasseStd']=$this->risicoklassen[$risicoklasse]['klasseStd']/100;
    $this->simulatie=array();
    $this->gemiddelde=0;
    $this->doelKans=0;
    $aantalSimulaties=10000;
    $this->berekenSimulaties(0,$aantalSimulaties);
    $this->berekenDoelKans();
    $this->berekenVerdeling();

    $max=100-$this->profieldata['ScenarioMinimaleKans'];
    $scenarioWaardeBijGewensteKans=0;
    $n=0;
    foreach($this->gesorteerdeResultaten as $index=>$waarde)
    {
      $n++;
      $percentage=$n/$aantalSimulaties*100;
      if($percentage>$max)
      {
        $scenarioWaardeBijGewensteKans=round($waarde);
        break;
      } 
    }


    return array('kans'=>$this->doelKans,
                 'gemiddelde'=>$this->gemiddelde,
                 'scenarioEindwaarden'=>$this->verwachteWaarden,//$scenarioEindwaarden,
                 'ScenarioWaardeBijGewensteKans'=>$scenarioWaardeBijGewensteKans);
  }
  
  function berekenKansBijOpgehaaldeRisicoklassen()
  {
    $waarden=array();
    $beste=array();
    $MaxKans=array();
    $maxKansTmp=0;
   // echo "<br>\n";
    foreach($this->risicoklassen as $risicoklasse=>$risicoklasseData)
    {
      if($risicoklasseData['afkorting']<>'')                    
          $grafiekKey=$risicoklasseData['afkorting'];
        else
          $grafiekKey=$risicoklasse;
          
      $uitkomst=$this->brekenRisicoklasseKans($risicoklasse);
        
      if($uitkomst['kans'] > $maxKansTmp)
      {
        if($risicoklasseData['klasseStd']<=$this->profieldata['maximaalRisicoprofielStdev'] || $this->profieldata['maximaalRisicoprofielStdev']==0)
        {
          $MaxKans=array('verwachtRendement'=>$risicoklasseData['verwachtRendement'],'scenario'=>$grafiekKey,'risicoklasse'=>$risicoklasse);
          $maxKansTmp=$uitkomst['kans'];
        }
      }

//|| $this->profieldata['maximaalRisicoprofielStdev']==0 misschien toevoegen?
      if($risicoklasseData['klasseStd']<=$this->profieldata['maximaalRisicoprofielStdev'] && round($uitkomst['kans']) > $this->profieldata['ScenarioMinimaleKans'])
      {
        if($risicoklasseData['verwachtRendement'] >= $beste['verwachtRendement'])
        {
          //if($this->CRMdata['doelvermogen']>0 || ($this->CRMdata['doelvermogen']==0 && $uitkomst['ScenarioWaardeBijGewensteKans'] >= $beste['verwachteWaarde']))
            $beste = array('verwachtRendement' => $risicoklasseData['verwachtRendement'], 'scenario' => $grafiekKey, 'risicoklasse' => $risicoklasse,
                         'verwachteWaarde'   => $uitkomst['ScenarioWaardeBijGewensteKans']);
        }
      }

      $waarden['risicoklassen'][$risicoklasse]['risicoklasseData']=$risicoklasseData;
      $waarden['risicoklassen'][$risicoklasse]['uitkomstKans']=$uitkomst;
      $waarden['grafiekKeys'][$risicoklasse]=$grafiekKey;
      $waarden['grafiekData'][$grafiekKey]=array('x'=>$risicoklasseData['klasseStd'],'y'=>$uitkomst['kans'],'risicoklasse'=>$risicoklasse,'verwachtRendement'=>$risicoklasseData['verwachtRendement']);

    }
    $waarden['beste']=$beste;
    $waarden['maxKans']=$MaxKans;
//listarray($waarden);
    return $waarden;
  }
  
  function loadMatrix()
  {
    if(file_exists('matrix.txt'))
    {
      $this->matrix = unserialize(gzuncompress(file_get_contents('matrix.txt')));
      $this->simulatie=array();
      return 1;
    }  
    else
    {
      return 0;
    }  
  }
  
  function createNewMatix($store=false)
  {
    for($jaar=0;$jaar<41;$jaar++)
    {
      for($regel=0;$regel<=10000;$regel++)
      {
        $random=rand()/getrandmax();
        $this->matrix[$regel][$jaar]=inverse_ncdf($random);
      }
    }
    if($store==true)
    {
      file_put_contents('matrix.txt', gzcompress(serialize($this->matrix)));
    }
  }

  function berekenVRH($beginVermogen,$vrhMethode, $vrhInstelling)
  {
    $vrhBedrag=0;
    $rekenVermogen=0;
    $berekendOver=0;

    if($vrhMethode==1)
      $rekenVermogen=$beginVermogen;
    elseif($vrhMethode==2)
    {
      if($beginVermogen > $vrhInstelling['vrijstellingEenP'])
        $rekenVermogen=$beginVermogen-$vrhInstelling['vrijstellingEenP'];
      else
        return $vrhBedrag;
    }
    elseif($vrhMethode==3)
    {
      if($beginVermogen > $vrhInstelling['vrijstellingTweeP'])
        $rekenVermogen=$beginVermogen-$vrhInstelling['vrijstellingTweeP'];
      else
        return $vrhBedrag;
    }

    $beginRekenvermogen=$rekenVermogen;
    for($i=1;$i<5;$i++)
    {

      if($rekenVermogen+$berekendOver<$vrhInstelling['vermogen'.$i])
      {
        $waarde=$rekenVermogen*$vrhInstelling['percentage'.$i]/100;
        //echo "rest $i $waarde = $rekenVermogen*".$vrhInstelling['percentage'.$i]."/100 <br>\n";

        $vrhBedrag+=$waarde;
        //echo "eind $i $vrhBedrag <br>\n";
        return $vrhBedrag;
      }
      else
      {
        $waarde=($vrhInstelling['vermogen'.$i]-$berekendOver)*$vrhInstelling['percentage'.$i]/100;
        //echo "stap $i $waarde=(".$vrhInstelling['vermogen'.$i]."-".$berekendOver.")*".$vrhInstelling['percentage'.$i]."/100 <br>\n";
        $vrhBedrag+=$waarde;
        $rekenVermogen=$beginRekenvermogen-$vrhInstelling['vermogen'.$i];
        $berekendOver+=$vrhInstelling['vermogen'.$i];
      }

    }
//echo "eind $vrhBedrag <br>\n";

    return $vrhBedrag;
  }

  function berekenSimulaties($jaren=0,$senarios=50)
  {
    $startJaar=substr($this->CRMdata['startdatum'],0,4)-1;
    $doelJaar =substr($this->CRMdata['doeldatum'],0,4);
    $bepaaldeJaren=$doelJaar-$startJaar;
    if($bepaaldeJaren>0 && $jaren==0)
      $jaren=$bepaaldeJaren;
    
    if(count($this->matrix) < $senarios)
    {
      echo "Onvoldoende simulaties beschikbaar";
      exit;
    }
    
    if($this->debug)
      echo "Berkeningen met $senarios senarios met $jaren jaren.<br>\n";
    for($regel=0;$regel<$senarios;$regel++)
    {
       for($jaar=0;$jaar<$jaren;$jaar++)
       {
          if($jaar==0)
          {
            $beginVermogen=$this->CRMdata['startvermogen'];
            $this->simulatie[$regel][$jaar]=$beginVermogen;
          }
          else
          {
            $beginVermogen=$this->simulatie[$regel][$jaar-1];
            
 
          $simulatieJaar=$jaar+$startJaar;
          
          if(isset($_GET['vrhMethode']))
            $vrhMethode=$_GET['vrhMethode'];
          else    
            $vrhMethode=$this->vrhMethode;
          
          $this->vrhMethode=$vrhMethode;
          if($vrhMethode>0)
          {
            /*
               0 - Geen rekening houden met VRH
               1 - Volledige VRH (startjaar = rendHeffing.jaar --> percentage)
               2 - Vrijstelling 1P VRH (startjaar = rendHeffing.jaar --> vrijstelling + percentage)
               3 - Vrijstelling 2P VRH (startjaar = rendHeffing.jaar --> vrijstelling + percentage)
            */

            if(isset($this->rendementsheffing[$simulatieJaar]))
              $vrhInstelling=$this->rendementsheffing[$simulatieJaar];
            else
              $vrhInstelling=array();

            $vrhBedrag=$this->berekenVRH($beginVermogen,$vrhMethode,$vrhInstelling);

            $beginVermogen=$beginVermogen-$vrhBedrag; 
          }
          
          if(isset($this->cashflow[$simulatieJaar]))
            $storting=$this->cashflow[$simulatieJaar];
          else
            $storting=0;

          if($this->huidigeRisicoklasse <> '' && isset($this->rendementsConfiguratiePerDatum[$this->huidigeRisicoklasse][$simulatieJaar]))
          {

            $verwachtRendement=$this->rendementsConfiguratiePerDatum[$this->huidigeRisicoklasse][$simulatieJaar][0];
            $standaarddeviatie=$this->rendementsConfiguratiePerDatum[$this->huidigeRisicoklasse][$simulatieJaar][1];

          }
          else
          {
            $verwachtRendement=$this->profieldata['verwachtRendement'];
            $standaarddeviatie=$this->profieldata['klasseStd'];
          }

          $this->simulatie[$regel][$jaar]=($beginVermogen + 0.5*$storting)* //beginvermogen+halve storting (gemiddelde over jaar)
                                             (
                                               $verwachtRendement+
                                                  ($this->matrix[$regel][$jaar] * $standaarddeviatie)
                                             )
                                             + 0.5*$storting; // Helft van storting al meegenomen in rendement de rest bijgeteld zonder rendement.
          }
            
       }
    }
  }
  
  function berekenDoelKans()
  {
    $doelGehaald=0;
    $aantalSimulaties=count($this->simulatie);
    $this->gemiddelde=0;
    $resetDoel=false;

    if($this->CRMdata['doelvermogen']==0 && $this->profieldata['ScenarioMinimaleKans']>0)
    {
      $resetDoel=true;
      foreach($this->simulatie as $regel=>$jaren)
        $gesorteerdeResultaten[$regel]=$jaren[count($jaren)-1];
      asort($gesorteerdeResultaten);
      $aantalSimulaties=count($gesorteerdeResultaten);
      $verwachteMeetpunt=$aantalSimulaties*((100-$this->profieldata['ScenarioMinimaleKans'])/100);
      $n=0;
      foreach($gesorteerdeResultaten as $index=>$waarde)
      {
        if($n>$verwachteMeetpunt)
        {
          $verwachteWaarde = $waarde;
          $this->CRMdata['doelvermogen']=$verwachteWaarde;
          break;
        }
        $n++;
      }

    }

    foreach($this->simulatie as $regel=>$jaren)
    {
      if($jaren[count($jaren)-1] >= $this->CRMdata['doelvermogen'])
        $doelGehaald++;
      
      $this->gemiddelde+=$jaren[count($jaren)-1]/$aantalSimulaties;
    }
    $this->doelKans=$doelGehaald/$aantalSimulaties*100;
    
    if($this->doelKans < $this->profieldata['ScenarioMinimaleKans'])
      $this->adviseren=true;
    else
      $this->adviseren=false;    
      
    if($this->debug)
    {
      echo "$this->doelKans % kans op doel.<br>\n";
      echo round($this->gemiddelde,2)." gemiddelde eindvermogen .<br>\n";
    }
    if($resetDoel==true)
    {
      $this->CRMdata['doelvermogen'] = 0;
      $this->doelKans=round($this->doelKans,1);
    }
    return $this->doelKans;
  }
  
  function berekenVerdeling()
  {
    if(!isset($this->verdelingFiltering))
    
    foreach($this->simulatie as $regel=>$jaren)
    {
      foreach($jaren as $jaar=>$waarde)
      {
        $waardenPerJaar[$jaar][]=$waarde;
      }
      $this->gesorteerdeResultaten[$regel]=$jaren[count($jaren)-1];
    }
    foreach($waardenPerJaar as $jaar=>$data)
      sort($waardenPerJaar[$jaar]);
    
    asort($this->gesorteerdeResultaten);
    $aantalSimulaties=count($this->gesorteerdeResultaten);
    $this->verwachteWaarden=array();

    if($this->grensMarge==0)
    {
      foreach ($this->scenarios as $scenario => $grensPercentage)
      {
    
        $n = round($grensPercentage * $aantalSimulaties / 100);
        foreach ($waardenPerJaar as $jaar => $data)
        {
          $this->scenarioGemiddelde[$scenario][$jaar] = $data[$n];
          $this->verwachteWaarden[$scenario] = $data[$n];
        }
    
      }
    }
    else
    {
      $gemiddeldeReeksen=array();
      foreach($this->scenarios as $scenario=>$grensPercentage)
      {
        $percentage=0;
        $n=0;
        $gemiddeldeReeksen[$scenario]=array();
        $grensMarge=$this->grensMarge;
        foreach($this->gesorteerdeResultaten as $index=>$waarde)
        {
          $n++;
          $percentage=$n/$aantalSimulaties*100;
          
          if($percentage>($grensPercentage-$grensMarge) && $percentage <($grensPercentage+$grensMarge))
            $gemiddeldeReeksen[$scenario][]=$index;
          
          if(!isset($this->verwachteWaarden[$scenario]))
          {
            if($percentage>$grensPercentage)
              $this->verwachteWaarden[$scenario]=$waarde;
          }
          
        }
      }
      
      $this->scenarioGemiddelde=array();
      foreach($gemiddeldeReeksen as $scenario=>$reeksIds)
      {
        $deling=count($reeksIds);
        foreach($reeksIds as $reeksId)
        {
          foreach($this->simulatie[$reeksId] as $jaar=>$waarde)
          {
            $this->scenarioGemiddelde[$scenario][$jaar]+=($waarde/$deling);
          }
        }
      }
      //listarray($this->scenarioGemiddelde);
     
    }
    if($this->debug)
    {
      listarray($this->verwachteWaarden);
      echo "<br>\n";
    }  
  }
  
}

?>