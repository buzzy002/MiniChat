<script setup>
import { computed } from 'vue'
import MarkdownIt from 'markdown-it'
import hljs from 'highlight.js'
import 'highlight.js/styles/github-dark.css'

const props = defineProps({
    content: String,
})

const md = new MarkdownIt({
    highlight: function (str, lang) {
        if(lang && hljs.getLanguage(lang)) {
            try {
                return '<pre><code class="hljs">' +
                    hljs.highlight(str, { language: lang }).value +
                    '</code></pre>'
            } catch (__) {}
        }
        return ''
    }
})

const rendered = computed(() => md.render(props.content ?? ''))
</script>

<template>
    <div class="prose prose-invert prose-neutral max-w-none text-zinc-300" v-html="rendered" />
</template>
