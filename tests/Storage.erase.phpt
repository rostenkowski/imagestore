<?php

namespace Rostenkowski\ImageStore\Tests;


use Nette\Utils\Finder;
use Rostenkowski\ImageStore\Entity\ImageEntity;
use Rostenkowski\ImageStore\Files\ImageFile;
use Rostenkowski\ImageStore\ImageStorage;
use Rostenkowski\ImageStore\Requests\ImageRequest;
use Tester\Assert;

require_once __DIR__ . '/bootstrap.php';

// create storage
$storeDir = __DIR__ . '/store';
$cacheDir = __DIR__ . '/cache';
$storage = new ImageStorage($storeDir, $cacheDir);

$meta = new ImageEntity();
$storage->add(new ImageFile(__DIR__ . '/sample-images/sample-landscape.jpg'), $meta);
$storage->link(new ImageRequest($meta));

Assert::equal(1, Finder::findDirectories('*')->in($storeDir)->count());
Assert::equal(1, Finder::findDirectories('*')->in($cacheDir)->count());

$storage->erase();

Assert::equal(0, Finder::findDirectories('*')->in($storeDir)->count());
Assert::equal(0, Finder::findDirectories('*')->in($cacheDir)->count());

$storage->destroy();
