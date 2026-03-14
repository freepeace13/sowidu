<template>
    <form @submit.prevent="update">
        <v-layout column>
            <v-flex align-self-center>
                <v-avatar size="150">
                    <input-avatar
                        class="mx-auto"
                        :image="$page.props.profile.photo"
                        @saved="selectAvatar"
                    />
                </v-avatar>
            </v-flex>

            <v-flex>
                <v-layout
                    tw-flex-wrap
                    sm:tw-flex-nowrap
                >
                    <v-flex
                        sm6
                        xs12
                    >
                        <input-text-field
                            v-model="form.first_name"
                            :label="$t('labels.inputs.firstname')"
                            :error-messages="form.errors.first_name"
                            :hide-details="!form.errors.first_name"
                            required
                        />
                    </v-flex>

                    <v-flex
                        sm6
                        xs12
                    >
                        <input-text-field
                            v-model="form.last_name"
                            :label="$t('labels.inputs.lastname')"
                            :error-messages="form.errors.last_name"
                            :hide-details="!form.errors.last_name"
                            required
                        />
                    </v-flex>
                </v-layout>
            </v-flex>

            <v-flex>
                <input-text-field
                    v-model="form.birthdate"
                    type="date"
                    :label="$t('labels.inputs.birthdate')"
                    :error-messages="form.errors.birthdate"
                    :hide-details="!form.errors.birthdate"
                    required
                />
            </v-flex>

            <v-flex>
                <input-select
                    v-model="form.gender"
                    :label="$t('labels.inputs.gender')"
                    :error-messages="form.errors.gender"
                    :items="$page.props.genders"
                />
            </v-flex>

            <v-flex>
                <v-layout
                    tw-flex-wrap
                    sm:tw-flex-nowrap
                >
                    <v-flex xs12>
                        <AddressFields
                            v-model="form.address"
                            :is-loading="form.processing"
                            :errors="form.errors"
                        />
                    </v-flex>
                </v-layout>
            </v-flex>
            <v-flex>
                <v-layout>
                    <v-flex
                        pa-0
                        xs12
                        align-self-center
                        class="tw-flex tw-justify-end"
                    >
                        <v-btn
                            color="primary"
                            :loading="form.processing"
                            :disabled="form.processing"
                            @click.stop="update"
                        >
                            {{ $t('buttons.save-changes') }}
                            <template #loader>
                                <span>{{ $t('buttons.saving') }}</span>
                            </template>
                        </v-btn>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </form>
</template>

<script>
import UpdatesProfiles from '@/Mixins/UpdatesProfiles'
import AddressFields from '@components/Fields/AddressFields.vue'

export default {
    name: 'UserInformationForm',

    components: {
        AddressFields,
    },

    mixins: [UpdatesProfiles],

    data: (vm) => ({
        form: vm.$inertia.form({
            avatar: null,
            first_name: vm.$page.props.profile.first_name,
            last_name: vm.$page.props.profile.last_name,
            // nickname: vm.$page.props.profile.nickname,
            // bio: vm.$page.props.profile.bio,
            birthdate: vm.$page.props.profile.birthdate,
            gender: vm.$page.props.profile.gender,
            // job_title: vm.$page.props.profile.job_title,
            address: {
                house_number: vm.$page.props.profile?.address.house_number,
                street: vm.$page.props.profile?.address.street,
                zipcode: vm.$page.props.profile?.address.zipcode,
                city: vm.$page.props.profile?.address.city,
                state: vm.$page.props.profile?.address.state,
                country: vm.$page.props.profile?.address.country,
            },
        }),
    }),

    methods: {
        update() {
            this.form.clearErrors()

            this.form
                .transform((data) => ({
                    ...data,
                    _method: 'put',
                    address: {
                        ...data.address,
                        country: data.address.country?.code,
                    },
                }))
                .post(this.$route('account.profile.update'), {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['errors', 'profile', 'user'],
                })
        },
    },
}
</script>
