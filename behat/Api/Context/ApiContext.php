<?php

namespace Api\Context;

use Api\Util\ApiConnection;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Coduo\PHPMatcher\PHPMatcher;
use Common\Context\AbstractContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;

/**
 * Class ApiContext
 * @package Api\Context
 */
class ApiContext extends AbstractContext
{

    /**
     * @var ApiConnection
     */
    private $apiConnection;

    /**
     * @var string
     */
    private $authToken;

    /**
     * @param ContainerInterface $container   A ContainerInterface instance.
     * @param string             $baseUrl     Base url to app api.
     * @param string             $fixturesDir Path to fixtures directory.
     */
    public function __construct(
        ContainerInterface $container,
        $baseUrl,
        $fixturesDir
    ) {
        parent::__construct($container, $fixturesDir);

        $this->apiConnection = new ApiConnection($baseUrl, $this->debug);
    }

    /**
     * Make request to api.
     *
     * @Given /^(?:|I )[Mm]ake (?P<method>GET|POST|PUT|DELETE) request to (?P<endpoint>.+)$/
     *
     * @param string $method   HTTP method name.
     * @param string $endpoint Relative to base url.
     * @param mixed  $payload  Request parameters.
     *
     * @return void
     */
    public function request($method, $endpoint, $payload = null)
    {
        switch (true) {
            case $payload instanceof TableNode:
                $table = $payload->getTable();
                $payload = [];

                foreach ($table as $row) {
                    $payload[current($row)] = next($row);
                }
                break;

            case $payload instanceof PyStringNode:
                $payload = (array) json_decode($payload->getRaw(), true);
                $payload = array_filter($payload);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException(
                        'Request error: ' . json_last_error_msg()
                    );
                }
                break;

            default:
                $payload = [];
        }

        $payload = $this->processor->process($payload);

        $this->apiConnection->request($method, $endpoint, $payload, $this->authToken);
    }

    /**
     * @Then /^(?:|I )[Gg]ot response with code (?P<code>\d+)$/
     * @Then /^(?:|I )[Gg]ot successful response$/
     *
     * @param integer $expected Expected response.
     *
     * @return void
     */
    public function checkStatus($expected = 200)
    {
        self::assertEquals(
            $expected,
            $this->apiConnection->getLastResponse()->getStatusCode()
        );
    }

    /**
     * @Then /^(?:|I )[Gg]ot response with content$/
     * @Then /^(?:|[Rr]esponse|[Ii]t's )[Cc]ontains$/
     *
     * @param PyStringNode $pattern Pattern for coduo/PHPMatcher.
     *
     * @return void
     */
    public function responseMatch(PyStringNode $pattern)
    {
        $error = '';

        self::assertTrue(
            $this->match(
                $this->apiConnection->getLastResponseData(),
                $pattern->getRaw(),
                $error
            ),
            $error
        );
    }

    /**
     * @Then /^(?:|I )[Gg]ot empty response$/
     * @Then /^[Rr]esponse is empty$/
     * @Then /^[Ii]t's empty$/
     *
     * @return void
     */
    public function emptyResponse()
    {
        $error = '';

        self::assertTrue(
            PHPMatcher::match(
                $this->apiConnection->getLastResponseData(),
                '',
                $error
            ),
            $error
        );
    }

    /**
     * @Given /^(?:|I )[Aa]uthenticated as (?P<email>[\w@\.]+) with password (?P<password>.+)$/
     *
     * @param string $email    User email.
     * @param string $password User password.
     *
     * @return void
     */
    public function authenticate($email, $password)
    {
        $this->apiConnection
            ->request('POST', '/security/token/create', [
                'email' => $email,
                'password' => $password,
            ]);

        self::assertEquals(
            200,
            $this->apiConnection->getLastResponse()->getStatusCode()
        );
        $response = json_decode(
            $this->apiConnection->getLastResponseData(),
            true
        );
        self::assertTrue(is_array($response), 'Invalid response from server');
        self::assertArrayHasKey('token', $response);
        $this->authToken = $response['token'];
    }

    /**
     * @Then /^(?:[Oo]ne|(?P<count>\d+)) [Ee]mail is sent$/
     *
     * @param integer $count Expected emails count.
     *
     * @return void
     */
    public function emailsCount($count = 1)
    {
        $mailer = $this->getMailCollector();

        self::assertEquals($count, $mailer->getMessageCount());
    }

    /**
     * @Then /^[Nn]o emails sent$/
     *
     * @return void
     */
    public function noEmailsSent()
    {
        $mailer = $this->getMailCollector();

        self::assertEquals(0, $mailer->getMessageCount());
    }

    /**
     * @Then /^(?:|[Ff]irst )[Ee]mail subject is "(?P<subject>[^"]+)"$/
     * @Then /^(?P<index>\d+) email subject is "(?P<subject>[^"]+)"$/
     *
     * @param string  $subject Expected email subject.
     * @param integer $index   Email index.
     *
     * @return void
     */
    public function emailSubject($subject, $index = 0)
    {
        $mailer = $this->getMailCollector();

        /** @var \Swift_Message $message */
        $message = $mailer->getMessages()[$index];
        self::assertEquals($subject, $message->getSubject());
    }

    /**
     * @return \Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface|\Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector
     */
    protected function getMailCollector()
    {
        /** @var Profiler $profiler */
        $profiler = $this->container->get('profiler');
        $token = current($this->apiConnection->getLastResponse()
            ->getHeader('X-Debug-Token'));

        return $profiler->loadProfile($token)->getCollector('swiftmailer');
    }
}
