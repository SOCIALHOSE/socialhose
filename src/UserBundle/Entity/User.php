<?php

namespace UserBundle\Entity;

use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\EntityInterface;
use AppBundle\Exception\LimitExceedException;
use CacheBundle\Entity\Category;
use CacheBundle\Entity\SourceList;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use \FOS\UserBundle\Model\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use UserBundle\Entity\Recipient\PersonRecipient;
use UserBundle\Entity\Subscription\AbstractSubscription;
use UserBundle\Enum\AppLimitEnum;
use UserBundle\Enum\AppPermissionEnum;
use UserBundle\Enum\UserRoleEnum;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(
        repositoryClass="UserBundle\Repository\UserRepository"
 * )
 *
 * @UniqueEntity(fields={ "email" }, message="This email is already used")
 */
class User extends BaseUser implements
    EntityInterface,
    NormalizableEntityInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank(message="User first name should not be blank")
     */
    protected $firstName = '';

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank(message="User last name should not be blank")
     */
    protected $lastName = '';

    /**
     * @var string
     *
     * @ORM\Column
     */
    protected $position = '';

    /**
     * @var string
     *
     * @ORM\Column
     *
     */
    protected $phoneNumber = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $expirationDay;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(
     *  targetEntity="CacheBundle\Entity\Category",
     *  mappedBy="user",
     *  cascade={ "persist", "remove" }
     * )
     */
    protected $categories;

    /**
     * @ORM\OneToMany(
     *     targetEntity="CacheBundle\Entity\SourceList",
     *     mappedBy="user",
     *     cascade={ "persist", "remove" }
     * )
     */
    protected $sourcesLists;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="subscribers")
     * @ORM\JoinColumn(name="master_user", referencedColumnName="id",
     *                                     onDelete="CASCADE")
     */
    protected $masterUser;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="User",
     *     mappedBy="masterUser",
     *     cascade={ "persist", "remove" }
     * )
     */
    protected $subscribers;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\Plan",
     *     mappedBy="user",
     *     cascade={ "persist", "remove" }
     * )
     */
    protected $plan;

    /**
     * Proper recipient.
     *
     * @var PersonRecipient
     *
     * @ORM\OneToOne(
     *     targetEntity="UserBundle\Entity\Recipient\PersonRecipient",
     *     inversedBy="associatedUser",
     *     cascade={ "ALL" }
     * )
     * @ORM\JoinColumn(name="recipient_id", referencedColumnName="id",
     *                                      onDelete="SET NULL")
     */
    protected $recipient;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\Recipient\AbstractRecipient",
     *     mappedBy="owner",
     *     cascade={ "persist", "remove" }
     * )
     */
    protected $recipients;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $verified = false;

    /**
     * @var AbstractSubscription
     *
     * @ORM\ManyToOne(
     *     targetEntity="UserBundle\Entity\Subscription\AbstractSubscription",
     *     inversedBy="users",
     *     cascade={ "persist" }
     * )
     * @ORM\JoinColumn(name="billing_subscription_id", referencedColumnName="id",
     *                                                 onDelete="SET NULL")
     */
    protected $billingSubscription;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Company name should not be blank")
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $companyName = '';

    /**
     * @var string
     * @Assert\NotBlank(message="Job Function should not be blank")
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $jobFunction = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Number of employee should not be blank")
     */
    private $numberOfEmployee = '';

    /**
     * @var string
     * @Assert\NotBlank(message="Industry should not be blank")
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $industry = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $websiteUrl = '';

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $hubSpot= false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stripeUserId = '';
   
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->categories = new ArrayCollection();
        $this->sourcesLists = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
        $this->recipients = new ArrayCollection();
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return string[]
     */
    public function getRoles()
    {
        $roles = $this->roles;

        if (count($roles) === 0) {
            $roles = [UserRoleEnum::SUBSCRIBER];
        }

        return array_unique($roles);
    }

    /**
     * Create new user i    

     */
    public static function create($email, $password = null)
    {
        $entity = new User();
        $entity
            ->setPlainPassword($password)
            ->setEmail($email);

        if ($password === null) {
            $entity->generatePassword();
        }

        return $entity;
    }

    /**
     * Create new subscriber.
     *
     * @param string $email A user email address.
     * @param string $password A user plain password.
     *
     * @return User
     */
    public static function createSubscriber($email, $password = null)
    {
        return User::create($email, $password)
            ->setRoles([UserRoleEnum::SUBSCRIBER]);
    }
    

    public static function createMasterUser($email, $password = null)
    {
        return User::create($email, $password)
            ->setRoles([UserRoleEnum::MASTER_USER]);
    }

    /**
     * Create new admin.
     *
     * @param string $email A user email address.
     * @param string $password A user plain password.
     *
     * @return User
     */
    public static function createAdmin($email, $password = null)
    {
        return User::create($email, $password)
            ->setRoles([UserRoleEnum::ADMIN]);
    }

    /**
     * Set firstName
     *
     * @param string $firstName User first name.
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        if (($this->recipient !== null) && ($this->recipient->getFirstName() !== $firstName)) {
            $this->recipient->setFirstName($firstName);
        }

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName User last name.
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        if (($this->recipient !== null) && ($this->recipient->getLastName() !== $lastName)) {
            $this->recipient->setLastName($lastName);
        }

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Get full user name.
     *    

     * @return string
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * Sets the username.
     *
     * @param string $username New username.
     *
     * @return User
     */
    public function setUsername($username)
    {
        parent::setUsername($username);
        $this->email = $username;

        return $this;
    }

    /**
     * Sets the canonical username.
     *
     * @param string $usernameCanonical New canonical username.
     *
     * @return User
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        parent::setUsernameCanonical($usernameCanonical);
        $this->emailCanonical = $usernameCanonical;

        return $this;
    }


    /**
     * Sets the email.
     *
     * @param string $email New user email.
     *
     * @return User
     */
    public function setEmail($email)
    {
        parent::setEmail($email);
        // Copy email into username.
        $this->username = $email;

        if (($this->recipient !== null) && ($this->recipient->getEmail() !== $email)) {
            $this->recipient->setEmail($email);
        }

        return $this;
    }

    /**
     * Sets the canonical email.
     *
     * @param string $emailCanonical New canonicla email.
     *
     * @return User
     */
    public function setEmailCanonical($emailCanonical)
    {
        parent::setEmailCanonical($emailCanonical);
        $this->usernameCanonical = $emailCanonical;

        return $this;
    }

    /**
     * Add category
     *
     * @param Category $category A Category entity instance.
     *
     * @return User
     */
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;
        $category->setUser($this);

        return $this;
    }

    /**
     * Remove category
     *
     * @param Category $category A Category entity instance.
     *
     * @return User
     */
    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
        $category->setUser(null);

        return $this;
    }

    /**
     * Get categories
     *
     * @return Category[]|Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Generate random password for this user.
     *
     * @return User
     */
    public function generatePassword()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+=-';
        $this->plainPassword = substr(str_shuffle($chars), 0, 12);

        return $this;
    }

    /**
     * Add sourcesList
     *
     * @param SourceList $sourcesList A SourceList instance.
     *
     * @return User
     */
    public function addSourcesList(SourceList $sourcesList)
    {
        $this->sourcesLists[] = $sourcesList;

        return $this;
    }
    

    /**
     * Remove sourcesList
     *
     * @param SourceList $sourcesList A SourceList instance.
     *
     * @return User
     */
    public function removeSourcesList(SourceList $sourcesList)
    {
        $this->sourcesLists->removeElement($sourcesList);

        return $this;
    }

    /**    

        return $this->sourcesLists;
    }


    /**
     * Set expirationDay
     *
     * @param \DateTime $expirationDay When user is expires.
     *
     * @return User
     */
    public function setExpirationDay(\DateTime $expirationDay = null)
    {
        $this->expirationDay = $expirationDay;
    

        return $this;
    }

    /**
     * Get expirationDay
     *
     * @return \DateTime
     */
    public function getExpirationDay()
    {
        return $this->expirationDay;
    }

    /**
     * Set masterUser
     *
     * @param User $masterUser A master User entity instance.
     *
     * @return User
     */
    public function setMasterUser(User $masterUser = null)
    {
        $this->masterUser = $masterUser;
    

        return $this;
    }

    /**
     * Get masterUser
     *
     * @return User
     */
    public function getMasterUser()
    {
        return $this->masterUser;
    }

    /**
     * Add subscriber
     *
     * @param User $subscriber A subscriber User entity instance.
     *
     * @return User
     */
    public function addSubscriber(User $subscriber)
    {
        $this->subscribers[] = $subscriber;
        $subscriber->setMasterUser($this);

        return $this;
    }

    /**
     * Remove subscriber
     *
     * @param User $subscriber A subscriber User entity instance.
     *
     * @return User
     */
    public function removeSubscriber(User $subscriber)
    {
        $this->subscribers->removeElement($subscriber);
        $subscriber->setMasterUser(null);

        return $this;
    }

    /**
     * Get subscribers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * Set position
     *
     * @param string $position User position.
     *
     * @return User
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber Phone number.
     *
     * @return User
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @return PersonRecipient
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param PersonRecipient $recipient A Associated recipient.
     *
     * @return User
     */
    public function setRecipient(PersonRecipient $recipient = null)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Return metadata for current entity.
     *
     * @return Metadata
     */
    public function getMetadata()
    {
        return new Metadata(static::class, [
            PropertyMetadata::createInteger('id', ['id']),
            PropertyMetadata::createString('firstName', ['comment', 'user', 'subscriber', 'source_list']),
            PropertyMetadata::createString('lastName', ['comment', 'user', 'subscriber', 'source_list']),
            PropertyMetadata::createString('email', ['user', 'subscriber', 'notification_list']),
            PropertyMetadata::createString('role', ['user'])
                ->setField(function () {
                    $roles = $this->getRoles();
                    return $roles[0];
                }),
            PropertyMetadata::createDate('lastLogin', ['user', 'subscriber'])
                ->setNullable(true),
            PropertyMetadata::createBoolean('enabled', ['user', 'subscriber']),
            PropertyMetadata::createString('position', ['subscriber']),
            PropertyMetadata::createString('phoneNumber', ['subscriber']),
            PropertyMetadata::createBoolean('allowToCreateSavedFeeds', ['subscriber']),
            PropertyMetadata::createEntity('recipient', PersonRecipient::class, ['recipient']),
            PropertyMetadata::createObject('restrictions', ['restrictions'])
                ->setField(function () {
                    return $this->getRestrictions();
                }),
        ]);
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return ['user', 'id'];
    }

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'user';
    }

    /**
     * Set billingSubscription
     *
     * @param AbstractSubscription $billingSubscription A billing subscription
     *                                                  entity instance.
     *
     * @return User
     */
    public function setBillingSubscription(AbstractSubscription $billingSubscription = null)
    {
        $this->billingSubscription = $billingSubscription;

        return $this;
    }

    /**
     * Get billingSubscription
     *
     * @return AbstractSubscription
     */
    public function getBillingSubscription()
    {
        return $this->billingSubscription;
    }

    /**
     * @return boolean
     */
    public function isVerified()
    {
        return $this->verified;
    }

    /**
     * @param boolean $verified Is this account verified or not.
     *
     * @return User
     */
    public function setVerified($verified = true)
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * Checks that passed permission is allowed for this user.
     *
     * @param AppPermissionEnum $appPermission Requested permission.
     *
     * @return boolean
     */
    public function isAllowedTo(AppPermissionEnum $appPermission)
    {
        switch ($appPermission->getValue()) {
            case AppPermissionEnum::ANALYTICS:
                return $this->getBillingSubscription()->getPlan()->isAnalytics();

            default:
                throw new \LogicException(sprintf(
                    'Unhandled app permission \'%s\'',
                    $appPermission->getValue()
                ));
        }
    }

    /**
     * Get current used limit value.
     *
     * @param AppLimitEnum $appLimit Requested limit name.
     *
     * @return integer
     */
    public function getUsedLimit(AppLimitEnum $appLimit)
    {
        return $this->billingSubscription->getLimitValue($appLimit);
    }

    /**
     * Get allowed limit value.
     *
     * @param AppLimitEnum $appLimit Requested limit name.
     *
     * @return integer
     */
    public function getAllowedLimit(AppLimitEnum $appLimit)
    {
        return $this->billingSubscription->getPlan()->getLimitValue($appLimit);
    }

    /**
     * Use specified limit for current user.
     *
     * @param AppLimitEnum $appLimit A required limit.
     * @param integer $count How much limit should be used.
     *
     * @return $this
     *
     * @throws LimitExceedException If requested limit is exceeded.
     */
    public function useLimit(AppLimitEnum $appLimit, $count = 1)
    {
        $newValue = $this->checkLimit($appLimit, $count);
        $this->billingSubscription->setLimitValue($appLimit, $newValue);
        return $this;
    }

    /**
     * Use specified limit for current user.
     *
     * @param AppLimitEnum $appLimit A required limit.
     * @param integer $count How much limit should be used.
     *
     * @return integer A new limit value
     *
     * @throws LimitExceedException If requested limit is exceeded.
     */
    public function checkLimit(AppLimitEnum $appLimit, $count = 1)
    {
        $currValue = $this->getUsedLimit($appLimit);
        $newValue = $currValue + $count;
        $allowed = $this->getAllowedLimit($appLimit);

        if ($newValue > $allowed) {
            throw new LimitExceedException($this, $appLimit, $currValue, $count, $allowed);
        }
        return $newValue;
    }

    /**
     * Release specific user limit.
     *
     * @param AppLimitEnum $appLimit A required limit.
     * @param integer $count How much limit should be released.
     *
     * @return $this
     */
    public function releaseLimit(AppLimitEnum $appLimit, $count = 1)
    {
        $newValue = $this->getUsedLimit($appLimit) - $count;

        if ($newValue < 0) {
            $newValue = 0;
        }

        $this->billingSubscription->setLimitValue($appLimit, $newValue);

        return $this;
    }

    /**
     * Get restrictions for current user.
     *
     * @return array
     */
    public function getRestrictions()
    {
        $limits = [];
        /** @var AppLimitEnum $value */
        foreach (AppLimitEnum::getValues() as $value) {
            $limits[$value->getValue()] = [
                'limit' => $this->getAllowedLimit($value),
                'current' => $this->getUsedLimit($value),
            ];
        }

        $permissions = [];
        /** @var AppPermissionEnum $value */
        foreach (AppPermissionEnum::getValues() as $value) {
            $permissions[$value->getValue()] = $this->isAllowedTo($value);
        }
        $planData = [];
        $plan = $this->getBillingSubscription()->getPlan();
        if ($plan) {
            $planData = [
                'news' => $plan->isNews(),
                'blogs' => $plan->isBlog(),
                'reddit' => $plan->isReddit(),
                'instagram' => $plan->isInstagram(),
                'twitter' => $plan->isTwitter(),
                'price' => $plan->getPrice(),
                'analytics' => $plan->isAnalytics(),
                'savedFeeds' => $plan->getSavedFeeds(),
                'masterAccounts' => $plan->getMasterAccounts(),
            ];
        }
        return [
            'limits' => $limits,
            'permissions' => $permissions,
            'plans' => $planData,
            'isPaymentId' => !empty($this->getStripeUserId()) ? true :false,
            'isPlanCancelled' => $this->getBillingSubscription()->isSubscriptionCancelled(),
            'isPlanDowngrade' => $this->getBillingSubscription()->isPlanDowngrade(),
            'subStartDate' => ($this->getBillingSubscription()->getStartDate() instanceof \DateTime) ? $this->getBillingSubscription()->getStartDate()->format('Y-m-d h:i:s') : '',
            'subEndDate' => ($this->getBillingSubscription()->getEndDate() instanceof \DateTime) ? $this->getBillingSubscription()->getEndDate()->format('Y-m-d h:i:s') : '',
        ];
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     * @return string
     */
    public function getJobFunction(): string
    {
        return $this->jobFunction;
    }

    /**
     * @param string $jobFunction
     */
    public function setJobFunction($jobFunction)
    {
        $this->jobFunction = $jobFunction;
    }

    /**
     * @return string
     */
    public function getNumberOfEmployee(): string
    {
        return $this->numberOfEmployee;
    }

    /**
     * @param string $numberOfEmployee
     */
    public function setNumberOfEmployee($numberOfEmployee)
    {
        $this->numberOfEmployee = $numberOfEmployee;
    }

    /**
     * @return string
     */
    public function getIndustry(): string
    {
        return $this->industry;
    }

    /**
     * @param string $industry
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;
    }

    /**
     * @return bool
     */
    public function isHubSpot(): bool
    {
        return $this->hubSpot;
    }

    /**
     * @param bool $hubSpot
     */
    public function setHubSpot(bool $hubSpot): void
    {
        $this->hubSpot = $hubSpot;
    }

    /**
     * @return string
     */
    public function getWebsiteUrl(): string
    {
        return $this->websiteUrl;
    }

    /**
     * @param string $websiteUrl
     */
    public function setWebsiteUrl($websiteUrl)
    {
        $this->websiteUrl = $websiteUrl;
    }

    /**
     * Set stripeUserId
     *
     * @param string $stripeUserId User stripeUserId.
     *
     * @return User
     */
    public function setStripeUserId($stripeUserId)
    {
        $this->stripeUserId = $stripeUserId;

        return $this;
    }

    /**
     * Get stripeUserId
     *
     * @return string
     */
    public function getStripeUserId()
    {
        return $this->stripeUserId;
    }

    /**
     * Get plan
     *
     * @return Collection
     */
    public function getPlans()
    {
        return $this->plan;
    }
}
