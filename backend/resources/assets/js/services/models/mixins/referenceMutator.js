/** @flow */
import { hasKey, nullify } from '~/support/helpers';

const refkey = (v: string) => v + 'Id';

export default (Base: Function) => class ReferenceMutator extends Base {
    reference: Object = {};

    constructor(props: any) {
        super(props);
    }

    get $refs() {
        return new Proxy(this, {
            set(target: Object, prop: string, value: Object) {
                value = Object.assign({}, value);

                if (hasKey(target, prop) && hasKey(target.reference, refkey(prop))) {
                    target[prop] = nullify(value.name);
                    target['reference'][refkey(prop)] = nullify(value.id);
                }

                return true;
            },
            get(target: Object, prop: string) {
                if (hasKey(target, prop) && hasKey(target.reference, refkey(prop))) {
                    const qualifiedKey = target.reference[refkey(prop)];

                    return {
                        id: nullify(qualifiedKey) && +qualifiedKey,
                        name: nullify(target[prop])
                    }
                }
                
                return Reflect.get(...arguments);
            }
        });
    }
}