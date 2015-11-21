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
 * Validates a directory. By default, the directory separator is PHP DIRECTORY_SEPARATOR,
 * however this may be changed in the options with the key directorySeparator.
 */
class Directory extends BaseValidator {
    const INVALID = 'invalid';
    
    protected $options = array(
        'directorySeparator' => DIRECTORY_SEPARATOR
    );
    
    protected $templates = array(
        self::INVALID => 'The value cannot be empty.'
    );
    
    public function isValid( $value ) {
        //if( $this->getOption( 'allowRelativeUrl' ) == true )
        //    $this->isValidRelative( $value );
    
        //relative url regex
        //preg_match( '/(.\/|\/)[a-z0-9-]/?/i', $value, $i)
        
        
        return true;
    }
}