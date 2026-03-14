import Vue from 'vue'
import modal from './modal/observer'
import snackbar from './snackbar/observer'
import lockscreen from './lockscreen/observer';
import chunkUploader from './chunkUploader/observer';
import confirmDialog from './confirmDialog/observer';

export default new Vue({
    computed: {
        [modal.name]() {
            return modal.bindTo(this);
        },
        [snackbar.name]() {
            return snackbar.bindTo(this);
        },
        [lockscreen.name]() {
            return lockscreen.bindTo(this);
        },
        [chunkUploader.name]() {
            return chunkUploader.bindTo(this);
        },
        [confirmDialog.name]() {
            return confirmDialog.bindTo(this);
        }
    }
});
