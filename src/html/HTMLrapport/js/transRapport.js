$(document).ready(function()
{

  $('.double-scroll').doubleScroll();



  /** tooltip **/
  $('body').tooltip({ selector: '[data-toggle="tooltip"]' });

  $(document).on('click', 'button[name=saveBtn]', function (e) {
    e.preventDefault();
    $btn = $(this);


    AEConfirm(
      'Weet u zeker dat u deze instellingen wilt opslaan?',
      'Standaard instellingen',
      function () {
        $('<input>').attr('type','hidden').attr('name','saveBtn').attr('value',$btn.val()).appendTo($btn.parents('form:first'));
        $btn.parents('form:first').submit();
      },
      function () {
        return false;
      }
    );
  });

  $( ".AIRSdatepicker" ).datepicker({
    showOn: "button",
    buttonImage: "../javascript/calendar/img.gif",//"images/datePicker.png",
    buttonImageOnly: true,
    dateFormat: "dd-mm-yy",
    dayNamesMin: ["Zo", "Ma", "Di", "Wo", "Do", "Vr", "Za"],
    monthNames: ["januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december"],
    monthNamesShort: [ "Jan", "Feb", "Mrt", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dec" ],
    nextText: "volgende maand",
    prevText: "vorige maand",
    currentText: "huidige maand",
    changeMonth: true,
    changeYear: true,
    yearRange: '2000:2050',
    closeText: "sluiten",
    showAnim: "slideDown",
    showButtonPanel: true,
    showOtherMonths: true,
    selectOtherMonths: true,
    numberOfMonths: 2,
    showWeek: true,
    firstDay: 1
  });

  $(".btnValue").click(function(e)
  {
    e.preventDefault();
    $("#periodeStartDatum").val($(this).attr("data-btn"));
  });

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


  // $('#tableHeader').after($('.tableFoot').clone());



  /*
   ** start chris
   */


  $('#terugBtn').on('click', function ()
  {
    window.open("../rapportFrontofficeClientSelectie.php","content");
  });

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
    $("#infoTab5").html('');
    $("#ui-dialog-title-extraInfoPopup").html('');

    var btnId = $(this).attr("id").substring(6);
    var stop = $(document).find('#stop').val();
    infoDialog.dialog('open');
    $("#infoTab1").load("transExtraInfo.php?tab=1&id=" + btnId + "&stop=" + stop);
    $("#infoTab2").load("transExtraInfo.php?tab=2&id=" + btnId + "&stop=" + stop);
    $("#infoTab3").load("transExtraInfo.php?tab=3&id=" + btnId + "&stop=" + stop);
    $("#infoTab4").load("transExtraInfo.php?tab=4&id=" + btnId + "&stop=" + stop);
    $("#infoTab5").load("transExtraInfo.php?tab=5&id=" + btnId + "&stop=" + stop);
    $("#titleTab1").click();
  });

  /*
   ** end chris
   */
  $('#transTable').fixedHeader();




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
    $('#transHeader').css('display', 'table');
    if ($(this).is(':checked'))
    {
      hideCol = $(this).data('colname');
      $(document).find('*[data-field=' + hideCol + ']').slideDown('slow');
      $('#transHeader').find('*[data-field=' + hideCol + ']').slideDown('slow');
    }
    else
    {
      hideCol = $(this).data('colname');
      $(document).find('*[data-field=' + hideCol + ']').slideUp('slow');
      $('#transHeader').find('*[data-field=' + hideCol + ']').slideUp('slow');
    }
    $('#transHeader').css('display', 'none');
  });


  $('#transTable').dragtable({
    placeholder: 'dragtable-col-placeholder test3',
    items: 'thead th:not( .notdraggable ):not( :has( .dragtable-drag-handle ) ), .dragtable-drag-handle',
    scroll: true
  });

//[data-field=totaalAantal]
  $(document).on('click', '.sortColumn', function (e)
  {
    console.log('clicked');
    e.preventDefault();
    var element = $(this);
    element.closest('.headerTD').find('[data-sortdirection="none"]').addClass('hidden');
    var sort = $(this).closest('.headerTD').data('sort');
    var field = $(this).closest('.headerTD').data('field');

    $('#transTable').find('tbody').each(function (i, el)
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
        if ( $('#transTable').find('tbody').length === 1 ) {
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
      fixedHeader.attr('id', 'transHeader');
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








/*
 * @name DoubleScroll
 * @desc displays scroll bar on top and on the bottom of the div
 * @requires jQuery, jQueryUI
 *
 * @author Pawel Suwala - http://suwala.eu/
 * @version 0.3 (12-03-2014)
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */

(function($){
  $.widget("suwala.doubleScroll", {
    options: {
      contentElement: undefined, // Widest element, if not specified first child element will be used
      topScrollBarMarkup: '<div class="suwala-doubleScroll-scroll-wrapper" style="height: 20px;"><div class="suwala-doubleScroll-scroll" style="height: 20px;"></div></div>',
      topScrollBarInnerSelector: '.suwala-doubleScroll-scroll',
      scrollCss: {
        'overflow-x': 'scroll',
        'overflow-y':'hidden'
      },
      contentCss: {
        'overflow-x': 'scroll',
        'overflow-y':'hidden'
      }
    },
    _create : function() {
      var self = this;
      var contentElement;

      // add div that will act as an upper scroll
      var topScrollBar = $($(self.options.topScrollBarMarkup));
      self.element.before(topScrollBar);

      // find the content element (should be the widest one)
      if (self.options.contentElement !== undefined && self.element.find(self.options.contentElement).length !== 0) {
        contentElement = self.element.find(self.options.contentElement);
      }
      else {
        contentElement = self.element.find('>:first-child');
      }

      // bind upper scroll to bottom scroll
      topScrollBar.scroll(function(){
        self.element.scrollLeft(topScrollBar.scrollLeft());
      });

      // bind bottom scroll to upper scroll
      self.element.scroll(function(){
        topScrollBar.scrollLeft(self.element.scrollLeft());
      });

      // apply css
      topScrollBar.css(self.options.scrollCss);
      self.element.css(self.options.contentCss);

      // set the width of the wrappers
      $(self.options.topScrollBarInnerSelector, topScrollBar).width(contentElement[0].scrollWidth);
      topScrollBar.width(self.element[0].clientWidth);
    },
    refresh: function(){
      // this should be called if the content of the inner element changed.
      // i.e. After AJAX data load
      var self = this;
      var contentElement;
      var topScrollBar = self.element.parent().find('.suwala-doubleScroll-scroll-wrapper');

      // find the content element (should be the widest one)
      if (self.options.contentElement !== undefined && self.element.find(self.options.contentElement).length !== 0) {
        contentElement = self.element.find(self.options.contentElement);
      }
      else {
        contentElement = self.element.find('>:first-child');
      }

      // set the width of the wrappers
      $(self.options.topScrollBarInnerSelector, topScrollBar).width(contentElement[0].scrollWidth);
      topScrollBar.width(self.element[0].clientWidth);
    }
  });
})(jQuery);