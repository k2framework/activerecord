<?php

require_once __DIR__ . '/autoload.php';

use ActiveRecord\Metadata\Metadata;
use ActiveRecord\Metadata\Attribute;

class MetadataTest extends PHPUnit_Framework_TestCase
{

    public function testAtribute()
    {
        $attr = new Attribute();

        $this->assertFalse($attr->FK);
        $this->assertFalse($attr->PK);
        $this->assertFalse($attr->unique);
        $this->assertFalse($attr->autoIncrement);
        $this->assertNull($attr->alias);
        $this->assertNull($attr->default);
        $this->assertNull($attr->format);
        $this->assertFalse($attr->notNull);
        $this->assertNull($attr->type);
        $this->assertEquals(50, $attr->length);
    }

    public function testMetadata()
    {
        
    }

}
