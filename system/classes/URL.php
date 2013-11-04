<?php

    /**
     * @author Joshua Kissoon
     * @date 20130323
     * @descriptin A class that is used to manipulate any URL
     */
    class URL
    {

       private $base_url;
       public $args = array();

       public function __construct($url)
       {
          /* Get the URL sections */
          $url = explode("?", $url);
          $this->base_url = $url[0];

          /* Get the URL args */
          $args = rtrim(ltrim(@$url[1], "&"), "&"); // Remove extra & from the start and end of the URL 
          if (@$args)
          {
             $parts = explode("&", @$args);
             foreach ((array) $parts as $part)
             {
                $part = explode("=", $part);
                $this->args[$part[0]] = $part[1];
             }
          }
       }

       public function addArg($title, $value)
       {
          /*
           * Add a new argument to the URL
           */
          $this->args[$title] = $value;
       }

       public function getURL()
       {
          $args = array();
          foreach ($this->args as $title => $value)
             $args[] = "$title=$value";

          $url_args = implode("&", $args);
          return $this->base_url . "?" . $url_args;
       }

    }