<?php
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
$router->route('encode', '/image', function () {
    return new TemplateView(__DIR__ . '/encodeimage.html');
})
->route('upload', '/image', function () {
    $options = new upload\UploadOptions();
    $options->setAllowOverwrite(true)
      ->setMaxFiles(1)
      ->setMaxSize(200000)
      ->setMimetypes(array('imgage/png','image/gif','image/jpg','image/jpeg','image/pjpeg'));
    
    $manager = new upload\UploadManager();
    $files = $manager->getUploadedFiles();
    $encoded = '';
    /* @var $file \upload\UploadedFile */
    if ($files->count()) {
        $file = $files->offsetGet(0);
        $encoded = base64_encode(file_get_contents($file->getPathname()));
    }
    
    return new TemplateView(__DIR__ . '/encodeimage.html', array(
        'encoded'=>$encoded,
        'filename'=>$file->getOriginalFilename(),
        'size'=> round($file->getSize()/1024, 2),
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
