<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/05/27 13:37:19 $
 		File Versie					: $Revision: 1.10 $
    
    $Log: rekeningAddStamgegevens.php,v $
    Revision 1.10  2020/05/27 13:37:19  cvs
    no message

    Revision 1.9  2018/07/04 11:42:54  cvs
    call 6879

    Revision 1.8  2018/06/20 12:01:01  cvs
    call 6734

    Revision 1.7  2017/10/20 08:30:07  cvs
    call 6277

    Revision 1.6  2017/10/16 12:25:19  cvs
    call 6170

    Revision 1.5  2017/09/20 10:10:52  cvs
    call 2834

    Revision 1.4  2017/09/20 06:12:53  cvs
    megaupdate

    Revision 1.3  2016/09/21 08:31:03  cvs
    call 5200

    Revision 1.2  2015/01/08 11:26:15  cvs
    *** empty log message ***

    Revision 1.1  2014/11/13 11:26:01  cvs
    dbs 2834

 */
include_once("AE_cls_template.php");
class rekeningAddStamgegevens
{
  var $vb;
  var $depot;
  var $valutas;
  var $beleggingsCategorien;
  var $attributieCategorien;
  var $portefeuilles;
  var $rekeningTypes;
  var $rowIndex = 100;
  var $buffer = "";
  var $validateData = array();

  
  function rekeningAddStamgegevens($vb, $depot)
  {
    $this->vb = $vb;
    $this->depot = $depot;
//    debug($this);
    $db = new DB();
    $db->executeQuery("
      SELECT 
        Portefeuille 
      FROM 
        Portefeuilles 
      WHERE 
        Depotbank = '".$this->depot."' AND 
        Einddatum > NOW() AND
        consolidatie = 0
      ORDER BY 
        Portefeuille");
    while($portRec = $db->NextRecord())
    {
      $this->portefeuilles[] = $portRec["Portefeuille"];
    }
    
    $db->executeQuery("SELECT Valuta FROM Valutas ORDER BY Valuta");
    while($valRec = $db->NextRecord())
    {
      $this->valutas[] = $valRec["Valuta"];
    }
    $this->valutas[] = "MEM";
    $query = "
      SELECT 
        waarde as value
      FROM 
        KeuzePerVermogensbeheerder
      WHERE
        categorie='Beleggingscategorien'
      AND 
        vermogensbeheerder='".$this->vb."'
      ORDER BY
        waarde
      ";

    $db->executeQuery($query);
    if ($db->records() < 1)
    {
      $query = "
        SELECT 
          Beleggingscategorie as value
        FROM
          Beleggingscategorien
        ORDER BY
          Afdrukvolgorde ";
      $db->executeQuery($query); 
    }
    
    while ($catRec = $db->nextRecord())
    {
      
      $this->beleggingsCategorien[] = $catRec["value"];
    }

    $query = "
    SELECT 
      AttributieCategorie ,
      AttributieCategorie  
    FROM AttributieCategorien ";
    $db->executeQuery($query);
    while($attRec = $db->NextRecord())
    {
      $this->attributieCategorien[] = $attRec["AttributieCategorie"];
    }

    $query = "
    SELECT 
      check_rekeningATT as attr,
      check_rekeningCat as cat
    FROM 
      Vermogensbeheerders 
    
    WHERE 
      Vermogensbeheerder='".$this->vb."'
    ";
    $this->validateData = $db->lookupRecordByQuery($query);

    //debug( $this->validateData, $query);
//    debug($this);
    $this->rekeningTypes = array(
      "cash" => "Standaard geldrekening",
      "AABBELSP" => "AAB Beleggersspaarrek.",
      "AABONDDEP" => "AAB Ondernemersdep.",
      "AABORR" => "AAB Optimale Renterekening",
      "AABPBS" => "AAB Priv Banking Spaarrek.",
      "AABSPAAR" => "AAB Spaarrekening",
      "AABVERM" => "AAB Vermogens Spaarrek.",
      "AABzakenrek" => "AAB Zakenrekening",
      "AABMPPRC" => "AAB MeesP Part Rek Crt",
      "AABMPPBS" => "AAB MeesP Private Banking Spaarrek",
      "AABMP653" => "AAB MeesP 24.91.18.653 F BV",
      "AABMP928" => "AAB MeesP 24.93.36.928 F BV",
      "AABMP956" => "AAB MeesP 56.12.89.956 F BV",
      "AABMP483" => "AAB MeesP 56.34.74.483 F BV",
      "AABMP965" => "AAB MeesP Beleggingsrek pl 25.15.47.965",
      "AABMP379" => "AAB MP Vermogensbeheer 25.38.13.379",
      "AABMP082" => "AAB MP PB Spaarrekening 25.38.28.082",
      "AAB619" => "AAB RC EUR 40.95.80.619",
      "MARGIN" => "Margin rekening",
      "VLER"    => "Van Lanschot Effectenrekening"
    );

  }
  
  function addRekeningen($data)
  {
    global $USR;
    $db2 = new DB();
    $db = new DB();
    foreach($data as $key => $value)
    {
      $parts = explode("_",$key);
      $index = (int)$parts[0];
      if ($index > 99 AND $index < 199)
      {
        $rek[$index][$parts[1]] = $value;
      }
    }
    $count=0;
    foreach ($rek as $addRek)
    {
      $count++;
      if ($addRek["check"] == "on")
      {
        
        $query = "INSERT INTO Rekeningen SET 
          `add_date`            = NOW(),
          `add_user`            = '$USR',
          `change_date`         = NOW(),
          `change_user`         = '$USR',
          `Inactief`            = 0,
          `Rekening`            = '".$addRek["rekNr"]."',
          `Valuta`              = '".$addRek["valuta"]."',
          `Portefeuille`        = '".$addRek["portefeuille"]."',
          `Depotbank`           = '".$addRek["depot"]."',
          `Beleggingscategorie` = '".$addRek["beleggingscategorie"]."',
          `AttributieCategorie` = '".$addRek["attributiecategorie"]."',
          `typeRekening`        = '".$addRek["typeRekening"]."',
          `Memoriaal`           = '". (($addRek["memoriaal"] == "JA")?1:0) ."' 
            ";
        $db->executeQuery($query);

        $lastid = $db->last_id();

        addTrackAndTrace("Rekeningen", $lastid, "Rekening", "", $addRek["rekNr"], $USR);
        addTrackAndTrace("Valuta", $lastid, "Rekening", "", $addRek["valuta"], $USR);
        addTrackAndTrace("Portefeuille", $lastid, "Rekening", "", $addRek["portefeuille"], $USR);
        addTrackAndTrace("Depotbank", $lastid, "Rekening", "", $addRek["depot"], $USR);
        addTrackAndTrace("Beleggingscategorie", $lastid, "Rekening", "", $addRek["beleggingscategorie"], $USR);
        addTrackAndTrace("AttributieCategorie", $lastid, "Rekening", "", $addRek["attributiecategorie"], $USR);
        addTrackAndTrace("typeRekening", $lastid, "Rekening", "", $addRek["typeRekening"], $USR);
        addTrackAndTrace("Memoriaal", $lastid, "Rekening", "", (($addRek["memoriaal"] == "JA")?1:0), $USR);

        $q2 = "
        INSERT INTO 
          ae_log 
        SET  
          `txt`      ='[$count] ". addslashes($query) ."', 
          `date`     = now(),
          `add_user` = '$USR',
          `bron`     = 'rekeningAddStamgegevens.php:177'";

        $db2->executeQuery($q2);

      }
    }
    unset($_SESSION["rekeningAddArray"]);
  }
  
  function getJS()
  {
    //debug($this->validateData);
    $tmpl = new AE_template();
    $tmpl->templatePath = "../".$tmpl->templatePath;
    $tmpl->appendSubdirToTemplatePath("import");
    $tmpl->loadTemplateFromFile("addRekening.js","addRekening");
    $out = "
    </script>
    <link rel=\"stylesheet\" href=\"../style/smoothness/jquery-ui-1.11.1.custom.css\">
    
	  <script type=\"text/javascript\" src=\"../javascript/algemeen.js\"></script>
	  <script type=\"text/javascript\" src=\"../javascript/jquery-min.js\"></script>
	  <script type=\"text/javascript\" src=\"../javascript/jquery-ui-min.js\"></script>
	  <script>
    ";
    $out .= $tmpl->parseBlock("addRekening", $this->validateData);
    $ajx = new AE_cls_ajaxLookup(array("portefeuille"));
    $ajx->changeRoot = "../";
    $ajx->vbSelectReload = true;
//    $ajx->extraParameters = "vb=".$this->vb."&depot=".$this->depot;
    $ajx->extraParameters = "depot=".$this->depot;
    $ajx->changeModuleTriggerClass("portefeuille", "ajaxPortefeuille");
    $out .= "\n\n".$ajx->getJS();
    $out .= "\n\n
  console.log( 'na init attributie ='  + attributie + '  categorie = ' + categorie);
  function updateSelectionPerVb(vb, idx, port)
  {
    
    $.post('../ajax/getKeuzePerVb.php', 
      { 
        'vb' : vb,
        'cat': 'Beleggingscategorien'
      },
      function(data) 
      {
        var sel = $('#'+idx+'_beleggingscategorie');
        sel.empty();
        for (var i=0; i<data.length; i++) 
        {
          sel.append('<option value=\"' + data[i].id + '\">' + data[i].desc + '</option>');
        }
      }, 
      'json');
    
    
    $.post('../ajax/getKeuzePerVb.php', 
      { 
        'vb' : vb,
        'cat': 'AttributieCategorien'
      },
      function(data) 
      {
        var sel = $('#'+idx+'_attributiecategorie');
        sel.empty();
        for (var i=0; i<data.length; i++) 
        {
          sel.append('<option value=\"' + data[i].id + '\">' + data[i].desc + '</option>');
        }
      }, 
      'json');
    
    
    $.post('../ajax/getRekeningValidatePerVb.php', 
      { 
        'portefeuille' : port,
        
      },
      function(data) 
      {
        attributie  = data.AttributieCategorie;
        categorie   = data.Beleggingscategorie;
        console.log( 'na lookup attributie ='  + attributie + '  categorie = ' + categorie);
        if (categorie == '1')
        {
          $('#'+idx+'_beleggingscategorie').addClass('selectRood');
        }
        
        
      }, 
      'json');
    
  }


";

    return $out;
  }
  
  function getStyles()
  {
?>
<style>
  .rekTable{
    padding: 2px;
    margin:0;
    margin-top:10px;
    border:2px #333 solid;
  }
  .selectRood{
    background: #ff0612;
    color:white;
  }
  .rekTable .head{ background: #eee;  }
  .rekTable .head td{ background: #eee; padding:5px; font-weight: bold}
  .rekTable .extraRow { background: #ffcc66;  }
  
  .ac{ text-align: center}
  .ar{ text-align: right}
  .al{ text-align: left}
</style>
<?
  }
  
  function makeOptions($sourceArray, $selected, $emptyFirst=false, $keyed=false)
  {
    //debug( $sourceArray);
    define("_LF","\n",true);
    define("_TAB","\t",true);
    
    $out = _LF;
    if ($emptyFirst)
    {
      $out .= _LF._TAB._TAB."<option value=''>---</options>";
    }
    if ($keyed)
    {
      foreach ($sourceArray as $key=>$item)
      {

        $isSelected = ($key == $selected)?"SELECTED":"";
        $out .= _LF._TAB._TAB."<option value='".$key."' $isSelected>".$item."</options>";
      }
    }
    else
    {
      foreach ($sourceArray as $item)
      {

        $isSelected = ($item == $selected)?"SELECTED":"";
        $out .= _LF._TAB._TAB."<option value='".$item."' $isSelected>".$item."</options>";
      }
    }


    return $out._LF;
  }


  function getHTML()
  {
    $out  = "<script> indexCount = ".($this->rowIndex - 1)." </script>\n";
    $out .= "<table class='rekTable'>";
    $out .= $this->buffer;
    $out .= "</table>";

    return $out;
  }
  
  function makeInputRow($record)
  {
    $skipArray = array();
    define("_LF","\n");
    define("_TAB","\t");
    $idx = $this->rowIndex;
    $this->rowIndex++;
    $out = "";
    if ($idx == 100)
    {
      $out = "
               <tr class='head'><td>aanmaak</td>
               <td>rekeningnummer</td>
               <td>mem.</td>
               <td>porteuille</td>
               <td>beleggingsCategorie</td>
               <td>attributieCategorie</td>
               <td>depot</td>
               <td>type rekening</td></tr>
        ";
    }
    
    
    $db = new DB();
    $data = explode("|",$record);
    $data2 = array();
    /*
     * data[0] = depotbank
     * data[1] = portefeuille
     * data[2] = valuta
     */
    
    //debug($data);
    if ($data[0] == "SNS" OR $data[0] == "NIBC" OR $data[0] == "AAB")
    {
     
    }
    else
    {
      $query = "SELECT id FROM Rekeningen WHERE Rekening = '".$data[1].$data[2]."' ";
      if (!$rec = $db->lookupRecordByQuery($query))
      {
        if ($data[2] == "MEM")
        {
          $query = "SELECT id FROM Rekeningen WHERE Rekening = '".$data[1]."EUR' ";
          if (!$testRec = $db->lookupRecordByQuery($query))
          {
            $data2 = $data;
            $data2[2] = "EUR";
          }
        }
        if ($data[2] == "EUR")
        {
          $query = "SELECT id FROM Rekeningen WHERE Rekening = '".$data[1]."MEM' ";
          if (!$testRec = $db->lookupRecordByQuery($query))
          {
            $data2 = $data;
            $data2[2] = "MEM";
          }
        }

      }
      else
      {
        $skipArray[] = $data[1].$data[2];
        $out .=  "<tr><td colspan='20'> Rekeningnr ".$data[1].$data[2]." komt al voor icm andere depotbank, maak deze handmatig aan. </td></tr>";
      }

      
      if (!in_array(($data[1].$data[2]), $skipArray))
      {
        $valuta = ($data[2] == "MEM")?"EUR":$data[2];
        $out .= _LF."<tr>";
        $out .= _LF._TAB."<td class='ac'><input  id='{$idx}_check' name='{$idx}_check' type='checkbox' checked/></td>";
        $out .= _LF._TAB."<td><input  name='{$idx}_rekNr' value='".$data[1].$data[2]."' style='width: 90px' readonly>
                              <input type='hidden' name='{$idx}_valuta' value='{$valuta}' ></td>";
        $out .= _LF._TAB."<td class='ac'><input  name='{$idx}_memoriaal' style='width:25px' type='text' READONLY value='".(($data[2] == "MEM")?"JA":"")."'/></td>";          
        $out .= _LF._TAB."<td><input name='{$idx}_portefeuille' data-idx='{$idx}' class='ajaxPortefeuille' /></td>";
        //$out .= _LF._TAB."<td><select name='{$idx}_valuta'>".$this->makeOptions($this->valutas,$valuta,true)."</select></td>";
        $out .= _LF._TAB."<td><select name='{$idx}_beleggingscategorie' id='{$idx}_beleggingscategorie' class='selBeleg'></select></td>";
        $out .= _LF._TAB."<td><select name='{$idx}_attributiecategorie' id='{$idx}_attributiecategorie' class='selAttr'></select></td>";
        $out .= _LF._TAB."<td><input  name='{$idx}_depot' value='".$this->depot."' style='width: 40px' readonly></td>";
        $out .= _LF._TAB."<td><select name='{$idx}_typeRekening'>".$this->makeOptions($this->rekeningTypes,"",true,true)."</select></td>";
        $out .= _LF."</tr>";
        $skipArray[] = $data[1].$data[2];
      }
      
      if (count($data2) > 0)
      {
        $data = $data2;
        
        if (!in_array($data[1].$data[2], $skipArray))
        {
        
          $valuta = ($data[2] == "MEM")?"EUR":$data[2];
          $idx = $this->rowIndex;
          $this->rowIndex++;
          $out .= _LF."<!-- extra row -->";
          $out .= _LF."<tr class='extraRow'>";
          $out .= _LF._TAB."<td class='ac'><input id='{$idx}_check' name='{$idx}_check' type='checkbox' checked/></td>";
          $out .= _LF._TAB."<td><input  name='{$idx}_rekNr' value='".$data[1].$data[2]."' style='width: 90px' readonly>
                                <input type='hidden' name='{$idx}_valuta' value='{$valuta}' ></td>";
          //$out .= _LF._TAB."<td class='ac'><input  name='{$idx}_memoriaal' type='checkbox' ".(($data[2] == "MEM")?"checked":"")." /></td>";          
          $out .= _LF._TAB."<td class='ac'><input  name='{$idx}_memoriaal' type='text' style='width:25px'  READONLY value='".(($data[2] == "MEM")?"JA":"")."'/></td>"; 
          $out .= _LF._TAB."<td><input name='{$idx}_portefeuille' data-idx='{$idx}' class='ajaxPortefeuille' /></td>";
//          $out .= _LF._TAB."<td><select name='{$idx}_valuta'>".$this->makeOptions($this->valutas,$valuta,false)."</select></td>";
          $out .= _LF._TAB."<td><select name='{$idx}_beleggingscategorie' id='{$idx}_beleggingscategorie' class='selBeleg'></select></td>";
          $out .= _LF._TAB."<td><select name='{$idx}_attributiecategorie' id='{$idx}_attributiecategorie' class='selAttr'></select></td>";
          $out .= _LF._TAB."<td><input  name='{$idx}_depot' value='".$this->depot."' style='width: 40px' readonly></td>";          
          $out .= _LF._TAB."<td><select name='{$idx}_typeRekening'>".$this->makeOptions($this->rekeningTypes,"",true,true)."</select></td>";
          $out .= _LF."</tr>"; 
          $skipArray[] = $data[1].$data[2];
        }  
      }
      
    }
    $this->buffer .= "\n\n".$out;
    return true;
    
  }  
}

?>