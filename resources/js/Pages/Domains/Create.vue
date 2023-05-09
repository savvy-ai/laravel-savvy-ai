<script setup>
import AppLayout from "../../Layouts/AppLayout.vue"
import { useForm } from "@inertiajs/vue3"
import TextInput from "../../Components/TextInput.vue"
import InputError from "../../Components/InputError.vue"
import PrimaryButton from "../../Components/PrimaryButton.vue"

const form = useForm({
    name: '',
    handle: '',
})

function setHandle(name) {
    const handle = name || form.name

    form.handle = handle
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '')
}

function submit() {
    setHandle(form.handle || form.name)

    form.post(route('domains.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        }
    })
}
</script>

<template>
    <AppLayout title="Create a knowledge base">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create a knowledge base
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="max-w-2xl mx-auto bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <form @submit.prevent="submit()">
                        <TextInput @keyup="setHandle(form.name)" v-model="form.name" class="w-full p-4" placeholder="Knowledge base name"/>
                        <InputError :message="form.errors.name" class="mb-4"/>

                        <TextInput v-model="form.handle" class="w-full p-4 mt-4" placeholder="Knowledge base handle"/>
                        <InputError :message="form.errors.handle" class="mb-4"/>

                        <PrimaryButton @click="submit" :disabled="form.processing" class="mt-4 w-full py-4 justify-center">
                            Create
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
