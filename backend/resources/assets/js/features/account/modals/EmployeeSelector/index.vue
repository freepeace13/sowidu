<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-container grid-list-lg fluid class="py-0">
            <v-list>
                <v-list-tile
                    v-for="employee in employees"
                    :key="employee.id"
                    :class="{ 'grey darken-3': isSelected(employee) }"
                    @click="toggleSelection(employee)"
                >
                    <v-list-tile-avatar>
                        <v-img :src="employee.avatar.url" :lazy-src="employee.avatar.url" />
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ employee.name }}</v-list-tile-title>
                        <v-list-tile-sub-title>
                            {{ employee.specialization }} &middot; {{ employee.email }}
                        </v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-container>

        <template v-slot:actions>
            <v-btn
                color="primary" block
                @click="handleSelect()"
            >
                Select ({{ choosen.length }})
            </v-btn>

            <v-btn
                color="grey darken-3" block
                @click="$modal.close($vnode.key)"
            >
                Close
            </v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import EmployeeTableItem from '~/components/dataTables/EmployeeTableItem';
import Employee from '~/services/models/employee';
import { Response } from '~/services/events/modal'
import UsesEmployeeStore from '~/components/Mixins/UsesEmployeeStore';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

export default {
    name: 'EmployeeSelectorModal',

    mixins: [
        UsesEmployeeStore(),
        DispatchWhenTokenChanges('employee/all')
    ],

    components: { EmployeeTableItem },

    props: {
        selected: {
            type: Array,
            default: () => ([])
        },

        onSelect: {
            type: Function,
            required: true
        }
    },

    data: () => ({
        choosen: []
    }),

    methods: {
        handleSelect() {
            this.onSelect(new Response(this, this.choosen));
        },

        toggleSelection(employee) {
            if (this.isSelected(employee)) {
                this.choosen.splice(this.findIndex(employee), 1);
            } else {
                this.choosen.push(employee);
            }
        },

        findIndex(employee) {
            return Employee
                .collection(this.choosen)
                .findIndex(employee);
        },

        isSelected(employee) {
            return Employee
                .collection(this.choosen)
                .includes(employee);
        }
    },

    created() {
        this.choosen = [...this.selected];
    }
}
</script>
