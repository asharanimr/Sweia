<?php

    /**
     * @author Joshua Kissoon
     * @date 20121220
     * @description Theme modification class,
     *              This is an abstraction class on the template class to let modules modify the content of the theme
     */
    class Theme
    {

        private $templates = array(
            "html" => "html",
            "main" => "main",
        );
        private $stylesheets = array();
        private $scripts = array();
        private $regions = array();
        private $variables = array();
        private $head = array();
        public $sitetitle;

        public function __construct()
        {
            $this->variables['sitename'] = JSmart::getSiteName();
        }

        public function setHtmlTemplate($file)
        {
            /* Function used to change the html template file */
            $name = rtrim($file, ".tpl.php");
            $file = $name . ".tpl.php";
            if (is_file(TEMPLATES_PATH . $file))
            {
                $this->templates['html'] = $name;
            }
            else
            {
                /* Throw some error */
            }
        }

        public function setMainTemplate($file)
        {
            /* Function used to change the main template file */
            $name = rtrim($file, ".tpl.php");
            $file = $name . ".tpl.php";
            if (is_file(TEMPLATES_PATH . $file))
            {
                $this->templates['main'] = $name;
            }
            else
            {
                /* Throw some error */
            }
        }

        public function addCss($params, $weight = 10)
        {
            /* Takes in an array of parameters of a stylesheet and stores it */
            while (isset($this->stylesheets[$weight]))
            {
                $weight++;
            }
            $this->stylesheets[$weight] = $params;
        }

        private function renderCssFiles()
        {
            /* Returns the HTML code for all the site required stylesheets */
            ksort($this->stylesheets);
            return HTML::stylesheets($this->stylesheets);
        }

        public function addScript($params, $weight = 10)
        {
            /* Takes in an array of parameters of a script file and stores it */
            while (isset($this->scripts[$weight]))
            {
                $weight++;
            }
            $this->scripts[$weight] = $params;
        }

        public function renderScriptFiles()
        {
            /* Returns the HTML code for all the scripts stored */
            ksort($this->scripts);
            return HTML::scripts($this->scripts);
        }

        public function setContent($region, $content)
        {
            /* Dummy function for mixed up function name calls */
            $this->addContent($region, $content);
        }

        public function addContent($region, $content)
        {
            /* Add content to a specific region within the theme */
            if ($region == "head")
            {
                $this->head[] = $content;
                return true;
            }
            if (!is_array(@$this->regions[$region]))
            {
                $this->regions[$region] = array();
            }
            $this->regions[$region][] = $content;
        }

        public function clearRegion($region)
        {
            /*
             * Clears all the content currently in this region
             */
            $this->regions[$region] = array();
        }

        public function setVariable($var, $value)
        {
            /* Sets a variable to be loaded */
            $this->variables[$var] = $val;
        }

        public function setSiteTitle($title)
        {
            $this->sitetitle = $title;
        }

        public function render()
        {
            /* Render the theme */
            /*
             * Load main file
             * Set Regions variables
             * Save the template output
             */
            $main = new Template(TEMPLATES_PATH . $this->templates['main']);
            foreach ($this->regions as $region => $region_array)
            {
                /*
                 * The different content added to the regions are initially stored in an array
                 * Now we implode each region into a string
                 */
                $main->$region = implode("", $region_array);
            }
            foreach ($this->variables as $var => $value)
            {
                /*
                 * Add the variables to the template
                 */
                $main->$var = $value;
            }

            /*
             * Load HTML template
             * Put css, scripts, header_tags and main into html variables
             * Publish this template
             */
            $html = new Template(TEMPLATES_PATH . $this->templates['html']);
            $html->stylesheets = $this->renderCssFiles();
            $html->scripts = $this->renderScriptFiles();
            $html->content = $main->parse();
            $html->stylesheets = $this->renderCssFiles();
            $html->title = ($this->sitetitle) ? $this->sitetitle : JSmart::getSiteName();
            $html->head = implode("", $this->head);
            $html->publish();
        }

    }

    /* Initialize the theme */
    $THEME = new Theme();
    