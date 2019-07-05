<?php
    namespace HTTP;

    class Parser {
        public static function ParseGet($url = false, $toParse = array()) {
            if (!$url || empty($toParse)) return $url;

            $url .= "?" . http_build_query($toParse);

            return $url;
        }

        public static function ParseCurlFile($source_file = false) {
            if (!$source_file || !file_exists($source_file)) return false;

            $cFile = '@' . realpath($source_file);
            if (function_exists('curl_file_create'))  // php 5.5+
              $cFile = curl_file_create($source_file);

            return $cFile;
        }

        public static function ParseHeader($header = false) {
            if(!$header) return array();

            $to_iterate = explode("\r\n", $header);

            $beauty_header = array();
            foreach ($to_iterate as $i => $line) {
                if(empty($line)) continue;

                if(strpos($line, 'HTTP') !== false)
                    $beauty_header['Protocol-Code'] = $line;
                else if(strpos($line, ": ") !== false) {
                    list ($key, $value) = explode(': ', $line);

                    $beauty_header[$key] = $value;
                }
            }

            return $beauty_header;
        }
    }
?>
