<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OperationSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $req = $event->getRequest();
        if ($req->isMethod('POST')) {
            $this->logger->info('[GRH] Requête POST reçue', [
                'route' => $req->getPathInfo(),
                'ip'    => $req->getClientIp(),
            ]);
        }
    }

    public function onException(ExceptionEvent $event): void
    {
        $this->logger->error('[GRH] Exception capturée', [
            'message' => $event->getThrowable()->getMessage(),
            'route'   => $event->getRequest()->getPathInfo(),
        ]);
    }
}
