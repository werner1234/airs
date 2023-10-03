<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/12/16 10:49:15 $
 		File Versie					: $Revision: 1.1 $

 		$Log: AE_cls_fondskoers.php,v $
 		Revision 1.1  2019/12/16 10:49:15  cvs
 		tbv binckImport
 		



*/
class AE_cls_fondskoers
{
  var $user;

  var $fonds;
  var $datum;
  var $koers;
  var $db;
  var $fondsArray = array();
  var $fondsData = array();
  var $f;
  function AE_cls_fondskoers()
  {
    global $USR;
	  $this->user = $USR;
	  $this->db = new DB();
	  $this->f = new AE_cls_formatter();
  }

  function fondsExist($fonds)
  {
    $query = "SELECT * FROM `Fondskoersen` WHERE `Fonds` = '$fonds'";
    if ($rec = $this->db->lookupRecordByQuery($query))
    {
      return true;
    }
    else
    {
      return false;
    }

  }

  function checkDatum($fonds, $datum)
  {
     $query = "SELECT * FROM `Fondskoersen` WHERE `Fonds` = '$fonds' AND DATE(Datum) = '$datum' ";
     if ($rec = $this->db->lookupRecordByQuery($query))
     {
//       debug($rec, $fonds." ".$datum);
       return true;
     }
     else
     {
       return false;
     }
  }

  function addFondsKoers($fonds, $datum, $koers)
  {


      if ($this->fondsExist($fonds) AND (float) $koers > 0)
      {
        $query = "INSERT INTO  `Fondskoersen` SET 
        `Fonds`       = '$fonds',
        `Datum`       = '$datum',
        `Koers`       = '$koers',
        `add_date`    = NOW(),
        `add_user`    = '{$this->user}',
        `change_date` = NOW(),
        `change_user` = '{$this->user}',
        `oorspKrsDt`  = '$datum'
        ";
//        debug($query);
//        $this->db->executeQuery($query);
      }
  }

  function addToArray($fonds, $datum, $koers)
  {

    if (!in_array($fonds, $this->fondsArray) AND trim($fonds) != "")
    {
      $this->fondsArray[] = $fonds;
      $this->fondsData[$fonds] = array(
        "datum" => $this->f->format("@D{form}", $datum),
        "koers" => $this->f->format("@N{.2}",$koers),
        "inAirs" => ($this->checkDatum($fonds, $datum) == false)?"Nee":"ja",
      );
    }
  }

  function showNotInAirs($showNewOnly = false)
  {
    $prefix = rand(1111,9999);
    $html = "
    <style>
       .tdh$prefix{
        background: rgba(20,60,90,1); 
        color: white; 
        padding: 10px
       }
       .ar{
       text-align: right;
       }
       .bgB{
         background: beige;
       }
       .ajaxOk{
       background: darkseagreen;
       color:white;
       width: 100%;
       }
       .ajaxFail{
       background: indianred;
       color:white;
       width: 100%;
       }
    </style>
    <table style='border:1px solid #999'>
      <tr>
        <td class='tdh$prefix' colspan='10' style='text-align: center' >Fondskoersen aanmaken</td>
      </tr>
      <tr>
        <td class='tdh$prefix' >Fonds</td>
        <td class='tdh$prefix'>Datum</td>
        <td class='tdh$prefix'>Koers</td>
        <td class='tdh$prefix'>in Airs</td>
      </tr>";

    $cnt = 0;
    foreach ($this->fondsData as $fonds=>$data)
    {
      $cnt++;
      if ($data["inAirs"] != "Nee" AND $showNewOnly)
      {
        continue;
      }

      if ($data["inAirs"] == "Nee")
      {
        $but = "<button class='fndKrsUpdate' id='btn-{$cnt}' data-fonds='{$fonds}' data-datum='{$data["datum"]}' data-koers='{$data["koers"]}'>aanmaken</button>";
        $tdRow = "bgB";
      }
      else{
        $but = "Ja";
        $tdRow = "";
      }
       $html .= "
        <tr>
          <td class='$tdRow'>{$fonds}</td>
          <td class='ar $tdRow'>{$data["datum"]}</td>
          <td class='ar $tdRow'>{$data["koers"]}</td>
          <td class='ar $tdRow'>{$but}</td>
        </tr>
        ";


    }
    $html .= "</table><br/><br/><br/>";
    return $html;
  }

  function js()
  {
    $out = "
<script>
  $(document).ready(function(){
    $('.fndKrsUpdate').click(function(e){
        e.preventDefault();
        var btn = $(this).attr('id');
        console.log('id=' + btn);
        var postData = {
            'fonds': $(this).data('fonds'),
            'koers': $(this).data('koers'),
            'datum': $(this).data('datum'),
            'user': '{$this->user}'
        };
        console.log(postData);
        $.ajax({
            type: 'POST',
            url: '../ajax/fondsKoersAdd.php',
            data: postData,
            dataType:'json',
            success:function(data)
            {
                $('#'+btn).addClass('ajaxOk');
                $('#'+btn).text('gelukt');
                console.log(data);
            },
            error:function(data){
                $('#'+btn).addClass('ajaxFail');
                $('#'+btn).text('MISLUKT');
                console.log('error');
            },
        });
        $(this).attr('disabled','true');
    });
  });

</script>
    
    ";
    return $out;
  }



}

