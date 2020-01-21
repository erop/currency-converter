<?php

namespace App\Controller;

use App\Dto\ExchangeRequest;
use App\Service\CurrencyConverter;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * ExchangeController constructor.
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param CurrencyConverter $converter
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CurrencyConverter $converter
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->converter = $converter;
    }

    /**
     * @Route("/exchange", name="exchange", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $content = $request->getContent();
        /** @var ExchangeRequest $conversionRequest */
        $conversionRequest = $this->serializer->deserialize($content, ExchangeRequest::class, 'json');
        $errors = $this->validator->validate($conversionRequest);
        if (count($errors) > 0) {
            throw new InvalidArgumentException((string)$errors);
        }
        $response = $this->converter->process($conversionRequest);
        if (null === $response) {
            return $this->json(['error' => 'Could not convert given currency']);
        }

        return $this->json($response);
    }
}
