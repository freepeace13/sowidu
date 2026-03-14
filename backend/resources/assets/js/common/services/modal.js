import Vue from 'vue'
import Str from '@common/utils/Str';

const $ref = Vue.observable({
    items: []
});

function get(identifier) {
    return $ref.items.find((item) => item.identifier === identifier);
}

function head() {
    return ($ref.items.length)
        ? $ref.items[$ref.items.length - 1]
        : null;
}

function tail() {
    return ($ref.items.length) ? $ref.items[0] : null;
}

function has(item) {
    return position(item) !== -1;
}

function position(item) {
    return $ref.items.findIndex((entry) => {
        return entry.identifier === item.identifier
    });
}

function remove(item) {
    if (has(item)) {
        $ref.items.splice(position(item), 1);
    }
}

export class ModalConfig {
    constructor(options) {
        options = Object.assign({
            component: null,
            title: null,
            fullscreen: false,
            size: ModalManager.sizes.MEDIUM,
        }, options);

        this.props = {};
        this.callback = (event) => {};

        this.identifier = null;
        this.show = false;
        this.position = -1;
        this.component = options.component;
        this.title = options.title;
        this.props = options.props;
        this.size = options.size;
        this.fullscreen = options.fullscreen;
    }
}

export class ModalManager {
    static sizes = {
        XSMALL: 'xs',
        SMALL: 'sm',
        MEDIUM: 'md',
        LARGE: 'lg',
        XLARGE: 'xl'
    }

    static modals = {};

    

    get items() {
        return $ref.items;
    }

    set items(items) {
        $ref.items = items;
    }

    register(key, options) {
        ModalManager.modals[identifier] = new ModalConfig(options);
    }
    
    show(key, props) {
        if (!(key in ModalManager.modals)) {
            throw new Error(`unkown modal key ${key}`);
        }

        const item = ModalManager.modals[key];

        return (callback) => {
            item.show = true;
            item.identifier = Str.random(5);
            item.props = props;

            item.callback = (typeof callback !== 'function')
                ? (event) => {}
                : callback;

            this.items = [item];

            item.position = position(item);

            return item;
        }
    }

    append(key, props) {
        if (!(key in ModalManager.modals)) {
            throw new Error(`unkown modal key ${key}`);
        }

        this.items = this.items.map((entry) => {
            return Object.assign(entry, { show: false });
        });

        const item = ModalManager.modals[key];

        return (callback) => {
            item.show = true;
            item.identifier = Str.random(5);
            item.props = Object.assign(item.props, props);

            item.callback = (typeof callback !== 'function')
                ? (event) => {}
                : callback;

            this.items.push(item);

            item.position = this.position(item);

            return item;
        }
    }
}

ModalManager.prototype.get = get;
ModalManager.prototype.head = head;
ModalManager.prototype.tail = tail;
ModalManager.prototype.has = has;
ModalManager.prototype.position = position;
ModalManager.prototype.remove = remove;

export default new ModalManager;