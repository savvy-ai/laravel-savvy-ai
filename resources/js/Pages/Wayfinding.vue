<script setup>
import { onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import Layout from '@/Layouts/ClientLayout.vue'

onMounted(() => {
    // Enable Mapsted integration on the mapContainer element
    mapstedLib.integration.mapInto('.mapContainer')
})
</script>

<template>
    <Head title="Wayfinding" />
    <Layout>
        <div id="mapContainer" class="mapContainer h-full w-full"></div>
    </Layout>
</template>
