<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import { useVueTable, getCoreRowModel } from '@tanstack/vue-table'
import {
    Pencil, Trash2, CheckCircle,
    Plus, Search, ArrowUp, ArrowDown, ArrowUpDown, Users
} from 'lucide-vue-next'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    clients:   Object,
    countries: Array,
    filters:   Object,
    canCreate: Boolean,
    canDelete: Boolean,
    role:      String,
    pageTitle: String,
})

const isReceptionist = computed(() => props.role === 'receptionist')
const isApprovedPage = computed(() => !!props.pageTitle)
const title          = computed(() => props.pageTitle ?? 'Manage Clients')

// ── Filters ─────────────────────────────────────────────────
const search     = ref(props.filters?.search     ?? '')
const country_id = ref(props.filters?.country_id ?? '')
const gender     = ref(props.filters?.gender     ?? '')
const status     = ref(props.filters?.status     ?? '')
const sortBy     = ref(props.filters?.sort_by    ?? '')
const sortDir    = ref(props.filters?.sort_dir   ?? 'asc')

function applyFilters() {
    const routeName = isApprovedPage.value
        ? 'admins.clients.approved'
        : 'admins.clients.index'

    router.get(route(routeName), {
        search:     search.value     || undefined,
        country_id: country_id.value || undefined,
        gender:     gender.value     || undefined,
        status:     (!isReceptionist.value && !isApprovedPage.value)
                        ? (status.value || undefined)
                        : undefined,
        sort_by:    sortBy.value  || undefined,
        sort_dir:   sortDir.value || undefined,
    }, { preserveState: true, replace: true })
}

let searchTimeout
watch(search, () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(applyFilters, 400)
})
watch([country_id, gender, status], applyFilters)

function toggleSort(field) {
    if (sortBy.value === field) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value  = field
        sortDir.value = 'asc'
    }
    applyFilters()
}

// ── Country name lookup ──────────────────────────────────────
function countryName(id) {
    return props.countries.find(c => c.id === id)?.name ?? '—'
}

// ── TanStack columns ─────────────────────────────────────────
const columns = computed(() => {
    const cols = [
        { accessorKey: 'name',       header: 'Name',    enableSorting: true  },
        { accessorKey: 'email',      header: 'Email',   enableSorting: true  },
        { accessorKey: 'mobile',     header: 'Mobile',  enableSorting: false },
        { accessorKey: 'country_id', header: 'Country', enableSorting: false },
        { accessorKey: 'gender',     header: 'Gender',  enableSorting: true  },
    ]

    if (!isReceptionist.value && !isApprovedPage.value) {
        cols.push({ accessorKey: 'is_approved', header: 'Status', enableSorting: false })
    }

    if (!isApprovedPage.value) {
        cols.push({ accessorKey: 'actions', header: 'Actions', enableSorting: false })
    }

    return cols
})

const table = useVueTable({
    get data()    { return props.clients?.data ?? [] },
    get columns() { return columns.value },
    getCoreRowModel:  getCoreRowModel(),
    manualPagination: true,
    manualSorting:    true,
    manualFiltering:  true,
    pageCount: props.clients?.last_page ?? 1,
})

// ── Delete modal ─────────────────────────────────────────────
const showDeleteModal = ref(false)
const clientToDelete  = ref(null)

function confirmDelete(client) {
    clientToDelete.value = client
    showDeleteModal.value = true
}

function deleteClient() {
    router.delete(route('admins.clients.destroy', clientToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false
            clientToDelete.value  = null
        },
    })
}

function approveClient(client) {
    router.patch(route('admins.clients.approve', client.id))
}

function goToPage(url) {
    if (url) router.get(url, {
        search:     search.value     || undefined,
        country_id: country_id.value || undefined,
        gender:     gender.value     || undefined,
        status:     status.value     || undefined,
        sort_by:    sortBy.value     || undefined,
        sort_dir:   sortDir.value    || undefined,
    }, { preserveState: true })
}

// ── Avatar helpers ────────────────────────────────────────────
function initials(name) {
    return name?.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) ?? '??'
}
const colors = ['bg-blue-500','bg-green-500','bg-purple-500','bg-orange-500','bg-rose-500','bg-teal-500']
function avatarColor(name) {
    return colors[(name?.charCodeAt(0) ?? 0) % colors.length]
}
</script>

<template>
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">{{ title }}</h1>
                <p class="text-sm text-gray-400 mt-1">{{ clients.total }} total</p>
            </div>

            <div class="flex items-center gap-3">
                <!-- My Approved Clients button — receptionist on main index only -->
                <button
                    v-if="isReceptionist && !isApprovedPage"
                    @click="router.get(route('admins.clients.approved'))"
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors"
                >
                    <Users class="w-4 h-4" />
                    My Approved Clients
                </button>

                <!-- Back to Pending button — receptionist on approved page -->
                <button
                    v-if="isReceptionist && isApprovedPage"
                    @click="router.get(route('admins.clients.index'))"
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors"
                >
                    Pending Clients
                </button>

                <!-- Add Client button — admin + manager only -->
                <button
                    v-if="canCreate"
                    @click="router.get(route('admins.clients.create'))"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <Plus class="w-4 h-4" />
                    Add Client
                </button>
            </div>
        </div>

        <!-- Status tabs — admin + manager on main index only -->
        <div v-if="!isReceptionist && !isApprovedPage" class="flex gap-2">
            <button @click="status = ''"
                :class="['px-4 py-1.5 rounded-full text-sm font-medium transition-colors border',
                    status === ''
                        ? 'bg-gray-900 text-white border-gray-900'
                        : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50']">
                All
            </button>
            <button @click="status = 'approved'"
                :class="['px-4 py-1.5 rounded-full text-sm font-medium transition-colors border flex items-center gap-1.5',
                    status === 'approved'
                        ? 'bg-green-600 text-white border-green-600'
                        : 'bg-white text-green-700 border-green-200 hover:bg-green-50']">
                <CheckCircle class="w-3.5 h-3.5" />
                Approved
            </button>
            <button @click="status = 'pending'"
                :class="['px-4 py-1.5 rounded-full text-sm font-medium transition-colors border',
                    status === 'pending'
                        ? 'bg-yellow-500 text-white border-yellow-500'
                        : 'bg-white text-yellow-700 border-yellow-200 hover:bg-yellow-50']">
                Pending
            </button>
        </div>

        <!-- Table card -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">

            <!-- Filters -->
            <div class="flex flex-wrap gap-3 p-4 border-b border-gray-100">
                <div class="relative flex-1 min-w-[200px]">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search clients..."
                        class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400"
                    />
                </div>

                <!-- Country filter -->
                <select
                    v-model="country_id"
                    class="px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 text-gray-600"
                >
                    <option value="">All Countries</option>
                    <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>

                <!-- Gender filter -->
                <select
                    v-model="gender"
                    class="px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 text-gray-600"
                >
                    <option value="">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th
                                v-for="header in table.getFlatHeaders()"
                                :key="header.id"
                                class="text-left px-5 py-3"
                            >
                                <button
                                    v-if="header.column.columnDef.enableSorting"
                                    @click="toggleSort(header.column.id)"
                                    class="flex items-center gap-1.5 text-xs font-semibold text-gray-400 uppercase tracking-wide hover:text-gray-700 transition-colors group"
                                >
                                    {{ header.column.columnDef.header }}
                                    <ArrowUp
                                        v-if="sortBy === header.column.id && sortDir === 'asc'"
                                        class="w-3.5 h-3.5 text-gray-800"
                                    />
                                    <ArrowDown
                                        v-else-if="sortBy === header.column.id && sortDir === 'desc'"
                                        class="w-3.5 h-3.5 text-gray-800"
                                    />
                                    <ArrowUpDown v-else class="w-3.5 h-3.5 opacity-40 group-hover:opacity-70" />
                                </button>
                                <span
                                    v-else
                                    :class="[
                                        'text-xs font-semibold text-gray-400 uppercase tracking-wide',
                                        header.column.id === 'actions' ? 'flex justify-end' : ''
                                    ]"
                                >
                                    {{ header.column.columnDef.header }}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            class="hover:bg-gray-50 transition-colors"
                        >
                            <!-- Name + avatar -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div
                                        v-if="!row.original.avatar_image || row.original.avatar_image === 'default.jpg'"
                                        :class="['w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0', avatarColor(row.original.name)]"
                                    >
                                        {{ initials(row.original.name) }}
                                    </div>
                                    <img
                                        v-else
                                        :src="'/storage/' + row.original.avatar_image"
                                        class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                                    />
                                    <span class="font-medium text-gray-800 text-sm">{{ row.original.name }}</span>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-5 py-3.5 text-sm text-gray-500">{{ row.original.email }}</td>

                            <!-- Mobile -->
                            <td class="px-5 py-3.5 text-sm text-gray-500">{{ row.original.mobile ?? '—' }}</td>

                            <!-- Country — looked up from id -->
                            <td class="px-5 py-3.5 text-sm text-gray-500">
                                {{ countryName(row.original.country_id) }}
                            </td>

                            <!-- Gender -->
                            <td class="px-5 py-3.5 text-sm text-gray-500 capitalize">{{ row.original.gender }}</td>

                            <!-- Status — admin + manager main index only -->
                            <td v-if="!isReceptionist && !isApprovedPage" class="px-5 py-3.5">
                                <span
                                    :class="[
                                        'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium',
                                        row.original.is_approved
                                            ? 'bg-green-50 text-green-700'
                                            : 'bg-yellow-50 text-yellow-700'
                                    ]"
                                >
                                    <span
                                        :class="[
                                            'w-1.5 h-1.5 rounded-full',
                                            row.original.is_approved ? 'bg-green-500' : 'bg-yellow-500'
                                        ]"
                                    ></span>
                                    {{ row.original.is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </td>

                            <!-- Actions — hidden on approved page -->
                            <td v-if="!isApprovedPage" class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">

                                    <!-- Approve button -->
                                    <button
                                        v-if="!row.original.is_approved"
                                        @click="approveClient(row.original)"
                                        title="Approve client"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-green-600 hover:bg-green-50 transition-colors"
                                    >
                                        <CheckCircle class="w-4 h-4" />
                                    </button>

                                    <!-- Edit button -->
                                    <button
                                        v-if="canCreate"
                                        @click="router.get(route('admins.clients.edit', row.original.id))"
                                        title="Edit"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                                    >
                                        <Pencil class="w-4 h-4" />
                                    </button>

                                    <!-- Delete button -->
                                    <button
                                        v-if="canDelete"
                                        @click="confirmDelete(row.original)"
                                        title="Delete"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Empty state -->
                        <tr v-if="table.getRowModel().rows.length === 0">
                            <td
                                :colspan="columns.length"
                                class="text-center py-16 text-gray-400"
                            >
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-3xl">👤</span>
                                    <span class="text-sm">No clients found</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
                <p class="text-sm text-gray-400">
                    Showing {{ clients.from ?? 0 }}–{{ clients.to ?? 0 }} of {{ clients.total }}
                </p>
                <div class="flex gap-1">
                    <button
                        v-for="link in clients.links"
                        :key="link.label"
                        :disabled="!link.url"
                        @click="goToPage(link.url)"
                        v-html="link.label"
                        :class="[
                            'min-w-[34px] h-8 px-2 text-sm rounded-lg border transition-colors',
                            link.active
                                ? 'bg-gray-900 text-white border-gray-900'
                                : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50',
                            !link.url ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer',
                        ]"
                    />
                </div>
            </div>
        </div>

        <!-- Delete modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
        >
            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <Trash2 class="w-5 h-5 text-red-600" />
                    </div>
                    <h3 class="text-base font-semibold text-gray-800">Delete client</h3>
                </div>
                <p class="text-sm text-gray-500 mb-6">
                    Are you sure you want to delete
                    <strong class="text-gray-800">{{ clientToDelete?.name }}</strong>?
                    This cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteClient"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>