// File: resources/js/pages/admin/users/Show.tsx
// Comments in English only
import React, { useMemo } from 'react'
import { Head, Link, router, usePage } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout'
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs'
import { Separator } from '@/components/ui/separator'
import type { BreadcrumbItem } from '@/types'
import { Mail, Copy, Edit3, ShieldOff, Phone, Calendar, User as UserIcon, ArrowLeft } from 'lucide-react'

type RawUser = {
    id: number
    name?: string | null
    email?: string | null
    phone?: string | null
    status?: 'active' | 'inactive' | string | null
    roles?: string[] | null
    created_at?: unknown
    updated_at?: unknown
}

type PageProps =
    | { user: RawUser }            // flat object
    | { user: { data: RawUser } }  // resource-wrapped
    | { data: RawUser }            // whole page is a resource

function extractUser(props: any): RawUser | null {
    if (!props) return null
    if (props.user?.data) return props.user.data
    if (props.user) return props.user
    if (props.data) return props.data
    return null
}

function asString(value: unknown, fallback = '—'): string {
    if (value === null || value === undefined) return fallback
    if (typeof value === 'string') return value.length ? value : fallback
    if (typeof value === 'number' || typeof value === 'boolean') return String(value)
    if (Array.isArray(value)) return value.length ? value.join(', ') : fallback
    try {
        return JSON.stringify(value)
    } catch {
        return fallback
    }
}

function fmtDate(value: unknown, fallback = '—'): string {
    if (typeof value === 'string') {
        const d = new Date(value)
        return isNaN(d.getTime()) ? asString(value, fallback) : d.toLocaleString()
    }
    return asString(value, fallback)
}

function initials(name: string): string {
    return name
        .split(' ')
        .filter(Boolean)
        .map((p) => p[0]?.toUpperCase() ?? '')
        .slice(0, 2)
        .join('') || 'U'
}

export default function Show() {
    const { props } = usePage<PageProps>()
    const user = extractUser(props)

    const name = asString(user?.name)
    const email = asString(user?.email)
    const phone = asString(user?.phone)
    const status = asString(user?.status)
    const rolesArray = Array.isArray(user?.roles) ? user!.roles! : []
    const roles = rolesArray.length ? rolesArray : []

    const createdAt = fmtDate(user?.created_at)
    const updatedAt = fmtDate(user?.updated_at)

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Users', href: '/admin/users' },
        { title: name || 'User', href: `/admin/users/${user?.id ?? ''}` },
    ]

    function goBack() {
        router.visit(route('admin.users.index'))
    }

    function onCopyEmail() {
        if (!email || email === '—') return
        navigator.clipboard?.writeText(email).catch(() => {})
    }

    function deactivate() {
        if (!user) return
        if (!confirm('Deactivate this user?')) return
        router.delete(route('admin.users.destroy', { user: user.id }), {
            preserveScroll: true,
            onSuccess: () => router.visit(route('admin.users.index')),
        })
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={name || 'User'} />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Page header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <Button variant="outline" onClick={goBack} className="gap-2">
                            <ArrowLeft className="h-4 w-4" />
                            Back
                        </Button>
                        <h1 className="text-2xl font-semibold">User</h1>
                    </div>

                    <div className="flex items-center gap-2">
                        {user && (
                            <Link href={route('admin.users.edit', { user: user.id })}>
                                <Button variant="outline" className="gap-2">
                                    <Edit3 className="h-4 w-4" />
                                    Edit
                                </Button>
                            </Link>
                        )}
                        {user && (
                            <Button variant="destructive" onClick={deactivate} className="gap-2">
                                <ShieldOff className="h-4 w-4" />
                                Deactivate
                            </Button>
                        )}
                    </div>
                </div>

                {/* Hero card */}
                <Card>
                    <CardContent className="p-6">
                        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                            <div className="flex items-center gap-4">
                                <div className="flex h-14 w-14 items-center justify-center rounded-full border">
                                    <UserIcon className="hidden h-6 w-6 opacity-60 sm:block" />
                                    <span className="sm:hidden text-lg font-semibold">{initials(name)}</span>
                                </div>
                                <div>
                                    <div className="flex items-center gap-3 flex-wrap">
                                        <h2 className="text-xl font-semibold">{name}</h2>
                                        <Badge variant="secondary" className="capitalize">
                                            {status}
                                        </Badge>
                                        {roles.map((r) => (
                                            <Badge key={r} variant="outline" className="capitalize">
                                                {r.replaceAll('_', ' ')}
                                            </Badge>
                                        ))}
                                    </div>
                                    <div className="mt-1 text-sm text-muted-foreground break-all">{email}</div>
                                </div>
                            </div>

                            <div className="flex flex-wrap gap-2">
                                {email && email !== '—' && (
                                    <>
                                        <a href={`mailto:${email}`}>
                                            <Button variant="outline" className="gap-2">
                                                <Mail className="h-4 w-4" />
                                                Email
                                            </Button>
                                        </a>
                                        <Button variant="outline" onClick={onCopyEmail} className="gap-2">
                                            <Copy className="h-4 w-4" />
                                            Copy email
                                        </Button>
                                    </>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Content tabs */}
                <Tabs defaultValue="profile" className="w-full">
                    <TabsList className="w-full justify-start">
                        <TabsTrigger value="profile">Profile</TabsTrigger>
                        <TabsTrigger value="activity">Activity</TabsTrigger>
                    </TabsList>

                    {/* Profile tab */}
                    <TabsContent value="profile" className="mt-4">
                        <div className="grid gap-4 md:grid-cols-3">
                            {/* Left: primary facts */}
                            <Card className="md:col-span-2">
                                <CardHeader>
                                    <CardTitle>Details</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <div>
                                            <div className="text-sm text-muted-foreground">Name</div>
                                            <div className="text-base">{name}</div>
                                        </div>
                                        <div>
                                            <div className="text-sm text-muted-foreground">Email</div>
                                            <div className="text-base break-all">{email}</div>
                                        </div>
                                        <div>
                                            <div className="text-sm text-muted-foreground">Phone</div>
                                            <div className="text-base">{phone}</div>
                                        </div>
                                        <div>
                                            <div className="text-sm text-muted-foreground">Status</div>
                                            <div className="text-base capitalize">{status}</div>
                                        </div>
                                    </div>

                                    <Separator />

                                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <div className="flex items-center gap-2">
                                            <Calendar className="h-4 w-4 opacity-60" />
                                            <div>
                                                <div className="text-sm text-muted-foreground">Created at</div>
                                                <div className="text-base">{createdAt}</div>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <Calendar className="h-4 w-4 opacity-60" />
                                            <div>
                                                <div className="text-sm text-muted-foreground">Updated at</div>
                                                <div className="text-base">{updatedAt}</div>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Right: quick meta */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Summary</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-3">
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm text-muted-foreground">User ID</span>
                                            <span className="text-sm font-medium">{asString(user?.id)}</span>
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm text-muted-foreground">Roles</span>
                                            <span className="text-sm font-medium">
                        {roles.length ? roles.join(', ') : '—'}
                      </span>
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm text-muted-foreground">Phone</span>
                                            <span className="text-sm font-medium">{phone}</span>
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm text-muted-foreground">Status</span>
                                            <span className="text-sm font-medium capitalize">{status}</span>
                                        </div>
                                    </div>

                                    <Separator className="my-4" />

                                    <div className="flex flex-col gap-2">
                                        <Link href={route('admin.users.edit', { user: user?.id })}>
                                            <Button variant="outline" className="w-full gap-2">
                                                <Edit3 className="h-4 w-4" />
                                                Edit user
                                            </Button>
                                        </Link>
                                        <Button variant="destructive" className="w-full gap-2" onClick={deactivate}>
                                            <ShieldOff className="h-4 w-4" />
                                            Deactivate
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </TabsContent>

                    {/* Activity tab (placeholder to plug audit/events later) */}
                    <TabsContent value="activity" className="mt-4">
                        <Card>
                            <div className="relative overflow-hidden rounded-xl">
                                <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                                <div className="relative p-6">
                                    <h3 className="text-lg font-semibold mb-1">Activity</h3>
                                    <p className="text-sm text-muted-foreground mb-4">
                                        Recent actions and events related to this user (audit log, flags, etc.).
                                    </p>
                                    <Separator className="mb-4" />
                                    <div className="text-sm text-muted-foreground">
                                        No activity yet.
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </TabsContent>
                </Tabs>
            </div>
        </AppLayout>
    )
}
