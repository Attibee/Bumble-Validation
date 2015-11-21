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

class Email extends BaseValidator {
    const INVALID_EMAIL          = 'invalidEmail';
    const INVALID_EMAIL_USER      = 'invalidEmailUser';
    const INVALID_EMAIL_PROVIDER = 'invalidEmailProvider';
    
    protected $templates = array(
        self::INVALID_EMAIL          => 'An invalid email was provided.',
        self::INVALID_EMAIL_USER     => 'The email user is invalid.',
        self::INVALID_EMAIL_PROVIDER => 'The email provider is invalid.'
    );
    
    protected $defaults = array(
        'validateMX' => false
    );

    public function isValid( $value ) {
        $parts = explode( '@', $value );

        //don't have two parts, must be invalid
        if( count( $parts ) != 2 ) {
            $this->error( self::INVALID_EMAIL, $value );
            return false;
        }

        $user = $parts[0];
        $domain = $parts[1];

        //make sure there are not consecutive periods, dashes,plusses
        //and ensure it begins and ends with number or letter
        //allow consecutive udnerscore
        if( preg_match( '/^[a-z0-9]+(([-.+]|_+)[a-z0-9]+)*$/ixs', $user, $matches ) == false ) {
            $this->error( self::INVALID_EMAIL_USER, $value );
            return false;
        }

        //validate domain format, something like: alphanum.alphanum.alphanum
        if( preg_match( '/^[a-z0-9]+(\.[a-z0-9]+)*$/ixs', $domain ) == false ) {
            $this->error( self::INVALID_EMAIL_PROVIDER, $value );
            return false;
        }

        //validate the mx
        if( $this->getOption( 'validateMX' ) ) {
            if( !$this->isValidDomainMx( $domain ) ) {
                $this->error( self::INVALID_EMAIL_PROVIDER, $value );
                return false;
            }
        }

        return true;
    }

    private function isValidDomainMx( $domain ) {
        //we use this list of "approved" email providers. This is so we only need to check mx
        //in rare cases (majority of emails are gmail, hotmail, etc)
        $domains = array( 'aol.com', 'arnet.com.ar', 'att.net', 'bellsouth.net', 'blueyonder.co.uk', 'bt.com', 'btinternet.com', 'charter.net', 'comcast.net', 'comcast.net', 'cox.net', 'daum.net', 'earthlink.net', 'email.com', 'facebook.com', 'fastmail.fm', 'fibertel.com.ar', 'free.fr', 'freeserve.co.uk', 'games.com', 'gmail.com', 'gmail.com', 'gmx.com', 'gmx.de', 'gmx.fr', 'gmx.net', 'google.com', 'googlemail.com', 'hanmail.net', 'hotmail.be', 'hotmail.co.uk', 'hotmail.com', 'hotmail.com', 'hotmail.com.ar', 'hotmail.com.mx', 'hotmail.de', 'hotmail.es', 'hotmail.fr', 'hush.com', 'hushmail.com', 'icloud.com', 'inbox.com', 'juno.com', 'laposte.net', 'lavabit.com', 'list.ru', 'live.be', 'live.co.uk', 'live.com', 'live.com', 'live.com.ar', 'live.com.mx', 'live.de', 'live.fr', 'love.com', 'mac.com', 'mail.com', 'mail.ru', 'me.com', 'msn.com', 'msn.com', 'nate.com', 'naver.com', 'neuf.fr', 'ntlworld.com', 'o2.co.uk', 'online.de', 'orange.fr', 'orange.net', 'outlook.com', 'pobox.com', 'prodigy.net.mx', 'qq.com', 'rambler.ru', 'rocketmail.com', 'safe-mail.net', 'sbcglobal.net', 'sfr.fr', 'sina.com', 'sky.com', 'skynet.be', 'speedy.com.ar', 't-online.de', 'talktalk.co.uk', 'telenet.be', 'tiscali.co.uk', 'tvcablenet.be', 'verizon.net', 'virgin.net', 'virginmedia.com', 'voo.be', 'wanadoo.co.uk', 'wanadoo.fr', 'web.de', 'wow.com', 'ya.ru', 'yahoo.co.id', 'yahoo.co.in', 'yahoo.co.jp', 'yahoo.co.kr', 'yahoo.co.uk', 'yahoo.com', 'yahoo.com', 'yahoo.com.ar', 'yahoo.com.mx', 'yahoo.com.ph', 'yahoo.com.sg', 'yahoo.de', 'yahoo.fr', 'yandex.ru', 'ygm.com', 'ymail.com', 'zoho.com' );

        //skip mx search, we know it exists
        if( in_array( $domain, $domains ) )
            return true;

        //simple validation
        if( getmxrr( $domain ) ) {
            
            return true;
        }
        
        //gethostbynamel
        
        return false;
    }
}