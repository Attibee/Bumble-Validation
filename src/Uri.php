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
 * By default, the Uri validator validates ALL valid URIs. This can include a URI as complicate as
 * ftp://username:password@ftp.server.com:21/a/directory/file.php?a=b&c=d&e#anchor
 *
 * This is often undesirable as user input. For better targetted URI validation, it is recommended you
 * extend this class or use {@link AdvancedUri}, although the latter can be a very expensive
 * operation. An example derived class that limits to HTTP/HTTPS:
 *
 * class UriHttpValidator extend Uri {
 *        public function isValid( $value ) {
 *            if( !parent::isValid( $value ) ) return false;
 *
 *          $protocol = $this->URI->getProtocol();
 *            $username = $this->URI->getUsername();
 *            $password = $this->URI->getPassword();
 *
 *            if($protocol != 'http' && $protocol != 'https' || $protocol != null
 *                && $username != null && $password != null ) {
 *                $this->error( self::INVALID_URI, $value );
 *                return false;
 *            }
 *        } 
 * }
 *
 * If you still find the performance to be subpar for your application, we recommend you create your own
 * validator that targets your specific URI format.
 */
class Uri extends BaseValidator {
    //message template keys
    const INVALID_URI        = 'invalidUri';
    
    protected $templates = array(
        self::INVALID_URI        => 'An invalid URI was provided.'
    );
    
    protected $URI = null;
    
    /**
     * Validates the URI. Uses the {@link \Bumble\Uri\UriParser} to parse the link. If the link is
     * successfully parsed, it return true, else false.
     * @param $value The url to parse.
     * @return True if valid, else false.
     */
    public function isValid( $value ) {
        parent::isValid( $value );

        $parser = new \Bumble\Uri\UriParser();
        $this->URI = $parser->parse( $value );

        if( !$this->URI ) {
            $this->error( self::INVALID_URI, $value );
            return false;
        }

        if( $this->hasMessages() )
            return false;
        
        return true;
    }
}