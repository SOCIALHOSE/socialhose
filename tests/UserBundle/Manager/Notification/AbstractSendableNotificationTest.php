<?php

namespace UserBundle\Manager\Notification;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\AppTestCaseTrait;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\Notification\NotificationTheme;
use UserBundle\Entity\Notification\NotificationThemeOptions;
use UserBundle\Enum\ThemeTypeEnum;
use UserBundle\Manager\Notification\Model\FeedData;

/**
 * Class AbstractSendableNotificationTest
 *
 * @package UserBundle\Manager\Notification
 */
abstract class AbstractSendableNotificationTest extends KernelTestCase
{

    use AppTestCaseTrait;

    const PATTERN_TPL = '/%s[^\{]*?\{[^\}]*%s[^\}]*\}/i';

    /**
     * @var EngineInterface
     */
    private static $templating;

    /**
     * @beforeClass
     *
     * @return void
     */
    public static function getServices()
    {
        self::bootKernel();

        self::$templating = self::$kernel->getContainer()->get('templating');
    }

    /**
     * @param ThemeTypeEnum $themeType A ThemeTypeEnum instance.
     * @param array         $diffs     Notification theme diffs.
     * @param FeedData[]    $data      Array of feed data.
     *
     * @return string
     */
    protected function render(ThemeTypeEnum $themeType, array $diffs = [], array $data = [])
    {
        $notification = Notification::create()
            ->setTheme(
                NotificationTheme::create()
                    ->setEnhanced(NotificationThemeOptions::createDefault())
                    ->setPlain(NotificationThemeOptions::createDefault())
            )
            ->setThemeType($themeType);

        switch ($themeType->getValue()) {
            case ThemeTypeEnum::ENHANCED:
                $notification->setEnhancedThemeOptionsDiff($diffs);
                break;

            case ThemeTypeEnum::PLAIN:
                $notification->setPlainThemeOptionsDiff($diffs);
                break;

            default:
                throw new \DomainException('Unhandled theme type: '. $themeType->getValue());
        }

        $sendableNotification = new SendableNotification(
            new SendableNotificationConfig(
                0,
                0,
                0,
                0,
                '<p>empty</p>',
                0
            ),
            $notification,
            $data
        );

        return $sendableNotification->render(self::$templating);
    }
}
