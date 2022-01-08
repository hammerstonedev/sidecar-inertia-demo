import {createSSRApp, h} from 'vue'
import {renderToString} from '@vue/server-renderer'
import {createInertiaApp} from '@inertiajs/inertia-vue3'
import route from 'ziggy';

exports.handler = async function (event) {
    return await createInertiaApp({
        page: event,
        render: renderToString,
        resolve: (name) => require(`./Pages/${name}`),
        setup({app, props, plugin}) {
            const Ziggy = {
                ...event.props.ziggy,
                location: new URL(event.props.ziggy.url)
            }

            return createSSRApp({
                render: () => h(app, props),
            }).use(plugin).mixin({
                methods: {
                    // Pull the ziggy config off of the event.
                    route: (name, params, absolute, config = Ziggy) => route(name, params, absolute, config),
                },
            })
        },
    });
}
