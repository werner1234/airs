// afronden op (d) decimalen
function round (n, d) {
  //http://developer.irt.org/script/1494.htm
  n = n - 0; // force number
  var minus = false;
  if (n < 0)
  {
    minus = true;
    n = n * -1;
  }
  if (d == null) d = 2;
  var f = Math.pow(10, d);
  n += Math.pow(10, - (d + 1)); // round first
  n = Math.round(n * f) / f;
  n += Math.pow(10, - (d + 1)); // and again
  if (minus == true)
  {
    n = n * -1;
  }
  n += ''; // force string
  return d == 0 ? n.substring(0, n.indexOf('.')) :
      n.substring(0, n.indexOf('.') + d + 1);
}

// controleer of waarde een nummer is
function checkNumber(field)
{
	var x=field.value;
	var anum=/^-?([0-9]*)(\.([0-9]+))?$/;
	if (anum.test(x) != true)
	{
		alert("Veld \"" + field.name + "\" bevat een ongeldige waarde.");
		selectAll(field);
		field.focus();
		return false;
	}
	return true;
}

function checkAndFixNumber(field)
{
	var x=field.value+"";
	var anum=/^-?([0-9]*)(\.([0-9]+))?$/;
	if (anum.test(x) != true)
	{
      x=x.replace(",",".");
	  if (anum.test(x) == true)
	  {
	    alert("Veld \"" + field.name + "\" is gecorrigeerd van (" + field.value + ") naar (" + x + ")." );
	    field.value=x;
	  }
	  else
	  {
		var OkayValues = "1234567890-.,";var i;var returnString = "";
        for (i = 0; i < x.length; i++)
		{
          var c = x.charAt(i);
          if (OkayValues.indexOf(c) != -1) returnString += c;
        }
        x = returnString;
		if (anum.test(x) == true)
	    {
	      alert("Veld \"" + field.name + "\" is gecorrigeerd van (" + field.value + ") naar (" + x + ")." );
	      field.value=x;
	    }
		else
		{
	      alert("Veld \"" + field.name + "\" bevat een ongeldige waarde (" + field.value + ").");
		  field.value="";
		}
	  }
	}
}

function selectAll(input)
{	//functie om range te selecteren.
  if (input.setSelectionRange)
  {									//NS/Mozilla range select
    input.focus();
    input.setSelectionRange(0, input.value.length);
  }
  else if (input.createTextRange)
  {									//IE range select
    var range = input.createTextRange();
    range.collapse(true);
    range.moveEnd('character',input.value.length);
    range.moveStart('character',0);
    range.select();
  }
	input.focus();
}

function formatNumericField(input,d)
{
	if (input == null) return;
	var num = input.value.replace(/\,/g,'.');
	num = num.replace(/ /g,'');
	return round(num,d);
}

function fillSelect(field,Category)
{
  $('#'+field+' >option').remove();
			$.ajax({
				type: "GET",
				url: "dropdown.xml",
				dataType: "xml",
				success: function(xml)
				{
				var select = $('#'+field);
				$(xml).find('item').each(function()
				{
					  var key       = $(this).find('key').text();
						var value = $(this).find('value').text();
						select.append("<option value='"+ key +"'>"+value+"</option>");
				});
					select.children(":first").text("2 set").attr("selected",true);
				}
			});


}

/************************************************
 *
 * DATUM functies
 *
 ************************************************/

function date_complete(input)
{

	var Doth = new Array(); // max dagen in de maand
	Doth[1]  = '31';		Doth[2]  = '29';		Doth[3]  = '31';		Doth[4]  = '30';
	Doth[5]  = '31';		Doth[6]  = '30';		Doth[7]  = '31';		Doth[8]  = '31';
	Doth[9]  = '30';		Doth[10] = '31';		Doth[11] = '30';		Doth[12] = '31';

	if (input.value == '')  return;  // niks te doen = exit

  datum   = input.value.toString();
  vandaag = new Date();

  current_dag   = vandaag.getDate();
  current_month = vandaag.getMonth()+1;
  current_year  = vandaag.getFullYear();
  dag   = current_dag.toString();
  maand = current_month.toString();
  jaar  = current_year.toString();

  // zoek delimiter
  delimit = '.';
  if (datum.search(" ") > 0) delimit = " ";
  if (datum.search("-") > 0) delimit = "-";
  if (datum.search("/") > 0) delimit = "/";
  calcDate = datum.split(delimit);

  if (calcDate.length == 0)
    calcDate = datum.split(".");

  endDag   =  eval(calcDate[0]);
  endMaand =	eval(calcDate[1]);
  endJaar  =	eval(calcDate[2]);
  setToday= false;

  if (!isNaN(endJaar))
  {
    if (endJaar < 50)                       endJaar = 2000 + eval(endJaar);
    if (endJaar < 1950 || endJaar > 2050)   endJaar = jaar;
  }
  else
    endJaar = jaar;


  if (!isNaN(endMaand))
  {
    if (endMaand < 1 || endMaand > 12)   setToday = true;
  }
  else
    endMaand = maand;

  if (!isNaN(endDag))
  {
    if (endDag < 1 || endMaand > 12)    setToday = true;
  }
  else
    endMaand = maand;

  if (endDag < 1 || endDag > Doth[endMaand])  setToday = true;

  if (setToday == true)
  {
    input.value= dag+'-'+maand+'-'+jaar;
    alert('Ingegeven datum is ongeldig, datum teruggezet naar vandaag: '+input.value);
  }
  else
    input.value= endDag+'-'+endMaand+'-'+endJaar;
  return input.value;
}


function dagen_bijtellen(in_datum,dagen)
{
  var atmp = in_datum.split('-');
  var d = new Date();
  d.setYear(parseInt(atmp[2],10));
  d.setMonth(parseInt(atmp[1]-1,10));
  d.setDate(parseInt(atmp[0],10));
  d.setTime(eval(d.getTime() + eval(dagen * 86400000)));
  return(d.getDate()+'-'+eval(d.getMonth()+1)+'-'+d.getFullYear());

}


function dagen_verschil(uit_datum,in_datum)
{
  var atmp = in_datum.split('-');
  var btmp = uit_datum.split('-');
  var i = new Date();
  var u = new Date();

  i.setYear(parseInt(atmp[2],10));
  i.setMonth(parseInt(atmp[1]-1,10));
  i.setDate(parseInt(atmp[0],10));

  u.setYear(parseInt(btmp[2],10));
  u.setMonth(parseInt(btmp[1]-1,10));
  u.setDate(parseInt(btmp[0],10));

  var output = eval( eval(u.getTime()/86400000) - eval(i.getTime()/86400000) );
  var output = round(output,0);
  return output;
}


function elfproef(veld)
{

  input = new String(veld.value);
  if (input == '')
  {
    return true;
  }

  var postbank = input.substr(0,1);
  input = input.replace(/[^0-9]*/g,'');

  var tot=0;
  var deel=0;
  var rest =0;

  if (postbank.toUpperCase() == 'P')
  {
    if (input.length > 3 )
      return true;
    else
      alert("Postbanknummer moet minimaal uit 3 cijfers bestaan");
      return true;
  }

  if (input.length > 9 && postbank.toUpperCase() != 'P')
  {
    alert("u moet 9 cijfers ingeven of een Postbank rekening invoeren als Pxxx waar xxx het rekeningnummer is");
    veld.focus();
    return true;
  }
  else
  {
    for (i=0;i<input.length;i++)
    {
      getal = input.substr(i,1);
      tot  += getal * (9 - i);
    }
    deel = tot/11;
    rest = tot%11;
    if (rest != 0)
    {
      alert("Banknummer klopt niet. Bij een Postbank rekening invoeren als Pxxx waar xxx het rekeningnummer is");
      veld.focus();
    }
  }
  checkcre();
}

function clog(txt)
{
  console.log("debug... "+txt);
}

function loadToDiv (id, url, data) {
  $('#' + id).html('');
  $('#' + id).load(url, data);
}

function clearModalContent () {
  $('#modelContent').html();
}



/**
 * Readonly v1.0.0
 * by Arthur Corenzan <arthur@corenzan.com>
 * more on //github.com/haggen/readonly
 */
;(function($, undefined) {

  function readonly(element) {
    if(element.is('select')) {
      element.addClass('readonly').data('readonly', true).prop('disabled', true);
      element.after('<input type="hidden" name="' + element[0].name + '" value="' + element[0].value + '" class="readonly-' + element[0].name + '">');
    } else {
      element.prop('readonly', true);
    }
  }

  function editable(element) {
    if(element.is('select')) {
      element.removeClass('readonly').removeData('readonly');
      element.prop('disabled', false);
      $('.readonly-'+ element[0].name).remove();
      $('.readonly-'+ element[0].name).attr('name', 'testtesttest');
    } else {
      element.prop('readonly', false);
    }
  }

  $.fn.readonly = function(state) {
    return this.each(function(index, element) {
      element = $(element);

      if(state === undefined) {
        if(element.is('select')) {
          state = !element.data('readonly');
        } else {
          state = !element.prop('readonly');
        }
      }

      if(state) {
        readonly(element);
      } else {
        editable(element);
      }
    });
  };
})(window.jQuery);


/** maak en jquery ui dialog voor bevestiging **/
function AEConfirm(dialogText, dialogTitle, JaFunc, NeeFunc) {
   $('<div style="padding: 10px; max-width: 500px; word-wrap: break-word;">' + dialogText + '</div>').dialog({
    draggable: false,
    modal: true,
    resizable: false,
    width: 'auto',
    title: dialogTitle || 'Confirm',
    minHeight: 75,
    buttons: {
      "Ja": function () {
        if (typeof (JaFunc) == 'function') {
          setTimeout(JaFunc, 50);
        }
        $(this).dialog('destroy');
      },
      "Nee": function () {
        if (typeof (NeeFunc) == 'function') {
          setTimeout(NeeFunc, 50);
        }
        $(this).dialog('destroy');
      }
    }
  });
}

/** maak en jquery ui dialog voor bevestiging **/
function AEMessage(dialogText, dialogTitle, okFunc) {
  $('<div style="padding: 10px; max-width: 500px; word-wrap: break-word;">' + dialogText + '</div>').dialog({
    draggable: false,
    modal: true,
    resizable: false,
    width: 'auto',
    title: dialogTitle || 'Confirm',
    minHeight: 75,
    buttons: {
      "Ok": function () {
        if (typeof (okFunc) == 'function') {
          setTimeout(okFunc, 50);
        }
        $(this).dialog('destroy');
      }
    }
  });
}


/** maak en jquery ui dialog voor bevestiging **/
function AECustomMessage(dialogText, dialogTitle, buttons) {
  $('<div style="padding: 10px; max-width: 500px; word-wrap: break-word;">' + dialogText + '</div>').dialog({
    draggable: false,
    modal: true,
    resizable: false,
    width: 'auto',
    title: dialogTitle || 'Confirm',
    minHeight: 75,
    buttons: buttons
  });
}


/** Color fields **/
var AEColor = function () {
  
  var vars = {
    fieldClass: '.colorp'
  };
  var root = this;
  
  this.construct = function(options){
    $.extend(vars , options);
  };
  
  this.initColors = function () {
    $skipList = '';
  
    $(vars.fieldClass).each(function(){
      $indentifier = $(this).data("group");
      if ( $skipList != $indentifier ) {
        $skipList = $indentifier;
        toonKleur($indentifier);
        createPicker($indentifier);
      }
    });
  };
  
  this.initColorChange = function ()
  {
    $(vars.fieldClass).change(function () {
      $indentifier = $(this).data("group");
      toonKleur($indentifier);
    });
  };
  
  
  var toonKleur = function ($indentifier)
  {
    var rood = parseInt($("#" + $indentifier + "_R").val());
    var groen = parseInt($("#" + $indentifier + "_G").val());
    var blauw = parseInt($("#" + $indentifier + "_B").val());
  
    if(isNaN(rood)) {var rood = 0;}
    if(isNaN(groen)) {var groen = 0;}
    if(isNaN(blauw)) {var blauw = 0;}
    
    var color = "rgb(" + parseInt(rood) + "," + parseInt(groen) + "," + parseInt(blauw) + ")";
    // $("#" + $indentifier + "-colorPicker .input-group-addon i").css("background", color);
    $("#" + $indentifier + "-colorPicker .input-group-addon input").val("rgb(" + parseInt(rood) + "," + parseInt(groen) + "," + parseInt(blauw) + ")");
  
    $("#" + $indentifier + "-colorPicker").colorpicker({color: "rgb(" + parseInt(rood) + "," + parseInt(groen) + "," + parseInt(blauw) + ")"});
    $("#" + $indentifier + "-colorPicker").colorpicker('setValue', "rgb(" + parseInt(rood) + "," + parseInt(groen) + "," + parseInt(blauw) + ")");
  //
  };
  
  var createPicker = function ($indentifier)
  {
    $("#" + $indentifier + "-colorPicker").colorpicker({
      format: "rgb",
      useAlpha: false,
      color: "rgb("+$("#" + $indentifier + "_R").val()+", "+ $("#" + $indentifier + "_G").val()+", "+$("#" + $indentifier + "_B").val()+")"
    }).on('changeColor.colorpicker', function (ev) {
      color = ev.color.toRGB();
      $("#" + $indentifier + "_R").val(color.r);
      $("#" + $indentifier + "_G").val(color.g);
      $("#" + $indentifier + "_B").val(color.b);
    });
  };
}

var AEColor = new AEColor();