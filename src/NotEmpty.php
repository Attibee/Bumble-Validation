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
 * Validates if a value is empty.
 */
class NotEmpty extends BaseValidator {
    const INVALID = 'invalid';
    
    protected $templates = array(
        self::INVALID => 'The value cannot be empty.'
    );
    
    public function isValid( $value ) {
        $value = (string)$value; //empty strings, null and false will be empty
        
        if( $value == '' ) {
            $this->error( self::INVALID, $value );
            return false;
        }
        
        return true;
    }
}