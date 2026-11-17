<?php


namespace App\Services;


use App\Entity\Inscription;
use App\Repository\SchoolRepository;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class SendMailerService
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @var string|null
     */
    private ?string $senderMail;

    /**
     * @var string|null
     */
    private ?string $name;


    /**
     * SendMailer constructor.
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface  $mailer,)
    {
        $this->mailer = $mailer;
        //$this->senderMail = getenv('MAILER_EMAIL');/*En mode production*/
        $this->senderMail = $_ENV['MAILER_EMAIL'];
        $this->name = 'Geststock';
    }


    // send a invoice to the customer
    public function sendfile($email , $file): bool
    {
        $validator = new EmailValidator();
        if ($validator->isValid($email, new RFCValidation())) {
            $message = (new TemplatedEmail())
                ->from(new Address($this->senderMail, $this->name))
                ->to($email)
                ->subject('Facture achat');

            $fileName = 'facture-' . time() . '.pdf';
            $message->attach($file, $fileName, 'application/pdf');

            try {
                $this->mailer->send($message);
            } catch (TransportExceptionInterface $e) {
                return false;
            }
        }
        return true;
    }

    public function sendMail(string $email, string $text, UploadedFile $file = null, string $mimeType = null): bool
    {
        $validator = new EmailValidator();
        if ($validator->isValid($email, new RFCValidation())) {
            $message = (new TemplatedEmail())
                ->from(new Address($this->senderMail, $this->name))
                ->to($email)
                ->subject('Support');

            $message->text($text);

            if ($file !== null && $mimeType !== null) {
                $message->attach(fopen($file->getPathname(), 'rb'));
            }

            try {
                $this->mailer->send($message);
            } catch (TransportExceptionInterface $e) {
                return false;
            }
        }
        return true;
    }
}
