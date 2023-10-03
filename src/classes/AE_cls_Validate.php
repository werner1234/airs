<?PHP

class AE_Validate {
  
  
  function isValidEmail ($emails = null) {
    
    if ( is_array($emails) ) {
      foreach ( $emails as $email ) {
        
      }
    }
    
    $multipleEmail=explode(";",$emails);
    
    foreach ($multipleEmail as $address)
    {
      $address=trim($address);
      if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,20})$", $address) || (strlen($address)==0))
        return false;
    }
    return true;
  }
  
}