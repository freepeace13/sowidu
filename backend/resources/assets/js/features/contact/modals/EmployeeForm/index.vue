<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-subheader class="px-4">{{ $t('headings.basic-information') }}</v-subheader>

        <v-container grid-list-lg fluid>
            <v-select
                v-if="!$store.getters['auth/check']('company') && !employee.exists()"
                :placeholder="$t('hints.choose-company')"
                :items="companies"
                v-model="employee.employer"
                item-text="name"
                item-value="id"
                return-object
                :error-messages="$contacts.$errors.get('company_id', [])"
                :error="$contacts.$errors.has('company_id')"
                solo
            />

            <SpecializationSelector
                :disabled="employee.exists()"
                :placeholder="$t('labels.inputs.specialization')"
                v-model="employee.$refs.specialization"
                :errors="$contacts.$errors.get('specialization', [])"
            />

            <v-layout row>
                <v-flex xs6>
                    <TextField
                        :label="$t('labels.inputs.firstname')"
                        v-model="employee.firstName"
                        :errors="$contacts.$errors.get('first_name', [])"
                    />
                </v-flex>
                <v-flex xs6>
                    <TextField
                        :label="$t('labels.inputs.lastname')"
                        v-model="employee.lastName"
                        :errors="$contacts.$errors.get('last_name', [])"
                    />
                </v-flex>
            </v-layout>

            <TextField
                :label="$t('labels.inputs.email')"
                type="email"
                v-model="employee.email"
                :errors="$contacts.$errors.get('email', [])"
                solo
            />
        </v-container>

        <v-divider></v-divider>
        <v-subheader class="px-4">{{ $t('headings.address-details') }}</v-subheader>

        <v-container grid-list-lg fluid>
            <v-layout row>
                <v-flex xs3 class="pr-1">
                    <HouseNumberSelector
                        v-model="employee.address.$refs.houseNumber"
                        :errors="$contacts.$errors.get('address.house_number')"
                        :label="$t('labels.inputs.house-no')"
                    />
                </v-flex>
                <v-flex xs9 class="pl-1">
                    <StreetSelector
                        v-model="employee.address.$refs.street"
                        :errors="$contacts.$errors.get('address.street')"
                        :label="$t('labels.inputs.street')"
                    />
                </v-flex>
            </v-layout>

            <CountrySelector
                v-model="employee.address.$refs.country"
                :errors="$contacts.$errors.get('address.country')"
                :label="$t('labels.inputs.country')"
            />

            <StateSelector
                :errors="$contacts.$errors.get('address.state')"
                v-model="employee.address.$refs.state"
                :country="employee.address.$refs.country"
                :label="$t('labels.inputs.state')"
            />

            <v-layout row>
                <v-flex xs6 class="pr-1">
                    <CitySelector
                        :state="employee.address.$refs.state"
                        :country="employee.address.$refs.country"
                        v-model="employee.address.$refs.city"
                        :errors="$contacts.$errors.get('address.city')"
                        :label="$t('labels.inputs.city')"
                    />
                </v-flex>
                <v-flex xs6 class="pl-1">
                    <ZipcodeSelector
                        v-model="employee.address.$refs.zipcode"
                        :errors="$contacts.$errors.get('address.zipcode')"
                        :label="$t('labels.inputs.zipcode')"
                    />
                </v-flex>
            </v-layout>
        </v-container>

        <template v-slot:actions>
            <v-btn
                color="primary"
                @click="save"
                :loading="$contacts.$loading"
                block
            >
                {{ employee.exists() ? $t('buttons.save-changes') : $t('buttons.create') }}
            </v-btn>

            <v-btn color="grey darken-3" block @click="$modal.close($vnode.key)">
                {{ $t('buttons.cancel') }}
            </v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import { Employee } from '~/services/models';
import EmployeeService from '~/services/EmployeeService';
import UsesContactStore from '../../mixins/UsesContactStore';
import { UsesAuthStore } from '~/components/Mixins';

export default {
    name: 'ContactEmployeeFormModal',

    mixins: [
        UsesContactStore(),
        UsesAuthStore()
    ],

    props: {
        employeeId: {
            validator(prop) {
                return prop === undefined || typeof(prop) === 'number';
            }
        }
    },

    data: () => ({
        employee: Employee.create({
            firstName: null,
            lastName: null,
            email: null,
            mobile: null,
            specialization: null
        }),
    }),

    methods: {
        async save() {
            if (this.employee.exists()) {
                await this.$contacts.update(this.employee);
            } else {
                await this.$contacts.create(this.employee);
            }

            this.$modal.close(this.$vnode.key);
        },
    },

    async created() {
        if (this.employeeId !== undefined) {
            this.employee = await EmployeeService.retrieve(this.employeeId);
        }
    }
}
</script>
