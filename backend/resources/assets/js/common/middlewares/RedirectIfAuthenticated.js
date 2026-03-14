

export default (store) => function (to, from, next) {
    if (store.getters['auth/check']()) {
        next({ name: 'desktop' });
    } else {
        next();
    }
}