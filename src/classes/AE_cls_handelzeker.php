<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/04/17 07:02:21 $
 		File Versie					: $Revision: 1.10 $

 		$Log: AE_cls_handelzeker.php,v $
 		Revision 1.10  2020/04/17 07:02:21  cvs
 		call 8314
 		
 		Revision 1.9  2020/04/15 08:25:14  cvs
 		call 8314
 		
 		Revision 1.8  2020/04/06 13:43:43  cvs
 		call 8314
 		
 		Revision 1.7  2020/04/06 09:37:50  cvs
 		call 8314
 		
 		Revision 1.6  2020/01/27 13:14:06  cvs
 		call 8314
 		
 		Revision 1.5  2020/01/27 12:59:32  cvs
 		call 8314
 		
 		Revision 1.4  2020/01/20 10:15:18  cvs
 		call 8314
 		
 		Revision 1.3  2020/01/08 13:25:16  cvs
 		call 8314
 		
 		Revision 1.2  2020/01/08 13:12:20  cvs
 		call 8314
 		
 		Revision 1.1  2020/01/08 13:07:20  cvs
 		call 8314

*/
class AE_cls_handelzeker
{
  var $username;
  var $password;
  var $token ;
  var $refresh_token;
  var $result;
  var $httpCode;
  var $individuals;
  var $feedback = "no Connection";


  var $sanctionAuthorities = array(
  "ofac"  => "Office of Foreign Assets Control",
  "hmt"   => "Her Majesty's Treasury",
  "au"    => "Austrac",
  "vn"    => "United Nations",
  "eu"    => "European union",
  "ppn"   => "apan Ministry of Finance",
  "mmoi"  => "Malaysian Ministry of Home Affairs",
  "rffms" => "Russian Federal Financial Monitoring Service",
  "zse"   => "Swiss State Secretariat for Economic Affairs",
  "nsag"  => "Australian Government National Security",
  "scg"   => "Canadian Government Sanctions",
  "mas"   => "Monetary Authority of Singapore",
  "jmeti" => "Japanese Ministry of Economy Trade and Industry (METI) End User List",
  "cmps"  => "Chinese Ministry of Public Security",
  "nnts"  => "Netherlands National Terrorism Sanctions",
  "unsd"  => "Ukraine National Security and Defence Council",
  "adfat" => "Australian Department of Foreign Affairs and Trade",
  "bfpsf" => "Belgian Federal Public Service Finance",
  "fmef"  => "French Ministry of Economy and Finance, DG Treasury",
  "usds"  => "United States Department of State",
  "adb"   => "Asian Development Bank",
  "pncta" => "Pakistan National Counter Terrorism Authority",
  "usfms" => "Ukraine State Financial Monitoring Service",
);
  var $risk_classification = array(
    0 => "Niet geclassificeerd",
    1 => "Laag risico",
    2 => "Normaal risico",
    3 => "Hoog risico",
    4 => "onacceptabel risico",
  );

  var $dossierNr;
  var $referentie;

  function __construct()
  {
    global $__sanctieVars;
	  $this->username = $__sanctieVars["username"];
	  $this->password = $__sanctieVars["password"];
	  $this->getToken();
  }

  function getToken()
  {
    $url = "https://api.handelzeker.nl/jwt/login_check";
    $curl = curl_init();
    $postFields = array(
      "username" => $this->username,
      "password" => $this->password
    );
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS =>json_encode($postFields),
      CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json"
      ),
    ));


    $response = curl_exec($curl);
    $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);
    $res = (array) json_decode($response);

    if ($this->httpCode == "200")
    {
       $this->token         = $res["token"];
       $this->refresh_token = $res["refresh_token"];
       return true;
    }
    else
    {
      $this->token         = "";
      $this->refresh_token = "";
      return false;
    }

  }

  function customer_due_dilligence($inputData)
  {
    global $_SESSION;
    $_SESSION["hzStamp"] = date("Ymd-His").":".$inputData["reference"];
    $url = "https://api.handelzeker.nl/customer_due_dilligence/new?";
    $curl = curl_init();
    $postFields = array(
      "general" => array(
        "firstname"   => $inputData["firstname"],
		    "middlename"  => $inputData["middlename"],
		    "surname"     => $inputData["surname"],
		    "relevance"   => $inputData["relevance"],
		    "reference"   => $inputData["reference"],
		    "pep"         => ($inputData["pep"] == "1")?"Y":"N",
		    "pep_tier"    => "all"
      )
    );
    if ($inputData["dob"] != "")
    {
      $postFields["general"]["dob"] = $inputData["dob"];
    }

    if ($inputData["dossier"] == 1)
    {
      $postFields["general"]["create_dossier"] = true;
      $postFields["general"]["risk_classification"] = $inputData["risk_classification"];
    }
    else
    {
      if ($inputData["dossiernr"] != "")
      {
        $postFields["general"]["dossier_id"] = $inputData["dossiernr"];
      }
    }

    $this->logApiCall($postFields);
    $headers = array(
      "Content-Type: application/json",
      "Authorization: Bearer {$this->token}"
    );
//    debug($postFields);
//    debug($headers);

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS =>json_encode($postFields),
      CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);
    $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);
    $res = (array) json_decode($response, true);

//debug($res);
    $this->updateLogApiCall($res);
    if ($this->httpCode == "200" AND $res["message"] == "success")
    {
      $this->result = (array)$res["result"];
      $this->dossierNr = $res["dossier_id"];
      $this->referentie = $res["reference"];
      $this->parseIndividuals();
      return true;
    }
    else
    {
      $this->result = array();
      return false;
    }
  }

  function parseIndividuals()
  {
    $this->individuals = array();
    foreach ($this->result as $item)
    {
      $this->individuals[$item["individual"]["unique_id"]] = $item["individual"];
      $this->individuals[$item["individual"]["unique_id"]]["address"] = $item["addresses"][0];
      foreach ($item["sanction_types"] as $sanc)
      {
        if ($sanc["current"] != 0 OR $sanc["found"] != 0)
        {
          $data = $sanc;
          unset($data["body"]);
          $s[$sanc["body"]] =  $data;
        }
      }
      $this->individuals[$item["individual"]["unique_id"]]["sanction_types"] = $s;
    }

    $this->feedback = (int)count($this->individuals). " vermeldingen";

  }

  function callApiAndOutputPDF()
  {
    global $__appvar;
    define('FPDF_FONTPATH', $__appvar["basedir"] . "/html/font/");
    require_once($__appvar["basedir"]."/classes/AE_cls_fpdi.php");

    $fmt = new AE_cls_formatter();

    $pdf = new AE_cls_fpdi();
    $pdf->setSourceFile($__appvar["basedir"].'/html/handelzeker/airs-hz_briefpapier.pdf');
//$pdf->mutlipageHeader = false;
    $pdf->SetAutoPageBreak(true,15);
    $pdf->pagebreak = 190;
    $pdf->AliasNbPages();
    $pdf->AddPage();
//$pdf->Body("test");
    $leftMargin = 10;
    $tplIdx = $pdf->importPage(1);
    $pdf->useTemplate($tplIdx);
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->setXY($leftMargin, 60);
    $pdf->Cell(0, 0, "Handelzeker verslag", 0, 0, "L");

    $pdf->SetFont('Arial','',10);
    $pep = ($_REQUEST["pep"] == "1")?"Yes":"No";
    $pdf->SetTableWidths(array("40","40","50","25","25"));
    $pdf->SetTableAligns(array("L","L","L","L","L"));
    $pdf->setXY($leftMargin, 80);
    $pdf->SetFont('Arial','b',10);
    $pdf->AddTableRow(array("Ingevoerde gegevens"));
    $pdf->SetFont('Arial','',10);
    $pdf->AddTableRow(array(""));
    $pdf->AddTableRow(array("Portefeuille",$_REQUEST["reference"]));
    $pdf->AddTableRow(array("First name",$_REQUEST["firstname"]));
    $pdf->AddTableRow(array("Middle name",$_REQUEST["middlename"]));
    $pdf->AddTableRow(array("Last name", $_REQUEST["surname"]));
    $pdf->AddTableRow(array("Date of birth", $_REQUEST["dob"]));
    $pdf->AddTableRow(array("Relevance", $_REQUEST["relevance"]));
    $pdf->AddTableRow(array("PEP", $pep));
    $pdf->AddTableRow(array(""));
    $pdf->SetFont('Arial','b',10);
    $pdf->AddTableRow(array("Resultaten Handelzeker"));
    if ($this->dossierNr > 0)
    {
      $pdf->AddTableRow(array(""));
      $pdf->AddTableRow(array("Dossiernr.", $this->dossierNr));
    }
    $pdf->SetFont('Arial','',10);
    $pdf->AddTableRow(array(""));
    $pdf->AddTableRow(array("Timestamp", date("d-m-Y H:i:s")));
    $pdf->AddTableRow(array("Terugmelding", $this->feedback ));
    $pdf->SetFont('Arial','b',10);
    $pdf->AddTableRow(array(""));
    $pdf->AddTableRow(array(""));
    $pdf->AddTableRow(array("Teruggemeldingen"));
    $pdf->AddTableRow(array(""));
    $pdf->AddTableRow(array(
                        "firstname",
                        "middlename",
                        "surname",
                        "date_of_birth",
                        "unique_id",
                      ));

    $pdf->SetFont('Arial','',8);
    foreach ( $this->individuals as $item)
    {
      $pdf->AddTableRow(array(
                          $item["firstname"],
                          $item["middlename"],
                          $item["surname"],
                          $item["date_of_birth"],
                          $item["unique_id"],
                        ));
    }




    $filename = "handelzekerRapport_" . date("Ymd-his") . ".pdf";
    $pdf->Output($filename,"D");
    $bottomMargin = 65;
  }

  function toJson($data)  // output Json string
  {
    include_once "../classes/AE_cls_Json.php";
    $json = new AE_Json();
    return $json->json_encode($data);
  }

  function logApiCall($s)  // logApicall to table
  {
    global $error, $_SESSION;


    $db = new DB();
    $query = "
    INSERT INTO
      `API_HZ_logging`
    SET 
        `add_user`   = 'apiEngine'
      , `add_date` = NOW()
      , `ip`       = '".$_SERVER["REMOTE_ADDR"]."'
      , `referer`  = '".$_SESSION["hzStamp"]."'
      , `request`  = '".$this->toJson($s)."'
      , `errors`   = '".$this->toJson($error)."'
      , `results`  = ''
     ";

    $db->executeQuery($query);
    $_SESSION["logId"] = $db->last_id();


    return true;
  }


  function updateLogApiCall($res)  // update logApiCall for exit
  {
    global $error, $_SESSION;
    $db = new DB();
    $query = "
    UPDATE
      `API_HZ_logging`
    SET 
        `errors`   = '".mysql_real_escape_string($this->toJson($error))."'
      , `results`  = '".mysql_real_escape_string($this->toJson($res))."'
    WHERE 
      id = ".$_SESSION["logId"]."  
     ";

    $db->executeQuery($query);

    return true;
  }

}

