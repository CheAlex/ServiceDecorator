<?php

declare(strict_types=1);

namespace App\ServiceDecorator\Middleware;

use Doctrine\ORM\EntityManagerInterface;

class TransactionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function execute($proxy, $instance, $method, $params, &$returnEarly, object $decoratedService, callable $next)
    {
        echo 'transaction:start;';

        $this->entityManager->getConnection()->beginTransaction();

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $returnValue = $next($proxy, $instance, $method, $params, $returnEarly, $decoratedService);
            $connection->commit();
        } catch (\Throwable $e) {
            $connection->rollback();

            if ((!$connection->isTransactionActive() || $connection->isRollbackOnly()) && $this->entityManager->isOpen()) {
                $this->entityManager->close();
            }

            throw $e;
        }

        echo 'transaction:stop;';

        return $returnValue;
    }
}
