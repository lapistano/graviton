<?php

namespace Graviton\PersonBundle\DataFixtures\MongoDB;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Graviton\PersonBundle\Document\Customer;

/**
 * Load a example customer and loads of fake data
 *
 * @category PersonBundle
 * @package  Graviton
 * @author   Lucas Bickel <lucas.bickel@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
class LoadCustomerData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @private ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     *
     * @param ContainerInterface $container service_container
     *
     * @return void
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     *
     * @param ObjectManager $manager Object Manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $serializer = $this->container->get('serializer');
        $faker = $this->container->get('test.faker');
        $router = $this->container->get('router');
        // this is rather fragile since it depends on consultants being loaded already, atm this is by alphabet
        $consultants = $this->container->get('graviton.person.repository.consultant');
        $maxCustomers = 150;

        for ($i = 0; $i < $maxCustomers; $i++) {
            // use round(150/x)+1 to get a distribution that is somewhat top heavy
            $skipNum = $i;
            if ($i !== 0) {
                $skipNum = round($maxCustomers/3*2/$i)+1;
            }
            if ($skipNum > 14) {
                $skipNum = 14;
            }
            $consultant = $consultants->createQueryBuilder()->skip($skipNum)->getQuery()->getSingleResult();
            $consultantId = $consultant->getId();
            $consultant = array(
                'id' => $consultantId,
                '$ref' => $router->generate('graviton.person.rest.consultant.get', array('id' => $consultantId), true),
                'profile' => $router->generate('graviton.person.rest.consultant.canonicalIdSchema', array(), true)
            );

            $faker->seed($i);
            $customer = array(
                'id' => strtoupper($faker->bothify('????###??')),
                'firstName' => $faker->firstName,
                'lastName' => $faker->lastName,
                'consultant' => $consultant
            );
            $manager->persist(
                $serializer->deserialize(
                    json_encode($customer),
                    'Graviton\PersonBundle\Document\Customer',
                    'json'
                )
            );
        }
        $manager->flush();
    }
}
