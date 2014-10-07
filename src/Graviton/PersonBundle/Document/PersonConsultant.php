<?php

namespace Graviton\PersonBundle\Document;

/**
 * Graviton\PersonBundle\Document\PersonConsultant
 *
 * @category GravitonPersonBundle
 * @package  Graviton
 * @author   Lucas Bickel <lucas.bickel@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
class PersonConsultant
{
    /**
     * @var MongoId $id
     */
    protected $id;

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
     * @var string $uri
     */
    protected $uri;

    /**
     * @var string $profile
     */
    protected $profile;

    /**
     * Set uri
     *
     * @param string $uri uri
     *
     * @return self
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get uri
     *
     * @return string $uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set profile
     *
     * @param string $profile profile
     *
     * @return self
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return string $profile
     */
    public function getProfile()
    {
        return $this->profile;
    }
}
