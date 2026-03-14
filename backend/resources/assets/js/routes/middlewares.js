import store from '~/services/store';

const AuthResolver = (route) => Guard(route, (to, from, next) => {
    if (! store.getters['auth/check']()) {
        next({ name: 'auth.login' });
    } else {
        next();
    }
})

const GuestResolver = (route) => Guard(route, (to, from, next) => {
    if (store.getters['auth/check']()) {
        next({ name: 'desktop' });
    } else {
        next();
    }
})

/**
 * Redirect to login if guest.
 *
 * @param  {Array} routes
 * @return {Array}
 */
export const auth = (routes) => {
    const factory = (route) => ({
        ...route,
        beforeEnter: AuthResolver(route),
    });

    return Array.isArray(routes) ? routes.map(factory) : factory(routes);
}


/**
 * Redirect home if authenticated.
 *
 * @param  {Array} routes
 * @return {Array}
 */
export const guest = (routes) => {
    const factory = (route) => ({
        ...route,
        beforeEnter: GuestResolver(route),
    });

    return Array.isArray(routes) ? routes.map(factory) : factory(routes);
}

function multiple(guards) {
    return (to, from, next) => {
        const stack = [].concat(guards)
        function another(args) {
            const guard = stack.pop()
            guard ? guard(to, from, another) : next(args)
        }
        another()
    }
}


function Guard(route, beforeEnter) {
    return multiple(
        [route.beforeEnter].concat(beforeEnter)
    )
}
