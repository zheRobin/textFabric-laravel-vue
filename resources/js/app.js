import './bootstrap';
import '../css/app.css';
import '../css/vueform-slider.css';
import 'floating-vue/dist/style.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from './inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

import { i18nVue } from 'laravel-vue-i18n'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(name),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18nVue, {
                lang: localStorage.getItem('locale') || 'en',
                resolve: lang => import(`../../lang/${lang}.json`)
            })
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
