<template>
    <ModalLayout
        :title="computedTitle"
        :id="$attrs.modal.id"
        :on-close="close"
    >
        <v-container grid-list-lg fluid>
            <div>{{ $t('labels.started') }}: {{ task.formattedDates.startedAt }}</div>
            <div>{{ $t('labels.ended') }}: {{ task.formattedDates.endedAt }}</div> 
        </v-container>

        <v-divider></v-divider>

        <v-container grid-list-lg fluid>
            <SuggestionField
                v-model="task.title"
                :items="tasks.map((v) => v.title)"
                :label="$t('labels.inputs.title')"
                :errors="$tasks.$errors.get('title', [])"
            />

            <v-textarea
                :placeholder="$t('hints.tell-something-about-this-task')"
                v-model="task.description"
                :error="$tasks.$errors.has('description')"
                :error-messages="$tasks.$errors.get('description', [])"
                solo>
            </v-textarea>
        </v-container>

        <template v-if="task.exists()">
            <v-divider></v-divider>
            <v-subheader class="px-4">{{ $t('labels.status') }}</v-subheader>

            <v-container fluid class="pt-0">
                <ResourceStateSelector
                    :items="selectableStates"
                    v-model="task.states.state.current"
                    block
                />
            </v-container>
        </template>

        <v-divider></v-divider>
        <v-subheader class="px-4">{{ $t('labels.inputs.members') }}</v-subheader>

        <v-container fluid class="py-0">
            <AuthorizableSelector
                v-model="task.members"
                :errors="$tasks.$errors.get('members', [])"
                multiple
            />
        </v-container>

        <FormsMediaList :media="task.media"/>
        <FormsDeliveriesList :deliveries="task.deliveries" @remove="removeDelivery" />
        <FormsOrdersList :orders="task.orders" @remove="removeOrder" />

        <v-divider></v-divider>
        <v-subheader class="px-4">{{ $t('labels.comments') }}</v-subheader>

        <v-container fluid class="py-0">
            <CommentForm
                :avatar="profile().avatar.url"
                @send="postComment"
            />

            <CommentTile
                color="grey darken-3"
                v-for="comment in task.comments"
                :subtitle="comment.author.title"
                :caption="task.hasMember(comment.author) ? $t('labels.inputs.members') : $t('labels.inputs.author')"
                :key="comment.id"
                :title="comment.author.name"
                :avatar="comment.author.avatar.url"
                :posted-on="comment.postedAt"
            >
                {{ comment.message }}
            </CommentTile>
        </v-container>

        <template v-slot:actions>
            <BrowsersMenu
                :except="['tasks']"
                @browse-deliveries="browseDeliveries"
                @browse-orders="browseOrders"
                @browse-media="browseMedia"
            />

            <v-spacer></v-spacer>

            <v-btn color="primary" @click="onSave()" :loading="$tasks.$loading">
                {{ task.exists() ? $t('buttons.save-changes') : $t('buttons.create') }}
            </v-btn>

            <v-btn color="grey darken-3" @click="close">
                {{ $t('buttons.close') }}
            </v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import * as utils from '~/support/helpers';
import ResourceStateSelector from '~/components/UI/Inputs/Selectors/ResourceStateSelector';
import { ProgressState } from '~/services/models/task/states';
import { Response } from '~/services/events/modal'
import { Task, Delivery, Comment } from '~/services/models';
import TaskService from '@features/task/api';
import UsesTaskStore from '../../mixins/UsesTaskStore';
import { isFunction, isNullOrUndefined } from '~/support/helpers';
import BrowsersMenu from '@common/components/BrowseButton';
import TogglesDeliveries from '@features/delivery/mixins/TogglesDeliveries';
import TogglesOrders from '@features/ordering/mixins/TogglesOrders';
import TogglesMedia from '@features/media/mixins/TogglesMedia';
import { HandlesAuthorizations } from '~/components/Mixins';
import FormsDeliveriesList from '~/components/UI/Modals/Components/FormsDeliveriesList';
import FormsOrdersList from '@features/ordering/components/OrdersAttachForm';
import FormsMediaList from '@features/media/components/MediaAttachForm';
import SuggestionField from '~/components/UI/Inputs/SuggestionField';
import CommentTile from '~/components/features/CommentTile';
import CommentForm from '~/components/features/CommentForm';
import Events from '~/services/store/websocket/events';
import { lazy, createResource } from 'vue-async-manager';


const AuthorizableSelector = lazy(() => import('~/components/UI/Inputs/Selectors/AuthorizableSelector'));

export default {
    name: 'TaskFormModal',

    mixins: [
        UsesTaskStore(),
        TogglesDeliveries('task'),
        TogglesOrders('task'),
        TogglesMedia('task'),
        HandlesAuthorizations()
    ],

    components: {
        CommentTile,
        CommentForm,
        BrowsersMenu,
        FormsDeliveriesList,
        FormsOrdersList,
        FormsMediaList,
        AuthorizableSelector,
        ResourceStateSelector,
        SuggestionField
    },

    props: {
        taskId: {
            validator(prop) {
                return prop === undefined || utils.isNumber(prop);
            }
        },

        onSuccess: {
            default: null,
            validator(prop) {
                return isNullOrUndefined(prop) || isFunction(prop);
           }
        },
    },

    data: () => ({
        task: Task.create({
            title: null,
            description: null,
            members: [],
            deliveries: [],
            orders: [],
            media: []
        }),
    }),

    computed: {
        selectableStates() {
            return (new ProgressState).states.filter((state) => {
                const policies = this.task.policies.states('state');
                return policies.isTransitionableTo(state) || policies.is(state);
            });
        },

        computedTitle() {
            return ! this.task.exists()
                ? this.task.title || 'Task'
                : `Created By: ${this.task.creator.name}`;
        }
    },

    methods: {
        postComment(message) {
            TaskService.comments(this.task.id).create({ message });
        },

        close(callback = null) {
            this.$modal.close(this.$vnode.key);

            if (typeof(callback) === 'function') {
                callback();
            }
        },

        async onSave() {
            if (this.task.exists()) {
                this.task = await this.$tasks.update(this.task);
            } else {
                this.task = await this.$tasks.create(this.task);
            }

            if (isFunction(this.onSuccess)) {
                this.onSuccess(new Response(this, this.task));
            } else {
                this.$modal.close(this.$vnode.key);
            }
        },
    },

    async created() {
        this.$rm = createResource(async (id) => {
            this.task = await TaskService.retrieve(id);

            this.$wsChannel = Echo.private(`tasks.${id}`);

            this.$wsChannel.listen(Events.CommentCreated, (value) => {
                this.task.comments.unshift(Comment.create(value));
            });
        });
        
        if (this.taskId !== undefined) {
            this.$rm.read(this.taskId);
        }
    },

    beforeDestroy() {
        if (this.$wsChannel) {
            this.$wsChannel.stopListening(Events.CommentCreated);
        }
    }
}
</script>
