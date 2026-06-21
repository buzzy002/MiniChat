<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue'
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { Trash2 } from '@lucide/vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const chats = computed(() => usePage().props.chats)
const { isCurrentUrl } = useCurrentUrl();

const chatToDelete = ref<string | null>(null)

const confirmDelete = (id: string) => {
    chatToDelete.value = id
}

const deleteChat = () => {
    if (!chatToDelete.value) return
    router.delete(`/chat/${chatToDelete.value}`)
    chatToDelete.value = null
}
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Chats</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in chats" :key="item.id">
                <div class="group/item flex items-center rounded-md hover:bg-sidebar-accent px-2 py-1.5 w-full">
                    <Link :href="`/chat/${item.id}`" class="flex-1 truncate text-sm"
                        :class="{ 'text-cn-yellow font-bold': isCurrentUrl(`/chat/${item.id}`) }">
                        {{ item.title }}
                    </Link>
                    <button @click="confirmDelete(item.id)"
                        class="opacity-0 group-hover/item:opacity-100 transition-opacity p-1 text-zinc-500 hover:text-cn-red shrink-0">
                        <Trash2 :size="14" />
                    </button>
                </div>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>

    <!-- Modal -->
    <Teleport to="body">
        <div v-if="chatToDelete" class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/70" @click="chatToDelete = null" />

            <!-- Panel -->
            <div class="relative bg-cn-surface border border-cn-border font-mono p-6 w-96 z-10">
                <div class="text-cn-red text-xs tracking-widest mb-1">// WARNING</div>
                <h2 class="text-zinc-200 text-sm tracking-wider mb-4 uppercase">
                    Terminate this contract ?
                </h2>
                <p class="text-zinc-500 text-xs mb-6">
                    This operation is irreversible. All transmissions will be lost.
                </p>
                <div class="flex gap-3 justify-end">
                    <button @click="chatToDelete = null"
                        class="px-4 py-2 text-xs tracking-widest text-zinc-400 border border-cn-border hover:border-zinc-500 transition-colors">
                        // ABORT
                    </button>
                    <button @click="deleteChat"
                        class="px-4 py-2 text-xs tracking-widest text-black bg-cn-red font-bold hover:opacity-80 transition-opacity">
                        // TERMINATE
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
