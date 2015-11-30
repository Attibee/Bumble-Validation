<?php
namespace Bumble\Validation;

class NotEmptyTest extends \PHPUnit_Framework_TestCase {
    public function testEmptyValidation() {
        $v = new NotEmpty();
        
        $result = $v->isValid('');
        
        $this->assertFalse( $v->isValid('') ); 
    }
    
    public function testNotEmptyValidation() {
        $v = new NotEmpty();
        
        $this->assertTrue( $v->isValid('notEmpty') );
    }
}