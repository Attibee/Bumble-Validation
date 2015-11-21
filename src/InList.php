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
 * Validates that a value is part of a list. Often used with <select> boxes to
 * validate a valid item was selected from the select box. By default, the validator
 * uses strict checking. This means the value AND type must match. Use the option 'strict'
 * set to false to change this behavior.
 *
 * Example:
 *  $validator = new Bumble\Validation\List(array( 'red, 'orange', 'blue', 'green' ));
 *  $validator->isValid('green'); //returns true
 *  $validator->isValid('black'); //returns false
 *
 * Example:
 *    $validator = new Bumble\Validation\List(array( 'red, 'orange', 'blue', 'green' ));
 *  $validator->isValid('green'); //returns true
 *  $validator->isValid('black'); //returns false
 */
class InList extends BaseValidator {
    const NOT_IN_LIST = 'notInList';

    protected $templates = array(
        self::NOT_IN_LIST => "An invalid value was provided."
    );
    
    public function  __construct( $options = null ) {
        //it's not associative, so this means the options is the list
        if( array_keys( $options ) === range( 0, count($options) - 1 ) )
            $options = array( 'list' => $options );
        
        parent::__construct( $options );
    }
    
    public function isValid( $value ) {
        $list = $this->getOption( 'list', array() );
        $strict = $this->getOption( 'strict', true );
        
        //if it exists in the list, we return true
        foreach( $list as $token ) {
            if( $strict && $value === $token )
                return true;
            else if( !$strict && $value == $token )
                return true;
        }
        
        //if we got here, it didn't exists
        $this->error( self::NOT_IN_LIST, $value );
        
        return false;
    }
}