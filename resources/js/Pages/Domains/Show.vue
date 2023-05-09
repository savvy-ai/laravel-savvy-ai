<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import InputError from "../../Components/InputError.vue"
import PrimaryButton from "../../Components/PrimaryButton.vue"
import TextArea from "../../Components/TextArea.vue"
import { useForm } from "@inertiajs/vue3"
import TextInput from "../../Components/TextInput.vue"

const props = defineProps({
    domain: Object,
})

const intakeForm = useForm({
    text: '',
})

const promptForm = useForm({
    prompt: '',
})

function submit() {
    intakeForm.post(route('training.intake', props.domain), {
        preserveScroll: true,
        onSuccess: () => {
            intakeForm.reset()
        }
    })
}

function ask() {
    promptForm.post(route('training.ask', props.domain), {
        preserveScroll: true,
        onSuccess: () => {
            promptForm.reset()
        }
    })
}
</script>

<template>
    <AppLayout title="Training">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ domain.name }} Training
            </h2>
            <div class="mt-2 flex item-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                </svg>
                <a :href="route('chat.show', [domain])" target="_blank">{{ route('chat.show', [domain]) }}</a>
            </div>
        </template>

        <div class="py-12">
            <div class="grid grid-cols-1 gap-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <h2 class="text-2xl font-bold mb-4">Import text data</h2>
                    <form @submit.prevent="submit()" class="">
                        <TextArea v-model="intakeForm.text" class="w-full p-4" rows="16" placeholder="Paste your text data here."/>
                        <InputError :message="intakeForm.errors.text" class="mb-4"/>

                        <PrimaryButton @click="submit" :disabled="intakeForm.processing" class="mt-4 w-full py-4 justify-center">
                            Start Training
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
