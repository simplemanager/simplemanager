<?php
namespace Sma\Db;

/**
 * Registry of sql queries
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 */
class DbRegistry
{
    use DbRegistry\NotificationManagement;
    use DbRegistry\AccountManagement;
    use DbRegistry\AddressManagement;
    use DbRegistry\CompanyManagement;
    use DbRegistry\ContactManagement;
    use DbRegistry\GuestManagement;
    use DbRegistry\ExchangeManager;
}
