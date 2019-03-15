<?php
/**
 * Exception
 * PHP Version 7
 *
 * @category Exception
 * @package  Nathan\ContentBuilder\Exceptions
 * @author   Nathan Bastiaans <contact@nathanbastiaans.nl>
 */
namespace Nathan\ContentBuilder\Exceptions;

/**
 * This exception will be thrown when the model isn't compatible with the content builder
 *
 * @category Exception
 * @package  Nathan\ContentBuilder\Exceptions
 * @author   Nathan Bastiaans <contact@nathanbastiaans.nl>
 */
class IncompatibleModelException extends \Exception
{
    /**
     * IncompatibleModelException constructor.
     *
     * We extend the exception class and create a new constructor to make sure the
     * message is required when we throw the exception.
     *
     * @param string          $message  The message to be shown
     * @param int             $code     The error code
     * @param \Exception|null $previous Previous exception
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}