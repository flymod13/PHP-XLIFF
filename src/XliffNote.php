<?php

namespace oyagev\PhpXliff;

/**
 * Concrete class for note tag
 * @todo implement attributes:
 *     - xml:lang
 *     - from
 *     - priority
 *     - annotates .
 */
class XliffNote extends XliffNode
{
    protected $name = 'note';
}
