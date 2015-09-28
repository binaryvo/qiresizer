<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

convertSizes($sizesWQXGA);
convertSizes($sizesFullHD);

$beanstalk = getBeanstalk();

$sourceDirPath = realpath($config['source_dir']);
$files = scandir($sourceDirPath);

foreach($files as $filename) {
    if (!empty($filename) && $filename != '.' && $filename != '..') {

        $beanstalk->put(json_encode(array(
            'filename' => $filename,
            'sizes' => (isWQXGAFile($sourceDirPath . DIRECTORY_SEPARATOR . $filename)) ?
                array_merge($sizesFullHD, $sizesWQXGA) :
                $sizesFullHD
        )));

    }
}