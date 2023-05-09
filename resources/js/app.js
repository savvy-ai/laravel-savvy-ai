import './bootstrap'
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m'
import { createPinia } from 'pinia'
import piniaPersistedState from 'pinia-plugin-persistedstate'
import mitt from 'mitt'

const appName = window.document.getElementsByTagName('title')[0]?.innerText
const emitter = mitt()

const pinia = createPinia()
pinia.use(piniaPersistedState)

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(emitter)
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue, Ziggy)
            
        app.config.globalProperties.emitter = emitter
        app.mount(el)
    },
    progress: {
        color: '#4B5563',
    },
})
