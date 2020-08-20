<?php

namespace oyagev\PhpXliff;

/**
 * Concrete class for file body tag
 *
 * @method XliffUnitsGroup group()
 * @method XliffUnit unit()
 * @method array groups()
 * @method array units()
 */
class XliffFileBody extends XliffNode
{
    protected $name = 'body';
    protected $supportedContainers = array(
        'groups'    => 'oyagev\PhpXliff\XliffUnitsGroup',
        'trans-units'       => 'oyagev\PhpXliff\XliffUnit'
    );
}
