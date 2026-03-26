<script setup>
import { usePage } from '@inertiajs/vue3'
import { watch, ref } from 'vue'
import { CheckCircle2, XCircle, X } from 'lucide-vue-next'

const page = usePage()
const show = ref(false)
const message = ref('')
const type = ref('success')

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            message.value = flash.success
            type.value = 'success'
            show.value = true
            setTimeout(() => { show.value = false }, 6000)
        }
        else if (flash?.error) {
            message.value = flash.error
            type.value = 'error'
            show.value = true
            setTimeout(() => { show.value = false }, 6000)
        }
    },
    { immediate: true, deep: true }
)
</script>

<template>
    <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-4"
        enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-4">

        <div v-if="show"
            class="fixed top-6 right-6 z-[100] flex items-center gap-3 px-5 py-4 rounded-xl shadow-lg border text-sm font-medium min-w-[280px] max-w-md"
            :class="type === 'success'
                ? 'bg-white border-emerald-200 text-emerald-700'
                : 'bg-white border-red-200 text-red-700'">

            <CheckCircle2 v-if="type === 'success'" class="w-5 h-5 text-emerald-500 shrink-0" />
            <XCircle v-else class="w-5 h-5 text-red-500 shrink-0" />

            <span>{{ message }}</span>

            <button @click="show = false" class="ml-auto opacity-50 hover:opacity-100 transition-opacity">
                <X class="w-4 h-4" />
            </button>
        </div>
    </Transition>
</template>
