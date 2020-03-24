<?php
declare(strict_types=1);

namespace Serato\AppEvents\Exception;

use RuntimeException;

/**
 * Indicates that a host ID does not meet the expected requirements.
 *
 * Requirements are that:
 * 1. There are at minimum two sections within the host ID separated by a "~" character
 */
class InvalidHostMachineUidException extends RuntimeException
{

}
