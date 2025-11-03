<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServicesRequest;
use App\Http\Requests\UpdateServicesRequest;
use App\Http\Resources\ServicesResource;
use App\Services\Contracts\ServicesServiceInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Services",
    description: "Endpoints para gerenciamento de Serviços"
)]
#[OA\SecurityRequirement(
    name: "bearerAuth"
)]
class ServicesController extends Controller
{
    public function __construct(
        private ServicesServiceInterface $servicesService
    )
    {}

    #[OA\Get(
        path: '/api/services',
        operationId: 'listServices',
        summary: 'Lista todos os serviços (paginado)',
        tags: ['Services'],
        parameters: [
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                required: false,
                description: 'Número de itens por página',
                schema: new OA\Schema(type: 'integer', default: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista paginada de serviços',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ServicesResource')
                        ),
                        new OA\Property(property: 'links', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object')
                    ]
                )
            )
        ]
    )]
    public function index(Request $request)
    {
        return ServicesResource::collection($this->servicesService->listAllServices($request->all()));
    }

    #[OA\Get(
        path: '/api/services/{id}',
        operationId: 'getServiceById',
        summary: 'Busca um serviço pelo ID',
        tags: ['Services'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do serviço a ser buscado',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Serviço encontrado',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/ServicesResource')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Serviço não encontrado'
            )
        ]
    )]
    public function show(int $id)
    {
        return new ServicesResource($this->servicesService->listServiceById($id));
    }

    #[OA\Post(
        path: '/api/services',
        operationId: 'createService',
        summary: 'Cria um novo serviço',
        tags: ['Services'],
        requestBody: new OA\RequestBody(
            description: 'Dados do serviço a ser criado',
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/StoreServicesRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Serviço criado com sucesso',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/ServicesResource')
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Erro de validação'
            )
        ]
    )]
    public function store(StoreServicesRequest $request)
    {
        $service = $this->servicesService->createService($request->all());
        return (new ServicesResource($service))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Put(
        path: '/api/services/{id}',
        operationId: 'updateService',
        summary: 'Atualiza um serviço existente',
        tags: ['Services'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do serviço a ser atualizado',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Dados do serviço a ser atualizado',
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/UpdateServicesRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Serviço atualizado com sucesso',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/ServicesResource')
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Serviço não encontrado'),
            new OA\Response(response: 422, description: 'Erro de validação')
        ]
    )]
    public function update(UpdateServicesRequest $request, int $idService)
    {
        return new ServicesResource($this->servicesService->updateService($request->all(), $idService));
    }

    #[OA\Delete(
        path: '/api/services/{id}',
        operationId: 'deleteService',
        summary: 'Exclui um serviço',
        tags: ['Services'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do serviço a ser excluído',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Serviço excluído com sucesso (Sem conteúdo)'
            ),
            new OA\Response(response: 404, description: 'Serviço não encontrado'),
            new OA\Response(response: 422, description: 'Serviço em uso, não pode ser excluído')
        ]
    )]
    public function delete(int $id)
    {
        $this->servicesService->deleteService($id);
        return response()->noContent();
    }
}

