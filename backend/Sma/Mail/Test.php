<?php
namespace Sma\Mail;

use Osf\Test\Runner as OsfTest;
use Sma\Mail;
use H;

/**
 * Test du template de mail
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage test
 */
class Test extends OsfTest
{
    public static function run()
    {
        self::reset();
        
        $mail = new Mail;
        $mail->addTitle('Bienvenue sur Simplemanager');
        $mail->addParagraph("Vous êtes un bien heureux utilisateur de cette solution magnifique. Veuillez noter les choses suivantes :");
        $mail->addBullet('SimpleManager va vous simplifier la vie');
        $mail->addBullet("Et en plus c'est fantastique");
        $mail->addParagraph("Et pour aller plus loin il y a ces liens :");
        $mail->addLinkBullet('Connectez-vous !', 'https://www.simplemanager.fr' . H::url('account', 'login'));
        $mail->addLinkBullet('Identifiez-vous !', 'https://www.simplemanager.fr' . H::url('account', 'login'));
        $mail->addTitle('Mais encore !');
        $mail->addParagraph('Voilà pourquoi nous sommes cools, il faut des choses magnifiques dans la vie.');
        
        $text = $mail->buildTextBody();
        self::assert(strpos($text, '[Connectez-vous !]'));
        self::assert(strpos($text, 'SimpleManager'));
        
        $html = $mail->buildHtmlBody();
        self::assert(!strpos($html, "\n"));
        self::assert(strpos($html, '&nbsp;:'));
        self::assert(strpos($html, 'SimpleManager'));
        
//        file_put_contents('/tmp/mail.html', $mail->buildHtmlBody());
//        file_put_contents('/tmp/mail.txt', $mail->buildTextBody());
        
        return self::getResult();
    }
}
