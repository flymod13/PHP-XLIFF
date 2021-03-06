<?php

namespace oyagev\PhpXliff;
/**
 * Concrete class for file header tag
 */
class XliffFileHeader extends XliffNode
{
    protected $name = 'header';

    protected $supportedContainers = array(
        'notes' => 'oyagev\PhpXliff\XliffNote',
    );
}
