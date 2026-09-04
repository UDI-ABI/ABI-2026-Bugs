<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffContentVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para la consulta de valores de contenido por versión.
 *
 * Solo lectura: el historico es inmutable y no se edita ni se elimina
 * desde esta API (ver ProjectController/ProjectEvaluationController
 * para como se registran estos valores dentro del flujo real).
 */
class ContentVersionController extends Controller
{
    /**
     * Lista los valores diligenciados por contenido y versión
     *
     * Permite filtrar por version_id, content_id, project_id y búsqueda de texto.
     *
     * @param Request $request Parámetros de consulta
     * @return JsonResponse Listado paginado de content_versions
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validar y obtener parámetro de paginación
            $perPage = (int) $request->query('per_page', 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            // Construir query base con relaciones
            $query = ResearchStaffContentVersion::query()->with(['content', 'version.project']);

            // Aplicar filtros si existen
            if ($versionId = $request->query('version_id')) {
                $query->where('version_id', $versionId);
            }

            if ($contentId = $request->query('content_id')) {
                $query->where('content_id', $contentId);
            }

            if ($projectId = $request->query('project_id')) {
                $query->whereHas('version', function ($q) use ($projectId) {
                    $q->where('project_id', $projectId);
                });
            }

            // Búsqueda por valor
            if ($search = trim((string) $request->query('search', ''))) {
                $query->where('value', 'like', '%' . $search . '%');
            }

            // Obtener resultados paginados
            $contentVersions = $query
                ->orderByDesc('updated_at')
                ->paginate($perPage)
                ->withQueryString();

            return response()->json($contentVersions);

        } catch (\Exception $e) {
            Log::error('Error al listar versiones de contenido: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al obtener los registros.',
            ], 500);
        }
    }

    /**
     * Muestra el detalle de un registro contenido-versión
     *
     * @param ContentVersion $contentVersion Registro a mostrar
     * @return JsonResponse Detalle del registro
     */
    public function show(ResearchStaffContentVersion $contentVersion): JsonResponse
    {
        try {
            // Cargar relaciones
            $contentVersion->load(['content', 'version.project']);

            return response()->json($contentVersion);

        } catch (\Exception $e) {
            Log::error('Error al mostrar versión de contenido: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al obtener el registro.',
            ], 500);
        }
    }

}
