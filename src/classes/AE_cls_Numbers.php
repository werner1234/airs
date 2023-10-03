<?php

class AE_Numbers {
  var $minDecimals = 2;
  var $maxDecimals = 6;

  function viewFormat2Decimals ($number) {
    return number_format($number, 2, ',', '.');
  }
  
  function viewFormatMinMaxDecimals ($number, $min = null, $max = null)
  {
    $number = floatval($number);
    /** set min and max decimals **/
    if ( ! $min ) {$min = $this->minDecimals;}
    if ( ! $max ) {$max = $this->maxDecimals;}
    
    /** get number of decimals of this number **/
    $fieldDecimals = strlen(substr(strrchr($number, '.'), 1));
    if ( $fieldDecimals < 2 ) {
      $fieldDecimals = $min;
    } elseif ( $fieldDecimals > 2 && $fieldDecimals > $max ) {
      $fieldDecimals = $max;
    }
    
    return number_format($number, $fieldDecimals, ',', '.');
  }

  function viewFormatMaxDecimals ($number, $max = null)
  {
    $number = floatval($number);
    /** set max decimals **/
    if ( ! $max ) {$max = $this->maxDecimals;}

    /** get number of decimals of this number **/
    $fieldDecimals = strlen(substr(strrchr($number, '.'), 1));
    if ( $fieldDecimals > 2 && $fieldDecimals > $max ) {
      $fieldDecimals = $max;
    }

    return number_format($number, $fieldDecimals, ',', '.');
  }
  
}