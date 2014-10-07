<?php

namespace Graviton\PersonBundle\Document;

/**
 * Graviton\PersonBundle\Document\Customer
 *
 * @category GravitonPersonBundle
 * @package  Graviton
 * @author   Lucas Bickel <lucas.bickel@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
class Customer
{
    /**
     * @var MongoId $id
     */
    protected $id;

    /**
     * @var string $firstName
     */
    protected $firstName;

    /**
     * @var string $lastName
     */
    protected $lastName;

    /**
     * constructor
     *
     * @return self
     */
    public function __construct()
    {
    }

    /**
     * Get id
     *
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get firstName
     *
     * @return string $firstName
     */
    public function getFirstname()
    {
        return $this->firstName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName value for firstName
     *
     * @return self
     */
    public function setFirstname($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string $lastName
     */
    public function getLastname()
    {
        return $this->lastName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName value for lastName
     *
     * @return self
     */
    public function setLastname($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }
    /**
     * @var Graviton\PersonBundle\Document\PersonConsultant
     */
    protected $consultant;

    /**
     * Set consultant
     *
     * @param Graviton\PersonBundle\Document\PersonConsultant $consultant consultant
     *
     * @return self
     */
    public function setConsultant(\Graviton\PersonBundle\Document\PersonConsultant $consultant)
    {
        $this->consultant = $consultant;

        return $this;
    }

    /**
     * Get consultant
     *
     * @return Graviton\PersonBundle\Document\PersonConsultant $consultant
     */
    public function getConsultant()
    {
        return $this->consultant;
    }
}
