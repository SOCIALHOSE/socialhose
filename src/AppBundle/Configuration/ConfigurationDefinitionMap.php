<?php

namespace AppBundle\Configuration;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Traversable;

/**
 * Class ConfigurationDefinitionMap
 * @package AppBundle\Configuration
 */
class ConfigurationDefinitionMap implements \IteratorAggregate
{

    /**
     * @var string[]
     */
    private static $availablePeriods = [
        'day',
        'days',
        'week',
        'weeks',
        'month',
        'months',
        'year',
        'years',
        'hour',
        'hours',
        'minute',
        'minutes',
        'second',
        'seconds',
    ];

    /**
     * @var array[]
     */
    private $definitions;

    /**
     * ConfigurationDefinitionMap constructor.
     */
    public function __construct()
    {
        $this->definitions = [
            ParametersName::MAILER_ADDRESS => [
                'section' => 'Mailer',
                'title' => 'support@socialhose.io',
                'type' => 'string',
                'formType' => null,
                'default' => 'support@socialhose.io',
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'string' ]),
            ],
            ParametersName::MAILER_SENDER_NAME => [
                'section' => 'Mailer',
                'title' => 'Socialhose',
                'type' => 'string',
                'formType' => null,
                'default' => 'Socialhose',
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'string' ]),
            ],
            ParametersName::NOTIFICATION_COMMENTS_PER_DOCUMENT => [
                'section' => 'Notification',
                'title' => 'Max comments per document',
                'type' => 'integer',
                'formType' => null,
                'default' => 5,
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'numeric' ]),
            ],
            ParametersName::NOTIFICATION_DOCUMENT_PER_FEED => [
                'section' => 'Notification',
                'title' => 'Max documents per feed in notification',
                'type' => 'integer',
                'formType' => null,
                'default' => 10,
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'numeric' ]),
            ],
            ParametersName::NOTIFICATION_START_EXTRACT_LENGTH => [
                'section' => 'Search',
                'title' => 'Number of character for \'Start of text extract\'',
                'type' => 'integer',
                'formType' => null,
                'default' => 400,
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'numeric' ]),
            ],
            ParametersName::NOTIFICATION_CONTEXT_EXTRACT_LENGTH => [
                'section' => 'Search',
                'title' => 'Numbers of character before and after first search keyword',
                'type' => 'integer',
                'formType' => null,
                'default' => 150,
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'numeric' ]),
            ],
            ParametersName::SEARCH_DOCUMENTS_FROM_FUTURE => [
                'section' => 'Search',
                'title' => 'What we should do if documents published date in future',
                'type' => 'string',
                'formType' => ChoiceType::class,
                'choices' => [
                    'Exclude' => 'exclude',
                    'Fix date' => 'fix_date',
                ],
                'default' => 'exclude',
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'numeric' ]),
            ],
            ParametersName::NOTIFICATION_EMPTY_MESSAGE => [
                'section' => 'Notification',
                'title' => 'Empty notification message',
                'type' => 'string',
                'formType' => CKEditorType::class,
                'default' => '<p>We have not found any mentions for your search criteria today.</p>',
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'string' ]),
            ],
            ParametersName::NOTIFICATION_SEND_HISTORY_MODIFY  => [
                'section' => 'Notification',
                'title' => 'How long we story notification history',
                'type' => 'string',
                'formType' => null,
                'default' => '-3 months',
                'normalizer' => function ($value) {
                    return preg_replace('/(\d+)/', '-$1', str_replace('-', '', $value));
                },
                'denormalizer' => function ($value) {
                    return str_replace('-', '', $value);
                },
                'constrains' => [
                    new Type([ 'type' => 'string' ]),
                    new Callback([ $this, 'validateHistoryLifetime' ]),
                ],
            ],

            ParametersName::REGISTRATION_PAYMENT_AWAITING => [
                'section' => 'Registration',
                'title' => 'Message after user provide billing information',
                'type' => 'string',
                'formType' => CKEditorType::class,
                'default' => '<p>Thanks for submitting the form. Your payment is processing. When it done, you will receive email with passwird.</p>',
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'string' ]),
            ],

            ParametersName::MAIL_PASSWORD => [
                'section' => 'Email',
                'title' => 'Password email content',
                'type' => 'string',
                'formType' => CKEditorType::class,
                'default' => '<h4>Hello {{ user.firstName }} {{ user.lastName }}!</h4><p>You new password is {{ password }}</p><p>Regards, the Team.</p>',
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'string' ]),
            ],
            ParametersName::MAIL_VERIFICATION_SUCCESS => [
                'section' => 'Email',
                'title' => 'Verification success email content',
                'type' => 'string',
                'formType' => CKEditorType::class,
                'default' => '<h4>Hello {{ user.firstName }} {{ user.lastName }}!</h4><p>You registration is verified and you may proceed login with you credentials </p><p> Email: {{ user.email }} Password: {{ password }}</p><p>Regards, the Team.</p>',
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'string' ]),
            ],
            ParametersName::MAIL_VERIFICATION_REJECT => [
                'section' => 'Email',
                'title' => 'Verification success email content',
                'type' => 'string',
                'formType' => CKEditorType::class,
                'default' => '<h4>Hello {{ user.firstName }} {{ user.lastName }}!</h4><p>Unfortunately you registration is rejected. Payments will be refund. </p><p>Regards, the Team.</p>',
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'string' ]),
            ],
            ParametersName::MAIL_RESETTING_CONFIRMATION => [
                'section' => 'Email',
                'title' => 'Password resetting email content',
                'type' => 'string',
                'formType' => CKEditorType::class,
                'default' => '<h4>Hello {{ user.firstName }} {{ user.lastName }}!</h4> <p> To reset your password - please visit {{ confirmationUrl }} </p> <p> Regards, the Team. </p>',
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'string' ]),
            ],
            ParametersName::MAIL_UNSUBSCRIBE => [
                'section' => 'Email',
                'title' => 'Unsubscribe email content',
                'type' => 'string',
                'formType' => CKEditorType::class,
                'default' => '<p>{{ user.firstName}} {{ user.lastName }} has unsubscribed from your notification</p>',
                'normalizer' => null,
                'denormalizer' => null,
                'constrains' => new Type([ 'type' => 'string' ]),
            ],
        ];
    }

    /**
     * Get definition for specified parameter.
     *
     * @param string $name Parameter name.
     *
     * @return array
     */
    public function getDefinition($name)
    {
        if (! isset($this->definitions[$name])) {
            throw new \InvalidArgumentException('Unknown '. $name);
        }

        return $this->definitions[$name];
    }

    /**
     * Normalize value.
     *
     * @param string $name  Parameter name.
     * @param mixed  $value Raw value.
     *
     * @return mixed
     */
    public function normalize($name, $value)
    {
        $definition = $this->getDefinition($name);

        if (isset($definition['normalizer'])) {
            $value = $definition['normalizer']($value);
        }

        return $value;
    }

    /**
     * Denormalize value.
     *
     * @param string $name  Parameter name.
     * @param mixed  $value Normalized value.
     *
     * @return mixed
     */
    public function denormalize($name, $value)
    {
        $definition = $this->getDefinition($name);

        if (isset($definition['denormalizer'])) {
            $value = $definition['denormalizer']($value);
        }

        return $value;
    }

    /**
     * Retrieve an external iterator.
     *
     * @return Traversable An instance of an object implementing \Iterator or
     * \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->definitions);
    }

    /**
     * @param string                    $value   Raw value from user.
     * @param ExecutionContextInterface $context A ExecutionContextInterface instance.
     *
     * @return void
     */
    public function validateHistoryLifetime($value, ExecutionContextInterface $context)
    {
        //
        // Split expiration time on groups
        //
        $valid = true;
        $matches = [];
        $result = preg_match_all('/(\d+\s?[A-Za-z]+)/', $value, $matches);

        if (($result === 0) || ($result === false)) {
            $valid = false;
        } else {
            $matches = $matches[0];

            foreach ($matches as $match) {
                $match = trim($match);
                $parts = explode(' ', $match);

                $count = 1;
                $period = $parts[0];
                if (count($parts) === 2) {
                    list($count, $period) = $parts;
                }

                if (! is_numeric($count) || !in_array($period, self::$availablePeriods, true)) {
                    $valid = false;
                    break;
                }
            }
        }

        if (! $valid) {
            $context->buildViolation('Invalid expiration time, should be space separated string where each element id match to patter "number year(s)|month(s)|week(s)|day(s)|hour(s)|minute(s)|second(s)"')
                ->atPath('expirationTime')
                ->addViolation();
        }
    }
}
