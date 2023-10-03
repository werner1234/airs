<?php

class AE_cls_RapportTemplate
{
  var $pdf = null;
  
  var $pdfPosition = array('p', 'l');
  
  var $layout = null;
  
  function AE_cls_RapportTemplate ($pdf) {
    if (is_a($pdf, 'PDFRapport')) {
      $this->pdf = $pdf;
      $this->layout = $this->pdf->rapport_layout;
    } else {
      die('Geen pdf object');
    }
  }
  
  /**
   * @param string $position portrait or landscape
   * @return bool
   */
  function getHeader($position = 'p') {
    global $__appvar;
    $pdf = $this->pdf;
    if ( ! in_array($position, $this->pdfPosition) ) {
      return false;
    }
  
    $rapportDir = $__appvar['rapportdir'] . DIRECTORY_SEPARATOR . 'include/templates/layout_'. $this->layout . DIRECTORY_SEPARATOR;
    $layoutFile = 'header_L' . $this->layout . '_' . $position . '.php';
    
    if (file_exists($rapportDir . $layoutFile)) {
      include_once($rapportDir . $layoutFile);
    }
  }
  
  /**
   * @param string $position portrait or landscape
   * @return bool
   */
  function getFooter($position = 'p') {
    global $__appvar;
    $pdf = $this->pdf;
    if ( ! in_array($position, $this->pdfPosition) ) {
      return false;
    }
    
    $rapportDir = $__appvar['rapportdir'] . DIRECTORY_SEPARATOR . 'include/templates/layout_'. $this->layout . DIRECTORY_SEPARATOR;
    $layoutFile = 'footer_L' . $this->layout . '_' . $position . '.php';
    
    if (file_exists($rapportDir . $layoutFile)) {
      include_once($rapportDir . $layoutFile);
    }
  }


}