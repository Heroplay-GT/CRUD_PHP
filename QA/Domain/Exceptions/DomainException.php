<?php

declare(strict_types=1);

namespace QA\Domain\Exceptions;

/**
 * Errores de reglas de negocio (no de validación de argumentos puntuales).
 */
abstract class DomainException extends \LogicException
{
}
