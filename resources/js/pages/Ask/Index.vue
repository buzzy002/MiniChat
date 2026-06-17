<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import { ask, changeModel as changeModelRoute } from '@/actions/App/Http/Controllers/AskController'
import MarkdownRenderer from '@/components/MarkdownRenderer.vue'


const props = defineProps({
    models: Array,
    selectedModel: String,
    message: String,
    response: String,
    error: String,
})

const form = useForm({
    message: props.message ?? '',
    model: props.selectedModel ?? '',
})

const submit = () => {
    form.post(ask())
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


</script>

<template>
    <Head title="Poser une question" />

    <div class="bg-neutral-100 text-neutral-950">
        <div class="flex flex-col gap-1 mx-2 w-fit ml-auto mr-3 my-2">
            <label class="text-xs text-end font-medium uppercase tracking-widest text-neutral-950">
                Model
            </label>
            <div class="relative">
                <select v-model="form.model" @change="handleChangeModel"
                class="appearance-none bg-white border border-neutral-200 rounded-lg px-4 py-2.5 pr-10 text-sm text-neutral-800 shadow-sm cursor-pointer transition focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:border-transparent hover:border-neutral-400">
                    <option disabled value="">Choose your model</option>
                    <hr />
                    <option v-for="model in props.models"
                        :key="model.id"
                        :value="model.id">
                        {{ model.name }}
                    </option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                    <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
            </div>

        <br />


        <div class="mx-2 my-3">
            <label for="UserQuestion">Your question</label>
            <textarea v-model="form.message" class="w-full border-2 rounded-lg p-4"
                placeholder="Type your question here..." @keypress.enter="handleSubmitEnter" />
        </div>

        <div class="flex justify-end mr-2">
            <button @click="submit" class="bg-neutral-400 text-gray-950 p-2 rounded-lg">
                Send
            </button>
        </div>

        <div class="px-3">
            <MarkdownRenderer :content="props.response" />
        </div>

        <div v-if="props.error" class="text-red-500 p-4 rounded bg-red-50">
            Erreur : {{ props.error }}
        </div>
    </div>
</template>
