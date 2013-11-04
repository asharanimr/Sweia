<?php

    /**
     * @author Joshua Kissoon
     * @date 20121211
     * @description Class that handles pagination throughout the website
     */
    class JPager
    {
       /* Pageination variables */

       private $rows_per_page = 20, $total_records, $num_pages, $current_page = 1;
       public $limit_query = '', $links = array(), $links_html = "";
       public $offset; /* Current offset to be used in SQL query to start row number retrieval from */
       public $delta = 4;  // Number of page links to display before and after the current page

       /* URL Variables */
       private $url = "";   // The URL to add to the pager links
       private $urlVar = "pager_page";   // The Pager class url variable to use

       public function __construct($params)
       {
          /*
           * Setup the pager query and links
           * @params - all parameters are in the $params array containing
           *  -> total_records - the total records to be displayed
           *  -> rows_per_page - the total records to be displayed per page
           *  -> current_page - the current page to be shown
           *  -> delta - Number of page links to display before and after the current page
           *  -> retHtml - Whether to return the html or an array of links
           *  -> htmlOptions - Array with html options for the html links
           *  -> url - URL of the page we are on
           *  -> urlVar - The page number var to append to the url
           */

          /* Setting basic variables */
          if (@$params['total_records'])
             $this->total_records = $params['total_records'];
          else
             return false;
          $this->rows_per_page = (@$params['rows_per_page']) ? $params['rows_per_page'] : $this->rows_per_page;
          $this->current_page = (@$params['current_page'] > 0) ? $params['current_page'] : $this->current_page;
          $this->delta = (@$params['delta'] > 0) ? $params['delta'] : $this->delta;
          $this->retHtml = isset($params['retHtml']) ? $params['retHtml'] : false;
          $this->htmlOptions = is_array(@$params['htmlOptions']) ? $params['htmlOptions'] : array();
          $this->total_records = $params['total_records'];

          /* Setting URL variables */
          $this->url = ($params['url']) ? $params['url'] : $this->url;
          if (strpos($this->url, "?") === false)
             $this->url = $this->url . "?";
          $this->urlVar = ($params['urlVar']) ? $params['urlVar'] : $this->urlVar;

          /* Compute paginated values */
          $this->calculatePages();
          if ($this->num_pages < 2)
          {
             /* If we have 1 or less pages here, there is no sense in having pager intervene with the code */
             return false;
          }
          $this->updateQuery();
          $this->buildLinks();
          if (@$params['retHtml'] === true)
             $this->generateLinksHtml();
       }

       private function calculatePages()
       {
          /* Calculates the number of pages of records we will have */
          $this->num_pages = ceil($this->total_records / $this->rows_per_page);
       }

       private function updateQuery()
       {
          /* Based on the # rows per page and current page we generate the updated SQL */
          if ($this->current_page > $this->num_pages)
             $this->current_page = $this->num_pages;
          if ($this->current_page < 1)
             $this->current_page = 1;

          /* calculate offset bases on rows per page and then update the query */
          $this->offset = ($this->current_page - 1) * $this->rows_per_page;
          $this->limit_query = " LIMIT {$this->offset}, {$this->rows_per_page}";
       }

       public function buildLinks()
       {
          /*
           * This method will build pagination links
           */
          $this->links = array();
          $starting_offset = (($this->current_page - $this->delta) > 0) ? ($this->current_page - $this->delta) : 1;
          $ending_offset = (($this->current_page + $this->delta) <= $this->num_pages) ? ($this->current_page + $this->delta) : $this->num_pages;
          for ($i = $starting_offset; $i <= $ending_offset; $i++)
          {
             if (($this->current_page - $i) == 1)
             {
                /* If this is the previous item, insert a previous link */
                $prev = "<a href='$this->url&$this->urlVar=$i'><<</a>";
                $this->links = array_merge(array("prev" => $prev), $this->links);
             }
             else if (($this->current_page - $i) == -1)
             {
                /* If this is the next item, save it to insert at the end of the array */
                $next = "<a href='$this->url&$this->urlVar=$i'>>></a>";
             }
             $this->links[$i] = "<a href='$this->url&$this->urlVar=$i'>$i</a>";
          }
          if (valid(@$next))
             $this->links["next"] = $next;
       }

       public function generateLinksHtml()
       {
          $class = (@$this->htmlOptions['class']) ? $this->htmlOptions['class'] : "pager-link";
          $this->links_html = "<ul id='pager-links-wrapper' class='clearfix'>";
          foreach ($this->links as $id => $link)
          {
             if ($id == $this->current_page)
                $this->links_html .= "<li class='$class $id current-page'>$link</li>";
             else
                $this->links_html .= "<li class='$class $id'>$link</li>";
          }
          $this->links_html .= "</ul>";
       }

    }