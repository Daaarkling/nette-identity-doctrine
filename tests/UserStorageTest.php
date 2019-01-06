<?php declare(strict_types = 1);

namespace Darkling\Doctrine2Identity\Tests;

use Nette\Security\Identity;
use PHPUnit\Framework\TestCase;

class UserStorageTest extends TestCase
{

	private const IDENTITY_CLASS = 'Darkling\Doctrine2Identity\Tests\Entities\User';
	private const I_USER_STORAGE_CLASS = 'Nette\Security\IUserStorage';
	private const USER_STORAGE_CLASS = 'Darkling\Doctrine2Identity\UserStorage';

	/** @var \Nette\DI\Container */
	private $container;

	/** @var \Nette\Security\IUserStorage */
	private $userStorage;

	/** @var \Doctrine\ORM\EntityManager */
	private $entityManager;

	/** @var \Darkling\Doctrine2Identity\Tests\DatabaseLoader */
	private $databaseLoader;

	protected function setUp(): void
	{
		$containerFactory = new ContainerFactory();
		$this->container = $containerFactory->create();

		$this->userStorage = $this->container->getByType(self::I_USER_STORAGE_CLASS) ?? $this->container->getService('nette.userStorage');
		$this->entityManager = $this->container->getByType('Doctrine\ORM\EntityManager');
		$this->databaseLoader = $this->container->getByType('Darkling\Doctrine2Identity\Tests\DatabaseLoader');
	}

	public function testInstance(): void
	{
		$this->assertInstanceOf(self::I_USER_STORAGE_CLASS, $this->userStorage);
		$this->assertInstanceOf(self::USER_STORAGE_CLASS, $this->userStorage);
	}

	public function testSetGetIdentity(): void
	{
		$this->assertNull($this->userStorage->getIdentity());

		$identity = new Identity(1);
		$this->userStorage->setIdentity($identity);

		$this->assertSame($identity, $this->userStorage->getIdentity());
	}

	public function testEntityIdentity(): void
	{
		$this->databaseLoader->loadUserTableWithOneItem();
		$userRepository = $this->entityManager->getRepository(self::IDENTITY_CLASS);
		/** @var \Darkling\Doctrine2Identity\Tests\Entities\User $user */
		$user = $userRepository->find(1);

		$userStorage = $this->userStorage->setIdentity($user);
		$this->assertInstanceOf(self::I_USER_STORAGE_CLASS, $userStorage);
		$this->assertInstanceOf(self::USER_STORAGE_CLASS, $userStorage);

		/** @var \Darkling\Doctrine2Identity\Tests\Entities\User $userIdentity */
		$userIdentity = $userStorage->getIdentity();
		$this->assertSame($user, $userIdentity);
		$this->assertSame(1, $userIdentity->getId());
		$this->assertSame([], $userIdentity->getRoles());
	}

	public function testEntityProxyIdentity(): void
	{
		$this->databaseLoader->loadUserTableWithOneItem();
		$userRepository = $this->entityManager->getRepository(self::IDENTITY_CLASS);
		/** @var \Darkling\Doctrine2Identity\Tests\Entities\User $userProxy */
		$userProxy = $this->entityManager->getProxyFactory()->getProxy(self::IDENTITY_CLASS, ['id' => 1]);
		$user = $userRepository->find(1);

		$userStorage = $this->userStorage->setIdentity($userProxy);
		$this->assertInstanceOf(self::I_USER_STORAGE_CLASS, $userStorage);
		$this->assertInstanceOf(self::USER_STORAGE_CLASS, $userStorage);

		/** @var \Darkling\Doctrine2Identity\Tests\Entities\User $userIdentity */
		$userIdentity = $userStorage->getIdentity();
		$this->assertSame($user, $userIdentity);
		$this->assertNotSame($userProxy, $userIdentity);
		$this->assertSame(1, $userIdentity->getId());
		$this->assertSame([], $userIdentity->getRoles());
	}

}
