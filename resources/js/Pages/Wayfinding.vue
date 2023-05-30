<script setup>
import {onMounted} from 'vue'
import {Head} from '@inertiajs/vue3'
import Layout from '@/Layouts/ClientLayout.vue'

const props = defineProps({
    propertyId: {
        type: String,
        required: true,
    },
    buildingId: {
        type: String,
        required: false,
    }, floorId: {
        type: String,
        required: false,
    }, entityId: {
        type: String,
        required: false,
    },
})

let BUILDING_DATA

onMounted(async () => {

    // Get data for a building within a property
    await fetch(`https://public.mapsted.com/api/v2/cms/property/${props.propertyId}`)
        .then((resp) => resp.json())
        .then((data) => (BUILDING_DATA = data))

    await mapsted.maps.initialize({
        element: document.getElementById('mapContainer'),
    })

    mapsted.maps.addEventListener('load', async () => {
        if (!props.floorId) return

        mapsted.maps.changeFloorById(parseInt(props.floorId))

        if (!props.entityId) return

        setTimeout(() => {
            mapsted.maps.selectEntity(parseInt(props.entityId))
        }, 500)
    })
})

// Go to this floor in any building for the current property
function selectFloor(floorId) {
    mapsted.maps.changeFloorById(floorId)
}
// Set currently selected entity to this
function selectEntity(entityId) {
    mapsted.maps.selectEntity(entityId)
}
</script>

<template>
    <Head title="Wayfinding"/>
    <Layout>
        <div id="mapContainer" class="mapContainer h-full w-full"></div>
    </Layout>
</template>
