<?php
namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use App\Entity\Base\EntityId;
use Doctrine\Common\Persistence\ObjectRepository;

class EntityIdTransformer implements DataTransformerInterface
{

    private $repo;

    public function getRepo(): ObjectRepository
    {
        return $this->repo;
    }

    public function setRepo(ObjectRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param EntityId|null $entityId
     * @return string
     */
    public function transform($entityId)
    {
        if (null === $entityId) {
            return '';
        }
        
        return $entityId->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param string $issueNumber
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($id)
    {
        // no issue number? It's optional, so that's ok
        if (! $id) {
            return;
        }
        
        $entity = $this->getRepo()->find($id);
        
        if (null === $entity) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf('An entity with id "%s" does not exist!', $id));
        }
        
        return $entity;
    }
}