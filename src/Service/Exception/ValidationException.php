<?php


namespace AmberCore\Helper\Service\Exception;


use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class ValidationException extends \Exception
{
    /**
     * @var ConstraintViolationListInterface
     */
    public $errors;
    /** @var string */
    public $entity;

    public function __construct(string $entity, ConstraintViolationListInterface $errors, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->entity = $entity;
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }
}