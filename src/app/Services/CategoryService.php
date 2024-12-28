<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories($perPage = 10)
    {
        return $this->categoryRepository->getAllCategories($perPage);
    }

    public function createCategory($validatedData)
    {
        return $this->categoryRepository->createCategory($validatedData);
    }

    public function getCategoryById($id)
    {    
        try {
            return $this->categoryRepository->getCategoryById($id);
            } catch (\Exception $e) {
            return response()->json([
                'message' => 'Categoría no existe en la base de datos.',
                'error' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function updateCategory($id, $validatedData)
    {
       try {
         $category = $this->categoryRepository->getCategoryById($id);
         return $this->categoryRepository->updateCategory($category, $validatedData);
       } catch (\Exception $e) {
            return response()->json(['message' => 'Categoria no existe en la base de datos.',], Response::HTTP_NOT_FOUND);
       } 
    }

    public function deleteCategory($id)
    {
        $category = $this->getCategoryById($id);
      try {
            
            if ($category->products()->count() > 0) {
                return response()->json([
                    'message' => 'No se puede eliminar la categoría porque tiene productos asociados.',
                    'error_code' => Response::HTTP_BAD_REQUEST
                ], 400);
            }

           return  $this->categoryRepository->deleteCategory($category);
          
          } catch (\Exception $e) {
            return response()->json([
                'message' => "Categoría no encontrada",
                'error_code' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }  
    }
}
