<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 6/29/2017
 * Time: 12:59 PM
 */

namespace Mocks\MockHttpService\Mappers;

use PhpPact\Mocks\MockHttpService\Mappers\HttpRequestMessageMapper;
use PHPUnit\Framework\TestCase;

class HttpRequestMessageMapperTest extends TestCase
{

    public function testConvert() {
        $mapper = new \PhpPact\Mocks\MockHttpService\Mappers\HttpRequestMessageMapper();

        // test standard
        $obj = new \stdClass();
        $obj->method = 'post';
        $obj->path = '/test';
        $obj->headers = array();
        $obj->headers["Content-Type"] = "application/json";
        $obj->body = "Do not tell me what I can do to my body";

        $providerServiceRequestMapper = new \PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceRequestMapper();
        $providerServiceRequest = $providerServiceRequestMapper->Convert($obj);
        $httpRequest = $mapper->Convert($providerServiceRequest, "http://localhost");

        $this->assertTrue(($httpRequest instanceof \Psr\Http\Message\RequestInterface), "We expect a Psr request");

        $actualHeaders = $httpRequest->getHeaders();
        $this->assertTrue(isset($actualHeaders["Content-Type"]), "We expect one header - content-type");
        $this->assertEquals('/test',$httpRequest->getUri()->getPath(), "Test that path was set appropriately");
        $this->assertFalse(($httpRequest->getUri()->getQuery()? true : false), "Test that query was not set.  Note this is an explicit false check");
        $this->assertEquals($obj->body, (string) $httpRequest->getBody(), "Body is set appropriately");


        // test query
        $obj = new \stdClass();
        $obj->method = 'post';
        $obj->path = '/test';
        $obj->query = '?x=1&y=2';
        $obj->headers = array();
        $obj->headers["Content-Type"] = "application/json";
        $obj->body = "Do not tell me what I can do to my body";

        $providerServiceRequestMapper = new \PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceRequestMapper();
        $providerServiceRequest = $providerServiceRequestMapper->Convert($obj);
        $httpRequest = $mapper->Convert($providerServiceRequest, "http://localhost");

        $this->assertTrue(($httpRequest instanceof \Psr\Http\Message\RequestInterface), "We expect a Psr request");

        $actualHeaders = $httpRequest->getHeaders();
        $this->assertTrue(isset($actualHeaders["Content-Type"]), "We expect one header - content-type");
        $this->assertEquals('/test',$httpRequest->getUri()->getPath(), "Test that path was set appropriately");
        $this->assertEquals("x=1&y=2", $httpRequest->getUri()->getQuery(), "Test that query was set with the ? removed");
        $this->assertEquals($obj->body, (string) $httpRequest->getBody(), "Body is set appropriately");
    }

}
