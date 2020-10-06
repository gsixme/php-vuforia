Vuforia Web Service PHP Wrapper
===========

Note: This is not an official package from Vuforia.

To learn more, go to https://developer.vuforia.com


Installation
------------

Include the package in your composer file

    "require": {
        "gsixme/php-vuforia": "^1.0.0"
    }

Run `composer update`

Or simply run

`composer require gsixme/php-vuforia`

Usage
-----

Create new VuforiaClient

	$vuforiaClient = new Gsix\Vuforia\VuforiaClient([
		'access_key' => YOUR_ACCESS_KEY,
		'secret_key' => YOUR_SECRET_KEY,
	]);

	
Create new Target

	$target = new Gsix\Vuforia\Target([
		'name' => 'Targetname',
		'width' => 320, // must be > 320
		'path' => 'image.jpg', // path to image
		'active' => 1,
		'metadata' => 'other relevant data that you may need later'
	]);

	
Available methods
-----------------

	// list all targets
	$vuforiaClient->targets->all();

	// get target by id
	$vuforiaClient->targets->get($id);

	// create target
	$vuforiaClient->targets->create($target);

	// update target
	$vuforiaClient->targets->update($id, $target);

	// delete target
	$vuforiaClient->targets->delete($id);

	// list uplicates for target
	$vuforiaClient->duplicates->get($id);

	// summary for the whole db
	$vuforiaClient->summary->database();

	// summary for target
	$vuforiaClient->summary->target($id);

