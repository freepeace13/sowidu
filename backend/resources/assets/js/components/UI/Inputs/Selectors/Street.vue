<template>
    <div class="text-field">
        <h4 class="font-weight-bold mb-1 text-uppercase" v-if="label" v-html="label"></h4>
        <v-autocomplete
            v-model="selected"
            :items="streets"
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
                    @click="createStreet(search)"
                    block
                >
                    Create Street
                </v-btn>
            </template>
        </v-autocomplete>
    </div>
</template>


<script>
import { UsesMiscStore } from '~/components/Mixins';

export default {
    name: 'StreetSelector',

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
            default: 'Choose Street'
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
    }
}
</script>

<style lang="scss" scoped>
    /deep/ .v-text-field__details {
        padding: 0px !important;
    }
</style>
