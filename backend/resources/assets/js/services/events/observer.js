import Vue from 'vue'
import { MODULES } from './constants'
import { mapValues } from 'lodash'
import EventHandler from './eventHandler';

export default class Observer {
    constructor({ modules, events }) {
        this._events = this._filterDuplicates(events)
        this.name = this._validateName(modules)
    }

    _filterDuplicates(events) {
        return events.filter((v, i, a) => a.indexOf(v) === i)
    }

    _validateName(name) {
        if (Object.values(MODULES).indexOf(name) === -1) {
            throw new Error(`Invalid module name ${name}.`)
        }

        return name
    }

    bindTo(terminal = null) {
        if (!(terminal instanceof Vue)) {
            throw new Error(`Invalid terminal instance bounded.`)
        }

        this._terminal = terminal

        return {
            ...this.mapEventObservers(),
            listen: this.listen.bind(this)
        }
    }

    mapEventObservers() {
        return this._events.reduce((observers, eventItem) => {
            if (eventItem instanceof EventHandler) {
                observers[eventItem.name] = eventItem.handler.bind(this);
            } else {
                observers[eventItem] = (args) => (
                    this._terminal.$emit(this.namespaceOf(eventItem), args)
                )
            }

            return observers
        }, {})
    }

    listen(eventName, listener) {
        if (!(listener instanceof Function)) {
            throw new Error(`Invalid terminal instance bounded.`)
        }

        if (this._events.indexOf(eventName) === -1) {
            throw new Error(`Unknown event name [${eventName}].`)
        }

        let namespace = this.namespaceOf(eventName)

        this._terminal.$off(namespace)
        this._terminal.$on(namespace, listener)
    }

    namespaceOf(fn) {
        return [this.name, fn].join('.')
    }
}
