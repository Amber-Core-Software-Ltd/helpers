<?php


namespace AmberCore\Helper\Service;


use AmberCore\Helper\Service\Exception\EntityWasNotFoundException;
use AmberCore\Helper\Service\Exception\UnableToCastException;
use AmberCore\Helper\Service\Exception\ValidationException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractService
{
    protected EntityManagerInterface $em;
    protected ValidatorInterface $validator;

    /**
     * AbstractService constructor.
     *
     * @param EntityManagerInterface $entity_manager
     * @param ValidatorInterface     $validator
     */
    public function __construct(
        EntityManagerInterface $entity_manager,
        ValidatorInterface $validator
    ) {
        $this->em        = $entity_manager;
        $this->validator = $validator;
    }

    /**
     * @param object $entity
     * @param bool   $soft
     */
    protected function remove(
        object $entity,
        bool $soft = false
    ): void {
        if (property_exists($entity, 'deleted_at') && !$soft)
        {
            $entity->deleted_at = time();

            $this->save($entity, false);
        }
        else
        {
            $this->em->remove($entity);

            $this->em->flush();
        }
    }

    /**
     * @param object $entity
     * @param bool   $validate
     *
     * @throws ValidationException
     */
    protected function save(
        object $entity,
        bool $validate = true
    ): void {
        if ($validate)
        {
            $this->validate($entity);
        }

        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * @param object $entity
     *
     * @throws ValidationException
     */
    protected function validate(object $entity): void
    {
        $errors = $this->validator->validate($entity);

        if (count($errors) > 0)
        {
            $class_path = explode('\\', get_class($entity));
            $class      = lcfirst(end($class_path));
            throw new ValidationException($class, $errors);
        }
    }

    /**
     * @param $entity
     * @param $class_name
     *
     * @return object|null
     * @throws EntityWasNotFoundException
     * @throws UnableToCastException
     */
    protected function cast($entity, $class_name): ?object
    {
        if ($entity === null)
        {
            return null;
        }

        if (is_a($entity, $class_name))
        {
            return $entity;
        }

        if (is_int($entity))
        {
            $entity_id = $entity;

            /** @var ServiceEntityRepository $company_repository */
            $company_repository = $this->em->getRepository($class_name);

            $entity = $company_repository->find($entity_id);

            if (!$entity)
            {
                throw new EntityWasNotFoundException(
                    'Entity for class ' . $class_name . ' with id: ' . $entity_id . ' was not found');
            }

            return $entity;
        }

        throw new UnableToCastException(
            'Unable to cast ' . $class_name . ' with type ' . gettype($entity) . ' data: ' . json_encode($entity));
    }
}