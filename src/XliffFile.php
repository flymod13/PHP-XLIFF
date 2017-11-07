<?php

namespace oyagev\PhpXliff;
/**
 * Concrete class for file tag
 * 
 * @method XliffFileBody body()
 * @method XliffFileHeader header()
 */
class XliffFile extends XliffNode{
	protected $name = 'file';
	protected $supportedNodes = array(
		'header' 	=> 'oyagev\PhpXliff\XliffFileHeader',
		'body' 		=> 'oyagev\PhpXliff\XliffFileBody',
	);
	
}