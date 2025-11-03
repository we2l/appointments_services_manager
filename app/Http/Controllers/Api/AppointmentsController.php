<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexAppointmentsRequest;
use App\Http\Requests\StoreAppointmentsRequest;
use App\Http\Requests\UpdateAppointmentsRequest;
use App\Http\Resources\AppointmentsResource;
use App\Services\Contracts\AppointmentsServiceInterface;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Appointments",
    description: "Endpoints para gerenciamento de Agendamentos"
)]
#[OA\SecurityRequirement(
    name: "bearerAuth"
)]
class AppointmentsController extends Controller
{
    public function __construct(
        private AppointmentsServiceInterface $appointmentsService
    ) {}

    #[OA\Get(
        path: '/api/appointments',
        operationId: 'listAppointments',
        summary: 'Lista todas as consultas (paginado)',
        tags: ['Appointments'],
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
                description: 'Lista paginada de consultas',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/AppointmentsResource')
                        ),
                        new OA\Property(property: 'links', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object')
                    ]
                )
            )
        ]
    )]
    public function index(IndexAppointmentsRequest $request)
    {
        return AppointmentsResource::collection($this->appointmentsService->listAllAppointments($request->all()));
    }

    #[OA\Get(
        path: '/api/appointments/{id}',
        operationId: 'getAppointmentById',
        summary: 'Busca uma consulta pelo ID',
        tags: ['Appointments'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do agendamento a ser buscado',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Agendamento encontrado',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/AppointmentsResource')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Consulta não encontrado'
            )
        ]
    )]
    public function show(int $idAppointment)
    {
        return new AppointmentsResource($this->appointmentsService->listAppointmentById($idAppointment));
    }

    #[OA\Post(
        path: '/api/appointments',
        operationId: 'createAppointment',
        summary: 'Cria uma nova consulta',
        tags: ['Appointments'],
        requestBody: new OA\RequestBody(
            description: 'Dados da consulta a ser criado',
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/StoreAppointmentsRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Consulta criado com sucesso',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/AppointmentsResource')
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Erro de validação'
            )
        ]
    )]
    public function store(StoreAppointmentsRequest $request)
    {
        $appointment = $this->appointmentsService->createAppointment($request->all());
        return (new AppointmentsResource($appointment))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Put(
        path: '/api/appointments/{id}',
        operationId: 'updateAppointment',
        summary: 'Atualiza uma consulta existente',
        tags: ['Appointments'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID da consulta a ser atualizado',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Dados da consulta a ser atualizado',
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/UpdateAppointmentsRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'consulta atualizada com sucesso',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/AppointmentsResource')
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'consulta não encontrada'),
            new OA\Response(response: 422, description: 'Erro de validação')
        ]
    )]
    public function update(UpdateAppointmentsRequest $request, int $idAppointment)
    {
        $appointment = $this->appointmentsService->updateAppointment($request->all(), $idAppointment);
        return new AppointmentsResource($appointment);
    }

    #[OA\Patch(
        path: '/api/appointments/cancel/{id}',
        operationId: 'cancelAppointment',
        summary: 'Cancela uma consulta',
        tags: ['Appointments'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID da consulta a ser cancelada',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Consulta cancelado com sucesso'
            ),
            new OA\Response(response: 404, description: 'consulta não encontrada')
        ]
    )]
    public function cancel(int $idAppointment)
    {
        $this->appointmentsService->cancelAppointment($idAppointment);
        return response()->noContent();
    }
}
