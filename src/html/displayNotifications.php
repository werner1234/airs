<?php
include_once("wwwvars.php");

?>

<style>
  #notifications-holder {
    z-index: 90000;
    margin: 0 auto;
    margin-top: 35px;
    position: relative;
  }
  #notificationsCountdownItem{
    float: right;
    padding: 4px;
  }
  .ui-tabs .ui-tabs-panel {
    height: 200px;
    overflow: auto;
    padding: 0px!important;
  }
  .list-group-item ul {
    padding: 0px 0px 0px 10px;
  }
</style>


<span id="notifications-btn" class="btn-new btn-default">
    Notificaties
    <span class="label label-error">0</span>
    <span class="label label-warning">0</span>
    <span class="label label-info">0</span>
    <span class="label label-success">0</span>



  </span>
  <div id="notifications-holder" style="display: none;">
    <div id="notifications-body"  >
      <div id="tabs">
        <ul>
          <li><a href="#tabs-1">Notificaties</a></li>
          <li><a href="#tabs-2">Alle meldingen</a></li>
          <li><a href="#tabs-3">Foutmeldingen</a></li>

          <li id="notificationsCountdownItem"><span id="notificationsCountdownHolder"></span> <span id="closeNotifier" style="padding: 1px 10px 1px;" class="btn-new btn-default">Sluiten</span> </li>
        </ul>
        <div id="tabs-1">
          <ul class="list-group"></ul>
        </div>
        <div id="tabs-2">
          <ul class="list-group"></ul>
        </div>
        <div id="tabs-3">
          <ul class="list-group"></ul>
        </div>
        <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
          <span id="markAllAsRead" class="btn-new btn-default">Alles markeren als gelezen</span>
        </ul>
      </div>
    </div>
  </div>


  <script>
    $(function() {
      $('#tabs').tabs({
        active: 1
      });
      var $alertActive = 0;

      $(document).on('click', '#notifications-btn', function() {
        if ( $alertActive === 0 ) {
          $('#notifications-holder').animate({'width':'70%'},300,function(){
            $('#notifications-holder').slideDown(300);
          });
          $alertActive = 1;
        } else {
          $('#notifications-holder').animate({'width':'0px'},300,function(){
            $('#notifications-holder').slideUp(300);
          });
          $alertActive = 0;
        }
      });

      $(document).on('click', '#closeNotifier', function() {
          $('#notifications-holder').animate({'width':'0px'},300,function(){
            $('#notifications-holder').slideUp(300);
          });
          $alertActive = 0;
      });




      $(document).on('click', '.parentDismissNotification', function () {
        parent = $(this).parent();
        subNotifications = $(this).parent().find('input[data-rowmoduleid='+$(this).data('rowid')+']');

        var idArray = [];
        subNotifications.each(function( index, element ) {
          idArray.push($( element).data('rowid'));
        });
        console.log(idArray);
        console.log(subNotifications);
        console.log($(this));
        //data-rowmoduleid

        $.ajax({
          url : "lookups/ajaxLookup.php",
          type: "GET",
          dataType: "json",
          data : {
            fromClass : "AIRS_Notify",
            type : "seenNotification",
            notificationId: idArray,
            seen : 1
          },
          success:function(data, textStatus, jqXHR) {
            if ( data.saved == true ) {
              $(parent).hide('slow', function(){ subNotifications.html(); });
            } else {
            }
          }
        });


      });

      $(document).on('click', '#markAllAsRead', function () {
        var selector = $("#tabs .ui-tabs-panel:visible");

        var idArray = [];

        $(selector).find('.dismissNotification').each(function( index ) {
          idArray.push($(this).data("rowid"));
        });
        $.ajax({
          url : "lookups/ajaxLookup.php",
          type: "GET",
          dataType: "json",
          data : {
            fromClass : "AIRS_Notify",
            type : "seenNotification",
            notificationId: idArray,
            seen : 1
          },
          success:function(data, textStatus, jqXHR) {
            if ( data.saved == true ) {
//              $('#notifications-body .list-group').remove();
              $('#'+selector.attr('id') + ' .list-group').html('');
              $('#notifications-holder').animate({'width':'0px'},300,function(){
                $('#notifications-holder').slideUp(300);
              });
              $alertActive = 0;
            } else {
            }
          }
        });
      });

      $(document).on('change', '.dismissNotification', function () {
        var element = $(this).parent();
        $.ajax({
            url : "lookups/ajaxLookup.php",
            type: "GET",
            dataType: "json",
            data : {
              fromClass : "AIRS_Notify",
              type : "seenNotification",
              notificationId: $(this).data("rowid"),
              seen : 1
            },
            success:function(data, textStatus, jqXHR) {
              if ( data.saved == true ) {
//                element.remove();
                element.hide('slow', function(){ element.remove(); });
              } else {
              }
            }
          });
      });



      function startTimer(duration, display) {
        var timer = duration, minutes, seconds;
        setInterval(function () {
          minutes = parseInt(timer / 60, 10)
          seconds = parseInt(timer % 60, 10);

          seconds = seconds < 10 ? "0" + seconds : seconds;

          display.text(seconds);

          if (--timer < 0) {
          reloadNotifications();
              timer = duration;
          }
        }, 1000);
      }
      startTimer(10, $("#notificationsCountdownHolder"));

    var messageTypes = {
      1 : 'success',
      2 : 'info',
      3 : 'warning',
      4 : 'error'
    };
    var messagePrio = {
      'success' : 1,
      'info'    : 2,
      'warning' : 3,
      'error'   : 4
    };

    function reloadNotifications () {
      $('#notifications-btn').removeClass('btn-delete');
      $('#notifications-btn').removeClass('btn-blue');
      $('#notifications-btn').removeClass('btn-save');



      $.ajax({
        url : "lookups/ajaxLookup.php",
        type: "GET",
        dataType: "json",
        data : {
          fromClass : "AIRS_Notify",
          type : "getNotifier",
        },
        success:function(data, textStatus, jqXHR) {

          if ( data.status ) {
            higestType = 0;

            $.each( data.status, function( index, value ){

              if ( value > 0 ) {
                if ( higestType < messagePrio[index] ) {higestType = messagePrio[index]}
                $('#notifications-btn .label-' + index).show();
                $('#notifications-btn .label-' + index).html(value);
              } else {
                $('#notifications-btn .label-' + index).hide();
                $('#notifications-btn .label-' + index).html(0);
              }
            });

            if ( higestType !== 0 ) {
              $('#notifications-btn').show();

              $('#notifications-btn').removeClass('btn-default');
              topMessage = messageTypes[higestType];
              if (topMessage == "error") {
                $('#notifications-btn').addClass('btn-delete');
              } else if (topMessage == "warning") {
                $('#notifications-btn').addClass('btn-delete');
              } else if (topMessage == "info") {
                $('#notifications-btn').addClass('btn-default');
              } else if (topMessage == "success") {
                $('#notifications-btn').addClass('btn-default');
              }
            } else {
              $('#notifications-btn').hide();
              $('#notifications-holder').animate({'width':'0px'},300,function(){
                $('#notifications-holder').slideUp(300);
              });
              $alertActive = 0;
            }

          }

          if ( data.groupedResults ) {
            $('#tabs-1 .list-group').html('');
            $.each( data.groupedResults, function( index, value ){
              listData = '';
              listData += '<li class="list-group-item list-group-item-sm list-group-item-' + value.module.module + '-' + value.module.id + '  ">';
              listData += '<input type="checkbox" class="parentDismissNotification" data-rowid="' + value.module.module + '-' + value.module.id + '" name="notification" value=""> <strong>' + value.module.module + ': ' + value.module.id + '</strong>';

              listData += '<ul>';
              $.each( value.data, function( dataIndex, dataValue ){
                listData += '<li class="list-group-item list-group-item-sm list-group-item-' + dataValue.type + ' "> <input type="checkbox" class="dismissNotification" data-rowmoduleid="' + value.module.module + '-' + value.module.id + '" data-rowid="' + dataValue.id + '" name="notification" value=""> ' + dataValue.message + '</li>'
              });
              listData += '</ul>';
              listData += '</li>'
              $('#tabs-1 .list-group').append(listData);
            });
          }

          if ( data.fetchAll ) {
            $('#tabs-2 .list-group').html('');
            $.each( data.fetchAll, function( index, value ){
                $('#tabs-2 .list-group').append('<li class="list-group-item list-group-item-sm list-group-item-' + value.type + ' "> <input type="checkbox" class="dismissNotification" data-rowid="' + value.id + '" name="notification" value=""> ' + value.message + '</li>');
            });
          }

          if ( data.fetchAllError ) {
            $('#tabs-3 .list-group').html('');
            $.each( data.fetchAllError, function( index, value ){
                $('#tabs-3 .list-group').append('<li class="list-group-item list-group-item-sm list-group-item-' + value.type + ' "><input type="checkbox" class="dismissNotification" data-rowid="' + value.id + '" name="notification" value=""> ' + value.message + '</li>');
            });
          }

        },
        error: function(){

        },
        timeout: 5000
      });



//       $.ajax({
//         url : "lookups/ajaxLookup.php",
//         type: "GET",
//         dataType: "json",
//         data : {
//           fromClass : "AIRS_Notify",
//           type : "getStatus",
//         },
//         success:function(data, textStatus, jqXHR) {
//           higestType = 0;
//
//           $.each( data, function( index, value ){
//             if ( value > 0 ) {
//               if ( higestType < messagePrio[index] ) {higestType = messagePrio[index]}
//               $('#notifications-btn .label-' + index).show();
//               $('#notifications-btn .label-' + index).html(value);
//             } else {
//               $('#notifications-btn .label-' + index).hide();
//               $('#notifications-btn .label-' + index).html(0);
//             }
//           });
//
//           if ( higestType !== 0 ) {
//             $('#notifications-btn').show();
//
//             $('#notifications-btn').removeClass('btn-default');
//             topMessage = messageTypes[higestType];
//             if (topMessage == "error") {
//               $('#notifications-btn').addClass('btn-delete');
//             } else if (topMessage == "warning") {
//               $('#notifications-btn').addClass('btn-delete');
//             } else if (topMessage == "info") {
//               $('#notifications-btn').addClass('btn-default');
// //              $('#notifications-btn').addClass('btn-blue');
//             } else if (topMessage == "success") {
//               $('#notifications-btn').addClass('btn-default');
// //              $('#notifications-btn').addClass('btn-save');
//             }
//           } else {
//             $('#notifications-btn').hide();
//             $('#notifications-holder').animate({'width':'0px'},300,function(){
//               $('#notifications-holder').slideUp(300);
//             });
//             $alertActive = 0;
// //            $('#notifications-btn').addClass('btn-default');
//           }
//
//         }
//       });

      // $.ajax({
      //   url : "lookups/ajaxLookup.php",
      //   type: "GET",
      //   dataType: "json",
      //   data : {
      //     fromClass : "AIRS_Notify",
      //     type : "fetchAllGrouped",
      //   },
      //   success:function(data, textStatus, jqXHR) {
      //     $('#tabs-1 .list-group').html('');
      //     $.each( data, function( index, value ){
      //       listData = '';
      //       listData += '<li class="list-group-item list-group-item-sm list-group-item-' + value.module.module + '-' + value.module.id + '  ">';
      //       listData += '<input type="checkbox" class="parentDismissNotification" data-rowid="' + value.module.module + '-' + value.module.id + '" name="notification" value=""> <strong>' + value.module.module + ': ' + value.module.id + '</strong>';
      //
      //       listData += '<ul>';
      //       $.each( value.data, function( dataIndex, dataValue ){
      //         listData += '<li class="list-group-item list-group-item-sm list-group-item-' + dataValue.type + ' "> <input type="checkbox" class="dismissNotification" data-rowmoduleid="' + value.module.module + '-' + value.module.id + '" data-rowid="' + dataValue.id + '" name="notification" value=""> ' + dataValue.message + '</li>'
      //       });
      //       listData += '</ul>';
      //       listData += '</li>'
      //       $('#tabs-1 .list-group').append(listData);
      //     });
      //   }
      // });

      // $.ajax({
      //   url : "lookups/ajaxLookup.php",
      //   type: "GET",
      //   dataType: "json",
      //   data : {
      //     fromClass : "AIRS_Notify",
      //     type : "fetchAll",
      //   },
      //   success:function(data, textStatus, jqXHR) {
      //     $('#tabs-2 .list-group').html('');
      //     $.each( data, function( index, value ){
      //         $('#tabs-2 .list-group').append('<li class="list-group-item list-group-item-sm list-group-item-' + value.type + ' "> <input type="checkbox" class="dismissNotification" data-rowid="' + value.id + '" name="notification" value=""> ' + value.message + '</li>');
      //     });
      //   }
      // });


      // $.ajax({
      //   url : "lookups/ajaxLookup.php",
      //   type: "GET",
      //   dataType: "json",
      //   data : {
      //     fromClass : "AIRS_Notify",
      //     type : "fetchAll",
      //     notificationType : "error",
      //   },
      //   success:function(data, textStatus, jqXHR) {
      //     $('#tabs-3 .list-group').html('');
      //     $.each( data, function( index, value ){
      //         $('#tabs-3 .list-group').append('<li class="list-group-item list-group-item-sm list-group-item-' + value.type + ' "><input type="checkbox" class="dismissNotification" data-rowid="' + value.id + '" name="notification" value=""> ' + value.message + '</li>');
      //     });
      //   }
      // });

    }

    /** initial load **/
    reloadNotifications ();






    });

  </script>