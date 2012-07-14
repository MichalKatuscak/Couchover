<?php

/**
 * Console for Couchover Framework
 * 
 * @author      Richard Hutta & Michal KatuĹˇÄŤĂˇk
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
                
                $this->setStatus('PĹ™Ă­kaz nenalezen', 'error');
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
    
    abstract class Extension {
        
        /**
         * Get data with CURL
         * 
         * @param string $uri
         */
        private function curl($uri){
            
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$uri);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
            $data = curl_exec($ch);
            curl_close($ch);
            
            return $data; 
            
        }
        
        /**
         * Get data with file_get_contents
         * 
         * @param string $uri
         */
        private function fgc($uri){
            
           return file_get_contents($uri); 
           
        }
        
        /**
         * Get data
         * 
         * @param string $uri
         */
        private function getContentUrl($uri){
            
           if(extension_loaded("curl")){
               
              return $this->curl($uri);
              
           } else {
               
              return $this->fgc($uri);
              
           } 
           
        }
        
        /**
         * Exists package
         * 
         * @param string $name
         * @return boolean 
         */
        private function existPackpage($name){
            
           $pack = $this->getContentUrl('http://source.couchover.com/source/');
           $pack = explode("\n", $pack);
           
           foreach($pack as $p){
               
                $to = explode(' - ', $p);
                
                if($to[0] == $name){
                    
                    return $to;
                    
                }
                
           } 
           
           return false;
        }
        
        /**
         * Get packege from source.couchover.com
         * 
         * @param string $name 
         */
        protected function getPackpage($name){
            
            if($this->existPackpage($name)){
                
                $pack = $this->getContentUrl('http://source.couchover.com/'.$name.'.zip');
                
            } 
            
        }
        
        /**
         * Get ZIP files from http://files.couchover.com
         * 
         * @param array $file_names
         * @return array
         */
        protected function getZipFiles($file_names) {
            
            $files = Array();
            
            foreach ($file_names as $file_name) {
                
                $file_name = str_replace('/', '', $file_name);
                $file = @file_get_contents('http://files.couchover.com/'.$file_name.'.zip');
                
                if ($file) {
                    
                    file_put_contents('./temp/'.$file_name.'.zip', $file);
                    $files[] = $file_name;
                    
                }
                
            }
            
            return $files;
                
        }
        
        /**
         * UnZIP files from archive 
         * 
         * @param array $files
         * @return bool
         */
        protected function unZipFiles($files) {
            
            $return = true;
            
            foreach ($files as $file_name) {
                
                $zip = zip_open('./temp/'.$file_name.'.zip');
                
                while ($entry = zip_read($zip)) {
                    
                    $entry_name = zip_entry_name($entry);
                    
                    if (!file_exists($entry_name) && substr($entry_name,0,-1).'/' == $entry_name) {
                        
                        $dir = '';
                        foreach (explode('/',$entry_name) as $name) {
                            
                            $dir .= $name.'/';
                            if (!file_exists($dir)) {
                                
                                mkdir($dir, 0777);
                            
                            }
                            
                        }
                        
                    } elseif (substr($entry_name,0,-1).'/' != $entry_name) {
                        
                        $file = zip_entry_read($entry, zip_entry_filesize($entry));
                        
                        if (!file_put_contents($entry_name, $file)) {
                            
                           $return = false;
                           
                        }
                        
                    }
                    
                }
                
            }
            
            return $return;
            
        }
        
        
    }
    
    final class Source extends Extension implements \Console\Interfaces\iFunction {

        /** @var string */
        private $main_option = 'install';
        
        /** @var array */
        private $options;
        
        /**
         * Set main option
         * 
         * @param array $parameters
         */
        public function __construct($parameters){
            
            $this->main_option = $parameters[0];
            unset($parameters[0]);
            $this->options = array_values($parameters);
            
        }
        
        /**
         * Run modul
         * 
         * @return string
         */
        public function run() {
            
            switch ($this->main_option) {
                
                case 'install':
                    
                    $files = $this->getZipFiles($this->options);
                    
                    if ($this->unZipFiles($files)) {
                        return 'Instalace modulĹŻ <b>' . implode(' ', $this->options) . '</b> probÄ›hla ĂşspÄ›ĹˇnÄ›. {success}';
                    } else {
                        return 'Nastala chyba.';
                    }
                    
                    break;
                    
                case 'update':
                    
                    $files = $this->getZipFiles($this->options);
                    if ($this->unZipFiles($files)) {
                        return 'NovĂ© verze modulĹŻ <b>' . implode(' ', $this->options) . '</b> byly nainstalovĂˇny. {success}';
                    } else {
                        return 'Nastala chyba.';
                    }
                    
                    break;
                
                default:
                    
                    return 'Volba <b>' . $this->main_option . '</b> nebyla nalezena. {warning}';
                    
            }
            
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