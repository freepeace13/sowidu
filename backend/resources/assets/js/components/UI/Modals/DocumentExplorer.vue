<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-container grid-list-lg fluid fill-height>
            <v-layout row wrap fill-height>
                <v-flex xs12 v-if="$rm.$loading" class="text-xs-center" align-self-center>
                    <Loader />
                </v-flex>
                <v-flex xs2 v-for="(item, i) in computedResult" :key="i" v-else>
                    <DocumentThumbnail
                        :attachable="item"
                        :label="item.toString()"
                        :selected="getItemIndex(item) !== -1"
                        @toggle-selection="toggleSelection"
                    />
                </v-flex>
            </v-layout>
        </v-container>

        <template v-slot:actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="onSelectFiles">Select File(s)</v-btn>
            <v-btn color="grey darken-3" @click="$modal.close($vnode.key)">Close</v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import axios from 'axios'
import { fetchTasks } from '~/services/api/task';
import { createResource } from 'vue-async-manager'
import { Response } from '~/services/events/modal'
import Collection from '~/services/collections/attachables'
import Loader from '~/components/UI/Loader';

export default {
    name: 'DocumentExplorer',

    components: {
        Loader,
    },

    props: {
        selected: {
            type: Array,
            required: true
        },

        filter: {
            validator(prop) {
                return prop === null
                    || prop === undefined
                    || typeof(prop) === 'function';
            }
        }
    },

    data: () => ({
        values: [],
    }),

    computed: {
        computedResult() {
            if (typeof(this.filter) === 'function' && this.$rm.$result) {
                return this.$rm.$result.filter(this.filter); 
            }
            
            return this.$rm.$result;
        }
    },

    methods: {
        onSelectFiles() {
            this.$emit('onSelect', new Response(
                this, this.values
            ))
        },

        getItemIndex(item) {
            return this.values.findIndex(({ id, docType }) => {
                return id === item.id && docType === item.docType
            });
        },

        toggleSelection(item) {
            if (this.getItemIndex(item) === -1) {
                this.values.push(item);
            } else {
                this.values.splice(this.getItemIndex(item), 1);
            }
        }
    },

    created() {
        this.$rm = createResource(async () => {
            // const [tasks, deliveries, orders] = await Promise.all([
            //     fetchTasks(),
            //     fetchOrders(),
            //     fetchDeliveries()
            // ]);

            // return tasks.data.data
            //     .concat(deliveries.data.data)
            //     .concat(orders.data.data);
        })

        this.$rm.read();
        this.values = [...this.selected];
    }
}
</script>
