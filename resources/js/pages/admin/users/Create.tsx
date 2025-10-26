// File: resources/js/pages/admin/users/Create.tsx
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
import { Eye, EyeOff, Plus, ArrowLeft } from 'lucide-react'

type PageProps = { roles?: string[] }

type UserPayload = {
    name: string
    email: string
    password: string
    role: string | null // "null" means no role
    status: 'active' | 'inactive'
}

export default function Create() {
    const { props } = usePage<PageProps>()
    const roles = useMemo(() => (props.roles?.length ? props.roles : ['admin', 'user']), [props.roles])

    const form = useForm<UserPayload>({
        name: '',
        email: '',
        password: '',
        role: null,            // use "null" when no role
        status: 'active',
    })

    const [showPwd, setShowPwd] = useState(false)

    function submit(e?: React.FormEvent) {
        e?.preventDefault()
        form.clearErrors()

        // 1) apply a one-off transform (no chaining)
        form.transform((data) => ({
            ...data,
            role: (data.role as any) === 'none' ? null : data.role,
        }))

        // 2) then call post()
        form.post(route('admin.users.store'), {
            onSuccess: () => router.visit(route('admin.users.index')),
            onFinish: () => {
                // optional: reset transform so it doesn't persist to future submits
                form.transform((data) => data)
            },
            preserveScroll: true,
        })
    }

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Users', href: '/admin/users' },
        { title: 'Create', href: '/admin/users/create' },
    ]

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create user" />

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
                        <h1 className="text-2xl font-semibold">Create user</h1>
                    </div>

                    <Button onClick={() => submit()} className="gap-2" disabled={form.processing}>
                        <Plus className="h-4 w-4" />
                        {form.processing ? 'Creating…' : 'Create'}
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

                                {/* Password */}
                                <div className="space-y-2">
                                    <Label htmlFor="password">Password</Label>
                                    <div className="relative">
                                        <Input
                                            id="password"
                                            type={showPwd ? 'text' : 'password'}
                                            value={form.data.password}
                                            onChange={(e) => form.setData('password', e.target.value)}
                                            placeholder="Minimum 8 characters"
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

                                {/* Role */}
                                <div className="space-y-2">
                                    <Label>Role</Label>
                                    <Select
                                        value={form.data.role ?? 'none'}                // Radix cannot use empty string
                                        onValueChange={(v) => form.setData('role', v)}  // v is string; "none" is sentinel
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
                                        onValueChange={(v) => form.setData('status', v as UserPayload['status'])}
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
                                You can adjust user roles and status later on the edit page.
                            </div>
                        </CardContent>

                        <CardFooter className="flex items-center justify-end gap-2">
                            <Link href={route('admin.users.index')}>
                                <Button type="button" variant="outline">
                                    Cancel
                                </Button>
                            </Link>
                            <Button type="submit" disabled={form.processing}>
                                {form.processing ? 'Creating…' : 'Create user'}
                            </Button>
                        </CardFooter>
                    </Card>
                </form>
            </div>
        </AppLayout>
    )
}
