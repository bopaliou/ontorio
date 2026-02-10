<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a migration integrity check fails.
 */
class MigrationIntegrityException extends RuntimeException {}
