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
 * Validates the length of a string. The validator is inclusive, meaning
 * strings can equal min or max.
 */
class Length extends BaseValidator {
    const INVALID_MIN  = 'invalidMin';
    const INVALID_MAX  = 'invalidMax';
    const INVALID_BOTH = 'invalidBoth';
        
    //constant to indicate any value accepted
    const ANY = 'anyValue';
    
    /**
     * Default options accepts a string of Length::ANY size.
     */
    protected $defaults = array(
        'min' => self::ANY,
        'max' => self::ANY
    );
    
    protected $templates = array(
        self::INVALID_MIN  => 'The value should be longer than {min} characters.',
        self::INVALID_MAX  => 'The value should be smaller than {max} characters.',
        self::INVALID_BOTH => 'The value should be between {min} and {max} characters.'
    );
    
    public function isValid( $value ) {
        $min = $this->getOption('min');
        $max = $this->getOption('max');
        $len = strlen( $value );
        
        //any value accepted
        if( $min == self::ANY && $max == self::ANY )
            return true;
        
        //max is any, so min must be a value
        if( $this->options['max'] == self::ANY && $len < $min )
            $this->error( self::INVALID_MIN, $value );
        //min is any, so max must be a value
        else if( $this->options['min'] == self::ANY && $len > $max )
            $this->error( self::INVALID_MAX, $value );
        //both are set
        else if( $max != self::ANY && $min != self::ANY ) {
            if( $len > $max || $len < $min )
                $this->error( self::INVALID_BOTH, $value );
        }
        
        if( $this->hasMessages() )
            return false;
        
        return true;
    }
}