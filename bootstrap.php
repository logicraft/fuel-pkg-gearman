<?php

/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel-Gearman
 * @version    1.0
 * @author     Miura Daisuke
 * @link
 */

Autoloader::add_core_namespace('Gearman');

Autoloader::add_classes(array(
	'Gearman\\Gearman'          => __DIR__.'/classes/gearman.php',
	'Gearman\\GearmanClient'    => __DIR__.'/classes/gearman/client.php',
	'Gearman\\GearmanWorker'    => __DIR__.'/classes/gearman/worker.php',
	'Gearman\\GearmanException' => __DIR__.'/classes/gearman/exception.php',
));

/* End of file bootstrap.php */