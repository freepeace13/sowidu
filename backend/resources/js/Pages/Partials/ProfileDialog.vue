<template>
    <v-dialog
        v-model="show"
        persistent
        scrollable
    >
        <v-card
            class="overflow-hidden"
            flat
        >
            <v-toolbar
                extended
                flat
            >
                <v-btn
                    icon
                    @click="show = false"
                >
                    <v-icon>arrow_back</v-icon>
                </v-btn>

                <v-spacer />

                <v-toolbar-items>
                    <v-btn icon>
                        <v-icon>star_border</v-icon>
                    </v-btn>

                    <v-btn icon>
                        <v-icon>more_vert</v-icon>
                    </v-btn>
                </v-toolbar-items>
            </v-toolbar>

            <v-layout row>
                <v-flex
                    xs8
                    offset-xs2
                >
                    <v-card style="margin-top: -64px">
                        <v-card-title>
                            <v-layout
                                mt-3
                                column
                                align-center
                                justify-center
                            >
                                <v-avatar
                                    v-if="profile.photo"
                                    color="primary"
                                    size="140"
                                >
                                    <v-img :src="profile.photo" />
                                </v-avatar>

                                <v-spacer class="my-2" />

                                <v-layout
                                    align-center
                                    justify-center
                                    column
                                >
                                    <div class="headline font-weight-bold">
                                        {{ profile.name }}
                                    </div>

                                    <div
                                        v-if="profile.type == 'Company'"
                                        class="grey--text"
                                    >
                                        {{ $t('labels.organization') }}
                                    </div>
                                    <div
                                        v-else-if="profile.type == 'User'"
                                        class="grey--text"
                                    >
                                        {{ $t('labels.personal-account') }}
                                    </div>
                                    <div
                                        v-else-if="profile.type == 'Employee'"
                                        class="grey--text"
                                    >
                                        {{ $t('labels.inputs.members') }}
                                    </div>
                                </v-layout>

                                <v-btn
                                    v-if="isNotAdded"
                                    color="primary"
                                    :loading="isSendingRequest"
                                    @click="performAddContact"
                                >
                                    <v-icon left>person_add</v-icon>
                                    {{ $t('buttons.add-contact') }}
                                </v-btn>

                                <v-btn
                                    v-else-if="isWaitingToAccept"
                                    :loading="isSendingRequest"
                                    @click="performCancelRequest"
                                >
                                    <v-icon left>pending</v-icon>
                                    {{ $t('buttons.cancel-request') }}
                                </v-btn>

                                <v-btn
                                    v-else-if="isAlreadyAdded"
                                    :loading="isSendingRequest"
                                    color="secondary"
                                >
                                    <v-icon left>check</v-icon>
                                    {{ $t('addressbook.contact') }}
                                    <v-icon>arrow_drop_down</v-icon>
                                </v-btn>
                            </v-layout>
                        </v-card-title>

                        <v-divider class="mt-2" />

                        <v-tabs centered>
                            <v-tab>{{ $t('heading.about') }}</v-tab>
                            <v-tab>{{ $t('heading.organizations') }}</v-tab>
                            <v-tab>{{ $t('heading.about') }}</v-tab>
                        </v-tabs>
                    </v-card>

                    <v-card class="my-3">
                        <v-card-text>
                            Lorem ipsum dolor sit amet consectetur adipisicing
                            elit. Quibusdam iusto adipisci modi magnam illum,
                            rerum vero quam dicta doloribus quaerat esse
                            aperiam. Quaerat doloribus cumque assumenda,
                            doloremque dolor eius ipsa!
                        </v-card-text>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-card>
        <!-- <v-card tile class="hide-overflow">
      <div class="grey lighten-5" style="height: 200px">
        <v-toolbar color="transparent" class="mb-5" flat>
          <v-btn icon @click="show = false">
            <v-icon>arrow_back</v-icon>
          </v-btn>

          <v-spacer />

          <v-toolbar-items>
            <v-btn icon>
              <v-icon>star_border</v-icon>
            </v-btn>

            <v-btn icon>
              <v-icon>more_vert</v-icon>
            </v-btn>
          </v-toolbar-items>
        </v-toolbar>

        <v-divider class="mt-5" />

        <v-card tile>
          <v-card-text class="py-0" />

          <v-tabs class="mt-4" centered>
            <v-tab>About</v-tab>
            <v-tab>Organizations</v-tab>
            <v-tab>About</v-tab>
          </v-tabs>
        </v-card>
      </div>

      <v-container fluid>
        <v-card color="transparent">
          <v-card-text>asd</v-card-text>
        </v-card>
      </v-container>
    </v-card> -->
    </v-dialog>
</template>

<script>
import Http from '@/Modules/Http'

export default {
    data: () => ({
        isSendingRequest: false,
        show: false,
        edit: false,
        profile: {
            id: null,
            type: null,
            profile_id: null,
            name: null,
            mobile: null,
            photo: null,
            company: null,
            contactships: {
                contact_id: null,
                has_pending_request: false,
            },
        },
    }),

    computed: {
        hasPendingRequest() {
            return this.profile.contactships.has_pending_request
        },

        isAlreadyAdded() {
            return (
                this.profile.contactships.contact_id && !this.hasPendingRequest
            )
        },

        isWaitingToAccept() {
            return (
                !this.profile.contactships.contact_id && this.hasPendingRequest
            )
        },

        isNotAdded() {
            return (
                !this.profile.contactships.contact_id && !this.hasPendingRequest
            )
        },
    },

    methods: {
        view(profileId) {
            Http.get(this.$route('apps.profile.show', { profile: profileId }))
                .then((response) => {
                    this.profile = response.data.profile
                    this.show = true
                })
                .catch(console.error)
        },

        performAddContact() {
            this.isSendingRequest = true

            this.$inertia.post(
                this.$route('apps.contacts.requests.store'),
                {
                    recipient_id: this.profile.id,
                    recipient_type: this.profile.type,
                },
                {
                    onFinish: () => (this.isSendingRequest = false),
                    onSuccess: () => this.view(this.profile.profile_id),
                    onError: console.error,
                },
            )
        },

        performCancelRequest() {
            this.isSendingRequest = true

            this.$inertia.delete(
                this.$route('apps.contacts.requests.destroy'),
                {
                    data: {
                        recipient_id: this.profile.id,
                        recipient_type: this.profile.type,
                    },
                    onFinish: () => (this.isSendingRequest = false),
                    onError: (errors) => console.error(errors),
                    onSuccess: () => this.view(this.profile.profile_id),
                },
            )
        },
    },
}
</script>
