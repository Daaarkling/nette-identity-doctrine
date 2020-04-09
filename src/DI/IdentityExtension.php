<?php declare(strict_types = 1);

namespace Darkling\Doctrine2Identity\DI;

use Darkling\Doctrine2Identity\UserStorage;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Security\IUserStorage;

class IdentityExtension extends CompilerExtension
{

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$userStorageDefinitionName = $builder->getByType(IUserStorage::class) ?? 'nette.userStorage';

		/** @var ServiceDefinition $definition */
		$definition = $builder->getDefinition($userStorageDefinitionName);
		$definition->setFactory(UserStorage::class);
	}

}
