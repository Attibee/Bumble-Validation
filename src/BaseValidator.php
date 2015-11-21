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
 * All validators are derived from the this class. To extend the class you should be
 * aware of the members $templates and $replacements and the method isValid( $value ).
 *
 * BaseValidator::$templates contains an associative array of error message templates.
 * 
 * BaseValidator::$replacements contains an associative array that maps the key
 * to the string replacement. Consider the following example purchase price validator:
 *
 * class Price extends BaseValidator {
 *     const INVALID_PRICE = 'invalidPrice';
 *     const INVALID_CURRENCY = 'invalidCurrency';
 *
 *    protected $templates = array(
 *        self::INVALID_PRICE => '{value} is an invalid price.',
 *        self::INVALID_CURRENCY => 'Only {currencyValue} is a valid currency.';
 *    );
 *
 *    protected $replacements = array( 'currency' => 'currencyValue' );
 * }
 *
 * The $templates array contain our list of valid templates. Notice inside the templates
 * we have strings wrapped in {}. {value} contains the value passed into the validator when
 * isValid( $value ) is called. {currencyValue} is the value to our option BaseValidator::options['currency'].
 * Notice that BaseValidator::$replacements maps the key to its replacement name.
 *
 * Now consider the isValid method:
 * public function isValid( $value ) {
 *        //check if the currency option equals the first character
 *        if( $value[0] != $this->getOption( 'currency' ) ) {
 *             $this->error( self::INVALID_CURRENCY, $value );
 *            return false;
 *        }
 * }
 *
 * To add an error message we call BaseValidator::error( $messageKey, $value ). This will find the correct
 * template and build the message.
 */
abstract class BaseValidator {
    //key used when we replace local messages with the global message
    const GLOBAL_MESSAGE_KEY = 'globalMessage';

    //list of message templates
    protected $templates = array();
    
    //list of error messages
    protected $messages = array();
    
    //list of options
    protected $defaults = array();
    
    //list of replacements
    protected $replacements = array();
    
    //the list of options
    protected $options = array();
    
    /**
     * Returns true if the $value is valid, else false.
     * @param $value the value to validate
     * @return True if $value is valid, else false.
     */
    abstract public function isValid( $value );
    
    /**
     * Sets the options. Options is expected to be an array.
     * @param $options The array of options. Optional.
     */
    public function __construct( $options = null ) {
        if( is_array( $options ) )
            $this->setOptions( $options );
        
        //add defaults if they were not set
        foreach( $this->defaults as $key=>$default ) {
            if( !array_key_exists( $key, $this->options ) )
                $this->options[$key] = $default;
        }
    }
    
    /**
     * Adds an error message to the list of errors.
     * @param $messageKey The key of the message template.
     * @param $value The value that is being validated.
     * @throws Exception\MessageTemplateDoesNotExist The message template does not exist.
     */
    protected function error( $messageKey, $value = null ) {
        //was a global message set? let's use the global key then
        if( key_exists( self::GLOBAL_MESSAGE_KEY, $this->templates ) )
            $messageKey = self::GLOBAL_MESSAGE_KEY;
        
        //invalide message key
        if( !key_exists( $messageKey, $this->templates ) )
            throw new Exception\MessageTemplateDoesNotExist( "A message template does not exist for key $messageKey" );

        $this->messages[ $messageKey ] = $this->buildMessage( $this->templates[$messageKey], $value );
    }
    
    /**
     * Updates the message template. This overrides existing message templates.
     * @param $template The message template.
     * @param $key The message key.
     */
    private function updateMessageTemplate( $template, $key ) {    
        //invalide message key
        if( !key_exists( $messageKey, $this->templates ) && $key != self::DEFAULT_KEY )
            throw new Exception\MessageTemplateDoesNotExist( "A message template does not exist for key $messageKey" );

        $this->templates[$key] = $template;
    }
    
    /**
     * This overrides existing message templates. All error messages use this single template.
     * @param $template The message template.
     * @param $key The message key.
     */
    private function overrideTemplates( $template ) {
        $this->templates = [];
        $this->templates[self::GLOBAL_MESSAGE_KEY] = $template;
    }
    
    /**
     * Returns the parsed template messsage. $value is optional an may not always be passed in. It's recommended
     * that derived classes always pass in $value since users may override the template with their own.
     * @param $template The template to parse.
     * @param $value The value to replace in the message template. Optional.
     * @return The parsed template message.
     */
    private function buildMessage( $template, $value ) {
        if( $value )
            $template = str_replace( '{value}', $value, $template );
        
        //add in the options values
        foreach( $this->replacements as $key ) {
            if( !isset( $this->options[$key] ) ) continue;
            
            $replacement = $this->options[$key];
            
            //cannot pass in array, object, etc
            $template = str_replace( '{' . $key . '}', $replacement, $template );
        }
        
        return $template;
    }
    
    /**
     * Returns true if there are error messages, else false.
     * @return True if there are error messages, else false.
     */
    public function hasMessages() {
        return count( $this->messages ) > 0;
    }
    
    /**
     * Sets the option $name given the $value.
     * @param string $name The name of the option.
     * @param string $value The value of the option.
     */
    public function setOption( $name, $value ) {
        if( key_exists( $name, $this->options) )
            $this->options[$name] = $value;
        else
            throw new Exception\OptionDoesNotExist( $name );
    }
    
    /**
     * Sets the options give an array.
     * @param array $options An array of options.
     */
    public function setOptions( $options ) {
        $this->options = $options;
    }
    
    /**
     * Gets the option by the key $name.
     * @param string $name    The name of the option.
     * @param mixed  $default The value to return if key $name does not exist.
     * @return The value of the option. If 
     */
    public function getOption( $name ) {
        if( array_key_exists( $name, $this->options ) )
            return $this->options[$name];
    }
    
    /**
     * Returns the array of error messages.
     * @return An array of error messages.
     */
    public function getMessages() {
        return $this->messages;
    }
}