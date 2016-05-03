<?php

namespace Spot\ImageStore\Tests;


use Spot\ImageStore\Entity\ImageEntity;
use Spot\ImageStore\Files\ImageFile;
use Spot\ImageStore\ImageStorage;
use Tester\Assert;

require_once __DIR__ . '/bootstrap.php';

// create storage
$storeDir = __DIR__ . '/store';
$cacheDir = __DIR__ . '/cache';
$storage = new ImageStorage($storeDir, $cacheDir);

// test: remove
$meta = new ImageEntity();
$storage->add(new ImageFile(__DIR__ . '/sample-images/sample-landscape.jpg'), $meta);
Assert::true($storage->contains($meta));
$storage->remove($meta);
Assert::false($storage->contains($meta));

$storage->destroy();
