<?php
namespace Sma;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\File as FileTransport;
use Zend\Mail\Transport\FileOptions;
use Zend\Mime\Mime;
use Osf\Filter\Filter as F;
use Osf\Exception\ArchException;
use Sma\Container;
use Sma\Log;
use Sma\Mail\Template;
use Sma\Session\Identity;
use Sma\Controller\Cli\DeferredMailProcessing as DMP;
use H;

/**
 * SMA mail
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage mail
 */
class Mail
{
    const R_CC       = 'cc';
    const R_BCC      = 'bcc';
    const R_TO       = 'to';
    const R_FROM     = 'from';
    const R_REPLY_TO = 'replyTo';
    
    const I_TEXT = 'text';
    const I_LINK = 'link';
    const I_TITLE = 'title';
    const I_BULLET = 'bullet';
    const I_PARAGRAPH = 'paragraph';
    
    const C_WHITE = null;
    const C_GRAY  = '#F8F8F8';
    
    const MIMETYPE_PDF = 'application/pdf';
    
    const DEBUG_PATH = '/tmp/sma_mails';
    
    protected $subject;
    protected $footer;
    protected $items = [];
    protected $attachments = [];
    
    /**
     * @var TransportInterface
     */
    protected $transport;
    protected $template;
    
    protected $colors = [
        'page'       => null,
        'background' => '#F6F6F6',
        'primary'    => '#3498DB',
        'footer'     => '#E9E9E9'
    ];
    
    protected $recipients = [];
    protected $sender;
    
    /**
     * Add recipient or from. See types constants in this class.
     * @param string $email
     * @param string|null $name
     * @param string $type
     * @return $this
     * @throws ArchException
     */
    public function addRecipient(string $email, ?string $name = null, string $type = self::R_TO)
    {
        if (!in_array($type, [self::R_TO, self::R_BCC, self::R_CC, self::R_FROM, self::R_REPLY_TO])) {
            throw new ArchException('Bad recipient [' . $type . ']');
        }
        $this->recipients[] = ['type' => $type, 'mail' => $email, 'name' => $name];
        return $this;
    }
    
    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setSender(string $email, ?string $name = null)
    {
        $this->sender = ['mail' => $email, 'name' => $name];
        return $this;
    }
    
    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addFrom(string $email, ?string $name = null)
    {
        return $this->addRecipient($email, $name, self::R_FROM);
    }
    
    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addTo(string $email, ?string $name = null)
    {
        return $this->addRecipient($email, $name, self::R_TO);
    }
    
    /**
     * @param string $email
     * @param string|null $name
     * @return type
     */
    public function addReplyTo(string $email, ?string $name = null)
    {
        return $this->addRecipient($email, $name, self::R_REPLY_TO);
    }
    
    /**
     * @return $this
     */
    public function addToAdmin()
    {
        $config = Container::getConfig()->getConfig('mail', 'admin');
        if (!is_array($config) || !isset($config['name']) || !isset($config['mail'])) {
            Log::error(__("Impossible d'envoyer un email à l'administrateur, mauvaise config serveur"), 'MAIL', $config);
        } else {
            $this->addTo($config['mail'], $config['name']);
        }
        return $this;
    }
    
    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addCc(string $email, ?string $name = null)
    {
        return $this->addRecipient($email, $name, self::R_CC);
    }
    
    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addBcc(string $email, ?string $name = null)
    {
        return $this->addRecipient($email, $name, self::R_BCC);
    }
    
    /**
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = (string) $subject;
        return $this;
    }

    public function getSubject()
    {
        return $this->subject;
    }
    
    /**
     * @return $this
     */
    
    /**
     * @param string $footer
     * @return $this
     */
    public function setFooter(?string $footer)
    {
        $this->footer = $footer;
        return $this;
    }
    
    /**
     * Pied de page par défaut SimpleManager
     * @return $this
     */
    public function setFooterDefault()
    {
        $url = H::baseUrl('', true);
        return $this->setFooter('&#169; ' . date('Y') . ' ' . 
            H::html(APP_NAME, 'a')->setAttributes([
                'href'   => $url,
                'target' => '_blank'
            ]));
    }
    
    /**
     * Pied de page spécifique pour l'utilisateur courant
     * @return $this
     */
    public function setFooterUser()
    {
        $bean = Identity::getContactBean();
        
        $title = $bean->getUrl() 
               ? H::html($bean->getComputedTitle(), 'a')->setAttributes([
                'href'   => $bean->getUrl(),
                'target' => '_blank'
            ]) : H::html($bean->getComputedTitle());
        $items = [$title];
        if ($bean->getCompanyDesc()) {
            $items[] = H::html($bean->getCompanyDesc());
        }
        $address = trim($bean->getAddress()->getComputedAddress(false, false));
        if ($address) {
            $items[] = nl2br(H::html($address));
        }
        if ($bean->getTel()) {
            $items[] = __("tél.") . ' ' . F::getTelephone($bean->getTel());
        }
        return $this->setFooter(implode('<br>', $items));
    }

    public function getFooter(): ?string
    {
        return $this->footer;
    }
    
    /**
     * @return $this
     */
    public function addLinkBullet($label, $url)
    {
        $this->items[] = [
            'type' => self::I_LINK,
            'text' => (string) $label,
            'url'  => (string) $url
        ];
        return $this;
    }
    
    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }
    
    /**
     * Un titre dans le contenu
     * @param string $htmlOrText
     * @param bool $escape
     * @return $this
     */
    public function addTitle($htmlOrText, bool $escape = true)
    {
        $this->items[] = [
            'type'   => self::I_TITLE,
            'text'   => (string) $htmlOrText,
            'escape' => $escape
        ];
        return $this;
    }
    
    /**
     * Paragraphe (p)
     * @param string $htmlOrText
     * @param bool $escape
     * @return $this
     */
    public function addParagraph($htmlOrText, bool $escape = true)
    {
        $this->items[] = [
            'type'  => self::I_PARAGRAPH,
            'text'  => (string) $htmlOrText,
            'escape' => $escape
        ];
        return $this;
    }
    
    /**
     * Un LI, les li consécutifs sont rassemblés dans un ul
     * @param string $htmlOrText
     * @param bool $escape
     * @return $this
     */
    public function addBullet($htmlOrText, bool $escape = true)
    {
        $this->items[] = [
            'type'  => self::I_BULLET,
            'text'  => (string) $htmlOrText,
            'escape' => $escape
        ];
        return $this;
    }
    
    /**
     * Contenu brut (html ou texte)
     * @param string $htmlOrText
     * @param bool $escape
     * @return $this
     */
    public function addText($htmlOrText, bool $escape = true)
    {
        $this->items[] = [
            'type'  => self::I_TEXT,
            'text'  => (string) $htmlOrText,
            'escape' => $escape
        ];
        return $this;
    }
    
    /**
     * Convert markdown to text and call addText()
     * @param string $md
     * @param string $style
     * @param string $bgColor
     * @return $this
     */
    public function addMarkdown($md)
    {
        $text = Container::getMarkdown()->text($md);
        return $this->addText($text);
    }
    
    /**
     * Pièce jointe
     * @param string $content
     * @param string $type
     * @param string $fileName
     * @param string $disposition
     * @return $this
     */
    public function addAttachment(
            $content, $type, $fileName, 
            $disposition = Mime::DISPOSITION_ATTACHMENT)
    {
        $attachment = new MimePart($content);
        $attachment->setType($type);
        $attachment->setFileName($fileName);
        $attachment->setEncoding(Mime::ENCODING_BASE64);
        $attachment->setDisposition($disposition);
        $this->attachments[] = $attachment;
        return $this;
    }
    
    /**
     * Set colors (#xxxxxx)
     * @param string $page
     * @param string $background
     * @param string $primary
     * @deprecated since version 0.1
     * @return $this
     */
    public function setColors($page = null, $background = null, $primary = null, $footer = null)
    {
        $this->colors['background'] = self::filterColor($background) ?: $this->colors['background'];
        $this->colors['page']       = self::filterColor($page)       ?: $this->colors['page'];
        $this->colors['primary']    = self::filterColor($primary)    ?: $this->colors['primary'];
        $this->colors['footer']     = self::filterColor($footer)     ?: $this->colors['footer'];
        return $this;
    }
    
    /**
     * @param string $colorKey
     * @deprecated since version 0.1
     * @return string
     */
    public function getColor($colorKey, $pattern = 'background-color: %s;')
    {
        if (!$this->colors[$colorKey]) {
            return '';
        }
        return sprintf($pattern, $this->colors[$colorKey]);
    }
    
    /**
     * Construit et envoi l'e-mail
     * @param type $transport
     * @return bool
     */
    public function send(?TransportInterface $transport = null): bool
    {
        // Détermine le footer
        if (!$this->getFooter()) {
            $this->setFooterDefault();
        }
        
        // Récupération des informations
        $mail   = new Message();
        $config = Container::getConfig()->toArray()['mail'];
        
        // Création du mail
        $this->setRecipients($config, $mail);
        $mail->setEncoding('UTF-8')
             ->setSubject($this->subject)
             ->setBody($this->buildBody());
//             ->setEncoding(Mime::ENCODING_QUOTEDPRINTABLE);
//             ->getHeaders()->get('content-type')->setType(Mime::MULTIPART_ALTERNATIVE);
        
        if (!$mail->getTo()->count()) {
            Log::error('Email not sent, no recipient found.', 'MAIL', $mail->toString());
        }
        
        // Configuration du transport
        $transport = $transport ?? $this->getTransport();
        if (($transport === null || $transport === true) && isset($config['smtp'])) {
            $transport = new Smtp(new SmtpOptions($config['smtp']));
        } else if ($transport === null) {
            $transport = new Sendmail();
        }
        
        // Dump de débogage et arrêt si environnement de développement
        if (isset($config['debug_mode']) && $config['debug_mode']) {
            $this->debug($mail, $transport);
            Log::info('Email saved in DEBUG MODE (not sent) : ' . $this->getSubject(), 'MAIL', ['mail' => $mail->getSubject(), 'transport' => get_class($transport)]);
            return true;
        }
        
        // Envoi
        $retVal = true;
        try {
            $transport instanceof TransportInterface ? $transport->send($mail) : false;
            Log::info('Email sent: ' . $this->getSubject(), 'MAIL', ['mail' => $mail->getSubject(), 'transport' => get_class($transport)]);
        } catch (\Exception $e) {
            Log::error("Sending email error: " . $e->getMessage(), 'MAIL', ['subject' => $this->getSubject(), 'mail' => $this->buildTextBody(), 'transport' => get_class($transport)]);
            $retVal = false;
        }
        
        return $retVal;
    }
    
    /**
     * Envoi en différé
     * @param string $mailKey
     * @param TransportInterface $transport
     * @return $this
     */
    public function sendDeferred(
            $mailKey = null, 
            TransportInterface $transport = null)
    {
        $mailKey = $mailKey === null ? null : (string) $mailKey;
        if ($transport instanceof TransportInterface) {
            $this->setTransport($transport);
        }
        DMP::registerMail($this, $mailKey);
        return $this;
    }
    
    /**
     * @param array $config
     * @param Message $mail
     * @return $this
     */
    protected function setRecipients(array $config, Message $mail)
    {
        $mail->setSender($config['sender']['mail'], $config['sender']['name']);
        
//        if (Container::getApplication()->isDevelopment()) {
//            $mail->addTo($config['debug']['mail'], $config['debug']['name']);
//            $mail->addFrom($config['from']['mail'], $config['from']['name']);
//            return $this;
//        }
        
        foreach ($this->recipients as $recipient) {
            $method = 'add' . ucfirst($recipient['type']);
            $mail->$method($recipient['mail'], $recipient['name']);
        }
        
        if (!$mail->getFrom()->count()) {
            $mail->addFrom($config['noreply']['mail'], $config['noreply']['name']);
        }
        
        return $this;
    }
    
    /**
     * @task [mail] attached files
     * @return MimeMessage
     */
    protected function buildBody(): MimeMessage
    {
        $parts = [];
        
        // Contenu HTML / Text
        $parts[] = (new MimePart($this->buildHtmlBody()))
                        ->setType(Mime::TYPE_HTML)
                        ->setCharset('utf-8')
                        ->setEncoding(Mime::ENCODING_QUOTEDPRINTABLE);
//        $parts[] = (new MimePart($this->buildTextBody()))
//                        ->setType(Mime::TYPE_TEXT)
//                        ->setCharset('utf-8')
//                        ->setEncoding(Mime::ENCODING_QUOTEDPRINTABLE);
        
        // Fichiers attachés
        foreach ($this->attachments as $attachmentPart) {
            $parts[] = $attachmentPart;
        }
        
        // Construction du message
        $message = new MimeMessage();
        $message->setParts($parts);
        return $message;
    }
    
    /**
     * Contenu HTML du corps de mail
     * @param bool $refresh
     * @return string
     */
    public function buildHtmlBody(bool $refresh = false): string
    {
        return $this->getTemplate()->render(false, $refresh);
    }
    
    /**
     * Contenu texte du corps de mail
     * @param bool $refresh
     * @return string
     */
    public function buildTextBody(bool $refresh = false): string
    {
        return $this->getTemplate()->render(true, $refresh);
    }
    
    /**
     * @staticvar int $cpt
     * @param Message $mail
     * @param TransportInterface $transport
     * @return $this
     */
    protected function debug(Message $mail, $transport)
    {
        static $cpt = 0;
        
        if (!is_dir(self::DEBUG_PATH)) {
            mkdir(self::DEBUG_PATH, 0777, true);
        }
        $date = date('Ymd-his-');
        $fhtml = self::DEBUG_PATH . '/' . $date . $cpt . '.html';
        $ftext = self::DEBUG_PATH . '/' . $date . $cpt . '.txt';
        $ftransport = new FileTransport();
        $ftransport->setOptions(new FileOptions([
            'path' => self::DEBUG_PATH,
            'callback' => function (FileTransport $transport) { 
                return date('Ymd-his-') . mt_rand() . '.html';
            }
        ]));
        $ftransport->send($mail);
        file_put_contents($ftext, 
            "Subject: " . $mail->getSubject() . "\n" . 
            "Sender: "  . $mail->getSender()->toString() . "\n" . 
            "Transport: " . (is_object($transport) ? get_class($transport) : '[not defined]') . "\n\n" . 
            $this->buildTextBody() . "\n\n" . print_r($this, true) . "\n\n" . print_r($mail, true) . "\n"
        );
        file_put_contents($fhtml, $this->buildHtmlBody());
        /* @var $attachment MimePart */
        foreach ($this->attachments as $attachment) {
            if ($attachment->getDisposition() === Mime::DISPOSITION_ATTACHMENT) {
                $fileName = self::DEBUG_PATH . '/' . $date . $cpt . '-' . $attachment->getFileName();
                file_put_contents($fileName, $attachment->getRawContent());
            }
        }
        $cpt++;
        return $this;
    }
    
    /**
     * Template par défaut pour générer html et texte
     * @staticvar Template $template
     * @return Template
     */
    public function getTemplate()
    {
        if ($this->template === null) {
            $this->template = new Template($this);
        }
        return $this->template;
    }
    
    /**
     * Définit le transport à utiliser
     * @param mixed $transport
     * @return $this
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;
        return $this;
    }
    
    /**
     * @return TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }
    
    /**
     * @param type $color
     * @return type
     * @deprecated since version 0.1
     * @throws ArchException
     */
    protected static function filterColor($color)
    {
        if (!$color) {
            return self::C_WHITE;
        }
        $color = (string) $color;
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            throw new ArchException('Bad color syntax [' . $color . ']');
        }
        return $color;
    }
}
