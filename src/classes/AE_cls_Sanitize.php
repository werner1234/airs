<?php

class AE_Sanitize
{
  var $replacements = array(
     "\x00"=>'\x00',
     "\n"=>'\n',
     "\r"=>'\r',
     "\\"=>'\\\\',
     "'"=>"\'",
     '"'=>'\"',
     "\x1a"=>'\x1a'
  );

  function escape($data)
  {
    if (is_array($data)) {
      foreach ($data as $key => $value) {
        $data[$key] = $this->escape($value);
      }
    } else {
      $data = htmlentities($data, ENT_QUOTES, 'UTF-8');
      $data = strtr($data, $this->replacements);
    }
    return $data;
  }

}
