<?php

namespace App\Presentation\Api\Rest\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TestController
 * @package App\Presentation\Api\Rest\Controller
 */
final class TestController implements TokenAuthenticatedController
{
    /**
     * @Route("test", name="oauth2_test", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function getTest(Request $request): Response
    {
        $info = [
            'user_id' => $request->get('oauth_user_id'),
            'client_id' => $request->get('oauth_client_id'),
        ];
        return new JsonResponse($info, Response::HTTP_OK);
    }
}
