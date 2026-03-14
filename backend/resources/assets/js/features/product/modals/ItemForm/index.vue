<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-container grid-list-lg fluid>
            <TextField
                :readonly="!editable"
                label="Name"
                v-model="item.name"
                :errors="$items.$errors.get('name', [])"
            />

            <h4 class="mb-2 font-weight-bold">DESCRIPTION</h4>
            <v-textarea
                :readonly="!editable"
                placeholder="(Optional) Tell something about this item..."
                v-model="item.longDescription"
                :error="$items.$errors.has('long_description')"
                :error-messages="$items.$errors.get('long_description', [])"
                solo
            />

            <v-layout row>
                <v-flex xs6 class="pr-2">
                    <h4 class="mb-2">UNIT</h4>
                    <ItemUnitSelector
                        :readonly="!editable"
                        v-model="item.$refs.unit"
                        label="Unit"
                        :errors="$items.$errors.get('unit', [])"
                    />
                </v-flex>
                <v-flex xs6 class="pl-2">
                    <h4 class="mb-2">TYPE</h4>
                    <ItemTypeSelector
                        :readonly="!editable"
                        v-model="item.$refs.type"
                        label="Type"
                        :errors="$items.$errors.get('type', [])"
                    />
                </v-flex>
            </v-layout>

            <TextField
                :readonly="!editable"
                label="Offered Price"
                placeholder="0.00"
                v-model="item.offeredPrice"
                :errors="$items.$errors.get('offered_price', [])"
                type="number"
            />

            <TextField
                :readonly="!editable"
                placeholder="0.00"
                label="Fix Traded Price"
                v-model="item.fixTradedPrice"
                :errors="$items.$errors.get('fix_traded_price', [])"
                type="number"
            />

            <TextField
                :readonly="!editable"
                label="Retail Price"
                placeholder="0.00"
                v-model="item.retailPrice"
                :errors="$items.$errors.get('retail_price')"
                type="number"
            />
        </v-container>

        <FormsMediaList :media="item.media" />

        <template v-slot:actions>
            <template v-if="editable">
                <BrowsersMenu
                    :only="['media']"
                    @browse-media="browseMedia"
                />

                <v-spacer></v-spacer>

                <v-btn
                    color="primary"
                    :loading="$items.$loading"
                    :disabled="$items.$loading"
                    @click="save"
                >
                    {{ item.exists() ? 'Save Changes' : 'Create' }}
                </v-btn>
            </template>

            <v-btn
                :block="!editable"
                color="grey darken-3"
                @click="$modal.close($vnode.key)"
            >
                {{ editable ? 'Cancel' : 'Close' }}
            </v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import { AuthorizationService } from '@features/auth/api';
import { Item, Media } from '~/services/models';
import UsesItemStore from '../../mixins/UsesItemStore';
import ItemService from '@features/product/api';
import { isFunction } from '~/support/helpers';
import { Response } from '~/services/events/modal';
import TogglesMedia from '@features/media/mixins/TogglesMedia';
import FormsMediaList from '@features/media/components/MediaAttachForm';
import BrowsersMenu from '@common/components/BrowseButton';
import { createResource } from 'vue-async-manager';
import * as Enums from '../../enums';
import ItemTypeSelector from '../../components/ItemTypeSelector';
import ItemUnitSelector from '../../components/ItemUnitSelector';

export default {
    name: 'ItemFormModal',

    mixins: [
        UsesItemStore(),
        TogglesMedia('item')
    ],

    components: {
        FormsMediaList,
        BrowsersMenu,
        ItemUnitSelector,
        ItemTypeSelector
    },

    props: {
        itemId: Number,
        onSuccess: Function
    },

    data: () => ({
        editable: true,
        item: Item.create({
            name: null,
            longDescription: null,
            offeredPrice: 0,
            retailPrice: 0,
            fixTradedPrice: 0,
            type: null,
            unit: null,
            media: []
        })
    }),

    methods: {
        async save() {
            if (this.item.exists()) {
                this.item = await this.$items.update(this.item);
            } else {
                this.item = await this.$items.create(this.item);
            }

            if (isFunction(this.onSuccess)) {
                this.onSuccess(new Response(this, this.item));
            } else {
                this.$modal.close(this.$vnode.key);
            }
        }
    },

    async created() {
        this.$rm = createResource(async (id) => {
            this.item = await ItemService.retrieve(id);

            AuthorizationService.can(Enums.PERMISSIONS.UPDATE_PRODUCT)
                .then(() => {
                    this.editable = true;
                })
                .catch(() => {
                    this.editable = false;
                });
        });

        if (this.itemId !== undefined) {
            await this.$rm.read(this.itemId);
        }
    }
}
</script>
