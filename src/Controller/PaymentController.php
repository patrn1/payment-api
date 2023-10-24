<?php

namespace App\Controller;

use App\Form\PaymentRequestType;
use App\Helper\FormHelper;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Repository\TaxRateRepository;
use App\Request\PaymentRequest;
use App\Response\ErrorResponse;
use App\Response\SuccessResponse;
use App\Service\PaymentProviderEnum;
use App\Service\PaymentService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Handles payment requests for products.
 *
 **/
class PaymentController extends BaseController
{
    public function __construct(
        protected ProductRepository $productRepository,
        protected CouponRepository $couponRepository,
        protected PaymentService $paymentService,
        protected LoggerInterface $logger,
        protected TaxRateRepository $taxRateRepository,
    ) {
    }

    /**
     * Gets the requested data after validating a request.
     *
     * @param Request request
     *
     * @return array
     *
     **/
    public function getRequestedData(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        $request = new PaymentRequest($this->taxRateRepository);
        $form = $this->createForm(PaymentRequestType::class, $request);
        $form->submit($data);

        if (!$form->isValid()) {
            $formErrors = array_merge(
                FormHelper::getErrors($form),
                [(string) $form->getErrors()],
            );

            $response = new ErrorResponse($form->isValid() ? [] : $formErrors);

            $response->send();

            exit;
        }

        $formData = $form->getData();

        $product = $this->productRepository->findOneBy([
            'id' => $formData->product,
        ]);

        if (!$product) {
            $response = new ErrorResponse(['Product not found !']);

            $response->send();
            exit;
        }

        if (isset($formData->couponCode)) {
            $coupon = $this->couponRepository->findOneBy([
                'code' => $formData->couponCode,
            ]);

            if (!$coupon) {
                $response = new ErrorResponse(['Coupon not found !']);

                $response->send();
                exit;
            }
        }

        return [
            $formData,
            $product,
            $coupon ?? null,
            PaymentProviderEnum::tryFrom($formData->paymentProcessor),
        ];
    }

    /**
     * Calculates price for a product.
     *
     * @param Request request
     *
     * @return JsonResponse
     *
     **/
    #[Route('/calculate-price', name: 'app_calculate_price', methods: ['POST'])]
    public function calculatePrice(Request $request): JsonResponse
    {
        list($requestData, $product, $coupon) = $this->getRequestedData($request);

        $totalPrice = $this->paymentService->getTotalPrice(
            $requestData->taxRate, $product, $coupon
        );

        return new SuccessResponse([
            'totalPrice' => $totalPrice,
        ]);
    }

    /**
     * Handles purchase of a product.
     *
     * @param Request request
     *
     * @return JsonResponse
     *
     **/
    #[Route('/purchase', name: 'app_purchase', methods: ['POST'])]
    public function purchase(Request $request): JsonResponse
    {
        list($requestData, $product, $coupon, $paymentProcessor) = $this->getRequestedData($request);

        try {
            $this->paymentService->processPayment(
                $paymentProcessor,
                $requestData->taxRate,
                $product,
                $coupon
            );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());

            return new ErrorResponse([
                $e->getMessage(),
            ]);
        }

        return new SuccessResponse();
    }
}
