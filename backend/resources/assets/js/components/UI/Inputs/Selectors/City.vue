<template>
    <div class="text-field">
        <h4 class="font-weight-bold mb-1 text-uppercase" v-if="label" v-html="label"></h4>
        <v-autocomplete
            v-model="selected"
            :items="cities"
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
                    v-if="country && state"
                    @click="createCity(country.id, state.id, search)"
                    block
                >
                    Create City
                </v-btn>

                <v-alert :value="!country">Country is not selected.</v-alert>
                <v-alert :value="!state && country">State is not selected.</v-alert>
            </template>
        </v-autocomplete>
    </div>
</template>

<script>
import { UsesMiscStore } from '~/components/Mixins';
import Cache from '~/support/Cache';
import { isNullOrUndefined } from '~/support/helpers';

export default {
    name: 'CitySelector',

    mixins: [UsesMiscStore()],

    props: {
        errors: {
            type: Array,
            default: () => ([])
        },

        value: {
            required: true
        },

        label: {
            type: String,
            default: 'Choose City'
        },

        country: {
            type: Object,
            required: true,
            validator: (prop) => {
                return typeof(prop.id) === 'number' || prop.id === null;
            }
        },

        state: {
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
        state: {
            immediate: true,
            handler() {
                if (! this.$cache) {
                    this.$cache = new Cache;
                }

                if (isNullOrUndefined(this.country.id) || isNullOrUndefined(this.state.id)) {
                    this.cities = [];
                    this.selected = null;
                }

                if (this.state.id && this.$cache.get('state', null) !== this.state.id) {
                    this.getCities(this.country.id, this.state.id);
                }
                
                this.$cache.set('state', this.state.id);
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
