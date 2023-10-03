<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/03/07 15:48:55 $
    File Versie         : $Revision: 1.4 $

    $Log: taken_ImportFase3.php,v $
    Revision 1.4  2018/03/07 15:48:55  cvs
    call 6440

    Revision 1.3  2018/03/07 09:14:03  cvs
    call 6440

    Revision 1.2  2018/03/07 09:00:45  cvs
    call 6440

    Revision 1.1  2018/03/06 14:32:02  cvs
    call 6440

    Revision 1.5  2018/02/07 13:19:32  cvs
    call 6549

    Revision 1.4  2017/12/01 11:20:38  cvs
    check of tempdir bestaat, aanmaken indien niet aanwezig

    Revision 1.3  2017/11/17 08:03:57  cvs
    call 6145

    Revision 1.2  2017/11/13 13:31:21  cvs
    call 6145 bevindingen

    Revision 1.1  2017/11/08 07:31:26  cvs
    call 6145



*/

include_once("wwwvars.php");

$db = new DB();

echo template($__appvar["templateContentHeader"],$content);
?>
  <link rel="stylesheet" href="widget/css/font-awesome.min.css">
  <link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">



<style>
  legend{
    background: rgba(20,60,90,1);
    color: white;
    width:25%;
    padding:5px;
  }
  #msg{
    padding: 15px;
    color:red;
    font-weight: bold;
  }
  .container{
    width: 800px;
  }
  #msg{
    display: none;

    background: rgba(223,235,250,1);;
    border-radius: 5px;
    color: #333;
    font-weight: bold;
    padding: 30px;
    margin-bottom: 20px;
  }

  button{
    padding:10px 15px 10px 15px ;
    background: rgba(20,60,90,1);
    color: white;
    border: 0px;
  }

  h2{ font-size: 1.2em; font-weight: bold}
  tr:nth-child(even) {background: #EEE}
  tr:nth-child(odd) {background: #FFF}
  .k1{ min-width: 250px}
  .k2{ width: 30px}
  .k3{ min-width: 250px}
  td{ padding: 5px;}
  th{ padding: 5px;}
  .head{ background: rgba(20,60,90,1); color: white; }
  .container{
    width: 800px;
  }

  .filledRow td{
    background: #FFF;
    border-top:3px rgba(20,60,90,1) solid;

  }
.notOk{
  color:red;
}
</style>
<?

$data = $_SESSION["taakImport"];

$tel = 0;
foreach ($data as $row)
{
  $tel++;
  $query = "
  INSERT INTO taken SET 
    add_date    = NOW(),
    add_user    = '$USR',
    change_date = NOW(),
    change_user = '$USR',
    gebruiker   = '{$row["A"]}',
    zichtbaar   = '".substr($row["B"],0,4)."-".substr($row["B"],4,2)."-".substr($row["B"],6,2)."',
    spoed       = '{$row["C"]}',
    relatie     = '{$row["E"]}',
    soort       = '{$row["F"]}',
    kop         = '".mysql_real_escape_string($row["G"])."',
    txt         = '".mysql_real_escape_string($row["H"])."',
    rel_id      = '{$row["D"]}'
  ";

  //debug($query);
  $db->executeQuery($query);
}

echo "<h2>Klaar, er zijn ".($tel)." taken toegevoegd";
echo template($__appvar["templateRefreshFooter"],$content);


