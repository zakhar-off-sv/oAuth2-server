<?php

namespace App\Presentation\Api\Rest\Controller;

use App\Infrastructure\oAuth2Server\Bridge\Entity\User;
use App\Infrastructure\oAuth2Server\Bridge\Repository\AuthCodeRepository;
use App\Infrastructure\oAuth2Server\Bridge\Repository\RefreshTokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Zend\Diactoros\Response as Psr7Response;

final class AuthorizationController
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var AuthorizationServer
     */
    private $authorizationServer;

    /**
     * @var AuthCodeGrant
     */
    private $authCodeGrant;

    /**
     * AuthorizationController constructor.
     * @param Security $security
     * @param AuthorizationServer $authorizationServer
     * @param AuthCodeRepository $authCodeRepository
     * @param RefreshTokenRepository $refreshTokenRepository
     * @throws \Exception
     */
    public function __construct(
        Security $security,

        AuthorizationServer $authorizationServer,

        AuthCodeRepository $authCodeRepository,
        RefreshTokenRepository $refreshTokenRepository
    )
    {
        $this->security = $security;

        $this->authorizationServer = $authorizationServer;

        $this->authCodeGrant = new AuthCodeGrant(
            $authCodeRepository,
            $refreshTokenRepository,
            new \DateInterval('PT10M')
        );
    }

    /**
     * @Route("authorize", name="oauth2_authorize", methods={"GET"})
     * @param ServerRequestInterface $serverRequest
     * @return Psr7Response
     */
    public function authorize(ServerRequestInterface $serverRequest): Psr7Response
    {
        return $this->withErrorHandling(function () use ($serverRequest) {

            $this->authCodeGrant->setRefreshTokenTTL(new \DateInterval('P1M'));
            $this->authorizationServer->enableGrantType(
                $this->authCodeGrant,
                new \DateInterval('PT1H')
            );

            $authRequest = $this->authorizationServer->validateAuthorizationRequest($serverRequest);

            $user = $this->security->getUser();
            if ($user instanceof UserInterface) {
                $authRequest->setUser(new User($user->getId()->toString()));
                $authRequest->setAuthorizationApproved(true);
            } else {
                $authRequest->setUser(new User());
                $authRequest->setAuthorizationApproved(false);
            }

            return $this->authorizationServer->completeAuthorizationRequest($authRequest, new Psr7Response());
        });
    }

    private function withErrorHandling($callback): Psr7Response
    {
        try {
            return $callback();
        } catch (OAuthServerException $e) {
            return $this->convertResponse(
                $e->generateHttpResponse(new Psr7Response())
            );
        } catch (\Exception $e) {
            return new Psr7Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $e) {
            return new Psr7Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function convertResponse(Psr7Response $psrResponse): Psr7Response
    {
        return new Psr7Response(
            $psrResponse->getBody(),
            $psrResponse->getStatusCode(),
            $psrResponse->getHeaders()
        );
    }
}
