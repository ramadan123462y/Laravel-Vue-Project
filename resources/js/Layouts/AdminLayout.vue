<script setup>
import { SidebarProvider } from '@/components/ui/sidebar'
import AdminSidebar from './admin/AdminSidebar.vue'
import AdminTopbar from './admin/AdminTopbar.vue'
import FlashMessage  from '@/components/FlashMessage.vue'

</script>

<template>
    <SidebarProvider>
        <div class="flex min-h-screen w-full bg-[#eef2f7]">
            <AdminSidebar />
            <div class="flex flex-col flex-1 min-w-0">
                <AdminTopbar />
                <main class="flex-1 overflow-y-auto">
                    <div class="p-7 max-w-[1440px]">
                           <slot />
                    </div>
                </main>
            </div>
        </div>
       <FlashMessage />
    </SidebarProvider>
</template>
