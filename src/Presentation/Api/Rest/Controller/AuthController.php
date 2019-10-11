<?php

namespace App\Presentation\Api\Rest\Controller;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Zend\Diactoros\Response as Psr7Response;

final class AuthController
{
    const ACCESS_TOKEN_TTL = 'PT1H'; // ttl is 1 hour
    const REFRESH_TOKEN_TTL = 'P1M'; // ttl is 1 month
    const AUTH_CODE_TTL = 'PT10M'; // ttl is 10 minutes

    /**
     * @var AuthorizationServer
     */
    private $authorizationServer;

    /**
     * @var ClientCredentialsGrant
     */
    private $clientCredentialsGrant;

    /**
     * @var PasswordGrant
     */
    private $passwordGrant;

    /**
     * @var RefreshTokenGrant
     */
    private $refreshTokenGrant;

    /**
     * AuthController constructor.
     * @param AuthorizationServer $authorizationServer
     * @param ClientCredentialsGrant $clientCredentialsGrant
     * @param PasswordGrant $passwordGrant
     * @param RefreshTokenGrant $refreshTokenGrant
     */
    public function __construct(
        AuthorizationServer $authorizationServer,
        ClientCredentialsGrant $clientCredentialsGrant,
        PasswordGrant $passwordGrant,
        RefreshTokenGrant $refreshTokenGrant
    )
    {
        $this->authorizationServer = $authorizationServer;
        $this->clientCredentialsGrant = $clientCredentialsGrant;
        $this->passwordGrant = $passwordGrant;
        $this->refreshTokenGrant = $refreshTokenGrant;
    }

    /**
     * @Route("token", name="oauth2_token", methods={"POST"})
     * @param ServerRequestInterface $serverRequest
     * @return Psr7Response
     * @throws \Exception
     */
    public function getToken(ServerRequestInterface $serverRequest): Psr7Response
    {
        return $this->withErrorHandling(function () use ($serverRequest) {

            $this->authorizationServer->enableGrantType(
                $this->clientCredentialsGrant,
                new \DateInterval(self::ACCESS_TOKEN_TTL)
            );

            $this->passwordGrant->setRefreshTokenTTL(new \DateInterval(self::REFRESH_TOKEN_TTL));
            $this->authorizationServer->enableGrantType(
                $this->passwordGrant,
                new \DateInterval(self::ACCESS_TOKEN_TTL)
            );

            $this->refreshTokenGrant->setRefreshTokenTTL(new \DateInterval(self::REFRESH_TOKEN_TTL));
            $this->authorizationServer->enableGrantType(
                $this->refreshTokenGrant,
                new \DateInterval(self::ACCESS_TOKEN_TTL)
            );

            return $this->authorizationServer->respondToAccessTokenRequest($serverRequest, new Psr7Response());
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
