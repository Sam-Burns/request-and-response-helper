<?php
namespace SamBurns\RequestAndResponseHelper;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Stream;

class RequestAndResponseHelper
{
    /**
     * Turns the JSON in the request object into an array
     */
    public function requestBodyJsonToArray(ServerRequestInterface $request): array
    {
        $request->getBody()->rewind();
        $rawBodyContent = $request->getBody()->getContents();
        $urlDecodedContent = urldecode($rawBodyContent);
        $jsonDecodedContent = json_decode($urlDecodedContent, true);
        return $jsonDecodedContent ?: [];
    }

    /**
     * Takes an array and puts JSON on the request object
     */
    public function requestWithJsonAdded(RequestInterface $request, array $requestData): RequestInterface
    {
        $request = $this->getCopyOfRequestWithEmptyBody($request);
        return $this->addJsonToHttpMessage($request, $requestData);
    }

    /**
     * Takes an array and puts JSON on the response object
     */
    public function responseWithJsonAdded(ResponseInterface $response, array $responseData): ResponseInterface
    {
        return $this->addJsonToHttpMessage($response, $responseData);
    }

    private function addJsonToHttpMessage(MessageInterface $message, array $dataToTurnIntoJson)
    {
        $json = json_encode($dataToTurnIntoJson);
        $message->getBody()->write($json);
        return $message;
    }

    /**
     * Turns an HTTP response object, with JSON in the body, into an array
     */
    public function convertIncomingResponseToArray(ResponseInterface $response): array
    {
        $request->getBody()->rewind();
        $body = $response->getBody()->getContents();
        $bodyArray = json_decode($body, true);

        return $bodyArray ?: [];
    }

    private function getCopyOfRequestWithEmptyBody(RequestInterface $request): RequestInterface
    {
        $emptyStream = new Stream(fopen('php://temp', 'r+'));
        return $request->withBody($emptyStream);
    }
}
