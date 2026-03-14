<template>
    <div class="modal-container">
        <v-dialog
            v-for="modal in modals"
            :key="modal.id"
            v-model="modal.visible"
            :fullscreen="modal.options.fullscreen"
            transition="scale-transition"
            persistent
            scrollable
            max-height="90%"
            :max-width="sizes[modal.options.size]"
        >
            <Component
                :is="modal.component"
                :modal="modal"
                :fullscreen="modal.options.fullscreen"
                v-on="modal.listeners"
                v-bind="modal.attrs"
                :key="modal.id"
            />
        </v-dialog>
    </div>
</template>


<script>
    import { random } from '~/utils/string'
    import { $modal } from '~/services/events/modal'

    const shape = (modal, options, attrs, listeners) => {
        return {
            id: options.key || random(),
            component: (modal instanceof Function) ? modal() : modal,
            visible: false,
            listeners: listeners ? {...listeners} : {},
            attrs: attrs ? {...attrs} : {},
            options: {
                size: options.size || 'sm',
                title: options.title || 'Modal',
                fullscreen: options.fullscreen || false,
                minHeight: options.minHeight || '200px'
            }
        }
    }

    export default {
        data: () => ({
            modals: [],
            // TODO: Hard coded data will be transfered to appropreciate files
            sizes: {
                xs: 350,
                sm: 550,
                md: 720,
                lg: 990,
                xl: '100%'
            }
        }),

        methods: {
            _findIndex(modal) {
                return this.modals.findIndex(e => e.id == modal.id)
            },

            _clearThenPush(modal) {
                this.clear();
                this.modals.push(modal);
            },

            _toggleVisibilityByIndex(index) {
                if (index !== -1) {
                    this.modals.splice(index, 1, {
                        ...this.modals[index],
                        visible: !this.modals[index].visible
                    })
                }
            },

            _toggleVisibility(modal) {
                this.$nextTick(() => {
                    const index = this._findIndex(modal)
                    this._toggleVisibilityByIndex(index)
                })
            },

            show(modal, options, attrs, listeners) {
                const shapedEntry = shape(modal, options, attrs, listeners)

                this._clearThenPush(shapedEntry)
                this._toggleVisibility(shapedEntry)
            },

            append(modal, options, attrs, listeners) {
                const shapedEntry = shape(modal, options, attrs, listeners)

                this.modals.push(shapedEntry)
                this._toggleVisibility(shapedEntry)
            },

            closeAll() {
                this.modals = [...this.modals.map(e => ({...e, visible: false}))]
            },

            close(key) {
                const index = this.modals.findIndex(e => e.id === key);
                if (index === -1) return;

                let modal = this.modals[index];
                let closable = true;
                let { beforeClose, afterClose } = modal.listeners;

                if (typeof beforeClose === 'function') {
                    if (beforeClose(modal.component) === false) {
                        closable = false;
                    }
                }

                if (closable) {
                    this.modals.splice(index, 1);
                    closable = true;

                    if (typeof afterClose === 'function') {
                        afterClose(modal.id);
                    }
                }
            },

            clear() {
                this.modals = []
            }
        },

        beforeMount() {
            $modal.listen('show', args => {
                const { modal, listeners, attrs, ...options } = args
                this.show(modal, options, attrs, listeners)
            })

            $modal.listen('close', this.close)

            $modal.listen('closeAll', this.closeAll)

            $modal.listen('append', args => {
                const { modal, listeners, attrs, ...options } = args
                this.append(modal, options, attrs, listeners)
            })

            $modal.listen('clear', this.clear)
        },

        // watch: {
        //     $route(to, from) {
        //         console.log(to, from)
        //     }
        // }
    }
</script>

<style lang="scss" scoped>
/deep/ .v-dialog {
    overflow: hidden;
}
</style>
