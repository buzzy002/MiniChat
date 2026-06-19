<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    models: Array,
    modelValue: String
})

const showModels = ref(false)
const selectorRef = ref(null)

const emit = defineEmits(['update:modelValue', 'change'])

const selectModel = (modelId) => {
    console.log('selectModel called', modelId)
    emit('update:modelValue', modelId)
    emit('change', modelId)
    showModels.value = false
}

const handleClickOutside = (e) => {
    if (selectorRef.value && !selectorRef.value.contains(e.target)) {
        showModels.value = false
    }
}

onMounted(() => document.addEventListener('mousedown', handleClickOutside))
onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside))

</script>

<template>
  <div ref="selectorRef" class="relative flex flex-col items-center gap-1">
    <label class="text-xs text-cn-cyan uppercase tracking-widest">// model</label>
    <button
      @click="showModels = !showModels"
      class="text-zinc-400 cursor-pointer border-zinc-600  transition-colors duration-300 text-xs tracking-wider"
    >
      <span class="text-cn-yellow uppercase">Dispatch</span> - <span class="border-b border-dashed hover:text-cn-cyan hover:border-cn-cyan">{{ modelValue }}</span>
    </button>

    <div v-if="showModels" class="absolute top-full max-h-64 overflow-y-auto mt-2 bg-cn-surface border border-cn-border z-50 min-w-48">
      <div class="text-xs text-zinc-600 tracking-widest px-3 py-2 border-b border-cn-border">SELECT MODEL</div>
      <button
        v-for="model in models"
        :key="model.id"
        @click="selectModel(model.id)"
        class="w-full text-left px-3 py-2 text-xs font-mono cursor-pointer transition-colors duration-300"
        :class="modelValue === model.id ? 'text-cn-yellow' : 'text-zinc-500 hover:text-zinc-300'"
      >
        {{ model.name }}
      </button>
    </div>
  </div>
</template>
