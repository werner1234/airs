<?php
/*
*** General-use version

DEBUG HINT:
- Inside function printbuffer make $fill=1
- Inside function Cell make:
if($fill==1 or $border==1)
{
//		if ($fill==1) $op=($border==1) ? 'B' : 'f';
//		else $op='S';
$op='S';
- Following these 2 steps you will be able to see the cell's boundaries

WARNING: When adding a new tag support, also add its name inside the function DisableTags()'s very long string

ODDITIES (?):
. It seems like saved['border'] and saved['bgcolor'] are useless inside the FlowingBlock...
These 2 attributes do the same thing?!?:
. $this->pdf->currentfont - mine
. $this->pdf->CurrentFont - fpdf's

TODO (in the future...):
- Make font-family, font-size, lineheight customizable
- Increase number of HTML/CSS tags/properties, Image/Font Types, recognized/supported
- allow BMP support? (tried with http://phpthumb.sourceforge.net/ but failed)
- Improve CSS support
- support image side-by-side or one-below-another or both?
- Improve code clarity even more (modularize and get better var names like on textbuffer array's indexes for example)

//////////////////////////////////////////////////////////////////////////////
//////////////DO NOT MODIFY THE CONTENTS OF THIS BOX//////////////////////////
//////////////////////////////////////////////////////////////////////////////
//                                                                          //
// HTML2FPDF is a php script to read a HTML text and generate a PDF file.   //
// Copyright (C) 2004-2005 Renato Coelho                                    //
// This script may be distributed as long as the following files are kept   //
// together: 								                                                //
//	                          					                                    //
// fpdf.php, html2fpdf.php, gif.php,htmltoolkit.php,license.txt,credits.txt //
//                                                                          //
//////////////////////////////////////////////////////////////////////////////

Misc. Observations:
- CSS + align = bug! (?)
OBS1: para textos de mais de 1 página, talvez tenha que juntar varios $texto_artigo
antes de mandar gerar o PDF, para que o PDF gerado seja completo.
OBS2: there are 2 types of spaces 32 and 160 (ascii values)
OBS3: //! is a special comment to be used with source2doc.php, a script I created
in order to generate the doc on the site html2fpdf.sf.net
OBS4: var $LineWidth; // line width in user unit - use this to make css thin/medium/thick work
OBS5: Images and Textareas: when they are inserted you can only type below them (==display:block)
OBS6: Optimized to 'A4' paper (default font: Arial , normal , size 11 )
OBS7: Regexp + Perl ([preg]accepts non-greedy quantifiers while PHP[ereg] does not)
Perl:  '/regexp/x'  where x == option ( x = i:ignore case , x = s: DOT gets \n as well)
========================END OF INITIAL COMMENTS=================================
*/

define('HTML2FPDF_VERSION','3.0(beta)');
if (!defined('RELATIVE_PATH')) define('RELATIVE_PATH','');
if (!defined('FPDF_FONTPATH')) define('FPDF_FONTPATH','font/');
require_once(RELATIVE_PATH.'AE_cls_fpdf.php');
require_once(RELATIVE_PATH.'AE_cls_htmltoolkit.php');

class html2fpdfRapport extends FPDF
{
//internal attributes
var $HREF; //! string
var $pgwidth; //! float
var $fontlist; //! array
var $issetfont; //! bool
var $issetcolor; //! bool
var $titulo; //! string
var $oldx; //! float
var $oldy; //! float
var $B; //! int
var $U; //! int
var $I; //! int

var $tablestart; //! bool
var $tdbegin; //! bool
var $table; //! array
var $cell; //! array
var $col; //! int
var $row; //! int

var $divbegin; //! bool
var $divalign; //! char
var $divwidth; //! float
var $divheight; //! float
var $divbgcolor; //! bool
var $divcolor; //! bool
var $divborder; //! int
var $divrevert; //! bool

var $listlvl; //! int
var $listnum; //! int
var $listtype; //! string
//array(lvl,# of occurrences)
var $listoccur; //! array
//array(lvl,occurrence,type,maxnum)
var $listlist; //! array
//array(lvl,num,content,type)
var $listitem; //! array

var $buffer_on; //! bool
var $pbegin; //! bool
var $pjustfinished; //! bool
var $blockjustfinished; //! bool
var $SUP; //! bool
var $SUB; //! bool
var $toupper; //! bool
var $tolower; //! bool
var $dash_on; //! bool
var $dotted_on; //! bool
var $strike; //! bool

var $CSS; //! array
var $cssbegin; //! bool
var $backupcss; //! array
var $textbuffer; //! array
var	$currentstyle; //! string
var $currentfont; //! string
var $colorarray; //! array
var $bgcolorarray; //! array
var $internallink; //! array
var $enabledtags; //! string

var $lineheight; //! int
var $basepath; //! string
// array('COLOR','WIDTH','OLDWIDTH')
var $outlineparam; //! array
var $outline_on; //! bool

var $specialcontent; //! string
var $selectoption; //! array

//options attributes
var $usecss; //! bool
var $usepre; //! bool
var $usetableheader; //! bool
var $shownoimg; //! bool

function html2fpdfRapport($orientation='P',$unit='mm',$format='A4')
{
//! @desc Constructor
//! @return An object (a class instance)
	//Call parent constructor
	//$this->FPDF($orientation,$unit,$format);
	//To make the function Footer() work properly
	$this->pdf->AliasNbPages();
	//Enable all tags as default
	$this->DisableTags();

  //Set default display preferences
 // $this->DisplayPreferences('');
	//Initialization of the attributes
	$this->pdf->SetFont('Arial','',11); // Changeable?(not yet...)
  $this->pdf->lineheight = 5; // Related to FontSizePt == 11
  $this->pdf->pgwidth = $this->pdf->fw - $this->pdf->lMargin - $this->pdf->rMargin ;
  $this->pdf->SetFillColor(255);
	$this->pdf->HREF='';
	$this->pdf->titulo='';
	$this->pdf->oldx=-1;
	$this->pdf->oldy=-1;
	$this->pdf->B=0;
	$this->pdf->U=0;
	$this->pdf->I=0;

  $this->pdf->listlvl=0;
  $this->pdf->listnum=0;
  $this->pdf->listtype='';
  $this->pdf->listoccur=array();
  $this->pdf->listlist=array();
  $this->pdf->listitem=array();

  $this->pdf->tablestart=false;
  $this->pdf->tdbegin=false;
  $this->pdf->table=array();
  $this->pdf->cell=array();
  $this->pdf->col=-1;
  $this->pdf->row=-1;

	$this->pdf->divbegin=false;
	$this->pdf->divalign="L";
	$this->pdf->divwidth=0;
	$this->pdf->divheight=0;
	$this->pdf->divbgcolor=false;
	$this->pdf->divcolor=false;
	$this->pdf->divborder=0;
	$this->pdf->divrevert=false;

	$this->fontlist=array("arial","times","courier","helvetica","symbol","monospace","sans");//"serif",
	$this->pdf->issetfont=false;
	$this->pdf->issetcolor=false;

  $this->pdf->pbegin=false;
  $this->pdf->pjustfinished=false;
  $this->pdf->blockjustfinished = true; //in order to eliminate exceeding left-side spaces
  $this->pdf->toupper=false;
  $this->pdf->tolower=false;
	$this->pdf->dash_on=false;
	$this->pdf->dotted_on=false;
  $this->pdf->SUP=false;
  $this->pdf->SUB=false;
  $this->pdf->buffer_on=false;
  $this->pdf->strike=false;

	$this->pdf->currentfont='';
	$this->pdf->currentstyle='';
  $this->pdf->colorarray=array();
  $this->pdf->bgcolorarray=array();
	$this->CSSbegin=false;
  $this->pdf->textbuffer=array();
	$this->CSS=array();
	$this->pdf->backupcss=array();
	$this->pdf->internallink=array();

  $this->pdf->basepath = "";

  $this->pdf->outlineparam = array();
  $this->pdf->outline_on = false;

  $this->pdf->specialcontent = '';
  $this->pdf->selectoption = array();

  $this->pdf->shownoimg=false;
  $this->pdf->usetableheader=false;
  $this->pdf->usecss=true;
  $this->pdf->usepre=true;
}

//Page header
function Header($content='')
{
//! @return void
//! @desc The header is printed in every page.
  if($this->pdf->usetableheader and $content != '')
  {
    $y = $this->pdf->y;
    foreach($content as $tableheader)
    {
      $this->pdf->y = $y;
      //Set some cell values
      $x = $tableheader['x'];
      $w = $tableheader['w'];
      $h = $tableheader['h'];
      $va = $tableheader['va'];
      $mih = $tableheader['mih'];
      $fill = $tableheader['bgcolor'];
      $border = $tableheader['border'];
      $align = $tableheader['a'];
      //Align
      $this->pdf->divalign=$align;
			$this->pdf->x = $x;
		  //Vertical align
		  if (!isset($va) || $va=='M') $this->pdf->y += ($h-$mih)/2;
      elseif (isset($va) && $va=='B') $this->pdf->y += $h-$mih;
			if ($fill)
      {
 					$color = ConvertColor($fill);
 					$this->pdf->SetFillColor($color['R'],$color['G'],$color['B']);
 					$this->pdf->Rect($x, $y, $w, $h, 'F');
			}
   		//Border
  		if (isset($border) and $border != 'all') $this->pdf->_tableRect($x, $y, $w, $h, $border);
  		elseif (isset($border) && $border == 'all') $this->pdf->Rect($x, $y, $w, $h);
  		//Print cell content
      $this->pdf->divwidth = $w-2;
      $this->pdf->divheight = 1.1*$this->pdf->lineheight;
      $textbuffer = $tableheader['textbuffer'];
      if (!empty($textbuffer)) $this->printbuffer($textbuffer,false,true/*inside a table*/);
      $textbuffer = array();
    }
    $this->pdf->y = $y + $h; //Update y coordinate
  }//end of 'if usetableheader ...'
}

function setBasePath($str)
{
//! @desc Inform the script where the html file is (full path - e.g. http://www.google.com/dir1/dir2/dir3/file.html ) in order to adjust HREF and SRC links. No-Parameter: The directory where this script is.
//! @return void
  $this->pdf->basepath = dirname($str) . "/";
  $this->pdf->basepath = str_replace("\\","/",$this->pdf->basepath); //If on Windows
}

function ShowNOIMG_GIF($opt=true)
{
//! @desc Enable/Disable Displaying the no_img.gif when an image is not found. No-Parameter: Enable
//! @return void
  $this->pdf->shownoimg=$opt;
}

function UseCSS($opt=true)
{
//! @desc Enable/Disable CSS recognition. No-Parameter: Enable
//! @return void
  $this->pdf->usecss=$opt;
}

function UseTableHeader($opt=true)
{
//! @desc Enable/Disable Table Header to appear every new page. No-Parameter: Enable
//! @return void
  $this->pdf->usetableheader=$opt;
}

function UsePRE($opt=true)
{
//! @desc Enable/Disable pre tag recognition. No-Parameter: Enable
//! @return void
  $this->pdf->usepre=$opt;
}


///////////////////
/// HTML parser ///
///////////////////
function WriteHTML($html)
{
//! @desc HTML parser
//! @return void
/* $e == content */

  $this->ReadMetaTags($html);
  $html = AdjustHTML($html,$this->pdf->usepre); //Try to make HTML look more like XHTML

  if ($this->pdf->usecss) $html = $this->ReadCSS($html);
	//Add new supported tags in the DisableTags function
	$html=str_replace('<?','< ',$html); //Fix '<?XML' bug from HTML code generated by MS Word
	$html=strip_tags($html,$this->enabledtags); //remove all unsupported tags, but the ones inside the 'enabledtags' string
  //Explode the string in order to parse the HTML code
	$a=preg_split('/<(.*?)>/ms',$html,-1,PREG_SPLIT_DELIM_CAPTURE);

	$html=str_replace("\r",'---',$html);

	foreach($a as $i => $e)
	{

		if($i%2==0)
		{
			//TEXT

			//Adjust lineheight
      //			$this->pdf->lineheight = (5*$this->pdf->FontSizePt)/11; //should be inside printbuffer?
			//Adjust text, if needed
			if (strpos($e,"&") !== false) //HTML-ENTITIES decoding
			{
        if (strpos($e,"#") !== false) $e = value_entity_decode($e); // Decode value entities
        //Avoid crashing the script on PHP 4.0
        $version = phpversion();
        $version = str_replace('.','',$version);
        if ($version >= 430) $e = html_entity_decode($e,ENT_QUOTES,'cp1252'); // changes &nbsp; and the like by their respective char
        else $e = lesser_entity_decode($e);
      }
      $e = str_replace(chr(160),chr(32),$e); //unify ascii code of spaces (in order to recognize all of them correctly)
      if (strlen($e) == 0) continue;
			if ($this->pdf->divrevert) $e = strrev($e);
			if ($this->pdf->toupper) $e = strtoupper($e);
			if ($this->pdf->tolower) $e = strtolower($e);
      //Start of 'if/elseif's
			if($this->pdf->titulo) $this->pdf->SetTitle($e);
  		elseif($this->pdf->specialcontent)
			{
			    if ($this->pdf->specialcontent == "type=select" and $this->pdf->selectoption['ACTIVE'] == true) //SELECT tag (form element)
          {
             $stringwidth = $this->pdf->GetStringWidth($e);
             if (!isset($this->pdf->selectoption['MAXWIDTH']) or $stringwidth > $this->pdf->selectoption['MAXWIDTH']) $this->pdf->selectoption['MAXWIDTH'] = $stringwidth;
             if (!isset($this->pdf->selectoption['SELECTED']) or $this->pdf->selectoption['SELECTED'] == '') $this->pdf->selectoption['SELECTED'] = $e;
          }
          else $this->pdf->textbuffer[] = array("»¤¬"/*identifier*/.$this->pdf->specialcontent."»¤¬".$e);
      }
			elseif($this->pdf->tablestart)
			{
          if($this->pdf->tdbegin)
          {
	  				$this->pdf->cell[$this->pdf->row][$this->pdf->col]['textbuffer'][] = array($e,$this->pdf->HREF,$this->pdf->currentstyle,$this->pdf->colorarray,$this->pdf->currentfont,$this->pdf->SUP,$this->pdf->SUB,''/*internal link*/,$this->pdf->strike,$this->pdf->outlineparam,$this->pdf->bgcolorarray);
  					$this->pdf->cell[$this->pdf->row][$this->pdf->col]['text'][] = $e;
            $this->pdf->cell[$this->pdf->row][$this->pdf->col]['s'] += $this->pdf->GetStringWidth($e);
					}
					//Ignore content between <table>,<tr> and a <td> tag (this content is usually only a bunch of spaces)
			}
			elseif($this->pdf->pbegin or $this->pdf->HREF or $this->pdf->divbegin or $this->pdf->SUP or $this->pdf->SUB or $this->pdf->strike or $this->pdf->buffer_on) $this->pdf->textbuffer[] = array($e,$this->pdf->HREF,$this->pdf->currentstyle,$this->pdf->colorarray,$this->pdf->currentfont,$this->pdf->SUP,$this->pdf->SUB,''/*internal link*/,$this->pdf->strike,$this->pdf->outlineparam,$this->pdf->bgcolorarray); //Accumulate text on buffer
			else
			{
     			if ($this->pdf->blockjustfinished) $e = ltrim($e);
     			if ($e != '')
     			{
               $this->pdf->Write($this->pdf->lineheight,$e); //Write text directly in the PDF
               if ($this->pdf->pjustfinished) $this->pdf->pjustfinished = false;
          }
      }
		}
		else
		{
			//Tag
			if($e{0}=='/') $this->CloseTag(strtoupper(substr($e,1)));
			else
			{
        $regexp = '|=\'(.*?)\'|s'; // eliminate single quotes, if any
      	$e = preg_replace($regexp,"=\"\$1\"",$e);
				$regexp = '| (\\w+?)=([^\\s>"]+)|si'; // changes anykey=anyvalue to anykey="anyvalue" (only do this when this happens inside tags)
      	$e = preg_replace($regexp," \$1=\"\$2\"",$e);
      	//Fix path values, if needed
      	if ((stristr($e,"href=") !== false) or (stristr($e,"src=") !== false) )
        {
            $regexp = '/ (href|src)="(.*?)"/i';
            preg_match($regexp,$e,$auxiliararray);
            $path = $auxiliararray[2];
            $path = str_replace("\\","/",$path); //If on Windows
            //Get link info and obtain its absolute path
            $regexp = '|^./|';
            $path = preg_replace($regexp,'',$path);
            if($path{0} != '#') //It is not an Internal Link
            {
              if (strpos($path,"../") !== false ) //It is a Relative Link
              {
                  $backtrackamount = substr_count($path,"../");
                  $maxbacktrack = substr_count($this->pdf->basepath,"/") - 1;
                  $filepath = str_replace("../",'',$path);
                  $path = $this->pdf->basepath;
                  //If it is an invalid relative link, then make it go to directory root
                  if ($backtrackamount > $maxbacktrack) $backtrackamount = $maxbacktrack;
                  //Backtrack some directories
                  for( $i = 0 ; $i < $backtrackamount + 1 ; $i++ ) $path = substr( $path, 0 , strrpos($path,"/") );
                  $path = $path . "/" . $filepath; //Make it an absolute path
              }
              elseif( strpos($path,":/") === false) //It is a Local Link
              {
                $path = $this->pdf->basepath . $path;
              }
              //Do nothing if it is an Absolute Link
            }
            $regexp = '/ (href|src)="(.*?)"/i';
          	$e = preg_replace($regexp,' \\1="'.$path.'"',$e);
        }//END of Fix path values
				//Extract attributes
				$contents=array();
        preg_match_all('/\\S*=["\'][^"\']*["\']/',$e,$contents);
        preg_match('/\\S+/',$e,$a2);
        $tag=strtoupper($a2[0]);
				$attr=array();
				if (!empty($contents))
				{
  				foreach($contents[0] as $v)
  				{
  				    if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
    					{
    						$attr[strtoupper($a3[1])]=$a3[2];
     					}
  				}
				}
				$this->OpenTag($tag,$attr);
			}
		}
	}//end of	foreach($a as $i=>$e)
	//Create Internal Links, if needed
  if (!empty($this->pdf->internallink) )
  {
    foreach($this->pdf->internallink as $k=>$v)
    {
      if (strpos($k,"#") !== false ) continue; //ignore
      $ypos = $v['Y'];
      $pagenum = $v['PAGE'];
      $sharp = "#";
      while (array_key_exists($sharp.$k,$this->pdf->internallink))
      {
         $internallink = $this->pdf->internallink[$sharp.$k];
         $this->pdf->SetLink($internallink,$ypos,$pagenum);
         $sharp .= "#";
      }
    }
  }
}

function OpenTag($tag,$attr)
{
//! @return void
// What this gets: < $tag $attr['WIDTH']="90px" > does not get content here </closeTag here>

  $align = array('left'=>'L','center'=>'C','right'=>'R','top'=>'T','middle'=>'M','bottom'=>'B','justify'=>'J');

  $this->pdf->blockjustfinished=false;
	//Opening tag
	switch($tag){
	  case 'PAGE_BREAK': //custom-tag
	  case 'NEWPAGE': //custom-tag
			$this->pdf->blockjustfinished = true;
	    $this->pdf->AddPage();
	    break;
	  case 'OUTLINE': //custom-tag (CSS2 property - browsers don't support it yet - Jan2005)
  	  //Usage: (default: width=normal color=white)
  	  //<outline width="(thin|medium|thick)" color="(usualcolorformat)" >Text</outline>
  	  //Mix this tag with the <font color="(usualcolorformat)"> tag to get mixed colors on outlined text!
	    $this->pdf->buffer_on = true;
	    if (isset($attr['COLOR'])) $this->pdf->outlineparam['COLOR'] = ConvertColor($attr['COLOR']);
	    else $this->pdf->outlineparam['COLOR'] = array('R'=>255,'G'=>255,'B'=>255); //white
      $this->pdf->outlineparam['OLDWIDTH'] = $this->pdf->LineWidth;
	    if (isset($attr['WIDTH']))
	    {
	       switch(strtoupper($attr['WIDTH']))
	       {
	           case 'THIN': $this->pdf->outlineparam['WIDTH'] = 0.75*$this->pdf->LineWidth; break;
	           case 'MEDIUM': $this->pdf->outlineparam['WIDTH'] = $this->pdf->LineWidth; break;
	           case 'THICK': $this->pdf->outlineparam['WIDTH'] = 1.75*$this->pdf->LineWidth; break;
         }
      }
      else $this->pdf->outlineparam['WIDTH'] = $this->pdf->LineWidth; //width == oldwidth
	    break;
	  case 'BDO':
  	  if (isset($attr['DIR']) and (strtoupper($attr['DIR']) == 'RTL' )) $this->pdf->divrevert = true;
	    break;
	  case 'S':
	  case 'STRIKE':
	  case 'DEL':
	    $this->pdf->strike=true;
	    break;
		case 'SUB':
		  $this->pdf->SUB=true;
		  break;
		case 'SUP':
		  $this->pdf->SUP=true;
      break;
    case 'CENTER':
      $this->pdf->buffer_on = true;
      if ($this->pdf->tdbegin)	$this->pdf->cell[$this->pdf->row][$this->pdf->col]['a'] = $align['center'];
      else
      {
   			$this->pdf->divalign = $align['center'];
        if ($this->pdf->x != $this->pdf->lMargin) $this->pdf->Ln($this->pdf->lineheight);
      }
      break;
    case 'ADDRESS':
      $this->pdf->buffer_on = true;
  		$this->SetStyle('I',true);
      if (!$this->pdf->tdbegin and $this->pdf->x != $this->pdf->lMargin) $this->pdf->Ln($this->pdf->lineheight);
      break;
		case 'TABLE': // TABLE-BEGIN
    	if ($this->pdf->x != $this->pdf->lMargin) $this->pdf->Ln($this->pdf->lineheight);
      $this->pdf->tablestart = true;
   		$this->pdf->table['nc'] = $this->pdf->table['nr'] = 0;
   		if (isset($attr['REPEAT_HEADER']) and $attr['REPEAT_HEADER'] == true) $this->pdf->UseTableHeader(true);
			if (isset($attr['WIDTH'])) $this->pdf->table['w']	= ConvertSize($attr['WIDTH'],$this->pdf->pgwidth);
			if (isset($attr['HEIGHT']))	$this->pdf->table['h']	= ConvertSize($attr['HEIGHT'],$this->pdf->pgwidth);
			if (isset($attr['ALIGN']))	$this->pdf->table['a']	= $align[strtolower($attr['ALIGN'])];
			if (isset($attr['BORDER']))	$this->pdf->table['border']	= $attr['BORDER'];
			if (isset($attr['BGCOLOR'])) $this->pdf->table['bgcolor'][-1]	= $attr['BGCOLOR'];
			break;
		case 'TR':
			$this->pdf->row++;
			$this->pdf->table['nr']++;
			$this->pdf->col = -1;
			if (isset($attr['BGCOLOR']))$this->pdf->table['bgcolor'][$this->pdf->row]	= $attr['BGCOLOR'];
  		break;
		case 'TH':
			$this->SetStyle('B',true);
     	if (!isset($attr['ALIGN'])) $attr['ALIGN'] = "center";
		case 'TD':
		  $this->pdf->tdbegin = true;
			$this->pdf->col++;
      while (isset($this->pdf->cell[$this->pdf->row][$this->pdf->col])) $this->pdf->col++;
			//Update number column
  		if ($this->pdf->table['nc'] < $this->pdf->col+1) $this->pdf->table['nc'] = $this->pdf->col+1;
			$this->pdf->cell[$this->pdf->row][$this->pdf->col] = array();
			$this->pdf->cell[$this->pdf->row][$this->pdf->col]['text'] = array();
			$this->pdf->cell[$this->pdf->row][$this->pdf->col]['s'] = 3;
			if (isset($attr['WIDTH'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['w'] = ConvertSize($attr['WIDTH'],$this->pdf->pgwidth);
			if (isset($attr['HEIGHT'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['h']	= ConvertSize($attr['HEIGHT'],$this->pdf->pgwidth);
			if (isset($attr['ALIGN'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['a'] = $align[strtolower($attr['ALIGN'])];
			if (isset($attr['VALIGN'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['va'] = $align[strtolower($attr['VALIGN'])];
			if (isset($attr['BORDER'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['border'] = $attr['BORDER'];
			if (isset($attr['BGCOLOR'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['bgcolor'] = $attr['BGCOLOR'];
			$cs = $rs = 1;
			if (isset($attr['COLSPAN']) && $attr['COLSPAN']>1)	$cs = $this->pdf->cell[$this->pdf->row][$this->pdf->col]['colspan']	= $attr['COLSPAN'];
			if (isset($attr['ROWSPAN']) && $attr['ROWSPAN']>1)	$rs = $this->pdf->cell[$this->pdf->row][$this->pdf->col]['rowspan']	= $attr['ROWSPAN'];
			//Chiem dung vi tri de danh cho cell span (¿mais hein?)
			for ($k=$this->pdf->row ; $k < $this->pdf->row+$rs ;$k++)
        for($l=$this->pdf->col; $l < $this->pdf->col+$cs ;$l++)
        {
  				if ($k-$this->pdf->row || $l-$this->pdf->col)	$this->pdf->cell[$k][$l] = 0;
  			}
			if (isset($attr['NOWRAP'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['nowrap']= 1;
  		break;
		case 'OL':
      if ( !isset($attr['TYPE']) or $attr['TYPE'] == '' ) $this->pdf->listtype = '1'; //OL default == '1'
      else $this->pdf->listtype = $attr['TYPE']; // ol and ul types are mixed here
		case 'UL':
      if ( (!isset($attr['TYPE']) or $attr['TYPE'] == '') and $tag=='UL')
      {
         //Insert UL defaults
         if ($this->pdf->listlvl == 0) $this->pdf->listtype = 'disc';
         elseif ($this->pdf->listlvl == 1) $this->pdf->listtype = 'circle';
         else $this->pdf->listtype = 'square';
      }
      elseif (isset($attr['TYPE']) and $tag=='UL') $this->pdf->listtype = $attr['TYPE'];
      $this->pdf->buffer_on = false;
      if ($this->pdf->listlvl == 0)
      {
        //First of all, skip a line
        if (!$this->pdf->pjustfinished)
        {
            if ($this->pdf->x != $this->pdf->lMargin) $this->pdf->Ln($this->pdf->lineheight);
            $this->pdf->Ln($this->pdf->lineheight);
        }
        $this->pdf->oldx = $this->pdf->x;
        $this->pdf->listlvl++; // first depth level
        $this->pdf->listnum = 0; // reset
        $this->pdf->listoccur[$this->pdf->listlvl] = 1;
        $this->pdf->listlist[$this->pdf->listlvl][1] = array('TYPE'=>$this->pdf->listtype,'MAXNUM'=>$this->pdf->listnum);
      }
      else
      {
        if (!empty($this->pdf->textbuffer))
        {
          $this->pdf->listitem[] = array($this->pdf->listlvl,$this->pdf->listnum,$this->pdf->textbuffer,$this->pdf->listoccur[$this->pdf->listlvl]);
          $this->pdf->listnum++;
        }
  		  $this->pdf->textbuffer = array();
  		  $occur = $this->pdf->listoccur[$this->pdf->listlvl];
        $this->pdf->listlist[$this->pdf->listlvl][$occur]['MAXNUM'] = $this->pdf->listnum; //save previous lvl's maxnum
        $this->pdf->listlvl++;
        $this->pdf->listnum = 0; // reset

        if ($this->pdf->listoccur[$this->pdf->listlvl] == 0) $this->pdf->listoccur[$this->pdf->listlvl] = 1;
        else $this->pdf->listoccur[$this->pdf->listlvl]++;
  		  $occur = $this->pdf->listoccur[$this->pdf->listlvl];
        $this->pdf->listlist[$this->pdf->listlvl][$occur] = array('TYPE'=>$this->pdf->listtype,'MAXNUM'=>$this->pdf->listnum);
      }
      break;
		case 'LI':
		  //Observation: </LI> is ignored
      if ($this->pdf->listlvl == 0) //in case of malformed HTML code. Example:(...)</p><li>Content</li><p>Paragraph1</p>(...)
      {
        //First of all, skip a line
        if (!$this->pdf->pjustfinished and $this->pdf->x != $this->pdf->lMargin) $this->pdf->Ln(2*$this->pdf->lineheight);
        $this->pdf->oldx = $this->pdf->x;
        $this->pdf->listlvl++; // first depth level
        $this->pdf->listnum = 0; // reset
        $this->pdf->listoccur[$this->pdf->listlvl] = 1;
        $this->pdf->listlist[$this->pdf->listlvl][1] = array('TYPE'=>'disc','MAXNUM'=>$this->pdf->listnum);
      }
      if ($this->pdf->listnum == 0)
      {
        $this->pdf->buffer_on = true; //activate list 'bufferization'
        $this->pdf->listnum++;
  		  $this->pdf->textbuffer = array();
      }
      else
      {
        $this->pdf->buffer_on = true; //activate list 'bufferization'
        if (!empty($this->pdf->textbuffer))
        {
          $this->pdf->listitem[] = array($this->pdf->listlvl,$this->pdf->listnum,$this->pdf->textbuffer,$this->pdf->listoccur[$this->pdf->listlvl]);
          $this->pdf->listnum++;
        }
  		  $this->pdf->textbuffer = array();
      }
      break;
		case 'H1': // 2 * fontsize
		case 'H2': // 1.5 * fontsize
		case 'H3': // 1.17 * fontsize
		case 'H4': // 1 * fontsize
		case 'H5': // 0.83 * fontsize
		case 'H6': // 0.67 * fontsize
  		//Values obtained from: http://www.w3.org/TR/REC-CSS2/sample.html
		  if(isset($attr['ALIGN'])) $this->pdf->divalign = $align[strtolower($attr['ALIGN'])];
      $this->pdf->buffer_on = true;
			if ($this->pdf->x != $this->pdf->lMargin) $this->pdf->Ln(2*$this->pdf->lineheight);
			elseif (!$this->pdf->pjustfinished) $this->pdf->Ln($this->pdf->lineheight);
			$this->SetStyle('B',true);
      switch($tag)
      {
          case 'H1':
              $this->pdf->SetFontSize(2*$this->pdf->FontSizePt);
              $this->pdf->lineheight *= 2;
              break;
          case 'H2':
              $this->pdf->SetFontSize(1.5*$this->pdf->FontSizePt);
              $this->pdf->lineheight *= 1.5;
              break;
          case 'H3':
              $this->pdf->SetFontSize(1.17*$this->pdf->FontSizePt);
              $this->pdf->lineheight *= 1.17;
              break;
          case 'H4':
              $this->pdf->SetFontSize($this->pdf->FontSizePt);
              break;
          case 'H5':
              $this->pdf->SetFontSize(0.83*$this->pdf->FontSizePt);
              $this->pdf->lineheight *= 0.83;
              break;
          case 'H6':
              $this->pdf->SetFontSize(0.67*$this->pdf->FontSizePt);
              $this->pdf->lineheight *= 0.67;
              break;
      }
		  break;
		case 'HR': //Default values: width=100% align=center color=gray
		  //Skip a line, if needed
			if ($this->pdf->x != $this->pdf->lMargin) $this->pdf->Ln($this->pdf->lineheight);
			$this->pdf->Ln(0.2*$this->pdf->lineheight);
		  $hrwidth = $this->pdf->pgwidth;
		  $hralign = 'C';
		  $hrcolor = array('R'=>200,'G'=>200,'B'=>200);
		  if($attr['WIDTH'] != '') $hrwidth = ConvertSize($attr['WIDTH'],$this->pdf->pgwidth);
		  if($attr['ALIGN'] != '') $hralign = $align[strtolower($attr['ALIGN'])];
		  if($attr['COLOR'] != '') $hrcolor = ConvertColor($attr['COLOR']);
      $this->pdf->SetDrawColor($hrcolor['R'],$hrcolor['G'],$hrcolor['B']);
      $x = $this->pdf->x;
      $y = $this->pdf->y;
      switch($hralign)
      {
          case 'L':
          case 'J':
              break;
          case 'C':
              $empty = $this->pdf->pgwidth - $hrwidth;
              $empty /= 2;
              $x += $empty;
              break;
          case 'R':
              $empty = $this->pdf->pgwidth - $hrwidth;
              $x += $empty;
              break;
      }
      $oldlinewidth = $this->pdf->LineWidth;
			$this->pdf->SetLineWidth(0.3);
			$this->pdf->Line($x,$y,$x+$hrwidth,$y);
			$this->pdf->SetLineWidth($oldlinewidth);
			$this->pdf->Ln(0.2*$this->pdf->lineheight);
		  $this->pdf->SetDrawColor(0);
      $this->pdf->blockjustfinished = true; //Eliminate exceeding left-side spaces
			break;
		case 'INS':
			$this->SetStyle('U',true);
		  break;
		case 'SMALL':
		  $newsize = $this->pdf->FontSizePt - 1;
		  $this->pdf->SetFontSize($newsize);
		  break;
		case 'BIG':
		  $newsize = $this->pdf->FontSizePt + 1;
		  $this->pdf->SetFontSize($newsize);
		case 'STRONG':
			$this->SetStyle('B',true);
			break;
		case 'CITE':
		case 'EM':
			$this->SetStyle('I',true);
			break;
		case 'TITLE':
			$this->pdf->titulo = true;
			break;
		case 'B':
		case 'I':
		case 'U':
			if( isset($attr['CLASS']) or isset($attr['ID']) or isset($attr['STYLE']) )
      {
   			$this->CSSbegin=true;
 				if (isset($attr['CLASS'])) $properties = $this->CSS[$attr['CLASS']];
				elseif (isset($attr['ID'])) $properties = $this->CSS[$attr['ID']];
				//Read Inline CSS
				if (isset($attr['STYLE'])) $properties = $this->readInlineCSS($attr['STYLE']);
				//Look for name in the $this->CSS array
				$this->pdf->backupcss = $properties;
				if (!empty($properties)) $this->setCSS($properties); //name found in the CSS array!
		  }
			$this->SetStyle($tag,true);
			break;
		case 'A':
      if (isset($attr['NAME']) and $attr['NAME'] != '') $this->pdf->textbuffer[] = array('','','',array(),'',false,false,$attr['NAME']); //an internal link (adds a space for recognition)
			if (isset($attr['HREF'])) $this->pdf->HREF=$attr['HREF'];
			break;
		case 'DIV':
      //in case of malformed HTML code. Example:(...)</div><li>Content</li><div>DIV1</div>(...)
  	  if ($this->pdf->listlvl > 0) // We are closing (omitted) OL/UL tag(s)
   	  {
	        $this->pdf->buffer_on = false;
          if (!empty($this->pdf->textbuffer)) $this->pdf->listitem[] = array($this->pdf->listlvl,$this->pdf->listnum,$this->pdf->textbuffer,$this->pdf->listoccur[$this->pdf->listlvl]);
	        $this->pdf->textbuffer = array();
	        $this->pdf->listlvl--;
	        $this->printlistbuffer();
	        $this->pdf->pjustfinished = true; //act as if a paragraph just ended
      }
			$this->pdf->divbegin=true;
      if ($this->pdf->x != $this->pdf->lMargin)	$this->pdf->Ln($this->pdf->lineheight);
			if( isset($attr['ALIGN']) and  $attr['ALIGN'] != '' ) $this->pdf->divalign = $align[strtolower($attr['ALIGN'])];
			if( isset($attr['CLASS']) or isset($attr['ID']) or isset($attr['STYLE']) )
      {
   			$this->CSSbegin=true;
 				if (isset($attr['CLASS'])) $properties = $this->CSS[$attr['CLASS']];
				elseif (isset($attr['ID'])) $properties = $this->CSS[$attr['ID']];
				//Read Inline CSS
				if (isset($attr['STYLE'])) $properties = $this->readInlineCSS($attr['STYLE']);
				//Look for name in the $this->CSS array
				if (!empty($properties)) $this->setCSS($properties); //name found in the CSS array!
		  }
			break;
		case 'IMG':
		  if(!empty($this->pdf->textbuffer) and !$this->pdf->tablestart)
		  {
		    //Output previously buffered content and output image below
        //Set some default values
        $olddivwidth = $this->pdf->divwidth;
        $olddivheight = $this->pdf->divheight;
        if ( $this->pdf->divwidth == 0) $this->pdf->divwidth = $this->pdf->pgwidth - $x + $this->pdf->lMargin;
        if ( $this->pdf->divheight == 0) $this->pdf->divheight = $this->pdf->lineheight;
        //Print content
    	  $this->printbuffer($this->pdf->textbuffer,true/*is out of a block (e.g. DIV,P etc.)*/);
        $this->pdf->textbuffer=array();
      	//Reset values
        $this->pdf->divwidth = $olddivwidth;
        $this->pdf->divheight = $olddivheight;
		    $this->pdf->textbuffer=array();
		    $this->pdf->Ln($this->pdf->lineheight);
      }
			if(isset($attr['SRC']))
      {
          $srcpath = $attr['SRC'];
  				if(!isset($attr['WIDTH'])) $attr['WIDTH'] = 0;
				  else $attr['WIDTH'] = ConvertSize($attr['WIDTH'],$this->pdf->pgwidth);//$attr['WIDTH'] /= 4;
				  if(!isset($attr['HEIGHT']))	$attr['HEIGHT'] = 0;
				  else $attr['HEIGHT'] = ConvertSize($attr['HEIGHT'],$this->pdf->pgwidth);//$attr['HEIGHT'] /= 4;
				  if ($this->pdf->tdbegin)
				  {
  				  $bak_x = $this->pdf->x;
            $bak_y = $this->pdf->y;
            //Check whether image exists locally or on the URL
            $f_exists = @fopen($srcpath,"rb");
            if (!$f_exists) //Show 'image not found' icon instead
            {
                if(!$this->pdf->shownoimg) break;
                $srcpath = str_replace("\\","/",dirname(__FILE__)) . "/";
                $srcpath .= 'no_img.gif';
            }
            $sizesarray = $this->pdf->Image($srcpath, $this->pdf->GetX(), $this->pdf->GetY(), $attr['WIDTH'], $attr['HEIGHT'],'','',false);
            $this->pdf->y = $bak_y;
            $this->pdf->x = $bak_x;
          }
				  elseif($this->pdf->pbegin or $this->pdf->divbegin)
				  {
            //In order to support <div align='center'><img ...></div>
            $ypos = 0;
  				  $bak_x = $this->pdf->x;
            $bak_y = $this->pdf->y;
            //Check whether image exists locally or on the URL
            $f_exists = @fopen($srcpath,"rb");
            if (!$f_exists) //Show 'image not found' icon instead
            {
                if(!$this->pdf->shownoimg) break;
                $srcpath = str_replace("\\","/",dirname(__FILE__)) . "/";
                $srcpath .= 'no_img.gif';
            }
            else
              $sizesarray = $this->pdf->Image($srcpath, $this->pdf->GetX(), $this->pdf->GetY(), $attr['WIDTH'], $attr['HEIGHT'],'','',false);
            $this->pdf->y = $bak_y;
            $this->pdf->x = $bak_x;
            $xpos = '';
            switch($this->pdf->divalign)
            {
                case "C":
                     $spacesize = $this->pdf->CurrentFont[ 'cw' ][ ' ' ] * ( $this->pdf->FontSizePt / 1000 );
                     $empty = ($this->pdf->pgwidth - $sizesarray['WIDTH'])/2;
                     $xpos = 'xpos='.$empty.',';
                     break;
                case "R":
                     $spacesize = $this->pdf->CurrentFont[ 'cw' ][ ' ' ] * ( $this->pdf->FontSizePt / 1000 );
                     $empty = ($this->pdf->pgwidth - $sizesarray['WIDTH']);
                     $xpos = 'xpos='.$empty.',';
                     break;
                default: break;
            }
     				$numberoflines = (integer)ceil($sizesarray['HEIGHT']/$this->pdf->lineheight) ;
     				$ypos = $numberoflines * $this->pdf->lineheight;
     				$this->pdf->textbuffer[] = array("»¤¬"/*identifier*/."type=image,ypos=$ypos,{$xpos}width=".$sizesarray['WIDTH'].",height=".$sizesarray['HEIGHT']."»¤¬".$sizesarray['OUTPUT']);
            while($numberoflines) {$this->pdf->textbuffer[] = array("\n",$this->pdf->HREF,$this->pdf->currentstyle,$this->pdf->colorarray,$this->pdf->currentfont,$this->pdf->SUP,$this->pdf->SUB,''/*internal link*/,$this->pdf->strike,$this->pdf->outlineparam,$this->pdf->bgcolorarray);$numberoflines--;}
          }
          else
          {
            $imgborder = 0;
            if (isset($attr['BORDER'])) $imgborder = ConvertSize($attr['BORDER'],$this->pdf->pgwidth);
            //Check whether image exists locally or on the URL
            $f_exists = @fopen($srcpath,"rb");
            if (!$f_exists) //Show 'image not found' icon instead
            {
                $srcpath = str_replace("\\","/",dirname(__FILE__)) . "/";
                $srcpath .= 'no_img.gif';
            }
            else
              $sizesarray = $this->pdf->Image($srcpath, $this->pdf->GetX(), $this->pdf->GetY(), $attr['WIDTH'], $attr['HEIGHT'],'',$this->pdf->HREF); //Output Image
  				  $ini_x = $sizesarray['X'];
            $ini_y = $sizesarray['Y'];
            if ($imgborder)
            {
                $oldlinewidth = $this->pdf->LineWidth;
			          $this->pdf->SetLineWidth($imgborder);
                $this->pdf->Rect($ini_x,$ini_y,$sizesarray['WIDTH'],$sizesarray['HEIGHT']);
			          $this->pdf->SetLineWidth($oldlinewidth);
            }
          }
  				if ($sizesarray['X'] < $this->pdf->x) $this->pdf->x = $this->pdf->lMargin;
  				if ($this->pdf->tablestart)
  				{
     				$this->pdf->cell[$this->pdf->row][$this->pdf->col]['textbuffer'][] = array("»¤¬"/*identifier*/."type=image,width=".$sizesarray['WIDTH'].",height=".$sizesarray['HEIGHT']."»¤¬".$sizesarray['OUTPUT']);
            $this->pdf->cell[$this->pdf->row][$this->pdf->col]['s'] += $sizesarray['WIDTH'] + 1;// +1 == margin
            $this->pdf->cell[$this->pdf->row][$this->pdf->col]['form'] = true; // in order to make some width adjustments later
            if (!isset($this->pdf->cell[$this->pdf->row][$this->pdf->col]['w'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['w'] = $sizesarray['WIDTH'] + 3;
            if (!isset($this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'] = $sizesarray['HEIGHT'] + 3;
  				}
			}
			break;
		case 'BLOCKQUOTE':
		case 'BR':
		  if($this->pdf->tablestart)
		  {
		    $this->pdf->cell[$this->pdf->row][$this->pdf->col]['textbuffer'][] = array("\n",$this->pdf->HREF,$this->pdf->currentstyle,$this->pdf->colorarray,$this->pdf->currentfont,$this->pdf->SUP,$this->pdf->SUB,''/*internal link*/,$this->pdf->strike,$this->pdf->outlineparam,$this->pdf->bgcolorarray);
      	$this->pdf->cell[$this->pdf->row][$this->pdf->col]['text'][] = "\n";
        if (!isset($this->pdf->cell[$this->pdf->row][$this->pdf->col]['maxs'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['maxs'] = $this->pdf->cell[$this->pdf->row][$this->pdf->col]['s'] +2; //+2 == margin
        elseif($this->pdf->cell[$this->pdf->row][$this->pdf->col]['maxs'] < $this->pdf->cell[$this->pdf->row][$this->pdf->col]['s']) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['maxs'] = $this->pdf->cell[$this->pdf->row][$this->pdf->col]['s']+2;//+2 == margin
        $this->pdf->cell[$this->pdf->row][$this->pdf->col]['s'] = 0;// reset
      }
			elseif($this->pdf->divbegin or $this->pdf->pbegin or $this->pdf->buffer_on)  $this->pdf->textbuffer[] = array("\n",$this->pdf->HREF,$this->pdf->currentstyle,$this->pdf->colorarray,$this->pdf->currentfont,$this->pdf->SUP,$this->pdf->SUB,''/*internal link*/,$this->pdf->strike,$this->pdf->outlineparam,$this->pdf->bgcolorarray);
			else {$this->pdf->Ln($this->pdf->lineheight);$this->pdf->blockjustfinished = true;}
			break;
		case 'P':
      //in case of malformed HTML code. Example:(...)</p><li>Content</li><p>Paragraph1</p>(...)
  	  if ($this->pdf->listlvl > 0) // We are closing (omitted) OL/UL tag(s)
   	  {
	        $this->pdf->buffer_on = false;
          if (!empty($this->pdf->textbuffer)) $this->pdf->listitem[] = array($this->pdf->listlvl,$this->pdf->listnum,$this->pdf->textbuffer,$this->pdf->listoccur[$this->pdf->listlvl]);
	        $this->pdf->textbuffer = array();
	        $this->pdf->listlvl--;
	        $this->printlistbuffer();
	        $this->pdf->pjustfinished = true; //act as if a paragraph just ended
      }
      if ($this->pdf->tablestart)
      {
          $this->pdf->cell[$this->pdf->row][$this->pdf->col]['textbuffer'][] = array($e,$this->pdf->HREF,$this->pdf->currentstyle,$this->pdf->colorarray,$this->pdf->currentfont,$this->pdf->SUP,$this->pdf->SUB,''/*internal link*/,$this->pdf->strike,$this->pdf->outlineparam,$this->pdf->bgcolorarray);
          $this->pdf->cell[$this->pdf->row][$this->pdf->col]['text'][] = "\n";
          break;
      }
		  $this->pdf->pbegin=true;
			if ($this->pdf->x != $this->pdf->lMargin) $this->pdf->Ln(2*$this->pdf->lineheight);
			elseif (!$this->pdf->pjustfinished) $this->pdf->Ln($this->pdf->lineheight);
		  //Save x,y coords in case we need to print borders...
		  $this->pdf->oldx = $this->pdf->x;
		  $this->pdf->oldy = $this->pdf->y;
			if(isset($attr['ALIGN'])) $this->pdf->divalign = $align[strtolower($attr['ALIGN'])];
			if(isset($attr['CLASS']) or isset($attr['ID']) or isset($attr['STYLE']) )
      {
   			$this->CSSbegin=true;
 				if (isset($attr['CLASS'])) $properties = $this->CSS[$attr['CLASS']];
				elseif (isset($attr['ID'])) $properties = $this->CSS[$attr['ID']];
				//Read Inline CSS
				if (isset($attr['STYLE'])) $properties = $this->readInlineCSS($attr['STYLE']);
				//Look for name in the $this->CSS array
				$this->pdf->backupcss = $properties;
				if (!empty($properties)) $this->setCSS($properties); //name(id/class/style) found in the CSS array!
		  }
			break;
		case 'SPAN':
		  $this->pdf->buffer_on = true;
 		  //Save x,y coords in case we need to print borders...
 		  $this->pdf->oldx = $this->pdf->x;
 		  $this->pdf->oldy = $this->pdf->y;
			if( isset($attr['CLASS']) or isset($attr['ID']) or isset($attr['STYLE']) )
      {
   			$this->CSSbegin=true;
 				if (isset($attr['CLASS'])) $properties = $this->CSS[$attr['CLASS']];
				elseif (isset($attr['ID'])) $properties = $this->CSS[$attr['ID']];
				//Read Inline CSS
				if (isset($attr['STYLE'])) $properties = $this->readInlineCSS($attr['STYLE']);
				//Look for name in the $this->CSS array
				$this->pdf->backupcss = $properties;
				if (!empty($properties)) $this->setCSS($properties); //name found in the CSS array!
		  }
      break;
		case 'PRE':
		  if($this->pdf->tablestart)
		  {
		    $this->pdf->cell[$this->pdf->row][$this->pdf->col]['textbuffer'][] = array("\n",$this->pdf->HREF,$this->pdf->currentstyle,$this->pdf->colorarray,$this->pdf->currentfont,$this->pdf->SUP,$this->pdf->SUB,''/*internal link*/,$this->pdf->strike,$this->pdf->outlineparam,$this->pdf->bgcolorarray);
      	$this->pdf->cell[$this->pdf->row][$this->pdf->col]['text'][] = "\n";
      }
			elseif($this->pdf->divbegin or $this->pdf->pbegin or $this->pdf->buffer_on)  $this->pdf->textbuffer[] = array("\n",$this->pdf->HREF,$this->pdf->currentstyle,$this->pdf->colorarray,$this->pdf->currentfont,$this->pdf->SUP,$this->pdf->SUB,''/*internal link*/,$this->pdf->strike,$this->pdf->outlineparam,$this->pdf->bgcolorarray);
      else
      {
      	if ($this->pdf->x != $this->pdf->lMargin) $this->pdf->Ln(2*$this->pdf->lineheight);
			  elseif (!$this->pdf->pjustfinished) $this->pdf->Ln($this->pdf->lineheight);
		    $this->pdf->buffer_on = true;
		    //Save x,y coords in case we need to print borders...
		    $this->pdf->oldx = $this->pdf->x;
		    $this->pdf->oldy = $this->pdf->y;
			  if(isset($attr['ALIGN'])) $this->pdf->divalign = $align[strtolower($attr['ALIGN'])];
			  if(isset($attr['CLASS']) or isset($attr['ID']) or isset($attr['STYLE']) )
        {
       			$this->CSSbegin=true;
            if (isset($attr['CLASS'])) $properties = $this->CSS[$attr['CLASS']];
				    elseif (isset($attr['ID'])) $properties = $this->CSS[$attr['ID']];
				    //Read Inline CSS
				    if (isset($attr['STYLE'])) $properties = $this->readInlineCSS($attr['STYLE']);
				    //Look for name in the $this->CSS array
				    $this->pdf->backupcss = $properties;
				    if (!empty($properties)) $this->setCSS($properties); //name(id/class/style) found in the CSS array!
  		  }
			}
    case 'TT':
    case 'KBD':
    case 'SAMP':
		case 'CODE':
			$this->pdf->SetFont('courier');
  		$this->pdf->currentfont='courier';
		  break;
		case 'TEXTAREA':
		  $this->pdf->buffer_on = true;
      $colsize = 20; //HTML default value
      $rowsize = 2; //HTML default value
  		if (isset($attr['COLS'])) $colsize = $attr['COLS'];
  		if (isset($attr['ROWS'])) $rowsize = $attr['ROWS'];
  		if (!$this->pdf->tablestart)
  		{
		    if ($this->pdf->x != $this->pdf->lMargin) $this->pdf->Ln($this->pdf->lineheight);
		    $this->pdf->col = $colsize;
		    $this->pdf->row = $rowsize;
		  }
		  else //it is inside a table
		  {
  		  $this->pdf->specialcontent = "type=textarea,lines=$rowsize,width=".((2.2*$colsize) + 3); //Activate form info in order to paint FORM elements within table
        $this->pdf->cell[$this->pdf->row][$this->pdf->col]['s'] += (2.2*$colsize) + 6;// +6 == margin
        if (!isset($this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'] = 1.1*$this->pdf->lineheight*$rowsize + 2.5;
      }
		  break;
		case 'SELECT':
		  $this->pdf->specialcontent = "type=select"; //Activate form info in order to paint FORM elements within table
		  break;
		case 'OPTION':
      $this->pdf->selectoption['ACTIVE'] = true;
		  if (empty($this->pdf->selectoption))
      {
  		  $this->pdf->selectoption['MAXWIDTH'] = '';
        $this->pdf->selectoption['SELECTED'] = '';
      }
      if (isset($attr['SELECTED'])) $this->pdf->selectoption['SELECTED'] = '';
		  break;
		case 'FORM':
		  if($this->pdf->tablestart)
		  {
		    $this->pdf->cell[$this->pdf->row][$this->pdf->col]['textbuffer'][] = array($e,$this->pdf->HREF,$this->pdf->currentstyle,$this->pdf->colorarray,$this->pdf->currentfont,$this->pdf->SUP,$this->pdf->SUB,''/*internal link*/,$this->pdf->strike,$this->pdf->outlineparam,$this->pdf->bgcolorarray);
      	$this->pdf->cell[$this->pdf->row][$this->pdf->col]['text'][] = "\n";
      }
		  elseif ($this->pdf->x != $this->pdf->lMargin) $this->pdf->Ln($this->pdf->lineheight); //Skip a line, if needed
		  break;
    case 'INPUT':
      if (!isset($attr['TYPE'])) $attr['TYPE'] == ''; //in order to allow default 'TEXT' form (in case of malformed HTML code)
      if (!$this->pdf->tablestart)
      {
        switch(strtoupper($attr['TYPE'])){
          case 'CHECKBOX': //Draw Checkbox
                $checked = false;
                if (isset($attr['CHECKED'])) $checked = true;
        			  $this->pdf->SetFillColor(235,235,235);
        			  $this->pdf->x += 3;
                $this->pdf->Rect($this->pdf->x,$this->pdf->y+1,3,3,'DF');
                if ($checked)
                {
                  $this->pdf->Line($this->pdf->x,$this->pdf->y+1,$this->pdf->x+3,$this->pdf->y+1+3);
                  $this->pdf->Line($this->pdf->x,$this->pdf->y+1+3,$this->pdf->x+3,$this->pdf->y+1);
                }
        			  $this->pdf->SetFillColor(255);
        			  $this->pdf->x += 3.5;
                break;
          case 'RADIO': //Draw Radio button
                $checked = false;
                if (isset($attr['CHECKED'])) $checked = true;
                $this->pdf->x += 4;
                $this->pdf->Circle($this->pdf->x,$this->pdf->y+2.2,1,'D');
                $this->pdf->_out('0.000 g');
                if ($checked) $this->pdf->Circle($this->pdf->x,$this->pdf->y+2.2,0.4,'DF');
                $this->pdf->Write(5,$texto,$this->pdf->x);
                $this->pdf->x += 2;
                break;
          case 'BUTTON': // Draw a button
          case 'SUBMIT':
          case 'RESET':
                $texto='';
                if (isset($attr['VALUE'])) $texto = $attr['VALUE'];
                $nihil = 2.5;
                $this->pdf->x += 2;
        			  $this->pdf->SetFillColor(190,190,190);
                $this->pdf->Rect($this->pdf->x,$this->pdf->y,$this->pdf->GetStringWidth($texto)+2*$nihil,4.5,'DF'); // 4.5 in order to avoid overlapping
        			  $this->pdf->x += $nihil;
                $this->pdf->Write(5,$texto,$this->pdf->x);
        			  $this->pdf->x += $nihil;
        			  $this->pdf->SetFillColor(255);
                break;
          case 'PASSWORD':
                if (isset($attr['VALUE']))
                {
                    $num_stars = strlen($attr['VALUE']);
                    $attr['VALUE'] = str_repeat('*',$num_stars);
                }
          case 'TEXT': //Draw TextField
          default: //default == TEXT
                $texto='';
                if (isset($attr['VALUE'])) $texto = $attr['VALUE'];
                $tamanho = 20;
                if (isset($attr['SIZE']) and ctype_digit($attr['SIZE']) ) $tamanho = $attr['SIZE'];
        			  $this->pdf->SetFillColor(235,235,235);
                $this->pdf->x += 2;
                $this->pdf->Rect($this->pdf->x,$this->pdf->y,2*$tamanho,4.5,'DF');// 4.5 in order to avoid overlapping
                if ($texto != '')
                {
                  $this->pdf->x += 1;
                  $this->pdf->Write(5,$texto,$this->pdf->x);
                  $this->pdf->x -= $this->pdf->GetStringWidth($texto);
                }
        		    $this->pdf->SetFillColor(255);
        		    $this->pdf->x += 2*$tamanho;
                break;
        }
      }
      else //we are inside a table
      {
        $this->pdf->cell[$this->pdf->row][$this->pdf->col]['form'] = true; // in order to make some width adjustments later
        $type = '';
        $text = '';
        $height = 0;
        $width = 0;
        switch(strtoupper($attr['TYPE'])){
          case 'CHECKBOX': //Draw Checkbox
                $checked = false;
                if (isset($attr['CHECKED'])) $checked = true;
                $text = $checked;
                $type = 'CHECKBOX';
                $width = 4;
   			        $this->pdf->cell[$this->pdf->row][$this->pdf->col]['textbuffer'][] = array("»¤¬"/*identifier*/."type=input,subtype=$type,width=$width,height=$height"."»¤¬".$text);
                $this->pdf->cell[$this->pdf->row][$this->pdf->col]['s'] += $width;
                if (!isset($this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'] = $this->pdf->lineheight;
                break;
          case 'RADIO': //Draw Radio button
                $checked = false;
                if (isset($attr['CHECKED'])) $checked = true;
                $text = $checked;
                $type = 'RADIO';
                $width = 3;
                $this->pdf->cell[$this->pdf->row][$this->pdf->col]['textbuffer'][] = array("»¤¬"/*identifier*/."type=input,subtype=$type,width=$width,height=$height"."»¤¬".$text);
                $this->pdf->cell[$this->pdf->row][$this->pdf->col]['s'] += $width;
                if (!isset($this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'] = $this->pdf->lineheight;
                break;
          case 'BUTTON': $type = 'BUTTON'; // Draw a button
          case 'SUBMIT': if ($type == '') $type = 'SUBMIT';
          case 'RESET': if ($type == '') $type = 'RESET';
                $texto='';
                if (isset($attr['VALUE'])) $texto = " " . $attr['VALUE'] . " ";
                $text = $texto;
                $width = $this->pdf->GetStringWidth($texto)+3;
                $this->pdf->cell[$this->pdf->row][$this->pdf->col]['textbuffer'][] = array("»¤¬"/*identifier*/."type=input,subtype=$type,width=$width,height=$height"."»¤¬".$text);
                $this->pdf->cell[$this->pdf->row][$this->pdf->col]['s'] += $width;
                if (!isset($this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'] = $this->pdf->lineheight + 2;
                break;
          case 'PASSWORD':
                if (isset($attr['VALUE']))
                {
                    $num_stars = strlen($attr['VALUE']);
                    $attr['VALUE'] = str_repeat('*',$num_stars);
                }
                $type = 'PASSWORD';
          case 'TEXT': //Draw TextField
          default: //default == TEXT
                $texto='';
                if (isset($attr['VALUE'])) $texto = $attr['VALUE'];
                $tamanho = 20;
                if (isset($attr['SIZE']) and ctype_digit($attr['SIZE']) ) $tamanho = $attr['SIZE'];
                $text = $texto;
                $width = 2*$tamanho;
                if ($type == '') $type = 'TEXT';
                $this->pdf->cell[$this->pdf->row][$this->pdf->col]['textbuffer'][] = array("»¤¬"/*identifier*/."type=input,subtype=$type,width=$width,height=$height"."»¤¬".$text);
                $this->pdf->cell[$this->pdf->row][$this->pdf->col]['s'] += $width;
                if (!isset($this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'] = $this->pdf->lineheight + 2;
                break;
        }
      }
      break;
		case 'FONT':
//Font size is ignored for now
			if (isset($attr['COLOR']) and $attr['COLOR']!='')
      {
				$cor = ConvertColor($attr['COLOR']);
				//If something goes wrong switch color to black
			  $cor['R'] = (isset($cor['R'])?$cor['R']:0);
        $cor['G'] = (isset($cor['G'])?$cor['G']:0);
        $cor['B'] = (isset($cor['B'])?$cor['B']:0);
			  $this->pdf->colorarray = $cor;
				$this->pdf->SetTextColor($cor['R'],$cor['G'],$cor['B']);
				$this->pdf->issetcolor = true;
			}
			if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist))
      {
				$this->pdf->SetFont(strtolower($attr['FACE']));
				$this->pdf->issetfont=true;
			}
			//'If' disabled in this version due lack of testing (you may enable it if you want)
//			if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist) and isset($attr['SIZE']) and $attr['SIZE']!='') {
//				$this->pdf->SetFont(strtolower($attr['FACE']),'',$attr['SIZE']);
//				$this->pdf->issetfont=true;
//			}
			break;
	}//end of switch
  $this->pdf->pjustfinished=false;
}

function CloseTag($tag)
{
//! @return void
	//Closing tag
	if($tag=='OPTION') $this->pdf->selectoption['ACTIVE'] = false;
	if($tag=='BDO') $this->pdf->divrevert = false;
	if($tag=='INS') $tag='U';
	if($tag=='STRONG') $tag='B';
	if($tag=='EM' or $tag=='CITE') $tag='I';
  if($tag=='OUTLINE')
  {
	  if(!$this->pdf->pbegin and !$this->pdf->divbegin and !$this->pdf->tablestart)
	  {
      //Deactivate $this->pdf->outlineparam for its info is already stored inside $this->pdf->textbuffer
      //if (isset($this->pdf->outlineparam['OLDWIDTH'])) $this->pdf->SetTextOutline($this->pdf->outlineparam['OLDWIDTH']);
      $this->pdf->SetTextOutline(false);
      $this->pdf->outlineparam=array();
      //Save x,y coords ???
      $x = $this->pdf->x;
      $y = $this->pdf->y;
      //Set some default values
      $this->pdf->divwidth = $this->pdf->pgwidth - $x + $this->pdf->lMargin;
      //Print content
  	  $this->printbuffer($this->pdf->textbuffer,true/*is out of a block (e.g. DIV,P etc.)*/);
      $this->pdf->textbuffer=array();
     	//Reset values
     	$this->Reset();
      $this->pdf->buffer_on=false;
    }
    $this->pdf->SetTextOutline(false);
    $this->pdf->outlineparam=array();
  }
	if($tag=='A')
	{
	  if(!$this->pdf->pbegin and !$this->pdf->divbegin and !$this->pdf->tablestart and !$this->pdf->buffer_on)
	  {
       //Deactivate $this->pdf->HREF for its info is already stored inside $this->pdf->textbuffer
       $this->pdf->HREF='';
       //Save x,y coords ???
       $x = $this->pdf->x;
       $y = $this->pdf->y;
       //Set some default values
       $this->pdf->divwidth = $this->pdf->pgwidth - $x + $this->pdf->lMargin;
       //Print content
       $this->printbuffer($this->pdf->textbuffer,true/*is out of a block (e.g. DIV,P etc.)*/);
       $this->pdf->textbuffer=array();
       //Reset values
       $this->Reset();
    }
    $this->pdf->HREF='';
  }
	if($tag=='TH') $this->SetStyle('B',false);
	if($tag=='TH' or $tag=='TD') $this->pdf->tdbegin = false;
	if($tag=='SPAN')
	{
    if(!$this->pdf->pbegin and !$this->pdf->divbegin and !$this->pdf->tablestart)
    {
      if($this->CSSbegin)
      {
          //Check if we have borders to print
          if ($this->CSSbegin and ($this->pdf->divborder or $this->pdf->dash_on or $this->pdf->dotted_on or $this->pdf->divbgcolor))
          {
   	          $texto='';
              foreach($this->pdf->textbuffer as $vetor) $texto.=$vetor[0];
              $tempx = $this->pdf->x;
              if($this->pdf->divbgcolor) $this->pdf->Cell($this->pdf->GetStringWidth($texto),$this->pdf->lineheight,'',$this->pdf->divborder,'','L',$this->pdf->divbgcolor);
              if ($this->pdf->dash_on) $this->pdf->Rect($this->pdf->oldx,$this->pdf->oldy,$this->pdf->GetStringWidth($texto),$this->pdf->lineheight);
		          if ($this->pdf->dotted_on) $this->pdf->DottedRect($this->pdf->x - $this->pdf->GetStringWidth($texto),$this->pdf->y,$this->pdf->GetStringWidth($texto),$this->pdf->lineheight);
              $this->pdf->x = $tempx;
              $this->pdf->x -= 1; //adjust alignment
          }
		      $this->CSSbegin=false;
		      $this->pdf->backupcss=array();
      }
      //Save x,y coords ???
      $x = $this->pdf->x;
      $y = $this->pdf->y;
      //Set some default values
      $this->pdf->divwidth = $this->pdf->pgwidth - $x + $this->pdf->lMargin;
      //Print content
  	  $this->printbuffer($this->pdf->textbuffer,true/*is out of a block (e.g. DIV,P etc.)*/);
      $this->pdf->textbuffer=array();
    	//Reset values
    	$this->Reset();
    }
    $this->pdf->buffer_on=false;
  }
	if($tag=='P' or $tag=='DIV') //CSS in BLOCK mode
	{
   $this->pdf->blockjustfinished = true; //Eliminate exceeding left-side spaces
	 if(!$this->pdf->tablestart)
   {
    if ($this->pdf->divwidth == 0) $this->pdf->divwidth = $this->pdf->pgwidth;
    if ($tag=='P')
    {
      $this->pdf->pbegin=false;
      $this->pdf->pjustfinished=true;
    }
    else $this->pdf->divbegin=false;
    $content='';
    foreach($this->pdf->textbuffer as $aux) $content .= $aux[0];
    $numlines = $this->pdf->WordWrap($content,$this->pdf->divwidth);
    if ($this->pdf->divheight == 0) $this->pdf->divheight = $numlines * 5;
    //Print content
	  $this->printbuffer($this->pdf->textbuffer);
    $this->pdf->textbuffer=array();
  	if ($tag=='P') $this->pdf->Ln($this->pdf->lineheight);
   }//end of 'if (!this->tablestart)'
   //Reset values
 	 $this->Reset();
	 $this->CSSbegin=false;
	 $this->pdf->backupcss=array();
  }
	if($tag=='TABLE') { // TABLE-END
    $this->pdf->blockjustfinished = true; //Eliminate exceeding left-side spaces
		$this->pdf->table['cells'] = $this->pdf->cell;
		$this->pdf->table['wc'] = array_pad(array(),$this->pdf->table['nc'],array('miw'=>0,'maw'=>0));
		$this->pdf->table['hr'] = array_pad(array(),$this->pdf->table['nr'],0);
		$this->_tableColumnWidth($this->pdf->table);
		$this->_tableWidth($this->pdf->table);
		$this->_tableHeight($this->pdf->table);

    //Output table on PDF
		$this->_tableWrite($this->pdf->table);

    //Reset values
    $this->pdf->tablestart=false; //bool
    $this->pdf->table=array(); //array
    $this->pdf->cell=array(); //array
    $this->pdf->col=-1; //int
    $this->pdf->row=-1; //int
    $this->Reset();
		$this->pdf->Ln(0.5*$this->pdf->lineheight);
	}
	if(($tag=='UL') or ($tag=='OL')) {
   if ($this->pdf->buffer_on == false) $this->pdf->listnum--;//Adjust minor BUG (this happens when there are two </OL> together)
	  if ($this->pdf->listlvl == 1) // We are closing the last OL/UL tag
	  {
       $this->pdf->blockjustfinished = true; //Eliminate exceeding left-side spaces
	     $this->pdf->buffer_on = false;
       if (!empty($this->pdf->textbuffer)) $this->pdf->listitem[] = array($this->pdf->listlvl,$this->pdf->listnum,$this->pdf->textbuffer,$this->pdf->listoccur[$this->pdf->listlvl]);
	     $this->pdf->textbuffer = array();
	     $this->pdf->listlvl--;
	     $this->printlistbuffer();
    }
    else // returning one level
    {
       if (!empty($this->pdf->textbuffer)) $this->pdf->listitem[] = array($this->pdf->listlvl,$this->pdf->listnum,$this->pdf->textbuffer,$this->pdf->listoccur[$this->pdf->listlvl]);
	     $this->pdf->textbuffer = array();
	     $occur = $this->pdf->listoccur[$this->pdf->listlvl];
       $this->pdf->listlist[$this->pdf->listlvl][$occur]['MAXNUM'] = $this->pdf->listnum; //save previous lvl's maxnum
	     $this->pdf->listlvl--;
	     $occur = $this->pdf->listoccur[$this->pdf->listlvl];
	     $this->pdf->listnum = $this->pdf->listlist[$this->pdf->listlvl][$occur]['MAXNUM']; // recover previous level's number
	     $this->pdf->listtype = $this->pdf->listlist[$this->pdf->listlvl][$occur]['TYPE']; // recover previous level's type
       $this->pdf->buffer_on = false;
    }
  }
 	if($tag=='H1' or $tag=='H2' or $tag=='H3' or $tag=='H4' or $tag=='H5' or $tag=='H6')
 	  {
      $this->pdf->blockjustfinished = true; //Eliminate exceeding left-side spaces
      if(!$this->pdf->pbegin and !$this->pdf->divbegin and !$this->pdf->tablestart)
      {
        //These 2 codelines are useless?
   	    $texto='';
        foreach($this->pdf->textbuffer as $vetor) $texto.=$vetor[0];
        //Save x,y coords ???
        $x = $this->pdf->x;
        $y = $this->pdf->y;
        //Set some default values
        $this->pdf->divwidth = $this->pdf->pgwidth;
        //Print content
    	  $this->printbuffer($this->pdf->textbuffer);
        $this->pdf->textbuffer=array();
  			if ($this->pdf->x != $this->pdf->lMargin) $this->pdf->Ln($this->pdf->lineheight);
      	//Reset values
      	$this->Reset();
      }
    $this->pdf->buffer_on=false;
    $this->pdf->lineheight = 5;
 		$this->pdf->Ln($this->pdf->lineheight);
    $this->pdf->SetFontSize(11);
 		$this->SetStyle('B',false);
  }
	if($tag=='TITLE')	{$this->pdf->titulo=false; $this->pdf->blockjustfinished = true;}
	if($tag=='FORM') $this->pdf->Ln($this->pdf->lineheight);
	if($tag=='PRE')
  {
      if(!$this->pdf->pbegin and !$this->pdf->divbegin and !$this->pdf->tablestart)
      {
        if ($this->pdf->divwidth == 0) $this->pdf->divwidth = $this->pdf->pgwidth;
        $content='';
        foreach($this->pdf->textbuffer as $aux) $content .= $aux[0];
        $numlines = $this->pdf->WordWrap($content,$this->pdf->divwidth);
        if ($this->pdf->divheight == 0) $this->pdf->divheight = $numlines * 5;
        //Print content
        $this->pdf->textbuffer[0][0] = ltrim($this->pdf->textbuffer[0][0]); //Remove exceeding left-side space
        $this->printbuffer($this->pdf->textbuffer);
        $this->pdf->textbuffer=array();
  			if ($this->pdf->x != $this->pdf->lMargin) $this->pdf->Ln($this->pdf->lineheight);
      	//Reset values
      	$this->Reset();
        $this->pdf->Ln(1.1*$this->pdf->lineheight);
      }
		  if($this->pdf->tablestart)
		  {
		    $this->pdf->cell[$this->pdf->row][$this->pdf->col]['textbuffer'][] = array("\n",$this->pdf->HREF,$this->pdf->currentstyle,$this->pdf->colorarray,$this->pdf->currentfont,$this->pdf->SUP,$this->pdf->SUB,''/*internal link*/,$this->pdf->strike,$this->pdf->outlineparam,$this->pdf->bgcolorarray);
      	$this->pdf->cell[$this->pdf->row][$this->pdf->col]['text'][] = "\n";
      }
			if($this->pdf->divbegin or $this->pdf->pbegin or $this->pdf->buffer_on)  $this->pdf->textbuffer[] = array("\n",$this->pdf->HREF,$this->pdf->currentstyle,$this->pdf->colorarray,$this->pdf->currentfont,$this->pdf->SUP,$this->pdf->SUB,''/*internal link*/,$this->pdf->strike,$this->pdf->outlineparam,$this->pdf->bgcolorarray);
      $this->CSSbegin=false;
	    $this->pdf->backupcss=array();
      $this->pdf->buffer_on = false;
      $this->pdf->blockjustfinished = true; //Eliminate exceeding left-side spaces
      $this->pdf->pjustfinished = true; //behaves the same way
  }
	if($tag=='CODE' or $tag=='PRE' or $tag=='TT' or $tag=='KBD' or $tag=='SAMP')
  {
  	 $this->pdf->currentfont='';
     $this->pdf->SetFont('arial');
	}
	if($tag=='B' or $tag=='I' or $tag=='U')
	{
	  $this->SetStyle($tag,false);
	  if ($this->CSSbegin and !$this->pdf->divbegin and !$this->pdf->pbegin and !$this->pdf->buffer_on)
	  {
      //Reset values
    	$this->Reset();
		  $this->CSSbegin=false;
  		$this->pdf->backupcss=array();
		}
	}
	if($tag=='TEXTAREA')
	{
	  if (!$this->pdf->tablestart) //not inside a table
	  {
  	  //Draw arrows too?
  	  $texto = '';
  	  foreach($this->pdf->textbuffer as $v) $texto .= $v[0];
    	$this->pdf->SetFillColor(235,235,235);
 			$this->pdf->SetFont('courier');
      $this->pdf->x +=3;
      $linesneeded = $this->pdf->WordWrap($texto,($this->pdf->col*2.2)+3);
      if ( $linesneeded > $this->pdf->row ) //Too many words inside textarea
      {
          $textoaux = explode("\n",$texto);
          $texto = '';
          for($i=0;$i < $this->pdf->row;$i++)
          {
               if ($i == $this->pdf->row-1) $texto .= $textoaux[$i];
               else $texto .= $textoaux[$i] . "\n";
          }
          //Inform the user that some text has been truncated
          $texto{strlen($texto)-1} = ".";
          $texto{strlen($texto)-2} = ".";
          $texto{strlen($texto)-3} = ".";
      }
      $backup_y = $this->pdf->y;
      $this->pdf->Rect($this->pdf->x,$this->pdf->y,(2.2*$this->pdf->col)+6,5*$this->pdf->row,'DF');
      if ($texto != '') $this->pdf->MultiCell((2.2*$this->pdf->col)+6,$this->pdf->lineheight,$texto);
      $this->pdf->y = $backup_y + $this->pdf->row*$this->pdf->lineheight;
 			$this->pdf->SetFont('arial');
    }
    else //inside a table
    {
 				$this->pdf->cell[$this->pdf->row][$this->pdf->col]['textbuffer'][] = $this->pdf->textbuffer[0];
				$this->pdf->cell[$this->pdf->row][$this->pdf->col]['text'][] = $this->pdf->textbuffer[0];
        $this->pdf->cell[$this->pdf->row][$this->pdf->col]['form'] = true; // in order to make some width adjustments later
       	$this->pdf->specialcontent = '';
    }
  	$this->pdf->SetFillColor(255);
    $this->pdf->textbuffer=array();
    $this->pdf->buffer_on = false;
  }
	if($tag=='SELECT')
	{
	  $texto = '';
	  $tamanho = 0;
    if (isset($this->pdf->selectoption['MAXWIDTH'])) $tamanho = $this->pdf->selectoption['MAXWIDTH'];
    if ($this->pdf->tablestart)
    {
        $texto = "»¤¬".$this->pdf->specialcontent."»¤¬".$this->pdf->selectoption['SELECTED'];
        $aux = explode("»¤¬",$texto);
        $texto = $aux[2];
        $texto = "»¤¬".$aux[1].",width=$tamanho,height=".($this->pdf->lineheight + 2)."»¤¬".$texto;
        $this->pdf->cell[$this->pdf->row][$this->pdf->col]['s'] += $tamanho + 7; // margin + arrow box
        $this->pdf->cell[$this->pdf->row][$this->pdf->col]['form'] = true; // in order to make some width adjustments later

        if (!isset($this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'])) $this->pdf->cell[$this->pdf->row][$this->pdf->col]['h'] = $this->pdf->lineheight + 2;
 				$this->pdf->cell[$this->pdf->row][$this->pdf->col]['textbuffer'][] = array($texto);
				$this->pdf->cell[$this->pdf->row][$this->pdf->col]['text'][] = '';

    }
    else //not inside a table
    {
      $texto = $this->pdf->selectoption['SELECTED'];
    	$this->pdf->SetFillColor(235,235,235);
      $this->pdf->x += 2;
      $this->pdf->Rect($this->pdf->x,$this->pdf->y,$tamanho+2,5,'DF');//+2 margin
      $this->pdf->x += 1;
      if ($texto != '') $this->pdf->Write(5,$texto,$this->pdf->x);
      $this->pdf->x += $tamanho - $this->pdf->GetStringWidth($texto) + 2;
  	  $this->pdf->SetFillColor(190,190,190);
      $this->pdf->Rect($this->pdf->x-1,$this->pdf->y,5,5,'DF'); //Arrow Box
  	  $this->pdf->SetFont('zapfdingbats');
      $this->pdf->Write(5,chr(116),$this->pdf->x); //Down arrow
  	  $this->pdf->SetFont('arial');
  	  $this->pdf->SetFillColor(255);
      $this->pdf->x += 1;
    }
    $this->pdf->selectoption = array();
   	$this->pdf->specialcontent = '';
    $this->pdf->textbuffer = array();
  }
	if($tag=='SUB' or $tag=='SUP')  //subscript or superscript
	{
	  if(!$this->pdf->pbegin and !$this->pdf->divbegin and !$this->pdf->tablestart and !$this->pdf->buffer_on and !$this->pdf->strike)
	  {
       //Deactivate $this->pdf->SUB/SUP for its info is already stored inside $this->pdf->textbuffer
       $this->pdf->SUB=false;
       $this->pdf->SUP=false;
       //Save x,y coords ???
       $x = $this->pdf->x;
       $y = $this->pdf->y;
       //Set some default values
       $this->pdf->divwidth = $this->pdf->pgwidth - $x + $this->pdf->lMargin;
       //Print content
       $this->printbuffer($this->pdf->textbuffer,true/*is out of a block (e.g. DIV,P etc.)*/);
       $this->pdf->textbuffer=array();
       //Reset values
       $this->Reset();
    }
	  $this->pdf->SUB=false;
	  $this->pdf->SUP=false;
	}
	if($tag=='S' or $tag=='STRIKE' or $tag=='DEL')
	{
    if(!$this->pdf->pbegin and !$this->pdf->divbegin and !$this->pdf->tablestart)
    {
      //Deactivate $this->pdf->strike for its info is already stored inside $this->pdf->textbuffer
      $this->pdf->strike=false;
      //Save x,y coords ???
      $x = $this->pdf->x;
      $y = $this->pdf->y;
      //Set some default values
      $this->pdf->divwidth = $this->pdf->pgwidth - $x + $this->pdf->lMargin;
      //Print content
  	  $this->printbuffer($this->pdf->textbuffer,true/*is out of a block (e.g. DIV,P etc.)*/);
      $this->pdf->textbuffer=array();
      //Reset values
    	$this->Reset();
    }
    $this->pdf->strike=false;
  }
	if($tag=='ADDRESS' or $tag=='CENTER') // <ADDRESS> or <CENTER> tag
	{
    $this->pdf->blockjustfinished = true; //Eliminate exceeding left-side spaces
    if(!$this->pdf->pbegin and !$this->pdf->divbegin and !$this->pdf->tablestart)
    {
      //Save x,y coords ???
      $x = $this->pdf->x;
      $y = $this->pdf->y;
      //Set some default values
      $this->pdf->divwidth = $this->pdf->pgwidth - $x + $this->pdf->lMargin;
      //Print content
  	  $this->printbuffer($this->pdf->textbuffer);
      $this->pdf->textbuffer=array();
    	//Reset values
    	$this->Reset();
    }
    $this->pdf->buffer_on=false;
	  if ($tag == 'ADDRESS') $this->SetStyle('I',false);
  }
  if($tag=='BIG')
  {
	  $newsize = $this->pdf->FontSizePt - 1;
	  $this->pdf->SetFontSize($newsize);
		$this->SetStyle('B',false);
  }
  if($tag=='SMALL')
  {
	  $newsize = $this->pdf->FontSizePt + 1;
	  $this->pdf->SetFontSize($newsize);
  }
	if($tag=='FONT')
  {
		if ($this->pdf->issetcolor == true)
    {
  	  $this->pdf->colorarray = array();
			$this->pdf->SetTextColor(0);
			$this->pdf->issetcolor = false;
		}
		if ($this->pdf->issetfont)
    {
			$this->pdf->SetFont('arial');
			$this->pdf->issetfont=false;
		}
		if ($this->CSSbegin)
		{
		  //Get some attributes back!
		  $this->setCSS($this->pdf->backupcss);
    }
	}
}

function printlistbuffer()
{
//! @return void
//! @desc Prints all list-related buffered info

    //Save x coordinate
    $x = $this->pdf->oldx;
    foreach($this->pdf->listitem as $item)
    {
        //Set default width & height values
        $this->pdf->divwidth = $this->pdf->pgwidth;
        $this->pdf->divheight = $this->pdf->lineheight;
        //Get list's buffered data
        $lvl = $item[0];
        $num = $item[1];
        $this->pdf->textbuffer = $item[2];
        $occur = $item[3];
        $type = $this->pdf->listlist[$lvl][$occur]['TYPE'];
        $maxnum = $this->pdf->listlist[$lvl][$occur]['MAXNUM'];
        switch($type) //Format type
        {
          case 'A':
              $num = dec2alpha($num,true);
              $maxnum = dec2alpha($maxnum,true);
              $type = str_pad($num,strlen($maxnum),' ',STR_PAD_LEFT) . ".";
              break;
          case 'a':
              $num = dec2alpha($num,false);
              $maxnum = dec2alpha($maxnum,false);
              $type = str_pad($num,strlen($maxnum),' ',STR_PAD_LEFT) . ".";
              break;
          case 'I':
              $num = dec2roman($num,true);
              $maxnum = dec2roman($maxnum,true);
              $type = str_pad($num,strlen($maxnum),' ',STR_PAD_LEFT) . ".";
              break;
          case 'i':
              $num = dec2roman($num,false);
              $maxnum = dec2roman($maxnum,false);
              $type = str_pad($num,strlen($maxnum),' ',STR_PAD_LEFT) . ".";
              break;
          case '1':
              $type = str_pad($num,strlen($maxnum),' ',STR_PAD_LEFT) . ".";
              break;
          case 'disc':
              $type = chr(149);
              break;
          case 'square':
              $type = chr(110); //black square on Zapfdingbats font
              break;
          case 'circle':
              $type = chr(186);
              break;
          default: break;
        }
        $this->pdf->x = (5*$lvl) + $x; //Indent list
        //Get bullet width including margins
        $oldsize = $this->pdf->FontSize * $this->pdf->k;
        if ($type == chr(110)) $this->pdf->SetFont('zapfdingbats','',5);
        $type .= ' ';
        $blt_width = $this->pdf->GetStringWidth($type)+$this->pdf->cMargin*2;
        //Output bullet
        $this->pdf->Cell($blt_width,5,$type,'','','L');
        $this->pdf->SetFont('arial','',$oldsize);
        $this->pdf->divwidth = $this->pdf->divwidth + $this->pdf->lMargin - $this->pdf->x;
        //Print content
  	    $this->printbuffer($this->pdf->textbuffer);
        $this->pdf->textbuffer=array();
    }
    //Reset all used values
    $this->pdf->listoccur = array();
    $this->pdf->listitem = array();
    $this->pdf->listlist = array();
    $this->pdf->listlvl = 0;
    $this->pdf->listnum = 0;
    $this->pdf->listtype = '';
    $this->pdf->textbuffer = array();
    $this->pdf->divwidth = 0;
    $this->pdf->divheight = 0;
    $this->pdf->oldx = -1;
    //At last, but not least, skip a line
    $this->pdf->Ln($this->pdf->lineheight);
}

function printbuffer($arrayaux,$outofblock=false,$is_table=false)
{
//! @return headache
//! @desc Prepares buffered text to be printed with FlowingBlock()

    //Save some previous parameters
    $save = array();
    $save['strike'] = $this->pdf->strike;
    $save['SUP'] = $this->pdf->SUP;
    $save['SUB'] = $this->pdf->SUB;
    $save['DOTTED'] = $this->pdf->dotted_on;
    $save['DASHED'] = $this->pdf->dash_on;
	  $this->pdf->SetDash(); //restore to no dash
	  $this->pdf->dash_on = false;
    $this->pdf->dotted_on = false;

    $bak_y = $this->pdf->y;
	  $bak_x = $this->pdf->x;
	  $align = $this->pdf->divalign;
	  $oldpage = $this->pdf->page;

	  //Overall object size == $old_height
	  //Line height == $this->pdf->divheight
	  $old_height = $this->pdf->divheight;
    if ($is_table)
    {
      $this->pdf->divheight = 1.1*$this->pdf->lineheight;
      $fill = 0;
    }
    else
    {
      $this->pdf->divheight = $this->pdf->lineheight;
      if ($this->pdf->FillColor == '1.000 g') $fill = 0; //avoid useless background painting (1.000 g == white background color)
      else $fill = 1;
    }

    $this->pdf->newFlowingBlock( $this->pdf->divwidth,$this->pdf->divheight,$this->pdf->divborder,$align,$fill,$is_table);

    $array_size = count($arrayaux);
    for($i=0;$i < $array_size; $i++)
    {
      $vetor = $arrayaux[$i];
      if ($i == 0 and $vetor[0] != "\n") $vetor[0] = ltrim($vetor[0]);
      if (empty($vetor[0]) and empty($vetor[7])) continue; //Ignore empty text and not carrying an internal link
      //Activating buffer properties
      if(isset($vetor[10]) and !empty($vetor[10])) //Background color
      {
          $cor = $vetor[10];
				  $this->pdf->SetFillColor($cor['R'],$cor['G'],$cor['B']);
				  $this->pdf->divbgcolor = true;
      }
      if(isset($vetor[9]) and !empty($vetor[9])) // Outline parameters
      {
          $cor = $vetor[9]['COLOR'];
          $outlinewidth = $vetor[9]['WIDTH'];
          $this->pdf->SetTextOutline($outlinewidth,$cor['R'],$cor['G'],$cor['B']);
          $this->pdf->outline_on = true;
      }
      if(isset($vetor[8]) and $vetor[8] === true) // strike-through the text
      {
          $this->pdf->strike = true;
      }
      if(isset($vetor[7]) and $vetor[7] != '') // internal link: <a name="anyvalue">
      {
        $this->pdf->internallink[$vetor[7]] = array("Y"=>$this->pdf->y,"PAGE"=>$this->pdf->page );
      //  $this->pdf->Bookmark($vetor[7]." (pg. $this->pdf->page)",0,$this->pdf->y);
        if (empty($vetor[0])) continue; //Ignore empty text
      }
      if(isset($vetor[6]) and $vetor[6] === true) // Subscript
      {
  		   $this->pdf->SUB = true;
         $this->pdf->SetFontSize(6);
      }
      if(isset($vetor[5]) and $vetor[5] === true) // Superscript
      {
         $this->pdf->SUP = true;
         $this->pdf->SetFontSize(6);
      }
      if(isset($vetor[4]) and $vetor[4] != '') $this->pdf->SetFont($vetor[4]); // Font Family
      if (!empty($vetor[3])) //Font Color
      {
        $cor = $vetor[3];
			  $this->pdf->SetTextColor($cor['R'],$cor['G'],$cor['B']);
      }
      if(isset($vetor[2]) and $vetor[2] != '') //Bold,Italic,Underline styles
      {
          if (strpos($vetor[2],"B") !== false) $this->SetStyle('B',true);
          if (strpos($vetor[2],"I") !== false) $this->SetStyle('I',true);
          if (strpos($vetor[2],"U") !== false) $this->SetStyle('U',true);
      }
      if(isset($vetor[1]) and $vetor[1] != '') //LINK
      {
        if (strpos($vetor[1],".") === false) //assuming every external link has a dot indicating extension (e.g: .html .txt .zip www.somewhere.com etc.)
        {
          //Repeated reference to same anchor?
          while(array_key_exists($vetor[1],$this->pdf->internallink)) $vetor[1]="#".$vetor[1];
          $this->pdf->internallink[$vetor[1]] = $this->pdf->AddLink();
          $vetor[1] = $this->pdf->internallink[$vetor[1]];
        }
        $this->pdf->HREF = $vetor[1];
      	$this->pdf->SetTextColor(0,0,255);
      	$this->SetStyle('U',true);
      }
      //Print-out special content
      if (isset($vetor[0]) and $vetor[0]{0} == '»' and $vetor[0]{1} == '¤' and $vetor[0]{2} == '¬') //identifier has been identified!
      {
        $content = explode("»¤¬",$vetor[0]);
        $texto = $content[2];
        $content = explode(",",$content[1]);
        foreach($content as $value)
        {
          $value = explode("=",$value);
          $specialcontent[$value[0]] = $value[1];
        }
        if ($this->pdf->flowingBlockAttr[ 'contentWidth' ] > 0) // Print out previously accumulated content
        {
            $width_used = $this->pdf->flowingBlockAttr[ 'contentWidth' ] / $this->pdf->k;
            //Restart Flowing Block
            $this->pdf->finishFlowingBlock($outofblock);
            $this->pdf->x = $bak_x + ($width_used % $this->pdf->divwidth) + 0.5;// 0.5 == margin
            $this->pdf->y -= ($this->pdf->lineheight + 0.5);
            $extrawidth = 0; //only to be used in case $specialcontent['width'] does not contain all used width (e.g. Select Box)
            if ($specialcontent['type'] == 'select') $extrawidth = 7; //arrow box + margin
            if(($this->pdf->x - $bak_x) + $specialcontent['width'] + $extrawidth > $this->pdf->divwidth )
            {
              $this->pdf->x = $bak_x;
              $this->pdf->y += $this->pdf->lineheight - 1;
            }
            $this->pdf->newFlowingBlock( $this->pdf->divwidth,$this->pdf->divheight,$this->pdf->divborder,$align,$fill,$is_table );
        }
        switch(strtoupper($specialcontent['type']))
        {
          case 'IMAGE':
                      //xpos and ypos used in order to support: <div align='center'><img ...></div>
                      $xpos = 0;
                      $ypos = 0;
                      if (isset($specialcontent['ypos']) and $specialcontent['ypos'] != '') $ypos = (float)$specialcontent['ypos'];
                      if (isset($specialcontent['xpos']) and $specialcontent['xpos'] != '') $xpos = (float)$specialcontent['xpos'];
                      $width_used = (($this->pdf->x - $bak_x) + $specialcontent['width'])*$this->pdf->k; //in order to adjust x coordinate later
                      //Is this the best way of fixing x,y coordinates?
                      $fix_x = ($this->pdf->x+2) * $this->pdf->k + ($xpos*$this->pdf->k); //+2 margin
                      $fix_y = ($this->pdf->h - (($this->pdf->y+2) + $specialcontent['height'])) * $this->pdf->k;//+2 margin
                      $imgtemp = explode(" ",$texto);
                      $imgtemp[5]=$fix_x; // x
                      $imgtemp[6]=$fix_y; // y
                      $texto = implode(" ",$imgtemp);
                      $this->pdf->_out($texto);
                      //Readjust x coordinate in order to allow text to be placed after this form element
                      $this->pdf->x = $bak_x;
                      $spacesize = $this->pdf->CurrentFont[ 'cw' ][ ' ' ] * ( $this->pdf->FontSizePt / 1000 );
                      $spacenum = (integer)ceil(($width_used / $spacesize));
                      //Consider the space used so far in this line as a bunch of spaces
                      if ($ypos != 0) $this->pdf->Ln($ypos);
                      else $this->pdf->WriteFlowingBlock(str_repeat(' ',$spacenum));
                      break;
          case 'INPUT':
                      switch($specialcontent['subtype'])
                      {
                              case 'PASSWORD':
                              case 'TEXT': //Draw TextField
                                          $width_used = (($this->pdf->x - $bak_x) + $specialcontent['width'])*$this->pdf->k; //in order to adjust x coordinate later
                                   		    $this->pdf->SetFillColor(235,235,235);
                                          $this->pdf->x += 1;
                                          $this->pdf->y += 1;
                                          $this->pdf->Rect($this->pdf->x,$this->pdf->y,$specialcontent['width'],4.5,'DF');// 4.5 in order to avoid overlapping
                                          if ($texto != '')
                                          {
                                               $this->pdf->x += 1;
                                               $this->pdf->Write(5,$texto,$this->pdf->x);
                                               $this->pdf->x -= $this->pdf->GetStringWidth($texto);
                                          }
                                          $this->pdf->SetFillColor(255);
                                          $this->pdf->y -= 1;
                                          //Readjust x coordinate in order to allow text to be placed after this form element
                                          $this->pdf->x = $bak_x;
                                          $spacesize = $this->pdf->CurrentFont[ 'cw' ][ ' ' ] * ( $this->pdf->FontSizePt / 1000 );
                                          $spacenum = (integer)ceil(($width_used / $spacesize));
                                          //Consider the space used so far in this line as a bunch of spaces
                                          $this->pdf->WriteFlowingBlock(str_repeat(' ',$spacenum));
                                          break;
                              case 'CHECKBOX': //Draw Checkbox
                                          $width_used = (($this->pdf->x - $bak_x) + $specialcontent['width'])*$this->pdf->k; //in order to adjust x coordinate later
                                          $checked = $texto;
                                          $this->pdf->SetFillColor(235,235,235);
                                          $this->pdf->y += 1;
                                          $this->pdf->x += 1;
                                          $this->pdf->Rect($this->pdf->x,$this->pdf->y,3,3,'DF');
                                          if ($checked)
                                          {
                                             $this->pdf->Line($this->pdf->x,$this->pdf->y,$this->pdf->x+3,$this->pdf->y+3);
                                             $this->pdf->Line($this->pdf->x,$this->pdf->y+3,$this->pdf->x+3,$this->pdf->y);
                                          }
                                          $this->pdf->SetFillColor(255);
                                          $this->pdf->y -= 1;
                                          //Readjust x coordinate in order to allow text to be placed after this form element
                                          $this->pdf->x = $bak_x;
                                          $spacesize = $this->pdf->CurrentFont[ 'cw' ][ ' ' ] * ( $this->pdf->FontSizePt / 1000 );
                                          $spacenum = (integer)ceil(($width_used / $spacesize));
                                          //Consider the space used so far in this line as a bunch of spaces
                                          $this->pdf->WriteFlowingBlock(str_repeat(' ',$spacenum));
                                          break;
                              case 'RADIO': //Draw Radio button
                                          $width_used = (($this->pdf->x - $bak_x) + $specialcontent['width']+0.5)*$this->pdf->k; //in order to adjust x coordinate later
                                          $checked = $texto;
                                          $this->pdf->x += 2;
                                          $this->pdf->y += 1.5;
                                          $this->pdf->Circle($this->pdf->x,$this->pdf->y+1.2,1,'D');
                                          $this->pdf->_out('0.000 g');
                                          if ($checked) $this->pdf->Circle($this->pdf->x,$this->pdf->y+1.2,0.4,'DF');
                                          $this->pdf->y -= 1.5;
                                          //Readjust x coordinate in order to allow text to be placed after this form element
                                          $this->pdf->x = $bak_x;
                                          $spacesize = $this->pdf->CurrentFont[ 'cw' ][ ' ' ] * ( $this->pdf->FontSizePt / 1000 );
                                          $spacenum = (integer)ceil(($width_used / $spacesize));
                                          //Consider the space used so far in this line as a bunch of spaces
                                          $this->pdf->WriteFlowingBlock(str_repeat(' ',$spacenum));
                                          break;
                              case 'BUTTON': // Draw a button
                              case 'SUBMIT':
                              case 'RESET':
                                          $nihil = ($specialcontent['width']-$this->pdf->GetStringWidth($texto))/2;
                                          $this->pdf->x += 1.5;
                                          $this->pdf->y += 1;
                              			      $this->pdf->SetFillColor(190,190,190);
                                          $this->pdf->Rect($this->pdf->x,$this->pdf->y,$specialcontent['width'],4.5,'DF'); // 4.5 in order to avoid overlapping
                                          $this->pdf->x += $nihil;
                                          $this->pdf->Write(5,$texto,$this->pdf->x);
                                          $this->pdf->x += $nihil;
                                          $this->pdf->SetFillColor(255);
                                          $this->pdf->y -= 1;
                                          break;
                              default: break;
                      }
                      break;
          case 'SELECT':
                      $width_used = (($this->pdf->x - $bak_x) + $specialcontent['width'] + 8)*$this->pdf->k; //in order to adjust x coordinate later
                      $this->pdf->SetFillColor(235,235,235); //light gray
                      $this->pdf->x += 1.5;
                      $this->pdf->y += 1;
                      $this->pdf->Rect($this->pdf->x,$this->pdf->y,$specialcontent['width']+2,$this->pdf->lineheight,'DF'); // +2 == margin
                      $this->pdf->x += 1;
                      if ($texto != '') $this->pdf->Write($this->pdf->lineheight,$texto,$this->pdf->x); //the combobox content
                      $this->pdf->x += $specialcontent['width'] - $this->pdf->GetStringWidth($texto) + 2;
  	                  $this->pdf->SetFillColor(190,190,190); //dark gray
                      $this->pdf->Rect($this->pdf->x-1,$this->pdf->y,5,5,'DF'); //Arrow Box
                  	  $this->pdf->SetFont('zapfdingbats');
                      $this->pdf->Write($this->pdf->lineheight,chr(116),$this->pdf->x); //Down arrow
  	                  $this->pdf->SetFont('arial');
  	                  $this->pdf->SetFillColor(255);
                      //Readjust x coordinate in order to allow text to be placed after this form element
                      $this->pdf->x = $bak_x;
                      $spacesize = $this->pdf->CurrentFont[ 'cw' ][ ' ' ] * ( $this->pdf->FontSizePt / 1000 );
                      $spacenum = (integer)ceil(($width_used / $spacesize));
                      //Consider the space used so far in this line as a bunch of spaces
                      $this->pdf->WriteFlowingBlock(str_repeat(' ',$spacenum));
                      break;
          case 'TEXTAREA':
                      //Setup TextArea properties
                      $this->pdf->SetFillColor(235,235,235);
                			$this->pdf->SetFont('courier');
  		                $this->pdf->currentfont='courier';
                      $ta_lines = $specialcontent['lines'];
                      $ta_height = 1.1*$this->pdf->lineheight*$ta_lines;
                      $ta_width = $specialcontent['width'];
                      //Adjust x,y coordinates
                      $this->pdf->x += 1.5;
                      $this->pdf->y += 1.5;
                      $linesneeded = $this->pdf->WordWrap($texto,$ta_width);
                      if ( $linesneeded > $ta_lines ) //Too many words inside textarea
                      {
                        $textoaux = explode("\n",$texto);
                        $texto = '';
                        for($i=0;$i<$ta_lines;$i++)
                        {
                          if ($i == $ta_lines-1) $texto .= $textoaux[$i];
                          else $texto .= $textoaux[$i] . "\n";
                        }
                        //Inform the user that some text has been truncated
                        $texto{strlen($texto)-1} = ".";
                        $texto{strlen($texto)-2} = ".";
                        $texto{strlen($texto)-3} = ".";
                      }
                      $backup_y = $this->pdf->y;
                      $backup_x = $this->pdf->x;
                      $this->pdf->Rect($this->pdf->x,$this->pdf->y,$ta_width+3,$ta_height,'DF');
                      if ($texto != '') $this->pdf->MultiCell($ta_width+3,$this->pdf->lineheight,$texto);
                      $this->pdf->y = $backup_y - 1.5;
                      $this->pdf->x = $backup_x + $ta_width + 2.5;
    	                $this->pdf->SetFillColor(255);
			                $this->pdf->SetFont('arial');
                  		$this->pdf->currentfont='';
                      break;
          default: break;
        }
      }
      else //THE text
      {
        if ($vetor[0] == "\n") //We are reading a <BR> now turned into newline ("\n")
        {
            //Restart Flowing Block
            $this->pdf->finishFlowingBlock($outofblock);
            if($outofblock) $this->pdf->Ln($this->pdf->lineheight);
            $this->pdf->x = $bak_x;
            $this->pdf->newFlowingBlock( $this->pdf->divwidth,$this->pdf->divheight,$this->pdf->divborder,$align,$fill,$is_table );
        }
        else $this->pdf->WriteFlowingBlock( $vetor[0] , $outofblock );
      }
      //Check if it is the last element. If so then finish printing the block
      if ($i == ($array_size-1)) $this->pdf->finishFlowingBlock($outofblock);
      //Now we must deactivate what we have used
      if( (isset($vetor[1]) and $vetor[1] != '') or $this->pdf->HREF != '')
      {
      	$this->pdf->SetTextColor(0);
      	$this->SetStyle('U',false);
        $this->pdf->HREF = '';
      }
      if(isset($vetor[2]) and $vetor[2] != '')
      {
        $this->SetStyle('B',false);
        $this->SetStyle('I',false);
        $this->SetStyle('U',false);
      }
      if(isset($vetor[3]) and $vetor[3] != '')
      {
        unset($cor);
  			$this->pdf->SetTextColor(0);
      }
      if(isset($vetor[4]) and $vetor[4] != '') $this->pdf->SetFont('arial');
      if(isset($vetor[5]) and $vetor[5] === true)
      {
        $this->pdf->SUP = false;
        $this->pdf->SetFontSize(11);
      }
      if(isset($vetor[6]) and $vetor[6] === true)
      {
        $this->pdf->SUB = false;
        $this->pdf->SetFontSize(11);
      }
      //vetor7-internal links
      if(isset($vetor[8]) and $vetor[8] === true) // strike-through the text
      {
        $this->pdf->strike = false;
      }
      if(isset($vetor[9]) and !empty($vetor[9])) // Outline parameters
      {
          $this->pdf->SetTextOutline(false);
          $this->pdf->outline_on = false;
      }
      if(isset($vetor[10]) and !empty($vetor[10])) //Background color
      {
				  $this->pdf->SetFillColor(255);
				  $this->pdf->divbgcolor = false;
      }
    }//end of for(i=0;i<arraysize;i++)

    //Restore some previously set parameters
    $this->pdf->strike = $save['strike'];
    $this->pdf->SUP = $save['SUP'];
    $this->pdf->SUB = $save['SUB'];
    $this->pdf->dotted_on = $save['DOTTED'];
    $this->pdf->dash_on = $save['DASHED'];
	  if ($this->pdf->dash_on) $this->pdf->SetDash(2,2);
    //Check whether we have borders to paint or not
    //(only works 100% if whole content spans only 1 page)
    if ($this->CSSbegin and ($this->pdf->divborder or $this->pdf->dash_on or $this->pdf->dotted_on or $this->pdf->divbgcolor))
    {
        if ($oldpage != $this->pdf->page)
        {
           //Only border on last page is painted (known bug)
           $x = $this->pdf->lMargin;
           $y = $this->pdf->tMargin;
           $old_height = $this->pdf->y - $y;
        }
        else
        {
           if ($this->pdf->oldx < 0) $x  = $this->pdf->x;
           else $x = $this->pdf->oldx;
           if ($this->pdf->oldy < 0) $y  = $this->pdf->y - $old_height;
           else $y = $this->pdf->oldy;
        }
        if ($this->pdf->divborder) $this->pdf->Rect($x,$y,$this->pdf->divwidth,$old_height);
        if ($this->pdf->dash_on) $this->pdf->Rect($x,$y,$this->pdf->divwidth,$old_height);
		    if ($this->pdf->dotted_on) $this->pdf->DottedRect($x,$y,$this->pdf->divwidth,$old_height);
        $this->pdf->x = $bak_x;
    }
}

function Reset()
{
//! @return void
//! @desc Resets several class attributes

//	if ( $this->pdf->issetcolor !== true )
//  {
		$this->pdf->SetTextColor(0);
		$this->pdf->SetDrawColor(0);
		$this->pdf->SetFillColor(255);
	  $this->pdf->colorarray = array();
	  $this->pdf->bgcolorarray = array();
$this->pdf->issetcolor = false;
//	}
$this->pdf->HREF = '';
$this->pdf->SetTextOutline(false);

//$this->pdf->strike = false;

  $this->pdf->SetFontSize(11);
	$this->SetStyle('B',false);
	$this->SetStyle('I',false);
	$this->SetStyle('U',false);
	$this->pdf->SetFont('arial');
	$this->pdf->divwidth = 0;
	$this->pdf->divheight = 0;
	$this->pdf->divalign = "L";
  $this->pdf->divrevert = false;
	$this->pdf->divborder = 0;
	$this->pdf->divbgcolor = false;
  $this->pdf->toupper = false;
  $this->pdf->tolower = false;
	$this->pdf->SetDash(); //restore to no dash
	$this->pdf->dash_on = false;
  $this->pdf->dotted_on = false;
  $this->pdf->oldx = -1;
  $this->pdf->oldy = -1;
}

function ReadMetaTags($html)
{
//! @return void
//! @desc Pass meta tag info to PDF file properties
	$regexp = '/ (\\w+?)=([^\\s>"]+)/si'; // changes anykey=anyvalue to anykey="anyvalue" (only do this when this happens inside tags)
 	$html = preg_replace($regexp," \$1=\"\$2\"",$html);
  $regexp = '/<meta .*?(name|content)="(.*?)" .*?(name|content)="(.*?)".*?>/si';
  preg_match_all($regexp,$html,$aux);

  $firstattr = $aux[1];
  $secondattr = $aux[3];
  for( $i = 0 ; $i < count($aux[0]) ; $i++)
  {

     $name = ( strtoupper($firstattr[$i]) == "NAME" )? strtoupper($aux[2][$i]) : strtoupper($aux[4][$i]);
     $content = ( strtoupper($firstattr[$i]) == "CONTENT" )? $aux[2][$i] : $aux[4][$i];
     switch($name)
     {
       case "KEYWORDS": $this->pdf->SetKeywords($content); break;
       case "AUTHOR": $this->pdf->SetAuthor($content); break;
       case "DESCRIPTION": $this->pdf->SetSubject($content); break;
     }
  }
  //Comercial do Aplicativo usado (no caso um script):
  $this->pdf->SetCreator("HTML2FPDF >> http://html2fpdf.sf.net");
}

//////////////////
/// CSS parser ///
//////////////////
function ReadCSS($html)
{
//! @desc CSS parser
//! @return string

/*
* This version ONLY supports:  .class {...} / #id { .... }
* It does NOT support: body{...} / a#hover { ... } / p.right { ... } / other mixed names
* This function must read the CSS code (internal or external) and order its value inside $this->CSS.
*/

	$match = 0; // no match for instance
	$regexp = ''; // This helps debugging: showing what is the REAL string being processed

	//CSS inside external files
	$regexp = '/<link rel="stylesheet".*?href="(.+?)"\\s*?\/?>/si';
	$match = preg_match_all($regexp,$html,$CSSext);
  $ind = 0;

	while($match){
    //Fix path value
    $path = $CSSext[1][$ind];
    $path = str_replace("\\","/",$path); //If on Windows
    //Get link info and obtain its absolute path
    $regexp = '|^./|';
    $path = preg_replace($regexp,'',$path);
    if (strpos($path,"../") !== false ) //It is a Relative Link
    {
       $backtrackamount = substr_count($path,"../");
       $maxbacktrack = substr_count($this->pdf->basepath,"/") - 1;
       $filepath = str_replace("../",'',$path);
       $path = $this->pdf->basepath;
       //If it is an invalid relative link, then make it go to directory root
       if ($backtrackamount > $maxbacktrack) $backtrackamount = $maxbacktrack;
       //Backtrack some directories
       for( $i = 0 ; $i < $backtrackamount + 1 ; $i++ ) $path = substr( $path, 0 , strrpos($path,"/") );
       $path = $path . "/" . $filepath; //Make it an absolute path
    }
    elseif( strpos($path,":/") === false) //It is a Local Link
    {
        $path = $this->pdf->basepath . $path;
    }
    //Do nothing if it is an Absolute Link
    //END of fix path value
    $CSSextblock = file_get_contents($path);

    //Get class/id name and its characteristics from $CSSblock[1]
	  $regexp = '/[.# ]([^.]+?)\\s*?\{(.+?)\}/s'; // '/s' PCRE_DOTALL including \n
	  preg_match_all( $regexp, $CSSextblock, $extstyle);

	  //Make CSS[Name-of-the-class] = array(key => value)
	  $regexp = '/\\s*?(\\S+?):(.+?);/si';

	  for($i=0; $i < count($extstyle[1]) ; $i++)
	  {
  		preg_match_all( $regexp, $extstyle[2][$i], $extstyleinfo);
  		$extproperties = $extstyleinfo[1];
  		$extvalues = $extstyleinfo[2];
  		for($j = 0; $j < count($extproperties) ; $j++)
  		{
  			//Array-properties and Array-values must have the SAME SIZE!
  			$extclassproperties[strtoupper($extproperties[$j])] = trim($extvalues[$j]);
  		}
  		$this->CSS[$extstyle[1][$i]] = $extclassproperties;
	  	$extproperties = array();
  		$extvalues = array();
  		$extclassproperties = array();
   	}
	  $match--;
	  $ind++;
	} //end of match

	$match = 0; // reset value, if needed

	//CSS internal
	//Get content between tags and order it, using regexp
	$regexp = '/<style.*?>(.*?)<\/style>/si'; // it can be <style> or <style type="txt/css">
	$match = preg_match($regexp,$html,$CSSblock);

	if ($match) {
  	//Get class/id name and its characteristics from $CSSblock[1]
  	$regexp = '/[.#]([^.]+?)\\s*?\{(.+?)\}/s'; // '/s' PCRE_DOTALL including \n
  	preg_match_all( $regexp, $CSSblock[1], $style);

	  //Make CSS[Name-of-the-class] = array(key => value)
	  $regexp = '/\\s*?(\\S+?):(.+?);/si';

	  for($i=0; $i < count($style[1]) ; $i++)
	  {
  		preg_match_all( $regexp, $style[2][$i], $styleinfo);
  		$properties = $styleinfo[1];
  		$values = $styleinfo[2];
  		for($j = 0; $j < count($properties) ; $j++)
  		{
  			//Array-properties and Array-values must have the SAME SIZE!
  			$classproperties[strtoupper($properties[$j])] = trim($values[$j]);
  		}
  		$this->CSS[$style[1][$i]] = $classproperties;
  		$properties = array();
  		$values = array();
  		$classproperties = array();
  	}
	} // end of match

	//Remove CSS (tags and content), if any
	$regexp = '/<style.*?>(.*?)<\/style>/si'; // it can be <style> or <style type="txt/css">
	$html = preg_replace($regexp,'',$html);

 	return $html;
}

function readInlineCSS($html)
{
//! @return array
//! @desc Reads inline CSS and returns an array of properties

  //Fix incomplete CSS code
  $size = strlen($html)-1;
  if ($html{$size} != ';') $html .= ';';
  //Make CSS[Name-of-the-class] = array(key => value)
  $regexp = '|\\s*?(\\S+?):(.+?);|i';
	preg_match_all( $regexp, $html, $styleinfo);
	$properties = $styleinfo[1];
	$values = $styleinfo[2];
	//Array-properties and Array-values must have the SAME SIZE!
	$classproperties = array();
	for($i = 0; $i < count($properties) ; $i++) $classproperties[strtoupper($properties[$i])] = trim($values[$i]);

  return $classproperties;
}

function setCSS($arrayaux)
{
//! @return void
//! @desc Change some class attributes according to CSS properties
  if (!is_array($arrayaux)) return; //Removes PHP Warning
	foreach($arrayaux as $k => $v)
  {
  	switch($k){
   			case 'WIDTH':
		   			$this->pdf->divwidth = ConvertSize($v,$this->pdf->pgwidth);
		  			break;
	  		case 'HEIGHT':
		   			$this->pdf->divheight = ConvertSize($v,$this->pdf->pgwidth);
		  			break;
	  		case 'BORDER': // width style color (width not supported correctly - it is always considered as normal)
		  			$prop = explode(' ',$v);
		  			if ( count($prop) != 3 ) break; // Not supported: borders not fully declared
            //style: dashed dotted none (anything else => solid )
		  			if (strnatcasecmp($prop[1],"dashed") == 0) //found "dashed"! (ignores case)
            {
               $this->pdf->dash_on = true;
               $this->pdf->SetDash(2,2); //2mm on, 2mm off
            }
		  			elseif (strnatcasecmp($prop[1],"dotted") == 0) //found "dotted"! (ignores case)
            {
               $this->pdf->dotted_on = true;
            }
			 		  elseif (strnatcasecmp($prop[1],"none") == 0) $this->pdf->divborder = 0;
					  else $this->pdf->divborder = 1;
					  //color
		  			$coul = ConvertColor($prop[2]);
		  			$this->pdf->SetDrawColor($coul['R'],$coul['G'],$coul['B']);
		  			$this->pdf->issetcolor=true;
					  break;
 			  case 'FONT-FAMILY': // one of the $this->fontlist fonts
            //If it is a font list, get all font types
            $aux_fontlist = explode(",",$v);
            $fontarraysize = count($aux_fontlist);
            for($i=0;$i<$fontarraysize;$i++)
            {
               $fonttype = $aux_fontlist[$i];
               $fonttype = trim($fonttype);
               //If font is found, set it, and exit loop
               if ( in_array(strtolower($fonttype), $this->fontlist) ) {$this->pdf->SetFont(strtolower($fonttype));break;}
               //If font = "courier new" for example, try simply looking for "courier"
               $fonttype = explode(" ",$fonttype);
               $fonttype = $fonttype[0];
               if ( in_array(strtolower($fonttype), $this->fontlist) ) {$this->pdf->SetFont(strtolower($fonttype));break;}
            }
					  break;
			  case 'FONT-SIZE': //Does not support: smaller, larger
			      if(is_numeric($v{0}))
			      {
			         $mmsize = ConvertSize($v,$this->pdf->pgwidth);
			         $this->pdf->SetFontSize( $mmsize*(72/25.4) ); //Get size in points (pt)
            }
			      else{
  			      $v = strtoupper($v);
  			      switch($v)
  			      {
  			         //Values obtained from http://www.w3schools.com/html/html_reference.asp
  			         case 'XX-SMALL': $this->pdf->SetFontSize( (0.7)* 11);
  			             break;
                 case 'X-SMALL': $this->pdf->SetFontSize( (0.77) * 11);
			               break;
			           case 'SMALL': $this->pdf->SetFontSize( (0.86)* 11);
  			             break;
  			         case 'MEDIUM': $this->pdf->SetFontSize(11);
  			             break;
  			         case 'LARGE': $this->pdf->SetFontSize( (1.2)*11);
  			             break;
  			         case 'X-LARGE': $this->pdf->SetFontSize( (1.5)*11);
  			             break;
  			         case 'XX-LARGE': $this->pdf->SetFontSize( 2*11);
			               break;
              }
            }
			   	  break;
				case 'FONT-STYLE': // italic normal oblique
				    switch (strtoupper($v))
				    {
				      case 'ITALIC':
				      case 'OBLIQUE':
            		  	    $this->SetStyle('I',true);
                        break;
				      case 'NORMAL': break;
				    }
					  break;
				case 'FONT-WEIGHT': // normal bold //Does not support: bolder, lighter, 100..900(step value=100)
				    switch (strtoupper($v))
				    {
				      case 'BOLD':
            		  	    $this->SetStyle('B',true);
                        break;
				      case 'NORMAL': break;
				    }
					  break;
				case 'TEXT-DECORATION': // none underline //Does not support: overline, blink
				    switch (strtoupper($v))
				    {
				      case 'LINE-THROUGH':
                        $this->pdf->strike = true;
				                break;
				      case 'UNDERLINE':
            		  	    $this->SetStyle('U',true);
				                break;
				      case 'NONE': break;
				    }
				case 'TEXT-TRANSFORM': // none uppercase lowercase //Does not support: capitalize
				    switch (strtoupper($v)) //Not working 100%
				    {
				      case 'UPPERCASE':
				                $this->pdf->toupper=true;
				                break;
				      case 'LOWERCASE':
 				                $this->pdf->tolower=true;
				                break;
				      case 'NONE': break;
				    }
				case 'TEXT-ALIGN': //left right center justify
				    switch (strtoupper($v))
				    {
				      case 'LEFT':
                        $this->pdf->divalign="L";
                        break;
				      case 'CENTER':
                        $this->pdf->divalign="C";
                        break;
				      case 'RIGHT':
                        $this->pdf->divalign="R";
                        break;
				      case 'JUSTIFY':
                        $this->pdf->divalign="J";
                        break;
				    }
					  break;
				case 'DIRECTION': //ltr(default) rtl
				    if (strtolower($v) == 'rtl') $this->pdf->divrevert = true;
					  break;
				case 'BACKGROUND': // bgcolor only
					  $cor = ConvertColor($v);
					  $this->pdf->bgcolorarray = $cor;
					  $this->pdf->SetFillColor($cor['R'],$cor['G'],$cor['B']);
					  $this->pdf->divbgcolor = true;
					  break;
				case 'COLOR': // font color
					  $cor = ConvertColor($v);
					  $this->pdf->colorarray = $cor;
					  $this->pdf->SetTextColor($cor['R'],$cor['G'],$cor['B']);
					  $this->pdf->issetcolor=true;
					  break;
		}//end of switch($k)
   }//end of foreach
}

function SetStyle($tag,$enable)
{
//! @return void
//! @desc Enables/Disables B,I,U styles
	//Modify style and select corresponding font
	$this->pdf->$tag+=($enable ? 1 : -1);
	$style='';
  //Fix some SetStyle misuse
	if ($this->pdf->$tag < 0) $this->pdf->$tag = 0;
	if ($this->pdf->$tag > 1) $this->pdf->$tag = 1;
	foreach(array('B','I','U') as $s)
		if($this->pdf->$s>0)
			$style.=$s;

	$this->pdf->currentstyle=$style;
	$this->pdf->SetFont('',$style);
}

function DisableTags($str='')
{
//! @return void
//! @desc Disable some tags using ',' as separator. Enable all tags calling this function without parameters.
  if ($str == '') //enable all tags
  {
    //Insert new supported tags in the long string below.
    $this->enabledtags = "<tt><kbd><samp><option><outline><span><newpage><page_break><s><strike><del><bdo><big><small><address><ins><cite><font><center><sup><sub><input><select><option><textarea><title><form><ol><ul><li><h1><h2><h3><h4><h5><h6><pre><b><u><i><a><img><p><br><strong><em><code><th><tr><blockquote><hr><td><tr><table><div>";
  }
  else
  {
    $str = explode(",",$str);
    foreach($str as $v) $this->enabledtags = str_replace(trim($v),'',$this->enabledtags);
  }
}

////////////////////////TABLE CODE (from PDFTable)/////////////////////////////////////
//Thanks to vietcom (vncommando at yahoo dot com)
/*     Modified by Renato Coelho
   in order to print tables that span more than 1 page and to allow
   bold,italic and the likes inside table cells (and alignment now works with styles!)
*/

//table		Array of (w, h, bc, nr, wc, hr, cells)
//w			Width of table
//h			Height of table
//nc		Number column
//nr		Number row
//hr		List of height of each row
//wc		List of width of each column
//cells		List of cells of each rows, cells[i][j] is a cell in the table
function _tableColumnWidth(&$table){
//! @return void
	$cs = &$table['cells'];
	$mw = $this->pdf->getStringWidth('W');
	$nc = $table['nc'];
	$nr = $table['nr'];
	$listspan = array();
	//Xac dinh do rong cua cac cell va cac cot tuong ung
	for($j = 0 ; $j < $nc ; $j++ ) //columns
  {
		$wc = &$table['wc'][$j];
		for($i = 0 ; $i < $nr ; $i++ ) //rows
    {
			if (isset($cs[$i][$j]) && $cs[$i][$j])
      {
				$c = &$cs[$i][$j];
				$miw = $mw;
				if (isset($c['maxs']) and $c['maxs'] != '') $c['s'] = $c['maxs'];
				$c['maw']	= $c['s'];
				if (isset($c['nowrap'])) $miw = $c['maw'];
				if (isset($c['w']))
        {
					if ($miw<$c['w'])	$c['miw'] = $c['w'];
					if ($miw>$c['w'])	$c['miw'] = $c['w']	  = $miw;
					if (!isset($wc['w'])) $wc['w'] = 1;
				}
        else $c['miw'] = $miw;
				if ($c['maw']  < $c['miw']) $c['maw'] = $c['miw'];
				if (!isset($c['colspan']))
        {
					if ($wc['miw'] < $c['miw'])		$wc['miw']	= $c['miw'];
					if ($wc['maw'] < $c['maw'])		$wc['maw']	= $c['maw'];
				}
        else $listspan[] = array($i,$j);
        //Check if minimum width of the whole column is big enough for a huge word to fit
        $auxtext = implode("",$c['text']);
        $minwidth = $this->pdf->WordWrap($auxtext,$wc['miw']-2);// -2 == margin
        if ($minwidth < 0 and (-$minwidth) > $wc['miw']) $wc['miw'] = (-$minwidth) +2; //increase minimum width
        if ($wc['miw'] > $wc['maw']) $wc['maw'] = $wc['miw']; //update maximum width, if needed
			}
		}//rows
	}//columns
	//Xac dinh su anh huong cua cac cell colspan len cac cot va nguoc lai
	$wc = &$table['wc'];
	foreach ($listspan as $span)
  {
		list($i,$j) = $span;
		$c = &$cs[$i][$j];
		$lc = $j + $c['colspan'];
		if ($lc > $nc) $lc = $nc;

		$wis = $wisa = 0;
		$was = $wasa = 0;
		$list = array();
		for($k=$j;$k<$lc;$k++)
    {
			$wis += $wc[$k]['miw'];
			$was += $wc[$k]['maw'];
			if (!isset($c['w']))
      {
				$list[] = $k;
				$wisa += $wc[$k]['miw'];
				$wasa += $wc[$k]['maw'];
			}
		}
		if ($c['miw'] > $wis)
    {
			if (!$wis)
      {//Cac cot chua co kich thuoc => chia deu
				for($k=$j;$k<$lc;$k++) $wc[$k]['miw'] = $c['miw']/$c['colspan'];
			}
      elseif(!count($list))
      {//Khong co cot nao co kich thuoc auto => chia deu phan du cho tat ca
				$wi = $c['miw'] - $wis;
				for($k=$j;$k<$lc;$k++) $wc[$k]['miw'] += ($wc[$k]['miw']/$wis)*$wi;
			}
      else
      {//Co mot so cot co kich thuoc auto => chia deu phan du cho cac cot auto
				$wi = $c['miw'] - $wis;
				foreach ($list as $k)	$wc[$k]['miw'] += ($wc[$k]['miw']/$wisa)*$wi;
			}
		}
		if ($c['maw'] > $was)
    {
			if (!$wis)
      {//Cac cot chua co kich thuoc => chia deu
				for($k=$j;$k<$lc;$k++) $wc[$k]['maw'] = $c['maw']/$c['colspan'];
			}
      elseif (!count($list))
      {
      //Khong co cot nao co kich thuoc auto => chia deu phan du cho tat ca
				$wi = $c['maw'] - $was;
				for($k=$j;$k<$lc;$k++) $wc[$k]['maw'] += ($wc[$k]['maw']/$was)*$wi;
			}
      else
      {//Co mot so cot co kich thuoc auto => chia deu phan du cho cac cot auto
				$wi = $c['maw'] - $was;
				foreach ($list as $k)	$wc[$k]['maw'] += ($wc[$k]['maw']/$wasa)*$wi;
			}
		}
	}
}

function _tableWidth(&$table){
//! @return void
//! @desc Calculates the Table Width
// @desc Xac dinh chieu rong cua table
	$widthcols = &$table['wc'];
	$numcols = $table['nc'];
	$tablewidth = 0;
	for ( $i = 0 ; $i < $numcols ; $i++ )
  {
		$tablewidth += isset($widthcols[$i]['w']) ? $widthcols[$i]['miw'] : $widthcols[$i]['maw'];
	}
	if ($tablewidth > $this->pdf->pgwidth) $table['w'] = $this->pdf->pgwidth;
	if (isset($table['w']))
  {
		$wis = $wisa = 0;
		$list = array();
		for( $i = 0 ; $i < $numcols ; $i++ )
    {
			$wis += $widthcols[$i]['miw'];
			if (!isset($widthcols[$i]['w'])){ $list[] = $i;$wisa += $widthcols[$i]['miw'];}
		}
		if ($table['w'] > $wis)
    {
			if (!count($list))
      {//Khong co cot nao co kich thuoc auto => chia deu phan du cho tat ca
      //http://www.ksvn.com/anhviet_new.htm - translating comments...
      //bent shrink essence move size measure automatic => divide against give as a whole
				//$wi = $table['w'] - $wis;
				$wi = ($table['w'] - $wis)/$numcols;
				for($k=0;$k<$numcols;$k++)
					//$widthcols[$k]['miw'] += ($widthcols[$k]['miw']/$wis)*$wi;
					$widthcols[$k]['miw'] += $wi;
			}
      else
      {//Co mot so cot co kich thuoc auto => chia deu phan du cho cac cot auto
				//$wi = $table['w'] - $wis;
				$wi = ($table['w'] - $wis)/count($list);
				foreach ($list as $k)
					//$widthcols[$k]['miw'] += ($widthcols[$k]['miw']/$wisa)*$wi;
					$widthcols[$k]['miw'] += $wi;
			}
		}
		for ($i=0;$i<$numcols;$i++)
    {
			$tablewidth = $widthcols[$i]['miw'];
			unset($widthcols[$i]);
			$widthcols[$i] = $tablewidth;
		}
	}
  else //table has no width defined
  {
		$table['w'] = $tablewidth;
		for ( $i = 0 ; $i < $numcols ; $i++)
    {
			$tablewidth = isset($widthcols[$i]['w']) ? $widthcols[$i]['miw'] : $widthcols[$i]['maw'];
			unset($widthcols[$i]);
			$widthcols[$i] = $tablewidth;
		}
	}
}

function _tableHeight(&$table){
//! @return void
//! @desc Calculates the Table Height
	$cells = &$table['cells'];
	$numcols = $table['nc'];
	$numrows = $table['nr'];
	$listspan = array();
	for( $i = 0 ; $i < $numrows ; $i++ )//rows
  {
		$heightrow = &$table['hr'][$i];
		for( $j = 0 ; $j < $numcols ; $j++ ) //columns
    {
			if (isset($cells[$i][$j]) && $cells[$i][$j])
      {
				$c = &$cells[$i][$j];
				list($x,$cw) = $this->_tableGetWidth($table, $i,$j);
        //Check whether width is enough for this cells' text
        $auxtext = implode("",$c['text']);
        $auxtext2 = $auxtext; //in case we have text with styles
        $nostyles_size = $this->pdf->GetStringWidth($auxtext) + 3; // +3 == margin
        $linesneeded = $this->pdf->WordWrap($auxtext,$cw-2);// -2 == margin
				if ($c['s'] > $nostyles_size and !isset($c['form'])) //Text with styles
				{
           $auxtext = $auxtext2; //recover original characteristics (original /n placements)
           $diffsize = $c['s'] - $nostyles_size; //with bold et al. char width gets a bit bigger than plain char
           if ($linesneeded == 0) $linesneeded = 1; //to avoid division by zero
           $diffsize /= $linesneeded;
           $linesneeded = $this->pdf->WordWrap($auxtext,$cw-2-$diffsize);//diffsize used to wrap text correctly
        }
        if (isset($c['form']))
        {
           $linesneeded = ceil(($c['s']-3)/($cw-2)); //Text + form in a cell
           //Presuming the use of styles
           if ( ($this->pdf->GetStringWidth($auxtext) + 3) > ($cw-2) ) $linesneeded++;
        }
        $ch = $linesneeded * 1.1 * $this->pdf->lineheight;
        //If height is bigger than page height...
        if ($ch > ($this->pdf->fh - $this->pdf->bMargin - $this->pdf->tMargin)) $ch = ($this->pdf->fh - $this->pdf->bMargin - $this->pdf->tMargin);
        //If height is defined and it is bigger than calculated $ch then update values
				if (isset($c['h']) && $c['h'] > $ch)
				{
           $c['mih'] = $ch; //in order to keep valign working
           $ch = $c['h'];
        }
        else $c['mih'] = $ch;
				if (isset($c['rowspan']))	$listspan[] = array($i,$j);
				elseif ($heightrow < $ch) $heightrow = $ch;
        if (isset($c['form'])) $c['mih'] = $ch;
      }
		}//end of columns
	}//end of rows
	$heightrow = &$table['hr'];
	foreach ($listspan as $span)
  {
		list($i,$j) = $span;
		$c = &$cells[$i][$j];
		$lr = $i + $c['rowspan'];
		if ($lr > $numrows) $lr = $numrows;
		$hs = $hsa = 0;
		$list = array();
		for($k=$i;$k<$lr;$k++)
    {
			$hs += $heightrow[$k];
			if (!isset($c['h']))
      {
				$list[] = $k;
				$hsa += $heightrow[$k];
			}
		}
		if ($c['mih'] > $hs)
    {
			if (!$hs)
      {//Cac dong chua co kich thuoc => chia deu
				for($k=$i;$k<$lr;$k++) $heightrow[$k] = $c['mih']/$c['rowspan'];
			}
      elseif (!count($list))
      {//Khong co dong nao co kich thuoc auto => chia deu phan du cho tat ca
				$hi = $c['mih'] - $hs;
				for($k=$i;$k<$lr;$k++) $heightrow[$k] += ($heightrow[$k]/$hs)*$hi;
			}
      else
      {//Co mot so dong co kich thuoc auto => chia deu phan du cho cac dong auto
				$hi = $c['mih'] - $hsa;
				foreach ($list as $k) $heightrow[$k] += ($heightrow[$k]/$hsa)*$hi;
			}
		}
	}
}

function _tableGetWidth(&$table, $i,$j){
//! @return array(x,w)
// @desc Xac dinh toa do va do rong cua mot cell

	$cell = &$table['cells'][$i][$j];
	if ($cell)
  {
		if (isset($cell['x0'])) return array($cell['x0'], $cell['w0']);
		$x = 0;
		$widthcols = &$table['wc'];
		for( $k = 0 ; $k < $j ; $k++ ) $x += $widthcols[$k];
		$w = $widthcols[$j];
		if (isset($cell['colspan']))
    {
			 for ( $k = $j+$cell['colspan']-1 ; $k > $j ; $k-- )	$w += $widthcols[$k];
		}
		$cell['x0'] = $x;
		$cell['w0'] = $w;
		return array($x, $w);
	}
	return array(0,0);
}

function _tableGetHeight(&$table, $i,$j){
//! @return array(y,h)
	$cell = &$table['cells'][$i][$j];
	if ($cell){
		if (isset($cell['y0'])) return array($cell['y0'], $cell['h0']);
		$y = 0;
		$heightrow = &$table['hr'];
		for ($k=0;$k<$i;$k++) $y += $heightrow[$k];
		$h = $heightrow[$i];
		if (isset($cell['rowspan'])){
			for ($k=$i+$cell['rowspan']-1;$k>$i;$k--)
				$h += $heightrow[$k];
		}
		$cell['y0'] = $y;
		$cell['h0'] = $h;
		return array($y, $h);
	}
	return array(0,0);
}

function _tableRect($x, $y, $w, $h, $type=1){
//! @return void
	if ($type==1)	$this->pdf->Rect($x, $y, $w, $h);
	elseif (strlen($type)==4){
		$x2 = $x + $w; $y2 = $y + $h;
		if (intval($type{0})) $this->pdf->Line($x , $y , $x2, $y );
		if (intval($type{1})) $this->pdf->Line($x2, $y , $x2, $y2);
		if (intval($type{2})) $this->pdf->Line($x , $y2, $x2, $y2);
		if (intval($type{3})) $this->pdf->Line($x , $y , $x , $y2);
	}
}

function _tableWrite(&$table){
//! @desc Main table function
//! @return void
	$cells = &$table['cells'];
	$numcols = $table['nc'];
	$numrows = $table['nr'];
	$x0 = $this->pdf->x;
	$y0 = $this->pdf->y;
	$right = $this->pdf->pgwidth - $this->pdf->rMargin;
	if (isset($table['a']) and ($table['w'] != $this->pdf->pgwidth))
  {
		if ($table['a']=='C') $x0 += (($right-$x0) - $table['w'])/2;
		elseif ($table['a']=='R')	$x0 = $right - $table['w'];
	}
  $returny = 0;
  $tableheader = array();
	//Draw Table Contents and Borders
	for( $i = 0 ; $i < $numrows ; $i++ ) //Rows
  {
    $skippage = false;
    for( $j = 0 ; $j < $numcols ; $j++ ) //Columns
    {
  			if (isset($cells[$i][$j]) && $cells[$i][$j])
        {
				  $cell = &$cells[$i][$j];
				  list($x,$w) = $this->_tableGetWidth($table, $i, $j);
				  list($y,$h) = $this->_tableGetHeight($table, $i, $j);
				  $x += $x0;
  			  $y += $y0;
          $y -= $returny;
          if ((($y + $h) > ($this->pdf->fh - $this->pdf->bMargin)) && ($y0 >0 || $x0 > 0))
          {
            if (!$skippage)
            {
               $y -= $y0;
               $returny += $y;
               $this->pdf->AddPage();
               if ($this->pdf->usetableheader) $this->pdf->Header($tableheader);
               if ($this->pdf->usetableheader) $y0 = $this->pdf->y;
               else $y0 = $this->pdf->tMargin;
               $y = $y0;
            }
            $skippage = true;
          }
				  //Align
				  $this->pdf->x = $x; $this->pdf->y = $y;
				  $align = isset($cell['a'])? $cell['a'] : 'L';
				  //Vertical align
				  if (!isset($cell['va']) || $cell['va']=='M') $this->pdf->y += ($h-$cell['mih'])/2;
          elseif (isset($cell['va']) && $cell['va']=='B') $this->pdf->y += $h-$cell['mih'];
				  //Fill
				  $fill = isset($cell['bgcolor']) ? $cell['bgcolor']
  					: (isset($table['bgcolor'][$i]) ? $table['bgcolor'][$i]
  					: (isset($table['bgcolor'][-1]) ? $table['bgcolor'][-1] : 0));
  				if ($fill)
          {
  					$color = ConvertColor($fill);
  					$this->pdf->SetFillColor($color['R'],$color['G'],$color['B']);
  					$this->pdf->Rect($x, $y, $w, $h, 'F');
  				}
   				//Border
  				if (isset($cell['border'])) $this->pdf->_tableRect($x, $y, $w, $h, $cell['border']);
  				elseif (isset($table['border']) && $table['border']) $this->pdf->Rect($x, $y, $w, $h);
          $this->pdf->divalign=$align;
          $this->pdf->divwidth=$w-2;
          //Get info of first row == table header
          if ($this->pdf->usetableheader and $i == 0 )
          {
              $tableheader[$j]['x'] = $x;
              $tableheader[$j]['y'] = $y;
              $tableheader[$j]['h'] = $h;
              $tableheader[$j]['w'] = $w;
              $tableheader[$j]['text'] = $cell['text'];
              $tableheader[$j]['textbuffer'] = $cell['textbuffer'];
              $tableheader[$j]['a'] = isset($cell['a'])? $cell['a'] : 'L';
              $tableheader[$j]['va'] = $cell['va'];
              $tableheader[$j]['mih'] = $cell['mih'];
              $tableheader[$j]['bgcolor'] = $fill;
              if ($table['border']) $tableheader[$j]['border'] = 'all';
              elseif (isset($cell['border'])) $tableheader[$j]['border'] = $cell['border'];
          }
          if (!empty($cell['textbuffer'])) $this->printbuffer($cell['textbuffer'],false,true/*inside a table*/);
          //Reset values
          $this->Reset();
        }//end of (if isset(cells)...)
    }// end of columns
    if ($i == $numrows-1) $this->pdf->y = $y + $h; //last row jump (update this->y position)
  }// end of rows
}//END OF FUNCTION _tableWrite()

/////////////////////////END OF TABLE CODE//////////////////////////////////

}//end of Class


?>
