<script setup>
import { onMounted, ref, computed } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import axios from '../Utils/axios'
import { useChatStore } from '../Store/chat'
import ClientLayout from '../Layouts/ClientLayout.vue'
import DemoChatLayout from '../Layouts/DemoChatLayout.vue'
import Thinking from '../Components/Thinking.vue'

import useEmitter from '@/Composables/useEmitter'

const emitter = useEmitter()

const chat = useChatStore()

const props = defineProps({
    domain: Object,
})

const history = ref([])

const thinking = ref(false)

const form = useForm({
    prompt: '',
})

onMounted(async () => {
    await axios
        .post(route('demo.chat.history', props.domain), {
            domainId: props.domain.id,
            chatId: chat.getChatId(props.domain.id),
        })
        .then((response) => (history.value = response.data.history))

    if (history.value.length === 0) {
        addWelcomeMessage()
    }

    scrollToRecentMessage()
    emitter.on('clearChat', clear)
})

const askIsDisabled = computed(() => {
    return thinking.value
})

function ask() {
    history.value.push({
        role: 'user',
        content: form.prompt,
        updated_at: new Date(),
    })

    setTimeout(() => {
        scrollToRecentMessage()
    }, 500)

    thinking.value = true

    const data = {
        prompt: form.prompt,
        domainId: props.domain.id,
        chatId: chat.getChatId(props.domain.id),
    }

    axios.post(route('demo.chat.ask', props.domain), data).then((response) => {
        chat.addChatId(response.data.domainId, response.data.chatId)

        history.value.push({
            role: 'assistant',
            content: response.data.reply,
            media: response.data.media.length != 0 ? response.data.media : null,
            updated_at: new Date(),
        })

        thinking.value = false
        form.reset('prompt')

        setTimeout(() => {
            scrollToRecentMessage()
        }, 500)
    })
}

function clear() {
    thinking.value = true

    chat.clear(props.domain.id)
    history.value = []
    addWelcomeMessage()

    thinking.value = false
}

function addWelcomeMessage() {
    history.value.push({
        role: 'assistant',
        type: 'completion',
        content: `I'm an AI trained to answer any questions you may have about ${props.domain.name}.`,
        domainId: props.domain.id,
        timestamp: new Date(),
    })
}

function scrollToRecentMessage() {
    let container = document.getElementById('messages')

    container.scrollTop = container.scrollHeight
}

function navigate(media) {
    window.location.assign([
        '/wayfinding',
        media.b,
        media.f,
        media.e,
    ].join('/'))
}
</script>

<template>
    <Head title="Chat" />
    <ClientLayout>
        <div class="relative h-full">
            <DemoChatLayout :domain="props.domain">
                <template #messages>
                    <div
                        v-for="message in history"
                        :key="message.id"
                        class="chat-message"
                    >
                        <div
                            v-if="message.role === 'user'"
                            class="flex items-end justify-end"
                        >
                            <div class="flex max-w-md flex-col items-start space-y-2">
                                <div
                                    v-html="message.content"
                                    class="inline-block rounded-xl bg-[#12284B] px-4 py-2 font-semibold text-white shadow"
                                ></div>
                            </div>
                        </div>
                        <div v-if="message.role === 'assistant'" class="flex items-end">
                            <div class="flex w-full flex-col items-end space-y-2 md:max-w-md">
                                <div class="w-full rounded-xl bg-white font-semibold text-slate-600 shadow">
                                    <div class="whitespace-pre-line px-4 py-2">
                                        {{ message.content }}
                                        <div v-for="media in message.media" :key="media.e">
                                            <div @click="navigate(media)" class="mt-4 mb-2 border-2 border-stone-100 hover:border-stone-200 bg-stone-50 hover:bg-stone-100 transition-all py-2 px-3 shadow block rounded-lg hover:shadow-md transition-shadow cursor-pointer">
                                                {{ media.title }}
                                                <span class="text-md align-text-top float-right pr-2">&#x27A4;</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="thinking" class="flex justify-center text-white">
                        <Thinking class="w-16" />
                    </div>
                </template>

                <template #prompt>
                    <form
                        @submit.prevent="ask"
                        class="relative grid rounded-lg focus-within:border-[#12284B] focus-within:ring-4 focus-within:ring-[#12284B] focus:outline-none"
                        style="grid-template-columns: 85% auto"
                    >
                        <input
                            v-model="form.prompt"
                            type="text"
                            required
                            autofocus
                            placeholder="How can I help?"
                            class="h-12 w-full rounded-l-lg border-2 border-slate-700 bg-white pr-12 font-semibold text-slate-600 placeholder-slate-600 focus:border-none focus:outline-none focus:ring-0 md:px-5"
                        />
                        <button
                            :disabled="askIsDisabled"
                            type="submit"
                            class="flex items-center justify-center rounded-r-lg border-l-4 border-[#12284B] bg-[#12284B] py-2 text-white outline-none transition-all duration-150 ease-linear hover:scale-105"
                        >
                            <svg
                                class="h-8 w-8 fill-white"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 96 960 960"
                            >
                                <path
                                    d="M140.001 865.998V286.002L828.458 576 140.001 865.998Zm45.384-70.306L710.537 576 185.385 354.078v168L403.922 576l-218.537 52.307v167.385Zm0 0V354.078v441.614Z"
                                />
                            </svg>
                        </button>
                    </form>
                </template>
            </DemoChatLayout>
        </div>
    </ClientLayout>
</template>
