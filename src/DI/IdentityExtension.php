<?php declare(strict_types = 1);

namespace Darkling\Doctrine2Identity\DI;

use Nette\DI\CompilerExtension;

class IdentityExtension extends CompilerExtension
{

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$userStorageDefinitionName = $builder->getByType('Nette\Security\IUserStorage') ?? 'nette.userStorage';

		/** @var \Nette\DI\Definitions\ServiceDefinition $definition */
		$definition = $builder->getDefinition($userStorageDefinitionName);
		$definition->setFactory('Darkling\Doctrine2Identity\UserStorage');
	}

}
