<?php
namespace App\Recipient\Model\RecipientDbManager;

use Osf\Test\Runner as OsfTest;
use App\Recipient\Model\RecipientDbManager as RDM;

/**
 * Admin menu test
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
        
        self::assertEqual(RDM::parseString('éric, smith'), [
            'u' => [
                'firstname' => 'Éric',
                'lastname' => 'Smith'
                ]
            ]);
        self::assertEqual(RDM::parseString('m éric, smith'), [
            'u' => [
                'civility' => 'M.',
                'firstname' => 'Éric',
                'lastname' => 'Smith'
                ]
            ]);
        self::assertEqual(RDM::parseString('m éric, smith; eric.smith@test.com'), [
            'u' => [
                'civility' => 'M.', 
                'firstname' => 'Éric', 
                'lastname' => 'Smith'
                ],
            'c' => [
                'email' => 'eric.smith@test.com'
                ]
            ]);
        self::assertEqual(RDM::parseString(' mme anne -sophie, du timon de la charette; asDuTimON@tEST.Com'), [
            'u' => [
                'civility' => 'Mme',
                'firstname' => 'Anne-Sophie',
                'lastname' => 'Du Timon de la Charette',],
            'c' => [
                'email' => 'asdutimon@test.com'
                ]
            ]); 
        self::assertEqual(RDM::parseString(' asDuTimON@tEST.Com'), [
            'c' => [
                'email' => 'asdutimon@test.com',
                'title' => 'asdutimon@test.com'
                ]
            ]); 
        self::assertEqual(RDM::parseString(' MyCompany; éric, smith; 45 rue des poules 44000 nantes; eric.SMITH@Test.com'), [
            'c' => [
                'title' => 'MyCompany',
                'email'=>'eric.smith@test.com'
                ],
            'u' => [
                'firstname' => 'Éric',
                'lastname' => 'Smith'
                ],
            'a' => [
                'address' => '45 rue des poules',
                'postal_code' => '44000',
                'city' => 'Nantes'
                ]
            ]);
        
        return self::getResult();
    }
}
