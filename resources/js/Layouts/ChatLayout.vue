<script setup>
import { Head, router } from '@inertiajs/vue3'
import ApplicationMark from "../Components/ApplicationMark.vue"
import { useChatStore } from "../Store/chat"

const chat = useChatStore()

const props = defineProps({
    domain: Object,
})

function clearChat() {
    router.post(route('chat.clear', [props.domain.handle]))
    chat.clearMessages()
}

</script>

<template>
    <div class="bg-slate-50">
        <Head :title="`Welcome to ${domain.name}`"/>

        <!-- component -->
        <div class="absolute inset-0 flex-1 justify-between flex flex-col bg-slate-50">

            <!-- header -->
            <div class="bg-white shadow relative">
                <div class="max-w-3xl mx-auto flex items-center gap-4 w-full p-4 md:p-8">
                    <ApplicationMark class="block h-9 w-auto"/>
                    <div class="flex items-center gap-4 text-xl text-slate-900 ml-auto">
                        {{ domain.name }}
                        <a @click.prevent="clearChat" href="#" class="text-xs uppercase font-bold px-2 py-1 rounded bg-slate-100 text-blue-500">Clear Chat</a>
                    </div>
                </div>
            </div>

            <!-- messages -->
            <div id="messages" class="max-w-3xl mx-auto w-full flex flex-col mt-auto space-y-8 p-4 overflow-y-auto scrolling-touch scroll-smooth">
                <slot name="messages"></slot>
            </div>

            <!-- prompt -->
            <div class="max-w-3xl mx-auto w-full px-4 py-8">
                <slot name="prompt"></slot>
            </div>

        </div>
    </div>
</template>
