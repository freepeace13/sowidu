<template>
    <section id="notifications">
        <v-snackbar
            v-for="(toast, index) in $notification.toasts"
            :key="toast.id"
            :id="`notification-${index}`"
            :value="true"
            :right="true"
            :top="true"
            :style="{ top: `${computedTopValue(index)}` }"
            @input="$notification.removeToast(index)"
            multi-line
            :timeout="5000"
        >
            <v-list class="py-0" three-line dense>
                <v-list-tile :to="toast.route()">
                    <v-list-tile-avatar>
                        <v-img :src="toast.avatar" />
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ toast.message }}</v-list-tile-title>
                        <v-list-tile-sub-title>
                            {{ toast.createdAt | timediff }}
                        </v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-snackbar>
    </section>
</template>

<script>
import moment from 'moment';
import { Notification } from '@libs/v-notifications';

export default {
    name: 'v-notification-snackbar',

    filters: {
        timediff(date) {
            return moment(date).fromNow();
        }
    },

    computed: {
        previousNode() {
            return (index) => document.querySelector(`#notification-${index - 1}`);
        },

        computedTopValue() {
            return (index) => {
                const previousNode = this.previousNode(index);

                if (previousNode) {
                    const styles = window.getComputedStyle(previousNode);
                    return `calc(${styles.top} + ${styles.height} + 20px)`;
                }

                return 0;
            }
        }
    }
}
</script>

<style lang="scss" scoped>
    @import './styles.scss';
</style>