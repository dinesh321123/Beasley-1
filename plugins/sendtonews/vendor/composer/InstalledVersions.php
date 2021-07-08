<?php











namespace Composer;

use Composer\Semver\VersionParser;






class InstalledVersions
{
private static $installed = array (
  'root' => 
  array (
    'pretty_version' => 'dev-master',
    'version' => 'dev-master',
    'aliases' => 
    array (
    ),
    'reference' => 'a4e51beee3602433e43ad6f921bb35019891389b',
    'name' => 'stnvideo/playerselector',
  ),
  'versions' => 
  array (
    '10quality/ayuco' => 
    array (
      'pretty_version' => 'v1.0.x-dev',
      'version' => '1.0.9999999.9999999-dev',
      'aliases' => 
      array (
      ),
      'reference' => '6c4d11232dc7b80ebb87c7899db98c76829e1a63',
    ),
    '10quality/wp-file' => 
    array (
      'pretty_version' => 'v0.9.4',
      'version' => '0.9.4.0',
      'aliases' => 
      array (
      ),
      'reference' => '282f0f6733a9e18b392a8b9999dcd8949275a77b',
    ),
    '10quality/wpmvc-commands' => 
    array (
      'pretty_version' => 'v1.1.10',
      'version' => '1.1.10.0',
      'aliases' => 
      array (
      ),
      'reference' => '31ae94e9d8665445f333d6842783c5191a164375',
    ),
    '10quality/wpmvc-core' => 
    array (
      'pretty_version' => 'v3.1.11',
      'version' => '3.1.11.0',
      'aliases' => 
      array (
      ),
      'reference' => '3c247ed0197d65ff01741ad972f2e79c1bd763bd',
    ),
    '10quality/wpmvc-logger' => 
    array (
      'pretty_version' => 'v2.0.x-dev',
      'version' => '2.0.9999999.9999999-dev',
      'aliases' => 
      array (
      ),
      'reference' => '3f8959bd7fe585d248d102e198aae4a2504a90d1',
    ),
    '10quality/wpmvc-mvc' => 
    array (
      'pretty_version' => 'v2.1.6',
      'version' => '2.1.6.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f90689907b6f1eab368a14e859e37bd16e87286c',
    ),
    '10quality/wpmvc-phpfastcache' => 
    array (
      'pretty_version' => 'v4.0.x-dev',
      'version' => '4.0.9999999.9999999-dev',
      'aliases' => 
      array (
      ),
      'reference' => '6d0b4ca7fd1e3d5b27992a2d8321768eb484873e',
    ),
    'nikic/php-parser' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '4.3.x-dev',
      ),
      'reference' => 'd86ca0f745b47efcf8d7cc1cfc69c55e78fd0b90',
    ),
    'psr/log' => 
    array (
      'pretty_version' => '1.0.0',
      'version' => '1.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'fe0936ee26643249e916849d48e3a51d5f5e278b',
    ),
    'stnvideo/playerselector' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
      ),
      'reference' => 'a4e51beee3602433e43ad6f921bb35019891389b',
    ),
  ),
);







public static function getInstalledPackages()
{
return array_keys(self::$installed['versions']);
}









public static function isInstalled($packageName)
{
return isset(self::$installed['versions'][$packageName]);
}














public static function satisfies(VersionParser $parser, $packageName, $constraint)
{
$constraint = $parser->parseConstraints($constraint);
$provided = $parser->parseConstraints(self::getVersionRanges($packageName));

return $provided->matches($constraint);
}










public static function getVersionRanges($packageName)
{
if (!isset(self::$installed['versions'][$packageName])) {
throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}

$ranges = array();
if (isset(self::$installed['versions'][$packageName]['pretty_version'])) {
$ranges[] = self::$installed['versions'][$packageName]['pretty_version'];
}
if (array_key_exists('aliases', self::$installed['versions'][$packageName])) {
$ranges = array_merge($ranges, self::$installed['versions'][$packageName]['aliases']);
}
if (array_key_exists('replaced', self::$installed['versions'][$packageName])) {
$ranges = array_merge($ranges, self::$installed['versions'][$packageName]['replaced']);
}
if (array_key_exists('provided', self::$installed['versions'][$packageName])) {
$ranges = array_merge($ranges, self::$installed['versions'][$packageName]['provided']);
}

return implode(' || ', $ranges);
}





public static function getVersion($packageName)
{
if (!isset(self::$installed['versions'][$packageName])) {
throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}

if (!isset(self::$installed['versions'][$packageName]['version'])) {
return null;
}

return self::$installed['versions'][$packageName]['version'];
}





public static function getPrettyVersion($packageName)
{
if (!isset(self::$installed['versions'][$packageName])) {
throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}

if (!isset(self::$installed['versions'][$packageName]['pretty_version'])) {
return null;
}

return self::$installed['versions'][$packageName]['pretty_version'];
}





public static function getReference($packageName)
{
if (!isset(self::$installed['versions'][$packageName])) {
throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}

if (!isset(self::$installed['versions'][$packageName]['reference'])) {
return null;
}

return self::$installed['versions'][$packageName]['reference'];
}





public static function getRootPackage()
{
return self::$installed['root'];
}







public static function getRawData()
{
return self::$installed;
}



















public static function reload($data)
{
self::$installed = $data;
}
}
