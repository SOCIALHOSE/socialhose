<?php

namespace QueueBundle\Consumer;

use AppBundle\Entity\EmailedDocument;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use UserBundle\Mailer\MailerInterface;

/**
 * Class DocumentsEmailConsumer
 *
 * @package QueueBundle\Consumer
 */
class DocumentsEmailConsumer extends AbstractConsumer
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * DocumentsEmailConsumer constructor.
     *
     * @param LoggerInterface        $logger A LoggerInterface instance.
     * @param EntityManagerInterface $em     A EntityManagerInterface instance.
     * @param MailerInterface        $mailer A MailerInterface instance.
     */
    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ) {
        parent::__construct($logger, $em->getConnection());

        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * Execute consumer specific code.
     *
     * @param string $id Emailed document id.
     *
     * @return mixed
     */
    protected function doExecute($id)
    {
        if (($id === '') || ! is_numeric($id)) {
            $this->error('Got empty or not numeric value', [ 'id' => $id ]);

            return true; // We return true in order to not requeue this invalid
                         // message again.
        }

        $this->info('Send emailed documents with', [ 'id' => $id ]);

        $repository = $this->em->getRepository(EmailedDocument::class);
        $emailedDocument = $repository->find($id);

        if ($emailedDocument instanceof EmailedDocument) {
            $this->mailer->sendEmailedDocument($emailedDocument);
            $this->mailer->flushQueue();

            $this->em->remove($this->em->getReference(EmailedDocument::class, $id));
            $this->em->flush();
        }

        return true;
    }
}
