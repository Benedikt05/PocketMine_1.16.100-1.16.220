<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\inventory;

use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\mcpe\protocol\types\NetworkInventoryAction;
use pocketmine\utils\BinaryDataException;
use UnexpectedValueException;
use function count;

abstract class TransactionData{
	/** @var NetworkInventoryAction[] */
	protected $actions = [];

	/**
	 * @return NetworkInventoryAction[]
	 */
	final public function getActions() : array{
		return $this->actions;
	}

	abstract public function getTypeId() : int;

	/**
	 * @throws BinaryDataException
	 * @throws UnexpectedValueException
	 */
	final public function decode(NetworkBinaryStream $stream, bool $hasItemStackIds) : void{
		$actionCount = $stream->getUnsignedVarInt();
		for($i = 0; $i < $actionCount; ++$i){
			$this->actions[] = (new NetworkInventoryAction())->read($stream, $hasItemStackIds);
		}
		$this->decodeData($stream);
	}

	/**
	 * @throws BinaryDataException
	 * @throws UnexpectedValueException
	 */
	abstract protected function decodeData(NetworkBinaryStream $stream) : void;

	final public function encode(NetworkBinaryStream $stream, bool $hasItemStackIds) : void{
		$stream->putUnsignedVarInt(count($this->actions));
		foreach($this->actions as $action){
			$action->write($stream, $hasItemStackIds);
		}
		$this->encodeData($stream);
	}

	abstract protected function encodeData(NetworkBinaryStream $stream) : void;
}