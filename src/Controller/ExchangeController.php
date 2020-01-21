<?php

namespace App\Controller;

use App\Dto\ExchangeRequest;
use App\Message\Query\GetCurrencyConverted;
use App\Service\CurrencyConverter;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ExchangeController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var CurrencyConverter
     */
    private $converter;
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * ExchangeController constructor.
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        MessageBusInterface $messageBus
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->messageBus = $messageBus;
    }

    /**
     * @Route("/exchange", name="exchange", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function exchange(Request $request): JsonResponse
    {
        /** @var ExchangeRequest $exchangeRequest */
        $exchangeRequest = $this->serializer->deserialize($request->getContent(), ExchangeRequest::class, 'json');
        $requestErrors = $this->validator->validate($exchangeRequest);
        if (count($requestErrors) > 0) {
            throw new Exception((string)$requestErrors);
        }

        $envelop = $this->messageBus->dispatch($this->createQuery($exchangeRequest));
        /** @var HandledStamp $stamp */
        $stamp = $envelop->last(HandledStamp::class);
        return $this->json($stamp->getResult());
    }

    /**
     * @param ExchangeRequest $exchangeRequest
     * @return GetCurrencyConverted
     */
    protected function createQuery(ExchangeRequest $exchangeRequest): GetCurrencyConverted
    {
        return new GetCurrencyConverted(
            $exchangeRequest->getFromCurrency(),
            $exchangeRequest->getToCurrency(),
            $exchangeRequest->getFromAmount()
        );
    }
}
