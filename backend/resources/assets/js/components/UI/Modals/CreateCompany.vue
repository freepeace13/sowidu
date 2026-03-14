<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-subheader class="px-4">{{ $t('headings.basic-information') }}</v-subheader>

        <v-container fluid grid-list-lg>
            <TextField
                :label="$t('labels.inputs.name')"
                :errors="$auth.$errors.get('name', [])"
                v-model="company.name"
            />

            <v-layout row>
                <v-flex xs6>
                    <InstitutionTypeSelector
                        :label="$t('labels.inputs.institution-type')"
                        v-model="company.$refs.institutionType"
                        :errors="$auth.$errors.get('institution_type', [])"
                    />
                </v-flex>
                <v-flex xs6>
                    <LegalFormSelector
                        :label="$t('labels.inputs.legal-form')"
                        v-model="company.$refs.legalForm"
                        :errors="$auth.$errors.get('legal_form_id', [])"
                    />
                </v-flex>
            </v-layout>
        </v-container>

        <v-divider></v-divider>
        <v-subheader class="px-4">{{ $t('headings.address-details') }}</v-subheader>

        <v-container fluid grid-list-lg class="pb-5">
            <v-layout row>
                <v-flex xs3 class="pr-1">
                    <HouseNumberSelector
                        v-model="company.address.$refs.houseNumber"
                        :errors="$auth.$errors.get('address.house_number')"
                        :label="$t('labels.inputs.house-no')"
                    />
                </v-flex>
                <v-flex xs9 class="pl-1">
                    <StreetSelector
                        v-model="company.address.$refs.street"
                        :errors="$auth.$errors.get('address.street')"
                        :label="$t('labels.inputs.street')"
                    />
                </v-flex>
            </v-layout>

            <CountrySelector
                v-model="company.address.$refs.country"
                :errors="$auth.$errors.get('address.country')"
                :label="$t('labels.inputs.country')"
            />

            <StateSelector
                :errors="$auth.$errors.get('address.state')"
                v-model="company.address.$refs.state"
                :country="company.address.$refs.country"
                :label="$t('labels.inputs.state')"
            />

            <v-layout row>
                <v-flex xs6 class="pr-1">
                    <CitySelector
                        :state="company.address.$refs.state"
                        :country="company.address.$refs.country"
                        v-model="company.address.$refs.city"
                        :errors="$auth.$errors.get('address.city')"
                        :label="$t('labels.inputs.city')"
                    />
                </v-flex>
                <v-flex xs6 class="pl-1">
                    <ZipcodeSelector
                        v-model="company.address.$refs.zipcode"
                        :errors="$auth.$errors.get('address.zipcode')"
                        :label="$t('labels.inputs.zipcode')"
                    />
                </v-flex>
            </v-layout>

            <v-divider></v-divider>

            <v-checkbox
                v-model="autoLogin"
                hide-details
                :label="$t('hints.inputs.login-me-immediately')"
            />

            <v-checkbox
                v-model="accepted"
                :error-messages="$auth.$errors.get('terms_accepted', [])"
                :error="$auth.$errors.has('terms_accepted')"
                :label="$t('hints.inputs.terms-agreement')"
                hide-details
            />
        </v-container>

        <template v-slot:actions>
            <v-btn
                color="primary"
                block
                @click="submit"
                :loading="$auth.$loading"
                :disabled="!accepted"
            >
                {{ $t('buttons.create-company') }}
            </v-btn>

            <v-btn color="grey darken-3" block @click="$modal.close($vnode.key)">
                {{ $t('buttons.cancel') }}
            </v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import { Company } from '~/services/models';
import { HandlesAuthentications } from '~/components/Mixins';

export default {
    mixins: [HandlesAuthentications()],

    data: () => ({
        company: Company.create({
            name: null,
            institutionType: null,
            legalForm: null,
            address: {
                street: null,
                houseNumber: null,
                city: null,
                state: null,
                country: null,
                zipcode: null
            }
        }),
        accepted: false,
        autoLogin: true,
    }),

    methods: {
        async submit() {
            await this.$auth.createCompany({
                company: this.company,
                autoLogin: this.autoLogin
            });

            this.$modal.close(this.$vnode.key);
        }
    }
}
</script>
