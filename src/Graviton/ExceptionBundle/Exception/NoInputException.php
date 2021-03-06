<?php
namespace Graviton\ExceptionBundle\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * No input exception class
 *
 * @category GravitonExceptionBundle
 * @package  Graviton
 * @author   Manuel Kipfer <manuel.kipfer@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
final class NoInputException extends RestException
{
    /**
     * Constructor
     *
     * @param string     $message Error message
     * @param \Exception $prev    Previous Exception
     *
     * @return void
     */
    public function __construct($message = "No Conent", $prev = null)
    {
        parent::__construct($message, Response::HTTP_BAD_REQUEST, $prev);
    }
}
