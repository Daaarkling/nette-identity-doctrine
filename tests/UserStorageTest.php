<?php declare(strict_types = 1);

namespace Darkling\Doctrine2Identity\Tests;

use Darkling\Doctrine2Identity\Tests\Entities\User;
use Darkling\Doctrine2Identity\UserStorage;
use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Nette\DI\Container;
use Nette\Security\Identity;
use Nette\Security\IUserStorage;
use PHPUnit\Framework\TestCase;

class UserStorageTest extends TestCase
{

	private const IDENTITY_CLASS = User::class;
	private const I_USER_STORAGE_CLASS = IUserStorage::class;
	private const USER_STORAGE_CLASS = UserStorage::class;

	private Container $container;

	private IUserStorage $userStorage;

	private EntityManagerDecorator $entityManager;

	private DatabaseLoader $databaseLoader;

	protected function setUp(): void
	{
		$containerFactory = new ContainerFactory();
		$this->container = $containerFactory->create();

		$this->userStorage = $this->container->getByType(self::I_USER_STORAGE_CLASS) ?? $this->container->getService('nette.userStorage');
		$this->entityManager = $this->container->getByType(EntityManagerDecorator::class);
		$this->databaseLoader = $this->container->getByType(DatabaseLoader::class);
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
		/** @var User $user */
		$user = $userRepository->find(1);

		$userStorage = $this->userStorage->setIdentity($user);
		$this->assertInstanceOf(self::I_USER_STORAGE_CLASS, $userStorage);
		$this->assertInstanceOf(self::USER_STORAGE_CLASS, $userStorage);

		/** @var User $userIdentity */
		$userIdentity = $userStorage->getIdentity();
		$this->assertSame($user, $userIdentity);
		$this->assertSame(1, $userIdentity->getId());
		$this->assertSame([], $userIdentity->getRoles());
	}

	public function testEntityProxyIdentity(): void
	{
		$this->databaseLoader->loadUserTableWithOneItem();
		$userRepository = $this->entityManager->getRepository(self::IDENTITY_CLASS);
		/** @var User $userProxy */
		$userProxy = $this->entityManager->getProxyFactory()->getProxy(self::IDENTITY_CLASS, ['id' => 1]);
		$user = $userRepository->find(1);

		$userStorage = $this->userStorage->setIdentity($userProxy);
		$this->assertInstanceOf(self::I_USER_STORAGE_CLASS, $userStorage);
		$this->assertInstanceOf(self::USER_STORAGE_CLASS, $userStorage);

		/** @var User $userIdentity */
		$userIdentity = $userStorage->getIdentity();
		$this->assertSame($user, $userIdentity);
		$this->assertNotSame($userProxy, $userIdentity);
		$this->assertSame(1, $userIdentity->getId());
		$this->assertSame([], $userIdentity->getRoles());
	}

}
