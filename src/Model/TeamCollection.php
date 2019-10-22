<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       06/09/2019 (dd-mm-YYYY)
 */

namespace App\Model;

use App\Entity\Team;

class TeamCollection implements \Iterator, \Countable, \ArrayAccess
{
    /**
     * @var \App\Entity\Team[]
     */
    private $teams = [];
    private $teamIds = [];
    private $locked = false;

    public function add(Team $team): self
    {
        if ($this->isLocked()) {
            throw new \LogicException('Collection is locked. You cannot add more team!');
        }

        $teamId = $team->getId();

        if (\in_array($teamId, $this->teamIds)) {
            throw new \LogicException('The team is already present!');
        }

        $this->teamIds[] = $teamId;
        $this->teams[$teamId] = $team;

        return $this;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function lock(): self
    {
        $this->locked = true;

        return $this;
    }

    /**
     * @return \App\Entity\Team[]
     */
    public function getTeams(): array
    {
        return $this->teams;
    }

    /**
     * Return the current element.
     */
    public function current(): Team
    {
        return \current($this->teams);
    }

    /**
     * Move forward to next element.
     */
    public function next()
    {
        return \next($this->teams);
    }

    /**
     * Return the key of the current element.
     */
    public function key(): string
    {
        return \key($this->teams);
    }

    /**
     * Checks if current position is valid.
     */
    public function valid(): bool
    {
        $key = \key($this->teams);

        return null !== $key && false !== $key;
    }

    /**
     * Rewind the Iterator to the first element.
     */
    public function rewind(): void
    {
        \reset($this->teams);
    }

    /**
     * Count elements of an object.
     */
    public function count(): int
    {
        return \count($this->getTeams());
    }

    /**
     * @param $offset
     *
     * @return bool true on success or false on failure
     */
    public function offsetExists($offset): bool
    {
        return \array_key_exists($offset, $this->teams);
    }

    /**
     * @param mixed $offset
     *
     * @return \App\Entity\Team
     */
    public function offsetGet($offset): Team
    {
        return $this->teams[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new \RuntimeException('Setting a new team is forbidden here');
    }

    /**
     * @param mixed $offset the offset to unset
     */
    public function offsetUnset($offset): void
    {
        throw new \RuntimeException('Unsetting a new team is forbidden here');
    }
}
