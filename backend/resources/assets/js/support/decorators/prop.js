import Decorator from './decorator';
import InvalidPropertyType from '~/exceptions/InvalidPropertyType';
import { isFunction, isArray } from '../helpers';

const Prop = function(...rules) {
    if (rules.some(e => !isFunction(e))) {
        throw new Error('Invalid rules argument pass.');
    }
    
    return Decorator(function(target, property, descriptor) {
        let propValue;

        if (descriptor.initializer && isFunction(descriptor.initializer)) {
            propValue = descriptor.initializer();
        }

        const targetProp = Reflect.getOwnPropertyDescriptor(target, property);

        Object.defineProperty(target, property, {
            configurable: true,
            enumerable: true,
            set: function(value) {
                if (targetProp && isFunction(targetProp.set)) {
                    value = targetProp.set.call(this, value);
                }

                if (! Prop.validate(rules, value)) {
                    throw InvalidPropertyType.rules(rules, property, value);
                }

                return propValue = value;
            },
            get: function() {
                if (targetProp && isFunction(targetProp.get)) {
                    return targetProp.get.call(this);
                }

                return propValue;
            }
        });
    });
}

Prop.validate = function (rules, value) {
    if (isArray(rules)) {
        return rules.some(rule => rule(value));
    }

    else if (isFunction(rules)) {
        return rules(value);
    }

    return false;
}

export default Prop;