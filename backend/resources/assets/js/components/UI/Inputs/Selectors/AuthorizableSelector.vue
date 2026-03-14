<template>
    <div class="authorizable-selector-input">
        <h4 class="font-weight-bold mb-1 text-uppercase" v-if="$attrs.label" v-html="$attrs.label"></h4>
        <v-autocomplete
            chips
            v-model="selected"
            v-bind="$attrs"
            :label="null"
            :loading="$rm.$loading"
            :readonly="readonly"
            :items="$rm.$result || []"
            hide-selected
            :item-value="(item) => item"
            :error="errors.length > 0"
            :error-messages="errors"
            :item-text="getItemText"
            :menu-props="{ closeOnContentClick: true }"
            :value-comparator="compareValue"
            solo
        >
            <template slot="selection" slot-scope="{ item, selected }">
                <v-chip :selected="selected" color="green darken-3">
                    <v-avatar><v-img :src="item.avatar.url" :alt="item.name" /></v-avatar>
                    {{ item.name }}
                    <template v-if="item.employer">
                        ({{ item.specialization }} of {{ item.employer.name }})
                    </template>
                </v-chip>
            </template>

            <template slot="item" slot-scope="{ item }">
                <v-list-tile-avatar
                    color="indigo"
                    class="headline font-weight-light white--text"
                >
                    <v-img :src="item.avatar.url" alt="" />
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title>
                        {{ item.name }} &middot;
                        <v-icon v-if="item.entity === 'users'" small>person</v-icon>
                        <v-icon v-else small>group</v-icon>
                    </v-list-tile-title>
                    <v-list-tile-sub-title>
                        <template v-if="item.entity === 'users'">
                            <span>{{ item.email }}</span>
                        </template>
                        <template v-else>
                            <span>{{ item.specialization }}</span>
                            &middot;
                            <span>{{ item.employer.name | capitalize }}</span>
                        </template>
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </template>
        </v-autocomplete>
    </div>
</template>

<script>
import { collect } from '~/support/wrappers/Collection';
import User from '~/services/models/user';
import Employee from '~/services/models/employee';
import UserService from '~/services/UserService';
import { createContext } from '~/support/factories';
import EmployeeService from '~/services/EmployeeService';
import { isObject } from '~/support/helpers';
import HandlesAuthorizations from '~/components/Mixins/HandlesAuthorizations';
import { createResource } from 'vue-async-manager';

export default {
    name: 'AuthorizableSelector',

    mixins: [HandlesAuthorizations()],

    props: {
        value: {
            type: [Employee, User, Array],
            required: true
        },

        errors: {
            type: Array,
            default: () => ([])
        },

        readonly: {
            type: Boolean,
            default: false
        },
    },

    data: () => ({
        selections: [],
    }),

    computed: {
        selected: {
            get() { return this.value; },
            set(value) {
                this.$emit('input', value);
                this.$emit('change', value);
            },
        }
    },

    methods: {
        getItemText(item) {
            let prefix = `${item.name} ${item.entity}`;
            return prefix + (item.employer ? ` ${item.employer.name}` : '');
        },

        compareValue(a, b) {
            return (isObject(a) && isObject(b)) ? b.equals(a) : false;
        }
    },

    created() {
        this.$rm = createResource(async () => {
            const [employees, users] = await Promise.all([
                EmployeeService.all(),
                UserService.all()
            ]);

            return [...employees, ...users];
        });

        this.$rm.read();
    },
}
</script>
