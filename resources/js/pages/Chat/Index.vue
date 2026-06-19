<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import { changeModel as changeModelRoute } from '@/actions/App/Http/Controllers/AskController';
import { ask } from '@/actions/App/Http/Controllers/ChatController'
import DispatchLayout from '@/layouts/DispatchLayout.vue';
import Message from '@/components/Message.vue';

defineOptions({ layout: DispatchLayout })

const props = defineProps({
    models: Array,
    selectedModel: String,
    chat: Object,
    messages: Array,
    message: String,
    error: String,
})

const textareaRef = ref(null)

const form = useForm({
    model: props.selectedModel ?? '',
    message: props.message ?? '',
})

const submit = () => {
    form.post(ask({ chatId: props.chat.id }), {
        onSuccess: () => {
            form.message = ''
        }
    })
}

const handleSubmitEnter = (e) => {
    if(e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault()
        submit()
    }
}

const handleChangeModel = () => {
    form.post(changeModelRoute())
}

onMounted(() => {
  textareaRef.value.addEventListener('input', function() {
    this.style.height = 'auto'
    this.style.height = this.scrollHeight + 'px'
  })
})
</script>

<template>
<Head :title="props.chat.title" />
<div class="flex flex-col h-full bg-cn-black text-zinc-400 px-3">

  <div class="bg-cn-black border-b border-cn-border px-4 py-2 shrink-0">
    <span class="font-mono text-xs text-zinc-400 tracking-widest uppercase">
      {{ props.chat.title }}
    </span>
  </div>

  <div class="flex-1 overflow-y-auto px-3 py-3">
    <div v-for="message in messages" :key="message.id" class="m-3">
      <Message :content="message.content" :role="message.role" />
    </div>
  </div>

  <hr>

  <div class="shrink-0 border-t border-cn-border p-5">
    <div class="grid grid-cols-12 gap-4 items-center">
      <div class="bg-cn-surface-light text-zinc-300 col-span-8 border-2 border-cn-border rounded-lg hover:border-zinc-500 transition-colors duration-400">
        <textarea v-model="form.message" rows="1"
          class="w-full font-mono resize-none overflow-hidden align-middle p-1 ps-2 focus:outline-none focus:ring-0 focus:border-transparent bg-transparent"
          placeholder="> Declare your contracts"
          @keypress.enter="handleSubmitEnter"
          ref="textareaRef" />
      </div>
      <div class="col-span-2 h-full">
        <button class="cursor-pointer bg-cn-surface text-zinc-300 border border-cn-border p-2 rounded-lg w-full h-full hover:border-zinc-500 transition-colors duration-400">
          Display cycle
        </button>
      </div>

      <div class="col-span-2 h-full">
            <button @click="submit" :disabled="form.processing" :class="{ 'cursor-pointer': !form.processing }"
                class="bg-cn-yellow text-black border font-bold border-cn-border flex items-center justify-center
                    p-2 rounded-lg w-full h-full clip-top-left-corner
                    hover:border-zinc-500 transition-colors duration-400
                    disabled:cursor-not-allowed disabled:opacity-50">
                <span v-if="form.processing" class="flex items-center justify-center gap-1">
                    <svg class="animate-spin h-4 w-4 mx-auto" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Sending
                </span>

                <span v-else>Send</span>
            </button>
        </div>
    </div>
    <div v-if="props.error" class="text-red-500 p-4 rounded bg-red-50 mt-2">
      Erreur : {{ props.error }}
    </div>
  </div>

</div>
</template>
