// File: resources/js/pages/admin/users/Edit.tsx
// Comments in English only
import React, { useMemo, useState } from 'react'
import { Head, Link, router, useForm, usePage } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout'
import type { BreadcrumbItem } from '@/types'
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Separator } from '@/components/ui/separator'
import { Eye, EyeOff, Save, ArrowLeft } from 'lucide-react'

type RawUser = {
    id: number
    name: string
    email: string
    phone?: string | null
    status: 'active' | 'inactive'
    roles?: string[] | null
}

type PageProps = {
    user: RawUser | { data: RawUser } // allow resource-wrapped
    roles?: string[]                   // available roles from backend
}

type UserPayload = {
    name: string
    email: string
    password: string | null // null / empty string -> do not change
    role: string | null     // single primary role (or null)
    status: 'active' | 'inactive'
}

function unwrapUser(u: PageProps['user']): RawUser {
    // Accept both plain object and { data: ... }
    if ((u as any)?.data) return (u as any).data as RawUser
    return u as RawUser
}

export default function Edit() {
    const { props } = usePage<PageProps>()
    const raw = unwrapUser(props.user)

    // if backend returns multiple roles, pick first as "primary" for this form
    const initialRole: string | null =
        Array.isArray(raw.roles) && raw.roles.length > 0 ? raw.roles[0] : null

    const form = useForm<UserPayload>({
        name: raw.name ?? '',
        email: raw.email ?? '',
        password: '',
        role: initialRole,
        status: (raw.status as 'active' | 'inactive') ?? 'active',
    })

    const roles = useMemo(() => (props.roles?.length ? props.roles : ['admin', 'user']), [props.roles])
    const [showPwd, setShowPwd] = useState(false)

    function submit(e?: React.FormEvent) {
        e?.preventDefault()
        form.clearErrors()

        // One-off transform:
        // - role: "none" -> null
        // - password: empty string -> remove (so backend won't try to set empty password)
        form.transform((data) => {
            const transformed: any = {
                ...data,
                role: (data.role as any) === 'none' ? null : data.role,
            }
            if (!data.password) {
                // remove the password field entirely to avoid validation/overwrite
                delete transformed.password
            }
            return transformed
        })

        form.patch(route('admin.users.update', { user: raw.id }), {
            preserveScroll: true,
            onSuccess: () => router.visit(route('admin.users.index')),
            onFinish: () => {
                // reset transform to identity
                form.transform((d) => d)
            },
        })
    }

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Users', href: '/admin/users' },
        { title: raw.name || `#${raw.id}`, href: route('admin.users.show', { user: raw.id }) },
        { title: 'Edit', href: route('admin.users.edit', { user: raw.id }) },
    ]

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit: ${raw.name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <Link href={route('admin.users.index')}>
                            <Button variant="outline" className="gap-2">
                                <ArrowLeft className="h-4 w-4" />
                                Back to list
                            </Button>
                        </Link>
                        <h1 className="text-2xl font-semibold">Edit user</h1>
                    </div>

                    <Button onClick={() => submit()} className="gap-2" disabled={form.processing}>
                        <Save className="h-4 w-4" />
                        {form.processing ? 'Saving…' : 'Save changes'}
                    </Button>
                </div>

                {/* Form */}
                <form onSubmit={submit}>
                    <Card>
                        <CardHeader>
                            <CardTitle>Basic information</CardTitle>
                        </CardHeader>

                        <CardContent className="space-y-6">
                            <div className="grid gap-6 md:grid-cols-2">
                                {/* Name */}
                                <div className="space-y-2">
                                    <Label htmlFor="name">Name</Label>
                                    <Input
                                        id="name"
                                        value={form.data.name}
                                        onChange={(e) => form.setData('name', e.target.value)}
                                        placeholder="Jane Doe"
                                        autoFocus
                                    />
                                    {form.errors.name && <p className="text-sm text-red-600">{form.errors.name}</p>}
                                </div>

                                {/* Email */}
                                <div className="space-y-2">
                                    <Label htmlFor="email">Email</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        value={form.data.email}
                                        onChange={(e) => form.setData('email', e.target.value)}
                                        placeholder="jane@example.com"
                                    />
                                    {form.errors.email && <p className="text-sm text-red-600">{form.errors.email}</p>}
                                </div>

                                {/* Password (optional) */}
                                <div className="space-y-2">
                                    <Label htmlFor="password">Password (leave blank to keep)</Label>
                                    <div className="relative">
                                        <Input
                                            id="password"
                                            type={showPwd ? 'text' : 'password'}
                                            value={form.data.password ?? ''}
                                            onChange={(e) => form.setData('password', e.target.value)}
                                            placeholder="New password"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowPwd((v) => !v)}
                                            className="absolute inset-y-0 right-2 flex items-center text-muted-foreground"
                                            aria-label={showPwd ? 'Hide password' : 'Show password'}
                                            tabIndex={-1}
                                        >
                                            {showPwd ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                                        </button>
                                    </div>
                                    {form.errors.password && (
                                        <p className="text-sm text-red-600">{form.errors.password}</p>
                                    )}
                                </div>

                                {/* Role (single) */}
                                <div className="space-y-2">
                                    <Label>Role</Label>
                                    <Select
                                        // Radix cannot use empty string; use 'none' sentinel
                                        value={form.data.role ?? 'none'}
                                        onValueChange={(v) => form.setData('role', v)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select role" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="none">No role</SelectItem>
                                            {roles.map((r) => (
                                                <SelectItem key={r} value={r}>
                                                    {r}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {form.errors.role && <p className="text-sm text-red-600">{form.errors.role}</p>}
                                </div>

                                {/* Status */}
                                <div className="space-y-2">
                                    <Label>Status</Label>
                                    <Select
                                        value={form.data.status}
                                        onValueChange={(v) =>
                                            form.setData('status', v as UserPayload['status'])
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="active">Active</SelectItem>
                                            <SelectItem value="inactive">Inactive</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {form.errors.status && (
                                        <p className="text-sm text-red-600">{form.errors.status}</p>
                                    )}
                                </div>
                            </div>

                            <Separator />

                            <div className="text-xs text-muted-foreground">
                                Leaving the password empty will keep the current one unchanged.
                            </div>
                        </CardContent>

                        <CardFooter className="flex items-center justify-end gap-2">
                            <Link href={route('admin.users.index')}>
                                <Button type="button" variant="outline">
                                    Cancel
                                </Button>
                            </Link>
                            <Button type="submit" disabled={form.processing}>
                                {form.processing ? 'Saving…' : 'Save changes'}
                            </Button>
                        </CardFooter>
                    </Card>
                </form>
            </div>
        </AppLayout>
    )
}
