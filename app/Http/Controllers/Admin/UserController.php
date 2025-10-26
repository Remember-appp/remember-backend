<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use App\Services\UserService;
use App\DTO\Admin\UserFilter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(private readonly UserService $service) {}

    /**
     * Inertia list page + server filters/pagination.
     */
    public function index(Request $request): Response
    {
        $filter = new UserFilter(
            q:       trim((string) $request->query('q', '')),
            status:  $request->query('status'),
            role:    $request->query('role'),
            perPage: (int) $request->query('per_page', 20),
            sort:    (string) $request->query('sort', 'created_at'),
            dir:     (string) $request->query('dir', 'desc'),
        );

        $page = $this->service->list($filter); // returns LengthAwarePaginator

        // Normalize to resource/array for front-end
        $items = UserResource::collection($page->items())->resolve();

        $meta = [
            'current_page' => $page->currentPage(),
            'last_page'    => $page->lastPage(),
            'per_page'     => $page->perPage(),
            'total'        => $page->total(),
        ];

        return Inertia::render('admin/users/Index', [
            'items'   => $items,
            'meta'    => $meta,
            'filters' => [
                'q'        => $filter->q,
                'status'   => $filter->status,
                'role'     => $filter->role,
                'per_page' => $filter->perPage,
                'sort'     => $filter->sort,
                'dir'      => $filter->dir,
            ],
            'roles'   => ['admin','user'],
        ]);
    }

    public function show(User $user): Response
    {
        $user = $this->service->show($user->id);
        return Inertia::render('admin/users/Show', [
            'user' => UserResource::make($user),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/users/Create');
    }

    public function edit(User $user): Response
    {
        $user = $this->service->show($user->id);
        return Inertia::render('admin/users/Edit', [
            'user' => UserResource::make($user),
        ]);
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $user = $this->service->store($request);

        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'User created');
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $user = $this->service->update($request, $user);

        return redirect()
            ->route('admin.users.edit', $user->id)
            ->with('success', 'User updated');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->service->deactivate($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deactivated');
    }
}
