<?php

namespace oyagev\PhpXliff;

class XliffNode
{
    /**
     * Map tag names to classes
     * @var array
     */
    static protected $mapNameToClass = array(
        'xliff'     => 'oyagev\PhpXliff\XliffDocument',
        'file'      => 'oyagev\PhpXliff\XliffFile',
        'body'      => 'oyagev\PhpXliff\XliffFileBody',
        'header'    => 'oyagev\PhpXliff\XliffFileHeader',
        'group'     => 'oyagev\PhpXliff\XliffUnitsGroup',
        'trans-unit'=> 'oyagev\PhpXliff\XliffUnit',
        'source'    => 'oyagev\PhpXliff\XliffNode',
        'target'    => 'oyagev\PhpXliff\XliffNode',
    );


    /**
     * Holds element's attributes
     * @var Array
     */
    protected $attributes = array();

    /**
     * Holds child nodes that can be repeated inside this node.
     * For example, an xliff document can have multiple "file" nodes
     * @var Array[tag-name][0..n]=XliffNode
     */
    protected $containers = array();

    /**
     * Indicate which child nodes are supported
     * @var Array[tag-name]=>Xliff Class
     */
    protected $supportedContainers = array();

    /**
     * Holds child nodes that can be presented only once inside this node.
     * For example, "trans-unit" element can have only one "source" node
     * @var Array[tag-name]=XliffNode
     */
    protected $nodes = array();

    /**
     * Indicate which child nodes are supported
     * @var Array[tag-name]=>Xliff Class
     */
    protected $supportedNodes = array();

    /**
     * Node's text, NULL if none
     * @var String|NULL
     */
    protected $textContent = NULL;

    /**
     * Node's tag name
     * @var string
     */
    protected $name = '';

    function __construct($name=NULL)
    {
        if($name) $this->setName($name);
        //initialize containers array
        foreach($this->supportedContainers as $name => $class) {
            $this->containers[$name] = array();
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return XliffNode
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the attribute value, FALSE if attribute missing
     * @param string $name
     * @return Ambigous <boolean, string> -
     */
    function getAttribute($name)
    {
        return (isset($this->attributes[$name])) ? $this->attributes[$name] : FALSE;
    }

    /**
     * Sets an attribute
     * @param string $name
     * @param string $value
     * @throws Exception
     * @return XliffNode
     */
    function setAttribute($name, $value)
    {
        /*if (!(string)$value){
            throw new Exception("Attribute must be a string");
        }*/
        $this->attributes[$name] = trim((string)$value);
        return $this;
    }

    /**
     * Set multiple attributes from a key=>value array
     * @param Array $attr_array
     * @return XliffNode
     */
    function setAttributes($attr_array)
    {
        foreach ($attr_array as $key => $val) {
            $this->setAttribute($key, $val);
        }

        return $this;
    }

    /**
     * @return Ambigous <string, NULL>
     */
    public function getTextContent()
    {
        return $this->textContent;
    }

    /**
     * @param string $textContent
     * @return XliffNode
     */
    public function setTextContent($textContent)
    {
        $this->textContent = $textContent;
        return $this;
    }

    /**
     * Append a new node to this element
     * @param XliffNode $node - node to append
     * @return XliffNode - this node
     */
    public function appendNode(XliffNode $node)
    {
        // Automatically detect where to append this node
        if (!empty($this->supportedContainers[$node->getName().'s'])) {
            $this->containers[$node->getName().'s'][] = $node;
        } elseif (!empty($this->supportedNodes[$node->getName()])) {
            $this->nodes[$node->getName()] = $node;
        } else {
            $this->nodes[$node->getName()] = $node;
        }

        return $this;
    }

    /**
     * Allow calling $node->tag_name($new=FALSE)
     * Supports the following methods:
     *
     * 1. $node->tag_name(TRUE) - create a new node for "tag_name" and return the new node
     * 2. $node->tag_name() - fetch the last added node for "tag_name", FALSE if none
     *
     * //On the following, notice that tag names are in plural formation...
     * 3. $node->tag_names() - return an array of tag_name nodes
     */
    function __call($name, $args)
    {
        $append = (!empty($args) && $args[0]==TRUE);
        $mapNames = array(
            '/^unit/' => 'trans-unit'
        );
        //re-map short names to actual tag names, for convenience
        $name = preg_replace(array_keys($mapNames), array_values($mapNames), $name);

        //plural ?
        if (!empty($this->supportedContainers[$name])) {
            return $this->containers[$name];
        }
        elseif (!empty($this->supportedContainers[$name.'s'])) {
            $pluralName= $name.'s';

            //Create new instance if explicitly specified by argument
            if ( $append ) {

                $cls = $this->supportedContainers[$pluralName];

                $this->containers[$pluralName][] = new $cls();

            }
            if (empty($this->containers[$pluralName])) return FALSE;
            return end($this->containers[$pluralName]);

        }
        elseif (!empty($this->supportedNodes[$name])) {


            //Create new node if explicitly required
            if ($append) {
                $cls = $this->supportedNodes[$name];
                $this->nodes[$name] = new $cls();
                $this->nodes[$name]->setName($name);
            }

            return (!empty($this->nodes[$name])) ? $this->nodes[$name] : FALSE;
        }
        throw new \Exception(sprintf("'%s' is not supported for '%s'",$name,get_class($this)));
    }

    /**
     * Export this node to a DOM object
     * @param DOMDocument $doc - parent DOMDocument must be provided
     * @return DOMElement
     */
    function toDOMElement(\DOMDocument $doc)
    {
        $element = $doc->createElement($this->getName());
        foreach ($this->attributes as $name => $value) {
            $element->setAttribute($name, $value);
        }
        foreach ($this->containers as $container) {
            foreach ($container as $node) {
                $element->appendChild($node->toDOMElement($doc));
            }
        }
        foreach ($this->nodes as $node) {
            $element->appendChild($node->toDOMElement($doc));
        }
        if ($text = $this->getTextContent()) {
            $textNode = $doc->createTextNode($text);
            $element->appendChild($textNode);
        }

        return $element;
    }

    /**
     * Convert DOM element to XliffNode structure
     * @param DOMNode $element
     * @throws Exception
     * @return string|XliffNode
     */
    public static function fromDOMElement(\DOMNode $element)
    {
        if ($element instanceOf \DOMText) {
            return $element->nodeValue;
        } else {
            $name = $element->tagName;

            //check if tag is supported
            if (empty(self::$mapNameToClass[$element->tagName])) {
                $cls = 'oyagev\PhpXliff\XliffNode';
                //throw new Exception(sprintf("Tag name '%s' is unsupported",$name));
            } else {
                //Create the XliffNode object (concrete object)
                $cls = self::$mapNameToClass[$element->tagName];
            }
            $node = new $cls($element->tagName);
            /* @var $node XliffNode */

            //Import attributes
            foreach ($element->attributes as $attrNode) {
                $node->setAttribute($attrNode->nodeName, $attrNode->nodeValue);
            }

            //Continue to nested nodes
            foreach ($element->childNodes as $child) {
                $res = self::fromDOMElement($child);
                if (is_string($res)) {
                    $node->setTextContent($res);
                } else {
                    $node->appendNode($res);
                }
            }
        }

        return $node;
    }
}
