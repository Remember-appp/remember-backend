// File: resources/js/pages/admin/users/Index.tsx
// Comments in English only
import React, { useEffect, useMemo, useState } from 'react'
import { Head, Link, router, usePage } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout'
import type { BreadcrumbItem } from '@/types'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { ChevronLeft, ChevronRight, ChevronUp, ChevronDown, Filter, Plus } from 'lucide-react'

type UserDto = {
    id: number
    name: string
    email: string
    // roles come as an array from backend
    roles?: string[] | null
    status?: 'active' | 'inactive' | null
    created_at?: string | null
}

type PageMeta = {
    current_page: number
    last_page: number
    per_page: number
    total: number
}

type Filters = {
    q: string
    status: string
    role: string
    per_page: number
    sort: 'id' | 'created_at' | 'name' | 'email'
    dir: 'asc' | 'desc'
}

type PageProps = {
    items: UserDto[]
    meta: PageMeta
    filters: Filters
    roles: string[]
}

const headers: Array<{ key: Filters['sort']; label: string }> = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'created_at', label: 'Created' },
]

// helper to format ISO date safely
function fmtDate(value?: string | null): string {
    if (!value) return '—'
    const d = new Date(value)
    return isNaN(d.getTime()) ? value : d.toLocaleString()
}

export default function Index() {
    const { props } = usePage<PageProps>()
    const initial = props.filters

    const [q, setQ] = useState(initial?.q ?? '')
    // Radix Select cannot use empty string: use sentinel "any"
    const [status, setStatus] = useState<string>(initial?.status ? initial.status : 'any')
    const [role, setRole] = useState<string>(initial?.role ? initial.role : 'any')
    const [sort, setSort] = useState<Filters['sort']>(initial?.sort ?? 'created_at')
    const [dir, setDir] = useState<Filters['dir']>(initial?.dir ?? 'desc')
    const [perPage, setPerPage] = useState<number>(initial?.per_page ?? 20)

    const items = props.items || []
    const meta = props.meta
    const roles = useMemo(() => props.roles ?? [], [props.roles])

    // Debounced server reload on filters change
    useEffect(() => {
        const t = setTimeout(() => {
            router.get(
                route('admin.users.index'),
                {
                    q,
                    status: status === 'any' ? '' : status,
                    role: role === 'any' ? '' : role,
                    per_page: perPage,
                    sort,
                    dir,
                    page: 1,
                },
                { preserveState: true, replace: true, only: ['items', 'meta', 'filters'] }
            )
        }, 250)
        return () => clearTimeout(t)
    }, [q, status, role, perPage, sort, dir])

    const canPrev = (meta?.current_page ?? 1) > 1
    const canNext = (meta?.current_page ?? 1) < (meta?.last_page ?? 1)

    function goPage(page: number) {
        router.get(
            route('admin.users.index'),
            {
                q,
                status: status === 'any' ? '' : status,
                role: role === 'any' ? '' : role,
                per_page: perPage,
                sort,
                dir,
                page,
            },
            { preserveState: true, replace: true, only: ['items', 'meta', 'filters'] }
        )
    }

    function deactivate(id: number) {
        if (!confirm('Deactivate this user?')) return
        router.delete(route('admin.users.destroy', { user: id }), {
            preserveScroll: true,
            onSuccess: () => router.reload({ only: ['items', 'meta'] }),
        })
    }

    function toggleSort(key: Filters['sort']) {
        if (sort === key) setDir((d) => (d === 'asc' ? 'desc' : 'asc'))
        else {
            setSort(key)
            setDir('asc')
        }
    }

    const breadcrumbs: BreadcrumbItem[] = [{ title: 'Users', href: '/admin/users' }]

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Users" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Toolbar */}
                <Card>
                    <CardContent className="p-4">
                        <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div className="flex items-center gap-2">
                                <Filter className="h-4 w-4 opacity-60" />
                                <div className="text-sm font-medium">Filters</div>
                            </div>
                            <div className="flex items-center gap-2">
                                <Link href={route('admin.users.create')}>
                                    <Button className="gap-2">
                                        <Plus className="h-4 w-4" />
                                        Create
                                    </Button>
                                </Link>
                            </div>
                        </div>

                        <Separator className="my-4" />

                        <div className="grid grid-cols-1 gap-3 md:grid-cols-5">
                            <Input
                                value={q}
                                onChange={(e) => setQ(e.target.value)}
                                placeholder="Search name or email…"
                            />

                            {/* Status */}
                            <Select value={status} onValueChange={(v) => setStatus(v)}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Status: Any" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="any">Any</SelectItem>
                                    <SelectItem value="active">Active</SelectItem>
                                    <SelectItem value="inactive">Inactive</SelectItem>
                                </SelectContent>
                            </Select>

                            {/* Role */}
                            <Select value={role} onValueChange={(v) => setRole(v)}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Role: Any" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="any">Any</SelectItem>
                                    {roles.map((r) => (
                                        <SelectItem key={r} value={r}>
                                            {r}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>

                            {/* Per page */}
                            <Select value={String(perPage)} onValueChange={(v) => setPerPage(Number(v))}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Per page" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="10">10</SelectItem>
                                    <SelectItem value="20">20</SelectItem>
                                    <SelectItem value="50">50</SelectItem>
                                </SelectContent>
                            </Select>

                            {/* Sort + direction */}
                            <div className="flex gap-2">
                                <Select value={sort} onValueChange={(v) => setSort(v as Filters['sort'])}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Sort by" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="created_at">Created</SelectItem>
                                        <SelectItem value="name">Name</SelectItem>
                                        <SelectItem value="email">Email</SelectItem>
                                    </SelectContent>
                                </Select>
                                <Select value={dir} onValueChange={(v) => setDir(v as Filters['dir'])}>
                                    <SelectTrigger className="w-24">
                                        <SelectValue placeholder="Dir" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="asc">Asc</SelectItem>
                                        <SelectItem value="desc">Desc</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Table */}
                <Card>
                    <CardContent className="p-0">
                        <div className="overflow-x-auto">
                            <table className="min-w-full text-sm">
                                <thead className="bg-muted/40">
                                <tr>
                                    {headers.map((h) => {
                                        const active = sort === h.key
                                        const DirIcon = active ? (dir === 'asc' ? ChevronUp : ChevronDown) : null
                                        return (
                                            <th key={h.key} className="text-left px-4 py-3 font-medium select-none">
                                                <button
                                                    onClick={() => toggleSort(h.key)}
                                                    className="inline-flex items-center gap-1 hover:underline"
                                                >
                                                    {h.label}
                                                    {DirIcon && <DirIcon className="h-4 w-4 opacity-60" />}
                                                </button>
                                            </th>
                                        )
                                    })}
                                    <th className="text-left px-4 py-3 font-medium">Role(s)</th>
                                    <th className="text-left px-4 py-3 font-medium">Status</th>
                                    <th className="text-right px-4 py-3 font-medium">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                {items.map((u) => {
                                    const rolesStr = (u.roles && u.roles.length) ? u.roles.join(', ') : '—'
                                    return (
                                        <tr key={u.id} className="border-t">
                                            <td className="px-4 py-3">{u.id}</td>
                                            <td className="px-4 py-3">{u.name}</td>
                                            <td className="px-4 py-3 break-all">{u.email}</td>
                                            {/* ✅ Created column is now rendered */}
                                            <td className="px-4 py-3">{fmtDate(u.created_at)}</td>
                                            <td className="px-4 py-3">
                                                {u.roles && u.roles.length ? (
                                                    <div className="flex flex-wrap gap-1">
                                                        {u.roles.map((r) => (
                                                            <Badge key={r} variant="outline" className="capitalize">
                                                                {r.replaceAll('_', ' ')}
                                                            </Badge>
                                                        ))}
                                                    </div>
                                                ) : (
                                                    '—'
                                                )}
                                            </td>
                                            <td className="px-4 py-3">
                                                <Badge
                                                    variant={u.status === 'active' ? 'secondary' : 'outline'}
                                                    className={u.status === 'active' ? 'bg-green-100 text-green-700' : 'text-foreground'}
                                                >
                                                    {u.status ?? '—'}
                                                </Badge>
                                            </td>
                                            <td className="px-4 py-3 text-right">
                                                <div className="inline-flex items-center gap-2">
                                                    <Link href={route('admin.users.show', { user: u.id })} className="underline">
                                                        View
                                                    </Link>
                                                    <Link href={route('admin.users.edit', { user: u.id })} className="underline">
                                                        Edit
                                                    </Link>
                                                    <button onClick={() => deactivate(u.id)} className="underline text-red-600">
                                                        Deactivate
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    )
                                })}

                                {items.length === 0 && (
                                    <tr>
                                        <td colSpan={7} className="px-4 py-12 text-center text-muted-foreground">
                                            No users found
                                        </td>
                                    </tr>
                                )}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

                {/* Pagination */}
                <div className="flex items-center justify-between">
                    <Button
                        variant="outline"
                        disabled={!canPrev}
                        onClick={() => goPage((meta?.current_page ?? 2) - 1)}
                        className="gap-2"
                    >
                        <ChevronLeft className="h-4 w-4" />
                        Prev
                    </Button>

                    <div className="text-sm">
                        Page {meta?.current_page ?? 1} of {meta?.last_page ?? 1} • Total {meta?.total ?? 0}
                    </div>

                    <Button
                        variant="outline"
                        disabled={!canNext}
                        onClick={() => goPage((meta?.current_page ?? 0) + 1)}
                        className="gap-2"
                    >
                        Next
                        <ChevronRight className="h-4 w-4" />
                    </Button>
                </div>
            </div>
        </AppLayout>
    )
}
