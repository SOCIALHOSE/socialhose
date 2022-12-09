<?php

namespace AdminBundle\Form\User\DataMapper;

use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\Form\FormInterface;
use UserBundle\Entity\Subscription\AbstractSubscription;
use UserBundle\Entity\Subscription\OrganizationSubscription;
use UserBundle\Entity\Subscription\PersonalSubscription;
use UserBundle\Entity\User;

/**
 * Class MasterDataMapper
 *
 * @package AdminBundle\Form\User\DataMaper
 */
class MasterDataMapper extends PropertyPathMapper
{

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param mixed|User                   $data  Structured data.
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface}
     *                                            instances.
     *
     * @return void
     */
    public function mapDataToForms($data, $forms)
    {
        $forms = iterator_to_array($forms);

        $billingSubscription = $data->getBillingSubscription();

        $forms['email']->setData($data->getEmail());
        $forms['firstName']->setData($data->getFirstName());
        $forms['lastName']->setData($data->getLastName());
        $forms['enabled']->setData($data->isEnabled());
        if ($billingSubscription instanceof AbstractSubscription) {
            $forms['plan']->setData($billingSubscription->getPlan());
            $forms['privatePerson']->setData($billingSubscription instanceof PersonalSubscription);

            if ($billingSubscription instanceof OrganizationSubscription) {
                $forms['organizationName']->setData($billingSubscription->getOrganization());
                $forms['organizationAddress']->setData($billingSubscription->getOrganizationAddress());
                $forms['organizationEmail']->setData($billingSubscription->getOrganizationEmail());
                $forms['organizationPhone']->setData($billingSubscription->getOrganizationPhone());
            }
        }
        $forms['expirationDay']->setData($data->getExpirationDay());
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface}
     *                                            instances.
     * @param mixed|User                   $data  Structured data.
     *
     * @return void
     */
    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);

        if (isset($forms['plan'], $forms['privatePerson'])) {
            $personal = (boolean) $forms['privatePerson']->getData();

            $oldBillingSubscription = $data->getBillingSubscription();
            $newBillingSubscription = $personal
                ? new PersonalSubscription()
                : new OrganizationSubscription();

            $allowedSearches = $oldBillingSubscription->getSearchesPerDay();
            $allowedSavedFeeds = $oldBillingSubscription->getSavedFeeds();
            $allowedMasters = $oldBillingSubscription->getMasterAccounts();
            $allowedSubscribers = $oldBillingSubscription->getSubscriberAccounts();
            $allowedAlerts = $oldBillingSubscription->getAlerts();
            $allowedNewsletter = $oldBillingSubscription->getNewsletters();

            if (($personal && ($oldBillingSubscription instanceof PersonalSubscription))
                || (! $personal && ($oldBillingSubscription instanceof OrganizationSubscription))
            ) {
                $newBillingSubscription = $oldBillingSubscription;
            } else {
                $oldBillingSubscription->removeUser($data);
            }

            $newBillingSubscription
                ->setPlan($forms['plan']->getData())
                ->setSearchesPerDay($allowedSearches)
                ->setSavedFeeds($allowedSavedFeeds)
                ->setMasterAccounts($allowedMasters)
                ->setSubscriberAccounts($allowedSubscribers)
                ->setAlerts($allowedAlerts)
                ->setNewsletters($allowedNewsletter);

            if (! $personal) {
                $newBillingSubscription
                    ->setOrganization($forms['organizationName']->getData())
                    ->setOrganizationAddress($forms['organizationAddress']->getData())
                    ->setOrganizationEmail($forms['organizationEmail']->getData())
                    ->setOrganizationPhone($forms['organizationPhone']->getData());

                unset(
                    $forms['organizationName'],
                    $forms['organizationAddress'],
                    $forms['organizationEmail'],
                    $forms['organizationPhone']
                );
            }

            $data->setBillingSubscription($newBillingSubscription);

            unset($forms['plan'], $forms['privatePerson']);
        }

        parent::mapFormsToData($forms, $data);
    }
}
