<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-container grid-list-lg fluid>
            <AvatarCropper
                v-model="avatar"
                type="company"
                :errors="$contacts.$errors.get('avatar', [])"
                :initialImage="company.avatar.url"
            />
        </v-container>

        <v-subheader class="px-4">{{ $t('headings.basic-information') }}</v-subheader>

        <v-container grid-list-lg fluid>
            <TextField
                :label="$t('labels.inputs.name')"
                v-model="company.name"
                :errors="$contacts.$errors.get('name', [])"
            />

            <TextField
                :label="$t('labels.inputs.email')"
                v-model="company.founder.email"
                :errors="$contacts.$errors.get('email', [])"
            />

            <v-layout row>
                <v-flex xs6>
                    <LegalFormSelector
                        :disabled="company.exists()"
                        :label="$t('labels.inputs.legal-form')"
                        v-model="company.$refs.legalForm"
                        :errors="$contacts.$errors.get('legal_form_id', [])"
                    />
                </v-flex>
                <v-flex xs6>
                    <InstitutionTypeSelector
                        :disabled="company.exists()"
                        :label="$t('labels.inputs.institution-type')"
                        v-model="company.$refs.institutionType"
                        :errors="$contacts.$errors.get('institution_type_id', [])"
                    />
                </v-flex>
            </v-layout>
        </v-container>

        <v-divider></v-divider>
        <v-subheader class="px-4">{{ $t('headings.address-details') }}</v-subheader>

        <v-container grid-list-lg fluid>
            <v-layout row>
                <v-flex xs3 class="pr-1">
                    <HouseNumberSelector
                        v-model="company.address.$refs.houseNumber"
                        :errors="$contacts.$errors.get('address.house_number')"
                        :label="$t('labels.inputs.house-no')"
                    />
                </v-flex>
                <v-flex xs9 class="pl-1">
                    <StreetSelector
                        v-model="company.address.$refs.street"
                        :errors="$contacts.$errors.get('address.street')"
                        :label="$t('labels.inputs.street')"
                    />
                </v-flex>
            </v-layout>

            <CountrySelector
                v-model="company.address.$refs.country"
                :errors="$contacts.$errors.get('address.country')"
                :label="$t('labels.inputs.country')"
            />

            <StateSelector
                :errors="$contacts.$errors.get('address.state')"
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
                        :errors="$contacts.$errors.get('address.city')"
                        :label="$t('labels.inputs.city')"
                    />
                </v-flex>
                <v-flex xs6 class="pl-1">
                    <ZipcodeSelector
                        v-model="company.address.$refs.zipcode"
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
                {{ company.exists() ? $t('buttons.save-changes') : $t('buttons.create') }}
            </v-btn>

            <v-btn color="grey darken-3" block @click="$modal.close($vnode.key)">
                {{ $t('button.cancel') }}
            </v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import { Company } from '~/services/models';
import CompanyService from '~/services/CompanyService';
import UsesContactStore from '../../mixins/UsesContactStore';

export default {
    name: 'ContactCompanyFormModal',

    mixins: [UsesContactStore()],

    props: {
        companyId: {
            validator(prop) {
                return prop === undefined || typeof(prop) === 'number';
            }
        }
    },

    data: () => ({
        avatar: null,
        company: Company.create({
            name: null,
            institutionType: null,
            legalForm: null,
            founder: {
                email: null,
                mobile: null
            }
        })
    }),

    methods: {
        async save() {
            if (this.company.exists()) {
                await this.$contacts.update(this.company);
            } else {
                await this.$contacts.create(this.company);
            }

            this.$modal.close(this.$vnode.key);
        },
    },

    async created() {
        if (this.companyId !== undefined) {
            this.company = await CompanyService.retrieve(this.companyId);
        }
    }
}
</script>
