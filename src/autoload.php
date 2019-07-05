<?php
spl_autoload_register(function ($class) {
    // project-specific namespace prefix
    $prefix = 'OpenOfficeConverter\\';


    // base directory for the namespace prefix
    $baseDir = __DIR__ . '/';

    // get the relative class name
    $relativeClass = $class;

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = rtrim($baseDir, '/') . '/' . str_replace('\\', '/', $relativeClass) . '.php';

    // if the file exists, require it
    if (file_exists($file)) require $file;
});
