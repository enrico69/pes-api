<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       06/09/2019 (dd-mm-YYYY)
 */

namespace App\Model;

use App\Entity\Match;

class MatchCollection implements \Iterator, \Countable, \ArrayAccess
{
    private $matches = [];

    public function add(Match $match): MatchCollection
    {
        $this->matches[] = $match;

        return $this;
    }

    public function debug(): void
    {
        // Debug
        $count = 0;
        foreach ($this->matches as $match) {
            /** @var \App\Entity\Match $match */
            echo $match->getTeam1()->getName()." ({$match->getGamer1()->getName()})".' - '
                .$match->getTeam2()->getName()." ({$match->getGamer2()->getName()}) <br/>";
            ++$count;
            if (2 === $count) {
                echo '<br/>';
                $count = 0;
            }
        }
    }

    /**
     * @return \App\Entity\Match[]
     */
    public function getMatches(): array
    {
        return $this->matches;
    }

    /**
     * Return the current element.
     */
    public function current(): Match
    {
        return \current($this->matches);
    }

    /**
     * Move forward to next element.
     */
    public function next()
    {
        return \next($this->matches);
    }

    /**
     * Return the key of the current element.
     */
    public function key(): string
    {
        return \key($this->matches);
    }

    /**
     * Checks if current position is valid.
     */
    public function valid(): bool
    {
        $key = \key($this->matches);

        return null !== $key && false !== $key;
    }

    /**
     * Rewind the Iterator to the first element.
     */
    public function rewind(): void
    {
        \reset($this->matches);
    }

    /**
     * Count elements of an object.
     */
    public function count(): int
    {
        return \count($this->matches);
    }

    /**
     * @param $offset
     *
     * @return bool true on success or false on failure
     */
    public function offsetExists($offset): bool
    {
        return \array_key_exists($offset, $this->matches);
    }

    /**
     * @param mixed $offset
     *
     * @return \App\Entity\Match
     */
    public function offsetGet($offset): Match
    {
        return $this->matches[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new \RuntimeException('Setting a new match is forbidden here');
    }

    /**
     * @param mixed $offset the offset to unset
     */
    public function offsetUnset($offset): void
    {
        throw new \RuntimeException('Unsetting a new match is forbidden here');
    }
}
