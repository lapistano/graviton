<?php

namespace Graviton\I18nBundle\Listener;

use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;

class I18nSerializationListener
{
    /**
     * @var mixed[]
     */
    protected $i18nFields = array();

    /**
     * @var Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    private $translator;

    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * set request
     *
     * @param Symfony\Component\HttpFoundation\Request $request request object
     *
     * @return void
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * set translator
     *
     * @param Symfony\Bundle\FrameworkBundle\Translation\Translator $translator translator
     *
     * @return void
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * remove translateable strings from object
     *
     * @param PreSerializeEvent $event event
     *
     * @return void
     */
    public function onPreSerialize(PreSerializeEvent $event)
    {
        $object = $event->getObject();
        if (method_exists($object, 'setName')) {
            $this->i18nFields['name'] = $object->getName();
            $object->setName(null);
        }
    }

    /**
     * translate all strings marked as multi lang
     *
     * @param ObjectEvent $event serialization event
     *
     * @return void
     */
    public function onPostSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();
        if (method_exists($object, 'setName')) {
            foreach ($this->i18nFields AS $field => $value) {
                $event->getVisitor()->addData(
                    $field,
                    $this->getTranslatedField($value)
                );
            }
        }
    }

    /**
     * build a complete translated field
     *
     * @param string $value value to translate
     *
     * @return array
     */
    private function getTranslatedField($value)
    {
        return array_map(
            function ($language) use ($value) {
                return $this->translator->trans($value);
            },
            $this->request->attributes->get('languages')
        );
    }
}
