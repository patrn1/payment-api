<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaymentControllerTest extends WebTestCase
{
    public $exampleProduct = [
        "product" => "1",
        "taxNumber" => "DE123456789",
        "paymentProcessor" => "paypal"
    ];

    public function getClient() {
        return static::createClient([], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);
    }

    public function request(string $url, array $content = []) {

        $content = json_encode(array_merge($this->exampleProduct, $content));

        $client = $this->getClient();
            
        $client->request('POST', $url, [], [], [], $content);

        $response = $client->getResponse();

        $responseJson = $response->getContent();

        $this->assertJson($responseJson);

        return json_decode($responseJson, true);
    }

    public function requestCalculatePrice(array $content = []) {

        return $this->request('/calculate-price', $content);
    }

    public function requestPurchase(array $content = []) {

        return $this->request('/purchase', $content);
    }

    public function checkHasSuccess(array $responseData) {

        $this->assertResponseIsSuccessful();

        $this->assertTrue($responseData['success']);

        $this->assertEquals(count($responseData['errors']), 0);
    }

    public function checkResponseScheme($responseData): void
    {
        $this->assertArrayHasKey('success', $responseData);

        $this->assertArrayHasKey('errors', $responseData);

        $this->assertArrayHasKey('data', $responseData);
    }

    public function testCalculatePriceResponse(): void
    {
        $responseData = $this->requestCalculatePrice();

        $this->checkResponseScheme($responseData);
    }

    public function testTotalPrice(): void
    {
        $responseData = $this->requestCalculatePrice();

        $this->checkHasSuccess($responseData);

        $this->assertArrayHasKey('totalPrice', $responseData['data']);

        $this->assertEquals($responseData['data']['totalPrice'], 119);
    }

    public function testTotalPriceWithFixedDiscountCoupon(): void
    {
        $responseData = $this->requestCalculatePrice([ "couponCode" => "D15" ]);

        $this->checkHasSuccess($responseData);

        $this->assertArrayHasKey('totalPrice', $responseData['data']);

        $this->assertEquals($responseData['data']['totalPrice'], 95.2);
    }

    public function testTotalPriceWithPercentDiscountCoupon(): void
    {
        $responseData = $this->requestCalculatePrice([ "couponCode" => "02I4JR" ]);

        $this->checkHasSuccess($responseData);

        $this->assertArrayHasKey('totalPrice', $responseData['data']);

        $this->assertEquals($responseData['data']['totalPrice'], 101.15);
    }

    // public function testPurchaseResponse(): void
    // {
    //     $responseData = $this->requestPurchase([ "couponCode" => "D15" ]);

    //     $this->checkResponseScheme($responseData);
    // }

    // public function testPurchase(): void
    // {
    //     $responseData = $this->requestPurchase([ "couponCode" => "D15" ]);

    //     $this->checkHasSuccess($responseData);
    // }
}
