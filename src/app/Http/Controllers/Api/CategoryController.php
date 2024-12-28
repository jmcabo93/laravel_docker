<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
            $categories = $this->categoryService->getAllCategories();
            return response()->json($categories, Response::HTTP_OK);
    }

    public function store(CategoryRequest $request)
    {
            $category = $this->categoryService->createCategory($request->validated());
            return response()->json($category, Response::HTTP_CREATED);
    }

    public function show($id)
    {        
            $category = $this->categoryService->getCategoryById($id);
            return response()->json($category, Response::HTTP_OK);
    }

    public function update(CategoryRequest $request, $id)
    {       
            $category = $this->categoryService->updateCategory($id, $request->validated());
            return response()->json($category, Response::HTTP_OK);        
    }

    public function destroy($id)
    {

           return $this->categoryService->deleteCategory($id);
           
        
    }
}
