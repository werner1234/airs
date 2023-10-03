<?php

class bedrijfSync
{
  function bedrijfSync()
  {
    $this->db=new DB();
  }
  
  function bepaalVerschil()
  {
    $query="SELECT VermogensbeheerdersPerBedrijf.Bedrijf, VermogensbeheerdersPerBedrijf.Vermogensbeheerder FROM VermogensbeheerdersPerBedrijf
JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder=VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE Vermogensbeheerders.Einddatum>now()";
    $this->db->SQL($query);
    $this->db->query();
    $bedrijven=array();
    $leidendeVBHPerBedrijf=array();
    while($data=$this->db->NextRecord())
    {
      $bedrijven[$data['Bedrijf']][]=$data['Vermogensbeheerder'];
    }
    foreach($bedrijven as $bedrijf=>$vermogensbeheerders)
    {
      if(count($vermogensbeheerders)==1)
        unset($bedrijven[$bedrijf]);
      else
      {
        $query="SELECT LeidendeVBH FROM Bedrijfsgegevens WHERE Bedrijf='".mysql_real_escape_string($bedrijf)."'";
        $this->db->SQL($query);
        $data=$this->db->lookupRecord();
        if($data['LeidendeVBH'] <> '')
          $leidendeVBHPerBedrijf[$bedrijf]=$data['LeidendeVBH'];
      }
    }
    $vergelijkTabellen=array('BeleggingscategoriePerFonds','BeleggingssectorPerFonds','ZorgplichtPerFonds');
    $this->vergelijking=array();
    $this->tabelVelden=array();
    foreach($leidendeVBHPerBedrijf as $bedrijf=>$leidendeVBH)
    {
      foreach($bedrijven[$bedrijf] as $vermogensbeheerder)
      {
        foreach($vergelijkTabellen as $tabel)
        {
          $query = "SELECT * FROM $tabel WHERE Vermogensbeheerder='".mysql_real_escape_string($vermogensbeheerder)."'";

          $this->db->SQL($query);
          $this->db->query();
          while($data=$this->db->NextRecord())
          {
            if(!isset($this->tabelVelden[$tabel]))
            {
              foreach($data as $key=>$value)
              {
                $this->tabelVelden[$tabel][]=$key;
              }
            }
            $this->vergelijking[$bedrijf][$tabel][$vermogensbeheerder][$data['Fonds']]=$data;
          }
        }
      }
    }

    $verschillen=array();
    $skipFields=array('id','add_date','change_date','add_user','change_user','Vermogensbeheerder');
    foreach($leidendeVBHPerBedrijf as $bedrijf=>$leidendeVBH)
    {
      $defaults=array();
      foreach ($vergelijkTabellen as $tabel)
      {
        foreach($this->vergelijking[$bedrijf][$tabel][$leidendeVBH] as $fonds=>$fondsData)
          $defaults[$tabel][$fonds]=$fondsData;
  
        /*
        foreach($bedrijven[$bedrijf] as $vermogensbeheerder)
        {
          foreach($this->vergelijking[$bedrijf][$tabel][$vermogensbeheerder] as $fonds=>$fondsData)
          {
            if(!isset($this->defaults[$bedrijf][$tabel][$fonds]))
            {
              $fondsData['nietLeidend']=$vermogensbeheerder;
              $this->defaults[$bedrijf][$tabel][$fonds] = $fondsData;
              $this->vergelijking[$bedrijf][$tabel][$vermogensbeheerder][$fonds]['nietLeidend']=$vermogensbeheerder;
            }
          }
    
        }
        */
  
        foreach($bedrijven[$bedrijf] as $vermogensbeheerder)
        {
          foreach($this->vergelijking[$bedrijf][$tabel][$vermogensbeheerder] as $fonds=>$fondsData)
          {
            foreach ($fondsData as $key=>$value)
            {
              if(in_array($key,$skipFields))
              {
                continue;
              }
              else
              {
                if($defaults[$tabel][$fonds][$key] <> $value)
                {
                  $verschillen[$bedrijf][$tabel][$fonds]['standaard'][$key]=$defaults[$tabel][$fonds][$key];
                //  $verschillen[$bedrijf][$tabel][$fonds]['standaard']['id']=$defaults[$tabel][$fonds]['id'];
                //  $verschillen[$bedrijf][$tabel][$fonds][$vermogensbeheerder]['id']=$fondsData['id'];
                  $verschillen[$bedrijf][$tabel][$fonds][$vermogensbeheerder][$key]=$value;
                }
              }
            }
          }
        }
      }
    }
    
    return $verschillen;
  }
  
  function maakVerschillenHtml($verschillen)
  {
    $tabelKopToegevoegd=array();
    $htmlTabel="<table border='1'>\n";
    foreach($verschillen as $bedrijf=>$tabellen)
    {
      //$htmlTabel.="<tr><td>$bedrijf</td></tr>\n";
      foreach($tabellen as $tabel=>$fondsen)
      {
        //$htmlTabel.="<tr><td>$bedrijf</td><td>$tabel</td></tr>\n";
        foreach($fondsen as $fonds=>$waardenPerVermogensbeheerder)
        {
        
          foreach($waardenPerVermogensbeheerder as $verm=>$velden)
          {
            if($verm=='standaard')
            {
              if(!isset($tabelKopToegevoegd[$bedrijf][$tabel]))
              {
                $htmlTabel .= "<tr><td><b>Bedrijf</b></td><td><b>$tabel</b></td><td><b>$verm</b></td><td><b>Fonds</b></td>";
                foreach ($this->tabelVelden[$tabel] as $key)//foreach($velden as $key=>$value)
                {
                  $htmlTabel .= "<td><b>$key</b></td>";
                }
                $htmlTabel .= " </tr>\n";
              }
              $tabelKopToegevoegd[$bedrijf][$tabel]=1;
            }
          
            $htmlTabel.="<tr><td>$bedrijf</td><td>$tabel</td><td>$verm</td><td>$fonds</td>";
           // foreach($velden as $key=>$value)
            foreach($this->tabelVelden[$tabel] as $key)
            {
              if(isset($velden[$key]))
              {
                if($verm=='standaard')
                  $htmlTabel .= "<td>" . $velden[$key] . "</td>";
                else
                  $htmlTabel .= "<td>##" . $velden[$key] . "</td>";
              }
              else
              {
                $htmlTabel .= "<td>" .$this->vergelijking[$bedrijf][$tabel][$verm][$fonds][$key]. "</td>";
              }
            }
            $htmlTabel.=" </tr>\n";
          }
        
        }
      }
    }
    $htmlTabel.="</table>\n";
    echo $htmlTabel;
  }
  
  function maakVerschillenCsv($verschillen)
  {
    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment;filename=verschillen.csv');
    $out = fopen('php://output', 'w');
    
    foreach($verschillen as $bedrijf=>$tabellen)
    {
      foreach($tabellen as $tabel=>$fondsen)
      {
        foreach($fondsen as $fonds=>$waardenPerVermogensbeheerder)
        {
        
          foreach($waardenPerVermogensbeheerder as $verm=>$velden)
          {
            if($verm=='standaard')
            {
              if(!isset($tabelKopToegevoegd[$bedrijf][$tabel]))
              {
                $data = array("Bedrijf", $tabel, $verm, "Fonds");
                foreach ($this->tabelVelden[$tabel] as $key)//foreach($velden as $key=>$value)
                {
                  $data[] = $key;
                }
                fputcsv($out, $data);
                $tabelKopToegevoegd[$bedrijf][$tabel]=1;
              }
            }
  
            $data=array($bedrijf,$tabel,$verm,$fonds);
            foreach($this->tabelVelden[$tabel] as $key)//foreach($velden as $key=>$value)
            {
              if(isset($velden[$key]))
              {
                if($verm=='standaard')
                  $data[] = $velden[$key];
                else
                  $data[] = "##".$velden[$key];
              }
              else
              {
                $data[] = $this->vergelijking[$bedrijf][$tabel][$verm][$fonds][$key];
              }
            }
            fputcsv($out, $data);
          }
        
        }
      }
    }
    fclose($out);
  }
}
