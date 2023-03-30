const moduleDivider = '::';

export async function resolvePageComponent(path) {
    const [root, child] = path.split(moduleDivider)

    const pagePath = child
        ? `/Modules/${root}/resources/js/Pages/${child}.vue`
        : `/Modules/Jetstream/resources/js/Pages/${root}.vue`;

    const pages = child
        ? import.meta.glob('Modules/*/resources/js/Pages/**/*.vue')
        : import.meta.glob('Modules/Jetstream/resources/js/Pages/**/*.vue')

    const page = pages[pagePath];
    if (typeof page === 'undefined') {
        throw new Error(`Page not found: ${pagePath}`);
    }
    return typeof page === 'function' ? page() : page;
}
