<?php

namespace App\Repositories;

use App\Models\Category;
//use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{

    public function getAllCategories($perPage = 10)
    {
        return Category::paginate($perPage);
    }

    public function createCategory(array $data)
    {
        return Category::create($data);
    }

    public function getCategoryById($id)
    {
        return Category::findOrFail($id);
    }

    public function updateCategory(Category $category, array $data)
    {
        $category->update($data);
        return $category;
    }

    public function deleteCategory(Category $category)
    {
        return $category->delete();
    }

}
