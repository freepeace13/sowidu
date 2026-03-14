import { AuthorizationService } from '../api';
import { camelCase } from 'lodash';

function bind(el, binding, vNode, oldVNode) {
    el.style.display = 'none';

    componentUpdated(el, binding, vNode, oldVNode);
}

function componentUpdated(el, binding, vNode, oldVNode) {
    let { arg, modifiers, value } = binding;

    arg = camelCase(arg);

    if (typeof AuthorizationService[arg] === 'undefined') {
        arg = 'can';
    }

    AuthorizationService[arg](value)
        .then(() => el.style.display = '')
        .catch(() => el.style.display = 'none');
}

export default {
    bind,
    componentUpdated
}