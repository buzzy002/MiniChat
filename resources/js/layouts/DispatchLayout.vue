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
    <div class="h-screen overflow-hidden">
        <AppShell variant="sidebar">
            <AppSidebar />
                <AppContent variant="sidebar" class="overflow-hidden flex flex-col h-screen">
                    <div class="shrink-0 bg-cn-black border-b border-cn-border px-4 py-3 flex items-center justify-between font-mono">
                        <div class="w-1/3 font-display font-bold tracking-widest text-lg text-cn-yellow">
                        CHRONO<span class="text-cn-cyan">//</span>NOIR
                        </div>

                        <div class="w-1/3 flex justify-center">
                            <ModelSelector :models="models" :model-value="selectedModel" @change="handleChangeModel" />
                        </div>

                        <div class="w-1/3 flex justify-end text-xs text-zinc-600 tracking-widest">
                            CYCLE 01
                        </div>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <slot />
                    </div>
                </AppContent>
            <Toaster />
        </AppShell>
    </div>
</template>
