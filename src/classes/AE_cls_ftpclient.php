<?PHP
/*
    AE-ICT sourcemodule created 24 jan. 2022
    Author              : Chris van Santen
    Filename            : AE_cls_ftpclient.php


*/

class AE_cls_ftpclient
{

  var $con;
  var $pasv = true;
  var $file = "";
  var $debug = true;

  
  function AE_cls_ftpclient()
  {
    global $ftpClient;


    if($this->con = ftp_connect($ftpClient['server']))
    {
      $this->msg("server connected");
      if ($login_result = ftp_login($this->con, $ftpClient['user'], $ftpClient['password']))
      {
        $this->msg("login succesfull");
        if ($this->pasv)
        {
          ftp_pasv($this->con, true);
          $this->msg("PASV mode set");
        }
      }
      else
      {
        $this->msg("login failed");
      }
    }
    else
    {
      $this->msg("no connection to server");
    }
  }

  function setFile($file)
  {
//    $this->msg("cwd = ".getcwd());
    if (file_exists($file))
    {
      $this->file = $file;
      $this->msg("file set to {$file}");
    }
    else
    {
      $this->file = "";
      $this->msg("file not found {$file}");
    }

  }

  function upload()
  {
    if ($this->file != "")
    {
      $this->msg("upload started for file {$file}");
      $suc = (ftp_put($this->con, $this->file, $this->file, FTP_BINARY))?"success":"failed";
      $this->msg("upload {$suc}!");
      return true;
    }
    else
    {
      $this->msg("upload no file set!");
      return false;
    }
  }


  function download($file)
  {
    if ($this->file != "")
    {
      $this->msg("download started for file {$file}");
      $suc = (ftp_get($this->con, $this->file, $this->file, FTP_BINARY))?"success":"failed";
      $this->msg("download {$suc}!");
      return true;
    }
    else
    {
      $this->msg("download no file set!");
      return false;
    }
  }

  function close()
  {
    ftp_close($this->con);
    $this->msg("server disconnected");
  }

  function msg($txt)
  {
    if ($this->debug)
    {
      echo "<br>".date("mdY_His")."::".$txt;
    }

  }


}