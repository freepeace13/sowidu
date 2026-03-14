import Decorator, { Decorate } from './decorator';

export const Enumerable = (value = true) => Decorator(
    function (target, property, descriptor) {
        descriptor.enumerable = value;
        return descriptor;
    }
);

export const ReadOnly = (value = true) => Decorator(
    function (target, property, descriptor) {
        descriptor.writable = !value;
        return descriptor;
    }
);

export const Configurable = (value = true) => Decorator(
    function (target, property, descriptor) {
        descriptor.configurable = value;
        return descriptor;
    }
);

export const Untouchable = () => Decorator(
    Decorate(
        ReadOnly(),
        Configurable(false),
        Enumerable(false)
    )
);