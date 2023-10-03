<?php
// include your custom functions.

function array_to_xml($array, $level=1) {
        $xml = '';
    if ($level==1) {
        $xml .= '<?xml version="1.0" encoding="ISO-8859-1"?>'.
                "\n<serverResponse>\n";
    }
    foreach ($array as $key=>$value) {
        $key = strtolower($key);
        if (is_array($value)) {
            $multi_tags = false;
            foreach($value as $key2=>$value2) {
                if (is_array($value2)) {
                    $xml .= str_repeat("\t",$level)."<$key>\n";
                    $xml .= array_to_xml($value2, $level+1);
                    $xml .= str_repeat("\t",$level)."</$key>\n";
                    $multi_tags = true;
                } else {
                    if (trim($value2)!='') {
                        if (htmlspecialchars($value2)!=$value2) {
                            $xml .= str_repeat("\t",$level).
                                    "<$key><![CDATA[$value2]]>".
                                    "</$key>\n";
                        } else {
                            $xml .= str_repeat("\t",$level).
                                    "<$key>$value2</$key>\n";
                        }
                    }
                    $multi_tags = true;
                }
            }
            if (!$multi_tags and count($value)>0) {
                $xml .= str_repeat("\t",$level)."<$key>\n";
                $xml .= array_to_xml($value, $level+1);
                $xml .= str_repeat("\t",$level)."</$key>\n";
            }
        } else {
            if (trim($value)!='') {
                if (htmlspecialchars($value)!=$value) {
                    $xml .= str_repeat("\t",$level)."<$key>".
                            "<![CDATA[$value]]></$key>\n";
                } else {
                    $xml .= str_repeat("\t",$level).
                            "<$key>$value</$key>\n";
                }
            }
        }
    }
    if ($level==1) {
        $xml .= "</serverResponse>\n";
    }
    return $xml;
}

function filterVars($formVars)
{
	reset($formVars);
	while (list($key, $value) = each($formVars))
	{
		// 
		if(substr($key,0,4) == "pre_")
		{
			$key = str_replace(substr($key,0,4),"",$key);
			$filteredArray[$key] = $value;
		}
	}
	
	return $filteredArray;
}

if(function_exists($executeFunction = "remote_".$_GET["__remoteFunction"]))
{
	$xmlData = array();
	if($result = $executeFunction($_GET))
	{
		$xmlData = array_to_xml($result,1);
		header("Content-type: text/xml"); 
		echo $xmlData;
	}
}
else 
{
	$result = array("error"=>"remote function remote_".$_GET["__remoteFunction"]." does not exist.");
	$xmlData = array();
	$xmlData = array_to_xml($result,1);
	header("Content-type: text/xml"); 
	echo $xmlData;
}
?>