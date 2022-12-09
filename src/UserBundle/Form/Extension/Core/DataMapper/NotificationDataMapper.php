<?php

namespace UserBundle\Form\Extension\Core\DataMapper;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\Form\FormInterface;
use UserBundle\Entity\Notification\Notification;

/**
 * Class NotificationDataMapper
 * @package UserBundle\Form\Extension\Core\DataMapper
 */
class NotificationDataMapper extends PropertyPathMapper
{

    /**
     * List of required form fields.
     *
     * @var string[]
     */
    private static $requiredFields = [
        'sources',
        'automatic',
        'plainDiff',
        'enhancedDiff',
    ];

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param mixed                 $data  Structured data.
     * @param FormInterface[]|array $forms A list of {@link FormInterface} instances.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function mapDataToForms($data, $forms)
    {
        // Do nothing because it's not necessary.
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface}
     *                                            instances.
     * @param mixed                        $data  Structured data.
     *
     * @return void
     *
     * @throws UnexpectedTypeException If the type of the data parameter is not
     * supported.
     */
    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);
        if (! $data instanceof Notification) {
            throw new UnexpectedTypeException($data, Notification::class);
        }

        //
        // Check that all required fields is set.
        //
//        if (\Functional\some(self::$requiredFields, function ($name) use ($forms) {
//            return ! isset($forms[$name]);
//        })) {
        if (\nspl\a\any(self::$requiredFields, function ($name) use ($forms) {
            return ! isset($forms[$name]);
        })) {
            throw new UnexpectedTypeException(current($forms)->getParent(), Notification::class);
        }

        //
        // Map sources.
        //
        $sources = $forms['sources']->getData();

        // todo uncomment and refactor when analytics is added
        // if (($sources['feeds'] !== null) && ($sources['charts'] !== null)) {
        if (isset($sources['feeds'])) {
            // Do not map data if we have null.
            $data
                ->setFeeds($sources['feeds']);
        }

        //
        // Map schedules.
        //
        $data->setSchedules($forms['automatic']->getData());

        //
        // Map diffs.
        //
        $data->setPlainThemeOptionsDiff($forms['plainDiff']->getData());
        $data->setEnhancedThemeOptionsDiff($forms['enhancedDiff']->getData());

        //
        // Remove all processed fields.
        //
//        \Functional\each(self::$requiredFields, function ($name) use (&$forms) {
//            unset($forms[$name]);
//        });
        foreach (self::$requiredFields as $name) {
            unset($forms[$name]);
        }

        //
        // Map other fields.
        //
        parent::mapFormsToData($forms, $data);
    }
}
