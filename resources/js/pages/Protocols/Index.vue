<script setup>
import DispatchLayout from '@/layouts/DispatchLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: DispatchLayout })

const props = defineProps({
    user: Object,
})

const tabs = [
    { key: 'identity', label: 'IDENTITY' },
    { key: 'tone', label: 'TONE' },
    { key: 'custom', label: 'COMMANDS' },
]

const activeTab = ref('identity')

const sectors = ['Tech / IT', 'Design', 'Education', 'Finance', 'Startup', 'Freelance', 'Student', 'Other']
const levels = [
  { key: 'NOVICE', sub: 'Just starting out' },
  { key: 'INTERMEDIATE', sub: 'I know the basics' },
  { key: 'ADVANCED', sub: 'I have solid command' },
  { key: 'EXPERT', sub: 'It\'s my work' },
]

const styles = [
    { key: 'CONCISE', sub: 'short directives' },
    { key: 'BALANCED', sub: 'standard briefing' },
    { key: 'DETAILED', sub: 'full analyses' },
]

const formats = [
    { key: 'LIST', sub: 'bullet points & structure' },
    { key: 'PARAGRAPHS', sub: 'flowing prose' },
    { key: 'TABLES', sub: 'data & comparisons' },
    { key: 'CODE_FIRST', sub: 'code before explanation' },
]

const identity = props.user.identity ?? {}
const tone = props.user.tone ?? {}

const form = useForm({
    identity: {
        operator_name: identity.operator_name ?? '',
        localisation: identity.localisation ?? '',
        about: identity.about ?? '',
        sector: identity.sector ?? '',
        level: identity.level ?? 'INTERMEDIATE',
        technologies: identity.technologies ?? '',
    },
    tone: {
        language: tone.language ?? 'EN',
        style: tone.style ?? 'BALANCED',
        format: tone.format ?? 'PARAGRAPHS',
    },
    commands: props.user.commands ?? [],
})

const submit = () => {
    form.post('/protocols')
}
</script>

<template>
    <div class="bg-cn-black p-6 m-4 rounded-xl border border-cn-border max-w-3xl">

        <div class="flex items-center justify-between mb-6">
            <div class="font-display font-bold text-xl text-cn-cyan tracking-widest">
                OPERATOR <span class="text-cn-border">//</span> PROTOCOLS
            </div>
            <span class="font-mono text-xs text-cn-muted tracking-widest">SYS_READY_FOR_INSTRUCTIONS <span class="text-cn-cyan animate-pulse">●</span></span>
        </div>

        <!-- Tabs -->
        <div class="flex gap-0 bg-cn-surface border border-cn-border rounded-md p-1 w-fit mb-6">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="activeTab = tab.key"
                class="font-mono text-xs px-3.5 py-1.5 rounded tracking-widest transition-colors"
                :class="activeTab === tab.key
                    ? 'bg-cn-cyan/10 border border-cn-cyan text-cn-cyan'
                    : 'border border-transparent text-cn-muted hover:text-cn-cyan hover:border-cn-cyan/40'"
            >
                // {{ tab.label }}
            </button>
        </div>

        <!-- Content -->
        <div class="border border-cn-border border-l-2 border-l-cn-cyan rounded-r-md rounded-bl-md p-5 min-h-36">

            <!-- IDENTITY -->
            <div v-show="activeTab === 'identity'" class="grid grid-cols-2 gap-4">
                <div class="bg-cn-surface border border-cn-border rounded-lg p-4 flex flex-col gap-3">
                    <span class="font-display font-bold text-xs text-cn-cyan tracking-widest">// IDENTIFICATION</span>
                    <div>
                        <label class="font-mono text-xs text-cn-muted tracking-widest block mb-1">OPERATOR_NAME</label>
                        <input v-model="form.identity.operator_name" type="text"
                            placeholder="Prénom, pseudo, codename..."
                            class="font-mono text-xs bg-cn-black border border-cn-border rounded text-zinc-300 px-2.5 py-2 w-full focus:outline-none focus:border-cn-cyan/40 transition-colors placeholder:text-zinc-700" />
                    </div>
                    <div>
                        <label class="font-mono text-xs text-cn-muted tracking-widest block mb-1">LOCALISATION</label>
                        <input v-model="form.identity.localisation" type="text"
                            placeholder="Bruxelles, Remote, UTC+1..."
                            class="font-mono text-xs bg-cn-black border border-cn-border rounded text-zinc-300 px-2.5 py-2 w-full focus:outline-none focus:border-cn-cyan/40 transition-colors placeholder:text-zinc-700" />
                    </div>
                    <div>
                        <label class="font-mono text-xs text-cn-muted tracking-widest block mb-1">ABOUT</label>
                        <textarea v-model="form.identity.about" rows="10"
                            placeholder="Contexte, projets en cours, motivations..."
                            class="font-mono text-xs bg-cn-black border border-cn-border rounded text-zinc-300 px-2.5 py-2 w-full focus:outline-none focus:border-cn-cyan/40 transition-colors placeholder:text-zinc-700 resize-none"
                        />
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <div class="bg-cn-surface border border-cn-border rounded-lg p-4">
                        <span class="font-display font-bold text-xs text-cn-cyan tracking-widest block mb-3">// SECTOR</span>
                        <div class="flex flex-wrap gap-1.5">
                            <button
                                v-for="sector in sectors" :key="sector"
                                @click="form.identity.sector = sector"
                                class="font-mono text-xs px-2.5 py-2.5 border rounded text-center transition-colors"
                                :class="form.identity.sector === sector
                                    ? 'border-cn-cyan text-cn-cyan bg-cn-cyan/5'
                                    : 'border-cn-border text-cn-muted hover:border-cn-cyan/40 hover:text-cn-cyan'"
                            >{{ sector }}</button>
                        </div>
                    </div>

                    <div class="bg-cn-surface border border-cn-border rounded-lg p-4">
                        <span class="font-display font-bold text-xs text-cn-cyan tracking-widest block mb-3">// LEVEL</span>
                        <div class="grid grid-cols-2 gap-1.5">
                            <button
                                v-for="level in levels" :key="level.key"
                                @click="form.identity.level = level.key"
                                class="font-mono text-xs py-2.5 border rounded text-center transition-colors"
                                :class="form.identity.level === level.key
                                    ? 'border-cn-cyan text-cn-cyan bg-cn-cyan/5'
                                    : 'border-cn-border text-cn-muted hover:border-cn-cyan/40 hover:text-cn-cyan'"
                            >
                                <span class="block text-xs">{{ level.key }}</span>
                                <span class="block text-xs opacity-50 mt-0.5">{{ level.sub }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="bg-cn-surface border border-cn-border rounded-lg p-4">
                        <span class="font-display font-bold text-xs text-cn-cyan tracking-widest block mb-3">// TECHNOLOGIES</span>
                        <input v-model="form.identity.technologies" type="text"
                            placeholder="Laravel, Vue.js, Python, C#..."
                            class="font-mono text-xs bg-cn-black border border-cn-border rounded text-zinc-300 px-2.5 py-2 w-full focus:outline-none focus:border-cn-cyan/40 transition-colors placeholder:text-zinc-700" />
                    </div>
                </div>
            </div>

            <!-- TONE -->
            <div v-show="activeTab === 'tone'" class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-4">
                    <div class="bg-cn-surface border border-cn-border rounded-lg p-4">
                        <span class="font-display font-bold text-xs text-cn-cyan tracking-widest block mb-3">// LANGUAGE</span>
                        <div class="flex gap-2">
                            <button v-for="language in ['FR', 'EN', 'ES', 'NL']" :key="language"
                                @click="form.tone.language = language"
                                class="font-mono text-xs px-4 py-2 border rounded tracking-widest transition-colors"
                                :class="form.tone.language === language
                                    ? 'border-cn-cyan text-cn-cyan bg-cn-cyan/5'
                                    : 'border-cn-border text-cn-muted hover:border-cn-cyan/40 hover:text-cn-cyan'"
                            >{{ language }}</button>
                        </div>
                    </div>

                    <div class="bg-cn-surface border border-cn-border rounded-lg p-4">
                        <span class="font-display font-bold text-xs text-cn-cyan tracking-widest block mb-3">// RESPONSE_STYLE</span>
                        <div class="flex flex-col gap-1.5">
                            <button v-for="style in styles" :key="style.key"
                                @click="form.tone.style = style.key"
                                class="font-mono text-xs py-2.5 border rounded text-center transition-colors"
                                :class="form.tone.style === style.key
                                    ? 'border-cn-cyan text-cn-cyan bg-cn-cyan/5'
                                    : 'border-cn-border text-cn-muted hover:border-cn-cyan/40 hover:text-cn-cyan'"
                            >
                                <span class="block text-xs">{{ style.key }}</span>
                                <span class="block text-xs opacity-50 mt-0.5">{{ style.sub }}</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="bg-cn-surface border border-cn-border rounded-lg p-4">
                        <span class="font-display font-bold text-xs text-cn-cyan tracking-widest block mb-3">// RESPONSE_FORMAT</span>
                        <div class="flex flex-col gap-1.5">
                            <button v-for="format in formats" :key="format.key"
                                @click="form.tone.format = format.key"
                                class="font-mono text-xs py-2.5 border rounded text-center transition-colors"
                                :class="form.tone.format === format.key
                                    ? 'border-cn-cyan text-cn-cyan bg-cn-cyan/5'
                                    : 'border-cn-border text-cn-muted hover:border-cn-cyan/40 hover:text-cn-cyan'"
                            >
                                <span class="block text-xs">{{ format.key }}</span>
                                <span class="block text-xs opacity-50 mt-0.5">{{ format.sub }}</span>
                            </button>
                        </div>
                    </div>

            </div>

            <!-- DIRECTIVES -->
            <div v-show="activeTab === 'custom'">
                <span class="font-display font-bold text-xs text-cn-cyan tracking-widest block mb-1">// CUSTOM_COMMANDS</span>
                <p class="font-mono text-xs text-cn-muted mb-4">Shortcuts injected into DISPATCH on trigger.</p>

                <div class="flex flex-col gap-3 mb-4">
                    <div v-for="(cmd, index) in form.commands" :key="index"
                        class="flex gap-2 items-start bg-cn-surface border border-cn-border rounded-lg p-3">
                        <input v-model="cmd.trigger"
                            placeholder="/trigger"
                            class="font-mono text-xs bg-cn-black border border-cn-border rounded text-cn-cyan px-2.5 py-2 w-28 shrink-0 focus:outline-none focus:border-cn-cyan/40 placeholder:text-zinc-700" />
                        <textarea v-model="cmd.instruction"
                            placeholder="Instruction sent to DISPATCH..."
                            class="font-mono text-xs bg-cn-black border border-cn-border rounded text-zinc-300 px-2.5 py-2 w-full focus:outline-none focus:border-cn-cyan/40 placeholder:text-zinc-700 resize-none h-16" />
                        <button @click="form.commands.splice(index, 1)"
                            class="font-mono text-xs text-cn-muted hover:text-cn-red transition-colors shrink-0 mt-1">
                            ✕
                        </button>
                    </div>
                </div>

                <button @click="form.commands.push({ trigger: '', instruction: '' })"
                    class="font-mono text-xs px-4 py-2 border border-cn-border text-cn-muted hover:border-cn-cyan/40 hover:text-cn-cyan rounded transition-colors">
                    + ADD COMMAND
                </button>
            </div>

        </div>

        <div class="flex justify-end mt-4">
            <button @click="submit"
                class="font-mono text-xs px-6 py-2 bg-cn-cyan/10 border border-cn-cyan text-cn-cyan tracking-widest clip-top-left-corner hover:bg-cn-cyan/20 transition-colors cursor-pointer">
                COMMIT PROTOCOLS ›
            </button>
        </div>

    </div>
</template>
