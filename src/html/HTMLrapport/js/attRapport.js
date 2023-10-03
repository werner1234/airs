$(document).ready(function()
{



  $('#fs').on('change',function(e) {
    //this refers to the option
    console.log($(this).find("option:selected").text());
    setFont($(this).find("option:selected").text());
  });

  function setFont(newFont) {
    var fontStyleSheet = $('style#FontCSS'),  fontRule = '* { font-family: ' + newFont + ' !important; }';
    $('#FontCSS').remove();
    if ( fontStyleSheet && fontStyleSheet.length > 0) {
      with ( document.styleSheets[document.styleSheets.length - 1].cssRules[0].style ) {
        cssText = "";
        fontFamily = newFont;
      }
    }
    else {
      jQuery("head").append('<style id="FontCSS" type="text/css">' + fontRule + '</style>');
    }
  }




  $('#terugBtn').on('click', function () {
    window.open("../rapportFrontofficeClientSelectie.php","content");
  });


/*
 ** start chris
 */

infoDialog = $('#extraInfoPopup').dialog({
  autoOpen: false,
  height: 550,
  width: '80%',
  modal: true,
  buttons: {},
  close: function ()
  {
  }
});
$('#infoTabs').tabs();

$(".iBtn").click(function (e)
{
  e.preventDefault();

  $("#infoTab1").html('');
  $("#infoTab2").html('');
  $("#infoTab3").html('');
  $("#infoTab4").html('');
  $("#ui-dialog-title-extraInfoPopup").html('');

  var btnId = $(this).attr("id").substring(6);
  infoDialog.dialog('open');
  $("#infoTab1").load("volkExtraInfo.php?tab=1&id=" + btnId);
  $("#infoTab2").load("volkExtraInfo.php?tab=2&id=" + btnId);
  $("#infoTab3").load("volkExtraInfo.php?tab=3&id=" + btnId);
  $("#infoTab4").load("volkExtraInfo.php?tab=4&id=" + btnId);
  $("#titleTab1").click();
});

/*
 ** end chris
 */
$('#attTable').fixedHeader();

filterDialog = $('#filterDialog').dialog({
  autoOpen: false,
  height: 500,
  width: '40%',
  modal: true,
  buttons: {},
  close: function ()
  {
  }
});

$(document).on('click', '.closeFilter', function () {
  filterDialog.dialog('close');
});

$('#filterDialogBtn').on('click', function ()
{
  filterDialog.dialog('open');
});

$('#tabs').tabs();

$('#sortable').sortable({
  placeholder: 'ui-state-highlight',
  start: function (e, ui)
  {
    $(ui.item).data('old-ndex', ui.item.index());
  },
  update: function (event, ui)
  {
    var old_index = $(ui.item).data('old-ndex');
    var new_index = ui.item.index();
    jQuery.moveColumn($('.list_tabel'), old_index, new_index);
    console.log('old_index ' + old_index + ' new index ' + new_index);
  }
});


jQuery.moveColumn = function (table, from, to)
{
  var cols;
  jQuery('tr', table).each(function ()
  {
    cols = jQuery(this).children('th, td');
    cols.eq(from).detach().insertBefore(cols.eq(to));
  });
}


$(document).on('click', '.showHideFilter', function ()
{
  $('#volktest').css('display', 'table');
  if ($(this).is(':checked'))
  {
    hideCol = $(this).data('colname');
    $(document).find('*[data-field=' + hideCol + ']').slideDown('slow');
    $('#volktest').find('*[data-field=' + hideCol + ']').slideDown('slow');
  }
  else
  {
    hideCol = $(this).data('colname');
    $(document).find('*[data-field=' + hideCol + ']').slideUp('slow');
    $('#volktest').find('*[data-field=' + hideCol + ']').slideUp('slow');
  }
  $('#volktest').css('display', 'none');
});


$('#volkTable').dragtable({
  placeholder: 'dragtable-col-placeholder test3',
  items: 'thead th:not( .notdraggable ):not( :has( .dragtable-drag-handle ) ), .dragtable-drag-handle',
  scroll: true
});

//[data-field=totaalAantal]
$(document).on('click', '.sortColumn', function (e)
{
  e.preventDefault();
  var element = $(this);

  element.closest('.headerTD').find('[data-sortdirection="none"]').addClass('hidden');
  var sort = $(this).closest('.headerTD').data('sort');
  var field = $(this).closest('.headerTD').data('field');
  var numbodies = $('#volkTable').find('tbody').length;
  $('#volkTable').find('tbody').each(function (i, el)
  {
    var table = $(this);
    var rows = $('tr.dataRow', $(this));

    rows.sort(function (a, b)
    {
      var val1 = $('td[data-field=' + field + ']', a).data('value');
      var val2 = $('td[data-field=' + field + ']', b).data('value');

      if (sort == 'ASC')
      {
        if (val1 > val2)
        {
          return 1;
        }
        if (val1 < val2)
        {
          return -1;
        }
      } else
      {
        if (val1 > val2)
        {
          return -1;
        }
        if (val1 < val2)
        {
          return 1;
        }
      }
      return 0;
    });

    rows.each(function (index, row)
    {
      if ( numbodies == 1 ) {
        table.append(row);
      } else {
        table.find('.footerRow:first').before(row);
      }
    });

  });


  //addClass('hidden')
  //removeClass('hidden')
  if (sort == 'ASC')
  {
    element.closest('.headerTD').find('[data-sortdirection="ASC"]').removeClass('hidden')
    element.closest('.headerTD').find('[data-sortdirection="DESC"]').addClass('hidden');

    console.log('DESC');
    $('.headerTD[data-field=' + field + '').data('sort', 'DESC');
  } else
  {
    element.closest('.headerTD').find('[data-sortdirection="ASC"]').addClass('hidden')
    element.closest('.headerTD').find('[data-sortdirection="DESC"]').removeClass('hidden');

    console.log('ASC');
    $('.headerTD[data-field=' + field + '').data('sort', 'ASC');
  }

});
});


$.fn.fixedHeader = function() {
  return this.each(function() {
    var element = $(this);
    var fixedHeader;

    function init() {
      // element.wrap('<div class="container" />');
      fixedHeader = element.clone();
      fixedHeader.attr('id', 'volktest');
      fixedHeader.css('width', 'calc(100% - 20px)');
      fixedHeader.find("tbody, tfoot, .tableFoot:first").remove().end().addClass("fixed").insertBefore(element);
      resizeHeader();
      scrollFixed();
    }

    function resizeHeader() {
      fixedHeader.find("th").each(function(index) {
        $(this).width(element.find("th").eq(index).width());
      });
    }

    function scrollFixed() {
      resizeHeader()
      var offset = $(this).scrollTop();
      var tableOffsetTop = element.offset().top;
      var tableOffsetBottom = tableOffsetTop + element.height() - element.find("thead").height();

      if(offset < tableOffsetTop || offset > tableOffsetBottom)
        fixedHeader.hide();
      else if(offset >= tableOffsetTop && offset <= tableOffsetBottom && fixedHeader.is(":hidden"))
        fixedHeader.show();
    }

    $(window).resize(resizeHeader);
    $(window).scroll(scrollFixed);
    init();
  });
};
