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

use DateTime;

/**
 * Validates a date given a format. By default, this format is the ISO8601 standard.
 *
 * The date format uses the PHP compatible format: {@link http://php.net/manual/en/datetime.createfromformat.php}
 *
 * The constructor accepts both a single parameter or the option "format".
 * 
 * Example:
 *    $validator = new Bumble\Validation\Date('F n, Y');
 *  $validator->isValid('October 28, 1988'); //returns true
 *  $validator->isValid('10-28-1988'); //returns false
 */
class Date extends BaseValidator {
    const INVALID_DATE = 'invalidDate';
    
    protected $templates = array(
        self::INVALID_DATE => "The date does not match the format {dateFormat}."
    );
    
    protected $replacements = array( 'format' => 'dateFormat' );
    
    /**
     * Accepts either a single date format string or an array with option 'format.'
     *
     * @param mixed $options A string format or an array with option 'format.'
     */
    public function __construct( $options = null ) {
        //not an array, so we assume $options is the token
        if( !is_array( $options ) )
            $options = array( 'format' => $options );
        
        parent::__construct( $options );
    }
    
    public function isValid( $value ) {
        $format = $this->getOption( 'format', DateTime::ISO8601 );
        
        $date = DateTime::createFromFormat( $format, $value );

        //no date object returned and has warnings, so must be false
        if( !$date || DateTime::getLastErrors()['warning_count'] > 0 ) {
            $this->error( self::INVALID_DATE, $value );
            return false;
        }

        return true;
    }
}