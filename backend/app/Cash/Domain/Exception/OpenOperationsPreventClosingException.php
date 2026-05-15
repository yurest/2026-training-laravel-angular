<?php

declare(strict_types=1);

namespace App\Cash\Domain\Exception;

final class OpenOperationsPreventClosingException extends \DomainException
{
    public function __construct(public readonly int $activeOrders)
    {
        $label = $activeOrders === 1 ? 'mesa activa' : 'mesas activas';
        parent::__construct(
            "No se puede iniciar el cierre: hay {$activeOrders} {$label} con comandas pendientes o pendientes de cobrar."
        );
    }
}
