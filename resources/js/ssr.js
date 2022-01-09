import {createSSRApp, h} from 'vue'
import {renderToString} from '@vue/server-renderer'
import {createInertiaApp} from '@inertiajs/inertia-vue3'
import route from 'ziggy';

exports.handler = async function (event) {
    // This is the file that Sidecar has compiled for us if
    // this application uses Ziggy. We import it using
    // this syntax since it may not exist at all.
    const compiledZiggy = await import('./compiledZiggy');

    return await createInertiaApp({
        page: event,
        render: renderToString,
        resolve: (name) => require(`./Pages/${name}`),
        setup({app, props, plugin}) {
            const Ziggy = {
                // Start with the stuff that may be baked into this Lambda.
                ...(compiledZiggy || {}),

                // Then if they passed anything to us via the event,
                // overwrite everything that was baked in.
                ...event?.props?.ziggy,
            }

            // Construct a new location, since window.location is not available.
            Ziggy.location = new URL(Ziggy.url)

            return createSSRApp({
                render: () => h(app, props),
            }).use(plugin).mixin({
                methods: {
                    // Use our custom Ziggy object as the config.
                    route: (name, params, absolute, config = Ziggy) => route(name, params, absolute, config),
                },
            })
        },
    });
}
