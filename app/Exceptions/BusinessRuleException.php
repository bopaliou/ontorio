<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a business rule is violated (e.g. already-paid rent).
 */
class BusinessRuleException extends RuntimeException {}
