<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue'
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
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

const deleteChat = (id: string) => {
    if (confirm('Supprimer ce chat ?')) {
        router.delete(`/chat/${id}`)
    }
}
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Chats</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in chats" :key="item.id">
                <div class="group/item flex items-center rounded-md hover:bg-sidebar-accent px-2 py-1.5 w-full">
                    <Link :href="`/chat/${item.id}`" class="flex-1 truncate text-sm" :class="{ 'text-cn-yellow font-bold': isCurrentUrl(`/chat/${item.id}`) }">
                        {{ item.title }}
                    </Link>
                    <button
                        @click="deleteChat(item.id)"
                        class="opacity-0 group-hover/item:opacity-100 transition-opacity p-1 text-zinc-500 hover:text-cn-red shrink-0">
                        <Trash2 :size="14" />
                    </button>
                </div>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
