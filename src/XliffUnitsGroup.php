<?php

namespace oyagev\PhpXliff;

/**
 * Concrete class for group tag
 *
 * @method XliffUnit unit()
 * @method array units()
 */
class XliffUnitsGroup extends XliffNode
{
    protected $name = 'group';
    protected $supportedContainers = array(
        'trans-units'       => 'oyagev\PhpXliff\XliffUnit'
    );
}
