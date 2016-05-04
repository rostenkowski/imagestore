# imagestore

*High Performance Image Storage written in PHP*

[![Build Status](https://travis-ci.org/rostenkowski/imagestore.svg?branch=master)](https://travis-ci.org/rostenkowski/imagestore)
[![License](https://img.shields.io/badge/license-New%20BSD-blue.svg)](https://github.com/rostenkowski/imagestore/blob/master/LICENSE)

## Requirements

- PHP 5.6+
- Nette Framework 2.2+

For the full list of dependencies see the [`composer.json`](composer.json) file.

## Features
- The images are stored in a regular files in the given directory.
- The files are organized in a 2-level directory structure with maximum of 256² directories.
- The directory tree is well balanced thanks to the image hashes used for the directory path creation.
- The storage stores only one file even if the same image is stored multiple times, thus images should be
 deleted only after it is sure it is not referenced from other entities.
- The image thumbnails are created on demand and cached in the cache directory.

## API

The API documentation is located in the `docs/api` directory.

## Tests

The source code of the library is fully covered by [**Nette Tester**](https://tester.nette.org/) tests.

To run the **ImageStore** tests simply install the dependencies
using the [**Composer**](https://getcomposer.org/doc/00-intro.md#globally) and then run the `bin/run-tests.sh` script.

To check the code coverage see the `docs/code-coverage.html` file.

```bash
$ bin/setup.sh
$ bin/run-tests.sh
```

```
 _____ ___  ___ _____ ___  ___
|_   _/ __)( __/_   _/ __)| _ )
  |_| \___ /___) |_| \___ |_|_\  v2.0.x

PHP 5.6.20-1+deb.sury.org~trusty+1 (cgi-fcgi) | php-cgi -n -c .../tests/php.ini | 1 thread

................


OK (16 tests, 2.2 seconds)

```

## Usage

This simple example demonstrates how to use the **ImageStore** library in the [**Nette**](https://doc.nette.org/cs/2.3/quickstart) application
using the [**Doctrine**](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/tutorials/getting-started.html) library as ORM.

It assumes that the **Doctrine's EntityManager** is available trough the application DI container.

### Configuration

Image macros should be added to the [**Latte engine**](https://latte.nette.org/) as described
in [documentation](https://doc.nette.org/en/2.2/configuring#toc-latte):

```yaml
nette:
    latte:
        macros:
            - Spot\ImageStore\Macro\ImageMacro::install
```


### Entity

```php
<?php

namespace MyApp\Entities;

use Spot\ImageStore\Entity\ImageEntity;

/** @ORM\Entity */
class MyImageEntity extends ImageEntity
{
	/** @ORM\Id */
	private $id;

	public function getId()
	{
		return $this->id;
	}

}
```

### Presenter

```php
<?php

namespace MyApp\Presenters;

use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;
use Spot\ImageStore\ImageStorage;

class MyPresenter extends Presenter
{
	/** @var ImageStorage */
	private $images;

	/** @inject @var EntityManager */
    private $em;

    /** @persistent @var integer */
    private $avatar;

	protected function startup()
	{
		$this->images = new ImageStorage(__DIR__ . '/images', __DIR__ . '/cache');
		$this->template->__imageStore = $this->images; // this is important for the image macros
	}

	public function createComponentProfile()
	{
		$form = new Form();
		$form->addUpload('avatar');
		$form->addSubmit('save', 'Save changes');
		$form->onSuccess[] = function($form, $values) {
			$image = new MyImageEntity();
			$this->images->upload($values->avatar, $image);
			$this->em->persist($image);
			$this->avatar = $image->getId();
		};

		return $form;
	}

	public function renderDefault()
	{
		$this->template->avatar = $this->em->find(MyImageEntity::class, $this->avatar);
	}
}
```

### Latte template

```html
<div>
	...
	<img n:crop="$avatar, '64x64'">
	...
</div>
```
