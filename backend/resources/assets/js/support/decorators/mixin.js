

const Mixin = (...mixins) => (Constructor) => {
    mixins.forEach((mixin) => {
        for (let [ key, value ] of mixin) {
            Object.defineProperty(Constructor.prototype, key, { value });
        }
    });
}