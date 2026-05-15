<?php

declare(strict_types=1);

namespace App\Cash\Application\StartClosingCashSession;

use App\Cash\Domain\Exception\CashSessionNotFoundException;
use App\Cash\Domain\Exception\OpenOperationsPreventClosingException;
use App\Cash\Domain\Interfaces\CashSessionRepositoryInterface;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Shared\Domain\Interfaces\TransactionManagerInterface;
use App\Shared\Domain\ValueObject\Uuid;

final class StartClosingCashSession
{
    public function __construct(
        private readonly CashSessionRepositoryInterface $cashSessionRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly TransactionManagerInterface $transactionManager,
    ) {}

    public function __invoke(StartClosingCashSessionCommand $command): StartClosingCashSessionResponse
    {
        return $this->transactionManager->run(function () use ($command) {
            $cashSession = $this->cashSessionRepository->findByUuid(Uuid::create($command->cashSessionId))
                ?? throw CashSessionNotFoundException::withId($command->cashSessionId);

            $activeOrders = $this->orderRepository->countActiveByRestaurantId($cashSession->restaurantId());

            if ($activeOrders > 0) {
                throw new OpenOperationsPreventClosingException($activeOrders);
            }

            $cashSession->startClosing();
            $this->cashSessionRepository->save($cashSession);

            return StartClosingCashSessionResponse::create(
                id: $cashSession->id()->value(),
                status: $cashSession->status()->value(),
            );
        });
    }
}
