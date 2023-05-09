<script setup>
import ChatLayout from "../Layouts/ChatLayout.vue"
import { useChatStore } from "../Store/chat"
import { useForm } from "@inertiajs/vue3"
import { onMounted, ref } from "vue"
import axios from "../Utils/axios"
import Thinking from "../Components/Thinking.vue"

const chat = useChatStore()

const props = defineProps({
    domain: Object,
})

const history = ref([])

const thinking = ref(false)

const form = useForm({
    prompt: ''
})

onMounted(() => {
    if (history.value.length === 0) {
        addWelcomeMessage()
    }

    axios.post(route('chat.history', props.domain), {
            domainId: props.domain.id,
            chatId: chat.getChatId(props.domain.id)
        }).then(response => history.value = response.data.history)


    scrollToRecentMessage()
})

function clear() {
    thinking.value = true

    const data = {
        chatId: chat.getChatId(props.domain.id)
    }

    axios.post(route("chat.clear", props.domain), data)
        .then((response) => {
            if (response.data.success) {
                history.value = []
                
                this.addWelcomeMessage()
            }
        });

    thinking.value = false
}

function ask() {
    history.value.push({
        role: 'user',
        content: form.prompt,
        updated_at: new Date()
    })

    setTimeout(() => {
        scrollToRecentMessage()
    }, 500)

    thinking.value = true

    const data = {
        prompt: form.prompt,
        domainId: props.domain.id,
        chatId: chat.getChatId(props.domain.id)
    }

    axios
        .post(route('chat.ask', props.domain), data)
        .then(response => {
            chat.addChatId(
                response.data.domainId,
                response.data.chatId
            )

            history.value.push({
                role: 'assistant',
                content: response.data.reply,
                updated_at: new Date()
            })

            thinking.value = false
            form.reset('prompt')

            setTimeout(() => {
                scrollToRecentMessage()
            }, 500)
    })
}

function addWelcomeMessage() {
    history.value.push({
        type: 'completion',
        text: `I'm an AI trained to schedule appointments and answer any questions you may have about ${props.domain.name}.`,
        domainId: props.domain.id,
        timestamp: new Date()
    })
}

function scrollToRecentMessage() {
    let container = document.getElementById('messages')

    container.scrollTop = container.scrollHeight
}
</script>

<template>
    <ChatLayout :domain="props.domain">
        <template #messages>
            <div v-for="message in history" :key="message.id" class="chat-message">
                <div v-if="message.role === 'user'" class="flex items-end justify-end">
                    <div class="flex flex-col space-y-2 max-w-md items-start">
                        <div v-html="message.content" class="px-4 py-2 rounded-xl inline-block font-semibold bg-slate-700 shadow text-white"></div>
                    </div>
                </div>
                <div v-if="message.role === 'assistant'" class="flex items-end">
                    <div class="flex flex-col space-y-2 w-full md:max-w-md items-end">
                        <div class="w-full rounded-xl font-semibold bg-white shadow text-slate-600">
                            <div v-html="message.content" class="px-4 py-2 whitespace-pre-line"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="thinking" class="flex justify-center">
                <Thinking class="w-12" />
            </div>
        </template>

        <template #prompt>
            <form @submit.prevent="ask" class="relative">
                <input v-model="form.prompt" type="text" required placeholder="How can I help?" class="w-full h-12 text-slate-600 font-semibold placeholder-slate-600 bg-white rounded-lg px-5 pr-12 border-2 border-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-300 focus:border-slate-900">
                <button type="submit" class="absolute right-0 inset-y-0 w-16 h-10 mr-1 mt-1 flex items-center justify-center rounded-md text-white bg-slate-700 outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </form>
            <form @submit.prevent="clear">
                <button
                    type="submit"
                    class="inset-y-0 w-16 h-10 mr-1 mt-1 flex items-center justify-center rounded-md text-white bg-slate-700 outline-none"
                >Clear</button>
            </form>
        </template>
    </ChatLayout>
</template>
