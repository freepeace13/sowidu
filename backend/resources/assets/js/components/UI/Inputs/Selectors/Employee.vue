<template>
    <div class="employee-selector-input">
        <h4 class="font-weight-bold mb-1 text-uppercase" v-if="$attrs.label" v-html="$attrs.label"></h4>
        <v-autocomplete
            chips
            v-model="selected"
            v-bind="$attrs"
            :label="null"
            :readonly="readonly"
            :items="employees"
            item-value="id"
            hide-selected
            return-object
            :error="errors.length > 0"
            :error-messages="errors"
            solo
        >
            <template slot="selection" slot-scope="{ item, selected }">
                <v-chip :selected="selected" color="green darken-3">
                    <v-avatar>
                        <v-img :src="item.avatar.url" :alt="item.name" />
                    </v-avatar>
                    {{ item.name }}
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
                        {{ item.name }}
                    </v-list-tile-title>
                    <v-list-tile-sub-title>
                        {{ item.specialization }} &middot; {{ item.email }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </template>
        </v-autocomplete>
    </div>
</template>

<script>
import UsesEmployeeStore from '~/components/Mixins/UsesEmployeeStore';

export default {
    name: 'EmployeeSelector',

    mixins: [UsesEmployeeStore()],

    props: {
        includes: {
            type: String,
            default: ''
        },

        value: {
            type: [Number, Array],
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

    computed: {
        selected: {
            set(value) {
                this.$emit('input', value);
                this.$emit('change', value);
            },
            get() {
                return this.value;
            }
        }
    }
}
</script>
