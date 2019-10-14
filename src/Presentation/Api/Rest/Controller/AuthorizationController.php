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
use Zend\Diactoros\Response as Psr7Response;

final class AuthorizationController
{
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
     * @param AuthorizationServer $authorizationServer
     * @param AuthCodeRepository $authCodeRepository
     * @param RefreshTokenRepository $refreshTokenRepository
     * @throws \Exception
     */
    public function __construct(
        AuthorizationServer $authorizationServer,
        AuthCodeRepository $authCodeRepository,
        RefreshTokenRepository $refreshTokenRepository
    )
    {
        $this->authorizationServer = $authorizationServer;

        $this->authCodeGrant = new AuthCodeGrant(
            $authCodeRepository,
            $refreshTokenRepository,
            new \DateInterval('PT10M')
        );
        $this->authCodeGrant->disableRequireCodeChallengeForPublicClients();
    }

    /**
     * @Route("authorize", name="api_authorize", methods={"GET"})
     * @param ServerRequestInterface $serverRequest
     * @return Psr7Response
     * @throws \Exception
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

            $authRequest->setUser(new User());
            $authRequest->setAuthorizationApproved(false);

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
