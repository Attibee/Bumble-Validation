<?php
namespace Bumble\Validation;

class EmailTest extends \PHPUnit_Framework_TestCase {
    public function testIsValidEmail() {
        $v = new Email();
        
        $this->assertTrue( $v->isValid('g.anthony.massie@gmail.com') ); 
        $this->assertTrue( $v->isValid('g-anthony-massie@gmail.com') );
    }
    
    public function testUserSpecialCharacters() {
        $v = new Email();
        
        $this->assertFalse( $v->isValid('-test@test.com') );
        $this->assertFalse( $v->isValid('test-@test.com') );
        $this->assertFalse( $v->isValid('te--st@test.com') );
        $this->assertFalse( $v->isValid('te..st@test.com') );
    }
    
    public function testEmailDomain() {
        $v = new Email();
        
        $this->assertFalse( $v->isValid('test@test.co.m.') );
        $this->assertTrue( $v->isValid('test@test.c.om.test') );
        $this->assertFalse( $v->isValid('test@test.co-m') );
    }
}