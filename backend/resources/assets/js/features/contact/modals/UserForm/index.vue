<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-container grid-list-lg fluid>
            <AvatarCropper
                v-model="avatar"
                type="user"
                :errors="$contacts.$errors.get('avatar', [])"
                :initialImage="user.avatar.url"
            />
        </v-container>

        <v-subheader class="px-4">{{ $t('headings.user-information') }}</v-subheader>

        <v-container grid-list-lg fluid>
            <v-layout row>
                <v-flex xs6>
                    <TextField
                        :label="$t('labels.inputs.firstname')"
                        v-model="user.firstName"
                        :errors="$contacts.$errors.get('first_name', [])"
                    />
                </v-flex>
                <v-flex xs6>
                    <TextField
                        :label="$t('labels.inputs.lastname')"
                        v-model="user.lastName"
                        :errors="$contacts.$errors.get('last_name', [])"
                    />
                </v-flex>
            </v-layout>
        </v-container>

        <v-divider></v-divider>
        <v-subheader class="px-4">{{ $t('headings.contact-details') }}</v-subheader>

        <v-container grid-list-lg fluid>
            <v-layout row wrap>
                <v-flex xs6>
                    <TextField
                        :label="$t('labels.inputs.fax')"
                        v-model="user.fax"
                        :errors="[]"
                    />
                </v-flex>
                <v-flex xs6>
                    <TextField
                        :label="$t('labels.inputs.landline')"
                        v-model="user.landline"
                        :errors="[]"
                    />
                </v-flex>
                <v-flex xs6>
                    <TextField
                        :label="$t('labels.inputs.mobile')"
                        mask="(###) #### - #####"
                        v-model="user.mobile"
                        :errors="$contacts.$errors.get('mobile', [])">
                    </TextField>
                </v-flex>
                <v-flex xs6>
                    <TextField
                        :label="$t('labels.inputs.email')"
                        type="email"
                        v-model="user.email"
                        :errors="$contacts.$errors.get('email', [])"
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
                        v-model="user.address.$refs.houseNumber"
                        :errors="$contacts.$errors.get('address.house_number')"
                        :label="$t('labels.inputs.house-no')"
                    />
                </v-flex>
                <v-flex xs9 class="pl-1">
                    <StreetSelector
                        v-model="user.address.$refs.street"
                        :errors="$contacts.$errors.get('address.street')"
                        :label="$t('labels.inputs.street')"
                    />
                </v-flex>
            </v-layout>

            <CountrySelector
                v-model="user.address.$refs.country"
                :errors="$contacts.$errors.get('address.country')"
                :label="$t('labels.inputs.country')"
            />

            <StateSelector
                :errors="$contacts.$errors.get('address.state')"
                v-model="user.address.$refs.state"
                :country="user.address.$refs.country"
                :label="$t('labels.inputs.state')"
            />

            <v-layout row>
                <v-flex xs6 class="pr-1">
                    <CitySelector
                        :state="user.address.$refs.state"
                        :country="user.address.$refs.country"
                        v-model="user.address.$refs.city"
                        :errors="$contacts.$errors.get('address.city')"
                        :label="$t('labels.inputs.city')"
                    />
                </v-flex>
                <v-flex xs6 class="pl-1">
                    <ZipcodeSelector
                        v-model="user.address.$refs.zipcode"
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
                {{ user.exists() ? $t('buttons.save-changes') : $t('buttons.create') }}
            </v-btn>

            <v-btn color="grey darken-3" block @click="$modal.close($vnode.key)">
                {{ $t('buttons.cancel') }}
            </v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import { User } from '~/services/models';
import UsesContactStore from '../../mixins/UsesContactStore';
import UserService from '~/services/UserService';

export default {
    name: 'ContactUserFormModal',

    mixins: [UsesContactStore()],

    props: {
        userId: {
            validator(prop) {
                return prop === undefined || typeof(prop) === 'number';
            }
        }
    },

    data: () => ({
        avatar: null,
        user: User.create({
            firstName: null,
            lastName: null,
            mobile: null,
            email: null
        })
    }), 

    methods: {
        async save() {
            if (this.user.exists()) {
                await this.$contacts.update(this.user);
            } else {
                await this.$contacts.create(this.user);
            }

            this.$modal.close(this.$vnode.key);
        },
    },

    async created() {
        if (this.userId !== undefined) {
            this.user = await UserService.retrieve(this.userId);
        }
    }
}
</script>
