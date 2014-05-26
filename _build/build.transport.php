<?php
/**
 * @package s3Extended
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define package */
define('PKG_NAME','S3Extended');
define('PKG_NAME_LOWER',strtolower(PKG_NAME));
define('PKG_VERSION','0.0.1');
define('PKG_RELEASE','pl');

/* define sources */
$root = dirname(dirname(__FILE__)) . '/';
$sources= array (
    'root' => $root,
    'build' => $root . '_build/',
	'resolvers' => $root . '_build/resolvers/',
	'lexicon' => $root . 'core/components/'.PKG_NAME_LOWER.'/lexicon/',
    'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
    'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
    'docs' => $root . 'core/components/'.PKG_NAME_LOWER.'/docs/',
);
unset($root);

/* instantiate MODx */
require_once $sources['build'].'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
echo "Loading builder.<br>";
/* load builder */
$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/'.PKG_NAME_LOWER.'/');
echo "Done loading builder.<br>";

/* add in file vehicle */
echo "Adding files to file vehicle.<br>";
$attributes = array (
    'vehicle_class' => 'xPDOFileVehicle',
);
$c = array (
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
);
$vehicle = $builder->createVehicle($c, $attributes);
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'] . 'extpack.resolver.php',
));
$builder->putVehicle($vehicle);
unset ($c, $vehicle, $attributes);
echo "files added to file vehicle.<br>";
/* now pack in the license file, readme and setup options */
echo "Adding docs.<br>";
 $builder->setPackageAttributes(array(
 	'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
     'license' => file_get_contents($sources['docs'] . 'license.txt'),
     'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
 ));
echo "Docs added.<br>";
echo "Packing package\n.";
$builder->pack();
echo "Done.<br>";


$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

echo "\nExecution time: {$totalTime}<br>";

exit ();
