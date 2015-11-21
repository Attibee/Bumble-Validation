<?php
namespace Bumble\Validation;

class LengthTest extends \PHPUnit_Framework_TestCase {
    public function testIsValidLength() {
        $v = new Length(array(
            'min' => 2,
            'max' => 4
        ));
        
        $this->assertTrue( $v->isValid('123') ); 
    }
    
    public function testIsTooBig() {
        $v = new Length(array(
            'min' => 2,
            'max' => 4
        ));
        
        $this->assertFalse( $v->isValid('12345') );
    }
    
    public function testIsTooSmall() {
        $v = new Length(array(
            'min' => 2,
            'max' => 4
        ));
        
        $this->assertFalse( $v->isValid('1') );
    }
}