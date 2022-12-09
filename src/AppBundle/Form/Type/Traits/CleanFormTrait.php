<?php

namespace AppBundle\Form\Type\Traits;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;

/**
 * Trait CleanFormTrait
 *
 * @package AppBundle\Form\Type\Traits
 */
trait CleanFormTrait
{

    /**
     * Remove all unused form fields.
     *
     * @param FormEvent $event A FormEvent instance.
     *
     * @return void
     */
    public function clean(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $available = array_keys(iterator_to_array($form));

        if (is_array($data)) {
            $exists = array_keys($event->getData());
            $unknown = array_diff($exists, $available);

            if (count($unknown) > 0) {
                //
                // Remove all filters to avoid unnecessary validation error
                // messages.
                //
                foreach ($available as $filter) {
                    $form->remove($filter);
                }

                $form->addError(new FormError(sprintf(
                    'Unknowns fields: %s.',
                    implode(', ', $unknown)
                )));

                return;
            }

            //
            // Remove filters which not exists in request.
            //
            $notUsed = array_diff($available, $exists);
            foreach ($notUsed as $filter) {
                $form->remove($filter);
            }
        } else {
            //
            // Remove all filters to avoid unnecessary validation error
            // messages.
            //
            $event->setData([]);
            foreach ($available as $filter) {
                $form->remove($filter);
            }
        }
    }
}
