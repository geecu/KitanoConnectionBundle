<?php

namespace Kitano\ConnectionBundle\Manager;

use Kitano\ConnectionBundle\Exception\InvalidFilterException;

use Kitano\ConnectionBundle\Model\ConnectionInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Collection;

class FilterValidator
{
    protected $validator;

    /**
     * Validate and normalize input filters for connections retrieval
     *
     * @param array &$filters
     * @throws InvalidFilterException
     */
    public function validateFilters(array &$filters)
    {
        $direction = new Choice();
        $direction->choices = array(
            ConnectionInterface::DIRECTION_ONE_WAY,
            ConnectionInterface::DIRECTION_TWO_WAYS
        );
        $filterConstraint = new Collection(array(
                'type' => array(
                    new NotBlank(),
                    new NotNull(),
                ),
                'direction' => $direction,
                'depth' => new Type('integer'),
            ));

        $filtersDefault = array(
            'direction' => ConnectionInterface::DIRECTION_TWO_WAYS,
            'depth' => 1
        );

        $filters = array_merge($filtersDefault, $filters);

        $errorList = $this->getValidator()->validateValue($filters, $filterConstraint);

        if (count($errorList) == 0) {
            return true;
        } else {
            throw new InvalidFilterException($errorList);
        }
    }

    public function setValidator(Validator\LegacyValidator $validator)
    {
        $this->validator = $validator;
    }

    public function getValidator()
    {
        return $this->validator;
    }
}
