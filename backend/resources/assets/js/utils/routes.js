import router from '~/routes'

export const resolveRouteUrl = (routeName) => {
    return window.location.origin + router.resolve({
        name: routeName
    }).href
}
