<template>
    <v-navigation-drawer
        class="transparent"
        stateless
        permanent
    >
        <v-list class="transparent py-0" three-line>
            <v-list-tile :to="{ query: {} }" :active-class="activeClass"  exact>
                <v-list-tile-content>
                    <v-list-tile-title class="subheading">{{ $t('labels.state.pending') }}</v-list-tile-title>
                    <v-list-tile-sub-title class="caption">
                        {{ $t('hints.status.pending') }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
                <v-list-tile-action>
                    <v-chip>0</v-chip>
                </v-list-tile-action>
            </v-list-tile>
            <v-list-tile
                :active-class="activeClass"
                :to="{ query:{ state: 'final' }}"
                exact
            >
                <v-list-tile-content>
                    <v-list-tile-title class="subheading">{{ $t('labels.state.ongoing') }}</v-list-tile-title>
                    <v-list-tile-sub-title class="caption">
                        {{ $t('hints.status.ongoing') }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
                <v-list-tile-action>
                    <v-chip>0</v-chip>
                </v-list-tile-action>
            </v-list-tile>
            <v-list-tile
                exact
                :active-class="activeClass"
                :to="{ query:{ state: 'completed' }}"
            >
                <v-list-tile-content>
                    <v-list-tile-title class="subheading">{{ $t('labels.state.unconfirmed') }}</v-list-tile-title>
                    <v-list-tile-sub-title class="caption">
                        {{ $t('hints.status.unconfirmed') }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
                <v-list-tile-action>
                    <v-chip>0</v-chip>
                </v-list-tile-action>
            </v-list-tile>
            <v-list-tile
                exact
                :active-class="activeClass"
                :to="{ query:{ state: 'preparation' }}"
            >
                <v-list-tile-content>
                    <v-list-tile-title class="subheading">{{ $t('labels.state.drafts') }}</v-list-tile-title>
                    <v-list-tile-sub-title class="caption">
                        {{ $t('hints.status.drafts') }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </v-list-tile>
            <v-list-tile
                exact
                :active-class="activeClass"
                :to="{ query:{ state: 'done' }}"
            >
                <v-list-tile-content>
                    <v-list-tile-title class="subheading">{{ $t('labels.state.done') }}</v-list-tile-title>
                    <v-list-tile-sub-title class="caption">
                        {{ $t('hints.status.done') }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
                <v-list-tile-action>
                    <v-chip>0</v-chip>
                </v-list-tile-action>
            </v-list-tile>
            <v-list-tile
                exact
                :active-class="activeClass"
                :to="{ query:{ state: 'cancelled' }}"
            >
                <v-list-tile-content>
                    <v-list-tile-title class="subheading">{{ $t('labels.state.cancelled') }}</v-list-tile-title>
                    <v-list-tile-sub-title class="caption">
                        {{ $t('hints.status.cancelled') }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
    </v-navigation-drawer>
</template>

<script>
import { FILTER_TYPES } from '../enums';

export default {
    name: 'IndexSidebar',

    filters: {
        isActive(value) {
            return this.$route.query.state === value;
        }
    },

    props: {
        activeClass: {
            type: String,
            default: 'grey darken-4'
        }
    },

    methods: {
        goTo(value) {
            this.$router.push({ query: {
                state: value ? value : FILTER_TYPES.STATE.DEFAULT
            }});
        }
    }
}
</script>