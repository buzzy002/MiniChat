<script setup>
import MarkdownRenderer from '@/components/MarkdownRenderer.vue'
const props = defineProps({
    'content': String,
    'role': String,
})
</script>

<template>

    <div v-if="content != null" class="flex flex-col w-full" :class="{'items-end': role === 'user'}">
        <div class="font-mono w-300">
            <div v-if="role === 'LLM'" class="uppercase text-yellow-400">//Dispatch</div>
            <div v-if="role === 'user'" class="uppercase text-cyan-400">//Operator</div>
            <div class="bg-cn-surface border-l-3 p-4" :class="{'border-yellow-400': role === 'LLM', 'border-cyan-400': role === 'user'}">
                <MarkdownRenderer :content="props.content" />
            </div>
        </div>
    </div>

</template>
