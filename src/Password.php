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
 * Validates a password. This forces a user to create a very secure password. It is highly
 * recommended that the default settings are used.
 *
 *
 */
class Password extends BaseValidator {
    //template keys
    const INVALID_LENGTH        = 'invalidLength';
    const INVALID_TOPOLOGY      = 'invalidTopology';
    const NO_UPPERCASE          = 'noUppercase';
    const NO_SPECIAL_CHARACTER  = 'noSpecialCharacter';
    const NO_DIGIT              = 'noDigit';
    
    //templates
    protected $templates = array(
        self::INVALID_TOPOLOGY      => 'Your password is very common. Try adding more numbers, special characters, or capitalizations.',
        self::INVALID_LENGTH        => 'Your password must be greater than {minimumLength} characters.',
        self::NO_UPPERCASE          => 'The password must contain an uppercase letter.',
        self::NO_SPECIAL_CHARACTER  => 'The password must contain a special character.',
        self::NO_DIGIT              => 'The password must contain a digit.'
    );
    
    /**
     * Contains a list of topologies such that u = uppercase, l = lowercase, d = digit
     * and s = special character. Any password that matches this list is automatically
     * invalid. For the strongest protection, you should update the topology list to the
     * most common topologies in your own database.
     */
    protected $topologies = array('ullllldd','ulllllldd','ullldddd','llllllld','ullllllldd','ulllllld','ullllldddd','ulllldddd','lllllldd','ullllllld','ullllddd','ulldddds','llllllll','ulllllddd','llllllldd','llsddlddl','lllllllld','ullllldds','ulllllldddd','ulllllllldd','ulllllds','ulllllllld','ullllldddds','lllllllll','lllllllldd','ullllllddd','lllllddd','ullldddds','ullllllldddd','ulllllsdd','uuuuuudl','lllldddd','ddulllllll','ullsdddd','ulllldds','ullllllds','ddullllll','llllsddd','llllllllld','llllldddd','llllllllll','llllllddd','ullllllllldd','ullllllllld','ddddddul','ulllllllddd','ulllllldds','uuuuuuds','uudllldddu','ullllsdd','ulllllsd','lllsdddd','lllllldddd','ullllllldds','ddulllll','ulllllllds','ullllddds','ulllldddds','ulllsdddd','ullllsddd','ulllllldddds','ulllddds','llllsdddd','llllllsdd','lllllldds','ddddulll','dddddddd','ullllllsd','uldddddd','llllllsd','udllllllld','lllllllllll','lllllllllld','llllldds','llllddds','ulllllllldddd','uuuuuuuu','ulllsddd','ullllllsdd','ulllllddds','lllllsdd','ullllsdddd','ulllddddd','ulldddddd','ullddddd','llllllllldd','llllllldds','lllllllddd','llllllds','llldddds','uuullldddd','ulllllsddd','ulllllllsd','llllllllsd','llllllldddd','ulllllsdddd','lllllllds','lllldddds','ddddullll','uudllldddd');
    //protected $graphenes = array(
    /*
     * Default options.
     */
    protected $defaults = array(
        'minimumLength'     => 8,    //password must be 8 chars
        'requireSpecial'    => true, //must have special character
        'requireUppercase'  => true, //must have uppercase
        'requireDigit'      => true, //must have a digit
        'matchTopologies'   => true, //will deny passwords that are a common topology
        'maxLength'         => 128
    );
    
    /**
     * Converts a string to a topology such that uppercase, lowercase, digit and
     * special characters to u, l, d, and s, respectively. 
     * @param  string $str 
     * @return string The string converted to a topology
     */
    public function strToTopology( $str ) {
        $len = strlen( $str );
        $data = array(
            'str' => '',
            'u' => 0,
            'l' => 0,
            'd' => 0,
            's' => 0
        );
        
        //loop through each letter and build the topology
        //and count the letter types
        for( $i = 0; $i < $len; $i++ )  {
            if( ctype_upper( $str[$i]  ) ) {
                    $str[$i] = 'u';
                    $data['u']++;
            } elseif( ctype_lower( $str[$i] ) ) {
                    $str[$i] = 'l';
                    $data['l']++;
            } elseif( ctype_digit( $str[$i] ) ) {
                    $str[$i] = 'd';
                    $data['d']++;
            } else {
                    $str[$i] = 's';
                    $data['s']++;
            }
        }

        $data['str'] = $str;
        
        return $data;
    }

    public function isValid( $value ) {
        $topology = $this->topology( (string)$value );
        
        //match topology
        if( $this->getOption( 'matchTopologies' ) && !in_array( $topology['str'], $this->topologies ) ) {
            $this->error( self::INVALID_TOPOLOGY, $value );
        }

        //enforce minimum length
        if( strlen( $value ) < $this->getOption( 'minimumLength' ) ) {
            $this->error( self::INVALID_LENGTH, $value );
        }

        //require uppercase and no uppercase exists
        if( $this->getOption( 'requireUppercase' ) && $topology['u'] == 0 ) {
            $this->error( self::NO_UPPERCASE, $value );
        }
        
        //require special and no special exists
        if( $this->getOption( 'requireSpecial' ) && $topology['s'] == 0 ) {
            $this->error( self::NO_SPECIAL_CHARACTER, $value );
        }
        
        //require digit and no digit exists
        if( $this->getOption( 'requireDigit' ) && $topology['d'] == 0 ) {
            $this->error( self::NO_DIGIT, $value );
        }

        if( $this->hasMessages() ) {
            return false;
        }

        return true;   
    }
    
    /**
     * Returns a number that indicates the strength of a topology. The password strength
     * does not consider the characters but only character type. It is found that humans
     * follow patterns based on topologies of uppercase, lowercase, digits, and special
     * characters.
     */
    public function getPasswordStrength( $password ) {
        $topology = $this->strToTopology( $password );
        $str = $topology['str'];
        $upper = $topology['u'];
        $lower = $topology['l'];
        $digit = $topology['d'];
        $special = $topology['s'];
        $len = strlen( $str );

        $pairs = unserialize( file_get_contents( __DIR__ . '/phoneme.php' ) );
        
        //start at 0
        $value = 0;
        
        //length of password adds 1 to the score
        $value += $len * 0.5;
        
        //bonus for a mix of upper, digit, and special
        $value += $upper*0.5 + $digit*0.5 + $special / $len * 50;

        //===============================//
        //bonus for each none lowercase in the center
        for( $i = 1; $i < $len - 1; $i++ ) {
            //the position goes 1 2 3 2 1, not 1 2 3 4 5
            $pos = ($i > $len / 2) ? $len - $i : $i;
            
            if( $str[$i] != 'l' ) {
                $value += $pos / $len * 10; //add bonus for distance from edge
            }    
        }
        
        //===============================//
        //consecutive characters are bad
        $prev = null;
        $prevTopo = null;
        
        for( $i = 0; $i < $len; $i++ ) {
            if( $password[$i] == $prev ) {
                $value -= 1.0;
            }

            if( $str[$i] !== $prevTopo ) {
                $value -= 1.0;
            }
            
            $prevTopo = $str[$i];
            $prev = $password[$i];
        }
        
        //===============================//
        //character set
        $set = array();
        
        for( $i = 0; $i < $len; $i++ ) {
            $set[$password[$i]] = null;
        }
        
        $value += count( $set );
        
        //===============================//
        //check pairs
        $pairTotal = 0;
        
        foreach( $pairs as $phoneme=>$c ) {
            if( strpos( $password, $phoneme ) !== false )
                $pairTotal += $c * 100;
        }
        
        $value -= $pairTotal;
        
        //===============================//
        //good mix of ULDS?
        $value -= 10*(abs(0.25 - $upper/$len) + abs(0.25 - $lower/$len) + abs(0.25 - $digit/$len) + abs(0.25 - $special/$len));
        return $value;
    }
}