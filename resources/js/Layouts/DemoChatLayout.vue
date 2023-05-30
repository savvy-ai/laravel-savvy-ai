<script setup>
import { router } from '@inertiajs/vue3'

import { useChatStore } from '../Store/chat'

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
    <div class="absolute inset-0 flex flex-1 flex-col justify-between">
        <!-- messages -->
        <div
            id="messages"
            class="no-scrollbar scrolling-touch mx-auto mt-auto flex w-full max-w-3xl flex-col space-y-8 overflow-y-auto scroll-smooth p-4"
        >
            <slot name="messages"></slot>
        </div>

        <!-- prompt -->
        <div class="mx-auto w-full max-w-3xl px-4 py-8">
            <slot name="prompt"></slot>
        </div>
    </div>
</template>
