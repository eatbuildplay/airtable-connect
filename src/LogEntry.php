<?php

namespace AirtableConnect;

class LogEntry {

  public $timestamp;
  public $action;
  public $code;
  public $message;
  public $data = array();

  public function create( $timestamp, $code, $action, $message, $data ) {
    $this->timestamp  = $timestamp;
    $this->action     = $action;
    $this->code       = $code;
    $this->message    = $message;
    $this->data       = $data;
  }

}
