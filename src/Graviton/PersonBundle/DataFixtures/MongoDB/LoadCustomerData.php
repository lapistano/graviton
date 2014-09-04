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

        for ($i = 0; $i < 15; $i++) {
            // seed like the first third of consultants
            $faker->seed($i);
            $consultant = array(
                '$ref' => $router->generate('graviton.person.rest.consultant.get', array('id' => strtoupper($faker->bothify('????###??'))), true),
                'profile' => $router->generate('graviton.person.rest.consultant.canonicalIdSchema', array(), true)
            );

            // and seed again with large offset to make sure we don't get the same seeds as consultants
            $faker->seed($i * 100);
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
