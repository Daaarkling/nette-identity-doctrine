<?php declare(strict_types = 1);

namespace Darkling\Doctrine2Identity;

use Darkling\Doctrine2Identity\Utils\DoctrineClassUtils;
use Doctrine\ORM\EntityManager;
use Nette\Http\Session;
use Nette\Http\UserStorage as NetteUserStorage;
use Nette\Security\IIdentity;

class UserStorage extends NetteUserStorage
{

	/** @var \Doctrine\ORM\EntityManager */
	private $entityManager;

	public function __construct(Session $sessionHandler, EntityManager $entityManager)
	{
		parent::__construct($sessionHandler);

		$this->entityManager = $entityManager;
	}

	public function setIdentity(?IIdentity $identity): self
	{
		if ($identity !== null) {
			$class = DoctrineClassUtils::getClass($identity);

			// we want to convert identity entities into fake identity
			// so only the identifier fields are stored,
			// but we are only interested in identities which are correctly
			// mapped as doctrine entities
			if ($this->entityManager->getMetadataFactory()->hasMetadataFor($class)) {
				$cm = $this->entityManager->getClassMetadata($class);
				$identifier = $cm->getIdentifierValues($identity);
				$identity = new FakeIdentity($identifier, $class);
			}
		}

		return parent::setIdentity($identity);
	}

	public function getIdentity(): ?IIdentity
	{
		$identity = parent::getIdentity();

		// if we have our fake identity, we now want to
		// convert it back into the real entity
		// returning reference provides potentially lazy behavior
		if ($identity instanceof FakeIdentity) {
			/** @var \Nette\Security\IIdentity $entity */
			$entity = $this->entityManager->getReference($identity->getClass(), $identity->getId());
			return $entity;
		}

		return $identity;
	}

}
