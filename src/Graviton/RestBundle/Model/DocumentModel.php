<?php

namespace Graviton\RestBundle\Model;

use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Use doctrine odm as backend
 *
 * @category GravitonRestBundle
 * @package  Graviton
 * @author   Lucas Bickel <lucas.bickel@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
class DocumentModel implements ModelInterface
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * create new app model
     *
     * @param ObjectRepository $countries Repository of countries
     *
     * @return void
     */
    public function setRepository(ObjectRepository $countries)
    {
        $this->repository = $countries;
    }

    /**
     * get repository instance
     *
     * @return ObjectRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * {@inheritDoc}
     *
     * @param String $id id of entity to find
     *
     * @return Object
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritDoc}
     *
     * @return Array
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     *
     * @param object $entity entityy to insert
     *
     * @return Object
     */
    public function insertRecord($entity)
    {
        $dm = $this->repository->getDocumentManager();
        $res = $dm->persist($entity);
        $dm->flush();

        return $this->find($entity->getId());
    }

    /**
     * {@inheritDoc}
     *
     * @param String $id     id of entity to update
     * @param Object $entity new enetity
     *
     * @return Object
     */
    public function updateRecord($id, $entity)
    {
        $dm = $this->repository->getDocumentManager();
        $dm->persist($entity);
        $dm->flush();

        return $entity;
    }

    /**
     * {@inheritDoc}
     *
     * @param String $id id of entity to delete
     *
     * @return Boolean
     */
    public function deleteRecord($id)
    {
        $dm = $this->repository->getDocumentManager();
        $entity = $this->find($id);

        $return = false;
        if ($entity) {
            $dm->remove($entity);
            $return = true;
        }

        return $return;
    }

    /**
     * get classname of entity
     *
     * @return String
     */
    public function getEntityClass()
    {
        return $this->repository->getDocumentName();
    }

    /**
     * {@inheritDoc}
     *
     * Currently this is being used to build the route id used for redirecting
     * to newly made documents.
     *
     * We might use a convention based mapping here:
     * Graviton\CoreBundle\Document\App -> mongodb://graviton_core
     * Graviton\CoreBundle\Entity\Table -> mysql://graviton_core
     *
     * @todo implement this in a more convention based manner
     *
     * @return String
     */
    public function getConnectionName()
    {
        return 'graviton.core';
    }
}