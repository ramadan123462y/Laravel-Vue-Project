<script setup>
import { SidebarProvider } from '@/components/ui/sidebar'
import ClientSidebar from './client/ClientSidebar.vue'
import ClientTopbar from './client/ClientTopbar.vue'
</script>

<template>
  <SidebarProvider>
    <div class="flex min-h-screen w-full bg-[#eef2f7]">
      <ClientSidebar />
      <div class="flex flex-col flex-1 min-w-0">
        <ClientTopbar />
        <main class="flex-1 overflow-y-auto">
          <div class="p-7 max-w-[1440px]">
             <slot />
          </div>
        </main>
      </div>
    </div>
  </SidebarProvider>
</template>