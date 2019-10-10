<?php

namespace App\Application\EventSubscriber;

use App\Presentation\Api\Rest\Controller\TokenAuthenticatedController;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class TokenSubscriber implements EventSubscriberInterface
{
    /**
     * @var ResourceServer
     */
    private $resourceServer;

    /**
     * TokenSubscriber constructor.
     * @param ResourceServer $resourceServer
     */
    public function __construct(ResourceServer $resourceServer)
    {
        $this->resourceServer = $resourceServer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * @param ControllerEvent $event
     * @throws OAuthServerException
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        if (!\is_array($controller)) {
            return;
        }
        if ($controller[0] instanceof TokenAuthenticatedController) {
            $request = $event->getRequest();
            $psrRequest = (function (Request $symfonyRequest) {
                $psr17Factory = new Psr17Factory();
                $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
                return $psrHttpFactory->createRequest($symfonyRequest);
            })($request);
            try {
                $psrRequest = $this->resourceServer->validateAuthenticatedRequest($psrRequest);
            } catch (OAuthServerException $exception) {
                throw $exception;
            } catch (\Exception $exception) {
                throw new OAuthServerException($exception->getMessage(), 0, 'unknown_error', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $this->enrichSymfonyRequestWithAuthData($request, $psrRequest);
        }
    }

    /**
     * @param Request $request
     * @param ServerRequestInterface $psrRequest
     */
    private function enrichSymfonyRequestWithAuthData(Request $request, ServerRequestInterface $psrRequest): void
    {
        $request = $request->request;
        $requestArray = $request->all();
        $requestArray['oauth_user_id'] = $psrRequest->getAttribute('oauth_user_id');
        $requestArray['oauth_access_token_id'] = $psrRequest->getAttribute('oauth_access_token_id');
        $requestArray['oauth_client_id'] = $psrRequest->getAttribute('oauth_client_id');
        $request->replace($requestArray);
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getException();
        if (!($exception instanceof OAuthServerException)) {
            return;
        }
        $response = new JsonResponse(['error' => $exception->getMessage()], $exception->getHttpStatusCode());
        $event->setResponse($response);
    }
}
