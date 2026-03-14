/** @flow */

import * as types from './constants';
import OrderService from '~/services/OrderService';
import { MessageBag } from '~/support/wrappers';
import { pick } from 'lodash';
import { camelKeys } from '~/support/helpers';
import ModelState from '~/services/models/fundamentals/state';

import {
    Order,
    Employee,
    Media,
    Task,
    Delivery,
    Item,
    Customer,
    resolveFromRaw
} from '~/services/models';

type SummaryPropTypes = {
    contractor: Authenticatable,
    customer: Customer,
    description: string,
    items: Array<Item>,
    formattedDates: {
        orderDate: string,
        deliveryDate: string,
    }
}

export default {
    namespaced: true,

    state: {
        original: Order.create({}),
        sheet: Order.create({}),
        errors: new MessageBag,
    },

    actions: {
        async initialize({ commit, dispatch }: Object, orderId: number): Promise<Order> {
            try {
                dispatch('wait/start', 'apploader', { root: true });
                const result: Order = await OrderService.retrieve(orderId);
                commit(types.SET_SHEET, result);
            } catch (error) {
                throw error;
            } finally {
                dispatch('wait/end', 'apploader', { root: true });
            }
        },

        async update({ dispatch, commit }: Object, instance: Order): Promise<void> {
            try {
                commit(types.SET_ERRORS, new MessageBag);
                const result: Order = await dispatch('order/update', instance, { root: true });
                commit(types.SET_SHEET, result);
            } catch (error) {
                commit(types.SET_ERRORS, error);
                throw error;
            }
        }
    },

    mutations: {
        [types.SET_SHEET] (state: Object, order: Order): void {
            state.sheet = Order.create(order);
            state.original = Order.create(order);
        },

        [types.SET_ERRORS] (state: Object, error: MessageBag): void {
            if (error instanceof MessageBag) {
                state.errors = error;
            }
        },

        [types.SUMMARY_UPDATE] (state: Object, props: SummaryPropTypes): void {
            const summary = pick(camelKeys(props), [
                'contractor',
                'customer',
                'description',
                'items',
                'formattedDates',
                'orderNumber'
            ]);

            if (summary.contractor) {
                summary.contractor = resolveFromRaw(summary.contractor);
            }

            if (summary.customer) {
                summary.customer = resolveFromRaw(summary.customer);
            }

            state.sheet = Order.create({
                ...state.sheet,
                ...summary,
                formattedDates: {
                    ...state.sheet.formattedDates,
                    ...summary.formattedDates
                }
            });
        },

        [types.STATE_UPDATE] (state: Object, value: ModelState): void {
            state.sheet.states.state.current = value;
        },

        [types.PROGRESS_UPDATE] (state: Object, progressState: Object): void {
            state.original = Order.create({
                ...state.original,
                ...progressState
            });
        },

        [types.MEMBERS_UPDATE] (state: Object, members: Array<Employee>): void {
            state.sheet.members = members.map((v) => Employee.create(v));
        },

        [types.TASKS_UPDATE] (state: Object, tasks: Array<Task>): void {
            state.sheet.tasks = tasks.map((v) => Task.create(v));;
        },

        [types.INSERT_TASK] (state: Object, task: Task): void {
            state.sheet.tasks = Task
                .collection(state.sheet.tasks)
                .insert(task)
                .all();
        },

        [types.INSERT_DELIVERY] (state: Object, delivery: Delivery): void {
            state.sheet.deliveries = Delivery
                .collection(state.sheet.deliveries)
                .insert(delivery)
                .all();
        },

        [types.MEDIA_UPDATE] (state: Object, media: Array<Media>): void {
            state.sheet.media = media.map((v) => Media.create(v));;
        },

        [types.DELIVERIES_UPDATE] (state: Object, deliveries: Array<Delivery>): void {
            state.sheet.deliveries = deliveries.map((v) => Delivery.create(v));;
        },

        [types.ITEMS_UPDATE] (state: Object, items: Array<Item>): void {
            state.sheet.items = items.map((v) => Item.create(v));
        }
    }
}