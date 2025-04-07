<?php

namespace App\Http\Controllers;

use App\Services\CategoriaService;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function __construct(
        private CategoriaService $categoriaService
    ) {}

    // GET /categorias
    public function getCategorias(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $size = $request->input('size', 10);
            $filters = $request->only(['nombre']);

            return response()->json(
                $this->categoriaService->getCategorias($page, $size, $filters)
            );
        } catch (\Exception $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                $e->getCode() ?: 400
            );
        }
    }

    // POST /categorias
    public function create(Request $request)
    {
        try {
            $categoria = $this->categoriaService->createCategoria($request->all());
            
            return response()->json([
                'message' => 'Categoría creada con éxito',
                'categoria' => $categoria
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }


    // PUT /categorias/{id}
    public function updateCategoria(Request $request, string $id)
    {
        try {
            $data = $request->all();
            $categoria = $this->categoriaService->updateCategoria($id, $data);
            return response()->json($categoria);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // PATCH /categorias/{id}/disable
    public function disableCategoria(string $id)
    {
        try {
            return response()->json(
                $this->categoriaService->disableCategoria($id)
            );
        } catch (\Exception $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                $e->getCode() ?: 400
            );
        }
    }
}