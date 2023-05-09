<script setup>
import AppLayout from "../../Layouts/AppLayout.vue"
import { Link } from "@inertiajs/vue3"

const props = defineProps({
    domains: Array,
})

</script>

<template>
    <AppLayout title="Domains">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Knowledge Bases
            </h2>
        </template>

        <div class="grid grid-cols-4 gap-8 mt-12">
            <Link :href="route('domains.show', [domain])" v-for="domain in domains" class="block p-4 bg-white shadow-xl rounded-lg">
                <h2 class="text-2xl font-bold mb-4">{{ domain.name }}</h2>
                <p class="text-gray-500">Handle: {{ domain.handle }}</p>
                <p class="text-gray-500">Statements: {{ domain.statements_count }}</p>
            </Link>
        </div>
    </AppLayout>
</template>
