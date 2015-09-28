<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

$beanstalk = getBeanstalk();

while ($job = $beanstalk->reserve()) {

    //echo json_encode(['filename' => '00010_waxinggibbousmoon.jpg', 'sizes' => [ ['width' => 1024, 'height' => 768] ]]);

    $result = \Jobs\ResizeImagesJob::instance()->init(json_decode($job->getData(), true))->work();

    if ($result) {
        // Done with the job so delete it!
        $beanstalk->delete($job);
    } else {
        // bury the job
        $beanstalk->bury($job);
    }
}