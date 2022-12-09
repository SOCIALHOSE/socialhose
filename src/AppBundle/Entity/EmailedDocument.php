<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EmailedDocument
 *
 * @ORM\Table(name="emailed_documents")
 * @ORM\Entity
 */
class EmailedDocument implements EntityInterface
{

    use BaseEntityTrait;

    /**
     * @var string[]
     *
     * @ORM\Column(type="array")
     *
     * @Assert\Count(min=1)
     */
    private $emailTo;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     */
    private $emailReplyTo;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank
     */
    private $content;

    /**
     * Set emailTo
     *
     * @param array|mixed $emailTo List of recipients.
     *
     * @return EmailedDocument
     */
    public function setEmailTo($emailTo)
    {
        $this->emailTo = (array) $emailTo;

        return $this;
    }

    /**
     * Get emailTo
     *
     * @return array
     */
    public function getEmailTo()
    {
        return $this->emailTo;
    }

    /**
     * Set emailReplyTo
     *
     * @param string $emailReplyTo Reply to.
     *
     * @return EmailedDocument
     */
    public function setEmailReplyTo($emailReplyTo)
    {
        $this->emailReplyTo = $emailReplyTo;

        return $this;
    }

    /**
     * Get emailReplyTo
     *
     * @return string
     */
    public function getEmailReplyTo()
    {
        return $this->emailReplyTo;
    }

    /**
     * Set subject
     *
     * @param string $subject Email subject.
     *
     * @return EmailedDocument
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set content
     *
     * @param string $content Email content.
     *
     * @return EmailedDocument
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
