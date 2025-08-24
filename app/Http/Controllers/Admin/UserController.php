<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use App\Services\UserService;
use App\DTO\Admin\UserFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(private readonly UserService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filter = new UserFilter(
            q:       trim((string) $request->query('q', '')),
            status:  $request->query('status'),
            role:    $request->query('role'),
            perPage: (int) $request->query('per_page', 20),
            sort:    (string) $request->query('sort', 'created_at'),
            dir:     (string) $request->query('dir', 'desc'),
        );

        $page = $this->service->list($filter);
        return UserResource::collection($page);
    }

    public function show(User $user): UserResource
    {
        $user = $this->service->show($user->id);
        return UserResource::make($user);
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $user = $this->service->store($request);
        return UserResource::make($user)->response()->setStatusCode(201);
    }

    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        $user = $this->service->update($request, $user);
        return UserResource::make($user);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->service->deactivate($user);
        return response()->json(['message' => 'User deactivated']);
    }
}
