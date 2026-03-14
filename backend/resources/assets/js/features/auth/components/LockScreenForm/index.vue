<template>
    <div class="lockscreen">
        <v-container fluid fill-height>
            <v-layout row wrap>
                <v-flex xs4 offset-xs4 align-self-center>
                    <form class="validation-form" @submit.prevent="unlock(password)">
                        <div class="account-info">
                            <v-avatar size="100">
                                <img :src="profile.avatar.url"/>
                            </v-avatar>

                            <div class="account-name">
                                {{ profile.name }}
                            </div>
                        </div>

                        <v-text-field
                            v-model="password"
                            type="password"
                            placeholder="Password"
                            solo dark
                            :error="errors.has('password')"
                            :error-messages="errors.get('password')"
                            append-icon="lock_open"
                            :loading="loading"
                            @click:append="unlock(password)"
                        />
                    </form>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>

<script>
/** @flow */
import { MessageBag } from '~/support/wrappers';
import { User } from '~/services/models';

export default {
    props: {
        unlock: {
            type: Function,
            required: true
        },

        loading: {
            type: Boolean,
            default: false,
        },

        errors: {
            type: MessageBag,
            default: () => new MessageBag
        },

        profile: {
            type: User,
            required: true
        }
    },

    data: () => ({
        password: null
    })
}
</script>

<style lang="scss" scoped>
    .lockscreen {
        background: #424242;
        height: 100vh;

        .validation-form {
            .account-info {
                width: 50%;
                text-align: center;
                margin: 0 auto;
                margin-bottom: 15px;

                .account-name {
                    font-size: 20px;
                    letter-spacing: 1px;
                    line-height: 50px;
                    color: #ffff;
                }
            }
        }
    }
</style>
