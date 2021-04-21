<?php

namespace Registration\Log;

use Laminas\Log\LoggerInterface;

trait LoggerAwareTrait
{

    // @var LoggerInterface $logger The logger instance in use.
    private $logger;

    public function getLogger(): LoggerInterface
    {
        if ($this->logger == null) {
            $writer = new \Laminas\Log\Writer\Stream('php://stderr');
            $logger = new \Laminas\Log\Logger();
            $logger->addWriter($writer);
            $this->logger = $logger;
        }
        return $this->logger;
    }

}