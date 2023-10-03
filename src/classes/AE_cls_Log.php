<?php

class AE_Log {
  
  function AE_Log()
  {
    $this->db = new DB();
  }
  
  
  function logIt($txt="")
  {
    if (tableExists("ae_log"))
    {
      $txt=addslashes($txt);
      $query = "INSERT INTO ae_log SET  txt='$txt', date = now()";
      $this->db->executeQuery($query);
    }
    else
    {
      echo "<script>alert('".$txt."');</script>";
    }
  }

  
}