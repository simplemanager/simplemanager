<?php
namespace App\Sandbox;

use App\Common\Container;
use Sma\Controller\Json as JsonAction;
use Sma\Db\DbContainer;
use Osf\Pdf\Document\Bean\AddressBean;
use Osf\Pdf\Document\Bean\ContactBean;
use Osf\Pdf\Document\Bean\LetterBean;
use Osf\Pdf\Document\Bean\ImageBean;
use App\Document\Model\Pdf\LetterDbHydrator;
use Osf\Image\ImageHelper as Image;
use Osf\Generator\DbGenerator;

use Osf\Pdf\Tcpdf\Document as PdfDocument;
use Osf\Pdf\Tcpdf\Letter as TcpdfLetter;

use App\Account\Form\FormLogin;
use App\Sandbox\Form\FormTest;
use DB, L;

/**
 * Espace administration
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 16 nov. 2013
 * @package company
 * @subpackage controllers
 */
class Controller extends JsonAction
{
    const TMP_SERIALIZED_FILE = '/tmp/serialized.txt';
    
    public function init()
    {
        $this->layout()->setPageTitle('Tests techniques');
    }
    
    public function indexAction()
    {
    }
    
    public function testimageAction()
    {
        $this->disableViewAndLayout();
        $this->clearResponse();
    
        Container::getResponse()->setRawHeader('Content-Type: image/png');
    
        //$logos = glob('/usr/share/icons/Humanity/mimes/24/*.svg');
        $logos = glob('/home/guillaume/tmp-logos/*.png');
        $logo = $logos[rand(0, count($logos) - 1)];
        
//        $key = md5($logo);
//        $cache = new \Osf\Cache\OsfCache();
//        if (!($content = $cache->load($key))) {
            $content = Image::getImageContent($logo);
//            $cache->save($key, $content);
//        }
        
        echo $content;
        
        //echo Image::getImageContent('/usr/share/icons/Humanity/mimes/24/application-msword.svg');
    }
    
    public function letterdbAction()
    {
        try {
            $letterBean = (new LetterDbHydrator())
            ->setCompanyProvider(DbContainer::getCompanyTable()->find(1))
            ->setContactProvider(DbContainer::getContactTable()->find(rand(1, 2)))
            ->setCompanyReceiver(DbContainer::getCompanyTable()->find(2))
            ->setContactReceiver(DbContainer::getContactTable()->find(rand(1, 2)))
            ->setLetterTemplate(DbContainer::getLetterTemplateTable()->find(rand(1, 2)))
            ->hydrate(new LetterBean());
            $this->pdf(new TcpdfLetter($letterBean));
        } catch (Exception $e) {
            var_dump($e);
        }
    }
    
    public function serializedAction()
    {
        $this->pdf(new TcpdfLetter(unserialize(file_get_contents(self::TMP_SERIALIZED_FILE))));
    }
    
    public function lettersimpleAction()
    {
        $this->disableViewAndLayout();
        $this->clearResponse();
        Container::getResponse()->setTypePdf();
    
        try {
    
            $company = DbContainer::getCompanyTable()->find(12);
            $companyAddress = $company->getRelatedAddressRowFromIdAddressFk();
    
            $fromAddress = new AddressBean();
            $fromAddress->setAddress($companyAddress->getAddress())
            ->setCity($companyAddress->getCity())
            ->setPostalCode($companyAddress->getPostalCode())
            ->setTitle($company->getTitle());
    
            $logos = glob('/home/guillaume/tmp-logos/*.png');
            $logo = $logos[rand(0, count($logos) - 1)];
            //$logo = '/home/guillaume/tmp-logos/xbox.png';
    
            $from = new ContactBean();
            $from
            ->setCivility('M.')
            ->setFirstname("Guillaume-Marie")
            ->setLastname("Ponçon")
            ->setFunction("Directeur de la comptabilité")
            ->setTel("+33 (0) 5 59 83 94 06")
            ->setFax("+33 (0) 1 47 08 47 85")
            ->setGsm('+33 (0) 6 34 29 39 48')
            ->setEmail("contact@openstates.com")
            ->setUrl('http://www.openstates.com')
            ->setAddress($fromAddress)
            ->setCompanyLogo(new ImageBean($logo));
    
            $toAddress = new AddressBean();
            $toAddress
            ->setTitle('Direction Générale des Finances Publiques')
            ->setAddress("S.I.E. de Nanterre 1\n235, avenue Georges Clémenceau")
            ->setPostalCode('92756')
            ->setCity('NANTERRE CEDEX')
            ->setCountry('France');
    
            $to = new ContactBean();
            $to
            ->setCivility("Madame")
            ->setFirstname("Anne-Sophie")
            ->setLastname("Casal")
            ->setAddress($toAddress);
    
            $body = trim("

Votre facture du mois de mai ne nous a pas du tout été payée. 
C'est un **scandale** que nous ne souhaitons pas réitérer à l'avenir car trop 
dangereux pour notre comptabilité.

Veuillez s'il vous plait, illico presto, payer cette facture sous peine de 
poursuites de polices dans les rues de Paris et de New-York, c'est votre 
dernière chance avant d'être en mauvais état d'arrestation :

* Emprisonnement de 12 ans pour non respect des délais.
* Amende de 67500€ majorée de 1000€ par jour jusqu'à ce que mort s'en suive.

Si vous le souhaitez, vous pouvez nous payer par chèque bancaire, par chèque 
vacance, par carte bleue que vous nous envoyez avec vos code et des sous sur 
le compte, par liquide via une malette à remettre à notre banque.

Veuillez agréer, madame, monsieur, l'expression de nos sentiments les plus 
impatients quand à la réception de votre paiement.");
            
$body = trim("
    
Depuis la première seconde de notre rencontre, je suis absolument fou de vous. 
Tous les jours je suis hanté par votre présence, il ne passe pas une minute 
sans que je rêve de vous le soir.
    
Oui, je suis sûr que vous êtes la personne qui est faite pour moi, celle qui 
m'est promise depuis l'éternité, un cadeau venu du ciel, une bombe atomique 
de mon coeur.
    
D'ailleurs, je sais que le repassage, la cuisine, les rangements, la poussière, 
les lessives et toutes les corvées ménagères ne vous font pas peur.
    
Veuillez agréer l'expression de mes déclarations les plus sincères,");
    
//                        $body = "<p style=\"text-align:justify;\">Denique Antiochensis ordinis vertices sub uno elogio iussit occidi ideo efferatus, quod ei celebrari vilitatem intempestivam urgenti, cum inpenderet inopia, gravius rationabili responderunt; et perissent ad unum ni comes orientis tunc Honoratus fixa constantia restitisset.
//            Horum adventum praedocti speculationibus fidis rectores militum tessera data sollemni armatos omnes celeri eduxere procursu et agiliter praeterito Calycadni fluminis ponte, cuius undarum magnitudo murorum adluit turres, in speciem locavere pugnandi. neque tamen exiluit quisquam nec permissus est congredi. formidabatur enim flagrans vesania manus et superior numero et ruitura sine respectu salutis in ferrum.
//            Unde Rufinus ea tempestate praefectus praetorio ad discrimen trusus est ultimum. ire enim ipse compellebatur ad militem, quem exagitabat inopia simul et feritas, et alioqui coalito more in ordinarias dignitates asperum semper et saevum, ut satisfaceret atque monstraret, quam ob causam annonae convectio sit impedita.
//            Sed (saepe enim redeo ad Scipionem, cuius omnis sermo erat de amicitia) querebatur, quod omnibus in rebus homines diligentiores essent; capras et oves quot quisque haberet, dicere posse, amicos quot haberet, non posse dicere et in illis quidem parandis adhibere curam, in amicis eligendis neglegentis esse nec habere quasi signa quaedam et notas, quibus eos qui ad amicitias essent idonei, iudicarent. Sunt igitur firmi et stabiles et constantes eligendi; cuius generis est magna penuria. Et iudicare difficile est sane nisi expertum; experiendum autem est in ipsa amicitia. Ita praecurrit amicitia iudicium tollitque experiendi potestatem.
//            Sed (saepe enim redeo ad Scipionem, cuius omnis sermo erat de amicitia) querebatur, quod omnibus in rebus homines diligentiores essent; capras et oves quot quisque haberet, dicere posse, amicos quot haberet, non posse dicere et in illis quidem parandis adhibere curam, in amicis eligendis neglegentis esse nec habere quasi signa quaedam et notas, quibus eos qui ad amicitias essent idonei, iudicarent. Sunt igitur firmi et stabiles et constantes eligendi; cuius generis est magna penuria. Et iudicare difficile est sane nisi expertum; experiendum autem est in ipsa amicitia. Ita praecurrit amicitia iudicium tollitque experiendi potestatem.</p>";
    
//    $body = "Common: “ ” ‘ ’ – — … ‐ ‒ ° © ® ™ • ½ ¼ ¾ ⅓ ⅔ † ‡ µ ¢ £ € « » ♠ ♣ ♥ ♦ ¿ �
//
//Math: - × ÷ ± ∞ π ∅ ≤ ≥ ≠ ≈ ∧ ∨ ∩ ∪ ∈ ∀ ∃ ∄ ∑ ∏ ← ↑ → ↓ ↔ ↕ ↖ ↗ ↘ ↙ ↺ ↻ ⇒ ⇔
//
//SuperSub: ⁰ ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁺ ⁻ ⁽ ⁾ ⁿ ⁱ ₀ ₁ ₂ ₃ ₄ ₅ ₆ ₇ ₈ ₉ ₊ ₋ ₌ ₍ ₎";
            
            $letterBean = new LetterBean();
            $letterBean
            ->setSubject('Réclamation de facture impayée')
            ->setBody('coucou')
            ->setProvider($from)
            ->setRecipient($to)
            ->setObject("Facture impayée n°2398 de 67500€")
            //->setAttn(null)
            ->setHeadLib('Remarque :', "c'est pas la première fois que vous faites ce coup là")
            //->setVref("#234589")
            //->setNref('F34/A45')
//            ->setAttn("Service comptabilité et factures")
            ->setBody($body)
            //->setSignature("Charles-Joseph Martin")
            ;
    
            if (!file_exists(self::TMP_SERIALIZED_FILE)) {
                file_put_contents(self::TMP_SERIALIZED_FILE, serialize($letterBean));
            }
            
            if ($this->getParam('font') == 'times') {
                $fonts = ['times', 'times'];
            } elseif ($this->getParam('font') == 'helvetica') {
                $fonts = ['helvetica', 'helvetica_light'];
            } else {
                $fonts = ['latomedium', 'latolight'];
            }
            
            $letter = new TcpdfLetter($letterBean);
            $letter->getBean()
                   ->getConfig()
                   ->setMaxFontSize(12)
                   ->setFontFamily($fonts[0], $fonts[1]);
            echo $letter->output();
        } catch (\Exception $e) {
            var_dump($e); exit;
        }
    
    }
    
    public function modelsAction()
    {
        $this->disableView();
        $this->disableLayout();
        header('Content-type: text/html; charset=utf-8');
        $generator = new DbGenerator();
        $generator->generateClasses();
        echo 'Modèles générés';
    }
    
    public function uiAction()
    {
        $loginForm = new FormLogin();
        $testForm = new FormTest();
        if ($testForm->isPostedAndValid()) {
            echo "OK";
        }
        return ['loginForm' => $loginForm, 
                'testForm'  => $testForm];
    }
    
    public function formsAction()
    {
        L::setPageTitle('Form generation from DB');
        $tables = glob(__DIR__ . '/../../Sma/Db/*Table.php');
        $forms = [];
        foreach ($tables as $file) {
            $table = basename($file, '.php');
            $getter = 'get' . $table;
            $form = DB::$getter()->getForm()
                    ->setPrefix(DB::$getter()->getTableName())
                    ->displayLabels(false)
                    ->setTitle(DB::$getter()->getTableName(), 'pencil');
            if ($form->isPostedAndValid()) {
                \H::layout()->addAlert(null, 'Ok good !', L::STATUS_SUCCESS);
            }
            $forms[] = $form;
        }
        
        return ['forms' => $forms];
    }
    
    public function kitchenAction()
    {
//        Container::getJsonRequest()->cleanButtons();
//        Container::getJsonRequest()->updateButtonAlerts('Alertes', L::ICON_WARNING, 1, 'danger', 'http://www.google.fr', 'Google');
//        Container::getJsonRequest()->updateButtonMessages('Messages', L::ICON_LETTER, 1, 'success', 'http://www.google.fr', 'Google');
//        Container::getJsonRequest()->updateButtonNotifications('Notifications', L::ICON_CALLOUT, 10, 'warning', 'http://www.google.fr', 'Google');
//        Container::getJsonRequest()->addButtonAlertsLink(1, '/', "<h3>Design some buttons<small class='pull-right'>20%</small></h3><div class='progress xs'><div class='progress-bar progress-bar-aqua' style='width: 20%' role='progressbar' aria-valuenow='20' aria-valuemin='0' aria-valuemax='100'><span class='sr-only'>20% Complete</span></div></div>");
//        Container::getJsonRequest()->addButtonMessageLink(2, '/product/list', "<h3>Design some buttons<small class='pull-right'>30%</small></h3><div class='progress xs'><div class='progress-bar progress-bar-aqua' style='width: 20%' role='progressbar' aria-valuenow='20' aria-valuemin='0' aria-valuemax='100'><span class='sr-only'>20% Complete</span></div></div>");
//        $i = 10;
//        Container::getJsonRequest()->addButtonNotificationsLink($i--, '/dev/list', 'Arf...', 'user');
//        Container::getJsonRequest()->addButtonNotificationsLink($i--, '/dev/list', 'Ya des soucis ?', 'at');
//        Container::getJsonRequest()->addButtonNotificationsLink($i--   , '/dev/list', '5 new members joined today5 new members joined today5 new members joined today5 new members joined today', 'users', 'orange');
//        Container::getJsonRequest()->addButtonNotificationsLink($i--, '/dev/list', 'Une autre...', 'user');
//        Container::getJsonRequest()->addButtonNotificationsLink($i--, '/dev/list', 'Une Encore...', 'circle-o');
//        Container::getJsonRequest()->addButtonNotificationsLink($i--, '/dev/list', 'Ya des soucis ?', 'at');
//        Container::getJsonRequest()->addButtonNotificationsLink($i--, '/dev/list', 'Arf...', 'user');
//        Container::getJsonRequest()->addButtonNotificationsLink($i--   , '/dev/list', '5 new members joined today', 'users', 'orange');
//        Container::getJsonRequest()->addButtonNotificationsLink($i--, '/dev/list', 'Une autre...', 'user');
//        Container::getJsonRequest()->addButtonNotificationsLink($i--, '/dev/list', 'Une Encore...', 'circle-o');
//        Container::getFlashMessenger()->cleanNotifications();
//        Container::getFlashMessenger()
//                ->addNotification(1, 'Je suis un dieu', 'http://www.openstates.com', 'user', 'orange')
//                ->addNotification(2, 'Il est un esclave', '/dev/detail/id/4', 'plus', 'red')
//                ->addNotification(3, 'Une notification avec un texte super long qui est cool', '/dev/list', 'power-off', 'aqua')
//                ->addNotification(6, 'Une autre notification encore', '/dev/list', 'circle-o', 'green')
//                ->addNotification(8, 'La vie est dure', '/dev/list', 'circle-o', 'black');
//        var_dump(Container::getFlashMessenger()->getActions());
    }
    
    public function testtcAction()
    {
        
        $fromAddress = new AddressBean();
        $fromAddress->setAddress("16 avenue des Chateaupieds\nImmeuble des grands airs")
        ->setCity('St Germain Lès Roses')
        ->setPostalCode('45389')
        //->setCountry('FRANCE')
        ->setTitle('OpenStates S.A.R.L.');

        $logos = glob('/home/guillaume/tmp-logos/*.png');
        $logo = $logos[rand(0, count($logos) - 1)];
        //$logo = '/home/guillaume/tmp-logos/ingdirect.png';

        $contactBean =(new ContactBean())
            ->setCivility('M.')
            ->setFirstname("Guillaume-Marie")
            ->setLastname("Ponçon")
            ->setFunction("Directeur de la comptabilité")
            ->setTel("+33 (0) 5 59 83 94 06")
            ->setFax("+33 (0) 1 47 08 47 85")
            ->setGsm("+33 (0) 6 67 08 89 85")
            ->setEmail("contact@openstates.com")
            ->setUrl('http://www.openstates.com/contact')
            ->setAddress($fromAddress)
            ->setCompanyLogo(new ImageBean($logo))
            ;

            $toAddress = new AddressBean();
            $toAddress
            ->setTitle('Direction Générale des Finances Publiques')
            ->setAddress("S.I.E. de Nanterre 1\n235, avenue Georges Clémenceau")
            ->setPostalCode('92756')
            ->setCity('NANTERRE CEDEX')
            //->setCountry('France')
                    ;
        
        $this->disableViewAndLayout();
        $this->getResponse()->setTypePdf();
        
        $doc = new PdfDocument();
        $doc->setCreator(APP_NAME . ' generator')
            ->setAuthor(APP_NAME)
            ->setTitle('Document Title')
            ->setSubject('Sujet')
            ->setHeadFootInfo($contactBean, 'Rueil-Malmaison')
            //->setDefaultFont('helvetica', 'helvetica_light')->setFont('helvetica_light')
            ->addPage()
            ->addAddressWindow($toAddress)
            ->write(0, str_repeat("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin faucibus consectetur ipsum et placerat. Nulla aliquet egestas justo. Maecenas eleifend in urna ultrices molestie. Pellentesque ut vehicula orci. Suspendisse potenti. Vivamus libero augue, interdum vel placerat non, maximus eu est. Suspendisse sit amet ante neque. Sed ac pharetra neque. Mauris vulputate mollis augue et mollis. Ut finibus augue libero. ", 20))
            ;
        echo $doc->output();
    }
    
    public function testemailAction()
    {
        $this->disableView();
        $mail = new \Sma\Mail;
        $r = $mail->addTo('gponcon@gmail.com', 'Guillaume')
                ->setSender('contact@openstates.com', 'Contact OpenStates')
                ->addFrom('contact@openstates.com')
                ->addCc('guillaume.poncon@openstates.com', 'Guillaume')
                //->setHeader("Email invisible ? Tant pis...")
                //->setFooter('Copyright XXX')
                ->setTitle("Titre de l'e-mail")
                ->setSubject("Sujet de l'email")
                //->setSubtitle("Sous titre de l'email")
                ->setShortpreview("Un short preview de cet email...")
                //->setIntroduction("Cet email est un email de test. Ce paragraphe est une intro.")
                ->addParagraph('Bienvenue sur ' . APP_NAME, "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse quis gravida arcu. Phasellus venenatis erat eu erat iaculis imperdiet. Aenean sagittis et velit in aliquam. Nullam magna nibh, euismod in pharetra non, mollis ut lacus. Fusce eu tempus nibh. Sed commodo, turpis in varius laoreet, ligula eros hendrerit felis, eu mollis nulla mauris ac magna. Morbi luctus mauris at augue fringilla, vitae lobortis massa accumsan. Duis dignissim blandit luctus. Suspendisse ac tristique lacus, sit amet viverra nisi. Integer luctus pharetra tellus, et pretium mauris cursus id. Vestibulum pharetra nibh sit amet tellus pretium laoreet. Sed placerat molestie risus non scelerisque. Donec hendrerit eros id bibendum tristique. Fusce consectetur nec ex et scelerisque. Nullam ac mi mollis, fermentum nunc eu, ornare risus. Proin at ex metus.")
                ->addHr()
                ->addParagraph("Point 1", "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse quis gravida arcu.", "Point 2", "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse quis gravida arcu.")
                ->addHr()
                ->addMarkdown("# Titre de niveau 1\n"
                        . "\n"
                        . "## Titre de niveau 2\n"
                        . "\n"
                        . "## Titre de niveau 3\n"
                        . "\n"
                        . "## Titre de niveau 4\n"
                        . "\n"
                        . "## Titre de niveau 5\n"
                        . "\n"
                        . "Avec du texte en **gras** et des [liens](http://www.openstates.com) et plein d'autres choses encore tel que des points :\n"
                        . "\n"
                        . "* Un bon point\n"
                        . "* Un autre bon point\n"
                        . "\n"
                        . "> Un texte\n"
                        . "> indenté ?\n"
                        . "\n"
                        . "Voila pour le markdown.")
                ->send(false);
        if ($r === false) {
            $this->alertWarning("Arf... ça a pas l'air de marcher...");
        } else {
            $this->alertInfo("E-mail envoyé. En mode debug, voir dans /tmp/sma_mails.");
        }
    }
}
