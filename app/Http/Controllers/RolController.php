<?php

namespace App\Http\Controllers;

use App\Services\RolService;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function __construct(
        private RolService $rolService
    ) {}

    // GET /roles
    public function getRoles(Request $request)
    {
        try {
            $filters = $request->only(['nombre']);

            // Llamar al método que obtiene todos los roles
            return response()->json(
                $this->rolService->getRoles($filters)
            );
        } catch (\Exception $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                $e->getCode() ?: 400
            );
        }
    }
}
