

export default (store) => function (to, from, next) {
    if (! store.getters['auth/check']()) {
        next({ name: 'auth.login' });
    } else {
        next();
    }
}