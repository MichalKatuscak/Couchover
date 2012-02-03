<?php
 
namespace Couchover;
 
// {{{ Debugger
 
/**
 * Debugger.class.php
 *
 * Class to debugging application.
 *
 * @author     Michal Katuščák <michal@katuscak.cz>
 * @license    Creative Commons 3.0 http://creativecommons.org/licenses/by/3.0/
 */
final class Debugger 
{
    // {{{ properties
 
    /**
     * Level of debugging
     * level==0 -> Do not show or loging errors 
     * level==1 -> Loging errors
     * level==2 -> Show errors  
     *
     * @var int
     */
    private static $level = 0;
 
    /**
     * File for loging errors
     *
     * @var string
     */
    private static $file_log_errors = '';
 
    /**
     * Timer - start time
     *
     * @var array
     */
    private static $timer_start_time = Array();
 
    /**
     * Timer - end time
     *
     * @var array
     */
    private static $timer_end_time = Array();
 
    /**
     * Tests - passed or failed
     * 
     * $test = Array(
     *      0 => Array(1 || 0, 'Description') // pass || fail
     * );
     *
     * @var array
     */
    private static $tests = Array();
 
    // }}}

    // {{{ timerStart()
 
    /**
     * Start timer
     * 
     * @param string $name Timer name (for more measurements)
     */
    public static function timerStart ($name = 'Default') {
        self::$timer_start_time[$name] = microtime(TRUE);
    }
    
    // }}}

    // {{{ timerEnd()
 
    /**
     * End timer
     * 
     * @param string $name Timer name (for more measurements)
     */
    public static function timerEnd ($name = 'Default') {
        self::$timer_end_time[$name] = microtime(TRUE);
    }
    
    // }}}

    // {{{ timerEndAll()
 
    /**
     * End of all timers
     */
    private static function timerEndAll () {
        $start_times = self::$timer_start_time;
        foreach($start_times as $name=>$start_time) {
            if (!array_key_exists($name, self::$timer_end_time)) {
                self::$timer_end_time[$name] = microtime(TRUE);
            }
        }
    }
    
    // }}}

    // {{{ test()

    /**
     * Testing
     * 
     * @param string $name Name of test (isInt || isArray)
     * @param $subject
     * @param string $description Desription of test
     */
    public static function test ($name, $subject, $description) {
        switch ($name) {
            case 'isString':
                $pass = is_string($subject);
                break;
            case 'isInt':
                $pass = is_int($subject);
                break;
            case 'isArray':
                $pass = is_array($subject);
                break;
            default:
                $pass = false;
        }

        self::$tests[] = Array($pass, $description);

        return $pass;
    }

    // }}}

    // {{{ error()

    /**
     * User error
     * 
     * @param string $message Error message
     * @param constant $level E_USER_NOTICE || E_USER_WARNING || E_USER_ERROR || E_USER_DEPRECATED
     */
    public static function error ($message, $level = E_USER_NOTICE) {
        $last_error = next(debug_backtrace());
        $nlbr = (self::$level==1) ? "\n" : "\n<br>";
        $message .= ' in <strong>'.$last_error['file'].'</strong> on line <strong>'.$last_error['line'].'</strong>';
        user_error($message . $nlbr . 'From Debugger', $level);
    }

    // }}}

    // {{{ setErrors()
 
    /**
     * Setting display errors
     * 
     * @param int $level Configuration parameters
     */
    private static function setErrors ($level, $file_log) {
        error_reporting(E_ALL);
        switch ($level) {
            case 1:     // Log errors
                ini_set('display_errors','Off');
                ini_set('error_log', $file_log);
                break;
            case 2:     // Show errors
                ini_set('display_errors','On');
                break;
            default:    // Hide errors
                error_reporting(0);
        }
    }
    
    // }}}

    // {{{ configuration()

    /**
     * Configuration of debugger
     * 
     * @param array $config Configuration parameters
     */
    public static function configuration ($config) {
        if (is_array($config)) {
            self::$level = intval($config['level']);
            self::$file_log_errors = file_exists($config['file_log_errors']) ? $config['file_log_errors'] : '';
        }

            // Set errors
        self::setErrors(self::$level, self::$file_log_errors);
    }

    // }}}

    // {{{ view()
 
    /**
     * Show debug panel
     */
    public static function view () {
            // Get tests
        $tests_count = count(self::$tests);
        $tests_passed = 0;
        $tests_failed = 0;
        for($i=0;$i<$tests_count;$i++) {
            if (self::$tests[$i][0]) {
                $tests_passed++;
            } else {
                $tests_failed++;
            }
        }
        
            // End of all timers
        self::timerEndAll();
        
            // Get runtime
        $time = self::$timer_end_time['Default'] - self::$timer_start_time['Default'];
        
        if (self::$level == 2) {

            $script = '<script>';
            $script .= '
            couchover = {
                debugger: {
                    showTests: function () {
                        document.getElementById("couchover-debugger-tests").style.display = "block";
                    },
                    hideTests: function () {
                        document.getElementById("couchover-debugger-tests").style.display = "none";
                    }
                }
            }
            ';
            $script .= '</script>';

            $style = '<style>';
            $style .= '
            @font-face {font-family: "Istok Web"; font-style: normal; font-weight: 400; src: url("http://view.couchover.com/fonts/IstokWeb-Regular.ttf");}
            #couchover-debugger {
                font-family: "Istok Web", Verdana, Sans-serif;
                font-size: 80%;
                width: 100%;
                padding: 5px;
                border-top: 1px #999 solid;
                position: fixed;
                left: 0;
                bottom: 0;
                text-align: center;	
                background-color: rgba(255,255,255,0.9);
            }
            #couchover-debugger-tests {
                width:100%;
                height:100%;
                position:fixed;
                top:0;
                left:0;
                display: none;
                background-color: rgba(10,10,10,0.5);
            }
            #couchover-debugger-tests-in {
                font-family: "Istok Web", Verdana, Sans-serif;
                font-size: 80%;
                width: 500px;
                max-height: 500px;
                border: 1px #999 solid;
                margin: 50px auto;
                text-align: center;	
                background-color: rgba(255,255,255,0.9);
                overflow: auto;
            }
            #couchover-debugger-tests-table {
                width: 100%;
            }
            #couchover-debugger-tests-table td {
                padding: 2px 4px;
                font-size: 90%;
            }
            #couchover-debugger-tests-table td:first-child {
                text-align: left;
                width: 400px;
            }
            .couchover-debugger-tests-passed {
                background-color: #9f9;
            }
            .couchover-debugger-tests-failed {
                background-color: #f99;
            }
            .couchover-debugger-tests-passed:hover {
                background-color: #5f5;
            }
            .couchover-debugger-tests-failed:hover {
                background-color: #f55;
            }
            ';
            $style .= '</style>';

            $html = '<div id="couchover-debugger">';
            $html .= '<a href="javascript: couchover.debugger.showTests()">Proběhlo ' . $tests_count . ' testů (' . $tests_failed . ' neúspěšně)</a>, ';
            $html .= 'Čas běhu: ' . (sprintf("%0.6f", $time) . 's');
            $html .= '</div>';
            
                // Show tests
            $html .= '<div id="couchover-debugger-tests" onClick="couchover.debugger.hideTests()" style="cursor:pointer;"><div id="couchover-debugger-tests-in" onclick="event.cancelBubble=true;if (event.stopPropagation) event.stopPropagation();"><table id="couchover-debugger-tests-table">';
            for ($i=0;$i<$tests_count;$i++) {
                $pass = (self::$tests[$i][0] == true)?'passed':'failed';
                $html .= '<tr class="couchover-debugger-tests-'.$pass.'"><td>' . htmlspecialchars(self::$tests[$i][1]) . '</td><td>'.$pass.'</td></tr>';
            }
            $html .= '</table></div></div>';

            return $script . $style . $html;
        }

        return 0;
    }
    
    // }}}

}
 
// }}}