<?php
function loader($class)
{
    $file = $class . '.php';
    if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . '../src'. DIRECTORY_SEPARATOR . $file)) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '../src'. DIRECTORY_SEPARATOR . $file;
    }else if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . '../test'. DIRECTORY_SEPARATOR . $file)){
      require_once file_exists(__DIR__ . DIRECTORY_SEPARATOR . '../test'. DIRECTORY_SEPARATOR . $file);
    }
}
spl_autoload_register('loader');

include __DIR__ . '/../vendor/autoload.php';