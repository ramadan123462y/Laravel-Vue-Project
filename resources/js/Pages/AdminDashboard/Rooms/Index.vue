<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
    AlertDialog, AlertDialogAction, AlertDialogCancel,
    AlertDialogContent, AlertDialogDescription,
    AlertDialogFooter, AlertDialogHeader, AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Pencil, Trash2, Plus, Building2, Users } from 'lucide-vue-next'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    rooms: Array,
})

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
</script>

<template>
    <div>
        <div class="mb-6 flex items-end justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Manage Rooms</h1>
                <p class="text-sm text-slate-500 mt-1">{{ rooms.length }} rooms total</p>
            </div>
            <Link href="/admins/rooms/create">
                <Button>
                    <Plus class="w-4 h-4 mr-2" /> Add Room
                </Button>
            </Link>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Room</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Floor</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Capacity</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Price</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="room in rooms"
                        :key="room.id"
                        class="border-b border-slate-100 hover:bg-slate-50 transition-colors"
                    >
                        <td class="px-5 py-4 font-bold text-slate-900">#{{ room.number }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1.5 text-slate-500">
                                <Building2 class="w-3.5 h-3.5" />
                                {{ room.floor.name }}
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1.5 text-slate-500">
                                <Users class="w-3.5 h-3.5" />
                                {{ room.capacity }} guests
                            </div>
                        </td>
                        <td class="px-5 py-4 font-semibold text-slate-700">
                            ${{ (room.price / 100).toFixed(2) }}
                        </td>
                        <td class="px-5 py-4">
                            <Badge class="bg-emerald-50 text-emerald-700 border-emerald-200">
                                Available
                            </Badge>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2 justify-end">
                                <Link :href="`/admins/rooms/${room.id}/edit`">
                                    <Button variant="outline" size="icon" class="h-8 w-8">
                                        <Pencil class="w-3.5 h-3.5" />
                                    </Button>
                                </Link>
                                <Button
                                    variant="outline" size="icon"
                                    class="h-8 w-8 text-red-500 hover:text-red-600 hover:border-red-300"
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
                        class="bg-red-500 hover:bg-red-600"
                        @click="destroy"
                    >
                        Delete
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>
