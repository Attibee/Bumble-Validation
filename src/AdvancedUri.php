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
 * Validates if a URI is valid. The validator validates absolute urls (must include a domain).
 * By default, the Uri validator validates all valid urls. To restrict this, you can use the
 * following is a list of options. The first option is always default.
 *         allowableProtocol => Url::ANY | Url::NONE | array( 'http', 'ftp', ... )
 *         allowableDomain => Url::ANY | Url:NONE | array( 'google', 'mail.google', ... )
 *         allowableTld => Url::ANY | Url:NONE | array( 'com', 'net', ... )
 *         allowablePort => Url::ANY | Url:NONE | array( '80', '8080', ... )
 *        allowablePath => Url::ANY | Url:NONE | array( '/path/', /path/file.php', ... )
 *        allowableQuery => Url::ANY | Url:NONE | array( 'a=b&c=d', ... )
 *        allowableAnchor => Url::ANY | Url::NONE | array( 'anchor1', 'top', ... )
 *
 * Example:
 *        //allow only simple http urls
 *         $uri = new Uri(array(
 *            'allowableProtocol' => array( 'http', 'https' ),
 *            'allowablePort' => Uri::NONE,
 *            'allowablePath' => Uri::NONE,
 *            'allowableQuery' => Uri::NONE,
 *            'allowableAnchor' => Uri::NONE
 *        );
 *
 *        $uri->isValid( 'google.com' ); //valid
 *        $uri->isValid( 'http://google.com' ); //valid
 *        $uri->isValid( 'google.com/random/directory/' ); //not valid!
 */
class AdvancedUri extends Uri {
    //message template keys
    const INVALID_USERNAME = 'invalidUsername';
    const INVALID_PASSWORD = 'invalidPassword';
    const INVALID_PROTOCOL = 'invalidProtocol';
    const INVALID_DOMAIN   = 'invalidDomain';
    const INVALID_TLD       = 'invalidTld';
    const INVALID_PORT     = 'invalidPort';
    const INVALID_PATH     = 'invalidPath';
    const INVALID_QUERY    = 'invalidQuery';
    const INVALID_ANCHOR   = 'invalidAnchor';
    
    //allow any url component
    const ANY = '__anyAllowable__';
    
    //allow no url component
    const NONE = '__noneAllowable__';
    
    protected $options = array(
        'allowableProtocol' => self::ANY,
        'allowableUsername' => self::ANY,
        'allowablePassword' => self::ANY,
        'allowableDomain' => self::ANY,
        'allowableTld' => self::ANY,
        'allowablePort' => self::ANY,
        'allowablePath' => self::ANY,
        'allowableQuery' => self::ANY,
        'allowableAnchor' => self::ANY,
    );
    
    protected $templates = array(
        self::INVALID_URI        => 'An invalid URI was provided.',
        self::INVALID_USERNAME => 'An invalid username was provided',
        self::INVALID_PASSWORD => 'An invalid password was provided.',
        self::INVALID_PROTOCOL => 'An invalid protocol was provided.',
        self::INVALID_DOMAIN   => 'An invalid domain was provided.',
        self::INVALID_TLD      => 'An invalid tld was provided.',
        self::INVALID_PORT     => 'An invalid port was provided.',
        self::INVALID_PATH     => 'An invalid path was provided.',
        self::INVALID_QUERY    => 'An invalid query was provided.',
        self::INVALID_ANCHOR   => 'An invalid anchor was provided.'
    );
    
    public function isValid( $value ) {
        if( !parent::isValid( $value ) ) return false;

        $keys = array( 'Protocol', 'Username', 'Password', 'Domain', 'Tld', 'Port', 'Path', 'Anchor' );
        
        foreach( $keys as $key ) {
            $method = "get" . $key;
            
            if( !$this->validate( $key, $this->URI->$method() ) ) {
                $key = 'invalid' . $key;
                $this->error( $key, $value );
            }
        }

        if( $this->hasMessages() )
            return false;
        
        return true;
    }
    
    protected function validate( $name, $value ) {
        $opt = $this->getOption( "allowable$name" );
        
        //any, we always return true
        if( $opt == self::ANY )
            return true;
        else if( $opt == self::NONE && $value == null )
            return true;
        else if( is_array( $opt ) && in_array( $value, $opt ) ) //array of values to match, check if it exists
            return true;


        return false;
    }
}