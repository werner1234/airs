<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/12/31 18:16:25 $
 		File Versie					: $Revision: 1.4 $
 		
 		$Log: AE_cls_DHTML_Calendar.php,v $
 		Revision 1.4  2014/12/31 18:16:25  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/12/10 16:08:40  rm
 		Update voor nieuwe jqueryui datepicker
 		
 		Revision 1.2  2009/09/12 10:20:24  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2005/12/16 14:43:09  jwellner
 		classes aangepast
 		
 		Revision 1.3  2005/12/07 12:16:38  cvs
 		windows compatible gemaakt
 		
 		Revision 1.2  2005/11/28 07:31:48  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2005/11/21 10:08:25  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2005/11/10 09:13:58  cvs
 		nl taal default
 		
 		Revision 1.1.1.1  2005/11/09 15:16:16  cvs
 		no message
 		
 		Revision 1.2  2005/11/09 15:09:56  cvs
 		*** empty log message ***
 		
 	
*/

/**
 *  File: calendar.php | (c) dynarch.com 2004
 *  Distributed as part of "The Coolest DHTML Calendar"
 *  under the same terms.
 *  -----------------------------------------------------------------
 *  This file implements a simple PHP wrapper for the calendar.  It
 *  allows you to easily include all the calendar files and setup the
 *  calendar by instantiating and calling a PHP object.
 */

define('NEWLINE', "\n");

class DHTML_Calendar 
{
    var $calendar_lib_path;

    var $calendar_file;
    var $calendar_lang_file;
    var $calendar_setup_file;
    var $calendar_theme_file;
    var $calendar_options;

    function DHTML_Calendar($calendar_lib_path = 'javascript/calendar/',
                            $lang              = 'nl',
                            $theme             = 'calendar-win2k-1',
                            $stripped          = true) {
        if ($stripped) {
            $this->calendar_file = 'calendar_stripped.js';
            $this->calendar_setup_file = 'calendar-setup_stripped.js';
        } else {
            $this->calendar_file = 'calendar.js';
            $this->calendar_setup_file = 'calendar-setup.js';
        }
        $this->calendar_lang_file = 'lang/calendar-' . $lang . '.js';
        $this->calendar_theme_file = $theme.'.css';
        $this->calendar_lib_path = preg_replace('/\/+$/', '/', $calendar_lib_path);
        $this->calendar_options = array('ifFormat' => '%d-%m-%Y',
                                        'daFormat' => '%d-%m-%Y');
    }

    function set_option($name, $value) {
        $this->calendar_options[$name] = $value;
    }

    function load_files() {
        echo $this->get_load_files_code();
    }

    function get_load_files_code() {
        $code  = ( '<link rel="stylesheet" type="text/css" media="all" href="' .
                   $this->calendar_lib_path . $this->calendar_theme_file .
                   '" />' . NEWLINE );
        $code .= ( '<script type="text/javascript" src="' .
                   $this->calendar_lib_path . $this->calendar_file .
                   '"></script>' . NEWLINE );
        $code .= ( '<script type="text/javascript" src="' .
                   $this->calendar_lib_path . $this->calendar_lang_file .
                   '"></script>' . NEWLINE );
        $code .= ( '<script type="text/javascript" src="' .
                   $this->calendar_lib_path . $this->calendar_setup_file .
                   '"></script>' );
        return $code;
    }

    function _make_calendar($other_options = array()) {
        $js_options = $this->_make_js_hash(array_merge($this->calendar_options, $other_options));
        $code  = ( '<script type="text/javascript">Calendar.setup({' .
                   $js_options .
                   '});</script>' );
        return $code;
    }

    function make_input_field($cal_options = array(), $field_attributes = array(), $extrastr="") 
    {
        $id = $this->_gen_id();
        $_options = array('id'   => $this->_field_id($id),'type' => 'text');
        $theOptions = $_options;
        if (is_array($cal_options))
        {
          $theOptions = array_merge($cal_options,$_options);
        }
        else
        {
          $cal_options = array();
        }
        
        $attrstr = $this->_make_html_attr(array_merge($field_attributes,
                                                      $theOptions));
        $str  = '<input ' . $attrstr .' '.$extrastr.'/>';
//        $str .= '<a href="#" id="'. $this->_trigger_id($id) . '"><img align="middle" border="0" src="' . $this->calendar_lib_path . 'img.gif" alt="" /></a>';
        if (!is_array($cal_options)) $cal_options = array();
        $options = array_merge($cal_options,
                               array('inputField' => $this->_field_id($id),
                                     'button'     => $this->_trigger_id($id)));
        if ( ! isset ($cal_options['ui_calendar']) ) {$cal_options['ui_calendar'] = false;}
        
        if(stripos($extrastr,'disabled') === false && $cal_options['ui_calendar'] === false )
        {
          $str .= '<a href="#" id="'. $this->_trigger_id($id) . '"><img align="middle" border="0" src="' . $this->calendar_lib_path . 'img.gif" alt="" /></a>';                             
				  $str .= $this->_make_calendar($options);
        }

        return $str;
    }

    /// PRIVATE SECTION

    function _field_id($id) { return 'f-calendar-field-' . $id; }
    function _trigger_id($id) { return 'f-calendar-trigger-' . $id; }
    function _gen_id() { static $id = 0; return ++$id; }

    function _make_js_hash($array) {
        $jstr = '';
        reset($array);
        while (list($key, $val) = each($array)) {
            if (is_bool($val))
                $val = $val ? 'true' : 'false';
            else if (!is_numeric($val))
                $val = '"'.$val.'"';
            if ($jstr) $jstr .= ',';
            $jstr .= '"' . $key . '":' . $val;
        }
        return $jstr;
    }

    function _make_html_attr($array) {
        $attrstr = '';
        reset($array);
        while (list($key, $val) = each($array)) {
            $attrstr .= $key . '="' . $val . '" ';
        }
        return $attrstr;
    }
};

?>