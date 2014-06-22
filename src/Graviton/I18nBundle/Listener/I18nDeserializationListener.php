<?php

namespace Graviton\I18nBundle\Listener;

use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Symfony\Component\HttpFoundation\Request;
use Graviton\I18nBundle\Document\TranslatableDocumentInterface;
use Graviton\I18nBundle\Document\Translatable;
use Graviton\I18nBundle\Model\Translatable as TranslatableModel;

/**
 * translate fields during serialization
 *
 * @category I18nBundle
 * @package  Graviton
 * @author   Lucas Bickel <lucas.bickel@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
class I18nDeserializationListener
{
    /**
     * @var mixed[]
     */
    protected $localizedFields = array();

    /**
     * @var Graviton\I18nBundle\Document\Model\Translatable;
     */
    private $translatables;

    /**
     * set request
     *
     * @param \Symfony\Component\HttpFoundation\Request $request request object
     *
     * @return void
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * setup storage for translatable strings
     *
     * @param \Graviton\I18nBundle\Model\Translatable $translatables model
     *
     * @return void
     */
    public function setTranslatables(TranslatableModel $translatables)
    {
        $this->translatables = $translatables;
    }

    /**
     * remove translateable strings from object
     *
     * @param PreSerializeEvent $event event
     *
     * @return void
     */
    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $eventClass = $event->getType()['name'];
        $object = new $eventClass;
        if ($object instanceof TranslatableDocumentInterface) {
            $data = $event->getData();

            foreach ($object->getTranslatableFields() as $field) {
                $this->localizedFields[$field] = $data[$field];
                $defaultValue = \reset($data[$field]);
                if (array_key_exists('en', $data[$field])) {
                    $defaultValue = $data[$field]['en'];
                }
                $data[$field] = $defaultValue;
            }
            $event->setData($data);
        }

        return $event;
    }

    /**
     * translate all strings marked as multi lang
     *
     * @param ObjectEvent $event serialization event
     *
     * @return void
     */
    public function onPostDeserialize(ObjectEvent $event)
    {
        \array_walk(
            $this->localizedFields,
            function ($values, $field) {
                $this->createTranslatables($field, $values);
            }
        );
    }

    /**
     * create translatables for all the given languages
     *
     * @param string   $field  name of field
     * @param string[] $values values for multiple languages
     *
     * @return void
     */
    public function createTranslatables($field, $values)
    {
        if (!array_key_exists('en', $values)) {
            throw new \Exception('Creating new trans strings w/o en is not support yet.');
            // @todo generate convention based keys instead of excepting
        }
        $original = $values['en'];
        // @todo change this so it grabs all languages and not negotiated ones
        $languages = $this->request->attributes->get('languages');
        \array_walk(
            $languages,
            function ($locale) use ($original, $values) {
                $isLocalized = false;
                $translated = '';
                if (array_key_exists($locale, $values)) {
                    $translated = $values[$locale];
                    $isLocalized = true;
                }
                $translatable = new Translatable;
                $translatable->setId('messages-'.$locale.'-'.$original);
                $translatable->setLocale($locale);
                $translatable->setDomain('messages');
                $translatable->setOriginal($original);
                $translatable->setTranslated($translated);
                $translatable->setIsLocalized($isLocalized);
                $this->translatables->insertRecord($translatable);
            }
        );
    }
}
