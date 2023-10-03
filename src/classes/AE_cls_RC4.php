<?php
/*
    AE-ICT CODEX source module versie 1.6, 4 augustus 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/06/10 10:06:42 $
    File Versie         : $Revision: 1.2 $

    $Log: AE_cls_RC4.php,v $
    Revision 1.2  2015/06/10 10:06:42  rvv
    *** empty log message ***

    Revision 1.1  2012/01/15 11:00:50  rvv
    *** empty log message ***

    Revision 1.1  2010/08/06 16:31:14  rvv
    *** empty log message ***


*/

class AE_RC4
{
  function AE_RC4($pass)
  {
    $this->last_rc4_key='';
    $this->key=md5($pass.'-'.(ord(substr($pass,0,1))*3+7));
  }

  function RC4($text)
  {
    $key=$this->key;
    if ($this->last_rc4_key != $key)
    {
        $k = str_repeat($key, 256/strlen($key)+1);
        $rc4 = range(0, 255);
        $j = 0;
        for ($i=0; $i<256; $i++)
        {
            $t = $rc4[$i];
            $j = ($j + $t + ord($k{$i})) % 256;
            $rc4[$i] = $rc4[$j];
            $rc4[$j] = $t;
        }
        $this->last_rc4_key = $key;
        $this->last_rc4_key_c = $rc4;
    } else {
        $rc4 = $this->last_rc4_key_c;
        $rc4=$this->rc4;
    }
    $len = strlen($text);
    $a = 0;
    $b = 0;
    $out = '';
    for ($i=0; $i<$len; $i++){
        $a = ($a+1)%256;
        $t= $rc4[$a];
        $b = ($b+$t)%256;
        $rc4[$a] = $rc4[$b];
        $rc4[$b] = $t;
        $k = $rc4[($rc4[$a]+$rc4[$b])%256];
        $out.=chr(ord($text{$i}) ^ $k);
    }
    $this->rc4=$rc4;
    return $out;
  }

  function RC4_file($input,$output)
  {
    $key=$this->key;
    if($key=='')
    {
      echo 'No encryption key!';
      exit;
    }
    if ($this->last_rc4_key != $key)
    {
        $k = str_repeat($key, 256/strlen($key)+1);
        $rc4 = range(0, 255);
        $j = 0;
        for ($i=0; $i<256; $i++)
        {
            $t = $rc4[$i];
            $j = ($j + $t + ord($k{$i})) % 256;
            $rc4[$i] = $rc4[$j];
            $rc4[$j] = $t;
        }
        $this->last_rc4_key = $key;
       $this->last_rc4_key_c = $rc4;
    } else {
        $rc4 = $this->last_rc4_key_c;
    }
    $len = filesize ($input);
    $a = 0;
    $b = 0;
    $out = '';

    $rhandle = fopen($input, "r");
    $whandle = fopen($output, "w");
    $contents = fread($rhandle, $len);
    for ($i=0; $i<$len; $i++){
        $a = ($a+1)%256;
        $t= $rc4[$a];
        $b = ($b+$t)%256;
        $rc4[$a] = $rc4[$b];
        $rc4[$b] = $t;
        $k = $rc4[($rc4[$a]+$rc4[$b])%256];
        $out.=chr(ord($contents{$i}) ^ $k);
        if($i%8192==0)
        {
          fwrite($whandle, $out);
          $out='';
        }
    }
    fwrite($whandle, $out);
    fclose($whandle);
    fclose($rhandle);
  }

}
?>