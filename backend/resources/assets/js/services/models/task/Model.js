/** @flow */

import Model from '~/support/wrappers/Model';
import type { PropTypes } from 'task-prop-types';
import createStates from '../mixins/hasStates';
import { camelKeys, arrwrap } from '~/support/helpers';
import { ModelCollection as Collection } from '~/support/wrappers';
import { Delivery, Employee, resolveFromRaw, Order, Media } from '..';
import { ProgressState } from './states';
import TaskPolicies from './Policies';
import HasComments from '../mixins/hasComments';

const HasStates = createStates({
    state: new ProgressState
});

export default class Task extends HasStates(HasComments(Model)) {
    title: ?string;
    description: ?string;
    members: Array<Employee> = [];
    deliveries: Array<Delivery> = [];
    media: Array<Media> = [];
    orders: Array<Order> = [];
    creator: Authorizable;
    formattedDates: {
        dateCreated: ?string,
        timeCreated: ?string,
        startedAt: ?string,
        endedAt: ?string
    } = {
        dateCreated: null,
        timeCreated: null,
        startedAt: null,
        endedAt: null
    };

    constructor(props: PropTypes) {
        super(props);

        this.title = props.title;
        this.description = props.description;
        this.members = props.members;
        this.media = props.media;
        this.orders = props.orders;
        this.deliveries = props.deliveries;
        this.creator = props.creator;

        this.formattedDates = Object.assign(
            this.formattedDates,
            props.formattedDates
        );
    }

    get policies(): TaskPolicies {
        return new TaskPolicies(this);
    }

    toString(): string {
        return ['Task - ', this.title].join(' ');
    }

    hasMember(v: Authorizable): bool {
        return this.members.some((member) => member.equals(v));
    }

    static create(attrs: Object): Task {
        const props: PropTypes = camelKeys(attrs);
        const members = arrwrap(props.members);
        const media = arrwrap(props.media);
        const orders = arrwrap(props.orders);
        const deliveries = arrwrap(props.deliveries);

        return new Task({
            ...props,
            creator: props.creator && resolveFromRaw(props.creator),
            members: members.map((v) => resolveFromRaw(v)),
            media: media.map((v) => Media.create(v)),
            deliveries: deliveries.map((v) => Delivery.create(v)),
            orders: orders.map((v) => Order.create(v)),
        });
    }

    static collection(collection: Array<Object>): Collection<Task> {
        return new Collection(collection.map((v) => Task.create(v)));
    }
}
