<?php

namespace AppBundle\Configuration;

/**
 * Class ParametersName
 * @package AppBundle\Configuration
 */
final class ParametersName
{

    const MAILER_ADDRESS = 'mailer.address';
    const MAILER_SENDER_NAME = 'mailer.sender_name';

    const NOTIFICATION_COMMENTS_PER_DOCUMENT = 'notification.comments_per_document_limit';
    const NOTIFICATION_DOCUMENT_PER_FEED = 'notification.documents_per_feed_limit';
    const NOTIFICATION_START_EXTRACT_LENGTH = 'notification.start_extract_length';
    const NOTIFICATION_CONTEXT_EXTRACT_LENGTH = 'notification.context_extract_length';
    const NOTIFICATION_SEND_HISTORY_MODIFY = 'notification.notification_send_history_modify';
    const NOTIFICATION_EMPTY_MESSAGE = 'notification.empty_massage';

    const REGISTRATION_PAYMENT_AWAITING = 'registration.payment.awaiting';

    const SEARCH_DOCUMENTS_FROM_FUTURE = 'search.documents_from_future';

    const MAIL_PASSWORD = 'mail.password';
    const MAIL_VERIFICATION_SUCCESS = 'mail.verification.success';
    const MAIL_VERIFICATION_REJECT = 'mail.verification.reject';
    const MAIL_RESETTING_CONFIRMATION = 'mail.resetting_confirmation';
    const MAIL_UNSUBSCRIBE = 'mail.unsubscribe';

    /**
     * Get available parameters name.
     *
     * @return array
     */
    public static function getAvailables()
    {
        return [
            self::MAILER_ADDRESS,
            self::MAILER_SENDER_NAME,

            self::NOTIFICATION_COMMENTS_PER_DOCUMENT,
            self::NOTIFICATION_DOCUMENT_PER_FEED,
            self::NOTIFICATION_START_EXTRACT_LENGTH,
            self::NOTIFICATION_CONTEXT_EXTRACT_LENGTH,
            self::NOTIFICATION_SEND_HISTORY_MODIFY,
            self::NOTIFICATION_EMPTY_MESSAGE,

            self::REGISTRATION_PAYMENT_AWAITING,

            self::SEARCH_DOCUMENTS_FROM_FUTURE,

            self::MAIL_PASSWORD,
            self::MAIL_VERIFICATION_SUCCESS,
            self::MAIL_VERIFICATION_REJECT,
            self::MAIL_RESETTING_CONFIRMATION,
            self::MAIL_UNSUBSCRIBE,
        ];
    }

    /**
     * @param string $name Checks that specified parameter is exists.
     *
     * @return boolean
     */
    public static function isExists($name)
    {
        return in_array($name, self::getAvailables(), true);
    }

    /**
     * Parameters constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return void
     */
    private function __clone()
    {
    }
}
