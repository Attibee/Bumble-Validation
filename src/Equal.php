<?php

/* Copyright 2015 Attibee (http://attibee.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Bumble\Validation;

/**
 * Validates that a value is equal. If the option 'strict' is true, the validator will check both value and type.
 * If it is false, only value is checked, f.e. 1 == true, '' == false, and 1 == '1'
 *
 * Example:
 *    $validator = new Equals('test');
 *  $validator->isValid('test'); //returns true
 *
 * $validator = newEquals(array( 'token' => 1, strict => false ));
 * $validator->isValid(1); //true
 * $validator->isValid(true); //true, because strict is off
 */
class Equal extends BaseValidator {
    const DOES_NOT_EQUAL = 'doesNotEqual';
    
    protected $templates = array(
        self::DOES_NOT_EQUAL => "The value must equal {token}."
    );
    
    /**
     * Constructor takes a single value and not an array. It will set the 'equals' options
     * to this value.
     */
    public function __construct( $options = null ) {
        //not an array, so we assume $options is the token
        if( !is_array( $options ) )
            $options = array( 'token' => $options );
        
        parent::__construct( $options );
    }
    
    public function isValid( $value ) {
        $token  = $this->getOption( 'token', null );
        $strict = $this->getOption( 'strict', true );
        
        //if strict, we use the !==, else we use !==
        if( $strict ) {
            if( $value !== $token ) {
                $this->error( self::DOES_NOT_EQUAL, $value );
                return false;
            }
        } else {
            if( $value != $token ) {
                $this->error( self::DOES_NOT_EQUAL, $value );
                return false;
            }
        }
        
        return true;
    }
}