<?php declare(strict_types = 1);

namespace Darkling\Doctrine2Identity\Tests\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nette\Security\IIdentity;

/**
 * @ORM\Entity
 */
class User implements IIdentity
{

	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private int $id;

	/** @ORM\Column(type="string") */
	private string $name;

	public function __construct(string $name)
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return mixed[]
	 */
	public function getRoles(): array
	{
		return [];
	}

}
