<?php
namespace Bumble\Validation;

class InListTest extends \PHPUnit_Framework_TestCase {
    public function testIsInList() {
        $v = new InList(array(
            'a', 'b', 'c'
        ));
        
        $this->assertTrue( $v->isValid('a') ); 
    }
    
    public function testNotInList() {
        $v = new InList(array(
            'a', 'b', 'c'
        ));
        
        $this->assertFalse( $v->isValid('d') );
    }
}