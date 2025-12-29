<?php

namespace App\Domain\Product\DTOs;

use App\Models\Category;

class CategoryDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description,
        public readonly ?string $imageUrl,
        public readonly bool $isActive,
    ) {}

    public static function fromModel(Category $category): self
    {
        return new self(
            id: $category->id,
            name: $category->name,
            slug: $category->slug,
            description: $category->description,
            imageUrl: $category->image_url,
            isActive: $category->is_active,
        );
    }
}
