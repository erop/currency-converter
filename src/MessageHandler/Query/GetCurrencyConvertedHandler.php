<?php


namespace App\MessageHandler\Query;


use App\Dto\ExchangeErrorResponse;
use App\Message\Query\GetCurrencyConverted;
use App\Service\CurrencyConverter;
use InvalidArgumentException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetCurrencyConvertedHandler implements MessageHandlerInterface
{
    /**
     * @var CurrencyConverter
     */
    private $converter;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * GetCurrencyConvertedHandler constructor.
     * @param CurrencyConverter $converter
     * @param ValidatorInterface $validator
     */
    public function __construct(CurrencyConverter $converter, ValidatorInterface $validator)
    {
        $this->converter = $converter;
        $this->validator = $validator;
    }

    public function __invoke(GetCurrencyConverted $query)
    {
        $errors = $this->validator->validate($query);
        if (count($errors) > 0) {
            throw new InvalidArgumentException((string)$errors);
        }
        $response = $this->converter->process($query);
        if (null === $response) {
            return new ExchangeErrorResponse('Could not convert given currency');
        }
        return $response;
    }


}
