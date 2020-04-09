<?php declare(strict_types = 1);

namespace Darkling\Doctrine2Identity\Tests;

use Darkling\Doctrine2Identity\Tests\Entities\User;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Decorator\EntityManagerDecorator;

class DatabaseLoader
{

	private bool $isDbPrepared = false;

	private Connection $connection;

	private EntityManagerDecorator $entityManager;

	public function __construct(Connection $connection, EntityManagerDecorator $entityManager)
	{
		$this->connection = $connection;
		$this->entityManager = $entityManager;
	}

	public function loadUserTableWithOneItem(): void
	{
		if ($this->isDbPrepared) {
			return;
		}

		$this->connection->query('CREATE TABLE user (id INTEGER NOT NULL, name string, PRIMARY KEY(id))');

		$user = new User('John');
		$this->entityManager->persist($user);
		$this->entityManager->flush();

		$this->isDbPrepared = true;
	}

}
