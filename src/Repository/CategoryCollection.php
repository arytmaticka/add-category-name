<?php

namespace App\Repository;

use App\Traits\JsonArrayTrait;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CategoryCollection
{
    private PropertyAccessorInterface $propertyAccess;
    private ?string $key = 'category_id';

    use JsonArrayTrait;

    public function __construct()
    {
        $this->propertyAccess = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param int $category_id Category identifier
     * @param string $language Language code
     * @return string|null Return category name or null if not exists
     */
    public function getCategoryNameById(int $category_id, string $language = 'pl_PL'): ?string
    {
        return $this->propertyAccess->getValue($this->data, "[$category_id][translations][$language][name]");
    }

}