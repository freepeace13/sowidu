<template>
    <v-card color="grey darken-4">
        <form class="pa-5" @submit.prevent="submit">
            <v-layout row>
                <v-flex xs8>
                    <v-layout row>
                        <v-flex xs6>
                            <TextField
                                label="Firstname"
                                v-model="form.first_name"
                                :errors="errors.first_name">
                            </TextField>
                        </v-flex>
                        <v-flex xs6>
                            <TextField
                                label="Lastname"
                                v-model="form.last_name"
                                :errors="errors.last_name">
                            </TextField>
                        </v-flex>
                    </v-layout>

                    <TextField
                        label="Username"
                        v-model="form.username"
                        :errors="errors.username">
                    </TextField>
                </v-flex>
                <v-flex xs4 class="text-xs-center">
                    <AvatarCropper
                        v-model="form.avatar"
                        type="user"
                        :errors="errors.avatar"
                        @input="saveAvatar"
                        :initialImage="avatar">
                    </AvatarCropper>


                    <v-btn color="primary" class="mt-3"
                        @click="saveAvatar">Choose Avatar</v-btn>
                </v-flex>
            </v-layout>

            <v-divider></v-divider>
            <v-subheader class="px-0">Contact Details</v-subheader>

            <v-layout row>
                <v-flex xs6>
                    <TextField
                        label="Phone"
                        v-model="form.mobile"
                        :errors="errors.mobile">
                    </TextField>
                </v-flex>
                <v-flex xs6>
                    <TextField
                        type="email"
                        label="Email address"
                        v-model="form.email"
                        :errors="errors.email">
                    </TextField>
                </v-flex>
            </v-layout>

            <v-divider></v-divider>
            <v-subheader class="px-0">Password</v-subheader>

            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation</p>

            <v-layout row>
                <v-flex xs6>
                    <TextField
                        type="password"
                        label="Old Password"
                        v-model="form.old_password"
                        :errors="errors.old_password">
                    </TextField>
                </v-flex>
                <v-flex xs6 align-self-center class="text-xs-right">
                    <a href="#">Forgot Password?</a>
                </v-flex>
            </v-layout>

            <v-layout row>
                <v-flex xs6>
                    <TextField
                        type="password"
                        label="New Password"
                        v-model="form.new_password"
                        :errors="errors.new_password">
                    </TextField>
                </v-flex>
                <v-flex xs6>
                    <TextField
                        type="password"
                        label="Confirm New Password"
                        v-model="form.new_password_confirmation"
                        :errors="errors.new_password_confirmation">
                    </TextField>
                </v-flex>
            </v-layout>

            <v-divider class="mt-3"></v-divider>
            <v-subheader class="px-0">Default address</v-subheader>

            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco</p>

            <v-layout row>
                <v-flex xs10>
                    <div class="bordered pa-2">
                        {{ form.address ? form.address.long_address : 'No default address yet.' }}
                    </div>
                </v-flex>
                <v-flex xs2 align-self-center class="text-xs-right">
                    <a href="#" @click="viewAddresses">Change</a>
                </v-flex>
            </v-layout>

            <v-layout row class="mt-5">
                <v-flex>
                    <v-btn :loading="loading" block color="primary" type="submit">
                        Update
                    </v-btn>
                </v-flex>
                <v-flex>
                    <v-btn block color="grey darken-3">
                        Cancel
                    </v-btn>
                </v-flex>
            </v-layout>
        </form>
    </v-card>
</template>

<script>
    import axios from 'axios'

    export default {
        data: () => ({
            form: {
                first_name: null,
                last_name: null,
                avatar: null,
                address: null
            },
            errors: {},
            loading: false,
            avatar: null
        }),

        methods: {
            async submit() {
                try {
                    this.loading = true
                    this.errors = {}
                    const { data } = await axios.patch(`/api/settings/account/user`, this.form)
                    this.form = { ...data.data }
                    this.$store.dispatch('auth/user/setUser', data.data)
                    this.$events.$emit('alert', 'Account settings changes updated')
                } catch (e) {
                    this.errors = e.errors || {}
                } finally {
                    this.loading = false
                }
            },

            viewAddresses() {
                this.$modal.show({
                    size: 'md',
                    title: 'Change Address',
                    modal: require('~/components/UI/Modals/AddressSelector').default,
                    listeners: {
                        select: (address) => {
                            this.$store.dispatch('auth/changeAccountAddress', address)
                            this.form.address = address
                        }
                    }
                })
            },

            async saveAvatar(file) {
                this.loading = true

                // Save to `media` first
                this.$uploader.upload({
                    url: '/api/media',
                    files: [file],
                    onError: (file, message) => {
                        const response = JSON.parse(message)
                        let error = {}
                        if (response.errors.file)
                            error = response.errors.file[0]
                        else if (response.errors.resumableType)
                            error = response.errors.resumableType[0]
                        else if (response.errors.resumableTotalSize)
                            error = response.errors.resumableTotalSize[0]

                        this.$events.$emit('alert', error)
                    },
                    onFileSuccess: (file, message) => {
                        const response = JSON.parse(message)
                        this.setAsAvatar(response.data)
                    },
                    onCompleted: () => {
                        this.$uploader.close()
                    }
                })

            },

            async setAsAvatar({ id }) {
                const { data } = await axios.patch(`/api/media/${id}/set-as-avatar`)
                this.$store.dispatch('auth/user/fetchUser')
                this.$events.$emit('alert', 'Your primary avatar successfully changed.')
                this.loading = false
            }
        },

        created() {
            this.form = {
                ...this.$store.getters['auth/activeAccount']
            }

            this.avatar = this.form.avatar
        },
    }
</script>

<style lang="scss" scoped>
    /deep/ .v-input__control {
        width: 100% !important;

        .v-radio {
            margin-right: 0px;
        }

        .v-input--selection-controls__input {
            margin-right: 15px;
        }

        .v-label {
            width: 100%;
        }
    }
</style>
