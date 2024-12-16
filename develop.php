<?php

if(!function_exists("_d")){

    function _d(...$inputs) {
        $backtrace = debug_backtrace();
        $caller = $backtrace[0];

        if (isset($backtrace[0])) {
            $caller = $backtrace[0];

            $caller['file'] = str_replace("http://" , "" , str_replace("https://" , "" , home_url())) . '::' . str_replace( str_replace('/' , '' , ABSPATH) , '' , $caller['file']);
        }

        echo "<style>code>span>span:first-child{display:none} code>span>span:last-child{display:none}</style>";

        echo "<div dir='ltr' style='background-color: #eee;margin : 12px 0; padding: 10px; border-radius: 5px; font-family: monospace;'>";

        // Show the file and line number where the function was called
        echo "<strong>Called from file:</strong> " . htmlspecialchars($caller['file']) . " <strong>on line:</strong> " . htmlspecialchars($caller['line']) . "<br><br>";

        // Capture the var dump output
        ob_start();
        foreach($inputs as $index => $input){ $i = $index + 1;
            echo "\n#----- log $i -----#\n";
            var_dump($input);
        }
        $dump = ob_get_clean();

        // Highlight the var dump inside PHP tags
        $phpCode = "<?php\n" . $dump . "\n?>";
        highlight_string($phpCode);

        echo "</div>";
    }

}

if(!function_exists("_dd")){

    function _dd(...$inputs) {
        $backtrace = debug_backtrace();
        $caller = $backtrace[0];

        if (isset($backtrace[0])) {
            $caller = $backtrace[0];

            $caller['file'] = str_replace("http://" , "" , str_replace("https://" , "" , home_url())) . '::' . str_replace( str_replace('/' , '' , ABSPATH) , '' , $caller['file']);
        }

        echo "<style>code>span>span:first-child{display:none} code>span>span:last-child{display:none}</style>";

        echo "<div dir='ltr' style='background-color: #eee;margin : 12px 0; padding: 10px; border-radius: 5px; font-family: monospace;'>";

        // Show the file and line number where the function was called
        echo "<strong>Called from file:</strong> " . htmlspecialchars($caller['file']) . " <strong>on line:</strong> " . htmlspecialchars($caller['line']) . "<br><br>";

        // Capture the var dump output
        ob_start();
        foreach($inputs as $index => $input){ $i = $index + 1;
            echo "\n#----- log $i -----#\n";
            var_dump($input);
        }
        $dump = ob_get_clean();

        // Highlight the var dump inside PHP tags
        $phpCode = "<?php\n" . $dump . "\n?>";
        highlight_string($phpCode);

        echo "</div>";

        die;
    }

}

if(!function_exists("_log_call_hook")){
    function _log_call_hook( $handle ){

        $debug_info = [
            'handle' => $handle,
            'url' => $_SERVER['REQUEST_URI'],
            'method' => $_SERVER['REQUEST_METHOD'],
            'user_id' => is_user_logged_in() ? get_current_user_id() : 'Guest'
        ];

        error_log(print_r($debug_info, true));
    }

}