<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, onMounted, computed, watch, nextTick } from 'vue'
import { useStream } from '@laravel/stream-vue'
import { ask } from '@/actions/App/Http/Controllers/ChatController'
import DispatchLayout from '@/layouts/DispatchLayout.vue';
import Message from '@/components/Message.vue';

defineOptions({
    layout: DispatchLayout,
})

const props = defineProps({
    selectedModel: String,
    chat: Object,
    messages: Array,
    pending: Boolean,
    error: String,
})

const textareaRef = ref(null)
const messagesContainer = ref(null)

const form = useForm({
    model: props.selectedModel ?? '',
    message: '',
})

const { data: streamedContent, isFetching, isStreaming, send } = useStream(
    `/chat/${props.chat.id}/stream`,
    {
        onFinish: () => {
            if (props.chat.title === '') {
                setTimeout(() => {
                    window.location.reload()
                }, 3000)
            } else {
                router.reload()
            }
        },
    }
)

const isWaiting = computed(() => isFetching.value || isStreaming.value)

const startStream = () => {
    send({ model: props.selectedModel })
}

const submit = () => {
    form.post(ask({ chatId: props.chat.id }), {
        onSuccess: () => {
            form.message = ''
            console.log('onSuccess, pending:', props.pending)
        }
    })
}

const handleSubmitEnter = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault()
        submit()
    }
}

onMounted(() => {
    textareaRef.value?.addEventListener('input', function () {
        this.style.height = 'auto'
        this.style.height = this.scrollHeight + 'px'
    })

    nextTick(() => {
        messagesContainer.value?.scrollTo({
            top: messagesContainer.value.scrollHeight,
            behavior: 'instant'
        })
    })
})

watch(() => props.pending, (val) => {
    if (val) startStream()
}, { immediate: true })

watch(streamedContent, async () => {
    await nextTick()
    messagesContainer.value?.scrollTo({
        top: messagesContainer.value.scrollHeight,
        behavior: 'smooth'
    })
})

watch(() => props.messages, async () => {
    await nextTick()
    messagesContainer.value?.scrollTo({
        top: messagesContainer.value.scrollHeight,
        behavior: 'smooth'
    })
}, { deep: true })
</script>

<template>
    <Head :title="props.chat.title" />
    <div class="flex flex-col h-full bg-cn-black text-zinc-400 px-3">

        <div class="bg-cn-black border-b border-cn-border px-4 py-2 shrink-0">
            <span class="font-mono text-xs text-zinc-400 tracking-widest uppercase">
                {{ props.chat.title }}
            </span>
        </div>

        <div ref="messagesContainer" class="flex-1 overflow-y-auto px-3 py-3">
            <div v-for="message in messages" :key="message.id" class="m-3">
                <Message :content="message.content" :role="message.role" />
            </div>

            <!-- Message en cours de stream -->
            <div v-if="isWaiting || streamedContent" class="m-3">
                <Message
                    :content="streamedContent || '...'"
                    role="LLM"
                />
            </div>
        </div>

        <hr>

        <div class="shrink-0 border-t border-cn-border p-5">
            <div class="grid grid-cols-12 gap-4 items-center">
                <div class="bg-cn-surface-light text-zinc-300 col-span-10 border-2 border-cn-border rounded-lg hover:border-zinc-500 transition-colors duration-400">
                    <textarea v-model="form.message" rows="1"
                        class="w-full font-mono resize-none overflow-hidden align-middle p-1 ps-2 focus:outline-none focus:ring-0 focus:border-transparent bg-transparent"
                        placeholder="> Declare your contracts"
                        @keypress.enter="handleSubmitEnter"
                        :disabled="isWaiting"
                        ref="textareaRef" />
                </div>
                <div class="col-span-2 h-full">
                    <button @click="submit"
                        :disabled="form.processing || isWaiting"
                        class="bg-cn-yellow text-black border font-bold border-cn-border flex items-center justify-center p-2 rounded-lg w-full h-full clip-top-left-corner hover:border-zinc-500 transition-colors duration-400 disabled:cursor-not-allowed disabled:opacity-50">
                        <span v-if="form.processing || isWaiting" class="flex items-center justify-center gap-1">
                            <svg class="animate-spin h-4 w-4 mx-auto" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            {{ isStreaming ? 'Streaming...' : 'Sending' }}
                        </span>
                        <span v-else>Send</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>
