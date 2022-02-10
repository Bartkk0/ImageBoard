<?php
function thumbnail($file){
    error_log($file);
    $imagick = new Imagick(realpath($file));
    $imagick->setImageFormat('jpeg');
    $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
    $imagick->setImageCompressionQuality(90);
    $imagick->thumbnailImage(256, 256, true, false);

    if (file_put_contents($file . '_thumb.jpg', $imagick) === false) {
        throw new Exception("Could not put contents.");
    }
}