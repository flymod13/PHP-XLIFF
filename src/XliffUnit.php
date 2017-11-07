<?php

namespace oyagev\PhpXliff;

/**
 * Concrete class for trans-unit tag
 * 
 * @method XliffNode source()
 * @method XliffNode target()
 */
class XliffUnit extends XliffNode{
	protected $name = 'trans-unit';
	protected $supportedNodes = array(
		'source' => 'oyagev\PhpXliff\XliffNode',
		'target' => 'oyagev\PhpXliff\XliffNode',
	);
}