<?php

namespace App\Entity\base;

use Symfony\Component\Validator\Constraints as Assert;

class EntityId {

	/**
	 *
	 * @Assert\NotNull()
	 * @Assert\Type("\DateTime")
	 */
	public $inserted;

	/**
	 *
	 * @Assert\NotNull()
	 * @Assert\Type("\DateTime")
	 */
	public $updated;

	/**
	 *
	 * @Assert\NotNull()
	 */
	public $estabelecimento;

	public function getInserted() {
		return $this->inserted;
	}

	public function setInserted($inserted) {
		$this->inserted = $inserted;
	}

	public function getUpdated() {
		return $this->updated;
	}

	public function setUpdated($updated) {
		$this->updated = $updated;
	}

	public function getEstabelecimento() {
		return $this->estabelecimento;
	}

	public function setEstabelecimento($estabelecimento) {
		$this->estabelecimento = $estabelecimento;
	}
}