<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       06/09/2019 (dd-mm-YYYY)
 */

namespace App\Model;


use App\Entity\Gamer;

class GamerCollection implements \Iterator, \Countable, \ArrayAccess
{
    /**
     * @var \App\Entity\Gamer[]
     */
    private $gamers = [];
    private $gamerIds = [];
    private $locked = false;
    private $computerCount = 0;
    private $isDamienHere = false;

    public function add(Gamer $gamer) : self
    {
        if ($this->isLocked()) {
            throw new \LogicException('Collection is locked. You cannot add more gamer!');
        }

        $gamerId = $gamer->getId();

        if (\in_array($gamerId, $this->gamerIds)) {
            throw new \LogicException('The gamer is already present!');
        }

        $this->gamerIds[] = $gamerId;
        $this->gamers[$gamerId] = $gamer;

        if ($gamer->getType() === Gamer::TYPE_COMPUTER) {
            $this->computerCount++;
        }

        if ($gamerId === Gamer::getDamienId()) {
            $this->isDamienHere = true;
        }

        return $this;
    }

    public function isDamienHere() : bool
    {
        return $this->isDamienHere;
    }

    public function getComputerCount() : int
    {
        return $this->computerCount;
    }

    public function isLocked() : bool
    {
        return $this->locked;
    }

    public function lock() : self
    {
        $this->locked = true;

        return $this;
    }

    /**
     * @return \App\Entity\Gamer[]
     */
    public function getGamers() : array
    {
        return $this->gamers;
    }

    /**
     * Return the current element
     */
    public function current() : Gamer
    {
        return \current($this->gamers);
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        return \next($this->gamers);
    }

    /**
     * Return the key of the current element
     */
    public function key() : string
    {
        return \key($this->gamers);
    }

    /**
     * Checks if current position is valid
     */
    public function valid() : bool
    {
        $key = \key($this->gamers);

        return ($key !== null && $key !== false);
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind() : void
    {
        \reset($this->gamers);
    }

    /**
     * Count elements of an object
     */
    public function count() : int
    {
        return \count($this->getGamers());
    }

    /**
     * @param $offset
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset) : bool
    {
        return \array_key_exists($offset, $this->gamers);
    }

    /**
     * @param mixed $offset
     * @return \App\Entity\Gamer
     */
    public function offsetGet($offset) : Gamer
    {
        return $this->gamers[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value) : void
    {
        throw new \RuntimeException('Setting a new gamer is forbidden here');
    }

    /**
     * @param mixed $offset The offset to unset.
     * @return void
     */
    public function offsetUnset($offset) : void
    {
        throw new \RuntimeException('Unsetting a new gamer is forbidden here');
    }
}
