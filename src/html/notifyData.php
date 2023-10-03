<?php
/*
  AE-ICT source module
  Author  						: $Author: rm $
 	Laatste aanpassing	: $Date: 2015/11/04 11:42:40 $
 	File Versie					: $Revision: 1.3 $

 	$Log: notifyData.php,v $
 	Revision 1.3  2015/11/04 11:42:40  rm
 	3897
 	
 	Revision 1.2  2015/06/26 14:09:27  rm
 	notify
 	
 	Revision 1.1  2015/06/26 12:47:16  rm
 	Notify
 	

*/
include_once("wwwvars.php");
$notifier = new AIRS_Notify();

//$notifier->addRow('123', 'hello dit is een melding', array(
//  'ttl' => '2d'
//));

$template['inlineStyle'] = " 
  #notify-box {
    width: 300px;
    right: 1%;
    bottom: 1%;
    position:absolute!important;
  }

  fieldset#notificationHolder {
    padding:0px;
    background-image: -webkit-gradient(linear, top, bottom, color-stop(0, #D4D1BF), color-stop(1, #EFEFEF));
    background-image: -ms-linear-gradient(top, #D4D1BF, #EFEFEF);
    background-image: -o-linear-gradient(top, #D4D1BF, #EFEFEF);
    background-image: -moz-linear-gradient(top, #D4D1BF, #EFEFEF);
    background-image: -webkit-linear-gradient(top, #D4D1BF, #EFEFEF);
    background-image: linear-gradient(to bottom, #D4D1BF, #EFEFEF); 
    box-shadow: 5px 5px 10px #808080
  }

  #notificationHolder legend {
    width: calc(100% + 4px);
    background-image: none;
    background-color: #333;
    border-bottom-color: #d9d9d9;
    color:white;

    -webkit-box-shadow: 0 1px 0 #ffffff inset;
    box-shadow: 0 1px 0 #ffffff inset;
    border-top-right-radius: 6px;
    border-top-left-radius: 6px;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;

    margin-left: -2px;
    padding:5px;
    padding-left:15px;
    font-weight: bold;

    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
  }
  .notificationTitleerror {
    background-color: darkred!important;
  }
  .notificationTitleinfo {
    background-color: darkblue!important;
  }
  .notificationTitlesuccess {
    background-color: darkgreen!important;
  }
  
  .notificationMsgerror {
    color: darkred!important;
  }
  .notificationMsginfo {
    color: darkblue!important;
  }
  .notificationMsgsuccess {
    color: darkgreen!important;
  }

  #notification-list {
    max-height: 145px;
    overflow-x:hidden;
    overflow-y: scroll;
  }

  .notification-msg {
    display: block;
    cursor: hand;
  }
";
$notifications = $notifier->getAll();


if( requestType('ajax') ) {
  $AETemplate = new AE_template();
  echo template('templates/ajax_head.inc', $template);
  
  if ( ! empty($notifications) ) {
    $higestType = $notifier->getHighestState($notifications);
    echo '
      <fieldset id="notificationHolder">
        <legend class="' . ( ! empty($higestType) ? 'notificationTitle'.$higestType : '' ) . '" id="notificationTitle">Notificaties <span style="float:right;" id="notificationCountdown"></span></legend>
        <div id="notification-list" style="padding:5px;">
    ';
      foreach ($notifications as $notification) {
        echo '<span class="notification-msg notificationMsg' . $notification['type'] . '" data-id="' . $notification['id'] . '" title="' . $notification['message'] . '" data-title="' . $notification['message'] . '">' . date("d-m H:i", strtotime($notification['add_date'])) . ' - ' . (strlen($notification['message']) > 35 ? substr($notification['message'],0,35)."..." : $notification['message']) . '</span>';
      }
    echo '</div></fieldset>';
  }
 ?>
 <script>
  
    $.fn.countdown = function (callback, duration, message) {

      // If no message is provided, we use an empty string
      message = message || "";
      // Get reference to container, and set initial content
      var container = $(this).html(duration + message);
      // Get reference to the interval doing the countdown
      var countdown = setInterval(function () {
          // If seconds remain
          if (--duration) {
              // Update our container\'s message
              container.html(duration + message);
          // Otherwise
          } else {
              // Clear the countdown interval
              clearInterval(countdown);
              // And fire the callback passing our container as `this`
              callback.call(container);   
          }
      // Run interval every 1000ms (1 second)
      }, 1000);
    };
    function reloadNotifications () {
      $("#notify-box").load("notifyData.php");
    }

    $(document).ready(function(){
      $("#notificationCountdown").countdown(reloadNotifications, 10, "s");
      
      $(".notification-msg").on("dblclick", function () {
        var element = $(this);
        $.ajax({
            url : "lookups/ajaxLookup.php",
            type: "GET",
            dataType: "json",
            data : {
              fromClass : "AIRS_Notify",
              type : "seenNotification",
              notificationId: $(this).data("id"),
              seen : 1
            },
            success:function(data, textStatus, jqXHR) {
              if ( data.saved == true ) {
                element.slideToggle();
              } else {
              }
            }
          });
      });
    });
  </script>
  <?php
  echo template('templates/ajax_voet.inc');
}