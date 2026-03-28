<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, watch } from 'vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
    AlertDialog, AlertDialogAction, AlertDialogCancel,
    AlertDialogContent, AlertDialogDescription,
    AlertDialogFooter, AlertDialogHeader, AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Pencil, Trash2, Plus, Building2, Users } from 'lucide-vue-next'
import {
    useVueTable,
    getCoreRowModel,
} from '@tanstack/vue-table'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    rooms: Object,
    filters: Object, 
})

/* ================= DELETE ================= */
const openDelete = ref(false)
const targetId   = ref(null)

const form = useForm({})

function confirmDelete(id) {
    targetId.value   = id
    openDelete.value = true
}

function destroy() {
    form.transform(() => ({ _method: 'DELETE' }))
        .post(`/admins/rooms/${targetId.value}`, {
            onSuccess: () => { openDelete.value = false }
        })
}

/* ================= STATE ================= */
const search = ref(props.filters?.search || '')
const sorting = ref(props.filters?.sort || 'id')
const direction = ref(props.filters?.direction || 'asc')

/* ================= FETCH ================= */
function fetchData(extra = {}) {
    router.get('/admins/rooms', {
        search: search.value,
        sort: sorting.value,
        direction: direction.value,
        ...extra
    }, {
        preserveState: true,
        replace: true
    })
}

/* ================= SEARCH ================= */
watch(search, (val) => {
    fetchData({ page: 1 })
})

/* ================= SORT ================= */
function changeSort(column) {
    if (sorting.value === column) {
        direction.value = direction.value === 'asc' ? 'desc' : 'asc'
    } else {
        sorting.value = column
        direction.value = 'asc'
    }
    fetchData()
}

/* ================= TABLE ================= */
const table = useVueTable({
    data: props.rooms.data,
    columns: [
        {
            accessorKey: 'number',
            header: () => 'Room',
        },
        {
            accessorKey: 'floor.name',
            header: () => 'Floor',
        },
        {
            accessorKey: 'capacity',
            header: () => 'Capacity',
        },
        {
            accessorKey: 'price',
            header: () => 'Price',
        },
    ],
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    pageCount: props.rooms.last_page,
})
</script>

<template>
<div>
    <!-- HEADER -->
    <div class="mb-6 flex items-end justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manage Rooms</h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ props.rooms.total }} rooms total
            </p>
        </div>

        <Link href="/admins/rooms/create">
            <Button>
                <Plus class="w-4 h-4 mr-2" /> Add Room
            </Button>
        </Link>
    </div>

    <!-- SEARCH -->
    <div class="mb-4">
        <input
            v-model="search"
            placeholder="Search by id or number..."
            class="border px-3 py-2 rounded w-64"
        />
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b">

                    <th @click="changeSort('number')" class="cursor-pointer px-5 py-3">
                        Room
                    </th>

                    <th @click="changeSort('floor')" class="cursor-pointer px-5 py-3">
                        Floor
                    </th>

                    <th @click="changeSort('capacity')" class="cursor-pointer px-5 py-3">
                        Capacity
                    </th>

                    <th @click="changeSort('price')" class="cursor-pointer px-5 py-3">
                        Price
                    </th>

                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for="room in props.rooms.data"
                    :key="room.id"
                    class="border-b hover:bg-slate-50"
                >
                    <td class="px-5 py-4 font-bold">#{{ room.number }}</td>

                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5">
                            <Building2 class="w-3.5 h-3.5" />
                            {{ room.floor.name }}
                        </div>
                    </td>

                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5">
                            <Users class="w-3.5 h-3.5" />
                            {{ room.capacity }} guests
                        </div>
                    </td>

                    <td class="px-5 py-4 font-semibold">
                        ${{ (room.price / 100).toFixed(2) }}
                    </td>

                    <td class="px-5 py-4">
                        <Badge class="bg-emerald-50 text-emerald-700">
                            Available
                        </Badge>
                    </td>

                    <td class="px-5 py-4">
                        <div class="flex gap-2 justify-end">
                            <Link :href="`/admins/rooms/${room.id}/edit`">
                                <Button variant="outline" size="icon">
                                    <Pencil class="w-3.5 h-3.5" />
                                </Button>
                            </Link>

                            <Button
                                variant="outline"
                                class="text-red-500"
                                @click="confirmDelete(room.id)"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                            </Button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="flex justify-between mt-4">
        <Button
            :disabled="!props.rooms.prev_page_url"
            @click="fetchData({ page: props.rooms.current_page - 1 })"
        >
            Prev
        </Button>

        <span>
            Page {{ props.rooms.current_page }} of {{ props.rooms.last_page }}
        </span>

        <Button
            :disabled="!props.rooms.next_page_url"
            @click="fetchData({ page: props.rooms.current_page + 1 })"
        >
            Next
        </Button>
    </div>

    <!-- DELETE MODAL -->
    <AlertDialog v-model:open="openDelete">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete Room</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure? This action cannot be undone.
                </AlertDialogDescription>
            </AlertDialogHeader>

            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction
                    class="bg-red-500"
                    @click="destroy"
                >
                    Delete
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</div>
</template>
