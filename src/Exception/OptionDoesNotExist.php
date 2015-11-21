<?php
namespace Bumble\Validation\Exception;

/**
 * Thrown when a validator's option does not exist.
 */
class OptionDoesNotExist extends \Exception {
    public function __construct( $optionName ) {
        $message = "The validator option \"$optionName\" does not exist.";
        
        parent::__construct( $message, 0, null );
    }
}