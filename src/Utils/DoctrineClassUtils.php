<?php declare(strict_types = 1);

namespace Darkling\Doctrine2Identity\Utils;

use Doctrine\ORM\Proxy\Proxy;
use Nette\Security\IIdentity;
use function get_class;
use function strrpos;
use function substr;

class DoctrineClassUtils
{

	/**
	 * Gets the real class name of a class name that could be a proxy.
	 *
	 * @param string $class
	 * @return string
	 */
	public static function getRealClass(string $class): string
	{
		$pos = strrpos($class, '\\' . Proxy::MARKER . '\\');
		if (!$pos) {
			return $class;
		}

		return substr($class, $pos + Proxy::MARKER_LENGTH + 2);
	}

	/**
	 * Gets the real class name of an object (even if its a proxy).
	 *
	 * @param IIdentity $object
	 * @return string
	 */
	// phpcs:ignore
	public static function getClass(IIdentity $object): string
	{
		return self::getRealClass(get_class($object));
	}

}
