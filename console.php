<?php

/**
 * Console for Couchover Framework
 * 
 * @author      Richard Hutta & Michal Katuščák
 * @copyright   2012
 * @package     Couchover
 */

namespace Console\Interfaces {
    
    interface iConsole {

        function __construct($string);

    }
    
    interface iFunction {

        function __construct($parameters);
        function run();

    }
    
}

namespace Console {

    final class Main implements \Console\Interfaces\iConsole {

        private $command = array();

        /**
         * Call maincommand class
         * 
         * @param string $string
         * @return bool 
         */
        public function __construct($string){
            
            $this->setHeaders();
            $this->command = $this->parseCommand($string);
            
            if ($this->command[0] == '') {
                
                return false;
                
            }
            
            $backcall = '\\Console\\Functions\\'.$this->command[0];
            
            if (class_exists($backcall)) {
                
                $class = new $backcall($this->command[1]);

                $status = $class->run();
                $status = $this->parseStatus($status);
                $this->setStatus($status[0], $status[1]);
                
            } else {
                
                $this->setStatus('Příkaz nenalezen', 'error');
                return false;
                
            }
            
        }
        
        /**
         * Set all headers 
         */
        private function setHeaders() {
            
            header('Content-type:text/html;charset:utf-8');
            
        }
        
        /**
         * Get message and message type
         * 
         * @param string $string
         * @return array 
         */
        private function parseStatus($status) {
            
            $pattern = '/\{[a-z]+\}/';
            preg_match($pattern, $status, $matches);
            $message_type = str_replace(array('{','}'),'',$matches[0]);
            $message = str_replace($matches[0],'',$status);
            return array($message_type, $message);
            
        }

        /**
         * Get maincommand and parameters from string
         * 
         * @param string $string
         * @return array 
         */
        private function parseCommand($string) {
            
            $pattern = '(?:[ \t\n\r\x0B\x00\x{A0}\x{AD}\x{2000}-\x{200F}\x{201F}\x{202F}\x{3000}\x{FEFF}]|&nbsp;|<br\s*\/?>)+';
            $string = preg_replace('/^' . $pattern . '|' . $pattern . '$/u', '', $string);
            $string = urldecode($string);
            $string = htmlspecialchars($string);
            
            $explode = explode(' ', $string);
            $maincommand = $explode[0];
            unset($explode[0]);
            return array($maincommand, array_values($explode));
            
        }

        /**
         * Set status from
         * 
         * @param string $string
         * @param string $type 
         */
        private function setStatus($string, $type){
            
            var_dump($string, $type);
            
        }

    }
}

namespace Console\Functions {
    
    final class Install implements \Console\Interfaces\iFunction {

        private $message = '';
        
        /**
         * Set message
         * 
         * @param array $parameters
         */
        public function __construct($parameters){
            
            $this->message = $parameters[0];
            
        }
        
        /**
         * Run modul
         * 
         * @return string
         */
        public function run(){
            
            return 'Instalace '.$this->message.' proběhla v pořádku {warning}';
            
        }
        
    }
}

namespace Page {

    if (isset($_GET['send'])) { // Is command
        
        $console = new \Console\Main($_GET['send']);
        exit;
        
    }

    ?><!doctype html>
    <html>
        <head>
            <meta charset="utf-8"/>
            <title>Console</title>
    <style>
    body {
        font-family: Calibri, Arial, sans-serif;
    }

    .text {
        outline:none;
    }
    </style>
    <script>
    var Console = {}

    /**
     * Set editable console and create first line
     */
    Console.init = function ()
    {
        var dollars = document.getElementsByClassName('dollar');
        for(var i in dollars) {
            dollars[i].innerText = '#';
        }
        var line = this.getLine();
        line.contentEditable = true;
        line.spellcheck = false;
        line.innerHTML = '&nbsp;';
        line.focus();
        line.onkeypress = function (e) {
            if (e.keyCode == 13) {
                this.contentEditable = false;
                Console.sendLine(this.innerText);
                return false;
            }
        }
    }

    /**
     * Send command and print response and create new editable line
     */
    Console.sendLine = function (text)
    {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'console.php?send='+text);
        xhr.onreadystatechange = function () {
            if(xhr.readyState==4 && xhr.status==200){
                if (xhr.responseText != '') {
                    var body = document.getElementsByTagName('body')[0];
                    body.innerHTML += '<div class="line">'+xhr.responseText+'</div>';
                }
                Console.addLine();
            }
        }
        xhr.send(null);
    }

    /**
     * Get last editable line
     */
    Console.getLine = function ()
    {
        var body = document.getElementsByTagName('body')[0];
        var divs = body.childNodes;
        var lastDiv = true;
        for (var i in divs) {
            if(divs[i].tagName == 'DIV') {
                lastDiv = divs[i];
            }
        } 
        return (lastDiv.lastChild);
    }

    /**
     * Create new editable line
     */
    Console.addLine = function ()
    {
        var body = document.getElementsByTagName('body')[0];
        body.innerHTML += '<div class="line"><span class="dollar"></span> <span class="text"> </span></div>';
        this.init();
    }
    </script>
        </head>
        <body onLoad="Console.init()">
            <div class="line">Couchover Console</div>
            <div class="line"><span class="dollar"></span> <span class="text"> </span></div>
        </body>
    </html>
<?php
}
?>