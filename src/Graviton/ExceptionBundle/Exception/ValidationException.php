<?php
namespace Graviton\ExceptionBundle\Exception;

/**
 * Validation exception class
 *
 * @category GravitonExceptionBundle
 * @package  Graviton
 * @author   Manuel Kipfer <manuel.kipfer@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
class ValidationException extends \Exception
{
    /**
     * Violations
     * 
     * @var Symfony\Component\Validator\ConstraintViolationList
     */
    private $violations;

    /**
     * Response object
     * 
     * @var Symfony\Component\HttpFoundation\Response
     */
    private $response = false;
    
    /**
     * Constructor
     * 
     * @param string $message Error message
     * @param number $code    Error code
     * 
     * @return void
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }
    
    /**
     * Set violations
     * 
     * @param Symfony\Component\Validator\ConstraintViolationList $violations Violation list
     * 
     * @return \Graviton\ExceptionBundle\Exception\ValidationException $this This
     */
    public function setViolations($violations)
    {
        $this->violations = $violations;
        
        return $this;
    }
    
    /**
     * Get violation list
     * 
     * @return \Graviton\ExceptionBundle\Exception\Symfony\Component\Validator\ConstraintViolationList $violations violations
     */
    public function getViolations()
    {
        return $this->violations;
    }
    
    /**
     * Set the response object (optional)
     * 
     * @param Symfony\Component\HttpFoundation\Response $response Response object
     * 
     * @return \Graviton\ExceptionBundle\Exception\ValidationException $this This
     */
    public function setResponse($response)
    {
        $this->response = $response;
        
        return $this;
    }
    
    /**
     * Get the response object
     * 
     * @return \Graviton\ExceptionBundle\Exception\Symfony\Component\HttpFoundation\Response $response Response object
     */
    public function getResponse()
    {
        return $this->response;
    }
}