<?php
namespace Jobs;

use Imagick;

class ResizeImagesJob extends AbstractJob {

    const SOURCE_DIR = 'hd';
    const DEST_DIR = 'media';

    protected $sourceFilename;
    protected $convertSizes;
    protected $sourcePath;
    protected $destPath;

    public function init($data)
    {
        $this->sourceFilename = $data['filename'];
        $this->convertSizes = $data['sizes'];
        $this->sourcePath = configParam('source_dir');
        $this->destPath = configParam('result_dir');
        return $this;
    }

    public function work()
    {

        try {
            if (file_exists($this->sourcePath . $this->sourceFilename)) {

                $imageDir = $this->destPath . DIRECTORY_SEPARATOR . md5($this->sourceFilename) . DIRECTORY_SEPARATOR;

                if (!file_exists($imageDir)) {
                    mkdir($imageDir, 0777, true);
                } elseif (!is_writable($imageDir)) {
                    chmod($imageDir, 0777);
                }

                foreach ($this->convertSizes as $size) {
                    $destFilename = $size['width'] . 'x' . $size['height'] . '_' . $this->sourceFilename;

                    // Check if file with this size exists, if not, create scaled one
                    if (!file_exists($imageDir . $destFilename)) {
                        $this->scaleImage(
                            realpath($this->sourcePath . $this->sourceFilename),
                            $size['width'],
                            $size['height'],
                            $imageDir . $destFilename
                        );
                    }
                }
            }
        } catch (\ErrorException $e) {
            return false;
        }

        return true;
    }

    private function scaleImage($sourceFilePath, $width, $height, $resultFilePath)
    {
        $imagick = new Imagick($sourceFilePath);
        $imagick->scaleImage($width, $height, true);
        return $imagick->writeImage($resultFilePath);
    }
}