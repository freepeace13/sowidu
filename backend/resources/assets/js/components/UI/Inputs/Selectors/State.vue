<template>
    <div class="text-field">
        <h4 class="font-weight-bold mb-1 text-uppercase" v-if="label" v-html="label"></h4>
        <v-autocomplete
            v-model="selected"
            :items="states"
            :search-input.sync="search"
            hide-selected
            clearable
            item-text="name"
            item-value="id"
            return-object
            solo
            :error="errors.length > 0"
            :error-messages="errors"
            ref="autocomplete"
        >
            <template slot="no-data">
                <v-btn slot="activator"
                    color="primary"
                    dark
                    v-if="country"
                    @click="createState(country.id, search)"
                    block
                >
                    Create State
                </v-btn>

                <v-alert :value="!country">Country is not selected.</v-alert>
            </template>
        </v-autocomplete>
    </div>
</template>


<script>
import { UsesMiscStore } from '~/components/Mixins';
import Cache from '~/support/Cache';
import { isNullOrUndefined } from '~/support/helpers';

export default {
    name: 'StateSelector',

    mixins: [UsesMiscStore()],

    props: {
        errors: {
            type: Array,
            default: () => ([])
        },

        value: {
            type: Object,
            required: true,
            validator(prop) {
                return typeof(prop.id) === 'number' || prop.id === null;
            }
        },

        label: {
            type: String,
            default: 'Choose State'
        },

        country: {
            type: Object,
            required: true,
            validator: (prop) => {
                return typeof(prop.id) === 'number' || prop.id === null;
            }
        }
    },

    data: () => ({
        search: null,
    }),

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
    },

    created() {
        this.$cache = new Cache;
    },

    watch: {
        country: {
            immediate: true,
            handler(country) {
                if (! this.$cache) {
                    this.$cache = new Cache;
                }

                this.$nextTick(() => {
                    console.log(this.country)
                });

                if (isNullOrUndefined(country.id))  {
                    this.states = [];
                    this.selected = null;
                }

                if (country.id && this.$cache.get('country', null) !== country.id) {
                    this.getStates(country.id);
                }

                this.$cache.set('country', country.id);
            }
        }
    }
}
</script>

<style lang="scss" scoped>
    /deep/ .v-text-field__details {
        padding: 0px !important;
    }
</style>
