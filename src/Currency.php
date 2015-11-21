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
 * Validates a currency field using the ISO 4217 standard. The validator will
 * parse the prefix and code from the money. It will automatically ensure the correct
 * decimal and comma format given the currency. If only one currency is to be validated
 * the prefix and code are not required; however, if multiple codes are provided
 * the validator requires the code or prefix to distinguish the currency being used.
 *
 * Options
 *    allowPrefix    A boolean, true it allows the currency code ($, £, etc), else false. DEFAULT: true
 *    allowCode   A boolean, true it allows the suffix (USD, CAD, etc), else false. DEFAULT: true 
 *    currencies    An array of allowable currencies using the code (USD, CAD, etc). DEFAULT: 'USD'
 *    codeField    Use this option if the code is in another field, such as a separate dropdown box.
 *    prefixField    Use this option if the prefix is in another field, such as a separate dropdown box. 
 * 
 */
class Currency extends BaseValidator {
    const ANY = null; //for options where any value is acceptable
    
    //template constants
    const NO_FILE_UPLOADED     = 'noFileUploaded';
    const FILE_TOO_BIG        = 'fileTooBig';
    const INVALID_FILETYPE     = 'invalidFiletype';
    
    protected $templates = array(
        self::NO_FILE_UPLOADED  => 'A file must be uploaded.',
        self::FILE_TOO_BIG        => 'The file uploaded is too large.',
        self::INVALID_FILETYPE    => 'An invalid filetype as provided.'
    );
    
    protected $defaults = array(
        'name'         => null,
        'require'     => false,
        'filesize'    => self::ANY,
        'filetype'  => self::ANY
    );
    
    public function isValid( $value = null ) {
        $name = $this->getOption('name');
        $filesize = $this->getOption('filesize');
        $filetype = $this->getOption('filetype');
        $required = $this->getOption('required');
        
        //required and file not uploaded
        if( $required && empty( $_FILES[$name]['name'] ) )  {
            $this->error( self::NO_FILE_UPLOADED );
            return false;
        }
        
        //validate file size
        if( $filesize != self::ANY && $_FILES[$name]['size'] > $filesize ) {
            $this->error( self::FILE_TOO_BIG );
        }
        
        //validate extension
        if( $filetype !== self::ANY ) {
            $name = $_FILES[$name]['name'];
            $pos = stripos( $name, '.' );
            
            //no extension, we assume the filename
            if( $pos === false ) {
                $ext = $name;
            } else {
                $ext = substr( $name, $pos + 1 );
            }

            //case insenstive
            $ext = strtolower( $ext );

            //check if in array, make sure we lowercase it all so it's case-insensitive
            if( !in_array( $ext, array_map( 'strtolower', $filetype ) ) ) {
                $this->error( self::INVALID_FILETYPE );
            }
        }
        
        //has errors, return false
        if( $this->hasMessages() ) {
            return false;
        }
        
        return true;
    }
}