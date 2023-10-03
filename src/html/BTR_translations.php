<?php

$htmlFolder = dirname(__FILE__);
include_once($htmlFolder . "/wwwvars.php");
include_once($htmlFolder . "/../classes/mysqlList.php");

class Translations
{
  private $db;

  function Translations()
  {
    $this->db = new DB();
    $this->db->debug = false;
    $this->lang = $this->getLang();
  }

    public function getTranslationsJSON()
    {
      return $this->toJSON($this->getAppTranslations());
    }

    private function getLang()
    {
      $lang = 'nl';
      if ( isset ($_GET['lang']) && in_array ($_GET['lang'], array('nl', 'en', 'du', 'fr')) ) {
        $lang = $_GET['lang'];
      }
      return $lang;
    }

    private function getAppTranslations()
    {
        $query = "
        SELECT
          `veld`
          , `" . mysql_real_escape_string($this->lang) . "` as vertaling 
        FROM 
          `appVertaling` 
        ORDER BY 
          `veld`";

        if ($this->db->executeQuery($query) === false) {
            $this->exitWithError($this->db->errorstr);
        }

        $result = array();
        while ($tRec = $this->db->nextRecord()) {
            $key = str_replace(array('\'', '"'), array('&#39;', '&#34;'), $tRec["veld"]);
            $value = str_replace(array('\'', '"'), array('&#39;', '&#34;'), $tRec["vertaling"]);
            $result[utf8_encode($key)] = utf8_encode($value);
        }

        return $result;
    }

    private function exitWithError($message)
    {
        echo json_encode(array("error" => true, "message" => $message));
        exit;
    }

    private function toJSON($translations)
    {
        $json = json_encode($translations);
        if (function_exists('json_last_error') && json_last_error() !== JSON_ERROR_NONE) {
          $this->exitWithError(json_last_error_msg());
        }

        echo $json;
    }
}

$translations = new Translations();
header("Content-Type:application/json");
echo $translations->getTranslationsJSON();
