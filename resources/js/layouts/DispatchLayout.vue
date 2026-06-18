<script setup>
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import { Toaster } from '@/components/ui/sonner';
import { computed } from 'vue';
import ModelSelector from '@/components/ModelSelector.vue';
import { router, usePage } from '@inertiajs/vue3'
import { changeModel as changeModelRoute } from '@/actions/App/Http/Controllers/AskController'

const page = usePage()
const models = computed(() => page.props.models)
const selectedModel = computed(() => page.props.selectedModel)

const handleChangeModel = (modelId) => {
    console.log('handleChangeModel called', modelId)
    router.post(changeModelRoute(), { model: modelId })
}
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <div class="bg-cn-black border-b border-cn-border px-4 py-3 flex items-center justify-between font-mono">
                <div class="font-display font-bold tracking-widest text-lg text-cn-yellow">
                CHRONO<span class="text-cn-cyan">//</span>NOIR
                </div>
                <ModelSelector :models="models" :model-value="selectedModel" @change="handleChangeModel" />
                <div class="text-xs text-zinc-600 tracking-widest">CYCLE 01</div>
            </div>

            <slot />
        </AppContent>
        <Toaster />
    </AppShell>
</template>
