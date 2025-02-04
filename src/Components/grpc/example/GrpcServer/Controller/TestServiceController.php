<?php

declare(strict_types=1);

namespace GrpcApp\GrpcServer\Controller;

use Grpc\TestServiceInterface;
use Imi\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Util\Http\Consts\RequestHeader;

/**
 * @Controller("/grpc.TestService/")
 */
class TestServiceController extends HttpController implements TestServiceInterface
{
    /**
     * Method <code>test</code>.
     *
     * @Action
     *
     * @return \Grpc\TestRequest
     */
    public function test(\Grpc\TestRequest $request)
    {
        $trailers = 'grpc-status, grpc-message, test';
        $this->response->setHeader('test', 'value');
        if ($this->request->hasHeader('grpc-test'))
        {
            $this->response->setHeader('grpc-test', $this->request->getHeaderLine('grpc-test'));
            $trailers .= ', grpc-test';
        }
        $this->response->setHeader(RequestHeader::TRAILER, $trailers);

        return $request;
    }
}
