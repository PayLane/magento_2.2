<?php
declare(strict_types=1);

/**
 * File:Data.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Notification;

/**
 * Class Data
 * @package LizardMedia\PayLane\Model\Notification
 */
class Data
{
    /**
     * @var string
     */
    const STATUS_PENDING = 'PENDING';

    /**
     * @var string
     */
    const STATUS_PERFORMED = 'PERFORMED';

    /**
     * @var string
     */
    const STATUS_CLEARED = 'CLEARED';

    /**
     * @var string
     */
    const STATUS_ERROR = 'ERROR';

    /**
     * @var string
     */
    const MODE_MANUAL = 'manual';

    /**
     * @var string
     */
    const MODE_AUTO = 'auto';
}
