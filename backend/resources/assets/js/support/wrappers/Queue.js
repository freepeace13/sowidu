/** @flow */

import { uniqueId } from 'lodash';

type PropTypes = {
    key?: ?string,
    processed?: ?number,
    action: Function,
    once?: ?boolean,
    options: ?{
        sleep: number
    }
}

const defaultOptions = {
    sleep: 0
}

export default class Queue {
    key: string;
    action: Function;
    processed: number = 0;
    once: boolean = false;
    options: ?{ sleep: number } = {};

    constructor(props: PropTypes): void {
        this.key = props.key || uniqueId('queue_');
        this.processed = props.processed || 0;
        this.action = props.action;
        this.once = props.once || false;
        this.options = { ...defaultOptions, ...props.options };
    }

    exceeded(v: number): boolean {
        return this.processed > v;
    }

    incrementProcess(): this {
        this.processed += 1;
        return this;
    }

    static create(props: PropTypes): Queue {
        return new Queue(props);
    }

    static action(v: Function, options: Object = {}): Queue {
        return Queue.create({ action: v, options });
    }
}