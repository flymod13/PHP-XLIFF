<?php
/**
 * @author Oren Yagev <oyagev@gmail.com>
 */

namespace oyagev\PhpXliff;
/**
 * Wrapper class for Xliff documents.
 * Externally, you'll want to use this class.
 *
 * @method XliffFile file() file()
 */
class XliffDocument extends XliffNode
{
    /**
     * uncomplete xliff Namespace
     */
    const NS = 'urn:oasis:names:tc:xliff:document:';

    protected $name = 'xliff';
    protected $supportedContainers = array(
        'files' => 'oyagev\PhpXliff\XliffFile',
    );


    protected $version;


    function __construct()
    {
        parent::__construct();
        $this->version = '1.2';
    }


    /**
     * Convert this XliffDocument to DOMDocument
     * @return DOMDocument
     */
    public function toDOM()
    {
        // create the new document
        $doc = new \DOMDocument();

        // create the xliff root element
        $xliff = $this->toDOMElement($doc);

        $xliff->setAttribute('xmlns', self::NS . $this->version);
        // add the xliff version
        $xliff->setAttribute('version',$this->version);

        $doc->appendChild($xliff);

        return $doc;
    }

    /**
     * Build XliffDocument from DOMDocument
     *
     * @param DOMDocument $doc
     * @throws Exception
     * @return XliffDocument
     */
    public static function fromDOM(\DOMDocument $doc)
    {
        if (!($doc->firstChild &&  $doc->firstChild->tagName=='xliff'))
            throw new \Exception("Not an XLIFF document");


        $xlfDoc = $doc->firstChild;
        /* @var $xlfDoc DOMElement */

        $ver = $xlfDoc->getAttribute('version') ? $xlfDoc->getAttribute('version') : '1.2';

        $xliffNamespace = $xlfDoc->namespaceURI;

        $element = self::fromDOMElement($xlfDoc);

        return $element;
    }
}
