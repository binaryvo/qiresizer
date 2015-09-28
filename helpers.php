<?php

function configParam($key) {
    global $config;
    return isset($config[$key]) ? $config[$key] : null;
}

function convertSizes(&$array) {
    array_walk($array, function(&$item) {
        $size = explode("x", $item);
        if (count($size) == 2) {
            $item = array('width' => $size[0], 'height' => $size[1]);
        }
    });
}

function isWQXGAFile($filename) {
    try {
        $image = new Imagick($filename);
        $data = $image->getImageGeometry();
    } catch (ImagickException $e) {

    }

    return (!empty($data) && $data['width'] == 2560 && $data['height'] == 1600);
}

function getBeanstalk() {
    $beanstalk = new Pheanstalk\Pheanstalk(configParam('beanstalk_host'));

    if (configParam('beanstalk_tube') == 'default') {
        $beanstalk->watch(configParam('beanstalk_tube'));
    } else {
        $beanstalk->watch(configParam('beanstalk_tube'))->ignore('default');
    }
    return $beanstalk;
}