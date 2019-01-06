<?php declare(strict_types = 1);

use Nette\Utils\FileSystem;

include __DIR__ . '/../vendor/autoload.php';

$tempDir = __DIR__ . '/temp/' . getmypid();
define('TEMP_DIR', $tempDir);
mkdir($tempDir, 0777, true);

register_shutdown_function(static function (): void {
	FileSystem::delete(__DIR__ . '/temp');
});
