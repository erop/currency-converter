<?php


namespace App\Dto;


use Symfony\Component\Validator\Constraints as Assert;

class ExchangeErrorResponse
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $error;

    /**
     * ExchangeErrorResponse constructor.
     * @param $error
     */
    public function __construct($error)
    {
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

}
