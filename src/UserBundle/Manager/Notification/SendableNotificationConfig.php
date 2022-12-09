<?php

namespace UserBundle\Manager\Notification;

use AppBundle\Configuration\ConfigurationImmutableInterface;
use AppBundle\Configuration\ParametersName;

/**
 * Class SendableNotification
 * @package UserBundle\Manager\Notification
 *
 * @property integer documentsPerFeed()
 * @property integer commentsPerDocument()
 * @property integer extractContextualCharacter()
 * @property integer extractFromStartCharacter()
 * @property string  emptyMessage()
 * @property integer historyStorePeriod()
 */
class SendableNotificationConfig
{

    /**
     * @var integer[]
     */
    private $config;

    /**
     * SendableNotificationConfig constructor.
     *
     * @param integer $documentsPerFeed           Max number of documents showing
     *                                            in each feed.
     * @param integer $commentsPerDocument        Max number of comments showing
     *                                            in each document.
     * @param integer $extractContextualCharacter Number of character before and
     *                                            after extracted document content.
     *                                            Used when notification field
     *                                            `articleExtracts` has value
     *                                            'contextual'.
     * @param integer $extractFromStartCharacter  Number of character from beginning
     *                                            of document content. Used when
     *                                            notification field `articleExtracts`
     *                                            has value 'start'.
     * @param string  $emptyMessage               Empty notification message.
     *                                            Used when notification field
     *                                            `sendWhenEmpty` set to true.
     * @param integer $historyStorePeriod         How long we should store render
     *                                            history.
     */
    public function __construct(
        $documentsPerFeed,
        $commentsPerDocument,
        $extractContextualCharacter,
        $extractFromStartCharacter,
        $emptyMessage,
        $historyStorePeriod
    ) {
        $this->config = [
            'documentsPerFeed' => $documentsPerFeed,
            'commentsPerDocument' => $commentsPerDocument,
            'extractContextualCharacter' => $extractContextualCharacter,
            'extractFromStartCharacter' => $extractFromStartCharacter,
            'emptyMessage' => $emptyMessage,
            'historyStorePeriod' => $historyStorePeriod,
        ];
    }

    /**
     * @param ConfigurationImmutableInterface $configuration A ConfigurationImmutableInterface
     *                                                       instance.
     *
     * @return static
     */
    public static function fromConfiguration(ConfigurationImmutableInterface $configuration)
    {
        return new static(
            $configuration->getParameter(ParametersName::NOTIFICATION_DOCUMENT_PER_FEED),
            $configuration->getParameter(ParametersName::NOTIFICATION_COMMENTS_PER_DOCUMENT),
            0, // TODO add proper parameter.
            $configuration->getParameter(ParametersName::NOTIFICATION_START_EXTRACT_LENGTH),
            $configuration->getParameter(ParametersName::NOTIFICATION_EMPTY_MESSAGE),
            $configuration->getParameter(ParametersName::NOTIFICATION_SEND_HISTORY_MODIFY)
        );
    }

    /**
     * @param string $name Parameter name.
     *
     * @return integer
     */
    public function __get($name)
    {
        if (isset($this->{$name})) {
            return $this->config[$name];
        }

        throw new \InvalidArgumentException('Unknown parameter name '. $name);
    }

    /**
     * @param string $name  Parameter name.
     * @param mixed  $value Parameter value.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __set($name, $value)
    {
        throw new \LogicException('SendableNotificationConfig is immutable.');
    }

    /**
     * @param string $name Parameter name.
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->config[$name]);
    }
}
