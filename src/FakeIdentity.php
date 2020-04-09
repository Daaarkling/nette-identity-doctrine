<?php declare(strict_types = 1);

namespace Darkling\Doctrine2Identity;

use Nette\Security\IIdentity;

class FakeIdentity implements IIdentity
{

	/** @var mixed */
	private $id;

	private string $class;

	/**
	 * @param mixed $id
	 * @param string $class
	 */
	public function __construct($id, string $class)
	{
		$this->id = $id;
		$this->class = $class;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getClass(): string
	{
		return $this->class;
	}

	/**
	 * @return mixed[]
	 */
	public function getRoles(): array
	{
		return [];
	}

}
