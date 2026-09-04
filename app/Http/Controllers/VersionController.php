<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffContentVersion;
use App\Models\ResearchStaff\ResearchStaffVersion;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para la consulta de versiones de proyectos.
 *
 * Solo lectura: el historico de versiones es inmutable y no se
 * edita ni se elimina desde esta API (ver ProjectController para
 * la creacion de versiones dentro del flujo real de proyectos).
 */
class VersionController extends Controller
{
    /**
     * Lista las versiones registradas con filtros
     *
     * Permite filtrar por project_id y paginar resultados.
     *
     * @param Request $request Parámetros de consulta
     * @return JsonResponse Listado paginado de versiones
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validación de paginación
            $perPage = (int) $request->query('per_page', 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            // Query base
            $query = ResearchStaffVersion::query()->with('project');

            /**
             * FILTRO: ID de proyecto
             */
            if ($projectId = $request->query('project_id')) {
                $query->where('project_id', $projectId);
            }

            /**
             * FILTRO: Título de proyecto
             */
            if ($title = $request->query('title')) {
                $query->whereHas('project', function ($q) use ($title) {
                    $q->where('title', 'like', "%{$title}%");
                });
            }

            /**
             * FILTRO: Programa (project → professors → cityProgram → program_id)
             */
            if ($programId = $request->query('program_id')) {
                $query->whereHas('project.professors.cityProgram', function ($q) use ($programId) {
                    $q->where('program_id', $programId);
                });
            }

            // Orden + Paginación
            $versions = $query
                ->orderByDesc('created_at')
                ->paginate($perPage)
                ->withQueryString();

            return response()->json($versions);

        } catch (\Exception $e) {
            Log::error('Error al listar versiones: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al obtener las versiones.',
            ], 500);
        }
    }


    /**
     * Muestra una versión específica con sus contenidos
     *
     * @param Version $version Versión a mostrar
     * @return JsonResponse Detalle de la versión
     */
    public function show(ResearchStaffVersion $version): JsonResponse
    {
        try {
            // Load the related project so the frontend can display contextual info.
            $version->load('project');

            // Gather associated contents using the model bound to the research staff connection.
            $contents = ResearchStaffContentVersion::query()
                ->with('content')
                ->where('version_id', $version->id)
                ->get()
                ->sortBy(function (ResearchStaffContentVersion $contentVersion) {
                    return Str::lower($contentVersion->content?->name ?? '');
                })
                ->values()
                ->map(function (ResearchStaffContentVersion $contentVersion) {
                    return [
                        'id' => $contentVersion->content?->id,
                        'name' => $contentVersion->content?->name,
                        'description' => $contentVersion->content?->description,
                        'pivot' => [
                            'id' => $contentVersion->id,
                            'content_id' => $contentVersion->content_id,
                            'version_id' => $contentVersion->version_id,
                            'value' => $contentVersion->value,
                            'created_at' => $contentVersion->created_at?->toJSON(),
                            'updated_at' => $contentVersion->updated_at?->toJSON(),
                        ],
                    ];
                })
                ->toArray();

            $payload = $version->toArray();
            $payload['contents'] = $contents;

            return response()->json($payload);

        } catch (\Exception $e) {
            Log::error('Error al mostrar versión: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al obtener la versión.',
            ], 500);
        }
    }

}
