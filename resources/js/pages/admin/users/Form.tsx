// File: resources/js/pages/admin/users/Form.tsx
import React from 'react'

export type UserPayload = {
    name: string
    email: string
    password?: string | null
    role?: string | null
    status?: 'active' | 'inactive' | null
}

type Props = {
    mode: 'create' | 'edit'
    data: UserPayload
    setData: <K extends keyof UserPayload>(key: K, value: UserPayload[K]) => void
    processing?: boolean
    roles?: string[]
    onSubmit: () => void
}

export default function Form({ mode, data, setData, processing, roles = [], onSubmit }: Props) {
    const isCreate = mode === 'create'

    return (
        <form
            onSubmit={e => {
                e.preventDefault()
                onSubmit()
            }}
            className="space-y-4"
        >
            <div>
                <label className="block text-sm font-medium mb-1">Name</label>
                <input
                    value={data.name}
                    onChange={e => setData('name', e.target.value)}
                    type="text"
                    className="w-full border rounded-lg px-3 py-2"
                    required
                />
            </div>

            <div>
                <label className="block text-sm font-medium mb-1">Email</label>
                <input
                    value={data.email}
                    onChange={e => setData('email', e.target.value)}
                    type="email"
                    className="w-full border rounded-lg px-3 py-2"
                    required
                />
            </div>

            {isCreate ? (
                <div>
                    <label className="block text-sm font-medium mb-1">Password</label>
                    <input
                        value={data.password ?? ''}
                        onChange={e => setData('password', e.target.value)}
                        type="password"
                        className="w-full border rounded-lg px-3 py-2"
                        minLength={6}
                        required
                    />
                </div>
            ) : (
                <div>
                    <label className="block text-sm font-medium mb-1">New Password (optional)</label>
                    <input
                        value={data.password ?? ''}
                        onChange={e => setData('password', e.target.value)}
                        type="password"
                        className="w-full border rounded-lg px-3 py-2"
                        minLength={6}
                        placeholder="Leave blank to keep current"
                    />
                </div>
            )}

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label className="block text-sm font-medium mb-1">Role</label>
                    <select
                        value={data.role ?? ''}
                        onChange={e => setData('role', e.target.value || null)}
                        className="w-full border rounded-lg px-3 py-2"
                    >
                        <option value="">—</option>
                        {roles.map(r => (
                            <option key={r} value={r}>
                                {r}
                            </option>
                        ))}
                    </select>
                </div>
                <div>
                    <label className="block text-sm font-medium mb-1">Status</label>
                    <select
                        value={data.status ?? 'active'}
                        onChange={e => setData('status', e.target.value as 'active' | 'inactive')}
                        className="w-full border rounded-lg px-3 py-2"
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div className="flex items-center gap-3">
                <button
                    type="submit"
                    className="rounded-xl px-4 py-2 border bg-black text-white disabled:opacity-60"
                    disabled={processing}
                >
                    {isCreate ? 'Create' : 'Save changes'}
                </button>
                {processing && <span className="text-sm">Saving…</span>}
            </div>
        </form>
    )
}
