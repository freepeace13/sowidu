const Decorator = function(handlerFn) {
    if (!handlerFn instanceof Function) {
        throw new Error("invalid");
    }

    const wrapper = function(target, property, descriptor) {
        return handlerFn(target, property, descriptor);
    }

    wrapper.prototype = Decorator.prototype;
    
    return wrapper;
}

Decorator.isDecorator = function(wrapper) {
    return wrapper.prototype === Decorator.prototype;
}

export const Decorate = function(...decorators) {
    return function(target, property, descriptor) {
        decorators.forEach(fn => {
            if (! Decorator.isDecorator(fn)) {
                throw new Error("Invalid");
            }

            descriptor = fn(target, property, descriptor);
        });

        return descriptor;
    }
}

export default Decorator;