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
 * Validates a file upload. This includs file size and filetype.
 *
 * Options
 *    name:      The name of the input field. DEFAULT: null
 *
 *  require:  A boolean, true if it is required, else false. DEFAULT: false
 *
 *    filesize: An integer to indicate number of bytes. If any size is allowed, use
 *              Upload::ANY. DEFAULT: Upload::ANY
 *
 *    filetype: An array of accepted file extensions. Use Upload::ANY to allow any
 *              file extension. DEFAULT: Upload::ANY
 */
class Upload extends BaseValidator {
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