<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

use router\Router;
use handler\Handlers;
use handler\http\HttpStatusHandler;
use handler\view\ViewHandler;
use handler\http\HttpStatus;
use view\TemplateView;

include __DIR__ . '/../vendor/autoload.php';

// all dates in UTC timezone
date_default_timezone_set("UTC");
ini_set('date.timezone', 'UTC');

$router = new Router();
$handlers = Handlers::get();
$handlers->add(new ViewHandler());
$handlers->add(new HttpStatusHandler());

/**
 * Fetch all posts
 *
 * @return Json array with all posts
*/
$router->route('encode', '/', function () {
    return new TemplateView(__DIR__ . '/encodeimage.html');
})
->route('upload', '/', function () {
    $options = new upload\UploadOptions();
    $options->setAllowOverwrite(true)
      ->setMaxFiles(1)
      ->setMaxSize(1024*200); // 200 KB
    
    $manager = new upload\UploadManager($options);
    try {
        $manager->validateUploads();
    } catch (\upload\UploadException $ex) {
        return new TemplateView(__DIR__ . '/encodeimage.html', array('error'=> $ex->getMessage()));
    }
    $files = $manager->getUploadedFiles();
    $encoded = '';
    /* @var $file \upload\UploadedFile */
    if ($files->count()) {
        $file = $files->offsetGet(0);
        $encoded = base64_encode(file_get_contents($file->getPathname()));
    }
    
    $size = $file->getSize();
    $base64size = mb_strlen($encoded);
    $percentage = ($base64size/$size) * 100;
    
    return new TemplateView(__DIR__ . '/encodeimage.html', array(
        'encoded'=>$encoded,
        'encodedTruncated'=>substr($encoded, 0, 10).'...'.substr($encoded, -10),
        'filename'=>$file->getOriginalFilename(),
        'size'=> round($size/1024, 2),
        'base64size'=>round($base64size/1024, 2),
        'percentage'=>round($percentage, 2),
        'mime'=>$file->getMimeType(),
        'isimage'=>preg_match('#^image\/.*#', $file->getMimeType())
    ));
}, 'POST');

$result = $router->match($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

$handler = $handlers->getHandler($result);

if ($handler) {
    $handler->handle($result);
} else {
    $error = new HttpStatus(404, ' ');
    $handler = $handlers->getHandler($error);
    $handler->handle($error);
}
