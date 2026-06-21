<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import { ask, changeModel as changeModelRoute } from '@/actions/App/Http/Controllers/AskController'
import { ref, onMounted } from 'vue'
import DispatchLayout from '@/layouts/DispatchLayout.vue'

defineOptions({ layout: DispatchLayout })

const props = defineProps({
    models: Array,
    selectedModel: String,
    message: String,
    response: String,
    error: String,
    auth: Object,
})

const textareaRef = ref(null)

const form = useForm({
    message: props.message ?? '',
    model: props.selectedModel ?? '',
})

const submit = () => {
    form.post(ask(), {
        onSuccess: () => {
            form.message = ''
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
})
</script>

<template>
    <Head title="New contract" />
    <div class="flex flex-col h-full bg-cn-black text-zinc-400 px-3">

        <!-- Centre -->
        <div class="flex-1 flex flex-col items-center justify-center gap-2">
            <span class="font-mono text-cn-yellow text-xs tracking-widest uppercase">// DISPATCH ONLINE</span>
            <h1 class="font-mono text-zinc-300 text-2xl tracking-widest uppercase">
                Declare your contracts,
                <span class="text-cn-cyan">{{ auth?.user.name ?? 'OPERATOR' }}</span>.
            </h1>
            <p class="font-mono text-zinc-600 text-xs tracking-wider">Awaiting transmission.</p>
        </div>

        <hr class="border-cn-border">

        <!-- Input fixé en bas -->
        <div class="shrink-0 border-t border-cn-border p-5">
            <div v-if="props.error" class="text-cn-red font-mono text-xs mb-3">
                // ERROR: {{ props.error }}
            </div>
            <div class="grid grid-cols-12 gap-4 items-center">
                <div class="bg-cn-surface-light text-zinc-300 col-span-10 border-2 border-cn-border rounded-lg hover:border-zinc-500 transition-colors duration-400">
                    <textarea
                        v-model="form.message"
                        rows="1"
                        class="w-full font-mono resize-none overflow-hidden align-middle p-1 ps-2 focus:outline-none focus:ring-0 focus:border-transparent bg-transparent"
                        placeholder="> Declare your contracts"
                        @keypress.enter="handleSubmitEnter"
                        :disabled="form.processing"
                        ref="textareaRef"
                    />
                </div>
                <div class="col-span-2 h-full">
                    <button
                        @click="submit"
                        :disabled="form.processing"
                        class="bg-cn-yellow text-black border font-bold border-cn-border flex items-center justify-center p-2 rounded-lg w-full h-full clip-top-left-corner hover:border-zinc-500 transition-colors duration-400 disabled:cursor-not-allowed disabled:opacity-50"
                    >
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
        </div>

    </div>
</template>
