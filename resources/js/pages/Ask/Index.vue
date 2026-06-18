<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import { ask, changeModel as changeModelRoute } from '@/actions/App/Http/Controllers/AskController'
import Message from '@/components/Message.vue'
import { ref, onMounted } from 'vue'
import DispatchLayout from '@/layouts/DispatchLayout.vue'
defineOptions({ layout: DispatchLayout })

const props = defineProps({
    models: Array,
    selectedModel: String,
    message: String,
    response: String,
    error: String,
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

    <Head title="Poser une question" />

    <div class="bg-cn-black text-zinc-300 p-3">

        <div class="px-3">
            <Message :content="props.response"  />
        </div>

        <hr class="my-3"/>

        <div class="grid grid-cols-12 gap-4 item-center">
            <div class="bg-cn-surface-light text-zinc-300 col-span-8 border-2 border-cn-border rounded-lg hover:border-zinc-500 transition-colors duration-400">
                <textarea v-model="form.message" rows="1" class="w-full font-mono resize-none overflow-hidden align-middle p-1 ps-2 focus:outline-none focus:ring-0 focus:border-transparent"
                    placeholder="> Declare your contracts" @keypress.enter="handleSubmitEnter" ref="textareaRef" />
            </div>

            <div class="col-span-3 h-full">
                <button class="cursor-pointer bg-cn-surface text-zinc-300 border border-cn-border p-2 rounded-lg w-full h-full hover:border-zinc-500 transition-colors duration-400">
                    Display cycle
                </button>
            </div>

            <div class="col-span-1 h-full">
                <button @click="submit" class="cursor-pointer bg-cn-surface text-zinc-300 border font-bold border-cn-border p-2 rounded-lg w-full h-full hover:border-zinc-500 transition-colors duration-400">
                    Send
                </button>
            </div>
        </div>


        <div v-if="props.error" class="text-red-500 p-4 rounded bg-red-50">
            Erreur : {{ props.error }}
        </div>
    </div>

</template>
