<?php

class AE_cls_ExchangeOnline extends AE_cls_Email{

  var $tenantID;
  var $clientID;
  var $clientSecret;
  var $Token;
  var $baseURL;
  var $error;

  function __construct()
  {
    global $__appvar;
    $config = new AE_config();
    $this->user = $_SESSION["USR"];

    $this->baseURL = 'https://graph.microsoft.com/v1.0/';

    $this->tenantID       = $__appvar['office365']['ddbExchangeTenantId'];
    $this->clientID       = $__appvar['office365']['ddbExchangeClientId'];
    $this->clientSecret   = $__appvar['office365']['ddbExchangeClientSecret'];
    $this->mailBox        = $__appvar['office365']['ddbExchangeMailbox'];

    $this->ownDomain      = trim(strtolower($config->getData("ddbOwnDomain")));

    $this->aeClsEmailInit();

    if ( empty ($this->tenantID) || empty ($this->clientID) || empty ($this->clientSecret) || empty ($this->mailBox) ) {
      return false;
    }

    $this->token = $this->getToken();
  }

  function getToken()
  {
    $request = 'client_id=' . $this->clientID . '&scope=https%3A%2F%2Fgraph.microsoft.com%2F.default&client_secret=' . $this->clientSecret . '&grant_type=client_credentials';
    $reply = $this->sendRequest('https://login.microsoftonline.com/' . $this->tenantID . '/oauth2/v2.0/token', $request);

    if ( $reply['code'] === 200 ) {
      $reply = json_decode($reply['data']);
      return $reply->access_token;
    } else {
      $reply = json_decode($reply['data'], true);
      $this->error = $reply['error'];
    }
  }

  function sendRequest($url, $fields, $headers = false)
  {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    if ($fields) curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    if ($headers) {
      $headers[] = 'Authorization: Bearer ' . $this->token;
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    return array('code' => $responseCode, 'data' => $response);
  }

  function sendGetRequest($URL)
  {
    $ch = curl_init($URL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->token, 'Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
  }

  function getAllMessages()
  {
    if ( ! $this->token) {
      $this->error[] = vt('Geen Token');
      return false;
    }
    $messageList = $this->sendGetRequest($this->baseURL . 'users/' . $this->mailBox . '/mailFolders/Inbox/Messages/?$top=1000');
    $messageList = json_decode($messageList);
    if (isset($messageList->error)) {
      $this->error[] = $messageList->error->code . ' ' . $messageList->error->message;
      return false;
    }

    $messageArray = array();
    $loopID = 0;
    foreach ($messageList->value as $mailItem) {
      $attachments = $this->sendGetRequest($this->baseURL . 'users/' . $this->mailBox . '/messages/' . $mailItem->id . '/attachments');
      $attachments = json_decode($attachments);
      $attachments = $attachments->value;
      if (count($attachments) < 1) {
        unset($attachments);
      } else {
        foreach ($attachments as $attachment) {
          if ($attachment->{'@odata.type'} == '#microsoft.graph.referenceAttachment') {
            $attachment->contentBytes = base64_encode('This is a link to a SharePoint online file, not yet supported');
            $attachment->isInline = 0;
          }
        }
      }

      $from = $mailItem->sender->emailAddress->address;
      $to = isset ($mailItem->toRecipients[0]->emailAddress->address) ? $mailItem->toRecipients[0]->emailAddress->address : null;

      $len = strlen($this->ownDomain);
      $fromOwnDomain = (substr($from, -$len) === $this->ownDomain);

      if ( $fromOwnDomain === true && ! empty ($to) ) {
        $from = $to;
      }

      $messageArray[$mailItem->id] = array(
        'id'                  => $mailItem->id,
        'index'               => $loopID,
        'sentDateTime'        => $mailItem->sentDateTime,
        'stamp'               => date('Y-m-d H:i:s', strtotime($mailItem->sentDateTime)),
        'subject'             => $mailItem->subject,
        'bodyPreview'         => $mailItem->bodyPreview,
        'importance'          => $mailItem->importance,
        'conversationId'      => $mailItem->conversationId,
        'isRead'              => $mailItem->isRead,
        'body'                => $mailItem->body->content,
        'sender'              => $mailItem->sender,
        'from'                => $from,
        'toRecipients'        => $mailItem->toRecipients,
        'ccRecipients'        => $mailItem->ccRecipients,
        'toRecipientsBasic'   => $this->basicAddress($mailItem->toRecipients),
        'ccRecipientsBasic'   => $this->basicAddress($mailItem->ccRecipients),
        'replyTo'             => $mailItem->replyTo,
        'attachments'         => isset($attachments) ? $attachments : null
      );
      $this->messageCount++;
      $this->rawMailArray[$mailItem->id]    = $this->sendGetRequest($this->baseURL . 'users/' . $this->mailBox . '/messages/' . $mailItem->id . '/$value');
      $this->headerArray[$mailItem->id]     = '';//$this->sendGetRequest($this->baseURL . 'users/' . $this->mailBox . '/messages/' . $mailItem->id . '/?$select=internetMessageHeaders');
      $this->messageArray[$mailItem->id]    = $messageArray[$mailItem->id];
      $this->bodyArray[$mailItem->id]       = $mailItem->body->content;
      $this->attachments[$mailItem->id]     = isset($attachments) ? $attachments : null;
      $loopID++;
    }

    return $messageArray;
  }

  function deleteEmail($id, $moveToDeletedItems = true)
  {
    if ( $moveToDeletedItems === true ) {
      $this->sendRequest($this->baseURL . 'users/' . $this->mailBox . '/messages/' . $id . '/move', '{ "destinationId": "deleteditems" }', array('Content-type: application/json'));
    } else {
      $this->sendDeleteRequest($this->baseURL . 'users/' . $this->mailBox . '/messages/' . $id);
    }
  }

  function sendDeleteRequest($URL)
  {
    $ch = curl_init($URL);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->Token, 'Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    echo $response;
  }

  function basicAddress($addresses)
  {
    $ret = array();
    foreach ($addresses as $address) {
      $ret[] = $address->emailAddress->address;
    }
    return $ret;
  }



  function populateInbox ()
  {
    $this->clearStore();
    $messages = $this->getAllMessages();
    foreach ( $messages as $index => $message ) {
      $this->storeMail($index);
    }
  }

  function getMessages ()
  {
    $this->user = "daemon";
    $this->clearStore();
    $messages = $this->getAllMessages();
    foreach ( $messages as $index => $message ) {
      $this->storeMail($index);
      $this->deleteEmail($index);
    }
  }



  function errorState()
  {
    if ( $this->error ) {
      return true;
    } else {
      return false;
    }
  }

  function lastStatus ()
  {
    return $this->error;
  }

}